<?php
session_start();
include "connect.php"; // Database connection

// Check if the user is logged in
$isAdmin = false;
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $query = $db->prepare("SELECT * FROM user WHERE username=?");
    $query->execute([$username]);
    $control = $query->fetch(PDO::FETCH_ASSOC);
    $isAdmin = ($control['admin'] == 1);
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch food items from the database
$foodItems = [];
try {
    $query = $db->query("SELECT * FROM menu ORDER BY ID");
    $foodItems = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log($e->getMessage());
}

// Function to calculate total items in the cart
function getTotalItems($cart) {
    return array_sum($cart); // Sum of all quantities in the cart
}

// Calculate total items in the cart
$totalItems = getTotalItems($_SESSION['cart']);

// Handle quantity adjustment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $productID = (int)$_POST['productID'];

    if ($productID <= 0) {
        exit('Invalid product ID');
    }

    if ($_POST['action'] === 'increment') {
        if (isset($_SESSION['cart'][$productID])) {
            $_SESSION['cart'][$productID]++; // Increase quantity
        } else {
            $_SESSION['cart'][$productID] = 1; // Set quantity to 1
        }
    } elseif ($_POST['action'] === 'decrement') {
        if (isset($_SESSION['cart'][$productID])) {
            $_SESSION['cart'][$productID]--; // Decrease quantity
            if ($_SESSION['cart'][$productID] <= 0) {
                unset($_SESSION['cart'][$productID]); // Remove item if quantity is 0
            }
        }
    }

    // Check for meal deal eligibility
    if ((isset($_SESSION['cart']['Popcorn']) || isset($_SESSION['cart']['Nachos']) || isset($_SESSION['cart']['Hot Dog'])) && isset($_SESSION['cart']['Soft Drink'])) {
        echo "<script>
                if (confirm('Do you want to make this a meal deal?')) {
                    window.location.href = 'combo.php?addMeal=true';
                }
              </script>";
    }

    // Recalculate total items after adjusting quantities
    $totalItems = getTotalItems($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cineplex - Food & Drink Menu</title>
    <link rel="stylesheet" href="stylesheets/main.css">
    <link rel="stylesheet" href="stylesheets/fooddrink.css">
    <link rel="stylesheet" href="stylesheets/menu1.css">
    <link rel="stylesheet" href="stylesheets/layout.css">
</head>
<body>
    <header>
        <h1>Cineplex - Food & Drink Menu</h1>
        <nav>
            <ul>
                <li><a href="films.php">Home</a></li>
                <?php if ($isAdmin): ?>
                    <li><a href="add_food_drink.php">Add Food Item</a></li>
                <?php endif; ?>
                <li><a href="upcomingfilms.php">Upcoming Films</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href='logout.php'>Logout</a></li>
                <li>
                    <a href="cart.php">View Cart</a>
                    <img src="images/cart.png" alt="Cart Icon" style="height: 20px; vertical-align: middle;">
                    <?php if ($totalItems > 0): ?>
                        <span>(<?php echo $totalItems; ?>)</span>
                    <?php endif; ?>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="welcome">
            <h2>Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>!</h2>
        </div>

        <section class="menu-list-section">
            <h2>Current Food & Drink Items</h2>
            <div class="menu-list">
                <?php if (count($foodItems) > 0): ?>
                    <?php foreach ($foodItems as $item): ?>
                        <div class="menu-item">
                            <div class="menu-image">
                                <img src="images/food_drink/menu_item<?php echo htmlspecialchars($item['ID']); ?>.jpg" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="menu-details">
                                <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p><?php echo htmlspecialchars($item['description']); ?></p>
                                <span class="price">£<?php echo number_format($item['price'], 2); ?></span>
                            </div>
                            <div class="menu-actions">
                                <form method="post" action="" style="display:flex; align-items: center;">
                                    <input type="hidden" name="productID" value="<?php echo htmlspecialchars($item['ID']); ?>">
                                    <button type="submit" name="action" value="decrement" class="quantity-btn" <?php echo (isset($_SESSION['cart'][$item['ID']]) && $_SESSION['cart'][$item['ID']] > 0) ? '' : 'disabled'; ?>>-</button>
                                    <span><?php echo isset($_SESSION['cart'][$item['ID']]) ? $_SESSION['cart'][$item['ID']] : 0; ?></span>
                                    <button type="submit" name="action" value="increment" class="quantity-btn">+</button>
                                    <a href="cart.php" class="view-cart-btn" style="margin-left: 10px;">View Cart</a>
                                </form>
                            </div>
                            <div class="admin-actions">
                                <?php if ($isAdmin): ?>
                                    <form method="get" action="edit_menu.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['ID']); ?>">
                                        <input type="submit" value="Edit" class="admin-btn">
                                    </form>
                                    <form method="post" action="delete_menu.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['ID']); ?>">
                                        <input type="submit" value="Delete" class="admin-btn" onclick="return confirm('Are you sure you want to delete this item?');">
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No food items available.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>© 2024 Cineplex. All Rights Reserved.</p>
    </footer>
</body>
</html>