<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * extrauserinfohelper helper class.
 *
 * @package	   Admin
 * @author	   Etherton Tech
 * @copyright  (c) 2012 Etherton Tech
 */
class extrauserinfohelper_Core {
	
	public static $roles = array('5' =>'Activarian', '6' => 'Guardian');
	
	
	public static $organization_parent_id = 2;
	public static $themes_parent_id = 1;
	public static $roles_parent_id = 3;
	public static $activarian_role_id = 6;
	public static $guardian_role_id = 7;
	
	/**
	 * Use this method to get a list of organization categories
	 */
	public static function get_organizations($show_hidden = false)
	{
		$orgs =  ORM::factory('category')
			->where('parent_id', self::$organization_parent_id);
		if(!$show_hidden)
		{
			$orgs = $orgs->where('category_visible', 1);
		}
		return $orgs->find_all();
	}
	
	/**
	 * Used to get things in an array formt that can be used for inputs
	 * @param unknown_type $show_hidden
	 */
	public static function get_organiztions_array($show_hidden = false)
	{
		$orgs_ORM = self::get_organizations($show_hidden);
		$orgs = array();
		
		foreach($orgs_ORM as $o)
		{
			$orgs[$o->id] = $o->category_title;
		}
		
		return $orgs;
	}
	
	/**
	 * Use this method to get a list of theme categories
	 */
	public static function get_themes($show_hidden = false)
	{
		$orgs =  ORM::factory('category')
		->where('parent_id', self::$themes_parent_id);
		if(!$show_hidden)
		{
			$orgs = $orgs->where('category_visible', 1);
		}
		return $orgs->find_all();
	}
	
}
