<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] == "true") {
    header("location:../index.php");

} else {
    include('../includes/credentials.php');

    $connection = new mysqli($server, $user_name, $password, $database);
    if ($connection->connect_error) {
        die ('Connection failed: ' . $connection->connect_error);
    }


    //$db_handle = mysql_connect($server, $user_name, $password);
    //$db_found = mysql_select_db($database, $db_handle);

        $user_user_input = $_POST['user_user_input'];
        $user_password_input = $_POST['user_password_input'];

        $SQL = "SELECT * FROM authentication_table WHERE user_name='$user_user_input';";
        //$result = mysql_query($SQL);
        $result = $connection->query($SQL);

        if ($result->num_rows > 0) {
            //while ($db_field = mysql_fetch_assoc($result)) {
            while ($db_field = mysqli_fetch_assoc($result)){
                $user_name = $db_field['user_name'];
                $password_hash = $db_field['password_hash'];
                $password_salt = $db_field['salt'];
            }

            //password_attempt
            $password_attempt = hash_hmac('sha512', $user_password_input . '|', $password_salt);
            echo "<br>Password Attempt: $password_attempt";

            if ($password_attempt == $password_hash) {
                echo "authenticated!";
                $_SESSION['authenticated'] = "true";
                header("location:../index.php");
            } else {
                echo "got in the else";
                $_SESSION['authenticated'] = "false";
                header("location:./login.php");
            }
        } else {

            echo "Error - could not connect to database! Please press 'Back' and contact your system administrator before trying again.";

        }
        $connection->close();

}
?>