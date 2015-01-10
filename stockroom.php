<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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

        $(document).ready(function () {
                $("table").tablesorter();
                $('#remove_stockroom_product_submit').prop('disabled', true);
            }
        );

        $(function () {
            var $sidebar = $("#stockroom_amendments_section"),
                $window = $(window),
                offset = $sidebar.offset(),
                topPadding = 15;

            $window.scroll(function () {
                if ($window.scrollTop() > offset.top) {
                    $sidebar.stop().animate({
                        marginTop: $window.scrollTop() - offset.top + topPadding
                    });
                } else {
                    $sidebar.stop().animate({
                        marginTop: 0
                    });
                }
            });

        });

        $(document).ready(function () {
            if ($("#database_check:contains('failure')").length = -1) {
                //document.getElementById("#database_check").setAttribute("id", "database_check_error");
                $("#database_check").attr('id', "database_check_error");
            }
            else {
                alert('test');
            }
        });

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

        function open_order_window() {
            var newwindow = window.open('./orders.php', 'name', 'height=600,width=800');
            if (window.focus) {
                newwindow.focus()
            }
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
    <div class="page_function_title"><h1>Stockroom Management</h1></div>
    <div style="float: left; width: 66%" align="center">

        <select id='stockroom_filter_dropdown' onchange="filter_selections(this.value)">
            <option value="no_filter" selected>No Filter</option>
            <option value="product_name">Product Name</option>
            <option value="in_stock">In Stock</option>
            <option value="out_of_stock">Out Of Stock</option>
            <option value="remaining_stock">Remaining Stock</option>
        </select>
        <button type="button" onclick="filter_table()">Filter!</button>
        <br>

        <div id="filter_options">

        </div>

        <div id="table_section">
            <?php
            include('./stockroom_table_content.php');
            ?>
        </div>


    </div>
    <div id="stockroom_amendments_section">

        <form name='stock_update' method='post' action='post.php'>

            <?php
            $SQL = "SELECT * FROM product_table ORDER BY product_name ASC;";
            $result = $connection->query($SQL);

            $product_array = array();
            $column_array = array();

            while ($db_field = $result->fetch_assoc()) {
                array_push($product_array, $db_field['product_name']);
            }

            $selected = 0;

            $SQL = 'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="vending_database" AND `TABLE_NAME`="product_table";';
            $result = $connection->query($SQL);
            while ($row = $result->fetch_assoc()) {
                array_push($column_array, $row['COLUMN_NAME']);

            }
            ?>
            <h3>Alter Existing Product</h3>
            <table class="adjustment_controls">
            <thead><th>Product Name</th><th>Attribute to Change</th></thead>
                <tbody>
                <tr>
                <td><?php
            echo dropdown_menu('product_list', $product_array, $product_array, 1);?></td>
            <td><?php echo dropdown_menu('column_list', ['product_name', 'stock_purchase_price', 'stock_sale_price', 'remaining_stock', 'low_stock_alert'], ['Change Name', 'Change Wholesale Purchase Price', 'Sale Price', 'Remaining Stock', 'Low Stock Alert'], 1);
            ?></td>
                </tr>
                </tbody>
                <thead><th>New Value</th></thead>
                <tbody>
                <td>
                    <input name="new_product_value" size="15" placeholder="New Value" title="Enter new value" pattern="[\w\d\s\W\D\S]{1,50}"/>
                </td>
                <td>
                    <input name="stockroom_alter_product_submit" type="submit" value="Update Database"/>
                </td>
                </tbody>
            </table>
            <!--ADDITION-->
            <hr class="adjustment_hr">

            <h3>Add New Product to Stockroom</h3>
            <table class="adjustment_controls">
                <tr>
                    <th>Product Name</th>
                    <th>Initial Stock</th>
                    <th>Low Stock Alert</th>
                </tr>
                <tr>
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
                    <td><input name='stockroom_new_purchase_price' style='width:100%' placeholder="Wholesale Purchase Price"
                               pattern="[0-9]{1,3}" title="Value from 0 to 999"></td>
                    <td><input name='stockroom_new_sale_price' style='width:100%' placeholder="New Sale Price"
                               pattern="[0-9]{1,3}" title="Value from 0 to 999"></td>
                    <td><input name="stockroom_new_stock_submit" type="submit" value="Add Product"/></td>
                </tr>
            </table>

            <?php
            $product_names = array();

            $SQL = 'SELECT * FROM product_table ORDER BY product_name ASC;';
            $result = $connection->query($SQL);

            while ($db_field = $result->fetch_assoc()) {
                array_push($product_names, $db_field['product_name']);
            }
            ?>
            <hr class="adjustment_hr">

            <h3>Remove Product from Stockroom</h3>
            <table class="adjustment_controls">
                <tr>
                    <th>Product Name</th>
                </tr>
                <tr>
                    <td> <?php echo dropdown_menu('remove_product_name', $product_names, $product_names, 1); ?></td>
                    <td>Confirm Delete?<input type="checkbox" id="remove_stockroom_checkbox"
                                              onclick="toggle_button('remove_stockroom_product_submit')"
                                              id="remove_stockroom_checkbox" value="true"></td>
                    <td><input name="remove_stockroom_product_submit" id="remove_stockroom_product_submit" type="submit"
                               value="Remove Product"/></td>
                </tr>
            </table>
        </form>
    </div>

    <div class="clear"></div>
</div>
<?php
include('./includes/footer.php');
$connection->close()
?>

</body>

</html>
