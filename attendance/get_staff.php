<?php
include("../inc/db.php");
date_default_timezone_set('Africa/Lagos');

header("Content-Type: application/json");

if(empty($_GET['staff_id'])){
    echo json_encode(['status'=>'error','message'=>'No staff ID']);
    exit;
}

$staff_id = $_GET['staff_id'];
$stmt = $conn->prepare("SELECT full_name, job_title FROM users WHERE staff_id=?");
$stmt->bind_param("s",$staff_id);
$stmt->execute();
$result = $stmt->get_result();

if($row = $result->fetch_assoc()){
    echo json_encode(['status'=>'success','name'=>$row['full_name'],'job'=>$row['job_title']]);
}else{
    echo json_encode(['status'=>'error','message'=>'Staff not found']);
}
