<?php
/**
 * User Class
 * Manages system users and their roles
 */

class User {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get all users
     */
    public function getAllUsers() {
        try {
            $stmt = $this->db->prepare("
                SELECT u.UserID, u.Username, u.Email, u.FirstName, u.LastName,
                       u.IsActive, u.CreatedAt, u.LastLogin, r.RoleName
                FROM Users u
                JOIN UserRoles r ON u.RoleID = r.RoleID
                ORDER BY u.Username
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get all users error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT u.UserID, u.Username, u.Email, u.FirstName, u.LastName,
                       u.IsActive, u.CreatedAt, u.LastLogin, u.RoleID, r.RoleName
                FROM Users u
                JOIN UserRoles r ON u.RoleID = r.RoleID
                WHERE u.UserID = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Get user by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new user
     */
    public function createUser($data) {
        try {
            // Check if username or email already exists
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM Users 
                WHERE Username = ? OR Email = ?
            ");
            $stmt->execute([$data['username'], $data['email']]);
            if ($stmt->fetch()['count'] > 0) {
                return [
                    'success' => false,
                    'message' => 'Username or email already exists.'
                ];
            }

            $stmt = $this->db->prepare("
                INSERT INTO Users (
                    Username, Email, PasswordHash, RoleID,
                    FirstName, LastName, IsActive
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $data['username'],
                $data['email'],
                Security::hashPassword($data['password']),
                $data['role_id'],
                $data['first_name'],
                $data['last_name'],
                $data['is_active'] ? 1 : 0
            ]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'User created successfully.',
                    'user_id' => $this->db->lastInsertId()
                ];
            }
            return [
                'success' => false,
                'message' => 'Failed to create user.'
            ];
        } catch (Exception $e) {
            error_log("Create user error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create user.'
            ];
        }
    }

    /**
     * Update user
     */
    public function updateUser($userId, $data) {
        try {
            // Check if username or email already exists for other users
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM Users 
                WHERE (Username = ? OR Email = ?) AND UserID != ?
            ");
            $stmt->execute([$data['username'], $data['email'], $userId]);
            if ($stmt->fetch()['count'] > 0) {
                return [
                    'success' => false,
                    'message' => 'Username or email already exists.'
                ];
            }

            $sql = "
                UPDATE Users SET
                    Username = ?, Email = ?, RoleID = ?,
                    FirstName = ?, LastName = ?, IsActive = ?
            ";
            $params = [
                $data['username'],
                $data['email'],
                $data['role_id'],
                $data['first_name'],
                $data['last_name'],
                $data['is_active'] ? 1 : 0
            ];

            // Add password update if provided
            if (!empty($data['password'])) {
                $sql .= ", PasswordHash = ?";
                $params[] = Security::hashPassword($data['password']);
            }

            $sql .= " WHERE UserID = ?";
            $params[] = $userId;

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'User updated successfully.'
                ];
            }
            return [
                'success' => false,
                'message' => 'Failed to update user.'
            ];
        } catch (Exception $e) {
            error_log("Update user error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to update user.'
            ];
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($userId) {
        try {
            // Check if user has any activity logs
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM ActivityLog WHERE UserID = ?");
            $stmt->execute([$userId]);
            $hasActivity = $stmt->fetch()['count'] > 0;

            if ($hasActivity) {
                // Soft delete if user has activity
                $stmt = $this->db->prepare("UPDATE Users SET IsActive = 0 WHERE UserID = ?");
                $result = $stmt->execute([$userId]);

                if ($result) {
                    return [
                        'success' => true,
                        'message' => 'User deactivated successfully.'
                    ];
                }
            } else {
                // Hard delete if no activity
                $stmt = $this->db->prepare("DELETE FROM Users WHERE UserID = ?");
                $result = $stmt->execute([$userId]);

                if ($result) {
                    return [
                        'success' => true,
                        'message' => 'User deleted successfully.'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to delete user.'
            ];
        } catch (Exception $e) {
            error_log("Delete user error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete user.'
            ];
        }
    }

    /**
     * Get all user roles
     */
    public function getAllRoles() {
        try {
            $stmt = $this->db->prepare("SELECT RoleID, RoleName, Description FROM UserRoles ORDER BY RoleName");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get all roles error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get user statistics
     */
    public function getUserStats() {
        try {
            // Get total users
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM Users");
            $stmt->execute();
            $total = $stmt->fetch()['total'];

            // Get users by role
            $stmt = $this->db->prepare("
                SELECT r.RoleName, COUNT(u.UserID) as count
                FROM UserRoles r
                LEFT JOIN Users u ON r.RoleID = u.RoleID
                GROUP BY r.RoleID, r.RoleName
                ORDER BY count DESC
            ");
            $stmt->execute();
            $byRole = $stmt->fetchAll();

            // Get active vs inactive
            $stmt = $this->db->prepare("
                SELECT IsActive, COUNT(*) as count
                FROM Users
                GROUP BY IsActive
            ");
            $stmt->execute();
            $byStatus = $stmt->fetchAll();

            // Get recent logins
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM Users
                WHERE LastLogin >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ");
            $stmt->execute();
            $recentLogins = $stmt->fetch()['count'];

            return [
                'total' => $total,
                'by_role' => $byRole,
                'by_status' => $byStatus,
                'recent_logins' => $recentLogins
            ];
        } catch (Exception $e) {
            error_log("Get user stats error: " . $e->getMessage());
            return [
                'total' => 0,
                'by_role' => [],
                'by_status' => [],
                'recent_logins' => 0
            ];
        }
    }
}
?> 