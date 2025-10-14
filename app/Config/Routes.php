<?php

//e tự tạo route restapi để có được endpoint có dạng,

//admin/system-settings
$routes->get('/', 'Home1::index');
$routes->get('/admin/system-settings', 'Setting::index');
