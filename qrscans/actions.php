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
        // Fixed: Ensure proper parameter types - event as integer, student as string
        $stmt = $conn->prepare('SELECT 1 FROM qr_scans WHERE event = ? AND student = ? LIMIT 1');
        $stmt->bind_param('is', $event, $student);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasRow = $result->num_rows > 0;
        $stmt->close();
        
        // Add logging for debugging
        error_log("Checking duplicate scan - Event: $event, Student: $student, Already scanned: " . ($hasRow ? 'YES' : 'NO'));
        
        return $hasRow;
    } catch (Exception $e) {
        error_log("Error in isAlreadyScanned: " . $e->getMessage());
        return true; // Err on the side of caution
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

        // Process QR scan - Fixed duplicate prevention
        elseif ($method == "GET" && isset($_GET['event']) && isset($_GET['student'])) {
            $event = filter_var($_GET['event'], FILTER_VALIDATE_INT);
            $student = trim($_GET['student']);

            // Enhanced input validation
            if ($event === false || $event <= 0) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Invalid event ID"];
            } elseif (empty($student)) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Student ID is required"];
            } else {
                // Log the scan attempt
                error_log("Scan attempt - Event: $event, Student: $student");
                
                // Check if already scanned FIRST
                if (isAlreadyScanned($conn, $event, $student)) {
                    http_response_code(409); // Conflict status code
                    $response = ["success" => false, "message" => "You have already scanned this event QR code"];
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
                                // Begin transaction to ensure data consistency
                                $conn->begin_transaction();
                                
                                try {
                                    // Double-check for race conditions within transaction
                                    if (isAlreadyScanned($conn, $event, $student)) {
                                        $conn->rollback();
                                        http_response_code(409);
                                        $response = ["success" => false, "message" => "You have already scanned this event QR code"];
                                    } else {
                                        $currentPoints = getStudentPoints($conn, $student);

                                        // Update student points
                                        if ($currentPoints === null) {
                                            // Create new student record
                                            $stmt = $conn->prepare('INSERT INTO glocal_points(student, points) VALUES(?, ?)');
                                            $stmt->bind_param('si', $student, $points);
                                            if (!$stmt->execute()) {
                                                throw new Exception("Failed to create student points record");
                                            }
                                            $stmt->close();
                                        } else {
                                            // Update existing student record
                                            $stmt = $conn->prepare('UPDATE glocal_points SET points = points + ? WHERE student = ?');
                                            $stmt->bind_param('is', $points, $student);
                                            if (!$stmt->execute()) {
                                                throw new Exception("Failed to update student points");
                                            }
                                            $stmt->close();
                                        }

                                        // Record the scan with timestamp
                                        $stmt = $conn->prepare('INSERT INTO qr_scans(event, student, points, scan_time) VALUES(?, ?, ?, NOW())');
                                        $stmt->bind_param('isi', $event, $student, $points);
                                        
                                        if (!$stmt->execute()) {
                                            throw new Exception("Failed to record QR scan");
                                        }
                                        $stmt->close();
                                        
                                        // Commit transaction
                                        $conn->commit();
                                        
                                        http_response_code(201);
                                        $response = [
                                            "success" => true,
                                            "message" => "QR scanned successfully",
                                            "points" => $points,
                                            "event_type" => $eventType,
                                            "event_id" => $event,
                                            "student_id" => $student
                                        ];
                                        
                                        error_log("Successful scan - Event: $event, Student: $student, Points: $points");
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
            $response = ["success" => false, "message" => "Missing required parameters (event and student required)"];
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

// Ensure JSON content type is set
header('Content-Type: application/json');
echo json_encode($response);
?>