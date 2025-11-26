<?php

// /app/Controllers/CoreController.php

// NOTĂ: Dacă folosiți o clasă de bază (BaseController) care gestionează includerea View-urilor,
// ați moșteni acea clasă. Deocamdată, vom include View-ul direct.

class CoreController {

    /**
     * Metodă implicită (default) pentru a afișa pagina principală (Home).
     * Aceasta este asociată rutei GET '/'.
     */
    public function index() {

        // 1. Logica Model: De obicei, aici ați apela un Model pentru a prelua date
        // (ex: lista de clase de fitness, știri, etc.), dar pentru pagina Home,
        // s-ar putea să nu fie necesară nicio logică.
        // $classes = $this->classModel->getAllClasses();

        // 2. Încărcarea View-ului: Controller-ul include View-ul corect

        // Calea către View-ul principal.
        // Asigură-te că PATH-ul este corect față de locația acestui Controller.
        $viewPath = __DIR__ . '/../views/home.php';

        if (file_exists($viewPath)) {
            // Dacă ați avea date de la Model ($classes), le-ați putea trece aici
            // prin intermediul unui "renderer" sau prin definirea unor variabile locale.

            // ATENȚIE: View-ul index.php va avea acces la variabilele de sesiune,
            // deoarece acestea sunt gestionate în Front Controller sau în CoreController.

            require_once $viewPath;

        } else {
            // View-ul nu a fost găsit
            http_response_code(500);
            echo "Error 500: Primary view file not found.";
        }
    }

    /**
     * Metodă opțională pentru a afișa View-uri statice simple (ex: About, Contact).
     * @param string $page Numele fișierului View (ex: 'about.php')
     */
    public function show($page) {
        $viewPath = __DIR__ . '/../../views/' . $page . '.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // Dacă pagina nu există, afișează 404
            http_response_code(404);
            // Puteți include un View 404 dedicat aici
            echo "404 Not Found.";
        }
    }

    /**
     * Afișează pagina cu retete
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
     * Afișează pagina cu antrenamente
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
}