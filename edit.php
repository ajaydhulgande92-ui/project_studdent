```php
<?php
$conn = new mysqli("localhost", "root", "", "student_management", 3307);

if ($conn->connect_error) {
    die("Database connection failed");
}

$conn->set_charset("utf8mb4");

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("Invalid student ID");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $age = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT);
    $course = trim($_POST["course"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($name === "" || !$age || $age < 1 || $age > 120 ||
        $course === "" || $phone === "") {
        $message = "Please enter valid details.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE students SET name=?, age=?, course=?, phone=? WHERE student_id=?"
        );

        $stmt->bind_param("sissi", $name, $age, $course, $phone, $id);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $message = "Update failed.";
        }
    }
}

$stmt = $conn->prepare(
    "SELECT name, age, course, phone FROM students WHERE student_id=?"
);
$stmt->bind_param("i", $id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student not found");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
</head>
<body>

<h2>Edit Student Details</h2>

<p><?php echo htmlspecialchars($message); ?></p>

<form method="POST">

    Name:
    <input type="text" name="name"
    value="<?php echo htmlspecialchars($student['name']); ?>" required>
    <br><br>

    Age:
    <input type="number" name="age"
    value="<?php echo htmlspecialchars((string)$student['age']); ?>" required>
    <br><br>

    Course:
    <input type="text" name="course"
    value="<?php echo htmlspecialchars($student['course']); ?>" required>
    <br><br>

    Phone:
    <input type="text" name="phone"
    value="<?php echo htmlspecialchars($student['phone']); ?>" required>
    <br><br>

    <button type="submit">Update Student</button>

</form>

<br>
<a href="index.php">Back to Home</a>

</body>
</html>
```