<div id="submenu">
<?php 
			$this_page = 'nic';
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
				Network Information Access
			</h1>
			<div style="font-size:150%;">
			<p>This section is for users who want to access more advanced functionality.</p>
			<br/>
			<p>The Resources section is for users to access information left by Guardians.</p>
			<br/>
			<p>The Users section is for seeing the users of this site and their areas of expertise.</p>
			</div>
			
		</div>
	</div>
	<!-- end content-bg -->
</div>