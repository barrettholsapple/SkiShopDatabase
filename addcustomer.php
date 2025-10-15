<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Customer</title>
    <link rel="stylesheet" href="skishopstyles.css">
    <script>
        // JavaScript function to format phone number as (xxx)-xxx-xxxx
        function formatPhoneNumber(input) {
            // Remove all non-numeric characters
            let phoneNumber = input.value.replace(/\D/g, '');
            
            // Check if we have at least 10 digits
            if (phoneNumber.length <= 3) {
                input.value = `(${phoneNumber}`;
            } else if (phoneNumber.length <= 6) {
                input.value = `(${phoneNumber.substring(0, 3)})-${phoneNumber.substring(3)}`;
            } else if (phoneNumber.length <= 10) {
                input.value = `(${phoneNumber.substring(0, 3)})-${phoneNumber.substring(3, 6)}-${phoneNumber.substring(6, 10)}`;
            } else {
                // Limit the input to 10 digits
                input.value = `(${phoneNumber.substring(0, 3)})-${phoneNumber.substring(3, 6)}-${phoneNumber.substring(6, 10)}`;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Add New Customer</h1>
        <form method="POST" action="processcustomer.php">
            <table class="form-table">
                <tr><th><label for="fname">First Name:</label></th><td><input type="text" id="fname" name="fname" required></td></tr>
                <tr><th><label for="lname">Last Name:</label></th><td><input type="text" id="lname" name="lname" required></td></tr>
                <tr><th><label for="streetadd">Street:</label></th><td><input type="text" id="streetadd" name="streetadd" required></td></tr>
                <tr><th><label for="city">City:</label></th><td><input type="text" id="city" name="city" required></td></tr>
                <tr><th><label for="stateadd">State:</label></th><td><input type="text" id="stateadd" name="stateadd" required></td></tr>
                <tr><th><label for="zip">Zip Code:</label></th><td><input type="number" id="zip" name="zip" required></td></tr>
                <tr><th><label for="driversli">Driver's License #:</label></th><td><input type="text" id="driversli" name="driversli" maxlength="15" required></td></tr>
                <tr><th><label for="phonenum">Phone Number:</label></th>
                    <td><input type="text" id="phonenum" name="phonenum" pattern="\(\d{3}\)-\d{3}-\d{4}" placeholder="(xxx)-xxx-xxxx" required oninput="formatPhoneNumber(this)"></td>
                </tr>
                <tr><th><label for="email">Email:</label></th><td><input type="email" id="email" name="email" maxlength="40" required></td></tr>
                <tr><th><label for="birthday">Birthday:</label></th><td><input type="date" id="birthday" name="birthday" required></td></tr>
                <tr><th><label for="weightlbs">Weight (lbs):</label></th><td><input type="number" id="weightlbs" name="weightlbs" required></td></tr>
                <tr><th>Height:</th>
                    <td>
                        <input type="number" name="heightft" style="width: 100px;" placeholder="ft" min="0" required> '
                        <input type="number" name="heightin" style="width: 100px;" placeholder="in" min="0" max="11" required> "
                    </td>
                </tr>
                <tr><th>Skill Level:</th>
                    <td>
                        <div class="radio-group">
                            <label><input type="radio" name="skilllevel" value="1" required> Beginner</label>
                            <label><input type="radio" name="skilllevel" value="2"> Intermediate</label>
                            <label><input type="radio" name="skilllevel" value="3"> Advanced</label>
                        </div>
                    </td>
                </tr>
            </table>
            <br>
            <input type="submit" value="Add Customer" class="submit-button">
        </form>

        <h4>
            <a href="dashboard.php" class="back-link">Back to Dashboard</a>
        </h4>
    </div>
</body>
</html>
