<?php
session_start();
require_once "db.php"; // connect databases

/*
// check if user logged in
if (isset($_SESSION['Username'])) {
    header("Location: index.php"); // redirection if user already logged in
    exit();
}
*/

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['Username'];
    $password = $_POST['Password'];

    
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($password === $user['Password']) {
            // if password is correct, store user in the session
            $_SESSION['Username'] = $username;
            header("Location: index.php"); // redirection after log in
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<?php require_once "header.php"; ?>

<main>
    <h2>Login</h2>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <form method="POST">
        <label for="username">Username</label>
        <input type="text" name="Username" id="username" required>
        <label for="password">Password</label>
        <input type="password" name="Password" id="password" required>
        <button type="submit">Login</button>
    </form>
</main>

<?php require_once "footer.php"; ?>