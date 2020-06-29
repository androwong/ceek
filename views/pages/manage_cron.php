<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
	

	$cron_jobs = select_rows($pdo1, $table_local_cron_jobs);

	if($page_type == "turn"){
		# Response Data Array
		$resp = array();

		$input_id = $_POST['cron_id'];
		$input_turn = $_POST['turn'];
		if($input_turn) $next_val = 0; else $next_val = 1;

		$resp['status'] = "fail";
		$resp['message'] = "";
		$resp['next_value'] = $next_val;

		// update
		$cron_query = "UPDATE `".$table_local_cron_jobs."` SET `is_run` = '".$next_val."' WHERE `id` = '".$input_id."';";
		$result1 = update_row($pdo1, $table_local_cron_jobs, $cron_query);

		if($result1){
			$resp['status'] = "success";
			$resp['message'] = "Success to turn " . (($next_val)?"on":"off");
		}

		$pdo1 = null;
		$pdo = null;

		echo json_encode($resp);
		die();
	}
	else if($page_type == "remove"){
		# Response Data Array
		$resp = array();

		$input_id = $_POST['cron_id'];

		$resp['status'] = "fail";
		$resp['message'] = "";

		// delete
		$cron_query = "`id` = '".$input_id."'";
		$result1 = delete_rows($pdo1, $table_local_cron_jobs, $cron_query);

		if($result1){
			$resp['status'] = "success";
		}

		$pdo1 = null;
		$pdo = null;

		echo json_encode($resp);
		die();
	}
	else if($page_type == "turn_log"){
		# Response Data Array
		$resp = array();

		$input_id = $_POST['cron_id'];
		$input_turn = $_POST['turn'];
		if($input_turn) $next_val = 0; else $next_val = 1;

		$resp['status'] = "fail";
		$resp['message'] = "";
		$resp['next_value'] = $next_val;

		// update
		$cron_query = "UPDATE `".$table_local_cron_jobs."` SET `is_log` = '".$next_val."' WHERE `id` = '".$input_id."';";
		$result1 = update_row($pdo1, $table_local_cron_jobs, $cron_query);

		if($result1){
			$resp['status'] = "success";
			$resp['message'] = "Success to turn " . (($next_val)?"on":"off");
		}

		$pdo1 = null;
		$pdo = null;

		echo json_encode($resp);
		die();
	}

	$table_series = "series";
	$table_streams = "streams";

	$streams = select_rows($pdo, $table_streams);
	$series = select_rows($pdo, $table_series);
	$youtubes = select_rows($pdo1, $table_local_manage_youtube);
	$setting_rows = select_rows($pdo1, $table_local_settings, "1 ORDER BY `created_at` DESC");

	$check_stream_step_time = DEFAULT_CHECK_STREAM_TIME;
	if(!empty($setting_rows)){
		foreach($setting_rows as $row){
			if($row['setting_key'] == 'check_stream'){
				$check_stream_step_time = $row['setting_val'];
				break;
			}
		}
	}

	$pdo = null;

