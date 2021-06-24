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
        $dataQuiz = $this->db->query('SELECT COUNT(*) Total FROM db_admission.q_question where active = 1')->result_array();

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

       //echo json_encode(1);
       echo json_encode(array('QID' => $QID));
    }

    public function getArrDataQuestion(){
        $data_arr = $this->getInputToken();
        $ArrQID = (array) $data_arr['ArrQID'];

        $result = [];

        if (count($ArrQID) > 0) {
            for ($i = 0; $i < count($ArrQID); $i++) {
                $QID = $ArrQID[$i];
                $dataQuestion = $this->db->query('SELECT q.ID,q.Question,q.Note, qt.Description AS Type, q.QTID FROM db_admission.q_question q 
                                                                LEFT JOIN db_academic.q_question_type qt ON (q.QTID = qt.ID)
                                                                WHERE q.ID = "' . $QID . '" ')->result_array();
                $dataOption = $this->db->select('Option,IsTheAnswer,Point')
                    ->get_where('db_admission.q_question_options', array('QID' => $QID))->result_array();
                $arrP = array(
                    'Question' => (count($dataQuestion) > 0) ? $dataQuestion[0] : [],
                    'Option' => $dataOption,
                    'Status' => (count($dataQuestion) > 0) ? 1 : 0,
                    'QID' => $QID
                );
                array_push($result, $arrP);
            }
        }

        echo json_encode($result);
    }

    public function countTotalMyQuestion(){
        $dataQuiz = $this->db->query('SELECT COUNT(*) Total FROM db_admission.q_question where active = 1')->result_array();
        echo json_encode(array('Total' => $dataQuiz[0]['Total']));
    }

    public function getMyQuestion(){
        $requestData = $_REQUEST;

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = 'AND ( q.Question LIKE "%' . $search . '%" )';
        }

        $queryDefault = 'SELECT q.*, qt.Description FROM db_admission.q_question q 
                                    LEFT JOIN db_academic.q_question_type qt ON (qt.ID = q.QTID)
                                    WHERE q.active = 1 ' . $dataSearch;

        $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';



        $sql = $queryDefault . ' ORDER BY q.UpdatedAt DESC , q.CreatedAt DESC LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $nestedData[] = '<div>' . $no . '</div>';
            $nestedData[] = '<div style="text-align: left;">' . $row['Question'] . '
                                <div>
                                    <span class="lbl-' . $row['QTID'] . '">' . $row['Description'] . '</span>
                                     <span class="label label-default" style="left: 0px;font-size: 11px;">Last modify : ' . date('d M Y H:i', strtotime($row['UpdatedAt'])) . '</span> 
                                </div>
                                </div>';
            $nestedData[] = '<div>
                            <div class="btn-group">
                              <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-edit"></i> <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu" style="left: -114px;">
                                <li><a href="javascript:void(0);" class="addToQuizFromMyQuestion" data-id="' . $row['ID'] . '">Add to quiz</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="hide"><a href="javascript:void(0);" class="editQuestion" data-tqid="' . $row['QTID'] . '" data-id="' . $row['ID'] . '">Edit</a></li>
                                <li><a href="javascript:void(0);" class="removeQuestion" data-tqid="' . $row['QTID'] . '" data-id="' . $row['ID'] . '">Remove</a></li>
                              </ul>
                            </div>
                            </div>';


            $no++;

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($queryDefaultRow),
            "recordsFiltered" => intval($queryDefaultRow),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    public function removeQuestion_old(){
        $data_arr =  $this->getInputToken();
        // cek apakah question sudah masuk kedalam kuis atau blm
        $data = $this->db->query('SELECT COUNT(*) AS Total 
                                    FROM db_admission.q_quiz_details 
                                    WHERE QID = "' . $data_arr['QID'] . '" ')
            ->result_array();

        if ($data[0]['Total'] <= 0) {
            // get data summernoteid
            $dataQuestion = $this->db->select('SummernoteID')->get_where(
                'db_admission.q_question',
                array('ID' => $data_arr['QID'])
            )->result_array();

            if (count($dataQuestion) > 0) {

                if (
                    $dataQuestion[0]['SummernoteID'] != ''
                    && $dataQuestion[0]['SummernoteID'] != null
                ) {
                    $this->m_rest
                        ->checkImageSummernote('delete', $dataQuestion[0]['SummernoteID'], '', '');

                    $this->db->where('ID', $data_arr['QID']);
                    $this->db->delete('db_admission.q_question');
                    $this->db->reset_query();


                    $dataOption = $this->db->select('SummernoteID')
                        ->get_where(
                            'db_admission.q_question_options',
                            array('QID' => $data_arr['QID'])
                        )->result_array();

                    if (count($dataOption) > 0) {
                        for ($opt = 0; $opt < count($dataOption); $opt++) {
                            $this->m_rest
                                ->checkImageSummernote('delete', $dataOption[$opt]['SummernoteID'], '', '');
                        }
                        $this->db->where('QID', $data_arr['QID']);
                        $this->db->delete('db_admission.q_question_options');
                        $this->db->reset_query();
                    }
                }
            }
        }

        return print_r(json_encode(array('Usage' => $data[0]['Total'])));
    }

    public function removeQuestion(){
        $data_arr =  $this->getInputToken();
        // cek apakah question sudah masuk kedalam kuis atau blm
        $data = $this->db->query('SELECT COUNT(*) AS Total 
                                    FROM db_admission.q_quiz_details 
                                    WHERE QID = "' . $data_arr['QID'] . '" ')
            ->result_array();

        if ($data[0]['Total'] <= 0) {
            // get data summernoteid
            $dataQuestion = $this->db->select('SummernoteID')->get_where(
                'db_admission.q_question',
                array('ID' => $data_arr['QID'])
            )->result_array();

            if (count($dataQuestion) > 0) {

                // check id in use in quiz details
                $d = $this->db->select('ID')->where('QID',$data_arr['QID'])->from('db_admission.q_quiz_details')->count_all_results();

                if ($d > 0) {
                    $this->db->where('ID',$data_arr['QID']);
                    $this->db->update('db_admission.q_question',['active' => 0]);
                }
                else
                {
                    if (
                        $dataQuestion[0]['SummernoteID'] != ''
                        && $dataQuestion[0]['SummernoteID'] != null
                    ) {
                        $this->m_rest
                            ->checkImageSummernote('delete', $dataQuestion[0]['SummernoteID'], '', '');

                        $this->db->where('ID', $data_arr['QID']);
                        $this->db->delete('db_admission.q_question');
                        $this->db->reset_query();


                        $dataOption = $this->db->select('SummernoteID')
                            ->get_where(
                                'db_admission.q_question_options',
                                array('QID' => $data_arr['QID'])
                            )->result_array();

                        if (count($dataOption) > 0) {
                            for ($opt = 0; $opt < count($dataOption); $opt++) {
                                $this->m_rest
                                    ->checkImageSummernote('delete', $dataOption[$opt]['SummernoteID'], '', '');
                            }
                            $this->db->where('QID', $data_arr['QID']);
                            $this->db->delete('db_admission.q_question_options');
                            $this->db->reset_query();
                        }
                    }
                }

            }
        }

        return print_r(json_encode(array('Usage' => $data[0]['Total'])));
    }

}
