<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    // initialize
    $js_files = array();
    $css_files = array();
    $flag_mainlayout = true;            // main layout
    if(!isset($flag_ajax_response)) $flag_ajax_response = false;        // ajax response
    $flag_redirect_login = true;        // redirect to login page
    $flag_redirect_info = true;         // redirect to info page
    $flag_breadcrumb = true;            // admin user
    $class_body = "";
    $page_title = "Ceek";               // page title
    $message = "";                      // alert message content
    $all_pages = array();               // all pages
    $current_page = array();            // current page
    $pages_used_sql = array();          // all pages using sql
    $permissionPages = array();         // pages according to permission
    $default_permission_page = array(); // default page according to permission

    
    // get all page infos
    if($pdo1){
        $all_pages = select_rows($pdo1, $table_local_pages, " 1 ORDER BY `parent_id` ASC, `sort` ASC");
    }
    if(!$pdo1 || empty($all_pages)) die("Database Connection & Loading page infos occur errors.");


    foreach($all_pages as $p){
        if($p['db']) $pages_used_sql[] = $p['name'];
        $permissions_temp = explode(",", $p['permission']);
        foreach($permissions_temp as $temp){
            if(!isset($permissionPages[$temp])) $permissionPages[$temp] = array();
            $permissionPages[$temp][] = $p['name'];
        }
        if($p['is_default']){
            $default_permission_page[$p['is_default']] = $p['name'];
        }
    }

    
    // get user
    if($user_id > 0) $flag_redirect_login = false;
    else if($cron_id != ""){
        // cron without log in
        $users = select_rows($pdo1, $table_local_users, "`cron_id` LIKE '".$cron_id."';");
        if(!empty($users)){
            $users = $users[0];
            $user_id = $users['id'];
            $permission = $users['permission'];
            $display_name = $users['display_name'];
            $flag_redirect_login = false;
        }
    }
    // get database information
    $pdo = null;
    if(in_array($page, $pages_used_sql)){
        getConnectionInfoFromFile();
        if($db_conn > 0){
            $pdo = db_connect($db_host, $db_port, $db_user, $db_pass, $db_database);
            if($pdo) $flag_redirect_info = false;
            // $flag_redirect_info = false;
        }
    }

    // redirect
    if($flag_redirect_login){
        // $_SESSION['next_page'] = $page;
        $next_page = $page;
        $page = "login";
    }
    else if($flag_redirect_info && in_array($page, $pages_used_sql)){
        // $_SESSION['next_page'] = $page;
        $next_page = $page;
        $page = "info";
    }
    else{
        // $_SESSION['next_page'] = "";
        $next_page = "";
    }

    // next page
    // if(!$flag_redirect_login && !$flag_redirect_info && $page == "" && $next_page != "" && !in_array($next_page, array("login", "logout", "info"))){
    //     $page = $next_page;
    // }

    // permission -> redirect
    $page = getPermissionOfPage($page);

    // route
    switch($page){
        case "login":
            $css_files = array(
            );
            $js_files = array(
                JS_PATH . "neon-login.js"
            );
            $flag_mainlayout = false;
            if($page_type == "sample-login-form" || $page_type == "logout") $flag_ajax_response = true;
            $class_body = "login-page login-form-fall";
            break;
        case "logout":
            session_destroy();
            redirect("index.php");
            break;
        case "info":
            $css_files = array(
            );
            $js_files = array(
                JS_PATH . "neon-database.js"
            );
            // $flag_mainlayout = false;
            if($page_type == "sample-connect-form") $flag_ajax_response = true;
            $class_body = "login-page login-form-fall";
            break;
        case "add_mag":
            $css_files = array(
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css",
                JS_PATH . "selectboxit/jquery.selectBoxIt.css",
                JS_PATH . "daterangepicker/daterangepicker-bs3.css",
                JS_PATH . "icheck/skins/minimal/_all.css",
                JS_PATH . "icheck/skins/square/_all.css",
                JS_PATH . "icheck/skins/flat/_all.css",
                JS_PATH . "icheck/skins/futurico/futurico.css",
                JS_PATH . "icheck/skins/polaris/polaris.css"
            );
            $js_files = array(
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "bootstrap-tagsinput.min.js",
                JS_PATH . "typeahead.min.js",
                JS_PATH . "selectboxit/jquery.selectBoxIt.min.js",
                JS_PATH . "bootstrap-datepicker.js",
                JS_PATH . "bootstrap-timepicker.min.js",
                JS_PATH . "bootstrap-colorpicker.min.js",
                JS_PATH . "moment.min.js",
                JS_PATH . "daterangepicker/daterangepicker.js",
                JS_PATH . "jquery.multi-select.js",
                JS_PATH . "icheck/icheck.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "insert") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "add_streams":
            $css_files = array(
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css",
                JS_PATH . "selectboxit/jquery.selectBoxIt.css",
                JS_PATH . "daterangepicker/daterangepicker-bs3.css",
                JS_PATH . "icheck/skins/minimal/_all.css",
                JS_PATH . "icheck/skins/square/_all.css",
                JS_PATH . "icheck/skins/flat/_all.css",
                JS_PATH . "icheck/skins/futurico/futurico.css",
                JS_PATH . "icheck/skins/polaris/polaris.css"
            );
            $js_files = array(
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "bootstrap-tagsinput.min.js",
                JS_PATH . "typeahead.min.js",
                JS_PATH . "selectboxit/jquery.selectBoxIt.min.js",
                JS_PATH . "bootstrap-datepicker.js",
                JS_PATH . "bootstrap-timepicker.min.js",
                JS_PATH . "bootstrap-colorpicker.min.js",
                JS_PATH . "moment.min.js",
                JS_PATH . "daterangepicker/daterangepicker.js",
                JS_PATH . "jquery.multi-select.js",
                JS_PATH . "icheck/icheck.min.js",
                JS_PATH . "neon-chat.js"
            );
            $class_body = "";
            break;
        case "add_series":
            $css_files = array(
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css",
                JS_PATH . "selectboxit/jquery.selectBoxIt.css",
                JS_PATH . "daterangepicker/daterangepicker-bs3.css",
                JS_PATH . "icheck/skins/minimal/_all.css",
                JS_PATH . "icheck/skins/square/_all.css",
                JS_PATH . "icheck/skins/flat/_all.css",
                JS_PATH . "icheck/skins/futurico/futurico.css",
                JS_PATH . "icheck/skins/polaris/polaris.css"
            );
            $js_files = array(
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "bootstrap-tagsinput.min.js",
                JS_PATH . "typeahead.min.js",
                JS_PATH . "selectboxit/jquery.selectBoxIt.min.js",
                JS_PATH . "bootstrap-datepicker.js",
                JS_PATH . "bootstrap-timepicker.min.js",
                JS_PATH . "bootstrap-colorpicker.min.js",
                JS_PATH . "moment.min.js",
                JS_PATH . "daterangepicker/daterangepicker.js",
                JS_PATH . "jquery.multi-select.js",
                JS_PATH . "icheck/icheck.min.js",
                JS_PATH . "neon-chat.js"
            );
            $class_body = "";
            break;
        case "manage_mag":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "jquery.multi-select.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "show" || $page_type == "edit" || $page_type == "update") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "manage_vod":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "jquery.multi-select.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "show") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "add_vod":
            $css_files = array(
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css",
                JS_PATH . "selectboxit/jquery.selectBoxIt.css",
                JS_PATH . "daterangepicker/daterangepicker-bs3.css",
                JS_PATH . "icheck/skins/minimal/_all.css",
                JS_PATH . "icheck/skins/square/_all.css",
                JS_PATH . "icheck/skins/flat/_all.css",
                JS_PATH . "icheck/skins/futurico/futurico.css",
                JS_PATH . "icheck/skins/polaris/polaris.css"
            );
            $js_files = array(
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "bootstrap-tagsinput.min.js",
                JS_PATH . "typeahead.min.js",
                JS_PATH . "selectboxit/jquery.selectBoxIt.min.js",
                JS_PATH . "bootstrap-datepicker.js",
                JS_PATH . "bootstrap-timepicker.min.js",
                JS_PATH . "bootstrap-colorpicker.min.js",
                JS_PATH . "moment.min.js",
                JS_PATH . "daterangepicker/daterangepicker.js",
                JS_PATH . "jquery.multi-select.js",
                JS_PATH . "icheck/icheck.min.js",
                JS_PATH . "neon-chat.js"
            );
            $class_body = "";
            break;
        case "tv_series":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "insert" || $page_type == "show" || $page_type == "auto") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "offline_streams":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "show") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "manage_streams":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "show") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "check_streams":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            $class_body = "";
            break;
        case "setting_check_stream":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "save") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "show_atv_star":
        case "show_atv_kanald":
        case "show_atv_fox":
        case "show_atv_show":
        case "show_atv_dizibox":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "refresh") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "edit_youtube":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "save") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "manage_youtube":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "remove" || $page_type == "auto") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "show_youtube":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            $class_body = "";
            break;
        case "setting_youtube":
            $css_files = array(
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css",
                JS_PATH . "selectboxit/jquery.selectBoxIt.css",
                JS_PATH . "daterangepicker/daterangepicker-bs3.css",
                JS_PATH . "icheck/skins/minimal/_all.css",
                JS_PATH . "icheck/skins/square/_all.css",
                JS_PATH . "icheck/skins/flat/_all.css",
                JS_PATH . "icheck/skins/futurico/futurico.css",
                JS_PATH . "icheck/skins/polaris/polaris.css"
            );
            $js_files = array(
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "bootstrap-tagsinput.min.js",
                JS_PATH . "typeahead.min.js",
                JS_PATH . "selectboxit/jquery.selectBoxIt.min.js",
                JS_PATH . "bootstrap-datepicker.js",
                JS_PATH . "bootstrap-timepicker.min.js",
                JS_PATH . "bootstrap-colorpicker.min.js",
                JS_PATH . "moment.min.js",
                JS_PATH . "daterangepicker/daterangepicker.js",
                JS_PATH . "jquery.multi-select.js",
                JS_PATH . "icheck/icheck.min.js",
                JS_PATH . "neon-chat.js"
            );
            $class_body = "";
            break;
        case "show_logs":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            $class_body = "";
            break;
        case "edit_mag":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                // JS_PATH . "jquery.dataTables.min.js",
                // JS_PATH . "dataTables.buttons.min.js",
                // JS_PATH . "dataTables.select.min.js",
                // JS_PATH . "dataTables.editor.min.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "bootstrap-timepicker.min.js",
                // JS_PATH . "jquery.tabledit.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "edit") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "edit_user":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "save") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "show_users":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "insert" || $page_type == "show" || $page_type == "auto") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "manage_cron":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "turn" || $page_type == "remove" || $page_type == "turn_log") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "show_cron":
            $css_files = array(
                JS_PATH . "datatables/datatables.css",
                JS_PATH . "select2/select2-bootstrap.css",
                JS_PATH . "select2/select2.css"
            );
            $js_files = array(
                JS_PATH . "datatables/datatables.js",
                JS_PATH . "select2/select2.min.js",
                JS_PATH . "neon-chat.js"
            );
            if($page_type == "clear") $flag_ajax_response = true;
            $class_body = "";
            break;
        case "cron_job":
            break;
        case "home":
        default :
            $page = "home";
            $css_files = array(
                JS_PATH . "jvectormap/jquery-jvectormap-1.2.2.css",
                JS_PATH . "rickshaw/rickshaw.min.css"
            );
            $js_files = array(
                JS_PATH . "jvectormap/jquery-jvectormap-europe-merc-en.js",
                JS_PATH . "jquery.sparkline.min.js",
                JS_PATH . "rickshaw/vendor/d3.v3.js",
                JS_PATH . "rickshaw/rickshaw.min.js",
                JS_PATH . "raphael-min.js",
                JS_PATH . "morris.min.js",
                JS_PATH . "toastr.js",
                JS_PATH . "neon-chat.js"
            );
            $flag_breadcrumb = false;
            if($page_type == "get_info") $flag_ajax_response = true;
            $class_body = "page-fade";
            break;
    }

    // current page
    $current_page = array_values(array_filter($all_pages, function($p){ global $page; return $p['name'] == $page; }))[0];
    $page_title = "Ceek - " . $current_page['title'];

    // show with layout
    if($flag_ajax_response){
        include_once(LAYOUT_PATH . "ajaxlayout.php");
    }
    else if($flag_mainlayout){
        include_once(LAYOUT_PATH . "mainlayout.php");
    }
    else{
        include_once(LAYOUT_PATH . "blanklayout.php");
    }

    // pdo local clear
    $pdo1 = null;
?>