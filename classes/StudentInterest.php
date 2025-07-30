<?php
/**
 * StudentInterest Class
 * Manages student interest registrations and mailing lists
 */

class StudentInterest {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Register student interest in a programme
     */
    public function registerInterest($data) {
        try {
            // Check if already registered
            $existing = $this->checkExistingInterest($data['programme_id'], $data['email']);
            if ($existing) {
                return ['success' => false, 'message' => 'You have already registered interest in this programme.'];
            }

            // Generate unsubscribe token
            $unsubscribeToken = Security::generateToken();

            $stmt = $this->db->prepare("
                INSERT INTO InterestedStudents
                (ProgrammeID, StudentName, Email, Phone, Country, CurrentEducation,
                 MessageToUniversity, UnsubscribeToken)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $data['programme_id'],
                $data['student_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['country'] ?? null,
                $data['current_education'] ?? null,
                $data['message'] ?? null,
                $unsubscribeToken
            ]);

            if ($result) {
                // Send confirmation email (implementation would go here)
                return [
                    'success' => true,
                    'message' => 'Thank you for your interest! We will contact you with programme updates.',
                    'interest_id' => $this->db->lastInsertId()
                ];
            }

            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        } catch (Exception $e) {
            error_log("Register interest error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }

    /**
     * Check if student has already registered interest
     */
    private function checkExistingInterest($programmeId, $email) {
        try {
            $stmt = $this->db->prepare("
                SELECT InterestID FROM InterestedStudents
                WHERE ProgrammeID = ? AND Email = ? AND IsSubscribed = 1
            ");
            $stmt->execute([$programmeId, $email]);
            return $stmt->fetch() !== false;
        } catch (Exception $e) {
            error_log("Check existing interest error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get interested students for a programme
     */
    public function getInterestedStudents($programmeId, $limit = null, $offset = 0) {
        try {
            $sql = "
                SELECT i.InterestID, i.StudentName, i.Email, i.Phone, i.Country,
                       i.CurrentEducation, i.MessageToUniversity, i.RegisteredAt,
                       p.ProgrammeName, p.ProgrammeCode, l.LevelName
                FROM InterestedStudents i
                JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID
                JOIN Levels l ON p.LevelID = l.LevelID
                WHERE i.ProgrammeID = ? AND i.IsSubscribed = 1
                ORDER BY i.RegisteredAt DESC
            ";

            if ($limit) {
                $sql .= " LIMIT $limit OFFSET $offset";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$programmeId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get interested students error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all interested students (admin view)
     */
    public function getAllInterestedStudents($programmeId = null, $limit = null, $offset = 0) {
        try {
            $sql = "
                SELECT i.InterestID, i.StudentName, i.Email, i.Phone, i.Country,
                       i.CurrentEducation, i.MessageToUniversity, i.RegisteredAt,
                       p.ProgrammeName, p.ProgrammeCode, l.LevelName
                FROM InterestedStudents i
                JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID
                JOIN Levels l ON p.LevelID = l.LevelID
                WHERE i.IsSubscribed = 1
            ";

            $params = [];
            if ($programmeId) {
                $sql .= " AND i.ProgrammeID = ?";
                $params[] = $programmeId;
            }

            $sql .= " ORDER BY i.RegisteredAt DESC";

            if ($limit) {
                $sql .= " LIMIT $limit OFFSET $offset";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get all interested students error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get interested students count
     */
    public function getInterestedStudentsCount($programmeId = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM InterestedStudents WHERE IsSubscribed = 1";
            $params = [];

            if ($programmeId) {
                $sql .= " AND ProgrammeID = ?";
                $params[] = $programmeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Get interested students count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Unsubscribe student using token
     */
    public function unsubscribe($token) {
        try {
            $stmt = $this->db->prepare("
                UPDATE InterestedStudents
                SET IsSubscribed = 0
                WHERE UnsubscribeToken = ? AND IsSubscribed = 1
            ");
            $result = $stmt->execute([$token]);

            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'You have been successfully unsubscribed.'];
            }

            return ['success' => false, 'message' => 'Invalid unsubscribe link or already unsubscribed.'];
        } catch (Exception $e) {
            error_log("Unsubscribe error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Unsubscribe failed. Please contact support.'];
        }
    }

    /**
     * Remove student interest (admin)
     */
    public function removeInterest($interestId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM InterestedStudents WHERE InterestID = ?");
            return $stmt->execute([$interestId]);
        } catch (Exception $e) {
            error_log("Remove interest error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Export mailing list for a programme
     */
    public function exportMailingList($programmeId = null, $format = 'csv') {
        try {
            $students = $this->getAllInterestedStudents($programmeId);

            if ($format === 'csv') {
                return $this->generateCSV($students);
            } elseif ($format === 'json') {
                return json_encode($students, JSON_PRETTY_PRINT);
            }

            return $students;
        } catch (Exception $e) {
            error_log("Export mailing list error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate CSV format for export
     */
    private function generateCSV($students) {
        $output = "Student Name,Email,Phone,Country,Current Education,Programme,Level,Registered Date,Message\n";

        foreach ($students as $student) {
            $row = [
                $this->escapeCsvField($student['StudentName']),
                $this->escapeCsvField($student['Email']),
                $this->escapeCsvField($student['Phone'] ?? ''),
                $this->escapeCsvField($student['Country'] ?? ''),
                $this->escapeCsvField($student['CurrentEducation'] ?? ''),
                $this->escapeCsvField($student['ProgrammeName']),
                $this->escapeCsvField($student['LevelName']),
                $this->escapeCsvField($student['RegisteredAt']),
                $this->escapeCsvField($student['MessageToUniversity'] ?? '')
            ];
            $output .= implode(',', $row) . "\n";
        }

        return $output;
    }

    /**
     * Escape CSV field
     */
    private function escapeCsvField($field) {
        if (strpos($field, ',') !== false || strpos($field, '"') !== false || strpos($field, "\n") !== false) {
            return '"' . str_replace('"', '""', $field) . '"';
        }
        return $field;
    }

    /**
     * Get interest statistics by programme
     */
    public function getInterestStatistics() {
        try {
            $stmt = $this->db->prepare("
                SELECT p.ProgrammeName, p.ProgrammeCode, l.LevelName,
                       COUNT(i.InterestID) as interest_count,
                       COUNT(CASE WHEN i.RegisteredAt >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent_count
                FROM Programmes p
                LEFT JOIN InterestedStudents i ON p.ProgrammeID = i.ProgrammeID AND i.IsSubscribed = 1
                JOIN Levels l ON p.LevelID = l.LevelID
                WHERE p.IsPublished = 1 AND p.IsActive = 1
                GROUP BY p.ProgrammeID, p.ProgrammeName, p.ProgrammeCode, l.LevelName
                ORDER BY interest_count DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get interest statistics error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get student interest by email and programme
     */
    public function getStudentInterest($email, $programmeId = null) {
        try {
            $sql = "
                SELECT i.InterestID, i.ProgrammeID, i.StudentName, i.Email, i.RegisteredAt,
                       p.ProgrammeName, p.ProgrammeCode, l.LevelName, i.UnsubscribeToken
                FROM InterestedStudents i
                JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID
                JOIN Levels l ON p.LevelID = l.LevelID
                WHERE i.Email = ? AND i.IsSubscribed = 1
            ";

            $params = [$email];
            if ($programmeId) {
                $sql .= " AND i.ProgrammeID = ?";
                $params[] = $programmeId;
            }

            $sql .= " ORDER BY i.RegisteredAt DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get student interest error: " . $e->getMessage());
            return [];
        }
    }
}
?>
