<?php
    // from index.php
    define('ABS_PATH', str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']) . '/'));

    session_start();

    require_once ABS_PATH . 'config.php';
    require_once LIB_PATH . 'lib.php';


    // sessions
    // user id
    if(isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) $user_id = $_SESSION['user_id'];
    else $user_id = 0;
    // user id
    if(isset($_SESSION['permission']) && $_SESSION['permission'] > 0) $permission = $_SESSION['permission'];
    else $permission = 0;
    // user id
    if(isset($_SESSION['display_name']) && $_SESSION['display_name'] != "") $display_name = $_SESSION['display_name'];
    else $display_name = '';
    // DB connect
    if(isset($_SESSION['db_conn']) && $_SESSION['db_conn'] > 0) $db_conn = $_SESSION['db_conn'];
    else $db_conn = 0;
    // DB host
    if(isset($_SESSION['db_host']) && $_SESSION['db_host'] != "") $db_host = $_SESSION['db_host'];
    else $db_host = '';
    // DB port
    if(isset($_SESSION['db_port']) && $_SESSION['db_port'] > 0) $db_port = $_SESSION['db_port'];
    else $db_port = 0;
    // DB username
    if(isset($_SESSION['db_user']) && $_SESSION['db_user'] != "") $db_user = $_SESSION['db_user'];
    else $db_user = '';
    // DB password
    if(isset($_SESSION['db_pass']) && $_SESSION['db_pass'] != "") $db_pass = $_SESSION['db_pass'];
    else $db_pass = '';
    // DB database
    if(isset($_SESSION['db_database']) && $_SESSION['db_database'] != "") $db_database = $_SESSION['db_database'];
    else $db_database = '';
    // last post save parameters
    if(isset($_SESSION['last_post']) && $_SESSION['last_post'] != "") $last_post = $_SESSION['last_post'];
    else $last_post = '';
    // next page after redirect
    // if(isset($_SESSION['next_page']) && $_SESSION['next_page'] != "") $next_page = $_SESSION['next_page'];
    // else $next_page = '';


    // GET parameters
    // page
    if(isset($_GET['page']) && $_GET['page'] != "") $page = $_GET['page'];
    else $page = "home";
    // page type
    if(isset($_GET['type']) && $_GET['type'] != '') $page_type = $_GET['type'];
    else $page_type = "";
    // id
    if(isset($_GET['id']) && $_GET['id'] > 0) $id = $_GET['id'];
    else $id = 0;
    // cron id
    if(isset($_GET['cron_id']) && $_GET['cron_id'] != "") $cron_id = $_GET['cron_id'];
    else $cron_id = "";

    // check cmd
    $cmd_gets = check_command();
    if(!empty($cmd_gets)){
        // page
        if(isset($cmd_gets['page']) && $cmd_gets['page'] != "") $page = $cmd_gets['page'];
        else $page = "home";
        // page type
        if(isset($cmd_gets['type']) && $cmd_gets['type'] != '') $page_type = $cmd_gets['type'];
        else $page_type = "";
        // id
        if(isset($cmd_gets['id']) && $cmd_gets['id'] > 0) $id = $cmd_gets['id'];
        else $id = 0;
        // cron id
        if(isset($cmd_gets['cron_id']) && $cmd_gets['cron_id'] != "") $cron_id = $cmd_gets['cron_id'];
        else $cron_id = "";
    }
    
    // router
    require_once LIB_PATH . 'route.php';

?>