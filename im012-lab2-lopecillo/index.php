<?php
require_once 'config.php';


$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$sqlStats = "
    SELECT
        COUNT(*) AS total,
        SUM(stock) AS total_stock,
        SUM(price * stock) AS total_value,
        SUM(CASE WHEN stock < 20 THEN 1 ELSE 0 END) AS low_stock
    FROM products
";

$resultStats = mysqli_query($conn, $sqlStats);
$stats = mysqli_fetch_assoc($resultStats);

$categories = mysqli_query($conn, "
    SELECT *
    FROM categories
    ORDER BY name
");

$sql = "
    SELECT
        p.id,
        p.name,
        p.description,
        p.price,
        p.stock,
        c.name AS category,
        s.name AS supplier,
        p.created_at
    FROM products p
    JOIN categories c
        ON p.category_id = c.id
    JOIN suppliers s
        ON p.supplier_id = s.id
    WHERE 1=1
";


if ($search != "") {
    $search = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
}


if ($category != "") {
    $category = mysqli_real_escape_string($conn, $category);
    $sql .= " AND c.name = '$category'";
}


$sql .= " ORDER BY p.id DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory Management</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>Inventory Management</h1>
   
       <?php include 'navbar.php'; ?>
    <div class="stats">
        <div class="card">
            <php 
            <h2><?php echo $stats['total']; ?></h2>
            <p>Total Products</p>
        </div>
        <div class="card">
            <h2><?php echo $stats['total_stock']; ?></h2>
            <p>Total Stock</p>
        </div>
        <div class="card">
            <h2>₱<?php echo number_format($stats['total_value'], 2); ?></h2>
            <p>Inventory Value</p>
        </div>
        <div class="card">
            <h2><?php echo $stats['low_stock']; ?></h2>
            <p>Low Stock</p>
        </div>
    </div>
    <p>
        <a href="add.php" class="button">Add Product</a>
        <a href="report.php" class="button report-btn">View Report</a>
    </p>
    <!-- Search -->
    <div class="search-box">
        <form method="GET">
            <input
                type="text"
                name="search"
                placeholder="Search Product"
                value="<?php echo htmlspecialchars($search); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
                    <option
                        value="<?php echo $c['name']; ?>"
                        <?php if ($category == $c['name']) echo "selected"; ?>>

                        <?php echo $c['name']; ?>
                    </option>
                <?php } ?>

            </select>
            <button type="submit">Search</button>
        </form>
    </div>
    <table>

        <tr>
            <th>ID</th>
            <th>Product</th>
            <th>Description</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
            <th>Supplier</th>
            <th>Date Added</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr class="<?php if ($row['stock'] < 20) echo 'low-stock'; ?>">
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>₱<?php echo number_format($row['price'], 2); ?></td>
                <td><?php echo $row['stock']; ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>
                <td><?php echo htmlspecialchars($row['supplier']); ?></td>
                <td><?php echo $row['created_at']; ?>
                </td><td class="action-cell">
                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="delete-link">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>