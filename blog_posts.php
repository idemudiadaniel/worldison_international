<?php
include("inc/db.php");
session_start();

// Restrict to specific roles
$allowedRoles = ['admin','ceo','editor',];

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

// ✅ Handle delete
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: blog_posts.php?msg=deleted");
    exit;
}

// ✅ Fetch all posts (published + drafts)
$result = $conn->query("SELECT * FROM blogs ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

    <div class="content-wrapper">
      <h2 class="mb-3">Manage Blog Posts</h2>

      <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-success">Post deleted successfully!</div>
      <?php endif; ?>

      <a href="blog_admin.php" class="btn btn-primary mb-3">+ Add New Post</a>

      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="thead-dark">
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Title</th>
              <th>Category</th>
              <th>Author</th>
              <th>Status</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= (int)$row['id'] ?></td>
                <td>
                  <?php if (!empty($row['image_url'])): ?>
                    <img src="<?= htmlspecialchars($row['image_url']) ?>" width="80" class="img-fluid rounded">
                  <?php else: ?>
                    <img src="assets/images/default.png" width="80" class="img-fluid rounded">
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= htmlspecialchars($row['author']) ?></td>
                <td>
                  <?php if (!empty($row['status']) && $row['status'] === 'published'): ?>
                    <span class="badge badge-success">Published</span>
                  <?php else: ?>
                    <span class="badge badge-warning">Draft</span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['created_at']) ?></td>
                <td>
                  <a href="edit_blog.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                  <a href="blog_posts.php?delete=<?= $row['id'] ?>" 
                     onclick="return confirm('Are you sure you want to delete this post?')" 
                     class="btn btn-sm btn-danger">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php include("inc/footer.php"); ?>
  </div>
</div>

<?php include("inc/script.php"); ?>
</body>
</html>
