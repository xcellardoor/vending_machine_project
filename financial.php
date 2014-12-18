<html>
<head>
    <link rel="stylesheet" type="text/css" href="stylesheet.css"
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

?>

<div style="float: left; width: 50%">

<p>Show Best Selling Products<br>
Show Best Product This Month<br>
</p>
</div>

<div style="float: left; width: 50%">
<div id="report_section">
<p>test</p>
</div>

</div>

<button type="button"
        onclick="document.getElementById('report_section').innerHTML = 'Hello JavaScript!'">
    Click Me!</button>

</body>
</html>

<?php
include("footer.php");
?>

</html>