<?php

namespace App\Http\Middleware;

use Closure;
use App;
use App\Post;
use App\PostSession;
use Illuminate\Http\Request;

class PhotoAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $action)
    {
        $token = $request->header("Authorization");
        $token = $request->header("token", $token);
        $name = $request->input('name');
        $name = $request->header("name", $name);
        $post = Post::GetPost($name);
        $request->merge(['post' => $post]);
        if ($post) {
            if ($action === 'read') {
                if ($post->type == 1 || $post->type == 2 || ($post->type == 3 && PostSession::isSessionValid($name, $token))) {
                    return $next($request);
                }
            } else if ($action == 'write') {
                if ($post->type == 1 || (($post->type == 2 || $post->type == 3) && PostSession::isSessionValid($name, $token))) {
                    return $next($request);
                }
            }
        } else {
            return response(array(
                'code' => 404,
                'message' => 'Album Not Found.',
                'name'=>$name
            ), 200);
        }
        return response(array(
            'code' => 401,
            'message' => 'Permission Denied.'
        ), 200);
    }
}
