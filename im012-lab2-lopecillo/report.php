<?php
require_once 'config.php';



$statsRes = mysqli_query($conn, "SELECT COUNT(*) AS total_products, COALESCE(SUM(stock),0) AS total_stock, COALESCE(SUM(price * stock),0) AS total_value, COALESCE(AVG(price),0) AS avg_price FROM products");
$stats = mysqli_fetch_assoc($statsRes);


$catSql = "SELECT c.name AS category, COUNT(p.id) AS product_count,
COALESCE(SUM(p.stock),0) AS 
total_stock, COALESCE(SUM(p.price * p.stock),0) AS total_value,
 COALESCE(AVG(p.price),0) AS 
 avg_price FROM products p JOIN 
 categories c ON p.category_id = c.id
  GROUP BY c.id, 
c.name ORDER BY total_value DESC";
$catRes = mysqli_query($conn, $catSql);

$supSql = "SELECT s.name AS supplier,
 COUNT(p.id) AS product_count, COALESCE(SUM(p.stock),0) AS 
 total_stock FROM products p JOIN suppliers s ON p.supplier_id = s.id 
 GROUP BY s.id, s.name ORDER BY product_count DESC";

$supRes = mysqli_query($conn, $supSql);

$prodRes = mysqli_query($conn, "SELECT p.id, p.name, p.price, p.stock, c.name AS category, s.name AS supplier, 
p.created_at FROM products p LEFT JOIN categories c ON p.category_id=c.id LEFT JOIN suppliers s ON p.supplier_id=s.id 
ORDER BY p.id DESC");
?>

<!DOCTYPE html>
<html>
<head>

<title>View Report</title>

<style>

body{
    font-family:Arial;
    background:#f5f5f5;
    margin:30px;
}

.container{
    width:95%;
    margin:auto;
}

h1{
    text-align:center;
    color:#333;
    margin-bottom:25px;
}

.stats{
    display:flex;
    justify-content:space-between;
    margin-bottom:25px;
    gap:15px;
}

.card{
    width:23%;
    background:#3498db;
    color:white;
    text-align:center;
    padding:20px;
    border-radius:10px;
}

.card h2{
    margin:0;
    font-size:30px;
}

.card p{
    margin-top:10px;
    font-size:16px;
}

.back-btn{
    background:#28a745;
    color:white;
    padding:10px 18px;
    text-decoration:none;
    border-radius:5px;
    display:inline-block;
    margin-bottom:20px;
}

.back-btn:hover{
    background:#218838;
}

.section-title{
    margin:30px 0 10px;
    color:#333;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
    margin-bottom:25px;
}

table th{
    background:#333;
    color:white;
}

table th,
table td{
    padding:10px;
    border:1px solid #ddd;
    text-align:center;
}

table tr:nth-child(even){
    background:#f9f9f9;
}

.low-stock{
    background:red;
}
.navbar {
    background-color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    padding: 12px 0;
    margin-bottom: 25px;
}

.navbar a {
    color: #17a2b8;
    text-decoration: none;
    font-size: 17px;
    text-transform: uppercase;
    padding: 12px 18px;
    border-radius: 5px;
    transition: 0.3s;
}

.navbar a:hover {
    background-color: grey;
    color: white;
}
</style>

</head>

<body>

<div class="container">
    <h1>View Report</h1>
     <?php include 'navbar.php'; ?>

    <a href="index.php" class="back-btn">Back</a>

    <div class="stats">

        <div class="card">
            <h2><?php echo number_format($stats['total_products']); ?></h2>
            <p>Total Products</p>
        </div>

        <div class="card">
            <h2><?php echo number_format($stats['total_stock']); ?></h2>
            <p>Total Stock</p>
        </div>

        <div class="card">
            <h2>₱<?php echo number_format($stats['total_value'],2); ?></h2>
            <p>Inventory Value</p>
        </div>

        <div class="card">
            <h2>₱<?php echo number_format($stats['avg_price'],2); ?></h2>
            <p>Average Price</p>
        </div>

    </div>

<h2 class="section-title">Per-Category Breakdown</h2>
<table>
    <tr>
        <th>Category</th>
        <th>Product Count</th>
        <th>Total Stock</th>
        <th>Total Value</th>
        <th>Average Price</th>
    </tr>
    <?php while($c = mysqli_fetch_assoc($catRes)){ ?>
    <tr>
        <td><?php echo htmlspecialchars($c['category']); ?></td>
        <td><?php echo number_format($c['product_count']); ?></td>
        <td><?php echo number_format($c['total_stock']); ?></td>
        <td>₱<?php echo number_format($c['total_value'],2); ?></td>
        <td>₱<?php echo number_format($c['avg_price'],2); ?></td>
    </tr>
    <?php } ?>
</table>

<h2 class="section-title">Per-Supplier Breakdown</h2>
<table>
    <tr>
        <th>Supplier</th>
        <th>Product Count</th>
        <th>Total Stock</th>
    </tr>
    <?php while($s = mysqli_fetch_assoc($supRes)){ ?>
    <tr>
        <td><?php echo htmlspecialchars($s['supplier']); ?></td>
        <td><?php echo number_format($s['product_count']); ?></td>
        <td><?php echo number_format($s['total_stock']); ?></td>
    </tr>
    <?php } ?>
</table>

<h2 class="section-title">Product Details</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Category</th>
        <th>Supplier</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Date Added</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($prodRes)){ ?>
    <tr class="<?php if($row['stock']<20) echo 'low-stock'; ?>">
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['category'] ?? '-'); ?></td>
        <td><?php echo htmlspecialchars($row['supplier'] ?? '-'); ?></td>
        <td>₱<?php echo isset($row['price']) ? number_format($row['price'],2) : '-'; ?></td>
        <td><?php echo isset($row['stock']) ? $row['stock'] : '-'; ?></td>
        <td><?php echo $row['created_at']; ?></td>
    </tr>
    <?php } ?>
</table>

</div>
</body>
</html>


