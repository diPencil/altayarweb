<?php

use Illuminate\Support\Facades\Route;

Route::post('fawaterk', 'Fawaterk\ProcessController@ipn')->name('Fawaterk');
Route::post('fawaterk_jsonex', 'Fawaterk\ProcessController@ipn')->name('FawaterkJson');
