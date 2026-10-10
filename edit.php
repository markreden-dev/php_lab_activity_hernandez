<?php
include 'dbcon.php';


function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);


if (!$id || $id < 1) {
    header("Location: index.php");
    exit;
}


$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $email);


if (!$stmt->fetch()) {
    $stmt->close();
    header("Location: index.php");
    exit;
}
$stmt->close();


$error = '';


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');


    if ($name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare(
            "UPDATE users SET name = ?, email = ? WHERE id = ?"
        );
        $stmt->bind_param("ssi", $name, $email, $id);


        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php?msg=updated");
            exit;
        }
        $stmt->close();
        $error = "Unable to update user.";
    } else {
        $error = "Please enter a valid name and email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h3 class="mb-4">Edit User</h3>


            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>


            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control"
                           value="<?= e($name) ?>" required>
                </div>


                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= e($email) ?>" required>
                </div>


                <button class="btn btn-primary">Update User</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>