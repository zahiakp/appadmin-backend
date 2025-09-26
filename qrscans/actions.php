

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
        $stmt = $conn->prepare('SELECT 1 FROM qr_scans WHERE event = ? AND student = ?');
        $stmt->bind_param('is', $event, $student);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        return $result->num_rows > 0;
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

        // Process QR scan - simplified version
        elseif ($method == "GET" && isset($_GET['event']) && isset($_GET['student'])) {
            $event = filter_var($_GET['event'], FILTER_VALIDATE_INT);
            $student = trim($_GET['student']);

            // Validate inputs
            if ($event === false) {
                $response = ["success" => false, "message" => "Invalid event ID"];
            } elseif (empty($student)) {
                $response = ["success" => false, "message" => "Student ID is required"];
            } elseif (isAlreadyScanned($conn, $event, $student)) {
                $response = ["success" => false, "message" => "Already Redeemed"];
            } else {
                // Validate event exists
                if (!validateEvent($conn, $event)) {
                    $response = ["success" => false, "message" => "Invalid Event"];
                } else {
                    $eventType = getEventType($conn, $event);
                    if (!$eventType) {
                        $response = ["success" => false, "message" => "Invalid Event Type"];
                    } else {
                        $points = handleOption($eventType);
                        $currentPoints = getStudentPoints($conn, $student);

                        // Update student points
                        if ($currentPoints === null) {
                            $stmt = $conn->prepare('INSERT INTO glocal_points(student, points) VALUES(?, ?)');
                            $stmt->bind_param('si', $student, $points);
                            $stmt->execute();
                            $stmt->close();
                        } else {
                            $stmt = $conn->prepare('UPDATE glocal_points SET points = points + ? WHERE student = ?');
                            $stmt->bind_param('is', $points, $student);
                            $stmt->execute();
                            $stmt->close();
                        }

                        // Record scan
                        $stmt = $conn->prepare('INSERT INTO qr_scans(event, student, points, scan_time) VALUES(?, ?, ?, NOW())');
                        $stmt->bind_param('isi', $event, $student, $points);
                        
                        if ($stmt->execute()) {
                            $stmt->close();
                            
                            http_response_code(201);
                            $response = [
                                "success" => true,
                                "message" => "QR scanned successfully",
                                "points" => $points,
                                "event_type" => $eventType
                            ];
                        } else {
                            $stmt->close();
                            $response = ["success" => false, "message" => "Failed to record QR scan"];
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
        $response = ["success" => false, "message" => "Unauthorized Access"];
    }
} catch (Exception $e) {
    error_log("General error in QR API: " . $e->getMessage());
    http_response_code(500);
    $response = [
        "success" => false,
        "message" => "Internal server error occurred"
    ];
}

echo json_encode($response);
?>