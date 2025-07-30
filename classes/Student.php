<?php
/**
 * Student Class
 * Manages student data and operations
 */

class Student {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get all students
     */
    public function getAllStudents($search = null, $limit = null, $offset = 0) {
        try {
            $sql = "
                SELECT s.StudentID, s.FirstName, s.LastName, s.Email, s.Phone,
                       s.DateOfBirth, s.Address, s.City, s.Country, s.PostalCode,
                       s.EnrollmentDate, s.Status, s.StudentNumber,
                       p.ProgrammeName, p.ProgrammeCode, l.LevelName
                FROM Students s
                LEFT JOIN Programmes p ON s.ProgrammeID = p.ProgrammeID
                LEFT JOIN Levels l ON p.LevelID = l.LevelID
            ";

            $params = [];
            if ($search) {
                $sql .= " WHERE (s.FirstName LIKE ? OR s.LastName LIKE ? OR s.Email LIKE ? OR s.StudentNumber LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_fill(0, 4, $searchTerm);
            }

            $sql .= " ORDER BY s.LastName, s.FirstName";

            if ($limit) {
                $sql .= " LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get all students error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get student by ID
     */
    public function getStudentById($studentId) {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, p.ProgrammeName, p.ProgrammeCode, l.LevelName
                FROM Students s
                LEFT JOIN Programmes p ON s.ProgrammeID = p.ProgrammeID
                LEFT JOIN Levels l ON p.LevelID = l.LevelID
                WHERE s.StudentID = ?
            ");
            $stmt->execute([$studentId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Get student by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new student
     */
    public function createStudent($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Students (
                    StudentNumber, FirstName, LastName, Email, Phone,
                    DateOfBirth, Address, City, Country, PostalCode,
                    ProgrammeID, EnrollmentDate, Status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $data['student_number'],
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['country'] ?? null,
                $data['postal_code'] ?? null,
                $data['programme_id'] ?? null,
                $data['enrollment_date'] ?? date('Y-m-d'),
                $data['status'] ?? 'Active'
            ]);

            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log("Create student error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update student
     */
    public function updateStudent($studentId, $data) {
        try {
            $stmt = $this->db->prepare("
                UPDATE Students SET
                    StudentNumber = ?, FirstName = ?, LastName = ?, Email = ?,
                    Phone = ?, DateOfBirth = ?, Address = ?, City = ?,
                    Country = ?, PostalCode = ?, ProgrammeID = ?,
                    EnrollmentDate = ?, Status = ?
                WHERE StudentID = ?
            ");

            return $stmt->execute([
                $data['student_number'],
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['date_of_birth'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['country'] ?? null,
                $data['postal_code'] ?? null,
                $data['programme_id'] ?? null,
                $data['enrollment_date'] ?? date('Y-m-d'),
                $data['status'] ?? 'Active',
                $studentId
            ]);
        } catch (Exception $e) {
            error_log("Update student error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete student
     */
    public function deleteStudent($studentId) {
        try {
            $stmt = $this->db->prepare("DELETE FROM Students WHERE StudentID = ?");
            return $stmt->execute([$studentId]);
        } catch (Exception $e) {
            error_log("Delete student error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get student count
     */
    public function getStudentCount($search = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM Students";
            $params = [];

            if ($search) {
                $sql .= " WHERE FirstName LIKE ? OR LastName LIKE ? OR Email LIKE ? OR StudentNumber LIKE ?";
                $searchTerm = "%$search%";
                $params = array_fill(0, 4, $searchTerm);
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            error_log("Get student count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get students by programme
     */
    public function getStudentsByProgramme($programmeId) {
        try {
            $stmt = $this->db->prepare("
                SELECT s.StudentID, s.FirstName, s.LastName, s.Email,
                       s.StudentNumber, s.Status, s.EnrollmentDate
                FROM Students s
                WHERE s.ProgrammeID = ?
                ORDER BY s.LastName, s.FirstName
            ");
            $stmt->execute([$programmeId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Get students by programme error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate unique student number
     */
    public function generateStudentNumber() {
        $year = date('Y');
        $prefix = substr($year, 2) . '000000';
        
        try {
            $stmt = $this->db->prepare("
                SELECT MAX(StudentNumber) as max_number
                FROM Students
                WHERE StudentNumber LIKE ?
            ");
            $stmt->execute([$prefix . '%']);
            $result = $stmt->fetch();
            
            if ($result && $result['max_number']) {
                $lastNumber = intval(substr($result['max_number'], -6));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        } catch (Exception $e) {
            error_log("Generate student number error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get student statistics
     */
    public function getStudentStatistics() {
        try {
            // Get total students
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM Students");
            $stmt->execute();
            $total = $stmt->fetch()['total'];

            // Get students by status
            $stmt = $this->db->prepare("
                SELECT Status, COUNT(*) as count
                FROM Students
                GROUP BY Status
            ");
            $stmt->execute();
            $byStatus = $stmt->fetchAll();

            // Get students by programme
            $stmt = $this->db->prepare("
                SELECT p.ProgrammeName, COUNT(s.StudentID) as count
                FROM Programmes p
                LEFT JOIN Students s ON p.ProgrammeID = s.ProgrammeID
                GROUP BY p.ProgrammeID, p.ProgrammeName
                ORDER BY count DESC
            ");
            $stmt->execute();
            $byProgramme = $stmt->fetchAll();

            return [
                'total' => $total,
                'by_status' => $byStatus,
                'by_programme' => $byProgramme
            ];
        } catch (Exception $e) {
            error_log("Get student statistics error: " . $e->getMessage());
            return [
                'total' => 0,
                'by_status' => [],
                'by_programme' => []
            ];
        }
    }
}
?> 