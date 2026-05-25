<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Необходима авторизация');
        }

        /** @var User $user */
        $user = Auth::user();
        if (!$user->isStudent() && !$user->isAdmin()) {
            abort(403, 'Доступ запрещен. Требуются права студента');
        }

        return $next($request);
    }
}
