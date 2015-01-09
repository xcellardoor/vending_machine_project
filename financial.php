<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if ($_SESSION['authenticated'] != "true") {
    header("location:./authentication/login.php");
} else {
}
?>
<html>
<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
    <link rel="stylesheet" type="text/css" href="./js/jquery.tablesorter/themes/blue/style.css">
    <link rel="icon" type="image/png" href="./includes/icon.png">
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

        function filter_selections(argument) {
            switch (argument) {
                case "between_dates":
                    var result = "<input id='between_dates_older_date' placeholder='Older Date?' type='date' value='2015-01-01'><input id='between_dates_newer_date' placeholder='Newer Date?' type='date' value='2015-01-01'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "popularity":
                    var result = "<input id='popularity_older_date' placeholder='Older Date?' type='date' value='2015-01-01'><input id='popularity_newer_date' placeholder='Newer Date?' type='date' value='2015-01-01'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                case "name":
                    var result = "<input id='name_value' placeholder='Name?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                default:
                    document.getElementById('filter_options').innerHTML = "";
            }
        }

        function filter_table() {
            var request = $.ajax({
                url: "financial_table_content.php?financial_sort_by_dropdown=" + $('#financial_sort_by_dropdown').val() + "&between_dates_older_date=" + $('#between_dates_older_date').val() + "&between_dates_newer_date=" + $('#between_dates_newer_date').val() + "&popularity_older_date=" + $('#popularity_older_date').val() + "&popularity_newer_date=" + $('#popularity_newer_date').val() + "&name_value=" + $('#name_value').val(),
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
?>

<div id="main-body">
    <div class="page_function_title"><h1>Financial</h1></div>

    <div style="float: left; width: 66%">
        <div align="center" id="report_section">

            <script>
                $("#report_section").load("financial_table_content.php");
            </script>
        </div>
        <br>
    </div>

    <div style="float: left; width: 33%" id="sorting_section">
        <div align="center">
            <p>Filter Controls</p>
            <table cellspacing='0' cellpadding='0'>
                <tr>
                    <td>
                        <select id='financial_sort_by_dropdown' onchange="filter_selections(this.value)">
                            <option value="no_filter" selected>No Filter...</option>
                            <option value="name">Name</option>
                            <option value="between_dates">Between Dates</option>
                            <option value="popularity">Product Popularity</option>
                        </select>

                        <button type='button' onclick='filter_table()'>Filter!</button>
                    </td>
                </tr>
            </table>
        </div>
        <div id='filter_options' align="center"></div>
        <br><br><br>

        <div id="download_area" align="center">
            <h4>Download Table Content</h4>
            <input type="text" id="download_filename" placeholder="Enter Filename...">
            <button type='button' id="download_button" onclick='download_financial_table()'>Prepare Download</button>
            <br>
        </div>

    </div>


    <div class="clear"></div>
</div>
<?php
include("./includes/footer.php");
?>

</body>
</html>