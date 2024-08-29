<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('dashboard', [ 'as' => 'dashboard', 'uses' => 'App\Http\Controllers\MainController@dashboard'])->middleware(['auth']);
Route::post('dashboard', [ 'as' => 'dashboard', 'uses' => 'App\Http\Controllers\MainController@dashboard'])->middleware(['auth']);


Route::post('disable_user', [ 'as' => 'disable_user', 'uses' => 'App\Http\Controllers\MainController@disable_user'])->middleware(['auth']);
Route::get('disable_user', [ 'as' => 'disable_user', 'uses' => 'App\Http\Controllers\MainController@disable_user'])->middleware(['auth']);
Route::post('enable_user', [ 'as' => 'enable_user', 'uses' => 'App\Http\Controllers\MainController@enable_user'])->middleware(['auth']);
Route::get('enable_user', [ 'as' => 'enable_user', 'uses' => 'App\Http\Controllers\MainController@enable_user'])->middleware(['auth']);

Route::post('load_info', [ 'as' => 'load_info', 'uses' => 'App\Http\Controllers\MainController@load_info'])->middleware(['auth']);
Route::get('load_info', [ 'as' => 'load_info', 'uses' => 'App\Http\Controllers\MainController@load_info'])->middleware(['auth']);

//routes from regole lotti
Route::get('rule_lotti', [ 'as' => 'rule_lotti', 'uses' => 'App\Http\Controllers\ControllerLotti@rule_lotti'])->middleware(['auth']);
Route::post('rule_lotti', [ 'as' => 'rule_lotti', 'uses' => 'App\Http\Controllers\ControllerLotti@rule_lotti'])->middleware(['auth']);

Route::post('save_pattern', [ 'as' => 'save_pattern', 'uses' => 'App\Http\Controllers\ControllerLotti@save_pattern'])->middleware(['auth']);
Route::get('save_pattern', [ 'as' => 'save_pattern', 'uses' => 'App\Http\Controllers\ControllerLotti@save_pattern'])->middleware(['auth']);

Route::post('save_inizia', [ 'as' => 'save_inizia', 'uses' => 'App\Http\Controllers\ControllerLotti@save_inizia'])->middleware(['auth']);
Route::get('save_inizia', [ 'as' => 'save_inizia', 'uses' => 'App\Http\Controllers\ControllerLotti@save_inizia'])->middleware(['auth']);

Route::post('load_rules', [ 'as' => 'load_rules', 'uses' => 'App\Http\Controllers\ControllerLotti@load_rules'])->middleware(['auth']);
Route::get('load_rules', [ 'as' => 'load_rules', 'uses' => 'App\Http\Controllers\ControllerLotti@load_rules'])->middleware(['auth']);

Route::post('load_modelli', [ 'as' => 'load_modelli', 'uses' => 'App\Http\Controllers\ControllerLotti@load_modelli'])->middleware(['auth']);
Route::get('load_modelli', [ 'as' => 'load_modelli', 'uses' => 'App\Http\Controllers\ControllerLotti@load_modelli'])->middleware(['auth']);

Route::post('testcodice', [ 'as' => 'testcodice', 'uses' => 'App\Http\Controllers\ControllerLotti@testcodice'])->middleware(['auth']);
Route::get('testcodice', [ 'as' => 'testcodice', 'uses' => 'App\Http\Controllers\ControllerLotti@testcodice'])->middleware(['auth']);

Route::post('dele_rule', [ 'as' => 'dele_rule', 'uses' => 'App\Http\Controllers\ControllerLotti@dele_rule'])->middleware(['auth']);
Route::get('dele_rule', [ 'as' => 'dele_rule', 'uses' => 'App\Http\Controllers\ControllerLotti@dele_rule'])->middleware(['auth']);

//end regole lotti

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
