<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function index(){
        if (Auth::check()) {
            $role = Auth::user()->getRoleNames()->first(); // returns role name like 'admin'
            return redirect()->route($role . '.dashboard');
        }
        return view('Auth.Login');
    }

    

    public function login(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'email'    => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $remember    = $request->filled('remember'); // support "remember me"
            $credentials = $request->only('email', 'password');

            // Attempt login
            if (!Auth::attempt($credentials, $remember)) {
                return response()->json(['message' => 'Invalid email or password.'], 422);
            }

            $user = Auth::user();

            // Optional: check user active status
            if (method_exists($user, 'isActive') && !$user->isActive()) {
                Auth::logout();
                return response()->json([
                    'message' => 'Your account is inactive. Contact administrator.'
                ], 403);
            }

            // Role-based redirect logic
            $roleRedirectMap = [
                'superAdmin' => route('superAdmin.dashboard'),
                'admin'      => route('Admin.dashboard'),
                'hr'         => route('HR.dashboard'),
                'teamLead'   => route('TeamLead.dashboard'),
                'finance'    => route('Finance.dashboard'),
                'employee'   => route('Employee.dashboard'),
            ];

            $userRole = $user->getRoleNames()->first(); // Assumes single-role per user

            $redirect = $roleRedirectMap[$userRole] ?? route('login');

            return response()->json([
                'status'   => 'success',
                'message'  => 'Login successful.',
                'redirect' => $redirect,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'A system error occurred. Please try again later.'
            ], 500);
        }
    }


    // Optional helper for unknown roles
    protected function handleUnknownRole($user)
    {
        Auth::logout();
        return redirect()->route('login')->withErrors(['role' => 'Your user role is not recognized. Please contact support.']);
    }


    public function logout(Request $request)
    {
        try {
            $user = Auth::user(); // Optional: get user info before logout for logging

            // Perform logout
            Auth::logout();

            // Invalidate session and regenerate CSRF token
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Optional: log activity
            Log::info("User logged out", ['user_id' => optional($user)->id, 'email' => optional($user)->email]);

            // Redirect to login page with success message
            return redirect()->route('login')->with('status', 'You have been logged out successfully.');
        } catch (\Exception $e) {
            Log::error("Logout error: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withErrors(['logout' => 'Something went wrong during logout. Please try again.']);
        }
    }
}
