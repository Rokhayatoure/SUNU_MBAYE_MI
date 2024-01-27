<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$role): Response
    {
        // dd($role);
        if($request->user()->role->nom_role == $role)
        {
           return $next($request);
        }
        return response()->json(['message' => 'Accès non autorisé'], 403);
    }
}
