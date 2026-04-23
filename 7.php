<?php
/**
 * Searches for profiles by name and outputs them as an HTML list.
 * * @param PDO $pdo       An active PDO database connection.
 * @param string $query  The search term provided by the user.
 */
function displayProfileResults($pdo, $query) {
    // 1. Prepare the search term for a partial match (e.g., "John" finds "Johnson")
    $searchTerm = "%" . $query . "%";

    // 2. Prepare the SQL statement
    // Using placeholders (?) prevents SQL Injection
    $sql = "SELECT name FROM profiles WHERE name LIKE ? ORDER BY name ASC";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute([$searchTerm]);
        $results = $stmt->fetchAll();

        // 3. Generate HTML output
        if ($results) {
            echo "<ul>";
            foreach ($results as $row) {
                // htmlspecialchars prevents XSS if a name contains special characters
                echo "<li>" . htmlspecialchars($row['name']) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No profiles found matching '" . htmlspecialchars($query) . "'.</p>";
        }
    } catch (PDOException $e) {
        echo "Error: Could not retrieve data.";
        // In a real app, log $e->getMessage() to a file
    }
}

// --- EXAMPLE USAGE ---
// $pdo = new PDO("mysql:host=localhost;dbname=test", "user", "pass");
// $nameFromForm = $_GET['name'] ?? '';
// displayProfileResults($pdo, $nameFromForm);
?>