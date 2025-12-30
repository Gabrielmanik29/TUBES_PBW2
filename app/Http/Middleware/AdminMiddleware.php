<?php
// app/Http/Middleware/AdminMiddleware.php - VERSI AMAN

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Cek authentication
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            // Get authenticated user
            $user = Auth::user();
            
            // Cek jika user ada dan memiliki role
            if (!$user) {
                return redirect()->route('login');
            }

            // Cek role (pastikan model User punya kolom 'role')
            if (!isset($user->role) || $user->role !== 'admin') {
                abort(403, 'Access denied. Admin privileges required.');
            }

            return $next($request);
            
        } catch (\Exception $e) {
            // Log error jika perlu
            // \Log::error('AdminMiddleware Error: ' . $e->getMessage());
            
            return redirect()->route('login')
                ->with('error', 'Authentication error. Please login again.');
        }
    }
}