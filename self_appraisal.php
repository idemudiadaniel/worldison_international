<?php
include("inc/init.php");
include("inc/db.php");

// Restrict only staff roles (not admin)
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answers'])) {
    $answers = $_POST['answers'];
    $success = true;

    foreach ($answers as $question_id => $answer) {
        $answer = trim($answer);
        if ($answer === "") continue; // skip empty answers

        // Check if already answered (prevent duplicates)
        $check = $conn->prepare("SELECT 1 FROM appraisal_answers WHERE user_id=? AND question_id=?");
        $check->bind_param("ii", $user_id, $question_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO appraisal_answers (user_id, question_id, answer_text) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iis", $user_id, $question_id, $answer);
                if (!$stmt->execute()) {
                    $success = false;
                    $message = "<div class='alert alert-danger'>❌ Error saving some answers: ".$stmt->error."</div>";
                }
                $stmt->close();
            } else {
                $success = false;
                $message = "<div class='alert alert-danger'>❌ SQL Prepare Failed: ".$conn->error."</div>";
            }
        } else {
            $message = "<div class='alert alert-warning'>⚠️ You already submitted answers for some questions.</div>";
        }

        $check->close();
    }

    if ($success && empty($message)) {
        $message = "<div class='alert alert-success'>✅ Appraisal submitted successfully!</div>";
    }
}

// Fetch all departments
$departments = $conn->query("SELECT * FROM departments ORDER BY dept_name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">Staff Appraisal Form</h3>
        </div>

        <?= $message ?>

        <!-- Department Selection -->
        <form method="get" class="mb-3">
          <div class="form-group">
            <label for="dept_id">Select Department</label>
            <select name="dept_id" id="dept_id" class="form-control" required onchange="this.form.submit()">
              <option value="">-- Choose Department --</option>
              <?php while ($d = $departments->fetch_assoc()) { ?>
                <option value="<?= $d['dept_id'] ?>" <?= isset($_GET['dept_id']) && $_GET['dept_id']==$d['dept_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($d['dept_name']) ?>
                </option>
              <?php } ?>
            </select>
          </div>
        </form>

        <!-- Show questions if department selected -->
        <?php if (isset($_GET['dept_id']) && $_GET['dept_id'] !== "") {
          $dept_id = intval($_GET['dept_id']);
          $questions = $conn->query("SELECT * FROM appraisal_questions WHERE dept_id=$dept_id ORDER BY question_id ASC");

          if ($questions && $questions->num_rows > 0) { ?>
            <form method="post">
              <?php while ($q = $questions->fetch_assoc()) { ?>
                <div class="card mb-3">
                  <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($q['title']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($q['content']) ?></p>
                    <div class="form-group">
                      <textarea name="answers[<?= $q['question_id'] ?>]" class="form-control" rows="3" required></textarea>
                    </div>
                  </div>
                </div>
              <?php } ?>
              <button type="submit" class="btn btn-primary">Submit Appraisal</button>
            </form>
          <?php } else { ?>
            <div class="alert alert-info">No questions found for this department.</div>
          <?php }
        } ?>
      </div>
      <?php include("inc/footer.php"); ?>
    </div>
  </div>
</div>
<?php include("inc/script.php"); ?>
</body>
</html>
