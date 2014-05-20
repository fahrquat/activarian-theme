<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<title><?php echo $page_title.$site_name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700" rel="stylesheet" type="text/css">
	<?php echo $header_block; ?>
		<?php
	// Action::header_scripts - Additional Inline Scripts from Plugins
	Event::run('ushahidi_action.header_scripts');
	?>
</head>


<?php
  // Add a class to the body tag according to the page URI
  // we're on the home page
  if (count($uri_segments) == 0)
  {
    $body_class = "page-main";
  }
  // 1st tier pages
  elseif (count($uri_segments) == 1)
  {
    $body_class = "page-".$uri_segments[0];
  }
  // 2nd tier pages... ie "/reports/submit"
  elseif (count($uri_segments) >= 2)
  {
    $body_class = "page-".$uri_segments[0]."-".$uri_segments[1];
  }

?>

<body id="page" class="<?php echo $body_class; ?>">

<?php echo $header_nav; ?>

	<!-- header -->
		<div id="header">
				<!-- searchbox -->
			<div id="searchbox">
	
				<!-- languages -->
				<?php echo $languages;?>
				<!-- / languages -->
	
				<!-- searchform -->
				<?php echo $search; ?>
				<!-- / searchform -->
				
		    </div>
			<!-- logo -->
			<?php if ($banner == NULL): ?>
			<div id="logo">
				<h1><a href="<?php echo url::site();?>"><?php echo $site_name; ?></a></h1>
				<span><?php echo $site_tagline; ?></span>
			</div>
			<?php else: ?>
			<a href="<?php echo url::site();?>"><img src="<?php echo $banner; ?>" alt="<?php echo $site_name; ?>" /></a>
			<?php endif; ?>
			<!-- / logo -->

			<?php
				// Action::main_sidebar - Add Items to the Entry Page Sidebar
				Event::run('ushahidi_action.main_sidebar');
			?>
			
		</div>
		<!-- / header -->
         <!-- / header item for plugins -->
        <?php
            // Action::header_item - Additional items to be added by plugins
	        Event::run('ushahidi_action.header_item');
        ?>

        <?php if(isset($site_message) AND $site_message != '') { ?>
			<div class="green-box">
				<h3><?php echo $site_message; ?></h3>
			</div>
		<?php } ?>
		<!-- mainmenu -->
			<div id="navbar">
			
				<div id="mainmenu" class="clearingfix">
					<ul>
						<?php nav::main_tabs($this_page); ?>
					</ul>
					<!-- filter dropdown button -->
					<a class="btn toggle" id="filter-menu-toggle" href="#the-filters">Filter reports on the map<!--<?php echo Kohana::lang('ui_main.filter_reports_by'); ?>--><span class="btn-icon ic-down">&raquo;</span></a>
					<!-- / filter dropdown button -->
					<!-- submit incident -->
					<?php echo $submit_btn; ?>
					<!-- / submit incident -->
				</div>
				
			</div>
				<!-- / mainmenu -->
		<!-- wrapper -->
	<div class="rapidxwpr floatholder">
	
		<!-- main body -->
		<div id="middle">
			<div class="background layoutleft">

				
