<?php
session_start();

try {
    require_once 'config/database.php';
    require_once 'includes/functions.php';
    require_once 'includes/auth.php';

    header('Content-Type: application/json');
    // Check if user is logged in and is admin
    $isLoggedIn = isset($_SESSION['user_id']);
    $currentUser = null;
    
    if ($isLoggedIn) {
        $currentUser = getUserById($_SESSION['user_id']);
    }
    
    if (!$isLoggedIn) {
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit();
    }
    
    if (!$currentUser || $currentUser['role'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'Admin access required']);
        exit();
    }

    // Get action type
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    // Handle different actions
    switch ($action) {
        case 'add':
            addProduct();
            break;
        case 'edit':
            editProduct();
            break;
        case 'delete':
            deleteProduct();
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}

// Function to add a new product
function addProduct() {
    global $pdo;
    
    // Validate required fields
    $requiredFields = ['name', 'category_id', 'price', 'stock', 'description'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit();
        }
    }
    
    // Check if image was uploaded
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Product image is required']);
        exit();
    }
    
    // Process image upload
    $uploadDir = 'uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $targetFilePath = $uploadDir . $fileName;
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
    $fileType = $_FILES['image']['type'];
    
    if (!in_array($fileType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed']);
        exit();
    }
    
    // Upload the file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
        // Sanitize inputs
        $name = $_POST['name'];
        $categoryId = $_POST['category_id'];
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $description = $_POST['description'];
        $image = $targetFilePath;
        $productId = generateId();
        
        // Insert into database using PDO
        $stmt = $pdo->prepare("INSERT INTO products (id, name, category_id, price, stock, description, image_url) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$productId, $name, $categoryId, $price, $stock, $description, $image])) {
            echo json_encode(['success' => true, 'message' => 'Product added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image']);
    }
}

// Function to edit a product
function editProduct() {
    global $pdo;
    
    // Validate required fields
    $requiredFields = ['id', 'name', 'category_id', 'price', 'stock', 'description'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit();
        }
    }
    
    $productId = $_POST['id'];
    $name = $_POST['name'];
    $categoryId = $_POST['category_id'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $description = $_POST['description'];
    
    // Check if new image was uploaded
    $newImagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Process image upload
        $uploadDir = 'uploads/products/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        
        // Check file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $fileType = $_FILES['image']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Only JPG, JPEG, PNG & GIF files are allowed']);
            exit();
        }
        
        // Get old image path to delete it later
        $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $oldImageData = $stmt->fetch();
        $oldImage = $oldImageData ? $oldImageData['image_url'] : null;
        
        // Upload the new file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $newImagePath = $targetFilePath;
            
            // Delete old image file if it exists
            if ($oldImage && file_exists($oldImage)) {
                unlink($oldImage);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload new image']);
            exit();
        }
    }
    
    // Update product in database
    if ($newImagePath) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, stock = ?, description = ?, image_url = ? WHERE id = ?");
        $result = $stmt->execute([$name, $categoryId, $price, $stock, $description, $newImagePath, $productId]);
    } else {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, stock = ?, description = ? WHERE id = ?");
        $result = $stmt->execute([$name, $categoryId, $price, $stock, $description, $productId]);
    }
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
}

// Function to delete a product
function deleteProduct() {
    global $pdo;
    
    // Validate product ID
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Product ID is required']);
        exit();
    }
    
    $productId = $_POST['id'];
    
    // Get product image path before deleting
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if ($product) {
        $imagePath = $product['image_url'];
        
        // Delete from database
        $deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        
        if ($deleteStmt->execute([$productId])) {
            // Delete image file if it exists
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }
            
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error occurred']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
    }
}
?>
