<?php
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

$selectedUser = "";
$myActiveTickets = [];

if (isset($_GET['username']) && !empty($_GET['username'])) {
    $selectedUser = $_GET['username'];
    
    // Sadece kullanıcının kendi biletlerini getir
    $filter = [
        'status' => true,
        'username' => $selectedUser
    ];
    $options = [];
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('gym_db.tickets', $query);

    foreach ($cursor as $document) {
        $myActiveTickets[] = $document;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Support Tickets</title>
</head>
<body>
    <h1>Support Tickets System</h1>
    
    <fieldset>
        <legend>Ticket Actions</legend>
        <a href="create_ticket.php"><button>Create New Ticket</button></a>
        <hr>
        <form method="get" action="tickets.php">
            <label>Enter your Username to view your tickets:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($selectedUser); ?>" required>
            <button type="submit">View My Tickets</button>
        </form>
    </fieldset>

    <?php if ($selectedUser != ""): ?>
    <hr>
    <h2>Active Tickets for: <i><?php echo htmlspecialchars($selectedUser); ?></i></h2>
    <table border="1" cellpadding="8" width="100%">
        <tr align="left">
            <th>Message</th>
            <th>Date Created</th>
            <th>Action</th>
        </tr>
        <?php if (count($myActiveTickets) > 0): ?>
            <?php foreach ($myActiveTickets as $ticket): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ticket->message); ?></td>
                    <td><?php echo htmlspecialchars($ticket->created_at); ?></td>
                    <td><a href="ticket_detail.php?id=<?php echo (string)$ticket->_id; ?>"><button>View Details</button></a></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">You have no active tickets.</td></tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>

    <br><br>
    <a href="index.php"><button>Back to Home</button></a>
</body>
</html>
