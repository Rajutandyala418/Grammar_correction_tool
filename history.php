<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Connect DB
$mysqli = new mysqli("localhost", "root", "", "grammar_tool");
if ($mysqli->connect_error) {
    die("DB Connection Failed: " . $mysqli->connect_error);
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $mysqli->prepare("DELETE FROM history WHERE id = ? AND user_id = ?");
    $stmt->bind_param("is", $id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: history.php");
    exit();
}

// Fetch history
$stmt = $mysqli->prepare("SELECT id, input_text, output_text FROM history WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>History</title>
<style>
    body {
        font-family: Arial, sans-serif;
       background: linear-gradient(135deg,#ff0000,#ff7f00,#ffff00,#7fff00,#00ff00,#00ff7f,#00ffff,#007fff,#0000ff,#7f00ff,#ff00ff,#ff007f,#ff6666,#ff9966,#ffcc66,#ccff66,#66ff66,#66ffcc,#66ccff,#6699ff,#6666ff,#9966ff,#cc66ff,#ff66ff,#ff66cc);
    background-size: 400% 400%;
    animation: gradientAnimation 20s ease infinite;
}
@keyframes gradientAnimation {0% {background-position:0% 50%;} 50% {background-position:100% 50%;} 100% {background-position:0% 50%;}}
        margin: 1px;
        padding: 1px;
    }
.top-nav { 
    position: absolute; 
    top: 400px;   /* move down */
    left: 50px;  /* move right */
    display: flex; 
    gap: 15px; 
    align-items: center; 
}
    .container {
        margin: 80px auto;
        width: 90%;
        max-width: 1000px;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 2px 8px rgba(0,0,0,0.1);
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    table th, table td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: left;
    }
    table th {
        background: #007BFF;
        color: white;
    }
    .actions a {
        padding: 6px 12px;
        border-radius: 5px;
        text-decoration: none;
        margin-right: 6px;
    }
    .delete-btn {
        background: #dc3545;
        color: white;
    }
    .update-btn {
        background: #28a745;
        color: white;
    }
</style>
</head>
<body>

<!-- Dashboard Button -->
<div class="top-nav">
    <a href="dashboard.php" style="color:white; font-weight:bold; text-decoration:none; background:rgba(0,0,0,0.5); padding:8px 15px; border-radius:6px;">Dashboard</a>
</div>

<div class="container">
    <h2>Your History</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Input</th>
            <th>Output</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars(substr($row['input_text'], 0, 100)); ?></td>
            <td><?php echo htmlspecialchars(substr($row['output_text'], 0, 100)); ?></td>
            <td class="actions">
                <a href="history.php?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this record?')">Delete</a>
                <a href="dashboard.php?input=<?php echo urlencode($row['input_text']); ?>" class="update-btn">Update</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
<?php
$stmt->close();
$mysqli->close();
?>
