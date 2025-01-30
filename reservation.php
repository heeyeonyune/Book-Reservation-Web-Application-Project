<?php
session_start();
require_once "db.php"; // Connect to DB

// Only allow access to logged-in users
if (!isset($_SESSION['Username'])) {
    header("Location: login.php"); // Redirect to login page
    exit();
}

// Handle book reservation 
if (isset($_GET['ISBN'])) {
    $isbn = $_GET['ISBN'];
    $username = $_SESSION['Username'];

    // Check if the book exists and is available for reservation
    $stmt = $conn->prepare("SELECT * FROM Books WHERE ISBN = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if ($book && $book['Reserved'] == 0) {
        // If the book is available, insert reservation into the ReservedBook table
        $stmt = $conn->prepare("INSERT INTO ReservedBook (ISBN, Username, ReservedDate) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $isbn, $username);
        $stmt->execute();

        // Update the reservation status to reserved(1)
        $stmt = $conn->prepare("UPDATE Books SET Reserved = 1 WHERE ISBN = ?");
        $stmt->bind_param("s", $isbn);
        $stmt->execute();

        // Redirect to the view_reservation page after successful reservation
        header("Location: view_reservations.php"); 
        exit();
    }
}
?>

<?php require_once "header.php"; ?>

<main>
    <h2>Reservation Success</h2>
    <p>Your reservation has been successfully made!</p>
    <a href="view_reservations.php">View My Reservations</a>
</main>

<?php require_once "footer.php"; ?>