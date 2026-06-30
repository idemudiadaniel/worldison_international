<?php
include("inc/init.php");
include("inc/db.php");


// Restrict to admin-like roles
$allowedRoles = ['admin','ceo','editor'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

$message = "";

// Fetch all departments
$departments = $conn->query("SELECT * FROM departments ORDER BY dept_name ASC");

// -------------------- SAVE QUESTIONS --------------------
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['save_questions'])) {
    $dept_id = intval($_POST['dept_id']);

    if (!empty($_POST['questions'])) {
        foreach ($_POST['questions'] as $q) {
            $title   = trim($q['title'] ?? '');
            $content = trim($q['content'] ?? '');
            if ($title && $content) {
                $stmt = $conn->prepare("INSERT INTO appraisal_questions (dept_id, title, content) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $dept_id, $title, $content);
                if (!$stmt->execute()) {
                    $message .= "<div class='alert alert-danger'>❌ Error: ".$stmt->error."</div>";
                }
                $stmt->close();
            }
        }
        if (empty($message)) {
            $message = "<div class='alert alert-success'>✅ Questions saved successfully!</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>⚠ Please add at least one question.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">Appraisal Question Management</h3>
        </div>

        <?= $message ?>

        <div class="row">
          <!-- Add New Appraisal Questions -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Add Appraisal Questions</h4>
                <form method="POST" action="">
                  <div class="form-group">
                    <label>Department</label>
                    <select name="dept_id" class="form-control" required>
                      <option value="">-- Select Department --</option>
                      <?php while($d = $departments->fetch_assoc()) { ?>
                        <option value="<?= $d['dept_id'] ?>"><?= htmlspecialchars($d['dept_name']) ?></option>
                      <?php } ?>
                    </select>
                  </div>

                  <div id="questions"></div>

                  <button type="button" onclick="addQuestion()" class="btn btn-info mt-2">+ Add Question</button>
                  <button type="submit" name="save_questions" class="btn btn-success mt-2">Save Questions</button>
                </form>
              </div>
            </div>
          </div>

          <!-- Latest Questions -->
          <div class="col-md-12 mt-4">
            <div class="card">
              <div class="card-body">
                <h4 class="card-title">Latest Questions</h4>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Department</th>
                        <th>Title</th>
                        <th>Content</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $qset = $conn->query("SELECT q.question_id, d.dept_name, q.title, q.content 
                                             FROM appraisal_questions q
                                             JOIN departments d ON q.dept_id=d.dept_id
                                             ORDER BY q.question_id DESC LIMIT 10");
                      if ($qset && $qset->num_rows > 0) {
                          while($row = $qset->fetch_assoc()){
                              echo "<tr>
                                  <td>{$row['question_id']}</td>
                                  <td>".htmlspecialchars($row['dept_name'])."</td>
                                  <td>".htmlspecialchars($row['title'])."</td>
                                  <td>".htmlspecialchars($row['content'])."</td>
                              </tr>";
                          }
                      } else {
                          echo "<tr><td colspan='4'>No questions found</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <?php include("inc/footer.php"); ?>
    </div>
  </div>
</div>
<?php include("inc/script.php"); ?>
<script>
let qIndex = 0;
function addQuestion() {
  let container = document.getElementById("questions");

  let block = document.createElement("div");
  block.classList.add("border", "p-3", "mb-2", "rounded");

  block.innerHTML = `
    <div class="form-group">
      <label>Title</label>
      <input type="text" name="questions[${qIndex}][title]" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Content</label>
      <textarea name="questions[${qIndex}][content]" class="form-control" rows="3" required></textarea>
    </div>
  `;

  container.appendChild(block);
  qIndex++;
}
</script>
</body>
</html>
