<?php

if(! isset($_GET['term']))die('Missing required parameter');
if(! isset($_COOKIE[session_name()]))die("Must be logged in");
session_start();
if(! isset($_SESSION['user_id']))die("ACCESS DENIED");
require_once "pdo.php";
header("Content-type:application/json; charset=utf-8");
$term=$_GET['term'];
error_log("Looking up typeahead term=".$term);
$stmt=$pdo->prepare('SELECT name FROM Institution where name LIKE :prefix');
$stmt->execute(array(
  ':prefix'=>$term."%"
));
$reteval=array();
while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
  $reteval[]=$row['name'];
}
echo(json_encode($reteval,JSON_PRETTY_PRINT));
