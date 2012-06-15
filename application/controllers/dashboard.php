<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Dashboard extends CI_Controller {
		
		var $bg;
		var $js;
		var $css;
		var $title;
		var $skin;
		var $data;
		var $template;
		
		public function __construct() {
			parent::__construct();
			//stylesheets and attributes for this controller
			//Skin name
			$this->skin = base_url() . 'assets/skin/v1/';
			//bg image can be different on everypage
			$this->bg = $this->skin . 'imgs/bg.jpg';
			//the css files can change from page to page, only use the ones needed.
			$this->css = array(
				array(
					'href' => base_url() . 'assets/default/reset.css',
					'media' => 'handheld,projector,tv,screen'
				),array(
					'href' => $this->skin . 'css/dashboard.css',
					'media' => 'projector,tv,screen'
				),
			);
			//js and attributes for this controller,
			//only load js files as needed.
			$this->js = array(
				array(
					'src' => 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'
				),
				array(
					'src' => 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js'
				),
			);
			//The default page title for this controller
			$this->title = 'AmpThis | Dashboard | Promote. Share. Connect.';
			
			$this->data = array (
				'page_title' => $this->title,
				'css' => $this->css,
				'js' => $this->js,
				'bg' => $this->bg
			);
			
		}
		
		public function Index() {
			$this->load->view('dashboard', $this->data);
		}
	}