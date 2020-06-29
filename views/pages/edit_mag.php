<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    $table_mag_devices = "mag_devices";
	$table_users = "users";
	
	$log_file = LIB_PATH . "customer1_log.txt";

    if(!$flag_redirect_info && $pdo){
		$mag_devices = select_rows($pdo, $table_mag_devices, "1 ORDER BY `mag_id` ASC");
        $users = select_rows($pdo, $table_users);
    }
    else{
		$mag_devices = array();
        $users = array();
    }
	
	if($page_type == "edit"){
		# Response Data Array
		$resp = "fail";

		$input_mag_id = $_POST['mag_id'];
		$input_user_id = $_POST['user_id'];
		$input_value = $_POST['value'];

		$regEx = '/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})/';
		preg_match($regEx, $input_value, $result);
		if($input_value == "Unlimited" || !empty($result)){
			// save
			if($input_value == "Unlimited") $exp_date = 'NULL';
			else $exp_date = strtotime($input_value.":00");
			$last_saved_exp_date = array_values(array_filter($users, function($u){
				global $input_user_id;
				return $u['id'] == $input_user_id;
			}));
			if($last_saved_exp_date) $old_exp_date = $last_saved_exp_date[0]['exp_date']; else $old_exp_date = "";
			$query_users = "UPDATE `".$table_users."` SET `exp_date` = '".$exp_date."' WHERE `id` = '".$input_user_id."';";
			$result1 = update_row($pdo, $table_users, $query_users);
			if($result1){
				$resp = "success";
				if(FLAG_SAVE_LOG_FILE){
					// write informations to log file
					$fp = fopen($log_file, "a");
					$logs = "\nuser:" . $user_id . ",mag_id:" . $id . ",user_id:" . $_POST['current_user_id'] 
							. ",old_exp_date:" . $selected_user['exp_date'] . ",exp_date:" . $exp_date 
							. ",old_bouquet:" . $selected_user['bouquet'] . ",bouquet:[" . $bouquest_str . "],datetime:" . time();
					fwrite($fp, $logs);
					fclose($fp);
				}
				else{
					// write informations to database
					$query_logs = "INSERT INTO `".$table_local_logs."` SET `user`='".$user_id."', `mag_id`='".$id."', `user_id`='".$_POST['current_user_id']."', 
								`old_exp_date`='".$selected_user['exp_date']."', `exp_date`='".$exp_date."', 
								`old_bouquet`='".$selected_user['bouquet']."', `bouquet`='[".$bouquest_str."', `created_at`='".time()."';";
					insert_row($pdo1, $table_local_logs, $query_logs);
				}
			}
		}

		$pdo = null;

		echo $resp;
		die();
	}

    $pdo = null;

