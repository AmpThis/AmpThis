<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	//Start the session for this page.
	session_start();
	
	class Patients extends CI_Controller {
		
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
		
		//controller global nav variable
		var $my_nav;
		//stores logged in users user level 
		var $user_level;
		var $validUser;
		
		public function __construct() {
			parent::__construct();
			//THIS IS HOW WE KNOW WHO IS LOGGED IN
			$session_data = $this->session->userdata('logged_in');
			//load the patient module by default for the patient controllers and sub controllers.
			$this->load->model('patient');
			//Define if the user is a valid user
			$this->validUser = ($this->session->userdata('logged_in')) ? true : false;
			//Define the level of the user
			$this->user_level = ($this->session->userdata('logged_in')) ? $this->session->userdata['logged_in']["Level"] : 1;
			$this->load->library('form_validation');
			//reformat custom library
			$this->load->library('reformat');
			//New Toolbar beta
			//$this->load->model('tools');

		}
		
		public function Current() {
			if($this->validUser) {
				
				$this->users->update_history(
					$this->session->userdata['logged_in']['UserID'], 
					$this->session->userdata['logged_in']['Username'], 
					$this->session->userdata['logged_in']['Email'], 
					'User Viewed Current Patients', $_SERVER['REMOTE_ADDR']
				);
				
				$this->my_nav = $this->nav->main_nav("0", $this->user_level);
				
				$iv_patients    = $this->patient->current_patients('1');
				//var_dump($iv_patients);
				$subq_patients  = $this->patient->current_patients('2');	
				
				
				
				$IVArr = array();
				$bgArr = array();
				if($iv_patients)
					foreach($iv_patients as $key => $value) {
						
						$IVArr[$key] = $value;
						$IVArr[$key]->Birthday = $this->reformat->Birthday($IVArr[$key]->DOB);
						$IVArr[$key]->divid = $IVArr[$key]->PatientID;
						$IVArr[$key]->class = (($this->reformat->Age($IVArr[$key]->DOB) <= '13') ? 'pediatric' : 'adult');
						$IVArr[$key]->BG = $this->reformat->dateDiff(date('Y-m-d H:i:s'), $IVArr[$key]->NextBGAt);
						array_push($bgArr, $this->patient->get_last_bg($IVArr[$key]->PatientID));
						$IVArr[$key]->LastBG = $bgArr;
					}
					
				$SubqArr = array();
				if($subq_patients)
					foreach($subq_patients as $key => $value) {
						$SubqArr[$key] = $value;
						$SubqArr[$key]->Birthday = $this->reformat->Birthday($SubqArr[$key]->DOB);
						$SubqArr[$key]->divid = $SubqArr[$key]->PatientHash;
						$SubqArr[$key]->class = (($this->reformat->Age($SubqArr[$key]->DOB) <= '13') ? 'pediatric' : 'adult');
					}
				
				$data = array(
					'page_title' => 'Glucommander&trade; Enterprise | Current Patients',
					'nav' => $this->my_nav,
					'toolbar' => $this->toolbar->build_toolbar('patients'),
					'iv_count' => count($iv_patients),
					'subq_count' => count($subq_patients),
					'iv_patients' => $IVArr,
					'subq_patients' => $SubqArr
					
				);
				
				$this->load->view('pages/patients/current', $data);
			}else {
				redirect('user/login', 'refresh');	
			}
			
		}
		
		function Add($form = NULL) {
			if($this->validUser) {
				if(!$form) {
					
					$this->load->model('tools');
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed patients/add page.', $_SERVER['REMOTE_ADDR']
					);
					
					$this->my_nav = $this->nav->main_nav("1", $this->user_level);
								
					$result = $this->patient->get_hl7_patients();
					$patient_details = array();
					$patient_details_array = array();
					
					if($result) {
						foreach($result as $key => $value) {
							$patient_details_array[$key] = $value;
							$patient_details_array[$key]->Birthday = $this->reformat->Birthday($patient_details_array[$key]->DOB);
							$patient_details_array[$key]->Started = $patient_details_array[$key]->STATUS;
						};
						$patient_details = $patient_details_array;
					}
					$data = array(
						'nav'	   => $this->my_nav,
						'toolbar'  => $this->toolbar->build_toolbar('patients/add'),
						'patients' => $patient_details
					);
					$this->parser->parse('pages/patients/add', $data);
				}else {
					
					$this->users->update_history(
						$this->session->userdata['logged_in']['UserID'], 
						$this->session->userdata['logged_in']['Username'], 
						$this->session->userdata['logged_in']['Email'], 
						'User Viewed patients/add/new_patient page.', $_SERVER['REMOTE_ADDR']
					);
					
					$this->load->model('options');
					
					$diagnosis = $this->options->list_diagnosis();
					$units = $this->options->list_hospital_units();
					$physicians = $this->options->list_physicians();
					$patientType = $this->options->list_patient_type();
					
					$this->my_nav = $this->nav->main_nav("1", $this->user_level);
													
					$data = array(
						'nav'	   => $this->my_nav,
						'toolbar'  => $this->toolbar->build_toolbar('patients/add'),
						'diagnosis' => $diagnosis,
						'unit' => $units,
						'physicians' => $physicians,
						'patientType' => $patientType,
						'UserID' => $this->session->userdata['logged_in']['UserID'],
						'PatientHash' => $this->reformat->RandomNumber(),
					);
					
					$this->parser->parse('forms/new_patient', $data);
				}
			}else {
				//If no session, redirect to login page
				redirect('user/login', 'refresh');
			}
	
		}
		
		public function Add_new_patient() {
			$this->load->model('options');
			
			$diagnosis = $this->options->list_diagnosis();
			$units = $this->options->list_hospital_units();
			$physicians = $this->options->list_physicians();
			$patientType = $this->options->list_patient_type();
			
			$this->my_nav = $this->nav->main_nav("1", $this->user_level);
											
			$data = array(
				'nav'	   => $this->my_nav,
				'toolbar'  => $this->toolbar->build_toolbar('patients/add'),
				'diagnosis' => $diagnosis,
				'unit' => $units,
				'physicians' => $physicians,
				'patientType' => $patientType,
				'UserID' => $this->session->userdata['logged_in']['UserID'],
				'PatientHash' => $this->reformat->RandomNumber(),
			);
			
			/*$config = array(
				'add' => array(
					array(
						'field' => 'first_name',
						'label' => 'First Name',
						'rules' => 'trim|alpha|required|callback_check_patient'
					),array(
						'field' => 'last_name',
						'label' => 'Last Name',
						'rules' => 'trim|alpha|required'
					),array(
						'field' => 'patient_id',
						'label' => 'Patient ID',
						'rules' => 'trim|required|alpha_numeric'
					),array(
						'field' => 'year',
						'label' => 'Year',
						'rules' => 'required|numeric'
					),array(
						'field' => 'month',
						'label' => 'Month',
						'rules' => 'required|numeric'
					),array(
						'field' => 'day',
						'label' => 'Day',
						'rules' => 'required|numeric'
					),array(
						'field' => 'gender',
						'label' => 'Gender',
						'rules' => 'numeric'
					),array(
						'field' => 'diagnosis',
						'label' => 'Diagnosis',
						'rules' => 'numeric'
					),array(
						'field' => 'hospital_units',
						'label' => 'HospitalUnits',
						'rules' => 'numeric'
					),array(
						'field' => 'height',
						'label' => 'Height',
						'rules' => 'required|numeric'
					),array(
						'field' => 'weight',
						'label' => 'Weight',
						'rules' => 'required|numeric'
					),array(
						'field' => 'A1C',
						'label' => 'A1C',
						'rules' => 'numeric'
					),array(
						'field' => 'physician',
						'label' => 'Physician',
						'rules' => 'required|numeric'
					),array(
						'field' => 'patient_type',
						'label' => 'Patient Type',
						'rules' => 'required|numeric'
					),array(
						'field' => 'patient_hash',
						'label' => 'Patient Hash',
						'rules' => 'required|numeric|is_unique[G2_Patients.PatientHash]'
					)
				)
			);*/
			
			$this->users->update_history(
				$this->session->userdata['logged_in']['UserID'], 
				$this->session->userdata['logged_in']['Username'], 
				$this->session->userdata['logged_in']['Email'], 
				'User added a patient status code G00', $_SERVER['REMOTE_ADDR']
			);
			
			$this->load->model('patient');
			$dob = $this->input->post('year') . '-' . $this->input->post('month') . '-' . $this->input->post('day');
			$form = array(
				'CreatedBy' => $this->input->post('createdBy'),
				'CreatedDate' => $this->input->post('createdDate'),
				'FirstName' => $this->input->post('first_name'),
				'LastName' => $this->input->post('last_name'),
				'Height' => $this->input->post('height'),
				'Weight' => $this->input->post('weight'),
				'DOB' => $dob,
				'AdmittingPhysicianID' => $this->input->post('physician'),
				'AttendingPhysicianID' => $this->input->post('physician'),
				'Gender' => $this->input->post('gender'),
				'Status' => $this->input->post('status'),
				'A1C' => $this->input->post('A1C'),
				'PatientType' => $this->input->post('patient_type'),
				'PatientHash' => $this->input->post('patient_hash'),
				'MRN' => $this->input->post('patient_id'),
				'AccountNumber' => $this->input->post('patient_id'),
				'EPIN' => $this->input->post('patient_id'),
				'HospitalUnitID' => $this->input->post('hospital_unit'),
				'DiagnosisID' => $this->input->post('diagnosis'),
			);
			$this->patient->insert_patient($form);
			redirect('patients/add', 'refresh');
		}
		
		public function check_patient() {
				return TRUE;
		}
		
		public function Details($type, $id) {
			if($this->validUser) {
				$this->my_nav = $this->nav->main_nav("0", $this->user_level);
				$this->users->update_history(
					$this->session->userdata['logged_in']['UserID'], 
					$this->session->userdata['logged_in']['Username'], 
					$this->session->userdata['logged_in']['Email'], 
					'User Viewed Patient Details Page', $_SERVER['REMOTE_ADDR']
				);
				
				$patients = $this->patient->get_patient($id);
				$patient_details = array();
				
				foreach($patients as $key => $value) {
					$patient_details[$key] = $value;
					$patient_details[$key]->Birthday = $this->reformat->Birthday($patient_details[$key]->DOB);
					$patient_details[$key]->divid = $patient_details[$key]->AccountNumber;
					$patient_details[$key]->class = (($this->reformat->Age($patient_details[$key]->DOB) <= '13') ? 'pediatric' : 'adult');
					$patient_details[$key]->BG = $this->reformat->dateDiff(date('Y-m-d g:i:s'), $patient_details[$key]->NextBGAt);
				}
				
				$latestBG = $this->patient->get_recent_bg($id);
				
				if($type != 'iv') :
					$page_template = 'pages/patients/sc_details';
					$toolbar = 'patients/details/subq';
				else :
					$page_template = 'pages/patients/iv_details';
					$toolbar = 'patients/details/iv';
				endif;
				$data = array(
					'nav'	   => $this->my_nav,
					'toolbar'  => $this->toolbar->build_toolbar($toolbar, $patient_details[0]->LastName . ', ' . $patient_details[0]->FirstName),
					'patients' => $patient_details,
					'recent_bg' => $latestBG
				);
				
				//var_dump($patient_details);
				
				$this->load->view($page_template, $data);
			}else {
				redirect('user/login', 'refresh');	
			}
		}
		
		public function Start($type, $id, $convert = NULL) {
			$this->my_nav = $this->nav->main_nav("1", $this->user_level);
			$patient = $this->patient->get_hl7_patient($id);
			$patient_info = array();
			
			foreach($patient as $key => $value) {
				$patient_info[$key] = $value;
				$patient_info[$key]->Birthday = $this->reformat->Birthday($patient_info[$key]->DOB);
				
			}
			
			$data = array(
				'nav'	   => $this->my_nav,
				'toolbar'  => $this->toolbar->build_toolbar('patients/add'),
				'UserID' => $this->session->userdata['logged_in']['UserID'],
				'PatientHash' => $id,
				'convert' => $convert,
				'type' => $type,
				'patient' => $patient_info
			);

			$this->load->view('forms/start_patient', $data);
		}
		
		public function Iv_orderset() {
			
		}
		
	}