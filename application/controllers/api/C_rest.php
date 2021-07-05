<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_rest extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('akademik/m_onlineclass');
        $this->load->model('master/m_master');
        $this->load->library('JWT');
        $this->load->library('google');
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

    private function cekAuthAPI($dataAuth)
    {

        $auth = (array) $dataAuth;

        if ($auth['user'] == 'students') {
            $db = 'db_academic.auth_students';
        } else if ($auth['user'] == 'lecturer') {
            $db = 'db_employees.employees';
        } else if ($auth['user'] == 'siak') {
            return true;
        }

        $data = $this->db->get_where($db, array('Password' => $auth['token']))->result_array();

        if (count($data) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function checkDateKRS()
    {
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if ($cekUser) {
            $user = (array) $dataToken['auth'];
            if ($user['user'] == 'lecturer') {
                $data = $this->m_api->__checkDateKRSLecturer($dataToken['date']);
            } else {
                $data = $this->m_api->__checkDateKRS(
                    $dataToken['SemesterIDActive'],
                    $dataToken['date'],
                    $dataToken['ProdiID'],
                    $dataToken['GroupProdiID'],
                    $dataToken['ClassOf'],
                    $dataToken['NPM'],
                    $dataToken['DB_std']
                );
            }

            return print_r(json_encode($data));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    function getDetailKRS()
    {
        $token = $this->input->post('token');
        $key = "s3Cr3T-G4N";
        $dataToken = (array) $this->jwt->decode($token, $key);

        $cekUser = $this->cekAuthAPI($dataToken['auth']);
        if ($cekUser) {
            $data = $this->m_api->getDetailStudyPlanning($dataToken['NPM'], $dataToken['ta']);
            return print_r(json_encode($data));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    function getKSM()
    {
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if ($cekUser) {

            $data = $this->m_rest->__getKSM($dataToken['DB_'], $dataToken['ProdiID'], $dataToken['NPM'], $dataToken['ClassOf']);
            return print_r(json_encode($data));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function getExamScheduleForStudent()
    {
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if ($cekUser) {
            $data = $this->m_rest->__getExamScheduleForStudent(
                $dataToken['DB_'],
                $dataToken['SemesterID'],
                $dataToken['NPM'],
                $dataToken['ClassOf'],
                $dataToken['ExamType']
            );

            return print_r(json_encode($data));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function geTimetable()
    {

        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if ($cekUser) {

            if ($dataToken['action'] == 'getTimeTable') {
                $NIP = $dataToken['NIP'];
                //                $SemesterID = $dataToken['SemesterID'];
                $schedule = $this->m_rest->__geTimetable($NIP);

                return print_r(json_encode($schedule));
            } else if ($dataToken['action'] == 'getTimeTableOneSemester') {

                $NIP = $dataToken['NIP'];
                $SemesterID = $dataToken['SemesterID'];
                $schedule = $this->m_rest->__geTimetable($NIP, $SemesterID);

                return print_r(json_encode($schedule));
            } else if ($dataToken['action'] == 'chekTeamTheaching') {

                $data = $this->db
                    ->get_where(
                        'db_academic.schedule_team_teaching',
                        array(
                            'ScheduleID' => $dataToken['ScheduleID'],
                            'NIP' => $dataToken['NIP']
                        ),
                        1
                    )
                    ->result_array();

                return print_r(json_encode($data));
            } else if ($dataToken['action'] == 'updateStatusTeamTeaching') {
                $this->db->set('Status', $dataToken['Status']);
                $this->db->where('ID', $dataToken['ID']);
                $this->db->update('db_academic.schedule_team_teaching');
                return print_r(1);
            } else if ($dataToken['action'] == 'getDetailsStudents') {
                $SemesterID = $dataToken['SemesterID'];
                $ScheduleID = $dataToken['ScheduleID'];

                $dataStd = $this->m_rest->__getStudentsDetails($SemesterID, $ScheduleID);

                return print_r(json_encode($dataStd));
            }
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function getExamSchedule()
    {

        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if ($cekUser) {

            if ($dataToken['action'] == 'readExamSchedule') {
                $NIP = $dataToken['NIP'];
                //                $SemesterID = $dataToken['SemesterID'];
                $schedule = $this->m_rest->__getExamSchedule($NIP, strtolower($dataToken['Type']));

                return print_r(json_encode($schedule));
            } else if ($dataToken['action'] == 'readExamSchedule2') {
                $NIP = $dataToken['NIP'];
                $SemesterID = $dataToken['SemesterID'];
                $schedule = $this->m_rest->__getExamSchedule4Lecturer($SemesterID, $NIP, strtolower($dataToken['Type']));

                return print_r(json_encode($schedule));
            }
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function cek_deadline_paymentNPM()
    {
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);
        if ($cekUser) {
            $NPM = $dataToken['NPM'];
            $arr = $this->m_api->cek_deadline_paymentNPM($NPM);
            return print_r(json_encode($arr));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function getStudyResult()
    {
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if ($cekUser) {

            $data = $this->m_rest->getDetailStudyResultByNPM($dataToken['ClassOf'], $dataToken['NPM']);
            return print_r(json_encode($data));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function getTranscript()
    {
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if ($cekUser) {

            $data = $this->m_rest->getTranscript($dataToken['ClassOf'], $dataToken['NPM'], 'ASC');

            if (isset($dataToken['Source']) && $dataToken['Source'] == 'Portal') {
                unset($data['dataIPK']);
                $dataCourse = $data['dataCourse'];
                for ($i = 0; $i < count($dataCourse); $i++) {
                    unset($data['dataCourse'][$i]['Score']);
                    unset($data['dataCourse'][$i]['Grade']);
                    unset($data['dataCourse'][$i]['GradeValue']);

                    unset($data['dataCourse'][$i]['CDID']);
                    unset($data['dataCourse'][$i]['MKType']);
                    unset($data['dataCourse'][$i]['MKID']);
                    unset($data['dataCourse'][$i]['Point']);
                    unset($data['dataCourse'][$i]['SemesterID']);
                }
            }

            return print_r(json_encode($data));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function getDetailsCourse()
    {

        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if ($cekUser) {

            $data = $this->m_rest->getDetailsCourse($dataToken['ClassOf'], $dataToken['NPM']);
            return print_r(json_encode($data));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    # OLD SCRIPT TO GET STUDENT SCORES
    public function getListStudentScores()
    {
        $requestData = $_REQUEST;

        $data_arr = $this->getInputToken();


        $w_ClassOf = ($data_arr['ClassOf'] != '' && $data_arr['ClassOf'] != null) ? ' AND ast.Year = "' . $data_arr['ClassOf'] . '"' : '';
        $w_ProdiGroupID = ($data_arr['ProdiGroupID'] != '' && $data_arr['ProdiGroupID'] != null) ? ' AND ast.ProdiGroupID = "' . $data_arr['ProdiGroupID'] . '"' : '';
        $w_StatusStudent = ($data_arr['StatusStudent'] != '' && $data_arr['StatusStudent'] != null) ? ' AND ast.StatusStudentID = "' . $data_arr['StatusStudent'] . '"' : '';
        $dataWhere = $w_ClassOf . ' ' . $w_ProdiGroupID . ' ' . $w_StatusStudent;

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = 'AND ( ast.Name LIKE "%' . $search . '%" OR ast.NPM LIKE "%' . $search . '%"
                           OR ss.Description LIKE "%' . $search . '%" OR em.Name LIKE "%' . $search . '%"
                             OR em.NIP LIKE "%' . $search . '%")';
        }

        $queryDefault = 'SELECT ast.*, ss.Description AS StatusDescription, em.Name AS Mentor, em.NIP
                                                          FROM db_academic.auth_students ast
                                                          LEFT JOIN db_academic.status_student ss ON (ast.StatusStudentID = ss.ID)
                                                          LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = ast.NPM)
                                                          LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                                                          WHERE ( ast.ProdiID = "' . $data_arr['ProdiID'] . '" ' . $dataWhere . ' ) ' . $dataSearch . '
                                                          ORDER BY ast.Year DESC, ast.NPM ASC';

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $db_ = 'ta_' . $row['Year'];
            $dataDetailStd = $this->db->select('Photo')->get_where($db_ . '.students', array('NPM' => $row['NPM']), 1)->result_array();

            // Get Photo
            $exp_photo = explode(' ', $dataDetailStd[0]['Photo']);
            if ($dataDetailStd[0]['Photo'] != '' && $dataDetailStd[0]['Photo'] != null && count($exp_photo) == 1) {
                $url_photo = base_url() . 'uploads/students/' . $db_ . '/' . $dataDetailStd[0]['Photo'];
            } else {
                $url_photo = base_url() . 'images/icon/userfalse.png';
            }

            // Get Semester
            $dataSemester = $this->db->order_by('ID', 'ASC')->get_where(
                'db_academic.semester',
                array('Year >=' => $row['Year'])
            )->result_array();

            $listSemester = '';
            $Semester = 1;
            if (count($dataSemester) > 0) {
                for ($s = 0; $s < count($dataSemester); $s++) {
                    $d_s = $dataSemester[$s];




                    $dataScore = $this->db->query('SELECT * FROM ' . $db_ . '.study_planning sr
                                                                    WHERE sr.SemesterID = "' . $d_s['ID'] . '"
                                                                    AND sr.NPM = "' . $row['NPM'] . '" ')->result_array();

                    $koma = ($s != 0) ? '' : '';
                    if (count($dataScore) > 0) {

                        $IPS_totalCredit = 0;
                        $IPS_totalGradeValue = 0;

                        for ($c = 0; $c < count($dataScore); $c++) {
                            $d_sc = $dataScore[$c];
                            $IPS_totalCredit = $IPS_totalCredit + $d_sc['Credit'];
                            $IPS_totalGradeValue = $IPS_totalGradeValue + ($d_sc['Credit'] * $d_sc['GradeValue']);
                        }

                        $IPS = ($IPS_totalGradeValue > 0) ? $IPS_totalGradeValue / $IPS_totalCredit : 0.00;


                        $listSemester = $listSemester . '' . $koma . ' <span class="label label-default"> Smt ' . $Semester . ' : ' . number_format(round($IPS, 2), 2) . '</span> ';
                    } else {
                        if ($d_s['Status'] == 1) {
                            break;
                        }
                        $listSemester = $listSemester . '' . $koma . ' <span class="label label-default"> Smt ' . $Semester . ' : -</span> ';
                    }

                    if ($d_s['Status'] == 1) {
                        break;
                    }

                    $Semester += 1;
                }
            }

            $dataNewTr = $this->m_rest->getTranscript($row['Year'], $row['NPM'], 'ASC');
            $IPK = $dataNewTr['dataIPK']['IPK'];



            $token = $this->jwt->encode(
                array(
                    'NPM' => $row['NPM'], 'ClassOf' => $row['Year'], 'Name' => $row['Name'],
                    'URL_Photo' => $url_photo, 'URL_Back' => base_url('student/list-student-scores-as-head')
                ),
                'UAP)(*'
            );
            $student = '<span style="color: #2b6886;">' . $row['Name'] . '</span><br/><span style="font-size: 12px;color: #808080;">' . $row['NPM'] . '</span>';

            $btnAct = '<div class="btn-group">
                      <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-edit"></i> <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a href="' . url_sign_in_lecturers . 'student/details-student-scores/' . $token . '">Details Score</a></li>
                        <li><a href="' . url_sign_in_lecturers . 'student/student-transcript/' . $token . '">Transcript</a></li>
                      </ul>
                    </div>';

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:center;"><img class="img-rounded" src="' . $url_photo . '" style="max-width: 33px;border: 1px solid #CCCCCC;padding: 1.5px;" /></div>';
            $nestedData[] = '<div  style="text-align:left;">' . $student . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . $row['Mentor'] . '<br/><span style="font-size: 12px;color: #808080;">' . $row['NIP'] . '</span></div>';
            $nestedData[] = '<div  style="text-align:left;">' . $listSemester . '</div>';
            $nestedData[] = $IPK;
            $nestedData[] = '<div  style="text-align:center;">' . $btnAct . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . ucwords(strtolower($row['StatusDescription'])) . '</div>';

            $no++;

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval(count($queryDefaultRow)),
            "data"            => $data
        );

        echo json_encode($json_data);
    }
    #END OLD SCRIPT

    /*UPDATED BY FEBRI @ MARCH 2020*/
    public function getListStudentScoresOBJ()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();
        $time = strtotime("-1 year", time());
        $CurrDate = date("Y", $time);
        $w_ClassOf = (!empty($data_arr['ClassOf']) && $data_arr['ClassOf'] != '' && $data_arr['ClassOf'] != null) ? ' AND ast.Year = "' . $data_arr['ClassOf'] . '"' : ' AND ast.Year = ' . $CurrDate;
        $w_ProdiGroupID = (!empty($data_arr['ProdiGroupID']) && $data_arr['ProdiGroupID'] != '' && $data_arr['ProdiGroupID'] != null) ? ' AND ast.ProdiGroupID = "' . $data_arr['ProdiGroupID'] . '"' : '';
        $w_StatusStudent = (!empty($data_arr['StatusStudent']) && $data_arr['StatusStudent'] != '' && $data_arr['StatusStudent'] != null) ? ' AND ast.StatusStudentID = "' . $data_arr['StatusStudent'] . '"' : '';
        $dataWhere = $w_ClassOf . ' ' . $w_ProdiGroupID . ' ' . $w_StatusStudent;

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = 'AND ( ast.Name LIKE "%' . $search . '%" OR ast.NPM LIKE "%' . $search . '%"
                           OR ss.Description LIKE "%' . $search . '%" OR em.Name LIKE "%' . $search . '%"
                             OR em.NIP LIKE "%' . $search . '%")';
        }

        $queryDefault = 'SELECT ast.*, ss.Description AS StatusDescription, em.Name AS Mentor, em.NIP
                                                          FROM db_academic.auth_students ast
                                                          LEFT JOIN db_academic.status_student ss ON (ast.StatusStudentID = ss.ID)
                                                          LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = ast.NPM)
                                                          LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                                                          LEFT JOIN db_academic.prodi_group pg ON (pg.ProdiID = ma.NIP)
                                                          WHERE ( ast.ProdiID = "' . $data_arr['ProdiID'] . '" ' . $dataWhere . ' ) ' . $dataSearch . '
                                                          ORDER BY ast.Year DESC, ast.NPM ASC';
        if (!empty($requestData['start']) && !empty($requestData['length'])) {
            $sql = $queryDefault . ' LIMIT ' . (!empty($requestData['start']) ? $requestData['start'] : 0) . ',' . (!empty($requestData['length']) ? $requestData['length'] : 0) . ' ';
        } else {
            $sql = $queryDefault;
        }

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = (!empty($requestData['start']) ? $requestData['start'] : 0) + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $db_ = 'ta_' . $row['Year'];
            $dataDetailStd = $this->db->select('Photo')->get_where($db_ . '.students', array('NPM' => $row['NPM']), 1)->result_array();

            //getdetail prodi_group
            if (!empty($row['ProdiGroupID'])) {
                $prodiGroup = $this->db->get_where('db_academic.prodi_group', array('ID' => $row['ProdiGroupID']))->row();
            }

            // Get Photo
            $exp_photo = explode(' ', $dataDetailStd[0]['Photo']);
            if ($dataDetailStd[0]['Photo'] != '' && $dataDetailStd[0]['Photo'] != null && count($exp_photo) == 1) {
                $url_photo = base_url() . 'uploads/students/' . $db_ . '/' . $dataDetailStd[0]['Photo'];
            } else {
                $url_photo = base_url() . 'images/icon/userfalse.png';
            }

            // Get Semester
            $dataSemester = $this->db->order_by('ID', 'ASC')->get_where(
                'db_academic.semester',
                array('Year >=' => $row['Year'])
            )->result_array();

            $listSemester = '';
            $Semester = 1;
            if (count($dataSemester) > 0) {
                for ($s = 0; $s < count($dataSemester); $s++) {
                    $d_s = $dataSemester[$s];
                    $dataScore = $this->db->query('SELECT * FROM ' . $db_ . '.study_planning sr
                                                                    WHERE sr.SemesterID = "' . $d_s['ID'] . '"
                                                                    AND sr.NPM = "' . $row['NPM'] . '" ')->result_array();

                    $koma = ($s != 0) ? '' : '';
                    if (count($dataScore) > 0) {

                        $IPS_totalCredit = 0;
                        $IPS_totalGradeValue = 0;

                        for ($c = 0; $c < count($dataScore); $c++) {
                            $d_sc = $dataScore[$c];
                            $IPS_totalCredit = $IPS_totalCredit + $d_sc['Credit'];
                            $IPS_totalGradeValue = $IPS_totalGradeValue + ($d_sc['Credit'] * $d_sc['GradeValue']);
                        }

                        $IPS = ($IPS_totalGradeValue > 0) ? $IPS_totalGradeValue / $IPS_totalCredit : 0.00;


                        $listSemester = $listSemester . '' . $koma . ' <span class="label label-default"> Smt ' . $Semester . ' : ' . number_format(round($IPS, 2), 2) . '</span> ';
                    } else {
                        if ($d_s['Status'] == 1) {
                            break;
                        }
                        $listSemester = $listSemester . '' . $koma . ' <span class="label label-default"> Smt ' . $Semester . ' : -</span> ';
                    }

                    if ($d_s['Status'] == 1) {
                        break;
                    }
                    $Semester += 1;
                }
            }

            $dataNewTr = $this->m_rest->getTranscript($row['Year'], $row['NPM'], 'ASC');
            $IPK = $dataNewTr['dataIPK']['IPK'];
            $token = $this->jwt->encode(
                array(
                    'NPM' => $row['NPM'], 'ClassOf' => $row['Year'], 'Name' => $row['Name'],
                    'URL_Photo' => $url_photo, 'URL_Back' => base_url('student/list-student-scores-as-head')
                ),
                'UAP)(*'
            );

            $data[] = array("No" => $no, "Photo" => $url_photo, "NPM" => $row['NPM'], "Student" => $row['Name'], "PGID" => $row['ProdiGroupID'], "ProdiGroupID" => (!empty($prodiGroup) ? $prodiGroup->Code : null), "MentorName" => $row['Mentor'], "MentorNIP" => $row['NIP'], "ListSemester" => $listSemester, "IPK" => number_format($IPK, 2), "Token" => $token, "StatusDescription" => ucwords(strtolower($row['StatusDescription'])));
            $no++;
        }

        $json_data = array(
            "draw"            => intval((!empty($requestData['draw']) ? $requestData['draw'] : null)),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval(count($queryDefaultRow)),
            "data"            => (!empty($data) ? $data : 0)
        );

        echo json_encode($json_data);
    }
    /*END UPDATED BY FEBRI @ MARCH 2020*/

    // Nandang - Get Student digunakan khusus untuk selec2.js
    public function getStudent_ServerSide()
    {

        $term = $this->input->get('term');

        $data = $this->db->query('SELECT * FROM db_academic.auth_students auts WHERE 
                                                  auts.NPM LIKE "%' . $term . '%" 
                                                  OR auts.Name LIKE "%' . $term . '%" LIMIT 15 ')->result_array();

        $result = [];
        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $d = $data[$i];
                $arr = array(
                    'id' => $d['NPM'],
                    'text' => $d['NPM'] . ' - ' . $d['Name']
                );
                array_push($result, $arr);
            }
        }

        $data_result = array(
            'results' => $result
        );

        return print_r(json_encode($data_result));
    }

    // Nandang - Get HighSchool digunakan khusus untuk selec2.js
    public function getHighSchool_ServerSide()
    {

        $term = $this->input->get('term');


        $q = 'SELECT sch.ID, sch.CityID, sch.SchoolName, r.RegionName AS CityName FROM db_admission.school sch 
                                                LEFT JOIN db_admission.region r ON (r.RegionID = sch.CityID)
                                                WHERE sch.CityID IS NOT NULL AND sch.CityID != ""  AND (
                                                  sch.SchoolName LIKE "%' . $term . '%" 
                                                  OR sch.CityName LIKE "%' . $term . '%" )
                                                   GROUP BY sch.ID
                                                   ORDER BY sch.CityID ASC 
                                                   LIMIT 25';

        $data = $this->db->query($q)->result_array();


        $result = [];
        if (count($data) > 0) {
            $CityIDNow = '';
            $CityNameNow = '';
            $chang = true;
            $arrChildren = [];
            for ($i = 0; $i < count($data); $i++) {
                $d = $data[$i];

                $children = array(
                    'id' => $d['ID'],
                    'text' => $d['SchoolName']
                );

                if ($chang == true) {
                    $arrChildren = [];
                    $CityIDNow = $d['CityID'];
                    $CityNameNow = strtoupper($d['CityName']);
                    $chang = false;
                }


                if ($d['CityID'] == $CityIDNow) {
                    array_push($arrChildren, $children);
                } else {

                    $arrRest = array(
                        'text' => $CityNameNow,
                        'children' => $arrChildren
                    );

                    array_push($result, $arrRest);

                    $chang = true;
                }

                if (count($data) == ($i + 1)) {
                    $arrRest = array(
                        'text' => $CityNameNow,
                        'children' => $arrChildren
                    );

                    array_push($result, $arrRest);
                }
            }
        }

        //        print_r($arrChildren);

        $data_result = array(
            'results' => $result
        );

        return print_r(json_encode($data_result));
    }

    // Nandang - Get Lecturer digunakan khusus untuk selec2.js
    public function getLecturer_ServerSide()
    {

        $term = $this->input->get('term');

        $data = $this->db->query('SELECT * FROM db_employees.employees em WHERE (em.StatusLecturerID = "3"  
                                                  OR em.StatusLecturerID = "4" 
                                                  OR em.StatusLecturerID = "5"
                                                  OR em.StatusLecturerID = "6" ) AND (
                                                  em.NIP LIKE "%' . $term . '%" 
                                                  OR em.Name LIKE "%' . $term . '%" ) LIMIT 15 ')->result_array();

        $result = [];
        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $d = $data[$i];
                $arr = array(
                    'id' => $d['NIP'],
                    'text' => $d['NIP'] . ' - ' . $d['Name']
                );
                array_push($result, $arr);
            }
        }

        $data_result = array(
            'results' => $result
        );

        return print_r(json_encode($data_result));
    }

    public function crudCounseling()
    {
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if ($cekUser) {
            if ($dataToken['action'] == 'insertToNewTopic') {

                $dataTopic = (array) $dataToken['dataTopic'];

                $this->db->insert('db_academic.counseling_topic', $dataTopic);
                $insert_id = $this->db->insert_id();

                // Insert Lecturer
                if ($dataTopic['InviteTo'] != 4 || $dataTopic['InviteTo'] != '4') {
                    $this->db->insert('db_academic.counseling_user', array(
                        'TopicID' => $insert_id,
                        'UserID' => $dataTopic['CreateBy'],
                        'ReadComment' => 0,
                        'Status' => '1'
                    ));
                }


                // Cek Invite To
                if ($dataTopic['InviteTo'] == 1 || $dataTopic['InviteTo'] == '1') {
                    $formSelectStudent = $dataToken['formSelectStudent'];
                    for ($u = 0; $u < count($formSelectStudent); $u++) {
                        $dataIns = array(
                            'TopicID' => $insert_id,
                            'UserID' => $formSelectStudent[$u],
                            'ReadComment' => 0,
                            'Status' => '2'
                        );
                        $this->db->insert('db_academic.counseling_user', $dataIns);
                    }
                } else if ($dataTopic['InviteTo'] == 2 || $dataTopic['InviteTo'] == '2') {
                    $dataStudent = $this->m_rest->__getStudentByScheduleID($dataToken['SemesterID'], $dataTopic['ScheduleID']);
                    if (count($dataStudent) > 0) {
                        for ($s = 0; $s < count($dataStudent); $s++) {
                            $d_s = $dataStudent[$s];
                            $dataIns = array(
                                'TopicID' => $insert_id,
                                'UserID' => $d_s['NPM'],
                                'ReadComment' => 0,
                                'Status' => '2'
                            );
                            $this->db->insert('db_academic.counseling_user', $dataIns);
                        }
                    }
                } else if ($dataTopic['InviteTo'] == 3 || $dataTopic['InviteTo'] == '3') {
                    $dataStdMentor = $this->db->select('NPM')->get_where(
                        'db_academic.mentor_academic',
                        array('NIP' => $dataTopic['CreateBy'])
                    )->result_array();

                    if (count($dataStdMentor) > 0) {
                        for ($m = 0; $m < count($dataStdMentor); $m++) {
                            $d_m = $dataStdMentor[$m];
                            $dataIns = array(
                                'TopicID' => $insert_id,
                                'UserID' => $d_m['NPM'],
                                'ReadComment' => 0,
                                'Status' => '2'
                            );
                            $this->db->insert('db_academic.counseling_user', $dataIns);
                        }
                    }
                } else if ($dataTopic['InviteTo'] == 4 || $dataTopic['InviteTo'] == '4') {

                    //                    print_r($dataToken['dataUsers']);
                    //                    print_r(count($dataToken['dataUsers']));
                    //
                    //                    exit;

                    $dataUsers = $dataToken['dataUsers'];

                    if (count($dataUsers)) {
                        for ($u = 0; $u < count($dataUsers); $u++) {

                            $dataIns = array(
                                'TopicID' => $insert_id,
                                'UserID' => $dataUsers[$u],
                                'ReadComment' => 0,
                                'Status' => ($u == 0) ? '1' : '2'
                            );

                            $this->db->insert('db_academic.counseling_user', $dataIns);
                        }
                    }
                }



                // push notifikasi hanya untuk invote group kelas
                if ($dataTopic['InviteTo'] == 2 || $dataTopic['InviteTo'] == '2') {

                    // get employee name
                    $dataEmp = $this->db->select('Name,TitleAhead,TitleBehind')
                        ->get_where(
                            'db_employees.employees',
                            array('NIP' => $dataTopic['CreateBy'])
                        )->result_array()[0];

                    $TitleAhead = ($dataEmp['TitleAhead'] != '' && $dataEmp['TitleAhead'] != null)
                        ? $dataEmp['TitleAhead'] . ' ' : '';

                    $TitleBehind = ($dataEmp['TitleBehind'] != '' && $dataEmp['TitleBehind'] != null)
                        ? ' ' . $dataEmp['TitleBehind'] : '';

                    $LecName = $TitleAhead . $dataEmp['Name'] . $TitleBehind;

                    // get mk name
                    $dataMK = $this->db->query('SELECT s.ClassGroup, mk.NameEng FROM db_academic.schedule s 
                                                            LEFT JOIN db_academic.schedule_details_course sdc 
                                                            ON (s.ID = sdc.ScheduleID)
                                                            LEFT JOIN db_academic.mata_kuliah mk 
                                                            ON (mk.ID = sdc.MKID)
                                                            WHERE s.ID = "' . $dataTopic['ScheduleID'] . '"
                                                             GROUP BY s.ID ')->result_array()[0];

                    // push notification
                    $url = 'https://firebase.podomorouniversity.ac.id/pushnotification.php';
                    $dataPush = array(
                        'type' => 'topic', // topic / token
                        'topic' => 'pu_course_' . $dataTopic['ScheduleID'],
                        //                        'topic' => 'pu_emp_12',
                        'title' => 'New Discussion - ' . $LecName,
                        'body' => $dataMK['ClassGroup'] . ' ' . $dataMK['NameEng']
                            . ' | ' . $dataTopic['Topic']
                    );
                    $this->m_master->http_request_post_data($url, $dataPush);
                }


                return print_r(1);
            } else if ($dataToken['action'] == 'readTopic') {

                $requestData = $_REQUEST;

                $UserID = $dataToken['UserID'];
                $user =  $dataToken['auth']->user;

                $dataSearch = '';
                if (!empty($requestData['search']['value'])) {
                    $search = $requestData['search']['value'];
                    $dataSearch = ' AND ( ct.Topic LIKE "%' . $search . '%" )';
                }

                $queryDefault = 'SELECT cu.ReadComment, ct.* 
                                              FROM db_academic.counseling_user cu
                                              LEFT JOIN db_academic.counseling_topic ct
                                              ON (ct.ID = cu.TopicID)
                                              WHERE ( cu.UserID = "' . $UserID . '" ) ' . $dataSearch . '
                                               ';

                $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';



                $sql = $queryDefault . ' ORDER BY cu.TopicID DESC LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

                $query = $this->db->query($sql)->result_array();
                $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

                $no = $requestData['start'] + 1;
                $data = array();

                for ($i = 0; $i < count($query); $i++) {
                    $nestedData = array();
                    $row = $query[$i];

                    $dataTotalUser = $this->db->query('SELECT cu.ID, auts.NPM, auts.Name FROM db_academic.counseling_user cu 
                                                                  LEFT JOIN db_academic.auth_students auts ON (auts.NPM = cu.UserID)
                                                                  WHERE cu.TopicID = "' . $row['ID'] . '" AND cu.Status = "2" 
                                                                  ORDER BY auts.NPM ASC')->result_array();

                    $dataTotalLecturer = $this->db->query('SELECT cu.ID, em.NIP, em.Name FROM db_academic.counseling_user cu 
                                                                  LEFT JOIN db_employees.employees em ON (em.NIP = cu.UserID)
                                                                  WHERE cu.TopicID = "' . $row['ID'] . '" AND cu.Status = "1" 
                                                                  ORDER BY cu.ID ASC')->result_array();

                    $this->db->select('ID')->get_where('db_academic.counseling_user', array('TopicID' => $row['ID'], 'Status' => '1'))->result_array();
                    $dataComment = $this->db->select('ID')->get_where('db_academic.counseling_comment', array('TopicID' => $row['ID']))->result_array();


                    $dataToken = array(
                        'TopicID' => $row['ID'],
                        'TotalComment' => count($dataComment)
                    );

                    $ReadComment = (int) $row['ReadComment'];
                    $ur = count($dataComment) - $ReadComment;
                    $unread = ($ur > 0) ? ' - <span style="color: #ff5722;">' . $ur . ' unread comments</span>' : '';

                    $key = "s3Cr3T-G4N";
                    $token = $this->jwt->encode($dataToken, $key);

                    $tokenLecturer = $this->jwt->encode($dataTotalLecturer, $key);
                    $tokenStudent = $this->jwt->encode($dataTotalUser, $key);

                    $urlDetail = ($user == 'lecturer') ? url_sign_in_lecturers : url_sign_in_students;

                    // Get Owner
                    if ($row['InviteTo'] == '4') {
                        $dataOwner = $this->db->select('Name')->get_where('db_academic.auth_students', array('NPM' => $row['CreateBy']))->result_array()[0];
                    } else {
                        $dataOwner = $this->db->select('Name')->get_where('db_employees.employees', array('NIP' => $row['CreateBy']))->result_array()[0];
                    }

                    $topic = '<a href="' . $urlDetail . 'counseling/detail-topic/' . $token . '">' . $row['Topic'] . '</a>
                              <br/>
                              <span style="font-size: 12px;color: #9e9e9e;">Owner : ' . $dataOwner['Name'] . '</span>
                              <br/>
                              <span style="font-size: 12px;color: #9e9e9e;">' . date('D, d M Y', strtotime($row['CreateAt'])) . '</span>' . $unread . '
                              ';

                    $btnAction = ($row['CreateBy'] == $UserID)
                        ? '<button class="btn btn-sm btn-default btn-default-danger btn-act-delete" data-id="' . $row['ID'] . '"><i class="fa fa-trash"></i></button>'
                        : '-';

                    $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
                    $nestedData[] = '<div  style="text-align:left;">' . $topic . '</div>';
                    $nestedData[] = '<div  style="text-align:left;">
                                            <span style="font-size: 12px;">Lecturer : <a href="javascript:void(0);" data-id="' . $row['ID'] . '" class="a-user btnShowLecturerInDiscussionBoard">' . count($dataTotalLecturer) . '</a></span>
                                            <br/><span style="font-size: 12px;">Students : <a href="javascript:void(0);" data-id="' . $row['ID'] . '" class="a-user btnShowStudentInDiscussionBoard">' . count($dataTotalUser) . '</a></span>
                                            <textarea id="showLecturer' . $row['ID'] . '" class="hide" readonly>' . $tokenLecturer . '</textarea>
                                            <textarea id="showStudent' . $row['ID'] . '" class="hide" readonly>' . $tokenStudent . '</textarea>
                                            <input id="owner' . $row['ID'] . '" class="hide" value="' . $row['CreateBy'] . '" readonly/>
                                      </div>';
                    $nestedData[] = '<div  style="text-align:center;"><i class="fa fa-comments"></i> <span>' . count($dataComment) . '</span></div>';

                    if ($user == 'lecturer') {
                        $nestedData[] = '<div  style="text-align:center;">' . $btnAction . '</div>';
                    }


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
            } else if ($dataToken['action'] == 'readDetailTopic') {

                $dataTopic = $this->db->query('SELECT * FROM db_academic.counseling_topic ct 
                                                          WHERE ct.ID = "' . $dataToken['TopicID'] . '" LIMIT 1 ')->result_array();


                // Read Comment
                if (count($dataTopic) > 0) {

                    $dataSchedule = [];

                    // Jika Forum dalam timetable
                    if ($dataTopic[0]['InviteTo'] == '2' && $dataTopic[0]['InviteTo'] != '' && $dataTopic[0]['InviteTo'] != null) {
                        $ScheduleID = $dataTopic[0]['ScheduleID'];
                        $dataSchedule = $this->db->query('SELECT s.ClassGroup, mk.NameEng FROM db_academic.schedule s
                                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (s.ID = sdc.ScheduleID)
                                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                                        WHERE s.ID = "' . $ScheduleID . '" 
                                                                        GROUP BY s.ID')->result_array();
                    }

                    $dataTopic[0]['dataSchedule'] = $dataSchedule;

                    $dataComment = $this->db->query('SELECT cc.*, cu.Status, em.Name AS Lecturer, em.Photo AS EmPhoto , auts.Name AS Student, auts.Year FROM db_academic.counseling_comment cc 
                                                                LEFT JOIN db_academic.counseling_user cu ON (cu.TopicID = cc.TopicID AND cu.UserID = cc.UserID) 
                                                                LEFT JOIN db_academic.auth_students auts ON (auts.NPM = cc.UserID)
                                                                LEFT JOIN db_employees.employees em ON (em.NIP = cc.UserID)
                                                                WHERE cc.TopicID = "' . $dataToken['TopicID'] . '"
                                                                ORDER BY cc.ID ASC ')->result_array();

                    // Update total comment
                    $this->db->set('ReadComment', count($dataComment));
                    $this->db->where(array(
                        'TopicID' => $dataToken['TopicID'],
                        'UserID' => $dataToken['UserID']
                    ));
                    $this->db->update('db_academic.counseling_user');
                    $this->db->reset_query();

                    if (count($dataComment) > 0) {
                        for ($i = 0; $i < count($dataComment); $i++) {

                            if ($dataComment[$i]['Status'] == 2 || $dataComment[$i]['Status'] == '2') {
                                // Get Photo Student
                                $db_std = 'ta_' . $dataComment[$i]['Year'];
                                $dataPhoto = $this->db->query('SELECT Photo FROM ' . $db_std . '.students WHERE NPM = "' . $dataComment[$i]['UserID'] . '" LIMIT 1')->result_array();

                                $dataComment[$i]['Photo'] = url_img_students . '' . $db_std . '/' . $dataPhoto[0]['Photo'];
                            } else {
                                $dataComment[$i]['Photo'] = url_img_employees . '' . $dataComment[$i]['EmPhoto'];
                            }

                            if ($dataComment[$i]['CommentID'] != null && $dataComment[$i]['CommentID'] != '') {
                                $dataQuote = $this->db->query('SELECT cc.*, cu.Status, em.Name AS Lecturer, auts.Name AS Student FROM db_academic.counseling_comment cc 
                                                                LEFT JOIN db_academic.counseling_user cu ON (cu.TopicID = cc.TopicID AND cu.UserID = cc.UserID) 
                                                                LEFT JOIN db_academic.auth_students auts ON (auts.NPM = cc.UserID)
                                                                LEFT JOIN db_employees.employees em ON (em.NIP = cc.UserID)
                                                                WHERE cc.ID = "' . $dataComment[$i]['CommentID'] . '" ')->result_array();
                                $dataComment[$i]['Quote'] = $dataQuote;
                            }
                        }
                    }

                    $dataTopic[0]['Comment'] = $dataComment;
                }

                return print_r(json_encode($dataTopic));
            } else if ($dataToken['action'] == 'addComment') {

                $dataForm = (array) $dataToken['dataForm'];
                $this->db->insert('db_academic.counseling_comment', $dataForm);

                $TopicID = $dataForm['TopicID'];
                $UserID = $dataForm['UserID'];

                // Cek untuk mendapatkan ScheduleID & Session
                $dataCk = $this->db->get_where(
                    'db_academic.counseling_topic',
                    array('ID' => $TopicID)
                )->result_array();

                $ScheduleID = $dataCk[0]['ScheduleID'];
                $Sessions = $dataCk[0]['Sessions'];

                if (
                    $ScheduleID != '' && $ScheduleID != null &&
                    $Sessions != '' && $Sessions != null
                ) {

                    // Cek attendace online
                    $this->m_onlineclass->checkOnlineAttendance($UserID, $ScheduleID, $Sessions);
                }

                // push notification

                // cek apakah user mhs atau bukan
                $dataMhs = $this->db->select('Name')->get_where('db_academic.auth_students', array('NPM' => $UserID))
                    ->result_array();

                if (count($dataMhs) > 0) {
                    $Sender = $dataMhs[0]['Name'];
                } else {
                    $dataEmp = $this->db->select('Name,TitleAhead,TitleBehind')
                        ->get_where(
                            'db_employees.employees',
                            array('NIP' => $UserID)
                        )->result_array()[0];

                    $TitleAhead = ($dataEmp['TitleAhead'] != '' && $dataEmp['TitleAhead'] != null)
                        ? $dataEmp['TitleAhead'] . ' ' : '';

                    $TitleBehind = ($dataEmp['TitleBehind'] != '' && $dataEmp['TitleBehind'] != null)
                        ? ' ' . $dataEmp['TitleBehind'] : '';

                    $Sender = $TitleAhead . $dataEmp['Name'] . $TitleBehind;
                }

                // get mk name
                $dataMK = $this->db->query('SELECT s.ClassGroup, mk.NameEng FROM db_academic.schedule s 
                                                            LEFT JOIN db_academic.schedule_details_course sdc 
                                                            ON (s.ID = sdc.ScheduleID)
                                                            LEFT JOIN db_academic.mata_kuliah mk 
                                                            ON (mk.ID = sdc.MKID)
                                                            WHERE s.ID = "' . $ScheduleID . '"
                                                             GROUP BY s.ID ')->result_array()[0];

                $url = 'https://firebase.podomorouniversity.ac.id/pushnotification.php';
                $dataPush = array(
                    'type' => 'topic', // topic / token
                    'topic' => 'pu_course_' . $ScheduleID,
                    //                    'topic' => 'pu_emp_12',
                    'title' => 'Discussion - ' . $Sender,
                    'body' => $dataMK['ClassGroup'] . ' ' . $dataMK['NameEng']
                        . ' | ' . $dataForm['Comment']
                );

                $this->m_master->http_request_post_data($url, $dataPush);

                return print_r(1);
            } else if ($dataToken['action'] == 'inviteLecturer') {

                $TopicID = $dataToken['TopicID'];
                $formSelectLecturer = (array) $dataToken['formSelectLecturer'];

                if (count($formSelectLecturer) > 0) {
                    for ($i = 0; $i < count($formSelectLecturer); $i++) {
                        $arr = array(
                            'TopicID' => $TopicID,
                            'UserID' => $formSelectLecturer[$i],
                            'ReadComment' => 0,
                            'Status' => '1'
                        );

                        $this->db->insert('db_academic.counseling_user', $arr);
                    }
                }

                return print_r(1);
            } else if ($dataToken['action'] == 'removeLecturer') {
                $ID = $dataToken['CUID'];

                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.counseling_user');
                return print_r(1);
            } else if ($dataToken['action'] == 'deleteTopic') {
                $TopicID = $dataToken['TopicID'];
                $this->db->where('TopicID', $TopicID);
                $this->db->delete(array('db_academic.counseling_comment', 'db_academic.counseling_user'));
                $this->db->reset_query();

                $this->db->where('ID', $TopicID);
                $this->db->delete('db_academic.counseling_topic');

                return print_r(1);
            } else if ($dataToken['action'] == 'checkSessionsToNewTopic') {

                $ScheduleID = $dataToken['ScheduleID'];
                //                $ScheduleID = 393;


                $data = $this->db->query('SELECT ID,Sessions FROM db_academic.counseling_topic 
                    WHERE ScheduleID = "' . $ScheduleID . '" ')->result_array();

                // $dataCkOnline = $this->db->select('OnlineLearning,SemesterID')->get_where('db_academic.schedule', array('ID' => $ScheduleID))->result_array();

                $dataCkOnline = $this->db->query('SELECT s.OnlineLearning, ay.totalSession 
                                                FROM db_academic.schedule s 
                                                LEFT JOIN db_academic.academic_years ay ON (ay.SemesterID = s.SemesterID)
                                                WHERE s.ID = "' . $ScheduleID . '" ')->result_array();


                if ($dataCkOnline[0]['OnlineLearning'] == 1 || $dataCkOnline[0]['OnlineLearning'] == '1') {
                    $dataOpenDate = $this->m_rest->getRangeDateLearningOnline($ScheduleID);
                }


                $result = [];

                for ($i = 1; $i <= (int) $dataCkOnline[0]['totalSession']; $i++) {

                    $Status = 1;
                    $TotalComment = 0;
                    $TopicID = 0;

                    if (count($data) > 0) {
                        foreach ($data as $itm) {


                            if ($i == $itm['Sessions']) {
                                $dataComment = $this->db->select('ID')
                                    ->get_where(
                                        'db_academic.counseling_comment',
                                        array('TopicID' => $itm['ID'])
                                    )->result_array();
                                $TopicID = $itm['ID'];
                                $Status = -1;
                                $TotalComment = count($dataComment);
                            }
                        }
                    }

                    if ($dataCkOnline[0]['OnlineLearning'] == 1 || $dataCkOnline[0]['OnlineLearning'] == '1') {

                        $arr = array(
                            'Sessions' => ($i),
                            'Status' => $Status,
                            'isOnline' => 1,
                            'TopicID' => $TopicID,
                            'TotalComment' => $TotalComment,
                            'StatusOnline' => $dataOpenDate[$i - 1]['Status'],
                            'RangeStart' => $dataOpenDate[$i - 1]['RangeStart'],
                            'RangeEnd' => $dataOpenDate[$i - 1]['RangeEnd'],
                            'isUTS' => $dataOpenDate[$i - 1]['isUTS']

                        );
                    } else {
                        $arr = array(
                            'Sessions' => ($i),
                            'Status' => $Status,
                            'isOnline' => 0,
                            'TopicID' => $TopicID,
                            'TotalComment' => $TotalComment
                        );
                    }


                    array_push($result, $arr);
                }

                // Cek apakah online atau tidak





                return print_r(json_encode($result));
            }
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function crudTask()
    {
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);
        if ($cekUser) {
            if ($dataToken['action'] == 'checkSessionsToNewTask') {

                $ScheduleID = $dataToken['ScheduleID'];

                // $dataCkOnline = $this->db->select('OnlineLearning')->get_where('db_academic.schedule', array('ID' => $ScheduleID))->result_array();

                $dataCkOnline = $this->db->query('SELECT s.OnlineLearning, ay.totalSession 
                                                FROM db_academic.schedule s 
                                                LEFT JOIN db_academic.academic_years ay ON (ay.SemesterID = s.SemesterID)
                                                WHERE s.ID = "' . $ScheduleID . '" ')->result_array();

                if ($dataCkOnline[0]['OnlineLearning'] == 1 || $dataCkOnline[0]['OnlineLearning'] == '1') {
                    $dataOpenDate = $this->m_rest->getRangeDateLearningOnline($ScheduleID);
                }

                $result = [];
                for ($i = 1; $i <= (int) $dataCkOnline[0]['totalSession']; $i++) {


                    // Cek sudah ada atau blm
                    $dataCkSession = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.schedule_task  st
                                                            WHERE st.ScheduleID = "' . $ScheduleID . '"
                                                             AND st.Session = "' . $i . '" ')->result_array();

                    $Status = ($dataCkSession[0]['Total'] > 0) ? -1 : 1;

                    //                    $Status =  1;


                    if ($dataCkOnline[0]['OnlineLearning'] == 1 || $dataCkOnline[0]['OnlineLearning'] == '1') {

                        $arr = array(
                            'Sessions' => ($i),
                            'Status' => $Status,
                            'isOnline' => 1,
                            'StatusOnline' => $dataOpenDate[$i - 1]['Status'],
                            'RangeStart' => $dataOpenDate[$i - 1]['RangeStart'],
                            'RangeEnd' => $dataOpenDate[$i - 1]['RangeEnd'],
                            'isUTS' => $dataOpenDate[$i - 1]['isUTS']

                        );
                    } else {
                        $arr = array(
                            'Sessions' => ($i),
                            'Status' => $Status,
                            'isOnline' => 0
                        );
                    }

                    array_push($result, $arr);
                }

                return print_r(json_encode($result));
            } else if ($dataToken['action'] == 'checkSessionsInTask') {
                $ScheduleID = $dataToken['ScheduleID'];
                $Session = $dataToken['Session'];
                $NPM = (isset($dataToken['NPM']) && $dataToken['NPM'] != '') ? $dataToken['NPM'] : '';

                $dataCkOnline = $this->db->select('OnlineLearning')->get_where('db_academic.schedule', array('ID' => $ScheduleID))->result_array();
                $isOnline = 0;
                $RangeStart = '';
                $RangeEnd = '';
                $StatusOnline = '';
                if ($dataCkOnline[0]['OnlineLearning'] == 1 || $dataCkOnline[0]['OnlineLearning'] == '1') {
                    $dataOpenDate = $this->m_rest->getRangeDateLearningOnline($ScheduleID);
                    $isOnline = 1;
                    for ($i = 0; $i < count($dataOpenDate); $i++) {
                        if ($dataOpenDate[$i]['Session'] == $Session) {
                            $RangeStart = $dataOpenDate[$i]['RangeStart'];
                            $RangeEnd = $dataOpenDate[$i]['RangeEnd'];
                            $StatusOnline = $dataOpenDate[$i]['Status'];
                        }
                    }
                }



                $dataCkSession = $this->db->query('SELECT st.*, em.Name AS Lecturer FROM db_academic.schedule_task  st
                                                            LEFT JOIN db_employees.employees em ON (em.NIP = st.NIP)
                                                            WHERE st.ScheduleID = "' . $ScheduleID . '"
                                                             AND st.Session = "' . $Session . '" ')->result_array();

                if (count($dataCkSession) > 0) {
                    $d = $dataCkSession[0];
                    $whereNPM = ($NPM != '') ? ' AND sts.NPM = "' . $NPM . '" ' : '';
                    $dataCkSession[0]['Details'] = $this->db->query('SELECT sts.*, ats.Name FROM db_academic.schedule_task_student sts 
                                                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = sts.NPM)
                                                                    WHERE sts.IDST = "' . $d['ID'] . '" ' . $whereNPM)->result_array();
                }

                $result = array(
                    'isOnline' => $isOnline,
                    'RangeStart' => $RangeStart,
                    'RangeEnd' => $RangeEnd,
                    'StatusOnline' => $StatusOnline,
                    'Data' => $dataCkSession
                );

                return print_r(json_encode($result));
            } else if ($dataToken['action'] == 'updateScoreTask') {

                $this->db->set('Score', $dataToken['Score']);
                $this->db->where('ID', $dataToken['ID']);
                $this->db->update('db_academic.schedule_task_student');

                return print_r(1);
            }
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function crudQuiz()
    {
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);
        if ($cekUser) {
            if ($dataToken['action'] == 'checkSessionsToNewQuiz') {

                $ScheduleID = $dataToken['ScheduleID'];

                // $dataCkOnline = $this->db->select('OnlineLearning')
                //     ->get_where('db_academic.schedule', array('ID' => $ScheduleID))
                //     ->result_array();

                $dataCkOnline = $this->db->query('SELECT s.OnlineLearning, ay.totalSession 
                    FROM db_academic.schedule s 
                    LEFT JOIN db_academic.academic_years ay ON (ay.SemesterID = s.SemesterID)
                    WHERE s.ID = "' . $ScheduleID . '" ')->result_array();


                if ($dataCkOnline[0]['OnlineLearning'] == 1 || $dataCkOnline[0]['OnlineLearning'] == '1') {
                    $dataOpenDate = $this->m_rest->getRangeDateLearningOnline($ScheduleID);
                }

                $result = [];
                for ($i = 1; $i <= (int) $dataCkOnline[0]['totalSession']; $i++) {


                    // Cek sudah ada atau blm
                    $dataCkSession = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.q_quiz  q
                                                            WHERE q.ScheduleID = "' . $ScheduleID . '"
                                                             AND q.Session = "' . $i . '" ')->result_array();

                    $Status = ($dataCkSession[0]['Total'] > 0) ? -1 : 1;


                    if ($dataCkOnline[0]['OnlineLearning'] == 1 || $dataCkOnline[0]['OnlineLearning'] == '1') {

                        $arr = array(
                            'Sessions' => ($i),
                            'Status' => $Status,
                            'isOnline' => 1,
                            'StatusOnline' => $dataOpenDate[$i - 1]['Status'],
                            'RangeStart' => $dataOpenDate[$i - 1]['RangeStart'],
                            'RangeEnd' => $dataOpenDate[$i - 1]['RangeEnd'],
                            'isUTS' => $dataOpenDate[$i - 1]['isUTS']

                        );
                    } else {
                        $arr = array(
                            'Sessions' => ($i),
                            'Status' => $Status,
                            'isOnline' => 0
                        );
                    }

                    array_push($result, $arr);
                }

                return print_r(json_encode($result));
            } else if ($dataToken['action'] == 'countTotalMyQuestion') {
                $NIP = $dataToken['NIP'];
                $dataQuiz = $this->db->query('SELECT COUNT(*) Total FROM db_academic.q_question 
                                        WHERE CreatedBy = "' . $NIP . '" ')->result_array();

                return print_r(json_encode(array('Total' => $dataQuiz[0]['Total'])));
            } else if ($dataToken['action'] == 'checkSessionsInQuiz') {
                $ScheduleID = $dataToken['ScheduleID'];
                $Session = $dataToken['Session'];
                $NPM = (isset($dataToken['NPM']) && $dataToken['NPM'] != '') ? $dataToken['NPM'] : '';

                $dataCkOnline = $this->db->select('OnlineLearning')->get_where('db_academic.schedule', array('ID' => $ScheduleID))->result_array();
                $isOnline = 0;
                $RangeStart = '';
                $RangeEnd = '';
                $StatusOnline = '';
                if ($dataCkOnline[0]['OnlineLearning'] == 1 || $dataCkOnline[0]['OnlineLearning'] == '1') {
                    $dataOpenDate = $this->m_rest->getRangeDateLearningOnline($ScheduleID);
                    $isOnline = 1;
                    for ($i = 0; $i < count($dataOpenDate); $i++) {
                        if ($dataOpenDate[$i]['Session'] == $Session) {
                            $RangeStart = $dataOpenDate[$i]['RangeStart'];
                            $RangeEnd = $dataOpenDate[$i]['RangeEnd'];
                            $StatusOnline = $dataOpenDate[$i]['Status'];
                        }
                    }
                }



                $dataCkSession = $this->db->query('SELECT q.*, em.Name AS Lecturer FROM db_academic.q_quiz  q
                                                            LEFT JOIN db_employees.employees em ON (em.NIP = q.CreatedBy)
                                                            WHERE q.ScheduleID = "' . $ScheduleID . '"
                                                             AND q.Session = "' . $Session . '" ')->result_array();

                $dateNow = $this->m_rest->getDateNow();
                if (count($dataCkSession) > 0) {
                    $d = $dataCkSession[0];

                    // Cek apakah tanggal sekarang masuk dalam range pengerjaan
                    $dataCkSession[0]['dateInRange'] = ($dateNow >= $RangeStart && $dateNow <= $RangeEnd) ? 1 : 0;


                    $whereNPM = ($NPM != '') ? ' AND qs.NPM = "' . $NPM . '" ' : '';
                    $Details = $this->db->query('SELECT qs.*, ats.Name FROM db_academic.q_quiz_students qs
                                                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = qs.NPM)
                                                                    WHERE qs.QuizID = "' . $d['ID'] . '" ' . $whereNPM)->result_array();

                    if (count($Details) > 0) {
                        if ($Details[0]['ShowScore'] == '0' || $Details[0]['ShowScore'] == 0) {
                            unset($Details[0]['Score']);
                        }
                    }
                    $dataCkSession[0]['Details'] = $Details;

                    // Cek totak soal
                    $dataCkSession[0]['TotalQuestion'] = $this->db->query('SELECT COUNT(*) AS Total 
                                                                    FROM db_academic.q_quiz_details
                                                                    WHERE QuizID = "' . $dataCkSession[0]['ID'] . '" ')
                        ->result_array()[0]['Total'];
                }

                $result = array(
                    'isOnline' => $isOnline,
                    'RangeStart' => $RangeStart,
                    'RangeEnd' => $RangeEnd,
                    'StatusOnline' => $StatusOnline,
                    'Data' => $dataCkSession
                );

                return print_r(json_encode($result));
            } else if ($dataToken['action'] == 'getDataQuiz') {
                $QuizID = $dataToken['QuizID'];
                $NPM = $dataToken['NPM'];

                $data = $this->db->query('SELECT q.ID, q.Duration, q.ForExam , qs.ID AS QuizStudentID,   
                                                    qs.StartSession, qs.EndSession,
                                                    qs.WorkDuration, qs.SubmittedAt, q.NotesForStudents
                                                    FROM db_academic.q_quiz q
                                                    LEFT JOIN db_academic.q_quiz_students qs 
                                                    ON (qs.QuizID = q.ID AND qs.NPM = "' . $NPM . '")
                                                    WHERE q.ID = "' . $QuizID . '"')->result_array();

                if (count($data) > 0) {

                    $dataTimeNow = $this->m_rest->getDateTimeNow();

                    $data[0]['NowSession'] =  $dataTimeNow;
                    $data[0]['Status'] = ($dataTimeNow <= $data[0]['EndSession']) ? 1 : 0;
                    $DurationQuiz = 0;

                    if ($data[0]['Status'] == 1) {
                        $to_time = strtotime($data[0]['EndSession']);
                        $from_time = strtotime($dataTimeNow);
                        $DurationQuiz = round(abs($to_time - $from_time) / 60);
                    }

                    $data[0]['DurationQuiz'] = $DurationQuiz;



                    $joinWithResult = (isset($dataToken['action2']) && $dataToken['action2'] == 'getDataQuiz2GetResume')
                        ? ' LEFT JOIN db_academic.q_quiz_students_details qsd ON (qsd.QID = q.ID AND qsd.QuizStudentID = "' . $data[0]['QuizStudentID'] . '") ' : '';

                    $selectWithResult = (isset($dataToken['action2']) && $dataToken['action2'] == 'getDataQuiz2GetResume')
                        ? ', qsd.Point AS PointAswer, qsd.EssayAnswer ' : '';


                    $Question = $this->db->query('SELECT qqd.QID, qqd.Point, q.Question, q.QTID,  
                                                            qt.Description, qqd.ID AS QuizDetailID
                                                             ' . $selectWithResult . '
                                                            FROM db_academic.q_quiz_details qqd 
                                                            LEFT JOIN db_academic.q_question q ON (qqd.QID = q.ID)
                                                            LEFT JOIN db_academic.q_question_type qt ON (qt.ID = q.QTID)
                                                            ' . $joinWithResult . '
                                                            WHERE qqd.QuizID = "' . $QuizID . '" ')->result_array();

                    if (count($Question) > 0) {
                        for ($q = 0; $q < count($Question); $q++) {
                            $Options = $this->db->select('ID,Option')->order_by('ID', 'RANDOM')->get_where(
                                'db_academic.q_question_options',
                                array('QID' => $Question[$q]['QID'])
                            )->result_array();
                            $Question[$q]['Options'] = $Options;

                            if (isset($dataToken['action2']) && $dataToken['action2'] == 'getDataQuiz2GetResume') {
                                $answer = $this->db->query('SELECT qso.QOptionID, qso.PointOption, qso.Status  FROM db_academic.q_quiz_students_option qso 
                                                                        LEFT JOIN db_academic.q_quiz_students_details qsd
                                                                        ON (qsd.ID = qso.QuizStudentsDetailsID)
                                                                        WHERE qsd.QuizStudentID = "' . $data[0]['QuizStudentID'] . '" 
                                                                        AND qsd.QID = "' . $Question[$q]['QID'] . '" ')->result_array();

                                $arrAnswer = [];
                                if (count($answer) > 0) {
                                    for ($ans = 0; $ans < count($answer); $ans++) {
                                        array_push($arrAnswer, $answer[$ans]['QOptionID']);
                                    }
                                }

                                $Question[$q]['Answer'] = $arrAnswer;
                            }
                        }
                    }

                    $data[0]['Question'] = $Question;
                }

                return print_r(json_encode($data));
            } else if ($dataToken['action'] == 'startMyQuiz') {
                $QuizID = $dataToken['QuizID'];
                $Duration = $dataToken['Duration'];
                $NPM = $dataToken['NPM'];

                // cek apakah sudah ada atau blm jika sudah maka tidak perlu update
                $dataCk = $this->db->get_where(
                    'db_academic.q_quiz_students',
                    array('NPM' => $NPM, 'QuizID' => $QuizID)
                )->result_array();

                if (count($dataCk) <= 0) {

                    $StartSession = $this->m_rest->getDateTimeNow();
                    $time = strtotime($StartSession);
                    $EndSession = date('Y-m-d H:i:s', strtotime('+' . $Duration . ' minutes', $time));
                    $dataInsert = array(
                        'NPM' => $NPM,
                        'QuizID' => $QuizID,
                        'StartSession' => $StartSession,
                        'EndSession' => $EndSession
                    );
                    $this->db->insert('db_academic.q_quiz_students', $dataInsert);
                }

                return print_r(1);
            } else if ($dataToken['action'] == 'saveAnswerQuiz') {
                //                print_r($dataToken);

                // Hapus jika ada datanya
                $this->db->where('QuizStudentID', $dataToken['QuizStudentID']);
                $this->db->delete(array(
                    'db_academic.q_quiz_students_details',
                    'db_academic.q_quiz_students_option'
                ));
                $this->db->reset_query();

                $dataQuestion = $dataToken['dataQuestion'];
                $ScoreSementara = 0;
                $ShowScore = '1';
                if (count($dataQuestion) > 0) {
                    for ($i = 0; $i < count($dataQuestion); $i++) {
                        $d = (array) $dataQuestion[$i];

                        if ($d['QTID'] == 3 || $d['QTID'] == '3') {
                            $ShowScore = '0';
                        }

                        $arrInsert = array(
                            'QuizStudentID' => $dataToken['QuizStudentID'],
                            'QuizDetailID' => $d['QuizDetailID'],
                            'QID' => $d['QID'],
                            'QTID' => $d['QTID'],
                            'EssayAnswer' => $d['EssayAnswer']
                        );

                        $this->db->insert('db_academic.q_quiz_students_details', $arrInsert);
                        $QuizStudentsDetailsID = $this->db->insert_id();

                        if (count($d['Options']) > 0) {

                            // Multiple Choise Type B
                            $TotalPointOption = 0;

                            for ($a = 0; $a < count($d['Options']); $a++) {
                                $d2 = (array) $d['Options'][$a];
                                // Cek apakah option benar atau salah
                                $dataCkOpt = $this->db->get_where(
                                    'db_academic.q_question_options',
                                    array('ID' => $d2['QOptionID'])
                                )->result_array()[0];

                                $arrInstOption = array(
                                    'QuizStudentID' => $dataToken['QuizStudentID'],
                                    'QuizStudentsDetailsID' => $QuizStudentsDetailsID,
                                    'QOptionID' => $d2['QOptionID'],
                                    'PointOption' => $dataCkOpt['Point'],
                                    'Status' => $dataCkOpt['IsTheAnswer']
                                );

                                $this->db->insert('db_academic.q_quiz_students_option', $arrInstOption);
                                $PointQuestion = 0;
                                if ($d['QTID'] == 2 || $d['QTID'] == '2') {
                                    $TotalPointOption = $TotalPointOption + (float) $dataCkOpt['Point'];

                                    if ($a == (count($d['Options']) - 1)) {
                                        $PointQuestion = ($TotalPointOption > 0)
                                            ? str_replace(',', '.', '' . ((float) $d['Point'] / 100) * $TotalPointOption)
                                            : 0;
                                        $this->db->where('ID', $QuizStudentsDetailsID);
                                        $this->db->update(
                                            'db_academic.q_quiz_students_details',
                                            array('Point' => $PointQuestion)
                                        );
                                        $this->db->reset_query();
                                    }
                                } else if ($d['QTID'] == 1 || $d['QTID'] == '1') {
                                    // Update point langsung karena multiple choise type A
                                    $PointQuestion = ($dataCkOpt['IsTheAnswer'] == 1 || $dataCkOpt['IsTheAnswer'] == '1')
                                        ? $d['Point'] : 0;
                                    $this->db->where('ID', $QuizStudentsDetailsID);
                                    $this->db->update(
                                        'db_academic.q_quiz_students_details',
                                        array('Point' => $PointQuestion)
                                    );
                                    $this->db->reset_query();
                                }

                                $ScoreSementara = $ScoreSementara + $PointQuestion;
                            }
                        }
                    }

                    $this->db->where('ID', $dataToken['QuizStudentID']);
                    $this->db->update(
                        'db_academic.q_quiz_students',
                        array(
                            'Score' => str_replace(',', '.', $ScoreSementara),
                            'ShowScore' => $ShowScore,
                            'WorkDuration' => $dataToken['WorkDuration'],
                            'SubmittedAt' => $this->m_rest->getDateTimeNow()
                        )
                    );
                    $this->db->reset_query();
                }



                // return quiz
                $dataReturn = $this->db->query('SELECT s.SemesterID, q.ScheduleID, q.Session,qs.NPM  
                                                FROM db_academic.q_quiz q
                                                LEFT JOIN db_academic.q_quiz_students qs 
                                                    ON (qs.QuizID = q.ID )
                                                LEFT JOIN db_academic.schedule s 
                                                    ON (s.ID = q.ScheduleID)
                                                WHERE qs.ID = "' . $dataToken['QuizStudentID'] . '" ')
                    ->result_array()[0];

                // Cek attendace online
                $this->m_onlineclass->checkOnlineAttendance(
                    $dataReturn['NPM'],
                    $dataReturn['ScheduleID'],
                    $dataReturn['Session']
                );

                return print_r(json_encode($dataReturn));
            } else if ($dataToken['action'] == 'updateScoreTask') {

                $this->db->set('Score', $dataToken['Score']);
                $this->db->where('ID', $dataToken['ID']);
                $this->db->update('db_academic.schedule_task_student');

                return print_r(1);
            }
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function getPaymentStudent()
    {
        $this->load->model('finance/m_finance');
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if ($cekUser) {

            $data = $this->db->query('SELECT p.*, pt.Description, s.Name AS SemesterName, sa.Name AS SemesterAntaraName, pt.Type AS TypePT FROM db_finance.payment p 
                                                    LEFT JOIN db_finance.payment_type pt ON (pt.ID = p.PTID)
                                                    LEFT JOIN db_academic.semester s ON (s.ID = p.SemesterID AND pt.Type="0")
                                                    LEFT JOIN db_academic.semester_antara sa ON (sa.ID = p.SemesterID AND pt.Type="1")
                                                    WHERE p.NPM = "' . $dataToken['NPM'] . '" ORDER BY pt.Type DESC, p.SemesterID DESC')->result_array();

            //            print_r($data);exit;

            //            print_r($data);

            $result = [];
            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {

                    if ($data[$i]['TypePT'] == '1') {
                        $data[$i]['SemesterName'] = $data[$i]['SemesterAntaraName'];

                        // Cek semester antara
                        $dataSmt_a = $this->db->query('SELECT SemesterID FROM db_academic.semester_antara WHERE ID = "' . $data[$i]['SemesterID'] . '" ')->result_array();

                        $dataSmt = $this->db->query('SELECT * FROM db_academic.semester WHERE Year >= "' . $dataToken['ClassOf'] . '" 
                                                    AND ID <= "' . $dataSmt_a[0]['SemesterID'] . '" ')->result_array();
                    } else {
                        // Cek semester
                        $dataSmt = $this->db->query('SELECT * FROM db_academic.semester WHERE Year >= "' . $dataToken['ClassOf'] . '" 
                                                    AND ID <= "' . $data[$i]['SemesterID'] . '" ')->result_array();
                    }

                    //Cek Bukti Upload
                    $payment_proof = $this->m_master->caribasedprimary('db_finance.payment_proof', 'ID_payment', $data[$i]['ID']);
                    $data[$i]['payment_proof'] = $payment_proof;

                    // potongan lain
                    $data[$i]['potonganLain'] = $this->m_finance->getPotonganLain($data[$i]['PTID'], $data[$i]['SemesterID'], $data[$i]['NPM']);

                    if (count($dataSmt) > 0) {
                        $data[$i]['Semester'] = count($dataSmt);
                        $datapay = $this->db->query('SELECT ID, Invoice, Status,Deadline,DatePayment FROM db_finance.payment_students WHERE ID_payment = "' . $data[$i]['ID'] . '" ')->result_array();

                        if (count($datapay) > 0) {
                            for ($k = 0; $k < count($datapay); $k++) {
                                $ID_payment_students = $datapay[$k]['ID'];

                                // subcicilan
                                $dataPembayaran = $this->m_master->caribasedprimary('db_finance.payment_student_details', 'ID_payment_students', $ID_payment_students);
                                $datapay[$k]['dataPembayaran'] = $dataPembayaran;
                            }
                        } else {
                            $datapay[$k]['dataPembayaran'] = [];
                        }


                        $data[$i]['DetailPay'] = $datapay;
                        array_push($result, $data[$i]);
                    }
                }
            }

            //            usort($result, function ($a, $b){return strcmp($a['Semester'], $b['Semester']);});

            return print_r(json_encode($result));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function getTableData($db = null, $table = null)
    {
        error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $json = array();
                if (($db != null || $db != '') && ($table != null || $table != '')) {
                    $json = $this->m_master->showData_array($db . '.' . $table);
                }
                echo json_encode($json);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function rule_service()
    {
        error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $json = $this->m_master->getData_rule_service();
                echo json_encode($json);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function rule_users()
    {
        error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $json = $this->datatableSSRuleUser();
                echo json_encode($json);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    private function datatableSSRuleUser()
    {
        $requestData = $_REQUEST;
        $gettotalData = function ($requestData) {
            $sql = 'select count(*) as total from (
                        select a.ID,a.NIP,a.IDDivision,a.privilege,b.Name,c.Division from
                        db_employees.rule_users as a left join 
                        db_employees.employees as b on 
                        a.NIP = b.NIP 
                        left join db_employees.division as c 
                        on a.IDDivision = c.ID
                        where 
                        b.Name like "%' . $requestData['search']['value'] . '%" or a.NIP like "%' . $requestData['search']['value'] . '%" or c.Division like "%' . $requestData['search']['value'] . '%"
                )aa';
            $query = $this->db->query($sql, array())->result_array();
            return $query[0]['total'];
        };
        $totalData = $gettotalData($requestData);
        $sql = 'select a.ID,a.NIP,a.IDDivision,a.privilege,b.Name,c.Division from
                db_employees.rule_users as a left join 
                db_employees.employees as b on 
                a.NIP = b.NIP 
                left join db_employees.division as c 
                on a.IDDivision = c.ID
                where 
                b.Name like "%' . $requestData['search']['value'] . '%" or a.NIP like "%' . $requestData['search']['value'] . '%" or c.Division like "%' . $requestData['search']['value'] . '%"';
        $sql .= ' order by a.NIP desc LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        $query = $this->db->query($sql)->result_array();

        $data = array();
        $No = $requestData['start'] + 1;
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];
            $nestedData[] = $No;
            $nestedData[] = $row['NIP'] . ' || ' . $row['Name'];
            $nestedData[] = $row['Division'];
            $action = '<button type="button" class="btn btn-danger btn-delete" data-sbmt="' . $row['ID'] . '"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>';

            $nestedData[] = $action;
            $nestedData[] = $row['NIP'];
            $nestedData[] = $row['IDDivision'];
            $nestedData[] = $row['ID'];
            $data[] = $nestedData;
            $No++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData),
            "data"            => $data
        );
        return $json_data;
        // echo json_encode($json_data);
    }

    public function getEmployees($Status = 'aktif')
    {
        //error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                if ($Status == 'aktif') {
                    $AddSql = ' where StatusEmployeeID not in (-1,-2,4,6)';
                } else {
                    $AddSql = ' where StatusEmployeeID = "' . $Status . '"';
                }

                $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                            ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status, em.Address, ems.Description, em.StatusEmployeeID
                            FROM db_employees.employees em 
                            LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                            LEFT JOIN db_employees.employees_status ems ON (ems.IDStatus = em.StatusEmployeeID) 
                            ';

                $sql .= $AddSql;
                $sql .= ' UNION
                        select NIK as NIP,NULL,NULL,Name,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL
                        from db_employees.holding
                        ';
                $sql .= 'order by NIP asc';
                $query = $this->db->query($sql, array())->result_array();

                echo json_encode($query);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function loadDataFormulirGlobal()
    {
        // error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $where = (!array_key_exists("division", $dataToken)) ? ' where a.Years = "' . $dataToken['selectTahun'] . '"' : ' where a.Division ="' . $dataToken['division'] . '" and a.Years = "' . $dataToken['selectTahun'] . '" ';
                $sql = 'SELECT a.*,b.FormulirCode,c.Division,c.Description from db_admission.formulir_number_global as a left join 
                    (
                        select ID,Years,FormulirCode,StatusJual as Status,No_Ref from db_admission.formulir_number_offline_m
                        UNION
                        select ID,Years,FormulirCode,Status,No_Ref from db_admission.formulir_number_online_m
                    )
                    b on a.FormulirCodeGlobal = b.No_Ref 
                    join db_employees.division as c on a.Division = c.ID
                    ' . $where . ' group by a.FormulirCodeGlobal';
                $query = $this->db->query($sql, array())->result_array();
                echo json_encode($query);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function loadDataFormulirGlobal_available()
    {
        // error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $Ta = $this->m_master->showData_array('db_admission.set_ta');
                $Ta = $Ta[0]['Ta'];
                $where = (!array_key_exists("division", $dataToken)) ? ' where a.Status = 0 and a.Years = "' . $Ta . '"' : ' where a.Division ="' . $dataToken['division'] . '" and a.Status = 0 and a.Years = "' . $Ta . '"';
                $sql = 'SELECT a.*,b.FormulirCode from db_admission.formulir_number_global as a left join db_admission.formulir_number_offline_m as b on a.FormulirCodeGlobal = b.No_Ref' . $where . ' group by a.FormulirCodeGlobal';
                $query = $this->db->query($sql, array())->result_array();
                echo json_encode($query);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function loadDataFormulirGlobal_available_new()
    {
        // error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $Ta = $this->m_master->showData_array('db_admission.set_ta');
                $Ta = $Ta[0]['Ta'];
                $where = '';
                $where = (!array_key_exists("division", $dataToken)) ? ' where a.Status = 0 ' : ' where a.Division ="' . $dataToken['division'] . '" and a.Status = 0';
                if (array_key_exists('action', $dataToken)) {
                    if ($dataToken['action'] == 'add') {
                        $q_add = ($where == '') ? ' where ' : ' and ';
                        $where .= $q_add . ' a.Years = ' . $Ta;
                    }
                }
                if (array_key_exists('TypeFormulir', $dataToken)) {
                    $q_add = ($where == '') ? ' where ' : ' and ';
                    $where .= $q_add . ' a.TypeFormulir = "' . $dataToken['TypeFormulir'] . '"';
                }
                $sql = 'SELECT a.* from db_admission.formulir_number_global as a ' . $where . ' group by a.FormulirCodeGlobal';
                $query = $this->db->query($sql, array())->result_array();
                echo json_encode($query);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function rekapintake()
    {
        // error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('statistik/m_statistik');
                $Year = $dataToken['Year'];
                $result = $this->m_statistik->ShowRekapIntake($Year);
                echo json_encode($result);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function rekapintake_reset()
    {
        $data = file_get_contents('php://input');

        $data_json = json_decode($data, true);

        if (!$data_json) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        } else {
            try {
                $getData = $data_json['data'];
                $token = $getData;
                $key = "UAP)(*";
                $dataToken = (array) $this->jwt->decode($token, $key);
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('statistik/m_statistik');
                    $Year = $dataToken['Year'];
                    if ($dataToken['action'] == 'reset') {
                        // drop table
                        $this->m_statistik->droptablerekapintake($Year);
                        // rekap intake admission
                        // special untuk data 2018 inject summary to db maka akan di skip
                        if ($Year != 2018) {
                            $this->m_statistik->droptablerekapintake_admission($Year);
                        }
                    }
                    //$this->m_statistik->droptablerekapintake($Year);
                    $result = $this->m_statistik->ShowRekapIntake($Year);
                    // rekap intake admission
                    // special untuk data 2018 inject summary to db maka akan di skip
                    if ($Year != 2018) {
                        $result = $this->m_statistik->ShowRekapIntake_admission($Year);
                    }

                    echo '{"status":"000"}';
                } else {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            } catch (Exception $e) {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
    }

    public function rekapintake_reset_client()
    {
        // error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('statistik/m_statistik');
                $Year = $dataToken['Year'];
                if ($dataToken['action'] == 'reset') {
                    // drop table
                    $this->m_statistik->droptablerekapintake($Year);
                }
                $result = $this->m_statistik->ShowRekapIntake($Year);
                echo json_encode($result);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function trigger_formulir()
    {
        $data = file_get_contents('php://input');

        $data_json = json_decode($data, true);

        if (!$data_json) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        } else {
            try {
                $getData = $data_json['data'];
                $token = $getData;
                $key = "UAP)(*";
                $dataToken = (array) $this->jwt->decode($token, $key);
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('statistik/m_statistik');
                    $ta = $dataToken['ta'];
                    // month & year
                    $month = $dataToken['month'];
                    $year = $dataToken['year'];
                    $ProdiID = $dataToken['ProdiID'];
                    $action = $dataToken['action'];
                    $this->m_statistik->trigger_formulir($ta, $month, $year, $ProdiID, $action);
                    echo '{"status":"000"}';
                } else {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            } catch (Exception $e) {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
    }

    public function rekapintake_beasiswa()
    {
        // error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('statistik/m_statistik');
                $Year = $dataToken['Year'];
                if (array_key_exists("action", $dataToken)) {
                    if ($dataToken['action'] == 'reset') {
                        $tblname = 'rekapintake_bea_' . $Year;
                        // drop table
                        $this->m_statistik->droptable($tblname);
                    }
                }
                $result = $this->m_statistik->ShowRekapIntake_Beasiswa($Year);
                echo json_encode($result);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function rekapintake_perschool()
    {
        // error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('statistik/m_statistik');
                $Year = $dataToken['Year'];
                if (array_key_exists("action", $dataToken)) {
                    if ($dataToken['action'] == 'reset') {
                        $tblname = 'rekapintake_sch_' . $Year;
                        // drop table
                        $this->m_statistik->droptable($tblname);
                    }
                }
                $result = $this->m_statistik->ShowRekapIntake_School($Year);
                echo json_encode($result);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function rekapmhspayment()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('statistik/m_statistik');
                if (array_key_exists("action", $dataToken)) {
                    if ($dataToken['action'] == 'reset') {
                        $tblname = 'summary_payment_mhs';
                        // drop table
                        $this->m_statistik->droptable($tblname);
                    }
                }
                $result = $this->m_statistik->ShowRekap_summary_payment_mhs();
                echo json_encode($result);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function sendEmail()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('m_sendemail');
                $arr = array('to', 'subject', 'text');
                $bool = true;
                foreach ($dataToken as $key => $value) {
                    if ($key != 'auth' && $key != 'attach') {
                        if (!in_array($key, $arr)) {
                            $bool = false;
                            $msg = 'Field is not match, the field is : ' . $key;
                            break;
                        }
                    }
                }

                if ($bool) {
                    $to = $dataToken['to'];
                    $subject = $dataToken['subject'];
                    $text = $dataToken['text'];
                    if (array_key_exists('attach', $dataToken)) {
                        $path = $dataToken['attach'];
                        $sendEmail = $this->m_sendemail->sendEmail($to, $subject, null, null, null, null, $text, $path);
                    } else {
                        $sendEmail = $this->m_sendemail->sendEmail($to, $subject, null, null, null, null, $text);
                    }

                    $msg =  $sendEmail['msg'];
                }

                echo json_encode($msg);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function v_reservation_json_list_booking()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('vreservation/m_reservation');
                if (array_key_exists("DivisionID", $dataToken)) {
                    $getData = $this->m_reservation->getDataT_booking_Api($dataToken['ID_t_booking'], $dataToken['DivisionID']);
                } else {
                    $getData = $this->m_reservation->getDataT_booking_Api($dataToken['ID_t_booking'], 17);
                }

                echo json_encode($getData);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function remind_vreservation()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                // action 
                $this->load->model('vreservation/m_reservation');
                $Q_remind_vreservation = $this->m_reservation->remind_vreservation();
                echo json_encode($msg);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function venue__fill_feedback()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('vreservation/m_reservation');
                $this->load->model('m_sendemail');
                // get data done besar sama dengan hari ini dan kecil sama dengan dua hari lagi
                $gg = $this->m_reservation->venue__fill_feedback();
                // ambil email requester / created by untuk email.
                // body email berisi token dengan auth via get dan passing ID
                if (count($gg) > 0) {
                    for ($i = 0; $i < count($gg); $i++) { // and grouping by Created by
                        $data = array();
                        $data[] = $gg[$i];
                        $Email = $gg[$i]['EmailPU'];
                        $CreatedBy = $gg[$i]['CreatedBy'];
                        $Name = $gg[$i]['Name'];
                        $DateLimit = $gg[$i]['Datelimit'] . ' 23:59:59';
                        $DateLimitCreated = DateTime::createFromFormat('Y-m-d', $gg[$i]['Datelimit']);
                        // print_r($DateLimit);die();
                        $NameDayDateLimit = $DateLimitCreated->format('l');
                        for ($j = $i + 1; $j < count($gg); $j++) {
                            if ($CreatedBy == $gg[$j]['CreatedBy']) {
                                $data[] = $gg[$j];
                            } else {
                                break;
                            }
                            $i = $j;
                        }

                        $token = array(
                            'CreatedBy' => $CreatedBy,
                            'data' => $data,
                            'auth' => 's3Cr3T-G4N',
                            'Datelimit' => $gg[$i]['Datelimit'],
                        );
                        $token = $this->jwt->encode($token, 'UAP)(*');
                        $text = 'Dear Mr/Mrs ' . $Name . ',<br><br>
                                    Thanks for using Venue Reservation Apps.<br><br>
                                    Please give me feedback about Room which you are  using with click View Button below.<br>
                                    <table width="200" cellspacing="0" cellpadding="12" border="0">
                                         <tbody>
                                         <tr>
                                             <td bgcolor="#51a351" align="center">
                                                 <a href="' . url_pas . 'vreservation/feedback/' . $token . '" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >View</a>
                                             </td>
                                         </tr>
                                         </tbody>
                                     </table><br><br>
                                     <strong> Link will be deactive on ' . $NameDayDateLimit . ',' . $DateLimit . '
                                ';
                        if ($_SERVER['SERVER_NAME'] == 'localhost') {
                            $to = 'alhadi.rahman@podomorouniversity.ac.id';
                            $subject = "Podomoro University Venue Reservation Feedback";
                            $sendEmail = $this->m_sendemail->sendEmail($to, $subject, null, null, null, null, $text);
                        } else {
                            $to = $Email;
                            $subject = "Podomoro University Venue Reservation Feedback";
                            $sendEmail = $this->m_sendemail->sendEmail($to, $subject, null, null, null, null, $text);
                        }
                        //print_r($data);
                    }
                }

                echo json_encode($gg);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    private function get_content($url, $post = '')
    {
        $usecookie = __DIR__ . "/cookie.txt";
        $header[] = 'Content-Type: application/json';
        $header[] = "Accept-Encoding: gzip, deflate";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        // curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $rs = curl_exec($ch);

        if (empty($rs)) {
            var_dump($rs, curl_error($ch));
            curl_close($ch);
            return false;
        }
        curl_close($ch);
        return $rs;
    }

    public function catalog__get_item()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                if ($dataToken['action'] == 'forUser') {
                    $Q_active = 'a.Active like "%%"';
                } else {
                    $Q_active = 'a.Active = 1';
                }

                $condition = ($dataToken['department'] == 'all') ? '' : ' and a.Departement = "' . $dataToken['department'] . '"';
                // get to assign department
                if ($dataToken['action'] == 'choices' && $dataToken['department'] != 'all') {
                    $condition = ' and ( a.ID in (select ID_m_catalog from db_purchasing.m_catalog_division where Departement = "' . $dataToken['department'] . '" ) or a.Departement = "' . $dataToken['department'] . '" )';
                }

                $add_approval = '';
                if (array_key_exists('approval', $dataToken)) {
                    $add_approval = ' and a.Approval =' . $dataToken['approval'];
                }

                if (array_key_exists('dtGetCatalogChoice', $dataToken)) {
                    $dtGetCatalogChoice = (array) json_decode(json_encode($dataToken['dtGetCatalogChoice']), true);
                    if (count($dtGetCatalogChoice) > 0) {
                        $implode = implode(',', $dtGetCatalogChoice);
                        $condition .= ' and a.ID NOT IN (' . $implode . ')';
                    }
                }

                $sql = 'select a.*,b.Name as NameCreated,c.NameDepartement,mcc.Name as NameCategory,mcc.Days
                        from db_purchasing.m_catalog as a
                        join db_purchasing.m_category_catalog as mcc on mcc.ID =  a.ID_category_catalog
                        join db_employees.employees as b on a.CreatedBy = b.NIP
                        join (
                        select * from (
                        select CONCAT("AC.",ID) as ID, CONCAT("Prodi ",NameEng) as NameDepartement from db_academic.program_study
                        UNION
                        select CONCAT("NA.",ID) as ID, Division as NameDepartement from db_employees.division where StatusDiv = 1
                        UNION
                        select CONCAT("FT.",ID) as ID, CONCAT("Faculty ",NameEng) as NameDepartement from db_academic.faculty
                        ) aa
                        ) as c on a.Departement = c.ID
                       ';

                $sql .= ' where ' . $Q_active . ' ' . $condition . $add_approval;
                $query = $this->db->query($sql)->result_array();
                $data = array();
                for ($i = 0; $i < count($query); $i++) {
                    $nestedData = array();
                    $row = $query[$i];
                    $nestedData[] = $i + 1;
                    $nestedData[] = $row['Item'] . '<br><span style = "color : red" >' . $row['NameCategory'] . '</span>';
                    $nestedData[] = $row['Desc'];
                    $EstimaValue = $row['EstimaValue'];
                    $EstimaValue = 'Rp ' . number_format($EstimaValue, 2, ',', '.') . '<br>' . '<span style = "color : red">Last Updated<br>' . $row['LastUpdateAt'] . '</span>';
                    $nestedData[] = $EstimaValue;
                    $Photo = $row['Photo'];
                    // print_r($Photo);
                    if ($Photo != '') {
                        // print_r('test');
                        $Photo = explode(",", $Photo);
                        $htmlPhoto = '<ul>';
                        for ($z = 0; $z < count($Photo); $z++) {
                            $htmlPhoto .= '<li>' . '<a href="' . base_url("fileGetAny/budgeting-catalog-" . $Photo[$z]) . '" target="_blank"></i>' . $Photo[$z] . '</a></li>';
                        }
                        $htmlPhoto .= '</ul>';
                    } else {
                        $htmlPhoto = '';
                    }
                    $nestedData[] = $htmlPhoto;
                    $DetailCatalog = $row['DetailCatalog'];
                    $DetailCatalog = json_decode($DetailCatalog);
                    $temp = '';
                    if ($DetailCatalog != "" || $DetailCatalog != null) {
                        foreach ($DetailCatalog as $key => $value) {
                            $temp .= $key . ' :  ' . $value . '<br>';
                        }
                    }
                    $nestedData[] = $temp;
                    $nestedData[] = $row['ID'];
                    $nestedData[] = $row['EstimaValue'];
                    $nestedData[] = $row['Approval'];
                    $nestedData[] = $row['Reason'];
                    $nestedData[] = $row['LastUpdateAt'];
                    $nestedData[] = $row['NameCategory'];
                    $nestedData[] = $row['Days'];
                    $data[] = $nestedData;
                }
                $json_data = array(
                    "data"            => $data
                );
                echo json_encode($json_data);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function Databank()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $getData = $this->m_master->caribasedprimary('db_finance.bank', 'Status', 1);
                echo json_encode($getData);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function GetpaymentByID()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $getData = $this->m_master->caribasedprimary('db_finance.payment_proof', 'ID_payment', $dataToken['idpayment']);
                echo json_encode($getData);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function save_upload_proof_payment()
    {
        $data['auth'] = $this->input->post('auth');
        $auth = $this->m_master->AuthAPI($data);
        if ($auth) {
            try {
                $msg = '';
                $dataToken = $this->getInputToken2();
                $action = $this->input->post('action');
                // get nim and PTID
                $ID_payment = $dataToken['ID_payment'];
                $G_payment = $this->m_master->caribasedprimary('db_finance.payment', 'ID', $ID_payment);
                $G_PTID = $this->m_master->caribasedprimary('db_finance.payment_type', 'ID', $G_payment[0]['PTID']);
                $path = './uploads/document/' . $G_payment[0]['NPM'];
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                switch ($action) {
                    case 'add':
                        if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                            // path overwrite
                            $path = 'document/' . $G_payment[0]['NPM'];
                            $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                            $uploadFile2 = $this->m_master->UploadManyFilesToNas($headerOrigin, 'BuktiBayar_' . $G_PTID[0]['Abbreviation'], 'fileData', $path, 'array');
                        } else {
                            $uploadFile2 = $this->m_rest->uploadDokumenMultiple('BuktiBayar_' . $G_PTID[0]['Abbreviation'], 'fileData', $path);
                        }

                        $FileUpload = array();
                        for ($i = 0; $i < count($uploadFile2); $i++) {
                            $FileUpload[] = array(
                                'Filename' => $uploadFile2[$i],
                                'VerifyFinance' => 0,
                            );
                        }
                        $dataToken['PTID'] = $G_payment[0]['PTID'];
                        $dataToken['NPM'] = $G_payment[0]['NPM'];
                        $dataToken['SemesterID'] = $G_payment[0]['SemesterID'];
                        $dataToken['FileUpload'] = json_encode($FileUpload);
                        $dataToken['Date_upload'] = date('Y-m-d H:i:s');
                        $this->db->insert('db_finance.payment_proof', $dataToken);
                        break;
                    case 'edit':
                        $uploadFile2 = $this->m_rest->uploadDokumenMultiple('BuktiBayar_' . $G_PTID[0]['Abbreviation'], 'fileData', $path);
                        $FileUpload = array();
                        for ($i = 0; $i < count($uploadFile2); $i++) {
                            $FileUpload[] = array(
                                'Filename' => $uploadFile2[$i],
                                'VerifyFinance' => 0,
                            );
                        }
                        $G_payment_proof = $this->m_master->caribasedprimary('db_finance.payment_proof', 'ID_payment', $ID_payment);
                        $G_FileUpload = (array)json_decode($G_payment_proof[0]['FileUpload']);
                        for ($i = 0; $i < count($FileUpload); $i++) {
                            $G_FileUpload[] =  $FileUpload[$i];
                        }

                        $dataToken['FileUpload'] = json_encode($G_FileUpload);
                        $this->db->where('ID_payment', $ID_payment);
                        $this->db->update('db_finance.payment_proof', $dataToken);
                        break;
                    default:
                        # code...
                        break;
                }

                echo json_encode($msg);
            }
            //catch exception
            catch (Exception $e) {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        } else {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function delete_file_proof_payment()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $msg = '';
                $getDataproof = $this->m_master->caribasedprimary('db_finance.payment_proof', 'ID', $dataToken['idtable']);
                $getDatapayment = $this->m_master->caribasedprimary('db_finance.payment', 'ID', $dataToken['ID_payment']);
                $NPM = $getDatapayment[0]['NPM'];
                $FileUpload = (array) json_decode($getDataproof[0]['FileUpload'], true);
                $index = $dataToken['index'];
                $filename = $dataToken['Filename'];
                // print_r(FCPATH);die();
                for ($i = 0; $i < count($FileUpload); $i++) {
                    if ($i == $index && $FileUpload[$i]['Filename'] == $filename) {
                        if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                            $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                            $path = ($_SERVER['SERVER_NAME'] == 'localhost') ? "localhost/document/" . $NPM . '/' . $filename : "pcam/document/" . $NPM . '/' . $filename;
                            $this->m_master->DeleteFileToNas($headerOrigin, $path);
                        } else {
                            $path = FCPATH . 'uploads/document/' . $NPM . '/' . $filename;
                            if (file_exists($path)) {
                                unlink($path);
                            }
                            unset($FileUpload[$i]);
                        }
                    }
                }

                if (count($FileUpload) == 0) {
                    $this->db->where('ID', $dataToken['idtable']);
                    $this->db->delete('db_finance.payment_proof');
                } else {
                    $FileUpload = array_values($FileUpload);
                    $datasave = array(
                        'FileUpload' => json_encode($FileUpload),
                    );
                    $this->db->where('ID', $dataToken['idtable']);
                    $this->db->update('db_finance.payment_proof', $datasave);
                }

                echo json_encode($msg);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function delete_all_file_proof_payment_byID()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $msg = '';
                $getDataproof = $this->m_master->caribasedprimary('db_finance.payment_proof', 'ID', $dataToken['idtable']);
                $getDatapayment = $this->m_master->caribasedprimary('db_finance.payment', 'ID', $dataToken['ID_payment']);
                $NPM = $getDatapayment[0]['NPM'];
                $FileUpload = (array) json_decode($getDataproof[0]['FileUpload'], true);
                for ($i = 0; $i < count($FileUpload); $i++) {
                    if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                        $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                        $path = ($_SERVER['SERVER_NAME'] == 'localhost') ? "localhost/document/" . $NPM . '/' . $FileUpload[$i]['Filename'] : "pcam/document/" . $NPM . '/' . $FileUpload[$i]['Filename'];
                        $this->m_master->DeleteFileToNas($headerOrigin, $path);
                    } else {
                        $path = FCPATH . 'uploads/document/' . $NPM . '/' . $FileUpload[$i]['Filename'];
                        if (file_exists($path)) {
                            unlink($path);
                        }
                    }
                    unset($FileUpload[$i]);
                }

                $FileUpload = array_values($FileUpload);
                for ($i = 0; $i < count($FileUpload); $i++) {
                    if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                        $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                        $path = ($_SERVER['SERVER_NAME'] == 'localhost') ? "localhost/document/" . $NPM . '/' . $FileUpload[$i]['Filename'] : "pcam/document/" . $NPM . '/' . $FileUpload[$i]['Filename'];
                        $this->m_master->DeleteFileToNas($headerOrigin, $path);
                    } else {
                        $path = FCPATH . 'uploads/document/' . $NPM . '/' . $FileUpload[$i]['Filename'];
                        if (file_exists($path)) {
                            unlink($path);
                        }
                    }
                    unset($FileUpload[$i]);
                }
                if (count($FileUpload) == 0) {
                    $this->db->where('ID', $dataToken['idtable']);
                    $this->db->delete('db_finance.payment_proof');
                }
                echo json_encode($msg);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function academic_fill_list_mhs_tidak_bayar()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('m_sendemail');
                $this->load->model('finance/m_finance');

                $Semester = $this->m_master->caribasedprimary('db_academic.semester', 'Status', 1);
                $SemesterActive = $Semester[0]['ID'];

                // cek date bayar end
                $bool = false;
                $AcademicYears = $this->m_master->caribasedprimary('db_academic.academic_years', 'SemesterID', $SemesterActive);
                if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                    $bayarEnd = date("Y-m-d", strtotime($AcademicYears[0]['bayarEnd']));
                    $NowDate = date('Y-m-d');
                    if ($bayarEnd == $NowDate) {
                        $bool = true;
                    }
                } else {
                    $bool = true;
                    // $bayarEnd = date("Y-m-d", strtotime($AcademicYears[0]['bayarEnd']));
                    // $NowDate = date('Y-m-d');
                    // if ($bayarEnd == $NowDate) {
                    //     $bool = true;
                    // }
                }

                if ($bool) {
                    $G_data = $this->m_finance->PaymentTidakLunas($SemesterActive);

                    if (count($G_data) > 0) {
                        $html = 'Dear <span style="color: #333;">Finance & Academic</span>,
                                    <br/>
                                     Perihal : <b>List Mahasiswa Belum Lunas Semester ' . $G_data[0]['NameSemester'] . '</b>     
                                     <div style="font-size: 12px;">
                                     <br/>
                                     <table  width="100%" cellspacing="0" cellpadding="4" border="0">
                                        <thead>
                                            <tr style="background: #607d8b;color: #ffffff;">
                                                <th style="width: 3%;text-align: center;">No</th>
                                                <th style="text-align: left;">NPM / Nama</th>
                                                <th style="width: 10%;text-align: center;">Prodi</th>
                                                <!--<th style="width: 10%;text-align: center;">Semester</th>-->
                                                <th style="width: 10%;text-align: center;">Tipe Pembayaran</th>
                                                <th style="width: 20%;text-align: left;">Invoice</th>
                                                <th style="width: 20%;text-align: left;">Pembayaran</th>
                                                <!--<th style="width: 10%;text-align: center;">Status</th>-->
                                            </tr>
                                        </thead>
                                        <tbody>        
                        ';
                        for ($i = 0; $i < count($G_data); $i++) {
                            $G_data[$i]['Payment'] = ($G_data[$i]['Payment'] == null || $G_data[$i]['Payment'] == 'null') ? 0 : $G_data[$i]['Payment'];
                            $html .= '<tr style="background: #607d8b24;">
                                        <td style="border-bottom: 1px solid #9e9e9e;text-align: center">
                                            ' . ($i + 1) .
                                '</td>
                                        <td style="border-bottom: 1px solid #9e9e9e;text-align: left">
                                            ' . $G_data[$i]['NPM'] . ' / ' . $G_data[$i]['NameMHS'] .
                                '</td>
                                        <td style="border-bottom: 1px solid #9e9e9e;text-align: center">
                                            ' . $G_data[$i]['NameEng'] .
                                '</td>
                                        <!--<td style="border-bottom: 1px solid #9e9e9e;text-align: center">
                                            ' . $G_data[$i]['NameSemester'] .
                                '</td>-->
                                        <td style="border-bottom: 1px solid #9e9e9e;text-align: center">
                                            ' . $G_data[$i]['Description'] .
                                '</td>
                                        <td style="border-bottom: 1px solid #9e9e9e;text-align: left">
                                            Rp ' . number_format($G_data[$i]['Invoice'], 2, ',', '.') .
                                '</td>
                                        <td style="border-bottom: 1px solid #9e9e9e;text-align: left">
                                            Rp ' . number_format($G_data[$i]['Payment'], 2, ',', '.') .
                                '</td>
                                        <!--<td style="border-bottom: 1px solid #9e9e9e;text-align: center">
                                            ' . $G_data[$i]['StatusPay'] .
                                '</td>-->
                                    </tr>    
                                    ';
                        }

                        $html .= '</tbody></table></div>';

                        $Email = 'alhadi.rahman@podomorouniversity.ac.id';
                        if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                            $G_fin = $this->m_master->caribasedprimary('db_employees.division', 'ID', 9);
                            $EmailFin = $G_fin[0]['Email'];
                            $G_Academic = $this->m_master->caribasedprimary('db_employees.division', 'ID', 6);
                            $EmailAcademic = $G_Academic[0]['Email'];
                            $Email = $EmailFin . ',' . $EmailAcademic . ',it@podomorouniversity.ac.id';
                        }
                        $to = $Email;
                        $subject = "Podomoro University Reminder";
                        $text = $html;
                        $sendEmail = $this->m_sendemail->sendEmail($to, $subject, null, null, null, null, $text);
                    }
                }

                echo json_encode(1);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function assign_by_finance_change_status()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('finance/m_finance');
                if (array_key_exists('filterSemester', $dataToken)) {
                    $SemesterActive = $dataToken['filterSemester'];
                } else {
                    $Semester = $this->m_master->caribasedprimary('db_academic.semester', 'Status', 1);
                    $SemesterActive = $Semester[0]['ID'];
                }

                $G_data = $this->m_finance->GetRequestChangeStatus_Mhs($SemesterActive);
                echo json_encode($G_data);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function change_status_mhs_multiple()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $Status = $dataToken['formChangeStatus'];
                $arr = (array)$dataToken['checkboxArr'];
                for ($i = 0; $i < count($arr); $i++) {
                    $NPM = $arr[$i];
                    $NPM = explode(';', $NPM);
                    $NPM = $NPM[0];
                    $datasave = array(
                        'StatusStudentID' => $Status,
                    );
                    $this->db->where('NPM', $NPM);
                    $this->db->update('db_academic.auth_students', $datasave);

                    // search ta
                    $G_Std = $this->m_master->caribasedprimary('db_academic.auth_students', 'NPM', $NPM);
                    $this->db->where('NPM', $NPM);
                    $this->db->update('ta_' . $G_Std[0]['Year'] . '.students', $datasave);
                }
                echo json_encode(1);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function show_schedule_exchange()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $Status = (array_key_exists('Status', $dataToken)) ? $dataToken['Status'] : '';
                $requestData = $_REQUEST;
                $Semester0 = (array_key_exists('Semester', $dataToken)) ? $dataToken['Semester'] : '';
                $totalData = $this->m_rest->count_get_schedule_exchange_by_status($Status, $Semester0);

                $Status = ($Status == '') ? '' : ' where a.Status ="' . $Status . '" ';
                $where = ($Status == '') ? ' where' : ' and';
                $sql = 'select a.ID as ScheduleExchangeID,a.NIP as NIPRequester,b.Name as NamaRequester,b.EmailPU as EmailRequster,a.Meeting,a.ClassroomID,a.Comment,c.Room,a.Status as StatusTbl,
                        a.DateOriginal,a.Date,a.DayID,d.NameEng as NamaHari,a.StartSessions,a.EndSessions,a.Reason,a.Token,
                        e.ProdiID,f.NameEng as NamaProdi,f.KaprodiID,g.Name as NameKaprodi,g.EmailPU as EmailKaprodi,h.ScheduleID as ScheduleIDAttedance,
                        i.MKID,j.MKCode,j.NameEng as NamaMatakuliah,k.ID as ScheduleID,k.ClassGroup,(select count(*) as total from db_academic.std_krs where ScheduleID = k.ID and Status = "3" limit 1) as TotalStd,
                        a.Updated1By as KaprodiChoice,l.EmailPU as EmailKaprodiChoice
                        from db_academic.schedule_exchange as a
                        left join db_employees.employees as b on a.NIP = b.NIP
                        left join db_academic.classroom as c on a.ClassroomID = c.ID
                        left join db_academic.days as d on a.DayID = d.ID
                        left join (select * from db_academic.schedule_exchange_prodi group by EXID)  as e on a.ID = e.EXID
                        left join db_academic.program_study as f on e.ProdiID = f.ID
                        left join db_employees.employees as g on f.KaprodiID = g.NIP
                        left join db_academic.attendance as h on a.ID_Attd = h.ID
                        left join (select * from db_academic.schedule_details_course group by ScheduleID) as i on h.ScheduleID = i.ScheduleID
                        left join db_academic.mata_kuliah as j on i.MKID = j.ID
                        left join db_academic.schedule as k on k.ID = i.ScheduleID
                        left join db_employees.employees as l on a.Updated1By = l.NIP
                        ' . $Status;

                $Semester = (array_key_exists('Semester', $dataToken)) ? ' and h.SemesterID ="' . $dataToken['Semester'] . '"' : '';
                $sql .= $where . ' (f.KaprodiID LIKE "%' . $requestData['search']['value'] . '%" or a.NIP LIKE "' . $requestData['search']['value'] . '%" or b.Name LIKE "' . $requestData['search']['value'] . '%"  or g.Name LIKE "' . $requestData['search']['value'] . '%" or c.Room LIKE "' . $requestData['search']['value'] . '%" or a.DateOriginal LIKE "' . $requestData['search']['value'] . '%" or a.Date LIKE "' . $requestData['search']['value'] . '%" or f.NameEng LIKE "' . $requestData['search']['value'] . '%"
                    or j.NameEng LIKE "' . $requestData['search']['value'] . '%" or k.ClassGroup LIKE "' . $requestData['search']['value'] . '%"
                        ) ' . $Semester;

                $sql .= ' ORDER BY a.Date Desc LIMIT ' . $requestData['start'] . ' , ' . $requestData['length'] . ' ';
                // print_r($sql);die();
                $query = $this->db->query($sql)->result_array();

                $No = $requestData['start'] + 1;
                $data = array();
                for ($i = 0; $i < count($query); $i++) {
                    $nestedData = array();
                    $row = $query[$i];
                    $token = $row['Token'];
                    $ScheduleExist = $row['DateOriginal'];
                    $ScheduleExchange = $row['Date'];
                    if ($row['Token'] != '' || $row['Token'] != null) {
                        $key = "s3Cr3T-G4N";
                        $data_arr_token = (array) $this->jwt->decode($token, $key);
                        $ScheduleExist = $data_arr_token['ScheduleExist'];
                        $ScheduleExchange = $data_arr_token['ScheduleExchange'];
                    }

                    $ScheduleExchange = date("d M Y", strtotime($row['Date'])) . ' | ' . substr($row['StartSessions'], 0, 5) . '-' . substr($row['EndSessions'], 0, 5);

                    $StatusTbl = '';
                    switch ($row['StatusTbl']) {
                        case '1':
                            $StatusTbl = 'Will be Set Room<br>' . $row['Comment'];
                            break;
                        case '2':
                            $StatusTbl = 'Already Set Room<br>' . $row['Comment'];
                            break;
                        case '-2':
                            $StatusTbl = 'Reject<br>' . $row['Comment'];
                            break;
                        default:
                            $StatusTbl = '';
                            break;
                    }
                    $Room = '';
                    if ($row['Room'] != null || $row['Room'] != '') {
                        $Room = ' | ' . $row['Room'];
                    }
                    $nestedData[] = $No;
                    $nestedData[] = $row['NamaRequester'];
                    // $nestedData[] = $row['NamaProdi'];
                    $nestedData[] = $row['NamaMatakuliah'];
                    $nestedData[] = $row['TotalStd'];
                    $nestedData[] = $row['ClassGroup'];
                    $nestedData[] = $row['Meeting'];
                    $nestedData[] = $ScheduleExist;
                    $nestedData[] = $ScheduleExchange . $Room;
                    $nestedData[] = $row['Reason'];
                    $nestedData[] = $StatusTbl;

                    $btnApprove  = '';
                    $btnreject = '';
                    if ($row['StatusTbl'] == '1') {
                        $btnApprove = '<button class = "btn btn-primary btnapprove" token = "' . $row['Token'] . '" emailrequest = "' . $row['EmailRequster'] . '" emailkaprodi = "' . $row['EmailKaprodiChoice'] . '" ScheduleExchangeID = "' . $row['ScheduleExchangeID'] . '"><i class="fa fa-check" aria-hidden="true"></i> Set Room </button>';
                        $btnreject = '<button class = "btn btn-inverse btnreject" token = "' . $row['Token'] . '" emailrequest = "' . $row['EmailRequster'] . '" emailkaprodi = "' . $row['EmailKaprodiChoice'] . '" ScheduleExchangeID = "' . $row['ScheduleExchangeID'] . '"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Reject </button>';
                    }


                    $nestedData[] = '<div>' . $btnApprove . '</div>' . '<div style = "margin-top : 10px">' . $btnreject . '</div>';

                    $data[] = $nestedData;
                    $No++;
                }

                $json_data = array(
                    "draw"            => intval($requestData['draw']),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalData),
                    "data"            => $data
                );
                echo json_encode($json_data);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function approve_pr()
    {
        $msg = '';
        $Reload = 0;
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $BoolReload = false;
                $this->load->model('budgeting/m_budgeting');
                $this->load->model('budgeting/m_pr_po');
                $PRCode = $dataToken['PRCode'];
                // untuk redirect notifikasi
                $key = "UAP)(*";
                $PRCodeURL = $this->jwt->encode($PRCode, $key);
                $URLDirect = 'budgeting_pr/' . $PRCodeURL;

                $approval_number = $dataToken['approval_number'];
                $NIP = $dataToken['NIP'];
                $G_emp = $this->m_master->SearchNameNIP_Employees_PU_Holding($NIP);
                $NameFor_NIP = $G_emp[0]['Name'];
                $action = $dataToken['action'];
                // check data telah berubah atau tidak
                $DtExisting = $dataToken['DtExisting'];
                $dt_pr_create = (array) json_decode(json_encode($DtExisting->pr_create), true);
                $dt_pr_detail = (array) json_decode(json_encode($DtExisting->pr_detail), true);

                // get data
                $G_data = $this->m_master->caribasedprimary('db_budgeting.pr_create', 'PRCode', $PRCode);
                $G_data_detail = $this->m_master->caribasedprimary('db_budgeting.pr_detail', 'PRCode', $PRCode);
                // print_r($dt_pr_detail);die();
                // Notes
                $NotesClient = $G_data[0]['Notes'];
                if ($dt_pr_create[0]['Notes'] != $NotesClient || $dt_pr_create[0]['Supporting_documents'] != $G_data[0]['Supporting_documents']) {
                    $BoolReload = true;
                }

                // pr_detail
                if (!$BoolReload) {
                    for ($i = 0; $i < count($dt_pr_detail); $i++) {
                        $ID_dt_pr_detail = $dt_pr_detail[$i]['ID'];
                        $boolFindPrDetail = false;
                        for ($j = 0; $j < count($G_data_detail); $j++) {
                            $ID_G_data_detail = $G_data_detail[$j]['ID'];
                            if ($ID_dt_pr_detail == $ID_G_data_detail) {
                                $boolFindPrDetail = true;
                                if ($dt_pr_detail[$i]['ID_budget_left'] != $G_data_detail[$j]['ID_budget_left'] || $dt_pr_detail[$i]['ID_m_catalog'] != $G_data_detail[$j]['ID_m_catalog'] ||  $dt_pr_detail[$i]['Spec_add'] !=  $G_data_detail[$j]['Spec_add'] || $dt_pr_detail[$i]['Need'] !=  $G_data_detail[$j]['Need'] || $dt_pr_detail[$i]['SubTotal'] !=  $G_data_detail[$j]['SubTotal'] || $dt_pr_detail[$i]['UploadFile'] !=  $G_data_detail[$j]['UploadFile']) {
                                    $BoolReload = true;
                                }
                                break;
                            }
                        }

                        if (!$boolFindPrDetail) {
                            $BoolReload = true;
                        }

                        if ($BoolReload) {
                            break;
                        }
                    }
                }

                if (!$BoolReload) {
                    $keyJson = $approval_number - 1; // get array index json
                    $JsonStatus = (array)json_decode($G_data[0]['JsonStatus'], true);
                    // get data update to approval
                    $arr_upd = $JsonStatus[$keyJson];
                    // print_r($keyJson);die();
                    if ($arr_upd['NIP'] == $NIP) {
                        $arr_upd['Status'] = ($action == 'approve') ? 1 : 2;
                        $arr_upd['ApproveAt'] = ($action == 'approve') ? date('Y-m-d H:i:s') : '-';
                        $JsonStatus[$keyJson] = $arr_upd;
                        $datasave = array(
                            'JsonStatus' => json_encode($JsonStatus),
                        );

                        // check all status for update data
                        $boolApprove = true;
                        for ($i = 0; $i < count($JsonStatus); $i++) {
                            $arr = $JsonStatus[$i];
                            $Status = $arr['Status'];
                            if ($Status == 2 || $Status == 0) {
                                $boolApprove = false;
                                break;
                            }
                        }

                        if ($boolApprove) {
                            $datasave['Status'] = 2;
                            $datasave['PostingDate'] = date('Y-m-d H:i:s');
                        } else {
                            $boolReject = false;
                            for ($i = 0; $i < count($JsonStatus); $i++) {
                                $arr = $JsonStatus[$i];
                                $Status = $arr['Status'];
                                if ($Status == 2) {
                                    $boolReject = true;
                                    break;
                                }
                            }

                            if ($boolReject) {
                                $NoteDel = $dataToken['NoteDel'];
                                $Notes = $G_data[0]['Notes'] . "\n" . $NoteDel;
                                $datasave['Status'] = 3;
                                // $datasave['Notes'] = $Notes;
                            } else {
                                // Notif to next step approval & User
                                // send revisi or not
                                $RevisiOrNotNotif = $this->m_master->__RevisiOrNotNotif($PRCode, 'db_budgeting.pr_circulation_sheet', 'PRCode');

                                $NIPApprovalNext = $JsonStatus[($keyJson + 1)]['NIP'];
                                $IDdiv = $G_data[0]['Departement'];
                                $G_div = $this->m_budgeting->SearchDepartementBudgeting($IDdiv);
                                // $NameDepartement = $G_div[0]['NameDepartement'];
                                $Code = $G_div[0]['Code'];
                                // Send Notif for next approval
                                $data = array(
                                    'auth' => 's3Cr3T-G4N',
                                    'Logging' => array(
                                        'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  ' . $RevisiOrNotNotif . 'Approval PR ' . $PRCode . ' of ' . $Code,
                                        'Description' => 'Please approve ' . $RevisiOrNotNotif . ' PR ' . $PRCode . ' of ' . $Code,
                                        'URLDirect' => $URLDirect,
                                        'CreatedBy' => $NIP,
                                    ),
                                    'To' => array(
                                        'NIP' => array($NIPApprovalNext),
                                    ),
                                    'Email' => 'No',
                                );

                                $url = url_pas . 'rest2/__send_notif_browser';
                                $token = $this->jwt->encode($data, "UAP)(*");
                                $this->m_master->apiservertoserver($url, $token);

                                // send email is holding or warek keatas
                                $this->m_master->send_email_budgeting_holding($NIPApprovalNext, $IDdiv, $data['Logging']['URLDirect'], $data['Logging']['Description']);

                                // Send Notif for user 
                                $data = array(
                                    'auth' => 's3Cr3T-G4N',
                                    'Logging' => array(
                                        'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  PR ' . $PRCode . ' has been Approved',
                                        'Description' => 'PR ' . $PRCode . ' of ' . $Code . ' has been approved by ' . $NameFor_NIP,
                                        'URLDirect' => $URLDirect,
                                        'CreatedBy' => $NIP,
                                    ),
                                    'To' => array(
                                        'NIP' => array($JsonStatus[0]['NIP']),
                                    ),
                                    'Email' => 'No',
                                );

                                $url = url_pas . 'rest2/__send_notif_browser';
                                $token = $this->jwt->encode($data, "UAP)(*");
                                $this->m_master->apiservertoserver($url, $token);
                            }
                        }

                        $this->db->where('PRCode', $PRCode);
                        $this->db->update('db_budgeting.pr_create', $datasave);

                        // insert to pr_circulation_sheet
                        $Desc = ($arr_upd['Status'] == 1) ? 'Approve' : 'Reject';
                        if (array_key_exists('Status', $datasave)) {
                            if ($datasave['Status'] == 2) {
                                $Desc = "All Approve and posting date at : " . $datasave['PostingDate'];
                                // save to db_purchasing pr_status
                                // delete first if exist di pr_status dan pr_status_detail
                                $G_pr_status = $this->m_master->caribasedprimary('db_purchasing.pr_status', 'PRCode', $PRCode);
                                if (count($G_pr_status) > 0) {
                                    $ID_pr_status = $G_pr_status[0]['ID'];
                                    $this->db->where('PRCode', $PRCode);
                                    $this->db->delete('db_purchasing.pr_status');

                                    $this->db->where('ID_pr_status', $ID_pr_status);
                                    $this->db->delete('db_purchasing.pr_status_detail');
                                }

                                $dataSave = array(
                                    'PRCode' => $PRCode,
                                    'Item_proc' => 0,
                                    'Item_done' => 0,
                                    'Item_pending' => count($this->m_master->caribasedprimary('db_budgeting.pr_detail', 'PRCode', $PRCode)),
                                    'Status' => 0,
                                );

                                $this->db->insert('db_purchasing.pr_status', $dataSave);
                                $ID_pr_status = $this->db->insert_id();

                                // save to db_purchasing pr_status_detail
                                for ($i = 0; $i < count($G_data_detail); $i++) {
                                    $ID_pr_detail = $G_data_detail[$i]['ID'];
                                    $dataSave = array(
                                        'ID_pr_status' => $ID_pr_status,
                                        'ID_pr_detail' => $ID_pr_detail,
                                        'Status' => 0,
                                    );
                                    $this->db->insert('db_purchasing.pr_status_detail', $dataSave);
                                }

                                // Notif All Approve to JsonStatus allkey
                                $IDdiv = $G_data[0]['Departement'];
                                $G_div = $this->m_budgeting->SearchDepartementBudgeting($IDdiv);
                                // $NameDepartement = $G_div[0]['NameDepartement'];
                                $Code = $G_div[0]['Code'];
                                $arr_to = array();
                                for ($i = 0; $i < count($JsonStatus); $i++) {
                                    $arr_to[] = $JsonStatus[$i]['NIP'];
                                }

                                $data = array(
                                    'auth' => 's3Cr3T-G4N',
                                    'Logging' => array(
                                        'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> PR ' . $PRCode . ' of ' . $Code . ' has been done',
                                        'Description' => 'PR ' . $PRCode . ' of ' . $Code . ' has been done',
                                        'URLDirect' => $URLDirect,
                                        'CreatedBy' => $NIP,
                                    ),
                                    'To' => array(
                                        'NIP' => $arr_to,
                                    ),
                                    'Email' => 'No',
                                );

                                $url = url_pas . 'rest2/__send_notif_browser';
                                $token = $this->jwt->encode($data, "UAP)(*");
                                $this->m_master->apiservertoserver($url, $token);

                                // Notif to Purchasing 
                                $IDdiv = $G_data[0]['Departement'];
                                $G_div = $this->m_budgeting->SearchDepartementBudgeting($IDdiv);
                                //$NameDepartement = $G_div[0]['NameDepartement'];
                                $Code = $G_div[0]['Code'];

                                $data = array(
                                    'auth' => 's3Cr3T-G4N',
                                    'Logging' => array(
                                        'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> PR ' . $PRCode . ' of ' . $Code . ' has been done',
                                        'Description' => 'PR ' . $PRCode . ' of ' . $Code . ' has been done',
                                        'URLDirect' => 'purchasing/transaction/po/list/open',
                                        'CreatedBy' => $NIP,
                                    ),
                                    'To' => array(
                                        'Div' => array(4),
                                    ),
                                    'Email' => 'No',
                                );

                                $url = url_pas . 'rest2/__send_notif_browser';
                                $token = $this->jwt->encode($data, "UAP)(*");
                                $this->m_master->apiservertoserver($url, $token);
                            }
                        }

                        if ($arr_upd['Status'] == 2) {
                            if ($dataToken['NoteDel'] != '' || $dataToken['NoteDel'] != null) {
                                $Desc .= '<br>{' . $dataToken['NoteDel'] . '}';
                            }

                            // Notif Reject to JsonStatus key 0
                            $IDdiv = $G_data[0]['Departement'];
                            $G_div = $this->m_budgeting->SearchDepartementBudgeting($IDdiv);
                            $NameDepartement = $G_div[0]['NameDepartement'];

                            // Send Notif for user 
                            $data = array(
                                'auth' => 's3Cr3T-G4N',
                                'Logging' => array(
                                    'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> PR ' . $PRCode . ' has been Rejected',
                                    'Description' => 'PR ' . $PRCode . ' has been Rejected by ' . $NameFor_NIP,
                                    'URLDirect' => $URLDirect,
                                    'CreatedBy' => $NIP,
                                ),
                                'To' => array(
                                    'NIP' => array($JsonStatus[0]['NIP']),
                                ),
                                'Email' => 'No',
                            );

                            $url = url_pas . 'rest2/__send_notif_browser';
                            $token = $this->jwt->encode($data, "UAP)(*");
                            $this->m_master->apiservertoserver($url, $token);
                        }

                        $this->m_pr_po->pr_circulation_sheet($PRCode, $Desc, $NIP);
                    } else {
                        $msg = 'Not Authorize';
                    }
                } else {
                    $Reload = 1;
                    $msg = 'The data was not approve and will do to reload and resubmit';
                }

                // echo json_encode($msg);
                echo json_encode(array('Reload' => $Reload, 'msg' => $msg));
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function approve_pr_old()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('budgeting/m_budgeting');
                $PRCode = $dataToken['PRCode'];
                $useraccess = $dataToken['useraccess'];
                $NIP = $dataToken['NIP'];
                $action = $dataToken['action'];

                // get data
                $G_data = $this->m_master->caribasedprimary('db_budgeting.pr_create', 'PRCode', $PRCode);
                $keyJson = $useraccess - 2; // get array index json
                $JsonStatus = (array)json_decode($G_data[0]['JsonStatus'], true);
                // get data update to approval
                $arr_upd = $JsonStatus[$keyJson];
                // print_r($keyJson);die();
                if ($arr_upd['ApprovedBy'] == $NIP) {
                    $arr_upd['Status'] = ($action == 'approve') ? 1 : 2;
                    $arr_upd['ApproveAt'] = ($action == 'approve') ? date('Y-m-d H:i:s') : '-';
                    $JsonStatus[$keyJson] = $arr_upd;
                    $datasave = array(
                        'JsonStatus' => json_encode($JsonStatus),
                    );

                    // check all status for update data
                    $boolApprove = true;
                    for ($i = 0; $i < count($JsonStatus); $i++) {
                        $arr = $JsonStatus[$i];
                        $Status = $arr['Status'];
                        if ($Status == 2 || $Status == 0) {
                            $boolApprove = false;
                            break;
                        }
                    }

                    if ($boolApprove) {
                        $datasave['Status'] = 2;
                        $datasave['PostingDate'] = date('Y-m-d H:i:s');
                    } else {
                        $boolReject = false;
                        for ($i = 0; $i < count($JsonStatus); $i++) {
                            $arr = $JsonStatus[$i];
                            $Status = $arr['Status'];
                            if ($Status == 2) {
                                $boolReject = true;
                                break;
                            }
                        }

                        if ($boolReject) {
                            $NoteDel = $dataToken['NoteDel'];
                            $Notes = $G_data[0]['Notes'] . "\n" . $NoteDel;
                            $datasave['Status'] = 3;
                            // $datasave['Notes'] = $Notes;
                        }
                    }

                    $this->db->where('PRCode', $PRCode);
                    $this->db->update('db_budgeting.pr_create', $datasave);

                    // insert to pr_circulation_sheet
                    $Desc = ($arr_upd['Status'] == 1) ? 'Approve' : 'Reject';
                    if (array_key_exists('Status', $datasave)) {
                        if ($datasave['Status'] == 2) {
                            $Desc = "All Approve and posting date at : " . $datasave['PostingDate'];
                        }
                    }

                    if ($arr_upd['Status'] == 2) {
                        if ($dataToken['NoteDel'] != '' || $dataToken['NoteDel'] != null) {
                            $Desc .= ', ' . $dataToken['NoteDel'];
                        }
                    }

                    $this->m_budgeting->pr_circulation_sheet($PRCode, $Desc, $NIP);
                } else {
                    // detection is represented or not ?
                    $represented = $dataToken['represented'];
                    if ($represented != '') {
                        if ($arr_upd['ApprovedBy'] == $represented) {
                            $NameRepresented = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $represented);
                            $NameRepresented = $NameRepresented[0]['Name'];
                            $arr_upd['Status'] = ($action == 'approve') ? 1 : 2;
                            $arr_upd['ApproveAt'] = ($action == 'approve') ? date('Y-m-d H:i:s') : '-';
                            $arr_upd['Representedby'] = $NIP;
                            $JsonStatus[$keyJson] = $arr_upd;
                            $datasave = array(
                                'JsonStatus' => json_encode($JsonStatus),
                            );

                            // check all status for update data
                            $boolApprove = true;
                            for ($i = 0; $i < count($JsonStatus); $i++) {
                                $arr = $JsonStatus[$i];
                                $Status = $arr['Status'];
                                if ($Status == 2 || $Status == 0) {
                                    $boolApprove = false;
                                    break;
                                }
                            }

                            if ($boolApprove) {
                                $datasave['Status'] = 2;
                                $datasave['PostingDate'] = date('Y-m-d H:i:s');
                            } else {
                                $boolReject = false;
                                for ($i = 0; $i < count($JsonStatus); $i++) {
                                    $arr = $JsonStatus[$i];
                                    $Status = $arr['Status'];
                                    if ($Status == 2) {
                                        $boolReject = true;
                                        break;
                                    }
                                }

                                if ($boolReject) {
                                    $NoteDel = $dataToken['NoteDel'];
                                    $Notes = $G_data[0]['Notes'] . "\n" . $NoteDel;
                                    $datasave['Status'] = 3;
                                    // $datasave['Notes'] = $Notes;
                                }
                            }

                            $this->db->where('PRCode', $PRCode);
                            $this->db->update('db_budgeting.pr_create', $datasave);

                            // insert to pr_circulation_sheet
                            $Desc = ($arr_upd['Status'] == 1) ? 'Approve, Represented from [' . $represented . ' || ' . $NameRepresented . ']' : 'Reject, Represented from [' . $represented . ' || ' . $NameRepresented . ']';
                            if (array_key_exists('Status', $datasave)) {
                                if ($datasave['Status'] == 2) {
                                    $Desc = "All Approve and posting date at : " . $datasave['PostingDate'] . '<br>, Represented from [' . $represented . ' || ' . $NameRepresented . ']';
                                }
                            }

                            if ($arr_upd['Status'] == 2) {
                                if ($dataToken['NoteDel'] != '' || $dataToken['NoteDel'] != null) {
                                    $Desc .= '<br> Reason : ' . $dataToken['NoteDel'];
                                }
                            }

                            $this->m_budgeting->pr_circulation_sheet($PRCode, $Desc, $NIP);
                        }
                    } else {
                        $msg = 'Not Authorize';
                    }
                }

                echo json_encode($msg);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function approve_budget()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('budgeting/m_budgeting');
                $id_creator_budget_approval = $dataToken['id_creator_budget_approval'];
                $NIP = $dataToken['NIP'];
                $G_emp = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $NIP);
                $NameFor_NIP = $G_emp[0]['Name'];
                $action = $dataToken['action'];

                // get data
                $G_data = $this->m_master->caribasedprimary('db_budgeting.creator_budget_approval', 'ID', $id_creator_budget_approval);
                $keyJson = $dataToken['approval_number'] - 1; // get array index json
                $JsonStatus = (array)json_decode($G_data[0]['JsonStatus'], true);
                // get data update to approval
                $arr_upd = $JsonStatus[$keyJson];
                $arr_upd['Status'] = ($action == 'approve') ? 1 : 2;
                $arr_upd['ApproveAt'] = ($action == 'approve') ? date('Y-m-d H:i:s') : '-';
                $JsonStatus[$keyJson] = $arr_upd;
                $datasave = array(
                    'JsonStatus' => json_encode($JsonStatus),
                );

                // check all status for update data
                $boolApprove = true;
                for ($i = 0; $i < count($JsonStatus); $i++) {
                    $arr = $JsonStatus[$i];
                    // if Acknowledge by then skipp
                    if ($arr['NameTypeDesc'] == 'Requested by') {
                        continue;
                    }
                    $Status = $arr['Status'];
                    if ($Status == 2 || $Status == 0) {
                        $boolApprove = false;
                        break;
                    }
                }

                if ($boolApprove) {
                    $datasave['Status'] = 2;
                    $datasave['PostingDate'] = date('Y-m-d H:i:s');
                } else {
                    $boolReject = false;
                    for ($i = 0; $i < count($JsonStatus); $i++) {
                        $arr = $JsonStatus[$i];
                        $Status = $arr['Status'];
                        if ($Status == 2) {
                            $boolReject = true;
                            break;
                        }
                    }

                    if ($boolReject) {
                        $datasave['Status'] = 3;
                    } else {
                        // Notif to next step approval & User
                        $NIPApprovalNext = $JsonStatus[($keyJson + 1)]['NIP'];
                        $IDdiv = $G_data[0]['Departement'];
                        $G_div = $this->m_budgeting->SearchDepartementBudgeting($IDdiv);
                        // $NameDepartement = $G_div[0]['NameDepartement'];
                        $Code = $G_div[0]['Code'];

                        // send revisi or not
                        $RevisiOrNotNotif = $this->m_master->__RevisiOrNotNotif($id_creator_budget_approval, 'db_budgeting.log_budget', 'ID_creator_budget_approval');

                        // Send Notif for next approval
                        $data = array(
                            'auth' => 's3Cr3T-G4N',
                            'Logging' => array(
                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  ' . $RevisiOrNotNotif . 'Approval Budget of ' . $Code,
                                'Description' => 'Please approve ' . $RevisiOrNotNotif . ' budget ' . $Code,
                                'URLDirect' => 'budgeting_entry',
                                'CreatedBy' => $NIP,
                            ),
                            'To' => array(
                                'NIP' => array($NIPApprovalNext),
                            ),
                            'Email' => 'No',
                        );

                        $url = url_pas . 'rest2/__send_notif_browser';
                        $token = $this->jwt->encode($data, "UAP)(*");
                        $this->m_master->apiservertoserver($url, $token);

                        // send email is holding or warek keatas
                        $this->m_master->send_email_budgeting_holding($NIPApprovalNext, $IDdiv, $data['Logging']['URLDirect'], $data['Logging']['Description']);

                        // Send Notif for user 
                        $data = array(
                            'auth' => 's3Cr3T-G4N',
                            'Logging' => array(
                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Budget Approve',
                                'Description' => 'Budget ' . $Code . ' has been approved by ' . $NameFor_NIP,
                                'URLDirect' => 'budgeting_entry',
                                'CreatedBy' => $NIP,
                            ),
                            'To' => array(
                                'NIP' => array($JsonStatus[0]['NIP']),
                            ),
                            'Email' => 'No',
                        );

                        $url = url_pas . 'rest2/__send_notif_browser';
                        $token = $this->jwt->encode($data, "UAP)(*");
                        $this->m_master->apiservertoserver($url, $token);
                    }
                }

                $this->db->where('ID', $id_creator_budget_approval);
                $this->db->update('db_budgeting.creator_budget_approval', $datasave);

                // insert to log
                $Desc = ($arr_upd['Status'] == 1) ? 'Approve' : 'Reject';
                if (array_key_exists('Status', $datasave)) {
                    if ($datasave['Status'] == 2) {
                        $Desc = "All Approve and posting date at : " . $datasave['PostingDate'];
                        // lock can't be delete
                        $Departement = $G_data[0]['Departement'];
                        $Year = $G_data[0]['Year'];
                        $sql = 'select a.CodePostBudget from db_budgeting.cfg_set_post as a join db_budgeting.cfg_head_account as b on a.CodeHeadAccount = b.CodeHeadAccount where b.Departement = ? and a.Year = ?
                                ';
                        $query = $this->db->query($sql, array($Departement, $Year))->result_array();
                        for ($i = 0; $i < count($query); $i++) {
                            $this->m_budgeting->makeCanBeDelete('db_budgeting.cfg_set_post', 'CodePostBudget', $query[$i]['CodePostBudget']);
                        }

                        // lock sub account
                        $G = $this->m_master->caribasedprimary('db_budgeting.creator_budget', 'ID_creator_budget_approval', $id_creator_budget_approval);
                        for ($i = 0; $i < count($G); $i++) {
                            $CodePostRealisasi = $G[$i]['CodePostRealisasi'];
                            $this->m_budgeting->makeCanBeDelete('db_budgeting.cfg_postrealisasi', 'CodePostRealisasi', $CodePostRealisasi);
                            // insert to budget_left for using on PR
                            $ID_creator_budget = $G[$i]['ID'];
                            $Value = $G[$i]['SubTotal'];
                            $dtSave = array(
                                'ID_creator_budget' => $ID_creator_budget,
                                'Value' => $Value,
                            );
                            $this->db->insert('db_budgeting.budget_left', $dtSave);
                        }

                        // Notif All Approve to JsonStatus allkey
                        $G_div = $this->m_budgeting->SearchDepartementBudgeting($Departement);
                        //$NameDepartement = $G_div[0]['NameDepartement'];
                        $Code = $G_div[0]['Code'];
                        $arr_to = array();
                        for ($i = 0; $i < count($JsonStatus); $i++) {
                            $arr_to[] = $JsonStatus[$i]['NIP'];
                        }

                        $data = array(
                            'auth' => 's3Cr3T-G4N',
                            'Logging' => array(
                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> Budget All Approve of ' . $Code,
                                'Description' => 'Budget ' . $Code . ' has been all approve and posting date at : ' . $datasave['PostingDate'],
                                'URLDirect' => 'budgeting_entry',
                                'CreatedBy' => $NIP,
                            ),
                            'To' => array(
                                'NIP' => $arr_to,
                            ),
                            'Email' => 'No',
                        );

                        $url = url_pas . 'rest2/__send_notif_browser';
                        $token = $this->jwt->encode($data, "UAP)(*");
                        $this->m_master->apiservertoserver($url, $token);
                    }
                }

                if ($arr_upd['Status'] == 2) {
                    if ($dataToken['NoteDel'] != '' || $dataToken['NoteDel'] != null) {
                        $Desc .= '</br> {' . $dataToken['NoteDel'] . '}';
                    }

                    // Notif Reject to JsonStatus key 0
                    $IDdiv = $G_data[0]['Departement'];
                    $G_div = $this->m_budgeting->SearchDepartementBudgeting($IDdiv);
                    //$NameDepartement = $G_div[0]['NameDepartement'];
                    $Code = $G_div[0]['Code'];
                    // Send Notif for user 
                    $data = array(
                        'auth' => 's3Cr3T-G4N',
                        'Logging' => array(
                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> Budget Reject of ' . $Code,
                            'Description' => 'Budget ' . $Code . ' has been rejected by ' . $NameFor_NIP,
                            'URLDirect' => 'budgeting_entry',
                            'CreatedBy' => $NIP,
                        ),
                        'To' => array(
                            'NIP' => array($JsonStatus[0]['NIP']),
                        ),
                        'Email' => 'No',
                    );

                    $url = url_pas . 'rest2/__send_notif_browser';
                    $token = $this->jwt->encode($data, "UAP)(*");
                    $this->m_master->apiservertoserver($url, $token);
                }
                // save to log
                $this->m_budgeting->log_budget($id_creator_budget_approval, $Desc, $NIP);

                echo json_encode($msg);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function budgeting_dashboard()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                // do action
                $this->load->model('budgeting/m_budgeting');
                $month = array(
                    'Jan',
                    'Feb',
                    'Mar',
                    'April',
                    'Mei',
                    'Jun',
                    'Jul',
                    'Aug',
                    'Sep',
                    'Okt',
                    'Nov',
                    'Des'
                );

                $YearActivated = $this->m_master->caribasedprimary('db_budgeting.cfg_dateperiod', 'Activated', 1);

                $StartMonth = 9;
                $EndMonth = 8;

                $st = $YearActivated[0]['StartPeriod'];
                $st = explode('-', $st);
                $StartMonth = (int) $st[1];

                $end = $YearActivated[0]['EndPeriod'];
                $end = explode('-', $end);
                $EndMonth = (int) $end[1];

                $Departement = $this->m_master->apiservertoserver(serverRoot . '/api/__getAllDepartementPU');
                $data = array();
                for ($i = 0; $i < count($Departement); $i++) {
                    $Code = $Departement[$i]['Code'];
                    $DepartementName = $Departement[$i]['Name2'];
                    // find ID_creator_budget_approval first
                    $G = $this->m_budgeting->get_creator_budget_approval($YearActivated[0]['Year'], $Code);
                    $ID_creator_budget_approval = 0;
                    if (count($G) > 0) {
                        $ID_creator_budget_approval = $G[0]['ID'];
                    }

                    $get = $this->m_budgeting->get_creator_budget($ID_creator_budget_approval);
                    $arr_temp = array();
                    for ($j = 0; $j < count($get); $j++) {
                        // get data to show in dashboard'
                        $DetailMonth = (array) json_decode($get[$j]['DetailMonth'], true);
                        $UnitCost = $get[$j]['UnitCost'];
                        for ($l = 0; $l < count($DetailMonth); $l++) {
                            $month_get = $DetailMonth[$l]['month'];
                            $aa = explode('-', $month_get);
                            $m1 = (int)$aa[1];
                            $value = $DetailMonth[$l]['value'];
                            $value = $value * $UnitCost;

                            // find month exist
                            $b = false;
                            for ($k = 0; $k < count($arr_temp); $k++) {
                                $m2 = $arr_temp[$k]['month'];
                                if ($m1 == $m2) {
                                    $b = true;
                                    break;
                                }
                            }

                            if ($b) {
                                // exist
                                $arr_temp[$k]['value'] = $arr_temp[$k]['value'] + $value;
                            } else {
                                $arr_temp[] = array(
                                    'month' => $m1,
                                    'value' => $value,
                                );
                            }
                        }
                    }
                    $data[] = array(
                        'Code' => $Code,
                        'DepartementName' => $DepartementName,
                        'data' => $arr_temp,
                        'Abbr' => $Departement[$i]['Abbr'],
                    );
                }

                echo json_encode(
                    array(
                        'month' => $month, 'data' => $data, 'StartMonth' => $StartMonth, 'EndMonth' => $EndMonth
                    )
                );
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function InputCatalog_saveFormInput()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            $this->load->model('budgeting/m_budgeting');
            $path = FCPATH . 'uploads\\budgeting\\catalog\\';
            if ($auth) {
                $Input = $dataToken;
                $Item = $Input['Item'];
                $Desc = $Input['Desc'];
                $EstimaValue = $Input['EstimaValue'];
                $Departement = $Input['Departement'];
                $Detail = $Input['Detail'];
                $user = $Input['user'];
                $Satuan = $Input['Satuan'];
                $Detail = json_encode($Detail);

                if (array_key_exists('ID_category_catalog', $Input)) {
                    $ID_category_catalog = $Input['ID_category_catalog'];
                }

                $filename = $Input['Item'] . '_Uploaded';
                $filename = str_replace(" ", '_', $filename);
                $varchkApproval = function ($Departement) {
                    $aa = $this->m_master->caribasedprimary('db_purchasing.catalog_permission', 'Departement', $Departement);
                    if (count($aa) > 0) {
                        return true;
                    } else {
                        return false;
                    }
                };

                $chk = $varchkApproval($Departement);
                switch ($Input['Action']) {
                    case 'add':
                        if (array_key_exists('fileData', $_FILES)) {
                            $path = './uploads/budgeting/catalog';
                            $uploadFile = $this->m_rest->uploadDokumenMultiple($filename, 'fileData', $path);
                            if (is_array($uploadFile)) {
                                $uploadFile = implode(',', $uploadFile);
                                $dataSave = array(
                                    'Item' => $Item,
                                    'Desc' => $Desc,
                                    'EstimaValue' => $EstimaValue,
                                    'ID_category_catalog' => $ID_category_catalog,
                                    'Photo' => $uploadFile,
                                    'Departement' => $Departement,
                                    'DetailCatalog' => $Detail,
                                    'Satuan' => $Satuan,
                                    'CreatedBy' => $user,
                                    'CreatedAt' => date('Y-m-d'),
                                    'Approval' => ($chk) ? 1 : 0,
                                    'ApprovalBy' => ($chk) ? $user : '',
                                    'ApprovalAt' => ($chk) ? date('Y-m-d H:i:s') : NULL,
                                    'LastUpdateAt' => date('Y-m-d H:i:s'),
                                );
                                $this->db->insert('db_purchasing.m_catalog', $dataSave);

                                // Send Notif for Purchasing 
                                $G_emp = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $user);
                                $NameFor_NIP = $G_emp[0]['Name'];
                                $G_div = $this->m_budgeting->SearchDepartementBudgeting($Departement);
                                $NameDepartement = $G_div[0]['NameDepartement'];
                                $Code = $G_div[0]['Code'];
                                $data = array(
                                    'auth' => 's3Cr3T-G4N',
                                    'Logging' => array(
                                        'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Catalog ' . $Code . ' has been added',
                                        'Description' => 'Catalog ' . $Code . ' has been added by ' . $NameFor_NIP,
                                        'URLDirect' => 'purchasing/master/catalog',
                                        'CreatedBy' => $user,
                                    ),
                                    'To' => array(
                                        'Div' => array(4),
                                    ),
                                    'Email' => 'No',
                                );

                                $url = url_pas . 'rest2/__send_notif_browser';
                                $token = $this->jwt->encode($data, "UAP)(*");
                                $this->m_master->apiservertoserver($url, $token);

                                echo json_encode(array('msg' => 'Saved', 'status' => 1));
                            } else {
                                echo json_encode(array('msg' => $uploadFile, 'status' => 0));
                            }
                        } else {
                            $dataSave = array(
                                'Item' => $Item,
                                'Desc' => $Desc,
                                'EstimaValue' => $EstimaValue,
                                'ID_category_catalog' => $ID_category_catalog,
                                'Photo' => '',
                                'Departement' => $Departement,
                                'DetailCatalog' => $Detail,
                                'Satuan' => $Satuan,
                                'CreatedBy' => $user,
                                'CreatedAt' => date('Y-m-d'),
                                'Approval' => ($chk) ? 1 : 0,
                                'ApprovalBy' => ($chk) ? $user : '',
                                'ApprovalAt' => ($chk) ? date('Y-m-d H:i:s') : NULL,
                                'LastUpdateAt' => date('Y-m-d H:i:s'),
                            );
                            $this->db->insert('db_purchasing.m_catalog', $dataSave);

                            // Send Notif for Purchasing 
                            $G_emp = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $user);
                            $NameFor_NIP = $G_emp[0]['Name'];
                            $G_div = $this->m_budgeting->SearchDepartementBudgeting($Departement);
                            //$NameDepartement = $G_div[0]['NameDepartement'];
                            $Code = $G_div[0]['Code'];
                            $data = array(
                                'auth' => 's3Cr3T-G4N',
                                'Logging' => array(
                                    'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Catalog ' . $Code . ' has been added',
                                    'Description' => 'Catalog ' . $Code . ' has been added by ' . $NameFor_NIP,
                                    'URLDirect' => 'purchasing/master/catalog',
                                    'CreatedBy' => $user,
                                ),
                                'To' => array(
                                    'Div' => array(4),
                                ),
                                'Email' => 'No',
                            );

                            $url = url_pas . 'rest2/__send_notif_browser';
                            $token = $this->jwt->encode($data, "UAP)(*");
                            $this->m_master->apiservertoserver($url, $token);

                            echo json_encode(array('msg' => 'Saved', 'status' => 1));
                        }

                        break;
                    case 'edit':
                        $Get_Data = $this->m_master->caribasedprimary('db_purchasing.m_catalog', 'ID', $Input['ID']);
                        $Status = $Get_Data[0]['Status'];
                        $ApprovalGet = $Get_Data[0]['Approval'];
                        if ($Status == 1) {
                            if (array_key_exists('fileData', $_FILES)) {
                                $path = './uploads/budgeting/catalog';
                                $uploadFile = $this->m_rest->uploadDokumenMultiple($filename, 'fileData', $path);
                                if (is_array($uploadFile)) {
                                    $uploadFile = implode(',', $uploadFile);
                                    // get all file first
                                    $F = $Get_Data[0]['Photo'];
                                    if ($F != '' && $F != null && !empty($F)) {
                                        $uploadFile = $uploadFile . ',' . $F;
                                    }
                                    $dataSave = array(
                                        'Item' => $Item,
                                        'Desc' => $Desc,
                                        'EstimaValue' => $EstimaValue,
                                        'ID_category_catalog' => $ID_category_catalog,
                                        'Photo' => $uploadFile,
                                        'Departement' => $Departement,
                                        'DetailCatalog' => $Detail,
                                        'Satuan' => $Satuan,
                                        // 'Approval' => ($ApprovalGet == -1) ? 0 : $ApprovalGet,
                                        'Approval' => ($chk) ? 1 : 0,
                                        'LastUpdateBy' => $user,
                                        'LastUpdateAt' => date('Y-m-d H:i:s'),
                                    );
                                    $this->db->where('ID', $Input['ID']);
                                    $this->db->update('db_purchasing.m_catalog', $dataSave);

                                    if (!$chk) {
                                        // Send Notif for Purchasing 
                                        $G_emp = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $user);
                                        $NameFor_NIP = $G_emp[0]['Name'];
                                        $G_div = $this->m_budgeting->SearchDepartementBudgeting($Departement);
                                        $NameDepartement = $G_div[0]['NameDepartement'];
                                        $Code = $G_div[0]['Code'];
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Catalog ' . $Code . ' has been edited',
                                                'Description' => 'Catalog ' . $Code . ' has been added by ' . $NameFor_NIP,
                                                'URLDirect' => 'purchasing/master/catalog',
                                                'CreatedBy' => $user,
                                            ),
                                            'To' => array(
                                                'Div' => array(4),
                                            ),
                                            'Email' => 'No',
                                        );

                                        $url = url_pas . 'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data, "UAP)(*");
                                        $this->m_master->apiservertoserver($url, $token);
                                    }

                                    echo json_encode(array('msg' => 'Saved', 'status' => 1));
                                } else {
                                    echo json_encode(array('msg' => $uploadFile, 'status' => 0));
                                }
                            } else {
                                $dataSave = array(
                                    'Item' => $Item,
                                    'Desc' => $Desc,
                                    'EstimaValue' => $EstimaValue,
                                    'ID_category_catalog' => $ID_category_catalog,
                                    'Departement' => $Departement,
                                    'DetailCatalog' => $Detail,
                                    'Satuan' => $Satuan,
                                    // 'Approval' => ($ApprovalGet == -1) ? 0 : $ApprovalGet,
                                    'Approval' => ($chk) ? 1 : 0,
                                    'LastUpdateBy' => $user,
                                    'LastUpdateAt' => date('Y-m-d H:i:s'),
                                );
                                $this->db->where('ID', $Input['ID']);
                                $this->db->update('db_purchasing.m_catalog', $dataSave);
                                if (!$chk) {
                                    // Send Notif for Purchasing 
                                    $G_emp = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $user);
                                    $NameFor_NIP = $G_emp[0]['Name'];
                                    $G_div = $this->m_budgeting->SearchDepartementBudgeting($Departement);
                                    $NameDepartement = $G_div[0]['NameDepartement'];
                                    $Code = $G_div[0]['Code'];
                                    $data = array(
                                        'auth' => 's3Cr3T-G4N',
                                        'Logging' => array(
                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Catalog ' . $Code . ' has been edited',
                                            'Description' => 'Catalog ' . $Code . ' has been added by ' . $NameFor_NIP,
                                            'URLDirect' => 'purchasing/master/catalog',
                                            'CreatedBy' => $user,
                                        ),
                                        'To' => array(
                                            'Div' => array(4),
                                        ),
                                        'Email' => 'No',
                                    );

                                    $url = url_pas . 'rest2/__send_notif_browser';
                                    $token = $this->jwt->encode($data, "UAP)(*");
                                    $this->m_master->apiservertoserver($url, $token);
                                }
                                echo json_encode(array('msg' => 'Saved', 'status' => 1));
                            }
                        } else {
                            echo json_encode(array('msg' => 'The data has been used for transaction, Cannot be action', 'status' => 0));
                        }

                        break;
                    case 'delete':
                        $sql = 'select * from db_budgeting.pr_detail where ID_m_catalog = ? limit 1';
                        $query = $this->db->query($sql, array($Input['ID']))->result_array();
                        if (count($query) == 0) {
                            $Get_Data = $this->m_master->caribasedprimary('db_purchasing.m_catalog', 'ID', $Input['ID']);
                            $F = $Get_Data[0]['Photo'];
                            $F = explode(',', $F);

                            $this->db->where('ID', $Input['ID']);
                            $this->db->delete('db_purchasing.m_catalog');

                            // delete all file
                            for ($i = 0; $i < count($F); $i++) {
                                unlink($path . $F[$i]);
                            }

                            echo json_encode(array(''));
                        } else {
                            echo json_encode(array('The data has been used for transaction, Cannot be action'));
                        }
                        break;
                    case 'approve':
                        $dataSave = array(
                            'Approval' => 1,
                            'ApprovalBy' => $user,
                            'ApprovalAt' => date('Y-m-d H:i:s'),
                            'Reason' => '',
                            'LastUpdateAt' => date('Y-m-d H:i:s'),
                        );
                        $this->db->where('ID', $Input['ID']);
                        $this->db->update('db_purchasing.m_catalog', $dataSave);
                        echo json_encode(array(''));
                        break;
                    case 'reject':
                        $dataSave = array(
                            'Approval' => -1,
                            'ApprovalBy' => $this->session->userdata('NIP'),
                            'ApprovalAt' => date('Y-m-d H:i:s'),
                            'Reason' => $Input['Reason'],
                            'LastUpdateAt' => date('Y-m-d H:i:s'),
                        );
                        $this->db->where('ID', $Input['ID']);
                        $this->db->update('db_purchasing.m_catalog', $dataSave);
                        echo json_encode(array(''));
                        break;
                    case 'status':
                        $dataSave = array(
                            'Active' => 0,
                            'LastUpdateBy' => $this->session->userdata('NIP'),
                            'LastUpdateAt' => date('Y-m-d H:i:s'),
                        );
                        $this->db->where('ID', $Input['ID']);
                        $this->db->update('db_purchasing.m_catalog', $dataSave);
                        echo json_encode(array(''));
                        break;
                    default:
                        # code...
                        break;
                }
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function show_circulation_sheet()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $rs = array();
                $PRCode = $dataToken['PRCode'];
                $sql = 'select a.Desc,a.Date,b.NIP,b.Name from db_budgeting.pr_circulation_sheet as a 
                        join db_employees.employees as b on a.By = b.NIP
                        where a.PRCode = ?
                        ';
                $query = $this->db->query($sql, array($PRCode))->result_array();
                // $rs['PR_Process'] = $query;        
                $rs = $query;
                echo json_encode($rs);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function show_circulation_sheet_po()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $rs = array();
                $Code = $dataToken['Code'];
                $sql = 'select a.Desc,a.Date,b.NIP,b.Name from db_purchasing.po_circulation_sheet as a 
                        join db_employees.employees as b on a.By = b.NIP
                        where a.Code = ?
                        ';
                $query = $this->db->query($sql, array($Code))->result_array();
                $rs = $query;
                echo json_encode($rs);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function log_budgeting()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $rs = array();
                $id_creator_budget_approval = $dataToken['id_creator_budget_approval'];
                $sql = 'select a.Desc,a.Date,b.NIP,b.Name from db_budgeting.log_budget as a 
                        join db_employees.employees as b on a.By = b.NIP
                        where a.ID_creator_budget_approval = ?
                        ';
                $query = $this->db->query($sql, array($id_creator_budget_approval))->result_array();
                for ($i = 0; $i < count($query); $i++) {  // update textarea fill to nl2br
                    $query[$i]['Desc'] = nl2br($query[$i]['Desc']);
                }
                $rs = $query;
                echo json_encode($rs);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function getAllBudget()
    {
        $msg = '';
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('budgeting/m_budgeting');
                $dt = array();
                $Year = $dataToken['Year'];
                $Query = $this->m_budgeting->GetAllBudgetGrouping($Year);
                $dt = $Query;
                echo json_encode($dt);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }


    public function TestpostdataFrom_PowerApps()
    {

        $data = file_get_contents('php://input');
        $data_json = (array) json_decode($data, true);
        if ($data_json) {
            $dataSave = array(
                'Value' => $data_json['test'],
            );
            $this->db->insert('test.test', $dataSave);
            if ($this->db->affected_rows() > 0) {
                echo json_encode(array('msg' => 'The file has been successfully uploaded', 'status' => 1));
            } else {
                echo json_encode(array('msg' => '000', 'status' => 0));
            }
        } else {
            echo json_encode(array('msg' => '000', 'status' => 0));
        }
    }

    public function get_data_pr($Status)
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('budgeting/m_pr_po');
                $requestData = $_REQUEST;
                $StatusQuery = ($Status == 'All') ? '' : 'where a.Status = ' . $Status;
                if (array_key_exists('PurchasingStatus', $dataToken)) {
                    if ($StatusQuery == '') {
                        $StatusQuery = 'where b.Status ' . $dataToken['PurchasingStatus'];
                    } else {
                        $StatusQuery .= ' and b.Status ' . $dataToken['PurchasingStatus'];
                    }
                    // $StatusQuery = ($StatusQuery == '') ? 'where b.Status '.$dataToken['PurchasingStatus'] : ' and b.Status '.$dataToken['PurchasingStatus'] ;
                }

                if (array_key_exists('Item_pending', $dataToken)) {
                    if ($StatusQuery == '') {
                        $StatusQuery = 'where b.Item_pending ' . $dataToken['Item_pending'];
                    } else {
                        $StatusQuery .= ' and b.Item_pending ' . $dataToken['Item_pending'];
                    }
                    // $StatusQuery = ($StatusQuery == '') ? 'where b.Item_pending '.$dataToken['Item_pending'] : ' and b.Item_pending '.$dataToken['Item_pending'] ;
                }


                $sqltotalData = 'select count(*) as total from db_budgeting.pr_create as a left join db_purchasing.pr_status as b on a.PRCode = b.PRCode ' . $StatusQuery;
                $querytotalData = $this->db->query($sqltotalData)->result_array();
                $totalData = $querytotalData[0]['total'];

                if ($dataToken['action_edit'] != '') {
                    $totalData++;
                }

                $StatusQuery = ($Status == 'All') ? '' : 'and a.Status = ' . $Status;
                if (array_key_exists('PurchasingStatus', $dataToken)) {
                    if ($StatusQuery == '') {
                        $StatusQuery = 'and b.Status ' . $dataToken['PurchasingStatus'];
                    } else {
                        $StatusQuery .= ' and b.Status ' . $dataToken['PurchasingStatus'];
                    }
                    // $StatusQuery = ($StatusQuery == '') ? 'where b.Status '.$dataToken['PurchasingStatus'] : ' and b.Status '.$dataToken['PurchasingStatus'] ;
                }

                if (array_key_exists('Item_pending', $dataToken)) {
                    if ($StatusQuery == '') {
                        $StatusQuery = 'and b.Item_pending ' . $dataToken['Item_pending'];
                    } else {
                        $StatusQuery .= ' and b.Item_pending ' . $dataToken['Item_pending'];
                    }
                }

                $sql = 'select a.*,b.Item_proc,b.Item_done,Item_pending,b.Status as StatusPRPO from 
                        (
                            select a.PRCode,a.Year,a.Departement,b.NameDepartement,a.CreatedBy,a.CreatedAt,a.Status,
                                            if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = 3,"Reject","Cancel") ) ))
                                            as StatusName, a.JsonStatus,a.PostingDate,a.Supporting_documents,c.DateNeeded
                                            from db_budgeting.pr_create as a 
                            join (
                            select * from (
                            select CONCAT("AC.",ID) as ID, NameEng as NameDepartement from db_academic.program_study where Status = 1
                            UNION
                            select CONCAT("NA.",ID) as ID, Division as NameDepartement from db_employees.division where StatusDiv = 1
                            UNION
                            select CONCAT("FT.",ID) as ID, NameEng as NameDepartement from db_academic.faculty where StBudgeting = 1
                            ) aa
                            ) as b on a.Departement = b.ID
                            join (
                                    select * from (
                                            select * from db_budgeting.pr_detail ORDER BY DateNeeded asc
                                    ) za GROUP BY PRCode
                            ) as c on c.PRCode = a.PRCode
                        )a
                            LEFT JOIN db_purchasing.pr_status as b on a.PRCode = b.PRCode
                       ';

                $sql .= ' where (a.PRCode LIKE "%' . $requestData['search']['value'] . '%" or a.NameDepartement LIKE "' . $requestData['search']['value'] . '%") ' . $StatusQuery;
                if ($Status == 'All') {
                    $sql .= ' ORDER BY a.PRCode Desc LIMIT ' . $requestData['start'] . ' , ' . $requestData['length'] . ' ';
                } else {
                    $sql .= ' ORDER BY a.DateNeeded asc LIMIT ' . $requestData['start'] . ' , ' . $requestData['length'] . ' ';
                }


                // for edit in open po
                if ($dataToken['action_edit'] != '') {
                    // find number PR dari PO Number
                    $Code = $dataToken['POCode'];
                    $G_pr_po = $this->m_pr_po->Get_data_po_by_Code($Code);
                    $po_detail = $G_pr_po['po_detail'];
                    $temp = array();
                    for ($i = 0; $i < count($po_detail); $i++) {
                        $temp[] = '"' . $po_detail[$i]['PRCode'] . '"';
                    }

                    $temp = implode(',', $temp);


                    $sql = 'select * from (
                                select a.*,b.Item_proc,b.Item_done,Item_pending,b.Status as StatusPRPO from 
                                (
                                    select a.PRCode,a.Year,a.Departement,b.NameDepartement,a.CreatedBy,a.CreatedAt,a.Status,
                                                    if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = 3,"Reject","Cancel") ) ))
                                                    as StatusName, a.JsonStatus,a.PostingDate,a.Supporting_documents
                                                    from db_budgeting.pr_create as a 
                                    join (
                                    select * from (
                                    select CONCAT("AC.",ID) as ID, NameEng as NameDepartement from db_academic.program_study where Status = 1
                                    UNION
                                    select CONCAT("NA.",ID) as ID, Division as NameDepartement from db_employees.division where StatusDiv = 1
                                    UNION
                                    select CONCAT("FT.",ID) as ID, NameEng as NameDepartement from db_academic.faculty where StBudgeting = 1
                                    ) aa
                                    ) as b on a.Departement = b.ID
                                )a
                                    LEFT JOIN db_purchasing.pr_status as b on a.PRCode = b.PRCode
                                    where a.PRCode in (' . $temp . ') 
                                    UNION
                                select a.*,b.Item_proc,b.Item_done,Item_pending,b.Status as StatusPRPO from 
                                (
                                    select a.PRCode,a.Year,a.Departement,b.NameDepartement,a.CreatedBy,a.CreatedAt,a.Status,
                                                    if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = 3,"Reject","Cancel") ) ))
                                                    as StatusName, a.JsonStatus,a.PostingDate,a.Supporting_documents
                                                    from db_budgeting.pr_create as a 
                                    join (
                                    select * from (
                                    select CONCAT("AC.",ID) as ID, NameEng as NameDepartement from db_academic.program_study where Status = 1
                                    UNION
                                    select CONCAT("NA.",ID) as ID, Division as NameDepartement from db_employees.division where StatusDiv = 1
                                    UNION
                                    select CONCAT("FT.",ID) as ID, NameEng as NameDepartement from db_academic.faculty where StBudgeting = 1
                                    ) aa
                                    ) as b on a.Departement = b.ID
                                )a
                                    LEFT JOIN db_purchasing.pr_status as b on a.PRCode = b.PRCode
                                    where a.PRCode like "%%" ' . $StatusQuery . ' 
                            ) aa
                           ';
                    $sql .= ' where (PRCode LIKE "%' . $requestData['search']['value'] . '%" or NameDepartement LIKE "' . $requestData['search']['value'] . '%") ';

                    // $sql.= ' ORDER BY PRCode Desc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
                    $sql .= ' LIMIT ' . $requestData['start'] . ' , ' . $requestData['length'] . ' ';

                    // print_r($sql);die();
                }

                $query = $this->db->query($sql)->result_array();

                $No = $requestData['start'] + 1;
                $data = array();

                for ($i = 0; $i < count($query); $i++) {
                    $row = $query[$i];
                    $query[$i]['No'] = $No;
                    $JsonStatus = (array)json_decode($row['JsonStatus'], true);
                    $arr = array();
                    if (count($JsonStatus) > 0) {
                        for ($j = 1; $j < count($JsonStatus); $j++) {
                            $getName = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $JsonStatus[$j]['NIP']);
                            $Name = $getName[0]['Name'];
                            $StatusInJson = $JsonStatus[$j]['Status'];
                            switch ($StatusInJson) {
                                case '1':
                                    $stjson = '<i class="fa fa-check" style="color: green;"></i>';
                                    break;
                                case '2':
                                    $stjson = '<i class="fa fa-times" aria-hidden="true" style="color: red;"></i>';
                                    break;
                                default:
                                    $stjson = "-";
                                    break;
                            }
                            $arr[] = $stjson . '<br>' . 'Approver : ' . $Name . '<br>' . 'Approve At : ' . $JsonStatus[$j]['ApproveAt'];
                        }
                    }
                    $query[$i]['Approval'] = $arr;
                    $No++;
                }

                $data = $query;

                $json_data = array(
                    "draw"            => intval($requestData['draw']),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalData),
                    "data"            => $data
                );
                echo json_encode($json_data);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function show_pr_detail()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('budgeting/m_budgeting');
                $this->load->model('budgeting/m_pr_po');
                $arr_result = array('pr_create' => array(), 'pr_detail' => array());
                $arr_result['pr_create'] = $this->m_pr_po->GetPR_CreateByPRCode($dataToken['PRCode']);
                // $arr_result['pr_detail'] = $this->m_pr_po->GetPR_DetailByPRCode($dataToken['PRCode']);
                $POCode = '';
                if (array_key_exists('POCode', $dataToken)) {
                    $POCode = $dataToken['POCode'];
                }
                $arr_result['pr_detail'] = $this->m_pr_po->GetPR_DetailByPRCode_UN_PO($dataToken['PRCode'], $POCode);
                echo json_encode($arr_result);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function show_pr_detail_multiple_pr_code()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('budgeting/m_budgeting');
                $this->load->model('budgeting/m_pr_po');
                $arr_result = array('pr_create' => array(), 'pr_detail' => array());
                $arr_pr_code = json_decode(json_encode($dataToken['PRCode']), true);
                for ($i = 0; $i < count($arr_pr_code); $i++) {
                    $arr_pr_code[$i] = '"' . $arr_pr_code[$i] . '"';
                }
                $arr_result['pr_create'] = $this->m_pr_po->GetPR_CreateByPRCode_multiple_pr_code($arr_pr_code);
                $POCode = '';
                if (array_key_exists('POCode', $dataToken)) {
                    $POCode = $dataToken['POCode'];
                }
                $arr_result['pr_detail'] = $this->m_pr_po->GetPR_DetailByPRCode_UN_PO_multiple_pr_code($arr_pr_code, $POCode);
                echo json_encode($arr_result);
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function cek_deadline_payment_semester_antara()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $arr = array('status' => 0, 'StartKRS' => '0000-00-00', 'EndKRS' => '0000-00-00');
                $SemesterID = $dataToken['SemesterID']; // SemesterID semester antara
                // $G_data = $this->m_master->caribasedprimary('db_academic.sa_academic_years', 'SASemesterID', $SemesterID);

                $G_data = $this->db->select('sa_ca.*')
                                   ->join('db_academic.semester_antara as sa','sa.SemesterID = s.ID','join')
                                   ->join('db_academic.sa_academic_years as sa_ca','sa_ca.SASemesterID = sa.ID','join')
                                   ->where('sa.SemesterID',$SemesterID)
                                   ->get('db_academic.semester as s')
                                   ->result_array();
                if (count($G_data) > 0) {
                    $EndKRS = $G_data[0]['EndKRS'];
                    $StartKRS = $G_data[0]['StartKRS'];
                    $chkTgl = $this->m_master->checkTglNow($EndKRS);

                    $arr = array('status' => 0, 'StartKRS' => date("d M Y", strtotime($StartKRS)), 'EndKRS' => date("d M Y", strtotime($EndKRS)));
                    if (!$chkTgl) {
                        $arr['status'] = 1;
                    } else {
                        $arr['status'] = 0;
                    }
                }

                echo json_encode($arr); // melewati
            } else {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        } catch (Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function getAdminCRM()
    {

        $data = $this->db->get('db_admission.crm_admin')->result_array();

        $result = [];
        if (count($data) > 0) {
            foreach ($data as $item) {
                array_push($result, $item['NIP']);
            }
        }

        return print_r(json_encode($result));
    }

    public function getDetailCourseByIDAttd($ID_Attd)
    {

        $dataDetail = $this->db->query('SELECT attd.ScheduleID, smt.Name AS SemesterName, s.ClassGroup, cd.TotalSKS AS Credit, 
                                                    mk.Name AS Course, em.Name As CoordinatorName, s.Coordinator, sd.StartSessions, sd.EndSessions, cl.Room, d.Name AS DayName,
                                                    s.TotalAssigment, gc.Assg1, gc.Assg2, gc.Assg3, gc.Assg4, gc.Assg5, gc.Assigment, gc.UTS, gc.UAS, gc.Status AS StatusSyllabus
                                                    FROM db_academic.attendance attd
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = attd.ScheduleID)
                                                    LEFT JOIN db_academic.grade_course gc ON (gc.SemesterID = attd.SemesterID AND gc.ScheduleID = attd.ScheduleID)
                                                    LEFT JOIN db_academic.semester smt ON (smt.ID = attd.SemesterID)
                                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                    LEFT JOIN db_academic.schedule_details sd ON (sd.ScheduleID = s.ID)
                                                    LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                    LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                    WHERE attd.ID = "' . $ID_Attd . '" GROUP BY s.ID ')->result_array();

        if (count($dataDetail) > 0) {
            $ScheduleID = $dataDetail[0]['ScheduleID'];
            $dataDetail[0]['DetailProdi'] = $this->db->query('SELECT ps.Code, ps.Name FROM db_academic.schedule_details_course sdc 
                                                LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                                WHERE sdc.ScheduleID = "' . $ScheduleID . '" GROUP BY sdc.ProdiID')->result_array();

            $dataDetail[0]['TeamTeaching'] = $this->db->query('SELECT em.NIP, em.Name FROM db_academic.schedule_team_teaching stt 
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                    WHERE stt.ScheduleID = "' . $ScheduleID . '" ORDER BY em.NIP ASC')->result_array();

            $dataDetail[0]['TotalStudent'] = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.attendance_students 
                                                            WHERE ID_Attd = "' . $ID_Attd . '" ')->result_array()[0]['Total'];
        }

        return print_r(json_encode($dataDetail[0]));
    }
}
