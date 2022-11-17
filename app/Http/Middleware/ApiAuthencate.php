<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthencate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(empty($request->header('Authorization'))){
            return response()->json([
                'status'=> 400,
                'message' => 'Authorization token is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        $login_token = $request->header('Authorization');
        $user = User::where('login_token', $login_token)->first();
        if($user){
            $request->merge(array('user' => $user->only(['id', 'name', 'email', 'wallets'])));
            return $next($request);
        }else{
            return response()->json([
                'status'=> 401,
                'message' => 'Unauthorized Token'
            ], Response::HTTP_UNAUTHORIZED);
        }
    }
}
