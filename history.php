<?php
include("validate_token.php");
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/include/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM history WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: history.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, input_text, output_text FROM history WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>History</title>

<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    margin:0;
    padding:0;
    font-family:'Poppins',sans-serif;
    background:#f3f5f9;
    color:#333;
}
.container{
    margin:70px auto;
    width:95%;
    max-width:1100px;
    background:#ffffff;
    padding:28px;
    border-radius:12px;
    box-shadow:0 0 12px rgba(0,0,0,0.15);
    position:relative;
    box-sizing:border-box;
    overflow-x:auto;
}
.back-btn{
    position:absolute;
    top:15px;
    right:15px;
    background:#1e3c57;
    color:#fff;
    border:none;
    font-size:16px;
    padding:8px 16px;
    border-radius:8px;
    cursor:pointer;
    display:flex;
    align-items:center;
    gap:6px;
}
.back-btn i{font-size:17px;}
.back-btn:hover{background:#264a6e;}

h2{
    text-align:center;
    color:#1e3c57;
    margin-top:40px;
    margin-bottom:25px;
}

.table-wrap{
    width:100%;
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    table-layout:fixed;
}

table th,table td{
    padding:12px;
    border-bottom:1px solid rgba(0,0,0,0.15);
    text-align:left;
    font-size:14px;
}

table th{
    background:#1e3c57;
    color:white;
}

table td{
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
    max-width:350px;
}

.actions{
    width:170px;
    text-align:center;
}

.actions a{
    display:inline-block;
    padding:7px 12px;
    border-radius:6px;
    text-decoration:none;
    font-size:0.9rem;
    font-weight:600;
    margin-right:5px;
}

.delete-btn{background:#dc3545;color:white;}
.update-btn{background:#28a745;color:white;}
.delete-btn:hover{opacity:0.85;}
.update-btn:hover{opacity:0.85;}

@media(max-width:768px){
    .container{
        width:100%;
        margin-top:50px;
        padding:18px;
    }
    table td,table th{
        font-size:12px;
        padding:8px;
    }
}
</style>
</head>

<body>

<div class="container">

<button type="button" onclick="window.location.href='index.php'" class="back-btn">
    <i class="fa fa-arrow-left"></i> Back
</button>

<h2>Your History</h2>

<div class="table-wrap">
<table>
<tr>
    <th>Input</th>
    <th>Output</th>
    <th class="actions">Actions</th>
</tr>

<?php while($row=$result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['input_text']); ?></td>
    <td><?= htmlspecialchars($row['output_text']); ?></td>
    <td class="actions">
        <a href="history.php?delete=<?= $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this record?');">Delete</a>
        <a href="dashboard.php?input=<?= urlencode($row['input_text']); ?>" class="update-btn">Update</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
