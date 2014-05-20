<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Postal Code data entry view
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Postal Code - http://ethertontech.com
 */
?>


	
	<div class="row">
		<h4><a href="#" class="tooltip" title="<?php echo Kohana::lang("extrauserinfo.postal_code_why"); ?>"><?php echo Kohana::lang('extrauserinfo.postal_code');?></a> <span class="required"><?php echo Kohana::lang('ui_main.required'); ?></span></h4>		
		<?php print form::input('extra_postalcode', $form['extra_postalcode'], ' class="text "'); ?>
	</div>
	
	<div class="row">
		<h4><?php echo Kohana::lang('extrauserinfo.real_name');?> <span class="required"><?php echo Kohana::lang('ui_main.required'); ?></span></h4>		
		<?php print form::input('extra_real_name', $form['extra_real_name'], ' class="text "'); ?>
	</div>
	
	<div class="row">
		<h4><?php echo Kohana::lang('extrauserinfo.organization');?> <span class="required"><?php echo Kohana::lang('ui_main.required'); ?></span></h4>		
		<?php print form::dropdown('extra_organization', $organizations, $form['extra_organization']);?>
	</div>
	
	<div class="row">
		<h4><?php echo Kohana::lang('extrauserinfo.area_of_interest');?> <span class="required"><?php echo Kohana::lang('ui_main.required'); ?></span></h4>	
		<?php
			
			$tree = category::tree($themes, false, $form['extra_interest'], 'extra_interest',1);
			$tree = str_replace("<ul", "<ul style=\"list-style: none;\" class=\"treeview\"", $tree);
			echo $tree;
		?>
	</div>
	

