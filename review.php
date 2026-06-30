<?php
include("inc/init.php"); 
include("inc/db.php"); 

// Restrict to roles
$allowedRoles = ['admin','ceo','manager'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

$message = "";

// Handle new admin comment
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['comment'])) {
    $answer_id = intval($_POST['answer_id']);
    $admin_id  = intval($_SESSION['user_id']); // ✅ changed from staff_id
    $comment   = trim($_POST['comment']);

    if ($answer_id && $comment) {
        $stmt = $conn->prepare("INSERT INTO appraisal_comments (answer_id, admin_id, comment) VALUES (?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("iis", $answer_id, $admin_id, $comment);
            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>✅ Comment added successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>❌ Error saving comment: ".$stmt->error."</div>";
            }
            $stmt->close();
        } else {
            $message = "<div class='alert alert-danger'>❌ SQL Prepare Failed: ".$conn->error."</div>";
        }
    }
}

// ✅ Fixed query with correct joins
$sql = "SELECT 
            a.answer_id, 
            u.full_name, 
            q.title, 
            q.content, 
            a.answer_text, 
            a.created_at
        FROM appraisal_answers a
        JOIN users u ON a.user_id = u.id
        JOIN appraisal_questions q ON a.question_id = q.question_id
        ORDER BY a.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">Submitted Appraisals</h3>
        </div>

        <?php if (!empty($message)) echo $message; ?>

        <div class="row">
          <div class="col-md-12">
            <?php if ($result && $result->num_rows > 0) { 
              while ($row = $result->fetch_assoc()) { ?>
                <div class="card mb-3">
                  <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($row['full_name']) ?> 
                      <small class="text-muted float-right"><?= $row['created_at'] ?></small>
                    </h5>
                    <p><b><?= htmlspecialchars($row['title']) ?>:</b> <?= htmlspecialchars($row['content']) ?></p>
                    <p><b>Answer:</b> <?= nl2br(htmlspecialchars($row['answer_text'])) ?></p>

                    <!-- Comment form -->
                    <form method="post" class="mt-2">
                      <input type="hidden" name="answer_id" value="<?= $row['answer_id'] ?>">
                      <div class="form-group">
                        <textarea name="comment" class="form-control" rows="2" placeholder="Admin comment..." required></textarea>
                      </div>
                      <button type="submit" class="btn btn-sm btn-primary">Add Comment</button>
                    </form>
                  </div>
                </div>
              <?php } 
            } else { ?>
              <div class="alert alert-info">No appraisals submitted yet.</div>
            <?php } ?>
          </div>
        </div>
      </div>
      <?php include("inc/footer.php"); ?>
    </div>
  </div>
</div>
<?php include("inc/script.php"); ?>
</body>
</html>
