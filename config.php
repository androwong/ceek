<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');


	/**
	 * URL & Path settings
	 */
	
	define('LIB_PATH', ABS_PATH . 'lib/');
	define('VIEW_PATH', ABS_PATH . 'views/');

	define('COMPONENT_PATH', VIEW_PATH . 'components/');
	define('LAYOUT_PATH', VIEW_PATH . 'layouts/');
	define('PAGE_PATH', VIEW_PATH . 'pages/');


	// define('WEB_HOST', '139.99.174.9:3131');
	define('WEB_HOST', 'ceek.localhost');
	// define('WEB_PATH', 'http://'.WEB_HOST.'/ceek/');
	define('WEB_PATH', 'http://'.WEB_HOST.'/');
	define('ASSET_PATH', WEB_PATH . 'assets/');
	
	define('JS_PATH', ASSET_PATH . 'js/');
	define('CSS_PATH', ASSET_PATH . 'css/');
	define('IMAGE_PATH', ASSET_PATH . 'images/');

	
	/**
	 * Database settings
	 */
	define('CHARSET', 'utf8');

	/** MySQL database name */
	define('DB_NAME', 'ceek_panel');

	/** MySQL database username */
	define('DB_USER', 'root');

	/** MySQL database password */
	define('DB_PASSWORD', '');

	/** MySQL hostname */
	define('DB_HOST', 'localhost');

	/** MySQL port - undefined */
	define('DB_PORT', 0);


	/** log is saved file or db? */
	define('FLAG_SAVE_LOG_FILE', false);


	/** youtube site host url */
	define('YOUTUBE_HOST', 'https://www.youtube.com');

	
	/** default checking stream time [minutes] */
	define('DEFAULT_CHECK_STREAM_TIME', 5);
	/** default phone number count */
	define('DEFAULT_PHONE_COUNT', 5);


	/** when add new stream, filename is created from sort, or filename */
	define('STREAM_SORT_FILENAME', false);


	/** YouTube downloading uploaded end time & duration limit time */
	define('YOUTUBE_END_TIME', 86400);				// 24 hours
	define('YOUTUBE_DUR_TIME', 2100);				// 35 mins


	/** MySQL tables */
	$table_local_users = "users";
	$table_local_dbs = "dbs";
	$table_local_pages = "pages";
	$table_local_logs = "customer_logs";
	$table_local_categ = "category";
	$table_local_tv = "tv_series";
	$table_local_youtube = "youtubes";
	$table_local_manage_youtube = "manage_youtube";
	$table_local_cron_jobs = "cron_jobs";
	$table_local_cron_logs = "cron_logs";
	$table_local_settings = "settings";
?>