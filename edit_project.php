<?php 
include("inc/init.php"); 
include("inc/db.php"); 

// ✅ Only allow certain roles to view/edit projects
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin','ceo','editor'])) {
    header("Location: dashboard.php");
    exit;
}

// -------------------- GET PROJECT --------------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid project ID.");
}
$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM projects WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$project = $result->fetch_assoc();
$stmt->close();

if (!$project) {
    die("Project not found.");
}

// -------------------- UPDATE PROJECT --------------------
if (isset($_POST['update_project'])) {
    $title    = trim($_POST['title']);
    $category = trim($_POST['category']);
    $desc     = trim($_POST['description']); // will come from Quill
    $status   = ($_POST['update_project'] === 'draft') ? 'draft' : 'published';

    $image_url = $project['image_url'];
    $video_url = $project['video_url'];

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/projects/images/";
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
        $allowed = ['mp4','webm','ogg'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            if (move_uploaded_file($_FILES["video"]["tmp_name"], $target_file)) {
                $video_url = $filename;
            }
        }
    }

    $sql = "UPDATE projects SET title=?, category=?, description=?, image_url=?, video_url=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $title, $category, $desc, $image_url, $video_url, $status, $id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>✅ Project updated successfully!</div>";
        // refresh project data
        $project['title'] = $title;
        $project['category'] = $category;
        $project['description'] = $desc;
        $project['status'] = $status;
        $project['image_url'] = $image_url;
        $project['video_url'] = $video_url;
    } else {
        $message = "<div class='alert alert-danger'>❌ Error: ".$stmt->error."</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("inc/head.php"); ?>

<div class="main-panel">
  <div class="content-wrapper">
    <h3>Edit Project</h3>
    <?php if (!empty($message)) echo $message; ?>

    <form method="POST" enctype="multipart/form-data" onsubmit="return syncContent()">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?=htmlspecialchars($project['title'])?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <!-- Hidden input to store HTML content -->
            <input type="hidden" name="description" id="description">
            <div id="editor"><?= $project['description'] ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="<?=htmlspecialchars($project['category'])?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Project Video</label>
            <input type="file" name="video" class="form-control">
            <?php if (!empty($project['video_url'])): ?>
                <p>Current: <a href="uploads/projects/videos/<?=htmlspecialchars($project['video_url'])?>" target="_blank">View Video</a></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Project Image</label><br>
            <?php if ($project['image_url']): ?>
                <img src="uploads/projects/images/<?=htmlspecialchars($project['image_url'])?>" alt="Current Image" height="80" class="mb-2 rounded"><br>
            <?php endif; ?>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" name="update_project" value="published" class="btn btn-success">Update & Publish</button>
        <button type="submit" name="update_project" value="draft" class="btn btn-warning">Update as Draft</button>
        <a href="add_project.php" class="btn btn-secondary">Back</a>
    </form>
  </div>
</div>

<?php include("inc/footer.php"); ?>

<!-- QuillJS -->
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

  // Sync Quill content to hidden input before submit
  function syncContent() {
    document.getElementById('description').value = quill.root.innerHTML;
    return true;
  }
</script>

<?php include("inc/script.php"); ?>
</body>
</html>
