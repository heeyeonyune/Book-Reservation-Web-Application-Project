<?php
session_start();
require_once "db.php"; // Connect to DB

// Check if the user is logged in 
if (!isset($_SESSION['Username'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

$username = $_SESSION['Username']; // Get the username
$stmt = $conn->prepare("SELECT ReservedBook.*, Books.BookTitle FROM ReservedBook JOIN Books ON ReservedBook.ISBN = Books.ISBN WHERE ReservedBook.Username = ?");
$stmt->bind_param("s", $username); // Bind the useername to the query
$stmt->execute();
$result = $stmt->get_result(); // Execute the query and get the result
?>

<?php require_once "header.php"; ?>

<main>
    <h2>My Reservations</h2>
    <?php if ($result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Book Title</th>
                <th>Reserved Date</th>
                <th>Action</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['BookTitle']); ?></td>
                    <td><?php echo htmlspecialchars($row['ReservedDate']); ?></td>
                    <td>
                        <a href="cancel_reservation.php?ISBN=<?php echo $row['ISBN']; ?>">Cancel</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No reservations yet.</p>
    <?php endif; ?>
</main>

<?php require_once "footer.php"; ?>