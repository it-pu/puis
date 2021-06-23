<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_quiz extends Admission_Controler {
    public $data = array();

    function __construct()
    {
        parent::__construct();
        $this->data['department'] = parent::__getDepartement();
        $this->data['module_url'] = base_url().'page/admission/quiz/c_quiz/';
        $this->data['module_url_question'] = base_url().'page/admission/quiz/c_question/';
        
    }

    public function index(){
      $content = $this->load->view('page/'.$this->data['department'].'/master/quiz/quiz',$this->data,true);
      $this->temp($content);
    }

    public function filter_ta(){
      $TA =  $this->db->get('db_admission.set_ta')->row()->Ta;
      $TA =  $TA + 1;
      $x = $TA - 3;
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

    public function load_quiz(){
      $dataArr = $this->getInputToken();

      $ID_q_quiz_schedule = $dataArr['ID_q_quiz_schedule'];
      $ID_q_quiz_category = $dataArr['ID_q_quiz_category'];

      $data = $this->db->query(
          '
            SELECT qd.QID, qd.Point FROM db_admission.q_quiz_details qd 
                                               LEFT JOIN db_admission.q_quiz q ON (q.ID = qd.QuizID)
                                               WHERE q.ID_q_quiz_schedule = "' . $ID_q_quiz_schedule . '" 
                                               and   q.ID_q_quiz_category = "'.$ID_q_quiz_category.'"
          '
      )->result_array();

      $Quiz = $this->db->get_where(
          'db_admission.q_quiz',
          array('ID_q_quiz_schedule' => $ID_q_quiz_schedule,'ID_q_quiz_category' => $ID_q_quiz_category)
      )->result_array();


      $dataStd = $this->db->query('SELECT COUNT(*) AS TotalAnswer FROM db_admission.q_quiz_students qqs 
                                              LEFT JOIN db_admission.q_quiz qq 
                                              ON (qqs.QuizID = qq.ID)
                                              WHERE qq.ID_q_quiz_schedule = "' . $ID_q_quiz_schedule . '" 
                                              and qq.ID_q_quiz_category = "'.$ID_q_quiz_category.'"
                                              ')->result_array();

      $result = array(
          'Quiz' => $Quiz,
          'Details' => $data,
          'TotalAnswer' => $dataStd[0]['TotalAnswer']
      );

      echo json_encode($result);


    }



}
