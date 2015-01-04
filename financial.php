<html>
<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="./js/jquery.tablesorter/themes/blue/style.css">
    <script src="js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="./js/jquery.tablesorter/jquery.tablesorter.js"></script>
    <script type="text/javascript">

        $(document).ready(function () {
                $("table").tablesorter();
            }
        );
        $(function () {

            var $sidebar = $("#sorting_section"),
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

        function sort_table() {
            var request = $.ajax({
                url: "financial_table_content.php?sort_type=" + $('#financial_sort_by_dropdown').val() + "&older_date=" + $('#older_date').val() + "&newer_date=" + $('#newer_date').val(),
                type: "GET",
                dataType: "html"
            });

            request.done(function (msg) {
                $("#report_section").html(msg);
                $("table").tablesorter();

            });

            request.fail(function (jqXHR, textStatus) {
                alert("Request failed: " + textStatus);
            });
        }

        function alter_table(argument) {
            switch (argument) {

                case "date_of_sale":
                    var result = "<input id='older_date' placeholder='2015-01-01' type='date' value='2015-01-01'/><input id='newer_date' placeholder='2015-01-01' type='date' value='2015-01-01'/>";
                    document.getElementById('test_area').innerHTML = result;
                    break;

                default:
                    document.getElementById('test_area').innerHTML = "";
            }
        }

        function download_financial_table() {
            /*var a = document.body.appendChild(
             document.createElement("a")
             );*/
            var test = document.getElementById('download_area');
            var new_button = document.createElement("a");
            new_button.download = $('#download_filename').val() + ".html";
            new_button.href = "data:text/html," + document.getElementById("report_section").innerHTML;
            new_button.innerHTML = "Ready - Click Here to Download";

            test.appendChild(new_button);
        }

        //document.getElementById('financial_sort_by_dropdown').addEventListener('change',function(){alert('Hello');});
    </script>
    <title>Financial | Vending Machine Management System</title>
</head>

<body>

<?php

include("./includes/menu.php");
include('./includes/credentials.php');
$db_handle = mysql_connect($server, $user_name, $password);
$db_found = mysql_select_db($database, $db_handle);
?>
<div id="main-body">
    <div align="center"><h1>Financial</h1></div>

    <div style="float: left; width: 50%" id="sorting_section">
        <div align="center">
            <table cellspacing='0' cellpadding='0'>
                <tr>
                    <td>
                        <select id='financial_sort_by_dropdown' onchange="alter_table(this.value)">
                            <option value="sale_number" selected>Sale Number</option>
                            <option value="date_of_sale">Date of Sale</option>
                            <option value="popularity_descending">Product Popularity (Descending)</option>
                            <option value="popularity_ascending">Product_Popularity (Ascending)</option>
                        </select>

                        <button type='button' onclick='sort_table()'>Sort!</button>
                    </td>
                </tr>
            </table>

        </div>
        <div id='test_area' align="center"></div>
        <br><br><br>

        <div id="download_area" align="center">
            <h4>Download Table Content</h4>
            <input type="text" id="download_filename" placeholder="Enter Filename...">
            <button type='button' id="download_button" onclick='download_financial_table()'>Prepare Download</button>
            <br>
        </div>

    </div>


    <div style="float: left; width: 50%">
        <div align="center" id="report_section">

            <script>
                $("#report_section").load("financial_table_content.php");
            </script>
        </div>
        <br>
    </div>

    <div class="clear"></div>
</div>
<?php
include("./includes/footer.php");
?>

</body>
</html>