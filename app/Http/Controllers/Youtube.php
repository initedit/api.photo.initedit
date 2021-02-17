<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources;
use Illuminate\Support\Facades\DB;
use App;

class Youtube extends Controller
{
    public function Get(){
        // print_r( DB::select("SELECT CURRENT_TIMESTAMP FROM DUAL"));
        // echo "Hello World!";
        // return DB::select("SELECT CURRENT_TIMESTAMP FROM DUAL");
        return App\Post::all();
    }
}
