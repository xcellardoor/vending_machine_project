<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>
<!DOCTYPE HTML>
<html>

<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="./js/jquery.tablesorter/themes/blue/style.css">
    <link rel="icon" type="image/png" href="./includes/icon.png">
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

        function create_order_from_low_stock() {
            var request = $.ajax({
                url: "order_table_contents.php?create_from_low_stock=1",
                type: "GET",
                dataType: "html"
            });

            request.done(function (msg) {
                $("#order_compose_content").html(msg);
            });

            request.fail(function (jqXHR, textStatus) {
                alert("Request failed: " + textStatus);
            });
        }

        function download_order_table() {
            /*var a = document.body.appendChild(
             document.createElement("a")
             );*/
            var test = document.getElementById('download_area');
            var new_button = document.createElement("a");
            new_button.download = $('#download_filename').val() + ".html";
            new_button.href = "data:text/html," + document.getElementById("order_compose_content").innerHTML;
            new_button.innerHTML = "Ready - Click Here to Download";

            test.appendChild(new_button);
        }
    </script>

</head>
<title>Orders | Vending Machine Management System</title>

<body>


<?php

include "./includes/menu.php";

if ($_SESSION['authenticated'] != "true") {
    header("location:./authentication/login.php");
} else {
}

include "./includes/shared_php_functions.php";
include "./includes/credentials.php";

?>

<div id="main-body">
    <div align="center"><h1>Order Management</h1></div>

    <div style="float: left; width: 66%" align="center">

        <table class='tablesorter' id="order">
            <input type="text" placeholder="Order Number" id="order_history_order_number"/>
            <button id='order_window_button' type="button" onclick="open_order_window()">View Past Order</button>
            <?php
            include('./includes/credentials.php');

            $db_handle = mysql_connect($server, $user_name, $password);
            $db_found = mysql_select_db($database, $db_handle);


            #$conn = new mysqli($server, $user_name,)

            if ($db_found) {
                $SQL = "SELECT * FROM order_history INNER JOIN supplier_table ON order_history.supplier_id=supplier_table.supplier_id ORDER BY order_number;";
                $result = mysql_query($SQL);
                print "<thead><tr><th>Order Number<th>Supplier<th>Order Reference from Supplier<th>Order Date</th></tr></thead><tbody>";
                while ($db_field = mysql_fetch_assoc($result)) {
                    print "<tr><td>" . $db_field['order_number'] . "<td>" . $db_field['supplier_name'] . "<td>" . $db_field['order_reference_from_supplier'] . "<td>" . $db_field['order_date'];
                }
                //MYSQL CLOSE could go here
            } else {
                print "Database Access Error!";
                mysql_close($db_handle);

            }
            ?>
            </tbody>
        </table>

    </div>

    <div style='float: left; width: 33%' align=center id="past_orders">
        <h3>Order Creation</h3>


        <button id="create_order_from_low_stock_button" onclick="create_order_from_low_stock()">Auto-Create Order from
            Low Stock
        </button>

        <br><br><form name=order_creation' method='post' action='post.php'>

        </form>

        <div id="order_compose_content"></div>

        <div id="download_area" align="center">
            <input type="text" id="download_filename" placeholder="Enter Filename...">
            <button type='button' id="download_button" onclick='download_order_table()'>Prepare Download</button>
        </div>
        <br>


        <h3>Suppliers</h3>
        <button onclick="show_suppliers()">Click to show suppliers</button>
        <div id="supplier_section"></div>

    </div>


<div class="clear"></div>
</div>
<?php
include('./includes/footer.php');
mysql_close($db_handle);
?>

</body>

</html>
