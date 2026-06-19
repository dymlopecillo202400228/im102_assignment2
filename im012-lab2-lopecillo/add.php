<?php
require_once 'config.php';


$categories = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
$suppliers = mysqli_query($conn, "SELECT id, name FROM suppliers ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];

    $category_id = (int) $_POST['category_id'];
    $supplier_id = (int) $_POST['supplier_id'];

    if (
        !empty($name) &&
        !empty($description) &&
        $price >= 0 &&
        $stock >= 0 &&
        $category_id > 0 &&
        $supplier_id > 0
    ) {

        $sql = "INSERT INTO products
                (name, description, price, stock, category_id, supplier_id)
                VALUES
                ('$name','$description',$price,$stock,$category_id,$supplier_id)";

        mysqli_query($conn, $sql);

        header("Location: index.php");
        exit();
    }

    echo "Please fill in all required fields.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
</head>
<body>
        <head>
        <title>Add Product</title>
    

    <style>

body{
    font-family: Arial;
    background:#f5f5f5;
    margin:30px;
}

.container{
    width:600px;
    margin:40px auto;
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,.2);
}

h1{
    text-align:center;
    margin-bottom:25px;
    color:#333;
}

label{
    display:block;
    margin-top:15px;
    margin-bottom:5px;
    font-weight:bold;
}

input,
textarea,
select{
    width:100%;
    padding:10px;
    border:1px solid #ddd;
    border-radius:5px;
    font-size:15px;
    box-sizing:border-box;
}

textarea{
    resize:vertical;
    min-height:100px;
}

button{
    width:100%;
    background:#007bff;
    color:white;
    border:none;
    padding:12px;
    border-radius:5px;
    font-size:16px;
    cursor:pointer;
    margin-top:20px;
}

button:hover{
    background:#0056b3;
}

.back-btn{
    display:inline-block;
    margin-top:15px;
    background:#6c757d;
    color:white;
    text-decoration:none;
    padding:10px 18px;
    border-radius:5px;
}

.back-btn:hover{
    background:#5a6268;
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

<div class="container">
    <h1>Add Product</h1>
  <?php include 'navbar.php'; ?>
    <form method="POST">

        <label>Product Name</label>
        <input type="text" name="name" required>

        <label>Description</label>
        <textarea name="description" required></textarea>

        <label>Price</label>
        <input type="number" step="0.01" name="price" required>

        <label>Stock</label>
        <input type="number" name="stock" required>

        <label>Category</label>
        <select name="category_id" required>
            <option value="">-- Select Category --</option>

            <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                <option value="<?php echo $cat['id']; ?>">
                    <?php echo $cat['name']; ?>
                </option>
            <?php endwhile; ?>

        </select>

        <label>Supplier</label>
        <select name="supplier_id" required>
            <option value="">-- Select Supplier --</option>

            <?php while($sup = mysqli_fetch_assoc($suppliers)): ?>
                <option value="<?php echo $sup['id']; ?>">
                    <?php echo $sup['name']; ?>
                </option>
            <?php endwhile; ?>

        </select>

        <button type="submit">Save Product</button>

    </form>

    <a href="index.php" class="back-btn">Back </a>

</div>