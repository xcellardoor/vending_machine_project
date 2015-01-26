<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id();
}

//Check if Session is authenticated and if not redirect to the login page
if ($_SESSION['authenticated'] != "true") {
    header("location:./authentication/login.php");
} else {
} ?>
<html>

<head>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="./js/jquery.tablesorter/themes/blue/style.css">
    <link rel="icon" type="image/png" href="./includes/icon.png">
    <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="./js/jquery.tablesorter/jquery.tablesorter.js"></script>
    <script type="text/javascript" src="./includes/shared_javascript_functions.js"></script>

    <script>
        //Apply tablesorter functionality to any tables using the table sorter class.
        //Also set the delete machine button to disabled.
        $(document).ready(function () {
                $("table").tablesorter();
                $('#vending_remove_machine_submit').prop('disabled', true);
            }
        );

        //Include the script in shared_javascript which causes the sidebar to follow the user.
        $(function(){
            sidebar_follow_user_script('#vending_amendments_section');
        })

        //This script passes values to an external PHP file which does the database crunching and then returns the result to this parent - in this case, sorting the table.
        //The results are loaded back into the table div, without the need to reload the page.
        function filter_table() {
            var request = $.ajax({
                url: "vending_table_content.php?vending_filter_dropdown=" + $('#vending_filter_dropdown').val() + "&vending_filter_machine_id=" + $('#vending_filter_machine_id').val() + "&vending_filter_product_name=" + $('#vending_filter_product_name').val() + "&vending_filter_building=" + $('#vending_filter_building').val() + "&vending_filter_quantity_value=" + $('#vending_filter_quantity_value').val() + "&vending_filter_quantity_direction=" + $('#vending_filter_quantity_direction').val(),
                type: "GET",
                dataType: "html"
            });

            request.done(function (msg) {
                $("#table_section").html(msg);
                $("table").tablesorter();
            });

            request.fail(function (jqXHR, textStatus) {
                alert("Request failed: " + textStatus);
            });
        }

        //This SWITCH CASE is used to alter the sorting input boxes presented to the user. The DOM is changed based upon the choices made, as not all of these options will need to be on-screen all of the time.
        function filter_selections(argument) {
            switch (argument) {
                case "machine_id":
                    var result = "<input id='vending_filter_machine_id' placeholder='Machine ID?' pattern='[\d]{1,10}' title='Vending Machine ID Number (Max 10 numbers)'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "product_name":
                    var result = "Type start of name and click Filter<br><input id='vending_filter_product_name' placeholder='Product Name?' pattern='[\w\d\s\W\D\S]{1,50}' title='Maximum 50 character limit, no punctuation'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "building":
                    var result = "Type start of name and click Filter<br><input id='vending_filter_building' placeholder='Building?' pattern='[\w\d\s\W\D\S]{1,50}' title='Maximum 50 character limit, no punctuation'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "quantity":
                    var result = "<br><select id='vending_filter_quantity_direction'><option value='gt' selected>Greater than or equal to</option><option value='lt'>Less than or equal to</option></select><input id='vending_filter_quantity_value' placeholder='Quantity?' pattern='[\d]{1,5}' title='Number - 0 to 99,999'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                default:
                    document.getElementById('filter_options').innerHTML = "";
            }
        }

        //This script is used as a warning and triggered whenever a user ticks the 'arm' switch next to the vending machine delete button. The act of doing so causes this script to enable that button.
        /*function remove_machine() {
            if (window.confirm("Are you SURE you wish to delete the vending machine, and lose record of both it and all the products it currently contains?")) {
                var request = $.ajax({
                    url: "post.php?vending_remove_machine_dropdown=" + $('#vending_remove_machine_dropdown').val() + "&vending_remove_machine_submit='1'",
                    type: 'POST',
                    dataType: "html",
                    success: function (data) {
                        console.log(data); // Inspect this in your console
                    }
                });

                request.done(function (msg) {
                    alert("Vending Machine Deleted" + msg);
                });

                request.fail(function (jqXHR, textStatus) {
                    alert("Request failed: " + textStatus);
                });

            }
            else {
                die("javascript");
            }
        }*/

    </script>
</head>
<title>Vending Machines</title>

<body>

