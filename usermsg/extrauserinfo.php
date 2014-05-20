<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Postal Code data entry view for the login page
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Postal Code - http://ethertontech.com
 */
?>


	
	<tr>
		<td><strong><?php echo Kohana::lang('extrauserinfo.activarian_role'); ?>:</strong><br />
		<?php
			
			print form::dropdown('extra_role', $roles, $form['extra_role']); 
		?>
		</td>
	</tr>
	<tr>
		<td><strong><?php echo Kohana::lang('extrauserinfo.real_name'); ?>:</strong><br />
		<?php print form::input('extra_real_name', $form['extra_real_name'], 'class="login_text"'); ?></td>
	</tr>	
	<tr>
		<td><strong><?php echo Kohana::lang('extrauserinfo.postal_code'); ?>:</strong><br />
		<?php print form::input('extra_postalcode', $form['extra_postalcode'], 'class="login_text"'); ?></td>
	</tr>
	<tr>
		<td><strong><?php echo Kohana::lang('extrauserinfo.organization'); ?>:</strong><br />
		<?php			
			print form::dropdown('extra_organization', $organizations, $form['extra_organization']); 
		?>
		</td>
	</tr>
	<tr>
		<td><strong><?php echo Kohana::lang('extrauserinfo.area_of_interest'); ?>:</strong><br />
		<?php
			
			echo category::tree($themes, false, $form['extra_interest'], 'extra_interest',1); 
		?>
		</td>
	</tr>

