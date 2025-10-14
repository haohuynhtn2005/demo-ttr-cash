<?php

//e tự tạo route restapi để có được endpoint có dạng,

//admin/system-settings
$routes->get('/', 'Home::index');

$routes->group('api', function ($routes) {
  $routes->post('register', 'Api\AuthController::register');
  $routes->post('login', 'Api\AuthController::login');
  $routes->get('profile', 'Api\UserController::profile', ['filter' => 'jwt']);
});
$routes->get('/admin/system-settings', 'Admin\SystemSettingController::index');
$routes->post('/admin/system-settings', 'Admin\SystemSettingController::create');
$routes->get('/admin/system-settings/(:segment)', 'Admin\SystemSettingController::show/$1');
$routes->put('/admin/system-settings/(:segment)', 'Admin\SystemSettingController::update/$1');
$routes->patch('/admin/system-settings/(:segment)', 'Admin\SystemSettingController::update/$1');
$routes->delete('/admin/system-settings/(:segment)', 'Admin\SystemSettingController::delete/$1');
$routes->apiResource(
  'admin/email-histories',
  [
    'controller' => 'Admin\EmailHistoryController',
    'filter' => 'jwt',
    'only' => ['index', 'show', 'create', 'delete']
  ]
);
