<?php 
include("inc/init.php"); 
include("inc/db.php"); 

// Restrict to specific roles
$allowedRoles = ['admin','ceo','editor'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header("Location: " . BASE_URL . "dashboard.php");
    exit;
}

$message = "";

// -------------------- ADD POST --------------------
if (isset($_POST['save_post'])) {
    $title    = trim($_POST['title']);
    $category = trim($_POST['category']);
    $content  = trim($_POST['content']);
    $status   = ($_POST['save_post'] === 'draft') ? 'draft' : 'published';

    $image_url = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time()."_".basename($_FILES["image"]["name"]);
        $target_file = $target_dir.$filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $sql = "INSERT INTO blogs (title, category, content, image_url, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Prepare failed: " . $conn->error . " | Query: " . $sql);
    }
    $stmt->bind_param("sssss", $title, $category, $content, $image_url, $status);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>✅ Post saved as <b>{$status}</b>!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: ".$stmt->error."</div>";
    }

    $stmt->close();
}

// -------------------- DELETE POST --------------------
if (isset($_POST['delete_post'])) {
    $id = (int)$_POST['delete_id'];
    $stmt = $conn->prepare("DELETE FROM blogs WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>✅ Post deleted!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: ".$conn->error."</div>";
    }
    $stmt->close();
}

// -------------------- DELETE COMMENT --------------------
if (isset($_POST['delete_comment'])) {
    $id = (int)$_POST['delete_comment_id'];
    $stmt = $conn->prepare("DELETE FROM comments WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>✅ Comment deleted!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: ".$conn->error."</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="page-header">
      <h3 class="page-title">Blog Posts & Comments Management</h3>
    </div>

    <?php if (!empty($message)) echo $message; ?>

    <div class="row">
      <!-- Add New Post -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Add New Post</h4>
            <form method="POST" action="" enctype="multipart/form-data" novalidate onsubmit="return syncContent()">
              <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" class="form-control" value="Uncategorized">
              </div>

              <div class="form-group">
                <label>Content</label>
                <input type="hidden" name="content" id="content">
                <div id="editor"></div>
              </div>

              <div class="form-group">
                <label>Cover Image</label>
                <input type="file" name="image" class="form-control">
              </div>

              <button type="submit" name="save_post" value="published" class="btn btn-success">Publish</button>
              <button type="submit" name="save_post" value="draft" class="btn btn-warning">Save Draft</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Post & Comment List -->
      <div class="col-md-12 mt-4">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Latest Posts</h4>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $posts = $conn->query("SELECT * FROM blogs ORDER BY created_at DESC LIMIT 5");
                  if ($posts && $posts->num_rows > 0) {
                      while($row = $posts->fetch_assoc()){
                          $id       = (int)$row['id'];
                          $title    = htmlspecialchars($row['title'] ?? 'Untitled');
                          $status   = !empty($row['status']) ? $row['status'] : 'draft';
                          $category = htmlspecialchars($row['category'] ?? 'Uncategorized');
                          $imageUrl = !empty($row['image_url']) ? htmlspecialchars($row['image_url']) : 'assets/images/default.png';

                          echo "<tr>
                              <td>{$id}</td>
                              <td>{$title}</td>
                              <td>{$status}</td>
                              <td>{$category}</td>
                              <td><img src='{$imageUrl}' height='50' class='img-fluid rounded'></td>
                              <td>
                                <form method='POST' style='display:inline'>
                                  <input type='hidden' name='delete_id' value='{$id}'>
                                  <button type='submit' name='delete_post' class='btn btn-sm btn-danger'>Delete</button>
                                </form>
                              </td>
                          </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='6'>No posts found</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>

            <h4 class="card-title mt-4">Latest Comments</h4>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Post ID</th>
                    <th>Name</th>
                    <th>Comment</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $comments = $conn->query("SELECT * FROM comments ORDER BY created_at DESC LIMIT 5");
                if ($comments && $comments->num_rows > 0) {
                    while($c = $comments->fetch_assoc()){
                        $cid     = (int)$c['id'];
                        $blogId  = (int)$c['blog_id'];
                        $name    = htmlspecialchars($c['name'] ?? 'Anonymous');
                        $comment = htmlspecialchars($c['comment'] ?? '(No comment)');

                        echo "<tr>
                            <td>{$cid}</td>
                            <td>{$blogId}</td>
                            <td>{$name}</td>
                            <td>{$comment}</td>
                            <td>
                              <form method='POST' style='display:inline'>
                                <input type='hidden' name='delete_comment_id' value='{$cid}'>
                                <button type='submit' name='delete_comment' class='btn btn-sm btn-danger'>Delete</button>
                              </form>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No comments found</td></tr>";
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

<!-- QuillJS Script -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
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

  // Sync content to hidden input on form submit
  function syncContent() {
    document.getElementById("content").value = quill.root.innerHTML;
    return true;
  }
</script>

<?php include("inc/script.php"); ?>
</body>
</html>
