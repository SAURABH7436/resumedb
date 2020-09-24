<?php
require_once "pdo.php";
require_once "util.php";
?>
<!DOCTYPE html>
<html>
<head>
<title>Saurabh Gupta's EditprofileDB</title>
<?php require_once "bootstrap.php"; ?>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

 <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">

 <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>

 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
</head>
<body>
<div class="container" style="font-size: 150%;">
<h1>Profile information</h1>
<?php
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo "First Name:".htmlentities($row['first_name'])."<br/>";
echo "Last Name:".htmlentities($row['last_name'])."<br/>";
echo "Email:".htmlentities($row['email'])."<br/>";
echo "Headline:<br>".htmlentities($row['headline'])."<br/>";
echo "Summary:<br>".htmlentities($row['summary'])."<br/>";
$stmt = $pdo->prepare('SELECT name,year FROM Education join Institution on Education.institution_id=Institution.institution_id
         where profile_id=:pid ORDER BY rank');
$stmt->execute(array(':pid'=>$_REQUEST['profile_id']
      ));
echo "Education :<ul>";
while( $education=$stmt->fetch(PDO::FETCH_ASSOC)){
    echo"<li>".htmlentities($education['year']).": ".htmlentities($education['name'])."</li>";
};
echo"</ul>";
$stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
echo "Position<ul>";
$row = $stmt->fetch(PDO::FETCH_ASSOC);
do{
  echo"<li>".htmlentities($row['year']).": ".htmlentities($row['description'])."</li>";
}while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
echo"</ul>";
?>
<a href="index.php">Done</a>
</div>
</body>
</html>
