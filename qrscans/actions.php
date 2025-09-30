<?php

include '../inc/head.php';
include '../inc/const.php';
include '../inc/db.php';

$response = ["success" => false, "message" => "Invalid Request"];
$method = $_SERVER['REQUEST_METHOD'];

function handleOption($value)
{
    $pointsMap = [
        "Expert Convos" => 14,
        "Edu Login" => 14,
        "WriteWell Clinic" => 10,
        "Pro Chat" => 6,
        "Tranquil Wellness Hub" => 10,
    ];
    return $pointsMap[$value] ?? 0;
}

function getStudentPoints($conn, $student_id)
{
    try {
        $stmt = $conn->prepare('SELECT points FROM glocal_points WHERE student = ?');
        $stmt->bind_param('s', $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->num_rows > 0 ? $result->fetch_assoc()['points'] : null;
    } catch (Exception $e) {
        error_log("Error in getStudentPoints: " . $e->getMessage());
        return null;
    }
}

function isAlreadyScanned($conn, $event, $student)
{
    try {
        $stmt = $conn->prepare('SELECT 1 FROM qr_scans WHERE event = ? AND student = ? LIMIT 1');
        $stmt->bind_param('is', $event, $student);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasRow = $result->num_rows > 0;
        $stmt->close();
        
        error_log("Checking duplicate scan - Event: $event, Student: $student, Already scanned: " . ($hasRow ? 'YES' : 'NO'));
        
        return $hasRow;
    } catch (Exception $e) {
        error_log("Error in isAlreadyScanned: " . $e->getMessage());
        return true;
    }
}

function getEventType($conn, $id)
{
    try {
        $stmt = $conn->prepare('SELECT type FROM events WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->num_rows > 0 ? $result->fetch_assoc()['type'] : null;
    } catch (Exception $e) {
        error_log("Error in getEventType: " . $e->getMessage());
        return null;
    }
}

function validateEvent($conn, $event_id)
{
    try {
        $stmt = $conn->prepare('SELECT id FROM events WHERE id = ?');
        $stmt->bind_param('i', $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->num_rows > 0;
    } catch (Exception $e) {
        error_log("Error in validateEvent: " . $e->getMessage());
        return false;
    }
}

function checkScanFrequency($conn, $student_id, $event_id, $min_interval = 3)
{
    try {
        // Check last scan for THIS SPECIFIC EVENT only
        $stmt = $conn->prepare('SELECT MAX(scan_time) as last_scan FROM qr_scans WHERE student = ? AND event = ?');
        $stmt->bind_param('si', $student_id, $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            
            if ($row['last_scan']) {
                $last_scan_time = strtotime($row['last_scan']);
                $current_time = time();
                $time_diff = $current_time - $last_scan_time;
                
                if ($time_diff < $min_interval) {
                    error_log("Scan frequency violation - Student: $student_id, Event: $event_id, Time diff: $time_diff seconds");
                    return false;
                }
            }
        } else {
            $stmt->close();
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Error in checkScanFrequency: " . $e->getMessage());
        return false;
    }
}

// Main Logic
try {
    if (isset($_GET['api']) && $_GET['api'] == API) {

        // Get student points
        if ($method == "GET" && isset($_GET['ID'])) {
            $id = trim($_GET['ID']);
            if (empty($id)) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Student ID is required"];
            } else {
                $points = getStudentPoints($conn, $id);
                if ($points !== null) {
                    http_response_code(200);
                    $response = ["success" => true, "data" => $points];
                } else {
                    http_response_code(404);
                    $response = ["success" => false, "message" => "Student not found"];
                }
            }
        }

        // Admin scans student Jamia ID at event
        elseif ($method == "GET" && isset($_GET['event']) && isset($_GET['student'])) {
            $event = filter_var($_GET['event'], FILTER_VALIDATE_INT);
            $student = strtoupper(trim($_GET['student'])); // Jamia ID like 2019JM062

            // Validate Jamia ID format (basic validation)
            if (!preg_match('/^\d{4}[A-Z]{2}\d{3,4}$/', $student)) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Invalid Jamia ID format. Expected format: 2019JM062"];
            } elseif ($event === false || $event <= 0) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Invalid event ID"];
            } elseif (!checkScanFrequency($conn, $student, $event, 3)) {
                http_response_code(429);
                $response = ["success" => false, "message" => "This student was just scanned for this event. Please wait a moment."];
            } else {
                error_log("Admin scanning - Event: $event, Student Jamia ID: $student");
                
                // Check if student already scanned for this event
                if (isAlreadyScanned($conn, $event, $student)) {
                    http_response_code(409);
                    $response = ["success" => false, "message" => "Student has already been scanned for this event"];
                } else {
                    // Validate event exists
                    if (!validateEvent($conn, $event)) {
                        http_response_code(404);
                        $response = ["success" => false, "message" => "Event not found"];
                    } else {
                        $eventType = getEventType($conn, $event);
                        if (!$eventType) {
                            http_response_code(404);
                            $response = ["success" => false, "message" => "Event type not found"];
                        } else {
                            $points = handleOption($eventType);
                            if ($points <= 0) {
                                http_response_code(400);
                                $response = ["success" => false, "message" => "Invalid event type for points calculation"];
                            } else {
                                // Begin transaction
                                $conn->begin_transaction();
                                
                                try {
                                    // Double-check within transaction
                                    if (isAlreadyScanned($conn, $event, $student)) {
                                        $conn->rollback();
                                        http_response_code(409);
                                        $response = ["success" => false, "message" => "Student has already been scanned for this event"];
                                    } else {
                                        $currentPoints = getStudentPoints($conn, $student);

                                        // Update student points
                                        if ($currentPoints === null) {
                                            $stmt = $conn->prepare('INSERT INTO glocal_points(student, points) VALUES(?, ?)');
                                            $stmt->bind_param('si', $student, $points);
                                            if (!$stmt->execute()) {
                                                throw new Exception("Failed to create student points record");
                                            }
                                            $stmt->close();
                                        } else {
                                            $stmt = $conn->prepare('UPDATE glocal_points SET points = points + ? WHERE student = ?');
                                            $stmt->bind_param('is', $points, $student);
                                            if (!$stmt->execute()) {
                                                throw new Exception("Failed to update student points");
                                            }
                                            $stmt->close();
                                        }

                                        // Record the scan
                                        $stmt = $conn->prepare('INSERT INTO qr_scans(event, student, points, scan_time, ip_address) VALUES(?, ?, ?, NOW(), ?)');
                                        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                                        $stmt->bind_param('isis', $event, $student, $points, $ip_address);
                                        
                                        if (!$stmt->execute()) {
                                            throw new Exception("Failed to record attendance scan");
                                        }
                                        $stmt->close();
                                        
                                        // Commit transaction
                                        $conn->commit();
                                        
                                        http_response_code(201);
                                        $response = [
                                            "success" => true,
                                            "message" => "Attendance recorded successfully",
                                            "points" => $points,
                                            "event_type" => $eventType,
                                            "event_id" => $event,
                                            "student_id" => $student
                                        ];
                                        
                                        error_log("Successful attendance scan - Event: $event, Student: $student, Points: $points");
                                    }
                                } catch (Exception $e) {
                                    $conn->rollback();
                                    error_log("Transaction failed: " . $e->getMessage());
                                    throw $e;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            http_response_code(400);
            $response = ["success" => false, "message" => "Missing required parameters"];
        }

    } else {
        http_response_code(401);
        $response = ["success" => false, "message" => "Unauthorized Access - Invalid API key"];
    }
} catch (Exception $e) {
    error_log("General error in QR API: " . $e->getMessage());
    http_response_code(500);
    $response = [
        "success" => false,
        "message" => "Internal server error occurred. Please try again."
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>