<?php
// /app/Controllers/RecipeController.php

require_once __DIR__ . '/../Models/RecipeModel.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Core/Exceptions/ValidationException.php';
require_once __DIR__ . '/../Core/Exceptions/DatabaseException.php';

class RecipeController {

    private $recipeModel;
    private $userModel;

    public function __construct() {
        $this->recipeModel = new RecipeModel();
        $this->userModel = new UserModel();
    }

    /**
     * Afiseaza toate retetele
     */
    public function index() {
        $recipes = $this->recipeModel->getAllRecipes();
        $isAdmin = false;
        
        if (isset($_SESSION['user_id'])) {
            $isAdmin = $this->userModel->isAdmin($_SESSION['user_id']);
        }
        
        $viewPath = __DIR__ . '/../Views/retete.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            http_response_code(500);
            echo "Error 500: Recipes view file not found.";
        }
    }

    /**
     * Afiseaza formularul pentru a crea o reteta noua 
     */
    public function create() {
        // Verifica daca userul este autentificat
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Trebuie sa fii autentificat pentru a accesa aceasta pagina.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'));
            exit;
        }

        $viewPath = __DIR__ . '/../Views/recipe_form.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            http_response_code(500);
            echo "Error 500: Recipe form view file not found.";
        }
    }

    /**
     * Salveaza o reteta noua 
     */
    public function store() {
        // Verifica daca userul este autentificat
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Trebuie sa fii autentificat.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'));
            exit;
        }

        // Validate POST data
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $steps = trim($_POST['steps'] ?? '');
            $total_calories = intval($_POST['total_calories'] ?? 0);
            $is_public = true; // Toate retetele sunt publice automat

            try {
                $result = $this->recipeModel->createRecipe(
                    $title,
                    $description,
                    $steps,
                    $_SESSION['user_id'],
                    $total_calories,
                    $is_public
                );

                $_SESSION['success_message'] = $result['message'];
                
            } catch (ValidationException $e) {
                // Eroare de validare - mesaj prietenos pentru utilizator
                $_SESSION['error_message'] = $e->getFormattedMessage();
                
            } catch (DatabaseException $e) {
                // Eroare de baza de date
                $_SESSION['error_message'] = 'Eroare la salvarea retetei. Va rugam incercati din nou.';
                
            } catch (Exception $e) {
                // Eroare generica
                $_SESSION['error_message'] = 'A apărut o eroare neașteptată. Vă rugăm încercați din nou.';
            }

            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'retete');
            exit;
        }
    }

    /**
     * Afiseaza formularul pentru editarea unei retete (admin sau creatorul)
     */
    public function edit($recipe_id) {
        // Verifica daca userul este autentificat
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Trebuie sa fii autentificat pentru a accesa aceasta pagina.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'));
            exit;
        }

        $recipe = $this->recipeModel->getRecipeById($recipe_id);
        
        if (!$recipe) {
            $_SESSION['error_message'] = 'Reteta nu a fost gasita.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'retete');
            exit;
        }
        
        // Verifica daca userul este admin sau creatorul retetei
        $isAdmin = $this->userModel->isAdmin($_SESSION['user_id']);
        if (!$isAdmin && $recipe['created_by'] != $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'Nu ai permisiunea sa editezi aceasta reteta.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'retete');
            exit;
        }

        $viewPath = __DIR__ . '/../Views/recipe_form.php';

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            http_response_code(500);
            echo "Error 500: Recipe form view file not found.";
        }
    }

    /**
     * Actualizeaza o reteta (admin sau creatorul)
     */
    public function update($recipe_id) {
        // Verifica daca userul este autentificat
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Trebuie sa fii autentificat.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'));
            exit;
        }

        $recipe = $this->recipeModel->getRecipeById($recipe_id);
        
        if (!$recipe) {
            $_SESSION['error_message'] = 'Reteta nu a fost gasita.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'retete');
            exit;
        }
        
        // Verifica daca userul este admin sau creatorul retetei
        $isAdmin = $this->userModel->isAdmin($_SESSION['user_id']);
        if (!$isAdmin && $recipe['created_by'] != $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'Nu ai permisiunea sa modifici aceasta reteta.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'retete');
            exit;
        }

        // Validate POST data
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $steps = trim($_POST['steps'] ?? '');
            $total_calories = intval($_POST['total_calories'] ?? 0);
            $is_public = true; // Toate retetele sunt publice automat

            $result = $this->recipeModel->updateRecipe(
                $recipe_id,
                $title,
                $description,
                $steps,
                $total_calories,
                $is_public
            );

            if ($result['success']) {
                $_SESSION['success_message'] = $result['message'];
            } else {
                $_SESSION['error_message'] = $result['message'];
            }

            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'retete');
            exit;
        }
    }

    /**
     * Sterge o reteta (admin sau creatorul)
     */
    public function delete($recipe_id) {
        // Verifica daca userul este autentificat
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Trebuie sa fii autentificat.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'));
            exit;
        }

        $recipe = $this->recipeModel->getRecipeById($recipe_id);
        
        if (!$recipe) {
            $_SESSION['error_message'] = 'Reteta nu a fost gasita.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'retete');
            exit;
        }
        
        // Verifica daca userul este admin sau creatorul retetei
        $isAdmin = $this->userModel->isAdmin($_SESSION['user_id']);
        if (!$isAdmin && $recipe['created_by'] != $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'Nu ai permisiunea sa stergi aceasta reteta.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'retete');
            exit;
        }

        $result = $this->recipeModel->deleteRecipe($recipe_id);

        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }

        header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'retete');
        exit;
    }
}
