<?php
require_once "pdo.php";
session_start();
?>
<html>
<head>
  <?php require_once "bootstrap.php"; ?>
  <title>Saurabh Gupta  Resume Registry</title>
</head>
<body>
<div class="container">
<h1>Saurabh Gupta's Resume Registry</h1>
<?php
$stmt = $pdo->query("SELECT profile_id,first_name, last_name, headline FROM profile");
$rows = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $rows === false ) {
  echo "NO rows found";
}else{
  echo('<table border="1">'."\n");
  echo('<tbody>');
  echo('<tr><th>Name</th><th>headline</th><th>Action</th></tr>');
do  {
    echo "<tr><td>";
    echo('<a href="view.php?profile_id='.$rows['profile_id'].'">'.htmlentities($rows['first_name'])." ".htmlentities($rows['last_name']).'</a>');
    echo("</td><td>");
    echo(htmlentities($rows['headline']));
    echo("</td><td>");
    if(! isset($_SESSION['name'])){
    echo("read");
    }
    else{
    echo('<a href="edit.php?profile_id='.$rows['profile_id'].'">Edit</a> ');
    echo('<a href="delete.php?profile_id='.$rows['profile_id'].'">Delete</a>');
  }
    echo("</td></tr>\n");
} while ($rows = $stmt->fetch(PDO::FETCH_ASSOC));
  echo('</tbody>');
  echo ("</table>");
}
if ( ! isset($_SESSION['name']) ) {
  echo('<a href="login.php">Please log in </a>');
}
else{
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}
echo '<a href="add.php">Add New Entry</a><br/>';
echo '<a href="logout.php">logout</a>';
}
?>
</div>
</body>
</html>
