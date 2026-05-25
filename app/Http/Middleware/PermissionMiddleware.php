<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Необходима авторизация');
        }

        /** @var User $user */
        $user = Auth::user();
        
        // Администратор имеет все права
        if ($user->isAdmin()) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            abort(403, "Доступ запрещен. Требуется право: {$permission}");
        }

        return $next($request);
    }
}
