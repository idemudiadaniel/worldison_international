<?php
session_start();
include("inc/db.php");

// ✅ Only allow certain roles to view payroll list (admin, accountant, ceo, manager)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','ceo',])) {
  header("Location: dashboard.php");
  exit;
}

// ✅ Validate and sanitize user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request");
}
$user_id = intval($_GET['id']);

// ✅ Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("User not found.");
}
// ✅ Fetch branch name if assigned
$branch_name = 'Not assigned';
if (!empty($user['branch_id'])) {
    $stmtBranch = $conn->prepare("SELECT branch_name FROM branches WHERE branch_id = ?");
    $stmtBranch->bind_param("i", $user['branch_id']);
    $stmtBranch->execute();
    $branchRes = $stmtBranch->get_result();
    if ($branchRow = $branchRes->fetch_assoc()) {
        $branch_name = $branchRow['branch_name'];
    }
}

// ✅ Build absolute base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$baseUrl  = $protocol . "://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), "/\\") . "/";

// ✅ Load Dompdf
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;

$dompdf = new Dompdf([
    "enable_remote" => true
]);

// ✅ Profile / Document Picture as download link
$pictureLink = '<p>No picture available</p>';

if (!empty($user['document_picture'])) {
    $pictureLink = '<a href="'.$baseUrl.'uploads/'.htmlspecialchars($user['document_picture']).'" target="_blank">Download Document Picture</a>';
} elseif (!empty($user['profile_picture'])) {
    $pictureLink = '<a href="'.$baseUrl.'uploads/'.htmlspecialchars($user['profile_picture']).'" target="_blank">Download Profile Picture</a>';
}

// ✅ Start building HTML
$html = '
<html>
<head>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    h2 { text-align: center; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f4f4f4; width: 30%; }
    ul { padding-left: 20px; }
    li { margin-bottom: 6px; }
    a { color: #007bff; text-decoration: none; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <h2>Staff Profile Report</h2>
  <table>
    <tr><th>Staff ID</th><td>'.htmlspecialchars($user['staff_id']).'</td></tr>
    <tr><th>Full Name</th><td>'.htmlspecialchars($user['full_name']).'</td></tr>
    <tr><th>Picture</th><td>'.$pictureLink.'</td></tr>
    <tr><th>Email</th><td>'.htmlspecialchars($user['email']).'</td></tr>
    <tr><th>Phone</th><td>'.htmlspecialchars($user['phone']).'</td></tr>
    <tr><th>Address</th><td>'.htmlspecialchars($user['address']).'</td></tr>
    <tr><th>Gender</th><td>'.htmlspecialchars($user['gender']).'</td></tr>
    <tr><th>Date of Birth</th><td>'.htmlspecialchars($user['dob']).'</td></tr>
    <tr><th>Marital Status</th><td>'.htmlspecialchars($user['marital_status']).'</td></tr>
    <tr><th>Department</th><td>'.htmlspecialchars($user['department']).'</td></tr>
    <tr><th>Job Title</th><td>'.htmlspecialchars($user['job_title']).'</td></tr>
    <tr><th>Hire Date</th><td>'.htmlspecialchars($user['hire_date']).'</td></tr>
    <tr><th>Employment Type</th><td>'.htmlspecialchars($user['employment_type']).'</td></tr>
    <tr><th>Branch</th><td>'.htmlspecialchars($branch_name).'</td></tr>
    <tr><th>Work Location</th><td>'.htmlspecialchars($user['work_location']).'</td></tr>
    <tr><th>National ID</th><td>'.htmlspecialchars($user['national_id']).'</td></tr>
    <tr><th>Tax ID</th><td>'.htmlspecialchars($user['tax_id']).'</td></tr>
    <tr><th>Bank Account</th><td>'.htmlspecialchars($user['bank_account']).'</td></tr>
    <tr><th>Next of Kin</th><td>'.htmlspecialchars($user['next_of_kin']).' ('.htmlspecialchars($user['next_of_kin_contact']).')</td></tr>
    <tr><th>Guarantor Details</th><td>'.nl2br(htmlspecialchars($user['guarantor_details'])).'</td></tr>
    <tr><th>Previous Work Experience</th><td>'.nl2br(htmlspecialchars($user['previous_work_experience'])).'</td></tr>
    <tr><th>Role</th><td>'.htmlspecialchars($user['role']).'</td></tr>
    <tr><th>State of Origin</th><td>'.htmlspecialchars($user['state_of_origin']).'</td></tr>
    <tr><th>LGA of Origin</th><td>'.htmlspecialchars($user['lga_of_origin']).'</td></tr>
    <tr><th>Country of Origin</th><td>'.htmlspecialchars($user['country_of_origin']).'</td></tr>
  </table>

  <h3>Documents</h3>
  <ul>
';

// ✅ Add documents with clickable absolute links
if (!empty($user['academic_certificate'])) {
    $html .= '<li><a href="'.$baseUrl.'uploads/'.htmlspecialchars($user['academic_certificate']).'">Download Academic Certificate</a></li>';
}
if (!empty($user['other_certificate'])) {
    $html .= '<li><a href="'.$baseUrl.'uploads/'.htmlspecialchars($user['other_certificate']).'">Download Other Certificate</a></li>';
}
if (!empty($user['staff_documents'])) {
    $html .= '<li><a href="'.$baseUrl.'uploads/'.htmlspecialchars($user['staff_documents']).'">Download Staff Documents</a></li>';
}

$html .= '
  </ul>
</body>
</html>';

// ✅ Render PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// ✅ Clear any previous output buffer
if (ob_get_length()) ob_end_clean();

// ✅ Stream the file
$dompdf->stream("staff_profile_".$user['staff_id'].".pdf", ["Attachment" => true]);
exit;
