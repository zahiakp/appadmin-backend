<?php

include '../inc/head.php';
include '../inc/const.php';
include '../inc/db.php';



$response = array("success" => false, "message" => "Invalid Request");
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
if (isset($_GET['api']) && $_GET['api'] == API) {
    if ($method == "GET") {
        $searchQuery = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
        $page = isset($_GET['page']) ? (int)mysqli_real_escape_string($conn, $_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? (int)mysqli_real_escape_string($conn, $_GET['limit']) : 10;
        $offset = ($page - 1) * $limit;
        $category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : ''; // Category filter

        if (isset($_GET['id'])) {
            $id = mysqli_real_escape_string($conn, $_GET['id']);
            $sql = "SELECT * FROM news WHERE id = '$id'";
            $resp = mysqli_query($conn, $sql);
            if ($resp) {
                if (mysqli_num_rows($resp) > 0) {
                    $data = mysqli_fetch_assoc($resp);
                    http_response_code(200);
                    $response = array("success" => true, "data" => $data);
                } else {
                    http_response_code(404);
                    $response = array("success" => false, "message" => "News not found");
                }
            } else {
                http_response_code(500);
                $response = array("success" => false, "message" => "Error fetching News", "error" => mysqli_error($conn));
            }
        } elseif (isset($_GET['action']) && $_GET['action'] == 'catbasedarray') {
            $sql = "SELECT category, COUNT(*) as count FROM news GROUP BY category HAVING count > 3";
            $resp = mysqli_query($conn, $sql);

            if ($resp) {
                $categoryArray = array();

                while ($row = mysqli_fetch_assoc($resp)) {
                    $category = $row['category'];

                    // Fetch data for this category
                    $categorySql = "SELECT * FROM news WHERE category = '" . mysqli_real_escape_string($conn, $category) . "' ORDER BY id DESC";
                    $categoryResp = mysqli_query($conn, $categorySql);

                    if ($categoryResp) {
                        $categoryData = array();
                        while ($news = mysqli_fetch_assoc($categoryResp)) {
                            $categoryData[] = $news;
                        }
                        // Add to the category array
                        $categoryArray[] = array(
                            "name" => $category,
                            "data" => $categoryData
                        );
                    }
                }

                if (!empty($categoryArray)) {
                    http_response_code(200);
                    $response = array("success" => true, "data" => $categoryArray);
                } else {
                    http_response_code(404);
                    $response = array("success" => false, "message" => "No categories with more than 3 news items found");
                }
            } else {
                http_response_code(500);
                $response = array("success" => false, "message" => "Error fetching categories", "error" => mysqli_error($conn));
            }
        } else {
            // Base query
            $sql = "SELECT * FROM news WHERE 1=1";

            // Apply search query
            if (!empty($searchQuery)) {
                $sql .= " AND (title LIKE '%$searchQuery%')";
            }

            // Apply category filter
            if (!empty($category)) {
                $sql .= " AND category = '$category'";
            }

            // Add pagination
            $sql .= " ORDER BY id DESC LIMIT $offset, $limit";

            $resp = mysqli_query($conn, $sql);
            if ($resp) {
                $data = array();
                while ($row = mysqli_fetch_assoc($resp)) {
                    $data[] = $row;
                }

                // Fetch total count for pagination
                $countSql = "SELECT COUNT(*) as total FROM news WHERE 1=1";

                // Apply the same search and filter criteria to count total records
                if (!empty($searchQuery)) {
                    $countSql .= " AND (title LIKE '%$searchQuery%')";
                }

                // Apply category filter in count query
                if (!empty($category)) {
                    $countSql .= " AND category = '$category'";
                }

                $countResp = mysqli_query($conn, $countSql);
                $totalCount = mysqli_fetch_assoc($countResp)['total'];

                http_response_code(200);
                $response = array(
                    "success" => true,
                    "data" => $data,
                    "total" => $totalCount,
                    "page" => $page,
                    "limit" => $limit
                );
            } else {
                http_response_code(500);
                $response = array("success" => false, "message" => "Error fetching News", "error" => mysqli_error($conn));
            }
        }
    } elseif ($method == "POST") {
        $required_fields = ['title', 'image', 'type'];

        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
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
        $title = mysqli_real_escape_string($conn, $input['title']);
        $content = mysqli_real_escape_string($conn, $input['content']);
        $image = mysqli_real_escape_string($conn, $input['image']);
        $type = mysqli_real_escape_string($conn, $input['type']);
        $date = mysqli_real_escape_string($conn, $input['date']);
        $place = mysqli_real_escape_string($conn, $input['place']);


        $sql = "INSERT INTO events( title,content,image,type,date,place) VALUES('$title','$content','$image','$type','$date','$place')";
        $resp = mysqli_query($conn, $sql);
        if ($resp) {
            http_response_code(201);
            $response = array(
                "success" => true,
                "message" => "Events added succesfully",

            );
        } else {
            http_response_code(500);
            $response = array("success" => false, "message" => "Error adding News", "error" => mysqli_error($conn));
        }
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
