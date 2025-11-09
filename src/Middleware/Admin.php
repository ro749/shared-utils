<?php

namespace Ro749\SharedUtils\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
class Admin
{
    public function handle(Request $request, Closure $next): Response
    {
        if(Gate::allows('is-admin')){
            return $next($request);
        }
        return redirect()->route('login');
    }
}
