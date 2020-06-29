<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

	if($page_type == "sample-login-form"){
		
		/*
			Sample Processing of Forgot password form via ajax
			Page: extra-register.html
		*/

		# Response Data Array
		$resp = array();


		// Fields Submitted
		$username = $_POST["username"];
		$password = $_POST["password"];


		// This array of data is returned for demo purpose, see assets/js/neon-forgotpassword.js
		$resp['submitted_data'] = $_POST;


		// Login success or invalid login data [success|invalid]
		// Your code will decide if username and password are correct
		$login_status = 'invalid';

		if($username != "" && $password != ""){
			// user table
			$users = select_rows($pdo1, $table_local_users, "`name` LIKE '".$username."' and `password` LIKE '".$password."';");
			if(!empty($users)){
				$users = $users[0];
				$login_status = 'success';
				$log_user_id = $users['id'];
				$log_user_permission = $users['permission'];
				$log_user_display = $users['display_name'];
			}
		}

		$resp['login_status'] = $login_status;


		// Login Success URL
		if($login_status == 'success')
		{
			// If you validate the user you may set the user cookies/sessions here
			//setcookie("logged_in", "user_id");
			$_SESSION['user_id'] = $log_user_id;
			$_SESSION['permission'] = $log_user_permission;
			$_SESSION['display_name'] = $log_user_display;
			
			// Set the redirect url after successful login
			$resp['redirect_url'] = 'index.php';
		}
		else{
			$_SESSION['user_id'] = 0;
		}


		echo json_encode($resp);
		die();
	}

?>
<!-- This is needed when you send requests via Ajax -->
<script type="text/javascript">
// var baseurl = '<?php echo WEB_PATH; ?>';
</script>

<div class="login-container">
	
	<div class="login-header login-caret">
		
		<div class="login-content">
			
			<a href="<?php echo WEB_PATH; ?>index.php" class="logo">
				<img src="<?php echo IMAGE_PATH; ?>logo@2x.png" width="120" alt="" />
			</a>
			
			<p class="description">Dear user, log in to access the admin area!</p>
			
			<!-- progress bar indicator -->
			<div class="login-progressbar-indicator">
				<h3>43%</h3>
				<span>logging in...</span>
			</div>
		</div>
		
	</div>
	
	<div class="login-progressbar">
		<div></div>
	</div>
	
	<div class="login-form">
		
		<div class="login-content">
			
			<div class="form-login-error">
				<h3>Invalid login</h3>
				<p>Enter <strong>correct</strong> username and password.</p>
			</div>
			
			<form method="post" action="<?php echo WEB_PATH; ?>index.php" role="form" id="form_login">
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-user"></i>
						</div>
						
						<input type="text" class="form-control" name="username" id="username" placeholder="Username" autocomplete="off" />
					</div>
					
				</div>
				
				<div class="form-group">
					
					<div class="input-group">
						<div class="input-group-addon">
							<i class="entypo-key"></i>
						</div>
						
						<input type="password" class="form-control" name="password" id="password" placeholder="Password" autocomplete="off" />
					</div>
				
				</div>
				
				<div class="form-group">
					<button type="submit" class="btn btn-success btn-block btn-login">
						<i class="entypo-login"></i>
						Login In
					</button>
				</div>
				
			</form>
			
			
			<div class="login-bottom-links">
				
			</div>
			
		</div>
		
	</div>
	
</div>