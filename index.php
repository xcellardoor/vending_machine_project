<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id();
} ?>
<html>
<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
</head>
<title>Vending Machine Management System</title>

<body>

<?php
include('./includes/menu.php');
if ($_SESSION['authenticated'] != "true") {
    header("location:./authentication/login.php");
} else {
}

?>
<!--Main DIV-->
<div id="main-body">
    <section id="home"><h1>Welcome to the Vending Machine Management System</h1>

        <p>Thank you for trialling the pre-release version of the Vending Management Solution from S162320 Management
            Systems Ltd. We ask that you please report any anomalous or undesired behaviour to the testing representative
            who provided you with this pre-release copy of our software. To thank you for your participation, you will
            be given the opportunity to purchase the software for <b><em>half price</em></b> when the final version is
            released for sale.</p>

        <div class="landing_page_buttons">
            <input TYPE="button" onClick="parent.location='./vending.php'" value="Vending"/>
            <input TYPE="button" onClick="parent.location='./stockroom.php'" value="Stockroom"/>
            <input TYPE="button" onClick="parent.location='./orders.php'" value="Orders"/>
            <input TYPE="button" onClick="parent.location='./financial.php'" value="Financial"/>
        </div>
    </section>

</div>
<?php
include('./includes/footer.php');
?>

</body>

</html>