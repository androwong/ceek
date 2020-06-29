<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

	
	$cron_logs = select_rows($pdo1, $table_local_cron_logs, "1 ORDER BY `created_at` ASC");
	$cron_jobs = select_rows($pdo1, $table_local_cron_jobs);

	if($page_type == "clear"){
		# Response Data Array
		$resp = array();

		$resp['status'] = "fail";

		// delete
		$cron_query = "1";
		$result1 = delete_rows($pdo1, $table_local_cron_logs, $cron_query);

		if($result1){
			$resp['status'] = "success";
		}

		$pdo1 = null;

		echo json_encode($resp);
		die();
	}

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

			$("#btn-clear").click(function(){
				$.ajax({
					method : 'POST',
					url : baseurl + 'index.php?page=show_cron&type=clear',
					success : function(result){
						let status;
						try{
							result = JSON.parse(result);
							status = result.status;
						}
						catch(e){
						}
						if(status == "success"){
							$("#message-alert").removeClass('alert-danger');
							$("#message-alert").addClass('alert-success');
							$("#message-alert").html('<strong>Success!</strong> Success to clear.');
							$('#table-1 tr').remove();
							$('#table-3 tr').remove();
						}
						else{
							$("#message-alert").removeClass('alert-success');
							$("#message-alert").addClass('alert-danger');
							$("#message-alert").html('<strong>Warning!</strong> Failed to clear.');
						}
					},
					error : function(err){
						$("#message-alert").removeClass('alert-success');
						$("#message-alert").addClass('alert-danger');
						$("#message-alert").html('<strong>Warning!</strong> Failed to clear.');
					}
				});
			});
		});
		</script>

		<div id="message-alert" class="alert"></div>

		<div class="row">
			<div class="col-sm-3 col-sm-offset-9 text-right">
				<button class="btn btn-danger btn-sm btn-icon icon-left" id="btn-clear" style="margin:10px;">
					<i class="entypo-trash"></i>
					Clear
				</button>
			</div>
		</div>
		
		<table class="table table-bordered datatable" id="table-1">
			<thead>
				<tr>
					<th>Status</th>
					<th>Cron Type</th>
					<th>Cron ID</th>
					<th>Channel</th>
					<th>Result</th>
					<th>Run At</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($cron_logs as $c){
						$c['job'] = array();
						foreach($cron_jobs as $job){
							if($job['id'] == $c['cron_id']){
								$c['job'] = $job;
								break;
							}
						}
						if(empty($c['job'])) continue;
						if($c['job']['page'] != 'stream') continue;
						$c['date'] = date("Y-m-d H:i:s", $c['created_at']);
                ?>
				<tr class="odd gradeX">
					<td class="text-center"><?php echo ($c['status'])?'<i class="entypo-db-shape text-success"></i>':'<i class="entypo-db-shape text-danger"></i>'; ?></td>
					<td><?php echo $c['job']['page']; ?></td>
					<td><?php echo $c['job']['param_id']; ?></td>
					<td><?php echo $c['keywords']; ?></td>
					<td><?php echo $c['message']; ?></td>
					<td><?php echo $c['date']; ?></td>
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
					<th>Cron Type</th>
					<th>Cron ID</th>
					<th>Keywords</th>
					<th>Result</th>
					<th>Run At</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($cron_logs as $c){
						$c['job'] = array();
						foreach($cron_jobs as $job){
							if($job['id'] == $c['cron_id']){
								$c['job'] = $job;
								break;
							}
						}
						if(empty($c['job'])) continue;
						if($c['job']['page'] != 'youtube') continue;
						$c['date'] = date("Y-m-d H:i:s", $c['created_at']);
                ?>
				<tr class="odd gradeX">
					<td class="text-center"><?php echo ($c['status'])?'<i class="entypo-db-shape text-success"></i>':'<i class="entypo-db-shape text-danger"></i>'; ?></td>
					<td><?php echo $c['job']['page']; ?></td>
					<td><?php echo $c['job']['param_id']; ?></td>
					<td><?php echo $c['keywords']; ?></td>
					<td><?php echo $c['message']; ?></td>
					<td><?php echo $c['date']; ?></td>
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