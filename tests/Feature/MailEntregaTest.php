<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class MailEntregaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null,
            'app.frontend_url' => null,
            'app.url' => 'https://school.example.test',
            'mail.default' => 'array',
            'queue.default' => 'database',
            'queue.connections.database.connection' => 'sqlite',
        ]);
        DB::purge('sqlite');
        foreach (['0001_01_01_000000_create_users_table.php', '0001_01_01_000002_create_jobs_table.php'] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
    }

    public function test_reset_notification_renders_a_real_mime_email_without_a_worker(): void
    {
        $user = $this->user();
        $this->postJson('/api/auth/forgot-password', ['email' => $user->email])
            ->assertOk()->assertJsonPath('success', true);
        $messages = Mail::mailer()->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);
        $email = $messages->first()->getOriginalMessage();
        $this->assertSame($user->email, $email->getTo()[0]->getAddress());
        $this->assertStringContainsString('Next Level School', $email->getSubject());
        $html = html_entity_decode($email->getHtmlBody());
        $this->assertMatchesRegularExpression('~https://school\.example\.test/reset-password\?token=[^"\s]+~', $html);
        $this->assertStringContainsString('email=qa-navegador%40example.test', $html);
        $this->assertNotEmpty($email->getTextBody());
        $this->assertDatabaseCount('jobs', 0);
        $this->assertDatabaseCount('password_reset_tokens', 1);
    }

    public function test_database_worker_delivers_queued_mail_and_removes_the_job(): void
    {
        $this->user()->notify(new QueuedResetProbe('test-queue-token'));
        $this->assertDatabaseCount('jobs', 1);
        $this->assertCount(0, Mail::mailer()->getSymfonyTransport()->messages());
        $this->artisan('queue:work', ['connection' => 'database', '--once' => true, '--tries' => 1])->assertExitCode(0);
        $this->assertDatabaseCount('jobs', 0);
        $this->assertDatabaseCount('failed_jobs', 0);
        $this->assertCount(1, Mail::mailer()->getSymfonyTransport()->messages());
    }

    public function test_mail_failure_returns_an_error_without_exposing_transport_details(): void
    {
        $user = $this->user();
        config(['app.debug' => false]);
        Mail::shouldReceive('mailer')->andThrow(new TransportException('private-smtp-host secret-password'));
        $response = $this->postJson('/api/auth/forgot-password', ['email' => $user->email])
            ->assertStatus(500)->assertJsonPath('success', false)->assertJsonPath('error', null);
        $this->assertStringNotContainsString('private-smtp-host', $response->getContent());
        $this->assertStringNotContainsString('secret-password', $response->getContent());
    }

    private function user(): User
    {
        return User::create(['name' => 'PRUEBA correo', 'email' => 'qa-navegador@example.test', 'password' => 'Password123!']);
    }
}

class QueuedResetProbe extends ResetPasswordNotification implements ShouldQueue
{
}
