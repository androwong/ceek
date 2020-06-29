<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');


	$setting_check_stream = select_rows($pdo1, $table_local_settings, "`setting_key` = 'check_stream' ORDER BY `created_at` DESC LIMIT 0, 1");
	$setting_check_message = select_rows($pdo1, $table_local_settings, "`setting_key` = 'check_message' ORDER BY `created_at` DESC LIMIT 0, 1");
	$setting_check_message_frame = select_rows($pdo1, $table_local_settings, "`setting_key` = 'check_message_frame' ORDER BY `created_at` DESC LIMIT 0, 1");
	$setting_check_phones = select_rows($pdo1, $table_local_settings, "`setting_key` LIKE 'check_phone_%' ORDER BY `created_at` DESC");
	if($setting_check_stream){
		$default_selected_id = $setting_check_stream[0]['id'];
		$default_selected_value = $setting_check_stream[0]['setting_val'];
	}
	else{
		$default_selected_id = 0;
		$default_selected_value = DEFAULT_CHECK_STREAM_TIME;
	}
	if($setting_check_message){
		$default_selected_message_id = $setting_check_message[0]['id'];
		$default_selected_message = $setting_check_message[0]['setting_val'];
	}
	else{
		$default_selected_message_id = 0;
		$default_selected_message = "";
	}
	if($setting_check_message_frame){
		$default_selected_message_frame_id = $setting_check_message_frame[0]['id'];
		$default_selected_message_frame = $setting_check_message_frame[0]['setting_val'];
	}
	else{
		$default_selected_message_frame_id = 0;
		$default_selected_message_frame = "";
	}
	$total_phone_number_count = 0;
	if($setting_check_phones){
		$default_selected_phones = array();
		foreach($setting_check_phones as $val){
			$default_selected_phones[$val['id']] = $val['setting_val'];
			$temp_order_id = str_replace("check_phone_", "", $val['setting_key']);
			if($temp_order_id > $total_phone_number_count) $total_phone_number_count = $temp_order_id;
		}
	}
	else{
		$default_selected_phones = array(0 => "");
	}


	if($page_type == "save"){
		# Response Data Array
		$resp = array();

		$input_value = $_POST['value'];
		$input_message = $_POST['message'];
		$input_message_frame = $_POST['message_frame'];
		$input_phones = $_POST['phones'];
		$now_time = time();

		$resp['status'] = "fail";
		$resp['message'] = "";

		if($default_selected_id > 0){
			// update
			$setting_query = "UPDATE `".$table_local_settings."` SET `setting_key` = 'check_stream', `setting_val` = '".$input_value."', `created_at` = '".$now_time."' WHERE `id` = '".$default_selected_id."';";
			$result1 = update_row($pdo1, $table_local_settings, $setting_query);
		}
		else{
			// create
			$setting_query = "INSERT INTO `".$table_local_settings."` SET `setting_key` = 'check_stream', `setting_val` = '".$input_value."', `created_at` = '".$now_time."';";
			$result1 = insert_row($pdo1, $table_local_settings, $setting_query);
		}

		if($default_selected_message_id > 0){
			// update
			$setting_query = "UPDATE `".$table_local_settings."` SET `setting_key` = 'check_message', `setting_val` = '".$input_message."', `created_at` = '".$now_time."' WHERE `id` = '".$default_selected_message_id."';";
			$result2 = update_row($pdo1, $table_local_settings, $setting_query);
		}
		else{
			// create
			$setting_query = "INSERT INTO `".$table_local_settings."` SET `setting_key` = 'check_message', `setting_val` = '".$input_message."', `created_at` = '".$now_time."';";
			$result2 = insert_row($pdo1, $table_local_settings, $setting_query);
		}

		if($default_selected_message_frame_id > 0){
			// update
			$setting_query = "UPDATE `".$table_local_settings."` SET `setting_key` = 'check_message_frame', `setting_val` = '".$input_message_frame."', `created_at` = '".$now_time."' WHERE `id` = '".$default_selected_message_frame_id."';";
			$result2 = update_row($pdo1, $table_local_settings, $setting_query);
		}
		else{
			// create
			$setting_query = "INSERT INTO `".$table_local_settings."` SET `setting_key` = 'check_message_frame', `setting_val` = '".$input_message_frame."', `created_at` = '".$now_time."';";
			$result2 = insert_row($pdo1, $table_local_settings, $setting_query);
		}

		$index = 1;
		foreach($input_phones as $phone_number){
			$phone_pattern_result = preg_match("/[\+0-9\-]+/", $phone_number, $phone_pattern);
			if($phone_pattern_result && $phone_pattern[0] == $phone_number){
				$find_exist_same_order_id = 0;
				foreach($setting_check_phones as $val){
					if($val['setting_key'] == ("check_phone_".$index)){
						$find_exist_same_order_id = $val['id'];
						break;
					}
				}

				if($find_exist_same_order_id > 0){
					// update
					$setting_query = "UPDATE `".$table_local_settings."` SET `setting_key` = 'check_phone_".$index."', `setting_val` = '".$phone_number."', `created_at` = '".$now_time."' WHERE `id` = '".$find_exist_same_order_id."';";
					update_row($pdo1, $table_local_settings, $setting_query);
				}
				else{
					// create
					$setting_query = "INSERT INTO `".$table_local_settings."` SET `setting_key` = 'check_phone_".$index."', `setting_val` = '".$phone_number."', `created_at` = '".$now_time."';";
					insert_row($pdo1, $table_local_settings, $setting_query);
				}
				$index ++;
			}
		}
		for( ; $index <= $total_phone_number_count; $index++){
			$setting_query = "`setting_key` = 'check_phone_".$index."'";
			delete_rows($pdo1, $table_local_settings, $setting_query);
		}
		
		if($result1 && $result2){
			$resp['status'] = "success";
			$resp['message'] = "Updated the current setting.";
		}

		$pdo1 = null;

		echo json_encode($resp);
		die();
	}

