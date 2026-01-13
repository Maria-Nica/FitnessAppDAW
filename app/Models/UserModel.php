<?php
// /app/Models/UserModel.php

// Asiguram ca setarile de configurare sunt incarcate
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/../Core/AuthenticationInterface.php';
require_once __DIR__ . '/../Core/Exceptions/ValidationException.php';
require_once __DIR__ . '/../Core/Exceptions/DatabaseException.php';

class UserModel extends BaseModel implements AuthenticationInterface {

    public function __construct() {
        // Apeleaza constructorul parinte pentru a stabili conexiunea la baza de date
        parent::__construct();
    }

    /**
     * Inregistreaza un utilizator nou in baza de date.
     * @param string $name
     * @param string $email
     * @param string $password
     * @return array Rezultatul operatiei (success, message, user_id)
     */
    public function createUser(string $name, string $email, string $password): array {

        // Validare format Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ValidationException(
                "Format email invalid furnizat",
                'email',
                $email
            );
        }

        // Hash-uirea parolei si atribuirea rolului
        $password_hash_value = password_hash($password, PASSWORD_DEFAULT);
        $role_id = Config::DEFAULT_ROLE_ID;

        $sql = "INSERT INTO users (role_id, name, email, password_hash) VALUES (?, ?, ?, ?)";


        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("isss", $role_id, $name, $email, $password_hash_value);
            try {
                $stmt->execute();
                return [
                    'success' => true,
                    'message' => "Inregistrare reusita! Bine ai venit, " . htmlspecialchars($name) . ".",
                    'user_id' => $this->db->insert_id
                ];
            } catch (mysqli_sql_exception $e) {
                // 1062 = Intrare duplicat pentru cheie unica (email)
                if ($e->getCode() === 1062) {
                    throw new DatabaseException(
                        "Adresa de email este deja inregistrata",
                        'INSERT',
                        null,
                        1062,
                        0,
                        $e
                    );
                } else {
                    throw new DatabaseException(
                        "Inregistrare esuata: " . $e->getMessage(),
                        'INSERT',
                        null,
                        $e->getCode(),
                        0,
                        $e
                    );
                }
            } finally {
                if (isset($stmt)) {
                    $stmt->close();
                }
            }
        } else {
             return ['success' => false, 'message' => "Eroare baza de date: Nu s-a putut pregati instructiunea."];
        }
    }

    /**
     * Incearca sa autentifice utilizatorul prin verificarea emailului si parolei.
     * @param string $email
     * @param string $password
     * @return array Rezultatul operatiei (success, message, date utilizator)
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
                            'message' => "Bine ai revenit, " . $user['name'] . "!",
                            'user' => $user
                        ];
                    } else {
                        // Eroare: Parola incorecta
                        return ['success' => false, 'message' => "Eroare: Email sau parola invalida."];
                    }
                } else {
                    // Eroare: Utilizator negasit
                    return ['success' => false, 'message' => "Eroare: Email sau parola invalida."];
                }
            } catch (mysqli_sql_exception $e) {
                return ['success' => false, 'message' => "Eroare baza de date la autentificare: " . $e->getMessage()];
            } finally {
                 if (isset($stmt)) {
                    $stmt->close();
                }
            }
        } else {
            return ['success' => false, 'message' => "Eroare baza de date: Nu s-a putut pregati instructiunea."];
        }
    }

    /**
     * Obtine rolul utilizatorului dupa user_id
     * @param int $user_id
     * @return string|null Numele rolului sau null daca utilizatorul nu a fost gasit
     */
    public function getUserRole(int $user_id): ?string {
        $sql = "SELECT r.name as role_name 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.role_id 
                WHERE u.user_id = ?";

        if ($stmt = $this->db->prepare($sql)) {
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $stmt->close();
                return $row['role_name'];
            }
            
            $stmt->close();
        }

        return null;
    }

    /**
     * Verifica daca utilizatorul este admin
     * @param int $user_id
     * @return bool True daca utilizatorul este admin, false altfel
     */
    public function isAdmin(int $user_id): bool {
        $role = $this->getUserRole($user_id);
        return $role === 'admin';
    }
}