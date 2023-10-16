<?php
include("connect.php");
session_start();

// Function to get daily expenses for each user
function getDailyExpenses() {
    global $conn;

    $stmt = $conn->prepare("SELECT user_id, SUM(cena) as daily_expenses FROM nakupi WHERE DATE(nakup_datum) = CURDATE() GROUP BY user_id");
    $stmt->execute();
    $daily_expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $daily_expenses;
}

$daily_expenses = getDailyExpenses();

// Check if the user submitted their daily expense
if (isset($_POST['submit_expense'])) {
    $restavracija_id = $_POST['restavracija_id']; // Updated variable name
    $cena = $_POST['cena'];
    $user_id = $_SESSION['id'];
    $date = date("Y-m-d H:i:s");

    // Check if the user already submitted an expense for today
    $stmt = $conn->prepare("SELECT id FROM nakupi WHERE user_id = :user_id AND DATE(nakup_datum) = CURDATE()");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    if ($stmt->fetchColumn() === false) {
        // Insert the expense into the database
        $stmt = $conn->prepare("INSERT INTO nakupi (cena, malca_izbire_id, user_id, nakup_datum) VALUES (:cena, :restavracija_id, :user_id, :date)");
        $stmt->bindParam(':cena', $cena, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':restavracija_id', $restavracija_id);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
    }

    // Redirect to the same page to avoid resubmission on page refresh
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Get a list of users (restaurants)
$stmt = $conn->prepare("SELECT * FROM malca_izbire");
$stmt->execute();
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC); // Updated variable name
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daily Lunch Expense Tracker</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Daily Lunch Expense Tracker</h1>

    <?php 
    if (count($daily_expenses) == 0) {
        echo "<p>Nč še nismo zapravl dans</p>";
    } else {
        echo "<h2>Danes smo zapravili:</h2>";
        echo "<table>";
        echo "<tr>";
        echo "<th>User</th>";
        echo "<th>Plaču</th>";
        echo "</tr>";
        foreach ($daily_expenses as $row) {
            $sql = "SELECT ime FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$row['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $username = $result['ime'];
            echo "<tr><td>" . $username . "</td><td>" . $row['daily_expenses'] . "€</td></tr>";
        }
        echo "</table>";
    }
    $user_id = $_SESSION['id'];
    // Check if the user has already submitted an expense for today
    $stmt = $conn->prepare("SELECT id FROM nakupi WHERE user_id = :user_id AND DATE(nakup_datum) = CURDATE()");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    if ($stmt->fetchColumn() === false) {
        // Show the form only if the user hasn't submitted an expense for today

        echo "<h2>Vnesi kolk si zapravu dans:</h2>";
        echo "<form method='post'>";
        echo "<label for='restavracija_id'>Kje si jedu:</label>";
        echo "<select name='restavracija_id' id='restavracija_id'>";
        foreach ($restaurants as $restaurant) { // Updated variable name
            echo "<option value='" . $restaurant['id'] . "'>" . $restaurant['ime'] . "</option>";
        }
        echo "</select>";
        echo "<br>";
        echo "<br>";
        echo "<label for='cena'>Kolk si plaču:</label>";
        echo "<input type='number' name='cena' id='cena' required  step='0.01' min='0'> €";
        echo "<br>";
        echo "<br>";
        echo "<button type='submit' name='submit_expense'>Pošlji</button>";
        echo "</form>";
    }
    ?>
</body>
</html>
