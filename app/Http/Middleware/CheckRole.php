<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roleIds)
{
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    $userRole = $user->roles()->first();

    if (!$userRole) {
        Log::error('User has no role assigned', ['user_id' => $user->id]);
        return redirect()->route('dashboard')->with('error', 'Aucun rôle attribué.');
    }

    $userRoleId = $userRole->id;
    $roleIds = array_map('intval', $roleIds);
    
    if (!in_array($userRoleId, $roleIds)) {
        Log::warning('Role check failed', [
            'user_id' => $user->id,
            'user_role_id' => $userRoleId,
            'required_roles' => $roleIds
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Accès non autorisé.'], 403);
        }
        return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
    }

    return $next($request);
}
}






