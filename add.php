<?php
session_start();
require_once "pdo.php";
require_once "util.php";
// Demand a GET parameter
if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
}
if ( isset($_POST['cancel'])){
  header("Location: index.php");
  return;
}
// If the user requested logout go back to index.ph
if ( isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['summary']) && isset($_POST['email'])&&isset($_POST['headline'])) {
       if(strlen($_POST['first_name'])<=0 || strlen($_POST['last_name'])<=0 || strlen($_POST['headline'])<=0 || strlen($_POST['summary'])<=0|| strlen($_POST['email'])<=0){
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    }
    else if (strpos($_POST['email'],'@')<= -1){
         $_SESSION['error']="Email address must contain @";
         header("Location: add.php");
         return;
       }
       else{$msg=validatePos();
         if(is_string($msg)){
           $_SESSION['error']=$msg;
           header("Location: add.php");
           return;
         }
         $msg=validateEdu();
         if(is_string($msg)){
           $_SESSION['error']=$msg;
           header("Location: add.php");
           return;
         }
       else{
       $stmt = $pdo->prepare('INSERT INTO profile
            (user_id,first_name,email, headline, last_name, summary) VALUES ( :u,:fnm,:e, :hd, :lnm, :sm)');
        $stmt->execute(array(
            ':u' => $_SESSION['user_id'],
            ':e' => $_POST['email'],
            ':fnm' => $_POST['first_name'],
            ':hd' => $_POST['headline'],
            ':lnm' => $_POST['last_name'],
            ':sm' => $_POST['summary']));
            $profile_id = $pdo->lastInsertId();
        insertposition($pdo,$profile_id);
        inserteducation($pdo,$profile_id);
        $_SESSION['success'] = "Record added";
        header("Location: index.php");
        return;}}
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Saurabh Gupta's Resume Registry</title>
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
 echo "<h1>Adding Profile for ". $_SESSION['name']."</h1>";
 if(isset( $_SESSION['error'])){echo '<p style="color: red;">'. htmlentities($_SESSION['error'])."</p>";}
 unset($_SESSION['error']);
?>
<form method="post">
<p>first_name:
<input type="text" name="first_name" size="60"></p>
<p>last_name:
<input type="text" name="last_name" size="60"></p>
<p>Email:
<input type="text" name="email" size="30"></p>
<p>headline:<br/>
<input type="text" name="headline" size="70"></p>
<p>summary:<br/>
<textarea name="summary" row="10" cols="70"></textarea></p>
<p>education:
<input type="submit" id="addedu" value="+">
<div id="education_fld"></div></p>
<p>position:
<input type="submit" id="addpos" value="+">
<div id="position_fld"></div></p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="cancel">
</form>
<script>
countpos=0;
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
countedu=0;
$('#addedu').click(function(event){
  event.preventDefault();
  if(countedu>=9){
    alert("Maximum of nine Education entries exceeded");
    return;
  }
  countedu++;
  window.console && console.log("Adding Education"+countedu);

var source=$("#edu-template").html();
$('#education_fld').append(source.replace(/@COUNT@/g,countedu));
$('.school').autocomplete({ source: "school.php" });
});
$('.school').autocomplete({ source: "school.php" });
});
</script>
<script id='edu-template' type="text">
<div id="education@COUNT@">
<p>Year: <input type="text" name="edu_year@COUNT@" value=""/>
<input type="button" value="-" onclick="$('#education@COUNT@').remove();return false;"></p>
<p>school: <input type="text" size='80' name="school@COUNT@"class='school' value=""/>
</p>
</div>
</script>
</div>
</body>
</html>
