<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_quiz extends Admission_Controler {
    public $data = array();

    function __construct()
    {
        parent::__construct();
        $this->data['department'] = parent::__getDepartement();
        $this->data['module_url'] = base_url().'page/admission/quiz/c_quiz/';
        
    }

    public function index(){
      $content = $this->load->view('page/'.$this->data['department'].'/master/quiz/quiz',$this->data,true);
      $this->temp($content);
    }

    public function filter_ta(){
      $TA =  $this->db->get('db_admission.set_ta')->row()->Ta;
      $x = $TA - 1;
      $arr = [];
      for ($i=$TA; $i >=$x ; $i--) { 
        $arr[] = $i;
      }

      echo json_encode($arr);

    }

    public function filterCategory(){
      $get =  $this->db->get('db_admission.q_quiz_category')->result();
      echo json_encode($get);
    }

    public function filterQuizSchedule($TA){
      if (!isset($TA)) {
        die('No Parameter TA');
      }

      $get = $this->db->where('TA',$TA)->get('db_admission.q_quiz_schedule')->result();

      echo json_encode($get);

    }

    public function save_schedule(){
      $rs = ['status' => 1,'msg' => ''];
      $data =  $this->getInputToken();
      $DateStart = $data['DateStart'];
      $DateEnd = $data['DateEnd'];
      $TA = $data['TA'];

      if (strtotime($DateStart) > strtotime($DateEnd)) {
        $rs['status'] = 0;
        $rs['msg'] = 'Date Start and Date End Invalid';
        echo json_encode($rs);
        die();
      }

      $this->db->insert('db_admission.q_quiz_schedule',$data);
      echo json_encode($rs);


    }



}
