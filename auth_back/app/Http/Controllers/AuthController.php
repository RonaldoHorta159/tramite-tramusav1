<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Obtener un JWT con credenciales.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string',
            'password' => 'required|string',
        ], [
            'identifier.required' => 'Correo o DNI es obligatorio',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        // ¿Email o DNI?
        $field = filter_var($request->identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'dni';

        $credentials = [
            $field => $request->identifier,
            'password' => $request->password,
        ];

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'error' => 'Credenciales incorrectas. Verifica correo/DNI y contraseña'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->respondWithToken($token);
    }


    public function unauthorized()
    {
        return redirect()->route('login');
    }

    /**
     * Registrar un nuevo usuario.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email|unique:users,email',
            'dni' => 'required|string|size:8|unique:users,dni',
            'password' => 'required|string|min:8|max:255',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'name.min' => 'El nombre debe tener al menos 2 caracteres',
            'name.max' => 'El nombre no puede tener más de 100 caracteres',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser un email válido',
            'email.unique' => 'El email ya está registrado',
            'dni.required' => 'El DNI es obligatorio',
            'dni.size' => 'El DNI debe tener exactamente 8 caracteres',
            'dni.unique' => 'El DNI ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $user = User::create([
            'name' => htmlspecialchars($request->input('name')),
            'email' => htmlspecialchars($request->input('email')),
            'dni' => htmlspecialchars($request->input('dni')),
            'password' => Hash::make($request->input('password')),
            'rol' => 'usuario', // valor por defecto en la tabla
            // 'estado' no es necesario; usa el DEFAULT = 1 de la BD
        ]);

        if (!$user) {
            return response()->json(['error' => 'No se logró crear el usuario'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json($user, Response::HTTP_CREATED);
    }

    /**
     * Obtener el usuario autenticado.
     */
    public function me(Request $request)
    {
        return response()->json(auth()->user());
    }

    /**
     * Cerrar sesión (invalidar token).
     */
    public function logout()
    {
        auth()->logout();

        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json(['error' => 'Token no encontrado'], Response::HTTP_BAD_REQUEST);
            }

            JWTAuth::invalidate($token);

            return response()->json(['message' => 'Sesión cerrada correctamente'], Response::HTTP_OK);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cerrar sesión'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Refrescar un token.
     */
    public function refresh()
    {
        try {
            $token = JWTAuth::getToken();
            if (!$token) {
                return response()->json(['error' => 'Token no encontrado'], Response::HTTP_BAD_REQUEST);
            }

            $newToken = auth()->refresh();

            return $this->respondWithToken($newToken);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo refrescar el token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Estructura de respuesta con token.
     */
    protected function respondWithToken(string $token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ], Response::HTTP_OK);
    }
}
