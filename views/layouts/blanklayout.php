<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

	include_once(COMPONENT_PATH."header.php"); 
	include_once(PAGE_PATH.$page.".php"); 
	include_once(COMPONENT_PATH."footer.php"); 

?>