<div id="submenu">
<?php 
			$this_page = 'resources_add';
		 	$menu = "<ul>";
			$menu .= "<li><a href=\"".url::site()."resources\" ";
			$menu .= ($this_page == 'resources') ? " class=\"active\"" : "";
		 	$menu .= ">".Kohana::lang('ui_main.browse_resources')."</a></li>";
		 	
		 	$menu .= "<li><a href=\"".url::site()."resources/add\" ";
		 	$menu .= ($this_page == 'resources_add') ? " class=\"active\"":"";
		 	$menu .= ">".Kohana::lang('ui_main.add_resources')."</a></li>";
		 	$menu .= "</ul>";
		 	
		 	echo $menu;
?>
</div>

<div id="content">
	<div class="content-bg">

		<?php if ($site_submit_report_message != ''): ?>
			<div class="green-box" style="margin: 25px 25px 0px 25px">
				<h3><?php echo $site_submit_report_message; ?></h3>
			</div>
		<?php endif; ?>

		<!-- start report form block -->
		<?php print form::open(NULL, array('enctype' => 'multipart/form-data', 'id' => 'reportForm', 'name' => 'reportForm', 'class' => 'gen_forms')); ?>
		<input type="hidden" name="latitude" id="latitude" value="0">
		<input type="hidden" name="longitude" id="longitude" value="0">
		<input type="hidden" name="country_name" id="country_name" value="<?php echo $form['country_name']; ?>" />
		<input type="hidden" name="incident_zoom" id="incident_zoom" value="<?php echo $form['incident_zoom']; ?>" />
		<div class="big-block">
			<h1>Add a Resource</h1>
			<?php if ($form_error): ?>
			<!-- red-box -->
			<div class="red-box">
				<h3>Error!</h3>
				<ul>
					<?php
						foreach ($errors as $error_item => $error_description)
						{
							// print "<li>" . $error_description . "</li>";
							print (!$error_description) ? '' : "<li>" . $error_description . "</li>";
						}
					?>
				</ul>
			</div>
			<?php endif; ?>
			<div class="row">
				<input type="hidden" name="form_id" id="form_id" value="<?php echo $id?>">
			</div>
				<div class="report_row">
					<?php if(count($forms) > 1): ?>
					<div class="row">
						<h4><span><?php echo Kohana::lang('ui_main.select_form_type');?></span>
						<span class="sel-holder">
							<?php print form::dropdown('form_id', $forms, $form['form_id'],
						' onchange="formSwitch(this.options[this.selectedIndex].value, \''.$id.'\')"') ?>
						</span>
						<div id="form_loader" style="float:left;"></div>
						</h4>
					</div>
					<?php endif; ?>
					<h4>Resource Title <span class="required">*</span> </h4>
					<?php print form::input('incident_title', $form['incident_title'], ' class="text long"'); ?>
				</div>
				<div class="report_row">
					<h4>Resource <span class="required">*</span> </h4>
					<?php print form::textarea('incident_description', $form['incident_description'], ' cols="100" rows="30"  ') ?>
				</div>
				<div class="report_row" id="datetime_default">
					<h4>
						<a href="#" id="date_toggle" class="show-more"><?php echo Kohana::lang('ui_main.modify_date'); ?></a>
						<?php echo Kohana::lang('ui_main.date_time'); ?>: 
						<?php echo Kohana::lang('ui_main.today_at')." "."<span id='current_time'>".$form['incident_hour']
							.":".$form['incident_minute']." ".$form['incident_ampm']."</span>"; ?>
						<?php if($site_timezone != NULL): ?>
							<small>(<?php echo $site_timezone; ?>)</small>
						<?php endif; ?>
					</h4>
				</div>
				<div class="report_row hide" id="datetime_edit">
					<div class="date-box">
						<h4><?php echo Kohana::lang('ui_main.reports_date'); ?></h4>
						<?php print form::input('incident_date', $form['incident_date'], ' class="text short"'); ?>
						<script type="text/javascript">
							$().ready(function() {
								$("#incident_date").datepicker({ 
									showOn: "both", 
									buttonImage: "<?php echo url::file_loc('img'); ?>media/img/icon-calendar.gif", 
									buttonImageOnly: true 
								});
							});
						</script>
					</div>
					<div class="time">
						<h4><?php echo Kohana::lang('ui_main.reports_time'); ?></h4>
						<?php
							for ($i=1; $i <= 12 ; $i++)
							{
								// Add Leading Zero
								$hour_array[sprintf("%02d", $i)] = sprintf("%02d", $i);
							}
							for ($j=0; $j <= 59 ; $j++)
							{
								// Add Leading Zero
								$minute_array[sprintf("%02d", $j)] = sprintf("%02d", $j);
							}
							$ampm_array = array('pm'=>'pm','am'=>'am');
							print form::dropdown('incident_hour',$hour_array,$form['incident_hour']);
							print '<span class="dots">:</span>';
							print form::dropdown('incident_minute',$minute_array,$form['incident_minute']);
							print '<span class="dots">:</span>';
							print form::dropdown('incident_ampm',$ampm_array,$form['incident_ampm']);
						?>
						<?php if ($site_timezone != NULL): ?>
							<small>(<?php echo $site_timezone; ?>)</small>
						<?php endif; ?>
					</div>
					<div style="clear:both; display:block;" id="incident_date_time"></div>
				</div>
				<div class="report_row">
					<h4><?php echo Kohana::lang('ui_main.reports_categories'); ?> <span class="required">*</span></h4>
					<div class="report_category" id="categories">
					<?php
						$selected_categories = (!empty($form['incident_category']) AND is_array($form['incident_category']))
							? $selected_categories = $form['incident_category']
							: array();
							
						$columns = 2;
						echo category::tree($categories, TRUE, $selected_categories, 'incident_category', $columns);
						?>
					</div>
				</div>
				
				<input type="hidden" name="incident_category[]" value="<?php echo $user_org_id;?>"/>
				<input type="hidden" name="incident_category[]" value="<?php echo extrauserinfohelper::$guardian_role_id;?>"/>


				<?php
				// Action::report_form - Runs right after the report categories
				Event::run('ushahidi_action.report_form');
				?>

				<?php echo $custom_forms ?>

				
			
				
			
				<?php print form::hidden('location_name', 'n/a', ' class="text long"'); ?>

				<!-- News Fields -->
				<div id="divNews" class="report_row">
					<h4><?php echo Kohana::lang('ui_main.reports_news'); ?></h4>
					
					<?php 
						// Initialize the counter
						$i = (empty($form['incident_news'])) ? 1 : 0;
					?>

					<?php if (empty($form['incident_news'])): ?>
						<div class="report_row">
							<?php print form::input('incident_news[]', '', ' class="text long2"'); ?>
							<a href="#" class="add" onClick="addFormField('divNews','incident_news','news_id','text'); return false;">add</a>
						</div>
					<?php else: ?>
						<?php foreach ($form['incident_news'] as $value): ?>
						<div class="report_row" id="<?php echo $i; ?>">
							<?php echo form::input('incident_news[]', $value, ' class="text long2"'); ?>
							<a href="#" class="add" onClick="addFormField('divNews','incident_news','news_id','text'); return false;">add</a>

							<?php if ($i != 0): ?>
								<?php $css_id = "#incident_news_".$i; ?>
								<a href="#" class="rem"	onClick="removeFormField('<?php echo $css_id; ?>'); return false;">remove</a>
							<?php endif; ?>

						</div>
						<?php $i++; ?>

						<?php endforeach; ?>
					<?php endif; ?>

					<?php print form::input(array('name'=>'news_id', 'type'=>'hidden', 'id'=>'news_id'), $i); ?>
				</div>


				<!-- Video Fields -->
				<div id="divVideo" class="report_row">
					<h4><?php print Kohana::lang('ui_main.external_video_link'); ?></h4>
					<?php 
						// Initialize the counter
						$i = (empty($form['incident_video'])) ? 1 : 0;
					?>

					<?php if (empty($form['incident_video'])): ?>
						<div class="report_row">
							<?php print form::input('incident_video[]', '', ' class="text long2"'); ?>
							<a href="#" class="add" onClick="addFormField('divVideo','incident_video','video_id','text'); return false;">add</a>
						</div>
					<?php else: ?>
						<?php foreach ($form['incident_video'] as $value): ?>
							<div class="report_row" id="<?php  echo $i; ?>">

							<?php print form::input('incident_video[]', $value, ' class="text long2"'); ?>
							<a href="#" class="add" onClick="addFormField('divVideo','incident_video','video_id','text'); return false;">add</a>

							<?php if ($i != 0): ?>
								<?php $css_id = "#incident_video_".$i; ?>
								<a href="#" class="rem"	onClick="removeFormField('<?php echo $css_id; ?>'); return false;">remove</a>
							<?php endif; ?>

							</div>
							<?php $i++; ?>
						
						<?php endforeach; ?>
					<?php endif; ?>

					<?php print form::input(array('name'=>'video_id','type'=>'hidden','id'=>'video_id'), $i); ?>
				</div>
				
				<?php Event::run('ushahidi_action.report_form_after_video_link'); ?>

				<!-- Photo Fields -->
				<div id="divPhoto" class="report_row">
					<h4><?php echo Kohana::lang('ui_main.reports_photos'); ?></h4>
					<?php 
						// Initialize the counter
						$i = (empty($form['incident_photo']['name'][0])) ? 1 : 0;
					?>

					<?php if (empty($form['incident_photo']['name'][0])): ?>
					<div class="report_row">
						<?php print form::upload('incident_photo[]', '', ' class="file long2"'); ?>
						<a href="#" class="add" onClick="addFormField('divPhoto', 'incident_photo','photo_id','file'); return false;">add</a>
					</div>
					<?php else: ?>
						<?php foreach ($form[$this_field]['name'] as $value): ?>

							<div class="report_row" id="<?php echo $i; ?>">
								<?php print form::upload('incident_photo[]', $value, ' class="file long2"'); ?>
								<a href="#" class="add" onClick="addFormField('divPhoto','incident_photo','photo_id','file'); return false;">add</a>

								<?php if ($i != 0): ?>
									<?php $css_id = "#incident_photo_".$i; ?>
									<a href="#" class="rem"	onClick="removeFormField('<?php echo $css_id; ?>'); return false;">remove</a>
								<?php endif; ?>

							</div>

							<?php $i++; ?>

						<?php endforeach; ?>
					<?php endif; ?>

					<?php print form::input(array('name'=>'photo_id','type'=>'hidden','id'=>'photo_id'), $i); ?>
				</div>
									
				<div class="report_row">
					<input name="submit" type="submit" value="<?php echo Kohana::lang('ui_main.reports_btn_submit'); ?>" class="btn_submit" /> 
				</div>
			</div>
		<?php print form::close(); ?>
		<!-- end report form block -->
	</div>
</div>
