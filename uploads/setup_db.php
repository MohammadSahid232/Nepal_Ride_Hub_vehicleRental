<?php
// setup_db.php
require_once 'includes/db_connect.php';

echo "<h2>Setting up Nepal Ride Hub Database...</h2>";

try {
    // Users Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        role ENUM('customer', 'admin') DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>Users table created.</p>";

    // User Documents Table (Citizenship, License)
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_documents (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        document_type ENUM('citizenship', 'license', 'passport') NOT NULL,
        file_path VARCHAR(255) NOT NULL,
        status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
        expiry_date DATE NULL,
        uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    echo "<p>User Documents table created.</p>";

    // Vehicles Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS vehicles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        type ENUM('car', 'bike', 'bus') NOT NULL,
        brand VARCHAR(50) NOT NULL,
        model_year INT NOT NULL,
        price_per_day DECIMAL(10, 2) NOT NULL,
        status ENUM('available', 'maintenance', 'booked') DEFAULT 'available',
        description TEXT,
        image_path VARCHAR(255),
        gps_lat DECIMAL(10, 8) NULL,
        gps_lng DECIMAL(11, 8) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>Vehicles table created.</p>";

    // Bookings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        vehicle_id INT NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
    )");
    echo "<p>Bookings table created.</p>";

    // Feedback Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        vehicle_id INT NOT NULL,
        rating INT NOT NULL CHECK(rating >= 1 AND rating <= 5),
        comments TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
    )");
    echo "<p>Feedback table created.</p>";

    // Maintenance Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS maintenance (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vehicle_id INT NOT NULL,
        service_date DATE NOT NULL,
        description TEXT NOT NULL,
        cost DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE
    )");
    echo "<p>Maintenance table created.</p>";

    // Insert Default Admin
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE id=id");
    $stmt->execute(['Admin', 'admin@nepalridehub.com', $adminPassword, '9800000000', 'admin']);
    echo "<p>Default Admin account created (admin@nepalridehub.com / admin123).</p>";

    echo "<h3 style='color: green;'>Database setup complete successfully!</h3>";
    echo "<a href='index.php'>Go to Homepage</a>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error setting up database: " . $e->getMessage() . "</p>";
}
?>
