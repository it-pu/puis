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
        $this->load->model('m_rest');
        
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

    private function validation_quiz($data_arr){
      $bool = true;
      $msg = '';
      $callback_arr = [];
      $DurationType =  $data_arr['DurationType'];
      if ($DurationType == 'Flexi') {
        if (empty($data_arr['DurationFlexi'])) {
          $bool = false;
          $msg = 'DurationFlexi is empty';
        }
      }
      else
      {
        if (empty($data_arr['DurationFixedStart']) || empty($data_arr['DurationFixedEnd']) ) {
          $bool = false;
          $msg = 'DurationFixedStart or DurationFixedEnd is empty';
        }
        else
        {
          $c_Start = strtotime($data_arr['DurationFixedStart']);
          $c_End = strtotime($data_arr['DurationFixedEnd']);

          if ($c_Start >= $c_End) {
            $msg = 'time is invalid';
            $bool = false;
          }
        }
      }

      if ($bool) {
          if ($DurationType == 'Flexi') {
            unset($data_arr['DurationFixedStart']);
            unset($data_arr['DurationFixedEnd']);
          }
          else
          {
            unset($data_arr['DurationFlexi']);
          }
      }

      $callback_arr = $data_arr;

      return ['callback_arr' => $callback_arr,'msg' => $msg,'status' => $bool];

    }

    public function saveDataQuiz(){
      $data_arr = $this->getInputToken();
      // cek apakah ID Question exist atau tidak
      $dataForm = (array) $data_arr['dataForm'];

      $dataCheck_IDQ = true;
      if (count($dataForm) > 0) {
          for ($c = 0; $c < count($dataForm); $c++) {
              $dataCk = $this->db->select('ID')
                  ->get_where('db_admission.q_question', array('ID' => $dataForm[$c]->QID))->result_array();

              if (count($dataCk) <= 0) {
                  $dataCheck_IDQ = false;
              }
          }
      }

      $validation =  $this->validation_quiz($data_arr);
      if ($validation['status']) {
        if ($dataCheck_IDQ) {

            // cek total answer
            $dataFmQuiz = array(
                'NotesForStudents' => $data_arr['NotesForStudents']
            );

            // Cek apakah sudah pernah bikin quiz atau blm
            $ID_q_quiz_schedule = $data_arr['ID_q_quiz_schedule'];
            $ID_q_quiz_category = $data_arr['ID_q_quiz_category'];

            // Cek apakah ada student yang pernah ngisi atau engga
            $TotalAnswer = $this->db->query('SELECT COUNT(*) AS TotalAnswer FROM db_admission.q_quiz_students qqs 
                                            LEFT JOIN db_admission.q_quiz qq
                                            ON (qq.ID = qqs.QuizID)
                                            WHERE qq.ID_q_quiz_schedule = "' . $ID_q_quiz_schedule . '"
                                             AND qq.ID_q_quiz_category = "' . $ID_q_quiz_category . '" ')->result_array()[0]['TotalAnswer'];

            $dataCk = $this->db->select('ID')->get_where('db_admission.q_quiz', array(
                'ID_q_quiz_schedule' => $ID_q_quiz_schedule,
                'ID_q_quiz_category' => $ID_q_quiz_category
            ))->result_array();

            $QuizID = (count($dataCk) > 0) ? $dataCk[0]['ID'] : '';

            $dataFmQuiz['ID_q_quiz_category'] = $ID_q_quiz_category;
            $dataFmQuiz['ID_q_quiz_schedule'] = $ID_q_quiz_schedule;
            $dataFmQuiz['DurationType'] = $data_arr['DurationType'];

            if ($data_arr['DurationType'] == 'Flexi') {
               $dataFmQuiz['DurationFlexi'] = $data_arr['DurationFlexi'];
            }
            else
            {
              $dataFmQuiz['DurationFixedStart'] = $data_arr['DurationFixedStart'];
              $dataFmQuiz['DurationFixedEnd'] = $data_arr['DurationFixedEnd'];
            }

            if ($TotalAnswer <= 0) {

                if ($QuizID != '') {
                    // Update
                    $dataFmQuiz['UpdatedBy'] = $data_arr['NIP'];
                    $dataFmQuiz['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->where('ID', $QuizID);
                    $this->db->update('db_admission.q_quiz', $dataFmQuiz);
                    $this->db->reset_query();
                } else {
                    // Insert
                    $dataFmQuiz['CreatedBy'] = $data_arr['NIP'];
                    $dataFmQuiz['CreatedAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->insert('db_admission.q_quiz', $dataFmQuiz);
                    $QuizID = $this->db->insert_id();
                    $this->db->reset_query();
                }


                if (count($dataForm) > 0) {
                    $this->db->where('QuizID', $QuizID);
                    $this->db->delete('db_admission.q_quiz_details');
                    $this->db->reset_query();

                    for ($i = 0; $i < count($dataForm); $i++) {
                        $d = (array) $dataForm[$i];
                        $d['QuizID'] = $QuizID;
                        $this->db->insert('db_admission.q_quiz_details', $d);
                        $this->db->reset_query();
                    }
                }


                $result = array(
                    'Status' => 1,
                    'Message' => 'Data saved'
                );
            } else {
                $result = array(
                    'Status' => -1,
                    'Message' => 'Quiz cannot be edited',
                    'TotalAnswer' => $TotalAnswer
                );
            }
        } else {
            $result = array(
                'Status' => -2,
                'Message' => 'Question is outdated, please delete it immediately'
            );
        }
      }
      else
      {
        $result = array(
            'Status' => -3,
            'Message' => $validation['msg'],
        );
      }

      echo json_encode($result);

    }



}
