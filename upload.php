<?php

include './inc/head.php';
include './inc/const.php';
include './inc/db.php';
include './inc/upload.php';

$response = array();

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    if (isset($_GET['api']) && $_GET['api'] == API) {
        if (isset($_FILES['file']) && $_FILES['file']) {
            $upload = upload('./uploads/news/', $_FILES['file']);
            if ($upload['status']) {
                $filename = $upload['filename'];
                http_response_code(201);
                $response = array(
                    "success" => true,
                    "message" => "File uploaded successfully",
                    "filename" => $filename
                );
            } else {
                http_response_code(500);
                $response = array("success" => false, "error" => true, "message" => "Error uploading the file!");
            }
        } else {
            http_response_code(400);
            $response = array("success" => false, "message" => "File or Id not Provided");
        }
    } else {
        http_response_code(401);
        $response = array("success" => false, "message" => "Unauthorized Access");
    }
} else {
    http_response_code(400);
    $response = array("success" => false, "message" => "Invalid Method");
}


echo json_encode($response);
