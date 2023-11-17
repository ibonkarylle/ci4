<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'MainController::index');
$routes->post('/save', 'MainController::save');
$routes->get('/getUsers', 'MainController::getUsers');
$routes->get('/getAdmins', 'MainController::getAdmins');
$routes->get('/getPpo', 'MainController::getPpo');
$routes->post('/login', 'MainController::login');
$routes->post('/generate', 'MainController::generateExcel');
$routes->post('/upload', 'MainController::upload');
$routes->post('/sendEmail', 'MainController::sendEmail');