<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This controller is show the NIC page
 *GPL)
 */

class nic_Controller extends Main_Controller {
	


	/**
	 * Displays all reports.
	 */
	public function index()
	{
		// Cacheable Controller
		$this->is_cachable = TRUE;

		$this->template->header->this_page = 'nic';
		$this->template->content = new View('nic');
	}

}
