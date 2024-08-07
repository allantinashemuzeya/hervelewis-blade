<?php

namespace App\Http\Controllers;

use App\Facades\AnitaConfig;
use App\Http\Services\Album;
use FFMpeg\FFMpeg;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    //

    public function show($id, Album $album){
        $c_album = $album->getAlbum($id);
        $data  = new \stdClass();
        $data->name = $c_album['attributes']['title'];
        $data->description = $c_album['attributes']['field_description'];
        $data->category = $c_album['attributes']['field_category'];
        $data->media = $c_album['attributes']['media'];
        $data->cover = $c_album['attributes']['cover'];
       // dd($c_album['attributes']['nextAlbum']);
        $nextAlbum = $c_album['attributes']['nextAlbum']['id'];

        $data->nextAlbum = $album->getAlbum($nextAlbum);
        return view('Album/Album', ['id' => $id, 'data' => $data,'config' => AnitaConfig::getConfig()]);
    }
}
