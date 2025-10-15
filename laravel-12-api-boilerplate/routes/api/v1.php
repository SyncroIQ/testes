<?php

use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['version' => 'v1', 'message' => 'API is working']);
});

Route::get('/site-setting', function () {
    // return  app()->make('SiteSetting')->get('header_logo', 'default.png');
    return  app()->make('SiteSetting')->all();
});


/**
 * When would you ever manually interact with the container?
 * 
 * First, if you write a class that implements an interface and you wish to type-hint that interface on a route or class constructor, you must tell the container how to resolve that interface.
 * 
 * Secondly, if you are writing a Laravel package that you plan to share with other Laravel developers, you may need to bind your package's services into the container.
 */