<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    $table_mag_devices = "mag_devices";
    $table_reg_users = "reg_users";
    $table_users = "users";
    $table_user_output = "user_output";
    $table_bouquests = "bouquets";

    $log_file = LIB_PATH . "customer1_log.txt";

    $last_post_params = json_encode($_POST);

    if(!$flag_redirect_info && $pdo){
        $usernames = select_rows($pdo, $table_reg_users);
        $bouquests = select_rows($pdo, $table_bouquests);
    }
    else{
        $usernames = array();
        $bouquests = array();
    }


    $default_mac_address = "";
    $default_assign_account = "";
    $default_expire_unlimited = "";
    $default_expire_date = "";
    $default_expire_time = "";
    $default_bouquet = array();
    $default_admin_notes = "";
    $default_user_id = 0;

    $default_mac_address_flag = true;
    $default_assign_account_flag = true;
    $default_expire_date_time_flag = true;
    $default_bouquet_flag = true;
    $default_admin_notes_flag = true;
    if($user_id == 2 && $id > 0){
        // customer
        // $default_mac_address_flag = false;
        $default_assign_account_flag = false;
        $default_admin_notes_flag = false;
        // if($id <= 0) redirect(WEB_PATH . "index.php?page=edit_mag");
    }


    
    if($id > 0){
        // edit
        $selected_mag = select_rows($pdo, $table_mag_devices, "`mag_id`=".$id);
        if(count($selected_mag) > 0){
            $selected_mag = $selected_mag[0];
            $default_user_id = $selected_mag['user_id'];
            $selected_user = select_rows($pdo, $table_users, "`id`=".$default_user_id);
            if(count($selected_user) > 0){
                $selected_user = $selected_user[0];
            }
        }
    }
    

    if(isset($_POST) && count($_POST) > 0){
        // create
        if(isset($_POST['mac_address'])) $default_mac_address = $_POST['mac_address'];
        if(isset($_POST['assign_account'])) $default_assign_account = $_POST['assign_account'];
        if(isset($_POST['expire_unlimited']) && $_POST['expire_unlimited'] == "on") $default_expire_unlimited = "checked";
        if(isset($_POST['expire_date'])) $default_expire_date = $_POST['expire_date'];
        if(isset($_POST['expire_time'])) $default_expire_time = $_POST['expire_time'];
        if(isset($_POST['select-bouquest'])) $default_bouquet = $_POST['select-bouquest'];
        if(isset($_POST['admin_notes'])) $default_admin_notes = $_POST['admin_notes'];
    }
    else{
        if(isset($selected_mag) && $selected_mag){
            if($selected_user){
                $default_mac_address = base64_decode($selected_mag['mac']);
                $default_assign_account = $selected_user['member_id'];
                if(!$selected_user['exp_date'] || $selected_user['exp_date'] == NULL || $selected_user['exp_date'] == 'NULL') $default_expire_unlimited = "checked";
                else{
                    $default_expire_date = date("D, d F Y", $selected_user['exp_date']);
                    $default_expire_time = date("H:i:s", $selected_user['exp_date']);
                }
                $default_bouquet = explode(",", substr($selected_user['bouquet'], 1, strlen($selected_user['bouquet'])-2));
                $default_admin_notes = $selected_user['admin_notes'];
            }
        }
    }    

    
    if($page_type == "insert"){
        // save
        
		# Response Data Array
        $resp = array();
        
        if(isset($_POST['mac_address'])){
            $mac_address = get_mac_address($_POST['mac_address']);
        }
        else{
            $mac_address = "";
        }
        $assign_account = (isset($_POST['assign_account'])?$_POST['assign_account']:"");
        $expire_unlimited = (isset($_POST['expire_unlimited'])?$_POST['expire_unlimited']:"");
        $expire_date = (isset($_POST['expire_date'])?$_POST['expire_date']:"");
        $expire_time = (isset($_POST['expire_time'])?$_POST['expire_time']:"");
        $selected_bouquests = (isset($_POST['select-bouquest'])?$_POST['select-bouquest']:array());
        $admin_notes = (isset($_POST['admin_notes'])?$_POST['admin_notes']:"");
        
        if($expire_unlimited == "on") $exp_date = NULL;
        else{
            $exp_date = strtotime($expire_date . " " . $expire_time);
        }

        $random_username = generateRandomString();
        $random_password = generateRandomString();

        $bouquest_str = implode(",", $selected_bouquests);
        $created_at = time();

        // check if there is a mag device with the same mac_address in table
        $flag_exit_mag_with_same_address = false;
        if($mac_address != ""){
            $exit_mags = select_rows($pdo, $table_mag_devices, "`mac` LIKE '".$mac_address."'");
            if(!empty($exit_mags)){
                $flag_exit_mag_with_same_address = true;
            }
        }

        // $message_success = false;
        // $message = "Failed to Add new MAG Device.";
        $resp['status'] = "fail";
        $resp['message'] = "Failed to Add new MAG Device.";

        if($last_post == $last_post_params){
            // $message = "Failed to Add new MAG Device Because same datas.";
            $resp['message'] = "Failed to Add new MAG Device Because same datas.";
        }
        else if($flag_exit_mag_with_same_address && !($id > 0)){
            // $message = "Failed to Add new MAG Device Because same mag address - ".$_POST['mac_address'].".";
            $resp['message'] = "Failed to Add new MAG Device Because same mag address - ".$_POST['mac_address'].".";
        }
        else if($mac_address === false){
            // $message = "Failed to Add new MAG Device, Type mac address like 00:AB:CD:EF:12:34.";
            $resp['message'] = "Failed to Add new MAG Device, Type mac address like 00:AB:CD:EF:12:34.";
        }
        else if(!$flag_redirect_info && $pdo){
            if($id > 0){
                // update
                if($user_id == 1){
                    // adminer
                    $query_mag = "UPDATE `".$table_mag_devices."` SET `mac` = '".$mac_address."' WHERE `mag_id` = '".$id."';";
                    $result1 = update_row($pdo, $table_mag_devices, $query_mag);
                    
                    if($result1){
                        $query_user = "UPDATE `".$table_users."` SET  `exp_date` = '".$exp_date."', `admin_notes` = '".$admin_notes."', `bouquet` = '[".$bouquest_str."]' WHERE `id` = '".$_POST['current_user_id']."';";
                        $result2 = update_row($pdo, $table_users, $query_user);

                        if($result2){
                            // $message_success = true;
                            $resp['status'] = "success";
                            // $message = "Update MAG Device.";
                            $resp['message'] = "Update MAG Device.";
                            $_SESSION['last_post'] = $last_post_params;
                        }
                    }
                }
                else if($user_id == 2){
                    // customer
                    $selected_mag_exit = select_rows($pdo, $table_mag_devices, "`mag_id` = '".$id."'");
                    if(!empty($selected_mag_exit)){
                        $old_mag_address = $selected_mag_exit[0]['mac'];
                    }

                    $query_mag = "UPDATE `".$table_mag_devices."` SET `mac` = '".$mac_address."' WHERE `mag_id` = '".$id."';";
                    $result1 = update_row($pdo, $table_mag_devices, $query_mag);

                    if($result1){
                        $query_users = "UPDATE `".$table_users."` SET `exp_date` = '".$exp_date."', `bouquet` = '[".$bouquest_str."]' WHERE `id` = '".$_POST['current_user_id']."';";
                        $result2 = update_row($pdo, $table_users, $query_users);

                        if($result2){
                            if(FLAG_SAVE_LOG_FILE){
                                // write informations to log file
                                $fp = fopen($log_file, "a");
                                $logs = "\nuser:" . $user_id . ",mag_id:" . $id . ",user_id:" . $_POST['current_user_id'] 
                                        . ",old_exp_date:" . $selected_user['exp_date'] . ",exp_date:" . $exp_date 
                                        . ",old_mac:" . $old_mag_address . ",mac:" . $mac_address 
                                        . ",old_bouquet:" . $selected_user['bouquet'] . ",bouquet:[" . $bouquest_str . "],datetime:" . time() . ",is_create:0";
                                fwrite($fp, $logs);
                                fclose($fp);
                            }
                            else{
                                // write informations to database
                                $query_logs = "INSERT INTO `".$table_local_logs."` SET `user`='".$user_id."', `mag_id`='".$id."', `user_id`='".$_POST['current_user_id']."', 
                                            `old_exp_date`='".$selected_user['exp_date']."', `exp_date`='".$exp_date."', 
                                            `old_mac`='".$old_mag_address."', `mac`='".$mac_address."', 
                                            `old_bouquet`='".$selected_user['bouquet']."', `bouquet`='[".$bouquest_str."]', `created_at`='".time()."', `is_create`=0;";
                                insert_row($pdo1, $table_local_logs, $query_logs);
                            }
                            // $message_success = true;
                            $resp['status'] = "success";
                            // $message = "Update MAG Device.";
                            $resp['message'] = "Update MAG Device.";
                            $_SESSION['last_post'] = $last_post_params;
                        }
                    }
                }
            }
            else{
                // insert
                $query_user = "INSERT INTO `".$table_users."` (`id`, `member_id`, `username`, `password`, `exp_date`, `admin_enabled`, `enabled`, `admin_notes`, `reseller_notes`, `bouquet`, `max_connections`, `is_restreamer`, `allowed_ips`, `allowed_ua`, `is_trial`, `created_at`, `created_by`, `pair_id`, `is_mag`, `is_e2`, `force_server_id`, `is_isplock`, `isp_desc`, `forced_country`, `is_stalker`, `bypass_ua`, `as_number`, `play_token`) 
                        VALUES (NULL, '".$assign_account."', '".$random_username."', '".$random_password."', '".$exp_date."', '1', '1', '".$admin_notes."', '', '[".$bouquest_str."]', '1', '0', '[]', '[]', '0', '".$created_at."', '-1', NULL, '1', '0', '0', '0', '', '', '0', '0', NULL, '');";
                $result1 = insert_row($pdo, $table_users, $query_user);

                if($result1){
                    $query_mag = "INSERT INTO `".$table_mag_devices."` (`mag_id`, `user_id`, `bright`, `contrast`, `saturation`, `aspect`, `video_out`, `volume`, `playback_buffer_bytes`, `playback_buffer_size`, `audio_out`, `mac`, `ip`, `ls`, `ver`, `lang`, `locale`, `city_id`, `hd`, `main_notify`, `fav_itv_on`, `now_playing_start`, `now_playing_type`, `now_playing_content`, `time_last_play_tv`, `time_last_play_video`, `hd_content`, `image_version`, `last_change_status`, `last_start`, `last_active`, `keep_alive`, `playback_limit`, `screensaver_delay`, `stb_type`, `sn`, `last_watchdog`, `created`, `country`, `plasma_saving`, `ts_enabled`, `ts_enable_icon`, `ts_path`, `ts_max_length`, `ts_buffer_use`, `ts_action_on_exit`, `ts_delay`, `video_clock`, `rtsp_type`, `rtsp_flags`, `stb_lang`, `display_menu_after_loading`, `record_max_length`, `plasma_saving_timeout`, `now_playing_link_id`, `now_playing_streamer_id`, `device_id`, `device_id2`, `hw_version`, `parent_password`, `spdif_mode`, `show_after_loading`, `play_in_preview_by_ok`, `hdmi_event_reaction`, `mag_player`, `play_in_preview_only_by_ok`, `fav_channels`, `tv_archive_continued`, `tv_channel_default_aspect`, `last_itv_id`, `units`, `token`, `lock_device`, `watchdog_timeout`) 
                            VALUES (NULL, '".$result1."', '200', '127', '127', '', 'rca', '50', '0', '0', '1', '".$mac_address."', NULL, NULL, NULL, NULL, 'en_GB.utf8', '0', '1', '1', '0', NULL, '0', NULL, NULL, NULL, '1', NULL, NULL, NULL, NULL, NULL, '3', '10', '', NULL, NULL, '".$created_at."', NULL, '0', '0', '1', NULL, '3600', 'cyclic', 'no_save', 'on_pause', 'Off', '4', '0', 'en', '1', '180', '600', NULL, NULL, NULL, NULL, NULL, '0000', '1', 'main_menu', '1', '1', 'ffmpeg', 'true', '', '', 'stretch', '0', 'metric', '', '0', '');";
                    $result2 = insert_row($pdo, $table_mag_devices, $query_mag);

                    if($result2){
                        $query_user_output = "INSERT INTO `".$table_user_output."` (`id`, `user_id`, `access_output_id`) VALUES (NULL, '".$result1."', '1'), (NULL, '".$result1."', '2'), (NULL, '".$result1."', '3');";
                        $result3 = insert_row($pdo, $table_user_output, $query_user_output);

                        if($result3){
                            // $message_success = true;
                            $resp['status'] = "success";
                            // $message = "Add new MAG Device.";
                            $resp['message'] = "Add new MAG Device.";
                            $_SESSION['last_post'] = $last_post_params;
                        }
                    }
                }

                if($user_id == 1){
                    // adminer
                }
                else if($user_id == 2){
                    // customer
                    if($result3){
                        if(FLAG_SAVE_LOG_FILE){
                            // write informations to log file
                            $fp = fopen($log_file, "a");
                            $logs = "\nuser:" . $user_id . ",mag_id:" . $result2 . ",user_id:" . $result1 
                                    . ",old_exp_date:" . $exp_date . ",exp_date:" . $exp_date 
                                    . ",old_mac:" . $mac_address . ",mac:" . $mac_address 
                                    . ",old_bouquet:[],bouquet:[".$bouquest_str."],datetime:".time().",is_create:1";
                            fwrite($fp, $logs);
                            fclose($fp);
                        }
                        else{
                            // write informations to database
                            $query_logs = "INSERT INTO `".$table_local_logs."` SET `user`='".$user_id."', `mag_id`='".$result2."', `user_id`='".$result1."', 
                                        `old_exp_date`='".$exp_date."', `exp_date`='".$exp_date."', 
                                        `old_mac`='', `mac`='".$mac_address."', 
                                        `old_bouquet`='[]', `bouquet`='[".$bouquest_str."]', `created_at`='".time()."', `is_create`=1;";
                            insert_row($pdo1, $table_local_logs, $query_logs);
                        }
                    }
                }
            }
        }

		$pdo1 = null;
		$pdo = null;

		echo json_encode($resp);
		die();
    }


    // empty
    $default_enable_update_flag = true;
    // if(isset($message_success) && $message_success){
    //     $default_enable_update_flag = false;
    // }
    

    $pdo = null;

