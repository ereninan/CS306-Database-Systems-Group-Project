<?php
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$ticketId = $_GET['id'] ?? '';

$filter = ['_id' => new MongoDB\BSON\ObjectId($ticketId)];
$query = new MongoDB\Driver\Query($filter);
$cursor = $manager->executeQuery('gym_db.tickets', $query);
$ticket = current($cursor->toArray());
?>
<!DOCTYPE html>
<html>
<head>
<title>Ticket Detail</title>
</head>
<body>
    <h1>Ticket Detail</h1>

    <?php if ($ticket): ?>
        <fieldset>
            <legend>Ticket Info</legend>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($ticket->username); ?></p>
            <p><strong>Message:</strong> <?php echo htmlspecialchars($ticket->message); ?></p>
            <p><strong>Created At:</strong> <?php echo htmlspecialchars($ticket->created_at); ?></p>
            <p><strong>Status:</strong> <b><?php echo $ticket->status ? 'Active' : 'Resolved'; ?></b></p>
        </fieldset>

        <br>
        <fieldset>
            <legend>Comments (<?php echo count($ticket->comments); ?>)</legend>
            <table border="1" cellpadding="8" width="100%">
                <?php if (count($ticket->comments) > 0): ?>
                    <tr align="left"><th>User</th><th>Comment</th><th>Date</th></tr>
                    <?php foreach ($ticket->comments as $c): ?>
                        <tr>
                            <td><b><?php echo htmlspecialchars($c->username); ?></b></td>
                            <td><?php echo htmlspecialchars($c->comment); ?></td>
                            <td><i><?php echo htmlspecialchars($c->created_at); ?></i></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td>No comments yet. Only admins can reply to tickets.</td></tr>
                <?php endif; ?>
            </table>
        </fieldset>

    <?php else: ?>
        <p>Ticket not found.</p>
    <?php endif; ?>

    <br><br>
    <a href="tickets.php"><button>Back to Tickets</button></a>
</body>
</html>
