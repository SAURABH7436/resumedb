<?php
require_once "pdo.php";

function flashmessage(){if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}}
function validatePos() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
        }
        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
        }
    }
    return true;
}
function validateEdu() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['school'.$i]) ) continue;
        $year = $_POST['edu_year'.$i];
        $school = $_POST['school'.$i];
        if ( strlen($year) == 0 || strlen($school) == 0 ) {
            return "All fields are required";
        }
        if ( ! is_numeric($year) ) {
            return "Year must be numeric";
        }
    }
    return true;
}
function validateprofile(){
  if(strlen($_POST['first_name'])<=0 || strlen($_POST['email'])<=0 || strlen($_POST['last_name'])<=0 || strlen($_POST['headline'])<=0 || strlen($_POST['summary'])<=0){
   return "All fields are required";
 }
  if (strpos($_POST['email'],'@')<= -1){
      return "Email address must contain @";
}
return true;
}
function insertposition($pdo,$profile_id){
  $rank = 1;
for($i=1; $i<=9; $i++) {
   if ( ! isset($_POST['year'.$i]) ) continue;
   if ( ! isset($_POST['desc'.$i]) ) continue;
   $year = $_POST['year'.$i];
   $desc = $_POST['desc'.$i];

   $stmt = $pdo->prepare('INSERT INTO Position
            (profile_id, rank, year, description)
            VALUES ( :pid, :rank, :year, :desc)');
   $stmt->execute(array(
       ':pid' => $profile_id,
       ':rank' => $rank,
       ':year' => $year,
       ':desc' => $desc)
   );
   $rank++;
}
}
function inserteducation($pdo,$profile_id){
  $rank = 1;
for($i=1; $i<=9; $i++) {
   if ( ! isset($_POST['edu_year'.$i]) ) continue;
   if ( ! isset($_POST['school'.$i]) ) continue;
   $year = $_POST['edu_year'.$i];
   $school = $_POST['school'.$i];
   $institution_id=false;
   $stmt = $pdo->prepare('SELECT institution_id FROM Institution
          where name=:name ');
   $stmt->execute(array(
  ':name'=>$school
));
   $row=$stmt->fetch(PDO::FETCH_ASSOC);
   if($row!==false)$institution_id=$row['institution_id'];
   if($institution_id ===false){
     $stmt=$pdo->prepare('INSERT INTO Institution (name) VALUES(:name)');
     $stmt->execute(array(':name'=>$school ));
     $institution_id=$pdo->lastInsertId();
   }
   $stmt=$pdo->prepare('INSERT INTO Education (profile_id,rank,year,institution_id) VALUES(:pid,:rank,:year,:institution_id)');
   $stmt->execute(array(':pid'=>$profile_id,
    ':rank'=>$rank,
     ':year'=>$year,
     ':institution_id'=>$institution_id ));
 $rank++;
}}
function loadpos($pdo,$profile_id){
  $stmt = $pdo->prepare('SELECT * FROM Position
         where profile_id=:pid ORDER BY rank');
 $stmt->execute(array(':pid'=>$profile_id
      ));
  $positions=$stmt->fetchAll(PDO::FETCH_ASSOC);
  return $positions;
}
function loadedu($pdo,$profile_id){
  $stmt = $pdo->prepare('SELECT name,year FROM Education join Institution on Education.institution_id=Institution.institution_id
         where profile_id=:pid ORDER BY rank');
 $stmt->execute(array(':pid'=>$profile_id
      ));
  $educations=$stmt->fetchAll(PDO::FETCH_ASSOC);
  return $educations;
}
