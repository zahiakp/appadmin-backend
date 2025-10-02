<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

$con = new mysqli("localhost", "u999765516_application", "D|O^QBm|N^7g", "u999765516_application");
if ($con->connect_error) die(json_encode(["success" => false, "message" => "DB error"]));

$key = 'b1daf1bbc7bbd214045af';
if (!isset($_GET['api']) || $_GET['api'] !== $key) die(json_encode(["success" => false, "message" => "Unauthorized"]));

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ============================================
// Check if already assigned
// ============================================
if ($method === "GET" && $action === 'check_assignment') {
    $rid = (int)($_GET['result_id'] ?? 0);
    $pid = (int)($_GET['program_id'] ?? 0);
    $jid = trim($_GET['jamia_id'] ?? '');
    
    if (empty($jid)) {
        echo json_encode(["success" => false, "is_assigned" => false, "message" => "Missing jamia_id"]);
        exit;
    }
    
    // Check by result_id, program_id AND jamia_id
    $stmt = $con->prepare("SELECT * FROM assignment_status WHERE result_id=? AND program_id=? AND jamia_id=?");
    $stmt->bind_param("iis", $rid, $pid, $jid);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo json_encode([
        "success" => true,
        "is_assigned" => $result->num_rows > 0,
        "data" => $result->num_rows > 0 ? $result->fetch_assoc() : null
    ]);
    exit;
}

// ============================================
// Assign points to a student
// ============================================
if ($method === "POST" && $action === 'add_and_assign') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $rid = (int)($input['result_id'] ?? 0);
    $pid = (int)($input['program_id'] ?? 0);
    $jid = trim($input['jamia_id'] ?? '');
    $name = trim($input['student_name'] ?? '');
    $prog = trim($input['program_name'] ?? '');
    $rank = (int)($input['rank_position'] ?? 0);
    
    // Validation
    if (empty($jid)) {
        echo json_encode(["success" => false, "message" => "Missing jamia_id"]);
        exit;
    }
    
    if (empty($name)) {
        echo json_encode(["success" => false, "message" => "Missing student_name"]);
        exit;
    }
    
    if ($rid <= 0 || $pid <= 0) {
        echo json_encode(["success" => false, "message" => "Invalid result_id or program_id"]);
        exit;
    }
    
    $pts = [1 => 100, 2 => 80, 3 => 60][$rank] ?? 0;
    
    if ($pts <= 0) {
        echo json_encode(["success" => false, "message" => "Only ranks 1-3 eligible for points"]);
        exit;
    }
    
    // Check if THIS SPECIFIC STUDENT already got points for THIS SPECIFIC RESULT in THIS PROGRAM
    // This allows the same student to get points in different programs
    $check = $con->prepare("SELECT id, assigned_date FROM assignment_status WHERE result_id=? AND program_id=? AND jamia_id=?");
    $check->bind_param("iis", $rid, $pid, $jid);
    $check->execute();
    $checkResult = $check->get_result();
    
    if ($checkResult->num_rows > 0) {
        $existing = $checkResult->fetch_assoc();
        echo json_encode([
            "success" => false, 
            "message" => "Points already assigned to this student for this program on " . $existing['assigned_date']
        ]);
        exit;
    }
    
    $con->begin_transaction();
    
    try {
        // Insert assignment status with current timestamp
        $stmt1 = $con->prepare("INSERT INTO assignment_status (result_id, program_id, jamia_id, student_name, program_name, rank_position, points_assigned, assigned_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt1->bind_param("iissiii", $rid, $pid, $jid, $name, $prog, $rank, $pts);
        
        if (!$stmt1->execute()) {
            throw new Exception("Failed to insert assignment record: " . $stmt1->error);
        }
        
        // Get current points from glocal_points
        $getPoints = $con->prepare("SELECT points FROM glocal_points WHERE student=?");
        $getPoints->bind_param("s", $jid);
        $getPoints->execute();
        $result = $getPoints->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Student exists - UPDATE their balance
            $current = (int)$row['points'];
            $newBalance = $current + $pts;
            
            $updatePoints = $con->prepare("UPDATE glocal_points SET points=? WHERE student=?");
            $updatePoints->bind_param("is", $newBalance, $jid);
            
            if (!$updatePoints->execute()) {
                throw new Exception("Failed to update points: " . $updatePoints->error);
            }
        } else {
            // Student doesn't exist - INSERT new record
            $newBalance = $pts;
            $insertPoints = $con->prepare("INSERT INTO glocal_points (student, points) VALUES (?, ?)");
            $insertPoints->bind_param("si", $jid, $newBalance);
            
            if (!$insertPoints->execute()) {
                throw new Exception("Failed to insert new student: " . $insertPoints->error);
            }
        }
        
        $con->commit();
        
        echo json_encode([
            "success" => true,
            "message" => "Points assigned successfully",
            "points_assigned" => $pts,
            "student_name" => $name,
            "jamia_id" => $jid,
            "program_name" => $prog,
            "rank_position" => $rank,
            "new_wallet_balance" => $newBalance
        ]);
        
    } catch (Exception $e) {
        $con->rollback();
        echo json_encode(["success" => false, "message" => "Transaction failed: " . $e->getMessage()]);
    }
    
    exit;
}

echo json_encode(["success" => false, "message" => "Invalid action"]);
$con->close();
?>