<?php
// /app/Core/Router.php

class Router {
    protected $routes = [];

    /**
     * Adaugă o rută GET în lista de rute.
     */
    public function get($uri, $controllerAction) {
        $this->routes['GET'][$uri] = $controllerAction;
    }

    /**
     * Adaugă o rută POST (pentru formulare/AJAX) în lista de rute.
     */
    public function post($uri, $controllerAction) {
        $this->routes['POST'][$uri] = $controllerAction;
    }

    /**
     * Procesează URL-ul primit și apelează Controller-ul/Metoda corespunzătoare.
     */
    public function dispatch() {
        // 1. Preia calea URL-ului (inclusiv subdirectorul)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        // Preia metoda HTTP (GET sau POST)
        $method = $_SERVER['REQUEST_METHOD'];

        // ====================================================================
        // CORECȚIE CRITICĂ: Curățarea prefixului aplicației
        // ====================================================================

        // Obține calea de bază din Config::BASE_URL
        if (defined('Config::BASE_URL')) {
            $basePath = parse_url(Config::BASE_URL, PHP_URL_PATH);
        } else {
            // Cale implicită, dacă BASE_URL nu este definit (Ar trebui să fie definit în config.php)
            $basePath = '/fitnessapp/public/';
        }

        // Elimină calea de bază din URI (ex: scoate /fitnessapp/public/ din /fitnessapp/public/user/login)
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        // Curăță barele oblice (slash-urile) de la început și sfârșit
        $uri = trim($uri, '/');
        // ====================================================================

        // Verifică dacă ruta există pentru metoda respectivă
        if (array_key_exists($uri, $this->routes[$method])) {
            $controllerAction = $this->routes[$method][$uri];

            // controllerAction ar trebui să fie de forma "ControllerClass@methodName"
            list($controllerName, $methodName) = explode('@', $controllerAction);

            // 1. Instanțiază Controllerul
            // Verifică dacă fișierul Controller există înainte de a-l folosi
            $controllerPath = __DIR__ . '/../Controllers/' . $controllerName . '.php';
            if (!file_exists($controllerPath)) {
                http_response_code(404);
                die("Controller file not found: " . $controllerPath);
            }
            require_once $controllerPath;

            // 2. Execută Metoda
            $controller = new $controllerName();
            if (method_exists($controller, $methodName)) {
                // Returnează rezultatul metodei
                return $controller->$methodName();
            } else {
                http_response_code(404);
                die("Method not found in Controller: " . $controllerName . '->' . $methodName);
            }

        } else {
            // Ruta nu a fost găsită (404 Not Found)
            http_response_code(404);
            die("404 Not Found: The requested URL was not found on this server.");
        }
    }
}