<?php
// award/actions.php - Single Table Approach (Simplified)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Database connection
$servername = "localhost";
$password = "&k?XO;WgA7";
$username = "u999765516_application";
$dbname = "u999765516_application";

try {
    $con = new mysqli($servername, $username, $password, $dbname);
    if ($con->connect_error) {
        throw new Exception("Connection failed: " . $con->connect_error);
    }
    $con->set_charset("utf8mb4");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $e->getMessage()
    ]);
    exit();
}

$response = ["success" => false, "message" => "Invalid Request"];
$method = $_SERVER['REQUEST_METHOD'];

// API key validation
$validApiKey = 'b1daf1bbc7bbd214045af';

// Get rank points based on rank
function getRankPoints($rank) {
    switch ((int)$rank) {
        case 1: return 100;
        case 2: return 80;
        case 3: return 60;
        default: return 0;
    }
}

try {
    // API key validation
    if (!isset($_GET['api']) || $_GET['api'] !== $validApiKey) {
        http_response_code(401);
        $response = ["success" => false, "message" => "Unauthorized Access - Invalid API key"];
    }
    
    // Create/Update single student_rankings table with all necessary fields
    elseif ($method === "POST" && isset($_GET['action']) && $_GET['action'] === 'setup_tables') {
        
        // Drop old table to ensure clean structure (you may want to backup data first)
        // $con->query("DROP TABLE IF EXISTS student_rankings");
        
        // Create simplified student_rankings table with all fields
        $createRankingsTable = "
            CREATE TABLE IF NOT EXISTS student_rankings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                jamia_id VARCHAR(50) NOT NULL,
                student_name VARCHAR(100) NOT NULL,
                program_name VARCHAR(100) NOT NULL,
                program_type ENUM('individual', 'group') DEFAULT 'individual',
                rank_position INT NOT NULL,
                points_eligible INT NOT NULL,
                points_assigned INT NULL,
                is_assigned TINYINT(1) DEFAULT 0,
                assigned_date DATETIME NULL,
                assigned_by VARCHAR(50) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_student_program_rank (jamia_id, program_name, rank_position),
                INDEX idx_jamia_id (jamia_id),
                INDEX idx_student_name (student_name),
                INDEX idx_program (program_name),
                INDEX idx_program_type (program_type),
                INDEX idx_rank (rank_position)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        if ($con->query($createRankingsTable)) {
            // Add student_name column if upgrading from old structure
            $con->query("ALTER TABLE student_rankings ADD COLUMN IF NOT EXISTS student_name VARCHAR(100) NOT NULL AFTER jamia_id");
            
            // Add program_type column if it doesn't exist
            $con->query("ALTER TABLE student_rankings ADD COLUMN IF NOT EXISTS program_type ENUM('individual', 'group') DEFAULT 'individual' AFTER program_name");
            
            // Update existing records to individual if NULL
            $con->query("UPDATE student_rankings SET program_type = 'individual' WHERE program_type IS NULL OR program_type = ''");
            
            http_response_code(200);
            $response = [
                "success" => true,
                "message" => "Single table created/updated successfully - no more JOIN complexity!"
            ];
        } else {
            throw new Exception("Failed to create table: " . $con->error);
        }
    }
    
    // Add student ranking for a program
    elseif ($method === "POST" && isset($_GET['action']) && $_GET['action'] === 'add_ranking') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception("Invalid JSON input");
        }
        
        $jamiaId = $con->real_escape_string(trim($input['jamia_id']));
        $studentName = $con->real_escape_string(trim($input['student_name']));
        $programName = $con->real_escape_string(trim($input['program_name']));
        $programType = isset($input['program_type']) ? $con->real_escape_string($input['program_type']) : 'individual';
        $rankPosition = (int)$input['rank_position'];
        $pointsEligible = getRankPoints($rankPosition);
        
        if ($pointsEligible <= 0) {
            http_response_code(400);
            $response = ["success" => false, "message" => "Invalid rank. Only ranks 1-3 are eligible for points"];
        } else {
            // Insert or update ranking - no need for student table check
            $insertRanking = "
                INSERT INTO student_rankings (jamia_id, student_name, program_name, program_type, rank_position, points_eligible) 
                VALUES (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                    student_name = VALUES(student_name),
                    rank_position = VALUES(rank_position),
                    points_eligible = VALUES(points_eligible),
                    program_type = VALUES(program_type),
                    is_assigned = 0,
                    points_assigned = NULL,
                    assigned_date = NULL,
                    assigned_by = NULL
            ";
            
            $insertStmt = $con->prepare($insertRanking);
            $insertStmt->bind_param("ssssii", $jamiaId, $studentName, $programName, $programType, $rankPosition, $pointsEligible);
            
            if ($insertStmt->execute()) {
                http_response_code(201);
                $response = [
                    "success" => true,
                    "message" => "Student ranking added successfully",
                    "jamia_id" => $jamiaId,
                    "student_name" => $studentName,
                    "program_name" => $programName,
                    "program_type" => $programType,
                    "rank_position" => $rankPosition,
                    "points_eligible" => $pointsEligible
                ];
            } else {
                throw new Exception("Failed to add ranking: " . $con->error);
            }
        }
    }
    
    // Get all student rankings (ONLY INDIVIDUAL programs) - No JOIN needed!
    elseif ($method === "GET" && (!isset($_GET['action']) || $_GET['action'] === 'get_rankings')) {
        
        $sql = "
            SELECT 
                id,
                jamia_id,
                student_name,
                program_name,
                program_type,
                rank_position,
                points_eligible,
                points_assigned,
                is_assigned,
                assigned_date,
                assigned_by,
                created_at
            FROM student_rankings
            WHERE program_type = 'individual'
            ORDER BY program_name, rank_position, student_name
        ";
        
        $result = $con->query($sql);
        
        if (!$result) {
            throw new Exception("Database query failed: " . $con->error);
        }
        
        $rankings = [];
        while ($row = $result->fetch_assoc()) {
            $rankings[] = [
                'id' => (int)$row['id'],
                'jamia_id' => $row['jamia_id'],
                'student_name' => $row['student_name'],
                'program_name' => $row['program_name'],
                'program_type' => $row['program_type'],
                'rank_position' => (int)$row['rank_position'],
                'points_eligible' => (int)$row['points_eligible'],
                'points_assigned' => $row['points_assigned'] ? (int)$row['points_assigned'] : null,
                'is_assigned' => (bool)$row['is_assigned'],
                'assigned_date' => $row['assigned_date'],
                'assigned_by' => $row['assigned_by'],
                'can_assign' => !(bool)$row['is_assigned']
            ];
        }
        
        http_response_code(200);
        $response = [
            "success" => true,
            "data" => $rankings,
            "total_rankings" => count($rankings),
            "note" => "Single table approach - no JOIN complexity, only individual programs shown"
        ];
    }
    
    // Get wallet balance for a specific student
    elseif ($method === "GET" && isset($_GET['action']) && $_GET['action'] === 'get_wallet' && isset($_GET['jamia_id'])) {
        $jamiaId = $con->real_escape_string($_GET['jamia_id']);
        
        $sql = "SELECT points FROM glocal_points WHERE student = ? ORDER BY id DESC LIMIT 1";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $jamiaId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $balance = 0;
        if ($row = $result->fetch_assoc()) {
            $balance = (int)$row['points'];
        }
        
        http_response_code(200);
        $response = [
            "success" => true,
            "wallet_points" => $balance,
            "jamia_id" => $jamiaId
        ];
    }
    
    // Assign points to student for specific program (only individual programs)
    elseif ($method === "POST" && isset($_GET['action']) && $_GET['action'] === 'assign_points') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception("Invalid JSON input");
        }
        
        if (!isset($input['ranking_id'])) {
            http_response_code(400);
            $response = ["success" => false, "message" => "Missing required parameter: ranking_id"];
        } else {
            $rankingId = (int)$input['ranking_id'];
            
            // Start transaction
            $con->autocommit(FALSE);
            
            try {
                // Get ranking details (ensure it's individual program) - Simple SELECT
                $getRanking = "
                    SELECT * FROM student_rankings
                    WHERE id = ? AND is_assigned = 0 AND program_type = 'individual'
                ";
                $getRankingStmt = $con->prepare($getRanking);
                $getRankingStmt->bind_param("i", $rankingId);
                $getRankingStmt->execute();
                $rankingResult = $getRankingStmt->get_result();
                
                if (!$rankingRow = $rankingResult->fetch_assoc()) {
                    throw new Exception("Ranking not found, already assigned, or not an individual program");
                }
                
                $jamiaId = $rankingRow['jamia_id'];
                $studentName = $rankingRow['student_name'];
                $points = (int)$rankingRow['points_eligible'];
                
                // Update ranking as assigned
                $updateRanking = "
                    UPDATE student_rankings 
                    SET is_assigned = 1, 
                        points_assigned = ?, 
                        assigned_date = NOW(), 
                        assigned_by = 'admin'
                    WHERE id = ?
                ";
                $updateStmt = $con->prepare($updateRanking);
                $updateStmt->bind_param("ii", $points, $rankingId);
                
                if (!$updateStmt->execute()) {
                    throw new Exception("Failed to update ranking: " . $con->error);
                }
                
                // Get current wallet balance
                $walletSql = "SELECT points FROM glocal_points WHERE student = ? ORDER BY id DESC LIMIT 1";
                $walletStmt = $con->prepare($walletSql);
                $walletStmt->bind_param("s", $jamiaId);
                $walletStmt->execute();
                $walletResult = $walletStmt->get_result();
                
                $currentBalance = 0;
                if ($walletRow = $walletResult->fetch_assoc()) {
                    $currentBalance = (int)$walletRow['points'];
                }
                
                $newBalance = $currentBalance + $points;
                
                // Update wallet balance
                $walletInsertSql = "
                    INSERT INTO glocal_points (student, points) VALUES (?, ?) 
                    ON DUPLICATE KEY UPDATE points = ?
                ";
                $walletInsertStmt = $con->prepare($walletInsertSql);
                $walletInsertStmt->bind_param("sii", $jamiaId, $newBalance, $newBalance);
                
                if (!$walletInsertStmt->execute()) {
                    throw new Exception("Failed to update wallet balance: " . $con->error);
                }
                
                // Commit transaction
                $con->commit();
                $con->autocommit(TRUE);
                
                http_response_code(201);
                $response = [
                    "success" => true,
                    "message" => "Points assigned successfully!",
                    "points_assigned" => $points,
                    "jamia_id" => $jamiaId,
                    "student_name" => $studentName,
                    "program_name" => $rankingRow['program_name'],
                    "program_type" => $rankingRow['program_type'],
                    "rank_position" => (int)$rankingRow['rank_position'],
                    "new_wallet_balance" => $newBalance
                ];
                
            } catch (Exception $e) {
                $con->rollback();
                $con->autocommit(TRUE);
                throw $e;
            }
        }
    }
    
    // Delete a ranking (only if not assigned)
    elseif ($method === "DELETE" && isset($_GET['action']) && $_GET['action'] === 'delete_ranking' && isset($_GET['id'])) {
        $rankingId = (int)$_GET['id'];
        
        // Check if already assigned
        $checkSql = "SELECT is_assigned, program_type FROM student_rankings WHERE id = ?";
        $checkStmt = $con->prepare($checkSql);
        $checkStmt->bind_param("i", $rankingId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkRow = $checkResult->fetch_assoc()) {
            if ($checkRow['is_assigned']) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Cannot delete assigned ranking"];
            } else {
                $deleteSql = "DELETE FROM student_rankings WHERE id = ?";
                $deleteStmt = $con->prepare($deleteSql);
                $deleteStmt->bind_param("i", $rankingId);
                
                if ($deleteStmt->execute()) {
                    http_response_code(200);
                    $response = [
                        "success" => true,
                        "message" => "Ranking deleted successfully"
                    ];
                } else {
                    throw new Exception("Failed to delete ranking: " . $con->error);
                }
            }
        } else {
            http_response_code(404);
            $response = ["success" => false, "message" => "Ranking not found"];
        }
    }
    
    // Migration helper: Copy existing data from students table if needed
    elseif ($method === "POST" && isset($_GET['action']) && $_GET['action'] === 'migrate_data') {
        
        // Check if students table exists
        $checkStudentsTable = "SHOW TABLES LIKE 'students'";
        $studentsExists = $con->query($checkStudentsTable);
        
        if ($studentsExists && $studentsExists->num_rows > 0) {
            // Update student_rankings with student names from students table
            $updateQuery = "
                UPDATE student_rankings sr
                JOIN students s ON sr.jamia_id = s.jamia_id
                SET sr.student_name = s.student_name
                WHERE sr.student_name IS NULL OR sr.student_name = ''
            ";
            
            if ($con->query($updateQuery)) {
                $affectedRows = $con->affected_rows;
                http_response_code(200);
                $response = [
                    "success" => true,
                    "message" => "Migration completed successfully",
                    "updated_records" => $affectedRows
                ];
            } else {
                throw new Exception("Migration failed: " . $con->error);
            }
        } else {
            http_response_code(404);
            $response = ["success" => false, "message" => "Students table not found - no migration needed"];
        }
    }
    
    else {
        http_response_code(400);
        $response = ["success" => false, "message" => "Invalid action or method"];
    }
    
} catch (Exception $e) {
    if (isset($con)) {
        $con->rollback();
        $con->autocommit(TRUE);
    }
    
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    $response = [
        "success" => false,
        "message" => $e->getMessage()
    ];
}

// Close database connection
if (isset($con)) {
    $con->close();
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>