<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id();
}
//Check for authentication, if false, redirect to login.php
if ($_SESSION['authenticated'] != "true") {
    header("location:./authentication/login.php");
} else {
} ?>
<html>

<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="./js/jquery.tablesorter/themes/blue/style.css">
    <link rel="icon" type="image/png" href="./includes/icon.png">
    <script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="./includes/shared_javascript_functions.js"></script>
    <script type="text/javascript" src="./js/jquery.tablesorter/jquery.tablesorter.js"></script>

    <script>

        //Runs when the document has finished loading, - Apply the tablesorter functionality to tables using that class, and set the stock remove button to disabled for safety's sake.
        $(document).ready(function () {
                $("table").tablesorter();
                $('#remove_stockroom_product_submit').prop('disabled', true);
            }
        );

        //Use this script stored in the shared javascript file to have the sidebar follow the user.
        $(function(){
            sidebar_follow_user_script('#stockroom_amendments_section');
        })

        //SWITCH CASE used to manipulate the DOM based upon which filter functionality the user chooses from the dropdown box. Different choices cause different form elements to be placed at the head of the table.
        function filter_selections(argument) {

            switch (argument) {
                case "product_name":
                    var result = "Type start of name and click Filter<br><input id='stockroom_filter_product_name' placeholder='Product Name?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "remaining_stock":
                    var result = "<select id='stockroom_filter_stock_direction'><option value='gt' selected>Greater than or equal to</option><option value='lt'>Less than or equal to</option></select><input id='stockroom_filter_stock_value' placeholder='Amount of Stock?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "low_stock":
                    var result = "Type stock amount and click Filter<br><input id='stockroom_filter_low_stock' placeholder='Low Stock?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                default:
                    document.getElementById('filter_options').innerHTML = "";
            }
        }

        //Javascript to re-request the post.php file with certain filters applied. Place the returned content back in '#table-section'
        function filter_table() {
            var request = $.ajax({
                url: "stockroom_table_content.php?stockroom_filter_dropdown=" + $('#stockroom_filter_dropdown').val() + "&stockroom_filter_stock_direction=" + $('#stockroom_filter_stock_direction').val() + "&stockroom_filter_stock_value=" + $('#stockroom_filter_stock_value').val() + "&stockroom_filter_product_name=" + $('#stockroom_filter_product_name').val() + "&stockroom_filter_low_stock=" + $('#stockroom_filter_low_stock').val(),
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
    </script>
    <title>Stockroom | Vending Machine Management System</title>
</head>
<body>
<?php
include "./includes/menu.php";
include "./includes/shared_php_functions.php";
?>

<div id="main-body">
    <!--Float the page left and apply title-->
    <div class="page_function_title"><h1>Stockroom Management</h1></div>
    <div style="float: left; width: 66%" align="center">

        <!--Static dropdown box used to offer filter selections-->
        <select id='stockroom_filter_dropdown' onchange="filter_selections(this.value)">
            <option value="no_filter" selected>No Filter</option>
            <option value="product_name">Product Name</option>
            <option value="in_stock">In Stock</option>
            <option value="out_of_stock">Out Of Stock</option>
            <option value="remaining_stock">Remaining Stock</option>
        </select>
        <button type="button" onclick="filter_table()">Filter!</button>
        <br>

        <!--This empty div becomes populated by the Javascript function filter_options as and when the user triggers such an action-->
        <div id="filter_options">

        </div>

        <!--Use PHP to load the stockroom table with default values-->
        <div id="table_section">
            <?php
            include('./stockroom_table_content.php');
            ?>
        </div>


    </div>
    <div id="stockroom_amendments_section">

        <form name="stock_alter_product_form" method='post' action='post.php'>

            <?php
            //Non-prepared statement which is okay and uses less code, since no user-input is being used as a part of the SQL query.
            $SQL = "SELECT * FROM product_table ORDER BY product_name ASC;";
            $result = $connection->query($SQL);

            //Initialise arrays
            $product_array = array();
            $column_array = array();

            //Fetch results from the database and populate the product array
            while ($db_field = $result->fetch_assoc()) {
                array_push($product_array, $db_field['product_name']);
            }

            $selected = 1;
            //Fetch available product attributes that we are able to change
            //Fetch the columns to be used in the 'change attribute' dropdown menu.
            $SQL = 'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="vending_database" AND `TABLE_NAME`="product_table";';
            $result = $connection->query($SQL);
            while ($row = $result->fetch_assoc()) {
                array_push($column_array, $row['COLUMN_NAME']);

            }
            ?>
            <h3>Alter Existing Product</h3>
            <table class="adjustment_controls">
                <thead>
                <th>Product Name</th>
                <th>Attribute to Change</th>
                </thead>
                <tbody>
                <tr>
                    <td><?php
                        //Create fixed dropdown box but using the dropdown menu script in the shared php functions file.
                        echo dropdown_menu('product_list', $product_array, $product_array, 1); ?></td>
                    <td><?php echo dropdown_menu('column_list', ['product_name', 'stock_purchase_price', 'stock_sale_price', 'remaining_stock', 'low_stock_alert'], ['Change Name', 'Change Wholesale Purchase Price', 'Sale Price', 'Remaining Stock', 'Low Stock Alert'], 1);
                        ?></td>
                </tr>
                </tbody>
                <thead>
                <th>New Value</th>
                </thead>
                <tbody>
                <td>
                    <!--Input fields with set requirements-->
                    <input name="new_product_value" size="15" placeholder="New Value" title="Enter new value"
                           pattern="[\w\d\s\W\D\S]{1,50}" required/>
                </td>
                <td>
                    <input name="stockroom_alter_product_submit" type="submit" value="Update Database"/>
                </td>
                </tbody>
            </table>
            <!--ADDITION-->
            <hr class="adjustment_hr">
        </form>

        <form name="stock_add_new_product_form" method='post' action='post.php'>
            <h3>Add New Product to Stockroom</h3>
            <table class="adjustment_controls">
                <tr>
                    <th>Product Name</th>
                    <th>Initial Stock</th>
                    <th>Low Stock Alert</th>
                </tr>
                <tr>
                    <!--Input fields with set requirements-->
                    <td><input name='stockroom_new_product_name' style='width:100%' placeholder="New Product Name"
                               pattern="[\w\d\s\W\D\S]{1,50}" title="Maximum 50 character limit, no punctuation"></td>
                    <td><input name='stockroom_new_stock_level' style='width:100%'
                               placeholder="Initial Stock Level" title="Range from 0 to 99,999"/>
                    </td>
                    <td><input name='stockroom_new_stock_alert' style='width:100%' placeholder="Low Stock Alert"
                               pattern="[0-9]{1,5}" title="Range from 0 to 99,999"></td>
                </tr>
                <tr>
                    <th>Wholesale Purchase Price (Pence)</th>
                    <th>Stock Sale Price (Pence)</th>
                </tr>
                <tr>
                    <!--Input fields with set requirements-->
                    <td><input name='stockroom_new_purchase_price' style='width:100%'
                               placeholder="Wholesale Purchase Price"
                               pattern="[0-9]{1,3}" title="Value from 0 to 999"></td>
                    <td><input name='stockroom_new_sale_price' style='width:100%' placeholder="New Sale Price"
                               pattern="[0-9]{1,3}" title="Value from 0 to 999"></td>
                    <td><input name="stockroom_new_stock_submit" type="submit" value="Add Product"/></td>
                    <!--<td><button type="button" onclick="add_new_product()">Add Product</button></td>-->
                </tr>
            </table>

            <?php
            //Fetch a list of product names from the database and populate an array for later use.
            $product_names = array();
            $SQL = 'SELECT * FROM product_table ORDER BY product_name ASC;';
            $result = $connection->query($SQL);

            while ($db_field = $result->fetch_assoc()) {
                array_push($product_names, $db_field['product_name']);
            }
            ?>
            <hr class="adjustment_hr">
        </form>
        <!--Stock removal form-->
        <form name="stock_remove_product_form" method='post' action='post.php'>
            <h3>Remove Product from Stockroom</h3>
            <table class="adjustment_controls">
                <tr>
                    <th>Product Name</th>
                </tr>
                <tr>
                    <!-- Create dynamic drop-down menu based upon arrays created earlier-->
                    <td> <?php echo dropdown_menu('remove_product_name', $product_names, $product_names, 1); ?></td>
                    <td>Confirm Delete?<input type="checkbox" id="remove_stockroom_checkbox"
                                              onclick="toggle_button('remove_stockroom_product_submit')"
                                              id="remove_stockroom_checkbox" value="true"></td>
                    <td><input name="stockroom_remove_product_submit" id="remove_stockroom_product_submit" type="submit"
                               value="Remove Product"/></td>
                </tr>
            </table>
        </form>
    </div>

    <!--Clear any div formatting-->
    <div class="clear"></div>
</div>
<?php
include('./includes/footer.php');
$connection->close();
//Use the error checking script in shared javascript file to check the SESSION variable and see if we need to alert the user
check_for_errors();

?>

</body>

</html>