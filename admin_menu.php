<?php
session_start();
include 'connect.php';

if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header("Location: index.php");
    exit();
}

function handleImageUpload($fileInput) {
    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] !== UPLOAD_ERR_OK) return null;

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $mime = mime_content_type($_FILES[$fileInput]['tmp_name']);
    if (!in_array($mime, $allowedTypes)) return null;

    $ext = pathinfo($_FILES[$fileInput]['name'], PATHINFO_EXTENSION);
    $newName = uniqid("food_", true) . "." . $ext;
    $uploadPath = "uploads/" . $newName;

    if (move_uploaded_file($_FILES[$fileInput]['tmp_name'], $uploadPath)) {
        return $uploadPath;
    }

    return null;
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM menu WHERE food_id = $id");
    header("Location: admin_menu.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $name = $conn->real_escape_string($_POST['foodName']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $image = handleImageUpload('image_file') ?? '';

    $stmt = $conn->prepare("INSERT INTO menu (foodName, price, food_category, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $price, $category, $image);
    $stmt->execute();
    header("Location: admin_menu.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['food_id']);
    $name = $conn->real_escape_string($_POST['foodName']);
    $price = floatval($_POST['price']);
    $category = $conn->real_escape_string($_POST['category']);
    $image = handleImageUpload('image_file');

    if ($image) {
        $stmt = $conn->prepare("UPDATE menu SET foodName=?, price=?, food_category=?, image_url=? WHERE food_id=?");
        $stmt->bind_param("sdssi", $name, $price, $category, $image, $id);
    } else {
        $stmt = $conn->prepare("UPDATE menu SET foodName=?, price=?, food_category=? WHERE food_id=?");
        $stmt->bind_param("sdsi", $name, $price, $category, $id);
    }
    $stmt->execute();
    header("Location: admin_menu.php");
    exit();
}

$menu = $conn->query("SELECT * FROM menu ORDER BY food_category, foodName")->fetch_all(MYSQLI_ASSOC);

$edit_item = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $edit_item = $conn->query("SELECT * FROM menu WHERE food_id = $id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Menu – Admin</title>
  <link rel="stylesheet" href="CSS/admin.css">
  <link rel="stylesheet" href="CSS/admin_menu.css">
</head>
<body>
  <h1>📝 Manage Menu</h1>

  <form method="POST" enctype="multipart/form-data" class="menu-form">
    <input type="hidden" name="food_id" value="<?= $edit_item['food_id'] ?? '' ?>">
    <label>Food Name:
      <input type="text" name="foodName" required value="<?= $edit_item['foodName'] ?? '' ?>">
    </label>
    <label>Price:
      <input type="number" step="0.01" name="price" required value="<?= $edit_item['price'] ?? '' ?>">
    </label>
    <label>Category:
      <select name="category" required>
        <option value="">Select</option>
        <option value="Specialties" <?= ($edit_item['food_category'] ?? '') === 'Specialties' ? 'selected' : '' ?>>Specialties</option>
        <option value="Budget Bowls" <?= ($edit_item['food_category'] ?? '') === 'Budget Bowls' ? 'selected' : '' ?>>Budget Bowls</option>
        <option value="Side Orders" <?= ($edit_item['food_category'] ?? '') === 'Side Orders' ? 'selected' : '' ?>>Side Orders</option>
        <option value="Drinks" <?= ($edit_item['food_category'] ?? '') === 'Drinks' ? 'selected' : '' ?>>Drinks</option>
      </select>
    </label>

    <label>Upload Image:
      <input type="file" name="image_file" accept="image/*">
    </label>

    <?php if (!empty($edit_item['image_url'])): ?>
      <p>Current Image: <img src="<?= $edit_item['image_url'] ?>" alt="Current" width="60"></p>
    <?php endif; ?>

    <button type="submit" name="<?= $edit_item ? 'update' : 'add' ?>">
      <?= $edit_item ? 'Update Item' : 'Add Item' ?>
    </button>
  </form>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Image</th>
        <th>Food</th>
        <th>Price</th>
        <th>Category</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($menu as $item): ?>
        <tr>
          <td>
            <?php if ($item['image_url']): ?>
              <img src="<?= $item['image_url'] ?>" alt="img" class="menu-image">
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($item['foodName']) ?></td>
          <td>₱<?= number_format($item['price'], 2) ?></td>
          <td><?= htmlspecialchars($item['food_category']) ?></td>
          <td>
            <a href="?edit=<?= $item['food_id'] ?>">Edit</a> |
            <a href="?delete=<?= $item['food_id'] ?>" onclick="return confirm('Delete this item?')">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>
