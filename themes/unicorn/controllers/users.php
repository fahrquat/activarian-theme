<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This controller is used to list/ view and edit events
 *GPL)
 */

class Users_Controller extends Events_Controller {
	
	/**
	 * Whether an admin console user is logged in
	 * @var bool
	 */
	var $logged_in;
	
	

	public function __construct()
	{
		parent::__construct();

	}

	/**
	 * Displays all reports.
	 */
	public function index()
	{
		
		
		// Cacheable Controller
		$this->is_cachable = TRUE;

		$this->template->header->this_page = 'nic';
		$this->template->content = new View('users');
		$this->themes->js = new View('users_js');

		$this->template->header->page_title .= 'Users '.Kohana::config('settings.title_delimiter');

		// Store any exisitng URL parameters
		$this->themes->js->url_params = json_encode($_GET);

		// Enable the map
		$this->themes->map_enabled = TRUE;

		// Set the latitude and longitude
		$this->themes->js->latitude = Kohana::config('settings.default_lat');
		$this->themes->js->longitude = Kohana::config('settings.default_lon');
		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');

		// Get Default Color
		$this->themes->js->default_map_all = $this->template->content->default_map_all = Kohana::config('settings.default_map_all');
		
		// Get default icon
		$this->themes->js->default_map_all_icon = $this->template->content->default_map_all_icon = '';
		if (Kohana::config('settings.default_map_all_icon_id'))
		{
			$icon_object = ORM::factory('media')->find(Kohana::config('settings.default_map_all_icon_id'));
			$this->themes->js->default_map_all_icon = $this->template->content->default_map_all_icon = Kohana::config('upload.relative_directory')."/".$icon_object->media_thumb;
		}

		// Load the alert radius view
		$alert_radius_view = new View('alert_radius_view');
		$alert_radius_view->show_usage_info = FALSE;
		$alert_radius_view->enable_find_location = FALSE;
		$alert_radius_view->css_class = "rb_location-radius";

		$this->template->content->alert_radius_view = $alert_radius_view;

		// Get locale
		$l = Kohana::config('locale.language.0');

		// Get the report listing view
		$report_listing_view = $this->_get_report_listing_view($l);

		// Set the view
		$this->template->content->report_listing_view = $report_listing_view;

		// Load the category
		$category_id = (isset($_GET['c']) AND intval($_GET['c']) > 0)? intval($_GET['c']) : 0;
		$category = ORM::factory('category', $category_id);

		if ($category->loaded)
		{
			// Set the category title
			$this->template->content->category_title = Category_Lang_Model::category_title($category_id,$l);
		}
		else
		{
			$this->template->content->category_title = "";
		}

		// Collect report stats
		$this->template->content->report_stats = new View('reports_stats');
		
		// Total Reports
		$total_reports = Incident_Model::get_total_reports(TRUE);

		// Get the date of the oldest report
		if (isset($_GET['s']) AND !empty($_GET['s']) AND intval($_GET['s']) > 0)
		{
			$oldest_timestamp =  intval($_GET['s']);
		}
		else
		{
			$oldest_timestamp = Incident_Model::get_oldest_report_timestamp();
		}

		// Get the date of the latest report
		if (isset($_GET['e']) AND !empty($_GET['e']) AND intval($_GET['e']) > 0)
		{
			$latest_timestamp = intval($_GET['e']);
		}
		else
		{
			$latest_timestamp = Incident_Model::get_latest_report_timestamp();
		}

		// Round the number of days up to the nearest full day
		$days_since = ceil((time() - $oldest_timestamp) / 86400);
		$avg_reports_per_day = ($days_since < 1)? $total_reports : round(($total_reports / $days_since),2);

		// Percent Verified
		$total_verified = Incident_Model::get_total_reports_by_verified(TRUE);
		$percent_verified = ($total_reports == 0) ? '-' : round((($total_verified / $total_reports) * 100),2).'%';

		// Category tree view
		$this->template->content->category_tree_view = category::get_category_tree_view();

		// Additional view content
		$this->template->content->custom_forms_filter = new View('reports_submit_custom_forms');
		$disp_custom_fields = customforms::get_custom_form_fields();
		$this->template->content->custom_forms_filter->disp_custom_fields = $disp_custom_fields;
		$this->template->content->oldest_timestamp = $oldest_timestamp;
		$this->template->content->latest_timestamp = $latest_timestamp;
		$this->template->content->report_stats->total_reports = $total_reports;
		$this->template->content->report_stats->avg_reports_per_day = $avg_reports_per_day;
		$this->template->content->report_stats->percent_verified = $percent_verified;
		$this->template->content->services = Service_Model::get_array();

		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
	}

