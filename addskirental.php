<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'skishop');
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Ski Rental</title>
    <link rel="stylesheet" href="skishopstyles.css">
    <script>
function setupAutocomplete(inputId, resultId, hiddenId, endpoint) {
    const input = document.getElementById(inputId);
    const hidden = document.getElementById(hiddenId);
    const resultBox = document.getElementById(resultId);

    input.addEventListener('input', function () {
        const query = input.value;
        if (!query) {
            resultBox.innerHTML = '';
            return;
        }

        fetch(endpoint + '?term=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                resultBox.innerHTML = '';
                data.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item.label;
                    li.addEventListener('click', () => {
                        input.value = item.label;
                        hidden.value = item.id;
                        resultBox.innerHTML = '';
                    });
                    resultBox.appendChild(li);
                });
            });
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target)) {
            resultBox.innerHTML = '';
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    setupAutocomplete('customerSearch', 'customerResults', 'customerid', 'searchcustomersjava.php');
    setupAutocomplete('skiSearch', 'skiResults', 'skiid', 'searchskijava.php'); // change to searchboards.php for boards
});
</script>
</head>
<body>
    <div class="container">
        <h1>Add New  
            <?php 
                echo (isset($_GET['rental_type']) && $_GET['rental_type'] === 'seasonal') ? 'Seasonal Rental' : 'Daily Rental';
            ?>
        </h1>

        <form method="POST" action="processskirental.php">
            <table>
                <tr>
                    <th>Customer:</th>
                    <td>
                        <div class="autocomplete-container">
                            <input type="text" id="customerSearch" placeholder="Search customer..." autocomplete="off">
                            <input type="hidden" name="customerid" id="customerid">
                            <ul id="customerResults" class="autocomplete-results"></ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>Ski:</th>
                    <td>
                        <div class="autocomplete-container">
                            <input type="text" id="skiSearch" placeholder="Search ski..." autocomplete="off">
                            <input type="hidden" name="serialnumber" id="skiid">
                            <ul id="skiResults" class="autocomplete-results"></ul>
                        </div>
                    </td>
                </tr>
                <!-- DIN Settings -->
                <tr><th>Binding DIN Left Toe:</th><td><input type="text" name="bindingdinlt" required></td></tr>
                <tr><th>Binding DIN Left Heel:</th><td><input type="text" name="bindingdinlh" required></td></tr>
                <tr><th>Binding DIN Right Toe:</th><td><input type="text" name="bindingdinrt" required></td></tr>
                <tr><th>Binding DIN Right Heel:</th><td><input type="text" name="bindingdinrh" required></td></tr>

                <!-- Return Date -->
                <tr>
                    <th>Return Date:</th>
                    <td>
                        <?php
                        if (isset($_GET['rental_type']) && $_GET['rental_type'] === 'seasonal') {
                            echo '<input type="date" name="returndate" value="2026-04-01" required>';
                            echo '<input type="hidden" name="seasonal" value="1">';
                        } else {
                            echo '<input type="date" name="returndate" required>';
                            echo '<input type="hidden" name="seasonal" value="0">';
                        }
                        ?>
                    </td>
                </tr>

                <!-- Pole and Boot Info -->
                <tr>
                <tr>
                    <th>Poles Needed:</th>
                    <td>
                        <div class="radio-group">
                        <label><input type="radio" name="poles" value="1"> Yes</label>
                        <label><input type="radio" name="poles" value="0" checked> No</label>
                        </div>
                    </td>
                </tr>
                <tr><th>Pole Size:</th><td><input type="text" name="polesize"></td></tr>
                <tr><th>Pole Make:</th><td><input type="text" name="polemake"></td></tr>
                <tr>
                    <th>Boots Needed:</th>
                    <td>
                        <div class="radio-group">
                            <label><input type="radio" name="boots" value="1"> Yes</label>
                            <label><input type="radio" name="boots" value="0" checked> No</label>
                        </div>
                    </td>
                </tr>
                <tr><th>Boot Number:</th><td><input type="text" name="bootsize"></td></tr>
                <tr><th>Boot Length:</th><td><input type="text" name="bootmake"></td></tr>

                <tr><th>Salesperson:</th><td><input type="text" name="salesperson" required></td></tr>
                <tr><th>Notes:</th><td><textarea name="notes" rows="5" cols="50"></textarea></td></tr>
            </table>
            <br>
            <input type="submit" value="Add Rental">
        </form>

        <h4><a href="dashboard.php" class="back-link">Back to Dashboard</a></h4>
    </div>
</body>
</html>
