<html>

<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
</head>
<title>Vending Machine Management System</title>

<body>

<?php

include('./includes/menu.php');
?>
<div id="main-body">


    <section id="home"><h1>Welcome to the Vending Machine Management System</h1>

        <p>Thank you for trialling the pre-release version of the Vending Management Solution from S162320 Management
            Systems Ltd. We ask that you please report any anomalous or undesired behaviour to the sales representative
            who provided you with this pre-release copy of our software. To thank you for your participation, you will
            be given the opportunity to purchase the software for 50% the regular sales price when the final version is
            released for sale.</p>

        <div align="center">
            <button id="vending_button" type="button" style="width:100px;height:100px;">Vending</button>
            <button id="vending_button" type="button" style="width:100px;height:100px;">Stockroom</button>
            <button id="vending_button" type="button" style="width:100px;height:100px;">Financial</button>
        </div>

    </section>

</div>
<?php
include('./includes/footer.php');

?>

</body>

</html>