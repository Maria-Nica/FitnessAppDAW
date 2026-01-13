<?php

// /app/Controllers/UserController.php

// Asiguram ca UserModel si Config sunt disponibile
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Core/config.php';
require_once __DIR__ . '/../Core/Exceptions/ValidationException.php';
require_once __DIR__ . '/../Core/Exceptions/DatabaseException.php';

class UserController {

    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }



    // =========================================================================
    // 1. LOGIN (FARA AJAX) - Ruta POST 'user/login'
    // =========================================================================

    /**
     * Gestioneaza cererea de autentificare directa.
     * Verifica credentialele prin Model, seteaza sesiunea si redirectioneaza.
     */
    public function login(): void {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $_SESSION['error'] = 'Invalid access method.';
            $this->redirectToHome();
            return;
        }

        

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // 1. Apeleaza Modelul pentru verificare
        $result = $this->userModel->verifyLogin($email, $password);

        if ($result['success']) {
            // Autentificare reusita: seteaza variabilele de sesiune
            $user = $result['user'];
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role_id'] = $user['role_id'];
            // Foloseste mesajul Modelului sau un mesaj default de succes
            $_SESSION['success'] = $result['message'] ?? "Autentificare reusita! Bine ai revenit.";
        } else {
            // Autentificare esuata: seteaza mesaj de eroare
            $_SESSION['error'] = $result['message'] ?? "Autentificare esuata. Verificati emailul si parola.";
        }

        // 2. Redirectioneaza catre pagina principala
        $this->redirectToHome();
    }

    // =========================================================================
    // 2. INREGISTRARE  - Ruta POST 'user/register'
    // =========================================================================

    /**
     * Gestioneaza cererea de inregistrare directa.
     * Creeaza un utilizator nou prin Model si redirectioneaza.
     */
    public function register(): void {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $_SESSION['error'] = 'Invalid access method.';
            $this->redirectToHome();
            return;
        }

        

        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // 1. Apeleaza Modelul pentru creare - cu gestionare exceptii
        try {
            $result = $this->userModel->createUser($name, $email, $password);
            
            // Inregistrare reusita: seteaza variabilele de sesiune
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['role_id'] = Config::DEFAULT_ROLE_ID;
            $_SESSION['success'] = $result['message'] ?? "Inregistrare reusita! Acum esti autentificat.";
            
        } catch (ValidationException $e) {
            // Eroare de validare - mesaj prietenos pentru utilizator
            $_SESSION['error'] = $e->getFormattedMessage();
            
        } catch (DatabaseException $e) {
            // Eroare de baza de date - verifica daca e email duplicat
            if ($e->isDuplicateKeyError()) {
                $_SESSION['error'] = "Aceasta adresa de email este deja inregistrata. Folositi un email diferit sau incercati sa va autentificati.";
            } else {
                $_SESSION['error'] = "Inregistrarea a esuat din cauza unei erori tehnice. Va rugam incercati din nou mai tarziu.";
            }
            
        } catch (Exception $e) {
            // Eroare generica - fallback
            $_SESSION['error'] = "A aparut o eroare neasteptata. Va rugam incercati din nou.";
        }

        // 2. Redirectioneaza catre pagina principala
        $this->redirectToHome();
    }

    // =========================================================================
    // 3. DELOGARE - Ruta GET 'logout'
    // =========================================================================

    /**
     * Distruge sesiunea si redirectioneaza.
     */
    public function logout(): void {
        session_destroy();

        // Foloseste o variabila de sesiune temporara pentru a afisa mesaj dupa redirect
        $_SESSION['success'] = "Ati fost deconectat cu succes.";

        $this->redirectToHome();
    }

    // =========================================================================
    // 4. METODA AJUTATOARE
    // =========================================================================

    /**
     * Metoda ajutatoare pentru redirectionare catre pagina principala.
     */
    private function redirectToHome(): void {
        // Foloseste BASE_URL din Config (daca este definit) pentru URL complet
        $url = defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/';

        header("Location: " . $url);
        exit;
    }
}