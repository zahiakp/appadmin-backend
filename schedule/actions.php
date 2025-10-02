<?php
include '../inc/head.php';
include '../inc/const.php';
include '../inc/db.php';

$response = ["success" => false, "message" => "Invalid Request"];
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if (isset($_GET['api']) && $_GET['api'] == API) {
    if ($method == "GET") {
        if (isset($_GET['stage']) || isset($_GET['all_stages'])) {
            $data = [];

            if (isset($_GET['all_stages']) && $_GET['all_stages'] == 'true') {
                if (isset($_GET['date'])) {
                    $date = $_GET['date'];
                    $sql = "SELECT * FROM schedule WHERE date = ? ORDER BY stage, start";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $date);
                } else {
                    $sql = "SELECT * FROM schedule ORDER BY stage, start";
                    $stmt = $conn->prepare($sql);
                }
            } else {
                $stage = intval($_GET['stage']);
                
                if (isset($_GET['date'])) {
                    $date = $_GET['date'];
                    $sql = "SELECT * FROM schedule WHERE stage = ? AND date = ? ORDER BY start";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("is", $stage, $date);
                } else {
                    $sql = "SELECT * FROM schedule WHERE stage = ? ORDER BY start";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $stage);
                }
            }

            if ($stmt && $stmt->execute()) {
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                $response = ["success" => true, "data" => $data];
            } else {
                $response = ["success" => false, "message" => "Error executing query"];
            }
        } else {
            $response = ["success" => false, "message" => "Missing required 'stage' parameter or 'all_stages' flag."];
        }

    } elseif ($method == "DELETE") {
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Missing required 'id' parameter"
            ]);
            exit();
        }

        $id = intval($_GET['id']);
        
        try {
            $check_sql = "SELECT program_name, stage, date FROM schedule WHERE id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("i", $id);
            
            if ($check_stmt->execute()) {
                $result = $check_stmt->get_result();
                if ($result->num_rows === 0) {
                    http_response_code(404);
                    echo json_encode([
                        "success" => false,
                        "message" => "Schedule item not found"
                    ]);
                    exit();
                }
                $item = $result->fetch_assoc();
            }
            $check_stmt->close();
            
            $delete_sql = "DELETE FROM schedule WHERE id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            
            if (!$delete_stmt) {
                throw new Exception("Failed to prepare delete statement: " . $conn->error);
            }
            
            $delete_stmt->bind_param("i", $id);
            
            if ($delete_stmt->execute()) {
                if ($delete_stmt->affected_rows > 0) {
                    http_response_code(200);
                    $response = [
                        "success" => true,
                        "message" => "Schedule item deleted successfully",
                        "deleted_item" => $item
                    ];
                } else {
                    http_response_code(404);
                    $response = [
                        "success" => false,
                        "message" => "Schedule item not found or already deleted"
                    ];
                }
            } else {
                throw new Exception("Failed to delete item: " . $delete_stmt->error);
            }
            
            $delete_stmt->close();
            
        } catch (Exception $e) {
            http_response_code(500);
            $response = [
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            ];
        }

    } elseif ($method == "POST") {
        if (!isset($input['stage']) || !isset($input['data'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Missing required fields: stage or data"
            ]);
            exit();
        }

        $stage = intval($input['stage']);
        $data = $input['data'];
        
        $conflicts = [];
        $valid_items = [];
        
        // Fetch all existing programs with category
        $all_existing_programs = [];
        $all_programs_sql = "SELECT program_name, category, stage, date FROM schedule";
        $all_programs_stmt = $conn->prepare($all_programs_sql);
        
        if ($all_programs_stmt->execute()) {
            $all_programs_result = $all_programs_stmt->get_result();
            while ($row = $all_programs_result->fetch_assoc()) {
                $all_existing_programs[] = [
                    'program_name' => $row['program_name'],
                    'category' => $row['category'],
                    'stage' => $row['stage'],
                    'date' => $row['date']
                ];
            }
        }
        
        foreach ($data as $index => $schedule) {
            // Skip completely empty rows
            if (empty(trim($schedule['program_name'] ?? '')) && 
                empty(trim($schedule['category'] ?? '')) && 
                empty(trim($schedule['date'] ?? '')) && 
                empty(trim($schedule['start'] ?? '')) && 
                empty(trim($schedule['end'] ?? ''))) {
                continue;
            }
            
            $program_name = trim($schedule['program_name'] ?? '');
            $category = trim($schedule['category'] ?? '');
            $date = $schedule['date'] ?? '';
            $start = $schedule['start'] ?? '';
            $end = $schedule['end'] ?? '';
            
            // Required fields validation
            if (empty($program_name) || empty($category) || empty($date) || empty($start) || empty($end)) {
                $conflicts[] = "Row " . ($index + 1) . ": All fields are required (Program: '{$program_name}', Category: '{$category}')";
                continue;
            }
            
            // Date format validation
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $conflicts[] = "Row " . ($index + 1) . ": Invalid date format for '{$program_name}'. Expected YYYY-MM-DD";
                continue;
            }
            
            // Time format validation
            if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $start) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $end)) {
                $conflicts[] = "Row " . ($index + 1) . ": Invalid time format for '{$program_name}'";
                continue;
            }
            
            // Time logic validation
            if ($start >= $end) {
                $conflicts[] = "Row " . ($index + 1) . ": Start time must be before end time for '{$program_name}'";
                continue;
            }
            
            // UPDATED: Check if same program+category combination exists on different stage/date
            $existing_program_check = array_filter($all_existing_programs, function($existing) use ($program_name, $category, $stage, $date) {
                return $existing['program_name'] === $program_name && 
                       $existing['category'] === $category &&
                       !($existing['stage'] == $stage && $existing['date'] === $date);
            });
            
            if (!empty($existing_program_check)) {
                $existing = array_values($existing_program_check)[0];
                $conflicts[] = "Row " . ($index + 1) . ": Program '{$program_name}' in category '{$category}' is already scheduled on {$existing['date']} Stage {$existing['stage']}. Each program-category combination can only be scheduled once.";
                continue;
            }
            
            // UPDATED: Check for duplicate program+category on same date different stage
            $same_date_conflicts = array_filter($all_existing_programs, function($existing) use ($program_name, $category, $stage, $date) {
                return $existing['program_name'] === $program_name && 
                       $existing['category'] === $category &&
                       $existing['date'] === $date && 
                       $existing['stage'] != $stage;
            });
            
            if (!empty($same_date_conflicts)) {
                $existing = array_values($same_date_conflicts)[0];
                $conflicts[] = "Row " . ($index + 1) . ": Program '{$program_name}' in category '{$category}' is already scheduled on Stage {$existing['stage']} for {$date}.";
                continue;
            }
            
            // Check for duplicates within current submission
            $duplicates_in_submission = array_filter($valid_items, function($item) use ($program_name, $category) {
                return $item['program_name'] === $program_name && $item['category'] === $category;
            });
            
            if (!empty($duplicates_in_submission)) {
                $conflicts[] = "Row " . ($index + 1) . ": Program '{$program_name}' in category '{$category}' appears multiple times in current submission.";
                continue;
            }
            
            $valid_items[] = [
                'program_name' => $program_name,
                'category' => $category,
                'stage' => $stage,
                'date' => $date,
                'start' => $start,
                'end' => $end
            ];
        }
        
        if (!empty($conflicts)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Validation errors detected",
                "conflicts" => $conflicts
            ]);
            exit();
        }
        
        if (empty($valid_items)) {
            echo json_encode([
                "success" => true,
                "message" => "No valid items to process",
                "items_processed" => 0
            ]);
            exit();
        }
        
        $conn->begin_transaction();
        
        try {
            // Clear existing schedule for this stage and date(s)
            $dates_to_clear = array_unique(array_column($valid_items, 'date'));
            
            foreach ($dates_to_clear as $date_to_clear) {
                $clear_sql = "DELETE FROM schedule WHERE stage = ? AND date = ?";
                $clear_stmt = $conn->prepare($clear_sql);
                
                if (!$clear_stmt) {
                    throw new Exception("Failed to prepare clear statement: " . $conn->error);
                }
                
                $clear_stmt->bind_param("is", $stage, $date_to_clear);
                
                if (!$clear_stmt->execute()) {
                    throw new Exception("Failed to clear existing schedule: " . $clear_stmt->error);
                }
                $clear_stmt->close();
            }
            
            // Final cross-check before insert
            $final_conflicts = [];
            foreach ($valid_items as $item) {
                $check_sql = "SELECT stage, date FROM schedule WHERE program_name = ? AND category = ? AND NOT (stage = ? AND date = ?)";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("ssis", $item['program_name'], $item['category'], $stage, $item['date']);
                
                if ($check_stmt->execute()) {
                    $check_result = $check_stmt->get_result();
                    if ($check_result->num_rows > 0) {
                        $conflict_row = $check_result->fetch_assoc();
                        $final_conflicts[] = "Program '{$item['program_name']}' in category '{$item['category']}' is already scheduled on {$conflict_row['date']} Stage {$conflict_row['stage']}.";
                    }
                }
                $check_stmt->close();
            }
            
            if (!empty($final_conflicts)) {
                $conn->rollback();
                http_response_code(400);
                echo json_encode([
                    "success" => false,
                    "message" => "Cross-stage conflicts detected",
                    "conflicts" => $final_conflicts
                ]);
                exit();
            }
            
            // Insert new schedule items
            $insert_sql = "INSERT INTO schedule (program_name, category, stage, date, start, end) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            
            if (!$insert_stmt) {
                throw new Exception("Failed to prepare insert statement: " . $conn->error);
            }
            
            $successful_inserts = 0;
            
            foreach ($valid_items as $item) {
                $insert_stmt->bind_param("ssisss", 
                    $item['program_name'], 
                    $item['category'], 
                    $item['stage'], 
                    $item['date'], 
                    $item['start'], 
                    $item['end']
                );
                
                if ($insert_stmt->execute()) {
                    $successful_inserts++;
                }
            }
            
            $insert_stmt->close();
            
            if ($successful_inserts > 0) {
                $conn->commit();
                http_response_code(201);
                $response = [
                    "success" => true, 
                    "message" => "Schedule synced successfully",
                    "items_processed" => $successful_inserts,
                    "total_items" => count($valid_items)
                ];
            } else {
                $conn->rollback();
                throw new Exception("No items were successfully inserted");
            }
            
        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            $response = [
                "success" => false, 
                "message" => "Database error: " . $e->getMessage()
            ];
        }
        
    } else {
        http_response_code(405);
        $response = ["success" => false, "message" => "Invalid Method"];
    }
} else {
    http_response_code(401);
    $response = ["success" => false, "message" => "Unauthorized Access"];
}

echo json_encode($response);
?>