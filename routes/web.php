<?php

use App\Http\Controllers\contentController;
use App\Http\Controllers\controladorPrincipal;
use App\Http\Controllers\loginController;
use Illuminate\Support\Facades\Route;



Route::get('register', function() {
    return view('register');
});

Route::get('/login', function() {
    return view('login');
});

//------------PRINCIPAL
Route::get('/', [controladorPrincipal::class, 'inicio']);
Route::get('inicio', [controladorPrincipal::class, 'inicio']);
Route::get('upload', [controladorPrincipal::class, 'aUpload']);

//------------CONTENIDO
Route::get('user/{username}', [contentController::class, 'verCanal']);
Route::get('ajax-file-upload-progress-bar', [contentController::class, 'index']);
Route::post('ajax-file-upload-progress-bar', [contentController::class, 'upload']);

Route::get('videoExample', [contentController::class, 'videoExample']);

//-------------REGISTRO Y LOGIN
Route::post('register', [loginController::class, 'registrarCuenta']);
Route::post('login', [loginController::class, 'iniciarSesion']);
Route::get('cerrarSesion', [loginController::class, 'cerrarSesion']);
