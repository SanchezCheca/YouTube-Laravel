<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Video;
//use Facade\FlareClient\Stacktrace\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Pawlox\VideoThumbnail\Facade\VideoThumbnail;

class contentController extends Controller
{
    //Devuelve la información de un canal
    public function verCanal($username) {

    }

    public function index() {
        return view('upload');
    }

    /**
     * Sube un vídeo :)
     */
    public function upload(Request $req) {
        //Comprueba el usuario logueado
        $usuarioIniciado = $this->comprobarLogin();

        //Guarda el archivo original
        $path = $req->file('archivo')->store('videos','s3');
        $filename = basename($path);

        //Miniatura del vídeo
        $publicPath = 'https://vdm2.s3.eu-west-3.amazonaws.com/videos/' . $filename;
        $thumbnailFilename = substr($filename, 0, strrpos($filename, ".")) . '.jpg';    //Mismo nombre que el vídeo pero con extensión jpg
        VideoThumbnail::createThumbnail($publicPath, public_path(), $thumbnailFilename, 0, 1280, 720);   //Crea la imagen de miniatura y la guarda en local
        Storage::disk('s3')->put('thumbnails/' . $thumbnailFilename, file_get_contents($thumbnailFilename)); //Guarda la miniatura en el servidor s3
        File::delete($thumbnailFilename);    //Elimina la imagen de miniatura guardada en local

        //Guarda en BD
        $video = new Video;
        $video->filename = $filename;
        $video->publicUrl = $publicPath;
        $video->thumbnailFilename = $thumbnailFilename;
        $video->creator_id = $usuarioIniciado->id;
        $video->title = $req->get('titulo');
        $video->description = $req->get('descripcion');
        $video->save();

        //Etiquetas
        $tags = $req->get('etiquetas');
        $tagsSinEspacios = str_replace(' ','',$tags);
        $tagsArray = explode(',',$tagsSinEspacios);
        foreach ($tagsArray as $tag) {
            $tagActual = Tag::where('name','LIKE',$tag)->first();
            if ($tagActual == null) {
                //La etiqueta introducida no existe en BD, se crea
                $tagActual = Tag::create([
                    'name' => $tag
                ]);
            }
            //Se asigna la etiqueta introducida a la publicación
            DB::table('video_tag')->insert([
                'video_id' => $video->id,
                'tag_id' => $tagActual->id
            ]);
        }

        return response()->json(['success'=>$video->filename]);
    }

    public function videoExample() {
        $usuarioIniciado = $this->comprobarLogin();
        $videoActual = Video::where('id',2)->get();

        $datos = [
            'videoInfo' => $videoActual,
            'usuarioIniciado' => $usuarioIniciado
        ];
        Return view('videoExample', $datos);
    }

    //------------------MÉTODOS PRIVADOS
    private function comprobarLogin() {
        if (session()->has('usuarioIniciado')) {
            return session()->get('usuarioIniciado');
        } else {
            return null;
        }
    }
}
