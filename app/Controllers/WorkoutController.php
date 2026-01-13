<?php

require_once __DIR__ . '/../Models/WorkoutModel.php';
require_once __DIR__ . '/../Models/UserModel.php';

class WorkoutController {
    private $workoutModel;
    private $userModel;
    
    public function __construct() {
        $this->workoutModel = new WorkoutModel();
        $this->userModel = new UserModel();
    }
    
    // Afiseaza toate antrenamentele
    public function index() {
        $workouts = $this->workoutModel->getAllWorkouts();
        $isAdmin = false;
        
        if (isset($_SESSION['user_id'])) {
            $isAdmin = $this->userModel->isAdmin($_SESSION['user_id']);
        }
        
        require_once __DIR__ . '/../Views/antrenamente.php';
    }
    
    // Afiseaza formular criere antrenament (doar autentificat)
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Trebuie sa fii autentificat pentru a accesa aceasta pagina.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'));
            exit;
        }
        
        $workoutTypes = $this->workoutModel->getAllWorkoutTypes();
        require_once __DIR__ . '/../Views/workout_form.php';
    }
    
    // Salveaza antrenament nou (doar autentificat)
    public function store() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Nu ai permisiunea sa adaugi antrenamente.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'antrenamente');
            exit;
        }
        
        

        $data = [
            'user_id' => $_SESSION['user_id'],
            'workout_type_id' => $_POST['workout_type_id'],
            'description' => $_POST['description'] ?? '',
            'date' => $_POST['date'],
            'duration_min' => $_POST['duration_min'],
            'intensity' => $_POST['intensity'],
            'calories_burned' => $_POST['calories_burned'],
            'notes' => $_POST['notes'] ?? ''
        ];
        
        if ($this->workoutModel->createWorkout($data)) {
            $_SESSION['success_message'] = 'Antrenament adaugat cu succes!';
        } else {
            $_SESSION['error_message'] = 'Eroare la adaugarea antrenamentului.';
        }
        
        header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'antrenamente');
        exit;
    }
    
    // Afiseaza formularul de editare antrenament (admin sau creatorul)
    public function edit($workout_id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Trebuie sa fii autentificat pentru a accesa aceasta pagina.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/'));
            exit;
        }
        
        $workout = $this->workoutModel->getWorkoutById($workout_id);
        
        if (!$workout) {
            $_SESSION['error_message'] = 'Antrenamentul nu a fost gasit.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'antrenamente');
            exit;
        }
        
        // Verifica daca userul este admin sau creatorul antrenamentului
        $isAdmin = $this->userModel->isAdmin($_SESSION['user_id']);
        if (!$isAdmin && $workout['user_id'] != $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'Nu ai permisiunea sa editezi acest antrenament.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'antrenamente');
            exit;
        }
        
        $workoutTypes = $this->workoutModel->getAllWorkoutTypes();
        require_once __DIR__ . '/../Views/workout_form.php';
    }
    
    // Actualizeaza antrenament (admin sau creatorul)
    public function update($workout_id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Nu ai permisiunea sa editezi antrenamente.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'antrenamente');
            exit;
        }
        
        $workout = $this->workoutModel->getWorkoutById($workout_id);
        
        $isAdmin = $this->userModel->isAdmin($_SESSION['user_id']);
        if (!$workout || (!$isAdmin && $workout['user_id'] != $_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Nu ai permisiunea sa editezi acest antrenament.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'antrenamente');
            exit;
        }
        
        

        $data = [
            'workout_type_id' => $_POST['workout_type_id'],
            'description' => $_POST['description'] ?? '',
            'date' => $_POST['date'],
            'duration_min' => $_POST['duration_min'],
            'intensity' => $_POST['intensity'],
            'calories_burned' => $_POST['calories_burned'],
            'notes' => $_POST['notes'] ?? ''
        ];
        
        if ($this->workoutModel->updateWorkout($workout_id, $data)) {
            $_SESSION['success_message'] = 'Antrenament actualizat cu succes!';
        } else {
            $_SESSION['error_message'] = 'Eroare la actualizarea antrenamentului.';
        }
        
        header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'antrenamente');
        exit;
    }
    
    // Sterge antrenament (admin sau creatorul)
    public function delete($workout_id) {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Nu ai permisiunea sa stergi antrenamente.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'antrenamente');
            exit;
        }
        
        $workout = $this->workoutModel->getWorkoutById($workout_id);
        
        $isAdmin = $this->userModel->isAdmin($_SESSION['user_id']);
        if (!$workout || (!$isAdmin && $workout['user_id'] != $_SESSION['user_id'])) {
            $_SESSION['error_message'] = 'Nu ai permisiunea sa stergi acest antrenament.';
            header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'antrenamente');
            exit;
        }
        
        if ($this->workoutModel->deleteWorkout($workout_id)) {
            $_SESSION['success_message'] = 'Antrenament sters cu succes!';
        } else {
            $_SESSION['error_message'] = 'Eroare la stergerea antrenamentului.';
        }
        
        header('Location: ' . (defined('Config::BASE_URL') ? Config::BASE_URL : '/fitnessapp/public/') . 'antrenamente');
        exit;
    }
}
