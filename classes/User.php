<?php
class User {
    private $db;
    private $id;
    private $name;
    private $email;
    private $role;
    private $isLoggedIn = false;

    public function __construct($db) {
        $this->db = $db;
        $this->startSession();
    }

    private function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->checkLogin();
        $this->generateCSRFToken();
    }
    
    private function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public function login($email, $password) {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        
        $stmt = $this->db->prepare("SELECT id, name, email, role, password FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $this->id = $user['id'];
            $this->name = $user['name'];
            $this->email = $user['email'];
            $this->role = $user['role'] ?? 'user';
            $this->isLoggedIn = true;
            
            // Store user data in session
            $_SESSION['user_id'] = $this->id;
            $_SESSION['user_name'] = $this->name;
            $_SESSION['user_email'] = $this->email;
            $_SESSION['user_role'] = $this->role;
            
            // Generate and store CSRF token
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            
            return true;
        }
        
        return false;
    }

    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        $this->isLoggedIn = false;
        $this->id = null;
        $this->name = null;
        $this->email = null;
        
        return true;
    }

    private function checkLogin() {
        if (isset($_SESSION['user_id'])) {
            $this->id = $_SESSION['user_id'];
            $this->name = $_SESSION['user_name'] ?? '';
            $this->email = $_SESSION['user_email'] ?? '';
            $this->role = $_SESSION['user_role'] ?? 'user';
            $this->isLoggedIn = true;
        } else {
            $this->isLoggedIn = false;
        }
    }

    public function isLoggedIn() {
        return $this->isLoggedIn;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }
    
    public function getRole() {
        return $this->role;
    }
    
    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function getCSRFToken() {
        return $_SESSION['csrf_token'] ?? '';
    }

    public function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
