<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

$servername = "localhost";
$password = "D|O^QBm|N^7g";
$username = "u999765516_application";
$dbname = "u999765516_application";
// $username   = "root";  
// $password   = "";      
// $dbname     = "addadmin"; 


// Get category from query parameter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Validate category
$validCategories = ['minor', 'premier', 'subjunior', 'junior', 'senior'];
$category = strtolower($category);

if (empty($category)) {
    echo json_encode([
        'success' => false,
        'message' => 'Category parameter is required',
        'data' => null
    ]);
    exit;
}

if (!in_array($category, $validCategories)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid category. Valid categories are: ' . implode(', ', $validCategories),
        'data' => null
    ]);
    exit;
}

// Handle "Premeir" alternate spelling
if ($category === 'premeir') {
    $category = 'premier';
}

try {
    // Create database connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Prepare and execute query
    $stmt = $pdo->prepare("
        SELECT 
            id,
            jamiaid,
            campus,
            category,
            point,
            after
        FROM Campus_Points 
        WHERE LOWER(category) = :category 
        ORDER BY point DESC, campus ASC
    ");
    
    $stmt->execute(['category' => $category]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get the "after" value (assuming all rows have the same "after" value for a category)
    $afterValue = 0;
    if (!empty($results)) {
        $afterValue = $results[0]['after'];
    }
    
    // Add rank to results
    $rankedResults = [];
    foreach ($results as $index => $row) {
        $rankedResults[] = [
            'rank' => $index + 1,
            'campus' => $row['campus'],
            'points' => (int)$row['point'],
            'jamiaid' => $row['jamiaid'],
            'after' => (int)$row['after']
        ];
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Data fetched successfully',
        'data' => [
            'category' => ucfirst($category),
            'after' => $afterValue,
            'totalCampuses' => count($rankedResults),
            'results' => $rankedResults
        ]
    ]);
    
} catch (PDOException $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'data' => null
    ]);
}

// Close connection
$pdo = null;
?>