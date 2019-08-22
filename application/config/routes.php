<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['categories/(:any)'] = 'categories/index/$1';

$route['assets'] = 'assets/index';

$route['users'] = 'users';
$route['users/get/(:num)'] = 'users/get/$1';
$route['users/edit/(:num)'] = 'users/edit/$1';
$route['users/delete/(:num)'] = 'users/delete/$1';
$route['users/editpassword/(:num)'] = 'users/editpassword/$1';
