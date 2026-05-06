<?php
$resultMessage = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    
    $bulk = new MongoDB\Driver\BulkWrite;
    $document = [
        'username' => $_POST['username'],
        'message' => $_POST['message'],
        'created_at' => date('Y-m-d H:i:s'),
        'status' => true,
        'comments' => []
    ];
    $bulk->insert($document);
    
    try {
        $manager->executeBulkWrite('gym_db.tickets', $bulk);
        $resultMessage = "Ticket created successfully.";
    } catch (MongoDB\Driver\Exception\Exception $e) {
        $resultMessage = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Create Ticket</title>
</head>
<body>
    <h1>Create New Support Ticket</h1>
    <?php if ($resultMessage != "") echo "<h3>" . $resultMessage . "</h3>"; ?>
    
    <fieldset style="width: 50%;">
        <legend>Ticket Details</legend>
        <form method="post" action="create_ticket.php">
            <table>
                <tr>
                    <td><label>Username:</label></td>
                    <td><input type="text" name="username" required></td>
                </tr>
                <tr>
                    <td valign="top"><label>Message:</label></td>
                    <td><textarea name="message" rows="5" cols="40" required></textarea></td>
                </tr>
                <tr>
                    <td colspan="2" align="right"><button type="submit">Submit Ticket</button></td>
                </tr>
            </table>
        </form>
    </fieldset>

    <br>
    <a href="tickets.php"><button>Back to Tickets</button></a>
    <a href="index.php"><button>Back to Home</button></a>
</body>
</html>
