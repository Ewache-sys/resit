<?php
/**
 * Programme Class
 * Manages degree programmes data and operations
 */

class Programme {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get all published programmes for public view
     */
    public function getPublishedProgrammes($levelId = null, $search = null) {
        try {
            $sql = "
                SELECT p.ProgrammeID, p.ProgrammeCode, p.ProgrammeName, p.Description,
                       p.EntryRequirements, p.CareerProspects, p.Duration, p.Image,
                       l.LevelName, s.Name as ProgrammeLeader
                FROM Programmes p
                JOIN Levels l ON p.LevelID = l.LevelID
                LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID
                WHERE p.IsPublished = 1 AND p.IsActive = 1
            ";

            $params = [];

            if ($levelId) {
                $sql .= " AND p.LevelID = ?";
                $params[] = $levelId;
            }

            if ($search) {
                $sql .= " AND (p.ProgrammeName LIKE ? OR p.Description LIKE ? OR p.ProgrammeCode LIKE ?)";
                $searchTerm = "%$search%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $sql .= " ORDER BY l.SortOrder, p.ProgrammeName";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get published programmes error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all programmes for admin view
     */
    public function getAllProgrammes($includeInactive = false) {
        try {
            $sql = "
                SELECT p.ProgrammeID, p.ProgrammeCode, p.ProgrammeName, p.Description,
                       p.EntryRequirements, p.CareerProspects, p.Duration, p.Image,
                       p.IsPublished, p.IsActive, p.CreatedAt, p.UpdatedAt,
                       l.LevelName, s.Name as ProgrammeLeader
                FROM Programmes p
                JOIN Levels l ON p.LevelID = l.LevelID
                LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID
            ";

            if (!$includeInactive) {
                $sql .= " WHERE p.IsActive = 1";
            }

            $sql .= " ORDER BY l.SortOrder, p.ProgrammeName";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get all programmes error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get programme by ID
     */
    public function getProgrammeById($programmeId, $publishedOnly = true) {
        try {
            $sql = "
                SELECT p.ProgrammeID, p.ProgrammeCode, p.ProgrammeName, p.Description,
                       p.EntryRequirements, p.CareerProspects, p.Duration, p.Image,
                       p.IsPublished, p.IsActive, p.CreatedAt, p.UpdatedAt,
                       l.LevelID, l.LevelName, s.StaffID as ProgrammeLeaderID, s.Name as ProgrammeLeader,
                       s.Email as LeaderEmail, s.Title as LeaderTitle, s.Bio as LeaderBio
                FROM Programmes p
                JOIN Levels l ON p.LevelID = l.LevelID
                LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID
                WHERE p.ProgrammeID = ?
            ";

            if ($publishedOnly) {
                $sql .= " AND p.IsPublished = 1 AND p.IsActive = 1";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$programmeId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Get programme by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get programme modules
     */
    public function getProgrammeModules($programmeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT pm.*, m.ModuleCode, m.ModuleName, m.Credits, m.Description,
                       m.LearningOutcomes, m.AssessmentMethods,
                       s.Name as ModuleLeader, s.Title as LeaderTitle
                FROM ProgrammeModules pm
                JOIN Modules m ON pm.ModuleID = m.ModuleID
                LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
                WHERE pm.ProgrammeID = ? AND m.IsActive = 1
                ORDER BY pm.Year, pm.Semester, m.ModuleCode
            ");
            $stmt->execute([$programmeId]);
            $modules = $stmt->fetchAll();

            // Group modules by year
            $modulesByYear = [];
            foreach ($modules as $module) {
                $year = $module['Year'];
                if (!isset($modulesByYear[$year])) {
                    $modulesByYear[$year] = [];
                }
                $modulesByYear[$year][] = $module;
            }

            return $modulesByYear;
        } catch (Exception $e) {
            error_log("Get programme modules error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create new programme
     */
    public function createProgramme($data) {
        try {
            $this->db->beginTransaction();

            // Insert programme details
            $stmt = $this->db->prepare("
                INSERT INTO Programmes
                (ProgrammeCode, ProgrammeName, LevelID, ProgrammeLeaderID, Description,
                 EntryRequirements, CareerProspects, Duration, Image, IsPublished)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $success = $stmt->execute([
                $data['code'],
                $data['name'],
                $data['level_id'],
                $data['leader_id'] ?: null,
                $data['description'],
                $data['entry_requirements'],
                $data['career_prospects'],
                $data['duration'],
                $data['image'] ?: null,
                $data['is_published'] ? 1 : 0
            ]);

            if (!$success) {
                throw new Exception("Failed to create programme");
            }

            $programmeId = $this->db->lastInsertId();

            // Add programme modules if provided
            if (isset($data['modules'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO ProgrammeModules (ProgrammeID, ModuleID, Year, Semester, IsCore)
                    VALUES (?, ?, ?, ?, ?)
                ");

                foreach ($data['modules'] as $year => $modules) {
                    foreach ($modules as $moduleId) {
                        $semester = $data['module_semesters'][$moduleId] ?? 'Full Year';
                        $isCore = in_array($moduleId, $data['core_modules'] ?? []);
                        
                        $stmt->execute([
                            $programmeId,
                            $moduleId,
                            $year,
                            $semester,
                            $isCore ? 1 : 0
                        ]);
                    }
                }
            }

            $this->db->commit();
            return $programmeId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Create programme error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update programme
     */
    public function updateProgramme($programmeId, $data) {
        try {
            $this->db->beginTransaction();

            // Update programme details
            $stmt = $this->db->prepare("
                UPDATE Programmes SET
                ProgrammeCode = ?, ProgrammeName = ?, LevelID = ?, ProgrammeLeaderID = ?,
                Description = ?, EntryRequirements = ?, CareerProspects = ?, Duration = ?,
                Image = ?, IsPublished = ?, UpdatedAt = NOW()
                WHERE ProgrammeID = ?
            ");

            $success = $stmt->execute([
                $data['code'],
                $data['name'],
                $data['level_id'],
                $data['leader_id'] ?: null,
                $data['description'],
                $data['entry_requirements'],
                $data['career_prospects'],
                $data['duration'],
                $data['image'] ?: null,
                $data['is_published'] ? 1 : 0,
                $programmeId
            ]);

            if (!$success) {
                throw new Exception("Failed to update programme details");
            }

            // Update programme modules if provided
            if (isset($data['modules'])) {
                // Remove existing module assignments
                $stmt = $this->db->prepare("DELETE FROM ProgrammeModules WHERE ProgrammeID = ?");
                $stmt->execute([$programmeId]);

                // Add new module assignments
                $stmt = $this->db->prepare("
                    INSERT INTO ProgrammeModules (ProgrammeID, ModuleID, Year, Semester, IsCore)
                    VALUES (?, ?, ?, ?, ?)
                ");

                foreach ($data['modules'] as $year => $modules) {
                    foreach ($modules as $moduleId) {
                        $semester = $data['module_semesters'][$moduleId] ?? 'Full Year';
                        $isCore = in_array($moduleId, $data['core_modules'] ?? []);
                        
                        $stmt->execute([
                            $programmeId,
                            $moduleId,
                            $year,
                            $semester,
                            $isCore ? 1 : 0
                        ]);
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Update programme error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete programme (complete deletion)
     */
    public function deleteProgramme($programmeId) {
        try {
            // Get programme data for image deletion
            $stmt = $this->db->prepare("SELECT Image FROM Programmes WHERE ProgrammeID = ?");
            $stmt->execute([$programmeId]);
            $programme = $stmt->fetch();

            // Delete the programme image if it exists
            if ($programme && $programme['Image']) {
                $imagePath = __DIR__ . '/../' . $programme['Image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Delete the programme
            $stmt = $this->db->prepare("DELETE FROM Programmes WHERE ProgrammeID = ?");
            return $stmt->execute([$programmeId]);
        } catch (Exception $e) {
            error_log("Delete programme error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle programme published status
     */
    public function togglePublished($programmeId) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Programmes
                SET IsPublished = NOT IsPublished, UpdatedAt = NOW()
                WHERE ProgrammeID = ?
            ");
            return $stmt->execute([$programmeId]);
        } catch (Exception $e) {
            error_log("Toggle published error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get programme statistics
     */
    public function getProgrammeStats($programmeId) {
        try {
            // Get module count
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as module_count
                FROM ProgrammeModules pm
                JOIN Modules m ON pm.ModuleID = m.ModuleID
                WHERE pm.ProgrammeID = ? AND m.IsActive = 1
            ");
            $stmt->execute([$programmeId]);
            $moduleCount = $stmt->fetch()['module_count'];

            // Get interested students count
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as student_count
                FROM InterestedStudents
                WHERE ProgrammeID = ? AND IsSubscribed = 1
            ");
            $stmt->execute([$programmeId]);
            $studentCount = $stmt->fetch()['student_count'];

            return [
                'module_count' => $moduleCount,
                'interested_students' => $studentCount
            ];
        } catch (Exception $e) {
            error_log("Get programme stats error: " . $e->getMessage());
            return ['module_count' => 0, 'interested_students' => 0];
        }
    }

    /**
     * Search programmes with modules
     */
    public function searchProgrammes($query, $levelId = null) {
        try {
            $sql = "
                SELECT DISTINCT p.ProgrammeID, p.ProgrammeCode, p.ProgrammeName, p.Description,
                       l.LevelName, s.Name as ProgrammeLeader
                FROM Programmes p
                JOIN Levels l ON p.LevelID = l.LevelID
                LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID
                LEFT JOIN ProgrammeModules pm ON p.ProgrammeID = pm.ProgrammeID
                LEFT JOIN Modules m ON pm.ModuleID = m.ModuleID
                WHERE p.IsPublished = 1 AND p.IsActive = 1
                AND (p.ProgrammeName LIKE ? OR p.Description LIKE ? OR p.ProgrammeCode LIKE ?
                     OR m.ModuleName LIKE ? OR m.Description LIKE ?)
            ";

            $params = array_fill(0, 5, "%$query%");

            if ($levelId) {
                $sql .= " AND p.LevelID = ?";
                $params[] = $levelId;
            }

            $sql .= " ORDER BY l.SortOrder, p.ProgrammeName";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Search programmes error: " . $e->getMessage());
            return [];
        }
    }
}
?>
