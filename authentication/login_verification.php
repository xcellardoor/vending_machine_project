<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id();
}

if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] == "true") {
    header("location:../index.php");

} else {
    include('../includes/credentials.php');

    $connection = new mysqli($server, $user_name, $password, $database);
    if ($connection->connect_error) {
        die ('Connection failed - Database Connectivity Error: ' . $connection->connect_error);
    }


    //$db_handle = mysql_connect($server, $user_name, $password);
    //$db_found = mysql_select_db($database, $db_handle);

        $user_user_input = $_POST['user_user_input'];
        $user_password_input = $_POST['user_password_input'];

        $prepared_statement = $connection->prepare("SELECT * FROM authentication_table WHERE user_name = ?");
        $prepared_statement->bind_param('s',$user_user_input);
        //$user_name=$user_user_input;
        $prepared_statement->execute();

        //$SQL = "SELECT * FROM authentication_table WHERE user_name='$user_user_input';";
        //$result = mysql_query($SQL);
        //$result = $connection->query($SQL);
        $result = $prepared_statement->get_result();

        if ($result->num_rows > 0) {
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

            echo "Error - could not connect to database or username invalid! Please try again!";
            header("location:./login.php");

        }
        $connection->close();

}