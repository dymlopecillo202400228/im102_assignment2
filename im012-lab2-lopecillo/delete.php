<?php
require_once 'config.php';

// If accessed via GET with id, show a confirmation page
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = mysqli_prepare($conn, "SELECT p.id, p.name, p.price, p.stock, c.name AS category FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $product = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
    } else {
        header('Location: index.php?msg=error');
        exit;
    }

    if (!$product) {
        header('Location: index.php?msg=notfound');
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirm Delete</title>
        <style>
            body{font-family:Arial;background:#f5f5f5;margin:30px}
            .card{background:#fff;padding:20px;border-radius:8px;box-shadow:0 0 8px rgba(0,0,0,.1);max-width:700px;margin:auto}
            .danger{color:#c82333}
            .btn{display:inline-block;padding:8px 14px;border-radius:6px;text-decoration:none;color:#fff}
            .btn-cancel{background:#6c757d}
            .btn-confirm{background:#dc3545}
        </style>
    </head>
    <body>
        <div class="card">
            <h2 class="danger">Confirm Delete</h2>
            <p>Are you sure you want to delete the following product?</p>
            <ul>
                <li><strong>ID:</strong> <?php echo $product['id']; ?></li>
                <li><strong>Name:</strong> <?php echo htmlspecialchars($product['name']); ?></li>
                <li><strong>Category:</strong> <?php echo htmlspecialchars($product['category'] ?? '-'); ?></li>
                <li><strong>Price:</strong> ₱<?php echo number_format($product['price'],2); ?></li>
                <li><strong>Stock:</strong> <?php echo $product['stock']; ?></li>
            </ul>

            <form method="POST" action="delete.php" style="display:inline">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                <button type="submit" class="btn btn-confirm">Confirm Delete</button>
            </form>
            <a href="index.php" class="btn btn-cancel" style="margin-left:8px">Cancel</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_POST['id']);

$stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        header("Location: index.php?msg=deleted");
        exit;
    } else {
        mysqli_stmt_close($stmt);
        header("Location: index.php?msg=notfound");
        exit;
    }
} else {
    header('Location: index.php?msg=error');
    exit;
}
?>