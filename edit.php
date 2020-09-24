  <?php
require_once "pdo.php";
require_once "util.php";
session_start();
if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}
if ( isset($_POST['cancel'])){
  header("Location: index.php");
  return;
}
if ( ! isset($_REQUEST['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}
$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_REQUEST['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php');
    return;
}
if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email'])  && isset($_POST['headline']) && isset($_POST['profile_id']) && isset($_POST['summary']) ) {

    // Data validation
      $msg=validateprofile();
      if(is_string($msg)){
        $_SESSION['error']=$msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
    }
    else{  $msg=validateEdu();
      if(is_string($msg)){
        $_SESSION['error']=$msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
      }
  else {
     $msg=validatePos();
      if(is_string($msg)){
        $_SESSION['error']=$msg;
        header("Location: edit.php?profile_id=".$_REQUEST['profile_id']);
        return;
      }
     else{  $sql = "UPDATE profile SET first_name = :first_name,
            last_name = :last_name, email = :email, headline = :headline, summary= :summary
            WHERE profile_id = :profile_id AND user_id=:uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':first_name' => $_POST['first_name'],
        ':last_name' => $_POST['last_name'],
        ':email' => $_POST['email'],
        ':headline' => $_POST['headline'],
        ':summary' => $_POST['summary'],
        ':profile_id' => $_REQUEST['profile_id'],
        ':uid'=> $_SESSION['user_id']
        ));
        // Clear out the old position entries
       $stmt = $pdo->prepare('DELETE FROM Position
       WHERE profile_id=:pid');
       $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

// Insert the position entries
  insertposition($pdo,$_REQUEST['profile_id']);
  $stmt = $pdo->prepare('DELETE FROM Education
  WHERE profile_id=:pid');
  $stmt->execute(array( ':pid' => $_REQUEST['profile_id']));
  inserteducation($pdo,$_REQUEST['profile_id']);

    $_SESSION['success'] = 'Record edited';
    header( 'Location: index.php' ) ;
    return;
  }
}}}

// Guardian: first_name sure that profile_id is present
$positions=loadpos($pdo,$_REQUEST['profile_id']);
$schools=loadedu($pdo,$_REQUEST['profile_id']);

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
<div class="container">
<?php
echo "<h1>Editing Profile for ". $_SESSION['name']."</h1>";
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
$fnm = htmlentities($row['first_name']);
$lnm = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hd = htmlentities($row['headline']);
$profile_id = $row['profile_id'];
$sm = htmlentities($row['summary']);
?>
<form method="post">
<p>first_name:
<input type="text" name="first_name" value="<?= $fnm ?>"></p>
<p>last_name:
<input type="text" name="last_name" value="<?= $lnm ?>"></p>
<p>email:
<input type="text" name="email" value="<?= $em ?>"></p>
<p>headline:<br/>
<input type="text" name="headline" value="<?= $hd ?>"></p>
<p>summary:<br/>
<textarea name="summary" row="10" cols="70"><?= $sm?></textarea></p>
<input type="hidden" name="profile_id" value="<?= $profile_id ?>">
<?php
$countedu=0;
$countpos=0;
echo '<p>Education:<input type="submit" id="addedu" value="+">'."\n";
echo '<div id="education_fld">'."\n";
if(count($schools)>0){
  foreach ($schools as $school) {
    $countedu++;
    echo'<div id="education'.$countedu.'">';
echo ('<p>Year: <input type="text" name="edu_year'.$countedu.'" value="'.$school['year'].'"/>
<input type="button" value="-" onclick="$(\'#education'.$countedu.'\').remove();return false;"></p>
<p>school: <input type="text" size="80" name="school'.$countedu.'" class="school" value="'.htmlentities($school['name']).'"/>
</div>');
}}
echo "</div></p>\n";

echo '<p>position:<input type="submit" id="addpos" value="+">'."\n";
echo '<div id="position_fld">'."\n";
if(count($positions)>=0){
  foreach ($positions as $position) {
    $countpos++;
    echo'<div id="position'.$countpos.'">';
    echo ('<p>Year: <input type="text" name="year'.$countpos.'" value="'.$position['year'].'"/>
      <input type="button" value="-" onclick="$(\'#position'.$countpos.'\').remove();return false;"></p>
      <textarea name="desc'.$countpos.'" rows="8" cols="80">'.htmlentities($position['description'])."</textarea></div>\n");
  }
}
echo "</div>\n</p>\n";
?>
<p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="cancel"></p>
</form>
<script>
countpos=<?= $countpos ?>;
countedu=<?= $countedu ?>;
$(document).ready(function(){
  window.console&& console.log("document ready called");
$('#addpos').click(function(event){
  event.preventDefault();
  if(countpos>=9){
    alert("Maximum of nine position entries exceeded");
    return;
  }
  countpos++;
  window.console && console.log("Adding position"+countpos);
  $('#position_fld').append(
    '<div id="position'+countpos+'">\
    <p>Year: <input type="text" name="year'+countpos+'" value=" "/>\
    <input type="button" value="-" onclick="$(\'#position'+countpos+'\').remove();return false;"></p>\
    <textarea name="desc'+countpos+'" rows="8" cols="80"></textarea>\
    </div>');
});
$('#addedu').click(function(event){
  event.preventDefault();
  if(countedu>=9){
    alert("Maximum of nine Education entries exceeded");
    return;
  }
  countedu++;
  window.console && console.log("Adding Education"+countedu);
var source=$("#edu-template").html();
$('#education_fld').append(source.replace(/@count@/g,countedu));
$('.school').autocomplete({ source: "school.php" });
});
$('.school').autocomplete({ source: "school.php" });
});
</script>
<script id='edu-template' type="text">
<div id="education@count@">
<p>Year: <input type="text" name="edu_year@count@" value=""/>
<input type="button" value="-" onclick="$('#education@count@').remove();return false;"></p>
<p>school: <input type="text" size='80' name="school@count@"class='school' value=""/>
</p>
</div>
</script>
</div>
</body>
</html>
