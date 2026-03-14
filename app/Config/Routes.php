<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('api/chat', 'Chat::stream');
$routes->get('api/health', 'Chat::health');
$routes->get('api/wissen/(:segment)', 'Chat::wissen/$1');
