<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

	$table_series = "series";
	if(!$flag_redirect_info && $pdo){
		$series = select_rows($pdo, $table_series);
	}
	else{
		$series = array();
	}

	$youtubes = select_rows($pdo1, $table_local_manage_youtube);
	$default_selected_day_id = 0;
	$default_selected_day_value = array();
	$default_selected_hour_id = 0;
	$default_selected_hour_value = array();
	$default_selected_minute_id = 0;
	$default_selected_minute_value = array();

	$id = (isset($_POST['input-save-youtube-id'])?$_POST['input-save-youtube-id']:$id);

	if($id > 0){
		$setting_youtube_day = select_rows($pdo1, $table_local_settings, "`setting_key` = 'check_youtube_".$id."_day' ORDER BY `created_at` DESC LIMIT 0, 1");
		$setting_youtube_hour = select_rows($pdo1, $table_local_settings, "`setting_key` = 'check_youtube_".$id."_hour' ORDER BY `created_at` DESC LIMIT 0, 1");
		$setting_youtube_minute = select_rows($pdo1, $table_local_settings, "`setting_key` = 'check_youtube_".$id."_minute' ORDER BY `created_at` DESC LIMIT 0, 1");
		if($setting_youtube_day){
			$default_selected_day_id = $setting_youtube_day[0]['id'];
			$default_selected_day_value = explode(",", $setting_youtube_day[0]['setting_val']);
		}
		if($setting_youtube_hour){
			$default_selected_hour_id = $setting_youtube_hour[0]['id'];
			$default_selected_hour_value = explode(",", $setting_youtube_hour[0]['setting_val']);
		}
		if($setting_youtube_minute){
			$default_selected_minute_id = $setting_youtube_minute[0]['id'];
			$default_selected_minute_value = explode(",", $setting_youtube_minute[0]['setting_val']);
		}
	}


	if($page_type == "save" && isset($_POST) && !empty($_POST) && $id > 0){
		$default_selected_day_value = (isset($_POST['select-youtube-day'])?$_POST['select-youtube-day']:array());
		$default_selected_hour_value = (isset($_POST['select-youtube-hour'])?$_POST['select-youtube-hour']:array());
		$default_selected_minute_value = (isset($_POST['select-youtube-minute'])?$_POST['select-youtube-minute']:array());
		$input_day_value = implode(",", $default_selected_day_value);
		$input_hour_value = implode(",", $default_selected_hour_value);
		$input_minute_value = implode(",", $default_selected_minute_value);
		$now_time = time();

		$message_success = false;
		$message = "Failed to Save YouTube Settings.";
		
		

		if($default_selected_day_id > 0){
			// update
			$setting_query = "UPDATE `".$table_local_settings."` SET `setting_key` = 'check_youtube_".$id."_day', `setting_val` = '".$input_day_value."', `created_at` = '".$now_time."' WHERE `id` = '".$default_selected_day_id."';";
			$result1 = update_row($pdo1, $table_local_settings, $setting_query);
		}
		else{
			// create
			$setting_query = "INSERT INTO `".$table_local_settings."` SET `setting_key` = 'check_youtube_".$id."_day', `setting_val` = '".$input_day_value."', `created_at` = '".$now_time."';";
			$result1 = insert_row($pdo1, $table_local_settings, $setting_query);
		}

		if($default_selected_hour_id > 0){
			// update
			$setting_query = "UPDATE `".$table_local_settings."` SET `setting_key` = 'check_youtube_".$id."_hour', `setting_val` = '".$input_hour_value."', `created_at` = '".$now_time."' WHERE `id` = '".$default_selected_hour_id."';";
			$result2 = update_row($pdo1, $table_local_settings, $setting_query);
		}
		else{
			// create
			$setting_query = "INSERT INTO `".$table_local_settings."` SET `setting_key` = 'check_youtube_".$id."_hour', `setting_val` = '".$input_hour_value."', `created_at` = '".$now_time."';";
			$result2 = insert_row($pdo1, $table_local_settings, $setting_query);
		}

		if($default_selected_minute_id > 0){
			// update
			$setting_query = "UPDATE `".$table_local_settings."` SET `setting_key` = 'check_youtube_".$id."_minute', `setting_val` = '".$input_minute_value."', `created_at` = '".$now_time."' WHERE `id` = '".$default_selected_minute_id."';";
			$result3 = update_row($pdo1, $table_local_settings, $setting_query);
		}
		else{
			// create
			$setting_query = "INSERT INTO `".$table_local_settings."` SET `setting_key` = 'check_youtube_".$id."_minute', `setting_val` = '".$input_minute_value."', `created_at` = '".$now_time."';";
			$result3 = insert_row($pdo1, $table_local_settings, $setting_query);
		}

		$cron_jobs = select_rows($pdo1, $table_local_cron_jobs, "`page` = 'youtube' and `param_id` = '".$id."' ORDER BY `id` DESC");
		if(!empty($cron_jobs)) $cron_job_id = $cron_jobs[0]['id']; else $cron_job_id = 0;

		if($cron_job_id > 0){
			// update
			$cron_query = "UPDATE `".$table_local_cron_jobs."` SET `is_run` = 1 WHERE `id` = '".$cron_job_id."';";
			$result4 = update_row($pdo1, $table_local_cron_jobs, $cron_query);
		}
		else{
			// create
			$cron_query = "INSERT INTO `".$table_local_cron_jobs."` SET `page` = 'youtube', `param_id` = '".$id."', `last_time` = '".$now_time."', `is_run` = 1;";
			$result4 = insert_row($pdo1, $table_local_cron_jobs, $cron_query);
		}

		if($result1 && $result2 && $result3 && $result4){
			$message_success = true;
			$message = "Success to Save YouTube Settings.";
		}
	}

	$pdo1 = null;
	$pdo = null;

