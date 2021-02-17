<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App;
class Post extends Model {

    function isViewable($token){
        if($this->type===1 || $this->type===2){
            return true;
        }else if($this->type===3 && App\PostSession::isSessionValid($this->name,$token)){
            return true;
        }
        return false;
    }
    static function Exists($name, $token) {
        $count = Post::where('name', $name)
                ->where("password", $token)
                ->where("status", 0)
                ->count();
        return $count === 1;
    }

    static function GetPost($name) {
        $post = Post::where('name', $name)
                ->where("status", 0)
                ->first();
        return $post;
    }

    static function Add($name, $token, $type) {
        $post = new Post;
        $post->name = $name;
        $post->password = $token;
        $post->status = 0;
        $post->type = $type;
        return $post->save();
    }
    public function getStringType(){
        switch($this->type)
        {
            case 1:return "Public";break;
            case 2:return "Protected";break;
            case 3:return "Private";break;
        }
        return "Unknown";
    }

}
