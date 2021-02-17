<?php

namespace App\Http\Middleware;

use Closure;
use App;
class PhotoRequestValidator
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
        $token = $request->header("Authorization");
        $token = $request->header("token",$token);
        $name = $request->header("name","");
        if(App\PostSession::isSessionValid($name,$token)){
            return $next($request);
        }else{
            return response([
                "code"=>401,
                "message"=>"Invalid Session"
            ],200);
        }
    }
}
