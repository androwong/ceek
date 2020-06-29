<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

	$table_bouquests = "bouquets";
	$table_mag_devices = "mag_devices";
	$table_users = "users";

	if(!$flag_redirect_info && $pdo){
		$bouquests_temp = select_rows($pdo, $table_bouquests);
		$bouquests = array();
		foreach($bouquests_temp as $b){
			$bouquests[$b['id']] = $b;
		}
		$mag_devices_temp = select_rows($pdo, $table_mag_devices);
		$mag_devices = array();
		foreach($mag_devices_temp as $m){
			$mag_devices[$m['mag_id']] = $m;
		}
		$users_temp = select_rows($pdo, $table_users);
		$users = array();
		foreach($users_temp as $u){
			$users[$u['id']] = $u;
		}
    }
    else{
		$bouquests = array();
		$mag_devices = array();
		$users = array();
	}

	$log_file = LIB_PATH . "customer1_log.txt";

	function match_bouquet($bouquet_id){
		global $bouquests;
		if(isset($bouquests[$bouquet_id])) return $bouquests[$bouquet_id]['bouquet_name'];
		else return "";
	}

	function match_mag($mag_id){
		global $mag_devices;
		if(isset($mag_devices[$mag_id])) return base64_decode($mag_devices[$mag_id]['mac']);
		else return $mag_id;
	}

	function match_user($user_id){
		global $users;
		if(isset($users[$user_id])) return $users[$user_id]['admin_notes'];
		else return $user_id;
	}

	$customer_user_name = select_rows($pdo1, $table_local_users, "`id`=2")[0]['display_name'];

	if(FLAG_SAVE_LOG_FILE){
		// read logs from file
		$fp = fopen($log_file, "r");
		$results = array();
		while (!feof($fp) ) {
			$line = trim(fgets($fp));
			$regEx = '/user:(\d+),mag_id:(\d+),user_id:(\d+),old_exp_date:(\d+),exp_date:(\d+),old_mac:([0-9a-zA-Z\=\:]+),mac:([0-9a-zA-Z\=\:]+),old_bouquet:\[([0-9\,]+)\],bouquet:\[([0-9\,]+)\],datetime:(\d+),is_create:(\d+)/';
			preg_match($regEx, $line, $result);
			if(!empty($result)){
				$old_bouquet = explode(",", $result[8]);
				$new_bouquet = explode(",", $result[9]);
				$added_bouquet = array_map("match_bouquet", array_diff($new_bouquet, $old_bouquet));
				$removed_bouquet = array_map("match_bouquet", array_diff($old_bouquet, $new_bouquet));
				$added_bouquet_str = implode(",", $added_bouquet);
				$removed_bouquet_str = implode(",", $removed_bouquet);
				$results[] = array(
					// 'user' => $result[1],
					'user' => $customer_user_name,
					'mag_id' => match_mag($result[2]),
					'user_id' => match_user($result[3]),
					'old_exp_date' => date("Y-m-d H:i", $result[4]),
					'exp_date' => date("Y-m-d H:i", $result[5]),
					'old_mac' => base64_decode($result[6]),
					'new_mac' => base64_decode($result[7]),
					'datetime' => date("Y-m-d H:i:s", $result[10]),
					'added_bouquet' => $added_bouquet_str,
					'removed_bouquet' => $removed_bouquet_str,
					'is_create' => $result[11]
				);
			}
		}
		fclose($fp);
	}
	else{
		// read logs from database
		$rows = select_rows($pdo1, $table_local_logs, "`user`=2");
		$results = array();
		foreach($rows as $result){
			$old_bouquet = explode(",", substr($result['old_bouquet'], 1, strlen($result['old_bouquet'])-2));
			$new_bouquet = explode(",", substr($result['bouquet'], 1, strlen($result['bouquet'])-2));
			$added_bouquet = array_map("match_bouquet", array_diff($new_bouquet, $old_bouquet));
			$removed_bouquet = array_map("match_bouquet", array_diff($old_bouquet, $new_bouquet));
			$added_bouquet_str = implode(",", $added_bouquet);
			$removed_bouquet_str = implode(",", $removed_bouquet);
			$results[] = array(
				// 'user' => $result[1],
				'user' => $customer_user_name,
				'mag_id' => match_mag($result['mag_id']),
				'user_id' => match_user($result['user_id']),
				'old_exp_date' => date("Y-m-d H:i", $result['old_exp_date']),
				'exp_date' => date("Y-m-d H:i", $result['exp_date']),
				'old_mac' => base64_decode($result['old_mac']),
				'new_mac' => base64_decode($result['mac']),
				'datetime' => date("Y-m-d H:i:s", $result['created_at']),
				'added_bouquet' => $added_bouquet_str,
				'removed_bouquet' => $removed_bouquet_str,
				'is_create' => $result['is_create']
			);
		}
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
		});
		</script>

		<div id="message-alert"></div>
		
		<table class="table table-bordered datatable" id="table-3">
			<thead>
				<tr>
					<th>Customer</th>
					<th>Old MAG Device</th>
					<th>MAG Device</th>
					<th>User Note</th>
					<th>Exp Date From</th>
					<th>Exp Date To</th>
					<th>Added Bouquets</th>
					<th>Removed Bouquets</th>
					<th>Modified Time</th>
					<th>Create/Update</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($results as $r){
                ?>
				<tr class="odd gradeX">
					<td><?php echo $r['user']; ?></td>
					<td><?php echo $r['old_mac']; ?></td>
					<td><?php echo $r['new_mac']; ?></td>
					<td><?php echo $r['user_id']; ?></td>
					<td><?php echo $r['old_exp_date']; ?></td>
					<td><?php echo $r['exp_date']; ?></td>
					<td><?php echo $r['added_bouquet']; ?></td>
					<td><?php echo $r['removed_bouquet']; ?></td>
					<td><?php echo $r['datetime']; ?></td>
					<td><?php echo ($r['is_create'])?"CREATE":"UPDATE"; ?></td>
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
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</tfoot>
		</table>