?>
	

		<div class="row">
			<div class="col-sm-12">
				<?php 
					if($message != ""){
						echo '<div class="alert '.(($message_success)?'alert-success':'alert-danger').'">
							<strong>'.(($message_success)?'Success!':'Warning!').'</strong> '.$message.'</div>';
					} 
				?>

				<div class="panel panel-primary" data-collapsed="0">
				
					<div class="panel-heading">
						<div class="panel-title">
							Update Setting about YouTube Downloading
						</div>
						
						<div class="panel-options">
							<a href="#sample-modal" data-toggle="modal" data-target="#sample-modal-dialog-1" class="bg"><i class="entypo-cog"></i></a>
							<a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
							<a href="#" data-rel="reload"><i class="entypo-arrows-ccw"></i></a>
							<a href="#" data-rel="close"><i class="entypo-cancel"></i></a>
						</div>
					</div>
					
					<div class="panel-body">

						<form role="form" class="form-horizontal form-groups-bordered" method="POST" action="<?php echo WEB_PATH . 'index.php?page=setting_youtube&type=save'; ?>">

							<div class="form-group">
								<label for="input-save-youtube-id" class="col-sm-3 control-label">YouTube</label>
								<div class="col-sm-5">
                                    <select class="form-control" name="input-save-youtube-id" id="input-save-youtube-id" required >
                                        <option value="" default selected disabled>Select a YouTube</option>
                                        <?php 
                                            foreach($youtubes as $y){
												$y['series'] = "";
												foreach($series as $s){
													if($s['id'] == $y['tv_series']){
														$y['series'] = $s['title'];
														break;
													}
												}
                                                echo '<option value="'.$y['id'].'" '.(($id==$y['id'])?"selected":"").' >'. $y['series'] . ' => ' . $y['keywords']. '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
							</div>

							<div class="form-group">
								<label for="select-youtube-day" class="col-sm-3 control-label">Day of Week</label>
								<div class="col-sm-5">
									<select multiple="multiple" name="select-youtube-day[]" class="form-control multi-select">
										<?php 
                                            for($d = 0; $d < 7; $d++){
												$val = get_day($d);
                                                echo '<option value="'.$d.'" '.((in_array($d, $default_selected_day_value))?"selected":"").'>'.$val.'</option>';
                                            }
                                        ?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="select-youtube-hour" class="col-sm-3 control-label">Hour</label>
								<div class="col-sm-5">
									<select multiple="multiple" name="select-youtube-hour[]" class="form-control multi-select">
										<?php 
                                            for($h = 0; $h < 24; $h++){
                                                echo '<option value="'.$h.'" '.((in_array($h, $default_selected_hour_value))?"selected":"").'>'.$h.'</option>';
                                            }
                                        ?>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label for="select-youtube-minute" class="col-sm-3 control-label">Minute</label>
								<div class="col-sm-5">
									<select multiple="multiple" name="select-youtube-minute[]" class="form-control multi-select">
										<?php 
                                            for($m = 0; $m < 60; $m+=5){
                                                echo '<option value="'.$m.'" '.((in_array($m, $default_selected_minute_value))?"selected":"").'>'.$m.'</option>';
                                            }
                                        ?>
									</select>
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