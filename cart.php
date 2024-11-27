<?php
session_start();
include "connect.php"; // Include Database Connection Script

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("location:index.php");
    exit;
} else {
    $username = $_SESSION['username'];
    $query = $db->prepare("SELECT * FROM user WHERE username=?");
    $query->execute([$username]);
    $control = $query->fetch(PDO::FETCH_ASSOC);
    if ($control['admin'] != 1) {
        header("Location:films.php");
        exit;
    }
}

// Initialize the cart array if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle form submissions for cart actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productID = (int)$_POST['productID'];
    $action = $_POST['action'] ?? null;

    if ($action === 'increment') {
        if (isset($_SESSION['cart'][$productID])) {
            $_SESSION['cart'][$productID]++; // Increase quantity
        } else {
            $_SESSION['cart'][$productID] = 1; // Set quantity to 1
        }
    } elseif ($action === 'decrement') {
        if (isset($_SESSION['cart'][$productID]) && $_SESSION['cart'][$productID] > 1) {
            $_SESSION['cart'][$productID]--; // Decrease quantity
        } else {
            unset($_SESSION['cart'][$productID]); // Remove item if quantity is 0
        }
    } elseif ($action === 'remove_all') {
        unset($_SESSION['cart'][$productID]); // Remove all quantities
    }
}

// Fetch item details from the database
$cartItems = [];
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productID => $quantity) {
        $query = $db->prepare("SELECT * FROM menu WHERE ID = ?");
        $query->execute([$productID]);
        if ($query->rowCount() > 0) {
            $product = $query->fetch(PDO::FETCH_ASSOC);
            $product['quantity'] = $quantity; // Add quantity
            $cartItems[] = $product; // Store in cart items array
        }
    }
}

// Calculate total price and check for combo deals
$totalPrice = 0;
$comboDeals = [];
$comboDiscount = 0.70; // Discount for combos
$comboEligibleFoodItems = ['Popcorn', 'Nachos', 'Hot Dog']; // Define eligible food items
$softDrinkKey = 'Soft Drink'; // Define soft drink key
$foodWithDrinks = []; // Track combinations of food and drinks for combos

// Loop through cart items to calculate price and combo discounts
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];

    // Check if food item is eligible for combo
    if (in_array($item['name'], $comboEligibleFoodItems)) {
        $foodWithDrinks[$item['name']] = $item['quantity'];
    } elseif ($item['name'] === $softDrinkKey) {
        $softDrinkQuantity = $item['quantity'];
    }
}

// Process combos if valid combinations exist
foreach ($foodWithDrinks as $foodItem => $foodQuantity) {
    $combosPossible = min($foodQuantity, isset($softDrinkQuantity) ? $softDrinkQuantity : 0);
    
    if ($combosPossible > 0) {
        $foodItemPrice = $db->query("SELECT price FROM menu WHERE name = '$foodItem'")->fetchColumn();
        $comboPrice = ($foodItemPrice - $comboDiscount) * $combosPossible;

        $comboDeals[] = [
            'combo_name' => $foodItem . ' + ' . $softDrinkKey,
            'original_price' => $foodItemPrice * $combosPossible,
            'discounted_price' => $comboPrice,
            'quantity' => $combosPossible,
        ];

        $totalPrice -= $comboDiscount * $combosPossible;  // Adjust total price for discount
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="stylesheets/main.css">
    <link rel="stylesheet" href="stylesheets/cart.css">
    <link rel="stylesheet" href="stylesheets/layout.css">
</head>
<body>
    <header>
        <h1>Your Cart</h1>
        <nav>
            <ul>
                <li><a href="films.php">Home</a></li>
                <li><a href="new_food_drink.php">Menu</a></li>
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <div class="cart-content">
            <?php if (!empty($cartItems)): ?>
                <section class="menu-list-section">
                    <h2>Your Cart Items</h2>
                    <div class="cart-list">
                        <table>
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                                        <td>£<?php echo number_format($item['price'], 2); ?></td>
                                        <td>
                                            <?php echo $item['quantity']; ?>
                                            <form method="post" action="cart.php" style="display:inline;">
                                                <input type="hidden" name="productID" value="<?php echo $item['ID']; ?>">
                                                <button type="submit" name="action" value="decrement" class="quantity-btn">-</button>
                                                <button type="submit" name="action" value="increment" class="quantity-btn">+</button>
                                            </form>
                                        </td>
                                        <td>£<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                        <td>
                                            <form method="post" action="cart.php" style="display:inline;">
                                                <input type="hidden" name="productID" value="<?php echo $item['ID']; ?>">
                                                <button type="submit" name="action" value="remove_all" class="quantity-btn">Remove All</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <h2>Total Price: £<?php echo number_format($totalPrice, 2); ?></h2>

                <?php if (!empty($comboDeals)): ?>
                    <h2 style="color: green;">Combos Applied:</h2>
                    <?php foreach ($comboDeals as $combo): ?>
                        <p style="color: green;">
                            <?php echo htmlspecialchars($combo['combo_name']) . ': Original Price: £' . number_format($combo['original_price'], 2) . 
                            ' - Discounted Price: £' . number_format($combo['discounted_price'], 2); ?>
                        </p>
                    <?php endforeach; ?>
                <?php endif; ?>

                <form method="post" action="checkout.php">
                    <button type="submit" class="quantity-btn">Proceed to Checkout</button>
                </form>
            <?php else: ?>
                <p>Your cart is empty.</p>
                <a href="new_food_drink.php">Return to menu</a>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>© 2024 Cineplex. All Rights Reserved.</p>
    </footer>
</body>
</html>