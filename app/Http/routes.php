<?php

$routeTypes = ['index','show', 'store', 'update', 'destroy'];

/*Users*/
Route::get('users/authenticate', 'UsersController@authenticate');
Route::resource('users', 'UsersController', ['only' => ['show', 'store', 'update', 'destroy']]);
Route::resource('users.products', 'UsersProductsController', ['only' => $routeTypes]);
Route::resource('users.orders', 'UsersOrdersController', ['only' => $routeTypes]);

/*Orders*/
Route::get('orders/count/{order_id?}', 'OrdersController@count');
Route::get('product/nfc/{nfc_id}', 'OrdersController@nfc');
Route::resource('orders', 'OrdersController', ['only' => ['index', 'show', 'update']]);
Route::resource('orders.products', 'OrdersProductsController', ['only' => $routeTypes]);

/*Location*/
Route::resource('locations', 'LocationsController', ['only' => ['index', 'show', 'update']]);
