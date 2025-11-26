<?php


// 1. Setează calea de bază pentru a putea include fișierele din afara directorului public
define('ROOT_PATH', dirname(__DIR__));


// 2. Încarcă Routerul: Calea ar trebui să fie ROOT_PATH/app/Core/Router.php
require_once ROOT_PATH . '/app/Core/Router.php'; // <--- VERIFICĂ CALEA AICI!

// =========================================================================
// 3. DEFINIREA RUTELOR APLICAȚIEI
// =========================================================================

$router = new Router();

$router->get('fitnessapp/public', 'CoreController@index'); // Ruta pentru accesul direct la /public/
$router->get('fitnessapp/public/', 'CoreController@index'); // Adăugați și varianta cu slash

// Rută GET pentru Pagina Principală (View-ul index.php)
// Controller-ul ar trebui să fie CoreController care încarcă View-ul index.php
$router->get('', 'CoreController@index');

// Rută GET pentru Logout
$router->get('logout', 'UserController@logout');

// Rută POST pentru Logare
$router->post('user/login', 'UserController@login');

// Rută POST pentru Înregistrare
$router->post('user/register', 'UserController@register');

// Rută GET pentru pagina Retete
$router->get('retete', 'CoreController@retete');

// Rută GET pentru pagina Antrenamente
$router->get('antrenamente', 'CoreController@antrenamente');

// =========================================================================
// 4. INIȚIALIZAREA ȘI RULAREA APLICAȚIEI
// =========================================================================

$router->dispatch();