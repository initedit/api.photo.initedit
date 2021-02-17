<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App;
use Intervention\Image\ImageManagerStatic as Image;
class PhotoController extends Controller {

    public function Get(Request $request) {
        $jsonResponse = ["code" => 100, "message" => "Unknown Error"];
        $rules = array(
            'page' => 'required|filled|integer|min:1',
        );
        $pageIndex = $request->page;
        $postname = $request->header("name", "");
        $postToken = $request->header("token", "");
        $post = App\Post::GetPost($postname);
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $jsonResponse["message"] = $validator->errors()->first();
        } else if ($post) {
            if ($post->isViewable($postToken)) {
                $id = $post->id;
                $jsonResponse["message"] = "Loaded";
                $jsonResponse["code"] = 200;
                $jsonResponse["result"] = App\PostMeta::Get($id, $pageIndex);
                $jsonResponse["total"] = App\PostMeta::Total($id);
            } else {
                $jsonResponse["code"] = 401;
                $jsonResponse["message"] = "Permission Denied";
            }
        } else {
            $jsonResponse["code"]=404;
            $jsonResponse["message"] = "Post not found";
        }
        return $jsonResponse;
    }

    public function Upload(Request $request) {
        $jsonResponse = ["code" => 100, "message" => "Unknown Error"];
        $rules = array(
            'name' => 'required|max:1024|filled',
            'description' => 'nullable|max:4048',
            'tags' => 'nullable|max:512',
            'image' => 'required|file|max:8040800',
            'extra' => 'nullable|max:4048'
        );
        $messages = [
//            "name.required"=>"Name Required"
        ];
        $postname = $request->header("name", "");
        $postToken = $request->header("token", "");

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $jsonResponse["message"] = $validator->errors()->first();
        } else if (App\Post::Exists($postname, $postToken)) {
            $jsonResponse["message"] = "Post not found";
        } else {
            $userImageName = $request->name;
            $userImageDesc = $request->description;
            $userImageTags = $request->tags;
            $userImageExtra = $request->input("extra", "");
            $post = App\Post::GetPost($postname);
            $postid = $post->id;
            $image = $request->file('image');
            $imageName = str_random(16) . "_" . time() . '.' . $image->getClientOriginalExtension();
            $input['imagename'] = $imageName;

            $relativePath = "images/".date("Y")."/".date("m");

            $destinationPath = public_path($relativePath);
            $originaImageName = "original_".$imageName;
            $thumbImageName = "thumb_".$imageName;
            $bigImageName = "big_".$imageName;
            $userImageSize = $image->getClientSize();
            $returnPath = $image->move($destinationPath, $originaImageName);

            $image_resize = Image::make($returnPath);              
            $image_resize->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $image_resize->save($destinationPath."/".$thumbImageName);


            $image_resize = Image::make($returnPath);              
            $image_resize->resize(1024, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $image_resize->save($destinationPath."/".$bigImageName);


            list($width, $height) = getimagesize($relativePath."/".$originaImageName);
            $imageDetail = [$width, $height];
            $saveImagePath = "/".$relativePath."/" . $originaImageName;
            $saveImagePath = [
                "original"=>"/".$relativePath."/" . $originaImageName,
                "thumb"=>"/".$relativePath."/" . $thumbImageName,
                "big"=>"/".$relativePath."/" . $bigImageName,
            ];
            $saveImagePath = json_encode($saveImagePath);
            if ($imageDetail) {
                $userImageWidth = $imageDetail[0];
                $userImageHeight = $imageDetail[1];
                $postMeta = App\PostMeta::Add($postid, $userImageName, $userImageDesc, $userImageTags, $saveImagePath, $userImageExtra, $userImageWidth, $userImageHeight, $userImageSize);
                App\PostMeta::normalizePostPath($postMeta);
                $jsonResponse["message"] = "Uploaded";
                $jsonResponse["code"] = 200;
                $jsonResponse["result"] = $postMeta;
            }
        }
        return $jsonResponse;
    }
    public function Download(Request $request) {
        $id = $request->id;
        $postMeta = App\PostMeta::GetById($id);
        if($postMeta)
        {
            //PDF file is stored under project/public/download/info.pdf
            $file=public_path($postMeta->path->original);

            $headers = array(
                    'Content-Type', 'application/octate-stream',
                    );
            $extension = \File::extension($file);
            return response()->download($file, $postMeta->name.".".$extension, $headers);
        }
    }
    public function DeletePhoto(Request $request) {
        $jsonResponse = ["code" => 100, "message" => "Unknown Error"];
        $postname = $request->header("name", "");
        $postToken = $request->header("token", "");
        if (!App\Post::Exists($postname, $postToken)) {
            $jsonResponse["message"] = "Permission Denied";
        }else{
            $id = $request->id;
            $postMeta = App\PostMeta::GetById($id);
            if($postMeta)
            {
                $originalImage =public_path( $postMeta->path->original);
                $thumbImage = public_path($postMeta->path->thumb);
                $bigImage = public_path($postMeta->path->big);
                unlink($thumbImage);
                unlink($originalImage);
                unlink($bigImage);
                $postMeta->delete();
                $jsonResponse["message"] = "Deleted";
                $jsonResponse["code"] = 200;
            }
        }
        return $jsonResponse;
    }

    public function Update(Request $request) {
            $jsonResponse = ["code" => 100, "message" => "Unknown Error"];
            $rules = array(
                'id' => 'required|filled',
                'name' => 'required|max:1024|filled',
                'description' => 'nullable|max:4048',
                'tags' => 'nullable|max:512',
                'extra' => 'nullable|max:4048'
            );
            $messages = [];
            $postname = $request->header("name", "");
            $postToken = $request->header("token", "");

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $jsonResponse["message"] = $validator->errors()->first();
            } else if (!App\Post::Exists($postname, $postToken)) {
                $jsonResponse["message"] = "Post not found";
            } else {
                $userImageName = $request->name;
                $userImageDesc = $request->description;
                $userImageTags = $request->tags;
                $userImageExtra = $request->extra;
                $userPostMetaId = $request->id;

                $postMeta = App\PostMeta::GetById($userPostMetaId);
                if($postMeta){
                $postMeta->name = $userImageName;
                $postMeta->description=$userImageDesc;
                $postMeta->tags=$userImageTags;
                $postMeta->extra = $userImageExtra;
                $postMeta->path = json_encode($postMeta->path);
                if($postMeta->save()){
                    $jsonResponse["message"] = "Updated Post Detail";
                    $jsonResponse["code"] = 200;
                    $jsonResponse["result"] = $postMeta;
                }else{
                    $jsonResponse["message"] = "Unable to save";
                    $jsonResponse["code"] = 200;
                    
                }
            }else{
                $jsonResponse["message"] = "Not found";
                $jsonResponse["code"] = 404;
            }
        }
        return $jsonResponse;
    }
}