?>
        <script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			// save button click
			$('#btn-save').click(function(event){
				event.preventDefault();
				let $elm = $(this);
                let $form = $elm.closest('form');
                let data = $form.serialize();
                let url = $form.attr("action");
                let validResult = $form.valid();
                let clickedId = parseInt($elm.attr('data-click-id'));
				if(validResult){
					$.ajax({
						method : 'POST',
						url : url,
						data : data,
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
								$("#message-alert").html('<strong>Success!</strong> Success! '+message);
                                if(clickedId == 0) $elm.attr('disabled', true);
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
					$("#message-alert").html('<strong>Warning!</strong> You must input correctly.');
				}
			});
		} );
		</script>

        <div class="row">
			<div class="col-md-12">
                <?php 
                    // if($message != ""){
                    //     echo '<div class="alert '.(($message_success)?'alert-success':'alert-danger').'">
                    //         <strong>'.(($message_success)?'Success!':'Warning!').'</strong> '.$message.'</div>';
                    // } 
                ?>
                <div id="message-alert" class="alert"></div>
				
				<div class="panel panel-primary" data-collapsed="0">
				
					<div class="panel-heading">
						<div class="panel-title">
							Insert MAG Device Tabel
						</div>
						
						<div class="panel-options">
							<a href="#sample-modal" data-toggle="modal" data-target="#sample-modal-dialog-1" class="bg"><i class="entypo-cog"></i></a>
							<a href="#" data-rel="collapse"><i class="entypo-down-open"></i></a>
							<a href="#" data-rel="reload"><i class="entypo-arrows-ccw"></i></a>
							<a href="#" data-rel="close"><i class="entypo-cancel"></i></a>
						</div>
					</div>
					
					<div class="panel-body">
                        <form role="form" method="POST" action="<?php echo WEB_PATH . 'index.php?page=add_mag&type=insert' . (($id>0)?'&id='.$id:''); ?>" class="form-horizontal form-groups-bordered" id="form-add-mag">
                            <input type="hidden" name="current_user_id" value="<?php echo $default_user_id; ?>">
                            <?php if($default_mac_address_flag){ ?>
                            <div class="form-group">
                                <label for="mac_address" class="col-sm-4 control-label">Device MAG Address</label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" name="mac_address" id="mac_address" placeholder="Device MAG Address" value="<?php echo $default_mac_address; ?>" required>
                                </div>
                            </div>
                            <?php } if($default_assign_account_flag){ ?>
                            <div class="form-group">
                                <label for="assign_account" class="col-sm-4 control-label">Assign the Account To a Member</label>
                                <div class="col-sm-7">
                                    <select class="form-control" name="assign_account" id="assign_account" required>
                                        <option value="" default selected disabled>Select a Account</option>
                                        <?php 
                                            foreach($usernames as $u){
                                                echo '<option value="'.$u['id'].'" '.(($default_assign_account==$u['id'])?"selected":"").' >'.$u['username'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php } if($default_expire_date_time_flag){ ?>
                            <div class="form-group">
                                <label for="" class="col-sm-4 control-label">Expire Date</label>
                                <div class="col-sm-2">
                                    <input type="checkbox" class="icheck-2" name="expire_unlimited" id="expire_unlimited" <?php echo $default_expire_unlimited; ?> >
                                    <label for="expire_unlimited">Unlimited</label>
                                </div>
                                <div class="col-sm-5">
                                    <div class="date-and-time">
                                        <input type="text" name="expire_date" id="expire_date" class="form-control datepicker" data-format="D, dd MM yyyy" value="<?php echo $default_expire_date; ?>">
                                        <input type="text" name="expire_time" id="expire_time" class="form-control timepicker" data-template="dropdown" data-show-seconds="true" data-show-meridian="true" data-minute-step="1" data-second-step="1" value="<?php echo $default_expire_time; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <?php } if($default_bouquet_flag){ ?>
                            <div class="form-group">
								<label class="col-sm-4 control-label">Select Bouquets</label>
								<div class="col-sm-7">
									<select multiple="multiple" name="select-bouquest[]" class="form-control multi-select">
                                        <?php 
                                            foreach($bouquests as $b){
                                                echo '<option value="'.$b['id'].'" '.((in_array($b['id'], $default_bouquet))?"selected":"").'>'.$b['bouquet_name'].'</option>';
                                            }
                                        ?>
									</select>
								</div>
                            </div>
                            <?php } if($default_admin_notes_flag){ ?>
                            <div class="form-group">
								<label for="admin_notes" class="col-sm-4 control-label">Note</label>
								<div class="col-sm-7">
									<textarea class="form-control" name="admin_notes" id="admin_notes" placeholder="Type your note."><?php echo $default_admin_notes; ?></textarea>
								</div>
							</div>
                            <?php } if($default_enable_update_flag){ ?>
                            <div class="form-group" style="margin-top: 15px;">
                                <div class="col-sm-offset-4 col-sm-6">
                                    <button type="submit" class="btn btn-success" id="btn-save" data-click-id="<?php echo $id; ?>"><i class="entypo-publish"></i> Save</button>
                                </div>
                            </div>
                            <?php } ?>
						</form>
					</div>
				
				</div>
			
			</div>
		</div>