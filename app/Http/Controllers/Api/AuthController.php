<?php
// app/Http/Controllers/Api/AuthController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/auth/login
     * Iniciar sesión
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Buscar usuario por email
            $user = User::where('email', $request->email)->first();

            // Verificar credenciales
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['Las credenciales proporcionadas son incorrectas.'],
                ]);
            }

            // Eliminar tokens anteriores (opcional)
            $user->tokens()->delete();

            // Crear nuevo token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de autenticación',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * POST /api/auth/register
     * Registrar nuevo administrador (requiere código de invitación)
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $codigoEsperado = config('auth.admin_invitation_code');
            $codigoRecibido = $request->codigo_invitacion;

            if (empty($codigoEsperado) || $codigoRecibido !== $codigoEsperado) {
                return response()->json([
                    'success' => false,
                    'message' => 'El código de invitación es inválido',
                ], 403);
            }

            // Crear usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Crear token automáticamente
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Usuario administrador registrado exitosamente',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/auth/logout
     * Cerrar sesión (revocar token actual)
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revocar el token actual
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada exitosamente',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * GET /api/auth/me
     * Obtener datos del usuario autenticado
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                ],
            ],
        ]);
    }

    /**
     * POST /api/auth/refresh
     * Refrescar token (revocar actual, crear nuevo)
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Revocar token actual
            $user->currentAccessToken()->delete();

            // Crear nuevo token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refrescado exitosamente',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al refrescar token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/auth/forgot-password
     * Enviar email con link de recuperación
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            // Enviar link de recuperación
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email enviado. Revisa tu bandeja de entrada.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo enviar el email. Intenta nuevamente.',
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el email',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * POST /api/auth/reset-password
     * Restablecer contraseña con token
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    // Revocar todos los tokens existentes (seguridad)
                    $user->tokens()->delete();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'success' => true,
                    'message' => 'Contraseña restablecida exitosamente. Ya puedes iniciar sesión.',
                ]);
            }

            // Mapear errores
            $mensajes = [
                Password::INVALID_TOKEN => 'El token es inválido o ha expirado.',
                Password::INVALID_USER => 'No existe una cuenta con este email.',
                Password::RESET_THROTTLED => 'Espera un momento antes de intentarlo nuevamente.',
            ];

            return response()->json([
                'success' => false,
                'message' => $mensajes[$status] ?? 'No se pudo restablecer la contraseña.',
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restablecer la contraseña',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
