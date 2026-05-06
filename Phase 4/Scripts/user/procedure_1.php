<?php
$mysqli = new mysqli("localhost", "root", "Alperen@414", "gym_db");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$resultMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mid = $_POST['mid'];
    $cid = $_POST['cid'];
    
    $stmt = $mysqli->prepare("CALL EnrollMember(?, ?)");
    $stmt->bind_param("ii", $mid, $cid);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result && $row = $result->fetch_assoc()) {
            $resultMessage = $row['ProcedureResult'];
        }
    } else {
        $resultMessage = "Error: " . $stmt->error;
    }
    $stmt->close();
    
    while ($mysqli->more_results() && $mysqli->next_result()) {
        $extraResult = $mysqli->use_result();
        if ($extraResult instanceof mysqli_result) {
            $extraResult->free();
        }
    }
}

$members = $mysqli->query("SELECT mid, mname FROM Member ORDER BY mname ASC");
$classes = $mysqli->query("SELECT cid, cname FROM Class ORDER BY cname ASC");

$enrolledList = $mysqli->query("
    SELECT M.mname, C.cname, E.enrollment_date 
    FROM Enrolled E
    JOIN Member M ON E.mid = M.mid
    JOIN Class C ON E.cid = C.cid
    ORDER BY E.enrollment_date DESC
");

$mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
<title>Stored Procedure 1</title>
</head>
<body>
    <h1>Enroll Member in Class (Procedure)</h1>
    <?php if ($resultMessage != "") echo "<h3>" . $resultMessage . "</h3>"; ?>

    <fieldset style="width: 50%;">
        <legend>Execute Procedure</legend>
        <form method="post" action="procedure_1.php">
            <table>
                <tr>
                    <td><label>Select Member:</label></td>
                    <td>
                        <select name="mid" required>
                            <option value="">--Select Member--</option>
                            <?php if ($members) { while ($row = $members->fetch_assoc()) { ?>
                                <option value="<?php echo $row['mid']; ?>"><?php echo htmlspecialchars($row['mname']); ?></option>
                            <?php } } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label>Select Class:</label></td>
                    <td>
                        <select name="cid" required>
                            <option value="">--Select Class--</option>
                            <?php if ($classes) { while ($row = $classes->fetch_assoc()) { ?>
                                <option value="<?php echo $row['cid']; ?>"><?php echo htmlspecialchars($row['cname']); ?></option>
                            <?php } } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right"><button type="submit">Execute EnrollMember Procedure</button></td>
                </tr>
            </table>
        </form>
    </fieldset>

    <hr>
    <h3>Current Enrollments</h3>
    <table border="1" cellpadding="8" width="100%">
        <tr align="left">
            <th>Member Name</th>
            <th>Class Name</th>
            <th>Enrollment Date</th>
        </tr>
        <?php if ($enrolledList) { while ($row = $enrolledList->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['mname']); ?></td>
            <td><?php echo htmlspecialchars($row['cname']); ?></td>
            <td><?php echo htmlspecialchars($row['enrollment_date']); ?></td>
        </tr>
        <?php } } ?>
    </table>

    <br>
    <a href="index.php"><button>Back to Home</button></a>
</body>
</html>