	/**
	 * Helper method to load the report listing view
	 */
	private function _get_report_listing_view($locale = '')
	{
		// Check if the local is empty
		if (empty($locale))
		{
			$locale = Kohana::config('locale.language.0');
		}

		// Load the report listing view
		$report_listing = new View('user_listing');

		// Fetch all incidents
		
		
		$incidents = $this->fetch_users();
		
		
		
		// Set the view content
		$report_listing->incidents = $incidents;
		

		
		return $report_listing;
	}

	public function fetch_reports()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		if ($_GET)
		{
			$report_listing_view = $this->_get_report_listing_view();
			print $report_listing_view;
		}
		else
		{
			print "";
		}
	}

	/**
	 * Submits a new report.
	 */
	public function add($id = FALSE, $saved = FALSE)
	{
		$db = new Database();

		// First, are we allowed to submit new reports?
		if ( ! Kohana::config('settings.allow_reports'))
		{
			url::redirect(url::site().'main');
		}

		$this->template->header->this_page = 'resources';
		$this->template->content = new View('resources_add');

		$this->template->header->page_title .= Kohana::lang('ui_main.reports_submit_new').Kohana::config('settings.title_delimiter');
		
		//make sure the user is logged in as an admin or activarian
		if($this->user == null  OR $this->role == null OR ($this->role->name != 'Guardian' AND $this->role->name != 'admin'))
		{
			$this->template->content = new View('resources_login');
			return;
		}
		//grab user specifics
		$extra_user_info = ORM::factory('extrauserinfo')->where('user_id', $this->user)->find();
		$user_org_id = $extra_user_info->organization_id;
		$this->template->content->user_org_id = $user_org_id;

		//Retrieve API URL
		$this->template->api_url = Kohana::config('settings.api_url');

		// Setup and initialize form field names
		$form = array(
			'incident_title' => '',
			'incident_description' => '',
			'incident_date' => '',
			'incident_hour' => '',
			'incident_minute' => '',
			'incident_ampm' => '',
			'latitude' => '',
			'longitude' => '',
			'geometry' => array(),
			'location_name' => '',
			'country_id' => '',
			'country_name'=>'',
			'incident_category' => array(),
			'incident_news' => array(),
			'incident_video' => array(),
			'incident_photo' => array(),
			'incident_zoom' => '',
			'person_first' => '',
			'person_last' => '',
			'person_email' => '',
			'form_id'	  => '1',
			'custom_field' => array()
		);

		// Copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;
		$form_saved = ($saved == 'saved');

		// Initialize Default Values
		$form['incident_date'] = date("m/d/Y",time());
		$form['incident_hour'] = date('g');
		$form['incident_minute'] = date('i');
		$form['incident_ampm'] = date('a');
		$form['country_id'] = Kohana::config('settings.default_country');

		// Initialize Default Value for Hidden Field Country Name, just incase Reverse Geo coding yields no result
		$country_name = ORM::factory('country',$form['country_id']);
		$form['country_name'] = $country_name->country;

		// Initialize custom field array
		$form_id = $form['form_id'];
		$form['custom_field'] = customforms::get_custom_form_fields($id,$form_id,true);

		// GET custom forms
		$forms = array();
		foreach (customforms::get_custom_forms() as $custom_forms)
		{
			$forms[$custom_forms->id] = $custom_forms->form_title;
		}
		$this->template->content->forms = $forms;


		// Check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
			
			
			// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
			$post = array_merge($_POST, $_FILES);

			// Test to see if things passed the rule checks
			if (reports::validate($post))
			{

				// STEP 1: SAVE LOCATION
				$location = new Location_Model();
				reports::save_location($post, $location);

				// STEP 2: SAVE INCIDENT
				$incident = new Incident_Model();
				reports::save_report($post, $incident, $location->id);

				// STEP 2b: SAVE INCIDENT GEOMETRIES
				reports::save_report_geometry($post, $incident);

				// STEP 3: SAVE CATEGORIES
				reports::save_category($post, $incident);

				// STEP 4: SAVE MEDIA
				reports::save_media($post, $incident);

				// STEP 5: SAVE CUSTOM FORM FIELDS
				reports::save_custom_fields($post, $incident);

				// STEP 6: SAVE PERSONAL INFORMATION
				reports::save_personal_info($post, $incident);

				// Run evnets
				Event::run('ushahidi_action.report_submit', $post);
				Event::run('ushahidi_action.report_add', $incident);


				url::redirect('resources/thanks');
			}

			// No! We have validation errors, we need to show the form again, with the errors
			else
			{
				// Repopulate the form fields
				$form = arr::overwrite($form, $post->as_array());

				// Populate the error fields, if any
				$errors = arr::overwrite($errors, $post->errors('report'));
				$form_error = TRUE;
			}
		}

		// Retrieve Country Cities
		$default_country = Kohana::config('settings.default_country');
		$this->template->content->cities = $this->_get_cities($default_country);
		$this->template->content->multi_country = Kohana::config('settings.multi_country');

		$this->template->content->id = $id;
		$this->template->content->form = $form;
		$this->template->content->errors = $errors;
		$this->template->content->form_error = $form_error;

		$categories = $this->get_categories($form['incident_category']);
		$this->template->content->categories = $categories;

		// Pass timezone
		$this->template->content->site_timezone = Kohana::config('settings.site_timezone');

		// Pass the submit report message
		$this->template->content->site_submit_report_message = Kohana::config('settings.site_submit_report_message');

		// Retrieve Custom Form Fields Structure
		$this->template->content->custom_forms = new View('reports_submit_custom_forms');
		$disp_custom_fields = customforms::get_custom_form_fields($id,$form_id, FALSE);
		$this->template->content->disp_custom_fields = $disp_custom_fields;
		$this->template->content->stroke_width_array = $this->_stroke_width_array();
		$this->template->content->custom_forms->disp_custom_fields = $disp_custom_fields;
		$this->template->content->custom_forms->form = $form;

		// Javascript Header
		$this->themes->map_enabled = TRUE;
		$this->themes->datepicker_enabled = TRUE;
		$this->themes->treeview_enabled = TRUE;
		$this->themes->colorpicker_enabled = TRUE;

		$this->themes->js = new View('resources_add_edit_js');
		$this->themes->js->edit_mode = FALSE;
		$this->themes->js->incident_zoom = FALSE;
		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
		if (!$form['latitude'] OR !$form['latitude'])
		{
			$this->themes->js->latitude = Kohana::config('settings.default_lat');
			$this->themes->js->longitude = Kohana::config('settings.default_lon');
		}
		else
		{
			$this->themes->js->latitude = $form['latitude'];
			$this->themes->js->longitude = $form['longitude'];
		}
		$this->themes->js->geometries = $form['geometry'];


		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
	}

	 /**
	 * Displays a report.
	 * @param boolean $id If id is supplied, a report with that id will be
	 * retrieved.
	 */
	public function view($id = FALSE)
	{
		$this->template->header->this_page = 'resources';
		$this->template->content = new View('resources_view');

		// Load Akismet API Key (Spam Blocker)
		$api_akismet = Kohana::config('settings.api_akismet');

		// Sanitize the report id before proceeding
		$id = intval($id);

		if ($id > 0 AND Incident_Model::is_valid_incident($id,TRUE))
		{
			$incident = ORM::factory('incident')
				->where('id',$id)
				->where('incident_active',1)
				->find();
				
			// Not Found
			if ( ! $incident->loaded) 
			{
				url::redirect('resources');
			}
			
			$this->template->content->user = ORM::factory('user', $incident->user_id);

			// Comment Post?
			// Setup and initialize form field names

			$form = array(
				'comment_author' => '',
				'comment_description' => '',
				'comment_email' => '',
				'comment_ip' => '',
				'captcha' => ''
			);

			$captcha = Captcha::factory();
			$errors = $form;
			$form_error = FALSE;

			// Check, has the form been submitted, if so, setup validation

			if ($_POST AND Kohana::config('settings.allow_comments') )
			{
				// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
				$post = Validation::factory($_POST);

				// Add some filters
				$post->pre_filter('trim', TRUE);

				// Add some rules, the input field, followed by a list of checks, carried out in order
				if ( ! $this->user)
				{
					$post->add_rules('comment_author', 'required', 'length[3,100]');
					$post->add_rules('comment_email', 'required','email', 'length[4,100]');
				}
				$post->add_rules('comment_description', 'required');
				$post->add_rules('captcha', 'required', 'Captcha::valid');

				// Test to see if things passed the rule checks
				if ($post->validate())
				{
					// Yes! everything is valid
					if ($api_akismet != "")
					{
						// Run Akismet Spam Checker
						$akismet = new Akismet();

						// Comment data
						$comment = array(
							'website' => "",
							'body' => $post->comment_description,
							'user_ip' => $_SERVER['REMOTE_ADDR']
						);

						if ($this->user)
						{
							$comment['author'] = $this->user->name;
							$comment['email'] = $this->user->email;
						}
						else
						{
							$comment['author'] = $post->comment_author;
							$comment['email'] = $post->comment_email;
						}

						$config = array(
							'blog_url' => url::site(),
							'api_key' => $api_akismet,
							'comment' => $comment
						);

						$akismet->init($config);

						if ($akismet->errors_exist())
						{
							if ($akismet->is_error('AKISMET_INVALID_KEY'))
							{
								// throw new Kohana_Exception('akismet.api_key');
							}
							elseif ($akismet->is_error('AKISMET_RESPONSE_FAILED'))
							{
								// throw new Kohana_Exception('akismet.server_failed');
							}
							elseif ($akismet->is_error('AKISMET_SERVER_NOT_FOUND'))
							{
								// throw new Kohana_Exception('akismet.server_not_found');
							}

							// If the server is down, we have to post
							// the comment :(
							// $this->_post_comment($comment);
							$comment_spam = 0;
						}
						else
						{
							$comment_spam = ($akismet->is_spam()) ? 1 : 0;
						}
					}
					else
					{
						// No API Key!!
						$comment_spam = 0;
					}

					$comment = new Comment_Model();
					$comment->incident_id = $id;
					if ($this->user)
					{
						$comment->user_id = $this->user->id;
						$comment->comment_author = $this->user->name;
						$comment->comment_email = $this->user->email;
					}
					else
					{
						$comment->comment_author = strip_tags($post->comment_author);
						$comment->comment_email = strip_tags($post->comment_email);
					}
					$comment->comment_description = strip_tags($post->comment_description);
					$comment->comment_ip = $_SERVER['REMOTE_ADDR'];
					$comment->comment_date = date("Y-m-d H:i:s",time());

					// Activate comment for now
					if ($comment_spam == 1)
					{
						$comment->comment_spam = 1;
						$comment->comment_active = 0;
					}
					else
					{
						$comment->comment_spam = 0;
						$comment->comment_active = (Kohana::config('settings.allow_comments') == 1)? 1 : 0;
					}
					$comment->save();

					// Event::comment_add - Added a New Comment
					Event::run('ushahidi_action.comment_add', $comment);

					// Notify Admin Of New Comment
					$send = notifications::notify_admins(
						"[".Kohana::config('settings.site_name')."] ".
							Kohana::lang('notifications.admin_new_comment.subject'),
							Kohana::lang('notifications.admin_new_comment.message')
							."\n\n'".strtoupper($incident->incident_title)."'"
							."\n".url::base().'reports/view/'.$id
						);

					// Redirect
					url::redirect('reports/view/'.$id);

				}
				else
				{
					// No! We have validation errors, we need to show the form again, with the errors
					// Repopulate the form fields
					$form = arr::overwrite($form, $post->as_array());

					// Populate the error fields, if any
					$errors = arr::overwrite($errors, $post->errors('comments'));
					$form_error = TRUE;
				}
			}

			// Filters
			$incident_title = $incident->incident_title;
			$incident_description = $incident->incident_description;
			Event::run('ushahidi_filter.report_title', $incident_title);
			Event::run('ushahidi_filter.report_description', $incident_description);

			$this->template->header->page_title .= $incident_title.Kohana::config('settings.title_delimiter');

			// Add Features
			$this->template->content->features_count = $incident->geometry->count();
			$this->template->content->features = $incident->geometry;
			$this->template->content->incident_id = $incident->id;
			$this->template->content->incident_title = $incident_title;
			$this->template->content->incident_description = $incident_description;
			$this->template->content->incident_location = $incident->location->location_name;
			$this->template->content->incident_latitude = $incident->location->latitude;
			$this->template->content->incident_longitude = $incident->location->longitude;
			$this->template->content->incident_date = date('M j Y', strtotime($incident->incident_date));
			$this->template->content->incident_time = date('H:i', strtotime($incident->incident_date));
			$this->template->content->incident_category = $incident->incident_category;

			// Incident rating
			$rating = ORM::factory('rating')
					->join('incident','incident.id','rating.incident_id','INNER')
					->where('rating.incident_id',$incident->id)
					->find();
					
			$this->template->content->incident_rating = ($rating->rating == '')
				? 0
				: $rating->rating;

			// Retrieve Media
			$incident_news = array();
			$incident_video = array();
			$incident_photo = array();

			foreach($incident->media as $media)
			{
				if ($media->media_type == 4)
				{
					$incident_news[] = $media->media_link;
				}
				elseif ($media->media_type == 2)
				{
					$incident_video[] = $media->media_link;
				}
				elseif ($media->media_type == 1)
				{
					$incident_photo[] = array(
						'large' => url::convert_uploaded_to_abs($media->media_link),
						'thumb' => url::convert_uploaded_to_abs($media->media_thumb)
						);
				}
			}

			$this->template->content->incident_verified = $incident->incident_verified;

			// Retrieve Comments (Additional Information)
			$this->template->content->comments = "";
			if (Kohana::config('settings.allow_comments'))
			{
				$this->template->content->comments = new View('reports_comments');
				$incident_comments = array();
				if ($id)
				{
					$incident_comments = Incident_Model::get_comments($id);
				}
				$this->template->content->comments->incident_comments = $incident_comments;
			}
		}
		else
		{
			url::redirect('main');
		}

		// Add Neighbors
		$this->template->content->incident_neighbors = Incident_Model::get_neighbouring_incidents($id, TRUE, 0, 5);

		// News Source links
		$this->template->content->incident_news = $incident_news;


		// Video links
		$this->template->content->incident_videos = $incident_video;

		// Images
		$this->template->content->incident_photos = $incident_photo;

		// Create object of the video embed class
		$video_embed = new VideoEmbed();
		$this->template->content->videos_embed = $video_embed;

		// Javascript Header
		$this->themes->map_enabled = TRUE;
		$this->themes->photoslider_enabled = TRUE;
		$this->themes->videoslider_enabled = TRUE;
		$this->themes->js = new View('reports_view_js');
		$this->themes->js->incident_id = $incident->id;
		$this->themes->js->default_map = Kohana::config('settings.default_map');
		$this->themes->js->default_zoom = Kohana::config('settings.default_zoom');
		$this->themes->js->latitude = $incident->location->latitude;
		$this->themes->js->longitude = $incident->location->longitude;
		$this->themes->js->incident_zoom = $incident->incident_zoom;
		$this->themes->js->incident_photos = $incident_photo;

		// Initialize custom field array
		$this->template->content->custom_forms = new View('reports_view_custom_forms');
		$form_field_names = customforms::get_custom_form_fields($id, $incident->form_id, FALSE, "view");
		$this->template->content->custom_forms->form_field_names = $form_field_names;

		// Are we allowed to submit comments?
		$this->template->content->comments_form = "";
		if (Kohana::config('settings.allow_comments'))
		{
			$this->template->content->comments_form = new View('reports_comments_form');
			$this->template->content->comments_form->user = $this->user;
			$this->template->content->comments_form->form = $form;
			$this->template->content->comments_form->form_field_names = $form_field_names;
			$this->template->content->comments_form->captcha = $captcha;
			$this->template->content->comments_form->errors = $errors;
			$this->template->content->comments_form->form_error = $form_error;
		}

		// If the Admin is Logged in - Allow for an edit link
		$this->template->content->logged_in = $this->logged_in;

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
	}

	/**
	 * Report Thanks Page
	 */
	public function thanks()
	{
		$this->template->header->this_page = 'reports_submit';
		$this->template->content = new View('resource_add_thanks');

		// Rebuild Header Block
		$this->template->header->header_block = $this->themes->header_block();
		$this->template->footer->footer_block = $this->themes->footer_block();
	}

	/**
	 * Report Rating.
	 * @param boolean $id If id is supplied, a rating will be applied to selected report
	 */
	public function rating($id = false)
	{
		$this->template = "";
		$this->auto_render = FALSE;

		if (!$id)
		{
			echo json_encode(array("status"=>"error", "message"=>"ERROR!"));
		}
		else
		{
			if (!empty($_POST['action']) AND !empty($_POST['type']))
			{
				$action = $_POST['action'];
				$type = $_POST['type'];

				// Is this an ADD(+1) or SUBTRACT(-1)?
				if ($action == 'add')
				{
					$action = 1;
				}
				elseif ($action == 'subtract')
				{
					$action = -1;
				}
				else
				{
					$action = 0;
				}

				if (!empty($action) AND ($type == 'original' OR $type == 'comment'))
				{
					// Has this User or IP Address rated this post before?
					if ($this->user)
					{
						$filter = "user_id = ".$this->user->id;
					}
					else
					{
						$filter = "rating_ip = '".$_SERVER['REMOTE_ADDR']."' ";
					}

					if ($type == 'original')
					{
						$previous = ORM::factory('rating')
							->where('incident_id',$id)
							->where($filter)
							->find();
					}
					elseif ($type == 'comment')
					{
						$previous = ORM::factory('rating')
							->where('comment_id',$id)
							->where($filter)
							->find();
					}

					// If previous exits... update previous vote
					$rating = new Rating_Model($previous->id);

					// Are we rating the original post or the comments?
					if ($type == 'original')
					{
						$rating->incident_id = $id;
					}
					elseif ($type == 'comment')
					{
						$rating->comment_id = $id;
					}

					// Is there a user?
					if ($this->user)
					{
						$rating->user_id = $this->user->id;

						// User can't rate their own stuff
						if ($type == 'original')
						{
							if ($rating->incident->user_id == $this->user->id)
							{
								echo json_encode(array("status"=>"error", "message"=>"Can't rate your own Reports!"));
								exit;
							}
						}
						elseif ($type == 'comment')
						{
							if ($rating->comment->user_id == $this->user->id)
							{
								echo json_encode(array("status"=>"error", "message"=>"Can't rate your own Comments!"));
								exit;
							}
						}
					}

					$rating->rating = $action;
					$rating->rating_ip = $_SERVER['REMOTE_ADDR'];
					$rating->rating_date = date("Y-m-d H:i:s",time());
					$rating->save();

					// Get total rating and send back to json
					$total_rating = $this->_get_rating($id, $type);

					echo json_encode(array("status"=>"saved", "message"=>"SAVED!", "rating"=>$total_rating));
				}
				else
				{
					echo json_encode(array("status"=>"error", "message"=>"Nothing To Do!"));
				}
			}
			else
			{
				echo json_encode(array("status"=>"error", "message"=>"Nothing To Do!"));
			}
		}
	}

	public function geocode()
	{
		$this->template = "";
		$this->auto_render = FALSE;

		if (isset($_POST['address']) AND ! empty($_POST['address']))
		{
			$geocode_result = map::geocode($_POST['address']);
			if ($geocode_result)
			{
				echo json_encode(array_merge(
					$geocode_result, 
					array('status' => 'success')
				));
			}
			else
			{
				echo json_encode(array(
					'status' => 'error',
					'message' =>'ERROR!'
				));
			}
		}
		else
		{
			echo json_encode(array(
				'status' => 'error',
				'message' => 'ERROR!'
			));
		}
	}

	/**
	 * Retrieves Cities
	 */
	private function _get_cities()
	{
		$cities = ORM::factory('city')->orderby('city', 'asc')->find_all();
		$city_select = array('' => Kohana::lang('ui_main.reports_select_city'));

		foreach ($cities as $city)
		{
			$city_select[$city->city_lon.",".$city->city_lat] = $city->city;
		}

		return $city_select;
	}

	/**
	 * Retrieves Total Rating For Specific Post
	 * Also Updates The Incident & Comment Tables (Ratings Column)
	 */
	private function _get_rating($id = FALSE, $type = NULL)
	{
		if (!empty($id) AND ($type == 'original' OR $type == 'comment'))
		{
			if ($type == 'original')
			{
				$which_count = 'incident_id';
			}
			elseif ($type == 'comment')
			{
				$which_count = 'comment_id';
			}
			else
			{
				return 0;
			}

			$total_rating = 0;

			// Get All Ratings and Sum them up
			foreach (ORM::factory('rating')
							->where($which_count,$id)
							->find_all() as $rating)
			{
				$total_rating += $rating->rating;
			}
			
			return $total_rating;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Validates a numeric array. All items contained in the array must be numbers or numeric strings
	 *
	 * @param array $nuemric_array Array to be verified
	 */
	private function _is_numeric_array($numeric_array=array())
	{
		if (count($numeric_array) == 0)
			return FALSE;
		else
		{
			foreach ($numeric_array as $item)
			{
				if (! is_numeric($item))
					return FALSE;
			}

			return TRUE;
		}
	}

	/**
	 * Array with Geometry Stroke Widths
    */
	private function _stroke_width_array()
	{
		for ($i = 0.5; $i <= 8 ; $i += 0.5)
		{
			$stroke_width_array["$i"] = $i;
		}

		return $stroke_width_array;
	}

	/**
	 * Ajax call to update Incident Reporting Form
    */
    public function switch_form()
    {
        $this->template = "";
        $this->auto_render = FALSE;
        isset($_POST['form_id']) ? $form_id = $_POST['form_id'] : $form_id = "1";
        isset($_POST['incident_id']) ? $incident_id = $_POST['incident_id'] : $incident_id = "";

		$form_fields = customforms::switcheroo($incident_id,$form_id);
        echo json_encode(array("status"=>"success", "response"=>$form_fields));
    }
    
    /**
     * Retrieves Categories
     */
    protected function get_categories($selected_categories)
    {
    	$categories = ORM::factory('category')
    	->where('category_visible', '1')
    	->where('parent_id', '0')
    	->where('category_trusted != 1')
    	->where('id != '.extrauserinfohelper::$organization_parent_id)
    	->where('id != '.extrauserinfohelper::$roles_parent_id)
    	->orderby('category_position', 'ASC')
    	->orderby('category_title', 'ASC')
    	->find_all();
    
    	return $categories;
    }

    
    public  function fetch_users($paginate = FALSE, $items_per_page = 0)
    {
    	// Reset the paramters
    	$params = array();
    
    	// Initialize the category id
    	$category_id = 0;
    
    	$table_prefix = Kohana::config('database.default.table_prefix');
    
    	// Fetch the URL data into a local variable
    	$url_data = array_merge($_GET);
    
    	// Split selected parameters on ","
    	// For simplicity, always turn them into arrays even theres just one value
    	$exclude_params = array('c');
    	foreach ($url_data as $key => $value)
    	{
    		if (in_array($key, $exclude_params) AND !is_array($value))
    		{
    			$url_data[$key] = explode(",", $value);
    		}
    	}
    
    	//> BEGIN PARAMETER FETCH
    	//
    	// Check for the category parameter
    	//
    	if (isset($url_data['c']) AND is_array($url_data['c']))
    	{
    	// Sanitize each of the category ids
    		$category_ids = array();
    		foreach ($url_data['c'] as $c_id)
    		{
    			if (intval($c_id) > 0)
    			{
    				$category_ids[] = intval($c_id);
    			}
    		}
    		
	    	// Check if there are any category ids
    		if (count($category_ids) > 0)
    		{
    			$category_ids = implode(",", $category_ids);
    			array_push($params,
    			'(c.id IN ('.$category_ids.') OR c.parent_id IN ('.$category_ids.'))',
    			'c.category_visible = 1');
    		}
    	}
    
    	//> END PARAMETER FETCH

    	// Fetch all the incidents
    	$all_incidents = $this->get_users_from_db($params);
    	return $all_incidents;
    }
    
    
    /**
     * Gets the reports that match the conditions specified in the $where parameter
     * The conditions must relate to columns in the incident, location, incident_category
     * category and media tables
     *
     * @param array $where List of conditions to apply to the query
     * @param mixed $limit No. of records to fetch or an instance of Pagination
     * @param string $order_field Column by which to order the records
     * @param string $sort How to order the records - only ASC or DESC are allowed
     * @return Database_Result
     */
    public  function get_users_from_db($where = array(), $limit = NULL, $order_field = NULL, $sort = NULL)
    {
    	// Get the table prefix
    	$table_prefix = Kohana::config('database.default.table_prefix');
    
    	// To store radius parameters
    	$radius = array();
    	$having_clause = "";
    	if (array_key_exists('radius', $where))
    	{
    		// Grab the radius parameter
    		$radius = $where['radius'];
    
    		// Delete radius parameter from the list of predicates
    		unset ($where['radius']);
    	}
    
    	// Query
    	$sql = 'SELECT DISTINCT i.id, i.riverid, i.name, i.email, i.username ';
    
    	
    
    	$sql .=  'FROM '.$table_prefix.'users i '
    	    	. 'INNER JOIN '.$table_prefix.'extrauserinfo_category ic ON (ic.user_id = i.id) '
    	. 'INNER JOIN '.$table_prefix.'category c ON (ic.category_id = c.id) ';
    
    	$sql .= 'WHERE i.id <> 0 ';
    
    	// Check for the additional conditions for the query
    	if ( ! empty($where) AND count($where) > 0)
    	{
    		foreach ($where as $predicate)
    		{
    			$sql .= 'AND '.$predicate.' ';
    		}
    	}
    
    	// Add the having clause
    	$sql .= $having_clause;
    
    	$sql .= 'ORDER BY i.username ASC ';
  
    
    	// Kohana::log('debug', $sql);
    	// Database instance for the query
    	$db = new Database();
    
    	// Return
    	return $db->query($sql);
    }
    
    

}