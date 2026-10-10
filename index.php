<?php
include 'dbcon.php';


function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');


    if ($name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare(
            "INSERT INTO users (name, email) VALUES (?, ?)"
        );
        $stmt->bind_param("ss", $name, $email);


        if ($stmt->execute()) {
            header("Location: index.php?msg=added");
            exit;
        }
        $stmt->close();
    } else {
        $error = "Please enter a valid name and email.";
    }
}


$result = $conn->query("SELECT * FROM users ORDER BY id DESC");


$messages = [
    'added' => 'User added successfully!',
    'updated' => 'User updated successfully!',
    'deleted' => 'User deleted successfully!'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body class="bg-light">


<div class="container py-5">
    <h2 class="text-center mb-4">User Management System</h2>


    <?php if (isset($_GET['msg'], $messages[$_GET['msg']])): ?>
        <div class="alert alert-success">
            <?= e($messages[$_GET['msg']]) ?>
        </div>
    <?php endif; ?>


    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
    <?php endif; ?>


    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Add New User</h5>


                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name"
                                   class="form-control"
                                   placeholder="Enter full name" required>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email"
                                   class="form-control"
                                   placeholder="Enter email address" required>
                        </div>


                        <button type="submit"
                                class="btn btn-primary w-100">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>


        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="mb-3">Registered Users</h5>


                    <div class="table-responsive">
                        <table class="table ">
                            <thead class="table">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($user = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= e($user['id']) ?></td>
                                        <td><?= e($user['name']) ?></td>
                                        <td><?= e($user['email']) ?></td>
                                        <td><?= e($user['created_at']) ?></td>
                                        <td>
                                            <a href="edit.php?id=<?= (int)$user['id'] ?>"
                                               class="btn btn-warning btn-sm">
                                                Edit
                                            </a>
                                            <a href="delete.php?id=<?= (int)$user['id'] ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Are you sure you want to delete this user?')">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        No data found
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>