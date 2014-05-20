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
		<!-- start block -->
		<div class="big-block">
			<h1>Add a Resource - Thanks</h1>
			<!-- green-box -->
			<div class="green-box">
				<h3>Your Resource has been submitted to our staff for review. We will get back to you shortly if necessary.</h3>
				
			</div>
		</div>
		<!-- end block -->
	</div>
</div>