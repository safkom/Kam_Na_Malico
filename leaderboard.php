<?php
include("connect.php");

// Function to retrieve the leaderboard of users with the most lunch expenses
function getLeaderboard() {
    global $conn;
    $year = date("Y"); // Assuming a school year is a calendar year

    $stmt = $conn->prepare("SELECT username, SUM(expense_amount) as total_expenses FROM lunch_expenses WHERE YEAR(expense_date) = :year GROUP BY username ORDER BY total_expenses DESC");
    $stmt->bindParam(':year', $year);
    $stmt->execute();
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $leaderboard;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Lunch Expense Leaderboard</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <h1>Lunch Expense Leaderboard</h1>
    
    <table>
        <tr>
            <th>Username</th>
            <th>Total Expenses</th>
        </tr>
        <?php
        $leaderboard = getLeaderboard();
        foreach ($leaderboard as $row) {
            echo "<tr><td>" . $row['username'] . "</td><td>$" . $row['total_expenses'] . "</td></tr>";
        }
        ?>
    </table>

    <h2>Additional Features:</h2>
    <ul>
        <li>Add the ability to filter the leaderboard by a specific time frame.</li>
        <li>Implement user authentication and registration for tracking individual expenses.</li>
    </ul>
</body>
</html>
