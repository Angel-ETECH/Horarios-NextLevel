<?php

namespace Tests\Feature;

use App\Http\Middleware\OcultarErroresInternos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ErroresApiTest extends TestCase
{
    public function test_api_middleware_hides_internal_details_in_production(): void
    {
        config(['app.debug' => false]);
        Log::spy();
        Route::middleware('api')->get('/api/prueba-error-interno', fn () => response()->json([
            'success' => false, 'message' => 'SQLSTATE private-database',
            'error' => 'private-smtp-password', 'trace' => ['private-path'],
        ], 500));
        $response = $this->getJson('/api/prueba-error-interno')->assertStatus(500)
            ->assertJsonPath('success', false)->assertJsonPath('error', null)->assertJsonMissingPath('trace');
        $this->assertStringNotContainsString('private-', $response->getContent());
        Log::shouldHaveReceived('error')->once();
    }

    public static function responsesToPreserve(): array
    {
        return [[false, 200], [false, 422], [true, 500]];
    }

    #[DataProvider('responsesToPreserve')]
    public function test_success_validation_and_local_debug_responses_are_preserved(bool $debug, int $status): void
    {
        config(['app.debug' => $debug]);
        $data = ['success' => false, 'message' => 'Mensaje original', 'errors' => ['aula_id' => ['Selecciona un aula.']]];
        $result = (new OcultarErroresInternos())->handle(Request::create('/api/prueba'),
            fn () => response()->json($data, $status));
        $this->assertSame($status, $result->getStatusCode());
        $this->assertSame($data, $result->getData(true));
    }
}
