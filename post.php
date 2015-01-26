<!--SESSION start and authentication check-->
<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id();
}
//Check for authentication
if ($_SESSION['authenticated'] != "true") {
    header("location:./authentication/login.php");
} else {
}

//Set inter-page error variable to null, so that the next page won't flag an old error.
$_SESSION['error'] = null;

//As always include the credentials for the script to use to connect to the database >.<
include('./includes/credentials.php');


//Set up a new connection to the MySQL backend.
$connection = new mysqli($server, $user_name, $password, $database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

//Start the first optional route through the script.
if (isset($_POST['stockroom_alter_product_submit'])) {
    #Fetch variables from the POST
    $new_value = $_POST['new_product_value'];
    $product_name = $_POST['product_list'];
    $column_array = $_POST['column_list'];
    //Prepare the SQL statement, to ward off SQL injection
    $prepared_statement = $connection->prepare("UPDATE product_table SET $column_array = ? WHERE product_name=?");

    //Checking if the statement went in okay, if not alert the user and return to stockroom
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:stockroom.php");
    }
    //Bind input parameters to the SQL query and check for failure or warnings
    $prepared_statement->bind_param('ss', $new_value, $product_name);
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:stockroom.php");
    }
    //Lock tables so this thread does not act on old data, then execute.
    $connection->query('LOCK TABLES product_table WRITE;');
    if (!$prepared_statement->execute()) {
        $_SESSION['error'] = $connection->error;
    }
    $connection->query('UNLOCK TABLES;'); //Unlock
    //Error checking!
    if (!$prepared_statement) {
        $_SESSION['error'] = "$connection->error;";
        $connection->close();
        header("location:stockroom.php");
    }
    //Tidy up and close connection.
    $connection->close();
    header("location:stockroom.php");
}

//The second possible flow of execution through this script
if (isset($_POST['vending_add_product_submit'])) {
    #Declaration and fetching of variables
    $new_product_id = $_POST['add_product_name'];
    $new_machine_id = $_POST['add_vending_machine'];
    $new_quantity_in_machine = $_POST['new_quantity'];
    $new_best_before = $_POST['new_best_before'];

    #Preparing SQL statement...
    $prepared_statement = $connection->prepare("INSERT INTO vending_table (product_id, machine_id, quantity_in_machine, best_before) VALUES (?, ?, ?, ?);");

    #Verify statement was prepared successfully
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }

    //Bind parameters and check for an error.
    $prepared_statement->bind_param('ssss', $new_product_id, $new_machine_id, $new_quantity_in_machine, $new_best_before);
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }

    //Lock tables, execute query, unlock and check for errors.
    $connection->query('LOCK TABLES product_table WRITE;');
    if (!$prepared_statement->execute()) {
        //echo $connection->error;
        //die();
        $_SESSION['error'] = $connection->error;
    }
    $connection->query('UNLOCK TABLES;');
    if (!$prepared_statement) {
        $_SESSION['error'] = "$connection->error;";
        $connection->close();
        header("location:vending.php");
    }

    //Prepare the second part of this database manipulation action - altering the product table to reflect the change after a product has been put in a vending machine
    $prepared_statement = $connection->prepare("UPDATE product_table set remaining_stock = remaining_stock-? where product_id=?;");
    //Check for error in preparing
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }
    //Bind parameters and again check for errors
    $prepared_statement->bind_param('ss', $new_quantity_in_machine, $new_product_id);
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }
    //Lock, execute, unlock, error check
    $connection->query('LOCK TABLES product_table WRITE;');
    if (!$prepared_statement->execute()) {
        //echo $connection->error;
        //die();
        $_SESSION['error'] = $connection->error;
    }
    $connection->query('UNLOCK TABLES;');
    if (!$prepared_statement) {
        $_SESSION['error'] = "$connection->error;";
        $connection->close();
        header("location:vending.php");
    }

    $connection->close(); //Tidy up
    header("location:vending.php"); //Redirect to original page.
}

if (isset($_POST['vending_remove_product_submit'])) {
    //Fetch relevant variables
    $product_id = $_POST['remove_product_name'];
    $machine_id = $_POST['remove_vending_machine'];
    $quantity_left_in_machine = 0; //So that we don't accidentally add or remove stock if commands below fail

    //Prepare query and check for errors
    $prepared_statement = $connection->prepare("SELECT * from vending_table WHERE vending_table.machine_id=? and vending_table.product_id=?;");
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }

    //Bind parameters and check for errors
    $prepared_statement->bind_param('ss', $machine_id, $product_id);
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }

    //Lock, error check, execute, unlock.
    $connection->query('LOCK TABLES product_table WRITE;');
    if (!$prepared_statement->execute()) {
        //echo $connection->error;
        //die();
        $_SESSION['error'] = $connection->error;
    }
    $connection->query('UNLOCK TABLES;');
    if (!$prepared_statement) {
        $_SESSION['error'] = "$connection->error;";
        $connection->close();
        header("location:vending.php");
    }

    //Fetch the value from the database that we need for the next query.
    while ($db_field = $prepared_statement->fetch()) {
        $quantity_left_in_machine = $db_field['quantity_in_machine'];
    }

    //Execute the three queries below. It's okay that they aren't 'prepared' as these variables are not input by the user.
    $connection->query("UPDATE product_table set remaining_stock = remaining_stock+$quantity_left_in_machine;");
    $connection->query("DELETE FROM vending_table WHERE product_id='$product_id' and machine_id='$machine_id';");
    $connection->query("DELETE FROM vending_table WHERE product_id='$product_id' and machine_id='$machine_id';");

    //Close connection and redirect
    $connection->close();
    header("location:vending.php");
}

