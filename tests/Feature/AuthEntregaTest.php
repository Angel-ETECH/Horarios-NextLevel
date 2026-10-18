<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Events\TokenAuthenticated;
use Laravel\Sanctum\PersonalAccessToken;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class AuthEntregaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null,
            'auth.admin_invitation_code' => 'test-invitation',
            'app.frontend_url' => 'https://school.example.test',
        ]);
        DB::purge('sqlite');

        foreach ([
            '0001_01_01_000000_create_users_table.php',
            '2026_09_22_091535_create_personal_access_tokens_table.php',
        ] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
    }

    public function test_auth_database_is_isolated_in_memory_sqlite(): void
    {
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
    }

    public static function profiles(): array
    {
        return [['/api/auth/user'], ['/api/auth/me']];
    }

    #[DataProvider('profiles')]
    public function test_profile_returns_only_public_user_fields(string $uri): void
    {
        $user = $this->user();
        $token = $user->createToken('test')->plainTextToken;

        $this->api('GET', $uri, [], $token)->assertOk()->assertExactJson([
            'success' => true,
            'data' => ['user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]],
        ]);
    }

    public static function protectedEndpoints(): array
    {
        return [
            ['GET', '/api/auth/user'],
            ['GET', '/api/auth/me'],
            ['POST', '/api/auth/refresh'],
            ['POST', '/api/auth/logout'],
        ];
    }

    #[DataProvider('protectedEndpoints')]
    public function test_protected_endpoints_reject_missing_and_invalid_bearers(string $method, string $uri): void
    {
        $this->api($method, $uri)->assertUnauthorized();
        $this->api($method, $uri, [], '999|invalid-token')->assertUnauthorized();
    }

    public static function publicPrefixes(): array
    {
        return [['/api'], ['/api/auth']];
    }

    #[DataProvider('publicPrefixes')]
    public function test_login_keeps_the_existing_response_and_replaces_previous_tokens(string $prefix): void
    {
        $user = $this->user();
        $oldToken = $user->createToken('previous-login')->plainTextToken;

        $response = $this->api('POST', $prefix.'/login', [
            'email' => $user->email,
            'password' => 'Password123',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonMissingPath('data.user.password');

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->api('GET', '/api/auth/me', [], $oldToken)->assertUnauthorized();
        $this->api('GET', '/api/auth/user', [], $response->json('data.token'))->assertOk();
    }

    #[DataProvider('publicPrefixes')]
    public function test_invalid_login_preserves_the_existing_token(string $prefix): void
    {
        $user = $this->user();
        $token = $user->createToken('existing')->plainTextToken;

        $this->api('POST', $prefix.'/login', [
            'email' => $user->email,
            'password' => 'Incorrect123',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->api('GET', '/api/auth/me', [], $token)->assertOk();
    }

    #[DataProvider('publicPrefixes')]
    public function test_registration_requires_a_valid_configured_invitation(string $prefix): void
    {
        $payload = $this->registration();
        $this->api('POST', $prefix.'/register', array_diff_key($payload, ['codigo_invitacion' => true]))
            ->assertUnprocessable()->assertJsonValidationErrors('codigo_invitacion');

        $this->api('POST', $prefix.'/register', array_replace($payload, ['codigo_invitacion' => 'wrong']))
            ->assertForbidden()->assertJsonPath('success', false);

        config(['auth.admin_invitation_code' => null]);
        $this->api('POST', $prefix.'/register', $payload)->assertForbidden();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[DataProvider('publicPrefixes')]
    public function test_registration_issues_a_usable_token_and_hashes_the_password(string $prefix): void
    {
        $response = $this->api('POST', $prefix.'/register', $this->registration())
            ->assertCreated()->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'new-admin@example.test')
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonMissingPath('data.user.password');

        $this->assertTrue(Hash::check('Password123', User::firstOrFail()->password));
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->api('GET', '/api/auth/me', [], $response->json('data.token'))->assertOk();
        $this->api('POST', $prefix.'/register', $this->registration())
            ->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_refresh_rotates_only_the_current_token_and_preserves_its_limits(): void
    {
        $user = $this->user();
        $old = $user->createToken('limited-device', ['schedule:read'], now()->addHour());
        $other = $user->createToken('another-device')->plainTextToken;

        $response = $this->api('POST', '/api/auth/refresh', [], $old->plainTextToken)
            ->assertOk()->assertJsonPath('success', true)->assertJsonPath('data.token_type', 'Bearer');
        $newToken = $response->json('data.token');
        $this->assertNotSame($old->plainTextToken, $newToken);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $old->accessToken->id]);
        $this->assertDatabaseCount('personal_access_tokens', 2);
        $replacement = PersonalAccessToken::findToken($newToken);
        $this->assertSame('limited-device', $replacement->name);
        $this->assertSame(['schedule:read'], $replacement->abilities);
        $this->assertTrue($old->accessToken->expires_at->equalTo($replacement->expires_at));

        $this->api('GET', '/api/auth/user', [], $old->plainTextToken)->assertUnauthorized();
        $this->api('POST', '/api/auth/refresh', [], $old->plainTextToken)->assertUnauthorized();
        $this->api('GET', '/api/auth/me', [], $newToken)->assertOk();
        $this->api('GET', '/api/auth/me', [], $other)->assertOk();
    }

    public function test_refresh_restores_the_old_token_if_replacement_creation_fails(): void
    {
        $user = $this->user();
        $token = $user->createToken('existing')->plainTextToken;
        Event::listen('eloquent.creating: '.PersonalAccessToken::class, function () {
            throw new RuntimeException('Simulated token creation failure');
        });

        $this->api('POST', '/api/auth/refresh', [], $token)
            ->assertStatus(500)->assertJsonPath('success', false);

        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->assertNotNull(PersonalAccessToken::findToken($token));
        $this->api('GET', '/api/auth/me', [], $token)->assertOk();
    }

    public function test_refresh_cannot_replace_a_token_revoked_after_authentication(): void
    {
        $token = $this->user()->createToken('existing')->plainTextToken;
        Event::listen(TokenAuthenticated::class, function (TokenAuthenticated $event) {
            // Another request revokes the row without changing the guard's in-memory model.
            $event->token->newQuery()->whereKey($event->token->getKey())->delete();
        });

        $this->api('POST', '/api/auth/refresh', [], $token)->assertUnauthorized();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_revokes_only_the_current_token_and_rejects_reuse(): void
    {
        $user = $this->user();
        $token = $user->createToken('current-device')->plainTextToken;
        $other = $user->createToken('another-device')->plainTextToken;

        $this->api('POST', '/api/auth/logout', [], $token)
            ->assertOk()->assertJsonPath('success', true);
        $this->assertNull(PersonalAccessToken::findToken($token));
        $this->assertDatabaseCount('personal_access_tokens', 1);
        $this->api('POST', '/api/auth/logout', [], $token)->assertUnauthorized();
        $this->api('POST', '/api/auth/refresh', [], $token)->assertUnauthorized();
        $this->api('GET', '/api/auth/user', [], $token)->assertUnauthorized();
        $this->api('GET', '/api/auth/me', [], $other)->assertOk();
    }

    public function test_logout_rejects_a_token_revoked_after_authentication(): void
    {
        $token = $this->user()->createToken('existing')->plainTextToken;
        Event::listen(TokenAuthenticated::class, function (TokenAuthenticated $event) {
            $event->token->newQuery()->whereKey($event->token->getKey())->delete();
        });

        $this->api('POST', '/api/auth/logout', [], $token)->assertUnauthorized();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public static function tokenMutations(): array
    {
        return [['/api/auth/refresh'], ['/api/auth/logout']];
    }

    #[DataProvider('tokenMutations')]
    public function test_token_mutations_reject_session_authentication_without_a_personal_token(string $uri): void
    {
        $this->actingAs($this->user(), 'web')->postJson($uri)->assertUnauthorized();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    #[DataProvider('publicPrefixes')]
    public function test_password_reset_link_works_once_and_revokes_all_existing_tokens(string $prefix): void
    {
        Notification::fake();
        $user = $this->user();
        $first = $user->createToken('first-device')->plainTextToken;
        $second = $user->createToken('second-device')->plainTextToken;

        $this->api('POST', $prefix.'/forgot-password', ['email' => $user->email])
            ->assertOk()->assertJsonPath('success', true);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
        $notification = Notification::sent($user, ResetPasswordNotification::class)->sole();
        $url = $notification->toMail($user)->actionUrl;
        $this->assertSame('https://school.example.test/reset-password', strtok($url, '?'));
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        $this->assertSame(['token' => $notification->token, 'email' => $user->email], $query);
        $this->assertDatabaseMissing('password_reset_tokens', ['token' => $notification->token]);

        $payload = [
            'email' => $query['email'],
            'token' => $query['token'],
            'password' => 'NewPassword456',
            'password_confirmation' => 'NewPassword456',
        ];
        $this->api('POST', $prefix.'/reset-password', $payload)
            ->assertOk()->assertJsonPath('success', true);
        $this->assertTrue(Hash::check('NewPassword456', $user->fresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->assertDatabaseCount('password_reset_tokens', 0);
        $this->api('GET', '/api/auth/me', [], $first)->assertUnauthorized();
        $this->api('GET', '/api/auth/me', [], $second)->assertUnauthorized();
        $this->api('POST', $prefix.'/reset-password', $payload)->assertUnprocessable();
        $this->api('POST', $prefix.'/login', ['email' => $user->email, 'password' => 'Password123'])
            ->assertUnprocessable();
        $this->api('POST', $prefix.'/login', ['email' => $user->email, 'password' => 'NewPassword456'])
            ->assertOk();
    }

    #[DataProvider('publicPrefixes')]
    public function test_invalid_reset_token_keeps_password_and_existing_access(string $prefix): void
    {
        $user = $this->user();
        $token = $user->createToken('existing')->plainTextToken;
        $this->api('POST', $prefix.'/reset-password', [
            'email' => $user->email,
            'token' => 'invalid-reset-token',
            'password' => 'NewPassword456',
            'password_confirmation' => 'NewPassword456',
        ])->assertUnprocessable()->assertJsonPath('success', false);

        $this->assertTrue(Hash::check('Password123', $user->fresh()->password));
        $this->api('GET', '/api/auth/me', [], $token)->assertOk();
    }

    public static function resetOrigins(): array
    {
        return [
            [null, 'https://school.example.test', 'https://school.example.test/reset-password'],
            ['', 'https://school.example.test/', 'https://school.example.test/reset-password'],
            ['https://portal.example.test/', 'https://school.example.test', 'https://portal.example.test/reset-password'],
            ['https://portal.example.test/app/', 'https://school.example.test', 'https://portal.example.test/app/reset-password'],
        ];
    }

    #[DataProvider('resetOrigins')]
    public function test_reset_url_falls_back_to_app_url_and_normalizes_trailing_slashes(
        ?string $frontendUrl, string $appUrl, string $expectedPath
    ): void {
        config(['app.frontend_url' => $frontendUrl, 'app.url' => $appUrl]);
        $user = $this->user();
        $url = (new ResetPasswordNotification('test-token'))->toMail($user)->actionUrl;
        $this->assertSame($expectedPath, strtok($url, '?'));
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        $this->assertSame(['token' => 'test-token', 'email' => $user->email], $query);
    }

    private function user(): User
    {
        return User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.test',
            'password' => 'Password123',
        ]);
    }

    private function registration(): array
    {
        return [
            'name' => 'New Admin',
            'email' => 'new-admin@example.test',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'codigo_invitacion' => 'test-invitation',
        ];
    }

    private function api(string $method, string $uri, array $data = [], ?string $token = null)
    {
        // Each bearer must be resolved afresh, as it would be in a separate HTTP request.
        $this->app['auth']->forgetGuards();
        $this->app['auth']->shouldUse('web');

        return $this->json($method, $uri, $data, $token ? ['Authorization' => 'Bearer '.$token] : []);
    }
}
