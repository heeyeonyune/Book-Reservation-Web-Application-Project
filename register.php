<?php
session_start();
require_once "db.php"; // connect to DB

// checking if a session has already started 
if (isset($_SESSION['Username'])) {
    header("Location: index.php");
    exit();
}

// Handle the registration process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['Username'];
    $password = $_POST['Password'];
    $confirm_password = $_POST['confirm_password'];
    $firstname = $_POST['FirstName'];
    $surname = $_POST['Surname'];
    $address1 = $_POST['AddressLine1'];
    $address2 = $_POST['AddressLine2'];
    $city = $_POST['City'];
    $telephone = $_POST['Telephone'];
    $mobile = $_POST['Mobile'];

    // Check if required fields are empty
    if (empty($username) || empty($password) || empty($firstname) || empty($surname) || empty($address1) || empty($city) || empty($telephone) || empty($mobile)) {
        $error = "All fields are required.";
    } 

    // Validate password length
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } 
    // Validate if passwords match
    elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } 
    // Validate mobile number format
    elseif (strlen($mobile) != 10 || !ctype_digit($mobile)) {
        $error = "Mobile number must contain 10 numeric characters.";
    } 

    else {
        // Check if the username already exists in the database
        $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Username is already taken.";
        } else {
            // Insert the new user into the database
            $stmt = $conn->prepare("INSERT INTO Users (Username, Password, FirstName, Surname, AddressLine1, AddressLine2, City, Telephone, Mobile) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $username, $password, $firstname, $surname, $address1, $address2, $city, $telephone, $mobile);
            if ($stmt->execute()) {
                $_SESSION['Username'] = $username; // Auto login after registration
                header("Location: index.php"); 
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<?php require_once "header.php"; ?>

<main>
    <h2>Register</h2>
    <h4>• Choose a unique username.</h4>
    <h4>• Password must contain six characters.</h4>
    <h4>• Mobile phone numbers must contain 10 numeric characters.</h4>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <form method="POST">
        <label for="username">Username</label>
        <input type="text" name="Username" id="username" required>

        <label for="password">Password</label>
        <input type="password" name="Password" id="password" required>
        
        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        
        <label for="firstname">First Name</label>
        <input type="text" name="FirstName" id="firstname" required>
        
        <label for="surname">Surname</label>
        <input type="text" name="Surname" id="surname" required>
        
        <label for="address1">Address Line 1</label>
        <input type="text" name="AddressLine1" id="address1" required>
        
        <label for="address2">Address Line 2</label>
        <input type="text" name="AddressLine2" id="address2">

        <label for="city">City</label>
        <input type="text" name="City" id="city" required>

        <label for="telephone">Telephone</label>
        <input type="text" name="Telephone" id="telephone" required>

        <label for="mobile">Mobile</label>
        <input type="text" name="Mobile" id="mobile" required>

        <button type="submit">Register</button>
    </form>
</main>

<?php require_once "footer.php"; ?>