<html>
<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <script src="jquery-1.7.2.min.js"></script>
    <script type="text/javascript">
    function sort_table() {
        request = $.ajax({
            url: "financial_table_content.php?sort_type="+$('#financial_sort_by_dropdown').val()+"&older_date="+$('#older_date').val()+"&newer_date="+$('#newer_date').val(),
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

    function alter_table(argument){
        switch(argument){

            case "date_of_sale":
                    result="<input id='older_date' placeholder='2015-01-01' type='date' value='2015-01-01'/><input id='newer_date' placeholder='2015-01-01' type='date' value='2015-01-01'/>";
                    document.getElementById('test_area').innerHTML=result;
                break;

            default:
                document.getElementById('test_area').innerHTML="blah";
        }
    }

    //document.getElementById('financial_sort_by_dropdown').addEventListener('change',function(){alert('Hello');});
    </script>
<title>Financial | Vending Machine Management System</title>
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

<div align="center"><h1>Financial</h1></div>

<div style="float: left; width: 50%">
<div align="center">
    <table cellspacing='0' cellpadding='0'><tr><td>
                <select id='financial_sort_by_dropdown' onchange="alter_table(this.value)">
                    <option value="sale_number" selected>Sale Number</option>
                    <option value="date_of_sale">Date of Sale</option>
                </select>

    <button type='button' onclick='sort_table()'>Sort!</button></td></tr>
    </table>

</div>
    <div id='test_area' align="center">test</div>

</div>

<div style="float: left; width: 50%">
<div align="center" id="report_section">



<script>
    $("#report_section").load("financial_table_content.php");
</script>
</div>
</div>

</body>


<?php
include("footer.php");
?>

</html>