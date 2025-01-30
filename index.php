<?php
session_start(); // Session start

// Connect to DB
require_once "db.php";

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['Username']);

// bring the book categories from databases
$categories = [];
$stmt = $conn->prepare("SELECT CategoryID, CategoryDetails FROM Category ORDER BY CategoryDetails ASC");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}
$stmt->close();

?>

<?php require_once "header.php"; ?>

<main>
    <!-- For logged in users -->
    <?php if ($isLoggedIn): ?>
        <div class="welcome-message">
            <h2>Hello, <?php echo htmlspecialchars($_SESSION['Username']); ?>!</h2>
            <h2>Welcome to our Library System</h2>
        </div>
        <!-- Search form -->
        <form action="search.php" method="GET">
            <p>Search the book by book title or author</p>
            <input type="text" name="search" placeholder="Enter book title or author...">
            <select name="category" class="category-dropdown">
                <option value="">Select Category (Optional)</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['CategoryID']); ?>">
                        <?php echo htmlspecialchars($category['CategoryDetails']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Search</button>
        </form>
        <div class="reservation_container">
            <a href="view_reservations.php" id="viewReserve">View My Reservations</a>
        </div>
    <?php else: ?>
        <!-- Login and Register buttons for non logged in users -->
        <div class="library-banner">
            <img src="../images/library.jpg" alt="Library">
            <div class="btn_container">
                <p>Please login or register to use our services</p>
                <div class="index_container">
                    <a href="login.php"><button class="index_button">Login</button></a>
                    <a href="register.php"><button class="index_button">Register</button></a>
                </div>
            </div>
        </div>

    <?php endif; ?>
</main>

<?php require_once "footer.php"; ?>
