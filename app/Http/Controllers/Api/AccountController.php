<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App;

class AccountController extends Controller {

    public function Authenticate(Request $request) {
        $jsonResponse = ["code" => 100, "message" => "Unknown Error"];
        if ($request->isMethod("post")) {
            $name = $request->input("name");
            $token = $request->input("token");
            if (App\Post::Exists($name, $token)) {
                $post = App\Post::GetPost($name);
                $generatedToken = App\PostSession::Add($post->id);
                $jsonResponse["message"] = "Success";
                $jsonResponse["code"] = 200;
                $jsonResponse["token"] = $generatedToken;
            } else {
                $jsonResponse["message"] = "Failed Authentication";
            }
        }
        return $jsonResponse;
    }

    public function Info(Request $request) {
        $jsonResponse = ["code" => 100, "message" => "Unknown Error"];
        if ($request->isMethod("post")) {
            $name = $request->input("name");
            $post = App\Post::GetPost($name);
            if ($post) {
                $jsonResponse["code"] = 200;
                $jsonResponse["message"] = "Exists";
                $jsonResponse["type"] = $post->getStringType();
            } else {
                $jsonResponse["code"] = 404;
                $jsonResponse["message"] = "does not exists";
            }
        }
        return $jsonResponse;
    }

    public function Create(Request $request) {
        $jsonResponse = ["code" => 100, "message" => "Unknown Error"];
        if ($request->isMethod("post")) {
            $name = $request->input("name");
            $token = $request->input("token");
            $type = $request->input("type", 1);
            if (!App\Post::Exists($name, $token)) {
                if (App\Post::Add($name, $token, $type)) {
                    $post = App\Post::GetPost($name);
                    $jsonResponse["token"] = App\PostSession::Add($post->id);
                    $jsonResponse["message"] = "Created";
                    $jsonResponse["code"] = 200;
                } else {
                    $jsonResponse["message"] = "Sorry! We are not able to successfully process";
                }
            } else {
                $jsonResponse["message"] = "Permission Denied(Already Exists)";
            }
        }
        return $jsonResponse;
    }

    public function Delete(Request $request) {
        $jsonResponse = ["code" => 100, "message" => "Unknown Error"];
        if ($request->isMethod("post")) {
            $name = $request->input("name");
            $token = $request->input("token");
            if (App\Post::Exists($name, $token)) {
                $post = App\Post::GetPost($name);
                //TODO Also Delete all media files hold by these posts
                App\PostSession::DeleteByPostId($post->id);
                $post->delete();
                $jsonResponse["message"] = "Success";
                $jsonResponse["code"] = 200;
            } else {
                $jsonResponse["message"] = "Failed Authentication";
            }
        }
        return $jsonResponse;
    }

}
