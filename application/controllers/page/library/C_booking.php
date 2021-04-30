<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_booking extends Library
{

    function __construct()
    {
        parent::__construct();
    }


    public function temp($content)
    {
        parent::template($content);
    }

    public function menu_booking($page)
    {
        $data['department'] = parent::__getDepartement();
        $data['page'] = $page;
        $content = $this->load->view('page/' . $data['department'] . '/booking/menu_booking', $data, true);
        $this->temp($content);
    }

    public function booking_list()
    {
        $data['departement'] = $this->__getDepartement();
        $page = $this->load->view('page/' . $data['departement'] . '/booking/booking_list', '', true);
        $this->menu_booking($page);
    }

    // public function final_project()
    // {
    //     $data['departement'] = $this->__getDepartement();
    //     $page = $this->load->view('page/'.$data['departement'].'/yudisium/final_project','',true);
    //     $this->menu_booking($page);
    // }

    // public function final_project_details($NPM)
    // {
    //     $data['departement'] = $this->__getDepartement();
    //     $data['NPM'] = $NPM;
    //     $page = $this->load->view('page/'.$data['departement'].'/yudisium/final_project_details',$data,true);
    //     $this->menu_booking($page);
    // }




}
