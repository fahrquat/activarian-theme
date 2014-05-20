<?php
/**
 * View file for updating the reports display
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team - http://www.ushahidi.com
 * @package    Ushahidi - http://source.ushahididev.com
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */
?>
		<!-- Top reportbox section-->
		<div class="rb_nav-controls r-5">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<ul class="link-toggle report-list-toggle lt-icons-and-text">
							
						</ul>
					</td>
					<td><?php //echo $pagination; ?></td>
					<td><?php //echo $stats_breadcrumb; ?></td>
					<td class="last">
						<ul class="link-toggle lt-icons-only">							
						</ul>
					</td>
				</tr>
			</table>
		</div>
		<!-- /Top reportbox section-->
		
		<!-- Report listing -->
		<div class="r_cat_tooltip"><a href="#" class="r-3"></a></div>
		<div class="rb_list-and-map-box">
			<div id="rb_list-view">
			<?php
				foreach ($incidents as $incident)
				{
					/*
					$incident = ORM::factory('incident', $incident->incident_id);
					$incident_id = $incident->id;
					$incident_title = strip_tags($incident->incident_title);
					$incident_description = strip_tags($incident->incident_description);
					//$incident_category = $incident->incident_category;
					// Trim to 150 characters without cutting words
					// XXX: Perhaps delcare 150 as constant
					*/
					$incident_description = text::limit_chars(strip_tags($incident->username), 140, "...", true);
					//$incident_date = date('H:i M d, Y', strtotime($incident->incident_date));
					//$incident_time = date('H:i', strtotime($incident->incident_date));
					//$location_id = $incident->location_id;
					//$location_name = $incident->location->location_name;
					//$incident_verified = $incident->incident_verified;
					/*
					if ($incident_verified)
					{
						$incident_verified = '<span class="r_verified">'.Kohana::lang('ui_main.verified').'</span>';
						$incident_verified_class = "verified";
					}
					else
					{
						$incident_verified = '<span class="r_unverified">'.Kohana::lang('ui_main.unverified').'</span>';
						$incident_verified_class = "unverified";
					}

					$comment_count = $incident->comment->count();

					$incident_thumb = url::file_loc('img')."media/img/report-thumb-default.jpg";
					$media = $incident->media;
					if ($media->count())
					{
						foreach ($media as $photo)
						{
							if ($photo->media_thumb)
							{ // Get the first thumb
								$incident_thumb = url::convert_uploaded_to_abs($photo->media_thumb);
								break;
							}
						}
					}
					*/
				?>
				<div id="<?php echo $incident->id ?>" class="rb_report <?php //echo $incident_verified_class; ?>">
					<div class="r_media">
						<p class="r_photo" style="text-align:center;"> 
							<img src="https://secure.gravatar.com/avatar/a0de882176b7dd8acd1882c9ea9bf671?s=160&amp;d=mm&amp;r=g" width="89" height="59" />
						</p>

					</div>

					<div class="r_details">
						<h3><a class="r_title" href="<?php echo url::site(); ?>profile/user/<?php echo $incident->username; ?>">
								<?php echo html::specialchars($incident->username); ?>
							</a>
							<a href="<?php echo url::site(); ?>resources/view/<?php echo $incident->id; ?>#discussion" class="r_comments">
								<?php //echo $comment_count; ?></a> 
								<?php //echo $incident_verified; ?>
							</h3>
						<p style="background:none;"class="r_date r-3 bottom-cap"><?php //echo $incident_date; ?></p>
						<div class="r_description"> <?php echo $incident->username ?>  
						  
						</div>

						<?php
						// Action::report_extra_details - Add items to the report list details section
						//Event::run('ushahidi_action.report_extra_details', $incident->id);
						?>
					</div>
				</div>
			<?php } ?>
			</div>
			<div id="rb_map-view" style="display:none; width: 590px; height: 384px; border:1px solid #CCCCCC; margin: 3px auto;">
			</div>
		</div>
		<!-- /Report listing -->
		
		<!-- Bottom paginator -->
		<div class="rb_nav-controls r-5">
			<table border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>
						<ul class="link-toggle report-list-toggle lt-icons-and-text">
						</ul>
					</td>
					<td><?php //echo $pagination; ?></td>
					<td><?php //echo $stats_breadcrumb; ?></td>
					<td class="last">
						<ul class="link-toggle lt-icons-only">
							<?php //@todo Toggle the status of these links depending on the current page ?>
						</ul>
					</td>
				</tr>
			</table>
		</div>
		<!-- /Bottom paginator -->
	        