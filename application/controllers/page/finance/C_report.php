<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_report extends Finnance_Controler {

    private $data = array();

    function __construct()
    {
        parent::__construct();
        $this->data['department'] = parent::__getDepartement();
        $this->load->model('finance/m_finance');
        $this->load->model('m_sendemail');
        $this->load->model('admission/m_admission');
        $this->load->model('master/m_master');
    }


    public function temp($content)
    {
        parent::template($content);
    }

    public function reportTagihanMHS()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_report',$this->data,true);
        $this->temp($content);
    }

    public function get_reportingTagihanMHS($page = null)
    {
        $input = $this->getInputToken();
        $this->load->library('pagination');
        $sqlCount = 'show databases like "%ta_2%"';
        $queryCount=$this->db->query($sqlCount, array())->result_array();
        $total = count($queryCount);
        if ($input['ta'] != '' || $input['ta'] != null || $input['NIM'] != '') {
            $total = 1;
        }
        $StatusMHS = $input['StatusMHS'];

        $config = $this->config_pagination_default_ajax($total,1,3);
        $this->pagination->initialize($config);
        $page = $this->uri->segment(3);
        $start = ($page - 1) * $config["per_page"];
        $data = $this->m_finance->get_report_pembayaran_mhs2($input['ta'],$input['prodi'],$input['NIM'],$input['Semester'],$input['Status'],$config["per_page"], $start,$StatusMHS);
        $output = array(
        'pagination_link'  => $this->pagination->create_links(),
        'loadtable'   => $data,
        );
        echo json_encode($output);
    }

    public function page_report_admission()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/report_admission/page_report',$this->data,true);
        $this->temp($content);
    }

    public function report_admission($page)
    {
        $arr_result = array('html' => '','jsonPass' => '');
        $uri = $page;
        $content = $this->load->view('page/'.$this->data['department'].'/report_admission/'.$uri,$this->data,true);
        $arr_result['html'] = $content;
        echo json_encode($arr_result);
    }

    public function report_get($page)
    {
        $arr_result = array('html' => '','jsonPass' => '');
        $uri = $page;
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/'.$uri,$this->data,true);
        $arr_result['html'] = $content;
        echo json_encode($arr_result);
    }

}
