<?php

// /app/Controllers/CoreController.php


class CoreController {

    /**
     * Metoda implicita (default) pentru a afisa pagina principala (Home).
     * Aceasta este asociata cu ruta GET '/'.
     */
    public function index() {

        $viewPath = __DIR__ . '/../views/home.php';

        if (file_exists($viewPath)) {
            
            require_once $viewPath;

        } else {
            // View-ul nu a fost gasit
            http_response_code(500);
            echo "Error 500: Primary view file not found.";
        }
    }

    /**
     * Metoda optionala pentru a afisa View-uri statice simple (ex: About, Contact).
     * @param string $page Numele fisierului View (ex: 'about.php')
     */
    public function show($page) {
        $viewPath = __DIR__ . '/../../views/' . $page . '.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            http_response_code(404);
            echo "404 Not Found.";
        }
    }

    /**
     * Afiseaza pagina cu retete
     */
    public function retete() {
        $viewPath = __DIR__ . '/../views/retete.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            http_response_code(500);
            echo "Error 500: Retete view file not found.";
        }
    }

    /**
     * Afiseaza pagina cu antrenamente
     */
    public function antrenamente() {
        $viewPath = __DIR__ . '/../views/antrenamente.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            http_response_code(500);
            echo "Error 500: Antrenamente view file not found.";
        }
    }

    /**
     * Afiseaza pagina cu orar
     */
    public function orar() {
        $viewPath = __DIR__ . '/../views/orar.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            http_response_code(500);
            echo "Error 500: Orar view file not found.";
        }
    }
}