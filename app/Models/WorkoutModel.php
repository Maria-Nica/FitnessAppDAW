<?php

require_once __DIR__ . '/BaseModel.php';

class WorkoutModel extends BaseModel {
    
    public function __construct() {
        // Apeleaza constructorul parinte pentru a stabili conexiunea la baza de date
        parent::__construct();
    }
    
    // Obtine toate antrenamentele cu tipul de antrenament si informatii utilizator
    public function getAllWorkouts() {
        $sql = "SELECT w.workout_id, w.user_id, w.workout_type_id, w.description, w.date, w.duration_min, w.intensity, w.calories_burned, w.notes, w.created_at, wt.name as workout_type_name, u.name as user_name 
                FROM workouts w 
                JOIN workout_types wt ON w.workout_type_id = wt.workout_type_id 
                JOIN users u ON w.user_id = u.user_id 
                ORDER BY w.date DESC, w.created_at DESC";
        
        $result = $this->db->query($sql);
        
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    // Obtine antrenament dupa ID
    public function getWorkoutById($workout_id) {
        $sql = "SELECT w.*, wt.name as workout_type_name 
                FROM workouts w 
                JOIN workout_types wt ON w.workout_type_id = wt.workout_type_id 
                WHERE w.workout_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $workout_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    // Obtine toate tipurile de antrenament pentru dropdown
    public function getAllWorkoutTypes() {
        $sql = "SELECT * FROM workout_types ORDER BY name ASC";
        $result = $this->db->query($sql);
        
        if ($result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    // Creeaza antrenament nou
    public function createWorkout($data) {
        $sql = "INSERT INTO workouts (user_id, workout_type_id, description, date, duration_min, intensity, calories_burned, notes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "iisiiiss",
            $data['user_id'],
            $data['workout_type_id'],
            $data['description'],
            $data['date'],
            $data['duration_min'],
            $data['intensity'],
            $data['calories_burned'],
            $data['notes']
        );
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    // Actualizeaza antrenament
    public function updateWorkout($workout_id, $data) {
        $sql = "UPDATE workouts 
                SET workout_type_id = ?, description = ?, date = ?, duration_min = ?, intensity = ?, calories_burned = ?, notes = ? 
                WHERE workout_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param(
            "issiissi",
            $data['workout_type_id'],
            $data['description'],
            $data['date'],
            $data['duration_min'],
            $data['intensity'],
            $data['calories_burned'],
            $data['notes'],
            $workout_id
        );
        
        return $stmt->execute();
    }
    
    // Sterge antrenament
    public function deleteWorkout($workout_id) {
        $sql = "DELETE FROM workouts WHERE workout_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $workout_id);
        
        return $stmt->execute();
    }
}