?>
        <script type="text/javascript">
			jQuery( document ).ready( function( $ ) {
				// save button click
				$('#btn-save-setting').click(function(event){
					event.preventDefault();
					let value = $('#input-save-setting-value').val();
					let message = $('#input-save-setting-message').val();
					let message_frame = $('#input-save-setting-message-frame').val();
					let phones = $("input.input-phone-number").map(function(){return $(this).val();}).get();
					if(value != "" && message != "" && message_frame != "" && phones.length > 0){
						$.ajax({
							method : 'POST',
							url : baseurl + 'index.php?page=setting_check_stream&type=save',
							data : {
								value : value,
								message : message,
								message_frame : message_frame,
								phones : phones
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
						$("#message-alert").html('<strong>Warning!</strong> You must input step time and messages and phone numbers.');
					}
				});

				// add phone number button click
				$('#btn-add-phone').click(function(event){
					$elm_new = $("#form-group-phone-number").clone();
					$elm_new.find(".input-phone-number").val('');
					$elm_new.removeAttr("id");
					$elm_new.css("display", "block");
					$elm_new.insertBefore("#form-group-btns");
				});

				// remove phone number button click
				$('#form-settings').delegate('.btn-remove-phone', 'click', function(event){
					$(this).closest("div.form-group").remove();
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

						<form role="form" class="form-horizontal form-groups-bordered" method="POST" url="" id="form-settings">

							<div class="form-group">
								<label for="input-save-setting-value" class="col-sm-3 control-label">Step Time[min]</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-save-setting-value" placeholder="Input the step time minutes." value="<?php echo $default_selected_value; ?>">
								</div>
							</div>

							<div class="form-group">
								<label for="input-save-setting-message" class="col-sm-3 control-label">Message (not working)</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-save-setting-message" placeholder="Input the sms message." value="<?php echo $default_selected_message; ?>">
								</div>
							</div>

							<div class="form-group">
								<label for="input-save-setting-message-frame" class="col-sm-3 control-label">Message (never fame)</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-save-setting-message-frame" placeholder="Input the sms message." value="<?php echo $default_selected_message_frame; ?>">
								</div>
							</div>

							<?php
								$first_exist = false;
								foreach($default_selected_phones as $key => $val){
									$temp_add_content = "";
									if($val=="") $temp_add_content .= " style=\"display:none;\" ";
									if(!$first_exist){
										$first_exist = true;
										$temp_add_content .= " id=\"form-group-phone-number\" ";
									}
							?>
							<div class="form-group" <?php echo $temp_add_content; ?> >
								<label class="col-sm-3 control-label">Phone Number</label>
								<div class="col-sm-5">
									<input type="text" class="form-control input-phone-number" placeholder="Input the phone number." value="<?php echo $val; ?>">
								</div>
								<div class="col-sm-3">
									<button type="button" class="btn btn-danger btn-remove-phone">Remove</button>
								</div>
							</div>
							<?php
								}
							?>

							<div class="form-group" id="form-group-btns">
								<div class="col-sm-offset-3 col-sm-5">
									<button type="submit" class="btn btn-primary" id="btn-save-setting">Save</button>
									<button type="button" class="btn btn-secondary" id="btn-add-phone">Add Phone Number</button>
								</div>
							</div>

						</form>

					</div>
				</div>
			</div>
		</div>