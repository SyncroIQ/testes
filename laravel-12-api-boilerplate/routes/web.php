<?php

use App\Enums\Category;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

// Health/Root route for Codespaces probes
Route::get('/', function () {
    return response('OK', 200);
});

Route::get('/test', function () {
    return view('welcome');
});

Route::get('/welcome', fn(Request $request) => dump($request->ip()));
// Route::get($uri, $callback);

Route::get('/category/{category}', function(Category $category) {
            return 'Category Name: ' . $category->value;
        });