<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Board</title>
    <link rel="stylesheet" href="skishopstyles.css">
</head>
<body>
    <div class="container">
        <h1>Add New Board</h1>
        <form method="POST" action="processboard.php" class="form-table">
            <table>
                <tr>
                    <th><label for="boardmake">Make:</label></th>
                    <td><input type="text" name="boardmake" id="boardmake" required></td>
                </tr>
                <tr>
                    <th><label for="boardmodel">Model:</label></th>
                    <td><input type="text" name="boardmodel" id="boardmodel" required></td>
                </tr>
                <tr>
                    <th><label for="boardsize">Size:</label></th>
                    <td><input type="text" name="boardsize" id="boardsize" required></td>
                </tr>
                <tr>
                    <th><label for="boardserialnumber">Serial Number:</label></th>
                    <td><input type="text" name="boardserialnumber" id="boardserialnumber" required></td>
                </tr>
                <tr>
                    <th><label for="boardnotes">Notes:</label></th>
                    <td><textarea name="boardnotes" id="boardnotes" rows="5" maxlength="100"></textarea></td>
                </tr>
            </table>
            <br>
            <input type="submit" value="Add Board">
        </form>

        <h4><a href="dashboard.php" class="back-link">Back to Dashboard</a></h4>
    </div>
</body>
</html>
