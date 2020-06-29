<?php

	if ( ! defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
	
	include_once(COMPONENT_PATH."header.php"); 

?>

<div class="page-container"><!-- add class "sidebar-collapsed" to close sidebar by default, "chat-visible" to make chat appear always -->
	
	<?php include_once(COMPONENT_PATH."leftsidebar.php"); ?>

	<div class="main-content">
				
		<?php include_once(COMPONENT_PATH."topbar.php"); ?>

		<?php include_once(PAGE_PATH.$page.".php"); ?>
		
		<!-- Footer -->
		<footer class="main">
			
			&copy; 2019  <a href="<?php echo WEB_PATH; ?>index.php" target="_blank"><strong>Ceek</strong></a>
		
		</footer>
	</div>

		
	<div id="chat" class="fixed" data-current-user="Art Ramadani" data-order-by-status="1" data-max-chat-history="25">
	
		<div class="chat-inner">
	
	
			<h2 class="chat-header">
				<a href="#" class="chat-close"><i class="entypo-cancel"></i></a>
	
				<i class="entypo-users"></i>
				Chat
				<span class="badge badge-success is-hidden">0</span>
			</h2>
	
	
			<div class="chat-group" id="group-1">
				<strong>Favorites</strong>
	
				<a href="#" id="sample-user-123" data-conversation-history="#sample_history"><span class="user-status is-online"></span> <em>Catherine J. Watkins</em></a>
				<a href="#"><span class="user-status is-online"></span> <em>Nicholas R. Walker</em></a>
				<a href="#"><span class="user-status is-busy"></span> <em>Susan J. Best</em></a>
				<a href="#"><span class="user-status is-offline"></span> <em>Brandon S. Young</em></a>
				<a href="#"><span class="user-status is-idle"></span> <em>Fernando G. Olson</em></a>
			</div>
	
	
			<div class="chat-group" id="group-2">
				<strong>Work</strong>
	
				<a href="#"><span class="user-status is-offline"></span> <em>Robert J. Garcia</em></a>
				<a href="#" data-conversation-history="#sample_history_2"><span class="user-status is-offline"></span> <em>Daniel A. Pena</em></a>
				<a href="#"><span class="user-status is-busy"></span> <em>Rodrigo E. Lozano</em></a>
			</div>
	
	
			<div class="chat-group" id="group-3">
				<strong>Social</strong>
	
				<a href="#"><span class="user-status is-busy"></span> <em>Velma G. Pearson</em></a>
				<a href="#"><span class="user-status is-offline"></span> <em>Margaret R. Dedmon</em></a>
				<a href="#"><span class="user-status is-online"></span> <em>Kathleen M. Canales</em></a>
				<a href="#"><span class="user-status is-offline"></span> <em>Tracy J. Rodriguez</em></a>
			</div>
	
		</div>
	
		<!-- conversation template -->
		<div class="chat-conversation">
	
			<div class="conversation-header">
				<a href="#" class="conversation-close"><i class="entypo-cancel"></i></a>
	
				<span class="user-status"></span>
				<span class="display-name"></span>
				<small></small>
			</div>
	
			<ul class="conversation-body">
			</ul>
	
			<div class="chat-textarea">
				<textarea class="form-control autogrow" placeholder="Type your message"></textarea>
			</div>
	
		</div>
	
	</div>
	
	
	<!-- Chat Histories -->
	<ul class="chat-history" id="sample_history">
		<li>
			<span class="user">Art Ramadani</span>
			<p>Are you here?</p>
			<span class="time">09:00</span>
		</li>
	
		<li class="opponent">
			<span class="user">Catherine J. Watkins</span>
			<p>This message is pre-queued.</p>
			<span class="time">09:25</span>
		</li>
	
		<li class="opponent">
			<span class="user">Catherine J. Watkins</span>
			<p>Whohoo!</p>
			<span class="time">09:26</span>
		</li>
	
		<li class="opponent unread">
			<span class="user">Catherine J. Watkins</span>
			<p>Do you like it?</p>
			<span class="time">09:27</span>
		</li>
	</ul>
	
	
	<!-- Chat Histories -->
	<ul class="chat-history" id="sample_history_2">
		<li class="opponent unread">
			<span class="user">Daniel A. Pena</span>
			<p>I am going out.</p>
			<span class="time">08:21</span>
		</li>
	
		<li class="opponent unread">
			<span class="user">Daniel A. Pena</span>
			<p>Call me when you see this message.</p>
			<span class="time">08:27</span>
		</li>
	</ul>
	
</div>

	<!-- Sample Modal (Default skin) -->
	<div class="modal fade" id="sample-modal-dialog-1">
		<div class="modal-dialog">
			<div class="modal-content">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Widget Options - Default Modal</h4>
				</div>
				
				<div class="modal-body">
					<p>Now residence dashwoods she excellent you. Shade being under his bed her. Much read on as draw. Blessing for ignorant exercise any yourself unpacked. Pleasant horrible but confined day end marriage. Eagerness furniture set preserved far recommend. Did even but nor are most gave hope. Secure active living depend son repair day ladies now.</p>
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal (modal-add-stream Modal)-->
	<div class="modal fade" id="modal-add-stream">
		<div class="modal-dialog">
			<div class="modal-content">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Add a new Stream</h4>
				</div>
				
				<div class="modal-body">
				
					<div class="row">
						<div class="col-md-12">
							
							<div class="form-group">
								<label for="modal_url" class="control-label">URL</label>
								
								<input type="text" class="form-control" id="modal_url" name="modal_url" placeholder="URL">
							</div>	
							
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							
							<div class="form-group">
								<label for="modal_series" class="control-label">SERIES NUMBER</label>
								
								<input type="text" class="form-control" id="modal_series" name="modal_series" placeholder="SERIES NUMBER">
							</div>	
							
						</div>
					</div>
					
				</div>
				
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-info" id="modal_submit" data-dismiss="modal">Add Stream</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal (modal-show-series Modal)-->
	<div class="modal fade" id="modal-show-tv_series">
		<div class="modal-dialog">
			<div class="modal-content">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Show TV Series</h4>
				</div>
				
				<div class="modal-body">
				</div>
				
				<div class="modal-footer" style="border-top-width: 0px;">
					<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal (modal-show-mag-device Modal)-->
	<div class="modal fade" id="modal-show-manage_mag">
		<div class="modal-dialog">
			<div class="modal-content">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Show MAG Device</h4>
				</div>
				
				<div class="modal-body">
				</div>
				
				<div class="modal-footer" style="border-top-width: 0px;">
					<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal (modal-show-offline-stream Modal)-->
	<div class="modal fade" id="modal-show-offline_streams">
		<div class="modal-dialog">
			<div class="modal-content">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Show Offline Stream</h4>
				</div>
				
				<div class="modal-body">
				</div>
				
				<div class="modal-footer" style="border-top-width: 0px;">
					<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal (modal-show-manage-stream Modal)-->
	<div class="modal fade" id="modal-show-manage_streams">
		<div class="modal-dialog">
			<div class="modal-content">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Show A Stream</h4>
				</div>
				
				<div class="modal-body">
				</div>
				
				<div class="modal-footer" style="border-top-width: 0px;">
					<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal (modal-show-mag-vod Modal)-->
	<div class="modal fade" id="modal-show-manage_vod">
		<div class="modal-dialog">
			<div class="modal-content">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Show MAG VOD</h4>
				</div>
				
				<div class="modal-body">
				</div>
				
				<div class="modal-footer" style="border-top-width: 0px;">
					<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal (modal-edit-mag-device Modal)-->
	<div class="modal fade" id="modal-edit-manage_mag" data-exist="YES">
		<div class="modal-dialog">
			<div class="modal-content">
				
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title">Edit MAG</h4>
				</div>
				
				<div class="modal-body">
					
					<div class="row">
						<div class="col-md-12">
							<h3 data-id="message-loading-alert" style="display: none;">Loading...</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							
							<div class="form-group">
								<label for="mac" class="control-label">MAC NUMBER</label>
								
								<input type="text" class="form-control" data-id="mac" name="mac" placeholder="Type MAC NUMBER">
							</div>	
							
						</div>

					</div>
					<div class="row">

						<div class="col-md-12">
							
							<div class="form-group">
								<label for="exp_date" class="control-label">EXTP Date</label>
								
								<input type="text" class="form-control" data-id="exp_date" name="exp_date" placeholder="If EXTP Date is Unlimited, database field will be NULL.">
							</div>	
							
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							
							<div class="form-group">
								<label for="admin_notes" class="control-label">Admin Notes</label>
								
								<textarea class="form-control" name="admin_notes" data-id="admin_notes" placeholder="Type your note."></textarea>
							</div>	
							
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							
							<div class="form-group">
								<label for="select-bouquest" class="control-label">Bouquets</label>
								
								<select multiple="multiple" name="select-bouquest[]" data-id="select-bouquest" class="form-control multi-select">
									<?php 
										if(isset($bouquests) && count($bouquests) > 0){
											foreach($bouquests as $b){
												echo '<option value="'.$b['id'].'">'.$b['bouquet_name'].'</option>';
											}
										}
									?>
								</select>
							</div>	
							
						</div>
					</div>

				</div>
				
				<div class="modal-footer" style="border-top-width: 0px;">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-info" data-id="modal_edit_submit" data-dismiss="modal">Save</button>
				</div>
			</div>
		</div>
	</div>

<?php include_once(COMPONENT_PATH."footer.php"); ?>