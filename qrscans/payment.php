<?php

include '../inc/head.php';
include '../inc/const.php';
include '../inc/db.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ["success" => false, "message" => "Invalid Request"];
$method = $_SERVER['REQUEST_METHOD'];

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit();
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

function isBalance($conn, $student, $points)
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

function getMerchantId($merchant)
{
    // Normalize merchant name (lowercase, trim)
    $merchant = strtolower(trim($merchant));
    
    // Map merchant names to IDs based on shops table
    $merchantMap = [
        "cafe" => 1,     // MG Cafe
        "vr" => 2,       // VR Hub
        "papyrus" => 5,  // papyrus
        "tajammul" => 6  // Alternative spelling
    ];
    
    error_log("getMerchantId - Input: '$merchant', Mapped ID: " . ($merchantMap[$merchant] ?? 'null'));
    
    return $merchantMap[$merchant] ?? null;
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
    return [];
}

// Main Logic
if (isset($_GET['api']) && $_GET['api'] == API) {
    if ($method == "GET" && isset($_GET['id'])) {
        // Handle GET request - get merchant details
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
        // Handle POST request - process payment
        $student = $_GET['student'] ?? null;
        $merchant = $_GET['pay'] ?? null;
        $points = $_GET['points'] ?? null;

        // Log request for debugging
        error_log("Payment Request - Student: $student, Merchant: $merchant, Points: $points");

        // Validate inputs
        if (!$merchant || !$student || !$points) {
            http_response_code(400);
            $response = ["success" => false, "message" => "Missing required fields"];
        } elseif (!is_numeric($points) || $points <= 0) {
            http_response_code(400);
            $response = ["success" => false, "message" => "Invalid points amount"];
        } else {
            $merchantId = getMerchantId($merchant);
            
            if ($merchantId === null) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Invalid merchant: $merchant"];
            } elseif (!isBalance($conn, $student, $points)) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Insufficient balance"];
            } else {
                // Start transaction
                $conn->begin_transaction();
                
                try {
                    // Deduct points from student
                    $stmt = $conn->prepare('UPDATE glocal_points SET points = points - ? WHERE student = ?');
                    $stmt->bind_param('is', $points, $student);
                    $stmt->execute();
                    $affectedRows1 = $stmt->affected_rows;
                    $stmt->close();
                    
                    if ($affectedRows1 === 0) {
                        throw new Exception("Failed to update student points");
                    }
                    
                    // Add points to merchant
                    $stmt = $conn->prepare('UPDATE shops SET points = points + ? WHERE id = ?');
                    $stmt->bind_param('ii', $points, $merchantId);
                    $stmt->execute();
                    $affectedRows2 = $stmt->affected_rows;
                    $stmt->close();
                    
                    if ($affectedRows2 === 0) {
                        throw new Exception("Failed to update merchant points");
                    }
                    
                    // Record transaction
                    $stmt = $conn->prepare('INSERT INTO transactions(student, merchant, points) VALUES(?, ?, ?)');
                    $stmt->bind_param('sii', $student, $merchantId, $points);
                    $stmt->execute();
                    $stmt->close();
                    
                    // Commit transaction
                    $conn->commit();
                    
                    http_response_code(201);
                    $response = ["success" => true, "message" => "Payment successful", "points" => $points];
                    
                    error_log("Payment Success - Student: $student, Merchant: $merchantId, Points: $points");
                } catch (Exception $e) {
                    // Rollback on error
                    $conn->rollback();
                    http_response_code(500);
                    $response = ["success" => false, "message" => "Payment failed: " . $e->getMessage()];
                    
                    error_log("Payment Error: " . $e->getMessage());
                }
            }
        }
    } else {
        http_response_code(400);
        $response = ["success" => false, "message" => "Invalid request method or missing parameters"];
    }
} else {
    http_response_code(401);
    $response = ["success" => false, "message" => "Unauthorized Access"];
}

echo json_encode($response);
$conn->close();
?>