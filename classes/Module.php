<?php
/**
 * Module Class
 * Manages module data and operations
 */

class Module {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get all modules
     */
    public function getAllModules($includeInactive = false) {
        try {
            $sql = "
                SELECT m.ModuleID, m.ModuleCode, m.ModuleName, m.Description,
                       m.LearningOutcomes, m.AssessmentMethods, m.Credits,
                       m.Image, m.IsActive, m.CreatedAt, m.UpdatedAt,
                       s.StaffID as ModuleLeaderID, s.Name as ModuleLeader,
                       s.Email as LeaderEmail, s.Title as LeaderTitle
                FROM Modules m
                LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
            ";

            if (!$includeInactive) {
                $sql .= " WHERE m.IsActive = 1";
            }

            $sql .= " ORDER BY m.ModuleCode";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get all modules error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get module by ID
     */
    public function getModuleById($moduleId) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.ModuleID, m.ModuleCode, m.ModuleName, m.Description,
                       m.LearningOutcomes, m.AssessmentMethods, m.Credits,
                       m.Image, m.IsActive, m.CreatedAt, m.UpdatedAt,
                       s.StaffID as ModuleLeaderID, s.Name as ModuleLeader,
                       s.Email as LeaderEmail, s.Title as LeaderTitle
                FROM Modules m
                LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
                WHERE m.ModuleID = ?
            ");
            $stmt->execute([$moduleId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Get module by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new module
     */
    public function createModule($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Modules (
                    ModuleCode, ModuleName, ModuleLeaderID, Description,
                    LearningOutcomes, AssessmentMethods, Credits, Image
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $data['code'],
                $data['name'],
                $data['leader_id'] ?: null,
                $data['description'],
                $data['learning_outcomes'],
                $data['assessment_methods'],
                $data['credits'],
                $data['image'] ?: null
            ]);

            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log("Create module error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update module
     */
    public function updateModule($moduleId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Modules SET
                ModuleCode = ?, ModuleName = ?, ModuleLeaderID = ?,
                Description = ?, LearningOutcomes = ?, AssessmentMethods = ?,
                Credits = ?, Image = ?, UpdatedAt = NOW()
                WHERE ModuleID = ?
            ");

            return $stmt->execute([
                $data['code'],
                $data['name'],
                $data['leader_id'] ?: null,
                $data['description'],
                $data['learning_outcomes'],
                $data['assessment_methods'],
                $data['credits'],
                $data['image'] ?: null,
                $moduleId
            ]);
        } catch (Exception $e) {
            error_log("Update module error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update module content (for staff)
     */
    public function updateModuleContent($moduleId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Modules SET
                Description = ?,
                LearningOutcomes = ?,
                AssessmentMethods = ?,
                UpdatedAt = NOW()
                WHERE ModuleID = ?
            ");

            return $stmt->execute([
                $data['description'],
                $data['learning_outcomes'],
                $data['assessment_methods'],
                $moduleId
            ]);
        } catch (Exception $e) {
            error_log("Update module content error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete module (soft delete)
     */
    public function deleteModule($moduleId) {
        try {
            // Check if module is used in any programmes
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM ProgrammeModules 
                WHERE ModuleID = ?
            ");
            $stmt->execute([$moduleId]);
            $count = $stmt->fetch()['count'];

            if ($count > 0) {
                return [
                    'success' => false,
                    'message' => 'Cannot delete module that is used in programmes.'
                ];
            }

            $stmt = $this->db->prepare("
                UPDATE Modules 
                SET IsActive = 0, UpdatedAt = NOW() 
                WHERE ModuleID = ?
            ");
            $result = $stmt->execute([$moduleId]);

            if ($result) {
                return [
                    'success' => true,
                    'message' => 'Module deleted successfully.'
                ];
            }
            return [
                'success' => false,
                'message' => 'Failed to delete module.'
            ];
        } catch (Exception $e) {
            error_log("Delete module error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to delete module.'
            ];
        }
    }

    /**
     * Get modules by programme
     */
    public function getModulesByProgramme($programmeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.ModuleID, m.ModuleCode, m.ModuleName,
                       pm.Year, pm.Semester, pm.IsCore
                FROM Modules m
                JOIN ProgrammeModules pm ON m.ModuleID = pm.ModuleID
                WHERE pm.ProgrammeID = ? AND m.IsActive = 1
                ORDER BY pm.Year, pm.Semester, m.ModuleCode
            ");
            $stmt->execute([$programmeId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get modules by programme error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search modules
     */
    public function searchModules($query) {
        try {
            $stmt = $this->db->prepare("
                SELECT m.ModuleID, m.ModuleCode, m.ModuleName, m.Credits,
                       s.Name as ModuleLeader
                FROM Modules m
                LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
                WHERE m.IsActive = 1
                AND (m.ModuleCode LIKE ? OR m.ModuleName LIKE ? OR m.Description LIKE ?)
                ORDER BY m.ModuleCode
            ");
            
            $searchTerm = "%$query%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Search modules error: " . $e->getMessage());
            return [];
        }
    }
}
?> 