?>
        <script type="text/javascript">
		var editor; // use a global for the submit and return data rendering in the examples

		jQuery( document ).ready( function( $ ) {
			var $table3 = jQuery("#table-edit-mag");
			
			var table3 = $table3.DataTable( {
				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
			} );
			
			// Initalize Select Dropdown after DataTables is created
			$table3.closest( '.dataTables_wrapper' ).find( 'select' ).select2( {
				minimumResultsForSearch: -1
			});
			
			// Setup - add a text input to each footer cell
			$( '#table-edit-mag tfoot th' ).each( function () {
				var title = $('#table-edit-mag thead th').eq( $(this).index() ).text();
				if(title != 'Actions' && title != ''){
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

			var oldValue;

			$('#table-edit-mag').on( 'click', 'tbody a[data-key-id=table-edit-mag-button]', function (event) {
				event.preventDefault();
				$("#table-edit-mag a[data-key-id=table-edit-mag-button]").addClass("disabled");
				let $elmInput = $(this).closest('tr').find('td[data-key-id=table-edit-mag-editable]');
				let value = $elmInput.text();
				oldValue = value;
				if($elmInput.length){
					// input
					$elmInput.html('<input type="text" value="'+value+'" />');
				}
			} );

			$( "#table-edit-mag" ).delegate( "input:text", "keypress", function(event) {
				var keycode = (event.keyCode ? event.keyCode : event.which);
				var value = $(this).val();
				let valid = (value.search(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/) != -1) &&
						(value.substr(0,4) >= 0 && value.substr(0,4) >= 2000 && value.substr(0,4) <= 2100) &&
						(value.substr(5,2) >= 0 && value.substr(5,2) <= 12) &&
						(value.substr(8,2) >= 0 && value.substr(8,2) <= 31) &&
						(value.substr(11,2) >= 0 && value.substr(11,2) <= 24) &&
						(value.substr(14,2) >= 0 && value.substr(14,2) <= 59);
				if(keycode == '13'){
					$elmParent = $(this).closest('td');
					if(valid){
						// validate date - time
					}
					else{
						// unvalidate -> Unlimited
						value = "Unlimited";
					}
					if(confirm("Do you want to save this exp date with " + value + " ?")){
						$("#message-alert").html('');
						// ajax
						$.ajax({
							method : 'POST',
							url : baseurl + 'index.php?page=edit_mag&type=edit',
							data : {
								mag_id : clicked_id,
								user_id : clicked_id2,
								value : value
							},
							success : function(result){
								if(result == "success"){
									let statusValue;
									$elmParent.html(value);
									$("#message-alert").removeClass('alert-danger');
									$("#message-alert").addClass('alert-success');
									$("#message-alert").html('<strong>Success!</strong> Saved this mag device(exp_date='+value+').');
									if(value == "Unlimited"){
										statusValue = value;
									}
									else{
									}
									// $elmParent.closest("tr").find("td[data-key-id=table-edit-mag-status]").text(statusValue);
								}
								else{
									$elmParent.html(oldValue);
									$("#message-alert").removeClass('alert-success');
									$("#message-alert").addClass('alert-danger');
									$("#message-alert").html('<strong>Warning!</strong> Failed to save this mag device.');
								}
								$("#table-edit-mag a[data-key-id=table-edit-mag-button]").removeClass("disabled");
							},
							error : function(err){
								$elmParent.html(oldValue);
								$("#message-alert").removeClass('alert-success');
								$("#message-alert").addClass('alert-danger');
								$("#message-alert").html('<strong>Warning!</strong> Failed to save this mag device.');
								$("#table-edit-mag a[data-key-id=table-edit-mag-button]").removeClass("disabled");
							}
						});
					}
				}
			});

			// editor = new $.fn.dataTable.Editor( {
			// 	ajax: "../php/staff.php",
			// 	table: "#table-edit-mag",
			// 	fields: [ {
			// 			label: "First name:",
			// 			name: "first_name"
			// 		}, {
			// 			label: "Last name:",
			// 			name: "last_name"
			// 		}, {
			// 			label: "Position:",
			// 			name: "position"
			// 		}, {
			// 			label: "Office:",
			// 			name: "office"
			// 		}, {
			// 			label: "Extension:",
			// 			name: "extn"
			// 		}, {
			// 			label: "Start date:",
			// 			name: "start_date",
			// 			type: "datetime"
			// 		}, {
			// 			label: "Salary:",
			// 			name: "salary"
			// 		}
			// 	]
			// } );
		
			// // Activate an inline edit on click of a table cell
			// $('#table-edit-mag').on( 'click', 'tbody td:not(:first-child)', function (e) {
			// 	editor.inline( this );
			// } );
		
			// $('#table-edit-mag').DataTable( {
			// 	dom: "Bfrtip",
			// 	ajax: "../php/staff.php",
			// 	order: [[ 1, 'asc' ]],
			// 	columns: [
			// 		{
			// 			data: null,
			// 			defaultContent: '',
			// 			className: 'select-checkbox',
			// 			orderable: false
			// 		},
			// 		{ data: "first_name" },
			// 		{ data: "last_name" },
			// 		{ data: "position" },
			// 		{ data: "office" },
			// 		{ data: "start_date" },
			// 		{ data: "salary", render: $.fn.dataTable.render.number( ',', '.', 0, '$' ) }
			// 	],
			// 	select: {
			// 		style:    'os',
			// 		selector: 'td:first-child'
			// 	},
			// 	buttons: [
			// 		{ extend: "create", editor: editor },
			// 		{ extend: "edit",   editor: editor },
			// 		{ extend: "remove", editor: editor }
			// 	]
			// } );
		} );
		</script>

		<div id="message-alert" class="alert"></div>
		
		<table class="table mb-0" id="table-edit-mag">
			<thead>
				<tr>
					<th>MAG ID</th>
					<th>Status</th>
					<th>Device MAG Address</th>
					<th>Expire Date</th>
					<!-- <th>Bouquets</th> -->
					<th>Note</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$current_time = time();
					foreach($mag_devices as $m){
						$user = null;
                        foreach($users as $u){
                            if($u['id'] == $m['user_id']){
                                $user = $u;
                                break;
                            }
                        }
						// if(!isset($user['id'])) break;
						if(!$user['exp_date'] || strtoupper($user['exp_date']) == 'NULL'){
							$exp_date = "Unlimited";
							$status = "UNLIMITED";
						} 
						else{
							$exp_date = date("Y-m-d H:i", $user['exp_date']);
							if($user['exp_date'] > $current_time) $status = "ENABLED";
							else $status = "EXPIRED";
						}
						$mag_address = base64_decode($m['mac']);
				?>
					<tr>
						<td class="text-center"><?php echo $m['mag_id']; ?></td>
						<td data-key-id="table-edit-mag-status"><?php echo $status; ?></td>
						<td><?php echo $mag_address; ?></td>
						<td data-key-id="table-edit-mag-editable"><?php echo $exp_date; ?></td>
						<!-- <td><?php echo $user['bouquet']; ?></td> -->
						<td><?php echo $user['admin_notes']; ?></td>
						<td>
							<!-- <a href="#" class="btn btn-default btn-sm btn-icon icon-left" data-key-id="table-edit-mag-button" onclick="clicked_id=<?php echo $m['mag_id']; ?>; clicked_id2=<?php echo $user['id']; ?>;">
								<i class="entypo-pencil"></i>
								Edit
							</a> -->
							<a href="<?php echo WEB_PATH . 'index.php?page=add_mag&id=' . $m['mag_id']; ?>" class="btn btn-default btn-sm btn-icon icon-left">
								<i class="entypo-pencil"></i>
								Edit
							</a>
						</td>
					</tr>
				<?php
					}
				?>
			</tbody>
			<tfoot>
                <tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<!-- <th></th> -->
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table><!--end table-->