<?php
/**
 * Level Class
 * Manages programme levels (Undergraduate, Postgraduate, etc.)
 */

class Level {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get all levels
     */
    public function getAllLevels() {
        try {
            $stmt = $this->db->prepare("
                SELECT LevelID, LevelName, Description, SortOrder
                FROM Levels
                ORDER BY SortOrder, LevelName
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get all levels error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get level by ID
     */
    public function getLevelById($levelId) {
        try {
            $stmt = $this->db->prepare("
                SELECT LevelID, LevelName, Description, SortOrder
                FROM Levels
                WHERE LevelID = ?
            ");
            $stmt->execute([$levelId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Get level by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new level
     */
    public function createLevel($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Levels (LevelName, Description, SortOrder)
                VALUES (?, ?, ?)
            ");

            $result = $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['sort_order'] ?? 0
            ]);

            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log("Create level error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update level
     */
    public function updateLevel($levelId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Levels
                SET LevelName = ?, Description = ?, SortOrder = ?
                WHERE LevelID = ?
            ");

            return $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['sort_order'] ?? 0,
                $levelId
            ]);
        } catch (Exception $e) {
            error_log("Update level error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete level
     */
    public function deleteLevel($levelId) {
        try {
            // Check if level is used by any programmes
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM Programmes WHERE LevelID = ?");
            $stmt->execute([$levelId]);
            $count = $stmt->fetch()['count'];

            if ($count > 0) {
                return ['success' => false, 'message' => 'Cannot delete level that is used by programmes.'];
            }

            $stmt = $this->db->prepare("DELETE FROM Levels WHERE LevelID = ?");
            $result = $stmt->execute([$levelId]);

            if ($result) {
                return ['success' => true, 'message' => 'Level deleted successfully.'];
            }
            return ['success' => false, 'message' => 'Failed to delete level.'];
        } catch (Exception $e) {
            error_log("Delete level error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete level.'];
        }
    }
}
?>
