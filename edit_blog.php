<?php
include("inc/db.php");
session_start();

// ✅ Only allow certain roles to view payroll list (admin, accountant, ceo, manager)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','ceo','editor'])) {
  header("Location: dashboard.php");
  exit;
}
// ✅ Get post ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: blog_posts.php");
    exit;
}
$id = (int)$_GET['id'];

// ✅ Fetch post
$stmt = $conn->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$post) {
    die("Post not found!");
}

// ✅ Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']);
    $category = trim($_POST['category']);
    $content  = $_POST['content']; // will come from hidden input
    $author   = trim($_POST['author']);
    $status   = $_POST['status'];

    // Handle image upload (optional update)
    $imageUrl = $post['image_url']; // keep old one if none uploaded
    if (!empty($_FILES['image']['name'])) {
        $targetDir  = "uploads/";
        $fileName   = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imageUrl = $targetFile;
        }
    }

    $stmt = $conn->prepare("UPDATE blogs 
        SET title=?, category=?, content=?, author=?, status=?, image_url=?, updated_at=NOW() 
        WHERE id=?");
    $stmt->bind_param("ssssssi", $title, $category, $content, $author, $status, $imageUrl, $id);

    if ($stmt->execute()) {
        header("Location: blog_posts.php?msg=updated");
        exit;
    } else {
        $error = "Failed to update post.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

    <div class="content-wrapper">
      <h2 class="mb-3">Edit Blog Post</h2>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php endif; ?>

      <form method="post" enctype="multipart/form-data" onsubmit="return syncContent()">
        <div class="form-group">
          <label>Title</label>
          <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>

        <div class="form-group">
          <label>Category</label>
          <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($post['category']) ?>">
        </div>

        <div class="form-group">
          <label>Author</label>
          <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($post['author']) ?>">
        </div>

        <div class="form-group">
          <label>Status</label>
          <select name="status" class="form-control">
            <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
            <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Published</option>
          </select>
        </div>

        <div class="form-group">
          <label>Content</label>
          <!-- Hidden input to store HTML -->
          <input type="hidden" name="content" id="content">
          <div id="editor"><?= $post['content'] ?></div>
        </div>

        <div class="form-group">
          <label>Current Image</label><br>
          <?php if (!empty($post['image_url'])): ?>
            <img src="<?= htmlspecialchars($post['image_url']) ?>" width="120" class="mb-2 rounded"><br>
          <?php else: ?>
            <span class="text-muted">No image</span><br>
          <?php endif; ?>
          <label>Upload New Image (optional)</label>
          <input type="file" name="image" class="form-control-file">
        </div>

        <button type="submit" class="btn btn-success">Update Post</button>
        <a href="blog_posts.php" class="btn btn-secondary">Cancel</a>
      </form>
    </div>

    <?php include("inc/footer.php"); ?>
  </div>
</div>

<!-- QuillJS Script -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
  var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ 'header': [1, 2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        ['link', 'image', 'code-block'],
        ['clean']
      ]
    }
  });

  // Load existing content into Quill
  quill.root.innerHTML = <?= json_encode($post['content']) ?>;

  // Sync content before submit
  function syncContent() {
    document.getElementById("content").value = quill.root.innerHTML;
    return true;
  }
</script>
<?php include("inc/script.php"); ?>
</body>
</html>
