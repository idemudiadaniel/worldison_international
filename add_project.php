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

// -------------------- ADD PROJECT --------------------
if (isset($_POST['save_project'])) {
    $title    = trim($_POST['title']);
    $category = trim($_POST['category']);
    $desc     = trim($_POST['description']);
    $status   = ($_POST['save_project'] === 'draft') ? 'draft' : 'published';

    $image_url = null;
    $video_url = null;

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/projects/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time()."_".basename($_FILES["image"]["name"]);
        $target_file = $target_dir.$filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $filename;
        }
    }

    if (!empty($_FILES['video']['name'])) {
        $target_dir = "uploads/projects/videos/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $filename = time()."_".basename($_FILES["video"]["name"]);
        $target_file = $target_dir.$filename;
        if (move_uploaded_file($_FILES["video"]["tmp_name"], $target_file)) {
            $video_url = $filename;
        }
    }

    $sql = "INSERT INTO projects (title, category, description, image_url, video_url, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("SQL Prepare failed: " . $conn->error);
    $stmt->bind_param("ssssss", $title, $category, $desc, $image_url, $video_url, $status);
    $stmt->execute() ? $message = "<div class='alert alert-success'>✅ Project saved as <b>$status</b>!</div>"
                      : $message = "<div class='alert alert-danger'>❌ Error: ".$stmt->error."</div>";
    $stmt->close();
}

// -------------------- DELETE PROJECT --------------------
if (isset($_POST['delete_project']) && !empty($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    $res = $conn->query("SELECT image_url, video_url FROM projects WHERE id=$id LIMIT 1");
    $old = $res ? $res->fetch_assoc() : null;

    $stmt = $conn->prepare("DELETE FROM projects WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if ($old) {
            if (!empty($old['image_url']) && file_exists("uploads/projects/".$old['image_url'])) unlink("uploads/projects/".$old['image_url']);
            if (!empty($old['video_url']) && file_exists("uploads/projects/videos/".$old['video_url'])) unlink("uploads/projects/videos/".$old['video_url']);
        }
        $message = "<div class='alert alert-success'>✅ Project deleted!</div>";
    } else {
        $message = "<div class='alert alert-danger'>❌ Error deleting project: ".$stmt->error."</div>";
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
      <h3 class="page-title">Projects Management</h3>
    </div>

    <?php if ($message) echo $message; ?>

    <div class="row">
      <!-- Add New Project -->
      <div class="col-md-6 mt-4">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Add New Project</h4>
            <form method="POST" action="" enctype="multipart/form-data" onsubmit="return syncContent()">
              <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
              </div>

              <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" class="form-control" value="General">
              </div>

              <div class="form-group">
                <label>Content / Description</label>
                <input type="hidden" name="description" id="description">
                <div id="editor"></div>
              </div>

              <div class="form-group">
                <label>Project Image</label>
                <input type="file" name="image" class="form-control">
              </div>

              <div class="form-group">
                <label>Project Video (optional)</label>
                <input type="file" name="video" class="form-control" accept="video/*">
              </div>

              <button type="submit" name="save_project" value="published" class="btn btn-success">Publish</button>
              <button type="submit" name="save_project" value="draft" class="btn btn-warning">Save Draft</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Latest Projects -->
      <div class="col-md-6 mt-4">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Latest Projects</h4>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Image</th>
                    <th>Video</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $projects = $conn->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5");
                  if ($projects && $projects->num_rows > 0) {
                      while($row = $projects->fetch_assoc()){
                          $id       = (int)$row['id'];
                          $title    = htmlspecialchars($row['title']);
                          $status   = htmlspecialchars($row['status'] ?? 'draft');
                          $category = htmlspecialchars($row['category'] ?? 'General');
                          $imageUrl = $row['image_url'] ? 'uploads/projects/'.htmlspecialchars($row['image_url']) : 'assets/images/default.png';
                          $videoUrl = $row['video_url'] ? 'uploads/projects/videos/'.htmlspecialchars($row['video_url']) : null;

                          echo "<tr>
                                  <td>{$id}</td>
                                  <td>{$title}</td>
                                  <td>{$status}</td>
                                  <td>{$category}</td>
                                  <td><img src='{$imageUrl}' height='50' class='img-fluid rounded'></td>
                                  <td>".($videoUrl ? "<video src='{$videoUrl}' height='50' controls></video>" : "—")."</td>
                                  <td>
                                    <a href='edit_project.php?id={$id}' class='btn btn-sm btn-primary'>Edit</a>
                                    <form method='POST' style='display:inline' onsubmit=\"return confirm('Delete this project?');\">
                                      <input type='hidden' name='delete_id' value='{$id}'>
                                      <button type='submit' name='delete_project' class='btn btn-sm btn-danger'>Delete</button>
                                    </form>
                                  </td>
                                </tr>";
                      }
                  } else {
                      echo "<tr><td colspan='7'>No projects found</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include("inc/footer.php"); ?>
  </div>
</div>

<!-- QuillJS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
  var quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
      toolbar: [
        [{ 'header': [1, 2, 3, false] }],
        ['bold','italic','underline','strike'],
        [{ 'list':'ordered'},{ 'list':'bullet' }],
        ['link','image','code-block'],
        ['clean']
      ]
    }
  });

  function syncContent() {
    document.getElementById("description").value = quill.root.innerHTML;
    return true;
  }
</script>
<?php include("inc/script.php"); ?>
</body>
</html>
