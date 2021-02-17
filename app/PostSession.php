<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostSession extends Model {

    static function isSessionValid($name, $token) {
        $post = Post::GetPost($name);
        if ($post) {
            return PostSession::where("token", $token)
                            ->where("post_id", $post->id)
                            ->exists();
        }
        return false;
    }

    static function Add($postid) {
        $postSession = new PostSession;
        $postSession->post_id = $postid;
        $postSession->status = 0;
        $postSession->valid_till = 60 * 60 * 12;
        do {
            $postSession->token = str_random(512);
        } while (PostSession::where("token", $postSession->token)->count() > 0);
        $postSession->save();
        return $postSession->token;
    }

    static function DeleteByPostId($postid) {
        PostSession::where("post_id", $postid)
                ->delete();
    }

}
