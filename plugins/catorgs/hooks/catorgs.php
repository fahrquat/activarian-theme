<?php defined('SYSPATH') or die('No direct script access.');
/**
 * CatOrgs Hook
 *
 
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Cat Orgs
 * @license	   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class catorgs {
	
	
	public static $org_parent_id = 2;
	public static $role_parent_id = 3;
	
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{	
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));
	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		if(Router::$controller == "reports" AND Router::$method == "edit")
		{			
			Event::add('ushahidi_action.header_scripts', array($this, '_add_role_js'));
			Event::add('ushahidi_action.header_scripts_admin', array($this, '_add_js'));
			Event::add('ushahidi_action.header_scripts_member', array($this, '_add_role_js'));
			Event::add('ushahidi_action.report_form', array($this, '_add_cats_frontent'));
			//only show when admin
			$url = explode('/',url::current());
			if($url[0] == 'admin'){
				Event::add('ushahidi_action.report_form_admin', array($this, '_add_cats_frontent'));
			}
				 
			Event::add('ushahidi_action.report_submit_admin', array($this, '_remove_duplicates'));	 //used to remove duplicate data
			Event::add('ushahidi_action.report_submit', array($this, '_remove_duplicates'));	 //used to remove duplicate data
			
		}
	}//end add()
	
	//renders the front end
	public function _add_cats_frontent()
	{
		//get the orgs
		$orgs = ORM::factory('category')
			->where('parent_id', self::$org_parent_id)
			->find_all();
		if(count($orgs) == 0)
		{
			return;
		}
		$orgs_array = array();
		$first_org = null;
		$select = null;
		foreach($orgs as $org)
		{
			if($first_org == null)
			{
				$first_org = $org->id;
			}
			$orgs_array[$org->id] = $org->category_title;
		}
		
		//if there's an id
		$id = Event::$data;
		if($id AND intval($id) != 0)
		{
			//check if there's a link between one of the org cats and this incident
			foreach($orgs as $org)
			{
				$link = ORM::factory('incident_category')
					->where('incident_id', $id)
					->where('category_id', $org->id)
					->find();
				if($link->loaded)
				{
					$first_org = $org->id;
					$select = $org->id;
				}
			}
		}
		
		echo '<div class="report_row">';
		echo '<h4>Organization</h4>';
		echo  form::dropdown('orgs',$orgs_array, $select, 'id="orgs" onchange="orgclick(); return false;"');
		echo  form::checkbox('incident_category[]', $first_org, true, 'id="orgscheck" style="display:none;"');
		echo '</div>';
	}
	
	/**
	 * Keeps you from putting duplicate
	 * category data into the database
	 */
	public function _remove_duplicates()
	{
		$post = Event::$data;		
		$cats = array();
		foreach($post['incident_category'] as $cat)
		{
			if(!in_array($cat, $cats))
			{
				$cats[] = $cat;
			}
		}
		
		Event::$data['incident_category'] = $cats;
	}
	
	/**
	 * used to add JS that removes the organization check boxes
	 */
	public function _add_js()
	{
		$view = new View('catorgs/catorgs_js');
		$view->org_id = self::$org_parent_id;
		$view->role_id = 0;
		echo $view; 
	}
	
	/**
	 * used to add JS that removes the organization check boxes
	 */
	public function _add_role_js()
	{
		$view = new View('catorgs/catorgs_js');
		$view->org_id = self::$org_parent_id;
		$view->role_id = self::$role_parent_id;
		echo $view;
	}
}

new catorgs;