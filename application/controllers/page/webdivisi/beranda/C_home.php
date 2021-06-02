<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_home extends Webdivisi_Controler {
    public $data = array();

    function __construct()
    {
        parent::__construct();
        // load sambutan_model
        $this->load->model('webdivisi/beranda/m_home');
        $this->load->helper(array('form', 'url'));
    }


    function temp($content)
    {
        parent::template($content);
    }

    private function index($pageall)
    {
        $data['department'] = parent::__getDepartement();
        $data['pageall'] = $pageall;
        $content = $this->load->view('page/webdivisi/menu_navigation',$data,true);
        $this->temp($content);
    }

// ===== Slide ======
    function slide()
    {
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/webdivisi/beranda/V_slider',$data,true);
        $this->index($content);
    }
    

// ===== why ======

    private function menu_whyus($page){
        $data['department'] = parent::__getDepartement();
        $data['page'] = $page;
        $content = $this->load->view('page/webdivisi/beranda/whyus/menu_whyus',$data,true);
        $this->index($content);
    }
    
    public function about(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/webdivisi/beranda/whyus/about',$data,true);
        $this->menu_whyus($page);
    }
    public function excellence(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/webdivisi/beranda/whyus/excellence',$data,true);
        $this->menu_whyus($page);
    }
    public function graduate_profile(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/webdivisi/beranda/whyus/graduate_profile',$data,true);
        $this->menu_whyus($page);
    }
    public function career_opportunities(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/webdivisi/beranda/whyus/career_opportunities',$data,true);
        $this->menu_whyus($page);
    }

    private function menu_visimisi($pagevisimisi){
        $data['department'] = parent::__getDepartement();
        $data['pagevisimisi'] = $pagevisimisi;
        $content = $this->load->view('page/webdivisi/about/menu_visimisi',$data,true);
        $this->index($content);
    }
    
// ===== overview ======
    public function overview()
    {        
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/webdivisi/about/V_sambutan',$data,true);
        $this->index($content);
    }
    public function vision(){
        $data['department'] = parent::__getDepartement();
        $pagevisimisi = $this->load->view('page/webdivisi/about/V_vision',$data,true);
        $this->menu_visimisi($pagevisimisi);
    }
    public function mission(){
        $data['department'] = parent::__getDepartement();
        $pagevisimisi = $this->load->view('page/webdivisi/about/V_mission',$data,true);
        $this->menu_visimisi($pagevisimisi);
    }
    public function knowledge(){
        $data['department'] = parent::__getDepartement();
        $data['category'] = $this->m_home->get_category();
        $content = $this->load->view('page/webdivisi/about/V_knowledge',$data,true);
        $this->index($content);
    }
// ===== Call to Action ======
    function calltoaction()
    {
        
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/webdivisi/beranda/V_call',$data,true);
        $this->index($content);
    }
// ===== testimoni ======
    function testimoni()
    {
        
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/webdivisi/beranda/V_testimoni',$data,true);
        $this->index($content);
    }
// ===== Cliens ======
    function partner()
    {
        
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/webdivisi/beranda/V_partner',$data,true);
        $this->index($content);
    }
// ===== lecturer ======
    function lecturer()
    {
        
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/webdivisi/about/V_lecturer',$data,true);
        $this->index($content);
    }   
// ===== facilities ======
    function facilities()
    {
        
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/webdivisi/about/V_facilities',$data,true);
        $this->index($content);
    }  
    
    private function menu_contact($pagecontact){
            $data['department'] = parent::__getDepartement();
            $data['pagecontact'] = $pagecontact;
            $content = $this->load->view('page/webdivisi/contact/V_contact',$data,true);
            $this->index($content);
    }
    
    public function sosmed(){
        $data['department'] = parent::__getDepartement();
        $pagecontact = $this->load->view('page/webdivisi/contact/V_sosmed',$data,true);
        $this->menu_contact($pagecontact);
    }
    public function contact(){
        $data['department'] = parent::__getDepartement();
        $pagecontact = $this->load->view('page/webdivisi/contact/V_address',$data,true);
        $this->menu_contact($pagecontact);
    }

   
}

