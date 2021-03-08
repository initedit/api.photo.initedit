<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class PostMeta extends Model
{
    static $pageSize = 15;
    static function Get($postid, $filter)
    {
        $pageIndex = 1;
        $pageSize = PostMeta::$pageSize;
        if (array_key_exists('pageSize', $filter) && is_numeric($filter['pageSize'])) {
            if ((int) $filter['pageSize'] > 0 && (int) $filter['pageSize'] < 200) {
                $pageSize = (int) $filter['pageSize'];
            }
        }
        if (array_key_exists('page', $filter) && is_numeric($filter['page'])) {
            if ((int) $filter['page'] > 0) {
                $pageIndex = (int) $filter['page'];
            }
        }
        $postsQuery = PostMeta::where("post_id", $postid)
            ->orderBy("id", "desc")
            ->offset(($pageIndex - 1) * $pageSize)
            ->limit($pageSize);

        if (array_key_exists('query', $filter) && !empty($filter['query'])) {
            $postsQuery->where(function ($q) use ($filter) {
                $q->where('name', 'ilike', '%' . $filter['query'] . '%')
                    ->orWhere('description', 'ilike', '%' . $filter['query'] . '%');
            });
        }
        if (array_key_exists('tags', $filter) && !empty($filter['tags'])) {
            $tagsArray = explode(',', $filter['tags']);
            $postsQuery->where(function ($q) use ($tagsArray) {
                foreach ($tagsArray as $tag) {
                    $q->orWhere('tags', '=', $tag);
                }
            });
        }
        if (array_key_exists('minHeight', $filter) && is_numeric($filter['minHeight'])) {
            if ((int) $filter['minHeight'] >= 0) {
                $number = (int) $filter['minHeight'];
                $postsQuery->where('height', '>=', $number);
            }
        }
        if (array_key_exists('maxHeight', $filter) && is_numeric($filter['maxHeight'])) {
            if ((int) $filter['maxHeight'] >= 0) {
                $number = (int) $filter['maxHeight'];
                $postsQuery->where('height', '<=', $number);
            }
        }
        if (array_key_exists('minWidth', $filter) && is_numeric($filter['minWidth'])) {
            if ((int) $filter['minWidth'] >= 0) {
                $number = (int) $filter['minWidth'];
                $postsQuery->where('width', '>=', $number);
            }
        }
        if (array_key_exists('maxWidth', $filter) && is_numeric($filter['maxWidth'])) {
            if ((int) $filter['maxWidth'] >= 0) {
                $number = (int) $filter['maxWidth'];
                $postsQuery->where('width', '<=', $number);
            }
        }
        if (array_key_exists('minSize', $filter) && is_numeric($filter['minSize'])) {
            if ((int) $filter['minSize'] >= 0) {
                $number = (int) $filter['minSize'];
                $postsQuery->where('size', '>=', $number);
            }
        }
        if (array_key_exists('maxSize', $filter) && is_numeric($filter['maxSize'])) {
            if ((int) $filter['maxSize'] >= 0) {
                $number = (int) $filter['maxSize'];
                $postsQuery->where('size', '<=', $number);
            }
        }

        $posts = $postsQuery->get();
        foreach ($posts as $post) {
            PostMeta::normalizePostPath($post);
        }
        return array('items' => $posts, 'total' => $postsQuery->count());
    }
    static function GetByIdDetails($postid)
    {
        $post = PostMeta::where("id", $postid)
            ->orderBy("id", "desc")
            ->first();

        PostMeta::normalizePostPath($post);
        $previousPost = null;
        $nextPost = null;

        if ($post) {
            $previousPost = PostMeta::where('post_id', $post->post_id)
                ->where('id', '<', $post->id)
                ->orderBy('id', 'desc')
                ->first();
            if ($previousPost) {
                PostMeta::normalizePostPath($previousPost);
            }
            $nextPost = PostMeta::where('post_id', $post->post_id)
                ->where('id', '>', $post->id)
                ->orderBy('id', 'asc')
                ->first();
            if ($nextPost) {
                PostMeta::normalizePostPath($nextPost);
            }
        }
        return array(
            'item' => $post,
            'previous' => $previousPost,
            'next' => $nextPost,
        );
    }
    static function GetById($id)
    {
        $posts = PostMeta::where("id", $id)
            ->orderBy("id", "desc")
            ->get();
        foreach ($posts as $post) {
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

    static function Add($post_id, $name, $description, $tags, $path, $extra, $width, $height, $size)
    {
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