if (isset($_POST['stockroom_remove_product_submit'])) {
    //Fetch variable from post
    $remove_product_name = $_POST['remove_product_name'];

    //Prepare query, check for errors
    $prepared_statement = $connection->prepare("DELETE FROM product_table WHERE product_name=?;");
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:stockroom.php");
    }
    //Bind parameters and check for errors.
    $prepared_statement->bind_param('s', $remove_product_name);
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:stockroom.php");
    }

    //Lock, execute, error check, unlock, redirect.
    $connection->query('LOCK TABLES product_table WRITE;');
    if (!$prepared_statement->execute()) {
        //echo $connection->error;
        //die();
        $_SESSION['error'] = $connection->error;
    }
    $connection->query('UNLOCK TABLES;');
    if (!$prepared_statement) {
        $_SESSION['error'] = "$connection->error;";
        $connection->close();
        header("location:stockroom.php");
    }
    $connection->close();
    header("location:stockroom.php");
}

if (isset($_POST['stockroom_new_stock_submit'])) {
    //Grab several variables that should be set by the user.
    $product_name = $_POST['stockroom_new_product_name'];
    $stock_level = $_POST['stockroom_new_stock_level'];
    $stock_alert = $_POST['stockroom_new_stock_alert'];
    $purchase_price = $_POST['stockroom_new_purchase_price'];
    $sale_price = $_POST['stockroom_new_sale_price'];

    //Large prepare... values are actually product-name, purchase-price, sale-price, stock-level and stock-alert.
    $prepared_statement = $connection->prepare("INSERT INTO product_table (product_name, stock_purchase_price, stock_sale_price, remaining_stock, low_stock_alert) VALUES (?, ?, ?, ?, ?);");
    //Check for error
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error; //Assign session varialbe if there is an error.
        $connection->close();
        header("location:stockroom.php");
    }
    //Bind parameters and check for errors
    $prepared_statement->bind_param('sssss', $product_name, $purchase_price, $sale_price, $stock_level, $stock_alert);
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:stockroom.php");
    }

    //Lock, execute, error check, unlock and check for errors then redirect.
    $connection->query('LOCK TABLES product_table WRITE;');
    if (!$prepared_statement->execute()) {
        //echo $connection->error;
        //die();
        $_SESSION['error'] = $connection->error;
    }
    $connection->query('UNLOCK TABLES;');
    if (!$prepared_statement) {
        $_SESSION['error'] = "$connection->error;";
        $connection->close();
        header("location:stockroom.php");
    }
    header("location:stockroom.php");
    $connection->close();;
}


if (isset($_POST['vending_new_machine_submit'])) {
    //Collect variables passed in by form
    $machine_id = $_POST['vending_new_machine_id'];
    $building = $_POST['vending_new_machine_building'];
    $floor = $_POST['vending_new_machine_floor'];

    //Prepare the query and check for errors
    $prepared_statement = $connection->prepare("INSERT INTO machine_table (machine_id, building, floor) VALUES (?, ?, ?);");
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }

    //Bind user-entered parameters to the query and check for errors
    $prepared_statement->bind_param('sss', $machine_id, $building, $floor);
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }
    //Lock tables, execute, unlock, error check
    $connection->query('LOCK TABLES product_table WRITE;');
    if (!$prepared_statement->execute()) {
        $_SESSION['error'] = $connection->error;
    }
    $connection->query('UNLOCK TABLES;');
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }
    //Close connection and redirect
    $connection->close();
    header("location:vending.php");
}

/*
 * These last two functions perform identically to those above, albeit with different variable names
 *
 *
 *
 *
 */


if (isset($_POST['vending_remove_machine_submit'])) {
    $machine_to_delete = $_POST['vending_remove_machine_dropdown'];

    $prepared_statement = $connection->prepare("DELETE FROM machine_table WHERE machine_id=?;");
    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }

    $prepared_statement->bind_param('s', $machine_to_delete);

    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }

    $connection->query('LOCK TABLES product_table WRITE;');
    if (!$prepared_statement->execute()) {
        //echo $connection->error;
        //die();
        $_SESSION['error'] = $connection->error;
    }
    $connection->query('UNLOCK TABLES;');
    if (!$prepared_statement) {
        $_SESSION['error'] = "$connection->error;";
        $connection->close();
        header("location:vending.php");
    }
    header("location:vending.php");
    $connection->close();
}

if (isset($_POST['vending_alter_machine_submit'])) {
    $machine_id = $_POST['vending_alter_machine_id'];
    $attribute_to_alter = $_POST['vending_alter_machine_attribute'];
    $new_value = $_POST['vending_alter_machine_value'];

    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }

    $prepared_statement->bind_param('sss', $attribute_to_alter, $new_value, $machine_id);

    if (!$prepared_statement) {
        $_SESSION['error'] = $connection->error;
        $connection->close();
        header("location:vending.php");
    }

    $connection->query('LOCK TABLES product_table WRITE;');
    if (!$prepared_statement->execute()) {
        //echo $connection->error;
        //die();
        $_SESSION['error'] = $connection->error;
    }
    $connection->query('UNLOCK TABLES;');
    if (!$prepared_statement) {
        $_SESSION['error'] = "$connection->error;";
        $connection->close();
        header("location:vending.php");
    }
    header("location:vending.php");
    $connection->close();
} else {
    $connection->close();
    die("No value set");
}