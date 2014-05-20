<div id="submenu">
<?php 
			$this_page = 'event_submit';
		 	$menu = "<ul>";
			$menu .= "<li><a href=\"".url::site()."events\" ";
			$menu .= ($this_page == 'events') ? " class=\"active\"" : "";
		 	$menu .= ">".Kohana::lang('ui_main.browse_events')."</a></li>";
		 	
		 	$menu .= "<li><a href=\"".url::site()."events/mark\" ";
		 	$menu .= ($this_page == 'event_submit') ? " class=\"active\"":"";
		 	$menu .= ">".Kohana::lang('ui_main.submit')."</a></li>";
		 	$menu .= "</ul>";
		 	
		 	echo $menu;
?>
</div>

<div id="content">
	<div class="content-bg">



			<h1>Event  - Permissions</h1>
			<h2>You must <a href="<?php echo url::base();?>login">log in</a> as an Activarian before you can mark events.</h2>
	</div>
</div>
