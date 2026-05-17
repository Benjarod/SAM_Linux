<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function home()
    {
        return view('auth/home');
    }

    public function acceso()
    {
        return view("auth/login");
    }

    public function registro()
    {
        return view("auth/registro");
    }

    // Registro para web tradicional (con vistas)
    public function registrar(Request $request)
    {
        $validacion = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'institucion' => 'max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'required|same:password'
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico es inválido.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password_confirmation.required' => 'La confirmación de contraseña es obligatoria.',
            'password_confirmation.same' => 'Las contraseñas deben coincidir.',
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no debe contener más de :max caracteres.',
        ]);

        if ($validacion->fails()) {
            return redirect('registro')
                ->withErrors($validacion)
                ->withInput();
        }

        try {
            $item = new User();
            $item->name = $request->name;
            $item->institucion = $request->institucion;
            $item->email = $request->email;
            $item->password = Hash::make($request->password);
            $item->save();

            return to_route('login');
        } catch (\Exception $e) {
            return redirect('registro')->with('error', 'Ocurrió un error al registrar el usuario');
        }
    }

    // Registro desde API (usado por Angular)
    public function register(Request $request)
    {
        try {
            \Log::info('👉 Iniciando validación de datos');

            $request->validate([
                'name' => 'required|string|max:255',
                'institucion' => 'nullable|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|string|min:8',
            ]);

            \Log::info('✅ Validación completada');

            $user = User::create([
                'name' => $request->name,
                'institucion' => $request->institucion,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            \Log::info('✅ Usuario creado con éxito', ['id' => $user->id]);

            return response()->json([
                'message' => 'Usuario registrado correctamente',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            \Log::error('❌ Error al registrar usuario: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al registrar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Login para interfaz web tradicional
    public function acceder(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->route('login')->with('error', 'Credenciales incorrectas');
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;
        session(['token' => $token]);

        Auth::login($user);

        return redirect()->route('gestionBd')->with('success', '¡Bienvenido ' . $user->name . '!');
    }

    // Login desde Angular (API con Sanctum)
    public function login(Request $request)
    {
        \Log::info('👉 Intentando login vía API', $request->all());

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        \Log::info('✅ Validación completada');

        if (!Auth::attempt($request->only('email', 'password'))) {
            \Log::warning('❌ Falló Auth::attempt', [
                'email' => $request->email,
                'password' => $request->password
            ]);

            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 422);
        }

        $user = Auth::user();

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        \Log::info('🎉 Login exitoso para usuario: ' . $user->email);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();

            return response()->json(['message' => 'Token eliminado. Sesión cerrada correctamente.']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente');
    }
}
