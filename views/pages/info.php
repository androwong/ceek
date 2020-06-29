<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

	$log_file = LIB_PATH . "db_log.txt";

	if($page_type == "sample-connect-form"){
		
		/*
			Sample Processing of Forgot password form via ajax
			Page: extra-register.html
		*/

		# Response Data Array
		$resp = array();


		// Fields Submitted
		$hostname = $_POST["db_host"];
		$port = $_POST["db_port"];
		$username = $_POST["db_user"];
		$password = $_POST["db_pass"];
		$database = $_POST["db_database"];


		// This array of data is returned for demo purpose, see assets/js/neon-forgotpassword.js
		$resp['submitted_data'] = $_POST;


		// Login success or invalid login data [success|invalid]
		// Your code will decide if username and password are correct
		$connect_status = 'invalid';
		$pdo = db_connect($hostname, $port, $username, $password, $database);
		if($pdo)
		{
			$connect_status = 'success';
			$pdo = null;
		}

		$resp['connect_status'] = $connect_status;


		// Login Success URL
		if($connect_status == 'success')
		{
			// If you validate the user you may set the user cookies/sessions here
			//setcookie("logged_in", "user_id");
			$_SESSION['db_conn'] = 1;
			$_SESSION['db_host'] = $hostname;
			$_SESSION['db_port'] = $port;
			$_SESSION['db_user'] = $username;
			$_SESSION['db_pass'] = $password;
			$_SESSION['db_database'] = $database;

			if(FLAG_SAVE_LOG_FILE){
				// write database log file
				$fp = fopen($log_file, "w");
				$logs = "host:" . $hostname . ",port:" . $port . ",user:" . $username . ",pass:" . $password . ",database:" . $database;
				fwrite($fp, $logs);
				fclose($fp);
			}
			else{
				// write informations to database
				$query_dbs = "INSERT INTO `".$table_local_dbs."` SET `host`='".$hostname."', `port`='".$port."', `username`='".$username."', `password`='".$password."', `db_name`='".$database."';";
				insert_row($pdo1, $table_local_dbs, $query_dbs);
			}
			
			// Set the redirect url after successful login
			$resp['redirect_url'] = 'index.php';
		}
		else{
			$_SESSION['db_conn'] = 0;
		}


		echo json_encode($resp);
		die();
	}
	else{
		// if(getConnectionInfoFromFile()) redirect(WEB_PATH . "index.php" . (($next_page!='')?"?page=".$next_page:""));
	}

?>
<style>
	.login-page.logging-in .login-header {
		padding: 0px;
	}
	.login-page .login-header{
		background: none !important;
		padding: 0px;
	}
	.login-page .login-progressbar-indicator h3 {
		color: #000;
	}
	.login-page .login-form .form-group .input-group{
		background: none !important;
	}
	.login-page .login-form .form-group .input-group .form-control {
		color: #000;
	}
</style>
<!-- This is needed when you send requests via Ajax -->
<script type="text/javascript">
// var baseurl = '<?php echo WEB_PATH; ?>';
</script>

<div class="login-container">

	<div class="login-header">
		
		<div class="login-content">
			
			<p class="description">Dear user, connect to database!</p>
			
			<!-- progress bar indicator -->
			<div class="login-progressbar-indicator">
				<h3>43%</h3>
				<span>connecting in...</span>
			</div>
		</div>
		
	</div>
	
	<div class="login-progressbar">
		<div></div>
	</div>
	
	<div class="login-form">
		
		<div class="login-content">
			
			<div class="form-login-error">
				<h3>Invalid Connection</h3>
				<p><strong>Mysql Server</strong> informations must be correct.</p>
			</div>
			
			<form method="post" action="<?php echo WEB_PATH; ?>index.php" role="form" id="form_login">
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-network"></i>
						</div>
						
						<input type="text" class="form-control" name="hostname" id="hostname" placeholder="Hostname" autocomplete="off" value="<?php echo $db_host; ?>" />
					</div>
					
				</div>
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-link"></i>
						</div>
						
						<input type="text" class="form-control" name="port" id="port" placeholder="Port" autocomplete="off" value="<?php echo $db_port; ?>" />
					</div>
					
				</div>
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-user"></i>
						</div>
						
						<input type="text" class="form-control" name="user" id="user" placeholder="Username" autocomplete="off" value="<?php echo $db_user; ?>" />
					</div>
					
				</div>
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-key"></i>
						</div>
						
						<input type="password" class="form-control" name="pass" id="pass" placeholder="Password" autocomplete="off" value="<?php echo $db_pass; ?>" />
					</div>
				
				</div>
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-plus-squared"></i>
						</div>
						
						<input type="text" class="form-control" name="database" id="database" placeholder="Database" autocomplete="off" value="<?php echo $db_database; ?>" />
					</div>
					
				</div>
				
				<div class="form-group">
					<button type="submit" class="btn btn-success btn-block btn-login">
						<i class="entypo-check"></i>
						Connect
					</button>
				</div>
				
			</form>
			
			
			<div class="login-bottom-links">
				
			</div>
			
		</div>
		
	</div>
	
</div>