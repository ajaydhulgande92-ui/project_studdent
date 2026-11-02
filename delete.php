```php
<?php
$conn = new mysqli("localhost", "root", "", "student_management", 3307);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid student ID.");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Confirm Delete</title>
    </head>
    <body>
        <h2>Delete Student</h2>
        <p>Are you sure you want to delete this student?</p>

        <form method="POST" action="delete.php?id=<?= (int)$id ?>">
            <button type="submit">Yes, Delete</button>
            <a href="index.php">Cancel</a>
        </form>
    </body>
    </html>
    <?php
    exit;
}

$stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();

    header("Location: index.php");
    exit;
}

$stmt->close();
$conn->close();

die("Unable to delete student.");
?>
```