<?php
include("inc/init.php");
include("inc/db.php");

$res = $conn->query("SELECT * FROM appraisal_comments");
while($r = $res->fetch_assoc()) {
  echo "Comment: {$r['comment']} | Answer ID: {$r['answer_id']} | Admin ID: {$r['admin_id']}<br>";
}

// Fetch all appraisal answers
$sql = "SELECT a.answer_id, a.answer_text, a.created_at,
               q.title, q.content,
               d.dept_name,
               u.full_name
        FROM appraisal_answers a
        JOIN appraisal_questions q ON a.question_id = q.question_id
        JOIN departments d ON q.dept_id = d.dept_id
        JOIN users u ON a.user_id = u.id
        ORDER BY a.created_at DESC";
$responses = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

    <div class="main-panel">
      <div class="content-wrapper">
        <div class="page-header">
          <h3 class="page-title">All Self-Appraisals</h3>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                  <tr>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Question</th>
                    <th>Answer & Comments</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($responses && $responses->num_rows > 0) { 
                    while ($r = $responses->fetch_assoc()) { ?>
                      <tr>
                        <td><?= htmlspecialchars($r['full_name']) ?></td>
                        <td><?= htmlspecialchars($r['dept_name']) ?></td>
                        <td>
                          <b><?= htmlspecialchars($r['title']) ?>:</b><br>
                          <?= htmlspecialchars($r['content']) ?>
                        </td>
                        <td>
                          <?= nl2br(htmlspecialchars($r['answer_text'])) ?>

                          <!-- Display Comments -->
                          <?php
                          $comments_sql = "SELECT c.comment AS comment_text, c.created_at, u.full_name 
                          FROM appraisal_comments c
                          JOIN users u ON c.admin_id = u.id
                          WHERE c.answer_id = {$r['answer_id']}
                          ORDER BY c.created_at ASC";
         
                          $comments_res = $conn->query($comments_sql);
                          if ($comments_res && $comments_res->num_rows > 0) {
                            while ($c = $comments_res->fetch_assoc()) {
                              echo "<div class='comment-box'>";
                              echo "<span class='comment-author'>" . htmlspecialchars($c['full_name']) . "</span>: ";
                              echo htmlspecialchars($c['comment_text']);
                              echo "<br><span class='comment-time'>" . htmlspecialchars($c['created_at']) . "</span>";
                              echo "</div>";
                            }
                          }
                          ?>
                        </td>
                        <td><?= htmlspecialchars($r['created_at']) ?></td>
                      </tr>
                  <?php } } else { ?>
                      <tr>
                        <td colspan="5" class="text-center">No appraisals found.</td>
                      </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
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
