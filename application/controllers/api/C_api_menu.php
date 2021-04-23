<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_api_menu extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('m_search');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('akademik/m_onlineclass', 'm_oc');
        $this->load->model('master/m_master');
        $this->load->library('JWT');
        $this->load->library('google');

        date_default_timezone_set("Asia/Jakarta");
        setlocale(LC_ALL, 'id_ID.UTF8', 'id_ID.UTF-8', 'id_ID.8859-1', 'id_ID', 'IND.UTF8', 'IND.UTF-8', 'IND.8859-1', 'IND', 'Indonesian.UTF8', 'Indonesian.UTF-8', 'Indonesian.8859-1', 'Indonesian', 'Indonesia', 'id', 'ID', 'en_US.UTF8', 'en_US.UTF-8', 'en_US.8859-1', 'en_US', 'American', 'ENG', 'English');
    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "s3Cr3T-G4N";
        $data_arr = (array) $this->jwt->decode($token, $key);
        return $data_arr;
    }

    private function getInputToken2()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);
        return $data_arr;
    }

    public function crudShareMenu()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'getShareMenu') {

            $data = $this->db->get('db_it.sm_menu')->result_array();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['Child'] = $this->db->get_where(
                        'db_it.sm_menu_details',
                        array('IDSM' => $data[$i]['ID'])
                    )->result_array();
                }
            }

            return print_r(json_encode($data));
        }
    }


    public function crudSurvey()
    {
        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'getSurvQuestionType') {
            $data = $this->db->get_where('db_it.surv_question_type', array('IsActive' => '1'))->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateQuestionCategory') {
            $ID = $data_arr['ID'];
            $dataForm = array(
                'Description' => $data_arr['Description'],
                'DepartmentID' => $data_arr['DepartmentID']
            );

            if ($ID != '') {
                // Update
                $dataForm['UpdatedBy'] = $data_arr['NIP'];
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $ID);
                $this->db->update('db_it.surv_question_category', $dataForm);
            } else {
                // Insert
                $dataForm['CreatedBy'] = $data_arr['NIP'];
                $this->db->insert('db_it.surv_question_category', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'getQuestionCategory') {

            $data = $this->db->order_by('ID', 'DESC')->get_where('db_it.surv_question_category', array(
                'DepartmentID' => $data_arr['DepartmentID']
            ))->result_array();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['Link'] = $this->db
                        ->query('SELECT COUNT(*) AS Total 
                                    FROM db_it.surv_question 
                                    WHERE QCID = "' . $data[$i]['ID'] . '" ')
                        ->result_array()[0]['Total'];
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removeQuestionCategory') {

            $dataCk = $this->db
                ->query('SELECT COUNT(*) AS Total 
                                    FROM db_it.surv_question 
                                    WHERE QCID = "' . $data_arr['ID'] . '" ')
                ->result_array()[0]['Total'];

            $result = array('Status' => 0);

            if ($dataCk <= 0) {
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_it.surv_question_category');
                $result = array('Status' => 1);
            }


            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'readDataQuestion') {
            $ID = $data_arr['ID'];
            $data = $this->db->get_where(
                'db_it.surv_question',
                array('ID' => $ID)
            )->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getBankQuestion') {

            $requestData = $_REQUEST;

            $Type = $data_arr['Type'];
            $QuestionCategory = $data_arr['QuestionCategory'];

            $dataWhere = '';
            if ($Type != '' || $QuestionCategory != '') {
                $w_Type = ($Type != '')
                    ? 'AND sq.QTID = "' . $Type . '" ' : '';
                $w_QuestionCategory = ($QuestionCategory != '')
                    ? 'AND sq.QCID = "' . $QuestionCategory . '" ' : '';

                $dataWhere = $w_Type . $w_QuestionCategory;
            }

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataScr = 'sq.Question LIKE "%' . $search . '%"';

                $dataSearch = ' AND (' . $dataScr . ')';
            }

            $queryDefault = 'SELECT sq.ID, sq.Question, sq.IsRequired, sq.AnswerType,  
                                             sqc.Description AS QuestionCategory, 
                                             sqt.Description AS QuestionType FROM db_it.surv_question sq
                                            LEFT JOIN db_it.surv_question_category sqc ON (sqc.ID = sq.QCID)
                                            LEFT JOIN db_it.surv_question_type sqt ON (sqt.ID = sq.QTID) 
                                            WHERE sq.DepartmentID = "' . $data_arr['DepartmentID'] . '" 
                                            ' . $dataWhere . $dataSearch;

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];


                // Total link
                $dataTotalLink = $this->db->query('SELECT COUNT(*) AS Total 
                                            FROM db_it.surv_survey_detail 
                                            WHERE QuestionID = "' . $row['ID'] . '" ')
                    ->result_array()[0]['Total'];

                $tokenID = $this->jwt->encode(array('ID' => $row['ID']), 'UAP)(*');

                $btnAct = '<div class="btn-group">
                          <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-pencil"></i> <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a href="' . base_url('survey/create-question?tkn=' . $tokenID) . '" target="_blank">Edit</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="javascript:void(0)" data-id="' . $row['ID'] . '" class="removeThisQuestion">Remove</a></li>
                          </ul>
                        </div>';

                $showBtnAct = ($dataTotalLink <= 0)
                    ? $btnAct : '-';


                $isRequied = ($row['IsRequired'] == '1')
                    ? '<span class="label label-danger">Required</span>'
                    : '<span class="label label-warning">Optional</span>';

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align: left;">' . $row['Question'] . '</div>';
                $nestedData[] = '<a href="javascript:void(0);" 
                                    class="showLinkSurvey" data-id="' . $row['ID'] . '">' . $dataTotalLink . '</a>';
                $nestedData[] = $showBtnAct;
                $nestedData[] = '<div>' . $row['QuestionCategory'] . '<br/>
                                    <span class="label label-success">' . $row['QuestionType'] . '</span>
                                    ' . $isRequied . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval($queryDefaultRow),
                "recordsFiltered" => intval($queryDefaultRow),
                "data"            => $data,
                "dataQuery"            => $query
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'removeQuestion') {

            $QuestionID = $data_arr['ID'];

            // Cek apakah pertanyaan di gunakan dalam survey ?
            $dataCk = $this->db->query('SELECT COUNT(*) AS Total 
                                            FROM db_it.surv_survey_detail 
                                            WHERE QuestionID = "' . $QuestionID . '" ')
                ->result_array()[0]['Total'];

            $Status = 0;

            if ($dataCk <= 0) {

                $dataQuestion = $this->db->query('SELECT sq.SummernoteID FROM db_it.surv_question sq 
                                        WHERE sq.ID = "' . $QuestionID . '" ')
                    ->result_array();

                if (count($dataQuestion) > 0) {
                    for ($i = 0; $i < count($dataQuestion); $i++) {

                        $SummernoteID = $dataQuestion[$i]['SummernoteID'];

                        $this->m_rest
                            ->checkImageSummernote(
                                'delete',
                                $SummernoteID,
                                'db_it.surv_question',
                                'Question'
                            );
                    }
                }

                $this->db->where('ID', $QuestionID);
                $this->db->delete('db_it.surv_question');
                $Status = 1;
            }

            return print_r(json_encode(array('Status' => $Status)));
        } else if ($data_arr['action'] == 'showLinkQuestion') {

            $QuestionID = $data_arr['ID'];

            // Cek apakah pertanyaan di gunakan dalam survey ?
            $data = $this->db->query('SELECT ss.Title, ss.StartDate, ss.EndDate, ss.Status  
                                            FROM db_it.surv_survey_detail ssd
                                            LEFT JOIN db_it.surv_survey ss ON (ss.ID = ssd.SurveyID) 
                                            WHERE ssd.QuestionID = "' . $QuestionID . '" ')
                ->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getMasterQuestion') {

            $requestData = $_REQUEST;

            $Type = $data_arr['Type'];
            $QuestionCategory = $data_arr['QuestionCategory'];

            $dataWhere = '';
            if ($Type != '' || $QuestionCategory != '') {
                $w_Type = ($Type != '')
                    ? 'AND sq.QTID = "' . $Type . '" ' : '';
                $w_QuestionCategory = ($QuestionCategory != '')
                    ? 'AND sq.QCID = "' . $QuestionCategory . '" ' : '';

                $dataWhere = $w_Type . $w_QuestionCategory;
            }

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataScr = 'sq.Question LIKE "%' . $search . '%"';

                $dataSearch = ' AND (' . $dataScr . ')';
            }

            $queryDefault = 'SELECT sq.ID, sq.Question, sq.IsRequired, sq.AnswerType,  
                                             sqc.Description AS QuestionCategory, 
                                             sqt.Description AS QuestionType FROM db_it.surv_question sq
                                            LEFT JOIN db_it.surv_question_category sqc ON (sqc.ID = sq.QCID)
                                            LEFT JOIN db_it.surv_question_type sqt ON (sqt.ID = sq.QTID)
                                            WHERE sq.DepartmentID = "' . $data_arr['DepartmentID'] . '" 
                                            ' . $dataWhere . $dataSearch;

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $btnAct = '<div style="margin-bottom: 10px;">
                                <button class="btn btn-info btn-sm btnAddToSurvey" data-id="' . $row['ID'] . '"><i class="fa fa-arrow-left margin-right"></i> Add to survey</button></div>';

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align: left;">' . $btnAct . $row['Question'] . '</div>';
                $nestedData[] = '<div>' . $row['QuestionCategory'] . '<br/>
                                    <span class="label label-success">' . $row['QuestionType'] . '</span></div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval($queryDefaultRow),
                "recordsFiltered" => intval($queryDefaultRow),
                "data"            => $data,
                "dataQuery"            => $query
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'addQuestionToSurvey') {

            $dataInsrt = array(
                'SurveyID' => $data_arr['SurveyID'],
                'QuestionID' => $data_arr['QuestionID']
            );

            // Cek apakah sudah ada atau blm
            $dataCk = $this->db->get_where('db_it.surv_survey_detail', $dataInsrt)->result_array();

            $result = array('Status' => 0);

            if (count($dataCk) <= 0) {
                // Dapetin urutan
                $TotalQuestion = $this->db->query('SELECT COUNT(*) AS Total 
                                        FROM db_it.surv_survey_detail 
                                        WHERE SurveyID = "' . $data_arr['SurveyID'] . '" ')
                    ->result_array()[0]['Total'];

                $dataInsrt['Queue'] = $TotalQuestion + 1;
                $this->db->insert('db_it.surv_survey_detail', $dataInsrt);
                $result = array('Status' => 1);
            }

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'QuestionInMySurvey') {

            $SurveyID = $data_arr['SurveyID'];

            $data = $this->db->query('SELECT ssd.ID, sq.ID AS QuestionID , sq.Question,   
                                            sqc.Description AS Category, ssd.Queue
                                            FROM db_it.surv_survey_detail ssd
                                            LEFT JOIN db_it.surv_question sq ON (sq.ID = ssd.QuestionID)
                                            LEFT JOIN db_it.surv_question_category sqc ON (sqc.ID = sq.QCID)
                                            WHERE ssd.SurveyID = "' . $SurveyID . '" ORDER BY ssd.Queue ASC')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removeQUestionFromSurvey') {

            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_it.surv_survey_detail');

            return print_r(1);
        } else if ($data_arr['action'] == 'updateQueueQuestion') {
            $this->db->where('ID', $data_arr['ID']);
            $this->db->update(
                'db_it.surv_survey_detail',
                array('Queue' => $data_arr['Queue'])
            );
            return print_r(1);
        } else if ($data_arr['action'] == 'updateDataQuestion') {

            $ID = $data_arr['ID'];
            $dataQuestion = (array) $data_arr['dataQuestion'];

            if ($ID != '') {
                // Update
                // Cek apakah pertanyaan sudah di assign ke dalam survey atau blm
                $dataCk = $this->db->query('SELECT COUNT(*) AS Total 
                                            FROM db_it.surv_survey_detail 
                                            WHERE QuestionID = "' . $ID . '" ')
                    ->result_array()[0]['Total'];

                if ($dataCk <= 0) {
                    $dataQuestion['UpdatedBy'] = $data_arr['NIP'];
                    $dataQuestion['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->where('ID', $ID);
                    $this->db->update('db_it.surv_question', $dataQuestion);
                    $Status = 1;
                } else {
                    $Status = 0;
                }
            } else {
                // Insert
                $dataQuestion['CreatedBy'] = $data_arr['NIP'];
                $this->db->insert('db_it.surv_question', $dataQuestion);

                $Status = 1;
            }

            if ($Status == 1 || $Status == '1') {

                $SummernoteID = $dataQuestion['SummernoteID'];
                // Cek image in summernote
                $this->m_rest
                    ->checkImageSummernote('insert', $SummernoteID, 'db_it.surv_question', 'Question');
            }

            return print_r(json_encode(array('Status' => $Status)));
        } else if ($data_arr['action'] == 'updateSurvey') {

            $ID = $data_arr['ID'];
            $dataSurvey = (array) $data_arr['dataSurvey'];

            if ($ID != '') {
                // Update
                $dataSurvey['UpdatedBy'] = $data_arr['NIP'];
                $dataSurvey['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $ID);
                $this->db->update('db_it.surv_survey', $dataSurvey);
            } else {
                // Insert
                $dataSurvey['CreatedBy'] = $data_arr['NIP'];
                $this->db->insert('db_it.surv_survey', $dataSurvey);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'getListSurvey') {

            $requestData = $_REQUEST;

            $dataWhere = '';

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' AND ( ss.Title LIKE "%' . $search . '%" OR ss.Key LIKE "%' . $search . '%" )';
            }

            $queryDefault = 'SELECT ss.* FROM db_it.surv_survey ss WHERE 
                            ss.DepartmentID = "' . $data_arr['DepartmentID'] . '"  ' .
                $dataWhere . $dataSearch;

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $Range = date('d M Y', strtotime($row['StartDate'])) . ' - ' .
                    date('d M Y', strtotime($row['EndDate']));

                $dateNow = date("Y-m-d");
                $dateExpired = $row['EndDate'];

                if ($dateNow >= $dateExpired) {
                    $updates = array(
                        'isPublicSurvey' => '0',

                    );

                    $this->db->where('ID', $row['ID']);
                    $this->db->update('db_it.surv_survey', $updates);

                    $updates = array(
                        'SharePublicStat' => '0'
                    );

                    $this->db->where('SurveyID', $row['ID']);
                    $this->db->update('db_it.surv_survey_detail', $updates);
                }
                $Status = '<span class="label label-warning">Unpublish</span>';
                $btnClose = 'hide';
                $btnPublish = '';
                $btnRemove = '';
                if ($row['Status'] == '1') {
                    $Status = '<span class="label label-success">Publish</span>';
                    $btnClose = '';
                    $btnPublish = 'hide';
                    $btnRemove = 'hide';
                } else if ($row['Status'] == '2') {
                    $Status = '<span class="label label-danger">Close</span>';
                    $btnClose = 'hide';
                    $btnPublish = 'hide';
                    $btnRemove = 'hide';
                }

                $tokenBtn = $this->jwt->encode(array('ID' => $row['ID']), "UAP)(*");

                $showBtnAddNewDate = ($row['Status'] == '2')
                    ? '<li class="" id="li_btn_Close_' . $row['ID'] . '">
                                        <a href="javascript:void(0);" class="btnAddNewDate" data-id="' . $row['ID'] . '">Add new date 
                                            <i style="color: #FFC107;padding-top: 3px;" class="fa fa-circle pull-right"></i></a>
                                 </li>'
                    : '';

                $btnAct = '<div class="btn-group">
                              <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-pencil"></i> <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu">
                                <li class="' . $btnPublish . '" id="li_btn_Publish_' . $row['ID'] . '">
                                        <a href="javascript:void(0);" class="btnPublishSurvey" data-id="' . $row['ID'] . '">Publish</a>
                                        </li>
                                <li class="' . $btnClose . '" id="li_btn_Close_' . $row['ID'] . '">
                                        <a href="javascript:void(0);" class="btnCloseSurvey" data-id="' . $row['ID'] . '" style="color: red;">Close</a>
                                 </li>
                                 ' . $showBtnAddNewDate . '
                                <li role="separator" class="divider"></li>
                                <li><a href="javascript:void(0);" class="btnEditSurvey" data-id="' . $row['ID'] . '">View Survey</a></li>
                                <li><a href="javascript:void(0);" class="btnManageTarget" data-id="' . $row['ID'] . '">Manage Targets</a></li>
                                <li><a href="' . base_url('survey/manage-question/' . $tokenBtn) . '" target="_blank">Manage Question</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="javascript:void(0);" class="btnShareToPublic" data-id="' . $row['ID'] . '">Share to the public</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="' . $btnRemove . '"><a href="javascript:void(0);">Remove</a></li>
                              </ul>
                            </div>';

                // Cek jumlah yang sudah mengisi survey

                // ketika survey blm close dan sudah close

                $whereAnswerSurvey = ($row['Status'] == '2') ? ' AND RecapID = (SELECT MAX(RecapID) FROM db_it.surv_answer WHERE SurveyID= "' . $row['ID'] . '")'
                    : ' AND Status = "1" ';
                $TotalYgUdahIsiSurvey = $this->db->query('SELECT COUNT(*) AS Total FROM db_it.surv_answer 
                                                                    WHERE SurveyID= "' . $row['ID'] . '" AND 
                                                                      FormType = "internal" ' . $whereAnswerSurvey)->result_array()[0]['Total'];

                //                $TotalYgUdahIsiSurvey = $this->db->from('db_it.surv_answer')
                //                    ->where(array('SurveyID' => $row['ID'],
                //                        'FormType' => 'internal',
                //                        'Status' => '1'))->count_all_results();
                $btnTotalAlreadyFillOut = ($TotalYgUdahIsiSurvey > 0)
                    ? '<a href="javascript:void(0)" class="showAlreadyFillOut" data-type="internal" data-status="' . $row['Status'] . '" data-id="' . $row['ID'] . '">' . $TotalYgUdahIsiSurvey . '</a>'
                    : '0';

                // Cek jumlah yang sudah mengisi survey external
                $TotalYgUdahIsiSurvey_Ext = $this->db->query('SELECT COUNT(*) AS Total FROM db_it.surv_answer 
                                                                    WHERE SurveyID= "' . $row['ID'] . '" AND 
                                                                      FormType = "external" ' . $whereAnswerSurvey)->result_array()[0]['Total'];
                //                $TotalYgUdahIsiSurvey_Ext = $this->db->from('db_it.surv_answer')
                //                    ->where(array('SurveyID' => $row['ID'],
                //                        'FormType' => 'external',
                //                        'Status' => '1'))->count_all_results();
                $btnTotalAlreadyFillOut_ext = ($TotalYgUdahIsiSurvey_Ext > 0)
                    ? '<a href="javascript:void(0)" class="showAlreadyFillOut" data-type="external" data-status="' . $row['Status'] . '" data-id="' . $row['ID'] . '">' . $TotalYgUdahIsiSurvey_Ext . '</a>'
                    : '0';

                $TotalQuestion = $this->db->from('db_it.surv_survey_detail')->where('SurveyID', $row['ID'])->count_all_results();
                $btnShowTotalQuestion = ($TotalQuestion > 0) ? '<a href="javascript:void(0)" class="showQuestionList" data-id="' . $row['ID'] . '">' . $TotalQuestion . '</a>' : '0';

                $TotalFillOut = $TotalYgUdahIsiSurvey + $TotalYgUdahIsiSurvey_Ext;
                $ShowTotalFillOut = ($TotalFillOut > 0)
                    ? '<a href="javascript:void(0)" class="showAlreadyFillOut" data-status="' . $row['Status'] . '" data-type="all" data-id="' . $row['ID'] . '">' . $TotalFillOut . '</a>'
                    : '0';

                $TotalRecap = $this->db->from('db_it.surv_recap')
                    ->where('SurveyID', $row['ID'])
                    ->count_all_results();

                $StatusPublicationNow = ($row['Status'] == '1')
                    ? $TotalRecap + 1 : $TotalRecap;

                $TotalPublication = (($StatusPublicationNow) > 0)
                    ? '<a href="javascript:void(0)" class="showAllPublication" data-id="' . $row['ID'] . '">' . $StatusPublicationNow . '</a>' : 0;

                $Key = ($row['Key'] != '' && $row['Key'] != null) ? '<div style="margin-top: 10px;"><span class="key">' . $row['Key'] . '</span></div>' : '';

                // <i class="fa fa-check-square-o" aria-hidden="true"></i>
                $useTimer = ($row['useTimer'] == '1')
                    ? '<i class="fa fa-check-square-o" style="color:green;" aria-hidden="true"></i>' : '-';

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align: left;"><b>' . $row['Title'] . '</b>' . $Key . '</div>';
                $nestedData[] = $useTimer;
                $nestedData[] = $btnShowTotalQuestion;
                $nestedData[] = $btnTotalAlreadyFillOut;
                $nestedData[] = $btnTotalAlreadyFillOut_ext;
                $nestedData[] = '<b>' . $ShowTotalFillOut . '</b>';
                $nestedData[] = '<div>' . $btnAct . '</div>';
                $nestedData[] = '<div>' . $Range . '</div>';
                $nestedData[] = $TotalPublication;
                $nestedData[] = '<div id="viewStatusSurvey_' . $row['ID'] . '">' . $Status . '</div>';

                $data[] = $nestedData;
                $no++;
            }


            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval($queryDefaultRow),
                "recordsFiltered" => intval($queryDefaultRow),
                "data"            => $data,
                "dataQuery"            => $query
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'dataAllPublication') {

            // Get on recap
            $SurveyID = $data_arr['SurveyID'];

            $dataRecap = $this->db->query('SELECT sr.ID AS RecapID, 
                                                sr.SurveyID, sr.StartDate, sr.EndDate 
                                                    FROM db_it.surv_recap sr
                                                    WHERE sr.SurveyID = "' . $SurveyID . '"')
                ->result_array();

            if (count($dataRecap) > 0) {
                for ($i = 0; $i < count($dataRecap); $i++) {
                    $dataRecap[$i]['Question'] = $this->db->from('db_it.surv_recap_question')
                        ->where(array(
                            'SurveyID' => $SurveyID,
                            'RecapID' => $dataRecap[$i]['RecapID']
                        ))
                        ->count_all_results();

                    $dataRecap[$i]['TotalAnswer'] = $this->db->from('db_it.surv_answer')
                        ->where(array(
                            'SurveyID' => $SurveyID,
                            'RecapID' => $dataRecap[$i]['RecapID']
                        ))
                        ->count_all_results();

                    $dataRecap[$i]['Status'] = "2";
                }
            }

            // get yg aktif sekarang
            $dataSurvey = $this->db->select('ID,StartDate,EndDate')->get_where(
                'db_it.surv_survey',
                array('ID' => $SurveyID, 'Status' => '1')
            )->result_array();

            if (count($dataSurvey) > 0) {
                for ($i = 0; $i < count($dataSurvey); $i++) {
                    $arrPush = array(
                        'RecapID' => 0,
                        'SurveyID' => $dataSurvey[$i]['ID'],
                        'StartDate' => $dataSurvey[$i]['StartDate'],
                        'EndDate' => $dataSurvey[$i]['EndDate'],
                        'Question' => $this->db->from('db_it.surv_survey_detail')
                            ->where(array('SurveyID' => $SurveyID))
                            ->count_all_results(),
                        'TotalAnswer' => $this->db->from('db_it.surv_answer')
                            ->where(array(
                                'SurveyID' => $SurveyID,
                                'RecapID' => 0
                            ))
                            ->count_all_results(),
                        'Status' => "1",
                    );

                    array_push($dataRecap, $arrPush);
                }
            }

            return print_r(json_encode($dataRecap));
        } else if ($data_arr['action'] == 'shareRecap2email') {

            $this->load->model('m_sendemail');

            $to = $data_arr['Email'];
            //        $to = 'nndg.ace3@gmail.com';


            // Get survey title
            $dataSurv = (array) $this->jwt->decode($data_arr['tokenRecap'], 'UAP)(*');
            $dataSurvey = $this->db->get_where(
                'db_it.surv_survey',
                array('ID' => $dataSurv['SurveyID'])
            )->result_array();
            $dataPublishDate = $this->db->get_where(
                'db_it.surv_recap',
                array('ID' => $dataSurv['RecapID'])
            )
                ->result_array();


            // http://localhost/puis/save2excel/survey/1/1

            $PublicationDate = date('d M Y', strtotime($dataPublishDate[0]['StartDate'])) . ' - ' .
                date('d M Y', strtotime($dataPublishDate[0]['EndDate']));

            $subject = 'Recap - ' . $dataSurvey[0]['Title'] . ' | ' . $PublicationDate;

            $text = 'Dear <strong style="color: blue;">' . $data_arr['Name'] . '</strong>,

                <p style="color: #673AB7;">Recap survey <strong>"' . $dataSurvey[0]['Title'] . '"</strong>
                <br/> Publication Date :  ' . $PublicationDate . '</p>

                <table width="178" cellspacing="0" cellpadding="12" border="0">
                    <tbody>
                    <tr>
                        <td bgcolor="#4caf50" align="center">
                            <a href="' . base_url('save2excel/survey/' . $data_arr['tokenRecap']) . '" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color:#4caf50" target="_blank" >Download Survey Result</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br/>

                <p>Send by : ' . $data_arr['SentBy'] . ' | ' . date('d M Y H:i', strtotime($data_arr['SentAt'])) . '</p>';

            $this->m_sendemail->sendEmail($to, $subject, null, null, null, null, $text, null, 'Survey Result');

            // surv_share_with_email
            $arrIns = array(
                'SurveyID' => $dataSurv['SurveyID'],
                'RecapID' => $dataSurv['RecapID'],
                'Name' => $data_arr['Name'],
                'Email' => $data_arr['Email'],
                'EntredBy' => $data_arr['NIP']
            );

            $this->db->insert('db_it.surv_share_with_email', $arrIns);

            return print_r(json_encode($arrIns));
        } else if ($data_arr['action'] == 'getListHistorySendEmail') {

            $data = $this->db->query('SELECT swe.*, em.Name AS EntredByName FROM db_it.surv_share_with_email swe 
                                            LEFT JOIN db_employees.employees em 
                                            ON (swe.EntredBy = em.NIP)
                                            WHERE swe.SurveyID = "' . $data_arr['SurveyID'] . '"
                                             AND swe.RecapID = "' . $data_arr['RecapID'] . '"')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'setPublicSurvey') {
            $ID = $data_arr['SurveyID'];
            $this->load->library('Qrcode/qrlib');



            // Cek apakah sudah mempunyai key atau blm
            $dataCk = $this->db->select('Key,isPublicSurvey,Status')->get_where('db_it.surv_survey', array(
                'ID' => $ID
            ))->result_array();

            if ($dataCk[0]['Key'] != '' && $dataCk[0]['Key'] != null) {
                $KeyPublic = $dataCk[0]['Key'];
            } else {
                $KeyPublic = $this->m_api->checkCodeSurvey();
                // Update
                $dataUpdate['Key'] = $KeyPublic;
                $dataUpdate['UpdatedBy'] = $data_arr['NIP'];
                $dataUpdate['UpdatedAt'] = $this->m_rest->getDateTimeNow();

                $this->db->where('ID', $ID);
                $this->db->update('db_it.surv_survey', $dataUpdate);
            }

            $URLQrCode = url_sign_out . 'form/' . $KeyPublic;

            $t = QRcode::png($URLQrCode, false, 'L', 10, 4);
            $pic = 'data:image/png;base64,' . $t;

            $result = array(
                'Status' => 1,
                'Key' => $KeyPublic,
                'Encode' => $t,
                'QRCode' => $pic,
                'isPublicSurvey' => $dataCk[0]['isPublicSurvey'],
                'Sts' => $dataCk[0]['Status']
            );


            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'showQuestionInSurvey') {

            $SurveyID = $data_arr['SurveyID'];

            $data = $this->db->query('SELECT ssd.QuestionID, sq.Question, sq.QTID, sqc.Description AS Category, 
                                                 sqt.Description AS Type
                                                FROM db_it.surv_survey_detail ssd
                                                LEFT JOIN db_it.surv_question sq ON (sq.ID = ssd.QuestionID)
                                                LEFT JOIN db_it.surv_question_category sqc ON (sqc.ID = sq.QCID)
                                                LEFT JOIN db_it.surv_question_type sqt ON (sqt.ID = sq.QTID)
                                                WHERE ssd.SurveyId = "' . $SurveyID . '" ORDER BY ssd.Queue ASC ')
                ->result_array();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {

                    $AverageRate = '';

                    $dataWhere = ' sad.SurveyID = "' . $SurveyID . '" 
                                    AND sad.QuestionID = "' . $data[$i]['QuestionID'] . '"
                                     AND sad.QTID = "' . $data[$i]['QTID'] . '"
                                      AND sa.Status = "1" ';

                    if ($data[$i]['QTID'] == '4' || $data[$i]['QTID'] == 4) {
                        // Cek berapa yang udah jawab
                        //                        $dataTotalJawaban = $this->db->get_where('db_it.surv_answer_detail',$dataWhere)->result_array();

                        $whereRate = ' AND sad.Rate = 1';
                        $R_1 = $this->db->query('SELECT COUNT(*) AS Total 
                                                        FROM db_it.surv_answer_detail sad 
                                                        LEFT JOIN db_it.surv_answer sa 
                                                        ON (sa.ID = sad.AnswerID) 
                                                        WHERE ' . $dataWhere . $whereRate)
                            ->result_array()[0]['Total'];

                        $whereRate = ' AND sad.Rate = 2';
                        $R_2 = $this->db->query('SELECT COUNT(*) AS Total 
                                                        FROM db_it.surv_answer_detail sad 
                                                        LEFT JOIN db_it.surv_answer sa 
                                                        ON (sa.ID = sad.AnswerID) 
                                                        WHERE ' . $dataWhere . $whereRate)
                            ->result_array()[0]['Total'];

                        $whereRate = ' AND sad.Rate = 3';
                        $R_3 = $this->db->query('SELECT COUNT(*) AS Total 
                                                        FROM db_it.surv_answer_detail sad 
                                                        LEFT JOIN db_it.surv_answer sa 
                                                        ON (sa.ID = sad.AnswerID) 
                                                        WHERE ' . $dataWhere . $whereRate)
                            ->result_array()[0]['Total'];

                        $whereRate = ' AND sad.Rate = 4';
                        $R_4 = $this->db->query('SELECT COUNT(*) AS Total 
                                                        FROM db_it.surv_answer_detail sad 
                                                        LEFT JOIN db_it.surv_answer sa 
                                                        ON (sa.ID = sad.AnswerID) 
                                                        WHERE ' . $dataWhere . $whereRate)
                            ->result_array()[0]['Total'];

                        $AverageRate = '<div>B1 : ' . $R_1 . '</div>
                                        <div>B2 : ' . $R_2 . '</div>
                                        <div>B3 : ' . $R_3 . '</div>
                                        <div>B4 : ' . $R_4 . '</div>';
                    } else if ($data[$i]['QTID'] == '5' || $data[$i]['QTID'] == 5) {

                        $dataWhere = $dataWhere . ' AND IsTrue = "1"';

                        $TotalYes = $this->db->query('SELECT COUNT(*) AS Total FROM db_it.surv_answer_detail sad LEFT JOIN db_it.surv_answer sa 
                                                        ON (sa.ID = sad.AnswerID) WHERE ' . $dataWhere)->result_array()[0]['Total'];

                        $dataWhere = $dataWhere . ' AND IsTrue = "0"';
                        $TotalNo = $this->db->query('SELECT COUNT(*) AS Total FROM db_it.surv_answer_detail sad LEFT JOIN db_it.surv_answer sa 
                                                        ON (sa.ID = sad.AnswerID) WHERE ' . $dataWhere)->result_array()[0]['Total'];

                        $AverageRate = '<div>Y : ' . $TotalYes . '</div><div>N : ' . $TotalNo . '</div>';
                    }

                    $data[$i]['AverageRate'] = '<div style="text-align: left;">' . $AverageRate . '</div>';
                }
            }


            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'showUserAlreadyFill') {

            $requestData = $_REQUEST;

            $dataWhere = ($data_arr['Type'] == 'all') ? '' : ' AND sa.FormType = "' . $data_arr['Type'] . '" ';

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataScr = ' AND (sa.Username LIKE "%' . $search . '%" OR 
                                em.Name LIKE "%' . $search . '%"  OR 
                                ats.Name LIKE "%' . $search . '%")';
                $dataSearch = $dataScr;
            }


            $whereAnswerSurvey = ($data_arr['Status'] == '2') ?
                ' AND sa.RecapID = (SELECT MAX(RecapID) FROM db_it.surv_answer WHERE SurveyID= "' . $data_arr['SurveyID'] . '")'
                : ' AND sa.Status = "1" ';

            $queryDefault = 'SELECT sa.*,  CASE WHEN  sa.Type = "emp" THEN em.Name
                                                WHEN  sa.Type = "std" THEN ats.Name
                                                ELSE exu.FullName END AS "Name" FROM db_it.surv_answer sa 
                                                LEFT JOIN db_employees.employees em ON (em.NIP = sa.Username)
                                                LEFT JOIN db_academic.auth_students ats ON (ats.NPM = sa.Username)
                                                LEFT JOIN db_it.surv_external_user exu ON (exu.ID = sa.Username)
                                        WHERE sa.SurveyID = "' . $data_arr['SurveyID'] . '" ' . $whereAnswerSurvey .
                $dataWhere . $dataSearch;

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                //                $Type = ($row['Type']=='std')
                //                    ? '<span class="label label-primary">Student</span>'
                //                    : '<span class="label label-success">Emp / Lec</span>';
                $Type = '<span class="label label-warning">Other</span>';
                if ($row['Type'] == 'std') {
                    $Type = '<span class="label label-primary">Std</span>';
                } else if ($row['Type'] == 'emp') {
                    $Type = '<span class="label label-success">Emp / Lec</span>';
                }

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align: left;"><b>' . $row['Name'] . '</b><br/>' . $row['Username'] . '</div>';
                $nestedData[] = '<div>' . $Type . '</div>';
                $nestedData[] = '<div>' . date('d M Y H:i', strtotime($row['EntredAt'])) . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval($queryDefaultRow),
                "recordsFiltered" => intval($queryDefaultRow),
                "data"            => $data,
                "dataQuery"            => $query
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'createNewDateSurvey') {

            $SurveyID = $data_arr['SurveyID'];
            // update date nya
            $this->db->where(array(
                'ID' => $SurveyID,
            ));
            $this->db->update('db_it.surv_survey', array(
                'StartDate' => $data_arr['StartDate'],
                'EndDate' => $data_arr['EndDate'],
                'Status' => '0'
            ));

            return print_r(1);
        } else if ($data_arr['action'] == 'genrateClose') {

            // Cek semua survey yg udah close
            $data = $this->db->query('SELECT sa.SurveyID, sa.RecapID, s.StartDate, s.EndDate, s.Status FROM db_it.surv_answer sa 
                                                LEFT JOIN db_it.surv_survey s ON (s.ID = sa.SurveyID)
                                                WHERE sa.Status = "1" AND s.Status = "2"
                                                GROUP BY sa.SurveyID')->result_array();

            if (count($data) > 0) {
                for ($i2 = 0; $i2 < count($data); $i2++) {

                    $SurveyID = $data[$i2]['SurveyID'];

                    $insertRecap = array(
                        'SurveyID' => $SurveyID,
                        'StartDate' => $data[$i2]['StartDate'],
                        'EndDate' => $data[$i2]['EndDate'],
                        'EntredBy' => "2017090"
                    );
                    $this->db->insert('db_it.surv_recap', $insertRecap);
                    $RecapID = $this->db->insert_id();

                    // backup question
                    $dataQuestion = $this->db->select('QuestionID')
                        ->get_where(
                            'db_it.surv_survey_detail',
                            array('SurveyID' => $SurveyID)
                        )->result_array();

                    if (count($dataQuestion) > 0) {
                        for ($i = 0; $i < count($dataQuestion); $i++) {
                            $insertRecapQuestion = array(
                                'RecapID' => $RecapID,
                                'SurveyID' => $SurveyID,
                                'QuestionID' => $dataQuestion[$i]['QuestionID']
                            );
                            $this->db->insert('db_it.surv_recap_question', $insertRecapQuestion);
                        }
                    }

                    $this->db->where(array(
                        'SurveyID' => $SurveyID,
                        'Status' => '1'
                    ));
                    $this->db->update('db_it.surv_answer', array('RecapID' => $RecapID));
                    $this->db->reset_query();

                    $this->db->where(array(
                        'SurveyID' => $SurveyID,
                        'RecapID' => $RecapID
                    ));
                    $this->db->update('db_it.surv_answer', array('Status' => '2'));
                    $this->db->reset_query();
                }
            }

            print_r($data);



            // cek apakah sudah ke recap atau blm

        } else if ($data_arr['action'] == 'getOneDataSurvey') {
            $ID = $data_arr['ID'];
            $data = $this->db->get_where('db_it.surv_survey', array('ID' => $ID))->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'setStatusSurvey') {

            $dataFmQuiz['Status'] = $data_arr['Status'];
            $dataFmQuiz['UpdatedBy'] = $data_arr['NIP'];
            $dataFmQuiz['UpdatedAt'] = $this->m_rest->getDateTimeNow();
            $this->db->where('ID', $data_arr['ID']);
            $this->db->update('db_it.surv_survey', $dataFmQuiz);

            $row_Status = $data_arr['Status'];
            $Status = '<span class="label label-warning">Unpublish</span>';
            if ($row_Status == '1') {
                $Status = '<span class="label label-success">Publish</span>';
            } else if ($row_Status == '2') {
                $Status = '<span class="label label-danger">Close</span>';

                // input ke backup
                $SurveyID = $data_arr['ID'];

                // Get data survey
                $dataSurvey = $this->db
                    ->get_where(
                        'db_it.surv_survey',
                        array('ID' => $SurveyID)
                    )
                    ->result_array();

                $insertRecap = array(
                    'SurveyID' => $SurveyID,
                    'StartDate' => $dataSurvey[0]['StartDate'],
                    'EndDate' => $dataSurvey[0]['EndDate'],
                    'EntredBy' => $data_arr['NIP']
                );
                $this->db->insert('db_it.surv_recap', $insertRecap);
                $RecapID = $this->db->insert_id();

                // backup question
                $dataQuestion = $this->db->select('QuestionID')
                    ->get_where(
                        'db_it.surv_survey_detail',
                        array('SurveyID' => $SurveyID)
                    )->result_array();

                if (count($dataQuestion) > 0) {
                    for ($i = 0; $i < count($dataQuestion); $i++) {
                        $insertRecapQuestion = array(
                            'RecapID' => $RecapID,
                            'SurveyID' => $SurveyID,
                            'QuestionID' => $dataQuestion[$i]['QuestionID']
                        );
                        $this->db->insert('db_it.surv_recap_question', $insertRecapQuestion);
                    }
                }

                $this->db->where(array(
                    'SurveyID' => $SurveyID,
                    'Status' => '1'
                ));
                $this->db->update('db_it.surv_answer', array('RecapID' => $RecapID));
                $this->db->reset_query();

                $this->db->where(array(
                    'SurveyID' => $SurveyID,
                    'RecapID' => $RecapID
                ));
                $this->db->update('db_it.surv_answer', array('Status' => '2'));
                $this->db->reset_query();
            }

            return print_r(json_encode(array('Status' => 1, 'Label' => $Status)));
        } else if ($data_arr['action'] == 'updateTargetSurvey') {

            $ID = $data_arr['ID'];

            // ======= Employees ========

            $surv_survey_usr_emp = $data_arr['surv_survey_usr_emp'];
            // Remove data sebelumnya
            $this->db->where('SurveyID', $ID);
            $this->db->delete('db_it.surv_survey_usr_emp');
            $this->db->reset_query();

            if ($surv_survey_usr_emp != '-1' && $surv_survey_usr_emp != -1) {
                $this->db->insert(
                    'db_it.surv_survey_usr_emp',
                    array('SurveyID' => $ID, 'TypeUser' => $surv_survey_usr_emp)
                );
                $this->db->reset_query();
            }

            // ====== Student ========
            $surv_survey_usr_std = $data_arr['surv_survey_usr_std'];

            if ($surv_survey_usr_std != 0 && $surv_survey_usr_std != '0') {
                // Remove data sebelumnya
                $dataCk = $this->db->select('ID')->get_where(
                    'db_it.surv_survey_usr_std',
                    array('SurveyID' => $ID)
                )->result_array();

                if (count($dataCk) > 0) {
                    $SUSID = $dataCk[0]['ID'];
                    $this->db->where('SUSID', $SUSID);
                    $this->db->delete('db_it.surv_survey_usr_std_details');
                    $this->db->reset_query();

                    $this->db->where('ID', $SUSID);
                    $this->db->delete('db_it.surv_survey_usr_std');
                    $this->db->reset_query();
                }

                if ($surv_survey_usr_std != '-1' && $surv_survey_usr_std != -1) {
                    $this->db->insert(
                        'db_it.surv_survey_usr_std',
                        array('SurveyID' => $ID, 'TypeUser' => $surv_survey_usr_std)
                    );
                    $this->db->reset_query();
                }
            }



            return print_r(1);
        } else if ($data_arr['action'] == 'setDataTargetUsrtStdDetail') {

            // cek apakah ID sudah ada di table surv_survey_usr_std
            $dataCk = $this->db->select('ID')->get_where(
                'db_it.surv_survey_usr_std',
                array('SurveyID' => $data_arr['ID'])
            )
                ->result_array();



            if (count($dataCk) > 0) {
                $SUSID = $dataCk[0]['ID'];
                $this->db->where('ID', $SUSID);
                $this->db->update(
                    'db_it.surv_survey_usr_std',
                    array('TypeUser' => '0')
                );
            } else {
                $dataIns = array(
                    'SurveyID' => $data_arr['ID'],
                    'TypeUser' => '0'
                );
                $this->db->insert('db_it.surv_survey_usr_std', $dataIns);
                $SUSID = $this->db->insert_id();
            }

            $dataForm = (array) $data_arr['dataForm'];
            $dataForm['SUSID'] = $SUSID;

            $dataCk2 = $this->db->select('ID')
                ->get_where('db_it.surv_survey_usr_std_details', $dataForm)
                ->result_array();

            $result = array('Status' => 0);

            if (count($dataCk2) <= 0) {
                $result = array('Status' => 1);
                $this->db->insert('db_it.surv_survey_usr_std_details', $dataForm);
            }


            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'removeDataFromTargetUsrtStdDetail') {

            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_it.surv_survey_usr_std_details');

            return print_r(1);
        } else if ($data_arr['action'] == 'getDataTargetSurvey') {
            $ID = $data_arr['ID'];

            // Data survey
            $dataSurv = $this->db->get_where(
                'db_it.surv_survey',
                array('ID' => $ID)
            )->result_array();

            $dataEmp = $this->db->get_where(
                'db_it.surv_survey_usr_emp',
                array('SurveyID' => $ID)
            )->result_array();

            $dataStd = $this->db->get_where(
                'db_it.surv_survey_usr_std',
                array('SurveyID' => $ID)
            )->result_array();

            $dataSurv[0]['Employee'] = $dataEmp;
            $dataSurv[0]['Student'] = $dataStd;

            return print_r(json_encode($dataSurv[0]));
        } else if ($data_arr['action'] == 'getDataTargetCustomStudent') {
            $ID = $data_arr['ID'];
            $dataStd = $this->db->get_where(
                'db_it.surv_survey_usr_std',
                array('SurveyID' => $ID)
            )->result_array();

            $result = [];

            if (count($dataStd) > 0) {
                $d = $dataStd[0];
                $result = $this->db
                    ->query('SELECT ssusd.ID AS TargetUserID, ssusd.ClassOf, ps.NameEng AS Prodi, ss.Description 
                                    FROM db_it.surv_survey_usr_std_details ssusd
                                    LEFT JOIN db_academic.program_study ps 
                                    ON (ps.ID = ssusd.ProdiID)
                                    LEFT JOIN db_academic.status_student ss 
                                    ON (ss.ID = ssusd.StatusStudentID)
                                    WHERE ssusd.SUSID = "' . $d['ID'] . '" 
                                    ORDER BY ps.NameEng ASC, ssusd.ClassOf DESC, ss.ID ASC ')
                    ->result_array();
            }

            return print_r(json_encode($result));
        }
    }
}
