<html>
<head>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
    <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
    <script>
    function display_top_rated() {
            $("#report_section").load("financial_top_rated.php");
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

    <button type="button" onclick="display_top_rated()">Top Rated</button>

    <p>Show Best Selling Products<br>
    Show Best Product This Month<br>
    </p>
</div>

<div style="float: left; width: 50%">
<div id="report_section">
<p>test</p>
</div>

</div>


</body>
</html>

<?php
include("footer.php");
?>

</html>