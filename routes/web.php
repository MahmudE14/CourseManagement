<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


Route::group( ['middleware' => ['auth']], function() {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::resource('courses', 'CourseController')->except(['create', 'edit', 'store', 'update', 'destroy']);
    Route::resource('units', 'UnitController')->except(['create', 'edit', 'store', 'update', 'destroy']);

    Route::get('myCourses', 'CourseController@myCourses');
    Route::get('courses/{course}/details', 'CourseController@courseDetails');
    Route::get('getCourseProgress', 'CourseController@getCourseProgress');
    Route::get('registerInCourse', 'CourseController@registerInCourse');
    Route::get('completeUnit', 'UnitController@completeUnit');

    Route::group( ['middleware' => ['admin']], function() {
        Route::get('admin', function () {
            Route::post('courses', 'CourseController@store');
            Route::put('courses', 'CourseController@update');
            Route::delete('courses', 'CourseController@destroy');

            Route::post('units', 'UnitController@store');
            Route::put('units', 'UnitController@update');
            Route::delete('units', 'UnitController@destroy');
        });
    });
});
