<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');


	$setting_check_stream = select_rows($pdo1, $table_local_settings, "`setting_key` = 'check_stream' ORDER BY `created_at` DESC LIMIT 0, 1");
	if($setting_check_stream){
		$default_selected_id = $setting_check_stream[0]['id'];
		$default_selected_value = $setting_check_stream[0]['setting_val'];
	}
	else{
		$default_selected_id = 0;
		$default_selected_value = "";
	}


	if($page_type == "save"){
		# Response Data Array
		$resp = array();

		$input_value = $_POST['value'];
		$now_time = time();

		$resp['status'] = "fail";
		$resp['message'] = "";

		// TODO

		$pdo = null;
		$pdo1 = null;

		echo json_encode($resp);
		die();
	}

    $pdo = null;

?>
        <script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				// save button click
				$('#btn-save-setting').click(function(event){
					event.preventDefault();
					let value = $('#input-save-setting-value').val();
					if(value != ""){
						$.ajax({
							method : 'POST',
							url : baseurl + 'index.php?page=setting_check_stream&type=save',
							data : {
								value : value
							},
							success : function(result){
								let status, message = '';
								try{
									result = JSON.parse(result);
									status = result.status;
									message = result.message;
								}
								catch(e){
								}
								if(status == "success"){
									$("#message-alert").removeClass('alert-danger');
									$("#message-alert").addClass('alert-success');
									$("#message-alert").html('<strong>Success!</strong> Saved! '+message);
								}
								else{
									$("#message-alert").removeClass('alert-success');
									$("#message-alert").addClass('alert-danger');
									$("#message-alert").html('<strong>Warning!</strong> Failed to save! '+message);
								}
							},
							error : function(err){
								$("#message-alert").removeClass('alert-success');
								$("#message-alert").addClass('alert-danger');
								$("#message-alert").html('<strong>Warning!</strong> Failed to save.');
							}
						});
					}
					else{
						$("#message-alert").removeClass('alert-success');
						$("#message-alert").addClass('alert-danger');
						$("#message-alert").html('<strong>Warning!</strong> You must input step time.');
					}
				});
			} );
		</script>

		<div id="message-alert" class="alert"></div>

		<div class="row">
			<div class="col-sm-12">
				<div class="panel panel-primary" data-collapsed="0">
				
					<div class="panel-heading">
						<div class="panel-title">
							Update Setting about Check Stream
						</div>
						
						<div class="panel-options">
							<a href="#sample-modal" data-toggle="modal" data-target="#sample-modal-dialog-1" class="bg"><i class="entypo-cog"></i></a>
							<a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
							<a href="#" data-rel="reload"><i class="entypo-arrows-ccw"></i></a>
							<a href="#" data-rel="close"><i class="entypo-cancel"></i></a>
						</div>
					</div>
					
					<div class="panel-body">

						<form role="form" class="form-horizontal form-groups-bordered" method="POST" url="">

							<div class="form-group">
								<label for="input-save-setting-value" class="col-sm-3 control-label">Step Time</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-save-setting-value" placeholder="Input the step time minutes." value="<?php echo $default_selected_value; ?>">
								</div>
							</div>

							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-5">
									<button type="submit" class="btn btn-primary" id="btn-save-setting">Save</button>
								</div>
							</div>

						</form>

					</div>
				</div>
			</div>
		</div>