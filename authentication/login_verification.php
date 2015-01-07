<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 06/01/15
 * Time: 11:40
 */

/*include('./authentication/vending_auth.php');

$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    die ("Not authorized");
}

// If arrives here, is a valid user.
echo "<p>Welcome $user.</p>";
echo "<p>Congratulation, you are into the system.</p>";

$_SERVER['PHP_AUTH_USER'] = null;
$_SERVER['PHP_AUTH_PW'] = null;*/

/*$mysqli = new mysqli("localhost", "vending_user", "1q2w3e4r5t#'", "vending_database_mk2");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

if (!($stmt = $mysqli->prepare("INSERT INTO meep (id) VALUES (1)"))) {
    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
}*/
//session_start();

if (session_status() === PHP_SESSION_NONE){session_start();}


#$_POST['user_user_input']="user";
#$_POST['user_password_input']="1q2w3e4r5t#'";

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] == "true") {
    header("location:../index.php");

}
else{
    include('../includes/credentials.php');

    $db_handle = mysql_connect($server, $user_name, $password);
    $db_found = mysql_select_db($database, $db_handle);

    if ($db_found) {
        $user_user_input = $_POST['user_user_input'];
        $user_password_input = $_POST['user_password_input'];

        $SQL = "SELECT * FROM authentication_table WHERE user_name='$user_user_input';";
        $result = mysql_query($SQL);
        while ($db_field = mysql_fetch_assoc($result)) {
            $user_name = $db_field['user_name'];
            $password_hash = $db_field['password_hash'];
            $password_salt = $db_field['salt'];
        }

        //password_attempt
        $password_attempt = hash_hmac('sha512', $user_password_input . '|', $password_salt);
        echo "<br>Password Attempt: $password_attempt";

        if($password_attempt==$password_hash){
            echo "authenticated!";
            $_SESSION['authenticated']="true";
            header("location:../index.php");
        }
        else{
            echo "got in the else";
            $_SESSION['authenticated']="false";
            header("location:./login.php");
        }

    } else {

        echo "Error - could not connect to database! Please press 'Back' and contact your system administrator before trying again.";

    }
}
?>
