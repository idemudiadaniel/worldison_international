<?php
include("../inc/db.php");
header('Content-Type: application/json');
date_default_timezone_set('Africa/Lagos');

if(empty($_POST['staff_id']) || empty($_POST['clock_type']) || empty($_POST['photo_data']) || empty($_POST['lat']) || empty($_POST['long'])){
    echo json_encode(['status'=>'error','message'=>'All fields are required including photo']);
    exit;
}

$staff_id = $_POST['staff_id'];
$clock_type = $_POST['clock_type'];
$lat = $_POST['lat'];
$long = $_POST['long'];
$photo_data = $_POST['photo_data'];

// Check if staff exists
$stmt = $conn->prepare("SELECT full_name FROM users WHERE staff_id=?");
$stmt->bind_param("s",$staff_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows === 0){
    echo json_encode(['status'=>'error','message'=>'Invalid Staff ID']);
    exit;
}

// Check if user has already clocked in or out today
$stmt = $conn->prepare("SELECT clock_type FROM attendance WHERE staff_id=? AND DATE(resumption_time)=CURDATE()");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$existingRecords = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Logic to enforce 1-in, 1-out
$hasClockIn = false;
$hasClockOut = false;
foreach($existingRecords as $rec){
    if($rec['clock_type'] === 'in') $hasClockIn = true;
    if($rec['clock_type'] === 'out') $hasClockOut = true;
}

if($clock_type === 'in' && $hasClockIn){
    echo json_encode(['status'=>'error','message'=>'You have already clocked in today']);
    exit;
}
if($clock_type === 'out'){
    if(!$hasClockIn){
        echo json_encode(['status'=>'error','message'=>'You must clock in before clocking out']);
        exit;
    }
    if($hasClockOut){
        echo json_encode(['status'=>'error','message'=>'You have already clocked out today']);
        exit;
    }
}

// Decode Base64 photo
$photo = preg_replace('#^data:image/\w+;base64,#i', '', $photo_data);
$photo = str_replace(' ', '+', $photo);
$image_data = base64_decode($photo);

// Save photo in root/uploads
$upload_dir = __DIR__ . '/../uploads/';
if(!is_dir($upload_dir)){ mkdir($upload_dir, 0777, true); }

$filename = 'attendance_'.$staff_id.'_'.time().'.png';
$filepath = $upload_dir . $filename;
if(file_put_contents($filepath, $image_data) === false){
    echo json_encode(['status'=>'error','message'=>'Failed to save photo']);
    exit;
}

// Insert attendance
$stmt = $conn->prepare("INSERT INTO attendance (staff_id, clock_type, resumption_time, photo_path, location_lat, location_long, status) VALUES (?, ?, NOW(), ?, ?, ?, 'pending')");
$stmt->bind_param("sssdd", $staff_id, $clock_type, $filename, $lat, $long);

if($stmt->execute()){
    echo json_encode(['status'=>'success']);
}else{
    echo json_encode(['status'=>'error','message'=>'Failed to save attendance']);
}
?>
