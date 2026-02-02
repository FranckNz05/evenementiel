<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleDiagnostic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            Log::info('RoleDiagnostic: User not authenticated');
            return $next($request);
        }

        $user = Auth::user();
        $roles = $user->roles()->get();
        
        Log::info('RoleDiagnostic: User roles', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'roles' => $roles->toArray(),
            'route' => $request->route()->getName(),
            'uri' => $request->path()
        ]);

        return $next($request);
    }
}