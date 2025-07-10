<?php

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['error' => 'Inicie sesion'], Response::HTTP_UNAUTHORIZED);
})->name('login');
