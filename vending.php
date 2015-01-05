<html>

<head>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="./js/jquery.tablesorter/themes/blue/style.css">
    <script src="js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="./js/jquery.tablesorter/jquery.tablesorter.js"></script>
    <script type="text/javascript" src="./includes/shared_javascript_functions.js"></script>
    <script>

        $(document).ready(function () {
                $("table").tablesorter();
                $('#vending_remove_machine_submit').prop('disabled', true);
            }
        );

        $(function () {

            var $sidebar = $("#vending_amendments_section"),
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

        function validateForm(array) {
            for (var i in array) {
                if (i == null || i == "") {
                    alert(i + " must be filled out");
                    return false;
                }
            }

        }

        function sort_table() {
            var request = $.ajax({
                url: "vending_table_content.php?vending_sort_by_dropdown=" + $('#vending_sort_by_dropdown').val(),
                type: "GET",
                dataType: "html"
            });

            request.done(function (msg) {
                $("#table_section").html(msg);
            });

            request.fail(function (jqXHR, textStatus) {
                alert("Request failed: " + textStatus);
            });
        }

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

        function filter_selections(argument) {
            switch (argument) {
                case "machine_id":
                    var result = "<input id='vending_filter_machine_id' placeholder='Machine ID?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "product_name":
                    var result = "Type start of name and click Filter<br><input id='vending_filter_product_name' placeholder='Product Name?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "building":
                    var result = "Type start of name and click Filter<br><input id='vending_filter_building' placeholder='Building?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "quantity":
                    var result = "<br><select id='vending_filter_quantity_direction'><option value='gt' selected>Greater than or equal to</option><option value='lt'>Less than or equal to</option></select><input id='vending_filter_quantity_value' placeholder='Quantity?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                default:
                    document.getElementById('filter_options').innerHTML = "";
            }
        }

        function remove_machine() {
            if (window.confirm("Are you SURE you wish to delete the vending machine, and loose record of both it and all the products it currently contains?")) {
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
        }


    </script>
</head>
<title>Vending Machines</title>

<body>

<?php

include('./includes/shared_php_functions.php'); //Import shared functions
include('./includes/menu.php');
?>
<div id="main-body">

    <div align=center><h1>Vending Machine Management</h1></div>


    <div align=center id="left_column" style="float: left; width: 50%">
        <!--<table cellspacing='0' cellpadding='0'>-->
        <!--php echo dropdown_menu('vending_sort_by_dropdown', ['quantity_in_machine', 'machine_id', 'product_name', 'building', 'best_before'], ['Quantity', 'Vending Machine', 'Product Name', 'Building', 'Best Before'], 1); ?>
        <button type='button' onclick='sort_table()'>Sort!</button><br>-->

        <select id='vending_filter_dropdown' onchange="filter_selections(this.value)">
            <option value="no_filter" selected>No Filter</option>
            <option value="machine_id">Machine ID</option>
            <option value="in_date">In Date</option>
            <option value="out_of_date">Out Of Date</option>
            <option value="product_name">Product Name</option>
            <option value="building">Building</option>
            <option value="quantity">Quantity</option>
        </select>
        <button type="button" onclick="filter_table()">Filter!</button>
        <br>

        <div id="filter_options">

        </div>


        <!--</tr></table>-->

        <div align=center id='table_section'>


            <!--<table class='center'>-->
            <?php include('./includes/credentials.php');
            $db_handle = mysql_connect($server, $user_name, $password);
            $db_found = mysql_select_db($database, $db_handle);

            date_default_timezone_set('Europe/London');

            /**
             * if($db_found){
             * $SQL = "SELECT product_table.product_name, vending_table.machine_id, vending_table.quantity_in_machine, vending_table.best_before from vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id order by machine_id;";
             * $result = mysql_query($SQL);
             * print "<tr><th>Machine<th>Product<th>Quantity<th>Best Before</th></tr>";
             * while ($db_field = mysql_fetch_assoc($result)){
             * print "<tr><td>".$db_field['machine_id']."<td>".$db_field['product_name']."<td>".$db_field['quantity_in_machine'];
             *
             * if(strtotime($db_field['best_before'])<strtotime('now')){
             * print "<td BGCOLOR=\"#EE0000\">OUT OF DATE!</td></tr>";
             * }
             * else{
             * print "<td BGCOLOR=\"#00ff00\">IN DATE</td></tr>";
             * }
             * }
             * }
             *
             * else {
             * print "Database Access Error!";
             * mysql_close($db_handle);
             *
             * }
             **/

            include('./vending_table_content.php');
            ?>
            <!--</table>-->
        </div>
    </div>

    <div style="float: left; width: 50%" id="vending_amendments_section">


        <form class='form_alert' name='alter_vending_table' method='post' action='post.php'>
            <?php

            //onsubmit='return validateForm($test_array)
            $active_machine_array = array();
            $product_array_items = array();
            $machines_in_use_array = array();
            $product_array_values = array();
            $product_table_options = array();
            $product_table_values = array();
            $vending_table_columns = array();

            #$SQL = "SELECT * FROM vending_table";
            $SQL = 'SELECT * FROM vending_table INNER JOIN product_table ON vending_table.product_id=product_table.product_id;';
            $result = mysql_query($SQL);

            while ($db_field = mysql_fetch_assoc($result)) {
                array_push($machines_in_use_array, $db_field['machine_id']);
            }
            $result = mysql_query($SQL);
            while ($db_field = mysql_fetch_assoc($result)) {
                //array_push($product_array, $db_field['product_id']);
                array_push($product_array_items, $db_field['product_name']);
            }

            $result = mysql_query($SQL);
            while ($db_field = mysql_fetch_assoc($result)) {
                array_push($product_array_values, $db_field['product_id']);
            }

            $SQL = "SELECT machine_id FROM machine_table";
            $result = mysql_query($SQL);
            while ($db_field = mysql_fetch_assoc($result)) {
                array_push($active_machine_array, $db_field['machine_id']);
            }

            $SQL = "SELECT * from product_table;";
            $result = mysql_query($SQL);
            while ($db_field = mysql_fetch_assoc($result)) {
                array_push($product_table_values, $db_field['product_id']);
                array_push($product_table_options, $db_field['product_name']);
            }

            $SQL = 'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="vending_database" AND `TABLE_NAME`="vending_table";';
            $result = mysql_query($SQL);
            while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                array_push($vending_table_columns, $row['COLUMN_NAME']);
            }

            #$product_array_items = array_unique($product_array_items);
            $active_machine_array = array_unique($active_machine_array);
            $machines_in_use_array = array_unique($machines_in_use_array);
            sort($machines_in_use_array);
            #sort($product_array_items);
            ?>


            <div align=center>

                <h3>Alter Product</h3>
                <table cellspacing='0' cellpadding='0'>
                    <tr>
                        <th>Product Name</th>
                        <th>Vending Machine</th>
                        <th>Attribute to Alter</th>
                        <th>New Value</th>
                    </tr>
                    <!--Since the same list of 'existing products in machines' is needed, we can borrow the line used for deleting-->
                    <tr>
                        <td> <?php echo dropdown_menu('alter_product_id', $product_array_values, $product_array_items, 0); ?>
                        <td>
                            <?php echo dropdown_menu('alter_machine_id', $machines_in_use_array, $machines_in_use_array, 0); ?>
                        <td> <?php echo dropdown_menu('alter_product_choice', ['machine_id', 'quantity_in_machine', 'best_before'], ['Machine ID', 'Quantity in Machine', 'Best Before'], 1); ?>

                        <td><input name="alter_product_new_value" style="width:100%" placeholder="New Value"/></td>
                        <td><input name="alter_product_submit" type="submit" value="Alter Product"/></td>
                    </tr>
                </table>

                <h3>Add Product</h3>

                <table cellspacing='0' cellpadding='0'>
                    <tr>
                        <th>Product Name
                        <th>Machine ID
                        <th>Quantity
                        <th>Best-Before</th>
                    </tr>
                    <tr>
                        <td>
                            <?php echo dropdown_menu('add_product_name', $product_table_values, $product_table_options, 0); ?></td>
                        <td>
                            <?php echo dropdown_menu('add_vending_machine', $active_machine_array, $active_machine_array, 0); ?></td>
                        <td><input name="new_quantity" style="width:100%" placeholder="Quantity"/></td>
                        <td><input name="new_best_before" style="width:100%" placeholder='2015-01-01' type='date'
                                   value='2015-01-01'/></td>
                        <td><input name="add_product_submit" type="submit" value="Add Product"/></td>
                    </tr>
                </table>

                <h3>Remove Product</h3>
                <table cellspacing='0' cellpadding='0'>
                    <tr>
                        <th>Product Name
                        <th>Vending Machine</th>
                    </tr>
                    <tr>
                        <td>
                            <?php echo dropdown_menu('remove_product_name', $product_array_values, $product_array_items, 0); ?>
                        <td>
                            <?php echo dropdown_menu('remove_vending_machine', $machines_in_use_array, $machines_in_use_array, 1); ?>
                        <td>
                        <td><input name="remove_product_submit" type="submit" value="Remove Product"/></td>
                    </tr>

                </table>


                <h3>Add Machine</h3>
                <table cellspacing='0' cellpadding='0'>
                    <thead>
                    <tr>
                        <th>New Machine ID
                        <th>Building
                        <th>Floor (Optional)</th>
                    <tr>
                    </thead>
                    <tbody>
                    <td><input id='vending_new_machine_id' name="vending_new_machine_id" style="width:100%"
                               placeholder="Machine ID"/>
                    <td><input name="vending_new_machine_building" style="width:100%" placeholder="Building"/>
                    <td><input name="vending_new_machine_floor" style="width:100%" placeholder="Floor"/>
                    <td><input name="vending_new_machine_submit" type="submit" value="Add Machine"/></td>
                    </tr>
                    </tbody>
                </table>

                <h3>Alter Machine</h3>
                <table cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <th>Machine ID</th>
                        <th>Attribute</th>
                        <th>New Value</th>
                    </tr>
                    </thead>
                    <tbody>
                    <td><?php echo dropdown_menu('vending_alter_machine_id', $machines_in_use_array, $machines_in_use_array, 1); ?></td>
                    <td><?php echo dropdown_menu('vending_alter_machine_attribute', ['machine_id', 'building', 'floor'], ['Machine ID', 'Building', 'Floor'], 1) ?></td>
                    <td><input name="vending_alter_machine_value" style="width:100%" placeholder="New Value"/></td>
                    <td><input name="vending_alter_machine_submit" type="submit" value="Alter Machine"/></td>
                    </tbody>
                </table>

                <h3>Remove Machine</h3>
                <table cellpadding='0' cellspacing='0'>
                    <tr>
                        <th>Vending Machine</th>
                    </tr>
                    <tr>
                        <td>

                            <?php
                            echo dropdown_menu('vending_remove_machine_dropdown', $machines_in_use_array, $machines_in_use_array, 0); ?>
                        </td>
                        <td>Confirm?<input type="checkbox" id="remove_vending_checkbox"
                                           onclick="toggle_button('vending_remove_machine_submit')"
                                           id="remove_vending_machine_checkbox" value="true"></td>
                        <td><input id="vending_remove_machine_submit" type="submit" value="Remove Machine"/></td>
                        </button>
                    </tr>


                </table>


        </form>

        <!--</div>-->
    </div>
</div>
<div class="clear"></div>
</div>
<?php
include('./includes/footer.php');
mysql_close($db_handle);
?>

</body>
</html>
