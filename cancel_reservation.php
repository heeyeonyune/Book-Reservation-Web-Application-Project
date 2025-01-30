<?php
session_start();
require_once "db.php"; // Connect to DB


// Only allow access to logged-in users
if (!isset($_SESSION['Username'])) {
    header("Location: login.php"); // Redirect to the login page if the user is not logged in
    exit();
}

if (isset($_GET['ISBN'])) {
    $isbn = $_GET['ISBN']; // Get the ISBN of the book to cancel the reservation
    $username = $_SESSION['Username'];

    // Cancel the reservation by deleting the record from the ReservedBook table 
    $stmt = $conn->prepare("DELETE FROM ReservedBook WHERE ISBN = ? AND Username = ?");
    $stmt->bind_param("ss", $isbn, $username);
    $stmt->execute();

    // Update the reservation status to not reserved(0)
    $stmt = $conn->prepare("UPDATE Books SET Reserved = 0 WHERE ISBN = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();

    header("Location: view_reservations.php"); // Redirect to view_reservation page after canceling the reservation
    exit();
}
?>
 