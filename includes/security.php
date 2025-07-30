<?php
/**
 * Security Class
 * Handles authentication, CSRF protection, input sanitization
 */

class Security {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Generate CSRF token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitize input to prevent XSS
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate email format
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Hash password securely
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
      
        
        
        $result = password_verify($password, $hash);
        
       
        return $result;
    }

    /**
     * Generate secure random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Check if user has specific role
     */
    public static function hasRole($requiredRole) {
        if (!self::isLoggedIn()) {
            return false;
        }

        $userRole = $_SESSION['user_role'] ?? '';

        // Define role hierarchy
        $roleHierarchy = [
            'Super Admin' => 4,
            'Admin' => 3,
            'Staff' => 2,
            'Viewer' => 1
        ];

        // $userLevel = $roleHierarchy[$userRole] ?? 0;
        // $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;

        // return $userLevel >= $requiredLevel;
        if($userRole !== $requiredRole){
            return false;
        }
        return true;
    }

    /**
     * Login user
     */
    public function login($username, $password) {
        try {
            
            $query = "
                SELECT u.UserID, u.Username, u.Email, u.PasswordHash, u.FirstName, u.LastName,
                       u.IsActive, r.RoleName
                FROM Users u
                JOIN UserRoles r ON u.RoleID = r.RoleID
                WHERE u.Username = ? AND u.IsActive = 1
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$username]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
              
                if (self::verifyPassword($password, $user['PasswordHash'])) {
                    
                    // Set session data
                    $_SESSION['user_id'] = $user['UserID'];
                    $_SESSION['username'] = $user['Username'];
                    $_SESSION['user_email'] = $user['Email'];
                    $_SESSION['user_name'] = $user['FirstName'] . ' ' . $user['LastName'];
                    $_SESSION['user_role'] = $user['RoleName'];
                    $_SESSION['last_activity'] = time();

                    // Update last login
                    $this->updateLastLogin($user['UserID']);

                    // Log activity
                    $this->logActivity($user['UserID'], 'Login', 'Users', $user['UserID']);

                    return true;
                } else {
return false;                }
            } else {
return false;            }

            return false;
        } catch (Exception $e) {
            
            return false;
        }
    }

    /**
     * Logout user
     */
    public static function logout() {
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    /**
     * Update last login time
     */
    private function updateLastLogin($userId) {
        try {
            $stmt = $this->db->prepare("UPDATE Users SET LastLogin = NOW() WHERE UserID = ?");
            $stmt->execute([$userId]);
        } catch (Exception $e) {
            error_log("Update last login error: " . $e->getMessage());
        }
    }

    /**
     * Log user activity
     */
    public function logActivity($userId, $action, $tableName = null, $recordId = null, $oldValues = null, $newValues = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO ActivityLog (UserID, Action, TableName, RecordID, OldValues, NewValues, IPAddress, UserAgent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $action,
                $tableName,
                $recordId,
                $oldValues ? json_encode($oldValues) : null,
                $newValues ? json_encode($newValues) : null,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * Check session timeout
     */
    public static function checkSessionTimeout($timeout = 3600) { // 1 hour default
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > $timeout) {
                self::logout();
                return false;
            }
        }
        $_SESSION['last_activity'] = time();
        return true;
    }

    /**
     * Require login for protected pages
     */
    public static function requireLogin($redirectTo = 'login.php') {
        if (!self::isLoggedIn() || !self::checkSessionTimeout()) {
            header("Location: $redirectTo");
            exit();
        }
    }

    /**
     * Require specific role for protected pages
     */
    public static function requireRole($role, $redirectTo = 'index.php') {
         self::requireLogin();
        if (!self::hasRole($role)) {
            self::logout();
           self::requireLogin();
            exit();
        }
    }
}
?>
