<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Graphical Search Results with Filters</title>
        <link rel="stylesheet" href="web/modal.css" type="text/css" media="screen" />
		
        <link rel="stylesheet" href="web/pubtype-icons.css" />
        <link rel="shortcut icon" href="web/favicon.ico" />
        <script type="text/javascript" src="web/placard.js" ></script>
        <script type="text/javascript" src="web/daterange.js" ></script>
<!--        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" ></script>-->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

   <script defer src="//use.fontawesome.com/releases/v5.11.2/js/solid.js"></script>
    <script defer src="//use.fontawesome.com/releases/v5.11.2/js/fontawesome.js"></script>


<!--<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
<link href="//netdna.bootstrapcdn.com/font-awesome/5.11.2/css/font-awesome.css" rel="stylesheet">        -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css">

        <!-- jQuery UI supports both autocomplete and date slider-->
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">    
		
		<script src="web/page_javascript.js"></script>
        <link rel="stylesheet" href="web/styles.css" type="text/css" media="screen" />
        <link rel="stylesheet" href="web/custom_styles.css" type="text/css" media="screen" />

    </head>

    <body>
        <div class="container">
        <div class="content">
            <?php echo $content; ?>
        </div>
		</div>
<?php 
$xml ="Config.xml";
$dom = new DOMDocument();
$dom->load($xml);  
$version = $dom ->getElementsByTagName('Version')->item(0)->nodeValue;
?>

        <?php
        if(isset($_SESSION['autocomplete']) && $_SESSION['autocomplete'] == 'y'){
        echo '<script>';
        include_once('web/autocomplete.js.php');
        echo '</script>';
        }
        ?>
    </body>
</html>
