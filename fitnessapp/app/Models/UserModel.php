<?php
// /app/Models/UserModel.php

// Ensure the configuration settings are loaded
require_once __DIR__ . '/../Core/config.php';

class UserModel {

    private $db;

    public function __construct() {
        // Configure mysqli to throw exceptions on errors instead of warnings/fatals.
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        // Use constants from the Config class for database connection
        try {
            $this->db = new mysqli(
                Config::DB_SERVER,
                Config::DB_USERNAME,
                Config::DB_PASSWORD,
                Config::DB_NAME
            );
        } catch (mysqli_sql_exception $e) {
            echo "<h1>DATABASE CONNECTION ERROR: Could not connect to the database.</h1>";
            exit;
        }
    }

    /**
     * Registers a new user in the database.
     * @param string $name
     * @param string $email
     * @param string $password
     * @return array Operation result (success, message, user_id)
     */
    public function createUser(string $name, string $email, string $password): array {

        // Email Format Validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => "Error: Invalid email format."];
        }

        // Password Hashing and Role Assignment
        $password_hash_value = password_hash($password, PASSWORD_DEFAULT);
        $role_id = Config::DEFAULT_ROLE_ID;

        $sql = "INSERT INTO users (role_id, name, email, password_hash) VALUES (?, ?, ?, ?)";


        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("isss", $role_id, $name, $email, $password_hash_value);
            try {
                $stmt->execute();
                return [
                    'success' => true,
                    'message' => "Registration successful! Welcome, " . htmlspecialchars($name) . ".",
                    'user_id' => $this->db->insert_id
                ];
            } catch (mysqli_sql_exception $e) {
                // 1062 = Duplicate entry for unique key (email)
                if ($e->getCode() === 1062) {
                    return ['success' => false, 'message' => "Error: This email address is already registered."];
                } else {
                    return ['success' => false, 'message' => "Registration error: Could not execute query. " . $e->getMessage()];
                }
            } finally {
                if (isset($stmt)) {
                    $stmt->close();
                }
            }
        } else {
             return ['success' => false, 'message' => "Database error: Could not prepare statement."];
        }
    }

    /**
     * Attempts to log in the user by verifying email and password.
     * @param string $email
     * @param string $password
     * @return array Operation result (success, message, user data)
     */
    public function verifyLogin(string $email, string $password): array {

        $sql = "SELECT user_id, name, email, role_id, password_hash FROM users WHERE email = ?";

        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("s", $email);

            try {
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();

                    if (password_verify($password, $user['password_hash'])) {
                        unset($user['password_hash']);
                        return [
                            'success' => true,
                            'message' => "Welcome back, " . $user['name'] . "!",
                            'user' => $user
                        ];
                    } else {
                        // Error: Incorrect password
                        return ['success' => false, 'message' => "Error: Invalid email or password."];
                    }
                } else {
                    // Error: User not found
                    return ['success' => false, 'message' => "Error: Invalid email or password."];
                }
            } catch (mysqli_sql_exception $e) {
                return ['success' => false, 'message' => "Database error during login: " . $e->getMessage()];
            } finally {
                 if (isset($stmt)) {
                    $stmt->close();
                }
            }
        } else {
            return ['success' => false, 'message' => "Database error: Could not prepare statement."];
        }
    }

    public function __destruct() {
        if (isset($this->db)) {
            $this->db->close();
        }
    }
}