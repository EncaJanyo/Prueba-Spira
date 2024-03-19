<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;


Route::get('/', function () {
    return Inertia::render('Auth/Login');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');


    Route::group(['middleware' => ['role:admin']], function () {

        Route::get('/user', function () {
            return Inertia::render('User/Index');
        })->name('user');

        Route::get('/course', function () {
            return Inertia::render('Course/Index');
        })->name('course');

        Route::get('/list', function () {
            return Inertia::render('User/List');
        })->name('list');

        Route::get('/assign', function () {
            return Inertia::render('Assign/Index');
        })->name('assign');

    });

    Route::group(['middleware' => ['role:student']], function () {

        Route::get('/course-assign', function () {
            return Inertia::render('Student/Index');
        })->name('course-assign');

    });


});


