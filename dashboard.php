<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>SKI SHOP RENTAL DATA</h1>
        <div class="categories">
            <div class="category" id="customer-section">
                <h3>Customer</h3>
                <a href="customers.php">Search Existing Customers</a>
                <a href="addcustomer.php">Add New Customer</a>
            </div>

            <div class="category" id="ski-section">
                <h3>Skis</h3>
                <a href="addskirental.php?rental_type=daily">Daily Ski Rental</a>
                <a href="addskirental.php?rental_type=seasonal">Seasonal Ski Rental</a>
                <a href="skisearch.php">Search Ski Inventory</a>
                <a href="addski.php">Add Ski Inventory</a>
            </div>

            <div class="category" id="board-section">
                <h3>Snowboards</h3>
                <a href="addboardrental.php?rental_type=daily">Daily Board Rental</a>
                <a href="addboardrental.php?rental_type=seasonal">Seasonal Board Rental</a>
                <a href="boardsearch.php">Search Board Inventory</a>
                <a href="addboard.php">Add Board Inventory</a>
            </div>

            <div class="category" id="rental-section">
                <h3>Rentals</h3>
                <a href="skirentals.php">Return or View Skis</a>
                <a href="boardrentals.php">Return or View Boards</a>
                <a href="pastrentals.php">View Past Rentals</a>
            </div>
        </div>
    </div>
</body>
</html>

