<?php

Route::get('/', 'FbGraphController@index');
Route::get('facebook/login', 'FbGraphController@index');
Route::get('facebook/callback', 'FbGraphController@callback');
