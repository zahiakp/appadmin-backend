<?php
include '../inc/head.php';
include '../inc/const.php';
include '../inc/db.php';

$response = ["success" => false, "message" => "Invalid Request"];
$method   = $_SERVER['REQUEST_METHOD'];

if (isset($_GET['api']) && $_GET['api'] == API) {

    /* ================= READ ================= */
    if ($method == "GET") {

        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $sql = "SELECT * FROM events WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            if ($result->num_rows > 0) {
                $response = ["success" => true, "data" => $result->fetch_assoc()];
            } else {
                http_response_code(404);
                $response = ["success" => false, "message" => "Event not found"];
            }

        } else {
            $sql = "SELECT * FROM events ORDER BY id DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            $data = [];
            while ($row = $result->fetch_assoc()) $data[] = $row;

            if (!empty($data)) {
                $response = ["success" => true, "data" => $data];
            } else {
                http_response_code(404);
                $response = ["success" => false, "message" => "No events found"];
            }
        }
    }

    /* ================= CREATE / UPDATE ================= */
    elseif ($method == "POST") {

        // 🔹 check if front-end wants to fake PUT
        if (isset($_POST['_method']) && strtoupper($_POST['_method']) === 'PUT') {

            // -------- UPDATE --------
            if (!isset($_GET['id'])) {
                http_response_code(400);
                $response = ["success" => false, "message" => "Missing event ID"];
            } else {
                $id = intval($_GET['id']);
                $fields = ["title", "place", "time", "type"];
                $updates = [];
                $params  = [];
                $types   = "";

                foreach ($fields as $f) {
                    if (isset($_POST[$f]) && trim($_POST[$f]) !== "") {
                        $updates[] = "$f = ?";
                        $params[]  = $_POST[$f];
                        $types    .= "s";
                    }
                }

                // optional image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
                    $mime    = mime_content_type($_FILES['image']['tmp_name']);
                    $size    = $_FILES['image']['size'];
                    if (in_array($mime,$allowed) && $size <= 5*1024*1024) {
                        $dir = "../uploads/events/";
                        if (!is_dir($dir)) mkdir($dir,0777,true);
                        $ext = pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
                        $new = uniqid("event_",true).".".strtolower($ext);
                        $path = $dir.$new;
                        if (move_uploaded_file($_FILES['image']['tmp_name'],$path)) {
                            $updates[] = "image = ?";
                            $params[]  = "uploads/events/".$new;
                            $types    .= "s";
                        }
                    }
                }

                if (empty($updates)) {
                    http_response_code(400);
                    $response = ["success"=>false,"message"=>"No fields to update"];
                } else {
                    $sql = "UPDATE events SET ".implode(", ",$updates)." WHERE id=?";
                    $stmt = $conn->prepare($sql);
                    $types .= "i";
                    $params[] = $id;
                    $stmt->bind_param($types,...$params);
                    if ($stmt->execute() && $stmt->affected_rows>0) {
                        $response = ["success"=>true,"message"=>"Event updated successfully"];
                    } else {
                        http_response_code(404);
                        $response = ["success"=>false,"message"=>"Event not found or no changes"];
                    }
                    $stmt->close();
                }
            }

        } else {
            // -------- CREATE --------
            $required = ['title','place','time','type'];
            $missing = [];
            foreach ($required as $f) {
                if (!isset($_POST[$f]) || trim($_POST[$f]) === "") $missing[]=$f;
            }
            if (!empty($missing)) {
                http_response_code(400);
                $response = ["success"=>false,"message"=>"Missing required fields: ".implode(", ",$missing)];
            } else {
                $title=$_POST['title']; $place=$_POST['place']; $time=$_POST['time']; $type=$_POST['type'];
                $imagePath=null;

                if (isset($_FILES['image']) && $_FILES['image']['error']===UPLOAD_ERR_OK) {
                    $allowed=['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
                    $mime=mime_content_type($_FILES['image']['tmp_name']);
                    $size=$_FILES['image']['size'];
                    if (!in_array($mime,$allowed)){ http_response_code(400); echo json_encode(["success"=>false,"message"=>"Invalid file type"]); exit;}
                    if ($size>5*1024*1024){ http_response_code(400); echo json_encode(["success"=>false,"message"=>"Image size must be <5MB"]); exit;}
                    $dir="../uploads/events/"; if(!is_dir($dir)) mkdir($dir,0777,true);
                    $ext=pathinfo($_FILES['image']['name'],PATHINFO_EXTENSION);
                    $new=uniqid("event_",true).".".strtolower($ext);
                    if(move_uploaded_file($_FILES['image']['tmp_name'],$dir.$new)) $imagePath="uploads/events/".$new;
                }

                $sql="INSERT INTO events (title,time,type,place,image) VALUES (?,?,?,?,?)";
                $stmt=$conn->prepare($sql);
                $stmt->bind_param("sssss",$title,$time,$type,$place,$imagePath);
                if($stmt->execute()){
                    http_response_code(201);
                    $response=["success"=>true,"message"=>"Event added successfully","id"=>$stmt->insert_id,"image"=>$imagePath];
                } else {
                    http_response_code(500);
                    $response=["success"=>false,"message"=>"Error adding event","error"=>$stmt->error];
                }
                $stmt->close();
            }
        }
    }

    /* ================= DELETE ================= */
    elseif ($method == "DELETE") {
        if (!isset($_GET['id'])) {
            http_response_code(400);
            $response = ["success" => false, "message" => "Missing event ID"];
        } else {
            $id = intval($_GET['id']);
            $stmt = $conn->prepare("DELETE FROM events WHERE id=?");
            $stmt->bind_param("i",$id);
            if ($stmt->execute() && $stmt->affected_rows>0) {
                $response = ["success"=>true,"message"=>"Event deleted successfully"];
            } else {
                http_response_code(404);
                $response = ["success"=>false,"message"=>"Event not found"];
            }
            $stmt->close();
        }
    }

} else {
    http_response_code(401);
    $response = ["success"=>false,"message"=>"Unauthorized Access"];
}

echo json_encode($response);
