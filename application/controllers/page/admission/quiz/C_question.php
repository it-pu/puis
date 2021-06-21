<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_question extends Admission_Controler {
    public $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_rest');
        $this->data['department'] = parent::__getDepartement();
        $this->data['module_url'] = base_url().'page/admission/quiz/c_question/';
        
    }

    public function index(){
        $NIP = $this->session->userdata('NIP');
        $dataQuiz = $this->db->query('SELECT COUNT(*) Total FROM db_admission.q_question 
                                        WHERE CreatedBy = "' . $NIP . '" ')->result_array();

        $this->data['Total'] = $dataQuiz[0]['Total'];
    	$content = $this->load->view('page/'.$this->data['department'].'/master/quiz/question',$this->data,true);
    	$this->temp($content);
    }

    public function save(){
        // just insert
        $data = $this->getInputToken();
        $data =  json_decode(json_encode($data),true);
        $data_arr = $data;
        $dataQustion = $data_arr['dataQustion'];
        // Insert question
        $dataQustion['SummernoteID'] = $data_arr['SummernoteID'];
        $dataQustion['CreatedBy'] = $data_arr['NIP'];
        $dataQustion['CreatedAt'] = $this->m_rest->getDateTimeNow();
        $dataQustion['UpdatedAt'] = $this->m_rest->getDateTimeNow();
        $this->db->insert('db_admission.q_question', $dataQustion);
        $QID = $this->db->insert_id();

        $this->m_rest
            ->checkImageSummernote('insert', $data_arr['SummernoteID'], 'db_admission.q_question', 'Question');


        // Option Process
        if ($dataQustion['QTID'] == 1 || $dataQustion['QTID'] == 2) {

            // Remove Option
            //                $this->db->where('QID',$ID);
            //                $this->db->delete('db_academic.q_question_options');
            //                $this->db->reset_query();

            $dataOption = (array) $data_arr['dataOption'];
            // Insert option
            if (count($dataOption) > 0) {
               for ($i = 0; $i < count($dataOption); $i++) {

                   $arrins = (array) $dataOption[$i];
                   $arrins['QID'] = $QID;
                   $this->db->insert('db_admission.q_question_options', $arrins);

                   $this->m_rest
                       ->checkImageSummernote(
                           'insert',
                           $arrins['SummernoteID'],
                           'db_admission.q_question_options',
                           'Option'
                       );
               }
            }
        }

        echo json_encode(1);
    }

    

    

}
