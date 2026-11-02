<?php
$conn = new mysqli("localhost", "root", "", "student_management", 3307);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $age = filter_input(INPUT_POST, "age", FILTER_VALIDATE_INT);
    $course = trim($_POST["course"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($name === "" || $age === false || $age === null ||
        $age < 1 || $age > 120 || $course === "" || $phone === "") {
        $message = "Please enter valid information in all fields.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO students (name, age, course, phone) VALUES (?, ?, ?, ?)"
        );
        $stmt->bind_param("siss", $name, $age, $course, $phone);

        if ($stmt->execute()) {
            $message = "Student added successfully!";
        } else {
            $message = "Unable to add student.";
        }

        $stmt->close();
    }
}

$result = $conn->query("SELECT student_id, name, age, course, phone FROM students ORDER BY student_id DESC");
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM students");
$totalStudents = $totalResult->fetch_assoc()['total'];

$courseResult = $conn->query("SELECT COUNT(DISTINCT course) AS total FROM students");
$totalCourses = $courseResult->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>

    
<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #e0eafc, #cfdef3);
        margin: 0;
        padding: 35px 20px;
        color: #243247;
    }

    .container {
        max-width: 1100px;
        margin: auto;
        background: #ffffff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(31, 55, 90, 0.15);
    }

    h1 {
        text-align: center;
        color: #173b70;
        font-size: 32px;
        margin-bottom: 10px;
    }

    h2 {
        color: #244d80;
        font-size: 22px;
        margin-top: 30px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e5edf7;
    }

    form {
        background: #f8faff;
        padding: 22px;
        border-radius: 12px;
        border: 1px solid #e1e9f5;
    }

    label {
        display: block;
        font-weight: bold;
        margin-top: 10px;
        color: #344b68;
    }

    input {
        width: 100%;
        padding: 12px;
        margin: 8px 0 15px;
        border: 1px solid #cbd5e1;
        border-radius: 7px;
        font-size: 15px;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    input:focus {
        border-color: #1769aa;
        box-shadow: 0 0 0 3px rgba(23, 105, 170, 0.12);
    }

    button {
        background: #1769aa;
        color: white;
        padding: 12px 22px;
        border: none;
        border-radius: 7px;
        cursor: pointer;
        font-size: 15px;
        font-weight: bold;
        transition: background 0.2s, transform 0.2s;
    }

    button:hover {
        background: #104d82;
        transform: translateY(-1px);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
    }

    th, td {
        border: 1px solid #e0e7f0;
        padding: 13px 12px;
        text-align: left;
    }

    th {
        background: #174a7e;
        color: white;
        font-size: 14px;
    }

    tr:nth-child(even) {
        background: #f5f8fc;
    }

    tr:hover {
        background: #edf4ff;
    }

    td a {
        display: inline-block;
        text-decoration: none;
        padding: 7px 11px;
        margin: 2px;
        border-radius: 5px;
        font-size: 13px;
        font-weight: bold;
    }

    td a[href^="edit.php"] {
        background: #dbeafe;
        color: #1d4ed8;
    }

    td a[href^="edit.php"]:hover {
        background: #bfdbfe;
    }

    td a[href^="delete.php"] {
        background: #fee2e2;
        color: #b91c1c;
    }

    td a[href^="delete.php"]:hover {
        background: #fecaca;
    }

    .message {
        text-align: center;
        color: #15803d;
        font-weight: bold;
        background: #dcfce7;
        padding: 12px;
        border-radius: 7px;
    }

    .table-wrap {
        overflow-x: auto;
        border-radius: 8px;
    }

    @media (max-width: 600px) {
        body {
            padding: 15px 8px;
        }

        .container {
            padding: 16px;
        }

        h1 {
            font-size: 25px;
        }

        h2 {
            font-size: 20px;
        }

        th, td {
            padding: 9px 7px;
            font-size: 13px;
        }
    }
.dashboard {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 20px;
    margin: 25px 0;
}

.card {
    background: linear-gradient(135deg, #1769aa, #174a7e);
    color: white;
    padding: 22px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(23, 74, 126, 0.2);
}

.card h3 {
    margin: 0 0 12px;
    font-size: 18px;
}

.card p {
    font-size: 32px;
    font-weight: bold;
    margin: 0;
}

@media (max-width: 600px) {
    .dashboard {
        grid-template-columns: 1fr;
    }
}

</style>

</head>

<body>
<div class="container">
    <h1>Student Management System</h1>

<div class="dashboard">

    <div class="card">
        <h3>Total Students</h3>
        <p><?= (int)$totalStudents ?></p>
    </div>

    <div class="card">
        <h3>Total Courses</h3>
        <p><?= (int)$totalCourses ?></p>
    </div>

</div>


    <h2>Add New Student</h2>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Student Name</label>
        <input type="text" name="name" maxlength="100" required>

        <label>Age</label>
        <input type="number" name="age" min="1" max="120" required>

        <label>Course</label>
        <input type="text" name="course" maxlength="100" required>

        <label>Phone Number</label>
        <input type="tel" name="phone" maxlength="15" required>

        <button type="submit">Add Student</button>
    </form>

    <h2>Student Records</h2>

    <div class="table-wrap">
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Age</th>
                <th>Course</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars((string)$row["student_id"]) ?></td>
                        <td><?= htmlspecialchars($row["name"]) ?></td>
                        <td><?= htmlspecialchars((string)$row["age"]) ?></td>
                        <td><?= htmlspecialchars($row["course"]) ?></td>
                        <td><?= htmlspecialchars($row["phone"]) ?></td>
                        <td><a href="edit.php?id=<?= (int)$row['student_id'] ?>">Edit</a>
                            |

                            <a href="delete.php?id=<?= (int)$row['student_id'] ?>"
                            onclick="return confirm('Are you sure you want to delete this student?');">
                            Delete
                            </a>
                         </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No student records found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>
</body>
</html>

<?php
$conn->close();
?>