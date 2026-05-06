<?php
$manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");

$filter = ['status' => true];
$options = [];
$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('gym_db.tickets', $query);
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin - Active Tickets</title>
</head>
<body>
    <h1>Admin Dashboard - Active Support Tickets</h1>
    
    <fieldset>
        <legend>Pending Tickets</legend>
        <table border="1" cellpadding="8" width="100%">
            <tr align="left">
                <th>User</th>
                <th>Message</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
            <?php 
            $hasTickets = false;
            foreach ($cursor as $ticket): 
                $hasTickets = true;
            ?>
                <tr>
                    <td><b><?php echo htmlspecialchars($ticket->username); ?></b></td>
                    <td><?php echo htmlspecialchars($ticket->message); ?></td>
                    <td><i><?php echo htmlspecialchars($ticket->created_at); ?></i></td>
                    <td>
                        <a href="ticket_detail.php?id=<?php echo (string)$ticket->_id; ?>">
                            <button>View / Manage</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <?php if (!$hasTickets): ?>
                <tr><td colspan="4" align="center">No active tickets at the moment!</td></tr>
            <?php endif; ?>
        </table>
    </fieldset>

</body>
</html>
