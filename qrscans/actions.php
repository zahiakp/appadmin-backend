<?php

include '../inc/head.php';
include '../inc/const.php';
include '../inc/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

$response = ["success" => false, "message" => "Invalid Request"];
$method = $_SERVER['REQUEST_METHOD'];

function handleOption($value)
{
    $pointsMap = [
        "Expert Convos" => 4,
        "Edu Login" => 4,
        "WriteWell Clinic" => 3,
        "Pro Chat" => 2,
        "Mind Wellness Cliinic" => 3,
        "Science Orbit" => 4,
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
        $data = $result->num_rows > 0 ? $result->fetch_assoc()['points'] : null;
        $stmt->close();
        return $data;
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
        $data = $result->num_rows > 0 ? $result->fetch_assoc()['type'] : null;
        $stmt->close();
        return $data;
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
        $isValid = $result->num_rows > 0;
        $stmt->close();
        return $isValid;
    } catch (Exception $e) {
        error_log("Error in validateEvent: " . $e->getMessage());
        return false;
    }
}

function checkScanFrequency($conn, $student_id, $event_id, $min_interval = 5)
{
    try {
        // Check last scan for ANY event (prevent rapid scanning across different events)
        $stmt = $conn->prepare('SELECT MAX(scan_time) as last_scan FROM qr_scans WHERE student = ?');
        $stmt->bind_param('s', $student_id);
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
                    error_log("Scan frequency violation - Student: $student_id, Time since last scan: $time_diff seconds (minimum: $min_interval)");
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
            $student = strtoupper(trim($_GET['student'])); // Jamia ID like 2019JMC062 or JMF221

            error_log("QR Scan Request - Event: $event, Student: $student");

            // Updated validation: supports both formats
            // Format 1: 4 digits + 2-3 letters + 3-4 digits (e.g., 2019JMC062)
            // Format 2: 3 letters + 3 digits (e.g., JMF221)
            if (!preg_match('/^(\d{4}[A-Z]{2,3}\d{3,4}|[A-Z]{2,3}\d{3})$/', $student)) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Invalid Jamia ID format. Expected formats: 2019ABC123 or JMF221"];
            } elseif ($event === false || $event <= 0) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Invalid event ID"];
            }
            // Validate event exists FIRST
            elseif (!validateEvent($conn, $event)) {
                http_response_code(404);
                $response = ["success" => false, "message" => "Event not found"];
            }
            // Check if already scanned
            elseif (isAlreadyScanned($conn, $event, $student)) {
                http_response_code(409);
                $response = ["success" => false, "message" => "Student has already been scanned for this event"];
            }
            // Check scan frequency (5 second cooldown)
            elseif (!checkScanFrequency($conn, $student, $event, 5)) {
                http_response_code(429);
                $response = ["success" => false, "message" => "Please wait a moment before scanning again"];
            } else {
                // Get event type
                $eventType = getEventType($conn, $event);
                if (!$eventType) {
                    http_response_code(404);
                    $response = ["success" => false, "message" => "Event type not found"];
                } else {
                    $points = handleOption($eventType);
                    if ($points <= 0) {
                        http_response_code(400);
                        $response = ["success" => false, "message" => "Invalid event type for points calculation: " . $eventType];
                    } else {
                        // Begin transaction
                        $conn->begin_transaction();

                        try {
                            if (isAlreadyScanned($conn, $event, $student)) {
                                $conn->rollback();
                                http_response_code(409);
                                $response = ["success" => false, "message" => "Student has already been scanned for this event"];
                            } else {
                                $currentPoints = getStudentPoints($conn, $student);

                                // Update student points
                                if ($currentPoints === null) {
                                    // Create new student record
                                    $stmt = $conn->prepare('INSERT INTO glocal_points(student, points) VALUES(?, ?)');
                                    $stmt->bind_param('si', $student, $points);
                                    if (!$stmt->execute()) {
                                        throw new Exception("Failed to create student points record: " . $stmt->error);
                                    }
                                    $stmt->close();
                                } else {
                                    // Update existing points
                                    $stmt = $conn->prepare('UPDATE glocal_points SET points = points + ? WHERE student = ?');
                                    $stmt->bind_param('is', $points, $student);
                                    if (!$stmt->execute()) {
                                        throw new Exception("Failed to update student points: " . $stmt->error);
                                    }
                                    $stmt->close();
                                }

                                // Record the scan
                                $stmt = $conn->prepare('INSERT INTO qr_scans(event, student, points, scan_time, ip_address) VALUES(?, ?, ?, NOW(), ?)');
                                $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
                                $stmt->bind_param('isis', $event, $student, $points, $ip_address);

                                if (!$stmt->execute()) {
                                    throw new Exception("Failed to record attendance scan: " . $stmt->error);
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