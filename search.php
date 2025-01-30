<?php
// checking if a session has already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "db.php"; // Connect to DB

// Number of rows to show per page
$rows_per_page = 5;

$search_term = isset($_GET['search']) && $_GET['search'] !== "" ? "%" . $_GET['search'] . "%" : null; // Prepare the search term
$category = isset($_GET['category']) && $_GET['category'] !== "" ? $_GET['category'] : null;

// If both search term and category are empty, no results (set to 0)
if ($search_term === null && $category === null) {
    $total_rows = 0;  
} else {
    $query = "SELECT Books.*, Category.CategoryDetails 
              FROM Books 
              JOIN Category ON Books.CategoryID = Category.CategoryID
              WHERE 1=1";

    // Add search term condition
    $params = [];
    $types = ""; // Initialize as an empty string

    if ($search_term) {
        $query .= " AND (Books.BookTitle LIKE ? OR Books.Author LIKE ?)";
        $params[] = $search_term;
        $params[] = $search_term;
        $types .= "ss";  
    }

    // Add category condition
    if ($category) {
        $query .= " AND Books.CategoryID = ?";
        $params[] = $category;
        $types .= "s";  
    }

    // Get total number of results
    $count_query = "SELECT COUNT(*) as total FROM Books 
                    JOIN Category ON Books.CategoryID = Category.CategoryID
                    WHERE 1=1"; 

    if ($search_term) {
        $count_query .= " AND (Books.BookTitle LIKE ? OR Books.Author LIKE ?)";
    }
    if ($category) {
        $count_query .= " AND Books.CategoryID = ?";
    }

    // Execute the query
    $count_stmt = $conn->prepare($count_query);
    if ($count_stmt === false) {
        die('Error in prepare: ' . $conn->error);
    }

    // Ensure $types is not empty before binding parameters
    if (!empty($types)) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_rows = $count_result->fetch_assoc()['total']; // Get the total number of results
    $count_stmt->close();
}

// If no results, set $result to null
if ($total_rows == 0) {
    $result = null;
}

// Pagination variables
$total_pages = $total_rows > 0 ? ceil($total_rows / $rows_per_page) : 0; // Calculate total pages only if results exist
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get the current page
$offset = ($current_page - 1) * $rows_per_page; // Calculate the OFFSET value

// Execute the query for results
if ($total_rows > 0) {
    $query .= " LIMIT ?, ?";
    $params[] = $offset;
    $params[] = $rows_per_page;
    $types .= "ii";  

    // Prepare and execute the query
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die('Error in prepare: ' . $conn->error); 
    }
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<?php require_once "header.php"; ?>

<main>
    <h2>Search Results</h2>
    <?php if ($result && $result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Edition</th>
                <th>Year</th>
                <th>Category</th>
                <th>Availability Status</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['BookTitle']); ?></td>
                    <td><?php echo htmlspecialchars($row['Author']); ?></td>
                    <td><?php echo htmlspecialchars($row['Edition']); ?></td>
                    <td><?php echo htmlspecialchars($row['Year']); ?></td>
                    <td><?php echo htmlspecialchars($row['CategoryDetails']); ?></td>
                    <td>
                        <?php if ($row['Reserved'] > 0): ?>
                            <span>Not Available: Already Reserved</span>
                        <?php else: ?>
                            <span><a href="reservation.php?ISBN=<?php echo $row['ISBN']; ?>">Available: Reserve</a></span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="search.php?search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category); ?>&page=1">&laquo; First</a>
                <a href="search.php?search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category); ?>&page=<?php echo $current_page - 1; ?>">Previous</a>
            <?php endif; ?>

            <span>Page <?php echo $current_page; ?> of <?php echo $total_pages; ?></span>

            <?php if ($current_page < $total_pages): ?>
                <a href="search.php?search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category); ?>&page=<?php echo $current_page + 1; ?>">Next</a>
                <a href="search.php?search=<?php echo urlencode($search_term); ?>&category=<?php echo urlencode($category); ?>&page=<?php echo $total_pages; ?>">Last &raquo;</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>No books found.</p>
    <?php endif; ?>
</main>

<?php require_once "footer.php"; ?>
