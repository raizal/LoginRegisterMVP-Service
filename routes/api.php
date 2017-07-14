<?php

// Register
Route::post('/register', 'UserController@register');

// Login
Route::post('/login', 'UserController@login');

// Logout
Route::post('/logout', 'UserController@logout');
