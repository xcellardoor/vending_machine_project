<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id();
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
    <script type="text/javascript" src="./includes/shared_javascript_functions.js"></script>
    <script type="text/javascript">

        //Apply the tablesorter plugin to any table using the class, as soon as the page is finished loading.
        $(document).ready(function () {
                $("table").tablesorter();
            }
        );

        //Constantly active function used for the dynamic scrolling of the database control elements, defined in shared_javascript_functions.js
        $(function(){
            sidebar_follow_user_script('#sorting_section');
        })

        //Javascript SWITCH-CASE used to manipulate the DOM and provide extra filter controls and input boxes as and when they are needed, rather than cluttering up the page all the time.
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
                case "machine_id":
                    var result = "<input id='machine_id' placeholder='Machine ID?' type='text'>";
                    document.getElementById('filter_options').innerHTML = result;
                    break;
                default:
                    document.getElementById('filter_options').innerHTML = "";
            }
        }

        //Javascript function which calls to financial_table_content.php and passes arguments, and then takes any responses and repopulates the div that holds the table. Essentially the backbone of the searching functionality.
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

        //Script used to provide the user with the ability to download the financial table currently on screen.
        function download_financial_table() {
            var position_in_document = document.getElementById('download_area'); //Get the download area element.
            var new_button = document.createElement("a"); //Create a new hyperlink
            new_button.download = $('#download_filename').val() + ".html"; //Create the download filename
            new_button.href = "data:text/html," + document.getElementById("report_section").innerHTML; //Populate the link with the data.
            new_button.innerHTML = "Ready - Click Here to Download"; //Apply a title to the link

            position_in_document.appendChild(new_button);
            $("#download_button").prop('disabled', true);

        }
    </script>
    <title>Financial | Vending Machine Management System</title>
</head>

<body>

<?php
include("./includes/menu.php"); //Include the Menu as usual.
?>

<div id="main-body">
    <div class="page_function_title"><h1>Financial</h1></div>

    <div style="float: left; width: 66%">
        <div align="center" id="report_section">

            <!--Javascript used to request the financial_table_content.php file and populate the table div automatically during page load.-->
            <script>
                $("#report_section").load("financial_table_content.php");
            </script>
        </div>
        <br>
    </div>

    <!--Create the right-hand side sorting section controls-->
    <div style="float: left; width: 33%" id="sorting_section">
        <div align="center">
            <p>Filter Controls</p>
            <table cellspacing='0' cellpadding='0'>
                <tr>
                    <td>
                        <!--Dropdown box of sorting choices-->
                        <select id='financial_sort_by_dropdown' onchange="filter_selections(this.value)">
                            <option value="no_filter" selected>No Filter...</option>
                            <option value="name">Name</option>
                            <option value="between_dates">Between Dates</option>
                            <option value="popularity">Product Popularity</option>
                            <option value="machine_id">Machine ID (Coming soon in Software Assignment)</option>
                        </select>
                        <!--Provide the button to trigger the sorts!-->
                        <button type='button' onclick='filter_table()'>Filter!</button>
                    </td>
                </tr>
            </table>
        </div>
        <div id='filter_options' align="center"></div>
        <hr class="adjustment_hr">

        <!--Create the download area and buttons-->
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