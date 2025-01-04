<?php

include '../inc/head.php';
include '../inc/const.php';
include '../inc/db.php';



$response = array("success" => false, "message" => "Invalid Request");
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
if (isset($_GET['api']) && $_GET['api'] == API) {
    if ($method == "GET") {

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sql = "SELECT * FROM events WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                http_response_code(200);
                $response = array("success" => true, "data" => $data);
            } else {
                http_response_code(404);
                $response = array("success" => false, "message" => "Event not found");
            }


        } else {
            $sql = "SELECT * FROM events";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                $data = [];
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
                http_response_code(200);
                $response = array("success" => true, "data" => $data);
            } else {
                http_response_code(404);
                $response = array("success" => false, "message" => "No events found");
            }
        }



    } elseif ($method == "POST") {
        $required_fields = ['title', 'place', 'time', 'type'];

        foreach ($required_fields as $field) {
            if (!isset($input[$field])) {
                $missing_fields[] = $field;
            }
        }

        // Return error if any required fields are missing
        if (!empty($missing_fields)) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "Missing required fields: " . implode(', ', $missing_fields),
            ]);
            exit();
        }
        $title = $input['title'];
        $type = $input['type'];
        $time = $input['time'];
        $place = $input['place'];


        $sql = "INSERT INTO events(title,time,type,place) VALUES(?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $title, $time, $type, $place);
        if ($stmt->execute()) {
            http_response_code(201);
            $response = array("success" => true, "message" => "Event added successfully");
        } else {
            http_response_code(500);
            $response = array("success" => false, "message" => "Error adding News", "error" => mysqli_error());
        }


    }
} else {
    http_response_code(401);
    $response = array("success" => false, "message" => "Unauthorized Access", "server" => $_SERVER);
}


echo json_encode($response);
