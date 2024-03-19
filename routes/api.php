<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AssignController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentController;

Route::middleware('auth:sanctum')->group(function () {

    Route::group(['middleware' => ['role:admin']], function () { 

        Route::apiResource('users', UserController::class);
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('assigns', AssignController::class);
        Route::get('roles', [RoleController::class, 'index']);
        Route::get('lists', [UserController::class, 'list']);
        Route::get('users/{id}/assigned-courses', [AssignController::class, 'assign']);
        Route::get('users/{id}/unassigned-courses', [AssignController::class, 'unassign']);

    });

    Route::group(['middleware' => ['role:student']], function () { 

        Route::get('courses-assigns', [StudentController::class, 'index']);
        
    }); 
});
