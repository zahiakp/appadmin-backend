<?php

include '../inc/head.php';
include '../inc/const.php';
include '../inc/db.php';

$response = ["success" => false, "message" => "Invalid Request"];
$method = $_SERVER['REQUEST_METHOD'];

function handleOption($value)
{
    $pointsMap = [
        "expert" => 14,
        "edu" => 14,
        "write" => 10,
        "pro" => 6,
        "tranquil" => 10,
    ];
    return $pointsMap[$value] ?? 0;
}

function getStudentPoints($conn, $student_id)
{
    $stmt = $conn->prepare('SELECT points FROM glocal_points WHERE student = ?');
    $stmt->bind_param('s', $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->num_rows > 0 ? $result->fetch_assoc()['points'] : null;
}

function isAlreadyScanned($conn, $event, $student)
{
    $stmt = $conn->prepare('SELECT 1 FROM qr_scans WHERE event = ? AND student = ?');
    $stmt->bind_param('ii', $event, $student);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->num_rows > 0;
}

function getEventType($conn, $id)
{
    $stmt = $conn->prepare('SELECT type FROM events WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->num_rows > 0 ? $result->fetch_assoc()['type'] : null;
}

// Main Logic
if (isset($_GET['api']) && $_GET['api'] == API) {
    if ($method == "GET" && isset($_GET['ID'])) {
        // Handle GET request
        $id = $_GET['ID'];
        $points = getStudentPoints($conn, $id);
        if ($points !== null) {
            http_response_code(200);
            $response = ["success" => true, "data" => $points];
        } else {
            http_response_code(404);
            $response = ["success" => false, "message" => "Student not found"];
        }
    } elseif ($method == "POST") {
        // Handle POST request
        $event = $_GET['event'] ?? null;
        $student = $_GET['student'] ?? null;

        if (!$event || !$student) {
            $response = ["success" => false, "message" => "Missing required fields"];
        } elseif (isAlreadyScanned($conn, $event, $student)) {
            $response = ["success" => false, "message" => "Already Redeemed"];
        } else {
            $eventType = getEventType($conn, $event);
            if (!$eventType) {
                $response = ["success" => false, "message" => "Invalid QR"];
            } else {
                $points = handleOption($eventType);
                $currentPoints = getStudentPoints($conn, $student);

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

                $stmt = $conn->prepare('INSERT INTO qr_scans(event, student, points) VALUES(?, ?, ?)');
                $stmt->bind_param('isi', $event, $student, $points);
                if ($stmt->execute()) {
                    http_response_code(201);
                    $response = ["success" => true, "message" => "QR scanned successfully", "points" => $points];
                } else {
                    $response = ["success" => false, "message" => "Failed to record QR scan"];
                }
                $stmt->close();
            }
        }
    }
} else {
    http_response_code(401);
    $response = ["success" => false, "message" => "Unauthorized Access"];
}

echo json_encode($response);
