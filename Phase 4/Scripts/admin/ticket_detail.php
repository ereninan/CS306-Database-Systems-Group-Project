<?php
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
$ticketId = $_GET['id'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ticketId = $_POST['id'];
    $bulk = new MongoDB\Driver\BulkWrite;
    
    if (isset($_POST['resolve'])) {
        $bulk->update(
            ['_id' => new MongoDB\BSON\ObjectId($ticketId)],
            ['$set' => ['status' => false]]
        );
    } elseif (isset($_POST['add_comment'])) {
        $commentText = $_POST['comment'];
        $bulk->update(
            ['_id' => new MongoDB\BSON\ObjectId($ticketId)],
            ['$push' => ['comments' => [
                'username' => 'admin',
                'comment' => $commentText,
                'created_at' => date('Y-m-d H:i:s')
            ]]]
        );
    }
    if ($bulk->count() > 0) {
        $manager->executeBulkWrite('gym_db.tickets', $bulk);
    }
}

$filter = ['_id' => new MongoDB\BSON\ObjectId($ticketId)];
$query = new MongoDB\Driver\Query($filter);
$cursor = $manager->executeQuery('gym_db.tickets', $query);
$ticket = current($cursor->toArray());
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin - Ticket Detail</title>
</head>
<body>
    <h1>Admin - Ticket Management</h1>

    <?php if ($ticket): ?>
        <fieldset>
            <legend>Ticket Details</legend>
            <table width="100%">
                <tr><td width="150"><strong>Username:</strong></td><td><?php echo htmlspecialchars($ticket->username); ?></td></tr>
                <tr><td><strong>Message:</strong></td><td><?php echo htmlspecialchars($ticket->message); ?></td></tr>
                <tr><td><strong>Created At:</strong></td><td><?php echo htmlspecialchars($ticket->created_at); ?></td></tr>
                <tr><td><strong>Status:</strong></td><td><b><?php echo $ticket->status ? '<span style="color:green;">Active</span>' : '<span style="color:red;">Resolved</span>'; ?></b></td></tr>
            </table>
        </fieldset>

        <br>
        <fieldset>
            <legend>Comments Thread</legend>
            <table border="1" cellpadding="8" width="100%">
                <?php if (count($ticket->comments) > 0): ?>
                    <tr align="left"><th>User</th><th>Comment</th><th>Date</th></tr>
                    <?php foreach ($ticket->comments as $c): ?>
                        <tr <?php if($c->username == 'admin') echo 'bgcolor="#f0f0f0"'; ?>>
                            <td><b><?php echo htmlspecialchars($c->username); ?></b></td>
                            <td><?php echo htmlspecialchars($c->comment); ?></td>
                            <td><i><?php echo htmlspecialchars($c->created_at); ?></i></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td>No comments yet.</td></tr>
                <?php endif; ?>
            </table>
        </fieldset>

        <?php if ($ticket->status): ?>
        <br>
        <fieldset style="background-color: #fafafa;">
            <legend><b>Admin Actions</b></legend>
            <table width="100%">
                <tr>
                    <td valign="top" width="50%">
                        <form method="post" action="ticket_detail.php?id=<?php echo $ticketId; ?>">
                            <input type="hidden" name="id" value="<?php echo $ticketId; ?>">
                            <label><b>Add Response:</b></label><br>
                            <textarea name="comment" rows="4" cols="40" required></textarea><br><br>
                            <button type="submit" name="add_comment">Add Comment as Admin</button>
                        </form>
                    </td>
                    <td valign="top" width="50%">
                        <form method="post" action="ticket_detail.php?id=<?php echo $ticketId; ?>">
                            <input type="hidden" name="id" value="<?php echo $ticketId; ?>">
                            <label><b>Close Ticket:</b></label><br><br>
                            <button type="submit" name="resolve" style="color: red;">Deactivate/Resolve Ticket</button>
                        </form>
                    </td>
                </tr>
            </table>
        </fieldset>
        <?php endif; ?>

    <?php else: ?>
        <p>Ticket not found.</p>
    <?php endif; ?>
    
    <br><br>
    <a href="index.php"><button>Back to Active Tickets</button></a>
</body>
</html>
