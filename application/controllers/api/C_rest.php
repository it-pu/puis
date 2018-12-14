<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_rest extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('master/m_master');
        $this->load->library('JWT');
        $this->load->library('google');
    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "s3Cr3T-G4N";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    private function getInputToken2()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    private function cekAuthAPI($dataAuth){

        $auth = (array) $dataAuth;

        if($auth['user']=='students'){
            $db = 'db_academic.auth_students';
        } else if($auth['user']=='lecturer') {
            $db = 'db_employees.employees';
        } else if($auth['user']=='siak') {
            return true;
        }

        $data = $this->db->get_where($db, array('Password' => $auth['token']))->result_array();

        if(count($data)>0){
            return true;
        } else {
            return false;
        }

    }

    function checkDateKRS(){
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if($cekUser){
            $user = (array) $dataToken['auth'];
            if($user['user']=='lecturer'){
                $data = $this->m_api->__checkDateKRSLecturer($dataToken['date']);
            } else {
                $data = $this->m_api->__checkDateKRS($dataToken['SemesterIDActive'],$dataToken['date'],
                    $dataToken['ProdiID'],$dataToken['GroupProdiID'],$dataToken['ClassOf'],
                    $dataToken['NPM'],$dataToken['DB_std']);
            }

            return print_r(json_encode($data));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    function getDetailKRS(){
        $token = $this->input->post('token');
        $key = "s3Cr3T-G4N";
        $dataToken = (array) $this->jwt->decode($token,$key);

        $cekUser = $this->cekAuthAPI($dataToken['auth']);
        if($cekUser){
            $data = $this->m_api->getDetailStudyPlanning($dataToken['NPM'],$dataToken['ta']);
            return print_r(json_encode($data));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }

    }

    function getKSM(){
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if($cekUser){

            $data = $this->m_rest->__getKSM($dataToken['DB_'],$dataToken['ProdiID'],$dataToken['NPM'],$dataToken['ClassOf']);
            return print_r(json_encode($data));

        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }

    }

    public function getExamScheduleForStudent(){
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);
//
//        print_r($dataToken);
//        exit;

        if($cekUser){
            $data = $this->m_rest->__getExamScheduleForStudent($dataToken['DB_'],
                $dataToken['ProdiID'],$dataToken['SemesterID'],$dataToken['NPM'],
                $dataToken['SemeaterYear'],$dataToken['ClassOf'],
                $dataToken['ExamType'],$dataToken['Date']);
            return print_r(json_encode($data));
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function geTimetable(){

        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if($cekUser){

            if($dataToken['action']=='getTimeTable'){
                $NIP = $dataToken['NIP'];
//                $SemesterID = $dataToken['SemesterID'];
                $schedule = $this->m_rest->__geTimetable($NIP);

                return print_r(json_encode($schedule));
            }
            else if($dataToken['action']=='chekTeamTheaching'){

                $data = $this->db
                            ->get_where('db_academic.schedule_team_teaching',
                                array(
                                    'ScheduleID'=>$dataToken['ScheduleID'],
                                    'NIP'=>$dataToken['NIP']
                                ),1)
                            ->result_array();

                return print_r(json_encode($data));
            }
            else if($dataToken['action']=='updateStatusTeamTeaching'){
                $this->db->set('Status', $dataToken['Status']);
                $this->db->where('ID', $dataToken['ID']);
                $this->db->update('db_academic.schedule_team_teaching');
                return print_r(1);
            }

            else if($dataToken['action']=='getDetailsStudents'){
                $SemesterID = $dataToken['SemesterID'];
                $ScheduleID = $dataToken['ScheduleID'];

                $dataStd = $this->m_rest->__getStudentsDetails($SemesterID,$ScheduleID);

                return print_r(json_encode($dataStd));
            }

        }
        else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }


    }

    public function getExamSchedule(){

        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if($cekUser){

            if($dataToken['action']=='readExamSchedule'){
                $NIP = $dataToken['NIP'];
//                $SemesterID = $dataToken['SemesterID'];
                $schedule = $this->m_rest->__getExamSchedule($NIP,strtolower($dataToken['Type']));

                return print_r(json_encode($schedule));
            }


        }
        else {
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
        if($cekUser) {
            $NPM = $dataToken['NPM'];
            $arr = $this->m_api->cek_deadline_paymentNPM($NPM);
            return print_r(json_encode($arr));
        }
        else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function getStudyResult(){
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if($cekUser){

            $data = $this->m_rest->getDetailStudyResultByNPM($dataToken['ClassOf'],$dataToken['NPM']);
            return print_r(json_encode($data));

        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function getTranscript(){
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if($cekUser){

            $data = $this->m_rest->getTranscript($dataToken['ClassOf'],$dataToken['NPM'],'ASC');
            return print_r(json_encode($data));

        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    // Nandang - digunakan khusus untuk selec2.js
    public function geTStudent_ServerSide(){

        $term = $this->input->get('term');

        $data = $this->db->query('SELECT * FROM db_academic.auth_students auts WHERE 
                                                  auts.NPM LIKE "%'.$term.'%" 
                                                  OR auts.Name LIKE "%'.$term.'%" LIMIT 15 ')->result_array();

        $result = [];
        if(count($data)>0){
            for($i=0;$i<count($data);$i++){
                $d = $data[$i];
                $arr = array(
                    'id' => $d['NPM'],
                    'text' => $d['NPM'].' - '.$d['Name']
                );
                array_push($result,$arr);
            }
        }

        $data_result = array(
            'results' => $result
        );

        return print_r(json_encode($data_result));

    }

    public function crudCounseling(){
        $dataToken = $this->getInputToken();
        $cekUser = $this->cekAuthAPI($dataToken['auth']);

        if($cekUser){
            if($dataToken['action']=='insertToNewTopic'){

                $dataTopic = (array) $dataToken['dataTopic'];

                $this->db->insert('db_academic.counseling_topic', $dataTopic);
                $insert_id = $this->db->insert_id();

                // Insert Lecturer
                $this->db->insert('db_academic.counseling_user', array(
                    'TopicID' => $insert_id,
                    'UserID' => $dataTopic['CreateBy'],
                    'ReadComment' => 0,
                    'Status' => '1'
                ));

                // Cek Invite To
                if($dataTopic['InviteTo']==1 || $dataTopic['InviteTo']=='1'){
                    $formSelectStudent = $dataToken['formSelectStudent'];
                    for($u=0;$u<count($formSelectStudent);$u++){
                        $dataIns = array(
                            'TopicID' => $insert_id,
                            'UserID' => $formSelectStudent[$u],
                            'ReadComment' => 0,
                            'Status' => '2'
                        );
                        $this->db->insert('db_academic.counseling_user',$dataIns);
                    }
                }
                else if($dataTopic['InviteTo']==2 || $dataTopic['InviteTo']=='2'){
                    $dataStudent = $this->m_rest->__getStudentByScheduleID($dataToken['SemesterID'],$dataTopic['ScheduleID']);
                    if(count($dataStudent)>0){
                        for($s=0;$s<count($dataStudent);$s++){
                            $d_s = $dataStudent[$s];
                            $dataIns = array(
                                'TopicID' => $insert_id,
                                'UserID' => $d_s['NPM'],
                                'ReadComment' => 0,
                                'Status' => '2'
                            );
                            $this->db->insert('db_academic.counseling_user',$dataIns);
                        }
                    }
                }
                else {
                    $dataStdMentor = $this->db->select('NPM')->get_where('db_academic.mentor_academic'
                        ,array('NIP' => $dataTopic['CreateBy']))->result_array();

                    if(count($dataStdMentor)>0){
                        for($m=0;$m<count($dataStdMentor);$m++){
                            $d_m = $dataStdMentor[$m];
                            $dataIns = array(
                                'TopicID' => $insert_id,
                                'UserID' => $d_m['NPM'],
                                'ReadComment' => 0,
                                'Status' => '2'
                            );
                            $this->db->insert('db_academic.counseling_user',$dataIns);
                        }
                    }
                }
                return print_r(1);
            }
            else if($dataToken['action']=='readTopic'){

                $requestData= $_REQUEST;

                $UserID = $dataToken['UserID'];

                $dataSearch = '';
                if( !empty($requestData['search']['value']) ) {
                    $search = $requestData['search']['value'];
                    $dataSearch = ' AND ( ct.Topic LIKE "%'.$search.'%" )';
                }

                $queryDefault = 'SELECT cu.ReadComment, ct.* FROM db_academic.counseling_user cu
                                              LEFT JOIN db_academic.counseling_topic ct 
                                              ON (ct.ID = cu.TopicID)
                                              WHERE ( cu.UserID = "'.$UserID.'" ) '.$dataSearch.'
                                               ORDER BY cu.TopicID DESC';

                $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

                $query = $this->db->query($sql)->result_array();
                $queryDefaultRow = $this->db->query($queryDefault)->result_array();

                $no = $requestData['start'] + 1;
                $data = array();

                for($i=0;$i<count($query);$i++) {
                    $nestedData = array();
                    $row = $query[$i];

                    $dataTotalUser = $this->db->select('ID')->get_where('db_academic.counseling_user',array('TopicID' => $row['ID'], 'Status' => '2'))->result_array();
                    $dataComment = $this->db->select('ID')->get_where('db_academic.counseling_comment',array('TopicID' => $row['ID']))->result_array();

                    $dataToken = array(
                        'TopicID' => $row['ID'],
                        'TotalComment' => count($dataComment)
                    );

                    $ReadComment = (int) $row['ReadComment'];
                    $ur = count($dataComment) - $ReadComment;
                    $unread = ($ur>0) ? ' - <span style="color: #ff5722;">'.$ur.' unread comments</span>' : '';

                    $key = "s3Cr3T-G4N";
                    $token = $this->jwt->encode($dataToken,$key);


                    $topic = '<a href="'.url_sign_in_lecturers.'counseling/detail-topic/'.$token.'">'.$row['Topic'].'</a>
                              <br/><span style="font-size: 12px;color: #9e9e9e;">'.date('D, d M Y',strtotime($row['CreateAt'])).'</span>'.$unread;

                    $btnAction = '<button class="btn btn-sm btn-default btn-default-primary btn-act"><i class="fa fa-pencil"></i></button> | <button class="btn btn-sm btn-default btn-default-danger btn-act"><i class="fa fa-trash"></i></button>';
                    $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
                    $nestedData[] = '<div  style="text-align:left;">'.$topic.'</div>';
                    $nestedData[] = '<div  style="text-align:center;"><i class="fa fa-user"></i> <span>'.count($dataTotalUser).'</span></div>';
                    $nestedData[] = '<div  style="text-align:center;"><i class="fa fa-comments"></i> <span>'.count($dataComment).'</span></div>';
                    $nestedData[] = '<div  style="text-align:center;">'.$btnAction.'</div>';

                    $no++;

                    $data[] = $nestedData;
                }

                $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),
                    "recordsTotal"    => intval(count($queryDefaultRow)),
                    "recordsFiltered" => intval( count($queryDefaultRow) ),
                    "data"            => $data
                );

                echo json_encode($json_data);
//                $data = $this->m_rest->getTopicByUserID($UserID);
//                return print_r(json_encode($data));
            }
            else if($dataToken['action']=='readDetailTopic'){

                // Update total comment
                $this->db->set('ReadComment', $dataToken['TotalComment']);
                $this->db->where(array(
                    'TopicID' => $dataToken['TopicID'],
                    'UserID' => $dataToken['UserID']
                ));
                $this->db->update('db_academic.counseling_user');
                $this->db->reset_query();

                $dataTopic = $this->db->query('SELECT * FROM db_academic.counseling_topic ct 
                                                          WHERE ct.ID = "'.$dataToken['TopicID'].'" LIMIT 1 ')->result_array();


                // Read Comment
                if(count($dataTopic)>0){

                    $dataComment = $this->db->query('SELECT cc.*, cu.Status, em.Name AS Lecturer, em.Photo AS EmPhoto , auts.Name AS Student, auts.Year FROM db_academic.counseling_comment cc 
                                                                LEFT JOIN db_academic.counseling_user cu ON (cu.TopicID = cc.TopicID AND cu.UserID = cc.UserID) 
                                                                LEFT JOIN db_academic.auth_students auts ON (auts.NPM = cc.UserID)
                                                                LEFT JOIN db_employees.employees em ON (em.NIP = cc.UserID)
                                                                WHERE cc.TopicID = "'.$dataToken['TopicID'].'"
                                                                ORDER BY cc.ID ASC ')->result_array();

                    if(count($dataComment)>0){
                        for($i=0;$i<count($dataComment);$i++){

                            if($dataComment[$i]['Status']==2 || $dataComment[$i]['Status']=='2'){
                                // Get Photo Student
                                $db_std = 'ta_'.$dataComment[$i]['Year'];
                                $dataPhoto = $this->db->select('Photo')->get_where($db_std.'.students',array('NPM'=>$dataComment[$i]['UserID'],1))->result_array();

                                $dataComment[$i]['Photo'] = url_img_students.''.$dataPhoto[0]['Photo'];
                            } else {
                                $dataComment[$i]['Photo'] = url_img_employees.''.$dataComment[$i]['EmPhoto'];
                            }



                            if($dataComment[$i]['CommentID']!=null && $dataComment[$i]['CommentID']!=''){
                                $dataQuote = $this->db->query('SELECT cc.*, cu.Status, em.Name AS Lecturer, auts.Name AS Student FROM db_academic.counseling_comment cc 
                                                                LEFT JOIN db_academic.counseling_user cu ON (cu.TopicID = cc.TopicID AND cu.UserID = cc.UserID) 
                                                                LEFT JOIN db_academic.auth_students auts ON (auts.NPM = cc.UserID)
                                                                LEFT JOIN db_employees.employees em ON (em.NIP = cc.UserID)
                                                                WHERE cc.ID = "'.$dataComment[$i]['CommentID'].'" ')->result_array();
                                $dataComment[$i]['Quote'] = $dataQuote;
                            }
                        }
                    }

                    $dataTopic[0]['Comment'] = $dataComment;
                }

                return print_r(json_encode($dataTopic));


            }
            else if($dataToken['action']=='addComment'){

                $dataForm = (array) $dataToken['dataForm'];
                $this->db->insert('db_academic.counseling_comment',$dataForm);

                return print_r(1);
            }
        } else {
            $msg = array(
                'msg' => 'Error'
            );
            return print_r(json_encode($msg));
        }
    }

    public function getTableData($db = null,$table = null)
    {
        error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $json = array();
                if ( ($db != null || $db != '') && ($table != null || $table != '')  ) {
                    $json = $this->m_master->showData_array($db.'.'.$table);
                }
                echo json_encode($json);
                
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
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
                
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
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
                
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
        }
    }

    private function datatableSSRuleUser()
    {
        $requestData= $_REQUEST;
        $gettotalData = function($requestData){
                $sql = 'select count(*) as total from (
                        select a.ID,a.NIP,a.IDDivision,a.privilege,b.Name,c.Division from
                        db_employees.rule_users as a left join 
                        db_employees.employees as b on 
                        a.NIP = b.NIP 
                        left join db_employees.division as c 
                        on a.IDDivision = c.ID
                        where 
                        b.Name like "%'.$requestData['search']['value'].'%" or a.NIP like "%'.$requestData['search']['value'].'%" or c.Division like "%'.$requestData['search']['value'].'%"
                )aa';
                $query=$this->db->query($sql, array())->result_array();
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
                b.Name like "%'.$requestData['search']['value'].'%" or a.NIP like "%'.$requestData['search']['value'].'%" or c.Division like "%'.$requestData['search']['value'].'%"';
        $sql.= ' order by a.NIP desc LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';
        $query = $this->db->query($sql)->result_array();

        $data = array();
        $No = $requestData['start'] + 1;
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];
            $nestedData[] = $No;
            $nestedData[] = $row['NIP'].' || '.$row['Name'];
            $nestedData[] = $row['Division'];
            $action = '<button type="button" class="btn btn-danger btn-delete" data-sbmt="'.$row['ID'].'"> <i class="fa fa-trash" aria-hidden="true"></i> Delete</button>';

            $nestedData[] = $action;
            $nestedData[] = $row['NIP'];
            $nestedData[] = $row['IDDivision'];
            $nestedData[] = $row['ID'];
            $data[] = $nestedData;
            $No++;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData ),
            "data"            => $data
        );
        return $json_data;
        // echo json_encode($json_data);
    }

    public function getEmployees($Status = 'aktif')
    {
        error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                if ($Status == 'aktif') {
                    $AddSql = ' where StatusEmployeeID not in (-1,-2,4,6)';
                }
                else
                {
                    $AddSql = ' where StatusEmployeeID = "'.$Status.'"';
                }

                $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                            ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status, em.Address, ems.Description, em.StatusEmployeeID
                            FROM db_employees.employees em 
                            LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                            LEFT JOIN db_employees.employees_status ems ON (ems.IDStatus = em.StatusEmployeeID) 
                            ';

                $sql.= $AddSql;
                $query=$this->db->query($sql, array())->result_array();

                echo json_encode($query);
                
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
        }
        
    }

    public function loadDataFormulirGlobal()
    {
        // error_reporting(0);
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $where = (!array_key_exists("division",$dataToken)) ? ' where a.Years = "'.$dataToken['selectTahun'].'"' : ' where a.Division ="'.$dataToken['division'].'" and a.Years = "'.$dataToken['selectTahun'].'" ';
                $sql = 'SELECT a.*,b.FormulirCode from db_admission.formulir_number_global as a left join db_admission.formulir_number_offline_m as b on a.FormulirCodeGlobal = b.No_Ref'.$where.' group by a.FormulirCodeGlobal';
                $query=$this->db->query($sql, array())->result_array();
                echo json_encode($query);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
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
                $where = (!array_key_exists("division",$dataToken)) ? ' where a.Status = 0 and a.Years = "'.$Ta.'"' : ' where a.Division ="'.$dataToken['division'].'" and a.Status = 0 and a.Years = "'.$Ta.'"';
                $sql = 'SELECT a.*,b.FormulirCode from db_admission.formulir_number_global as a left join db_admission.formulir_number_offline_m as b on a.FormulirCodeGlobal = b.No_Ref'.$where.' group by a.FormulirCodeGlobal';
                $query=$this->db->query($sql, array())->result_array();
                echo json_encode($query);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
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
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
        }
    }

    public function rekapintake_reset()
    {
        $data = file_get_contents('php://input');
        
        $data_json = json_decode($data,true);

        if (!$data_json) {
            // handling orang iseng
            echo '{"status":"999","message":"jangan iseng :D"}';
        }
        else {
            try {
                $getData = $data_json['data'];
                $token = $getData;
                $key = "UAP)(*";
                $dataToken = (array) $this->jwt->decode($token,$key);
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('statistik/m_statistik');
                    $Year = $dataToken['Year'];
                    if ($dataToken['action'] == 'reset') {
                       // drop table
                        $this->m_statistik->droptablerekapintake($Year);
                    }
                     //$this->m_statistik->droptablerekapintake($Year);
                     $result = $this->m_statistik->ShowRekapIntake($Year);

                    echo '{"status":"000"}';
                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }

            }
            catch(Exception $e) {
              // handling orang iseng
              echo '{"status":"999","message":"jangan iseng :D"}';
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
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
        }
    }

    public function trigger_formulir()
    {
        $data = file_get_contents('php://input');
        
        $data_json = json_decode($data,true);

        if (!$data_json) {
            // handling orang iseng
            echo '{"status":"999","message":"jangan iseng :D"}';
        }
        else {
            try {
                $getData = $data_json['data'];
                $token = $getData;
                $key = "UAP)(*";
                $dataToken = (array) $this->jwt->decode($token,$key);
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('statistik/m_statistik');
                    $ta = $dataToken['ta'];
                    // month & year
                    $month = $dataToken['month'];
                    $year = $dataToken['year'];
                    $ProdiID = $dataToken['ProdiID'];
                    $action = $dataToken['action'];
                    $this->m_statistik->trigger_formulir($ta,$month,$year,$ProdiID,$action);
                    echo '{"status":"000"}';
                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }

            }
            catch(Exception $e) {
              // handling orang iseng
              echo '{"status":"999","message":"jangan iseng :D"}';
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
                if(array_key_exists("action",$dataToken))
                {
                    if ($dataToken['action'] == 'reset') {
                        $tblname = 'rekapintake_bea_'.$Year;
                       // drop table
                        $this->m_statistik->droptable($tblname);
                    }
                }
                $result = $this->m_statistik->ShowRekapIntake_Beasiswa($Year);
                echo json_encode($result);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
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
                if(array_key_exists("action",$dataToken))
                {
                    if ($dataToken['action'] == 'reset') {
                        $tblname = 'rekapintake_sch_'.$Year;
                       // drop table
                        $this->m_statistik->droptable($tblname);
                    }
                }
                $result = $this->m_statistik->ShowRekapIntake_School($Year);
                echo json_encode($result);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
        }
    }

    public function rekapmhspayment()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('statistik/m_statistik');
                if(array_key_exists("action",$dataToken))
                {
                    if ($dataToken['action'] == 'reset') {
                        $tblname = 'summary_payment_mhs';
                       // drop table
                        $this->m_statistik->droptable($tblname);
                    }
                }
                $result = $this->m_statistik->ShowRekap_summary_payment_mhs();
                echo json_encode($result);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
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
                $arr = array('to','subject','text');
                $bool = true;
                foreach ($dataToken as $key => $value) {
                    if ($key != 'auth' && $key != 'attach') {
                        if(!in_array($key,$arr))
                        {
                            $bool = false;
                            $msg ='Field is not match, the field is : '.$key;
                            break;
                        }
                    }
                }

                if ($bool) {
                    $to = $dataToken['to'];
                    $subject = $dataToken['to'];
                    $text = $dataToken['text'];
                    if (array_key_exists('attach',$dataToken)) {
                        $path = $dataToken['attach'];
                        $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text,$path);
                    }
                    else
                    {
                        $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                    }

                   $msg =  $sendEmail['msg'];
                    
                }

                echo json_encode($msg);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"jangan iseng :D"}';
        }
    }


}
