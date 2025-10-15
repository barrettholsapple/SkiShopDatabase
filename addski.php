<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Ski</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>Add New Ski</h1>
        <form method="POST" action="processski.php">
            <table>
                <tr><th>Make:</th><td><input type="text" name="make" required></td></tr>
                <tr><th>Model:</th><td><input type="text" name="model" required></td></tr>
                <tr><th>Size:</th><td><input type="text" name="sizeof" required></td></tr>
                <tr><th>Serial Number:</th><td><input type="text" name="serialnumber" required></td></tr>
                <tr><th>Notes:</th><td><textarea name="notes" rows="5" cols="50" maxlength="100"></textarea></td></tr>
            </table>
            <br>
            <input type="submit" value="Add Ski">
        </form>

        <h4><a href="dashboard.php" class="back-link">Back to Dashboard</a></h4>
    </div>
</body>
</html>

