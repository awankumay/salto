<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use Illuminate\Routing\Route;
class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    
    public function handle($request, Closure $next)
    {
        $response = [
            'success' => false,
            'data'    => [],
            'message' => 'User Or Role Not Found'
        ];

        if ($request->isMethod('post')) {
            //@dd($request->input('idUser'));
            if(!empty($request->input('idUser')) || !empty($request->input('id_user'))){
                $getUser = User::find($request->input('idUser'));
                if(empty($getUser)) {
                    return response()->json($response, 500);
                }
                $roleName = $getUser->getRoleNames()[0];
                if(empty($roleName)) {
                    return response()->json($response, 500);
                }
            }
        }else{
            if(!empty($request->id_user) || !empty($request->idUser)){
                $getUser = User::find($request->id_user);
                if(empty($getUser)) {
                    return response()->json($response, 500);
                }
                $roleName = $getUser->getRoleNames()[0];
                if(empty($roleName)) {
                    return response()->json($response, 500);
                }
            }
        }
        return $next($request);
    }
}
