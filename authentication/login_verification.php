<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id();
}
//Check if we are already authenticated! If so, redirect to index.php as there is no need to parse this page.
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] == "true") {
    header("location:../index.php");

} else {
    include('../includes/credentials.php');
    //Get database connection
    $connection = new mysqli($server, $user_name, $password, $database);
    if ($connection->connect_error) {
        die ('Connection failed - Database Connectivity Error: ' . $connection->connect_error);
    }

    $user_user_input = $_POST['user_user_input'];
    $user_password_input = $_POST['user_password_input'];

    //Use Prepared statement to avert any MySQL injection through the public login page.
    //Fetch _ONLY_ the records for the current username trying to login - that is, if there even is a user with that name. Anything more is a waste of computation.
    $prepared_statement = $connection->prepare("SELECT * FROM authentication_table WHERE user_name = ?");
    $prepared_statement->bind_param('s', $user_user_input);

    $prepared_statement->execute();

    $result = $prepared_statement->get_result();

    //Fetch the interesting data - username, password_hash, and the password's salt.
    if ($result->num_rows > 0) {
        while ($db_field = mysqli_fetch_assoc($result)) {
            $user_name = $db_field['user_name'];
            $password_hash = $db_field['password_hash'];
            $password_salt = $db_field['salt'];
        }

        //Hash up the password salt using the salt value stored in the database.
        $password_attempt = hash_hmac('sha512', $user_password_input . '|', $password_salt);

        //If the user is legitimate, the password hash just derived, and the one stored in the database should be a match, in which case allow the user to have their SESSION set and allowed in.
        //Anything else and they are not authenticated - fallback to login.php and they can try again.
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

        echo "Error - could not connect to database! Please try again!";
        header("location:./login.php");

    }
    //Tidy up by closing the connection to the DB.
    $connection->close();
}