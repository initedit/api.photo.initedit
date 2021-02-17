<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

class PostMeta extends Model {
    static $pageSize = 15;
    static function Get($postid, $pageIndex) {
        $posts = PostMeta::where("post_id", $postid)
                ->orderBy("id","desc")
                ->offset(($pageIndex-1) * PostMeta::$pageSize)
                ->limit(PostMeta::$pageSize)
                ->get();
        foreach($posts as $post){
            // $post->path = Storage::url($post->path);
          PostMeta::normalizePostPath($post);
        }
        return $posts;
    }
    static function GetById($id) {
        $posts = PostMeta::where("id", $id)
                ->orderBy("id","desc")
                ->get();
        foreach($posts as $post){
        $post->path = json_decode($post->path);
        }
        return $posts[0];
    }
    static function normalizePostPath($post)
    {
        $post->path = json_decode($post->path);
        $post->path->original = URL::to($post->path->original);
        $post->path->thumb = URL::to($post->path->thumb);
        $post->path->big = URL::to($post->path->big);
    }

    static function Total($postid) {
        return PostMeta::where("post_id", $postid)->count();
    }

    static function Add($post_id, $name, $description, $tags, $path, $extra, $width, $height, $size) {
        $postMeta = new PostMeta;
        $postMeta->post_id = $post_id;
        $postMeta->name = $name;
        $postMeta->description = $description;
        $postMeta->tags = $tags;
        $postMeta->path = $path;
        $postMeta->extra = $extra;
        $postMeta->width = $width;
        $postMeta->height = $height;
        $postMeta->size = $size;
        $postMeta->status = 0;
        $postMeta->save();
        return $postMeta;
    }

}
