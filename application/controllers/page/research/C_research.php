<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_research extends Research_Controler {
	public $data = array();
    function __construct()
    {
        parent::__construct();
    }

   public function portal_eksternal(){
   	// $page['department'] = parent::__getDepartement();
   	$content = $this->load->view('page/research/portal_eksternal/index',true);
   	$this->menu_portal_eksternal($content);
   }

}    