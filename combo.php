<?php
include "connect.php"; // Assuming you have a database connection here

function getCombos($db) {
    // You might want to fetch this from the database
    $comboDeals = [];

    // Example combo definition for Popcorn + Soft Drink, Nachos + Soft Drink, Hot Dog + Soft Drink
    $foodItems = ['Popcorn', 'Nachos', 'Hot Dog'];
    $softDrink = 'Soft Drink';

    foreach ($foodItems as $food) {
        // Here, you could define specific logic or prices for combos
        $comboDeals[] = [
            'combo_name' => "$food + $softDrink",
            'food_item' => $food,
            'soft_drink' => $softDrink,
            'discount' => 0.70, // Discount logic can be expanded later
        ];
    }
    
    return $comboDeals;
}

function applyComboDiscount($cartItems) {
    $totalDiscount = 0;

    foreach ($cartItems as $item) {
        // Check if the item is a food item eligible for combos
        if (in_array($item['name'], ['Popcorn', 'Nachos', 'Hot Dog'])) {
            // Check if we have a soft drink in cart
            // This should access your global cart or session variable holding cart items
            // Make sure to include a reference to it here or pass it as an argument
            $softDrinkCount = isset($_SESSION['cart']['Soft Drink']) ? $_SESSION['cart']['Soft Drink'] : 0;
            
            // Calculate the minimum number of combos
            $numberOfCombos = min($item['quantity'], $softDrinkCount);

            // Each combo discount
            if ($numberOfCombos > 0) {
                $totalDiscount += $numberOfCombos * 0.70; // Assuming a discount per combo
            }
        }
    }

    return $totalDiscount;
}

?>