<?php
include('./includes/shared_php_functions.php'); //Import shared functions
include('./includes/menu.php'); //Import menu for the top of the screen
date_default_timezone_set('Europe/London');
?>

<div id="main-body">

    <div class='page_function_title'><h1>Vending Machine Management</h1></div>

    <!--Create static dropdown box with sorting choices, a change in which triggers the filter_selections script-->
    <div align=center id="left_column" style="float: left; width: 66%">
        <select id='vending_filter_dropdown' onchange="filter_selections(this.value)">
            <option value="no_filter" selected>No Filter</option>
            <option value="machine_id">Machine ID</option>
            <option value="in_date">In Date</option>
            <option value="out_of_date">Out Of Date</option>
            <option value="product_name">Product Name</option>
            <option value="building">Building</option>
            <option value="quantity">Quantity</option>
        </select>
        <!--This button causes the filter_table script to be used to reload the table contents, based upon current sort choices-->
        <button type="button" onclick="filter_table()">Filter!</button>
        <br>

        <div id="filter_options">
        </div>

        <div id='table_section'>
            <!--Include the default vending database table!-->
            <?php include('./includes/credentials.php');
            include('./vending_table_content.php');
            ?>
        </div>
    </div>

    <div id="vending_amendments_section">

        <form name="vending_add_product_form" method='post' action='post.php'>
            <!--Create all of the elements that are needed for the vending machine add product form-->
            <?php
            //Check for connectivity errors
            if ($connection->connect_error) {
                echo("Connection failed - Database Connectivity Error: " . $connection->connect_error);
            } else {
                //Initialise Arrays for population
                $active_machine_array = array();
                $product_array_items = array();
                $machines_in_use_array = array();
                $product_array_values = array();
                $product_table_options = array();
                $product_table_values = array();
                $vending_table_columns = array();

                //Fetch current vending machine and product data.
                $SQL = 'SELECT * FROM vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id ORDER BY product_name ASC;';
                $result = $connection->query($SQL);

                //Populate these arrays based on the returned data.
                while ($db_field = $result->fetch_assoc()) {
                    array_push($machines_in_use_array, $db_field['machine_id']);
                    array_push($product_array_items, $db_field['product_name']);
                    array_push($product_array_values, $db_field['product_id']);
                }

                //Fetch a list of all machine IDs and push them into the active machine array.
                $SQL = "SELECT machine_id FROM machine_table";
                $result = $connection->query($SQL);
                while ($db_field = $result->fetch_assoc()) {
                    array_push($active_machine_array, $db_field['machine_id']);
                }

                //Fetch a list of all products, and place them into their respective arrays - these will be used to compose drop-down boxes.
                $SQL = "SELECT * from product_table order by product_name ASC;";
                $result = $connection->query($SQL);
                while ($db_field = $result->fetch_assoc()) {
                    array_push($product_table_values, $db_field['product_id']);
                    array_push($product_table_options, $db_field['product_name']);
                }

                //Fetch a list of the columns themselves, as these shall be used to create a dropdown list of database options which can be amended.
                $SQL = 'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="vending_database" AND `TABLE_NAME`="vending_table";';
                $result = $connection->query($SQL);
                while ($db_field = $result->fetch_assoc()) {
                    array_push($vending_table_columns, $db_field['COLUMN_NAME']);
                }
                $active_machine_array = array_unique($active_machine_array);
                $machines_in_use_array = array_unique($machines_in_use_array);
                sort($machines_in_use_array); //Sort the machines in use as they may not actually be in order when they come out of the database.
            }
            ?>

            <div align=center>
                <!--Create the database management controls!-->
                <h3>Add Product To Machine</h3>
                <table class="adjustment_controls">
                    <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Machine ID</th>
                        <th>Quantity</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <!--Dropdown menu of products, in the key->value sense of name->number-->
                            <?php echo dropdown_menu('add_product_name', $product_table_values, $product_table_options, 1); ?></td>
                        <td>
                            <!--Dropdown menu of machines-->
                            <?php echo dropdown_menu('add_vending_machine', $active_machine_array, $active_machine_array, 1); ?></td>
                        <td><input name="new_quantity" style="width:100%" placeholder="Quantity" pattern="[\d]{1,3}"
                                   title="Number, maximum 999"/></td>
                    </tr>
                    </tbody>
                    <thead>
                    <tr>
                        <th>Best-Before</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <!--HTML5 date input for a product's best before-->
                        <td><input name="new_best_before" style="width:100%" placeholder='2015-01-01' type='date'
                                   value='2015-01-01'/></td>
                        <td><input name="vending_add_product_submit" type="submit" value="Add Product"/></td>
                    </tr>
                    </tbody>
                </table>
        </form>
        <hr class="adjustment_hr">

        <form name="vending_remove_product_form" method='post' action='post.php'>
            <h3>Remove Product from Machine</h3>
            <table class="adjustment_controls">
                <tr>
                    <th>Product Name</th>
                    <th>Vending Machine</th>
                </tr>
                <tr>
                    <!-- These dropdown menus are used to list possible products to remove as well as possible machines. All of the items must be in active use by the vending machines table.-->
                    <td><?php echo dropdown_menu('remove_product_name', $product_array_values, $product_array_items, 1); ?></td>
                    <td><?php echo dropdown_menu('remove_vending_machine', $machines_in_use_array, $machines_in_use_array, 1); ?></td>
                    <td><input name="vending_remove_product_submit" type="submit" value="Remove Product"/></td>
                </tr>

            </table>

            <hr class="adjustment_hr">
        </form>

        <form name='vending_add_machine_form' method='post' action='post.php'>


            <h3>Add Machine to Database</h3>
            <table class="adjustment_controls">
                <thead>
                <tr>
                    <th>New Machine ID</th>
                    <th>Building</th>
                    <th>Floor (Optional)</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <!--HTML5 safeguard on the new machine_id-->
                    <td><input id='vending_new_machine_id' name="vending_new_machine_id" style="width:100%"
                               placeholder="Machine ID" pattern="[0-9]{1,5}" required/>
                    <td><input name="vending_new_machine_building" style="width:100%" placeholder="Building"/>
                    <td><input name="vending_new_machine_floor" style="width:100%" placeholder="Floor"/>
                    <td><input name="vending_new_machine_submit" type="submit" value="Add Machine"/></td>
                </tr>
                </tbody>
            </table>

            <hr class="adjustment_hr">

            <h3>Alter Machine in Database</h3>
            <table class="adjustment_controls">
                <thead>
                <tr>
                    <th>Machine ID</th>
                    <th>Attribute</th>
                    <th>New Value</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <!-- Create two dropdown menus for altering an existing machine - the machines in use and the attributes which can be altered-->
                    <td><?php echo dropdown_menu('vending_alter_machine_id', $machines_in_use_array, $machines_in_use_array, 1); ?></td>
                    <td><?php echo dropdown_menu('vending_alter_machine_attribute', ['machine_id', 'building', 'floor'], ['Machine ID', 'Building', 'Floor'], 1) ?></td>
                    <td><input name="vending_alter_machine_value" style="width:100%" placeholder="New Value"/></td>
                    <td><input name="vending_alter_machine_submit" type="submit" value="Alter Machine"/></td>
                </tr>
                </tbody>
            </table>
        </form>

        <hr class="adjustment_hr">
        <form name='vending_remove_machine_form' method='post' action='post.php'>
            <h3>Remove Machine from Database</h3>
            <table class="adjustment_controls">
                <tr>
                    <th>Vending Machine</th>
                </tr>
                <tr>
                    <td>

                        <?php
                        //Finally, create a dropdown menu of currently used machines which we can delete from the database.
                        echo dropdown_menu('vending_remove_machine_dropdown', $active_machine_array, $active_machine_array, 1); ?>
                    </td>
                    <!--This code creates the safeguard over the delete machine button, as doing so would take all records of products in the machine with it-->
                    <td>Confirm?<input type="checkbox" id="remove_vending_checkbox"
                                       onclick="toggle_button('vending_remove_machine_submit')"
                                       id="remove_vending_machine_checkbox" value="true"></td>
                    <td><input id="vending_remove_machine_submit" name="vending_remove_machine_submit" type="submit"
                               value="Remove Machine"/></td>
                    </button>
                </tr>
            </table>
        </form>
    </div>
</div>
<div class="clear"></div>
</div>
<?php
include('./includes/footer.php');
$connection->close();
//Check for any errors stored in SESSION and if there are any, display them (external shared script)
check_for_errors();
?>

</body>
</html>
