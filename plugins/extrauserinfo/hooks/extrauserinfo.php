<?php defined('SYSPATH') or die('No direct script access.');
/**
 * User Postal Code Hooks
 *
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   User Postal Code - http://ethertontech.com
 */

class extrauserinfo {

	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
		// Hook into routing
		Event::add('system.pre_controller', array($this, 'add'));

		//use this to store the post data
		$this->post = null;
	}

	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		if(Router::$controller == "users")
		{
			
			//hook into the UI for user admin/edit
			Event::add('ushahidi_action.users_form_admin', array($this, '_add_user_view'));	 //add the UI for setting up alerts
				
			//hook into the controller so we can see the contents of the post
			Event::add('ushahidi_action.users_add_admin', array($this, '_collect_post'));
			
			//hook into the controller so we can get the details of the user that was edited for the above post
			Event::add('ushahidi_action.user_edit', array($this, '_user_edited'));
			

		}
		
		if(Router::$controller == "login")
		{
			//hook into the UI for user admin/edit
			Event::add('ushahidi_action.login_new_user_form', array($this, '_add_user_view'));	 //add the UI for setting up alerts
		
			//hook into the controller so we can see the contents of the post
			Event::add('ushahidi_action.users_add_login_form', array($this, '_collect_post'));
				
			//hook into the controller so we can get the details of the user that was edited for the above post
			Event::add('ushahidi_action.user_edit', array($this, '_user_edited'));
		
		}
		 	
		 else if(Router::$controller == "profile")
		 {
							
			//hook into the UI for user admin/edit
			Event::add('ui_admin.profile_shown', array($this, '_add_user_view'));	 //add the UI for setting up alerts

				
			//hook into the controller so we can see the contents of the post
			Event::add('ushahidi_action.profile_add_admin', array($this, '_collect_post'));
			Event::add('ushahidi_action.profile_post_member', array($this, '_collect_post'));
			//hook into the controller so we can get the details of the user that was edited for the above post
			Event::add('ushahidi_action.profile_edit', array($this, '_user_edited'));
			Event::add('ushahidi_action.profile_edit_member', array($this, '_user_edited'));
			
		}


	}

	/**
	 * Adds the UI for to the user edit page
	 */
	public function _add_user_view()
	{

		$form = array('extra_postalcode'=> '',
					'extra_role'=>'',
					'extra_real_name'=>'',
					'extra_organization'=>'',
					'extra_interest'=>array());

		//is this for a new user, or a previous user?
		if(Router::$controller == "profile") //the profile doesn't do us the courtesy of telling us the user's id, so we have to figure it out ourselves
		{
			if(isset($_SESSION['auth_user']))
			{
				$id = $_SESSION['auth_user']->id;
			}
			else
			{
				return;
			}
		}
		else
		{
			$id = Event::$data;
		}
		
		if($id)
		{ //figure out who this user is and what they're settings are
			$extra = ORM::factory('extrauserinfo')
				->where('user_id', $id)
				->find();			
			//if the user has no admin alert settings
			if($extra->loaded)
			{
				$form['extra_postalcode'] = $extra->postalcode;
				$form['extra_real_name'] = $extra->real_name;
				$form['extra_organization'] = $extra->organization_id;
			}
			
			//grab the interest
			$interests = ORM::factory('extrauserinfo_category')
				->where('user_id', $id)
				->find_all();
			$interests_array = array();
			foreach($interests as $i)
			{
				$interests_array[$i->category_id] = $i->category_id;
			}			
			$form['extra_interest'] = $interests_array;
		}

		if(Router::$controller == "login")
		{
			$view = new View('extrauserinfo/extrauserinfo');
		}
		else
		{
			$view = new View('extrauserinfo/admin/extrauserinfo');
		}		
		$view->form = $form;
		$view->roles = extrauserinfohelper::$roles;
		$view->organizations =extrauserinfohelper::get_organiztions_array(false);
		$view->themes = extrauserinfohelper::get_themes(false); 
		echo $view;
	}

	/**
	 * This collects the contents of the HTTP post that has the details of the users
	 * alerts preferences that we want
	 */
	public function _collect_post()
	{
		$this->post = event::$data;
		
		//if this is an object add a validation rule
		if(is_object($this->post))
		{
			$this->post->add_rules('extra_postalcode','required','length[5,5]');
			$this->post->add_rules('extra_real_name','required','length[1,254]');
		}
	}

	/**
	 * This grabs the details of the user that was just edited, primarily the user ID, that's what we
	 * really want. This also does all the work of saving things to the DB
	 */
	public function _user_edited()
	{
		$user = event::$data;
		$post = $this->post;
		
		$role = null;
		$interest = array();
		//pull out the data we care about
		if(is_array($post))
		{
			$postalcode = $post['extra_postalcode'];
			if(isset($post['extra_role']))
			{
				$role = $post['extra_role'];
			}
			$real_name = $post['extra_real_name'];
			$organization = $post['extra_organization'];
			if(isset($post['extra_interest']))
			{
				$interest = $post['extra_interest'];
			}
		}
		else
		{
			$postalcode = $post->extra_postalcode;
			if(isset($post->extra_role))
			{
				$role = $post->extra_role;
			}
			$real_name = $post->extra_real_name;
			$organization = $post->extra_organization;
			
			if(isset($post->extra_interest))
			{
				$interest = $post->extra_interest;
			}
			
		}
		
		//grab the record in the DB if it exists
		$extra = ORM::factory('extrauserinfo')
		->where('user_id', $user->id)
		->find();

		//fill out the info
		$extra->postalcode = $postalcode;
		$extra->real_name = $real_name;
		$extra->organization_id = $organization;
		$extra->user_id = $user->id;
		$extra->save();
		//set the role
		if($role != null)
		{
			$user->add(ORM::factory('role', $role));
		}
		$user->save();
		//set the areas of interest
		ORM::factory('extrauserinfo_category')
			->where('user_id', $user->id)
			->delete_all();
		foreach($interest as $i)
		{
			$int = ORM::factory('extrauserinfo_category');
			$int->user_id = $user->id;
			$int->category_id = $i;
			$int->save();
		}
		//capture the organization as a category
		$int = ORM::factory('extrauserinfo_category');
		$int->user_id = $user->id;
		$int->category_id = $organization;
		$int->save();
		
		//hanlde roles as categories
		$role_name = $post['role'];
		//lookup that category
		$role_cat = ORM::factory('category')
			->where('category_title', $role_name)
			->find();
		if($role_cat->loaded)
		{
			//capture the role as a category
			$int = ORM::factory('extrauserinfo_category');
			$int->user_id = $user->id;
			$int->category_id = $role_cat->id;
			$int->save();
		}
		

	}


}

new extrauserinfo;
