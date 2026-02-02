<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\OrganizerAccessCode;
use Spatie\Permission\Models\Role;

class VerifyOrganizerAccess
{
    public function handle(Request $request, Closure $next)
    {
        $accessCode = $request->bearerToken();

        if (!$accessCode) {
            return response()->json(['message' => 'Code d\'accès manquant'], 401);
        }

        $organizerCode = OrganizerAccessCode::where('access_code', $accessCode)
            ->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now())
            ->first();

        if (!$organizerCode) {
            return response()->json(['message' => 'Code d\'accès invalide ou expiré'], 403);
        }

        return $next($request);
    }
}
