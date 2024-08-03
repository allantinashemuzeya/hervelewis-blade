<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlbumController extends Controller
{
    //

    public function show($id){
        dd($id);
        return view('album', ['id' => $id]);
    }
}
