<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class AuthController extends Controller
{
    public function index()
    {
        return view('home');
    }
    public function registerForm() {
        return view('auth.register');
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        if ($user) {
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Registration Successful! Please login.',
                'redirect' => url('/login')
            ], 200);
        }
        return response()->json([
            'status' => 500,
            'success' => false,
            'message' => 'Something went wrong. Please try again.'
        ], 500);
    }

    public function loginForm() {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401); 
        }
        Auth::login($user);
        return response()->json([
            'success'  => true,
            'message'  => 'Login successful! Redirecting...',
            'redirect' => url('/dashboard')
        ], 200); // OK
    }

    public function logout() {
        Auth::logout();
        return redirect('/login');
    }
}