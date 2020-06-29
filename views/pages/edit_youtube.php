<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');


	$default_youtube_id = 0;
	if(isset($_GET['id']) && $_GET['id'] > 0){
		// edit
		$default_youtube_id = $_GET['id'];
		$current_youtube = select_rows($pdo1, $table_local_manage_youtube, "`id` = '".$default_youtube_id."'");
		if(!empty($current_youtube)){
			$default_username = $current_youtube[0]['username'];
			$default_channel = $current_youtube[0]['channel'];;
			$default_keyword = $current_youtube[0]['keywords'];
			$default_tv_series = $current_youtube[0]['tv_series'];
			$default_folderpath = $current_youtube[0]['folderpath'];
		}
		else $default_youtube_id = 0;
	}
	if($default_youtube_id == 0){
		// new
		$default_youtube_id = 0;
		$default_username = "";
		$default_channel = "";
		$default_keyword = "";
		$default_tv_series = -1;
		if(isset($_GET['series_id']) && $_GET['series_id'] > 0) $default_tv_series = $_GET['series_id'];
		$default_folderpath = "";
	}


	$table_series = "series";
	$tv_series = select_rows($pdo, $table_series);


	if($page_type == "save"){
		# Response Data Array
		$resp = array();

		$input_id = $_POST['id'];
		$input_username = $_POST['username'];
		$input_channel = $_POST['channel'];
		$input_keyword = $_POST['keyword'];
		$input_tv_series = $_POST['tvseries'];
		$input_folderpath = $_POST['folderpath'];

		$resp['status'] = "fail";
		$resp['message'] = "";

		if($input_id > 0){
			// update
			$youtube_query = "UPDATE `".$table_local_manage_youtube."` SET `username` = '".$input_username."', `channel` = '".$input_channel."', `keywords` = '".$input_keyword."',
								`tv_series` = '".$input_tv_series."', `folderpath` = '".$input_folderpath."' WHERE `id` = '".$input_id."';";
			update_row($pdo1, $table_local_manage_youtube, $youtube_query);
			$resp['status'] = "success";
			$resp['message'] = "Updated the current youtube.";
		}
		else{
			// create
			$youtube_query = "INSERT INTO `".$table_local_manage_youtube."` SET `username` = '".$input_username."', `channel` = '".$input_channel."', `keywords` = '".$input_keyword."',
								`tv_series` = '".$input_tv_series."', `folderpath` = '".$input_folderpath."';";
			update_row($pdo1, $table_local_manage_youtube, $youtube_query);
			$resp['status'] = "success";
			$resp['message'] = "Created a new youtube.";
		}

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
				$('#btn-save-youtube').click(function(event){
					event.preventDefault();
					let id = $('#input-save-youtube-id').val();
					let username = $('#input-save-youtube-username').val();
					let channel = $('#input-save-youtube-channel').val();
					let keyword = $('#input-save-youtube-keyword').val();
					let tvseries = $('#input-save-youtube-tvseries').val();
					let folderpath = $('#input-save-youtube-folderpath').val();
					if((username != "" || channel != "") && keyword != "" && tvseries > 0 && folderpath != ""){
						$.ajax({
							method : 'POST',
							url : baseurl + 'index.php?page=edit_youtube&type=save',
							data : {
								id : id,
								username : username,
								channel : channel,
								keyword : keyword,
								tvseries : tvseries,
								folderpath : folderpath,
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
						$("#message-alert").html('<strong>Warning!</strong> You must input channel or username, keyword, download folder and select TV series.');
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
							<?php echo ($default_youtube_id==0)?"ADD a new Youtube":"EDIT Youtube"; ?>
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

							<input type="hidden" name="input-save-youtube-id" id="input-save-youtube-id" value="<?php echo $default_youtube_id; ?>">

							<div class="form-group">
								<label for="input-save-youtube-channel" class="col-sm-3 control-label">Channel</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-save-youtube-channel" placeholder="Input the channel code to search youtube." value="<?php echo $default_channel; ?>">
								</div>
							</div>

							<div class="form-group">
								<label for="input-save-youtube-username" class="col-sm-3 control-label">User Name</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-save-youtube-username" placeholder="Input the username to search youtube." value="<?php echo $default_username; ?>">
								</div>
							</div>

							<div class="form-group">
								<label for="input-save-youtube-keyword" class="col-sm-3 control-label">Keyword</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-save-youtube-keyword" placeholder="Input the keyword to search youtube." value="<?php echo $default_keyword; ?>">
								</div>
							</div>

							<div class="form-group">
                                <label for="input-save-youtube-tvseries" class="col-sm-3 control-label">Select TV Series</label>
                                <div class="col-sm-5">
                                    <select class="form-control" name="input-save-youtube-tvseries" id="input-save-youtube-tvseries" required>
                                        <option value="" default selected disabled>Select a TV Series</option>
                                        <?php 
                                            foreach($tv_series as $s){
                                                echo '<option value="'.$s['id'].'" '.(($default_tv_series==$s['id'])?"selected":"").' >'.$s['title'].'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>

							<div class="form-group">
								<label for="input-save-youtube-folderpath" class="col-sm-3 control-label">Download Folder</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" id="input-save-youtube-folderpath" placeholder="Input the Folder path to download." value="<?php echo $default_folderpath; ?>">
								</div>
							</div>

							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-5">
									<button type="submit" class="btn btn-primary" id="btn-save-youtube"><?php echo ($default_youtube_id==0)?"Create":"Update"; ?></button>
									<a class="btn btn-info" href="<?php echo WEB_PATH; ?>index.php?page=manage_youtube" type="button">Show List</a>
								</div>
							</div>

						</form>

					</div>
				</div>
			</div>
		</div>