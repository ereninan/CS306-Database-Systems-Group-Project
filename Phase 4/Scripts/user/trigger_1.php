<?php
$mysqli = new mysqli("localhost", "root", "Alperen@414", "gym_db");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$resultMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_branch'])) {
        $stmt = $mysqli->prepare("INSERT INTO Branch (bid, bname, bcity, bphone) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $_POST['bid'], $_POST['bname'], $_POST['bcity'], $_POST['bphone']);
        if ($stmt->execute()) {
            $resultMessage = "Branch added successfully! (Trigger fired)";
        } else {
            $resultMessage = "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['add_employee'])) {
        $stmt = $mysqli->prepare("INSERT INTO Employee (ssn, ename, ephone, job_title, salary, bid) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssdi", $_POST['ssn'], $_POST['ename'], $_POST['ephone'], $_POST['job_title'], $_POST['salary'], $_POST['bid']);
        if ($stmt->execute()) {
            $resultMessage = "Employee added successfully! (Trigger fired)";
        } else {
            $resultMessage = "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['add_member'])) {
        $stmt = $mysqli->prepare("INSERT INTO Member (mid, mname, mage, maddress) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $_POST['mid'], $_POST['mname'], $_POST['mage'], $_POST['maddress']);
        if ($stmt->execute()) {
            $resultMessage = "Member added successfully! (Trigger fired)";
        } else {
            $resultMessage = "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['delete_member'])) {
        $stmt = $mysqli->prepare("DELETE FROM Member WHERE mid = ?");
        $stmt->bind_param("i", $_POST['mid']);
        if ($stmt->execute()) {
            $resultMessage = "Member deleted successfully! (Trigger fired)";
        } else {
            $resultMessage = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

$branches = $mysqli->query("SELECT bid, bname FROM Branch ORDER BY bname ASC");
$members = $mysqli->query("SELECT mid, mname FROM Member ORDER BY mname ASC");
$logs = $mysqli->query("SELECT message, created_at FROM audit_log ORDER BY created_at DESC");

$mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
<title>Trigger 1</title>
</head>
<body>
    <h1>Gym Management Triggers</h1>
    <?php if ($resultMessage != "") echo "<h3>" . $resultMessage . "</h3>"; ?>

    <table width="100%">
    <tr>
        <td valign="top" width="33%">
            <fieldset>
                <legend>Add New Branch</legend>
                <form method="post" action="trigger_1.php">
                    <table>
                        <tr><td><label>Branch ID:</label></td><td><input type="number" name="bid" required></td></tr>
                        <tr><td><label>Branch Name:</label></td><td><input type="text" name="bname" required></td></tr>
                        <tr><td><label>City:</label></td><td><input type="text" name="bcity" required></td></tr>
                        <tr><td><label>Phone:</label></td><td><input type="text" name="bphone" required></td></tr>
                        <tr><td colspan="2"><button type="submit" name="add_branch">Add Branch</button></td></tr>
                    </table>
                </form>
            </fieldset>
        </td>
        <td valign="top" width="33%">
            <fieldset>
                <legend>Add New Employee</legend>
                <form method="post" action="trigger_1.php">
                    <table>
                        <tr><td><label>SSN:</label></td><td><input type="text" name="ssn" maxlength="11" required></td></tr>
                        <tr><td><label>Name:</label></td><td><input type="text" name="ename" required></td></tr>
                        <tr><td><label>Phone:</label></td><td><input type="text" name="ephone" required></td></tr>
                        <tr><td><label>Job Title:</label></td><td><input type="text" name="job_title" required></td></tr>
                        <tr><td><label>Salary:</label></td><td><input type="number" step="0.01" name="salary" required></td></tr>
                        <tr><td><label>Branch:</label></td><td>
                            <select name="bid" required>
                                <option value="">--Select Branch--</option>
                                <?php 
                                if ($branches && $branches->num_rows > 0) {
                                    $branches->data_seek(0);
                                    while ($row = $branches->fetch_assoc()) {
                                        echo '<option value="'.$row['bid'].'">'.htmlspecialchars($row['bname']).'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td></tr>
                        <tr><td colspan="2"><button type="submit" name="add_employee">Add Employee</button></td></tr>
                    </table>
                </form>
            </fieldset>
        </td>
        <td valign="top" width="33%">
            <fieldset>
                <legend>Add New Member</legend>
                <form method="post" action="trigger_1.php">
                    <table>
                        <tr><td><label>Member ID:</label></td><td><input type="number" name="mid" required></td></tr>
                        <tr><td><label>Name:</label></td><td><input type="text" name="mname" required></td></tr>
                        <tr><td><label>Age:</label></td><td><input type="number" name="mage" required></td></tr>
                        <tr><td><label>Address:</label></td><td><input type="text" name="maddress" required></td></tr>
                        <tr><td colspan="2"><button type="submit" name="add_member">Add Member</button></td></tr>
                    </table>
                </form>
            </fieldset>
            <br>
            <fieldset>
                <legend>Delete Member</legend>
                <form method="post" action="trigger_1.php">
                    <table>
                        <tr><td><label>Select Member:</label></td><td>
                            <select name="mid" required>
                                <option value="">--Select Member--</option>
                                <?php 
                                if ($members && $members->num_rows > 0) {
                                    $members->data_seek(0);
                                    while ($row = $members->fetch_assoc()) {
                                        echo '<option value="'.$row['mid'].'">'.htmlspecialchars($row['mname']).'</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td></tr>
                        <tr><td colspan="2"><button type="submit" name="delete_member">Delete Member</button></td></tr>
                    </table>
                </form>
            </fieldset>
        </td>
    </tr>
    </table>

    <hr>
    <h2>Audit Log (Trigger Results)</h2>
    <table border="1" cellpadding="8" width="100%">
        <tr align="left">
            <th>Message</th>
            <th>Date/Time</th>
        </tr>
        <?php if ($logs) { while ($log = $logs->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($log['message']); ?></td>
            <td><?php echo htmlspecialchars($log['created_at']); ?></td>
        </tr>
        <?php endwhile; } ?>
    </table>
    
    <br>
    <a href="index.php"><button>Back to Home</button></a>
</body>
</html>