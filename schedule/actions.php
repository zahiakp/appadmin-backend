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
                // Fetch all stages for a specific date (for cross-stage validation)
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
                // Fetch specific stage data
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
        
        // Enhanced validation with cross-stage checking
        $conflicts = [];
        $valid_items = [];
        
        // Get unique dates from the input data for cross-stage validation
        $dates = array_unique(array_filter(array_column($data, 'date')));
        $existing_programs_by_date = [];
        
        // Pre-fetch ALL existing programs (for date and cross-stage validation)
        $all_existing_programs = [];
        $all_programs_sql = "SELECT program_name, stage, date FROM schedule";
        $all_programs_stmt = $conn->prepare($all_programs_sql);
        
        if ($all_programs_stmt->execute()) {
            $all_programs_result = $all_programs_stmt->get_result();
            while ($row = $all_programs_result->fetch_assoc()) {
                $all_existing_programs[] = [
                    'program_name' => $row['program_name'],
                    'stage' => $row['stage'],
                    'date' => $row['date']
                ];
            }
        }
        
        // Also organize by date for efficient lookup
        $existing_programs_by_date = [];
        foreach ($all_existing_programs as $program) {
            if (!isset($existing_programs_by_date[$program['date']])) {
                $existing_programs_by_date[$program['date']] = [];
            }
            $existing_programs_by_date[$program['date']][] = [
                'program_name' => $program['program_name'],
                'stage' => $program['stage']
            ];
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
            
            // Validate required fields for non-empty rows
            if (empty($program_name) || empty($category) || empty($date) || empty($start) || empty($end)) {
                $conflicts[] = "Row " . ($index + 1) . ": All fields are required (Program: '{$program_name}', Category: '{$category}')";
                continue;
            }
            
            // Validate date format
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $conflicts[] = "Row " . ($index + 1) . ": Invalid date format for '{$program_name}'. Expected YYYY-MM-DD";
                continue;
            }
            
            // Validate time format
            if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $start) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $end)) {
                $conflicts[] = "Row " . ($index + 1) . ": Invalid time format for '{$program_name}'";
                continue;
            }
            
            // Validate time logic (start < end)
            if ($start >= $end) {
                $conflicts[] = "Row " . ($index + 1) . ": Start time must be before end time for '{$program_name}'";
                continue;
            }
            
            // NEW: Check if program already exists on ANY date (block duplicate programs entirely)
            $existing_program_check = array_filter($all_existing_programs, function($existing) use ($program_name, $stage, $date) {
                // Exclude current stage and date combination (allow updating current selection)
                return $existing['program_name'] === $program_name && 
                       !($existing['stage'] == $stage && $existing['date'] === $date);
            });
            
            if (!empty($existing_program_check)) {
                $existing = $existing_program_check[0]; // Get first match
                $conflicts[] = "Row " . ($index + 1) . ": Program '{$program_name}' is already scheduled on {$existing['date']} Stage {$existing['stage']}. Each program can only be scheduled once across all dates and stages.";
                continue;
            }
            
            // Check for cross-stage conflicts on the same date (additional validation)
            if (isset($existing_programs_by_date[$date])) {
                foreach ($existing_programs_by_date[$date] as $existing) {
                    if ($existing['program_name'] === $program_name && $existing['stage'] != $stage) {
                        $conflicts[] = "Row " . ($index + 1) . ": Program '{$program_name}' is already scheduled on Stage {$existing['stage']} for {$date}. A program cannot be scheduled on multiple stages on the same date.";
                        continue 2; // Skip to next schedule item
                    }
                }
            }
            
            // Check for duplicates within the current submission (any date)
            $duplicates_in_submission = array_filter($valid_items, function($item) use ($program_name) {
                return $item['program_name'] === $program_name;
            });
            
            if (!empty($duplicates_in_submission)) {
                $conflicts[] = "Row " . ($index + 1) . ": Program '{$program_name}' appears multiple times in current submission. Each program can only be scheduled once.";
                continue;
            }
            
            // Add to valid items
            $valid_items[] = [
                'program_name' => $program_name,
                'category' => $category,
                'stage' => $stage,
                'date' => $date,
                'start' => $start,
                'end' => $end
            ];
        }
        
        // If there are validation errors, return them
        if (!empty($conflicts)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Validation errors detected",
                "conflicts" => $conflicts
            ]);
            exit();
        }
        
        // If no valid items to process
        if (empty($valid_items)) {
            echo json_encode([
                "success" => true,
                "message" => "No valid items to process",
                "items_processed" => 0
            ]);
            exit();
        }
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // FIXED: Clear existing schedule for this stage AND the specific dates being updated
            // Get all dates that are being updated
            $dates_to_clear = array_unique(array_column($valid_items, 'date'));
            
            foreach ($dates_to_clear as $date_to_clear) {
                $clear_sql = "DELETE FROM schedule WHERE stage = ? AND date = ?";
                $clear_stmt = $conn->prepare($clear_sql);
                
                if (!$clear_stmt) {
                    throw new Exception("Failed to prepare clear statement: " . $conn->error);
                }
                
                $clear_stmt->bind_param("is", $stage, $date_to_clear);
                
                if (!$clear_stmt->execute()) {
                    throw new Exception("Failed to clear existing schedule for stage {$stage} on {$date_to_clear}: " . $clear_stmt->error);
                }
                $clear_stmt->close();
            }
            
            // Final validation: Check if program exists anywhere else in the database
            $final_conflicts = [];
            foreach ($valid_items as $item) {
                $check_sql = "SELECT stage, date FROM schedule WHERE program_name = ? AND NOT (stage = ? AND date = ?)";
                $check_stmt = $conn->prepare($check_sql);
                $check_stmt->bind_param("sis", $item['program_name'], $stage, $item['date']);
                
                if ($check_stmt->execute()) {
                    $check_result = $check_stmt->get_result();
                    if ($check_result->num_rows > 0) {
                        $conflict_row = $check_result->fetch_assoc();
                        $final_conflicts[] = "Program '{$item['program_name']}' is already scheduled on {$conflict_row['date']} Stage {$conflict_row['stage']}. Each program can only be scheduled once.";
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
                } else {
                    error_log("Failed to insert item: " . $insert_stmt->error . " - Data: " . json_encode($item));
                }
            }
            
            $insert_stmt->close();
            
            if ($successful_inserts > 0) {
                $conn->commit();
                http_response_code(201);
                $response = [
                    "success" => true, 
                    "message" => "Schedule synced successfully with unique program validation",
                    "items_processed" => $successful_inserts,
                    "total_items" => count($valid_items),
                    "dates_updated" => $dates_to_clear
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