?>
        <script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			var $table1 = jQuery("#table-1");
			
			var table1 = $table1.DataTable( {
				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
			} );
			
			// Initalize Select Dropdown after DataTables is created
			$table1.closest( '.dataTables_wrapper' ).find( 'select' ).select2( {
				minimumResultsForSearch: -1
			});
			
			// Setup - add a text input to each footer cell
			$( '#table-1 tfoot th' ).each( function () {
				var title = $('#table-1 thead th').eq( $(this).index() ).text();
				if(title != 'Actions'){
					$(this).html( '<input type="text" class="form-control" placeholder="Search ' + title + '" />' );
				}
			} );
			
			// Apply the search
			table1.columns().every( function () {
				var that = this;
			
				$( 'input', this.footer() ).on( 'keyup change', function () {
					if ( that.search() !== this.value ) {
						that
							.search( this.value )
							.draw();
					}
				} );
			} );

			var $table3 = jQuery("#table-3");
			
			var table3 = $table3.DataTable( {
				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
			} );
			
			// Initalize Select Dropdown after DataTables is created
			$table3.closest( '.dataTables_wrapper' ).find( 'select' ).select2( {
				minimumResultsForSearch: -1
			});
			
			// Setup - add a text input to each footer cell
			$( '#table-3 tfoot th' ).each( function () {
				var title = $('#table-3 thead th').eq( $(this).index() ).text();
				if(title != 'Actions'){
					$(this).html( '<input type="text" class="form-control" placeholder="Search ' + title + '" />' );
				}
			} );
			
			// Apply the search
			table3.columns().every( function () {
				var that = this;
			
				$( 'input', this.footer() ).on( 'keyup change', function () {
					if ( that.search() !== this.value ) {
						that
							.search( this.value )
							.draw();
					}
				} );
			} );

			// turn button click
			$('table').delegate('.btn-turn', 'click', function(event){
				event.preventDefault();
				let $elm = $(this);
				let id_val = $elm.attr('data-click-id');
				let turn_val = $elm.attr('data-status-turn');
				if(id_val != ""){
					$.ajax({
						method : 'POST',
						url : baseurl + 'index.php?page=manage_cron&type=turn',
						data : {
							cron_id : id_val,
							turn : turn_val
						},
						success : function(result){
							let status, message = '', next_value = 0;
							try{
								result = JSON.parse(result);
								status = result.status;
								message = result.message;
								next_value = result.next_value;
							}
							catch(e){
							}
							if(status == "success"){
								$("#message-alert").removeClass('alert-danger');
								$("#message-alert").addClass('alert-success');
								$("#message-alert").html('<strong>Success!</strong> Success! '+message);
								$elm.attr('data-status-turn', next_value);
								if(next_value){
									$elm.removeClass('btn-success');
									$elm.addClass('btn-danger');
									$elm.closest('tr').children().first().html('<i class="entypo-db-shape text-success"></i>');
								}
								else{
									$elm.removeClass('btn-danger');
									$elm.addClass('btn-success');
									$elm.closest('tr').children().first().html('<i class="entypo-db-shape text-danger"></i>');
								}
							}
							else{
								$("#message-alert").removeClass('alert-success');
								$("#message-alert").addClass('alert-danger');
								$("#message-alert").html('<strong>Warning!</strong> Failed to turn! '+message);
							}
						},
						error : function(err){
							$("#message-alert").removeClass('alert-success');
							$("#message-alert").addClass('alert-danger');
							$("#message-alert").html('<strong>Warning!</strong> Failed to turn.');
						}
					});
				}
				else{
					$("#message-alert").removeClass('alert-success');
					$("#message-alert").addClass('alert-danger');
					$("#message-alert").html('<strong>Warning!</strong> You must select the correct Stream or YouTube.');
				}
			});

			// remove button click
			$('table').delegate('.btn-remove', 'click', function(event){
				event.preventDefault();
				let $elm = $(this);
				let id_val = $elm.attr('data-click-id');
				if(id_val != ""){
					$.ajax({
						method : 'POST',
						url : baseurl + 'index.php?page=manage_cron&type=remove',
						data : {
							cron_id : id_val
						},
						success : function(result){
							let status, message = '', next_value = 0;
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
								$("#message-alert").html('<strong>Success!</strong> Success to remove! '+message);
								$elm.closest('tr').remove();
							}
							else{
								$("#message-alert").removeClass('alert-success');
								$("#message-alert").addClass('alert-danger');
								$("#message-alert").html('<strong>Warning!</strong> Failed to remove! '+message);
							}
						},
						error : function(err){
							$("#message-alert").removeClass('alert-success');
							$("#message-alert").addClass('alert-danger');
							$("#message-alert").html('<strong>Warning!</strong> Failed to remove.');
						}
					});
				}
				else{
					$("#message-alert").removeClass('alert-success');
					$("#message-alert").addClass('alert-danger');
					$("#message-alert").html('<strong>Warning!</strong> You must select the correct Stream or YouTube.');
				}
			});

			// log button click
			$('table').delegate('.btn-log', 'click', function(event){
				event.preventDefault();
				let $elm = $(this);
				let id_val = $elm.attr('data-click-id');
				let turn_val = $elm.attr('data-status-turn');
				if(id_val != ""){
					$.ajax({
						method : 'POST',
						url : baseurl + 'index.php?page=manage_cron&type=turn_log',
						data : {
							cron_id : id_val,
							turn : turn_val
						},
						success : function(result){
							let status, message = '', next_value = 0;
							try{
								result = JSON.parse(result);
								status = result.status;
								message = result.message;
								next_value = result.next_value;
							}
							catch(e){
							}
							if(status == "success"){
								$("#message-alert").removeClass('alert-danger');
								$("#message-alert").addClass('alert-success');
								$("#message-alert").html('<strong>Success!</strong> '+message);
								$elm.attr('data-status-turn', next_value);
								if(next_value){
									$elm.removeClass('btn-success');
									$elm.addClass('btn-danger');
								}
								else{
									$elm.removeClass('btn-danger');
									$elm.addClass('btn-success');
								}
							}
							else{
								$("#message-alert").removeClass('alert-success');
								$("#message-alert").addClass('alert-danger');
								$("#message-alert").html('<strong>Warning!</strong> '+message);
							}
						},
						error : function(err){
							$("#message-alert").removeClass('alert-success');
							$("#message-alert").addClass('alert-danger');
							$("#message-alert").html('<strong>Warning!</strong> Failed to log turn.');
						}
					});
				}
				else{
					$("#message-alert").removeClass('alert-success');
					$("#message-alert").addClass('alert-danger');
					$("#message-alert").html('<strong>Warning!</strong> You must select the correct Stream or YouTube.');
				}
			});
		} );
		</script>

		<div id="message-alert" class="alert"></div>
		
		<table class="table table-bordered datatable" id="table-1">
			<thead>
				<tr>
					<th>Status</th>
					<th>Page</th>
					<th>Stream ID</th>
					<th>Stream Display Name</th>
					<th>Step Time</th>
					<th style="width: 300px;">Actions</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($cron_jobs as $c){
						if($c['page'] != 'stream') continue;
						$selected_stream = array_values(array_filter($streams, function($s){
							global $c;
							return $s['id'] == $c['param_id'];
						}));
						if(!empty($selected_stream)) $selected_stream_display_name = $selected_stream[0]['stream_display_name']; 
						else $selected_stream_display_name = '';
                ?>
				<tr class="odd gradeX">
					<td class="text-center"><?php echo ($c['is_run'])?'<i class="entypo-db-shape text-success"></i>':'<i class="entypo-db-shape text-danger"></i>'; ?></td>
					<td>Check Stream</td>
					<td><?php echo $c['param_id']; ?></td>
					<td><?php echo $selected_stream_display_name; ?></td>
					<td>every <?php echo $check_stream_step_time; ?>minute(s)</td>
					<td>
						<a href="#" class="btn <?php echo ($c['is_run'])?"btn-danger":"btn-success"; ?> btn-sm btn-icon icon-left btn-turn" data-click-id="<?php echo $c['id']; ?>" data-status-turn="<?php echo $c['is_run']; ?>">
							<i class="entypo-switch"></i>
							Turn On/Off
						</a>
						<a href="#" class="btn btn-orange btn-sm btn-icon icon-left btn-remove" data-click-id="<?php echo $c['id']; ?>">
							<i class="entypo-trash"></i>
							Remove
						</a>
						<a href="#" class="btn <?php echo ($c['is_log'])?"btn-danger":"btn-success"; ?> btn-sm btn-icon icon-left btn-log" data-click-id="<?php echo $c['id']; ?>" data-status-turn="<?php echo $c['is_log']; ?>">
							<i class="entypo-switch"></i>
							Log On/Off
						</a>
					</td>
                </tr>
                <?php } ?>
			</tbody>
			<tfoot>
                <tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>

		<div class="row" style="height:30px;"></div>

		<table class="table table-bordered datatable" id="table-3">
			<thead>
				<tr>
					<th>Status</th>
					<th>Page</th>
					<th>YouTube ID</th>
					<th>Keyword</th>
					<th>TV Series</th>
					<th>Step Time</th>
					<th style="width: 300px;">Actions</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($cron_jobs as $c){
						if($c['page'] != 'youtube') continue;
						$selected_youtube_channel = '';
						$selected_youtube_keyword = '';
						$selected_tv_series = '';
						$selected_step_day = '';
						$selected_step_hour = '';

						$selected_youtube = array_values(array_filter($youtubes, function($y){
							global $c;
							return $y['id'] == $c['param_id'];
						}));
						if(!empty($selected_youtube)){
							$selected_youtube_channel = $selected_youtube[0]['channel']; 
							$selected_youtube_keyword = $selected_youtube[0]['keywords']; 
							$selected_series = array_values(array_filter($series, function($s){
								global $selected_youtube;
								return $s['id'] == $selected_youtube[0]['tv_series'];
							}));
						}
						if(!empty($selected_series)) $selected_tv_series = $selected_series[0]['title']; 
						$selected_settings = array_values(array_filter($setting_rows, function($s){
							global $c;
							return $s['setting_key'] == "check_youtube_".$c['param_id']."_day";
						}));
						if(!empty($selected_settings)){
							$selected_step_days = explode(",", $selected_settings[0]['setting_val']);
							foreach($selected_step_days as $d){
								$selected_step_day .= get_day($d) . ",";
							}
						} 
						$selected_settings = array_values(array_filter($setting_rows, function($s){
							global $c;
							return $s['setting_key'] == "check_youtube_".$c['param_id']."_hour";
						}));
						if(!empty($selected_settings)) $selected_step_hour = $selected_settings[0]['setting_val'] . " h";
						$selected_settings = array_values(array_filter($setting_rows, function($s){
							global $c;
							return $s['setting_key'] == "check_youtube_".$c['param_id']."_minute";
						}));
						if(!empty($selected_settings)) $selected_step_minute = $selected_settings[0]['setting_val'] . " m";
                ?>
				<tr class="odd gradeX">
					<td class="text-center"><?php echo ($c['is_run'])?'<i class="entypo-db-shape text-success"></i>':'<i class="entypo-db-shape text-danger"></i>'; ?></td>
					<td>YouTube</td>
					<td><?php echo $c['param_id']; ?></td>
					<td><?php echo $selected_youtube_keyword; ?></td>
					<td><?php echo $selected_tv_series; ?></td>
					<td><?php echo $selected_step_day . "/" . $selected_step_hour . "/" . $selected_step_minute; ?></td>
					<td>
						<a href="#" class="btn <?php echo ($c['is_run'])?"btn-danger":"btn-success"; ?> btn-sm btn-icon icon-left btn-turn" data-click-id="<?php echo $c['id']; ?>" data-status-turn="<?php echo $c['is_run']; ?>">
							<i class="entypo-switch"></i>
							Turn On/Off
						</a>
						<a href="#" class="btn btn-orange btn-sm btn-icon icon-left btn-remove" data-click-id="<?php echo $c['id']; ?>">
							<i class="entypo-trash"></i>
							Remove
						</a>
						<a href="#" class="btn <?php echo ($c['is_log'])?"btn-danger":"btn-success"; ?> btn-sm btn-icon icon-left btn-log" data-click-id="<?php echo $c['id']; ?>" data-status-turn="<?php echo $c['is_log']; ?>">
							<i class="entypo-switch"></i>
							Log On/Off
						</a>
					</td>
                </tr>
                <?php } ?>
			</tbody>
			<tfoot>
                <tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>