<?php

$routeTypes = ['index','show', 'store', 'update', 'destroy'];

/*Users*/
Route::get('users/authenticate', 'UsersController@authenticate');
Route::resource('users', 'UsersController', ['only' => ['show', 'store', 'update', 'destroy']]);
Route::resource('users.products', 'UsersProductsController', ['only' => $routeTypes]);
Route::resource('users.orders', 'UsersOrdersController', ['only' => $routeTypes]);

/*Orders*/
Route::resource('orders', 'OrdersController', ['only' => ['index', 'show', 'update']]);
Route::resource('orders.products', 'OrdersProductsController', ['only' => $routeTypes]);
