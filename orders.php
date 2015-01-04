<!DOCTYPE HTML>
<html>

<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="./js/jquery.tablesorter/themes/blue/style.css">
    <script type="text/javascript" src="./js/jquery-1.7.2.min.js"></script>

    <script type="text/javascript" src="./js/jquery.tablesorter/jquery.tablesorter.js"></script>

    <script>

        $(document).ready(function () {
                $("table").tablesorter();
            }
        );

        function open_order_window(id) {
            newwindow = window.open('./order_history_window.php?order_number=' + $('#order_history_order_number').val(), 'name', 'height=600,width=800');
            if (window.focus) {
                newwindow.focus()
            }
        }
    </script>

</head>
<title>Stockroom | Vending Machine Management System</title>

<body>


<?php

include "./includes/menu.php";
include "./includes/shared_php_functions.php";
include "./includes/credentials.php";

?>

<div id="main-body">
    <div align="center"><h1>Order Management</h1></div>

    <div style='float: left; width: 50%' align=center id="past_orders">
        <p>holder content</p>

        <form name=order_creation' method='post' action='post.php'>
            <input/>
            <input/>
            <input/>


        </form>

    </div>
    <div style="float: left; width: 50%" align="center">

        <table class='tablesorter' id="order">
            <input type="text" placeholder="Order Number" id="order_history_order_number"/>
            <button id='order_window_button' type="button" onclick="open_order_window()">View Past Order</button>
            <?php
            include('./includes/credentials.php');

            $db_handle = mysql_connect($server, $user_name, $password);
            $db_found = mysql_select_db($database, $db_handle);


            #$conn = new mysqli($server, $user_name,)

            if ($db_found) {
                $SQL = "SELECT * FROM order_history";
                $result = mysql_query($SQL);
                print "<thead><tr><th>Order Number<th>Supplier ID<th>Order Reference from Supplier<th>Order Date</th></tr></thead><tbody>";
                while ($db_field = mysql_fetch_assoc($result)) {
                    print "<tr><td>" . $db_field['order_number'] . "<td>" . $db_field['supplier_id'] . "<td>" . $db_field['order_reference_from_supplier'] . "<td>" . $db_field['order_date'];
                }
                //MYSQL CLOSE could go here
            } else {
                print "Database Access Error!";
                mysql_close($db_handle);

            }
            ?>
            </tbody>
        </table>
        <br>


        <h3>Suppliers</h3>
        <button onclick="show_suppliers()">Click to show suppliers</button>
        <div id="supplier_section"
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
