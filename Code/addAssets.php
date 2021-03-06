<?php
require_once("resources/userAdmin.php");
/* For phase 2
require_once '../resources/ticketGroup.php';
require_once '../resources/dbConn.php'; 
*/
$page = "add";

?>

<!doctype html>
<html lang="en">
<head>

        <meta charset="utf-8" />
    <link rel="icon" type="image/png" href="img/favicon.ico">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>Add Assets - AMS</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />


    <!-- Bootstrap core CSS     -->
    <link href="css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="css/animate.min.css" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="css/light-bootstrap-dashboard.css" rel="stylesheet"/>


    <!--  CSS for Demo Purpose, don't include it in your project     -->
    <link href="css/demo.css" rel="stylesheet" />


    <!--     Fonts and icons     -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="css/pe-icon-7-stroke.css" rel="stylesheet" />

</head>
<body>

<div class="wrapper">

    <?php 
        require_once("Views/nav/index.php");
        require_once("Views/addAssets/index.php");
     ?>

    
</div>


</body>

    <!--   Core JS Files   -->
    <script src="js/jquery-1.10.2.js" type="text/javascript"></script>
    <script src="js/bootstrap.min.js" type="text/javascript"></script>

    <!--  Checkbox, Radio & Switch Plugins -->
    <script src="js/bootstrap-checkbox-radio-switch.js"></script>

    <!--  Charts Plugin -->
    <script src="js/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="js/bootstrap-notify.js"></script>

    <!-- Light Bootstrap Table Core javascript and methods for Demo purpose -->
    <script src="js/light-bootstrap-dashboard.js"></script>

    <script type="text/javascript">
        $(document).ready(function(){


            $.notify({
                icon: 'pe-7s-tools',
                message: "<b>We are currently working on this site, all content is subject to change."

            },{
                type: 'danger',
                timer: 4000
            });

        });
        $(function (){
            $("#assetType").change(function (){
                var type = document.getElementById("assetType").value;
                if (type === "Computer" || type === "Laptop"){
                    //$(".hideComputer").css({'display': 'block'});
                    $(".hideComputer").removeAttr("style", {'display':'none'});
                    $("#hiddenComputer").prop('required', true);
                }else{
                    $(".hideComputer").css({'display':'none'});
                    $("#hiddenComputer").prop('required', false);
                }
            });
        });
    </script>

</html>
