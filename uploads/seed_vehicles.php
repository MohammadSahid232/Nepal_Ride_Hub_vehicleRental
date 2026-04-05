<?php
require_once '../includes/db_connect.php';

echo "<h2>Seeding Sample Vehicles...</h2>";

try {
    $vehicles = [
        [
            'name' => 'Hyundai i20',
            'type' => 'car',
            'brand' => 'Hyundai',
            'model_year' => 2022,
            'price_per_day' => 3500.00,
            'description' => 'A comfortable and reliable premium hatchback perfect for city tours and highway driving across Nepal.',
            'image_path' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&q=80&w=800'
        ],
        [
            'name' => 'Royal Enfield Classic 350',
            'type' => 'bike',
            'brand' => 'Royal Enfield',
            'model_year' => 2021,
            'price_per_day' => 1500.00,
            'description' => 'Experience the twisty mountain roads with the timeless and powerful Royal Enfield Classic.',
            'image_path' => 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&q=80&w=800'
        ],
        [
            'name' => 'Toyota Hiace Grandia',
            'type' => 'bus',
            'brand' => 'Toyota',
            'model_year' => 2018,
            'price_per_day' => 8500.00,
            'description' => 'Spacious 14-seater tourist bus ideal for large families and group travel. Fully air-conditioned.',
            'image_path' => 'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&q=80&w=800'
        ]
    ];

    $count = 0;
    foreach ($vehicles as $v) {
        $stmt = $pdo->prepare("INSERT INTO vehicles (name, type, brand, model_year, price_per_day, description, image_path, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'available')");
        $stmt->execute([
            $v['name'], $v['type'], $v['brand'], $v['model_year'], $v['price_per_day'], $v['description'], $v['image_path']
        ]);
        $count++;
    }

    echo "<h3 style='color: green;'>Successfully added {$count} sample vehicles to the database!</h3>";
    echo "<a href='index.php'>Go back to Home Page</a>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error inserting sample vehicles: " . $e->getMessage() . "</p>";
}
?>
