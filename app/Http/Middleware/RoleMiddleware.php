<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Необходима авторизация');
        }

        /** @var User $user */
        $user = Auth::user();
        
        // Администратор имеет доступ ко всем ролям
        if ($user->isAdmin()) {
            return $next($request);
        }

        $roleMethod = 'is' . ucfirst($role);
        
        if (!method_exists($user, $roleMethod) || !$user->$roleMethod()) {
            abort(403, "Доступ запрещен. Требуются права: {$role}");
        }

        return $next($request);
    }
}
