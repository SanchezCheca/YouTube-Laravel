<?php

namespace App\Http\Controllers;

use App\Models\Contacto;
use App\Models\Notification;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class controladorPrincipal extends Controller
{
    /**
     * Va a inicio comprobando el login
     */
    public function inicio(Request $req)
    {
        //Comprueba si el usuario ha iniciado sesión
        $usuarioIniciado = $this->comprobarLogin();
        $datos = [];
        if ($usuarioIniciado != null) {
            $datos += [
                'usuarioIniciado' => $usuarioIniciado
            ];
        }

        //Recoge los últimos 12 vídeos subidos
        $ultimosVideos = Video::take(12)->orderByDesc('created_at')->get();
        $ultimosVideosConCreatorUsername = [];
        foreach ($ultimosVideos as $video) {    //Añade el nombre del creador a cada vídeo
            $creator = User::find($video->creator_id);
            $video->creatorUsername = $creator->username;
            $video->creatorImageUrl = $creator->publicProfileImageUrl;
            $ultimosVideosConCreatorUsername[] = $video;
        }
        $datos += [
            'ultimosVideos' => $ultimosVideosConCreatorUsername
        ];

        //Recoge los 6 vídeos más vistos
        $videosMasVistos = Video::take(6)->orderByDesc('views')->get();
        $videosMasVistosConCreatorUsername = [];
        foreach ($videosMasVistos as $video) {
            $creator = User::find($video->creator_id);
            $video->creatorUsername = $creator->username;
            $video->creatorImageUrl = $creator->publicProfileImageUrl;
            $videosMasVistosConCreatorUsername[] = $video;
        }
        $datos += [
            'videosMasVistos' => $videosMasVistosConCreatorUsername
        ];

        //Recoge los últimos 6 vídeos de usuarios a los que está suscrito
        if ($usuarioIniciado) {
            $videosSuscripciones = DB::select('SELECT * FROM videos WHERE creator_id IN (SELECT user_following_id FROM user_following WHERE user_id = ?) ORDER BY created_at DESC LIMIT 6', [$usuarioIniciado->id]);
            if (sizeof($videosSuscripciones) > 0) {
                $videosSuscripcionesConCreatorUsername = [];
                foreach ($videosSuscripciones as $video) {    //Añade el nombre del creador a cada vídeo
                    $creator = User::find($video->creator_id);
                    $video->creatorUsername = $creator->username;
                    $video->creatorImageUrl = $creator->publicProfileImageUrl;
                    $videosSuscripcionesConCreatorUsername[] = $video;
                }
                $datos += [
                    'videosSuscripciones' => $videosSuscripcionesConCreatorUsername
                ];
            }
        }

        return view('inicio', $datos);
    }

    /**
     * Lleva a la vista upload
     */
    public function aUpload()
    {
        $usuario = $this->comprobarLogin();
        $datos = [];
        if ($usuario != null) {
            $datos += [
                'usuarioIniciado' => $usuario
            ];
        }
        return view('upload', $datos);
    }

    /**
     * Si ya ha iniciado sesión, vuelve hacia atrás
     */
    public function aLogin(Request $req)
    {
        $usuarioIniciado = $this->comprobarLogin();
        if ($usuarioIniciado) {
            return redirect()->back();
        } else {
            return view('login');
        }
    }

    /**
     * Lleva a la vista register
     */
    public function aRegister()
    {
        $usuarioIniciado = $this->comprobarLogin();
        if ($usuarioIniciado) {
            return redirect()->back();
        } else {
            return view('register');
        }
    }

    /**
     * Lleva a la vista ranking
     */
    public function aRanking() {
        $datos = [];
        $usuarioIniciado = $this->comprobarLogin();
        if ($usuarioIniciado) {
            $datos += [
                'usuarioIniciado' => $usuarioIniciado
            ];
        }

        //Recoge los 6 vídeos más vistos
        $videosMasVistos = Video::take(6)->orderByDesc('views')->get();
        $videosMasVistosConCreatorUsername = [];
        foreach ($videosMasVistos as $video) {
            $creator = User::find($video->creator_id);
            $video->creatorUsername = $creator->username;
            $video->creatorImageUrl = $creator->publicProfileImageUrl;
            $videosMasVistosConCreatorUsername[] = $video;
        }
        $datos += [
            'topVideos' => $videosMasVistosConCreatorUsername
        ];

        //Recoge los 6 canales con más suscriptores
        $topUsers = DB::select('SELECT users.*, COUNT(user_following.user_following_id) AS nSubs FROM users INNER JOIN user_following ON users.id = user_following.user_following_id GROUP BY user_following_id ORDER BY nSubs DESC LIMIT 6');
        $datos += [
            'topUsers' => $topUsers
        ];

        //Recoge los 6 vídeos con más likes
        $topVideosByLikes = DB::select('SELECT videos.*, COUNT(video_likes.video_id) AS nLikes FROM videos INNER JOIN video_likes ON videos.id = video_likes.video_id GROUP BY videos.id ORDER BY nLikes DESC');
        $topVideosByLikesConCreatorUsername = [];
        foreach ($topVideosByLikes as $video) {
            $creator = User::find($video->creator_id);
            $video->creatorUsername = $creator->username;
            $video->creatorImageUrl = $creator->publicProfileImageUrl;
            $topVideosByLikesConCreatorUsername[] = $video;
        }
        $datos += [
            'topVideosByLikes' => $topVideosByLikesConCreatorUsername
        ];

        //Recoge los 6 canales con más vídeos subidos
        $topUsersBynVideos = DB::select('SELECT users.*, COUNT(videos.id) AS nVideos FROM users INNER JOIN videos ON videos.creator_id = users.id GROUP BY users.id ORDER BY nVideos DESC');
        $datos += [
            'topUsersBynVideos' => $topUsersBynVideos
        ];

        return view('ranking', $datos);
    }

    /**
     * Lleva a la vista de contacto
     */
     public function aContacto() {
         $datos = [];
         $usuarioIniciado = $this->comprobarLogin();
         if ($usuarioIniciado) {
            $datos += [
                'usuarioIniciado' => $usuarioIniciado
            ];
         }
         return view('contacto', $datos);
     }

     /**
      * Guarda un contacto
      */
     public function procesarContacto(Request $req) {
        $datos = [];
         $usuarioIniciado = $this->comprobarLogin();
         if ($usuarioIniciado) {
            $datos += [
                'usuarioIniciado' => $usuarioIniciado
            ];
         }

        $contacto = new Contacto();
        $contacto->name = $req->input('name');
        $contacto->email = $req->input('email');
        $contacto->message = $req->input('message');
        $contacto->save();

        $datos += [
            'mensaje' => '¡Gracias!'
        ];

        return view('contacto', $datos);
     }

     /**
      * Lleva a la vista 'notificaciones'
      */
     public function aNotificaciones() {
        $usuarioIniciado = $this->comprobarLogin();
        if ($usuarioIniciado) {
            $datos = [
                'usuarioIniciado' => $usuarioIniciado
            ];

            $notificaciones = Notification::where('user_id','=',$usuarioIniciado->id)->get();
            $notificacionesSinLeer = [];
            $notificacionesLeidas = [];

            //Todas las notificaciones a "visto"
            foreach ($notificaciones as $notificacion) {
                if ($notificacion->leido == 0) {
                    $notificacionesSinLeer[] = $notificacion;
                    $notificacion->leido = 1;
                    $notificacion->save();
                } else {
                    $notificacionesLeidas[] = $notificacion;
                }
            }
            $datos += [
                'notificacionesSinLeer' => $notificacionesSinLeer,
                'notificacionesLeidas' => $notificacionesLeidas
            ];

            return view('notificaciones', $datos);
        } else {
            return redirect()->back();
        }
     }

    //------------------MÉTODOS PRIVADOS
    private function comprobarLogin()
    {
        if (session()->has('usuarioIniciado')) {
            $usuario = session()->get('usuarioIniciado');
            $notificaciones = Notification::where('user_id','=',$usuario->id)->get();
            $usuario->notificaciones = $notificaciones;

            //Comprueba si tiene notificaciones sin leer
            $notifSinLeer = Notification::where('user_id','=',$usuario->id)->where('leido','=',0)->get();
            if (sizeof($notifSinLeer) > 0) {
                $usuario->tieneNotifSinLeer = true;
            } else {
                $usuario->tieneNotifSinLeer = false;
            }

            return $usuario;
        } else {
            return null;
        }
    }
}
