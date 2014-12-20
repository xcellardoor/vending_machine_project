<html>
<head>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <script src="jquery-1.7.2.min.js"></script>
    <script language="javascript" type="text/javascript">
    function display_top_rated() {
            $("#report_section").load("financial_top_rated.php");
    }
    function display_all_sales_total(){
            $("#report_section").load("financial_all_sales_total.php");
    }
    function display_sales_between_dates() {
        var request = $.ajax({
            url: "financial_sales_between_dates.php?older_date="+$('#older_date').val()+"&newer_date="+$('#newer_date').val(),
            type: "GET",
            dataType: "html"
        });

        request.done(function(msg) {
            $("#report_section").html(msg);
        });

        request.fail(function(jqXHR, textStatus) {
            alert( "Request failed: " + textStatus );
        });
    }
    </script>

</head>

<body id="main-body">

<?php
/**
 * Created by PhpStorm.
 * User: cellardoor
 * Date: 18/12/14
 * Time: 10:12
 */

include("menu.php");
include('credentials.php');
$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database,$db_handle);
?>

<div style="float: left; width: 50%">
<div align="center">
    <button type="button" onclick="display_top_rated()">Show Best Sellers</button><br><br>
    <button type="button" onclick="display_all_sales_total()">Total of All Time Sales</button><br><br>
    <input id="older_date" name="older_date" style="width:auto" placeholder="Older Boundary"/>
    <input id="newer_date" name="newer_date" style="width:auto" placeholder="Recent Boundary"/>
    <button type="button" onclick="display_sales_between_dates()">Show Sales Between Dates</button>


    <p>Show Best Selling Products<br>
    Show Best Product This Month<br>
    </p>
</div>
</div>

<div style="float: left; width: 50%">
<div align="center" id="report_section">
<p>test</p>
</div>

</div>


</body>
</html>

<?php
include("footer.php");
?>

</html>