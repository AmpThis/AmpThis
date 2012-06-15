<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	//START THE SESSION FOR THIS PAGE.
	session_start();
	
	class Admin extends CI_Controller {
		
		/*
			Load defaults
			var my_nav creates the top level navigation and populates from the database.
			
			@librarys loaded by default are:
			
				@database = database engine to manipulate data from the database
				@session  = stores sessions for user authentication
				@reformat = easy reformat strings
				@alg 	  = the glucommander algorithm functions to calculate values
				@nav	  = uses the navigation module to build all navigation menues from the database
				@parser   = uses easy template engine for code seperation.
		*/
		
		var $my_nav;
		var $html_title;
		var $h2_title;
		var $user_level;
		
		//THIS FUNCTION IS CALLED EVERYTIME THE CONTROLLER IS RAN		
		public function __construct() {
			parent::__construct();
			
			//LOAD MODULES AND HELPERS HERE BY DEFAULT
			$this->load->helper('form');
			
			//HTML PAGE TITLE
			$this->html_title = 'Glucommander&trade; Enterprise';
			
			//THIS IS HOW WE KNOW WHO IS LOGGED IN
			$session_data = $this->session->userdata('logged_in');
			
			//Define if the user is a valid user
			$this->validUser = ($this->session->userdata('logged_in')) ? true : false;
			
			//Define the level of the user
			$this->user_level = ($this->validUser) ? $this->session->userdata['logged_in']["Level"] : 1;
			
			//CURRENT MENU @PARAM = BUTTON CODE -> USER LEVEL
			$this->my_nav = $this->nav->main_nav("4", $this->user_level);		
			
			//library for form validation
			$this->load->library('form_validation');
		}
		
		//NO INDEX IS NEEDED, BUT JUST INCASE SOMEONE HITS UP THE ADMIN URL, IT WILL REDIRECT THEM.
		public function index() {
			if($this->session->userdata('logged_in')) {
				redirect('admin/add_user', 'refresh');
			}else {
				redirect('user/login', 'refresh');	
			}
		}
		
		/*
			USER ADMIN CONTROLLERS
			0.) Add User
			1.) View Users
			2.) User Audit Trail
			3.) Log
			
			CONFIGURATION SETTINGS
			0.) General Settings
			1.) Protocol Settings
			2.) Subq Therapy
			3.) Communication Alerts
			4.) Facility
			5.) Hospital Units
			6.) SMTP Settings
			7.) Security Settings
			8.) HL7 Interface Configuration
			9.) License Information
			
			NUMBERS ARE USED TO HIGHLIGHT CURRENT BUTTONS WHEN CONTROLLER LOADS
		*/
		
		public function Add_user() {
			if($this->validUser) {
				if($this->user_level >= 2) {
					//LOG ACTIVITY TO THE DATABASE 
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed admin/add_user page.', 
						$_SERVER['REMOTE_ADDR']
					);
					
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'Add Users';
					
					$this->load->model('options');
					
					$units = $this->options->list_hospital_units();
					$user_roles = $this->options->list_user_roles();
					
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("0", $this->user_level);
					
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("NULL", $this->user_level);			
	
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin'),
						'units'	=> $units,
						'user_roles' => $user_roles, 
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					//
					$this->load->view('forms/add_user', $data);
					//
				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				//IF USER SESSION DOESNT EXISTS, REDIRECT TO LOGIN
				redirect('user/login', 'refresh');
			}
		}
		
		//Function used to use Servertime instead of local client time.
		public function Server_time() {
			$now = new DateTime(); 
			echo $now->format("M j, Y H:i:s"); 
		}
		
		public function Submit_user() {
			
			//<H2>HTML HEADER TEXT</H2>
			$this->h2_title = 'Add Users';
			
			$this->load->model('options');
			
			$units = $this->options->list_hospital_units();
			$user_roles = $this->options->list_user_roles();
			
			//LEFT SIDE NAVIGATION
			$user_nav = $this->nav->user_nav("0", $this->user_level);
			
			//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
			$config_nav = $this->nav->config_nav("NULL", $this->user_level);						

			$this->load->library('form_validation');
			
			$this->form_validation->set_rules('Username', 'Username', 'trim|required|xss_clean|is_unique[G2_Users.Username]|callback_check_user');
			$this->form_validation->set_rules('Password', 'Password', 'required');
			$this->form_validation->set_rules('ConfirmPassword', 'ConfirmPassword', 'required|matches[Password]');
			$this->form_validation->set_rules('FirstName', 'FirstName', 'trim|required|xss_clean');
			$this->form_validation->set_rules('LastName', 'LastName', 'trim|required|xss_clean');
			$this->form_validation->set_rules('EmailAddress', 'EmailAddress', 'required|valid_email');
			$this->form_validation->set_rules('PhoneNumber', 'MobilePhone', 'trim|xss_clean');
			$this->form_validation->set_rules('Title', 'Title', 'trim|xss_clean');
			$this->form_validation->set_rules('RoleName', 'RoleName', 'trim|required|xss_clean');
			$this->form_validation->set_rules('hospital_unit', 'HospitalUnit', 'trim|required|xss_clean');
			$this->form_validation->set_rules('default_unit', 'DefaultUnit', 'trim|required|xss_clean');
			
			$data = array(
				'title' => $this->html_title,
				'page_title' => $this->h2_title,
				'nav' => $this->my_nav,
				'user_nav' => $user_nav,
				'config_nav' => $config_nav,
				'toolbar' => $this->toolbar->build_toolbar('admin'),
				'units'	=> $units,
				'user_roles' => $user_roles,
				'user_level' => $this->user_level
			);
			
			if($this->form_validation->run() == FALSE) {
				//Field validation failed.  Redirect Back to form
				$this->load->view('forms/add_user', $data);
			}else{
				//Go to private area
				redirect('admin/view_users', 'refresh');
			}
	
		}
		
		public function Remove_user($user) {
			if($this->validUser) {
				if($this->user_level >= 2) {
					$this->users->remove_user($user);
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User deleted UserID(' . $user . ') from system.', 
						$_SERVER['REMOTE_ADDR']
					);
					redirect('admin/view_users','refresh');
				}else {
					reirect('access_denied', 'refresh');	
				}
			}else {
				redirect('user/login', 'refresh');	
			}
		}
		
		public function Edit_user($user) {
			if($this->validUser) {
				if($this->user_level >= 2) {
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'],
						$this->session->userdata['logged_in']['Username'],
						$this->session->userdata['logged_in']['Email'],
						'User edited UserID(' . $user . ') in system.',
						$_SERVER['REMOTE_ADDR']
					);
					$user = $this->users->single_user($user);
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'Add Users';
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("0", $this->user_level);
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("NULL", $this->user_level);	
					//Load Options	
					$this->load->model('options');
					//load options for units drop down
					$units = $this->options->list_hospital_units();
					//load options for user roles drop down
					$user_roles = $this->options->list_user_roles();
					//prepare data for view
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin'),
						'user_level' => $this->user_level,
						'user' => $user
					);
					
					$this->load->view('forms/edit_user_details', $data);
					
				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				redirect('user/login', 'refresh');	
			}
		}
		
		function check_user() {
			//Field validation succeeded.  Validate against database
			$Username = $this->input->post('Username');
			
			$data = array(
				'RoleNameID' => $this->input->post('RoleName'),
				'Username' => $this->input->post('Username'),
				'Password' => md5($this->input->post('Password')),
				'FirstName' => $this->input->post('FirstName'),
				'LastName' => $this->input->post('LastName'),
				'EmailAddress' => $this->input->post('EmailAddress'),
				'MobilePhone' => $this->input->post('PhoneNumber'),
				'isActive' => $this->input->post('isActive'),
				'Title' => $this->input->post('Title'),
				'LastLoginDateTime' => $this->input->post('DateTime'),
				'LastLoginIP' => $this->input->post('LastLoginIP'),
				'GeneratedPassword' => $this->input->post('GeneratedPassword'),
				'UserHospitalUnitID' => $this->input->post('hospital_unit'),
				'DefaultUserHospitalUnit' => $this->input->post('default_unit')
			);
	
			//query the database
			$result = $this->users->add_user($data);
	
			if($result) {
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User added username(' . $Username . ') to system.', 
						$_SERVER['REMOTE_ADDR']
					);

				$this->form_validation->set_message('check_database', 'User added to database');
				return TRUE;
			}else {
				$this->form_validation->set_message('check_database', 'Please supply all fields marked required to continue.');
				return false;
			}
		}

		
		public function View_users() {
			if($this->validUser) {
				if($this->user_level >= 2) {
					//LOG ACTIVITY TO THE DATABASE 
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed admin/view_users page.', 
						$_SERVER['REMOTE_ADDR']
					);
					
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'View Users';
											
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("1", $this->user_level);
					
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("NULL", $this->user_level);		
					
					//QUERY THE DATABASE AND RETURN ALL USERS
					$users = $this->users->all_users();	
	
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin'),
						'users'	=> $users,
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					
					$this->load->view('pages/admin/view_users', $data);
					
				}else {
					redirect('access_denied', 'refresh');	
				}
				
			}else {
				//IF USER SESSION DOESNT EXISTS, REDIRECT TO LOGIN
				redirect('user/login', 'refresh');
			}
		}
		
		public function User_audit_trail() {
			if($this->validUser) {
				if($this->user_level >= 2) {
					
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed admin/user_audit_trail page.', 
						$_SERVER['REMOTE_ADDR']
					);
					
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'View Users';
											
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("2", $this->user_level);
					
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("NULL", $this->user_level);	
					
					//Get audit information for all users
					$audit = $this->users->user_audit();
					$trail = array();
					if($audit)
						foreach($audit as $key => $value) {
							$trail[$key] = $value;
							$trail[$key]->ActionDate = $this->reformat->ConvertDateTime($trail[$key]->ActionDate);
						}
					
					$data = array(
						'title' => 'Glucommander&trade;  Enterprise',
						'page_title' => 'Admin',
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin'),
						'audit' => $trail,
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					
					$this->load->view('pages/admin/user_audit_trail', $data);
					
				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				redirect('user/login', 'refresh');	
			}
		}
		
		public function Log() {
			if($this->validUser) {
				if($this->user_level >= 2) {
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed admin/log page.', 
						$_SERVER['REMOTE_ADDR']
					);
					
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'View Users';
											
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("3", $this->user_level);
					
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("NULL", $this->user_level);	
					
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin'),
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					
					$this->load->view('pages/admin/log', $data);
					
				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				redirect('user/login', 'refresh');	
			}
		}
		
		public function Settings($type) {
			if($this->validUser) {
				if($this->user_level >= 3) {
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("NULL", $this->user_level);
					
					switch ($type) {
						case 'general':
							//log history
							$this->users->update_history(
								$this->session->userdata['logged_in']['UserID'], 
								$this->session->userdata['logged_in']['Username'], 
								$this->session->userdata['logged_in']['Email'], 
								'User Viewed settings/general page.', 
								$_SERVER['REMOTE_ADDR']
							);
							//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
							$config_nav = $this->nav->config_nav("0", $this->user_level);	
							$this->h2_title = 'General Settings';
							$template = 'general_settings';
						break;
						case 'protocol':
							//log history
							$this->users->update_history(
								$this->session->userdata['logged_in']['UserID'], 
								$this->session->userdata['logged_in']['Username'], 
								$this->session->userdata['logged_in']['Email'], 
								'User Viewed settings/protocol page.', 
								$_SERVER['REMOTE_ADDR']
							);
							//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
							$config_nav = $this->nav->config_nav("1", $this->user_level);	
							$this->h2_title = 'Protocol Settings';
							$template = 'protocol_settings';
						break;
						case 'smtp':
							//log history
							$this->users->update_history(
								$this->session->userdata['logged_in']['UserID'], 
								$this->session->userdata['logged_in']['Username'], 
								$this->session->userdata['logged_in']['Email'], 
								'User Viewed settings/smtp page.', 
								$_SERVER['REMOTE_ADDR']
							);
							//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
							$config_nav = $this->nav->config_nav("6", $this->user_level);	
							$this->h2_title = 'SMTP Settings';
							$template = 'smtp_settings';
						break;
						case 'security':
							//log history
							$this->users->update_history(
								$this->session->userdata['logged_in']['UserID'], 
								$this->session->userdata['logged_in']['Username'], 
								$this->session->userdata['logged_in']['Email'], 
								'User Viewed settings/general page.', 
								$_SERVER['REMOTE_ADDR']
							);
							//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
							$config_nav = $this->nav->config_nav("7", $this->user_level);	
							$this->h2_title = 'Security Settings';
							$template = 'security_settings';
						break;	
					}
					
					$toolbar = $this->toolbar->build_toolbar('admin');
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin'),
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					$this->load->view('forms/' . $template, $data);

				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				redirect('user/login', 'refresh');	
			}
		}
				
		public function Subq_therapy() {
			if($this->validUser) {
				if($this->user_level >= 3) {
					
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed admin/subq_therapy page.', 
						$_SERVER['REMOTE_ADDR']
					);
					
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'Subq Therapy';
											
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("NULL", $this->user_level);
					
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("2", $this->user_level);	
					
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin'),
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					
					$this->load->view('forms/subq_therapy', $data);
					
				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				redirect('user/login', 'refresh');	
			}
		}
		
		public function Communication_alerts() {
			if($this->validUser) {
				if($this->user_level >= 3) {
					
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed admin/communication_alerts page.', 
						$_SERVER['REMOTE_ADDR']
					);
					
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'Communication Alerts';
											
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("NULL", $this->user_level);
					
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("3", $this->user_level);	
					
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin'),
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					$this->load->view('pages/admin/communication_alerts', $data);
				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				redirect('user/login', 'refresh');	
			}
		}
		
		public function Facility() {
			if($this->validUser) {
				if($this->user_level >= 3) {
					
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed admin/facility page.', 
						$_SERVER['REMOTE_ADDR']
					);
					
					$this->load->model('options');
					
					$facility = $this->options->list_facility();
					
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'Add New Facility';
											
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("NULL", $this->user_level);
					
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("4", $this->user_level);	
					
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin/view_user'),
						'facility' => $facility,
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					$this->load->view('forms/facility', $data);
				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				//IF USER SESSION DOESNT EXISTS, REDIRECT TO LOGIN
				redirect('user/login','refresh');	
			}
		}
		
		public function Add_facility() {
			$this->form_validation->set_rules('facility', 'Facility', 'trim|required|callback_check_facility');
			if($this->form_validation->run() == FALSE) {
				redirect('admin/facility');
			}else {
				redirect('admin/facility');
			}
		}
		
		public function check_facility() {
			$this->load->model('options');
			//Field validation succeeded.  Validate against database
			$name = $this->input->post('facility');
	
			//query the database
			$result = $this->options->check_facility($name);
			if(!$result) {
				$this->options->insert_facility($name);
				return TRUE;
			}else {
				$this->form_validation->set_message('check_database', 'This Facility already exists in the database. Please try again.');
				return false;
			}
		}
		
		public function Hospital_units() {
			if($this->validUser) {
				if($this->user_level >= 3) {
					
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed admin/facility page.', 
						$_SERVER['REMOTE_ADDR']
					);
					
					$this->load->model('options');
					
					$facility = $this->options->list_facility();
					$units = $this->options->list_hospital_units();
					
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'Add New Unit';
											
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("NULL", $this->user_level);
					
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("5", $this->user_level);	
					
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin/view_user'),
						'facility' => $facility,
						'units'	=> $units,
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					$this->load->view('forms/hospital_units', $data);
				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				//IF USER SESSION DOESNT EXISTS, REDIRECT TO LOGIN
				redirect('user/login','refresh');	
			}
		}
		public function Add_unit() {
			$this->form_validation->set_rules('unit', 'HospitalUnitName', 'trim|required|callback_check_unit');
			if($this->form_validation->run() == FALSE) {
				$this->users->update_history(
					$this->session->userdata['logged_in']['UserID'], 
					$this->session->userdata['logged_in']['Username'], 
					$this->session->userdata['logged_in']['Email'], 
					'User Viewed admin/facility page.', 
					$_SERVER['REMOTE_ADDR']
				);
				
				$this->load->model('options');
				
				$facility = $this->options->list_facility();
				$units = $this->options->list_hospital_units();
				
				//<H2>HTML HEADER TEXT</H2>
				$this->h2_title = 'Add New Facility';
										
				//LEFT SIDE NAVIGATION
				$user_nav = $this->nav->user_nav("NULL", $this->user_level);
				
				//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
				$config_nav = $this->nav->config_nav("4", $this->user_level);	
				
				$data = array(
					'title' => $this->html_title,
					'page_title' => $this->h2_title,
					'nav' => $this->my_nav,
					'user_nav' => $user_nav,
					'config_nav' => $config_nav,
					'toolbar' => $this->toolbar->build_toolbar('admin/view_user'),
					'facility' => $facility,
					'units'	=> $units,
					'user_level' => $this->user_level
				);
				
				//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
				$this->load->view('forms/hospital_units', $data);
			}else {
				redirect('admin/hospital_units', 'refresh');
			}
		}
		
		public function check_unit() {
			$this->load->model('options');
			//Field validation succeeded.  Validate against database
			$facility = $this->input->post('facility');
			$unit = $this->input->post('unit');
			//query the database
			$result = $this->options->check_facility($facility, $unit);
			if($result) {
				$this->options->insert_unit($facility,$unit);
				return TRUE;
			}else {
				$this->form_validation->set_message('check_unit', 'This Unit already exits for this Facility. Please try again.');
				return false;
			}
		}
		
		public function Hl7_interface_config() {
			if($this->validUser) {
				if($this->user_level >= 2) {
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed Hl7 Configuration Page page.', 
						$_SERVER['REMOTE_ADDR']
					);
					
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'View Users';
											
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("NULL", $this->user_level);
					
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("8", $this->user_level);	
					
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin'),
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					
					$this->load->view('forms/hl7_interface_configuration', $data);
					
				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				redirect('user/login', 'refresh');	
			}
		}
		
		public function License_information() {
			if($this->validUser) {
				if($this->user_level >= 2) {
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed License Info page.', 
						$_SERVER['REMOTE_ADDR']
					);
					
					//<H2>HTML HEADER TEXT</H2>
					$this->h2_title = 'License Information';
											
					//LEFT SIDE NAVIGATION
					$user_nav = $this->nav->user_nav("NULL", $this->user_level);
					
					//PASS NULL VALUE TO NOT HIGHLIGHT A CURRENT NAV ITEM
					$config_nav = $this->nav->config_nav("9", $this->user_level);	
					
					$data = array(
						'title' => $this->html_title,
						'page_title' => $this->h2_title,
						'nav' => $this->my_nav,
						'user_nav' => $user_nav,
						'config_nav' => $config_nav,
						'toolbar' => $this->toolbar->build_toolbar('admin'),
						'user_level' => $this->user_level
					);
					
					//LOAD TEMPLATE VIEWS AND PASS THE DATA ARRAY
					
					$this->load->view('pages/admin/license_information', $data);
					
				}else {
					redirect('access_denied', 'refresh');	
				}
			}else {
				redirect('user/login', 'refresh');	
			}
		}
		
	}