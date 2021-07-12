<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        <?php if (isset($video)) {
            echo $video->title . ' - YT-copia';
        } else {
            echo 'Vídeo no encontrado - YT-copia';
        }
        ?>
    </title>
    <link href="//vjs.zencdn.net/5.4.6/video-js.min.css" rel="stylesheet">
    @include('estilos')
</head>

<body>
    @include('navbar')

    <?php if (isset($video)) { ?>
    <div class="row mx-0 px-5 mt-4">
        <div class="col-12 col-lg-8">
            <!-- CONTENIDO DE VÍDEO, TÍTULO Y DESCRIPCIÓN -->
            <div class="row">
                <div class="col-12">
                    <!-- Reproductor -->
                    <video class="video-js vjs-default-skin vjs-big-play-centered cajaVideo" controls preload="auto"
                        controlsList="nodownload" data-setup='{"example_option":true}'>
                        <source src="{{ $video->publicUrl }}" type="video/mp4" />
                        <p class="vjs-no-js">Si no puedes ver este vídeo es que tienes un navegador muy antigüo :)</p>
                    </video>
                </div>
                <div class="col-12 mt-2">
                    <p class="h3">
                        {{ $video->title }}
                    </p>
                    <p>
                        {{ $video->views }} visualizaciones&nbsp;&nbsp;
                        <?php
                        //Likes
                        if (isset($hasLiked) && $hasLiked) {
                            ?>
                            <a href="{{url('likeVideo/' . $video->filename)}}" class="text-dark" style="text-decoration: none"><i class="fas fa-thumbs-up"></i> {{$video->likes}}</a>
                            <?php
                        } else {
                            ?>
                            <a href="{{url('likeVideo/' . $video->filename)}}" class="text-dark" style="text-decoration: none"><i class="far fa-thumbs-up"></i> {{$video->likes}}</a>
                            <?php
                        }

                        echo ' &nbsp; ';

                        //Dislikes
                        if (isset($hasDisliked) && $hasDisliked) {
                            ?>
                            <a href="{{url('dislikeVideo/' . $video->filename)}}" class="text-dark" style="text-decoration: none"><i class="fas fa-thumbs-down"></i> {{$video->dislikes}}</a>
                            <?php
                        } else {
                            ?>
                            <a href="{{url('dislikeVideo/' . $video->filename)}}" class="text-dark" style="text-decoration: none"><i class="far fa-thumbs-down"></i> {{$video->dislikes}}</a>
                            <?php
                        }

                        ?>
                        <?php
                        //dd($video)
                        ?>
                    </p>
                    <hr>
                    <p>
                        {{$video->description}}
                    </p>
                </div>
            </div>


        </div>
        <div class="col-12 col-lg-4">
            <div class="row">
                <div class="col-12">
                    <h4 class="h4">
                        Vídeos recomendados
                    </h4>
                </div>
                <!--TARJETAS DE VÍDEOS RECOMENDADOS -->
                <?php
                if (isset($videosRecomendados)) {
                    foreach ($videosRecomendados as $videoRec) {
                        ?>
                <div class="col-12 mt-2">
                    <a href="{{ url('video/' . $videoRec->filename) }}"><img
                            src="{{ 'https://vdm2.s3.eu-west-3.amazonaws.com/thumbnails/' . $videoRec->thumbnailFilename }}"
                            width="120vh" class="float-left mr-2"></a>
                    <p class="my-0 tituloVideoRecomendado text-truncate">
                        <a href="{{ url('video/' . $videoRec->filename) }}">{{ $videoRec->title }}</a>
                    </p>
                    <p class="my-0 canalVideoRecomendado text-truncate">
                        <a
                            href="{{ url('user/' . $videoRec->creatorUsername) }}">{{ $videoRec->creatorUsername }}</a>
                    </p>
                    <p class="my-0 viewsVideoRecomendado">
                        {{ $videoRec->views }} visualizaciones
                    </p>
                </div>
                <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <?php } else {
        ?>
    <p class="mx-auto">
        El vídeo no se encuentra, <a href="inicio">volver a inicio.</a>
    </p>
    <?php
    } ?>

    <script src="//vjs.zencdn.net/5.4.6/video.min.js"></script>
    @include('scripts')
</body>

</html>
