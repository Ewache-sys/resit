<?php
/**
 * Staff Class
 * Manages staff members and their assignments
 */

class Staff {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get all active staff
     */
    public function getAllStaff($includeInactive = false) {
        try {
            $sql = "
                SELECT StaffID, Username, Name, Email, Phone, Department, Title, Bio,
                       ProfileImage, IsActive, CreatedAt
                FROM Staff
            ";

            if (!$includeInactive) {
                $sql .= " WHERE IsActive = 1";
            }

            $sql .= " ORDER BY Name";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get all staff error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get staff by ID
     */
    public function getStaffById($staffId) {
        try {
            $stmt = $this->db->prepare("
                SELECT StaffID, Username, Name, Email, Phone, Department, Title, Bio,
                       ProfileImage, IsActive, CreatedAt
                FROM Staff
                WHERE StaffID = ?
            ");
            $stmt->execute([$staffId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Get staff by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get staff by user ID
     */
    public function getStaffByUserId($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, u.Email
                FROM Staff s
                JOIN Users u ON s.UserID = u.UserID
                WHERE s.UserID = ? AND s.IsActive = 1
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Get staff by user ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get staff programmes (where they are programme leader)
     */
    public function getStaffProgrammes($staffId) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, l.LevelName
                FROM Programmes p
                JOIN Levels l ON p.LevelID = l.LevelID
                WHERE p.ProgrammeLeaderID = ? AND p.IsActive = 1
                ORDER BY l.SortOrder, p.ProgrammeName
            ");
            $stmt->execute([$staffId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get staff programmes error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get staff modules (where they are module leader)
     */
    public function getStaffModules($staffId) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.*, COUNT(pm.ProgrammeID) as programme_count
                FROM Modules m
                LEFT JOIN ProgrammeModules pm ON m.ModuleID = pm.ModuleID
                LEFT JOIN Programmes p ON pm.ProgrammeID = p.ProgrammeID AND p.IsActive = 1
                WHERE m.ModuleLeaderID = ? AND m.IsActive = 1
                GROUP BY m.ModuleID, m.ModuleCode, m.ModuleName, m.Description, m.Credits,
                         m.LearningOutcomes, m.AssessmentMethods, m.Image, m.IsActive,
                         m.CreatedAt, m.UpdatedAt
                ORDER BY m.ModuleCode
            ");
            $stmt->execute([$staffId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get staff modules error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create new staff member
     */
    public function createStaff($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Staff
                (Username, PasswordHash, Name, Email, Phone, Department, Title, Bio, ProfileImage)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            // Hash the password
            $passwordHash = Security::hashPassword($data['password']);

            $result = $stmt->execute([
                $data['username'],
                $passwordHash,
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['department'] ?? null,
                $data['title'] ?? null,
                $data['bio'] ?? null,
                $data['profile_image'] ?? null
            ]);

            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log("Create staff error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update staff member
     */
    public function updateStaff($staffId, $data) {
        try {
            // Start with base query
            $sql = "UPDATE Staff SET 
                    Name = ?, Email = ?, Phone = ?, Department = ?, 
                    Title = ?, Bio = ?, ProfileImage = ?";
            $params = [
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['department'] ?? null,
                $data['title'] ?? null,
                $data['bio'] ?? null,
                $data['profile_image'] ?? null
            ];

            // If username is being updated
            if (isset($data['username'])) {
                $sql .= ", Username = ?";
                $params[] = $data['username'];
            }

            // If password is being updated
            if (!empty($data['password'])) {
                $sql .= ", PasswordHash = ?";
                $params[] = Security::hashPassword($data['password']);
            }

            $sql .= " WHERE StaffID = ?";
            $params[] = $staffId;

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Update staff error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update staff profile
     */
    public function updateStaffProfile($staffId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Staff SET
                Phone = ?,
                Bio = ?,
                ProfileImage = COALESCE(?, ProfileImage)
                WHERE StaffID = ?
            ");

            return $stmt->execute([
                $data['phone'],
                $data['bio'],
                $data['profile_image'],
                $staffId
            ]);
        } catch (Exception $e) {
            error_log("Update staff profile error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete staff member
     */
    public function deleteStaff($staffId) {
        try {
            // Check if staff is assigned to any programmes or modules
            $stmt = $this->db->prepare("
                SELECT
                    (SELECT COUNT(*) FROM Programmes WHERE ProgrammeLeaderID = ?) as programme_count,
                    (SELECT COUNT(*) FROM Modules WHERE ModuleLeaderID = ?) as module_count
            ");
            $stmt->execute([$staffId, $staffId]);
            $counts = $stmt->fetch();

            if ($counts['programme_count'] > 0 || $counts['module_count'] > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete staff member who is assigned to programmes or modules.'
                ];
            }

            // Get staff data for profile image deletion
            $staffData = $this->getStaffById($staffId);

            // Begin transaction
            $this->db->beginTransaction();

            try {
                // Delete from Staff table
                $stmt = $this->db->prepare("DELETE FROM Staff WHERE StaffID = ?");
                $result = $stmt->execute([$staffId]);

                if ($result) {
                    // Delete profile image if exists
                    if ($staffData && $staffData['ProfileImage']) {
                        $imagePath = __DIR__ . '/../' . $staffData['ProfileImage'];
                        if (file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                    }

                    $this->db->commit();
                    return ['success' => true, 'message' => 'Staff member deleted successfully.'];
                }

                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to delete staff member.'];
            } catch (Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            error_log("Delete staff error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete staff member.'];
        }
    }

    /**
     * Get staff statistics
     */
    public function getStaffStats($staffId) {
        try {
            $programmes = $this->getStaffProgrammes($staffId);
            $modules = $this->getStaffModules($staffId);

            // Get total students interested in their programmes
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT i.InterestID) as interested_students
                FROM InterestedStudents i
                JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID
                WHERE p.ProgrammeLeaderID = ? AND i.IsSubscribed = 1
            ");
            $stmt->execute([$staffId]);
            $interestedStudents = $stmt->fetch()['interested_students'] ?? 0;

            return [
                'programme_count' => count($programmes),
                'module_count' => count($modules),
                'interested_students' => $interestedStudents,
                'programmes' => $programmes,
                'modules' => $modules
            ];
        } catch (Exception $e) {
            error_log("Get staff stats error: " . $e->getMessage());
            return [
                'programme_count' => 0,
                'module_count' => 0,
                'interested_students' => 0,
                'programmes' => [],
                'modules' => []
            ];
        }
    }

    /**
     * Search staff members
     */
    public function searchStaff($query) {
        try {
            $stmt = $this->db->prepare("
                SELECT StaffID, Name, Email, Department, Title
                FROM Staff
                WHERE IsActive = 1
                AND (Name LIKE ? OR Email LIKE ? OR Department LIKE ? OR Title LIKE ?)
                ORDER BY Name
            ");
            $searchTerm = "%$query%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Search staff error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Authenticate staff member
     */
    public function authenticate($username, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT StaffID, Username, PasswordHash, Name, Email, Title
                FROM Staff
                WHERE Username = ? AND IsActive = 1
            ");
            $stmt->execute([$username]);
            $staff = $stmt->fetch();
     
            if ($staff && Security::verifyPassword($password, $staff['PasswordHash'])) {
                return $staff;
            }
            return false;
        } catch (Exception $e) {
            error_log("Staff authentication error: " . $e->getMessage());
            return false;
        }
    }
}
?>
