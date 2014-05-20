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



			<h1>Resources  - Permissions</h1>
			<h2>You must <a href="<?php echo url::base();?>login">log in</a> as a Guardian before you can add resources.</h2>
	</div>
</div>
