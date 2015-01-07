<?php if (session_status() === PHP_SESSION_NONE){session_start();}?>
<html>

<head>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">


    <?php echo '<!--';
    print_r($_COOKIE);

    var_dump($_SESSION);
    echo '-->';


    ?>
    </head>
<title>Vending Machine Management System</title>

<body>

<?php




include('./includes/menu.php');
if ($_SESSION['authenticated'] != "true") {
    header("location:./authentication/login.php");
} else {}

?>
<div id="main-body">


    <section id="home"><h1>Welcome to the Vending Machine Management System</h1>

        <p>Thank you for trialling the pre-release version of the Vending Management Solution from S162320 Management
            Systems Ltd. We ask that you please report any anomalous or undesired behaviour to the sales representative
            who provided you with this pre-release copy of our software. To thank you for your participation, you will
            be given the opportunity to purchase the software for 50% the regular sales price when the final version is
            released for sale.</p>

        <div align="center">
            <input TYPE="button" onClick="parent.location='./vending.php'" style="width:100px;height:100px;" value="Vending"/>
            <input TYPE="button" onClick="parent.location='./stockroom.php'" style="width:100px;height:100px;" value="Stockroom"/>
            <input TYPE="button" onClick="parent.location='./orders.php'" style="width:100px;height:100px;" value="Orders"/>
            <input TYPE="button" onClick="parent.location='./financial.php'" style="width:100px;height:100px;" value="Financial"/>
        </div>

    </section>

</div>
<?php
include('./includes/footer.php');

?>

</body>

</html>