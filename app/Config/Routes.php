<?php

//e tự tạo route restapi để có được endpoint có dạng,

//admin/system-settings
$routes->get('/', 'Home::index');
$routes->get('/admin/system-settings', 'Admin\SystemSettingController::index');

$routes->group('api', function ($routes) {
  $routes->post('register', 'Api\AuthController::register');
  $routes->post('login', 'Api\AuthController::login');
  $routes->get('profile', 'Api\UserController::profile', ['filter' => 'jwt']);
});
