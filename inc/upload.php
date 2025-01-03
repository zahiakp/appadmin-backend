<?php

function upload($folder, $file)
{
    $file_name = $file["name"];
    $file_tmp_name = $file["tmp_name"];
    $error = $file["error"];
    if ($error > 0) {
        return array("status"=>false);
    } else {
        $upload_name = uniqid();
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $upload_name = $upload_name . "." . $file_ext;

        if (move_uploaded_file($file_tmp_name, $folder . $upload_name)) {
            return array("status"=>true,"filename"=>$upload_name);
        } else {
            return array("status"=>false);
        }
    }
}
