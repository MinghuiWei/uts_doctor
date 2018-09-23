<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');

	}

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->library('parser');
		$data = array(
			'blog_title' => 'My Blog Title',
			'blog_heading' => 'My Blog Heading'
		);
		$_SESSION['username'] = 'lee';

		$user = $this->user_model->login_user("user1@uts.com2", "user1");

		$this->my_smarty->assign($data);
		$this->my_smarty->view('home');
	}
}
