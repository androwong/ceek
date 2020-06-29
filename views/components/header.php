<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="Neon Admin Panel" />
	<meta name="author" content="" />

	<link rel="icon" href="assets/images/favicon.ico">

	<title><?php echo $page_title; ?></title>

	<link rel="stylesheet" href="<?php echo JS_PATH ?>jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css">
	<link rel="stylesheet" href="<?php echo CSS_PATH ?>font-icons/entypo/css/entypo.css">
	<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
	<link rel="stylesheet" href="<?php echo CSS_PATH ?>bootstrap.css">
	<link rel="stylesheet" href="<?php echo CSS_PATH ?>neon-core.css">
	<link rel="stylesheet" href="<?php echo CSS_PATH ?>neon-theme.css">
	<link rel="stylesheet" href="<?php echo CSS_PATH ?>neon-forms.css">
	<link rel="stylesheet" href="<?php echo CSS_PATH ?>custom.css">

	<script src="<?php echo JS_PATH ?>jquery-1.11.3.min.js"></script>

	<!--[if lt IE 9]><script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    
    <!-- Imported styles on this page -->
    <?php
        if(isset($css_files) && count($css_files) > 0){
            foreach($css_files as $css){
                echo '<link rel="stylesheet" href="'.$css.'">';
            }
        }
    ?>

</head>

<body class="page-body <?php echo $class_body; ?>" data-url="http://neon.dev">