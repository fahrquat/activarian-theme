<div id="submenu">
<?php 
			$this_page = 'users';
		 	$menu = "<ul>";
			$menu .= "<li><a href=\"".url::site()."nic\" ";
			$menu .= ($this_page == 'nic') ? " class=\"active\"" : "";
		 	$menu .= ">".Kohana::lang('ui_main.NIC')."</a></li>";
		 	
		 	$menu .= "<li><a href=\"".url::site()."resources\" ";
		 	$menu .= ($this_page == 'resources') ? " class=\"active\"":"";
		 	$menu .= ">Resources</a></li>";
		 	
		 	$menu .= "<li><a href=\"".url::site()."users\" ";
		 	$menu .= ($this_page == 'users') ? " class=\"active\"":"";
		 	$menu .= ">Users</a></li>";
		 	
		 	
		 	$menu .= "<li><a href=\"".url::site()."files\" ";
		 	$menu .= ($this_page == 'files') ? " class=\"active\"":"";
		 	$menu .= ">Files</a></li>";
		 	
		 	
		 	$menu .= "</ul>";
		 	
		 	echo $menu;
?>
</div>

<div id="content">
	<div class="content-bg">
		<!-- start reports block -->
		<div class="big-block">
			<h1 class="heading">
			Users
			</h1>
			

			<div style="overflow:auto;">
				<!-- reports-box -->
				<div id="reports-box">
					<?php echo $report_listing_view; ?>
				</div>
				<!-- end #reports-box -->
				
				<div id="filters-box">
					<h2><?php echo Kohana::lang('ui_main.filter_reports_by'); ?></h2>
					<div id="accordion">
						
						<h3>
							<a href="#" class="small-link-button f-clear reset" onclick="removeParameterKey('c', 'fl-categories');"><?php echo Kohana::lang('ui_main.clear')?></a>
							<a class="f-title" href="#"><?php echo Kohana::lang('ui_main.category')?></a>
						</h3>
						<div class="f-category-box">
							<ul class="filter-list fl-categories" id="category-filter-list">
								<li>
									<a href="#"><?php
									$all_cat_image = '&nbsp';
									$all_cat_image = '';
									if($default_map_all_icon != NULL) {
										$all_cat_image = html::image(array('src'=>$default_map_all_icon));
									}
									?>
									<span class="item-swatch" style="background-color: #<?php echo Kohana::config('settings.default_map_all'); ?>"><?php echo $all_cat_image ?></span>
									<span class="item-title">All Users</span>
									<!-- <span class="item-count" id="all_report_count"><?php echo $report_stats->total_reports; ?></span>  -->
									</a>
								</li>
								<?php echo $category_tree_view; ?>
							</ul>
						</div>
						
				
						
						
					</div>
					<!-- end #accordion -->
					
					<div id="filter-controls">
						<p>
							<a href="#" class="small-link-button reset" id="reset_all_filters"><?php echo Kohana::lang('ui_main.reset_all_filters'); ?></a> 
							<a href="#" id="applyFilters" class="filter-button"><?php echo Kohana::lang('ui_main.filter_reports'); ?></a>
						</p>
					</div>          
				</div>
				<!-- end #filters-box -->
			</div>
      
			<div style="display:none">
				<?php
					// Filter::report_stats - The block that contains reports list statistics
					Event::run('ushahidi_filter.report_stats', $report_stats);
					echo $report_stats;
				?>
			</div>

		</div>
		<!-- end reports block -->
		
	</div>
	<!-- end content-bg -->
</div>