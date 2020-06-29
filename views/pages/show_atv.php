<?php

    if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');

	if(isset($query_select_tvs)){
		$query_select_categs = "`id` in " . $query_select_tvs;
		$query_select_tvs = "`category_id` in " . $query_select_tvs;
	}
	else{
		$query_select_categs = "1";
		$query_select_tvs = "1";
	}

	$all_tvs = select_rows($pdo1, $table_local_tv, $query_select_tvs." ORDER BY `id` DESC");
	$temp_categs = select_rows($pdo1, $table_local_categ, $query_select_categs);
	$all_tv_categs = array();
	if(!empty($temp_categs)){
		foreach($temp_categs as $temp){
			$all_tv_categs[$temp['id']] = $temp;
		}
	}

	if($page_type == "refresh"){
		# Response Data Array
		$resp = array();

		$resp['status'] = "fail";
		
		$datas = array();

		foreach($all_tv_categs as $categ){
			// curl	
			$url = $categ['url'];
			$host_urls = parse_url($url);
			$host_url = (isset($host_urls['host']))?$host_urls['host']:$url;
			$host_url = (isset($host_urls['scheme']))?$host_urls['scheme']."://".$host_url:$host_url;
			$response = url_get_contents($url);

			if(isset($response['info']['http_code']) && $response['info']['http_code'] == 200){
				// response status ok
				// call analysis function
				$datas = analyze_response($response, $host_url, $categ, $datas);
			}
		}

		if(!empty($datas)){
			// update or insert
			$old_datas = array();
			foreach($all_tvs as $a){
				$old_datas[$a['url']]= $a;
			}
			foreach($datas as $key => $new_data){
				$new_data['title'] = str_replace("'", "\'", $new_data['title']);
				if(isset($old_datas[$new_data['url']])){
					// update
					$query_update = "UPDATE `".$table_local_tv."` SET `title` = '".$new_data['title']."', `category_id` = '".$new_data['category_id']."', 
						`url` = '".$new_data["url"]."', `thumbnail` = '".$new_data['thumbnail']."' WHERE `id` = '".$old_datas[$new_data["url"]]['id']."';";
					update_row($pdo1, $table_local_tv, $query_update);
				}
				else{
					// insert
					$query_insert = "INSERT INTO `".$table_local_tv."` SET `title` = '".$new_data['title']."', `category_id` = '".$new_data['category_id']."', 
						`url` = '".$new_data["url"]."', `thumbnail` = '".$new_data['thumbnail']."';";
					insert_row($pdo1, $table_local_tv, $query_insert);
				}

				$datas[$key]['status'] = 1;
				$datas[$key]['started'] = "";
				$datas[$key]['category'] = $all_tv_categs[$datas[$key]['category_id']]['title'];
			}

			$resp['data'] = $datas;
			$resp['status'] = "success";
		}

		$pdo1 = null;

		echo json_encode($resp);
		die();
	}

?>
        <script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			var $table3 = jQuery("#table-3");
			
			var table3 = $table3.DataTable( {
				"aLengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				dom: 'Bfrtip',
				buttons: [
					{
						text: 'Refresh',
						action: function ( e, dt, node, config ) {
							node.addClass('disabled');
							$.ajax({
								method : 'POST',
								url : baseurl + 'index.php?page=' + currentPageTitle + '&type=refresh',
								success : function(result){
									node.removeClass('disabled');

									// window.location.reload();
									return ;
									
									try{
										let json = JSON.parse(result);

										let table = $table3.dataTable();
										let oSettings = table.fnSettings();

										table.fnClearTable(this);

										for (var i=0; i<json.data.length; i++)
										{
											let a = json.data[i];
											table.oApi._fnAddData(oSettings, json.data[i]);
											// table.fnAddTr( $('<tr>'+
											// 	'<td class="text-center">'+((a['status'] == 0)?'<i class="entypo-db-shape text-success"></i>':'<i class="entypo-db-shape text-danger"></i>')+'</td>'+
											// 	'<td>'+a['title']+'</td>'+
											// 	'<td>'+a['category']+'</td>'+
											// 	'<td>'+a['url']+'</td>'+
											// 	'<td>'+a['started']+'</td>'+
											// 	'<td><a href="#" class="btn btn-gold btn-sm btn-icon icon-left"><i class="entypo-download"></i>Download</a></td>'+
											// 	'</tr>')[0]
											// );
										}

										oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
										table.fnDraw();
									}
									catch(e){
									}
								},
								error : function(err){
									node.removeClass('disabled');
								}
							});
						}
					}
				]
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
		} );
		</script>
		
		<table class="table table-bordered datatable" id="table-3">
			<thead>
				<tr>
					<th>Status</th>
					<th>Program Name</th>
					<th>Category Name</th>
					<th>URL</th>
					<th>Stream Started</th>
					<th style="min-width: 129px;">Actions</th>
				</tr>
			</thead>
			<tbody>
                <?php
                    foreach($all_tvs as $a){
						$a['status'] = 1;
						$a['started'] = "";
						$a['category'] = $all_tv_categs[$a['category_id']]['title'];
                ?>
				<tr class="odd gradeX">
					<td class="text-center"><?php echo ($a['status'] == 0)?'<i class="entypo-db-shape text-success"></i>':'<i class="entypo-db-shape text-danger"></i>'; ?></td>
					<td><?php echo $a['title']; ?></td>
					<td><?php echo $a['category']; ?></td>
					<td><?php echo $a['url']; ?></td>
					<td><?php echo $a['started']; ?></td>
					<td>
						<a href="#" class="btn btn-gold btn-sm btn-icon icon-left">
							<i class="entypo-download"></i>
							Download
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