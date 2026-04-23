<?php
// 1. Retrieve the search query from the URL (e.g., search.php?search_query=books)
// We use the null coalescing operator (??) to set a default if the parameter is missing.
$search_query = $_GET['search_query'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
</head>
<body>

    <?php if ($search_query !== ''): ?>
        <h1>You searched for: <?php echo htmlspecialchars($search_query); ?></h1>
    <?php else: ?>
        <h1>Search our site</h1>
    <?php endif; ?>

    <form method="GET">
        <input type="text" name="search_query" placeholder="Enter keywords...">
        <button type="submit">Search</button>
    </form>

</body>
</html>