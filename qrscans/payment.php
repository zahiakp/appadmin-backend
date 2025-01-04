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

function getMerchantPoints($conn, $id)
{
    $stmt = $conn->prepare('SELECT points FROM shops WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->num_rows > 0 ? $result->fetch_assoc()['points'] : null;
}

function isBalance($conn, $student, $points, )
{
    $stmt = $conn->prepare('SELECT points FROM glocal_points WHERE student = ?');
    $stmt->bind_param('s', $student);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        $result = $result->fetch_assoc();
        $balance = $result['points'];
        return $balance >= $points;
    }
    return false;

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
function getTransactions($conn, $id)
{
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE merchant = ? ORDER BY id DESC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}
// Main Logic
if (isset($_GET['api']) && $_GET['api'] == API) {
    if ($method == "GET" && isset($_GET['id'])) {
        // Handle GET request
        $id = $_GET['id'];
        $points = getMerchantPoints($conn, $id);
        $transactions = getTransactions($conn, $id);
        $data = [];
        $data['points'] = $points;
        if ($transactions) {
            $data['transactions'] = $transactions;
        }
        $response = ["success" => true, "data" => $data];

    } elseif ($method == "POST") {
        // Handle POST request
        $student = $_GET['student'] ?? null;
        $merchant = $_GET['pay'] ?? null;
        $points = $_GET['points'] ?? null;


        if (!$merchant || !$student || !$points) {
            $response = ["success" => false, "message" => "Missing required fields"];
        } elseif (!isBalance($conn, $student, $points)) {
            $response = ["success" => false, "message" => "No sufficient balance"];
        } else {



            $stmt = $conn->prepare('UPDATE glocal_points SET points = points - ? WHERE student = ?');
            $stmt->bind_param('is', $points, $student);
            $stmt->execute();
            $stmt->close();
            $merchantId = $merchant == "vr" ? 2 : 1;
            $stmt = $conn->prepare('UPDATE shops SET points = points + ? WHERE id = ?');
            $stmt->bind_param('ii', $points, $student);
            $stmt->execute();
            $stmt->close();


            $stmt = $conn->prepare('INSERT INTO transactions(student, merchant,points) VALUES(?, ?,?)');
            $stmt->bind_param('sii', $student, $merchantId, $points);
            if ($stmt->execute()) {
                http_response_code(201);
                $response = ["success" => true, "message" => "Payment  success", "points" => $points];
            } else {
                $response = ["success" => false, "message" => "Payment Failed"];
            }
            $stmt->close();

        }
    }
} else {
    http_response_code(401);
    $response = ["success" => false, "message" => "Unauthorized Access"];
}

echo json_encode($response);
