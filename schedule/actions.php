<?php

include '../inc/head.php';
include '../inc/const.php';
include '../inc/db.php';



$response = ["success" => false, "message" => "Invalid Request"];
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
if (isset($_GET['api']) && $_GET['api'] == API) {
    if ($method == "GET") {
        if (isset($_GET['stage'])) {
            $stmt = null;
            $data = [];
            $stage = intval($_GET['stage']); // Sanitize stage input

            if (isset($_GET['date'])) {
                $date = $_GET['date']; // Assume proper validation or sanitization is done elsewhere
                $sql = "SELECT S.*, B.name, B.category 
                        FROM schedule S 
                        JOIN programs B ON S.program = B.id 
                        WHERE S.stage = ? AND S.date = ? ORDER BY S.start";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("is", $stage, $date);
            } else {
                $sql = "SELECT S.*, B.name, B.category 
                        FROM schedule S 
                        JOIN branch B ON S.program = B.id 
                        WHERE S.stage = ?  ORDER BY S.start";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $stage);
            }

            if ($stmt && $stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $data[] = $row;
                    }
                    http_response_code(200);
                    $response = [
                        "success" => true,
                        "data" => $data,
                    ];
                } else {
                    http_response_code(404); // Not Found
                    $response = [
                        "success" => false,
                        "message" => "No records found.",
                    ];
                }
            } else {
                http_response_code(500); // Internal Server Error
                $response = [
                    "success" => false,
                    "message" => "Error executing query.",
                    "error" => $conn->error,
                ];
            }
        } else {
            http_response_code(400); // Bad Request
            $response = [
                "success" => false,
                "message" => "Missing required 'stage' parameter.",
            ];
        }

    } elseif ($method == "POST") {
        $required_fields = ['stage', 'data'];
        $missing_fields = [];
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
        $stage = $input['stage'];

        $data = json_decode($input['data'], true);
        foreach ($data as $schedule) {
            $program = $schedule['program'];
            $date = $schedule['date'];
            $start = $schedule['start'];
            $end = $schedule['end'];
            $sql = "UPDATE schedule SET  stage = ?, date = ?, start = ?, end = ? WHERE program = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssi", $stage, $date, $start, $end, $program);
            $stmt->execute();
        }

        http_response_code(201);
        $response = array(
            "success" => true,
            "message" => "Scedule added succesfully",

        );



    } else if ($method == "PUT") {
        if (!isset($_GET['id']) || !isset($input['title']) || !isset($input['body']) || !isset($input['image']) || !isset($input['url']) || !isset($input['tags']) || !isset($input['status']) || !isset($input['type'])) {
            http_response_code(400);
            $response = array("success" => false, "message" => "Missing required fields");
            echo json_encode($response);
            exit();
        }
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $result = mysqli_query($conn, "SELECT * FROM news WHERE id = '$id'");
        $data = mysqli_fetch_assoc($result);
        if ($data) {
            $title = mysqli_real_escape_string($conn, $input['title']);
            $body = mysqli_real_escape_string($conn, $input['body']);
            $image = mysqli_real_escape_string($conn, $input['image']);
            $type = mysqli_real_escape_string($conn, $input['type']);
            $url = mysqli_real_escape_string($conn, $input['url']);
            $tags = mysqli_real_escape_string($conn, $input['tags']);
            $status = mysqli_real_escape_string($conn, $input['status']);
            $sql = "UPDATE news SET title = '$title', body = '$body', image = '$image',category = '$type',url = '$url',tags='$tags',status='$status' WHERE id = '$id'";

            $resp = mysqli_query($conn, $sql);
            if ($resp) {
                http_response_code(200);
                $response = array("success" => true, "message" => "News updated successfully");
            } else {
                http_response_code(500);
                $response = array("success" => false, "message" => "Error updating News", "error" => mysqli_error($conn));
            }
        } else {
            http_response_code(404);
            $response = array("success" => false, "message" => "News not found");
        }
    } else if ($method == "DELETE") {
        if (!isset($_GET['id'])) {
            http_response_code(400);
            $response = array("success" => false, "message" => "Id missing");
            echo json_encode($response);
            exit();
        }
        $id = mysqli_real_escape_string($conn, $_GET['id']);
        $result = mysqli_query($conn, "SELECT * FROM news WHERE id = '$id'");
        $data = mysqli_fetch_assoc($result);
        if ($data) {
            $sql = "DELETE FROM news WHERE id = '$id'";
            $resp = mysqli_query($conn, $sql);
            if ($resp) {
                http_response_code(200);
                $response = array("success" => true, "message" => "News deleted successfully");
            } else {
                http_response_code(500);
                $response = array("success" => false, "message" => "Error deleting News", "error" => mysqli_error($conn));
            }
        } else {
            http_response_code(404);
            $response = array("success" => false, "message" => "News not found");
        }
    } else {
        http_response_code(405);
        $response = array("success" => false, "message" => "Invalid Method");
    }
} else {
    http_response_code(401);
    $response = array("success" => false, "message" => "Unauthorized Access", "server" => $_SERVER);
}


echo json_encode($response);
