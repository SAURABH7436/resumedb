<?php // Do not put any HTML above this line
require_once "pdo.php";
session_start();

$salt = 'XyZzy12*_';  // Pw is meow123
// If we have no POST data

// Check to see if we have some POST data, if we do process it
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
        $check = hash('md5', $salt.$_POST['pass']);
        $stmt = $pdo->prepare('SELECT user_id, name FROM users
        WHERE email = :em AND password = :pw');
        $stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( $row !== false ) {
        $_SESSION['name'] = $row['name'];
        $_SESSION['user_id'] = $row['user_id'];
    // Redirect the browser to index.php
       $_SESSION['success'] = "Login success.";
       error_log("Login success ".$_POST['email']);
       header("Location: index.php");
       return;}
       else {
            error_log("Login fail ".$_POST['email']." $check");
            $_SESSION['error'] = "Incorrect password";
            header("Location: login.php");
            return;
        }
    }

// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
  <?php require_once "bootstrap.php"; ?>
 <title>Saurabh Gupta's Login Page</title>
</head>
<body>
<div class="container">
<h1>Please Log In</h1>
<?php
if( isset($_SESSION['error'])){
  echo '<p style="color:red;">'.htmlentities($_SESSION['error'])."</p>\n";
  unset($_SESSION['error']);
}
 ?>
<form method="POST">
Email <input type="text" id="id_1722" name="email"><br/>
Password <input type="text" id="id_1723" name="pass"><br/>
<input type="submit" onclick="return doValidate();" value="Log In">
<a href="index.php">Cancel</a></p>
</form>
<p>
For a password hint, view source and find a password hint
in the HTML comments.
<!-- Hint: The password is the four character sound a cat
makes (all lower case) followed by 123. -->
</p>
<script type="text/javascript">
function doValidate() {
    console.log('Validating...');
    try {
        pw = document.getElementById('id_1723').value;
        em= document.getElementById('id_1722').value;
        console.log("Validating pw="+pw);
        if (pw == null || pw == "" || em==null || em=="") {
            alert("Both fields must be filled out");
            return false;
        }
        if(em.indexOf('@') == -1){
          alert("Invalid email address");
          return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</div>
</body>
</html>
