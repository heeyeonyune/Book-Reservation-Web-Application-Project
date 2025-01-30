<?php
$isLoggedIn = isset($_SESSION['Username']); // Check if the user is logged or not
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library</title>
    <!-- link to css file -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="banner">
    <img src="../images/library_banner.png" alt="Header Banner">
</div>

<header>
    <nav>
        <ul>
            <?php if ($isLoggedIn): ?>
                <!-- Navigation bar for logged in users -->
                <li><a href="index.php">Home</a></li>
                <li><a href="view_reservations.php">My Reservations</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <!-- Navigation bar for users who are not logged in -->
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

</body>
</html>
