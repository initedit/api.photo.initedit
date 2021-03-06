<?php

namespace App\Http\Middleware;

use Closure;
use App\PostMeta;
use Illuminate\Http\Request;

class PhotoMetaAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $postMeta = PostMeta::GetById($request->input('id'));
        if ($request->post->id == $postMeta->post_id) {
            $request->merge(['postMeta' => $postMeta]);
            return $next($request);
        }
        return response(array(
            'code' => 401,
            'message' => 'Permission denied'
        ), 200);
    }
}
