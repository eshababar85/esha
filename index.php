<?php

// ==========================
// DATABASE CONNECTION
// ==========================

$conn = new mysqli("localhost", "root", "", "mydatabase");

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}


// ==========================
// ADD DATA
// ==========================

if (isset($_POST['add'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "INSERT INTO students (name, email) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $name, $email);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    }
}


// ==========================
// UPDATE DATA
// ==========================

if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "UPDATE students
            SET name = ?, email = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $name, $email, $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    }
}


// ==========================
// DELETE DATA
// ==========================

if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    $sql = "DELETE FROM students WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    }
}


// ==========================
// GET DATA FOR EDIT
// ==========================

$editData = null;

if (isset($_GET['edit'])) {

    $id = $_GET['edit'];

    $sql = "SELECT * FROM students WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    $editData = $result->fetch_assoc();
}


// ==========================
// DISPLAY ALL DATA
// ==========================

$result = $conn->query(
    "SELECT * FROM students ORDER BY id DESC"
);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>PHP MySQL CRUD</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 40px;
        }

        .container {
            width: 800px;
            max-width: 100%;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        h2 {
            color: #444;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }

        .add-btn {
            background: green;
        }

        .update-btn {
            background: #007bff;
        }

        .cancel-btn {
            background: #777;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th {
            background: #333;
            color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .edit {
            color: blue;
            text-decoration: none;
            margin-right: 10px;
        }

        .delete {
            color: red;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Student Management</h1>


    <!-- ==========================
         ADD / UPDATE FORM
    =========================== -->

    <?php if ($editData) { ?>

        <h2>Update Student</h2>

        <form method="POST">

            <input
                type="hidden"
                name="id"
                value="<?php echo $editData['id']; ?>"
            >

            <label>Name</label>

            <input
                type="text"
                name="name"
                value="<?php echo htmlspecialchars($editData['name']); ?>"
                required
            >

            <label>Email</label>

            <input
                type="email"
                name="email"
                value="<?php echo htmlspecialchars($editData['email']); ?>"
                required
            >

            <button
                type="submit"
                name="update"
                class="update-btn"
            >
                Update Student
            </button>

            <a
                href="index.php"
                class="cancel-btn"
            >
                Cancel
            </a>

        </form>

    <?php } else { ?>

        <h2>Add Student</h2>

        <form method="POST">

            <label>Name</label>

            <input
                type="text"
                name="name"
                placeholder="Enter student name"
                required
            >

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Enter student email"
                required
            >

            <button
                type="submit"
                name="add"
                class="add-btn"
            >
                Add Student
            </button>

        </form>

    <?php } ?>


    <!-- ==========================
         DISPLAY DATA
    =========================== -->

    <h2>Student List</h2>

    <table>

        <tr>

            <th>ID</th>

            <th>Name</th>

            <th>Email</th>

            <th>Action</th>

        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>

            <tr>

                <td>
                    <?php echo $row['id']; ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars($row['name']);
                    ?>
                </td>

                <td>
                    <?php
                    echo htmlspecialchars($row['email']);
                    ?>
                </td>

                <td>

                    <a
                        class="edit"
                        href="index.php?edit=<?php echo $row['id']; ?>"
                    >
                        Edit
                    </a>

                    <a
                        class="delete"
                        href="index.php?delete=<?php echo $row['id']; ?>"
                        onclick="return confirm('Are you sure you want to delete this student?');"
                    >
                        Delete
                    </a>

                </td>

            </tr>

        <?php } ?>

    </table>

</div>

</body>

</html>