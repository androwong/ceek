<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

    $table_mag_devices = "mag_devices";
    $table_reg_users = "reg_users";
    $table_users = "users";
    $table_bouquests = "bouquets";

    if(!$flag_redirect_info && $pdo){
        $mag_devices = select_rows($pdo, $table_mag_devices);
        $usernames = select_rows($pdo, $table_reg_users);
        $users = select_rows($pdo, $table_users);
        $bouquests = select_rows($pdo, $table_bouquests);
    }
    else{
        $mag_devices = array();
        $usernames = array();
        $users = array();
        $bouquests = array();
	}
	
	if($page_type == "show"){
		$input_id = $_POST['id'];

		$selected_mag = array_values(array_map(function($m){
			$m['mac'] = base64_decode($m['mac']);
			return $m;
		}, array_filter($mag_devices, function($s){
			global $input_id;
			return $s['mag_id'] == $input_id;
		})));

		$pdo = null;

		echo json_encode($selected_mag[0]);
		die();
	}
	else if($page_type == "edit"){
		$input_id = $_POST['id'];

		$selected_mag = array_values(array_map(function($m){
			global $users, $bouquests;
			$result['mac'] = base64_decode($m['mac']);
			foreach($users as $u){
				if($u['id'] == $m['user_id']){
					$user = $u;
					break;
				}
			}
			if(!$user['exp_date'] || strtoupper($user['exp_date']) == 'NULL') $result['exp_date'] = "Unlimited";
			else $result['exp_date'] = date("Y-m-d H:i", $user['exp_date']);
			$result['admin_notes'] = $user['admin_notes'];
			$result['select-bouquest'] = array();
			$selected_bouquests = explode(",", substr($user['bouquet'], 1, strlen($user['bouquet'])-2));
			foreach($bouquests as $b){
				$result['select-bouquest'][$b['id']]= in_array($b['id'], $selected_bouquests)?'selected':'';
			}
			return $result;
		}, array_filter($mag_devices, function($s){
			global $input_id;
			return $s['mag_id'] == $input_id;
		})));

		$pdo = null;

		echo json_encode($selected_mag[0]);
		die();
	}
	else if($page_type == "update"){
		# Response Data Array
		$resp = array();
		
		$input_id = $_POST['id'];
		$input_id2 = $_POST['id2'];
		$input_exp_date = $_POST['exp_date'];
		$input_admin_notes = $_POST['admin_notes'];
		$input_mac = $_POST['mac'];
		$input_bouquest = $_POST['bouquest'];

		$resp['status'] = "fail";

		$exp_date = $input_exp_date;
		$admin_notes = $input_admin_notes;
		$bouquest_str = "[".implode(",", $input_bouquest)."]";
		$mac_address = get_mac_address($input_mac);

		if($mac_address !== false && !$flag_redirect_info && $pdo){
            $query_user = "UPDATE `".$table_users."` SET  `exp_date` = '".$exp_date."', `admin_notes` = '".$admin_notes."', `bouquet` = '".$bouquest_str."' WHERE `id` = '".$input_id2."';";
            $result1 = update_row($pdo, $table_users, $query_user);

            if($result1){
                $query_mag = "UPDATE `".$table_mag_devices."` SET `mac` = '".$mac_address."' WHERE `mag_id` = '".$input_id."';";
                $result2 = update_row($pdo, $table_mag_devices, $query_mag);

                if($result2){
					$resp['status'] = "success";
                }
            }
        }

		$pdo = null;

		echo json_encode($resp);
		die();
	}

    $pdo = null;

?>
        <script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
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

			// add new stream button click handler
			$("#modal-edit-manage_mag button[data-id=modal_edit_submit]").click(function(){
				var mac = $("#modal-edit-manage_mag [data-id=mac]").val();
				var exp_date = $("#modal-edit-manage_mag [data-id=exp_date]").val();
				var admin_notes = $("#modal-edit-manage_mag [data-id=admin_notes]").val();
				var bouquest = [];
				$("#modal-edit-manage_mag .ms-elem-selection").each(function(){
					if($(this).css("display") != "none"){
						let key = $(this).attr("id").replace("-selection", "");
						bouquest.push(key);
					}
				});
				$.ajax({
					method : 'POST',
					url : baseurl + 'index.php?page=manage_mag&type=update',
					data : {
						mac : mac,
						exp_date : exp_date,
						admin_notes : admin_notes,
						bouquest : bouquest,
						id : clicked_id,
						id2 : clicked_id2
					},
					success : function(result){
						console.log(result);
					},
					error : function(result){
						alert("An error occoured!");
					}
				});
			});
		} );
		</script>
		
		<table class="table table-bordered datatable" id="table-3">
			<thead>
				<tr>
					<th>Status</th>
					<th>Owner</th>
					<th>Device MAG Address</th>
					<th>Expire Date</th>
					<th>Started Date</th>
					<th>Note</th>
					<th style="min-width: 128px;">Actions</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($mag_devices as $m){ 
                        $status = "Enabled";
                        $user = null;
                        foreach($users as $u){
                            if($u['id'] == $m['user_id']){
                                $user = $u;
                                break;
                            }
                        }
                        // if(!$user) break;
                        foreach($usernames as $u){
                            if($u['id'] == $user['member_id']){
                                $owener = $u['username'];
                                break;
                            }
						}
                        $mag_address = base64_decode($m['mac']);
                        if(!$user['exp_date'] || strtoupper($user['exp_date']) == 'NULL') $exp_date = "Unlimited";
						else $exp_date = date("Y-m-d H:i", $user['exp_date']);
						$stated_date = date("Y-m-d H:i", $user['created_at']);
                ?>
				<tr class="odd gradeX">
					<td><?php echo $status; ?></td>
					<td><?php echo $owener; ?></td>
					<td><?php echo $mag_address; ?></td>
					<td><?php echo $exp_date; ?></td>
					<td><?php echo $stated_date; ?></td>
					<td><?php echo $user['admin_notes']; ?></td>
					<td>
						<a href="#" class="btn btn-orange btn-sm btn-icon icon-left" data-toggle="modal" data-target="#modal-show-manage_mag" onclick="clicked_id=<?php echo $m['mag_id']; ?>; showAjaxModalFromUrl('manage_mag');">
							<i class="entypo-search"></i>
							Show
						</a>
						<!-- <a href="#" class="btn btn-default btn-sm btn-icon icon-left" data-toggle="modal" data-target="#modal-edit-manage_mag" onclick="clicked_id=<?php echo $m['mag_id']; ?>; clicked_id2=<?php echo $user['id']; ?>; showAjaxModalFromUrl('manage_mag', 'edit');"> -->
						<a href="<?php echo WEB_PATH . 'index.php?page=add_mag&id=' . $m['mag_id']; ?>" class="btn btn-default btn-sm btn-icon icon-left">
							<i class="entypo-pencil"></i>
							Edit
						</a>
						<!-- <a href="#" class="btn btn-danger btn-sm btn-icon icon-left">
							<i class="entypo-cancel"></i>
							Delete
						</a> -->
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