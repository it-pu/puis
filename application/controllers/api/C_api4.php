<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_api4 extends CI_Controller
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

    public function crudAgregatorTB3()
    {
        $Input = $this->getInputToken();
        $action = $Input['action'];
        switch ($action) {
            case 'readDataDosenTidakTetap':
                $sql = 'select a.';

                break;

            default:
                # code...
                break;
        }
    }

    // Search Employees (termasuk dosen di dalamnya)
    public function searchEmployees()
    {

        $key = $this->input->get('key');
        $limit = $this->input->get('limit');

        $data = $this->m_search->searchEmployees($key, $limit);

        return print_r(json_encode($data));
    }

    public function crudPrefrencesEmployees()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'getDataRelatedNIP') {
            $NIP = $data_arr['NIP'];
            $data = $this->m_search->getDataRelatedNIP($NIP);

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'setToDataRelatedNIP') {
            $NIP = $data_arr['NIP'];
            $NIPInduk = $data_arr['NIPInduk'];
            $data = $this->m_search->setToDataRelatedNIP($NIPInduk, $NIP);

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removeDataRelatedNIP') {
            $ID = $data_arr['ID'];
            $this->db->where('ID', $ID);
            $this->db->delete('db_employees.related_nip');

            return print_r(1);
        } else if ($data_arr['action'] == 'readAllDataRelatedNIP') {

            $data = $this->db->query('SELECT em.NIP, em.Name FROM db_employees.related_nip rn 
                                                LEFT JOIN db_employees.employees em ON (em.NIP = rn.NIP)
                                                GROUP BY rn.NIP')->result_array();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['Details'] = $this->db->query('SELECT em.NIP, em.Name FROM db_employees.related_nip rn 
                                                LEFT JOIN db_employees.employees em ON (em.NIP = rn.RelatedNIP)
                                                WHERE rn.NIP = "' . $data[$i]['NIP'] . '" ')->result_array();
                }
            }

            return print_r(json_encode($data));
        }
    }

    /*ADDED BY FEBRI @ JAN 2020*/
    public function getStdInsurance()
    {
        $data = $this->input->post();
        $json = array();
        if ($data) {
            $key = "UAP)(*";
            $data_arr = (array) $this->jwt->decode($data['token'], $key);
            $isExist = $this->m_api->getStdInsurance(array("a.NPM" => $data_arr['NPM']))->row();
            if (!empty($isExist)) {
                $json = $isExist;
            }
        }

        echo json_encode($json);
    }


    public function detailEmployeeOBJ()
    {
        $this->load->model(array('General_model', 'hr/m_hr'));
        $data = $this->input->post();
        $json = array();
        if ($data) {
            $key = "UAP)(*";
            $data_arr = (array) $this->jwt->decode($data['token'], $key);
            $isExist = $this->General_model->fetchData("db_employees.employees", array("NIP" => $data_arr['NIP']))->row();
            if (!empty($isExist)) {
                //$isExist->MyCareer = $this->General_model->fetchData("db_employees.employees_career",array("NIP"=>$isExist->NIP))->result();
                $isExist->MyHistorical = $this->General_model->fetchData("db_employees.employees_joindate", array("NIP" => $isExist->NIP), "ID", "desc")->result();
                $isExist->MyCareer = $this->m_hr->getEmpCareer(array("a.NIP" => $isExist->NIP, "isShowSTO" => 0))->result();
                $isExist->MyBank = $this->General_model->fetchData("db_employees.employees_bank_account", array("NIP" => $isExist->NIP))->result();
                $isExist->MyEducation = $this->General_model->fetchData("db_employees.employees_educations", array("NIP" => $isExist->NIP))->result();
                $isExist->MyEducationNonFormal = $this->General_model->fetchData("db_employees.employees_educations_non_formal", array("NIP" => $isExist->NIP))->result();
                $isExist->MyEducationTraining = $this->General_model->fetchData("db_employees.employees_educations_training", array("NIP" => $isExist->NIP))->result();
                $isExist->MyFamily = $this->General_model->fetchData("db_employees.employees_family_member", array("NIP" => $isExist->NIP))->result();
                $isExist->MyExperience = $this->General_model->fetchData("db_employees.employees_experience", array("NIP" => $isExist->NIP))->result();
                $isExist->MyBPJS = $this->General_model->fetchData("db_employees.employees_bpjs", array("NIP" => $isExist->NIP), "Type", "asc")->result();
                $isExist->MyAllowance = $this->General_model->fetchData("db_employees.employees_allowance", array("NIP" => $isExist->NIP))->result();
                $json = $isExist;
            }
        }
        echo json_encode($json);
    }
    /*END ADDED BY FEBRI @ JAN 2020*/

    public function getTableMaster()
    {
        $input = $this->getInputToken2();
        if (array_key_exists('table', $input)) {
            $data = $this->m_master->showData_array($input['table']);
            echo json_encode($data);
        } else {
            echo '{"status":"999","message":"Not Authenfication"}';
        }
    }

    public function crudOnlineClass()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'getMonitoringAttd') {


            $ScheduleID = $data_arr['ScheduleID'];
            $Session = $data_arr['Session'];

            $dataLect = $this->m_rest->getAllLecturerByScheduleID($ScheduleID);

            // Get AttdID

            $dataArrAttdID = $this->db->query('SELECT attd.ID AS ID_Attd, d.NameEng, cl.Room, sd.StartSessions, sd.EndSessions 
                                                    FROM db_academic.attendance attd
                                                    LEFT JOIN db_academic.schedule_details sd ON (sd.ID = attd.SDID)
                                                    LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                    WHERE attd.ScheduleID = "' . $ScheduleID . '" 
                                                    ORDER BY sd.DayID ASC')->result_array();

            if (count($dataLect) > 0) {

                for ($i = 0; $i < count($dataLect); $i++) {

                    $d = $dataLect[$i];

                    // Cek Forum
                    $dataLect[$i]['Forum'] = $this->db->query('SELECT COUNT(*) AS Total FROM (SELECT ct.ID  
                                                            FROM db_academic.counseling_topic ct
                                                            WHERE ct.ScheduleID = "' . $ScheduleID . '" 
                                                            AND ct.Sessions = "' . $Session . '"
                                                             AND ct.CreateBy = "' . $d['NIP'] . '" 
                                                             UNION ALL
                                                             SELECT ct.ID FROM db_academic.counseling_comment cc
                                                             LEFT JOIN db_academic.counseling_topic ct ON (ct.ID = cc.TopicID)
                                                            WHERE ct.ScheduleID = "' . $ScheduleID . '" 
                                                            AND ct.Sessions = "' . $Session . '"
                                                             AND cc.UserID = "' . $d['NIP'] . '" ) xx  
                                                             ')->result_array()[0]['Total'];

                    // Cek Task
                    $dataLect[$i]['Task'] = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.schedule_task st
                                                                        WHERE st.ScheduleID = "' . $ScheduleID . '" 
                                                                        AND st.Session = "' . $Session . '"
                                                                        AND st.EntredBy = "' . $d['NIP'] . '" ')
                        ->result_array()[0]['Total'];

                    // Cek Material
                    $dataLect[$i]['Material'] = $this->db->query('SELECT sm.File, em.NIP, em.Name FROM db_academic.schedule_material sm 
                                                                            LEFT JOIN db_employees.employees em ON (em.NIP = sm.UpdateBy)
                                                                            WHERE sm.ScheduleID = "' . $ScheduleID . '"
                                                                             AND sm.Session = "' . $Session . '" ')->result_array();

                    // Cek Attendance
                    if (count($dataArrAttdID) > 0) {
                        $whereAttd = '';
                        for ($r = 0; $r < count($dataArrAttdID); $r++) {
                            $or = ($r != 0) ? ' OR ' : '';
                            $whereAttd = $whereAttd . $or . ' (al.ID_Attd = "' . $dataArrAttdID[$r]['ID_Attd'] . '" AND Meet = "' . $Session . '") ';
                        }

                        $SessionAttend = $this->db->query('SELECT * 
                                                                    FROM db_academic.attendance_lecturers al 
                                                                    WHERE al.NIP = "' . $d['NIP'] . '" 
                                                                    AND ( ' . $whereAttd . ') GROUP BY al.ID_Attd')->result_array();
                    }

                    $dataLect[$i]['SessionAttend'] = count($SessionAttend);
                    $dataLect[$i]['SessionAttendDetails'] = $SessionAttend;
                    $dataLect[$i]['SessionAttendSch'] = count($dataArrAttdID);
                }
            }

            $dataAttd = $this->db->query('SELECT attd.ID FROM db_academic.attendance attd WHERE 
                                                    attd.ScheduleID = "' . $ScheduleID . '" 
                                                    GROUP BY attd.ScheduleID')->result_array();
            $dataStd = [];
            if (count($dataAttd) > 0) {
                $dataStd = $this->db->query('SELECT auth.NPM, auth.Name FROM db_academic.attendance_students ats 
                                            LEFT JOIN db_academic.auth_students auth ON (auth.NPM = ats.NPM)
                                            WHERE ats.ID_Attd = "' . $dataAttd[0]['ID'] . '" 
                                            GROUP BY ats.NPM ORDER BY auth.NPM ASC')->result_array();
            }


            if (count($dataStd) > 0) {
                for ($i = 0; $i < count($dataStd); $i++) {
                    $d = $dataStd[$i];

                    // Comment
                    $dataStd[$i]['TotalComment'] = $this->db->query('SELECT COUNT(*) AS Total 
                                                    FROM db_academic.counseling_comment cc 
                                                    LEFT JOIN db_academic.counseling_topic ct ON (ct.ID = cc.TopicID)
                                                    WHERE ct.ScheduleID = "' . $ScheduleID . '"
                                                    AND ct.Sessions = "' . $Session . '" 
                                                    AND cc.UserID = "' . $d['NPM'] . '" ')->result_array()[0]['Total'];

                    // Task
                    $dataStd[$i]['TotalTask'] = $this->db->query('SELECT std.ID FROM db_academic.schedule_task_student std
                                                                    LEFT JOIN db_academic.schedule_task st ON (st.ID = std.IDST)
                                                                    WHERE st.ScheduleID = "' . $ScheduleID . '"
                                                                     AND st.Session = "' . $Session . '" 
                                                                     AND std.NPM = "' . $d['NPM'] . '"')->result_array();

                    $dataStd[$i]['TotalTaskRevisi'] = $this->db->query('SELECT stsr.EntredAt, em.Name 
                                                                                FROM db_academic.schedule_task_student_remove stsr 
                                                                                LEFT JOIN db_academic.schedule_task st 
                                                                                ON (st.ID = stsr.IDST)
                                                                                LEFT JOIN db_employees.employees em 
                                                                                ON (em.NIP = stsr.EntredBy) 
                                                                                WHERE st.ScheduleID = "' . $ScheduleID . '"
                                                                                AND st.Session = "' . $Session . '"
                                                                                AND stsr.NPM = "' . $d['NPM'] . '"
                                                                                 ORDER BY stsr.ID ASC')
                        ->result_array();

                    // Quiz
                    $dataStd[$i]['TotalQuiz'] = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.q_quiz_students qqs
                                                        LEFT JOIN db_academic.q_quiz qq 
                                                        ON (qq.ID = qqs.QuizID)
                                                        WHERE qqs.NPM = "' . $d['NPM'] . '"
                                                        AND qqs.WorkDuration IS NOT NULL
                                                        AND qqs.WorkDuration > 0
                                                        AND qq.ScheduleID = "' . $ScheduleID . '"
                                                         AND qq.Session = "' . $Session . '" ')->result_array()[0]['Total'];




                    // Attendance
                    $SessionAttend = [];
                    $SessionAttendTotal = 0;
                    if (count($dataArrAttdID) > 0) {
                        $whereAttd = '';
                        for ($r = 0; $r < count($dataArrAttdID); $r++) {
                            $or = ($r != 0) ? ' OR ' : '';
                            $whereAttd = $whereAttd . $or . ' al.ID_Attd = "' . $dataArrAttdID[$r]['ID_Attd'] . '" ';
                        }

                        $SessionAttend = $this->db->query('SELECT al.M' . $Session . ' FROM db_academic.attendance_students al 
                                                                WHERE al.NPM = "' . $d['NPM'] . '" AND (' . $whereAttd . ')')->result_array();

                        if (count($SessionAttend) > 0) {
                            for ($s = 0; $s < count($SessionAttend); $s++) {
                                $ses = ($SessionAttend[$s]['M' . $Session] != null && $SessionAttend[$s]['M' . $Session] != ''
                                    && $SessionAttend[$s]['M' . $Session] != 2 && $SessionAttend[$s]['M' . $Session] != '2') ? 1 : 0;
                                $SessionAttendTotal = $SessionAttendTotal + $ses;
                            }
                        }
                    }

                    $dataStd[$i]['SessionAttend'] = $SessionAttendTotal;
                    $dataStd[$i]['SessionAttendDetails'] = $SessionAttend;
                    $dataStd[$i]['SessionAttendSch'] = count($dataArrAttdID);
                }
            }


            $ScheduleTask = $this->db->get_where(
                'db_academic.schedule_task',
                array(
                    'ScheduleID' => $ScheduleID,
                    'Session' => $Session
                )
            )->result_array();

            $result = array(
                'Schedule' => $dataArrAttdID,
                'Lecturer' => $dataLect,
                'Student' => $dataStd,
                'ScheduleTask' => $ScheduleTask
            );

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'removeTaskStudent') {

            $ID = $data_arr['ID'];

            $dataCk = $this->db->get_where(
                'db_academic.schedule_task_student',
                array('ID' => $ID)
            )->result_array();

            if (count($dataCk) > 0) {
                $d = $dataCk[0];

                // Insert ke table remove
                $data_arrIns = array(
                    'IDST' => $d['IDST'],
                    'NPM' => $d['NPM'],
                    'EntredBy' => $data_arr['NIP']
                );

                $this->db->insert('db_academic.schedule_task_student_remove', $data_arrIns);
                $this->db->reset_query();

                // Cek apakah file ada atau tidak
                if ($d['File'] != '' && $d['File'] != null) {
                    $Path = './uploads/task/' . $d['File'];
                    if (file_exists($Path)) {
                        unlink($Path);
                    }
                }

                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.schedule_task_student');
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'getDataAttdOnlineStudent') {
            $ScheduleID = $data_arr['ScheduleID'];
            $NPM = $data_arr['NPM'];

            $result = [];

            for ($i = 1; $i <= 14; $i++) {

                // Task
                $dataTask = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.schedule_task_student sts 
                                                        LEFT JOIN  db_academic.schedule_task st
                                                        ON (sts.IDST = st.ID)
                                                        WHERE sts.NPM = "' . $NPM . '" AND 
                                                        st.ScheduleID = "' . $ScheduleID . '" AND
                                                        st.Session = "' . $i . '"
                                                           ')->result_array()[0]['Total'];


                // Forum
                $dataForum = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.counseling_comment cc
                                                        LEFT JOIN db_academic.counseling_topic ct 
                                                        ON (ct.ID = cc.TopicID)
                                                        WHERE cc.UserID = "' . $NPM . '" 
                                                        AND ct.ScheduleID = "' . $ScheduleID . '"
                                                         AND ct.Sessions = "' . $i . '" ')->result_array()[0]['Total'];


                // Quiz
                $dataQuiz = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.q_quiz_students qqs
                                                        LEFT JOIN db_academic.q_quiz qq 
                                                        ON (qq.ID = qqs.QuizID)
                                                        WHERE qqs.NPM = "' . $NPM . '"
                                                        AND qqs.WorkDuration IS NOT NULL
                                                        AND qqs.WorkDuration > 0
                                                        AND qq.ScheduleID = "' . $ScheduleID . '"
                                                         AND qq.Session = "' . $i . '" ')->result_array()[0]['Total'];

                // Attendance
                $dataAttd = $this->db->query('SELECT ID FROM db_academic.attendance 
                                                    WHERE ScheduleID = "' . $ScheduleID . '" ')->result_array();
                $dataPresent = 0;
                $dataAbsen = 0;
                if (count($dataAttd) > 0) {
                    for ($a = 0; $a < count($dataAttd); $a++) {

                        $ID_Attd = $dataAttd[$a]['ID'];

                        $dt = $this->db->query('SELECT ast.M' . $i . ' FROM db_academic.attendance_students ast 
                                                            WHERE 
                                                            ast.ID_Attd = "' . $ID_Attd . '" 
                                                            AND ast.NPM = "' . $NPM . '"
                                                             ')->result_array()[0]['M' . $i];

                        if ($dt == '1') {
                            $dataPresent = $dataPresent + 1;
                        } else if ($dt == '2') {
                            $dataAbsen = $dataAbsen + 1;
                        }
                    }
                }






                $result[$i - 1] = array(
                    'ScheduleID' => $ScheduleID,
                    'Session' => $i,
                    'Task' => $dataTask,
                    'Forum' => $dataForum,
                    'Quiz' => $dataQuiz,
                    'AttdID' => count($dataAttd),
                    'AttdID_Details' => $dataAttd,
                    'Present' => $dataPresent,
                    'Present' => $dataPresent,
                    'Absen' => $dataAbsen
                );
            }

            // Syarat presensi
            $dataSyarat = $this->db->get_where(
                'db_academic.setting_online_class',
                array('SemesterID' => $data_arr['SemesterID'])
            )->result_array();

            $res = array(
                'Requirements' => $dataSyarat,
                'Attendance' => $result
            );

            return print_r(json_encode($res));
        } else if ($data_arr['action'] == 'actBtnReconfirmAttdStudent') {

            $NPM = $data_arr['NPM'];
            $ScheduleID = $data_arr['ScheduleID'];
            $Session = $data_arr['Session'];

            $dataCk = $this->m_oc->checkOnlineAttendance($NPM, $ScheduleID, $Session);

            return print_r(json_encode(array('Status' => $dataCk)));

            // Task
            //            $dataTask = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.schedule_task_student sts
            //                                                        LEFT JOIN  db_academic.schedule_task st
            //                                                        ON (sts.IDST = st.ID)
            //                                                        WHERE sts.NPM = "'.$NPM.'" AND
            //                                                        st.ScheduleID = "'.$ScheduleID.'" AND
            //                                                        st.Session = "'.$Session.'"
            //                                                           ')->result_array()[0]['Total'];
            //
            //
            //            // Forum
            //            $dataForum = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.counseling_comment cc
            //                                                        LEFT JOIN db_academic.counseling_topic ct
            //                                                        ON (ct.ID = cc.TopicID)
            //                                                        WHERE cc.UserID = "'.$NPM.'"
            //                                                        AND ct.ScheduleID = "'.$ScheduleID.'"
            //                                                         AND ct.Sessions = "'.$Session.'" ')
            //                                    ->result_array()[0]['Total'];
            //
            //            if($dataTask>0 && $dataForum>0){
            //                $ArrAttdID = (array) $data_arr['ArrAttdID'];
            //
            ////                print_r($ArrAttdID);exit();
            //
            //                if(count($ArrAttdID)>0){
            //                    for($i=0;$i<count($ArrAttdID);$i++){
            //                        $IDAttd = $ArrAttdID[$i]->ID;
            //
            //                        $this->db->where(array(
            //                            'ID_Attd' => $IDAttd,
            //                            'NPM' => $NPM
            //                        ));
            //                        $this->db->update('db_academic.attendance_students',
            //                            array(
            //                                'M'.$Session => '1',
            //                                'IsOnline'.$Session => 1
            //                            ));
            //
            //                    }
            //                }
            //
            //            }

            //            return print_r(1);
        } else if ($data_arr['action'] == 'updateDateOnline') {

            // Cek
            $data = $this->db->get_where('db_academic.schedule_online', array(
                'ScheduleID' => $data_arr['ScheduleID'],
                'Session' => $data_arr['Session']
            ))->result_array();

            $dataIns = array(
                'ScheduleID' => $data_arr['ScheduleID'],
                'Session' => $data_arr['Session'],
                'DateStart' => $data_arr['DateStart'],
                'DateEnd' => $data_arr['DateEnd']
            );

            if (count($data) > 0) {
                // Update
                $dataIns['UpdatedBy'] = $this->session->userdata('NIP');
                $dataIns['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $data[0]['ID']);
                $this->db->update('db_academic.schedule_online', $dataIns);
            } else {
                // Insert
                $dataIns['EntredBy'] = $this->session->userdata('NIP');
                $dataIns['EntredAt'] = $this->m_rest->getDateTimeNow();
                $this->db->insert('db_academic.schedule_online', $dataIns);
            }

            return print_r(1);
        }
    }


    public function getDataOnlineClass()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken2();

        $SemesterID = $data_arr['SemesterID'];
        $WhereProdi = ($data_arr['ProdiID'] != '') ? ' AND sdc.ProdiID = "' . $data_arr['ProdiID'] . '" ' : '';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND (s.ClassGroup LIKE "%' . $search . '%" 
                                    OR mk.MKCode LIKE "%' . $search . '%" 
                                    OR mk.NameEng LIKE "%' . $search . '%"
                                    ) ';
        }

        $queryDefault = 'SELECT s.ID AS ScheduleID, s.ClassGroup, mk.NameEng AS CourseEng 
                                    FROM db_academic.schedule s
                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                    WHERE s.SemesterID = "' . $SemesterID . '" AND s.OnlineLearning = "1" ' . $WhereProdi . $dataSearch . '
                                    
                                    GROUP BY s.ID ';

        $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (SELECT s.ID FROM db_academic.schedule s
                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                    WHERE s.SemesterID = "' . $SemesterID . '" AND s.OnlineLearning = "1" ' . $WhereProdi . $dataSearch . '
                                    GROUP BY s.ID ) xx';

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';
        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $dataLect = $this->m_rest->getAllLecturerByScheduleID($row['ScheduleID']);

            $dataSession = $this->m_rest->getRangeDateLearningOnline($row['ScheduleID']);

            $viewLec = '';
            if (count($dataLect) > 0) {
                for ($t = 0; $t < count($dataLect); $t++) {
                    $co = ($t == 0) ? ' (Co)' : '';
                    $viewLec = $viewLec . '<div>' . $dataLect[$t]['NIP'] . ' - ' . $dataLect[$t]['Name'] . $co . '</div>';
                }
            }

            $nestedData[] = '<div>' . $no . '<textarea class="hide" id="text_' . $row['ScheduleID'] . '">' . json_encode($row) . '</textarea></div>';
            $nestedData[] = '<div style="text-align: left;"><b>' . $row['CourseEng'] . '</b>
                                    <div style="font-size: 12px;">Group : ' . $row['ClassGroup'] . '</div>
                                    <div>' . $viewLec . '</div>
                                    </div>';

            if (count($dataSession) > 0) {
                $s = 0;
                for ($ses = 0; $ses < count($dataSession); $ses++) {
                    $s += 1;
                    $d = $dataSession[$ses];

                    $dataDetail = $this->m_rest->checkKelengkapanLearningOnline($row['ScheduleID'], $s);

                    $rangeSt = date('d/M/y', strtotime($d['RangeStart']));
                    $rangeEn = date('d/M/y', strtotime($d['RangeEnd']));

                    $bg = ($d['Status'] == '1' || $d['Status'] == 1)
                        ? 'background: #ffeb3b42;border: 1px solid #9E9E9E;border-radius: 5px;' : '';

                    $linkDetail = ($d['ManualSet'] == '1' || $d['ManualSet'] == 1)
                        ? 'color: #ff3f03;' : 'color: #607d8b;';

                    // Material
                    $viewMaterial = (count($dataDetail['dataMaterial']))
                        ? '<a href="' . url_sign_in_lecturers . 'uploads/material/' . $dataDetail['dataMaterial'][0]['File'] . '" target="_blank">
                                <span class="label label-default" style="font-size: 8px;">Matrl</span></a>'
                        : '';

                    // Cek Topik
                    $viewCkTopik = ($dataDetail['CheckTopik'] > 0)
                        ? '<span class="label label-primary" style="font-size: 8px;">Frm</span>'
                        : '';

                    // Cek Task
                    $viewTask = ($dataDetail['CheckTask'] > 0)
                        ? '<span class="label label-success" style="font-size: 8px;">Tsk</span>'
                        : '';

                    // Cek Quiz
                    $viewQuiz = ($dataDetail['CheckQuiz'] > 0)
                        ? '<span class="label label-warning" style="font-size: 8px;">Qz</span>'
                        : '';

                    $arr = '<div style="' . $bg . 'padding-top: 5px;padding-bottom: 5px;">
                                                        ' . $viewCkTopik . $viewTask . $viewMaterial . $viewQuiz . '
                                    <a href="javascript:void(0);" data-active="' . $d['Status'] . '" data-schid="' . $row['ScheduleID'] . '"
                                    data-session="' . $s . '" data-start="' . $d['RangeStart'] . '"
                                    data-end="' . $d['RangeEnd'] . '" class="btnAdmShowAttendance">
                                    <div id="show_scheduleOnlinr_' . $row['ScheduleID'] . '_' . $s . '" 
                                    style="' . $linkDetail . 'font-size: 10px;margin-top: 5px;font-weight: bold;">
                                    ' . $rangeSt . '<br/>' . $rangeEn . '</div id="show_scheduleOnlinr_">
                                    </div></a>';
                    array_push($nestedData, $arr);
                }

                // looping tambahan
                $tambahanLoop = (count($dataSession) == 14) ? 2 : 1;
                for ($t = 1; $t <= $tambahanLoop; $t++) {
                    $arr = '-';
                    array_push($nestedData, $arr);
                }

                // if(count($dataSession)==14){

                // }
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
    }


    public function getDataOnlineClass_2()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken2();

        $SemesterID = $data_arr['SemesterID'];
        $WhereProdi = ($data_arr['ProdiID'] != '') ? ' AND sdc.ProdiID = "' . $data_arr['ProdiID'] . '" ' : '';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND (s.ClassGroup LIKE "%' . $search . '%" 
                                    OR mk.MKCode LIKE "%' . $search . '%" 
                                    OR mk.NameEng LIKE "%' . $search . '%"
                                    ) ';
        }

        $queryDefault = 'SELECT s.ID AS ScheduleID, s.ClassGroup, mk.NameEng AS CourseEng 
                                    FROM db_academic.schedule s
                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                    WHERE s.SemesterID = "' . $SemesterID . '" AND s.OnlineLearning = "1" ' . $WhereProdi . $dataSearch . '
                                    
                                    GROUP BY s.ID ';

        $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (SELECT s.ID FROM db_academic.schedule s
                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                    WHERE s.SemesterID = "' . $SemesterID . '" AND s.OnlineLearning = "1" ' . $WhereProdi . $dataSearch . '
                                    GROUP BY s.ID ) xx';

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';
        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $dataLect = $this->m_rest->getAllLecturerByScheduleID($row['ScheduleID']);
            $viewLec = '';
            if (count($dataLect) > 0) {
                for ($t = 0; $t < count($dataLect); $t++) {
                    $co = ($t == 0) ? ' (Co)' : '';
                    $viewLec = $viewLec . '<div>' . $dataLect[$t]['NIP'] . ' - ' . $dataLect[$t]['Name'] . $co . '</div>';
                }
            }

            $nestedData[] = '<div>' . $no . '<textarea class="hide" id="text_' . $row['ScheduleID'] . '">' . json_encode($row) . '</textarea></div>';
            $nestedData[] = '<div style="text-align: left;"><b>' . $row['CourseEng'] . '</b>
                                    <div style="font-size: 12px;">Group : ' . $row['ClassGroup'] . '</div>
                                    <div>' . $viewLec . '</div>
                                    </div>';
            //            $nestedData[] = '<div>'.$no.'</div>';


            for ($s = 1; $s <= 14; $s++) {
                // Get date
                $dataSes = $this->m_rest->getRangeDateLearningOnlinePerSession($row['ScheduleID'], $s);



                $rangeSt = date('d/M/Y', strtotime($dataSes['RangeStart']));
                $rangeEn = date('d/M/Y', strtotime($dataSes['RangeEnd']));

                $bg = ($dataSes['Status'] == '1' || $dataSes['Status'] == 1)
                    ? 'background: #ffeb3b42;border: 1px solid #9E9E9E;border-radius: 5px;' : '';

                // Material
                $viewMaterial = (count($dataSes['dataMaterial']))
                    ? '<div><a href="' . url_sign_in_lecturers . 'uploads/material/' . $dataSes['dataMaterial'][0]['File'] . '" target="_blank">
                                <span class="label label-default"><b>Material</b></span></a></div>'
                    : '';

                // Cek Topik
                $viewCkTopik = ($dataSes['CheckTopik'] > 0)
                    ? '<a href="javascript:void(0);" data-schid="' . $row['ScheduleID'] . '" data-session="' . $s . '" class="btnAdmShowForum">
                            <div><span class="label label-primary"><b>Forum ' . $dataSes['TotalComment'] . '</b></span></div></a>'
                    : '';

                // Cek Task
                $viewTask = ($dataSes['CheckTask'] > 0)
                    ? '<a href="javascript:void(0);" data-schid="' . $row['ScheduleID'] . '" data-session="' . $s . '" class="btnAdmShowTask">
                            <div><span class="label label-success"><b>Task ' . $dataSes['TotalTask'] . '</b></span></div></a>'
                    : '';

                $arr = '<div style="' . $bg . 'padding-top: 5px;padding-bottom: 5px;">
                                    ' . $viewCkTopik . $viewTask . $viewMaterial . '
                                    <a href="javascript:void(0);" data-active="' . $dataSes['Status'] . '" data-schid="' . $row['ScheduleID'] . '" 
                                    data-session="' . $s . '" data-start="' . $dataSes['RangeStart'] . '" 
                                    data-end="' . $dataSes['RangeEnd'] . '" class="btnAdmShowAttendance">
                                    <div id="show_scheduleOnlinr_' . $row['ScheduleID'] . '_' . $s . '" style="font-size: 10px;color: #607d8b;margin-top: 5px;font-weight: bold;">
                                    ' . $rangeSt . '<br/>' . $rangeEn . '</div id="show_scheduleOnlinr_">
                                    </div></a>';
                array_push($nestedData, $arr);
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
    }


    public function crudExamOnline()
    {

        $data_arr = $this->getInputToken();

        if ($data_arr['action'] == 'insertDateTimeExamOnline') {

            $ExamID = $data_arr['ExamID'];
            $NPM = $data_arr['NPM'];

            $ck = $this->db->get_where(
                'db_academic.exam_student_online',
                array(
                    'ExamID' => $ExamID,
                    'NPM' => $NPM
                )
            )->result_array();

            if (count($ck) <= 0) {


                $dataInsert = array(
                    'ExamID' => $ExamID,
                    'NPM' => $NPM
                );
                $this->db->insert(
                    'db_academic.exam_student_online',
                    $dataInsert
                );
                $this->db->reset_query();


                $this->db->set('Status', '1');
                $this->db->where($dataInsert);
                $this->db->update('db_academic.exam_details');
                $this->db->reset_query();

                $this->db->set('Status', '-1');
                $this->db->where(array(
                    'ExamID' => $ExamID,
                    'Status' => '0'
                ));
                $this->db->update('db_academic.exam_details');
            }

            // cek apakah punya quiz atau tidak
            $dataCkQuiz = $this->db
                ->get_where('db_academic.q_exam', array('ExamID' => $ExamID))
                ->result_array();

            $result = array('Status' => 1);
            if (count($dataCkQuiz) > 0) {

                $QuizID = $dataCkQuiz[0]['QuizID'];
                $ExamDate = $data_arr['ExamDate'];
                $ExamStart = $data_arr['ExamStart'];
                $ExamEnd = $data_arr['ExamEnd'];

                $dataCkDetailQuiz = $this->db->get_where(
                    'db_academic.q_quiz_students',
                    array('NPM' => $NPM, 'QuizID' => $QuizID)
                )->result_array();


                $ActionQuiz = 'start';

                if (count($dataCkDetailQuiz) <= 0) {

                    $StartSession = $this->m_rest->getDateTimeNow();
                    $EndSession = $ExamDate . ' ' . $ExamEnd;
                    $dataInsertDetailQuiz = array(
                        'NPM' => $NPM,
                        'QuizID' => $QuizID,
                        'StartSession' => $StartSession,
                        'EndSession' => $EndSession
                    );
                    $this->db->insert('db_academic.q_quiz_students', $dataInsertDetailQuiz);
                } else {
                    $dCkDetailQuiz = $dataCkDetailQuiz[0];
                    if ($dCkDetailQuiz['WorkDuration'] != null && $dCkDetailQuiz['WorkDuration'] != '') {
                        $ActionQuiz = 'resume';
                    }
                }

                $result = array('Status' => 2, 'Action' => $ActionQuiz, 'QuizID' => $QuizID);
            }

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'loadChatExamOnline') {
            $ExamID = $data_arr['ExamID'];
            $data = $this->db->order_by('ID', 'DESC')->order_by('ID', 'ASC')
                ->get_where(
                    'db_academic.exam_task_chat',
                    array('ExamID' => $ExamID)
                )
                ->result_array();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $d = $data[$i];
                    if ($d['TypeUser'] == 'std') {
                        $Name = $this->db->select('Name')->get_where(
                            'db_academic.auth_students',
                            array('NPM' => $d['UserID'])
                        )->result_array()[0]['Name'];
                    } else {
                        $Name = $this->db->select('Name')->get_where(
                            'db_employees.employees',
                            array('NIP' => $d['UserID'])
                        )->result_array()[0]['Name'];
                    }
                    $data[$i]['Name'] = $Name;
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'insertChatExamOnline') {
            $dataForm = (array) $data_arr['dataForm'];

            $this->db->insert('db_academic.exam_task_chat', $dataForm);
            return print_r(1);
        } else if ($data_arr['action'] == 'insertInvigilator') {
            $dataForm = (array) $data_arr['dataForm'];

            $ck = $this->db->get_where(
                'db_academic.exam_employees_online',
                $dataForm
            )->result_array();

            if (count($ck) <= 0) {
                $this->db->insert('db_academic.exam_employees_online', $dataForm);
            }
            return print_r(1);
        } else if ($data_arr['action'] == 'loadDataExamOnline') {
            $ExamID = $data_arr['ExamID'];

            // cek apakah menggunakan quiz atau tidak
            $dataCk = $this->db->get_where('db_academic.q_exam', array('ExamID' => $ExamID))->result_array();

            if (count($dataCk) > 0) {
                $QuizID = $dataCk[0]['QuizID'];
                $data = $this->db->query('SELECT qqs.ID AS QuizStudentID, qqs.StartSession AS StartWorking, 
                                                qqs.EndSession, qqs.Score, qqs.ShowScore, 
                                                qqs.WorkDuration, qqs.SubmittedAt AS SavedAt,
                                                ats.Name, ats.NPM
                                                FROM db_academic.q_quiz_students qqs
                                                LEFT JOIN db_academic.auth_students ats ON (qqs.NPM = ats.NPM)
                                                WHERE qqs.QuizID = "' . $QuizID . '" ')->result_array();
            } else {
                $data = $this->db->query('SELECT eso.*, ats.Name FROM 
                                                db_academic.exam_student_online eso 
                                                LEFT JOIN db_academic.auth_students ats ON (ats.NPM = eso.NPM)
                                                WHERE eso.ExamID = "' . $ExamID . '"
                                                 ORDER BY eso.StartWorking ASC ')->result_array();
            }



            return print_r(json_encode($data));
        }
    }

    public function crudBlockStudent()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'EditingDateBlock') {

            $ID = $data_arr['ID'];

            $dataForm = (array) $data_arr['dataForm'];

            if ($ID != '') {
                // Update
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.block', $dataForm);
                $this->db->reset_query();
                $insert_id = $ID;
            } else {
                $dataForm['CreatedBy'] = $this->session->userdata('NIP');
                $dataForm['CreatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->insert('db_academic.block', $dataForm);
                $insert_id = $this->db->insert_id();
                $this->db->reset_query();
            }

            $this->db->where('IDBlock', $insert_id);
            $this->db->delete('db_academic.block_student');
            $this->db->reset_query();
            for ($i = 0; $i < count($data_arr['dataStudent']); $i++) {
                $d = (array) $data_arr['dataStudent'][$i];
                $arrIns = array(
                    'IDBlock' => $insert_id,
                    'NPM' => $d['NPM']
                );
                $this->db->insert('db_academic.block_student', $arrIns);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'getDataBlock') {
            $data = $this->db->query('SELECT b.*, em.Name AS CreatedByName, em2.Name AS UpdatedByName FROM db_academic.block b 
                                                LEFT JOIN db_employees.employees em ON (em.NIP = b.CreatedBy)
                                                LEFT JOIN db_employees.employees em2 ON (em2.NIP = b.UpdatedBy) ')->result_array();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {
                    $data[$i]['Students'] = $this->db->query('SELECT ats.NPM, ats.Name FROM db_academic.block_student bs 
                                                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = bs.NPM)
                                                                    WHERE bs.IDBlock = "' . $data[$i]['ID'] . '" ')->result_array();
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removeDateBlock') {
            $ID = $data_arr['ID'];
            $this->db->where('ID', $ID);
            $this->db->delete('db_academic.block');
            $this->db->reset_query();

            $this->db->where('IDBlock', $ID);
            $this->db->delete('db_academic.block_student');
            $this->db->reset_query();

            return print_r(1);
        }
    }

    public function crudEula()
    {
        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'updateDataEula') {

            $ID = $data_arr['ID'];

            $dataForm = array(
                'Title' => $data_arr['Title'],
                'Description' => $data_arr['Description'],
                'SummernoteID' => $data_arr['SummernoteID']
            );

            if ($ID != '') {
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();

                $this->db->where('ID', $ID);
                $this->db->update('db_it.eula', $dataForm);
                $insert_id = $ID;
                $this->db->reset_query();
            } else {
                $dataForm['CreatedBy'] = $this->session->userdata('NIP');
                $dataForm['CreatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->insert('db_it.eula', $dataForm);
                $insert_id = $this->db->insert_id();
                $this->db->reset_query();
            }


            // Cek image in summernote
            $this->m_rest
                ->checkImageSummernote('insert', $data_arr['SummernoteID'], 'db_it.eula', 'Description');

            return print_r(json_encode(array('ID' => $insert_id)));
        } else if ($data_arr['action'] == 'updateQueueEula') {

            for ($i = 0; $i < count($data_arr['dataForm']); $i++) {
                $this->db->where('ID', $data_arr['dataForm'][$i]);
                $this->db->update('db_it.eula_linked', array('Queue' => ($i + 1)));
                $this->db->reset_query();
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'getDataEula') {

            $data = $this->db->query('SELECT e.* FROM db_it.eula e 
                                                WHERE e.ID = "' . $data_arr['ID'] . '" ')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updatePublicationDate') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];
            // Cek apakah tanggal bentrok atau tidak
            $Start = $dataForm['RangeStart'];
            $End = $dataForm['RangeEnd'];

            $WhereCkID = ($ID != '') ? 'ed.ID!= "' . $ID . '" AND ' : '';

            $dataCk = $this->db->query('SELECT * FROM db_it.eula_date ed WHERE ed.To = "' . $dataForm['To'] . '" AND ' . $WhereCkID . ' ( 
                                                        (ed.RangeStart>="' . $Start . '" AND ed.RangeStart<="' . $End . '") OR 
                                                        (ed.RangeStart<="' . $Start . '" AND ed.RangeEnd>="' . $End . '") OR 
                                                        (ed.RangeEnd>="' . $Start . '" AND ed.RangeEnd<="' . $End . '") OR
                                                        (ed.RangeStart>="' . $Start . '" AND ed.RangeEnd<="' . $End . '")
                                                        )')
                ->result_array();

            if (count($dataCk) <= 0) {

                if ($ID != '') {
                    // Update
                    $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                    $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->where('ID', $ID);
                    $this->db->update('db_it.eula_date', $dataForm);

                    $result = array(
                        'Status' => 1,
                        'Message' => 'Data updated'
                    );
                } else {
                    // Insert
                    $dataForm['CreatedBy'] = $this->session->userdata('NIP');
                    $dataForm['CreatedAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->insert('db_it.eula_date', $dataForm);

                    $result = array(
                        'Status' => 1,
                        'Message' => 'Data saved'
                    );
                }
            } else {
                $result = array(
                    'Status' => 0,
                    'Message' => 'Conflict',
                    'Details' => $dataCk
                );
            }



            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'getListEULAInPD') {
            $ID = $data_arr['ID'];

            $data = $this->db->query('SELECT el.*, e.Title FROM db_it.eula_linked el 
                                                LEFT JOIN db_it.eula e ON (e.ID = el.EID)
                                                WHERE el.EDID = "' . $ID . '" ORDER BY el.Queue ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getEULASearch') {
            $key = $data_arr['key'];
            $EDID = $data_arr['EDID'];
            $data = $this->db->query('SELECT e.ID,e.Title FROM db_it.eula e 
                                                        WHERE e.Title LIKE "%' . $key . '%"
                                                        AND e.ID NOT IN (SELECT EID FROM db_it.eula_linked WHERE EDID = "' . $EDID . '" ) 
                                                        GROUP BY e.ID
                                                        LIMIT 7')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'addTOListPD') {
            $EID = $data_arr['EID'];
            $EDID = $data_arr['EDID'];

            $dataCK = $this->db->query('SELECT COUNT(*) AS Total FROM db_it.eula_linked 
                                        WHERE EDID = "' . $EDID . '" ')->result_array()[0]['Total'];

            $Queue = $dataCK + 1;
            $dataIns = array(
                'EID' => $EID,
                'EDID' => $EDID,
                'Queue' => $Queue
            );

            $this->db->insert('db_it.eula_linked', $dataIns);

            return print_r(1);
        } else if ($data_arr['action'] == 'removeFromListEULAInPD') {

            // Remove eula_user
            $this->db->where('ELID', $data_arr['ID']);
            $this->db->delete('db_it.eula_user');
            $this->db->reset_query();

            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_it.eula_linked');
            return print_r(1);
        } else if ($data_arr['action'] == 'getListPublicationDate') {

            $requestData = $_REQUEST;

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataScr = '(e.Title LIKE "%' . $search . '%" OR e.Description LIKE "%' . $search . '%")';

                $dataSearch = ($data_arr['FilterPortal'] != '' || $data_arr['FilterDate'] != '') ? ' AND ' . $dataScr : ' WHERE ' . $dataScr;
            }

            $queryDefault = 'SELECT * FROM db_it.eula_date ed';

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';


            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];


                $Published = ($row['Published'] == '1')
                    ? '<span class="label label-success">Published</span>' : '<span class="label label-default">Unpublished</span>';

                $BtnAct = '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-edit"></i> <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a href="javascript:void(0);" data-pdid="' . $row['ID'] . '" class="btnEditDate">Edit Date</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="javascript:void(0);" data-pdid="' . $row['ID'] . '" class="btnEditListEula">List EULA</a></li>
                          </ul>
                        </div>';

                $viewCountEula = $this->db->query('SELECT COUNT(*) AS Total FROM db_it.eula_linked 
                                                                    WHERE EDID = "' . $row['ID'] . '" ')
                    ->result_array()[0]['Total'];

                $viewLISTEULA = '<a href="javascript:void(0);" id="viewDetailEULA_' . $row['ID'] . '" class="viewDetailEULA" data-edid="' . $row['ID'] . '">' . $viewCountEula . '</a>';

                $nestedData[] = '<div>' . $no . '<textarea class="hide" id="dataPD_' . $row['ID'] . '">' . json_encode($row) . '</textarea></div>';
                $nestedData[] = '<div>' . date('d M Y', strtotime($row['RangeStart'])) . '</div>';
                $nestedData[] = '<div>' . date('d M Y', strtotime($row['RangeEnd'])) . '</div>';
                $nestedData[] = '<div>' . $row['To'] . '</div>';
                $nestedData[] = '<div>' . $Published . '</div>';
                $nestedData[] = '<div>' . $BtnAct . '</div>';
                $nestedData[] = '<div>' . $viewLISTEULA . '</div>';


                //            $nestedData[] = '<div style="text-align: left;">'.$viewLink.$tokenText.'</div>';

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
        } else if ($data_arr['action'] == 'getListMasterEULA') {

            $requestData = $_REQUEST;

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataScr = '(e.Title LIKE "%' . $search . '%" OR e.Description LIKE "%' . $search . '%")';
                $dataSearch = ' WHERE ' . $dataScr;
            }


            $queryDefault = 'SELECT e.* FROM db_it.eula e ' . $dataSearch . ' GROUP BY e.ID ORDER BY e.ID';
            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';


            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align: left;"><div class="panel-title">' . $row['Title'] . '</div>
                                <div class="panel-description">' . $row['Description'] . '</div>
                                </div>';
                $nestedData[] = '<div style="text-align: left;"><div style="margin-top: 10px;"><a href="' . base_url('it/eula/create-eula?id=') . $row['ID'] . '" class="btn btn-warning">Edit</a></div></div>';


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
        } else if ($data_arr['action'] == 'getListEULAForUser') {

            $data = $this->db->query('SELECT el.ID AS ELID, e.Title, e.Description, el.Queue, eu.Username FROM db_it.eula_linked el 
                                                LEFT JOIN db_it.eula e ON (e.ID = el.EID)
                                                LEFT JOIN db_it.eula_user eu ON (eu.ELID = el.ID AND eu.Username = "' . $data_arr['Username'] . '")
                                                WHERE el.EDID = "' . $data_arr['EDID'] . '" 
                                                ORDER BY el.Queue ASC ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getListEULAForDirection') {

            $data = $this->db->get_where('db_it.eula_direct', array('Username' => $data_arr['Username']))->result_array()[0];

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'insertEULAForUser') {
            $dataIns = array(
                'ELID' => $data_arr['ELID'],
                'Username' => $data_arr['Username'],
                'UserType' => $data_arr['UserType']
            );
            $this->db->insert('db_it.eula_user', $dataIns);
            return print_r(1);
        } else if ($data_arr['action'] == 'getDetailListEULA') {

            $EDID = $data_arr['EDID'];

            $data = $this->db->query('SELECT el.ID, e.Title, e.Description FROM db_it.eula_linked el 
                                                LEFT JOIN db_it.eula e ON (e.ID = el.EID)
                                                WHERE el.EDID = "' . $EDID . '" ')->result_array();

            if (count($data) > 0) {

                for ($i = 0; $i < count($data); $i++) {
                    $dataUsr = $this->db->query('SELECT COUNT(*) AS Total FROM db_it.eula_user eu 
                                                    WHERE eu.ELID = "' . $data[$i]['ID'] . '" ')->result_array();
                    $data[$i]['TotalUser'] = $dataUsr[0]['Total'];
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getPublicationDate') {

            $where = ($data_arr['To'] != '') ? ' WHERE ed.To LIKE "' . $data_arr['To'] . '" ' : '';

            $data = $this->db->query('SELECT ed.* FROM db_it.eula_date ed ' . $where)->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getPublicationTo') {
            $data = $this->db->query('SELECT ed.To FROM db_it.eula_date ed GROUP BY ed.To ORDER BY ed.To ASC')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getEULATitle') {

            $EDID = $data_arr['EDID'];

            $data = $this->db->query('SELECT e.ID, e.Title FROM db_it.eula e LEFT JOIN db_it.eula_linked el ON (el.EID = e.ID) 
                                                WHERE el.EDID = "' . $EDID . '" ORDER BY el.Queue ASC ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getLogEULA') {

            $requestData = $_REQUEST;

            $To = $data_arr['To'];
            $EDID = $data_arr['EDID'];
            $EID = $data_arr['EID'];

            $dataWhere = '';
            if ($To != '' || $EDID != '' || $EID != '') {
                $wTo = ($To != '') ? 'AND ed.To = "' . $To . '" ' : '';
                $wEDID = ($EDID != '') ? 'AND el.EDID = "' . $EDID . '" ' : '';
                $wEID = ($EID != '') ? 'AND el.EID = "' . $EID . '" ' : '';

                $w = $wTo . $wEDID . $wEID;
                $dataWhere = ' WHERE ' . substr($w, 3);
            }

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataScr = 'e.Title LIKE "%' . $search . '%" OR em.Name LIKE "%' . $search . '%" 
                                OR em.NIP LIKE "%' . $search . '%" OR ats.Name LIKE "%' . $search . '%"
                                 OR ats.NPM LIKE "%' . $search . '%"';
                $dataSearch = ($To != '' || $EDID != '' || $EID != '')
                    ? ' AND (' . $dataScr . ')'
                    : ' WHERE ' . $dataScr;
            }

            $queryDefault = 'SELECT e.Title, ed.RangeStart, ed.RangeEnd, ed.To, eu.EntredAt, eu.Username,  
                                            em.Name AS NameEmp, ats.Name AS NameStd
                                            FROM db_it.eula_user eu
                                            LEFT JOIN db_it.eula_linked el ON (el.ID = eu.ELID)
                                            LEFT JOIN db_it.eula e ON (e.ID = el.EID)
                                            LEFT JOIN db_it.eula_date ed ON (ed.ID = el.EDID)
                                            
                                            LEFT JOIN db_employees.employees em ON (em.NIP = eu.Username)
                                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM = eu.Username) ' .
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

                $UserName = ($row['NameEmp'] != null && $row['NameEmp'] != '') ? $row['NameEmp'] : $row['NameStd'];

                $RangeStart = date('d M Y', strtotime($row['RangeStart']));
                $RangeEnd = '<br/>' . date('d M Y', strtotime($row['RangeEnd']));

                $labelUser = ($row['To'] == 'emp')
                    ? '<span class="label label-primary">Employee</span>'
                    : '<span class="label label-success">Student</span>';

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align: left;"><b>' . $UserName . '</b><div>' . $row['Username'] . '</div></div>';
                $nestedData[] = '<div>' . $RangeStart . $RangeEnd . '</div>';
                $nestedData[] = '<div>' . $labelUser . '</div>';
                $nestedData[] = '<div style="text-align: left;">' . $row['Title'] . '</div>';
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
        }
    }

    public function getLogLogin()
    {

        $requestData = $_REQUEST;

        $data_arr = $this->getInputToken2();


        $UserType = $data_arr['UserType'];
        $LogonBy = $data_arr['LogonBy'];
        //        $Date = $data_arr['Date'];

        $dataWhere = '';
        if ($UserType != '' || $LogonBy != '') {
            $w_UserType = ($UserType != '') ? 'AND ll.UserType = "' . $UserType . '" ' : '';
            $w_LogonBy = ($LogonBy != '') ? 'AND ll.LogonBy = "' . $LogonBy . '" ' : '';
            $w = $w_UserType . $w_LogonBy;
            $dataWhere = ' WHERE ' . substr($w, 3);
        }




        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataScr = 'll.Username LIKE "%' . $search . '%" OR ll.UserType LIKE "%' . $search . '%" 
                                OR ll.LogonBy LIKE "%' . $search . '%" OR ll.IPLocal LIKE "%' . $search . '%"
                                 OR ll.IPPublic LIKE "%' . $search . '%" OR em.Name LIKE "%' . $search . '%" 
                                  OR ats.Name LIKE "%' . $search . '%"';
            $dataSearch = ($UserType != '' || $LogonBy != '')
                ? ' AND (' . $dataScr . ')' : 'WHERE ' . $dataScr;
        }

        $queryDefault = 'SELECT ll.Username, ll.UserType, ll.LogonBy, ll.LogonAt, ll.IPLocal, ll.IPPublic,  
                                                CASE WHEN  em.Name IS NOT NULL THEN em.Name
                                                ELSE ats.Name END AS "Name" FROM db_it.log_login ll 
                                                LEFT JOIN db_employees.employees em ON (em.NIP = ll.Username)
                                                LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ll.Username)
                                                ' . $dataWhere . $dataSearch;

        $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';


        $sql = $queryDefault . ' ORDER BY ll.LogonAt DESC LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {

            $nestedData = array();
            $row = $query[$i];

            $IPLocal = ($row['IPLocal'] != '' && $row['IPLocal'] != null) ? $row['IPLocal'] : '';
            $IPPublic = ($row['IPPublic'] != '' && $row['IPPublic'] != null) ? $row['IPPublic'] : '';

            $LabelLoginAs = ($row['UserType'] == 'std')
                ? ' <span class="label label-primary">Student</span>'
                : ' <span class="label label-success">Employee</span>';

            $LabelLogin = '<span class="label label-default">Basic</span>';
            if ($row['LogonBy'] == 'gmail') {
                $LabelLogin = '<span class="label label-danger">Gmail</span>';
            } else if ($row['LogonBy'] == 'ad') {
                $LabelLogin = '<span class="label label-primary">Active Directory</span>';
            }

            $nestedData[] = '<div>' . $no . '</div>';
            $nestedData[] = '<div>' . $row['Username'] . '</div>';
            $nestedData[] = '<div style="text-align: left;"><b>' . $row['Name'] . '</b></div>';
            $nestedData[] = '<div>' . $LabelLoginAs . '</div>';
            $nestedData[] = '<div>' . $LabelLogin . '</div>';
            $nestedData[] = '<div>' . $IPLocal . '</div>';
            $nestedData[] = '<div>' . $IPPublic . '</div>';
            $nestedData[] = '<div>' . date('d M Y H:i', strtotime($row['LogonAt'])) . '</div>';


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
    }

    public function crudLiveChat()
    {
        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'getUserOnlineChat') {

            $data = $this->db->select('NIP,Name,Photo')->limit(10)->get_where('db_employees.employees', array('StatusEmployeeID' => 1))->result_array();

            return print_r(json_encode($data));
        }
    }

    public function crudQuiz()
    {
        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'getQuestionType') {
            $data = $this->db->get('db_academic.q_question_type')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'saveQuestion') {

            $ID = $data_arr['ID']; // ID Question
            $dataQustion = (array) $data_arr['dataQustion'];

            if ($ID != '') {
                // Update question
                $dataQustion['UpdatedBy'] = $data_arr['NIP'];
                $dataQustion['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $QID = $ID;
            } else {
                // Insert question
                $dataQustion['SummernoteID'] = $data_arr['SummernoteID'];
                $dataQustion['CreatedBy'] = $data_arr['NIP'];
                $dataQustion['CreatedAt'] = $this->m_rest->getDateTimeNow();
                $dataQustion['UpdatedAt'] = $this->m_rest->getDateTimeNow();

                $this->db->insert('db_academic.q_question', $dataQustion);
                $QID = $this->db->insert_id();
            }

            $this->m_rest
                ->checkImageSummernote('insert', $data_arr['SummernoteID'], 'db_academic.q_question', 'Question');


            // Option Process
            if ($dataQustion['QTID'] == 1 || $dataQustion['QTID'] == 2) {

                // Remove Option
                //                $this->db->where('QID',$ID);
                //                $this->db->delete('db_academic.q_question_options');
                //                $this->db->reset_query();

                $dataOption = (array) $data_arr['dataOption'];
                if ($ID != '') {
                } else {

                    // Insert option
                    if (count($dataOption) > 0) {
                        for ($i = 0; $i < count($dataOption); $i++) {

                            $arrins = (array) $dataOption[$i];
                            $arrins['QID'] = $QID;
                            $this->db->insert('db_academic.q_question_options', $arrins);

                            $this->m_rest
                                ->checkImageSummernote(
                                    'insert',
                                    $arrins['SummernoteID'],
                                    'db_academic.q_question_options',
                                    'Option'
                                );
                        }
                    }
                }
            }

            return print_r(json_encode(array('QID' => $QID)));
        } else if ($data_arr['action'] == 'removeOptionInQuestion') {
            $SummernoteID = $data_arr['SummernoteID'];

            $this->m_rest
                ->checkImageSummernote('delete', $SummernoteID, '', '');

            return print_r(1);
        } else if ($data_arr['action'] == 'getArrDataQuestion') {
            $ArrQID = (array) $data_arr['ArrQID'];

            $result = [];

            if (count($ArrQID) > 0) {
                for ($i = 0; $i < count($ArrQID); $i++) {
                    $QID = $ArrQID[$i];
                    $dataQuestion = $this->db->query('SELECT q.ID,q.Question,q.Note, qt.Description AS Type, q.QTID FROM db_academic.q_question q 
                                                                    LEFT JOIN db_academic.q_question_type qt ON (q.QTID = qt.ID)
                                                                    WHERE q.ID = "' . $QID . '" ')->result_array();
                    $dataOption = $this->db->select('Option,IsTheAnswer,Point')
                        ->get_where('db_academic.q_question_options', array('QID' => $QID))->result_array();
                    $arrP = array(
                        'Question' => (count($dataQuestion) > 0) ? $dataQuestion[0] : [],
                        'Option' => $dataOption,
                        'Status' => (count($dataQuestion) > 0) ? 1 : 0,
                        'QID' => $QID
                    );
                    array_push($result, $arrP);
                }
            }

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'removeDataQuiz') {

            $QuizID = $data_arr['QuizID'];

            $tables = array('db_academic.q_exam', 'db_academic.q_quiz_details');
            $this->db->where('QuizID', $QuizID);
            $this->db->delete($tables);
            $this->db->reset_query();

            $this->db->where('ID', $QuizID);
            $this->db->delete('db_academic.q_quiz');

            $result = array('Status' => true);
            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'saveDataQuiz') {

            // cek apakah ID Question exist atau tidak
            $dataForm = (array) $data_arr['dataForm'];
            $dataCheck_IDQ = true;
            if (count($dataForm) > 0) {
                for ($c = 0; $c < count($dataForm); $c++) {
                    $dataCk = $this->db->select('ID')
                        ->get_where('db_academic.q_question', array('ID' => $dataForm[$c]->QID))->result_array();

                    if (count($dataCk) <= 0) {
                        $dataCheck_IDQ = false;
                    }
                }
            }

            if ($dataCheck_IDQ) {

                $ForExam = (isset($data_arr['ForExam'])) ? $data_arr['ForExam'] : '0';

                // cek total answer

                $dataFmQuiz = array(
                    'NotesForStudents' => $data_arr['NotesForStudents']
                );

                if ($ForExam == '0') {

                    // Cek apakah sudah pernah bikin quiz atau blm
                    $ScheduleID = $data_arr['ScheduleID'];
                    $Session = $data_arr['Session'];

                    // Cek apakah ada student yang pernah ngisi atau engga
                    $TotalAnswer = $this->db->query('SELECT COUNT(*) AS TotalAnswer FROM db_academic.q_quiz_students qqs 
                                                    LEFT JOIN db_academic.q_quiz qq
                                                    ON (qq.ID = qqs.QuizID)
                                                    WHERE qq.ScheduleID = "' . $ScheduleID . '"
                                                     AND qq.Session = "' . $Session . '" ')->result_array()[0]['TotalAnswer'];

                    $dataCk = $this->db->select('ID')->get_where('db_academic.q_quiz', array(
                        'ScheduleID' => $ScheduleID,
                        'Session' => $Session
                    ))->result_array();

                    $QuizID = (count($dataCk) > 0) ? $dataCk[0]['ID'] : '';

                    $dataFmQuiz['ScheduleID'] = $ScheduleID;
                    $dataFmQuiz['Session'] = $Session;
                    $dataFmQuiz['Duration'] = $data_arr['Duration'];
                } else {

                    $dataFmQuiz['TypeExam'] = $data_arr['TypeExam'];
                    $dataFmQuiz['Title'] = $data_arr['Title'];
                    $dataFmQuiz['NoteForExam'] = $data_arr['NoteForExam'];
                    $dataFmQuiz['ForExam'] = $data_arr['ForExam'];

                    $TotalAnswer = 0;
                    $QuizID = $data_arr['QuizID'];
                    if ($QuizID != '') {
                        $TotalAnswer = $this->db->query('SELECT COUNT(*) AS TotalAnswer 
                                                            FROM db_academic.q_quiz_students qqs 
                                                            WHERE qqs.QuizID = "' . $QuizID . '" ')
                            ->result_array()[0]['TotalAnswer'];
                    }
                }



                if ($TotalAnswer <= 0) {

                    if ($QuizID != '') {
                        // Update
                        $dataFmQuiz['UpdatedBy'] = $data_arr['NIP'];
                        $dataFmQuiz['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                        $this->db->where('ID', $QuizID);
                        $this->db->update('db_academic.q_quiz', $dataFmQuiz);
                        $this->db->reset_query();
                    } else {
                        // Insert
                        $dataFmQuiz['CreatedBy'] = $data_arr['NIP'];
                        $dataFmQuiz['CreatedAt'] = $this->m_rest->getDateTimeNow();
                        $this->db->insert('db_academic.q_quiz', $dataFmQuiz);
                        $QuizID = $this->db->insert_id();
                        $this->db->reset_query();
                    }


                    if (count($dataForm) > 0) {
                        $this->db->where('QuizID', $QuizID);
                        $this->db->delete('db_academic.q_quiz_details');
                        $this->db->reset_query();

                        for ($i = 0; $i < count($dataForm); $i++) {
                            $d = (array) $dataForm[$i];
                            $d['QuizID'] = $QuizID;
                            $this->db->insert('db_academic.q_quiz_details', $d);
                            $this->db->reset_query();
                        }
                    }


                    if (isset($data_arr['dataTemporarySchedule'])) {
                        $this->db->where('QuizID', $QuizID);
                        $this->db->delete('db_academic.q_exam');
                        $this->db->reset_query();

                        $dataTemporarySchedule = (array) $data_arr['dataTemporarySchedule'];

                        for ($i = 0; $i < count($dataTemporarySchedule); $i++) {
                            $d = (array) $dataTemporarySchedule[$i];
                            $insArrQuizExam = array(
                                'QuizID' => $QuizID,
                                'ExamID' => $d['ExamID'],
                                'ExamDetail' => json_encode($d)
                            );
                            $this->db->insert('db_academic.q_exam', $insArrQuizExam);
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


            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'getQuizExam') {

            $requestData = $_REQUEST;

            $whereP = '';

            $orderBy = '';
            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {

                $search = $requestData['search']['value'];
                $dataSearch = ' AND
                                 (d.NameEng LIKE "%' . $search . '%" OR cl.Room LIKE "%' . $search . '%"
                                 OR p1.Name LIKE "%' . $search . '%" OR p2.Name LIKE "%' . $search . '%"
                                 OR p1.NIP LIKE "%' . $search . '%" OR p2.NIP LIKE "%' . $search . '%"
                                 ) ';
            }

            $queryDefault = 'SELECT qq.*, em.Name AS CreatedBy_Name, em1.Name AS UpdatedBy_Name FROM db_academic.q_quiz qq 
                                        LEFT JOIN db_employees.employees em ON (em.NIP = qq.CreatedBy)
                                        LEFT JOIN db_employees.employees em1 ON (em1.NIP = qq.UpdatedBy)
                                        WHERE qq.ForExam = "1" 
                                        ';

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';


            $dataTable = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $query = $dataTable;

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {
                $nestedData = array();
                $row = $query[$i];

                // get total question
                $TotalQuestion = $this->db->from('db_academic.q_quiz_details')
                    ->where('QuizID', $row['ID'])->count_all_results();

                // get total question
                $TotalExam = $this->db->from('db_academic.q_exam')
                    ->where('QuizID', $row['ID'])->count_all_results();


                $btnAct = '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-edit"></i> <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a href="' . base_url('academic/exam-schedule/exam-quiz-create?id=' . $row['ID']) . '" target="_blank">Edit</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#" style="color: red;">Remove</a></li>
                          </ul>
                        </div>';

                $UserActionCreate = '<div>Created By : ' . $row['CreatedBy_Name'] . ' (' . date('d M Y H:i', strtotime($row['CreatedAt'])) . ')</div>';
                $UserActionUpdate = ($row['UpdatedBy_Name'] != '' && $row['UpdatedBy_Name'] != null && $row['UpdatedBy_Name'] != 'null')
                    ? '<div style="margin-top:5px;padding-top:5px;border-top:1px solid #eaeaea;">Updated By : ' . $row['UpdatedBy_Name'] . ' (' . date('d M Y H:i', strtotime($row['UpdatedAt'])) . ')</div>' : '';

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align: left;">
                                    <b>' . $row['Title'] . '</b>
                                    <div style="margin-top: 15px;" class="well">' . $row['NoteForExam'] . '</div></div>';
                $nestedData[] = '<div>' . $btnAct . '</div>';
                $nestedData[] = '<div>' . $TotalQuestion . '</div>';
                $nestedData[] = '<div>' . $TotalExam . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $UserActionCreate . $UserActionUpdate . '</div>';


                $data[] = $nestedData;

                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval(count($queryDefaultRow)),
                "recordsFiltered" => intval(count($queryDefaultRow)),
                "data"            => $data
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'getQuizInThisSession') {

            $ScheduleID = $data_arr['ScheduleID'];
            $Session = $data_arr['Session'];

            $data = $this->db->query('SELECT qd.QID, qd.Point FROM db_academic.q_quiz_details qd 
                                               LEFT JOIN db_academic.q_quiz q ON (q.ID = qd.QuizID)
                                               WHERE q.ScheduleID = "' . $ScheduleID . '" AND q.Session = "' . $Session . '" ')
                ->result_array();

            $Quiz = $this->db->get_where(
                'db_academic.q_quiz',
                array('ScheduleID' => $ScheduleID, 'Session' => $Session)
            )->result_array();

            $dataStd = $this->db->query('SELECT COUNT(*) AS TotalAnswer FROM db_academic.q_quiz_students qqs 
                                                    LEFT JOIN db_academic.q_quiz qq 
                                                    ON (qqs.QuizID = qq.ID)
                                                    WHERE qq.ScheduleID = "' . $ScheduleID . '" 
                                                    AND qq.Session = "' . $Session . '" ')->result_array();

            $result = array(
                'Quiz' => $Quiz,
                'Details' => $data,
                'TotalAnswer' => $dataStd[0]['TotalAnswer']
            );

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'getQuizInThisID') {

            $ID = $data_arr['ID'];

            $data = $this->db->query('SELECT qd.QID, qd.Point FROM db_academic.q_quiz_details qd 
                                               LEFT JOIN db_academic.q_quiz q ON (q.ID = qd.QuizID)
                                               WHERE q.ID = "' . $ID . '" ')
                ->result_array();

            $Quiz = $this->db->get_where(
                'db_academic.q_quiz',
                array('ID' => $ID)
            )->result_array();

            $dataStd = $this->db->query('SELECT COUNT(*) AS TotalAnswer FROM db_academic.q_quiz_students qqs 
                                                    LEFT JOIN db_academic.q_quiz qq 
                                                    ON (qqs.QuizID = qq.ID)
                                                    WHERE qq.ID = "' . $ID . '"  ')->result_array();

            $result = array(
                'Quiz' => $Quiz,
                'Details' => $data,
                'TotalAnswer' => $dataStd[0]['TotalAnswer']
            );

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'getExamInQuiz') {

            $ID = $data_arr['ID'];

            $dataQuiz = $this->db->query('SELECT * FROM db_academic.q_quiz WHERE ID = "' . $ID . '" ')->result_array();

            if (count($dataQuiz) > 0) {

                $data = $this->db->query('SELECT q.Title, q.NoteForExam, e.ID AS ExamID, e.ExamDate, e.ExamStart, e.ExamEnd, 
                                                    qe.ID AS QExamID FROM db_academic.q_quiz q
                                                    RIGHT JOIN db_academic.q_exam qe ON (qe.QuizID = q.ID)
                                                    LEFT JOIN db_academic.exam e ON (e.ID = qe.ExamID)
                                                    WHERE q.ID = "' . $ID . '" ')->result_array();

                if (count($data) > 0) {
                    for ($i = 0; $i < count($data); $i++) {

                        $dataDetail = $this->db->query('SELECT mk.Name AS Course, mk.NameEng AS CourseEng, s.ClassGroup 
                                                                FROM db_academic.exam_group eg 
                                                                LEFT JOIN db_academic.schedule s 
                                                                ON (s.ID = eg.ScheduleID)
                                                                LEFT JOIN db_academic.schedule_details_course sdc 
                                                                ON (sdc.ScheduleID = eg.ScheduleID)
                                                                LEFT JOIN db_academic.mata_kuliah mk 
                                                                ON (mk.ID = sdc.MKID) 
                                                                WHERE eg.ExamID="' . $data[$i]['ExamID'] . '"
                                                                GROUP BY eg.ScheduleID')->result_array();
                        $data[$i]['Detail'] = $dataDetail;
                    }
                }

                $dataQuiz[0]['ListXeam'] = $data;
            }



            return print_r(json_encode($dataQuiz));
        } else if ($data_arr['action'] == 'addingExamInQuiz') {

            $ExamID = $data_arr['ExamID'];
            $QuizID = $data_arr['QuizID'];

            // cek apakah exam sudah pernah di masukan ke sebuah quiz atau blm
            $TotalExam = $this->db->from('db_academic.q_exam')
                ->where('ExamID', $ExamID)->count_all_results();

            $Status = 0;

            if ($TotalExam <= 0) {
                $this->db->insert('db_academic.q_exam', array(
                    'ExamID' => $ExamID,
                    'QuizID' => $QuizID
                ));
                $Status = 1;
            }

            return print_r(json_encode(array('Status' => $Status)));
        } else if ($data_arr['action'] == 'getMyQuestion') {
            $requestData = $_REQUEST;

            $NIP = $data_arr['NIP'];

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = 'AND ( q.Question LIKE "%' . $search . '%" )';
            }

            $queryDefault = 'SELECT q.*, qt.Description FROM db_academic.q_question q 
                                        LEFT JOIN db_academic.q_question_type qt ON (qt.ID = q.QTID)
                                        WHERE q.CreatedBy = "' . $NIP . '" ' . $dataSearch;

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
        } else if ($data_arr['action'] == 'getMasterQuestion') {
            $requestData = $_REQUEST;

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = 'WHERE q.Question LIKE "%' . $search . '%" 
                OR em.NIP LIKE "%' . $search . '%" OR em.Name LIKE "%' . $search . '%" ';
            }

            $queryDefault = 'SELECT q.*, qt.Description, em.NIP, em.Name FROM db_academic.q_question q 
                                        LEFT JOIN db_academic.q_question_type qt ON (qt.ID = q.QTID)
                                        LEFT JOIN db_employees.employees em ON (em.NIP = q.CreatedBy) 
                                        ' . $dataSearch;

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';



            $sql = $queryDefault . ' ORDER BY q.UpdatedAt DESC , q.CreatedAt DESC LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {
                $nestedData = array();
                $row = $query[$i];

                $btnAct = '<div><a href="javascript:void(0);" class=" btn btn-sm btn-default addToQuizFromMyQuestion" data-id="' . $row['ID'] . '">Add to quiz</a></div>';
                //                if(isset($data_arr['Portal']) && $data_arr['Portal']=='pcam'){
                //                    $btnAct = '<div><a href="javascript:void(0);" class="btn btn-sm btn-default"></a></div>';
                //                }

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align: left;">' . $row['Question'] . '
                                    <div>
                                        <span class="lbl-' . $row['QTID'] . '">' . $row['Description'] . '</span>
                                         <span class="label label-default" style="left: 0px;font-size: 11px;">' . $row['Name'] . ' | Last modify : ' . date('d M Y H:i', strtotime($row['UpdatedAt'])) . '</span> 
                                    </div>
                                    </div>';
                $nestedData[] = $btnAct;


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
        } else if ($data_arr['action'] == 'getListScheduleExam') {


            $NIP = $data_arr['NIP'];
            $Type = $data_arr['Type'];
            $SemesterID = $this->m_rest->_getSemesterActive()['SemesterID'];

            $schedule = $this->m_rest->__getExamSchedule4Lecturer($SemesterID, $NIP, strtolower($Type));

            return print_r(json_encode($schedule));
        } else if ($data_arr['action'] == 'getListScheduleExamSemesterActive') {


            $Type = $data_arr['Type'];
            $Date = $data_arr['Date'];
            $OnlineLearning = $data_arr['OnlineLearning'];
            $SemesterID = $this->m_rest->_getSemesterActive()['SemesterID'];

            $schedule = $this->m_rest->__getListScheduleExamSemesterActive($SemesterID, strtolower($Type), $Date, $OnlineLearning);

            return print_r(json_encode($schedule));
        } else if ($data_arr['action'] == 'ExamDateList') {

            $Type = $data_arr['Type'];
            $SemesterID = $this->m_rest->_getSemesterActive()['SemesterID'];
            $schedule = $this->m_rest->__getListExamDate($SemesterID, strtolower($Type));
            return print_r(json_encode($schedule));
        } else if ($data_arr['action'] == 'getDataStudentAnswers') {

            $QuizID = $data_arr['QuizID'];

            $data = $this->db->query('SELECT qqs.ID AS QuizStudentID, qqs.StartSession, 
                                                qqs.EndSession, qqs.Score, qqs.ShowScore, 
                                                qqs.WorkDuration, qqs.SubmittedAt,
                                                ats.Name, ats.NPM
                                                FROM db_academic.q_quiz_students qqs
                                                LEFT JOIN db_academic.auth_students ats ON (qqs.NPM = ats.NPM)
                                                WHERE qqs.QuizID = "' . $QuizID . '" ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getDataAnswersDetails') {

            $QuizStudentID = $data_arr['QuizStudentID'];

            $Question = $this->db->query('SELECT qqsd.ID AS QuizStudentsDetailsID, qqsd.EssayAnswer,
                                                            qqsd.Point AS PointAnswer, qqd.Point, 
                                                            qqsd.EntredAt, q.Question, qt.Description AS QT_Description, 
                                                            qqsd.QTID, qqsd.QID
                                                            FROM db_academic.q_quiz_students_details qqsd
                                                            LEFT JOIN db_academic.q_quiz_students qqs ON (qqs.ID = qqsd.QuizStudentID)
                                                            LEFT JOIN db_academic.q_quiz_details qqd 
                                                            ON (qqd.QID = qqsd.QID AND qqs.QuizID = qqd.QuizID)
                                                            LEFT JOIN db_academic.q_question q ON (q.ID = qqsd.QID)
                                                            LEFT JOIN db_academic.q_question_type qt ON (qt.ID = qqsd.QTID)
                                                            WHERE qqsd.QuizStudentID = "' . $QuizStudentID . '" ')
                ->result_array();

            if (count($Question) > 0) {
                for ($a = 0; $a < count($Question); $a++) {

                    $Options = $this->db->select('*')
                        ->order_by('ID', 'RANDOM')
                        ->get_where(
                            'db_academic.q_question_options',
                            array('QID' => $Question[$a]['QID'])
                        )->result_array();
                    $Question[$a]['Options'] = $Options;

                    $answer = $this->db->get_where(
                        'db_academic.q_quiz_students_option',
                        array(
                            'QuizStudentID' => $QuizStudentID, 'QuizStudentsDetailsID' => $Question[$a]['QuizStudentsDetailsID']
                        )
                    )->result_array();


                    $arrAnswer = [];
                    if (count($answer) > 0) {
                        for ($ans = 0; $ans < count($answer); $ans++) {
                            array_push($arrAnswer, $answer[$ans]['QOptionID']);
                        }
                    }

                    $Question[$a]['Answer'] = $arrAnswer;
                }
            }

            return print_r(json_encode($Question));
        } else if ($data_arr['action'] == 'setPointOfEssay') {

            $QuizStudentID = $data_arr['QuizStudentID'];
            $dataPoint = (array) $data_arr['dataPoint'];

            if (count($dataPoint) > 0) {
                for ($i = 0; $i < count($dataPoint); $i++) {
                    $d = (array) $dataPoint[$i];

                    $this->db->where('ID', $d['QuizStudentsDetailsID']);
                    $this->db->update(
                        'db_academic.q_quiz_students_details',
                        array('Point' => $d['Point'])
                    );
                    $this->db->reset_query();
                }
            }

            // menghitung ulang total point
            $arrPoint = $this->db->get_where(
                'db_academic.q_quiz_students_details',
                array('QuizStudentID' => $QuizStudentID)
            )->result_array();

            $TotalPoint = 0;
            if (count($arrPoint) > 0) {
                for ($p = 0; $p < count($arrPoint); $p++) {
                    $Point = ($arrPoint[$p]['Point'] != '' && $arrPoint[$p]['Point'] != null)
                        ? $arrPoint[$p]['Point'] : 0;
                    $TotalPoint = $TotalPoint + (float) $Point;
                }
            }

            $NewScore = str_replace(',', '.', $TotalPoint);
            // update point dan show score
            $this->db->where('ID', $QuizStudentID);
            $this->db->update(
                'db_academic.q_quiz_students',
                array('Score' => $NewScore, 'ShowScore' => '1')
            );
            $this->db->reset_query();

            return print_r(json_encode(array('NewScore' => $NewScore)));
        } else if ($data_arr['action'] == 'removeQuestion') {

            // cek apakah question sudah masuk kedalam kuis atau blm
            $data = $this->db->query('SELECT COUNT(*) AS Total 
                                        FROM db_academic.q_quiz_details 
                                        WHERE QID = "' . $data_arr['QID'] . '" ')
                ->result_array();

            if ($data[0]['Total'] <= 0) {
                // get data summernoteid
                $dataQuestion = $this->db->select('SummernoteID')->get_where(
                    'db_academic.q_question',
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
                        $this->db->delete('db_academic.q_question');
                        $this->db->reset_query();


                        $dataOption = $this->db->select('SummernoteID')
                            ->get_where(
                                'db_academic.q_question_options',
                                array('QID' => $data_arr['QID'])
                            )->result_array();

                        if (count($dataOption) > 0) {
                            for ($opt = 0; $opt < count($dataOption); $opt++) {
                                $this->m_rest
                                    ->checkImageSummernote('delete', $dataOption[$opt]['SummernoteID'], '', '');
                            }
                            $this->db->where('QID', $data_arr['QID']);
                            $this->db->delete('db_academic.q_question_options');
                            $this->db->reset_query();
                        }
                    }
                }
            }

            return print_r(json_encode(array('Usage' => $data[0]['Total'])));
        }
    }

    public function getkelompok_profesi()
    {
        $data = $this->db->where('DeletedBy is NULL')->order_by('ID_kelompok_profesi', 'ASC')->get('db_employees.employees_kelompok_profesi')->result_array();
        return print_r(json_encode($data));
    }
}
