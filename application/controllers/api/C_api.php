<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once APPPATH.'vendor/autoload.php';
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;

class C_api extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('master/m_master');
        $this->load->model('hr/m_hr');
        $this->load->model('vreservation/m_reservation');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('notification/m_log');
        $this->load->library('JWT');
        $this->load->library('google');

//        if($this->session->userdata('loggedIn')==false){
//            $data = array(
//                'Message' => 'Error',
//                'Description' => 'Your Session Login Is Destroy'
//            );
//            print_r(json_encode($data));
//            exit;
//        }
    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }



    public function getKurikulumByYear(){

//        $year = $this->input->get('year');

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $result = $this->m_api->__getKurikulumByYear($data_arr['SemesterSearch'],$data_arr['year'],$data_arr['ProdiID']);

        return print_r(json_encode($result));
    }

    public function getProdi(){
        $data = $this->m_api->__getBaseProdi();
        return print_r(json_encode($data));
    }

    public function getProdiSelectOption(){
        $data = $this->m_api->__getBaseProdiSelectOption();
        return print_r(json_encode($data));
    }

    public function getProdiSelectOptionAll(){
        $data = $this->m_api->__getBaseProdiSelectOptionAll();
        return print_r(json_encode($data));
    }

    public function getKurikulumSelectOption(){
        $data = $this->m_api->__getKurikulumSelectOption();
        return print_r(json_encode($data));
    }

    public function getKurikulumSelectOptionASC(){
        $data = $this->m_api->__getKurikulumSelectOptionASC();
        return print_r(json_encode($data));
    }

    public function getMKByID(){
        $ID = $this->input->post('idMK');
        $data = $this->m_api->__getMKByID($ID);
        return print_r(json_encode($data));
    }

    public function getSemester(){
        $data = $this->m_tahun_akademik->__getSemester();
        return print_r(json_encode($data));
    }

    public function getLecturer2(){
        $data = $this->m_api->__getLecturer();
        return print_r(json_encode($data));
    }

    public function getLecturer(){
        $requestData= $_REQUEST;

        $totalData = $this->db->query('SELECT *  FROM db_employees.employees WHERE PositionMain = "14.7"')->result_array();

        if( !empty($requestData['search']['value']) ) {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain = "14.5" OR em.PositionMain = "14.6" OR em.PositionMain = "14.7")  AND ( ';

            $sql.= ' em.NIP LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR em.Name LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR ps.NameEng LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ') ORDER BY em.PositionMain, NIP ASC';

        }
        else {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain = "14.5" OR em.PositionMain = "14.6" OR em.PositionMain = "14.7")';
            $sql.= 'ORDER BY em.PositionMain, NIP ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        }

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $jb = explode('.',$row["PositionMain"]);
            $Division = '';
            $Position = '';

            if(count($jb)>1){
                $dataDivision = $this->db->select('Division')->get_where('db_employees.division',array('ID'=>$jb[0]),1)->result_array()[0];
                $dataPosition = $this->db->select('Position')->get_where('db_employees.position',array('ID'=>$jb[1]),1)->result_array()[0];
                $Division = $dataDivision['Division'];
                $Position = $dataPosition['Position'];
            }

            $imgEmp = url_img_employees.''.$row["Photo"];
            $nestedData[] = $row["NIP"];
            $nestedData[] = $row["NIDN"];
            $nestedData[] = '<div style="text-align: center;"><img src="'.$imgEmp.'" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="'.base_url('database/lecturer-details/'.$row["NIP"]).'" style="font-weight: bold;">'.$row["Name"].'</a>';
            $nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = $Division.' - '.$Position;
            $nestedData[] = $row["ProdiNameEng"];

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);

    }

    public function getEmployees()
    {
        $requestData= $_REQUEST;
        // print_r($requestData);

        $totalData = $this->db->query('SELECT *  FROM db_employees.employees WHERE PositionMain not like "%14%"')->result_array();

        if( !empty($requestData['search']['value']) ) {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain not like "%14%")  AND ( ';

            $sql.= ' em.NIP LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR em.Name LIKE "%'.$requestData['search']['value'].'%" ';
            $sql.= ' OR ps.NameEng LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ') ORDER BY NIP,em.PositionMain  ASC';

        }
        else {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain not like "%14%")';
            $sql.= 'ORDER BY NIP,em.PositionMain ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        }

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $jb = explode('.',$row["PositionMain"]);
            $Division = '';
            $Position = '';

            if(count($jb)>1){
                $dataDivision = $this->db->select('Division')->get_where('db_employees.division',array('ID'=>$jb[0]),1)->result_array()[0];
                $dataPosition = $this->db->select('Position')->get_where('db_employees.position',array('ID'=>$jb[1]),1)->result_array()[0];
                $Division = $dataDivision['Division'];
                $Position = $dataPosition['Position'];
            }

            $nestedData[] = $row["NIP"];
            // $nestedData[] = $row["NIDN"];
            $nestedData[] = '<div style="text-align: center;"><img src="http://siak.podomorouniversity.ac.id/includes/foto/'.$row["Photo"].'" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="'.base_url('database/lecturer-details/'.$row["NIP"]).'" style="font-weight: bold;">'.$row["Name"].'</a>';
            $nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = $Division.' - '.$Position;
            $nestedData[] = $row["EmailPU"];
            $nestedData[] = ($row["Status"] == '1') ? 'Aktif' : 'Tidak Aktif';
            $nestedData[] = '<span data-smt="'.$row["NIP"].'" class="btn btn-xs btn-edit">
                                    <i class="fa fa-pencil-square-o"></i> Edit
                                   </span>
                                   <span data-smt="'.$row["NIP"].'" class="btn btn-xs btn-Active" data-active = "'.$row["Status"].'">
                                    <i class="fa fa-pencil-square-o"></i> ChangeStatus
                                   </span>';
            $data[] = $nestedData;
        }

        // print_r($data);

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }


    public function getEmployeesHR()
    {

        $status = $this->input->get('s');
        $requestData= $_REQUEST;
        // print_r($requestData);

        $whereStatus = ($status!='') ? ' AND StatusEmployeeID = "'.$status.'" ' : '';
        print_r($status);

        $totalData = $this->db->query('SELECT *  FROM db_employees.employees WHERE StatusEmployeeID != -2 '.$whereStatus)->result_array();

        if( !empty($requestData['search']['value']) ) {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status, em.Address, ems.Description, em.StatusEmployeeID
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        LEFT JOIN db_employees.employees_status ems ON (ems.IDStatus = em.StatusEmployeeID) 
                        WHERE em.StatusEmployeeID != -2 '.$whereStatus.' AND ( ';

            $sql.= ' em.NIP LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR em.Name LIKE "%'.$requestData['search']['value'].'%" ';
            $sql.= ' OR ps.NameEng LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ') ORDER BY NIP  ASC';

        }
        else {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status, em.Address, ems.Description, em.StatusEmployeeID
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        LEFT JOIN db_employees.employees_status ems ON (ems.IDStatus = em.StatusEmployeeID) 
                        WHERE em.StatusEmployeeID != -2 '.$whereStatus;
            $sql.= 'ORDER BY em.StatusEmployeeID, NIP ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        }

        $query = $this->db->query($sql)->result_array();

        $data = array();

        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $jb = explode('.',$row["PositionMain"]);
            $Division = '';
            $Position = '';

            if(count($jb)>1){
                $dataDivision = $this->db->select('Division')->get_where('db_employees.division',array('ID'=>$jb[0]),1)->result_array()[0];
                $dataPosition = $this->db->select('Position')->get_where('db_employees.position',array('ID'=>$jb[1]),1)->result_array()[0];
                $Division = $dataDivision['Division'];
                $Position = $dataPosition['Position'];
            }

            $photo = (file_exists('./uploads/employees/'.$row["Photo"]) && $row["Photo"]!='' && $row["Photo"]!=null)
                ? base_url('uploads/employees/'.$row["Photo"])
                : base_url('images/icon/userfalse.png');

            $nestedData[] = '<div style="text-align: center;"><img src="'.$photo.'" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';

            $emailPU = ($row["EmailPU"]!='' && $row["EmailPU"]!=null) ? '<br/><span style="color: darkred;">'.$row["EmailPU"].'</span>' : '';
            $nidn = ($row["NIDN"]!='' && $row["NIDN"]!=null) ? '<br/>NIDN : '.$row["NIDN"] : '';
            $nestedData[] = '<a href="'.base_url('human-resources/employees/edit-employees/'.$row["NIP"]).'" style="font-weight: bold;">'.$row["Name"].'</a>
                                '.$emailPU.'
                                <br/>NIK : '.$row["NIP"].'
                                '.$nidn;
//            $nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = $Division.'<br/>'.$Position;
            $nestedData[] = $row["Address"];

//            $nestedData[] = $row['Description'];
            $status = '-';
            if($row['StatusEmployeeID']==1){
                $status = '<i class="fa fa-circle" style="color: #4CAF50;"></i>';
            } else if($row['StatusEmployeeID']==2){
                $status = '<i class="fa fa-circle" style="color: #FF9800;"></i>';
            } else if($row['StatusEmployeeID']==3){
                $status = '<i class="fa fa-circle" style="color: #03A9F4;"></i>';
            } else if($row['StatusEmployeeID']==4){
                $status = '<i class="fa fa-circle" style="color: #9e9e9e;"></i>';
            } else if($row['StatusEmployeeID']==-1){
                $status = '<i class="fa fa-warning" style="color: #F44336;"></i>';
            }
            $nestedData[] = '<div style="text-align: center;">'.$status.'</div>';

            $data[] = $nestedData;

        }

        // print_r($data);

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getStudents(){
        $requestData= $_REQUEST;

        $dataYear = $this->input->get('dataYear');
        $dataProdiID = $this->input->get('dataProdiID');
        $dataStatus = $this->input->get('s');

        $db_ = 'ta_'.$dataYear;

        $dataWhere = 's.ProdiID = "'.$dataProdiID.'"';
        $arryWhere = array('ProdiID' => $dataProdiID);
        if($dataStatus!='' && $dataStatus!=null){
            $arryWhere = array(
                'ProdiID' => $dataProdiID,
                'StatusStudentID' => $dataStatus
            );
            $dataWhere = 's.ProdiID = "'.$dataProdiID.'" AND s.StatusStudentID = "'.$dataStatus.'" ';
        }

        $totalData = $this->db->get_where($db_.'.students',$arryWhere
        )->result_array();

        $sql = 'SELECT s.NPM, s.Photo, s.Name, s.Gender, s.ClassOf, ps.NameEng AS ProdiNameEng, s.StatusStudentID, 
                          ss.Description AS StatusStudent, ast.Password, ast.Password_Old, ast.Status AS StatusAuth, 
                          ast.EmailPU
                          FROM '.$db_.'.students s 
                          LEFT JOIN db_academic.program_study ps ON (ps.ID = s.ProdiID)
                          LEFT JOIN db_academic.status_student ss ON (ss.ID = s.StatusStudentID)
                          LEFT JOIN db_academic.auth_students ast ON (ast.NPM = s.NPM)';

        if( !empty($requestData['search']['value']) ) {
            $sql.= ' WHERE '.$dataWhere.' AND ( s.NPM LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR s.Name LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR s.ClassOf LIKE "'.$requestData['search']['value'].'%" )';
            $sql.= ' ORDER BY s.NPM, s.ProdiID ASC';
        }
        else {
            $sql.= 'WHERE '.$dataWhere.' ORDER BY s.NPM, s.ProdiID ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';
        }

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $Gender = ($row["Gender"]=='P') ? 'Female' : 'Male';

            $label = '';
            if($row['StatusStudentID']==7 || $row['StatusStudentID'] ==6 || $row['StatusStudentID'] ==4){
                $label = 'style="color: red;"';
            } else if($row['StatusStudentID'] ==2){
                $label = 'style="color: #ff9800;"';
            } else if($row['StatusStudentID'] ==3){
                $label = 'style="color: green;"';
            } else if($row['StatusStudentID'] ==1){
                $label = 'style="color: #03a9f4;"';
            }

//            $nestedData[] = '<div style="text-align: center;">'.$row["NPM"].'</div>';
            $nestedData[] = '<div style="text-align: center;"><img src="'.base_url('uploads/students/').$db_.'/'.$row["Photo"].'" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="javascript:void(0);" data-npm="'.$row["NPM"].'" data-ta="'.$row["ClassOf"].'" class="btnDetailStudent"><b>'.$row["Name"].'</b></a><br/>'.$row["NPM"];
            $nestedData[] = '<div style="text-align: center;">'.$Gender.'</div>';
            $nestedData[] = '<div class="dropdown">
                                  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="fa fa-pencil-square-o"></i>
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a href="javascript:void(0);" class="btn-edit-student " data-npm="'.$row["NPM"].'" ta = "'.$db_.'">Edit Student</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="javascript:void(0);" class="btn-reset-password " data-npm="'.$row["NPM"].'" data-name="'.$row["Name"].'" data-statusid="'.$row['StatusStudentID'].'">Reset Password</a></li>
                                    <li><a href="javascript:void(0);" class="btn-change-status " data-emailpu="'.$row["EmailPU"].'" data-year="'.$dataYear.'" data-npm="'.$row["NPM"].'" data-name="'.$row["Name"].'" data-statusid="'.$row['StatusStudentID'].'">Change Status</a></li>
                                    
                                  </ul>
                                </div>';
            $nestedData[] = '<div style="text-align: center;"><button class="btn btn-sm btn-primary btnLoginPortalStudents " data-npm="'.$row["NPM"].'"><i class="fa fa-sign-in right-margin"></i> Login Portal</button></div>';
//            $nestedData[] = $row["ProdiNameEng"];
            $nestedData[] = '<div style="text-align: center;"><i class="fa fa-circle" '.$label.'></i></div>';

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);

    }

    public function getStudentsAdmission(){
        $requestData= $_REQUEST;

        $dataYear = $this->input->get('dataYear');
        $dataProdiID = $this->input->get('dataProdiID');
        $dataStatus = $this->input->get('s');

        $db_ = 'ta_'.$dataYear;

        $dataWhere = 's.ProdiID = "'.$dataProdiID.'"';
        $arryWhere = array('ProdiID' => $dataProdiID);
        if($dataStatus!='' && $dataStatus!=null){
            $arryWhere = array(
                'ProdiID' => $dataProdiID,
                'StatusStudentID' => $dataStatus
            );
            $dataWhere = 's.ProdiID = "'.$dataProdiID.'" AND s.StatusStudentID = "'.$dataStatus.'" ';
        }

        $totalData = $this->db->get_where($db_.'.students',$arryWhere
        )->result_array();

        $sql = 'SELECT asx.FormulirCode, s.NPM, s.Photo, s.Name, s.Gender, s.ClassOf, ps.NameEng AS ProdiNameEng, s.StatusStudentID, 
                          ss.Description AS StatusStudent, ast.Password, ast.Password_Old, ast.Status AS StatusAuth, 
                          ast.EmailPU
                          FROM '.$db_.'.students s 
                          LEFT JOIN db_academic.program_study ps ON (ps.ID = s.ProdiID)
                          LEFT JOIN db_academic.status_student ss ON (ss.ID = s.StatusStudentID)
                          LEFT JOIN db_academic.auth_students ast ON (ast.NPM = s.NPM)
                          LEFT JOIN db_admission.to_be_mhs asx ON (ast.NPM = asx.NPM)
                          ';

        if( !empty($requestData['search']['value']) ) {
            $sql.= ' WHERE '.$dataWhere.' AND ( s.NPM LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR s.Name LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR s.ClassOf LIKE "'.$requestData['search']['value'].'%" )';
            $sql.= ' OR asx.FormulirCode LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' ORDER BY s.NPM, s.ProdiID ASC';
        }
        else {
            $sql.= 'WHERE '.$dataWhere.' ORDER BY s.NPM, s.ProdiID ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';
        }

        // print_r($sql);die();

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $Gender = ($row["Gender"]=='P') ? 'Female' : 'Male';

            $label = '';
            if($row['StatusStudentID']==7 || $row['StatusStudentID'] ==6 || $row['StatusStudentID'] ==4){
                $label = 'style="color: red;"';
            } else if($row['StatusStudentID'] ==2){
                $label = 'style="color: #ff9800;"';
            } else if($row['StatusStudentID'] ==3){
                $label = 'style="color: green;"';
            } else if($row['StatusStudentID'] ==1){
                $label = 'style="color: #03a9f4;"';
            }

//            $nestedData[] = '<div style="text-align: center;">'.$row["NPM"].'</div>';
            // $nestedData[] = '<div style="text-align: center;"><img src="'.base_url('uploads/students/').$db_.'/'.$row["Photo"].'" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = $row["FormulirCode"];
            $nestedData[] = '<a href="javascript:void(0);" data-npm="'.$row["NPM"].'" data-ta="'.$row["ClassOf"].'" class="btnDetailStudent"><b>'.$row["Name"].'</b></a><br/>'.$row["NPM"];
            $nestedData[] = '<div style="text-align: center;"><button class="btn btn-inverse btn-notification btn-show" NPM="'.$row["NPM"].'" Name = "'.$row["Name"].'">Show</button></div>';
            $nestedData[] = '<div style="text-align: center;">'.$Gender.'</div>';
            // $nestedData[] = '<div class="dropdown">
            //                       <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
            //                         <i class="fa fa-pencil-square-o"></i>
            //                         <span class="caret"></span>
            //                       </button>
            //                       <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
            //                         <li><a href="javascript:void(0);" class="btn-edit-student " data-npm="'.$row["NPM"].'" ta = "'.$db_.'">Edit Student</a></li>
            //                         <li role="separator" class="divider"></li>
            //                         <li><a href="javascript:void(0);" class="btn-reset-password " data-npm="'.$row["NPM"].'" data-name="'.$row["Name"].'" data-statusid="'.$row['StatusStudentID'].'">Reset Password</a></li>
            //                         <li><a href="javascript:void(0);" class="btn-change-status " data-emailpu="'.$row["EmailPU"].'" data-year="'.$dataYear.'" data-npm="'.$row["NPM"].'" data-name="'.$row["Name"].'" data-statusid="'.$row['StatusStudentID'].'">Change Status</a></li>

            //                       </ul>
            //                     </div>';
//            $nestedData[] = $row["ProdiNameEng"];
            $nestedData[] = '<div style="text-align: center;"><i class="fa fa-circle" '.$label.'></i></div>';

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);

    }

    // add bismar
    public function getfileEmployees() {

        $status = $this->input->get('s');
        $requestData= $_REQUEST;
        
        $whereStatus = ($status!='') ? ' AND StatusEmployeeID = "'.$status.'" ' : '';
        $totalData = $this->db->query('SELECT *  FROM db_employees.employees WHERE StatusEmployeeID != -2 '.$whereStatus)->result_array();

        if( !empty($requestData['search']['value']) ) {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status, em.StatusEmployeeID
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        LEFT JOIN db_employees.employees_status ems ON (ems.IDStatus = em.StatusEmployeeID) 
                        WHERE em.StatusEmployeeID != -2 '.$whereStatus.' AND ( ';

            $sql.= ' em.NIP LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ' OR em.Name LIKE "%'.$requestData['search']['value'].'%" ';
            $sql.= ' OR ps.NameEng LIKE "'.$requestData['search']['value'].'%" ';
            $sql.= ') ORDER BY NIP,em.PositionMain  ASC';

        }
        else {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status, em.StatusEmployeeID
                        FROM db_employees.employees em 
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        LEFT JOIN db_employees.employees_status ems ON (ems.IDStatus = em.StatusEmployeeID) 
                        WHERE em.StatusEmployeeID != -2 '.$whereStatus;
            $sql.= 'ORDER BY NIP,em.PositionMain ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        }
        
        $query = $this->db->query($sql)->result_array();
        //$sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';
        $no = $requestData['start']+1;

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $jb = explode('.',$row["PositionMain"]);
            $Division = '';
            $Position = '';
            

            if(count($jb)>1){
                $dataDivision = $this->db->select('Division')->get_where('db_employees.division',array('ID'=>$jb[0]),1)->result_array()[0];
                $dataPosition = $this->db->select('Position')->get_where('db_employees.position',array('ID'=>$jb[1]),1)->result_array()[0];
                $Division = $dataDivision['Division'];
                $Position = $dataPosition['Position'];
            }

             $photo = (file_exists('./uploads/employees/'.$row["Photo"]) && $row["Photo"]!='' && $row["Photo"]!=null)
                ? base_url('uploads/employees/'.$row["Photo"])
                : base_url('images/icon/userfalse.png');

            $NIP = $row['NIP'];
            
            $StatusFiles = array();
            $Get_MasterFiles = $this->m_master->showData_array('db_employees.master_files');
            $StatusFiles = '';
            for ($j=0; $j < count($Get_MasterFiles); $j++) { 
                $stDefault =' <span class="label label-danger"> '.$Get_MasterFiles[$j]['TypeFiles'].'</span>';
                $sql2 = 'select count(*) as total, LinkFiles from db_employees.files where NIP = ? and TypeFiles = ?';
                $query2=$this->db->query($sql2, array($NIP,$Get_MasterFiles[$j]['ID']))->result_array();
                if ($query2[0]['total'] > 0 ) {

                    if($query2[0]['LinkFiles'] != null){
                        $stDefault =' <span class="label label-success"> '.$Get_MasterFiles[$j]['TypeFiles'].'</span>';
                    } else {
                        $stDefault =' <span class="label label-danger"> '.$Get_MasterFiles[$j]['TypeFiles'].'</span>';
                    }
                    
                } 
                $StatusFiles .= $stDefault;
            }

            $status = '-';
            if($row['StatusEmployeeID']==1){
                $status = '<i class="fa fa-circle" style="color: #4CAF50;"></i>';
            } else if($row['StatusEmployeeID']==2){
                $status = '<i class="fa fa-circle" style="color: #FF9800;"></i>';
            } else if($row['StatusEmployeeID']==3){
                $status = '<i class="fa fa-circle" style="color: #03A9F4;"></i>';
            } else if($row['StatusEmployeeID']==4){
                $status = '<i class="fa fa-circle" style="color: #9e9e9e;"></i>';
            } else if($row['StatusEmployeeID']==-1){
                $status = '<i class="fa fa-warning" style="color: #F44336;"></i>';
            }

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div style="text-align: center;"><img src="'.$photo.'" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="'.base_url('human-resources/academic-details/'.$row["NIP"]).'" style="font-weight: bold;">'.$row["Name"].'</a>';
            //$nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = $Division.' - '.$Position;
            $nestedData[] = '<div style="text-align: center;">'.$status.'</div>';
            $nestedData[] = $StatusFiles;
            
            $nestedData[] = '<a class="btn btn-info" href="'.base_url('human-resources/academic-details/'.$row["NIP"]).'">
  <i class="icon-list icon-large"></i> Detail</a>';

             $no++;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }


    public function getAllMK(){
        $data = $this->m_api->__getAllMK();
        return print_r(json_encode($data));
    }

    public function setLecturersAvailability(){

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if($data_arr['action']=='add'){
            $dataInsert = (array) $data_arr['dataForm'];
            $this->db->insert('db_academic.lecturers_availability',$dataInsert);
            return print_r($this->db->insert_id());
        } else if($data_arr['action']=='edit'){

            $update_lad = (array) $data_arr['dataForm_lad'];
            $this->db->where('ID', $data_arr['ladID']);
            $this->db->update('db_academic.lecturers_availability_detail',$update_lad);

            return print_r(1);
        } else if($data_arr['action']=='delete'){

            // Cek apakah ID lebih dari satu
            $dataCek = $this->m_api->__cekTotalLAD($data_arr['laID']);

            if(count($dataCek)==1){
//                print_r($data_arr['laID']);
                $this->db->where('ID', $data_arr['ladID']);
                $this->db->delete('db_academic.lecturers_availability_detail');

                $this->db->where('ID', $data_arr['laID']);
                $this->db->delete('db_academic.lecturers_availability');
//

            } else {
//                print_r('delete1');
                $this->db->where('ID', $data_arr['ladID']);
                $this->db->delete('db_academic.lecturers_availability_detail');
            }


            return print_r(1);

        }

    }

    public function setLecturersAvailabilityDetail($action){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = $this->jwt->decode($token,$key);

//        print_r($data_arr);
        if($action=='insert'){
            $this->db->insert('db_academic.lecturers_availability_detail',$data_arr);
            return $this->db->insert_id();
        }
    }

    public function changeTahunAkademik(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);


        $data['department'] = $this->session->userdata('departementNavigation');
        $data['dosen'] = $this->m_tahun_akademik->__getKetersediaanDosenByTahunAkademik($data_arr['ID']);
        print_r(json_encode($data['dosen']));
//        $this->load->view('page/'.$data['department'].'/ketersediaan_dosen_detail',$data);
    }


    //-------- Kurikulum -----
    public function insertKurikulum(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        // Cek Tahun
        $data = $this->m_api->cekTahunKurikulum($data_arr['Year']);
        if(count($data)>0){
            return print_r(0);
        } else {
            $this->db->insert('db_academic.curriculum',$data_arr);
            return print_r(1);
        }

    }

    public function geteducationLevel(){
        $data = $this->m_api->__geteducationLevel();
        return print_r(json_encode($data));
    }

    public function getDosenSelectOption(){
        $data = $this->m_api->__getDosenSelectOption();
        return print_r(json_encode($data));
    }

    public function crudKurikulum(){

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

//        print_r($data_arr);
//        exit;
        if($data_arr['action']=='add'){
            $insert = (array) $data_arr['data_insert'];
            $this->db->insert('db_academic.'.$data_arr['table'],$insert);
            $insert_id = $this->db->insert_id();
            return print_r($insert_id);
        } else if($data_arr['action']=='edit'){
            $dataupdate = (array) $data_arr['data_insert'];
            $this->db->where('ID', $data_arr['ID']);
            $this->db->update('db_academic.'.$data_arr['table'],$dataupdate);
            return print_r(1);
        } else if($data_arr['action']=='delete'){
            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_academic.'.$data_arr['table']);
            return print_r(1);
        } else if($data_arr['action']=='read'){
            $data = $this->m_api->__getItemKuriklum($data_arr['table']);
            return print_r(json_encode($data));
        }
    }

    public function crudDetailMK(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if($data_arr['action']=='add'){
            $insert = (array) $data_arr['dataForm'];

            // Cek apakah sudah dimasukan ke detail kurikulum

            $where = array(
                'CurriculumID' => $insert['CurriculumID'],
                'ProdiID' => $insert['ProdiID'],
//                'EducationLevelID' => $insert['EducationLevelID'],
                'MKID' => $insert['MKID']);
            $this->db->select('Semester');
            $dataSmt = $this->db->get_where('db_academic.curriculum_details', $where)->result_array();

            if(count($dataSmt)>0){
                $result = array(
                    'msg' => 0,
                    'Semester' => $dataSmt[0]['Semester']
                );
                return print_r(json_encode($result));

            } else {


                $this->db->insert('db_academic.curriculum_details',$insert);
                $insert_id = $this->db->insert_id();
                $result = array(
                    'msg' => $insert_id
                );
                return print_r(json_encode($result));
            }



        }
        else if($data_arr['action']=='edit'){
            $update = (array) $data_arr['dataForm'];
            $this->db->where('ID', $data_arr['ID']);
            $this->db->update('db_academic.curriculum_details',$update);
//            print_r($data_arr);

//            $this->db->where('CurriculumDetailID', $data_arr['ID']);
//            $this->db->delete('db_academic.precondition');

            $insert_id = $data_arr['ID'];
            return print_r($insert_id);
        }
        else if($data_arr['action']=='delete') {
            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_academic.curriculum_details');
            return print_r(1);
        }
    }

    public function getdetailKurikulum(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            $CDID = $data_arr['CDID'];
            $data = $this->m_api->__getdetailKurikulum($CDID);

            return print_r(json_encode($data));
        }

    }

    public function genrateMKCode(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            $ID = $data_arr['ID'];
            $data = $this->m_api->__genrateMKCode($ID);

            return print_r(json_encode($data));
        }

    }

    public function cekMKCode(){
        $MKCode = $this->input->post('MKCode');
        $data = $this->m_api->__cekMKCode($MKCode);
        return print_r(json_encode($data));
    }

    public function crudMataKuliah(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='add'){
                $dataInsert = (array) $data_arr['dataForm'];
                $this->db->insert('db_academic.mata_kuliah',$dataInsert);
                $insert_id = $this->db->insert_id();

                return print_r($insert_id);
            }
            else if($data_arr['action']=='edit')
            {
                $dataInsert = (array) $data_arr['dataForm'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.mata_kuliah',$dataInsert);

                return print_r(1);
            }
            else if($data_arr['action']=='delete')
            {
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.mata_kuliah');
                return print_r(1);
            }
            else if($data_arr['action']=='read'){
                $ID = $data_arr['ID'];
                $MKCode = $data_arr['MKCode'];
                $data = $this->m_api->getMataKuliahSingle($ID,$MKCode);

                if(count($data)>0){
                    return print_r(json_encode($data[0]));
                }
            }
            else if($data_arr['action']=='readOfferings') {
                $dataForm = (array) $data_arr['dataForm'];
                $data = $this->m_api->getMatakuliahOfferings($dataForm['SemesterID'],$dataForm['MKID'],$dataForm['MKCode']);

                return print_r(json_encode($data[0]));
            }
        }
    }

    public function crudTahunAkademik(){
//        $token = $this->input->post('token');
//        $key = "UAP)(*";
//        $data_arr = (array) $this->jwt->decode($token,$key);

        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){

            if($data_arr['action']=='add'){
                $dataForm = (array) $data_arr['dataForm'];
                // Cek
                $check = $this->db->get_where('db_academic.semester',array('Year'=>$dataForm['Year'],'Code'=>$dataForm['Code']))
                    ->result_array();

//                print_r($check);
//                exit;
                if(count($check)>0){
                    return print_r(0);
                } else {
                    $this->db->insert('db_academic.semester',$dataForm);
                    $insert_id = $this->db->insert_id();

                    $this->db->insert('db_academic.academic_years',
                        array('SemesterID' => $insert_id));

                    return print_r($insert_id);
                }

            }
            else if($data_arr['action']=='edit'){
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.semester',$dataForm);
                return print_r(1);
            }
            else if($data_arr['action']=='delete'){
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.semester');
                return print_r(1);
            }
            else if($data_arr['action']=='read'){

                $data = $this->db->order_by('ID', 'DESC')
                    ->get('db_academic.semester')
                    ->result_array();

                return print_r(json_encode($data));

            }

            else if($data_arr['action']=='addSemesterAntara'){
                $dataForm = (array) $data_arr['dataForm'];
                // Cek
                $check = $this->db->get_where('db_academic.semester_antara',array('Year'=>$dataForm['Year'],'Code'=>$dataForm['Code']))
                    ->result_array();

                if(count($check)>0){
                    return print_r(0);
                } else {
                    $this->db->insert('db_academic.semester_antara',$dataForm);
                    $insert_id = $this->db->insert_id();

//                    $this->db->insert('db_academic.academic_years',
//                        array('SemesterID' => $insert_id));

                    return print_r($insert_id);
                }
            }
            else if($data_arr['action']=='readSemesterAntara'){
                $data = $this->db
                    ->select('semester_antara.*')
                    ->join('db_academic.semester','semester.ID = semester_antara.SemesterID')
                    ->order_by('semester_antara.Year', 'DESC')
                    ->get('db_academic.semester_antara')
                    ->result_array();

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='checkSemesterAntara'){
                $data = $this->db
                    ->get_where('db_academic.semester_antara',array('Status'=>'1'))
                    ->result_array();
                return print_r(json_encode($data));
            }

            else if($data_arr['action']=='DataSemester'){


                $data = $this->m_api->getSemesterCurriculum($data_arr['SemesterID'],$data_arr['IsSemesterAntara']);

                return print_r(json_encode($data));

            }
        }

    }

    public function crudStatusStudents(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){

            if($data_arr['action']=='read'){
                $data = $this->db->order_by('ID', 'ASC')->get('db_academic.status_student')->result_array();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='resetPassword'){

                $dataUpdate = array(
                    'Password_Old' => md5($data_arr['NewPassword']),
                    'Status' => '-1'
                );
                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update('db_academic.auth_students', $dataUpdate);
                return print_r(1);
            }
            else if($data_arr['action']=='changeStatus'){

                // Cek apakah data NPM ada di auth_students
                $dataAuth = $this->db->get_where('db_academic.auth_students',array(
                    'NPM' => $data_arr['NPM']
                ),1)->result_array();


                $statusLogin = ($data_arr['StatusID']=='3') ? '1' : '0';
                if(count($dataAuth)>0){
                    $arrUpdate = array(
                        'StatusStudentID' => $data_arr['StatusID'],
                        'Status' => $statusLogin
                    );
                    $this->db->where('NPM', $data_arr['NPM']);
                    $this->db->update('db_academic.auth_students', $arrUpdate);
                } else {
                    $dataInsert = array(
                        'NPM' => $data_arr['NPM'],
                        'Year' => $data_arr['dataYear'],
                        'EmailPU' => $data_arr['EmailPU'],
                        'StatusStudentID' => $data_arr['StatusID'],
                        'Status' => $statusLogin
                    );
                    $this->db->insert('db_academic.auth_students',$dataInsert);
                }

                // Update di table students
                $da_ = "ta_".$data_arr['dataYear'];
                $arrUpdateStd = array(
                    'StatusStudentID' => $data_arr['StatusID']
                );
                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update($da_.'.students', $arrUpdateStd);

                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update('db_academic.auth_students', $arrUpdateStd);

                return print_r(1);


            }

        }
    }

    public function crudSpecialCaseKRS(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='insertSP_KRS'){
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->insert('db_academic.academic_years_sp_krs_1', $dataForm);
                return print_r(1);
            }
            else if($data_arr['action']=='readSP_KRS'){

                $data = $this->db->query('SELECT aysk.*, ps.NameEng AS ProdiEng, pg.Code AS ProdiGroup FROM db_academic.academic_years_sp_krs_1 aysk 
                                                          LEFT JOIN db_academic.program_study ps ON (ps.ID = aysk.ProdiID)
                                                          LEFT JOIN db_academic.prodi_group pg ON (pg.ID = aysk.ProdiGroupID)
                                                          WHERE aysk.SemesterID = "'.$data_arr['SemesterID'].'" 
                                                          ORDER BY aysk.ID ASC ')->result_array();

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='deleteSP_KRS'){
                $ID = $data_arr['ID'];
                $this->db->delete('db_academic.academic_years_sp_krs_1',
                    array('ID'=>$ID));
                return print_r(1);
            }
        }
    }

    public function crudProdiGroup(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='readProdiGroup'){
                $data = $this->db->order_by('Code','ASC')->get_where('db_academic.prodi_group',array('ProdiID'=>$data_arr['ProdiID']))
                    ->result_array();
                return print_r(json_encode($data));
            }
        }
    }

    public function crudDataDetailTahunAkademik(){

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

//        print_r($data_arr);
        if(count($data_arr)>0){
            if($data_arr['action']=='read'){

                $data = $this->m_api->__crudDataDetailTahunAkademik($data_arr['ID']);
                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='edit') {
                $SemesterID = $data_arr['SemesterID'];
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->where('SemesterID',$SemesterID);
                $this->db->update('db_academic.academic_years',$dataForm);

                $this->db->reset_query();

                // Cek apakah sudah ada atau belum jika belum maka insert
                $cekSet = $this->db->get_where('db_academic.attendance_setting',
                    array('SemesterID' => $SemesterID))->result_array();
                $dataFormAttd = (array) $data_arr['dataFormAttd'];
                if(count($cekSet)>0){
                    $this->db->where('SemesterID',$SemesterID);
                    $this->db->update('db_academic.attendance_setting',$dataFormAttd);
                } else {
                    $dataFormAttd['SemesterID'] = $SemesterID;
                    $this->db->insert('db_academic.attendance_setting',$dataFormAttd);
                }


                return print_r($data_arr['SemesterID']);
            }
            else if($data_arr['action']=='publish'){
                $ID = $data_arr['ID'];
                $this->db->query('UPDATE db_academic.semester s SET s.Status=IF(s.ID="'.$ID.'",1,0)');
                return print_r($ID);
            }
            else if($data_arr['action']=='schedule'){
                $SemesterID = $data_arr['SemesterID'];
                $NIP = $data_arr['NIP'];
                $data = $this->m_api->__getScheduleTeacher($SemesterID,$NIP);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='insertSC'){
                $dataForm = (array) $data_arr['dataForm'];

                $result = [];
                for($i=0;$i<count($dataForm);$i++){
                    $dataM = (array) $dataForm[$i];
                    $dataInsert = (array) $dataM['Details'];

                    $dataWhere = array(
                        'SemesterID' => $dataInsert['SemesterID'],
                        'AcademicDescID' => $dataInsert['AcademicDescID'],
                        'UserID' => $dataInsert['UserID'],
                        'DataID' => $dataInsert['DataID']
                    );

                    $dataCek = $this->db->get_where('db_academic.academic_years_special_case', $dataWhere,1)->result_array();

                    if(count($dataCek)>0){
                        $msg = array(
                            'Course' => $dataM['Course'],
                            'Msg' => 'Already Exists',
                            'Status' => 0
                        );
                    } else {
                        $this->db->insert('db_academic.academic_years_special_case', $dataInsert);
                        $insert_id = $this->db->insert_id();

                        $dataDetails = $this->db->query('SELECT s.ClassGroup,aysc.*,mk.NameEng, em.Name AS Lecturers FROM db_academic.academic_years_special_case aysc 
                                            LEFT JOIN db_academic.schedule s ON (s.ID=aysc.DataID)
                                            RIGHT JOIN db_academic.schedule_details_course sdc ON (s.ID = sdc.ScheduleID)
                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                            LEFT JOIN  db_employees.employees em ON (em.NIP=aysc.UserID)
                                            WHERE aysc.ID = "'.$insert_id.'" 
                                            GROUP BY sdc.ScheduleID')->result_array();

                        $msg = array(
                            'Details' => $dataDetails[0],
                            'Course' => $dataM['Course'],
                            'Msg' => 'Saved',
                            'Status' => 1
                        );
                    }

                    array_push($result,$msg);

                }

                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='dataSC'){
                $SemesterID = $data_arr['SemesterID'];
                $AcademicDescID = $data_arr['AcademicDescID'];
                $data = $this->m_api->__getSpecialCase($SemesterID,$AcademicDescID);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='deleteSC'){
                $id = $data_arr['ID'];
                $this->db->where('ID', $id);
                $this->db->delete('db_academic.academic_years_special_case');

                return print_r(1);
            }
            else if($data_arr['action']=='insertSCKRS'){
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->insert('db_academic.academic_years_special_case', $dataForm);
                return print_r(1);
            }
            else if ($data_arr['action']=='readSCKRS'){
                $SemesterID = $data_arr['SemesterID'];

                $data = $this->db->query('SELECT aysc.ID,ps.ID AS ProdiID,ps.Code, aysc.Start, aysc.End 
                                              FROM db_academic.academic_years_special_case aysc
                                              LEFT JOIN db_academic.program_study ps ON (ps.ID = aysc.UserID)
                                              WHERE aysc.SemesterID = "'.$SemesterID.'" 
                                              AND aysc.Status = "2" ')->result_array();


                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='deleteSCKRS'){
                $ID = $data_arr['ID'];
                $this->db->delete('db_academic.academic_years_special_case',
                    array('ID'=>$ID));
                return print_r(1);
            }
        }

    }

    public function getAcademicYearOnPublish(){

        $smt = $this->input->get('smt');

        if($smt=='SemesterAntara'){
            $data = $this->db
                ->get_where('db_academic.semester_antara',array('Status'=>'1'))
                ->result_array();
        } else {
            $data = $this->m_api->__getAcademicYearOnPublish();
        }

//        $dataSMT = $this->m_api->getSemesterCurriculum();

//        $data[0]['Semester'] = $dataSMT[0]['Semester'];


        return print_r(json_encode($data[0]));
    }

    public function crudSchedule(){

        $data_arr = $this->getInputToken();

//        print_r($data_arr);
        if(count($data_arr)>0){
            if($data_arr['action']=='add'){
                $formData = (array) $data_arr['formData'];

                // Scedule
                $insertSchedule = (array) $formData['schedule'];
                $this->db->insert('db_academic.schedule',$insertSchedule);
                $insert_id = $this->db->insert_id();

                // schedule_details
                $dataScheduleDetails = (array) $formData['schedule_details'];
                for($s=0;$s<count($dataScheduleDetails);$s++){
                    $arr = (array) $dataScheduleDetails[$s];
                    $arr['ScheduleID'] = $insert_id;
                    $this->db->insert('db_academic.schedule_details',$arr);
                    $insert_id_SD = $this->db->insert_id();

                    // Insert Attd
                    $dataInsetAttd = array(
                        'SemesterID' => $insertSchedule['SemesterID'],
                        'ScheduleID' => $insert_id,
                        'SDID' => $insert_id_SD
                    );

                    $this->db->insert('db_academic.attendance',$dataInsetAttd);
                }


                // schedule_details_course
                $dataScheduleDetailsCourse = (array) $formData['schedule_details_course'];
                for($sdc=0;$sdc<count($dataScheduleDetailsCourse);$sdc++){
                    $arr = (array) $dataScheduleDetailsCourse[$sdc];
                    $arr['ScheduleID'] = $insert_id;
                    $this->db->insert('db_academic.schedule_details_course',$arr);
                }


                //schedule_team_teaching
                if($insertSchedule['TeamTeaching']==1){
                    $dataTemaTeaching = (array) $formData['schedule_team_teaching'];
                    for($t=0;$t<count($dataTemaTeaching);$t++){
                        $arr = (array) $dataTemaTeaching[$t];
                        $arr['ScheduleID'] = $insert_id;

                        $this->db->insert('db_academic.schedule_team_teaching',$arr);
                    }
                }



                return print_r($insert_id);


            }
            else if($data_arr['action']=='read'){
                $dataWhere = (array) $data_arr['dataWhere'];

                $days = $this->db->get_where('db_academic.days',array('ID'=>$data_arr['DayID']),1)->result_array();


                $data[0]['Day'] = $days[0];
                $data[0]['Details'] = $this->m_api->getSchedule($data_arr['DayID'],$dataWhere);

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='readOneSchedule'){

                $data = $this->m_api->getOneSchedule($data_arr['ScheduleID']);

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='readProdiGroup'){
                $dataGroupProdi = $this->db->order_by('Code','ASC')
                    ->get_where('db_academic.prodi_group',
                        array('ProdiID'=>$data_arr['ProdiID']))->result_array();

                return print_r(json_encode($dataGroupProdi));
            }
            else if($data_arr['action']=='delete'){
                $ID = $data_arr['ScheduleID'];

                // Get Attendance
                $dataAttd = $this->db->get_where('db_academic.attendance',
                    array('ScheduleID' => $ID))->result_array();

                // Delete Attendance Students
                $this->db->delete('db_academic.attendance_students',array('ID_Attd' => $dataAttd[0]['ID']));

                // Delete Attendance
                $this->db->delete('db_academic.attendance',array('ScheduleID' => $ID));

                $tables = array('db_academic.schedule_details',
                    'db_academic.schedule_details_course', 'db_academic.schedule_team_teaching');
                $this->db->where('ScheduleID', $ID);
                $this->db->delete($tables);

                $this->db->reset_query();
                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.schedule');


                return print_r(1);
            }
            else if($data_arr['action']=='deleteSubSesi') {
                $ID = $data_arr['sdID'];

                $dataSD = $this->db->get_where('db_academic.schedule_details',
                    array('ID' => $ID),1)->result_array();

                $whereAttd = array(
                    'ScheduleID' => $dataSD[0]['ScheduleID'],
                    'SDID' => $ID
                );

                $dataAttd = $this->db->get_where('db_academic.attendance',$whereAttd,1)->result_array();

                $this->db->delete('db_academic.attendance_students',array('ID_Attd'=>$dataAttd[0]['ID']));
                $this->db->delete('db_academic.attendance', $whereAttd);

                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.schedule_details');

                // Update Subsesi jika tinggal 1
                $dataSubSesi = $this->db->get_where('db_academic.schedule_details',
                    array('ScheduleID'=>$dataSD[0]['ScheduleID']))->result_array();
                $SubSesi = (count($dataSubSesi)>1) ? '1' : '0';
                $this->db->set('SubSesi', $SubSesi);
                $this->db->where('ID', $dataSD[0]['ScheduleID']);
                $this->db->update('db_academic.schedule');

                return print_r(1);
            }
            else if($data_arr['action']=='edit'){

                $formData = (array) $data_arr['formData'];
                $schedule_details = (array) $formData['schedule_details'];

                // Update Schedule
                $ScheduleID = $data_arr['ID'];
                $ScheduleUpdate = (array) $formData['schedule'];
                $this->db->where('ID', $ScheduleID);
                $this->db->update('db_academic.schedule',$ScheduleUpdate);
                $this->db->reset_query();

                // Update Schedule Detail
                $dataScheduleDetailsArray = (array) $schedule_details['dataScheduleDetailsArray'];
                for($d=0;$d<count($dataScheduleDetailsArray);$d++){
                    $ds = (array) $dataScheduleDetailsArray[$d];
                    $this->db->where('ID', $ds['sdID']);
                    $this->db->update('db_academic.schedule_details',(array) $ds['update']);
                    $this->db->reset_query();
                }

                // Insert Schedule Detail
                $dataScheduleDetailsArrayNew = (array) $schedule_details['dataScheduleDetailsArrayNew'];
                for($d2=0;$d2<count($dataScheduleDetailsArrayNew);$d2++){

                    $dataNewSesi = (array) $dataScheduleDetailsArrayNew[$d2];

                    $this->db->insert('db_academic.schedule_details', $dataNewSesi);
                    $insert_id_SD = $this->db->insert_id();

                    // Get Schedule
                    $dataSch = $this->db->get_where('db_academic.schedule',
                        array('ID' => $dataNewSesi['ScheduleID']),1)->result_array();

                    // Insert Attd
                    $dataInsetAttd = array(
                        'SemesterID' => $dataSch[0]['SemesterID'],
                        'ScheduleID' => $dataNewSesi['ScheduleID'],
                        'SDID' => $insert_id_SD
                    );
                    $this->db->insert('db_academic.attendance',$dataInsetAttd);
                    $insert_id_attd = $this->db->insert_id();


                    // Cek Mahasiswa Yang Ngambil
                    $dataMhs = $this->m_api->getDataStudents_Schedule($dataSch[0]['SemesterID'],$dataNewSesi['ScheduleID']);

                    for($m=0;$m<count($dataMhs);$m++){
                        $data_attd_s = array(
                            'ID_Attd' => $insert_id_attd,
                            'NPM' => $dataMhs[$m]['NPM']
                        );
                        $this->db->insert('db_academic.attendance_students',$data_attd_s);
                    }

                    $this->db->reset_query();
                }

                $this->db->where('ScheduleID', $ScheduleID);
                $this->db->delete('db_academic.schedule_team_teaching');
                $this->db->reset_query();
                // Team Teaching
                if($ScheduleUpdate['TeamTeaching']==1){
                    $dataTemaTeaching = (array) $formData['schedule_team_teaching'];
                    for($t=0;$t<count($dataTemaTeaching['teamTeachingArray']);$t++){

                        $arr = (array) $dataTemaTeaching['teamTeachingArray'][$t];
                        $this->db->insert('db_academic.schedule_team_teaching',$arr);
                        $this->db->reset_query();

                    }
                }


                // Update Subsesi jika tinggal 1
                $dataSubSesi = $this->db->get_where('db_academic.schedule_details',
                    array('ScheduleID'=>$ScheduleID))->result_array();
                $SubSesi = (count($dataSubSesi)>1) ? '1' : '0';
                $this->db->set('SubSesi', $SubSesi);
                $this->db->where('ID', $ScheduleID);
                $this->db->update('db_academic.schedule');

                return print_r(1);

            }
            else if($data_arr['action']=='readDetail') {

                $data = $this->m_api->getScheduleDetails($data_arr['ScheduleID']);

                return print_r(json_encode($data));
            }

            // Cek Group
            else if($data_arr['action']=='checkGroup'){
//                $dataG = $this->db->get_where('',
//                        array('ClassGroup' => ))->result_array();

                $dataG = $this->db->query('SELECT s.ID FROM db_academic.schedule s 
                                                WHERE s.ClassGroup LIKE "'.$data_arr['Group'].'" 
                                                AND s.SemesterID = "'.$data_arr['SemesterID'].'" ')
                    ->result_array();
                return print_r(json_encode($dataG));
            }

            else if($data_arr['action']=='getDataStudents'){

                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                // sp => Study_planning
                if($data_arr['Flag']=='sp'){
                    $data = $this->m_api->getStudentByScheduleID($SemesterID,$ScheduleID,$data_arr['CDID']);
                } else if($data_arr['Flag']=='std'){
                    $data = $this->m_api->getTotalStdNotYetApprovePerDay($SemesterID,$ScheduleID,$data_arr['CDID']);
                }

                return print_r(json_encode($data));
            }

            else if($data_arr['action']=='updateStudyPlanningResignStatus'){

                $ShowTranscript = ($data_arr['StatusResign']==0 || $data_arr['StatusResign']=='0') ? '1' : '0';

                $arrUpdate = array(
                    'StatusResign' => $data_arr['StatusResign'],
                    'ShowTranscript' => $ShowTranscript
                );

                $this->db->where('ID', $data_arr['SPID']);
                $this->db->update($data_arr['DB_Student'].'.study_planning',$arrUpdate);

                return print_r(1);
            }

            else if($data_arr['action']=='getClassGroup'){
                $data = $this->db->query('SELECT s.ID AS ScheduleID,s.ClassGroup, em.Name FROM db_academic.schedule s 
                                                  LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                  WHERE s.SemesterID = "'.$data_arr['SemesterID'].'"
                                                   ORDER BY s.ClassGroup ASC ')->result_array();

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='checkStudentToDelete'){
                $ScheduleID = $data_arr['ScheduleID'];
                $SemesterID = $data_arr['SemesterID'];
                $dataApprove = $this->m_api->__getStudentByScheduleIDApproved($SemesterID,$ScheduleID);
                $dataPlan = $this->m_api->__getStudentByScheduleIDInStudyPlanning($SemesterID,$ScheduleID);

                $result = array(
                    'Approve' => $dataApprove,
                    'Plan' => $dataPlan
                );

                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='deleteTimettables'){
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $arrWhere = array('SemesterID'=>$SemesterID,'ScheduleID'=>$ScheduleID);
                $this->db->where($arrWhere);
                $this->db->delete('db_academic.std_krs');
                $this->db->reset_query();

                // == Approved ==
                $dataCL = $this->m_api->getClassOf();

                for($c=0;$c<count($dataCL);$c++){
                    $d = $dataCL[$c];
                    $db_ = 'ta_'.$d['Year'];

                    // Cek DB Exist
                    $dbExist = $this->db->query('SELECT SCHEMA_NAME 
                                                    FROM INFORMATION_SCHEMA.SCHEMATA 
                                                    WHERE SCHEMA_NAME = "'.$db_.'" ')->result_array();

                    if(count($dbExist)>0){
                        $this->db->where($arrWhere);
                        $this->db->delete($db_.'.study_planning');
                        $this->db->reset_query();
                    }

                }

                // Delete Schedulenya
                $this->db->where(array('ID' => $ScheduleID, 'SemesterID' => $SemesterID));
                $this->db->delete('db_academic.schedule');
                $this->db->reset_query();

                $tables = array('db_academic.schedule_details', 'db_academic.schedule_details_course',
                    'db_academic.schedule_material','db_academic.schedule_team_teaching');
                $this->db->where('ScheduleID', $ScheduleID);
                $this->db->delete($tables);
                $this->db->reset_query();

                // Delete Attendance
                $dataAttd = $this->db->query('SELECT attd.ID FROM db_academic.attendance attd 
                                                            WHERE attd.SemesterID = "'.$SemesterID.'" 
                                                            AND attd.ScheduleID = "'.$ScheduleID.'" ')->result_array();

                if(count($dataAttd)>0){
                    for($i=0;$i<count($dataAttd);$i++){
                        // Delete Attendance Students
                        $this->db->where('ID_Attd',$dataAttd[$i]['ID']);
                        $this->db->delete('db_academic.attendance_students');
                        $this->db->reset_query();
                    }
                }

                return print_r(1);
            }
            else if($data_arr['action']=='loadEditCourse'){
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $data = $this->db->query('SELECT sdc.ID AS SDCID, sdc.CDID, mk.MKCode, mk.NameEng AS MKNameEng, mk.Name AS MKName, cd.Semester, ps.Name AS Prodi, 
                                                    co.Semester AS Offerto, sdc.ProdiGroupID, pg.Code AS ProdiGroup, crr.Year
                                                    FROM db_academic.schedule_details_course sdc
                                                    LEFT JOIN db_academic.prodi_group pg ON (pg.ID = sdc.ProdiGroupID)
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                                    LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                                    LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                    LEFT JOIN db_academic.course_offerings co ON (co.SemesterID = s.SemesterID AND co.ProdiID = sdc.ProdiID 
                                                    AND co.CurriculumID = cd.CurriculumID)
                                                    LEFT JOIN db_academic.curriculum crr ON (crr.ID = cd.CurriculumID)
                                                    WHERE sdc.ScheduleID = "'.$ScheduleID.'" AND s.SemesterID = "'.$SemesterID.'"
                                                    ORDER BY sdc.ProdiID, cd.Semester ASC ')->result_array();

                if(count($data)>0){
                    for($i=0;$i<count($data);$i++){
                        $dataApprove = $this->m_api->__getStudentByScheduleIDApproved_details($SemesterID,$ScheduleID,$data[$i]['CDID']);
                        $dataPlan = $this->m_api->__getStudentByScheduleIDInStudyPlanning_details($SemesterID,$ScheduleID,$data[$i]['CDID']);

                        $data[$i]['TotalStd_Approve'] = $dataApprove;
                        $data[$i]['TotalStd_Plan'] = $dataPlan;
                    }
                }

                $result = array(
                    'ScheduleDetails' => $data
                );
                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='loadEditCourseSchedule'){
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $dataProgram = $this->db->query('SELECT s.ID AS ScheduleID, s.ProgramsCampusID, sem.Name AS SemesterName, 
                                                              s.ClassGroup, s.Coordinator, s.TeamTeaching,
                                                             s.SemesterID, s.Attendance , mk.NameEng AS CourseEng, cd.TotalSKS AS TotalCredit
                                                            FROM db_academic.schedule s
                                                            LEFT JOIN db_academic.semester sem ON (sem.ID = s.SemesterID)
                                                            LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                            WHERE s.ID = "'.$ScheduleID.'" AND SemesterID = "'.$SemesterID.'"
                                                             GROUP BY s.ID ')
                    ->result_array();


                if(count($dataProgram)>0){
                    $detailTeamTeaching = [];
                    $dataTTC = $this->db->select('NIP')->get_where('db_academic.schedule_team_teaching',array('ScheduleID'=>$dataProgram[0]['ScheduleID']))
                        ->result_array();
                    if(count($dataTTC)>0){
                        foreach ($dataTTC as $item){
                            array_push($detailTeamTeaching,$item['NIP']);
                        }
                    }
                    $dataProgram[0]['detailTeamTeaching'] = $detailTeamTeaching;

                }

                $result = array(
                    'Schedule' => $dataProgram
                );
                return print_r(json_encode($result));
            }
            else if($data_arr['action']=='checktoAddNewCourse'){
                $dataWhere = (array) $data_arr['dataWhere'];
                $data = $this->db->get_where('db_academic.schedule_details_course',$dataWhere)->result_array();

                if(count($data)>0){
                    $result = array(
                        'Status' => '0'
                    );
                } else {
                    // Insert
                    $this->db->insert('db_academic.schedule_details_course',$dataWhere);

                    // Update Action
                    $UpdateLog = (array) $data_arr['UpdateLog'];
                    $this->db->where('ID', $dataWhere['ScheduleID']);
                    $this->db->update('db_academic.schedule',$UpdateLog);

                    $result = array(
                        'Status' => '1'
                    );
                }

                return print_r(json_encode($result));
            }
            else if($data_arr['action']=='deleteInEditCourse'){
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $SDCID = $data_arr['SDCID'];

                // GET CDID
                $dataCDID = $this->db->select('CDID')->get_where('db_academic.schedule_details_course',array('ID'=>$SDCID),1)->result_array()[0];

                // Delete Planning
                $whereDelete = array('SemesterID'=>$SemesterID,'ScheduleID'=>$ScheduleID,'CDID'=>$dataCDID['CDID']);
                $this->db->where($whereDelete);
                $this->db->delete('db_academic.std_krs');
                $this->db->reset_query();

                // Delete KRS yang sudah approve
                $dataCL = $this->m_api->getClassOf();
                foreach ($dataCL AS $itm){
                    $db_ = 'ta_'.$itm['Year'];
                    // Cek DB Exist
                    $dbExist = $this->db->query('SELECT SCHEMA_NAME 
                                                    FROM INFORMATION_SCHEMA.SCHEMATA 
                                                    WHERE SCHEMA_NAME = "'.$db_.'" ')->result_array();
                    if(count($dbExist)>0){
                        $this->db->where($whereDelete);
                        $this->db->delete($db_.'.study_planning');
                        $this->db->reset_query();
                    }
                }

                // Delete
                $this->db->where('ID',$SDCID);
                $this->db->delete('db_academic.schedule_details_course');
                $this->db->reset_query();

                // Delete Attendance
                $dataAttd = $this->db->query('SELECT attd.ID FROM db_academic.attendance attd 
                                                            WHERE attd.SemesterID = "'.$SemesterID.'" 
                                                            AND attd.ScheduleID = "'.$ScheduleID.'" ')->result_array();

                if(count($dataAttd)>0){
                    for($i=0;$i<count($dataAttd);$i++){
                        // Delete Attendance Students
                        $this->db->where('ID_Attd',$dataAttd[$i]['ID']);
                        $this->db->delete('db_academic.attendance_students');
                        $this->db->reset_query();
                    }
                }

                // Update Action
                $UpdateLog = (array) $data_arr['UpdateLog'];
                $this->db->where('ID', $data_arr['ScheduleID']);
                $this->db->update('db_academic.schedule',$UpdateLog);

                return print_r(1);

            }
            else if($data_arr['action']=='updateInfoInEditCourse'){

                // Cek apakah Class Group Sudah digunakan
                $UpdateForm = (array) $data_arr['UpdateForm'];
                $dataG = $this->db->query('SELECT ID FROM db_academic.schedule s 
                                                      WHERE s.SemesterID = "'.$data_arr['SemesterID'].'" 
                                                      AND s.ClassGroup LIKE "'.$UpdateForm['ClassGroup'].'"
                                                       AND s.ID != "'.$data_arr['ScheduleID'].'" ')->result_array();

                if(count($dataG)>0){
                    $result = array(
                        'Status' => 0
                    );
                } else {

                    // Update Schedule
                    $this->db->where('ID', $data_arr['ScheduleID']);
                    $this->db->update('db_academic.schedule',$UpdateForm);
                    $this->db->reset_query();

                    // Update Untuk Team Teachingnya
                    // -- Delete Team --
                    $this->db->where('ScheduleID',$data_arr['ScheduleID']);
                    $this->db->delete('db_academic.schedule_team_teaching');
                    $this->db->reset_query();

                    // -- Insert yg baru --
                    for ($i=0;$i<count($data_arr['dataTeamTeaching']);$i++){
                        $f_i = (array) $data_arr['dataTeamTeaching'][$i];
                        $this->db->insert('db_academic.schedule_team_teaching',$f_i);
                    }

                    // Update Action
                    $UpdateLog = (array) $data_arr['UpdateLog'];
                    $this->db->where('ID', $data_arr['ScheduleID']);
                    $this->db->update('db_academic.schedule',$UpdateLog);

                    $result = array(
                        'Status' => 1
                    );
                }

                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='loadEditSchedule'){
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $dataSchedule = $this->db->query('SELECT sd.ClassroomID, sd.Credit, sd.DayID, sd.EndSessions,  
                                                              sd.StartSessions, sd.TimePerCredit, sd.ID AS SDID, cl.Room,
                                                              d.NameEng AS DayEng, s.ClassGroup
                                                              FROM db_academic.schedule_details sd
                                                              LEFT JOIN db_academic.schedule s ON (s.ID = sd.ScheduleID)
                                                              LEFT JOIN db_academic.days d ON (d.ID=sd.DayID)
                                                              LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                              WHERE s.ID = "'.$ScheduleID.'" 
                                                              AND s.SemesterID = "'.$SemesterID.'"
                                                              ORDER BY d.ID ASC ')->result_array();

                $result = array(
                    'dataSchedule' => $dataSchedule
                );
                return print_r(json_encode($result));
            }
            else if($data_arr['action']=='updateCourse_Edit'){

                $arrUpdate = (array) $data_arr['formInsert'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.schedule_details',$arrUpdate);
                $this->db->reset_query();

                // Update Action
                $UpdateLog = (array) $data_arr['UpdateLog'];
                $this->db->where('ID', $data_arr['ScheduleID']);
                $this->db->update('db_academic.schedule',$UpdateLog);

                return print_r(1);

            } else if($data_arr['action']=='updateCourse_Add'){
                $formInsert = (array) $data_arr['formInsert'];

                // Add TO schedule_details
                $formInsert['ScheduleID'] = $data_arr['ScheduleID'];
                $this->db->insert('db_academic.schedule_details',$formInsert);
                $insert_id = $this->db->insert_id();

                // Add to attendance
                $dataInsertAttd = array(
                    'SemesterID' => $data_arr['SemesterID'],
                    'ScheduleID' => $data_arr['ScheduleID'],
                    'SDID' => $insert_id
                );
                $this->db->insert('db_academic.attendance',$dataInsertAttd);
                $insert_id_attdID = $this->db->insert_id();

                // Cek student yang sudah ngambil siapa aja lalu insert semua
                // Get Student
                $data_attd = $this->db->select('ID')->get_where('db_academic.attendance',array(
                        'SemesterID' => $data_arr['SemesterID'],
                        'ScheduleID' => $data_arr['ScheduleID'])
                    ,1)->result_array();

                $dataStudnet = $this->db->select('NPM')->get_where('db_academic.attendance_students',array('ID_Attd'=> $data_attd[0]['ID']))->result_array();

                // insert student to attendance
                if(count($dataStudnet)>0){
                    foreach ($dataStudnet as $itemS){
                        $arrIns = array(
                            'ID_Attd' => $insert_id_attdID,
                            'NPM' => $itemS['NPM']
                        );
                        $this->db->insert('db_academic.attendance_students',$arrIns);
                    }
                }


                // Update Action
                $UpdateLog = (array) $data_arr['UpdateLog'];
                $this->db->where('ID', $data_arr['ScheduleID']);
                $this->db->update('db_academic.schedule',$UpdateLog);

                return print_r(1);

            } else if($data_arr['action']=='deleteScheduleCourse'){

                // Get ID Attendance
                $data_attd = $this->db->select('ID')->get_where('db_academic.attendance',array(
                        'SemesterID' => $data_arr['SemesterID'],
                        'ScheduleID' => $data_arr['ScheduleID'],
                        'SDID' => $data_arr['SDID']
                    )
                    ,1)->result_array();

                $this->db->where('ID_Attd', $data_attd[0]['ID']);
                $this->db->delete('db_academic.attendance_students');
                $this->db->reset_query();

                $this->db->where('ID', $data_attd[0]['ID']);
                $this->db->delete('db_academic.attendance');
                $this->db->reset_query();

                $this->db->where('ID', $data_arr['SDID']);
                $this->db->delete('db_academic.schedule_details');
                $this->db->reset_query();

                // Update Action
                $UpdateLog = (array) $data_arr['UpdateLog'];
                $this->db->where('ID', $data_arr['ScheduleID']);
                $this->db->update('db_academic.schedule',$UpdateLog);

                return print_r(1);
            }
        }
    }

    public function getSchedulePerDay(){
        $requestData= $_REQUEST;

        $token = $this->input->get('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $dataWhere = (array) $data_arr['dataWhere'];

        if( !empty($requestData['search']['value']) ) {
            $sql = $this->m_api->getSchedulePerDaySearch($data_arr['DayID'],$dataWhere,$requestData['search']['value']);
            $query = $this->db->query($sql)->result_array();
            $totalData = $query;
        }
        else {
            $totalData = $this->m_api->getTotalPerDay($data_arr['DayID'],$dataWhere);
            $sql = $this->m_api->getSchedulePerDayLimit($data_arr['DayID'],$dataWhere,$requestData['start'],$requestData['length']);

            $query = $this->db->query($sql)->result_array();
        }



        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            // Mendapatkan Jumlah Students
            $Students = $this->m_api->getTotalStdPerDay($row['SemesterID'],$row['ID'],$row['CDID']);
            $total_std_KRS = $this->m_api->getTotalStdNotYetApprovePerDay($row['SemesterID'],$row['ID'],$row['CDID']);

            $StudentsNY = count($total_std_KRS);

            $btnDelMK = ($Students>0) ? 1 : 0;
            // Group Kelas
            $groupClass = '<b><a href="javascript:void(0)" class="btn-action" data-page="editjadwal" data-btndel="'.$btnDelMK.'" data-id="'.$row['ID'].'">'.$row["ClassGroup"].'</a></b>';
            $sbSesi = ($row['SubSesi']=='1' || $row['SubSesi']==1) ? '<br/><span class="label label-warning">Sub-Sesi</span>' : '';

            $TeamTeaching = '';
            if($row["TeamTeaching"]==1){
                $TeamTeaching = $this->m_api->getTeamTeachingPerDay($row['ID']);
            }

            $coor = '<div style="color: #427b44;margin-bottom: 10px;"><b>'.$row["Lecturer"].'</b></div>';

            // Mendapatkan matakuliah
            $courses = $this->m_api->getCoursesPerDay($row['ID']);

            $nestedData[] = '<div style="text-align:center;">'.$groupClass.''.$sbSesi.'</div>';
            $nestedData[] = $courses;
            $nestedData[] = '<div style="text-align:center;">'.$row["Credit"].'</div>';
            $nestedData[] = $coor.''.$TeamTeaching;
            $nestedData[] = '<div style="text-align:center;"><a href="javascript:void(0)" 
                                class="btn-sw-std" data-smtid="'.$row['SemesterID'].'" 
                                data-scheduleid="'.$row['ID'].'" data-flag="sp" 
                                data-cdid="'.$row['CDID'].'">'.$Students.'</a> of 
                                <a href="javascript:void(0)" class="btn-sw-std" data-smtid="'.$row['SemesterID'].'" 
                                data-scheduleid="'.$row['ID'].'" data-flag="std" 
                                data-cdid="'.$row['CDID'].'">'.$StudentsNY.'</a></div>';

            $nestedData[] = '<div style="text-align:center;">'.substr($row["StartSessions"],0,5).' - '.substr($row["EndSessions"],0,5).'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$row["Room"].'</div>';

            $data[] = $nestedData;
        }


        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getSchedulePerSemester(){
        $requestData= $_REQUEST;

        $token = $this->input->get('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $dataWhere = (array) $data_arr['dataWhere'];

        if( !empty($requestData['search']['value']) ) {
            $query = $this->m_api->getTotalPerSemesterSearch($dataWhere,$requestData['search']['value']);
//            $query = $this->db->query($sql)->result_array();
            $totalData = $query;
        } else {
            $totalData = $this->m_api->getTotalPerSemester($dataWhere);
            $query = $this->m_api->getTotalPerSemesterLimit($dataWhere,$requestData['start'],$requestData['length']);

        }


        $data = array();
        $no = 1;
        for($i=0;$i<count($query);$i++){

            $nestedData=array();
            $row = $query[$i];

            $Students = $this->m_api->getTotalStdPerDay($row['SemesterID'],$row['ID'],$row['CDID']);
            $total_std_KRS = $this->m_api->getTotalStdNotYetApprovePerDay($row['SemesterID'],$row['ID'],$row['CDID']);

            $StudentsNY = count($total_std_KRS);

            // Group Kelas
            $groupClass = '<b><a href="javascript:void(0)" class="btn-action" data-page="editjadwal" data-id="'.$row['ID'].'">'.$row["ClassGroup"].'</a></b>';
            $sbSesi = ($row['SubSesi']=='1' || $row['SubSesi']==1) ? '<br/><span class="label label-warning">Sub-Sesi</span>' : '';

            $TeamTeaching = '';
            if($row["TeamTeaching"]==1){
                $TeamTeaching = $this->m_api->getTeamTeachingPerDay($row['ID']);
            }

            $coor = '<div style="color: #427b44;margin-bottom: 10px;"><b>'.$row["Lecturer"].'</b></div>';

            // Mendapatkan matakuliah
            $courses = $this->m_api->getCoursesPerDay($row['ID']);

            // Mendapatkan Jumlah Students
//            $Students = $this->m_api->getTotalStdPerDay($row['SemesterID'],$row['ID']);


//            $nestedData[] = '<div style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$groupClass.''.$sbSesi.'</div>';
            $nestedData[] = $courses;
            $nestedData[] = '<div style="text-align:center;">'.$row["Credit"].'</div>';
            $nestedData[] = $coor.''.$TeamTeaching;
            $nestedData[] = '<div style="text-align:center;"><a href="javascript:void(0)" class="btn-sw-std" data-smtid="'.$row['SemesterID'].'" data-scheduleid="'.$row['ID'].'" data-flag="sp" data-cdid="'.$row['CDID'].'">'.$Students.'</a> of <a href="javascript:void(0)" class="btn-sw-std" data-smtid="'.$row['SemesterID'].'" data-scheduleid="'.$row['ID'].'" data-flag="std" data-cdid="'.$row['CDID'].'">'.$StudentsNY.'</a></div>';
            $nestedData[] = '<div style="text-align:center;"><b>'.$row['DayEng'].'</b><br/>'.substr($row["StartSessions"],0,5).' - '.substr($row["EndSessions"],0,5).'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$row["Room"].'</div>';

            $no++;
            $data[] = $nestedData;

        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval( count($totalData) ),
            "data"            => $data
        );
        echo json_encode($json_data);

    }

    public function getScheduleExam(){
        $requestData= $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $whereP = ($data_arr['ExamDate']!=null && $data_arr['ExamDate']!='')
            ? 'ex.SemesterID = "'.$data_arr['SemesterID'].'" AND ex.Type LIKE "'.$data_arr['Type'].'" AND ex.Status = "1" AND ex.ExamDate LIKE "'.$data_arr['ExamDate'].'" '
            : 'ex.SemesterID = "'.$data_arr['SemesterID'].'" AND ex.Type LIKE "'.$data_arr['Type'].'" AND ex.Status = "1"' ;

        $orderBy = ' ORDER BY ex.ExamDate, ex.ExamStart, ex.ExamEnd ASC ';
        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {

            $search = $requestData['search']['value'];
            $dataSearch = ' AND
                                 (d.NameEng LIKE "%'.$search.'%" OR cl.Room LIKE "%'.$search.'%" 
                                 OR p1.Name LIKE "%'.$search.'%" OR p2.Name LIKE "%'.$search.'%"
                                 OR p1.NIP LIKE "%'.$search.'%" OR p2.NIP LIKE "%'.$search.'%" 
                                 ) ';
        }

        $queryDefault = 'SELECT ex.ID, ex.ExamDate, ex.ExamStart, ex.ExamEnd, cl.Room, p1.Name AS P_Name1, p2.Name AS P_Name2,
                                p1.NIP AS P_NIP1, p2.NIP AS P_NIP2, em.Name AS InsertByName, ex.InsertAt
                                FROM db_academic.exam ex
                                LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                LEFT JOIN db_employees.employees p1 ON (p1.NIP = ex.Pengawas1)
                                LEFT JOIN db_employees.employees p2 ON (p2.NIP = ex.Pengawas2)
                                LEFT JOIN db_employees.employees em ON (em.NIP = ex.InsertBy)
                                LEFT JOIN db_academic.days d ON (d.ID = ex.DayID)
                                WHERE ( '.$whereP.' ) '.$dataSearch.' '.$orderBy;

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';



        $dataTable = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        if(count($dataTable)>0){
            for($i=0;$i<count($dataTable);$i++){
                $dataC = $this->db->query('SELECT exg.ID, exg.ScheduleID, s.ClassGroup, mk.NameEng AS CourseEng, mk.MKCode, em.Name AS Coordinator  
                                                        FROM db_academic.exam_group exg
                                                        LEFT JOIN db_academic.schedule s ON (s.ID = exg.ScheduleID)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                        lEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        WHERE exg.ExamID = "'.$dataTable[$i]['ID'].'" 
                                                        GROUP BY exg.ScheduleID ORDER BY s.ClassGroup ASC')->result_array();

                if(count($dataC)>0){
                    for($s=0;$s<count($dataC);$s++){
                        $dataSt = $this->db->query('SELECT NPM,DB_Students FROM db_academic.exam_details exd 
                                                              WHERE exd.ExamID = "'.$dataTable[$i]['ID'].'"
                                                               AND exd.ExamGroupID = "'.$dataC[$s]['ID'].'"
                                                               AND exd.ScheduleID = "'.$dataC[$s]['ScheduleID'].'"
                                                                ')->result_array();

                        // Details Students
                        if(count($dataSt)>0){
                            for ($ss=0;$ss<count($dataSt);$ss++){
                                $ddst = $this->db->select('Name')->get_where($dataSt[$ss]['DB_Students'].'.students',
                                    array('NPM' => $dataSt[$ss]['NPM']),1)->result_array();
                                $dataSt[$ss]['Name'] = $ddst[0]['Name'];
                            }
                        }
                        $dataC[$s]['DetailsStudent'] = $dataSt;
                    }
                }

                $dataTable[$i]['Course'] = $dataC;
            }
        }


        $query = $dataTable;

        $no = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $p = ($row['P_Name2']!='' && $row['P_Name2']!=null)
                ? ' - '.$row['P_NIP1'].' | '.$row['P_Name1'].'<br/> - '.$row['P_NIP2'].' | '.$row['P_Name2']
                : ' - '.$row['P_NIP1'].' | '.$row['P_Name1'] ;

            $course = '';
            $totalStudent = 0;
            for($c=0;$c<count($row['Course']);$c++){
                $d = $row['Course'][$c];
                $totalStudent = $totalStudent + count($d['DetailsStudent']);
                $course = $course.' <div style="margin-top: 1px;"><b>'.$d['ClassGroup'].' | '.$d['CourseEng'].'</b><br/><p style="color: #2196F3;font-size: 11px;">(Co) '.$d['Coordinator'].'</p></div> ';
            }


            $exam_date = date("l, d M Y", strtotime($row['ExamDate']));
            $exam_time = substr($row['ExamStart'],0,5).' - '.substr($row['ExamEnd'],0,5);
            $exam_room = $row['Room'];
            $data_token_soal_jawaban = array(
                'Semester' => strtoupper($data_arr['Semester']),
                'Exam' => strtoupper($data_arr['Type']),
                'Date' => $exam_date,
                'Time' => $exam_time,
                'Room' => $exam_room,
                'Pengawas_1' => $row['P_NIP1'].' - '.$row['P_Name1'],
                'Pengawas_2' => ($row['P_Name2']!='' && $row['P_Name2']!=null) ? $row['P_NIP2'].' - '.$row['P_Name2'] : '-',
                'Course' => $row['Course']

            );

            $tkn_soal_jawaban = $this->jwt->encode($data_token_soal_jawaban,'UAP)(*');

            $data_token_attendance_std = array(
                'Exam' => array(
                    'Semester' => strtoupper($data_arr['Semester']),
                    'Exam' => strtoupper($data_arr['Type']),
                    'Date' => $exam_date,
                    'Time' => $exam_time,
                    'Room' => $exam_room,
                ),
                'Course' => $row['Course']
            );
            $tkn_attendance_std = $this->jwt->encode($data_token_attendance_std,'UAP)(*');


//            <li><a class="btnSave2PDF_Exam" href="javascript:void(0);" data-url="save2pdf/attendance-list" data-token="'.$tkn_attendance_std.'">Exam Attendance</a></li>
//                    <li><a target="_blank" href="'.base_url('save2pdf/news-event').'">Berita Acara</a></li>
            $act = '<div  style="text-align:center;"><div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="'.base_url('academic/exam-schedule/edit-exam-schedule/'.$row['ID']).'">Edit</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a target="_blank" href="'.base_url('save2pdf/exam-layout/'.$row['ID']).'">Layout</a></li>
                    <li><a class="btnSave2PDF_Exam" href="javascript:void(0);" data-url="save2pdf/draft_questions_answer_sheet" data-token="'.$tkn_soal_jawaban.'">Draft Questions  & Answer Sheet</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a class="btnDeleteExam" data-id="'.$row['ID'].'" href="javascript:void(0);" style="color: red;">Delete</a></li>
                  </ul>
                </div>
                </div>';

            $dateInsert = ($row['InsertAt']!='' && $row['InsertAt']!=null) ? date('l, d M Y h:i',strtotime($row['InsertAt'])) : '-' ;

            $nestedData[] = '<div style="text-align:center;">'.($no++).'</div>';
            $nestedData[] = $course;
            $nestedData[] = $p;
            $nestedData[] = '<div style="text-align:center;"><a href="javascript:void(0);" class="btnShowDetailStdExam" data-examid="'.$row['ID'].'">'.$totalStudent.'</a></div>';
            $nestedData[] = $act;
            $nestedData[] = '<div  style="text-align:center;">'.$exam_date.'<br/>'.$exam_time.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$exam_room.'</div>';
            $nestedData[] = '<b><i class="fa fa-user margin-right"></i> '.$row['InsertByName'].'</b>
                                <br/><span style="font-size: 11px;color: #9e9e9e;">'.$dateInsert.'</span>';

            $data[] = $nestedData;
        }


        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval( count($queryDefaultRow) ),
            "data"            => $data
        );
        echo json_encode($json_data);


    }

    public function getScheduleExamWaitingApproval(){
        $requestData= $_REQUEST;

        $token = $this->input->get('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $whereP = (isset($data_arr['ProdiID']) && $data_arr['ProdiID']!='' && $data_arr['ProdiID']!=null)
            ? ' ex.SemesterID = "'.$data_arr['SemesterID'].'" AND ex.Type LIKE "'.$data_arr['Type'].'" 
            AND ex.Status = "0" AND ex.InsertByProdiID = "'.$data_arr['ProdiID'].'" '
            : ' ex.SemesterID = "'.$data_arr['SemesterID'].'" AND ex.Type LIKE "'.$data_arr['Type'].'" AND ex.Status = "0" ';

        if( !empty($requestData['search']['value']) ) {

            $search = $requestData['search']['value'];
            $sql = 'SELECT ex.ID, ex.ExamDate, ex.ExamStart, ex.ExamEnd, cl.Room , p1.Name AS P_Name1, p2.Name AS P_Name2,
                                em.Name AS NameInsertBy
                                FROM db_academic.exam ex
                                LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                LEFT JOIN db_academic.days d On (d.ID = ex.DayID)
                                LEFT JOIN db_employees.employees p1 ON (p1.NIP = ex.Pengawas1)
                                LEFT JOIN db_employees.employees p2 ON (p2.NIP = ex.Pengawas2)
                                LEFT JOIN db_employees.employees em ON (em.NIP = ex.InsertBy)
                                WHERE ( '.$whereP.' ) AND
                                 (d.NameEng LIKE "%'.$search.'%" OR cl.Room LIKE "%'.$search.'%" 
                                 OR p1.Name LIKE "%'.$search.'%" OR p2.Name LIKE "%'.$search.'%"
                                 OR p1.NIP LIKE "%'.$search.'%" OR p2.NIP LIKE "%'.$search.'%" 
                                 ) ORDER BY ex.ExamDate, ex.ExamStart, ex.ExamEnd ASC ';
        }
        else {

            $sql = 'SELECT ex.ID, ex.ExamDate, ex.ExamStart, ex.ExamEnd, cl.Room, p1.Name AS P_Name1, p2.Name AS P_Name2,
                                p1.NIP AS P_NIP1, p2.NIP AS P_NIP2, em.Name AS NameInsertBy
                                FROM db_academic.exam ex
                                LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                LEFT JOIN db_employees.employees p1 ON (p1.NIP = ex.Pengawas1)
                                LEFT JOIN db_employees.employees p2 ON (p2.NIP = ex.Pengawas2)
                                LEFT JOIN db_employees.employees em ON (em.NIP = ex.InsertBy)
                                WHERE '.$whereP.' ORDER BY ex.ExamDate, ex.ExamStart, ex.ExamEnd ASC ';
        }

        $dataTable = $this->db->query($sql)->result_array();

        if(count($dataTable)>0){
            for($i=0;$i<count($dataTable);$i++){
                $dataC = $this->db->query('SELECT exg.ID, exg.ScheduleID, s.ClassGroup, mk.NameEng AS CourseEng, mk.MKCode, em.Name AS Coordinator  
                                                        FROM db_academic.exam_group exg
                                                        LEFT JOIN db_academic.schedule s ON (s.ID = exg.ScheduleID)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                        lEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        WHERE exg.ExamID = "'.$dataTable[$i]['ID'].'" 
                                                        GROUP BY exg.ScheduleID ORDER BY s.ClassGroup ASC')->result_array();

                if(count($dataC)>0){
                    for($s=0;$s<count($dataC);$s++){
                        $dataSt = $this->db->query('SELECT NPM,DB_Students FROM db_academic.exam_details exd 
                                                              WHERE exd.ExamID = "'.$dataTable[$i]['ID'].'"
                                                               AND exd.ExamGroupID = "'.$dataC[$s]['ID'].'"
                                                               AND exd.ScheduleID = "'.$dataC[$s]['ScheduleID'].'"
                                                                ')->result_array();

                        // Details Students
                        if(count($dataSt)>0){
                            for ($ss=0;$ss<count($dataSt);$ss++){
                                $ddst = $this->db->select('Name')->get_where($dataSt[$ss]['DB_Students'].'.students',
                                    array('NPM' => $dataSt[$ss]['NPM']),1)->result_array();
                                $dataSt[$ss]['Name'] = $ddst[0]['Name'];
                            }
                        }
                        $dataC[$s]['DetailsStudent'] = $dataSt;
                    }
                }

                $dataTable[$i]['Course'] = $dataC;
            }
        }


        $query = $dataTable;


        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $course = '';
            $totalStudent = 0;
            for($c=0;$c<count($row['Course']);$c++){
                $d = $row['Course'][$c];
                $totalStudent = $totalStudent + count($d['DetailsStudent']);
                $course = $course.' <div style="margin-top: 1px;"><b>'.$d['ClassGroup'].' | '.$d['CourseEng'].'</b><br/><p style="color: #2196F3;font-size: 11px;">(Co) '.$d['Coordinator'].'</p></div> ';
            }


            $exam_date = date("D, d M Y", strtotime($row['ExamDate']));
            $exam_time = substr($row['ExamStart'],0,5).' - '.substr($row['ExamEnd'],0,5);
            $exam_room = $row['Room'];


//            <li><a class="btnSave2PDF_Exam" href="javascript:void(0);" data-url="save2pdf/attendance-list" data-token="'.$tkn_attendance_std.'">Exam Attendance</a></li>
//                    <li><a target="_blank" href="'.base_url('save2pdf/news-event').'">Berita Acara</a></li>
            $act = '<div  style="text-align:center;"><div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="'.base_url('academic/exam-schedule/edit-exam-schedule/'.$row['ID']).'">Approved</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a class="btnDeleteExam" data-id="'.$row['ID'].'" href="javascript:void(0);" style="color: red;">Delete</a></li>
                  </ul>
                </div>
                </div>';


            $nestedData[] = $course;
            $nestedData[] = '<div style="text-align:center;">'.$totalStudent.'</div>';
            $nestedData[] = $act;
            $nestedData[] = '<div  style="text-align:center;">'.$exam_date.'<br/>'.$exam_time.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$exam_room.'</div>';
            $nestedData[] = $row['NameInsertBy'];

            $data[] = $nestedData;
        }


        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($query)),
            "recordsFiltered" => intval( count($query) ),
            "data"            => $data
        );
        echo json_encode($json_data);


    }

    public function getScheduleExamLecturer()
    {
        $requestData= $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $status = ($data_arr['Status']!='') ? ' AND ex.Status = "'.$data_arr['Status'].'" ' : '';
        $whereP = ' ex.SemesterID = "'.$data_arr['SemesterID'].'" 
                        AND ex.Type = "'.strtolower($data_arr['Type']).'"
                        AND ex.InsertByProdiID = "'.$data_arr['ProdiID'].'" 
                         '.$status;

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( em1.Name LIKE "%'.$search.'%" OR em2.Name LIKE "%'.$search.'%" OR
                              em3.Name LIKE "%'.$search.'%" OR cl.Room LIKE "%'.$search.'%" ) ';
        }

        $queryDefault = 'SELECT ex.*, cl.Room, em1.Name AS P_Name1, em2.Name AS P_Name2, em3.Name AS Name_InsertBy 
                              FROM db_academic.exam ex
                              LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                              LEFT JOIN db_employees.employees em1 ON (em1.NIP = ex.Pengawas1)
                              LEFT JOIN db_employees.employees em2 ON (em2.NIP = ex.Pengawas2)
                              LEFT JOIN db_employees.employees em3 ON (em3.NIP = ex.InsertBy)
                              WHERE ( '.$whereP.' ) '.$dataSearch.' ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            // Get Course
            $dataC = $this->db->query('SELECT exg.*, mk.NameEng AS CourseEng, mk.MKCode, s.ClassGroup, em.Name AS Lecturer  
                                                    FROM db_academic.exam_group exg
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = exg.ScheduleID)
                                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                    WHERE exg.ExamID = "'.$row['ID'].'"
                                                     GROUP BY s.ID ORDER BY s.ClassGroup ASC ')->result_array();

            $viewCourse = '';
            $totalStudent = 0;
            if(count($dataC)>0){
                for($s=0;$s<count($dataC);$s++){
                    $br = ($s!=0) ? '' : '';
                    $viewCourse = $viewCourse.''.$br.''.$dataC[$s]['MKCode'].' - '.$dataC[$s]['CourseEng'].
                        '<br/><p style="font-size:12px;color: #009688;">Group : <b>'.$dataC[$s]['ClassGroup'].
                        '</b> | <i class="fa fa-user margin-right"></i> '.$dataC[$s]['Lecturer'].'</p>';

                    // Get Students
                    $dataStd = $this->db->query('SELECT * FROM db_academic.exam_details exd 
                                                  WHERE exd.ExamID = "'.$row['ID'].'"
                                                   AND exd.ExamGroupID = "'.$dataC[$s]['ID'].'"
                                                   AND exd.ScheduleID = "'.$dataC[$s]['ScheduleID'].'" ')
                        ->result_array();
                    $dataC[$s]['DetailStudent'] = $dataStd;
                    $totalStudent = $totalStudent + count($dataStd);
                }
            }




            $time = substr($row['ExamStart'],0,5).' - '.substr($row['ExamEnd'],0,5);

            $status = ($row['Status']=='0' || $row['Status']==0)
                ? '<i class="fa fa-circle" style="color:#d8d8d8;"></i>'
                : '<i class="fa fa-check-circle" style="color: #369c3a;"></i>';

            $p1 = ($row['P_Name1']!='' && $row['P_Name1']!=null) ? $row['P_Name1'] : '';
            $p2 = ($row['P_Name2']!='' && $row['P_Name2']!=null) ? $row['P_Name2'] : '';
            $invigilator = ($p2!='') ? '- '.$p1.'<br/>- '.$p2 : '- '.$p1;

            $tokenID = $this->jwt->encode(array(
                'ExamID' => $row['ID']
            ),'UAP)(*');
            $act = '-';

            //
            if($row['Status']=='0' || $row['Status']==0){
                $act = '<div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="'.url_sign_in_lecturers.'exam/edit-schedule-exam/'.$tokenID.'">Edit</a></li>
                    <li><a class="btnDeleteExamFromLecturer" data-id="'.$row['ID'].'" href="javascript:void(0);" style="color: red;">Delete</a></li>
                  </ul>
                </div>';
            }

            $nestedData[] = $viewCourse;
            $nestedData[] = $invigilator;
            $nestedData[] = '<div  style="text-align:center;">'.$totalStudent.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$act.'</div>';
            $nestedData[] = '<div style="text-align:center;">'.date('l, d M Y',strtotime($row['ExamDate'])).'<br/>'.$time.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['Room'].'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$status.'</div>';
            $nestedData[] = '<p style="font-size: 12px;"><i class="fa fa-user margin-right"></i><b>'.$row['Name_InsertBy'].
                '</b><br/>'.date('l, d M Y h:s:i',strtotime($row['InsertAt'])).'</p>';

            $data[] = $nestedData;

        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval( count($queryDefaultRow) ),
            "data"            => $data
        );
        echo json_encode($json_data);

    }

    public function checkSchedule(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0 && $data_arr['action']=='check'){
            $dataFilter =(array) $data_arr['formData'];
            $data = $this->m_api->__checkSchedule($dataFilter);

            return print_r(json_encode($data));
        }
    }

    public function crudProgramCampus(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $data = $this->m_api->getProgramCampus();
                return print_r(json_encode($data));
            }
        }
    }

    public function crudSemester(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $data = $this->m_api->getSemester($data_arr['order']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='ReadSemesterActive'){
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->getSemesterActive($formData['SemesterID'],$formData['CurriculumID'],
                    $formData['ProdiID'],$formData['Semester'],$formData['IsSemesterAntara']);
                return print_r(json_encode($data));
            }
        }
    }

    public function crudCourseOfferings(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0) {
            if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];
                $this->db->insert('db_academic.course_offerings',$formData);
                $insert_id = $this->db->insert_id();
                return print_r($insert_id);
            }
            else if($data_arr['action']=='edit'){
                $formData = (array) $data_arr['formData'];

                $this->db->where('ID', $data_arr['OfferID']);
                $this->db->update('db_academic.course_offerings',$formData);

                return print_r($data_arr['OfferID']);
            }
            else if($data_arr['action']=='read'){
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->getAllCourseOfferings($formData['SemesterID'],$formData['CurriculumID'],
                    $formData['ProdiID'],$formData['Semester'],$formData['IsSemesterAntara']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='readgabungan'){
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->getAllCourseOfferingsMKU($formData['SemesterID']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='editSemester') {
//                $formData = (array) $data_arr['formData'];
                $this->db->set('ToSemester', $data_arr['ToSemester']);
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.course_offerings');

                return print_r(1);
            }
            // Untuk mengecek apakah MK Offering sudah dibuatkan jadwal atau belum
            else if($data_arr['action']=='checkCourse'){
                $dataWhere = (array) $data_arr['dataWhere'];
//                $query = $this->db
//                    ->get_where('db_academic.schedule', $dataWhere)
//                    ->result_array();

                $query = $this->m_api->__checkCourse($dataWhere['SemesterID'],$dataWhere['MKID']);

                if(count($query)>0){
                    return print_r(0);
                } else {
                    return print_r(1);
                }
            }
            else if($data_arr['action']=='delete'){

                $query = $this->db->get_where('db_academic.course_offerings', array('ID' => $data_arr['OfferID']), 1)->result_array();

                if(count($query)>0){
                    $Arr_CDID = json_decode($query[0]['Arr_CDID']);

//                    print_r($Arr_CDID);
//
//                    exit;

                    if(count($Arr_CDID)>1){
                        $result = [];
                        if (($key = array_search($data_arr['CDID'], $Arr_CDID)) !== false) {
                            for($a=0;$a<count($Arr_CDID);$a++){
                                if($a!=$key){
                                    array_push($result,$Arr_CDID[$a]);
                                }
                            }
                        }

                        $this->db->set('Arr_CDID', json_encode($result));
                        $this->db->where('ID', $data_arr['OfferID']);
                        $this->db->update('db_academic.course_offerings');

                        return print_r(1);


                    } else if(count($Arr_CDID)==1){
                        $this->db->where('ID', $data_arr['OfferID']);
                        $this->db->delete('db_academic.course_offerings');
                        return print_r(1);

                    }


//                    print_r(json_encode($r));


                }

            }
            else if($data_arr['action']=='readToSchedule') {
                $formData = (array) $data_arr['formData'];

                $data = $this->m_api->getOfferingsToSetSchedule($formData);
                return print_r(json_encode($data));

            }
        }
    }


    public function getAllStudents(){

        $data = $this->m_api->__getTahunAngkatan();

        return print_r(json_encode($data));
    }

    public function crudeStudent(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->__getStudentByNPM($formData['ta'],$formData['NPM']);

                return print_r(json_encode($data));

            }
        }
    }

    public function getClassGroup(){
//        $token = $this->input->post('token');
//        $key = "UAP)(*";
//        $data_arr = (array) $this->jwt->decode($token,$key);
        $data_arr = $this->getInputToken();

        $data = $this->m_api->__checkClassGroup(
            $data_arr['ProgramsCampusID'],
            $data_arr['SemesterID'],
            $data_arr['ProdiCode'],
            $data_arr['IsSemesterAntara']
        );

        $result = array(
            'Group' => $data_arr['ProdiCode'].'-'.(count($data)+1)
        );

        return print_r(json_encode($result));
    }

    public function getClassGroupParalel(){
        $data_arr = $this->getInputToken();
        $data = $this->m_api->__checkClassGroupParalel(
            $data_arr['ProgramsCampusID'],
            $data_arr['SemesterID'],
            $data_arr['ProdiCode'],
            $data_arr['IsSemesterAntara']
        );

        return print_r(json_encode($data));;
    }

    public function crudClassroom(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0) {
            if($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllClassRoom();
                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'add'){
                $formData = (array) $data_arr['formData'];

                // Cek Apakah ruangan sudah di input
                $this->db->where('Room', $formData['Room']);
                $room = $this->db->get('db_academic.classroom')->result_array();


                if(count($room)>0){
                    $result = array(
                        'inserID' => 0
                    );
                } else {
                    $this->db->insert('db_academic.classroom',$formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            }
            else if($data_arr['action'] == 'edit'){
                $formData = (array) $data_arr['formData'];

                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.classroom',$formData);
                $result = array(
                    'inserID' => $ID
                );

                return print_r(json_encode($result));

            }
            else if($data_arr['action'] == 'delete'){
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.classroom');
                return print_r($ID);
            }

        }

    }

    public function uploadFfile($name)
    {
        // upload file
        $filename = md5($name);
        $config['upload_path']   = './uploads/vreservation/';
        $config['overwrite'] = TRUE;
        $config['allowed_types'] = '*';
        $config['file_name'] = $filename;
        //$config['max_size']      = 100;
        //$config['max_width']     = 300;
        //$config['max_height']    = 300;
        $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload('fileData')) {
            return $error = $this->upload->display_errors();
            //$this->load->view('upload_form', $error);
        }

        else {
            return $data =  $this->upload->data();
            //$this->load->view('upload_success', $data);
        }
    }

    public function crudClassroomVreservation(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0) {
            if($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllClassRoomCategory();

                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'add'){
                $formData = (array) $data_arr['formData'];

                // Cek Apakah ruangan sudah di input
                $this->db->where('Room', $formData['Room']);
                $room = $this->db->get('db_academic.classroom')->result_array();


                if(count($room)>0){
                    $result = array(
                        'inserID' => 0
                    );
                } else {

                    $uploadFile = $this->uploadFfile(mt_rand());
                    $filename = '';
                    if (is_array($uploadFile)) {
                        $filename = $uploadFile['file_name'];
                    }
                    $file = array('Layout' => $filename);
                    $formData = $formData + $file;
                    $this->db->insert('db_academic.classroom',$formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            }
            else if($data_arr['action'] == 'edit'){
                $formData = (array) $data_arr['formData'];

                $uploadFile = $this->uploadFfile(mt_rand());
                $filename = '';
                if (is_array($uploadFile)) {
                    $filename = $uploadFile['file_name'];
                }
                $file = array('Layout' => $filename);
                $formData = $formData + $file;

                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.classroom',$formData);
                $result = array(
                    'inserID' => $ID
                );

                return print_r(json_encode($result));

            }
            else if($data_arr['action'] == 'delete'){
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.classroom');
                return print_r($ID);
            }

        }

    }

    public function crudGrade(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0) {
            if($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllGrade();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='add'){
                $formData = (array) $data_arr['formData'];
                // Cek grade
                $this->db->where('Grade', $formData['Grade']);
                $grade = $this->db->get('db_academic.grade')->result_array();

                if(count($grade)>0){
                    $result = array(
                        'inserID' => 0
                    );
                } else {
                    $this->db->insert('db_academic.grade',$formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            }
            else if($data_arr['action']=='edit'){
                $formData = (array) $data_arr['formData'];
                // Cek grade
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.grade',$formData);
                $result = array(
                    'inserID' => $ID
                );

                return print_r(json_encode($result));

            }
            else if($data_arr['action'] == 'delete'){
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.grade');
                return print_r($ID);
            }
        }
    }

    public function crudRangeCredits() {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action'] == 'read') {
                $data = $this->m_api->__getRangeCredits();
                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'delete'){
//                print_r($data_arr);
//                exit;
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.range_credits');
                return print_r(1);
            }
            else if($data_arr['action']=='add'){
                $formData = (array) $data_arr['formData'];
                $this->db->insert('db_academic.range_credits', $formData);
                $insert_id = $this->db->insert_id();
                return print_r($insert_id);
            }
            else if($data_arr['action']=='edit'){
                $ID = $data_arr['ID'];
                $formData = (array) $data_arr['formData'];
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.range_credits',$formData);

                return print_r($ID);
            }
        }
    }

    public function crudTimePerCredit(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0) {
            if($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllTimePerCredit();
                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'add'){
                $formData = (array) $data_arr['formData'];
                // Cek Time
                $this->db->where('Time', $formData['Time']);
                $time = $this->db->get('db_academic.time_per_credits')->result_array();

                if(count($time)>0){
                    $result = array(
                        'inserID' => 0
                    );
                } else {
                    $this->db->insert('db_academic.time_per_credits',$formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            }
            else if($data_arr['action'] == 'delete') {
                $time = $this->db->get('db_academic.time_per_credits')->result_array();

                if(count($time)>1){
                    $ID = $data_arr['ID'];
                    $this->db->where('ID', $ID);
                    $this->db->delete('db_academic.time_per_credits');
                    $result = array(
                        'inserID' => $ID
                    );

                } else {
                    $result = array(
                        'inserID' => 0
                    );

                }
                return print_r(json_encode($result));
            }
        }
    }

    public function crudLecturer(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $NIP = $data_arr['NIP'];
                $data = $this->m_api->__getLecturerDetail($NIP);
                return print_r(json_encode($data));
            }

            else if($data_arr['action']=='readMini'){
                $NIP = $data_arr['NIP'];
                $data = $this->db->select('NIP,NIDN,Name,TitleAhead,TitleBehind,PositionMain,Phone,
                                        HP,Email,EmailPU,Password,Address,Photo,Photo_new')
                    ->get_where('db_employees.employees',array('NIP'=>$NIP),1)
                    ->result_array();


                if(count($data)>0){
                    $sp = explode('.',$data[0]['PositionMain']);
                    $DiviosionID = $sp[0];
                    $PositionID = $sp[1];

                    $div = $this->db->get_where('db_employees.division',array('ID'=>$DiviosionID),1)->result_array();
                    $data[0]['Division'] = $div[0]['Division'];

                    $pos = $this->db->get_where('db_employees.position',array('ID'=>$PositionID),1)->result_array();
                    $data[0]['Position'] = $pos[0]['Position'];

                    return print_r(json_encode($data[0]));
                } else {
                    return print_r(json_encode($data));
                }

            }

        }

    }

    //add bismar
    public function crudAcademic(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $NIP = $data_arr['NIP'];
                $data = $this->m_api->__getLecturerDetail($NIP);
                return print_r(json_encode($data));
            }

            else if($data_arr['action']=='readMini'){
                $NIP = $data_arr['NIP'];
                $data = $this->db->select('NIP,NIDN,Name,TitleAhead,TitleBehind,PositionMain,Phone,
                                        HP,Email,EmailPU,Password,Address,Photo,Photo_new')
                    ->get_where('db_employees.employees',array('NIP'=>$NIP),1)
                    ->result_array();


                if(count($data)>0){
                    $sp = explode('.',$data[0]['PositionMain']);
                    $DiviosionID = $sp[0];
                    $PositionID = $sp[1];

                    $div = $this->db->get_where('db_employees.division',array('ID'=>$DiviosionID),1)->result_array();
                    $data[0]['Division'] = $div[0]['Division'];

                    $pos = $this->db->get_where('db_employees.position',array('ID'=>$PositionID),1)->result_array();
                    $data[0]['Position'] = $pos[0]['Position'];

                    return print_r(json_encode($data[0]));
                } else {
                    return print_r(json_encode($data));
                }

            }
        }
    }


    public function review_academicdetail(){
        $NIP = $this->input->get('NIP');
        $viewacademic = $this->m_api->views_academic($NIP);
        echo json_encode($viewacademic);     

     }

     public function review_otherfile(){
        $NIP = $this->input->get('NIP');
        $viewfiles = $this->m_api->views_otherfile($NIP);
        echo json_encode($viewfiles);     

     }

    public function review_academics1(){

        $NIP = $this->input->get('NIP');
        $srata = $this->input->get('s');
        $viewfiles1 = $this->m_api->views_files1($NIP,$srata);
        echo json_encode($viewfiles1);   
     }


    public function getedit_datas1(){

        $academic = $this->input->get('s');

        if ($academic == 'S1') {
            $NIP = $this->input->get('n');
            $fileijazahs1 = $this->input->get('j');
            $filetranscripts1 = $this->input->get('t');
            
            $viewfiles1 = $this->m_api->views_editacademic($NIP,$fileijazahs1,$filetranscripts1);
            echo json_encode($viewfiles1);   
        }  
        else if ($academic == 'S2') {
            $NIP = $this->input->get('n');
            $fileijazahs1 = $this->input->get('j');
            $filetranscripts1 = $this->input->get('t');
            
            $viewfiles1 = $this->m_api->views_editacademic($NIP,$fileijazahs1,$filetranscripts1);
            echo json_encode($viewfiles1);   

        }
        else if ($academic == 'S3') {
            $NIP = $this->input->get('n');
            $fileijazahs1 = $this->input->get('j');
            $filetranscripts1 = $this->input->get('t');
            
            $viewfiles1 = $this->m_api->views_editacademic($NIP,$fileijazahs1,$filetranscripts1);
            echo json_encode($viewfiles1);   
        }
        else if ($academic == 'OTF') {
            $NIP = $this->input->get('n');
            $IDfiles = $this->input->get('t');
            //$filetranscripts1 = $this->input->get('t');
            
            $viewfiles1 = $this->m_api->views_editotfiles($NIP,$IDfiles);
            echo json_encode($viewfiles1);   
        }
    }
     

    public function insertWilayahURLJson()
    {
        $data = $this->input->post('data');
        $generate = $this->m_api->saveDataWilayah($data);
        echo json_encode($generate);
    }

    public function insertSchoolURLJson()
    {
        $data = $this->input->post('data');
        $generate = $this->m_api->saveDataSchool($data);
        echo json_encode($generate);
    }

    public function getWilayahURLJson()
    {
        $generate = $this->m_api->getdataWilayah();
        echo json_encode($generate);
    }

    public function getSMAWilayah()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $result = $this->m_api->__getSMAWilayah($data_arr['wilayah']);

        return print_r(json_encode($result));
    }

    public function getDataRegisterBelumBayar()
    {
        $Tahun = $this->input->post('Tahun');
        // print_r($Tahun);die();
        // $Tahun = $Tahun['Tahun'];
        // $getData = $this->m_api->getDataRegisterBelumBayar();
        $getData = $this->m_api->getDataRegisterBelumBayar2($Tahun);
        echo json_encode($getData);
    }

    public function getDataRegisterTelahBayar()
    {
        $Tahun = $this->input->post('Tahun');
        // $Tahun = $Tahun['Tahun'];
        // $getData = $this->m_api->getDataRegisterTelahBayar();
        $getData = $this->m_api->getDataRegisterTelahBayar2($Tahun);
        echo json_encode($getData);
    }

    public function getClassGroupAutoComplete($SemesterID){
        $term = $this->input->get('term');
        $data = $this->db->query('SELECT ID,ClassGroup FROM db_academic.schedule 
                                      WHERE SemesterID = "'.$SemesterID.'" AND ClassGroup LIKE "%'.$term.'%" LIMIT 7 ')->result_array();

        $dataRes = [];
        for($i=0;$i<count($data);$i++){
            $arrp = array(
                'ID' => $data[$i]['ID'],
                'label' => $data[$i]['ClassGroup'],
                'value' => $data[$i]['ClassGroup']
            );
            array_push($dataRes,$arrp);
        }

        return print_r(json_encode($dataRes));
    }

    public function getScheduleIDByClassGroup($SemesterID,$Group){
        $data = $this->db->query('SELECT ID FROM db_academic.schedule WHERE SemesterID = "'.$SemesterID.'" AND ClassGroup LIKE "'.$Group.'" LIMIT 1 ')->result_array();
        $res = (count($data)>0) ? $data[0]['ID'] : 0;

        return print_r($res);
    }

    public function crudStudyPlanning()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if($data_arr['action'] == 'readAvailableKRS'){
                $SemesterID = $data_arr['SemesterID'];
                $NPM = $data_arr['NPM'];

                // Load Auth
                $dataAuth = $this->m_api->getDataAuthStudent($NPM);

                // Load Credit
                $dataCredit = $this->m_api->getMaxCredit('ta_'.$dataAuth[0]['Year'],$NPM,$dataAuth[0]['Year'],$SemesterID,$dataAuth[0]['ProdiID']);
                $dataAuth[0]['dataCredit'] = $dataCredit;

                $Semester = $this->m_api->getSemesterStudentByYear($SemesterID,$dataAuth[0]['Year']);

                $dataSc = $this->m_api->__getAvailabelCourse($SemesterID,$NPM,$dataAuth[0]['ProgramID'],$dataAuth[0]['ProdiID'],
                    $Semester,'0',$dataAuth[0]['Year']);

                $result = array(
                    'Student' => $dataAuth[0],
                    'Course' => $dataSc
                );

                return print_r(json_encode($result));

            }

            else if($data_arr['action']=='chekSeat'){
                $dataToken = $data_arr;
                $dataWhere = (array) $dataToken['dataWhere'];
                $querySeat = $this->db->get_where('db_academic.std_krs', $dataWhere)->result_array();
                $countStudent = count($querySeat);

                // Get room
                $data = $this->db->query('SELECT cl.Room, cl.Seat FROM db_academic.schedule_details sd 
                                                LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                WHERE sd.ScheduleID = "'.$dataWhere['ScheduleID'].'" ')->result_array();

                $result = array('Status' => 1);
                if(count($data)>0){
                    foreach ($data AS $item){
                        if($item['Seat']<=$countStudent){
                            $result = array('Status' => 0);
                        }
                    }
                }

                return print_r(json_encode($result));
            }

            else if($data_arr['action']=='addAvailabelCourse'){

                $DBStudent = $data_arr['DBStudent'];
                $dataInsert = (array) $data_arr['dataInsert'];

                // Cek apakah sudah di insert atau belum, jika sudah langsung return 1
                // Cek apakah schedule sudah ada ataub belum jika ada maka cukup return saja
                $dataWhere = array(
                    'SemesterID' => $dataInsert['SemesterID'],
                    'NPM' => $dataInsert['NPM'],
                    'ScheduleID' => $dataInsert['ScheduleID'],
                    'CDID' => $dataInsert['CDID'],
                );

                $dataCheck = $this->db->get_where('db_academic.std_krs',$dataWhere)->result_array();
                if(count($dataCheck)<=0){

                    // Insert ke std krs
                    $this->db->insert('db_academic.std_krs', $dataInsert);

                    // Get Attendance Attendance
                    $dataAttd = $this->db->get_where('db_academic.attendance',
                        array('SemesterID' => $dataInsert['SemesterID'],
                            'ScheduleID' => $dataInsert['ScheduleID']))->result_array();

                    // Insert Ke Attendance
                    foreach ($dataAttd AS $itemA) {
                        $dataAins = array(
                            'ID_Attd' => $itemA['ID'],
                            'NPM' => $dataInsert['NPM']
                        );
                        $this->db->insert('db_academic.attendance_students', $dataAins);
                    }

                    // - get MKID
                    $dataC = $this->db->select('MKID')->get_where('db_academic.curriculum_details',
                        array('ID' => $dataInsert['CDID']),1)->result_array();

                    $dataUpdateKRS = array(
                        'SemesterID' => $dataInsert['SemesterID'],
                        'MhswID' => $data_arr['MhswID'],
                        'NPM' => $dataInsert['NPM'],
                        'ScheduleID' => $dataInsert['ScheduleID'],
                        'TypeSchedule' => $dataInsert['TypeSP'],
                        'CDID' => $dataInsert['CDID'],
                        'MKID' => $dataC[0]['MKID'],
                        'Approval' => '0',
                        'StatusSystem' => '1',
                        'Status' => '1'
                    );

                    $this->db->insert($data_arr['DBStudent'].'.study_planning', $dataUpdateKRS);

                }
                return print_r(1);
            }

            else if($data_arr['action']=='ApprovedByMentorAll'){

                $ArrToApproveAll = (array) $data_arr['ArrToApproveAll'];
                $ArrToApproveAll_Logging = (array) $data_arr['ArrToApproveAll_Logging'];



                if(count($ArrToApproveAll)>0){
                    for($i=0;$i<count($ArrToApproveAll);$i++){
                        $arrUpdate = array(
                            'Status' => '2',
                            'ApprovalPA_At' => $data_arr['ApprovalPA_At']
                        );
                        $this->db->where('ID', $ArrToApproveAll[$i]);
                        $this->db->update('db_academic.std_krs',$arrUpdate);

                        // Insert Logging
                        $Log_dataInsert = (array) $ArrToApproveAll_Logging[$i];

                        $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                        $insert_id = $this->db->insert_id();

                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id,
                            'UserID' => $data_arr['NPM']
                        );
                        $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                        // Cek Kaprodi
                        $dataKaprodi = $this->db->query('SELECT ps.KaprodiID FROM db_academic.auth_students auts
                                                                    LEFT JOIN db_academic.program_study ps 
                                                                    ON (ps.ID = auts.ProdiID)
                                                                    WHERE auts.NPM = "'.$data_arr['NPM'].'" ')->result_array();

                        if(count($dataKaprodi)>0){
                            if($dataKaprodi[0]['KaprodiID']!=$data_arr['ApprovedBy']){
                                $Log_arr_ins_kaprodi = array(
                                    'IDLogging' => $insert_id,
                                    'UserID' => $dataKaprodi[0]['KaprodiID']
                                );
                                $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins_kaprodi);
                            }
                        }

                    }

                }

                return print_r(1);
            }
            else if($data_arr['action']=='ApprovedByMentor'){
                $arrUpdate = array(
                    'Status' => '2',
                    'ApprovalPA_At' => $data_arr['ApprovalPA_At']
                );
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.std_krs',$arrUpdate);

                // Insert Logging
                $Log_dataInsert = (array) $data_arr['Logging'];
                $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                $insert_id = $this->db->insert_id();

                $Log_arr_ins = array(
                    'IDLogging' => $insert_id,
                    'UserID' => $data_arr['NPM']
                );

                // Send Notif To Kaprodi

                $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                // Cek Kaprodi
                $dataKaprodi = $this->db->select('KaprodiID')->get_where('db_academic.program_study',array('ID' => $data_arr['ProdiID']))->result_array();
                $Log_arr_ins_KA = array(
                    'IDLogging' => $insert_id,
                    'UserID' => $dataKaprodi[0]['KaprodiID']
                );
                $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins_KA);

                return print_r(1);
            }

            else if($data_arr['action']=='ApprovedByKaprodiAll'){

                $ArrToApproveAll = (array) $data_arr['ArrToApproveAll'];
                $ArrToApproveAll_Logging = (array) $data_arr['ArrToApproveAll_Logging'];

                if(count($ArrToApproveAll)>0){
                    for($i=0;$i<count($ArrToApproveAll);$i++){

                        $this->approveByKaprodi($ArrToApproveAll[$i]
                            ,$data_arr['MhswID'],$data_arr['ApprovalAt']);

                        // Insert Logging
                        $Log_dataInsert = (array) $ArrToApproveAll_Logging[$i];
                        $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                        $insert_id = $this->db->insert_id();

                        $Log_dataUser = $this->db->select('NIP')->get_where('db_employees.rule_users',
                            array('IDDivision' => '6'))->result_array();

                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id,
                            'UserID' => $data_arr['NPM']
                        );
                        $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                        if(count($Log_dataUser)>0){

                            for($l=0;$l<count($Log_dataUser);$l++){
                                $d = $Log_dataUser[$l]['NIP'];
                                $Log_arr_ins = array(
                                    'IDLogging' => $insert_id,
                                    'UserID' => $d
                                );
                                $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);
                            }

                        }

                    }
                }

                return print_r(1);

            }

            else if($data_arr['action']=='ApprovedByKaprodi'){
                $this->approveByKaprodi($data_arr['ID']
                    ,$data_arr['MhswID'],$data_arr['ApprovalAt']);

                // Insert Logging
                $Log_dataInsert = (array) $data_arr['Logging'];
                $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                $insert_id = $this->db->insert_id();

                $Log_dataUser = $this->db->select('NIP')->get_where('db_employees.rule_users',
                    array('IDDivision' => '6'))->result_array();

                $Log_arr_ins = array(
                    'IDLogging' => $insert_id,
                    'UserID' => $data_arr['NPM']
                );
                $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);
                if(count($Log_dataUser)>0){
                    for($i=0;$i<count($Log_dataUser);$i++){
                        $d = $Log_dataUser[$i]['NIP'];
                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id,
                            'UserID' => $d
                        );
                        $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);
                    }
                }

                return print_r(1);
            }

            else if($data_arr['action']=='RejectedByMentor'){

                $arrUpdate = array(
                    'Status' => '-2'
                );
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.std_krs',$arrUpdate);

                $dataInsert = array(
                    'KRSID' => $data_arr['ID'],
                    'Reason' => $data_arr['Reason'],
                    'UpdateBy' => $data_arr['UpdateBy'],
                    'UpdateAt' => $data_arr['UpdateAt']
                );
                $this->db->insert('db_academic.std_krs_comment',$dataInsert);

                // Insert Logging
                $Log_dataInsert = (array) $data_arr['dataLogging'];
                $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                $insert_id = $this->db->insert_id();

                $Log_dataUser = $this->db->select('NIP')->get_where('db_employees.rule_users',
                    array('IDDivision' => '6'))->result_array();

                $Log_arr_ins = array(
                    'IDLogging' => $insert_id,
                    'UserID' => $data_arr['NPM']
                );
                $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                if(count($Log_dataUser)>0){
                    for($i=0;$i<count($Log_dataUser);$i++){
                        $d = $Log_dataUser[$i]['NIP'];
                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id,
                            'UserID' => $d
                        );
                        $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);
                    }
                }

                return print_r(1);
            }

            else if($data_arr['action']=='RejectedByKaprodi'){

                $arrUpdate = array(
                    'Status' => '-3'
                );
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.std_krs',$arrUpdate);

                $dataInsert = array(
                    'KRSID' => $data_arr['ID'],
                    'Reason' => $data_arr['Reason'],
                    'UpdateBy' => $data_arr['UpdateBy'],
                    'UpdateAt' => $data_arr['UpdateAt']
                );
                $this->db->insert('db_academic.std_krs_comment',$dataInsert);

                // Insert Logging
                $Log_dataInsert = (array) $data_arr['dataLogging'];
                $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                $insert_id = $this->db->insert_id();

                $Log_dataUser = $this->db->select('NIP')->get_where('db_employees.rule_users',
                    array('IDDivision' => '6'))->result_array();

                $Log_arr_ins = array(
                    'IDLogging' => $insert_id,
                    'UserID' => $data_arr['NPM']
                );
                $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                if(count($Log_dataUser)>0){
                    for($i=0;$i<count($Log_dataUser);$i++){
                        $d = $Log_dataUser[$i]['NIP'];
                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id,
                            'UserID' => $d
                        );
                        $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);
                    }
                }

                return print_r(1);
            }

            else if($data_arr['action']=='deleteAvailabelCourse'){
                $SKID = $data_arr['SKID'];
                $dataStd = $this->db->query('SELECT sk.SemesterID, sk.NPM, sk.ScheduleID, sk.CDID, 
                                                          auts.Year
                                                          FROM db_academic.std_krs sk
                                                          LEFT JOIN db_academic.auth_students auts ON (auts.NPM = sk.NPM)
                                                          WHERE sk.ID = "'.$SKID.'" ')->result_array();

                if(count($dataStd)>0){
                    foreach ($dataStd AS $std){
                        // === Hapus Attendance ===
                        // Get ATTD ID
                        $dataAttd = $this->db->select('ID')->get_where('db_academic.attendance',array(
                            'SemesterID' => $std['SemesterID'],
                            'ScheduleID' => $std['ScheduleID']
                        ))->result_array();

                        if(count($dataAttd)>0){
                            foreach($dataAttd AS $attd){
                                $this->db->where(array(
                                    'ID_Attd' => $attd['ID'],
                                    'NPM' => $std['NPM']
                                ));
                                $this->db->delete('db_academic.attendance_students');
                                $this->db->reset_query();
                            }
                        }

                        // ===== Hapus di Study plan =====
                        $DBStudent = 'ta_'.$std['Year'];
                        $this->db->where(array(
                            'SemesterID' => $std['SemesterID'],
                            'ScheduleID' => $std['ScheduleID'],
                            'NPM' => $std['NPM']
                        ));
                        $this->db->delete($DBStudent.'.study_planning');
                        $this->db->reset_query();

                        // Trakhir hapus di std krs
                        $this->db->where('ID',$SKID);
                        $this->db->delete('db_academic.std_krs');
                        $this->db->reset_query();

                    }
                }

                return print_r(1);

            }


            else if ($data_arr['action'] == 'read') {
                $dataWhere = (array) $data_arr['dataWhere'];
                $data = $this->m_api->__getStudyPlanning($dataWhere);
                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'detailStudent'){
                $data = $this->m_api->getDetailStudyPlanning($data_arr['NPM'],$data_arr['ta']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'detailKRSStudents'){

                $NPM = $this->session->userdata('student_NPM');
                $ProgramCampusID = $this->session->userdata('student_ProgramCampusID');
                $Semester = $this->session->userdata('student_Semester');
                $IsSemesterAntara = '0';
                $ClassOf = $this->session->userdata('student_ClassOf');

                $data = $this->m_academic->__getKRS($data_arr['SemesterID'],$ProgramCampusID,$data_arr['ProdiID'],
                    $Semester,$IsSemesterAntara,$NPM,$ClassOf);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='add'){
                $formData = (array) $data_arr['formData'];
                $this->db->insert('db_academic.std_krs', $formData);
                $insert_id = $this->db->insert_id();

                return print_r($insert_id);
            }
            else if($data_arr['action']=='searchByGroup'){
                $data = $this->db->query('SELECT sdc.ID AS SDCID, sdc.ScheduleID, ps.NameEng,s.ID AS ProdiEng,sdc.CDID , 
                                                    cd.Semester, cr.Year, mk.MKCode, s.ClassGroup, mk.ID AS MKID 
                                                    FROM db_academic.schedule_details_course sdc 
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                                    LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                                    LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                    LEFT JOIN db_academic.curriculum cr ON (cr.ID = cd.CurriculumID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                    WHERE s.SemesterID = "'.$data_arr['SemesterID'].'" 
                                                    AND s.ClassGroup 
                                                    LIKE "%'.$data_arr['ClassGroup'].'%" ORDER BY cr.Year DESC LIMIT 10')->result_array();

                if(count($data)>0){
                    for($c=0;$c<count($data);$c++){
                        // Semester Saat Ini


                        $smt = $this->m_api->_getSeemsterByClassOf($data[$c]['Year']);

                        $data[$c]['Semester'] = $smt;

                        $aarw = array(
                            'SDCID' => $data[$c]['SDCID'],
                            'ScheduleID' => $data[$c]['ScheduleID']
                        );
                        $data[$c]['D'] = $this->m_api->getStdCombinedClass($aarw,$smt);
                    }
                }



                return print_r(json_encode($data));
            }
        }

    }

    public function approveByKaprodi($SKID,$MhswID,$ApprovalAt){
        $dataInsert = $this->db->query('SELECT sk.*, auts.Year FROM db_academic.std_krs sk
                                                                    LEFT JOIN db_academic.auth_students auts ON (auts.NPM = sk.NPM)
                                                                    WHERE sk.ID = "'.$SKID.'" LIMIT 1 ')->result_array()[0];

        // Get Attendance Attendance
        $dataAttd = $this->db->get_where('db_academic.attendance',
            array('SemesterID' => $dataInsert['SemesterID'],
                'ScheduleID' => $dataInsert['ScheduleID']))->result_array();

        // Insert Ke Attendance
        foreach ($dataAttd AS $itemA) {
            $dataAins = array(
                'ID_Attd' => $itemA['ID'],
                'NPM' => $dataInsert['NPM']
            );
            $this->db->insert('db_academic.attendance_students', $dataAins);
            $this->db->reset_query();
        }


        // - get MKID
        $dataC = $this->db->select('MKID')->get_where('db_academic.curriculum_details',
            array('ID' => $dataInsert['CDID']),1)->result_array();

        $dataUpdateKRS = array(
            'SemesterID' => $dataInsert['SemesterID'],
            'MhswID' => $MhswID,
            'NPM' => $dataInsert['NPM'],
            'ScheduleID' => $dataInsert['ScheduleID'],
            'TypeSchedule' => $dataInsert['TypeSP'],
            'CDID' => $dataInsert['CDID'],
            'MKID' => $dataC[0]['MKID'],
            'Approval' => '0',
            'StatusSystem' => '1',
            'Status' => '1'
        );

        $DBStudent = 'ta_'.$dataInsert['Year'];
        $this->db->insert($DBStudent.'.study_planning', $dataUpdateKRS);
        $this->db->reset_query();

        $arrUpdate = array(
            'Status' => '3',
            'ApprovalKaprodi_At' => $ApprovalAt
        );
        $this->db->where('ID', $SKID);
        $this->db->update('db_academic.std_krs',$arrUpdate);
        $this->db->reset_query();
    }

    public function crudYearAcademic()
    {

        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if($data_arr['action']=='read'){
                $data = $this->db->order_by('YearAcademic','ASC')->get('db_academic.std_ta')
                    ->result_array();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='add') {
                $dataInsert = (array) $data_arr['dataInsert'];
                $this->db->insert('db_academic.std_ta',$dataInsert);
                $insert_id = $this->db->insert_id();

                $db_new = 'ta_'.$dataInsert['YearAcademic'];

                $this->m_api->createDBYearAcademicNew($db_new);

                return print_r($insert_id);
            }
        }


    }

    public function filterStudents(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='readStudents'){
                $filter = (array) $data_arr['dataFilter'];

//                $data = $this->m_api->__filterStudents($filter);
                $data = $this->m_api->__filterStudents($filter);

                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='delete'){
                $this->db->where('ID', $data_arr['IDMA']);
                $this->db->delete('db_academic.mentor_academic');
                return print_r(1);

            }
            else if($data_arr['action']=='add'){
                $dataForm = (array) $data_arr['dataForm'];
                $dataNPM = (array) $data_arr['dataNPM'];

                for($i=0;$i<count($dataNPM);$i++){
                    $dataForm['NPM'] = $dataNPM[$i];
//                    print_r($dataForm);
                    $this->db->insert('db_academic.mentor_academic',$dataForm);
                }

                return print_r(1);
//                print_r($dataNPM);
            }
        }
    }

    public function crudTuitionFee(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $ClassOf = $data_arr['ClassOf'];

                $prodi = $this->db->select('ID,NameEng')->get('db_academic.program_study')->result_array();

                $result = [];

                for($i=0;$i<count($prodi);$i++){
                    $data = $this->db->query('SELECT tf.ID,tf.PTID,tf.Cost,pt.Description,pt.Abbreviation,tf.Pay_Cond FROM db_finance.tuition_fee tf 
                                                    LEFT JOIN db_finance.payment_type pt ON (tf.PTID = pt.ID)
                                                    LEFT JOIN db_academic.program_study ps ON (tf.ProdiID = ps.ID)
                                                    WHERE tf.ClassOf = "'.$ClassOf.'" AND tf.ProdiID = "'.$prodi[$i]['ID'].'" and tf.Pay_Cond = 1
                                                    ORDER BY tf.ProdiID, tf.PTID ASC ')->result_array();
                    if(count($data)>0){
                        $data_p = array(
                            'ProdiID' => $prodi[$i]['ID'],
                            'ProdiName' => $prodi[$i]['NameEng'],
                            'Detail' => $data
                        );
                        array_push($result,$data_p);
                    }

                }

                for($i=0;$i<count($prodi);$i++){
                    $data = $this->db->query('SELECT tf.ID,tf.PTID,tf.Cost,pt.Description,pt.Abbreviation,tf.Pay_Cond FROM db_finance.tuition_fee tf 
                                                    LEFT JOIN db_finance.payment_type pt ON (tf.PTID = pt.ID)
                                                    LEFT JOIN db_academic.program_study ps ON (tf.ProdiID = ps.ID)
                                                    WHERE tf.ClassOf = "'.$ClassOf.'" AND tf.ProdiID = "'.$prodi[$i]['ID'].'" and tf.Pay_Cond = 2
                                                    ORDER BY tf.ProdiID, tf.PTID ASC ')->result_array();
                    if(count($data)>0){
                        $data_p = array(
                            'ProdiID' => $prodi[$i]['ID'],
                            'ProdiName' => $prodi[$i]['NameEng'],
                            'Detail' => $data
                        );
                        array_push($result,$data_p);
                    }

                }

                return print_r(json_encode($result));
            }
        }
    }

    public function getEmployeesBy($division = null,$position = null)
    {
        try{
            $key = "UAP)(*";
            $division = $this->jwt->decode($division,$key);
            $position = $this->jwt->decode($position,$key);
            $getData = $this->m_api->getEmployeesBy($division,$position);
            echo json_encode($getData);
        }
        catch(Exception $e)
        {
            echo json_encode('No Result Data');
        }

    }

    public function getFormulirOfflineAvailable($StatusJual = 0)
    {
        $getData = $this->m_api->getFormulirOfflineAvailable($StatusJual);
        echo json_encode($getData);
    }

    public function AutoCompleteSchool()
    {
        $input = $this->getInputToken();
        $data['response'] = 'true'; //mengatur response
        $data['message'] = array(); //membuat array
        $getData = $this->m_api->getSchoolbyNameAC($input['School']);
        for ($i=0; $i < count($getData); $i++) {
            $data['message'][] = array(
                'label' => $getData[$i]['SchoolName'],
                'value' => $getData[$i]['ID']
            );
        }
        echo json_encode($data);
    }

    public function getSemesterActive(){
        $data = $this->m_api->_getSemesterActive();
        return print_r(json_encode($data));
    }

    public function getSumberIklan()
    {
        $getData = $this->m_master->showDataActive_array('db_admission.source_from_event',1);
        echo json_encode($getData);
    }

    public function getPriceFormulirOffline()
    {
        $getData = $this->m_master->showDataActive_array('db_admission.price_formulir_offline',1);
        echo json_encode($getData);
    }

    public function getEvent()
    {
        $getData = $this->m_master->showDataActive_array('db_admission.price_event',1);
        echo json_encode($getData);
    }

    public function getDocument()
    {
        $input = $this->getInputToken();
        $this->load->model('admission/m_admission');
        $getData = $this->m_admission->getDataDokumentRegister($input['ID_register_formulir']);
        echo json_encode($getData);
    }

    public function getDocumentAdmisiMHS()
    {
        $input = $this->getInputToken();
        $this->load->model('admission/m_admission');
        $getData = $this->m_admission->getDocumentAdmisiMHS($input['NPM']);
        echo json_encode($getData);
    }

    public function crudJadwalUjian(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $data = $this->m_api->getJadwalUjian();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='checkDateExam'){
                $data = $this->m_api->getDateExam($data_arr['SemesterID']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='checkCourse4Exam'){
                $data = $this->m_api
                    ->__checkDataCourseForExam($data_arr['ScheduleID'],$data_arr['Type']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='showDataClassGroupInExam'){

                // Get Exam ID

                $dataExam = $this->db->query('SELECT ex.ID AS ExamID, s.ID AS ScheduleID, s.ClassGroup, mk.NameEng AS CourseEng, mk.Name AS Course FROM db_academic.exam_group exg 
                                                            LEFT JOIN db_academic.exam ex ON (ex.ID = exg.ExamID)
                                                            LEFT JOIN db_academic.schedule s ON (s.ID = exg.ScheduleID)
                                                            LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = exg.ScheduleID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                            WHERE ex.SemesterID = "'.$data_arr['SemesterID'].'"
                                                             AND ex.Type = "'.strtolower($data_arr['Type']).'" GROUP BY ex.ID')->result_array();

                return print_r(json_encode($dataExam));

            }

            else if($data_arr['action']=='showDataExamByGroup'){
                $ExamID = $data_arr['ExamID'];

                $data = $this->db->query('SELECT ex.ExamDate, cl.Room, ex.ExamStart, ex.ExamEnd, 
                                                          em1.Name AS Inv1, em2.Name AS Inv2 FROM db_academic.exam ex
                                                          LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                                          LEFT JOIN db_employees.employees em1 ON (em1.NIP = ex.Pengawas1)
                                                          LEFT JOIN db_employees.employees em2 ON (em2.NIP = ex.Pengawas2)
                                                          WHERE ex.ID = "'.$ExamID.'" LIMIT 1 ')->result_array();

                if(count($data)>0){
                    $dataCourse = $this->db->query('SELECT mk.MKCode, mk.NameEng AS CourseEng, mk.Name AS Course, s.ClassGroup
                                                                        FROM db_academic.exam_group exd
                                                                        LEFT JOIN db_academic.schedule s ON (s.ID = exd.ScheduleID)
                                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = exd.ScheduleID)
                                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                                        WHERE exd.ExamID = "'.$ExamID.'" GROUP BY exd.ScheduleID')->result_array();
                    $data[0]['Course'] = $dataCourse;
                }

                return print_r(json_encode($data));
            }

            else if($data_arr['action']=='add'){
                $formData = (array) $data_arr['formData'];
                $dataStudents = (array) $data_arr['dataStudents'];

                print_r($data_arr);
                exit;

                $this->db->insert('db_academic.exam',$formData);
                $insert_id = $this->db->insert_id();

                for($e=0;$e<count($dataStudents);$e++){
                    $dataM = (array) $dataStudents[$e];
                    $dataInsert = array(
                        'ExamID' => $insert_id,
                        'MhswID' => $dataM['MhswID'],
                        'NPM' => $dataM['NPM'],
                        'DB_Students' => $dataM['DB_Students']
                    );
                    $this->db->insert('db_academic.exam_details',$dataInsert);
                }

                return print_r(1);

//                $data = $this->m_api->
            }
            else if($data_arr['action']=='readSchedule'){

                $data = $this->m_api->getScheduleExam(
                    $data_arr['SemesterID'],
                    $data_arr['Type']
                );

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='readDetailStudent'){
                $ExamID = $data_arr['ExamID'];
                $data = $this->m_api->getExamStudent($ExamID);
                return print_r(json_encode($data));
            }

            // ===== Save 2 PDF Exam ======
            else if($data_arr['action']=='save2pdf_Layout'){
                $IDExam = $data_arr['IDExam'];
            }
            else if($data_arr['action']=='save2pdf_DraftQuestions'){
                $IDExam = $data_arr['IDExam'];
                $dataPdf = $this->db->query('SELECT ex.Type, sm.Name AS SemesterName, d.NameEng AS DayEng, 
                                                      coor.Name AS Coordintor,
                                                      cl.Room, ex.ExamDate, ex.ExamStart, ex.ExamEnd, 
                                                      em1.Name AS Pengawas1, em2.Name AS Pengawas2 
                                                      FROM db_academic.exam ex
                                                      LEFT JOIN db_academic.schedule s ON (s.ID = ex.ScheduleID)                                                       
                                                      LEFT JOIN db_employees.employees coor ON (coor.NIP = s.Coordinator)                                                       
                                                      LEFT JOIN db_academic.semester sm ON (sm.ID = ex.SemesterID)
                                                      LEFT JOIN db_academic.days d ON (d.ID = ex.DayID)
                                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                                      LEFT JOIN db_employees.employees em1 ON (em1.NIP = ex.Pengawas1)
                                                      LEFT JOIN db_employees.employees em2 ON (em1.NIP = ex.Pengawas2)
                                                      WHERE ex.ID = "'.$IDExam.'" ')->result_array();
                print_r($dataPdf);

            }
            else if($data_arr['action']=='save2pdf_AnswerSheet'){
                $IDExam = $data_arr['IDExam'];
            }
            else if($data_arr['action']=='save2pdf_NewsEvent'){
                $IDExam = $data_arr['IDExam'];
            }
            else if($data_arr['action']=='save2pdf_AttendanceList'){
                $IDExam = $data_arr['IDExam'];
            }

            else if($data_arr['action']=='deleteExamInExamList'){
                $ExamID = $data_arr['ExamID'];

                // Delete Exam
                $this->db->where('ID', $ExamID);
                $this->db->delete('db_academic.exam');

                $this->db->where('ExamID', $ExamID);
                $this->db->delete(array('db_academic.exam_group','db_academic.exam_details'));

                return print_r(1);
            }

            else if($data_arr['action']=='deleteStuden4EditExam'){

                $this->db->where('ID', $data_arr['Exam_detail_ID']);
                $this->db->delete('db_academic.exam_details');

                return print_r(1);
            }

            else if($data_arr['action']=='editGroupExam'){

                // Update Exam
                $whereExam = array('ID' => $data_arr['ExamID'],'SemesterID' => $data_arr['SemesterID']);

                $updateExam = (array) $data_arr['updateExam'];

                $this->db->where($whereExam);
                $this->db->update('db_academic.exam',$updateExam);

                // Update Group Jika ada
                if(count($data_arr['insert_group'])>0){
                    for($g=0;$g<count($data_arr['insert_group']);$g++){
                        if($data_arr['insert_group'][$g]!=null && $data_arr['insert_group'][$g]!=''){
                            $arrInsert = array(
                                'ExamID' => $data_arr['ExamID'],
                                'ScheduleID' => $data_arr['insert_group'][$g]
                            );

                            $dataCk = $this->db->get_where('db_academic.exam_group',$arrInsert)->result_array();

                            if(count($dataCk)<=0){
                                $this->db->insert('db_academic.exam_group',$arrInsert);
                            }
                        }

                    }
                }

                // Update Details kalo ada
                if(count($data_arr['insert_details'])>0){
                    for($d=0;$d<count($data_arr['insert_details']);$d++){
                        $n = (array) $data_arr['insert_details'][$d];

                        // Get Exam ID
                        $dg = $this->db->select('ID')->get_where('db_academic.exam_group',
                            array('ExamID' => $data_arr['ExamID'], 'ScheduleID' => $n['ScheduleID']),1)->result_array();

                        $arrInsD = array(
                            'ExamID' =>  $data_arr['ExamID'],
                            'ExamGroupID' => $dg[0]['ID'],
                            'ScheduleID' => $n['ScheduleID'],
                            'MhswID' => $n['MhswID'],
                            'NPM' => $n['NPM'],
                            'DB_Students' => $n['DB_Students']
                        );

                        // Cek apakah mahasiswa sudah ada atau belum
                        $cekMhs = $this->db->get_where('db_academic.exam_details',$arrInsD,1)->result_array();
                        if(count($cekMhs)<=0){
                            $this->db->insert('db_academic.exam_details',$arrInsD);
                        }
                    }
                }

                return print_r(1);



            }

            else if($data_arr['action']=='checkBentrokExam'){

                $data = $this->db->query('SELECT ex.*,cl.Room FROM db_academic.exam ex
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                                    WHERE
                                                    ex.SemesterID = "'.$data_arr['SemesterID'].'" 
                                                    AND ex.Type LIKE "'.$data_arr['Type'].'" 
                                                    AND ex.ExamDate = "'.$data_arr['Date'].'" 
                                                    AND ex.ExamClassroomID = "'.$data_arr['RoomID'].'" 
                                                    AND ( ("'.$data_arr['Start'].'" >= ex.ExamStart AND "'.$data_arr['Start'].'" <= ex.ExamEnd) OR 
                                                        ("'.$data_arr['End'].'" >= ex.ExamStart AND "'.$data_arr['End'].'" <= ex.ExamEnd) OR
                                                        ("'.$data_arr['Start'].'" <= ex.ExamStart AND "'.$data_arr['End'].'" >= ex.ExamEnd)
                                                    )
                                                     ')->result_array();

                if(count($data)>0){
                    for($c=0;$c<count($data);$c++){
                        $dataC = $this->db->query('SELECT exg.*,mk.NameEng FROM db_academic.exam_group exg
                                                            LEFT JOIN db_academic.schedule s ON (s.ID = exg.ScheduleID)
                                                            LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                            WHERE exg.ExamID = "'.$data[$c]['ID'].'" GROUP BY exg.ScheduleID ')->result_array();
                        $data[$c]['Course'] = $dataC;
                    }
                }


                return print_r(json_encode($data));
            }

            else if($data_arr['action']=='deleteGroupExam'){
                $ExamID = $data_arr['ExamID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $res = 1;
                $dEx = $this->db->get_where('exam_group',array('ExamID' => $ExamID))->result_array();
                if(count($dEx)<=1){
                    $res = -1;
                    $this->db->where('ID',$ExamID);
                    $this->db->delete('exam');
                }


                $this->db->where(array('ExamID'=>$ExamID,'ScheduleID'=>$ScheduleID));
                $this->db->delete(array('exam_group','exam_details'));

                return print_r($res);

            }

            else if($data_arr['action']=='setExamSchedule'){

                $insert_exam = (array) $data_arr['insert_exam'];

                $this->db->insert('db_academic.exam',$insert_exam);
                $insert_exam_id = $this->db->insert_id();

                $ex_g = (array) $data_arr['insert_group'];

                for($g=0;$g<count($ex_g);$g++){

                    $ar = array(
                        'ExamID' => $insert_exam_id,
                        'ScheduleID' => $ex_g[$g]
                    );

                    $this->db->insert('db_academic.exam_group',$ar);
                }


                $ex_g_d = (array) $data_arr['insert_details'];

                for($s=0;$s<count($ex_g_d);$s++){
                    $ds = (array) $ex_g_d[$s];
                    $dg = $this->db->select('ID')->get_where('db_academic.exam_group',
                        array(
                            'ExamID' => $insert_exam_id,
                            'ScheduleID' => $ds['ScheduleID']
                        )
                        ,1)->result_array();

                    $ds['ExamID'] = $insert_exam_id;
                    $ds['ExamGroupID'] = $dg[0]['ID'];

                    $this->db->insert('db_academic.exam_details',$ds);

                }

                return print_r(1);

            }
        }
    }

    public function crudEmployees(){
        $data_arr = $this->getInputToken();
        
        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $data = $this->db->select('NIP,Name')->get_where('db_employees.employees',array('StatusEmployeeID !=' => -2))->result_array();
                return print_r(json_encode($data));
            }

            else if($data_arr['action']=='addEmployees'){
                $formInsert = (array) $data_arr['formInsert'];

                // Cek apakah NIP sudah digunakan atau belum
                $NIP = $formInsert['NIP'];
                $dataN = $this->db->select('NIP')->get_where('db_employees.employees',
                    array('NIP' => $NIP))->result_array();

                if(count($dataN)>0) {
                    return print_r(0);
                } else {
                    $formInsert['Password_Old'] = md5($formInsert['Password_Old']);
                    $this->db->insert('db_employees.employees',$formInsert);
                    // check fill admin Prodi
                    $ProdiArr = (array) $data_arr['arr_Prodi'];
                    $PositionMain = $formInsert['PositionMain'];
                    $PositionMain = explode('.', $PositionMain);
                    $Position = $PositionMain[1];
                    $Division = $PositionMain[0];
                    if ($Position == 6 || $Division == 15) {
                        if ($Division == 15) {
                            for($i=0;$i<count($ProdiArr);$i++){
                                // update Per ID Prodi
                                $dataSave = array(
                                    'AdminID' => $NIP,
                                );
                                $this->db->where('ID', $ProdiArr[$i]);
                                $this->db->update('db_academic.program_study',$dataSave);

                            }
                        }
                        else
                        {
                            for($i=0;$i<count($ProdiArr);$i++){
                                // update Per ID Prodi
                                $dataSave = array(
                                    'KaprodiID' => $NIP,
                                );
                                $this->db->where('ID', $ProdiArr[$i]);
                                $this->db->update('db_academic.program_study',$dataSave);

                            }
                        }
                    }

                    // insert to db venue for user access
                    $dataSave = array(
                        'NIP' => $NIP,
                        'G_user' => 4,
                    );
                    $this->db->insert('db_reservation.previleges_guser', $dataSave);

                    return print_r(1);
                }
            }

            else if($data_arr['action']=='UpdateEmployees'){

                // Cek apakah delete photo atau tidak
                if($data_arr['DeletePhoto']==1 || $data_arr['DeletePhoto']=='1'){
                    $pathPhoto = './uploads/employees/'.$data_arr['LastPhoto'];
                    if(file_exists($pathPhoto)){
                        unlink($pathPhoto);
                    }

                }

                $formUpdate = (array) $data_arr['formUpdate'];
                $formUpdate['Password_Old'] = md5($formUpdate['Password_Old']);

                $this->db->where('NIP', $data_arr['NIP']);
                $this->db->update('db_employees.employees',$formUpdate);

                // check fill admin Prodi / Ka prodi
                $PositionMain = $formUpdate['PositionMain'];
                $PositionMain = explode('.', $PositionMain);
                $Position = $PositionMain[1];
                $Division = $PositionMain[0];
                if ($Position == 6 || $Division == 15) {
                    $ProdiArr = (array) $data_arr['arr_Prodi'];
                    if ($Division == 15) {
                        $dataSave = array(
                            'AdminID' => null,
                        );
                        $this->db->where('AdminID', $data_arr['NIP']);
                        $this->db->update('db_academic.program_study',$dataSave);
                        for($i=0;$i<count($ProdiArr);$i++){
                            // update Per ID Prodi
                            $dataSave = array(
                                'AdminID' => $data_arr['NIP'],
                            );
                            $this->db->where('ID', $ProdiArr[$i]);
                            $this->db->update('db_academic.program_study',$dataSave);

                        }
                    }
                    else
                    {

                        $dataSave = array(
                            'KaprodiID' => null,
                        );
                        $this->db->where('KaprodiID', $data_arr['NIP']);
                        $this->db->update('db_academic.program_study',$dataSave);
                        for($i=0;$i<count($ProdiArr);$i++){
                            // update Per ID Prodi
                            $dataSave = array(
                                'KaprodiID' => $data_arr['NIP'],
                            );
                            $this->db->where('ID', $ProdiArr[$i]);
                            $this->db->update('db_academic.program_study',$dataSave);

                        }
                    }
                }

                return print_r(1);

            }

            else if($data_arr['action']=='deleteEmployees'){
                $this->db->set('StatusEmployeeID',-2);
                $this->db->where('NIP', $data_arr['NIP']);
                $this->db->update('db_employees.employees');

                return print_r(1);
            }
            else if($data_arr['action']=='deletePermanantEmployees'){
                $this->db->where('NIP', $data_arr['NIP']);
                $this->db->delete('db_employees.employees');
                return print_r(1);
            }

            else if($data_arr['action']=='updateEmailEmployees'){

                $fillM = ($data_arr['StatusEmployeeID']==4 || $data_arr['StatusEmployeeID']=='4') ? 'Email' : 'EmailPU' ;
                $this->db->set($fillM, $data_arr['Email']);
                $this->db->where('NIP', $data_arr['NIP']);
                $this->db->update('db_employees.employees');

                return print_r(1);

            }

            else if($data_arr['action']=='showLecturerMonitoring'){

                $SemesterID = $data_arr['SemesterID'];
                $StatusEmployeeID = $data_arr['StatusEmployeeID'];
                $Start = $data_arr['Start'];
                $End = $data_arr['End'];

                $data = $this->m_api->showLecturerMonitoring($SemesterID,$StatusEmployeeID,$Start,$End);

                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='getEmployeesFiles'){
                $NIP = $data_arr['NIP'];
                $data = $this->db->query('SELECT em.NIP AS NIPLec, em.Name, em.Photo, f.* FROM db_employees.employees em 
                                                  LEFT JOIN db_employees.files f ON (f.NIP = em.NIP)
                                                  WHERE em.NIP = "'.$NIP.'" ')->result_array();

                return print_r(json_encode($data));
            }
        }

    }


    public function editAcademicData(){

        $data_arr = $this->getInputToken();

        if($data_arr['action']=='editAcademicS1'){
            $formInsert = (array) $data_arr['formInsert'];
            $type = 'S1';

            $NIP = $formInsert['NIP'];
            $NoIjazah = strtoupper($formInsert['NoIjazah']);
            $NameUniversity = strtoupper($formInsert['NameUniversity']);
            $DateIjazah = $formInsert['IjazahDate'];
            $Major = strtoupper($formInsert['Major']);
            $ProgramStudy = strtoupper($formInsert['ProgramStudy']);
            $Grade = $formInsert['Grade'];
            $TotalCredit = $formInsert['TotalCredit'];
            $TotalSemester = $formInsert['TotalSemester'];
            $fileName = $formInsert['linkijazahs1'];
            $file_trans = $formInsert['linktranscripts1'];

            $dataAcademic = $this->db->get_where('db_employees.employees_academic',array('NIP'=>$NIP,'IjazahFile'=>$fileName,'TranscriptFile'=>$file_trans))->result_array();

            if(count($dataAcademic)>0){ // jika ada data di table files (update)

                    $dataUpdate = array(
                            'TypeAcademic' => $type,
                            'NoIjazah' => $NoIjazah,
                            'DateIjazah' => $DateIjazah,
                            'NameUniversity' => $NameUniversity,
                            'Major' => $Major,
                            'ProgramStudy' => $ProgramStudy,
                            'Grade' => $Grade,
                            'TotalCredit' => $TotalCredit,
                            'TotalSemester' => $TotalSemester,
                            'IjazahFile' => $fileName,
                            'TranscriptFile' => $file_trans
                    );

                    $this->db->where('NIP', $NIP);
                    $this->db->where('IjazahFile', $fileName);
                    $this->db->where('TranscriptFile', $file_trans);
                    $this->db->update('db_employees.employees_academic', $dataUpdate);
                    return print_r(1);
                } else {

                    $dataSave = array(
                            'NIP' => $NIP,
                            'TypeAcademic' => $type,
                            'NoIjazah' => $NoIjazah,
                            'DateIjazah' => $DateIjazah,
                            'NameUniversity' => $NameUniversity,
                            'Major' => $Major,
                            'ProgramStudy' => $ProgramStudy,
                            'Grade' => $Grade,
                            'TotalCredit' => $TotalCredit,
                            'TotalSemester' => $TotalSemester,
                            'IjazahFile' => $fileName,
                            'TranscriptFile' => $file_trans
                    );
                    $this->db->insert('db_employees.employees_academic', $dataSave);
                    return print_r(1);
            }
        }
        else if($data_arr['action']=='editAcademicS2'){
            $formInsert = (array) $data_arr['formInsert'];
            $type = 'S2';

            $NIP = $formInsert['NIP'];
            $NoIjazah = strtoupper($formInsert['NoIjazah']);
            $NameUniversity = strtoupper($formInsert['NameUniversity']);
            $DateIjazah = $formInsert['IjazahDate'];
            $Major = strtoupper($formInsert['Major']);
            $ProgramStudy = strtoupper($formInsert['ProgramStudy']);
            $Grade = $formInsert['Grade'];
            $TotalCredit = $formInsert['TotalCredit'];
            $TotalSemester = $formInsert['TotalSemester'];
            $fileName = $formInsert['linkijazahs1'];
            $file_trans = $formInsert['linktranscripts1'];

            $dataAcademic = $this->db->get_where('db_employees.employees_academic',array('NIP'=>$NIP,'IjazahFile'=>$fileName,'TranscriptFile'=>$file_trans))->result_array();

            if(count($dataAcademic)>0){

                    $dataUpdate = array(
                            'TypeAcademic' => $type,
                            'NoIjazah' => $NoIjazah,
                            'DateIjazah' => $DateIjazah,
                            'NameUniversity' => $NameUniversity,
                            'Major' => $Major,
                            'ProgramStudy' => $ProgramStudy,
                            'Grade' => $Grade,
                            'TotalCredit' => $TotalCredit,
                            'TotalSemester' => $TotalSemester,
                            'IjazahFile' => $fileName,
                            'TranscriptFile' => $file_trans
                    );

                    $this->db->where('NIP', $NIP);
                    $this->db->where('IjazahFile', $fileName);
                    $this->db->where('TranscriptFile', $file_trans);
                    $this->db->update('db_employees.employees_academic', $dataUpdate);
                    return print_r(1);

                } else {

                    $dataSave = array(
                            'NIP' => $NIP,
                            'TypeAcademic' => $type,
                            'NoIjazah' => $NoIjazah,
                            'DateIjazah' => $DateIjazah,
                            'NameUniversity' => $NameUniversity,
                            'Major' => $Major,
                            'ProgramStudy' => $ProgramStudy,
                            'Grade' => $Grade,
                            'TotalCredit' => $TotalCredit,
                            'TotalSemester' => $TotalSemester,
                            'IjazahFile' => $fileName,
                            'TranscriptFile' => $file_trans
                    );
                    $this->db->insert('db_employees.employees_academic', $dataSave);
                    return print_r(1);

            }
        }
        else if($data_arr['action']=='editAcademicS3'){

            $formInsert = (array) $data_arr['formInsert'];
            $type = 'S3';

            $NIP = $formInsert['NIP'];
            $NoIjazah = strtoupper($formInsert['NoIjazah']);
            $NameUniversity = strtoupper($formInsert['NameUniversity']);
            $DateIjazah = $formInsert['IjazahDate'];
            $Major = strtoupper($formInsert['Major']);
            $ProgramStudy = strtoupper($formInsert['ProgramStudy']);
            $Grade = $formInsert['Grade'];
            $TotalCredit = $formInsert['TotalCredit'];
            $TotalSemester = $formInsert['TotalSemester'];
            $fileName = $formInsert['linkijazahs1'];
            $file_trans = $formInsert['linktranscripts1'];

            $dataAcademic = $this->db->get_where('db_employees.employees_academic',array('NIP'=>$NIP,'IjazahFile'=>$fileName,'TranscriptFile'=>$file_trans))->result_array();

            if(count($dataAcademic)>0){ // jika ada data di table files (update)

                    $dataUpdate = array(
                            'TypeAcademic' => $type,
                            'NoIjazah' => $NoIjazah,
                            'DateIjazah' => $DateIjazah,
                            'NameUniversity' => $NameUniversity,
                            'Major' => $Major,
                            'ProgramStudy' => $ProgramStudy,
                            'Grade' => $Grade,
                            'TotalCredit' => $TotalCredit,
                            'TotalSemester' => $TotalSemester,
                            'IjazahFile' => $fileName,
                            'TranscriptFile' => $file_trans
                    );
                    $this->db->where('NIP', $NIP);
                    $this->db->where('IjazahFile', $fileName);
                    $this->db->where('TranscriptFile', $file_trans);
                    $this->db->update('db_employees.employees_academic', $dataUpdate);
                    return print_r(1);

                } else {

                    $dataSave = array(
                            'NIP' => $NIP,
                            'TypeAcademic' => $type,
                            'NoIjazah' => $NoIjazah,
                            'DateIjazah' => $DateIjazah,
                            'NameUniversity' => $NameUniversity,
                            'Major' => $Major,
                            'ProgramStudy' => $ProgramStudy,
                            'Grade' => $Grade,
                            'TotalCredit' => $TotalCredit,
                            'TotalSemester' => $TotalSemester,
                            'IjazahFile' => $fileName,
                            'TranscriptFile' => $file_trans
                    );
                    $this->db->insert('db_employees.employees_academic', $dataSave);
                    return print_r(1);
            }
        }
        else if($data_arr['action']=='EditFilesDocument'){

            $formInsert = (array) $data_arr['formInsert'];

            $NIP = $formInsert['formNIP'];
            $NoDocument = strtoupper($formInsert['NoDocument']);
            $DescriptionFile = $formInsert['DescriptionFile'];
            $DateDocument = $formInsert['DateDocument'];
            $type = $formInsert['typeotherfiles'];
            $idlinkfiles = $formInsert['idlinkfiles'];
            $linkotherfile = $formInsert['linkotherfile'];
            
            $dataUpdate = array(
                        'No_Document' => $NoDocument,
                        'Date_Files' => $DateDocument,
                        'Description_Files' => $DescriptionFile,
                        'LinkFiles' => $linkotherfile
                    );
            $this->db->where('NIP', $NIP);
            $this->db->where('ID', $idlinkfiles);
            $this->db->update('db_employees.files', $dataUpdate);
            return print_r(1);
        }
    }

    public function crudAcademicData(){

        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){

            if($data_arr['action']=='addAcademicS1'){

                $formInsert = (array) $data_arr['formInsert'];
                $type = 'S1';
                
                $NIP = $formInsert['NIP'];
                $NoIjazah = strtoupper($formInsert['NoIjazah']);
                $NameUniversity = strtoupper($formInsert['NameUniversity']);
                $DateIjazah = $formInsert['IjazahDate'];
                $Major = strtoupper($formInsert['Major']);
                $ProgramStudy = strtoupper($formInsert['ProgramStudy']);
                $Grade = $formInsert['Grade'];
                $TotalCredit = $formInsert['TotalCredit'];
                $TotalSemester = $formInsert['TotalSemester'];
                $fileName = $formInsert['fileName'];
                $file_trans = $formInsert['file_trans'];
                
                $dataSave = array(
                                'NIP' => $NIP,
                                'TypeAcademic' => $type,
                                'NoIjazah' => $NoIjazah,
                                'DateIjazah' => $DateIjazah,
                                'NameUniversity' => $NameUniversity,
                                'Major' => $Major,
                                'ProgramStudy' => $ProgramStudy,
                                'Grade' => $Grade,
                                'TotalCredit' => $TotalCredit,
                                'TotalSemester' => $TotalSemester,
                                'IjazahFile' => $fileName,
                                'TranscriptFile' => $file_trans
                            );
                $this->db->insert('db_employees.employees_academic', $dataSave);
                return print_r(1);
            } 
            else if($data_arr['action']=='addAcademicS2'){
                $formInsert = (array) $data_arr['formInsert'];
                $type = 'S2';
                
                $NIP = $formInsert['NIP'];
                $NoIjazah = strtoupper($formInsert['NoIjazah']);
                $NameUniversity = strtoupper($formInsert['NameUniversity']);
                $DateIjazah = $formInsert['IjazahDate'];
                $Major = strtoupper($formInsert['Major']);
                $ProgramStudy = strtoupper($formInsert['ProgramStudy']);
                $Grade = $formInsert['Grade'];
                $TotalCredit = $formInsert['TotalCredit'];
                $TotalSemester = $formInsert['TotalSemester'];
                $fileName = $formInsert['fileName'];
                $file_trans = $formInsert['file_trans'];

                $dataSave = array(
                               'NIP' => $NIP,
                                'TypeAcademic' => $type,
                                'NoIjazah' => $NoIjazah,
                                'DateIjazah' => $DateIjazah,
                                'NameUniversity' => $NameUniversity,
                                'Major' => $Major,
                                'ProgramStudy' => $ProgramStudy,
                                'Grade' => $Grade,
                                'TotalCredit' => $TotalCredit,
                                'TotalSemester' => $TotalSemester,
                                'IjazahFile' => $fileName,
                                'TranscriptFile' => $file_trans
                            );
                $this->db->insert('db_employees.employees_academic', $dataSave);
                return print_r(1);
            }
            else if($data_arr['action']=='addAcademicS3'){
                $formInsert = (array) $data_arr['formInsert'];
                $type = 'S3';
                
                $NIP = $formInsert['NIP'];
                $NoIjazah = strtoupper($formInsert['NoIjazah']);
                $NameUniversity = strtoupper($formInsert['NameUniversity']);
                $DateIjazah = $formInsert['IjazahDate'];
                $Major = strtoupper($formInsert['Major']);
                $ProgramStudy = strtoupper($formInsert['ProgramStudy']);
                $Grade = $formInsert['Grade'];
                $TotalCredit = $formInsert['TotalCredit'];
                $TotalSemester = $formInsert['TotalSemester'];
                $fileName = $formInsert['fileName'];
                $file_trans = $formInsert['file_trans'];

                $dataSave = array(
                               'NIP' => $NIP,
                                'TypeAcademic' => $type,
                                'NoIjazah' => $NoIjazah,
                                'DateIjazah' => $DateIjazah,
                                'NameUniversity' => $NameUniversity,
                                'Major' => $Major,
                                'ProgramStudy' => $ProgramStudy,
                                'Grade' => $Grade,
                                'TotalCredit' => $TotalCredit,
                                'TotalSemester' => $TotalSemester,
                                'IjazahFile' => $fileName,
                                'TranscriptFile' => $file_trans
                            );
                $this->db->insert('db_employees.employees_academic', $dataSave);
                return print_r(1);
            }
            else if($data_arr['action']=='AddFilesDocument'){
                $formInsert = (array) $data_arr['formInsert'];

                $NIP = $formInsert['NIP'];
                $IDuser = $this->session->userdata('NIP');
                $NoDocument = strtoupper($formInsert['NoDocument']);
                $DateDocument = $formInsert['DateDocument'];
                $type = $formInsert['type'];
                $DescriptionFile = $formInsert['DescriptionFile'];
                $fileName = $formInsert['fileName'];

                $Get_MasterFiles = $this->m_master->MasterfileStatus($type);
        
                $dataSave = array(
                                'NIP' => $NIP,
                                'TypeFiles' => $Get_MasterFiles[0]['ID'],
                                'No_Document' => $NoDocument,
                                'Date_Files' => $DateDocument,
                                'Description_Files' => $DescriptionFile,
                                'LinkFiles' => $fileName,
                                'UserCreate' => $IDuser
                            );
                $this->db->insert('db_employees.files',$dataSave);
                return print_r(1);

            }

        }

     }


     public function upload_fileAcademic($fileName, $formData){

            //$fileName = $this->input->get('fileName');
            $Colom = $this->input->get('c');
            $User = $this->input->get('u');
            print_r($fileName);
            print_r($formData);
            

            $config['upload_path']          = './uploads/files/';
            $config['allowed_types']        = '*';
            $config['max_size']             = 8000; // 8 mb
            $config['file_name']            = $fileName;

            if(is_file('./uploads/files/'.$fileName)){
                    unlink('./uploads/files/'.$fileName);
            }
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('userfile')){
                $error = array('error' => $this->upload->display_errors());
                return print_r(json_encode($error));
            }
            else {
                    
                $success = array('success' => $this->upload->data());
                $success['success']['formGrade'] = 0;
                // Cek apakah di db sudah ada
                $dataNIP = $this->db->get_where('db_employees.files',array('NIP'=>$User))->result_array();
                $dataUpdate = array(
                    $Colom => $fileName
                );
                if(count($dataNIP)>0){
                    $this->db->where('NIP', $User);
                    $this->db->update('db_employees.files',$dataUpdate);
                } else {
                    $dataUpdate['NIP'] = $User;
                    $this->db->insert('db_employees.files',$dataUpdate);
                }
                return print_r(json_encode($success));
            }

     }
    

    public function getProvinsi()
    {
        $generate = $this->m_master->showData_array('db_admission.province');
        echo json_encode($generate);
    }

    public function getRegionByProv()
    {
        $input = $this->getInputToken();
        $generate = $this->m_master->getRegionByProv($input['selectProvinsi']);
        echo json_encode($generate);
    }

    public function getDistrictByRegion()
    {
        $input = $this->getInputToken();
        $generate = $this->m_master->getDistrictByRegion($input['selectRegion']);
        echo json_encode($generate);
    }

    public function getTypeSekolah()
    {
        $generate = $this->m_master->getTypeSekolah();
        echo json_encode($generate);
    }

    public function getNotification()
    {
        $generateCount = $this->m_master->CountgetNotification();
        $generate = $this->m_master->getNotification();
        echo json_encode(array('count' => $generateCount, 'data'=>$generate));
    }

    public function getBasePaymentTypeSelectOption()
    {
        $generate = $this->m_master->showData_array('db_finance.payment_type');
        echo json_encode($generate);
    }

    public function getNotification_divisi()
    {
        $generateCount = $this->m_master->CountgetNotificationDivisi();
        $generate = $this->m_master->getNotificationDivisi();
        //print_r($generate);
        // $generate = json_encode($generate);

        $output = array(
            'count'  => $generateCount,
            'data'   => $generate,
        );

        echo json_encode($output);

    }

    public function getSMAWilayahApproval()
    {
        $generate = $this->m_master->getSMAWilayahApproval();
        echo json_encode($generate);
    }

    public function crudScore(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $ScheduleID = $data_arr['ScheduleID'];
                $SemesterID = $data_arr['SemesterID'];
                $data = $this->m_api->__getScore($SemesterID,$ScheduleID);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='grade'){
                $Score = $data_arr['Score'];
                $data = $this->m_api->__getGrade($Score);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='checkGrade'){
                $data = $this->db->get_where('db_academic.grade_course',
                    array('ScheduleID'=>$data_arr['ID']),1)->result_array();

                $result = array(
                    'Status' => 0,
                    'Details' => $data
                );
                if(count($data)>0){
                    if($data[0]['Silabus']!='' && $data[0]['Silabus']!=null &&
                        $data[0]['SAP']!='' && $data[0]['SAP']!=null &&
                        $data[0]['Assigment']!='' && $data[0]['Assigment']!=null &&
                        $data[0]['UTS']!='' && $data[0]['UTS']!=null &&
                        $data[0]['UAS']!='' && $data[0]['UAS']!=null &&
                        $data[0]['Status']=='2'){
                        $result['Status']=1;
                        $result['Details']=$data[0];
                    }
                }

                return print_r(json_encode($result));
            }
            else if($data_arr['action']=='update'){

                // Update Schedule
                $this->db->set('TotalAssigment', $data_arr['TotalAssigment']);
                $this->db->where('ID', $data_arr['ScheduleID']);
                $this->db->update('db_academic.schedule');

                $formUpdate = (array) $data_arr['formUpdate'];
                for($s=0;$s<count($formUpdate);$s++){
                    $dataF = (array) $formUpdate[$s];

                    $DB_Student = $dataF['DB_Student'];
                    $ID = $dataF['ID'];

                    $dataToUpdate = (array)$dataF['dataForm'];

//                    print_r($dataToUpdate);

                    $this->db->where('ID', $ID);
                    $this->db->update($DB_Student.'.study_planning',$dataToUpdate);
                }

                return print_r(1);

            }
            else if($data_arr['action']=='getGrade'){
                $ScheduleID = $data_arr['ScheduleID'];
                $data = $this->m_api->__getGradeSchedule($ScheduleID);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='gradeUpdate'){
//                $this->db->set('Status', $data_arr['Status']);
                $data_update = array('ReasonNotApprove' => '' , 'Status' => $data_arr['Status']);
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.grade_course',$data_update);
                return print_r(1);
            }
            else if($data_arr['action']=='dataCourse'){

                $data = $this->m_api->getDataCourse2Score($data_arr['SemesterID']
                    ,$data_arr['ProdiID'],$data_arr['CombinedClasses'],$data_arr['IsSemesterAntara']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='updateNotApprove'){

                $data_update = array(
                    'ReasonNotApprove' => $data_arr['ReasonNotApprove'],
                    'Status' => $data_arr['Status']
                );

                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.grade_course', $data_update);

                return print_r(1);

            }
        }
    }

    public function getBaseDiscountSelectOption()
    {
        $generate = $this->m_master->showData_array('db_finance.discount');
        echo json_encode($generate);

    }

    public function crudAttendance(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='read'){
                $data = $this->m_api->__getDataAttendance($data_arr['ScheduleID']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='getAttendance'){

                $data = $this->m_api->__getAttendanceSchedule($data_arr['AttendanceID']);

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='getAttdLecturers'){
                $ID = $data_arr['ID'];
                $No = $data_arr['No'];
                $data = $this->db->get_where('db_academic.attendance',
                    array('ID'=>$ID))->result_array();


                $coor = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule s 
                                            LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                            WHERE s.ID = "'.$data[0]['ScheduleID'].'"
                                              ')->result_array();

                $teamt = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule_team_teaching stt 
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                    WHERE stt.ScheduleID = "'.$data[0]['ScheduleID'].'" 
                                                    ')->result_array();

                if(count($teamt)>0){
                    for($t=0;$t<count($teamt);$t++){
                        array_push($coor,$teamt[$t]);
                    }
                }

                $res = array(
//                    'NIP' => $data[0]['NIP'.$No],
                    'BAP' => $data[0]['BAP'.$No],
//                    'Date' => $data[0]['Date'.$No],
//                    'In' => $data[0]['In'.$No],
//                    'Out' => $data[0]['Out'.$No],
                    'Details' => $data,
                    'DetailLecturers' => $coor
                );
                return print_r(json_encode($res));
            }
            else if($data_arr['action']=='UpdtAttdLecturers'){

                $ID = $data_arr['ID'];
                $No = $data_arr['No'];

                // Cek apakah sudah ada diabsen atau belum
                $insertAttdLecturer = (array) $data_arr['insertAttdLecturer'];
                $dataAttdLec = $this->db->get_where('db_academic.attendance_lecturers',
                    array(
                        'ID_Attd' => $insertAttdLecturer['ID_Attd'],
                        'NIP' => $insertAttdLecturer['NIP'],
                        'Meet' => $insertAttdLecturer['Meet']
                    ),1)->result_array();

                if(count($dataAttdLec)<=0){
                    $this->db->insert('db_academic.attendance_lecturers',(array) $data_arr['insertAttdLecturer']);
                } else {
                    // Update Attendance Lecturer
                    $this->db->where('ID', $dataAttdLec[0]['ID']);
                    $this->db->update('db_academic.attendance_lecturers', (array) $data_arr['insertAttdLecturer']);
                }

                $dataUpdate = array(
                    'Meet'.$No => '1',
                    'BAP'.$No => $data_arr['BAP']
                );

                $this->db->where('ID', $ID);
                $this->db->update('db_academic.attendance', $dataUpdate);

                return print_r(1);
            }
            else if($data_arr['action']=='DeleteAttdLecturers'){
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.attendance_lecturers');
                return print_r(1);
            }
            else if($data_arr['action']=='filterPresensi'){

                if($data_arr['CombinedClasses']=='0'){

                    $data = $this->db->query('SELECT s.* FROM db_academic.schedule s 
                                              LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                              WHERE s.SemesterID = "'.$data_arr['SemesterID'].'" 
                                              AND CombinedClasses = "0" 
                                              AND sdc.ProdiID = "'.$data_arr['ProdiID'].'" 
                                              ORDER BY s.ClassGroup ASC')->result_array();


                    $result = $data;

                } else {
                    $data_where = array(
                        'SemesterID' => $data_arr['SemesterID'],
                        'CombinedClasses' => '1'
                    );
                    $data = $this->db->order_by('ClassGroup', 'ASC')
                        ->get_where('db_academic.schedule',
                            $data_where)->result_array();

                    $result = $data;
                }

                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='getAttdStudents'){
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $SDID = $data_arr['SDID'];
                $Meeting = $data_arr['Meeting'];
                $data = $this->m_api->__getStudensAttd2Edit($SemesterID,$ScheduleID,$SDID,$Meeting);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='getAttdStudentsToEdit'){
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $SDID = $data_arr['SDID'];
                $Meeting = $data_arr['Meeting'];
                $data = $this->m_api->__getStudensAttd($SemesterID,$ScheduleID,$SDID,$Meeting);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='addAttdStudents'){
                $dataUpdate = (array) $data_arr['dataUpdate'];
//                print_r($dataUpdate);
                for($u=0;$u<count($dataUpdate);$u++){
                    $dataObj = (array) $dataUpdate[$u];

                    $d_updt = array(
                        'M'.$dataObj['Meeting'] => $dataObj['Status'],
                        'D'.$dataObj['Meeting'] => $dataObj['Description']
                    );

                    $this->db->where('ID', $dataObj['ID']);
                    $this->db->update('db_academic.attendance_students', $d_updt);
                }

                return print_r(1);
            }

            else if($data_arr['action']=='blastAttendance'){
                $SemesterID = $data_arr['SemesterID'];
                $Meet = $data_arr['Meet'];
                $Status = $data_arr['Status'];

                $set = ' M'.$Meet.' = '.$Status.' ';

                $this->db->query('UPDATE db_academic.attendance_students
                                    SET '.$set.'
                                    WHERE ID_Attd IN (SELECT ID FROM db_academic.attendance 
                                    WHERE SemesterID = "'.$SemesterID.'")');

                return print_r(1);

            }

            else if($data_arr['action']=='monitoringLecturer'){
                $SemesterID = $data_arr['SemesterID'];
                $ProdiID = $data_arr['ProdiID'];

                $data = $this->m_api->__getMonitoringAttdLecturer($SemesterID,$ProdiID);

                return print_r(json_encode($data));
            }

            else if ($data_arr['action']=='DeleteAttendance'){

                // Delete Attendance Lecturer
                $this->db->where(array('ID_Attd' => $data_arr['ID_Attd'], 'Meet' => $data_arr['Meet']));
                $this->db->delete('db_academic.attendance_lecturers');

                // Set Null di Student
                $this->db->set('M'.$data_arr['Meet'], null);
                $this->db->where('ID_Attd', $data_arr['ID_Attd']);
                $this->db->update('db_academic.attendance_students');

                // Set Null di Attendance
                $this->db->set('Meet'.$data_arr['Meet'], null);
                $this->db->where('ID', $data_arr['ID_Attd']);
                $this->db->update('db_academic.attendance');

                return print_r(1);

            }
            else if($data_arr['action']=='getStdAttendance'){

                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $data = $this->m_api->getStudentsAttendance($SemesterID,$ScheduleID);

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='delAttendanceStudents'){
                $NPM = $data_arr['NPM'];
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $ID_Attd = $data_arr['ID_Attd'];
                $ID_Attd_S = $data_arr['ID_Attd_S'];
                $db_student = $data_arr['db_student'];

                // Cek apakah double di attendance
                $dataAtdStd = $this->db
                    ->get_where('db_academic.attendance_students',$arrCheck = array(
                        'NPM' => $NPM,'ID_Attd' => $ID_Attd))->result_array();
                if(count($dataAtdStd)>1){
                    $this->db->where('ID', $ID_Attd_S);
                    $this->db->delete('db_academic.attendance_students');
                    $res = array(
                        'Status' => 1,
                        'Msg' => 'Delete Success'
                    );
                } else{
                    // Cek apakah di KSM tidak ada, jika ada maka tidak dapat di hapus
                    $dataKSM = $this->db->get_where($db_student.'.study_planning',array(
                        'NPM' => $NPM, 'SemesterID' => $SemesterID, 'ScheduleID' => $ScheduleID
                    ))->result_array();
                    if(count($dataKSM)>0){
                        $res = array(
                            'Status' => 0,
                            'Msg' => 'Schedule in KSM is Exist'
                        );
                    } else {
                        $this->db->where('ID', $ID_Attd_S);
                        $this->db->delete('db_academic.attendance_students');
                        $res = array(
                            'Status' => 1,
                            'Msg' => 'Delete Success'
                        );
                    }
                }

                return print_r(json_encode($res));

            }
            else if($data_arr['action']=='addAttendanceStudents'){

                $NPM = $data_arr['NPM'];
                $DB_Student = $data_arr['DB_Student'];
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                // Cek apakah di KSM ada
                $dataKSM = $this->db->get_where($DB_Student.'.study_planning',array(
                    'NPM' => $NPM,
                    'SemesterID' => $SemesterID,
                    'ScheduleID' => $ScheduleID
                ))->result_array();

                if(count($dataKSM)>0){
                    // Cek apakah sudah ada di attendance
                    $dataAttdS = $this->db->query('SELECT * FROM db_academic.attendance attd
                                                      LEFT JOIN db_academic.attendance_students attds ON (attds.ID_Attd = attd.ID)
                                                      WHERE attd.SemesterID = "'.$SemesterID.'" 
                                                      AND attd.ScheduleID = "'.$ScheduleID.'"
                                                       AND attds.NPM = "'.$NPM.'" ')->result_array();

                    if(count($dataAttdS)>0){
                        $res = array(
                            'Status' => 0,
                            'Msg' => 'Student Attendance is Exist'
                        );
                    } else {
                        $dataAttd = $this->db->query('SELECT * FROM db_academic.attendance attd
                                                      WHERE attd.SemesterID = "'.$SemesterID.'" 
                                                      AND attd.ScheduleID = "'.$ScheduleID.'"
                                                       ')->result_array();
                        if(count($dataAttd)>0){
                            for($r=0;$r<count($dataAttd);$r++){
                                $arrIns = array(
                                    'ID_Attd' => $dataAttd[$r]['ID'],
                                    'NPM' => $NPM
                                );
                                $this->db->insert('db_academic.attendance_students',$arrIns);
                            }
                        }
                        $res = array(
                            'Status' => 1,
                            'Msg' => 'Adding Success'
                        );
                    }

                } else {
                    $res = array(
                        'Status' => 0,
                        'Msg' => 'Schedule Not Exist, please check KSM'
                    );
                }

                return print_r(json_encode($res));

            }
        }
    }

    public function crudScheduleExchange(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='readExchange'){
                $ID_Attd = $data_arr['ID_Attd'];
                $ScheduleID = $data_arr['ScheduleID'];
                $SDID = $data_arr['SDID'];
                $Meeting = $data_arr['Meeting'];

                $data = $this->m_api->__getdataExchange($ID_Attd,$ScheduleID,$SDID,$Meeting);

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='addSceduleEx'){
                $dataInsert = (array) $data_arr['dataInsert'];

                // Cek Apakah sudah ada atau belum
                $dataWhere = array(
                    'ID_Attd' => $dataInsert['ID_Attd'],
                    'Meeting' => $dataInsert['Meeting']
                );

                $dataC = $this->db->get_where('db_academic.schedule_exchange',
                    $dataWhere,1)->result_array();

                if(count($dataC)>0){
                    $dataUpdate = array(
                        'NIP' => $dataInsert['NIP'],
                        'ClassroomID' => $dataInsert['ClassroomID'],
                        'Date' => $dataInsert['Date'],
                        'DayID' => $dataInsert['DayID'],
                        'StartSessions' => $dataInsert['StartSessions'],
                        'EndSessions' => $dataInsert['EndSessions'],
                        'Status' => $dataInsert['Status']
                    );

                    $this->db->where('ID', $dataC[0]['ID']);
                    $this->db->update('db_academic.schedule_exchange', $dataUpdate);

                } else {
                    $this->db->insert('db_academic.schedule_exchange',$dataInsert);
                }

                return print_r(1);

            }
            else if($data_arr['action']=='deleteSceduleEx'){
                $dataWhere = array(
                    'ID_Attd' => $data_arr['ID_Attd'],
                    'Meeting' => $data_arr['Meeting']
                );
                $dataC = $this->db->get_where('db_academic.schedule_exchange',
                    $dataWhere)->result_array();

                if(count($dataC)>0){
                    $this->db->delete('db_academic.schedule_exchange',$dataWhere);
                }

                return print_r(1);

            }
            else if($data_arr['action']=='readBySemesterID'){
                $SemesterID = $data_arr['SemesterID'];
                $Status = $data_arr['Status'];
                $Start = $data_arr['Start'];
                $End = $data_arr['End'];
                $data = $this->m_api->getExchangeBySmtID($SemesterID,$Status,$Start,$End);
                return print_r(json_encode($data));
            }
        }
    }

    public function crudKRSOnline(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='checkCountSeat'){
                $whereCheck = (array) $data_arr['whereCheck'];
                $query = $this->db->get_where('db_academic.std_krs', $whereCheck)->result_array();
                return print_r(json_encode($query));
            }
            else if($data_arr['action']=='add'){
                $formData = (array) $data_arr['formData'];
                $this->db->insert('db_academic.std_krs', $formData);
                $insert_id = $this->db->insert_id();

                // Masukan ke dalam attd_students
                $SemesterID = $formData['SemesterID'];
                $ScheduleID = $formData['ScheduleID'];
                $dataAttd = $this->db->get_where('db_academic.attendance',
                    array( 'SemesterID' => $SemesterID,
                        'ScheduleID' => $ScheduleID)
                )->result_array();

                if(count($dataAttd)>0){
                    $NPM = $formData['NPM'];

                    for($a=0;$a<count($dataAttd);$a++){
                        $dataInsAttd = array(
                            'ID_Attd' => $dataAttd[$a]['ID'],
                            'NPM' => $NPM
                        );
                        $this->db->insert('db_academic.attendance_students', $dataInsAttd);
                    }
                }

                // Tambahkan ke KSMnya
                $this->m_api->insertToMainKRS($insert_id,$formData['TypeSP'],$data_arr['Student_DB']);
                return print_r($insert_id);
            }
            else if($data_arr['action']=='deleteKRS'){

                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $NPM = $data_arr['NPM'];
                $Student_DB = $data_arr['Student_DB'];

                // Cek di attendance untuk di delete
                $dataAttd = $this->db->get_where('db_academic.attendance',
                    array('SemesterID' => $SemesterID,
                        'ScheduleID' => $ScheduleID))->result_array();

                if(count($dataAttd)>0){
                    for($a=0;$a<count($dataAttd);$a++){
                        $this->db->where(array('ID_Attd'=>$dataAttd[0]['ID'],'NPM' => $NPM));
                        $this->db->delete('db_academic.attendance_students');
                    }
                }

                // tabel std_krs
                $dataStdKRS = $this->db->get_where('db_academic.std_krs',array(
                    'SemesterID' => $SemesterID,
                    'ScheduleID' => $ScheduleID,
                    'NPM' => $NPM
                ))->result_array();
                if(count($dataStdKRS)>0){
                    for($k=0;$k<count($dataStdKRS);$k++){
                        $this->db->where('KRSID' , $dataStdKRS[$k]['ID']);
                        $this->db->delete('db_academic.std_krs_comment');

                        $this->db->where('ID',$dataStdKRS[$k]['ID']);
                        $this->db->delete('db_academic.std_krs');
                    }
                }

                // Cek apakah sudah jadi KSM ataubelum
                $this->db->where(array('SemesterID'=>$SemesterID,'NPM' => $NPM, 'ScheduleID' => $ScheduleID));
                $this->db->delete($Student_DB.'.study_planning');

                return print_r(1);


            }
        }
    }

    public function getAgama()
    {
        $generate = $this->m_api->getAgama();
        echo json_encode($generate);
    }

    public function getDivision()
    {
        $generate = $this->m_master->showData_array('db_employees.division');
        echo json_encode($generate);
    }

    public function getPosition()
    {
        $generate = $this->m_master->showData_array('db_employees.position');
        echo json_encode($generate);
    }

    public function getStatusEmployee()
    {
//        $generate = $this->m_master->showData_array('db_employees.employees_status');
        $generate = $this->db->query('SELECT * FROM db_employees.employees_status 
              WHERE IDStatus != -2 ORDER BY IDStatus DESC')->result_array();
        echo json_encode($generate);
    }

    public function searchnip_employees($NIP)
    {
        $generate = $this->m_master->caribasedprimary('db_employees.employees','NIP',$NIP);
        echo json_encode($generate);
    }

    public function cek_deadlineBPPSKS()
    {
        $this->load->model('finance/m_finance');
        $arr = array('result' => '','msg' => '');
        $input = $this->getInputToken();
        $fieldCek = $input['fieldCek'];
        $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
        $SemesterID = $SemesterID[0]['ID'];
        $getDeadlineTagihanDB = $this->m_finance->getDeadlineTagihanDB($fieldCek,$SemesterID);
        $dateFieldCek = $getDeadlineTagihanDB.' 23:59:00';
        $aaa = $this->m_master->chkTgl(date('Y-m-d H:i:s'),$dateFieldCek);
        if($aaa)
        {
            $arr['result'] = 'Tgl Deadline ; '.$dateFieldCek;
        }
        else
        {
            $arr['msg'] = 'Tanggal Akademik : '.$dateFieldCek.' melewati tanggal sekarang, Mohon cek inputan tanggal akademik';
        }

        echo json_encode($arr);
    }

    public function cek_deadline_paymentNPM()
    {
        $arr = array();
        try {
            $input = $this->getInputToken();
            $NPM = $input['NPM'];
            $arr = $this->m_api->cek_deadline_paymentNPM($NPM);
            echo json_encode($arr);
        }

            //catch exception
        catch(Exception $e) {
            echo json_encode($arr);
        }

    }


    public function crudLimitCredit(){

        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='getStudents'){

                $dataStd = $this->db->query('SELECT s.NPM,s.Name, lc.ID AS LCID FROM '.$data_arr['DB_Student'].'.students s 
                                                      LEFT JOIN db_academic.limit_credit lc ON (s.NPM=lc.NPM)
                                                      WHERE s.ProdiID = "'.$data_arr['ProdiID'].'" 
                                                      ORDER BY s.NPM ASC')->result_array();

                $dataLC = $this->db->query('SELECT s.NPM, s.Name, lc.Credit, lc.ID AS LCID FROM db_academic.limit_credit lc 
                                                    LEFT JOIN '.$data_arr['DB_Student'].'.students s ON (s.NPM = lc.NPM)
                                                    WHERE s.ProdiID = "'.$data_arr['ProdiID'].'" 
                                                    AND lc.SemesterID = "'.$data_arr['SemesterID'].'"
                                                    ORDER BY s.NPM ASC')->result_array();

                $res = array(
                    'Students' => $dataStd,
                    'dataLC' => $dataLC
                );

                return print_r(json_encode($res));
            }
            else if($data_arr['action']=='deleteLC'){
                $this->db->where('ID', $data_arr['LCID']);
                $this->db->delete('db_academic.limit_credit');

                return print_r(1);
            }
            else if($data_arr['action']=='addLC'){
                $dataInsert = (array) $data_arr['dataInsert'];
                $this->db->insert('db_academic.limit_credit', $dataInsert);
                return print_r(1);
            }
        }

    }


    public function crudCombinedClass(){

        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){
            if($data_arr['action']=='readGroupCalss'){
                $data = $this->m_api->__getCC_GroupCalss($data_arr['ProdiID']);
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='addCombine'){
                $dataInsert = (array) $data_arr['dataInsert'];


                // Cek apakah sudah di masukan apa belum
                $dataS = $this->db->query('SELECT * FROM db_academic.schedule_details_course sdc WHERE 
                                                                    sdc.ScheduleID = "'.$dataInsert['ScheduleID'].'"
                                                                    AND sdc.ProdiID = "'.$dataInsert['ProdiID'].'" 
                                                                    AND sdc.CDID = "'.$dataInsert['CDID'].'" ')->result_array();


                if(count($dataS)<=0){

                    // Hapus STD Dari KRS Online
                    $this->m_api->kickStudent($data_arr['SemesterID'],$dataInsert['ScheduleID'],$dataInsert['CDID'],$dataInsert['ProdiID']);


                    $this->db->insert('db_academic.schedule_details_course',$dataInsert);

                    // Update CombineCLass
                    $this->db->set('CombinedClasses', '1');
                    $this->db->where('ID', $dataInsert['ScheduleID']);
                    $this->db->update('db_academic.schedule');
                    return print_r(1);
                } else {
                    return print_r(0);
                }

            }
            else if($data_arr['action']=='getScheduleGC'){

                $dataDel = $this->db->select('ID')
                    ->get_where('db_academic.schedule_details_course',
                        array('ScheduleID' => $data_arr['ScheduleID']))->result_array();

                $dataCourse = $this->db->query('SELECT sdc.ScheduleID,sdc.ID AS SDCID, mk.NameEng AS MKNameEng, mk.MKCode FROM db_academic.schedule_details_course sdc
                                                          LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                          WHERE sdc.ScheduleID = "'.$data_arr['ScheduleID'].'" 
                                                          AND sdc.ProdiID = "'.$data_arr['ProdiID'].'" LIMIT 1')->result_array();

                if(count($dataCourse)>0){
                    $dataCourse[0]['TotalProdi'] = count($dataDel);
                }

                return print_r(json_encode($dataCourse));
            }
            else if($data_arr['action']=='delSDCGL'){

                $data = $this->m_api->delCombinedClass($data_arr);

                return print_r(json_encode($data));

            }
        }


    }

    public function m_equipment_additional()
    {
        $arr = $this->m_reservation->get_m_equipment_additional();
        echo json_encode($arr);
    }

    public function get_time_opt_reservation()
    {
        $data_arr = $this->getInputToken();
        $start = $data_arr['time'];
        $aaa = explode(':', $start);

        $arrHours = array();
        $endTime = '20';
        //$getHoursNow = date('H');

        // $getHoursNow = (int)$aaa[0] + 1;

        $Start2 = date("H", strtotime($start));
        // --- adding modification ---

        $xx= date("H:i", strtotime($start));
        $time = strtotime($xx);
        $time = date("H:i", strtotime('+30 minutes', $time));
        $bool = true;
        $arr = array();
        $inc = 0;
        while ($bool) {
            if (count($arr) == 0) {
                $arr[] = $time;
            }
            else
            {
                $xx= date("H:i", strtotime($arr[$inc]));
                $time = strtotime($xx);
                $time = date("H:i", strtotime('+30 minutes', $time));
                $arr[] = $time;
                $inc++;
            }

            $zz = count($arr) - 1;
            $yy = date("H", strtotime($arr[$zz]));

            // print_r('=> '.$yy.' =>');
            // print_r($endTime);
            if ($yy == $endTime) {
                $bool = false;
                break;
            }
        }

        // print_r($arr);die();

        //$endTime = (int)$endTime - (int)$Start2;
        //$endTime = $endTime + $getHoursNow - 1;
        //print_r($endTime);die();

        // $getHoursNow = (int)$Start2 + 1;

        // for ($i=$getHoursNow; $i <= $endTime; $i++) {
        //         // check len
        //         $a = $i;
        //         for ($j=0; $j < 2 - strlen($i); $j++) {
        //             $a = '0'.$a;
        //         }
        //         $d = $a.':30';
        //         $a = $a.':00';
        //         $arrHours[] = date("h:i a", strtotime($a));
        //         //$arrHours[] = date("h:i a", strtotime($d));
        //         if ($i != $endTime) {
        //             $arrHours[] = date("h:i a", strtotime($d));
        //         }
        //  }

        for ($i=0; $i < count($arr); $i++) {
            $arrHours[] = date("h:i a", strtotime($arr[$i]));
        }

        echo json_encode($arrHours);
    }

    public function m_additional_personel()
    {
        $arr = $this->m_reservation->get_m_additional_personel();
        echo json_encode($arr);
    }


    public function getSimpleSearch(){
        $key = $this->input->get('key');
        $data = $this->m_api->__getSimpleSearch($key);
        return print_r(json_encode($data));
    }

    public function getSimpleSearchStudents(){
        $key = $this->input->get('key');
        $data = $this->m_api->__getSimpleSearchStudents($key);
        return print_r(json_encode($data));
    }


    public function room_equipment()
    {
        $data_arr = $this->getInputToken();
        $room = $data_arr['room'];
        $arr = $this->m_reservation->get_m_room_equipment($room);
        echo json_encode($arr);
    }
    public function getStudentByScheduleID($ScheduleID){
        $data = $this->m_api->__getStudentByScheduleID($ScheduleID);
        return print_r(json_encode($data));

    }

    public function checkBentrokScheduleAPI()
    {
        $chk = $this->m_reservation->checkBentrokScheduleAPI();
        if (!$chk['bool']) {
            // insert table to t_booking_delete
            $this->load->model('m_sendemail');
            // get data user
            $get = $this->m_master->caribasedprimary('db_reservation.t_booking','ID',$chk['ID']);
            $getUser = $this->m_master->caribasedprimary('db_employees.employees','NIP',$get[0]['CreatedBy']);

            $dataSave = array(
                'Start' => $get[0]['Start'],
                'End' => $get[0]['End'],
                'Time' => $get[0]['Time'],
                'Colspan' => $get[0]['Colspan'],
                'Agenda' => $get[0]['Agenda'],
                'Room' => $get[0]['Room'],
                'ID_equipment_add' => $get[0]['ID_equipment_add'],
                'ID_add_personel' => $get[0]['ID_add_personel'],
                'Req_date' => $get[0]['Req_date'],
                'CreatedBy' => $get[0]['CreatedBy'],
                'ID_t_booking' => $get[0]['ID'],
                'Note_deleted' => 'Conflict',
                'DeletedBy' => 0,
                'Req_layout' => $get[0]['Req_layout'],
                'Status' => $get[0]['Status'],
                'MarcommSupport' => $get[0]['MarcommSupport'],
            );
            $this->db->insert('db_reservation.t_booking_delete', $dataSave);


                $this->m_master->delete_id_table_all_db($get[0]['ID'],'db_reservation.t_booking');
                $this->m_master->delete_id_table_all_db($get[0]['ID'],'db_reservation.t_booking_eq_additional');

            // send email and update notification
            // broadcase update js
            if($_SERVER['SERVER_NAME'] =='localhost') {
                $client = new Client(new Version1X('//10.1.10.230:3000'));
            }
            else{
                $client = new Client(new Version1X('//10.1.30.17:3000'));

            }    
                $client->initialize();
                // send message to connected clients
                $client->emit('update_schedule_notifikasi', ['update_schedule_notifikasi' => '1','date' => '']);
                $client->close();

                $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['Start']);
                $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['End']);
                $StartNameDay = $Startdatetime->format('l');
                $EndNameDay = $Enddatetime->format('l');


                //suggestion room
                    $ParticipantQty = $get[0]['ParticipantQty'];
                    //find room besar >= ParticipantQty and category room sama
                    $sg_room = function($ParticipantQty,$Room){
                        $result = '';
                        $r = array();
                        $a = $this->m_master->caribasedprimary('db_academic.classroom','Room',$Room);
                        $ID_CategoryRoom = $a[0]['ID_CategoryRoom'];
                        $b = $this->m_master->caribasedprimary('db_academic.classroom','ID_CategoryRoom',$ID_CategoryRoom);
                        for ($i=0; $i < count($b); $i++) { 
                            if ($b[$i]['Seat'] > $ParticipantQty) {
                                $r[] = $b[$i]['Room'];
                            }
                        }

                        if (count($r) > 0) {
                            $result = 'Following suggestion from our room :<ul>';
                            for ($i=0; $i < count($r); $i++) { 
                                $result .= '<li>'.$r[$i].'</li>';
                            }
                            $result .='</ul>';
                        }

                        return $result;
                    };
                    $sg_room = $sg_room($ParticipantQty,$get[0]['Room']);
                //suggestion room

                // send email
                $Email = $getUser[0]['EmailPU'];
                $text = 'Dear '.$getUser[0]['Name'].',<br><br>

                            Your Venue Reservation was conflict,<br><br>
                            <strong>Your schedule automated delete by System</strong>,<br><br>
                            Details Schedule : <br><ul>
                            <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                            <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                            <li>Room  : '.$get[0]['Room'].'</li>
                            </ul>
                            <br>
                            Please Create new schedule, if you need it and '.$sg_room.' <br>
                            
                        ';
            $to = $Email;
            $subject = "Podomoro University Venue Reservation";
            $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);


            // send email to Adum
            $text = 'Dear Team,<br><br>
                            Venue Reservation was conflict,<br><br>
                            <strong>The schedule automated delete by System</strong>,<br><br>
                            Details Schedule : <br><ul>
                            <li>Start       : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                            <li>End         : '.$EndNameDay.', '.$get[0]['End'].'</li>
                            <li>Room        : '.$get[0]['Room'].'</li>
                            <li>Request BY  : '.$getUser[0]['Name'].'</li>
                            </ul>
                            <br>
                            Please Create new schedule, if you need it and '.$sg_room.' <br>
                            
                        ';
            $eAdum = $this->m_master->caribasedprimary('db_reservation.email_to','Ownership','Adum');
            $to = $eAdum[0]['Email'];
            $subject = "Podomoro University Venue Reservation";
            $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);

            echo json_encode(0);
        }
        else
        {
            echo json_encode(1);
        }

    }

    function crudPartime(){
        $data_arr = $this->getInputToken();

        if($data_arr['action']=='readPartime'){
            $data = $this->m_hr->getLecPartime();
            return print_r(json_encode($data));
        }
    }

    public function crudConfig(){

        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){

            if($data_arr['action']=='readConfig'){
                $data = $this->db->get_where('db_academic.config',array('ConfigID' => $data_arr['ConfigID']))->result_array();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='updateConfig'){

                $this->db->set('Status', $data_arr['Status']);
                $this->db->where('ConfigID', $data_arr['ConfigID']);
                $this->db->update('db_academic.config');

                return print_r(1);
            }

        }


    }

    public function crudInvigilator(){
        $data_arr = $this->getInputToken();

        if(count($data_arr)>0){

            if($data_arr['action']=='readScheduleInvigilator'){

                $data = $this->m_api->getInvigilatorSch($data_arr['SemesterID'],
                    $data_arr['TypeExam'],$data_arr['NIP']);
                return print_r(json_encode($data));
            }

        }
    }


    public function getListCourseInScore(){
        $requestData= $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if($data_arr['StatusGrade']=='null'){
            $whereStatusGrade = ' AND gc.Status IS NULL ';
        } else {
            $whereStatusGrade = ($data_arr['StatusGrade']!='' && $data_arr['StatusGrade']!=null)
                ? ' AND gc.Status = "'.$data_arr['StatusGrade'].'" ' : '';
        }


        $whereP = ($data_arr['ProdiID'] !='' && $data_arr['ProdiID']!=null)
            ? ' sc.SemesterID = "'.$data_arr['SemesterID'].'" '.$whereStatusGrade.' AND sc.IsSemesterAntara = "'.$data_arr['IsSemesterAntara'].'" AND sdc.ProdiID = "'.$data_arr['ProdiID'].'" '
            : ' sc.SemesterID = "'.$data_arr['SemesterID'].'" '.$whereStatusGrade.' AND sc.IsSemesterAntara = "'.$data_arr['IsSemesterAntara'].'" ';
        $orderBy = ' GROUP BY sc.ID ORDER BY sc.ClassGroup, sdc.ID ASC ';
        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( sc.ClassGroup LIKE "%'.$search.'%" 
            OR mk.Name LIKE "%'.$search.'%"
             OR mk.NameEng LIKE "%'.$search.'%"
              OR em.Name LIKE "%'.$search.'%" ) ';
        }

        // Load Type
        $whereType = '';
        if($data_arr['Type']==10){
            $whereType = ' AND (ris_uts.NIP IS NULL)';
        } else if($data_arr['Type']==11){
            $whereType = ' AND (ris_uts.NIP IS NOT NULL)';
        } else if($data_arr['Type']==12){
            $whereType = ' AND (ris_uts.Status = "1")';
        } else if($data_arr['Type']==13){
            $whereType = ' AND (ris_uts.Status = "0")';
        }


        else if($data_arr['Type']==20){
            $whereType = ' AND (ris_uas.NIP IS NULL)';
        } else if($data_arr['Type']==21){
            $whereType = ' AND (ris_uas.NIP IS NOT NULL)';
        } else if($data_arr['Type']==22){
            $whereType = ' AND (ris_uas.Status = "1")';
        } else if($data_arr['Type']==22){
            $whereType = ' AND (ris_uas.Status = "0")';
        }

        $queryDefault = 'SELECT sc.ClassGroup,sc.TotalAssigment,
                                        sdc.*,cd.TotalSKS AS Credit, mk.MKCode, mk.Name AS MKName,
                                        mk.NameEng AS MKNameEng, sc.Coordinator, 
                                        em.Name AS CoordinatorName,
                                        gc.ID AS GradeID, gc.Status AS StatusGrade,
                                        ris_uts.NIP AS uts_UpdateBy, ris_uts.UpdateAt AS uts_UpdateAt, ris_uts.Status AS uts_Status, 
                                        ris_uas.NIP AS uas_UpdateBy, ris_uas.UpdateAt AS uas_UpdateAt, ris_uas.Status AS uas_Status 
                                        FROM db_academic.schedule_details_course sdc 
                                        LEFT JOIN db_academic.schedule sc ON (sc.ID = sdc.ScheduleID)
                                        LEFT JOIN db_employees.employees em ON (em.NIP = sc.Coordinator)
                                        LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID) 
                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                        LEFT JOIN db_academic.grade_course gc 
                                          ON (gc.SemesterID = sc.SemesterID AND gc.ScheduleID = sc.ID)
                                          LEFT JOIN db_academic.record_input_score ris_uts ON (ris_uts.ScheduleID = sc.ID AND ris_uts.Type="uts")
                                          LEFT JOIN db_academic.record_input_score ris_uas ON (ris_uas.ScheduleID = sc.ID AND ris_uas.Type="uas")
                                        WHERE ('.$whereP.' ) '.$whereType.' '.$dataSearch.' '.$orderBy.' ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $StatusGrade = '-';
            if($row['StatusGrade']=='2'){
                $StatusGrade = '<i class="fa fa-check-circle" style="color: green;"></i>';
            } else if ($row['StatusGrade']=='0'){
                $StatusGrade = '<i class="fa fa-repeat"></i>';
            } else if($row['StatusGrade']=='1'){
                $StatusGrade = '<i class="fa fa-question-circle" style="color: blue;"></i>';
            } else if($row['StatusGrade']=='-2'){
                $StatusGrade = '<i class="fa fa-times-circle" style="color: darkred;"></i>';
            }

            $btnAct = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span class="caret"></span></button>
                <ul class="dropdown-menu">
                <li><a href="javascript:void(0);" class="btnInputScore" data-nip="'.$row['Coordinator'].'" data-smt="'.$data_arr['SemesterID'].'" data-id="'.$row['ScheduleID'].'">Input Score</a></li>
                <li><a href="javascript:void(0);" class="btnGrade" data-page="InputGrade1" data-group="'.$row['ClassGroup'].'" data-id="'.$row['ScheduleID'].'">Approval - Score Weighted</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="javascript:void(0);" class="inputScheduleExchange" data-no="'.$no.'" data-id="">Cetak Report UTS</a></li>
                <li><a href="javascript:void(0);" class="inputScheduleExchange" data-no="'.$no.'" data-id="">Cetak Report UAS</a></li>
                </ul>
                </div>';

            // Student
            $dataStudent = $this->m_api->getDataStudents_Schedule($data_arr['SemesterID'],$row['ScheduleID']);

            $inputUTS = ($row['uts_UpdateBy']!=null && $row['uts_UpdateBy']!='') ? '<i class="fa fa-check-square-o" style="color: forestgreen;"></i>' : '-';
            $inputUAS = ($row['uas_UpdateBy']!=null && $row['uas_UpdateBy']!='') ? '<i class="fa fa-check-square-o" style="color: forestgreen;"></i>' : '-';

            $AppUTS = ($row['uts_Status']!=null && $row['uts_Status']!='' && $row['uts_Status']!='0') ? '<i class="fa fa-check-square-o" style="color: forestgreen;"></i>' : '-';
            $AppUAS = ($row['uas_Status']!=null && $row['uas_Status']!='' && $row['uas_Status']!='0') ? '<i class="fa fa-check-square-o" style="color: forestgreen;"></i>' : '-';


            $nestedData[] = '<div style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div style="text-align:left;"><b>'.$row['MKNameEng'].'</b><br/>'.$row['MKName'].'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$row['ClassGroup'].'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$row['Credit'].'</div>';
            $nestedData[] = '<div style="text-align:left;">'.$row['CoordinatorName'].'</div>';
            $nestedData[] = '<div style="text-align:center;">'.count($dataStudent).'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$inputUTS.'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$AppUTS.'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$inputUAS.'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$AppUAS.'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$btnAct.'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$StatusGrade.'</div>';

            $data[] = $nestedData;
            $no++;
        }


        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval( count($queryDefaultRow) ),
            "data"            => $data
        );
        echo json_encode($json_data);

    }

    public function getMonScoreStd(){
        $requestData= $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $queryDefaultRow = [];
        $data = [];

        if($data_arr['SemesterID']>=13){
            $w_prodi = ($data_arr['ProdiID']!='') ? ' AND auts.ProdiID = "'.$data_arr['ProdiID'].'"' : '';

            $whereType = '';
            if($data_arr['Type']==10){
                $whereType = ' AND (sp.UTS IS NULL OR sp.UTS=0 OR sp.UTS="")';
            } else if($data_arr['Type']==11){
                $whereType = ' AND (sp.UTS IS NOT NULL AND sp.UTS!=0 AND sp.UTS != "")';
            }

            else if($data_arr['Type']==20){
                $whereType = ' AND (sp.UAS IS NULL OR sp.UAS=0 OR sp.UAS="")';
            } else if($data_arr['Type']==21){
                $whereType = ' AND (sp.UAS IS NOT NULL AND sp.UAS!=0 AND sp.UAS != "")';
            }

            $dataSearch = '';
            if( !empty($requestData['search']['value']) ) {
                $search = $requestData['search']['value'];
                $dataSearch = ' AND ( s.ClassGroup LIKE "%'.$search.'%" 
            OR auts.Name LIKE "%'.$search.'%"
             OR auts.NPM LIKE "%'.$search.'%"
              OR em.NIP LIKE "%'.$search.'%"
               OR em.Name LIKE "%'.$search.'%") ';

            }



            $DB_ = 'ta_'.$data_arr['Year'];
            $queryDefault = 'SELECT s.ID,auts.NPM,auts.Name, s.ClassGroup, em.Name AS CoordinatorName, 
                                     sp.Evaluasi1, sp.Evaluasi2, sp.Evaluasi3, sp.Evaluasi4, sp.Evaluasi5, sp.UTS, sp.UAS,
                                      sp.Score, sp.Grade
                                    FROM '.$DB_.'.study_planning sp
                                    LEFT JOIN db_academic.auth_students auts ON (auts.NPM = sp.NPM)
                                    LEFT JOIN db_academic.schedule s ON (s.ID = sp.ScheduleID)
                                    LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                    WHERE ( sp.SemesterID = "'.$data_arr['SemesterID'].'" '.$w_prodi.' ) '.$whereType.' '.$dataSearch.' ORDER BY sp.NPM ASC
                                    ';

            $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();
            for($i=0;$i<count($query);$i++) {
                $nestedData = array();

                $row = $query[$i];

                $rowMK = $this->db->query('SELECT  mk.Name AS MKName, mk.NameEng AS MKNameEng, mk.MKCode 
                                                              FROM db_academic.schedule_details_course sdc
                                                              LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                              WHERE sdc.ScheduleID = "'.$row['ID'].'"  GROUP BY sdc.ScheduleID LIMIT 1')->result_array()[0];

                $ev1 = ($row['Evaluasi1']!=null && $row['Evaluasi1']!='' && $row['Evaluasi1']!=0 && $row['Evaluasi1']!='0') ? $row['Evaluasi1'] : '-';
                $ev2 = ($row['Evaluasi2']!=null && $row['Evaluasi2']!='' && $row['Evaluasi2']!=0 && $row['Evaluasi2']!='0') ? $row['Evaluasi2'] : '-';
                $ev3 = ($row['Evaluasi3']!=null && $row['Evaluasi3']!='' && $row['Evaluasi3']!=0 && $row['Evaluasi3']!='0') ? $row['Evaluasi3'] : '-';
                $ev4 = ($row['Evaluasi4']!=null && $row['Evaluasi4']!='' && $row['Evaluasi4']!=0 && $row['Evaluasi4']!='0') ? $row['Evaluasi4'] : '-';
                $ev5 = ($row['Evaluasi5']!=null && $row['Evaluasi5']!='' && $row['Evaluasi5']!=0 && $row['Evaluasi5']!='0') ? $row['Evaluasi5'] : '-';
                $UTS = ($row['UTS']!=null && $row['UTS']!='' && $row['UTS']!=0 && $row['UTS']!='0') ? $row['UTS'] : '-';
                $UAS = ($row['UAS']!=null && $row['UAS']!='' && $row['UAS']!=0 && $row['UAS']!='0') ? $row['UAS'] : '-';



                $nestedData[] = '<div style="text-align:center;">'.$no.'</div>';
                $nestedData[] = '<div style="text-align:left;"><b><i class="fa fa-user margin-right"></i>'.$row['Name'].'</b><br/>'.$row['NPM'].'</div>';
                $nestedData[] = '<div style="text-align:center;">'.$rowMK['MKCode'].'</div>';
                $nestedData[] = '<div style="text-align:left;"><span style="color: #009688;">'.$rowMK['MKNameEng'].'</span><br/><i style="color: #9e9e9e;">'.$rowMK['MKName'].'</i></div>';
                $nestedData[] = '<div style="text-align:center;">'.$row['ClassGroup'].'</div>';
                $nestedData[] = '<div style="text-align:left;">'.$row['CoordinatorName'].'</div>';
                $nestedData[] = '<div style="text-align:center;">'.$ev1.'</div>';
                $nestedData[] = '<div style="text-align:center;">'.$ev2.'</div>';
                $nestedData[] = '<div style="text-align:center;">'.$ev3.'</div>';
                $nestedData[] = '<div style="text-align:center;">'.$ev4.'</div>';
                $nestedData[] = '<div style="text-align:center;">'.$ev5.'</div>';
                $nestedData[] = '<div style="text-align:center;">'.$UTS.'</div>';
                $nestedData[] = '<div style="text-align:center;">'.$UAS.'</div>';
                $nestedData[] = '<div style="text-align:center;">'.$row['Score'].'</div>';
                $nestedData[] = '<div style="text-align:center;">'.$row['Grade'].'</div>';

                $data[] = $nestedData;
                $no++;

            }
        }



        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval( count($queryDefaultRow) ),
            "data"            => $data
        );
        echo json_encode($json_data);


    }


    // ====== Transcript Nilai =========
    public function getTranscript(){
        $requestData= $_REQUEST;
        $data_arr = $this->getInputToken();

        $dataWhere = ($data_arr['ProdiID']!='' && $data_arr['ProdiID']!=null)
            ? 'aut_s.Year = "'.$data_arr['Year'].'" AND ( aut_s.StatusStudentID = "3" OR aut_s.StatusStudentID = "1" ) AND aut_s.ProdiID = "'.$data_arr['ProdiID'].'" '
            : 'aut_s.Year = "'.$data_arr['Year'].'" AND ( aut_s.StatusStudentID = "3" OR aut_s.StatusStudentID = "1" ) ' ;

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = 'AND ( aut_s.Name LIKE "%'.$search.'%" OR aut_s.NPM LIKE "%'.$search.'%" 
                           OR ps.Name LIKE "%'.$search.'%"  OR ps.NameEng LIKE "%'.$search.'%" )';
        }

        $queryDefault = 'SELECT aut_s.*, ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng FROM db_academic.auth_students aut_s 
                                      LEFT JOIN db_academic.program_study ps ON (ps.ID = aut_s.ProdiID)
                                      WHERE ( '.$dataWhere.' ) '.$dataSearch.' ORDER BY aut_s.NPM ASC ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $db_ = 'ta_'.$row['Year'];

            $btnSKPI = '<div  style="text-align:center;">
                            <a href="'.base_url('save2pdf/diploma_supplement').'" target="_blank" class="btn btn-default btn-sm btn-default-warning btnDownloadSKPI"><i class="fa fa-download margin-right"></i> SKPI</a>
                            </div>';

//            $btnTranscript = '<div  style="text-align:center;">
//                                                <button class="btn btn-sm btn-default btn-default-primary btnDowloadTranscript" data-db="'.$db_.'" data-npm="'.$row['NPM'].'">
//                                                    <i class="fa fa-download margin-right"></i> Transcript</button></div>';

            $btnTranscript = '<div class="btn-group btn-sm" role="group" aria-label="...">
                              <button type="button" class="btn btn-sm btn-default btn-default-danger btnDowloadTempTranscript" data-db="'.$db_.'" data-npm="'.$row['NPM'].'"><i class="fa fa-hourglass-half margin-right"></i> Temp.</button>
                              <button type="button" class="btn btn-sm btn-default btn-default-primary btnDowloadTranscript" data-db="'.$db_.'" data-npm="'.$row['NPM'].'">
                              <i class="fa fa-download margin-right"></i> Final</button>
                            </div>';

            $btnIjazah = '<div  style="text-align:center;">
                            <button class="btn btn-sm btn-default btn-default-success btnDownloadIjazah" data-db="'.$db_.'" data-npm="'.$row['NPM'].'"><i class="fa fa-download margin-right"></i> Ijazah</button>
                            </div>';

//            $btnIjazah = '<div  style="text-align:center;">
//                            <a href="'.base_url('save2pdf/ijazah').'" target="_blank" class="btn btn-sm btn-default btn-default-success"><i class="fa fa-download margin-right"></i> Ijazah</a>
//                            </div>';

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:left;"><b>'.$row['NPM'].'</b></div>';
            $nestedData[] = '<div  style="text-align:left;">
                                    <b><i class="fa fa-user margin-right"></i> '.ucwords(strtolower($row['Name'])).'</b><br/>
                                        <a>'.$row['EmailPU'].'</a></div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['ProdiNameEng'].'</div>';
            $nestedData[] = '<div  style="text-align:left;">
                                    <div class="">
                                        <div class="col-xs-10" style="padding-right: 0px;">
                                            <input id="formCSN'.$row['NPM'].'" class="form-control hide" value="'.$row['CertificateSerialNumber'].'"/>
                                            <span id="viewCSN'.$row['NPM'].'">'.$row['CertificateSerialNumber'].'</span>
                                        </div>
                                        <div class="col-xs-2">
                                               
                                            <button class="btn btn-sm btn-success btn-block btnSaveCSN hide" data-npm="'.$row['NPM'].'"><i class="fa fa-check-circle"></i></button>
                                            <button class="btn btn-sm btn-default btn-block btnEditCSN" data-npm="'.$row['NPM'].'"><i class="fa fa-pencil-square-o"></i></button>   
                                        </div>
                                    </div>
                                     
                                    </div>';
            $nestedData[] = $btnSKPI;
            $nestedData[] = $btnTranscript;
            $nestedData[] = $btnIjazah;

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



    }

    public function crudTranscript(){

        $data_arr = $this->getInputToken();

        if(count($data_arr>0)){
            if($data_arr['action']=='readStudent'){
                $Year = $data_arr['Year'];
                $ProdiID = $data_arr['ProdiID'];
                $data = $this->db->get_where('db_academic.auth_students',array('ProdiID'=> $ProdiID,'Year'=>$Year))->result_array();
                print_r($data);

            }
            else if($data_arr['action']=='updateGrade'){

                $dataIns = (array) $data_arr['dataForm'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.graduation',$dataIns);
                return print_r(1);
            }
            else if($data_arr['action']=='updateSettingTranscript'){
                $dataIns = (array) $data_arr['dataForm'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.setting_transcript',$dataIns);
                return print_r(1);
            }
            else if($data_arr['action']=='updateEducation'){

                $this->db->set('DescriptionEng', $data_arr['DescriptionEng']);
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.education_level');
                return print_r(1);
            }
            else if($data_arr['action']=='updateCSN'){
                $this->db->set('CertificateSerialNumber', $data_arr['CSN']);
                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update('db_academic.auth_students');
                return print_r(1);
            }

            else if($data_arr['action']=='updateTempTranscript'){

                $dataUpdate = (array) $data_arr['dataForm'];
                $this->db->where('ID', 1);
                $this->db->update('db_academic.setting_temp_transcript',$dataUpdate);

                return print_r(1);
            }
        }

    }
    // ==========

    // ====== Final Project =======
    public function getFinalProject(){
        $requestData= $_REQUEST;
        $data_arr = $this->getInputToken();

        $dataWhere = ($data_arr['ProdiID']!='' && $data_arr['ProdiID']!=null)
            ? 'aut_s.Year = "'.$data_arr['Year'].'" AND ( aut_s.StatusStudentID = "3" OR aut_s.StatusStudentID = "1" )  AND aut_s.ProdiID = "'.$data_arr['ProdiID'].'" '
            : 'aut_s.Year = "'.$data_arr['Year'].'" AND ( aut_s.StatusStudentID = "3" OR aut_s.StatusStudentID = "1" )  ' ;

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = 'AND ( aut_s.Name LIKE "%'.$search.'%" OR aut_s.NPM LIKE "%'.$search.'%" 
                           OR ps.Name LIKE "%'.$search.'%"  OR ps.NameEng LIKE "%'.$search.'%" )';
        }

        $queryDefault = 'SELECT aut_s.*, ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng, ps.Code,  
                                      fp.TitleInd, fp.TitleEng
                                      FROM db_academic.auth_students aut_s
                                      LEFT JOIN db_academic.program_study ps ON (ps.ID = aut_s.ProdiID)
                                      LEFT JOIN db_academic.final_project fp ON (fp.NPM = aut_s.NPM)
                                      WHERE ( '.$dataWhere.' ) '.$dataSearch.' ORDER BY aut_s.NPM ASC ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['NPM'].'</div>';
            $nestedData[] = '<div  style="text-align:left;"><b><i class="fa fa-user margin-right"></i> '.$row['Name'].'</b></div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['Code'].'</div>';
            $nestedData[] = '<div  style="text-align:left;"><span id="viewTitleInd'.$row['ID'].'">'.$row['TitleInd'].'</span><input class="form-control hide fmFP'.$row['ID'].'" value="'.$row['TitleInd'].'" id="formTitleInd'.$row['ID'].'"></div>';
            $nestedData[] = '<div  style="text-align:left;"><span id="viewTitleEng'.$row['ID'].'">'.$row['TitleEng'].'</span><input class="form-control hide fmFP'.$row['ID'].'" value="'.$row['TitleEng'].'" id="formTitleEng'.$row['ID'].'"></div>';
            $nestedData[] = '<div  style="text-align:center;">
                                    <button class="btn btn-success btn-sm hide btnSaveEditFP" data-id="'.$row['ID'].'" data-npm="'.$row['NPM'].'" id="btnSaveFP'.$row['ID'].'">Save</button>
                                    <button class="btn btn-default btn-sm btnEditFP" data-id="'.$row['ID'].'" data-npm=""'.$row['NPM'].' id="btnEditFP'.$row['ID'].'">Edit</button>
                                </div>';

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

    }

    public function crudFinalProject(){

        $data_arr = $this->getInputToken();

        if(count($data_arr>0)) {
            if ($data_arr['action'] == 'updateFP') {

                // Cek apakah NIM sudah ada
                $dataNIM = $this->db->get_where('db_academic.final_project',array('NPM' => $data_arr['NPM']),1)->result_array();
                $dataForm = (array) $data_arr['dataForm'];
                if(count($dataNIM)>0){
                    // Update
                    $this->db->where('NPM', $data_arr['NPM']);
                    $this->db->update('final_project',$dataForm);
                } else {
                    $dataForm['NPM'] = $data_arr['NPM'];
                    $this->db->insert('final_project',$dataForm);
                }


                return print_r(1);

            }
        }
    }

    public function getAllDepartementPU()
    {
        $arr_result = array();
        // $NA = $this->m_master->showData_array('db_employees.division');
        $NA = $this->m_master->caribasedprimary('db_employees.division','StatusDiv',1);
        $AC = $this->m_master->caribasedprimary('db_academic.program_study','Status',1);
        for ($i=0; $i < count($NA); $i++) {
            $arr_result[] = array(
                'Code'  => 'NA.'.$NA[$i]['ID'],
                'Name1' => $NA[$i]['Description'],
                'Name2' => $NA[$i]['Division']
            );
        }

        for ($i=0; $i < count($AC); $i++) {
            $arr_result[] = array(
                'Code'  => 'AC.'.$AC[$i]['ID'],
                'Name1' => $AC[$i]['Name'],
                'Name2' => $AC[$i]['NameEng']
            );
        }

        echo json_encode($arr_result);
    }

    public function crudConfigSKPI(){
        $data_arr = $this->getInputToken();

        if(count($data_arr>0)) {
            if($data_arr['action'] == 'readData') {
                $ID = $data_arr['ID'];
                $data = $this->db->get_where('db_academic.ds_detail',
                    array('DS_TypeID' => $ID),1)->result_array();

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='updateSKPI'){

                $ID = $data_arr['ID'];
                $data = $this->db->get_where('db_academic.ds_detail',
                    array('DS_TypeID' => $ID),1)->result_array();

                $dataUpdate = (array) $data_arr['dataUpdate'];
                // Jika Null Maka Insert
                if(count($data)>0){

                    $this->db->where('ID', $data[0]['ID']);
                    $this->db->update('db_academic.ds_detail',$dataUpdate);
                } else {

                    $dataInsert = array(
                        'DS_TypeID' => $ID,
                        'DescInd' => $dataUpdate['DescInd'],
                        'DescEng' => $dataUpdate['DescEng'],
                        'CreateBy' => $dataUpdate['UpdateBy'],
                        'CreateAt' => $dataUpdate['UpdateAt']
                    );

                    $this->db->insert('db_academic.ds_detail', $dataInsert);
                }

                return print_r(1);

            }
        }
    }

    public function getListStudent(){
        $requestData= $_REQUEST;
        $data_arr = $this->getInputToken();


        $dataWhere = '';

        if($data_arr['Year']!='' || $data_arr['ProdiID']!='' || $data_arr['GroupProdiID']!='' || $data_arr['StatusStudents']!=''){
            $w_Year = ($data_arr['Year']!='') ?  ' AND aut_s.Year = "'.$data_arr['Year'].'"' : '';
            $w_ProdiID = ($data_arr['ProdiID']!='') ?  ' AND aut_s.ProdiID = "'.$data_arr['ProdiID'].'"' : '';
            $w_GroupProdiID = ($data_arr['GroupProdiID']!='') ?  ' AND aut_s.ProdiGroupID = "'.$data_arr['GroupProdiID'].'"' : '';
            $w_StatusStudents = ($data_arr['StatusStudents']!='') ?  ' AND aut_s.StatusStudentID = "'.$data_arr['StatusStudents'].'"' : '';

            $dataWherePlan = 'WHERE ('.$w_Year.''.$w_ProdiID.''.$w_GroupProdiID.''.$w_StatusStudents.')';

            $exp_w = explode(' ',$dataWherePlan);
            if(count($exp_w)>0){
                for($i=0;$i<count($exp_w);$i++){
                    if($i!=2){
                        $dataWhere = $dataWhere.' '.trim($exp_w[$i]);
                    }
                }
            }

        }


        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];

            if($dataWhere!=''){
                $dataSearch = 'AND ( aut_s.Name LIKE "%'.$search.'%" OR aut_s.NPM LIKE "%'.$search.'%" 
                           OR ps.Name LIKE "%'.$search.'%"  OR ps.NameEng LIKE "%'.$search.'%" )';
            } else {
                $dataSearch = 'WHERE ( aut_s.Name LIKE "%'.$search.'%" OR aut_s.NPM LIKE "%'.$search.'%" 
                           OR ps.Name LIKE "%'.$search.'%"  OR ps.NameEng LIKE "%'.$search.'%" )';
            }


        }

        $queryDefault = 'SELECT aut_s.*, ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng, ss.Description AS StatusStudent,  
                                      pg.Code AS ProdiGroup
                                      FROM db_academic.auth_students aut_s
                                      LEFT JOIN db_academic.program_study ps ON (ps.ID = aut_s.ProdiID)
                                      LEFT JOIN db_academic.prodi_group pg ON (pg.ID = aut_s.ProdiGroupID)
                                      LEFT JOIN db_academic.status_student ss ON (ss.ID = aut_s.StatusStudentID)
                                      '.$dataWhere.' '.$dataSearch.' ORDER BY aut_s.NPM ASC ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();
        $no = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++) {
            $nestedData = array();
            $row = $query[$i];

            $db_ = 'ta_'.$row['Year'];
            $dataDetailStd = $this->db->select('Photo,Gender')->get_where($db_.'.students',array('NPM' => $row['NPM']),1)->result_array();

            $dataToken = array(
                'Type' => 'std',
                'Name' => $row['Name'],
                'NPM' => $row['NPM'],
                'Email' => $row['EmailPU']
            );

            $token = $this->jwt->encode($dataToken,'UAP)(*');

            $disBtnEmail = ($row['EmailPU']=='' || $row['EmailPU']=='') ? 'disabled' : '';

            $nameS = str_replace(' ','-',ucwords(strtolower($row['Name'])));
            $btnAct = '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="'.$disBtnEmail.'"><a href="javascript:void(0);" '.$disBtnEmail.' class="btn-reset-password '.$disBtnEmail.'" data-token="'.$token.'">Reset Password</a></li>
                            <li><a href="'.base_url('database/students/edit-students/'.$db_.'/'.$row['NPM'].'/'.$nameS).'">Edit (Coming Soon)</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="javascript:void(0);" class="btn-change-status " data-emailpu="'.$row['EmailPU'].'" 
                            data-year="'.$row['Year'].'" data-npm="'.$row['NPM'].'" data-name="'.ucwords(strtolower($row['Name'])).'" 
                            data-statusid="'.$row['StatusStudentID'].'">Change Status</a>
                            </li>
                          </ul>
                        </div>';

            $srcImage = base_url('images/icon/userfalse.png');
            if($dataDetailStd[0]["Photo"]!='' && $dataDetailStd[0]["Photo"]!=null){
                $urlImg = './uploads/students/'.$db_.'/'.$dataDetailStd[0]["Photo"];
                $srcImage = (file_exists($urlImg)) ? base_url('uploads/students/'.$db_.'/'.$dataDetailStd[0]["Photo"]) : base_url('images/icon/userfalse.png') ;
            }

            $fm = '<input id="formTypeImage'.$row['NPM'].'" class="hide" /><form id="fmPhoto'.$row['NPM'].'" enctype="multipart/form-data" accept-charset="utf-8" method="post" action="">
                                <input id="formPhoto" class="hide" value="" hidden />
                                <div class="form-group"><label class="btn btn-sm btn-default btn-default-warning btn-upload">
                                        <i class="fa fa-upload"></i>
                                        <input type="file" id="filePhoto" name="userfile" data-db="'.$db_.'" data-npm="'.$row['NPM'].'" class="uploadPhotoEmp"
                                               style="display: none;" accept="image/*">
                                    </label>
                                </div>
                            </form>';

            $gp = ($row['ProdiGroupID']!='' && $row['ProdiGroupID']!=null) ? ' - '.$row['ProdiGroup'] : '';

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['NPM'].'</div>';
            $nestedData[] = '<div  style="text-align:center;"><img id="imgThum'.$row['NPM'].'" src="'.$srcImage.'" style="max-width: 35px;" class="img-rounded"></div>';
            $nestedData[] = '<div  style="text-align:left;"><a href="javascript:void(0);" data-npm="'.$row['NPM'].'" data-ta="'.$row['Year'].'" class="btnDetailStudent"><b>'.ucwords(strtolower($row['Name'])).'</b></a><br/><span style="color: #c77905;">'.$row['EmailPU'].'</span></div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['Year'].''.$gp.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['ProdiNameEng'].'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$fm.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$btnAct.'</div>';
            $nestedData[] = '<div  style="text-align:center;"><button class="btn btn-sm btn-default btn-default-primary btnLoginPortalStudents" data-npm="'.$row['NPM'].'">Login Portal</button></div>';
            $nestedData[] = '<div  style="text-align:center;">'.ucwords(strtolower($row['StatusStudent'])).'</div>';
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

    }

    public function getListEmployees(){
        $requestData= $_REQUEST;
        $data_arr = $this->getInputToken();

        $dataWhere = ($data_arr['StatusEmployeeID']!='' && $data_arr['StatusEmployeeID']!=null) ?
            'AND StatusEmployeeID = "'.$data_arr['StatusEmployeeID'].'" '
            : '' ;

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( em.Name LIKE "%'.$search.'%" OR em.NIP LIKE "%'.$search.'%" )';
        }

        $queryDefault = 'SELECT em.*, ems.Description AS StatusEmployees FROM db_employees.employees em 
                                      LEFT JOIN db_employees.employees_status ems ON (em.StatusEmployeeID = ems.IDStatus)
                                      WHERE ( em.StatusEmployeeID != -2  '.$dataWhere.' ) '.$dataSearch.' ORDER BY em.NIP ASC ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();
        $no = $requestData['start'] + 1;
        $data = array();

        for($i=0;$i<count($query);$i++) {
            $nestedData = array();
            $row = $query[$i];

            $division = '-';
            $position = '-';
            if($row['PositionMain']!='' && $row['PositionMain']!=null){
                $PositionMain = explode('.',$row['PositionMain']);

                $dataDivisi = $this->db->select('Division,Description')->get_where('db_employees.division',
                    array('ID' => $PositionMain[0]),1)->result_array();
                $division = $dataDivisi[0]['Division'];

                $dataPosition = $this->db->select('Position,Description')->get_where('db_employees.position'
                    ,array('ID' => $PositionMain[1]),1)->result_array();

                $position = $dataPosition[0]['Position'];
            }



            $gender = ($row['Gender']=='L') ? 'Male' : 'Female' ;

            $url_image = './uploads/employees/'.$row['Photo'];
            $srcImg = (file_exists($url_image)) ? base_url('uploads/employees/'.$row['Photo'])
                : base_url('images/icon/userfalse.png') ;


            $EmailSelect = ($row['StatusEmployeeID']==4 || $row['StatusEmployeeID']=='4') ? $row['Email'] : $row['EmailPU'] ;

            $Email = (($row['StatusEmployeeID']==4 || $row['StatusEmployeeID']=='4') && count(explode(',', $EmailSelect))>1) ? '' : $EmailSelect;

            $disBtnEmail = ($Email=='' || $Email==null) ? 'disabled' : '';
            $dataToken = array(
                'Type' => 'emp',
                'Name' => $row['Name'],
                'NIP' => $row['NIP'],
                'Email' => $Email
            );

            $token = $this->jwt->encode($dataToken,'UAP)(*');



            $btnAct = '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="'.$disBtnEmail.'"><a href="javascript:void(0);" '.$disBtnEmail.' id="btnResetPass'.$row['NIP'].'" class="btn-reset-password '.$disBtnEmail.'" data-token="'.$token.'">Reset Password</a></li>
                            <li><a href="javascript:void(0);" class="btn-update-email" id="btnUpdateEmail'.$row['NIP'].'" data-name="'.$row['Name'].'" data-nip="'.$row['NIP'].'" data-empid="'.$row['StatusEmployeeID'].'" data-email="'.$Email.'">Update Email</a></li>
                          </ul>
                        </div>';

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['NIP'].'</div>';
            $nestedData[] = '<div  style="text-align:center;"><img src="'.$srcImg.'" style="max-width: 35px;" class="img-rounded"></div>';
            $nestedData[] = '<div  style="text-align:left;"><b>'.$row['Name'].'</b><br/><span id="viewEmail'.$row['NIP'].'" style="color: #2196f3;">'.$Email.'</span></div>';
            $nestedData[] = '<div  style="text-align:center;">'.$gender.'</div>';
            $nestedData[] = '<div  style="text-align:left;">'.ucwords(strtolower($division)).'<br/>- '.ucwords(strtolower($position)).'</div>';
            $nestedData[] = '<div  style="text-align:left;">'.$row['Address'].'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$btnAct.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['StatusEmployees'].'</div>';

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

    }

    public function crudLecturerEvaluation(){
        $data_arr = $this->getInputToken();

        if(count($data_arr>0)) {
            if ($data_arr['action'] == 'readQuestion') {
                $dataQ = $this->db->query('SELECT ec.Category, eq.* FROM db_academic.edom_question eq
                                                      LEFT JOIN db_academic.edom_category ec 
                                                      ON (ec.ID = eq.CategoryID) 
                                                      ORDER BY eq.Order ASC ')->result_array();

                return print_r(json_encode($dataQ));
            }
            else if($data_arr['action']=='readLECategory'){
                $data = $this->db->order_by('ID','ASC')->get('db_academic.edom_category')->result_array();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='insertQuestion'){
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->insert('db_academic.edom_question',$dataForm);

                return print_r(1);
            }
            else if($data_arr['action']=='loadToEdit'){
                $ID = $data_arr['ID'];
                $data = $this->db->get_where('db_academic.edom_question',array('ID'=>$ID),1)->result_array();

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='editQuestion'){

                $dataForm = (array) $data_arr['dataForm'];

                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.edom_question',$dataForm);
                return print_r(1);
            }
            else if($data_arr['action']=='deleteQuestion'){
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.edom_question');
                return print_r(1);
            }
        }
    }

    public function getLecturerEvaluation(){

        $SemesterID = $this->input->get('SemesterID');
        $ProdiID = $this->input->get('ProdiID');

        $dataWhere = 's.SemesterID = "'.$SemesterID.'"';

        $dataSelect = 'SELECT s.ID, s.SemesterID, s.ClassGroup, mk.ID AS MKID, mk.MKCode, mk.Name AS Course, mk.NameEng AS CourseEng';

        $queryDefault = 'SELECT em.NIP, em.Name FROM db_employees.employees em WHERE em.ProdiID = "'.$ProdiID.'" 
                                                    AND em.StatusEmployeeID != "-2" 
                                                   AND em.StatusEmployeeID != "-1" ';
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $result = [];

        if(count($queryDefaultRow)>0){
            for($i=0;$i<count($queryDefaultRow);$i++){
                $d = $queryDefaultRow[$i];
                $queryCourse = $dataSelect.' FROM db_academic.schedule s 
                                                  LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                  LEFT JOIN db_academic.mata_kuliah mk ON (sdc.MKID = mk.ID)
                                                  WHERE  '.$dataWhere.' AND s.Coordinator = "'.$d['NIP'].'" GROUP BY s.ID
                                                  UNION ALL
                                                  '.$dataSelect.'
                                                  FROM db_academic.schedule_team_teaching stt
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = stt.ScheduleID)
                                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (sdc.MKID = mk.ID)
                                                    WHERE '.$dataWhere.' AND stt.NIP = "'.$d['NIP'].'" GROUP BY s.ID
                                                  ';
                $dataCourse = $this->db->query($queryCourse)->result_array();

                if(count($dataCourse)>0){
                    for($c=0;$c<count($dataCourse);$c++){
                        // Data Student
                        $dataStd = $this->m_api->getStudentByScheduleID($dataCourse[$c]['SemesterID'],$dataCourse[$c]['ID'],'');
                        $dataCourse[$c]['TotalStudent'] = count($dataStd);

                        // Data Edom Answer
                        $dataEd = $this->db->query('SELECT * FROM db_academic.edom_answer ea 
                                                                      WHERE ea.SemesterID = "'.$dataCourse[$c]['SemesterID'].'"
                                                                      AND ea.ScheduleID = "'.$dataCourse[$c]['ID'].'"
                                                                      AND ea.NIP = "'.$d['NIP'].'" ')->result_array();

                        $dataCourse[$c]['TotalAnswer'] = count($dataEd);
                    }

                    $queryDefaultRow[$i]['Course'] = $dataCourse;

                    array_push($result,$queryDefaultRow[$i]);
                }



            }
        }

        return print_r(json_encode($result));

    }

    public function getLecturerEvaluation2(){
        $requestData= $_REQUEST;
        $data_arr = $this->getInputToken();

        $dataWhere = 's.SemesterID = "'.$data_arr['SemesterID'].'"';

        $dataSelect = 'SELECT s.ID, s.SemesterID, mk.ID AS MKID, mk.MKCode, mk.Name AS Course, mk.NameEng AS CourseEng';

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( em.Name LIKE "%'.$search.'%" OR em.NIP LIKE "%'.$search.'%" )';
        }


        $queryDefault = 'SELECT em.NIP, em.Name FROM db_employees.employees em WHERE em.ProdiID = "'.$data_arr['ProdiID'].'" 
                                                    AND em.StatusEmployeeID != "-2" 
                                                   AND em.StatusEmployeeID != "-1" ';


        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        if(count($queryDefaultRow)>0){
            for($i=0;$i<count($queryDefaultRow);$i++){
                $d = $queryDefaultRow[$i];
                $queryCourse = $dataSelect.' FROM db_academic.schedule s 
                                                  LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                  LEFT JOIN db_academic.mata_kuliah mk ON (sdc.MKID = mk.ID)
                                                  WHERE  '.$dataWhere.' AND s.Coordinator = "'.$d['NIP'].'" GROUP BY s.ID
                                                  UNION ALL
                                                  '.$dataSelect.'
                                                  FROM db_academic.schedule_team_teaching stt
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = stt.ScheduleID)
                                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (sdc.MKID = mk.ID)
                                                    WHERE '.$dataWhere.' AND stt.NIP = "'.$d['NIP'].'" GROUP BY s.ID
                                                  ';
                $dataCourse = $this->db->query($queryCourse)->result_array();

                if(count($dataCourse)>0){
                    for($c=0;$c<count($dataCourse);$c++){
                        // Data Student
                        $dataStd = $this->m_api->getStudentByScheduleID($dataCourse[$c]['SemesterID'],$dataCourse[$c]['ID'],'');
                        $dataCourse[$c]['TotalStudent'] = count($dataStd);

                        // Data Edom Answer
                        $dataEd = $this->db->query('SELECT * FROM db_academic.edom_answer ea 
                                                                      WHERE ea.SemesterID = "'.$dataCourse[$c]['SemesterID'].'"
                                                                      AND ea.ScheduleID = "'.$dataCourse[$c]['ID'].'"
                                                                      AND ea.NIP = "'.$d['NIP'].'" ')->result_array();

                        $dataCourse[$c]['TotalAnswer'] = count($dataEd);
                    }
                }

                $queryDefaultRow[$i]['Course'] = $dataCourse;

            }
        }


        print_r($queryDefaultRow);
        exit;

        $no = $requestData['start'] + 1;
        $data = array();

        for($i=0;$i<count($query);$i++) {
            $nestedData = array();
            $row = $query[$i];

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:left;"><b>'.$row['Name'].'</b><br/>'.$row['NIP'].'</div>';
            $nestedData[] = '<div  style="text-align:left;">'.$row['Course'].'</div>';
            $nestedData[] = '<div  style="text-align:center;">-</div>';
            $nestedData[] = '<div  style="text-align:center;">-</div>';

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
    }

    public function crudStudent(){
        $data_arr = $this->getInputToken();

        if(count($data_arr>0)) {
            if ($data_arr['action'] == 'readDataStudent') {

                $DB_Student = $data_arr['DB_Student'];
                $NPM = $data_arr['NPM'];
                $data = $this->db->query('SELECT std.*, auts.EmailPU FROM '.$DB_Student.'.students std 
                                                  LEFT JOIN db_academic.auth_students auts ON (auts.NPM = std.NPM)
                                                  WHERE std.NPM = "'.$NPM.'" LIMIT 1')->result_array();

                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'updateBiodataStudent'){

                $DB_Student = $data_arr['DB_Student'];
                $NPM = $data_arr['NPM'];

                $dataUpdate = $data_arr['dataForm'];
                $this->db->where('NPM', $NPM);
                $this->db->update($DB_Student.'.students',$dataUpdate);


                $dataUpdtAuth = array('EmailPU' => $data_arr['EmailPU']);
                $this->db->where('NPM', $NPM);
                $this->db->update('db_academic.auth_students',$dataUpdtAuth);


                return print_r(1);

            }
        }
    }

    public function getTimetables(){
        $requestData= $_REQUEST;
        $data_arr = $this->getInputToken();

        $whereProdi = ($data_arr['ProdiID']!='') ? ' AND sdc.ProdiID = "'.$data_arr['ProdiID'].'" ' : '';
        $whereCombinedClasses = ($data_arr['CombinedClasses']) ? ' AND s.CombinedClasses = "'.$data_arr['CombinedClasses'].'" ' : '';
        $whereDay = ($data_arr['DayID']) ? ' AND sd.DayID = "'.$data_arr['DayID'].'" ' : '';

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $wl = 'LIKE "%'.$search.'%"';
            $dataSearch = ' AND (s.ClassGroup '.$wl.' 
                                    OR s.Coordinator '.$wl.'
                                    OR em.Name '.$wl.'
                                     OR mk.MKCode '.$wl.'
                                      OR mk.Name '.$wl.'
                                      OR mk.NameEng '.$wl.')';
        }

        $queryDefault = 'SELECT s.ID, s.CombinedClasses, s.ClassGroup, s.Coordinator, em.Name AS CoordinatorName,
                                      s.TeamTeaching, s.SubSesi, s.Attendance, cd.TotalSKS AS Credit,
                                       mk.MKCode, mk.Name AS MKName, mk.NameEng AS MKNameEng,
                                       cd.ID AS CDID
                                      FROM db_academic.schedule s
                                      LEFT JOIN db_academic.schedule_details sd ON (sd.ScheduleID = s.ID)
                                      LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                      LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                      LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                      
                                      LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                      
                                      WHERE ( s.ProgramsCampusID = "'.$data_arr['ProgramCampusID'].'" 
                                      AND s.SemesterID = "'.$data_arr['SemesterID'].'" '.$whereProdi.' '.$whereDay.' '.$whereCombinedClasses.' )
                                       '.$dataSearch.' 
                                      GROUP BY s.ID
                                      ORDER BY d.ID,sd.StartSessions, sd.EndSessions, s.ClassGroup ASC ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $SubSesi = ($row['SubSesi']=='1') ? '<br/><span class="label label-warning">Sub-Sesi</span>' : '';
            $Attendance = ($row['Attendance']=='0') ? '<br/><span class="label label-danger"><i class="fa fa-filter margin-right"></i> No Attd</span>' : '';

            $dataSchedule = $this->db->query('SELECT cl.Room, d.NameEng AS DayEng, sd.StartSessions, sd.EndSessions 
                                                                      FROM db_academic.schedule_details sd
                                                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                                      LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                                        WHERE sd.ScheduleID = "'.$row['ID'].'"
                                                                         ORDER BY sd.DayID ASC ')->result_array();
            $ScheduleDetails = '';
            if(count($dataSchedule)>0){
                foreach ($dataSchedule as $item){
                    $ScheduleDetails = $ScheduleDetails.''.$item['DayEng'].', <span style="color: #0b97c4;">'.substr($item['StartSessions'],0,5).' - '.substr($item['EndSessions'],0,5).'</span><br/>Room : '.$item['Room'].'<br/>';
                }
            }

            $TeamTeaching = '';
            if($row['TeamTeaching']=='1'){
                $dataTeamTeaching = $this->db->query('SELECT stt.NIP, em.Name FROM db_academic.schedule_team_teaching stt 
                                                                LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                                WHERE stt.ScheduleID = "'.$row['ID'].'" ')->result_array();

                if(count($dataTeamTeaching)>0){
                    for($t=0;$t<count($dataTeamTeaching);$t++){
                        $TeamTeaching = $TeamTeaching.'<br/> - '.$dataTeamTeaching[$t]['Name'];
                    }
                }
            }

            // Daftar Prodi
            $dataProdi = $this->db->query('SELECT ps.ID AS ProdiID, ps.Code FROM db_academic.schedule_details_course sdc 
                                                    LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                                    WHERE sdc.ScheduleID = "'.$row['ID'].'" 
                                                    GROUP BY sdc.ProdiID ORDER BY ps.Code ASC ')->result_array();
            $Prodi = '';
            if(count($dataProdi)>0){
                for($p=0;$p<count($dataProdi);$p++){

                    // Get Semester
                    $dataSMT = $this->db->query('SELECT co.Semester  
                                                    FROM db_academic.schedule_details_course sdc
                                                    LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                    LEFT JOIN db_academic.course_offerings co ON (co.CurriculumID = cd.CurriculumID)
                                                    WHERE sdc.ScheduleID = "'.$row['ID'].'" AND sdc.ProdiID = "'.$dataProdi[$p]['ProdiID'].'" 
                                                    AND co.ProdiID = "'.$dataProdi[$p]['ProdiID'].'" AND co.SemesterID = "'.$data_arr['SemesterID'].'"
                                                    ORDER BY co.Semester ASC ')->result_array();
                    $smt = '';
                    if(count($dataSMT)>0){
                        $k = true;
                        foreach ($dataSMT AS $itsm){
                            $smt = $smt.''.(($k==true) ? "" : ", ").''.$itsm['Semester'];
                            $k = false;
                        }
                    }

                    $koma = ($p!=0)? ',' : '';
                    $Prodi = $Prodi.''.$koma.' '.$dataProdi[$p]['Code'].' ('.$smt.')';
                }
            }

            $Student = $this->m_api->__getStudentByScheduleID($row['ID']);

            $btnAct = '<div class="btn-group">
                      <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-edit"></i> <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a href="'.base_url('academic/timetables/list/edit/'.$data_arr['SemesterID'].'/'.$row['ID'].'/'.str_replace(" ","-",$row['MKNameEng'])).'">Edit Course</a></li>
                        <li><a href="'.base_url('academic/timetables/list/edit-schedule/'.$data_arr['SemesterID'].'/'.$row['ID'].'/'.str_replace(" ","-",$row['MKNameEng'])).'">Edit Schedule</a></li>
                        
                        <li role="separator" class="divider"></li>
                        <li><a href="javascript:void(0);" class="btnTimetablesEditDelete" data-group="'.$row['ClassGroup'].'" data-id="'.$row['ID'].'">Delete</a></li>
                      </ul>
                    </div>';

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['ClassGroup'].''.$SubSesi.''.$Attendance.'</div>';
            $nestedData[] = '<div  style="text-align:left;"><b>'.$row['MKCode'].' - '.$row['MKName'].'</b><br/><i style="color: #9e9e9e;">'.$row['MKNameEng'].'</i>
                                                    <br/><span>Prodi : '.$Prodi.'</span></div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['Credit'].'</div>';
            $nestedData[] = '<div  style="text-align:left;"><span style="color:#0968b3;">(Co) '.$row['CoordinatorName'].'</span>'.$TeamTeaching.'</div>';
            $nestedData[] = '<div  style="text-align:center;"><a href="javascript:void(0);" class="showStudent" data-smtid="'.$data_arr['SemesterID'].'" 
                                        data-scheduleid="'.$row['ID'].'" data-cdid="'.$row['CDID'].'" data-course="'.$row['ClassGroup'].' | '.$row['MKCode'].' - '.$row['MKNameEng'].'" >'.count($Student).'</a>
                                        </div>';

//            $nestedData[] = '<div style="text-align:center;">
//                                    <a href="javascript:void(0)" class="btn-sw-std"
//                                    data-smtid="'.$row['SemesterID'].'"
//                                    data-scheduleid="'.$row['ID'].'"
//                                    data-flag="sp" data-cdid="'.$row['CDID'].'">'.$Students.'</a> of
//                                    <a href="javascript:void(0)" class="btn-sw-std"
//                                    data-smtid="'.$row['SemesterID'].'"
//                                    data-scheduleid="'.$row['ID'].'" data-flag="std"
//                                    data-cdid="'.$row['CDID'].'">'.$StudentsNY.'</a></div>';

            $nestedData[] = '<div  style="text-align:center;">'.$btnAct.'</div>';
            $nestedData[] = '<div  style="text-align:right;">'.$ScheduleDetails.'</div>';
//            $nestedData[] = '<div  style="text-align:center;">'.$row['Room'].'</div>';

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


    }

    public function getMonitoringAllStudent(){
        $requestData= $_REQUEST;
        $data_arr = $this->getInputToken();


        $w_year = ($data_arr['Year']!='' && $data_arr['Year']!=null) ? ' AND auts.Year = "'.$data_arr['Year'].'" ' : '';
        $w_prodi = ($data_arr['ProdiID']!='' && $data_arr['ProdiID']!=null) ? ' AND auts.ProdiID = "'.$data_arr['ProdiID'].'" ' : '';

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $wl = 'LIKE "%'.$search.'%"';
            $dataSearch = ' AND (auts.NPM '.$wl.' 
                                    OR auts.Name '.$wl.')';
        }

        $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year 
                                          FROM db_academic.auth_students auts 
                                          WHERE ( auts.StatusStudentID = "3" '.$w_year.' '.$w_prodi.' ) '.$dataSearch.' 
                                          ORDER BY NPM ASC';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++) {
            $nestedData = array();
            $row = $query[$i];

            $db_ = 'ta_'.$row['Year'];
            $dataCourse = $this->db->query('SELECT mk.MKCode, mk.NameEng, s.ClassGroup, s.ID AS ScheduleID, em.Name AS Lecturer  FROM '.$db_.'.study_planning sp 
                                                        LEFT JOIN db_academic.schedule s ON (s.ID = sp.ScheduleID)
                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                        WHERE sp.SemesterID = "'.$data_arr['SemesterID'].'" 
                                                        AND sp.NPM = "'.$row['NPM'].'"
                                                         GROUP BY sp.ScheduleID ORDER BY mk.MKCode ASC ')->result_array();

            $course = '';
            if(count($dataCourse)>0){
                for($c=0;$c<count($dataCourse);$c++){
                    $d = $dataCourse[$c];

//                    echo 'SELECT attd_s.M1, attd_s.M2, attd_s.M3, attd_s.M4, attd_s.M5, attd_s.M6, attd_s.M7, attd_s.M8, attd_s.M9,
//                                                            attd_s.M10, attd_s.M11, attd_s.M12, attd_s.M13, attd_s.M14
//                                                            FROM  db_academic.attendance_students attd_s
//                                                            LEFT JOIN db_academic.attendance attd ON (attd_s.ID_Attd = attd.ID)
//                                                            WHERE attd_s.NPM = "'.$row['NPM'].'" AND attd.ScheduleID = "'.$d['ScheduleID'].'"';

                    // Get Attendance
                    $dataAttd = $this->db->query('SELECT attd_s.M1, attd_s.M2, attd_s.M3, attd_s.M4, attd_s.M5, attd_s.M6, attd_s.M7, attd_s.M8, attd_s.M9,
                                                            attd_s.M10, attd_s.M11, attd_s.M12, attd_s.M13, attd_s.M14
                                                            FROM  db_academic.attendance_students attd_s
                                                            LEFT JOIN db_academic.attendance attd ON (attd_s.ID_Attd = attd.ID)
                                                            WHERE attd_s.NPM = "'.$row['NPM'].'" AND attd.ScheduleID = "'.$d['ScheduleID'].'" ')->result_array();

                    $dataCourse[$c]['Details'] = $dataAttd;

                    $MaxMeet = 14 * count($dataAttd);
                    $TotalMeet = 0;
                    foreach ($dataAttd AS $item){

                        $TotalMeet = ($item['M1']=='1' || $item['M1']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M2']=='1' || $item['M2']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M3']=='1' || $item['M3']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M4']=='1' || $item['M4']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M5']=='1' || $item['M5']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M6']=='1' || $item['M6']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M7']=='1' || $item['M7']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M8']=='1' || $item['M8']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M9']=='1' || $item['M9']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet =  ($item['M10']=='1' || $item['M10']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet =  ($item['M11']=='1' || $item['M11']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet =  ($item['M12']=='1' || $item['M12']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet =  ($item['M13']=='1' || $item['M13']==1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet =  ($item['M14']=='1' || $item['M14']==1) ? $TotalMeet + 1 : $TotalMeet + 0;

                    }

                    $PersenHadir = ($TotalMeet!=0) ? round($TotalMeet/$MaxMeet,2) * 100 : 0;

                    if($PersenHadir <= $data_arr['Percentage']){
                        $course = $course.'<div> - '.$d['ClassGroup'].' | <span style="color:#03a9f4;">'.$d['MKCode'].' - '.$d['NameEng'].'</span> | <span style="color:#009688;"><i class="fa fa-user margin-right"></i> '.$d['Lecturer'].' </span>| Attendance : <b>'.$PersenHadir.' %</b></div>';
                    } else if($PersenHadir > $data_arr['Percentage']){
                        $course = $course.' <div style="color:#ccc;">- '.$d['ClassGroup'].' | '.$d['MKCode'].' - '.$d['NameEng'].' | <i class="fa fa-user margin-right"></i> '.$d['Lecturer'].' | Attendance : <b>'.$PersenHadir.' %</b></div>';
                    }

                }
            }

//            print_r($dataCourse);
//            exit;

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['NPM'].'</div>';
            $nestedData[] = '<div  style="text-align:left;">'.$row['Name'].'</div>';
            $nestedData[] = '<div  style="text-align:left;">'.$course.'</div>';

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
    }


    public function crudCategoryClassroomVreservation()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0) {
            if($data_arr['action'] == 'read') {
                $data = $this->m_master->showData_array('db_reservation.category_room');
                for ($i=0; $i < count($data); $i++) {
                    if ($data[$i]['Approver1']) {
                        $y= json_decode($data[$i]['Approver1']);
                        $z = $data[$i]['Approver1'];
                        $x = array();
                        for ($l=0; $l < count($y); $l++) {
                            // Find User Type
                            $UserType = $this->m_master->caribasedprimary('db_reservation.cfg_group_user','ID',$y[$l]->UserType);
                            $UserType = $UserType[0]['GroupAuth'];

                            // cek Type Approver
                            $TypeApprover = $y[$l]->TypeApprover;
                            $ApproverGet = $y[$l]->Approver;
                            switch ($TypeApprover) {
                                case 'Division':
                                    $Approver = $this->m_master->caribasedprimary('db_employees.division','ID',$ApproverGet);
                                    $Approver = $Approver[0]['Division'];
                                    break;

                                case 'Position':
                                    $Approver = $this->m_master->caribasedprimary('db_employees.position','ID',$ApproverGet);
                                    $Approver = $Approver[0]['Position'];
                                    break;
                                case 'Employees':
                                    $Approver = $this->m_master->caribasedprimary('db_employees.employees','NIP',$ApproverGet);
                                    $Approver = $Approver[0]['NIP'].' - '.$Approver[0]['Name'];
                                    break;

                            }

                            $tanda = ($l==0) ? '*   ' : '';
                            $x[] = $tanda.$UserType.' -> '.$TypeApprover.' -> '.$Approver;
                        }
                        $data[$i]['Approver1'] = implode('<br>*  ', $x);
                        $data[$i]['Approver1_ori'] = str_replace('"', "'", $z) ;
                    }
                    if ($data[$i]['Approver2']) {
                        $y= json_decode($data[$i]['Approver2']);
                        $x = array();
                        for ($l=0; $l < count($y); $l++) {
                            $Name = $this->m_master->caribasedprimary('db_employees.division','ID',$y[$l]);
                            $tanda = ($l==0) ? '*   ' : '';
                            $x[] = $tanda.$Name[0]['Division'];
                        }
                        $data[$i]['Approver2'] = implode('<br>*  ', $x);
                        $data[$i]['Approver2_ori'] = $y;
                    }
                }

                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'add'){
                $result = '';
                $formData = (array) $data_arr['formData'];
                $Approver1 = json_encode($data_arr['Approver1']) ;
                $Approver2 = json_encode($data_arr['Approver2']) ;
                $formData = $formData + array('Approver1' => $Approver1,'Approver2' => $Approver2);
                $this->db->insert('db_reservation.category_room',$formData);
                return print_r(json_encode($result));
            }
            else if($data_arr['action'] == 'edit'){
                $formData = (array) $data_arr['formData'];
                $Approver1 = json_encode($data_arr['Approver1']) ;
                $Approver2 = json_encode($data_arr['Approver2']) ;
                $formData = $formData + array('Approver1' => $Approver1,'Approver2' => $Approver2);
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->update('db_reservation.category_room',$formData);
                $result = array(
                    'inserID' => $ID
                );

                return print_r(json_encode($result));

            }
            else if($data_arr['action'] == 'delete'){
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->delete('db_reservation.category_room');
                return print_r($ID);
            }

        }
    }


    public function getDataStudyPlanning(){
        $requestData= $_REQUEST;
        $data_arr = $this->getInputToken();

        $w_year = ($data_arr['Year']!='') ? ' AND auts.Year = "'.$data_arr['Year'].'" ' : '' ;
        $w_prodi = ($data_arr['ProdiID']!='') ? ' AND auts.ProdiID = "'.$data_arr['ProdiID'].'" ' : '' ;

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $wl = 'LIKE "%'.$search.'%"';
            $dataSearch = ' AND (auts.NPM '.$wl.' 
                                    OR auts.Name '.$wl.')';
        }

        $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiGroupID, em.Name AS MentorName, em.NIP AS MentorNIP 
                                          FROM db_academic.auth_students auts 
                                          LEFT JOIN db_academic.mentor_academic mac ON (mac.NPM = auts.NPM)
                                          LEFT JOIN db_employees.employees em ON (em.NIP = mac.NIP)
                                          WHERE ( auts.StatusStudentID = "3" '.$w_year.' '.$w_prodi.' ) '.$dataSearch.' 
                                          ORDER BY NPM ASC';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();


        $no = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++) {
            $nestedData = array();
            $row = $query[$i];

            // Credit
            $dataCredit = $this->m_api->getMaxCredit('ta_'.$row['Year'],$row['NPM'],$data_arr['Year'],$data_arr['SemesterID'],$data_arr['ProdiID']);

            // Get Course in std_krs
            $dataCourse = $this->db->query('SELECT sk.Status, mk.NameEng AS CourseEng, cd.TotalSKS AS Credit FROM db_academic.std_krs sk 
                                                            LEFT JOIN db_academic.schedule s ON (s.ID = sk.ScheduleID)
                                                            LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                            WHERE sk.NPM = "'.$row['NPM'].'" AND sk.SemesterID = "'.$data_arr['SemesterID'].'"
                                                             GROUP BY sk.ScheduleID ORDER BY mk.MKCode ASC')
                ->result_array();

            $totalCreditSP = 0;
            $course = '';
            if(count($dataCourse)>0){
                foreach ($dataCourse AS $item){

                    $statusKRS = '<span style="color: #9e9e9e;"><i class="fa fa-question-circle"></i> Student not yet sent</span>';
                    if($item['Status']=='1'){
                        $statusKRS = '<span style="color:#2196f3;"><i class="fa fa-clock-o"></i> Waiting approved by Mentor</span>';
                    } else if($item['Status']=='2'){
                        $statusKRS = '<span style="color:#2196f3;"><i class="fa fa-clock-o"></i> Waiting approved by Kaprodi</span>';
                    } else if($item['Status']=='3'){
                        $statusKRS = '<span style="color:green;"><i class="fa fa-check-circle"></i> Study Plan has been approved</span>';
                    } else if($item['Status']=='-2'){
                        $statusKRS = '<span style="color: #d03126;"><i class="fa fa-repeat"></i> Rejected by Mentor</span>';
                    } else if($item['Status']=='-3'){
                        $statusKRS = '<span style="color: #d03126;"><i class="fa fa-repeat"></i> Rejected by Kaprodi</span>';
                    }

                    $course = $course.'<div><b>'.$item['CourseEng'].' ('.$item['Credit'].')</b> | Status : '.$statusKRS.'</div>';
                    $totalCreditSP = $totalCreditSP + $item['Credit'];
                }
            }

            // Get Payment
            $datapayment = $this->m_api->getPayment($data_arr['SemesterID'],$row['NPM']);

            $BPPPay_Status = '';
            $CreditPay_Status = '';

            if(count($datapayment)>0){
                foreach ($datapayment AS $item){
                    if($item['PTID']==2 || $item['PTID']=='2'){
                        $BPPPay_Status = $item['Status'];
                    } else if ($item['PTID']==3 || $item['PTID']=='3'){
                        $CreditPay_Status = $item['Status'];
                    }
                }
            }

            $BPPPay = ($BPPPay_Status!='' && $BPPPay_Status!='0' && $BPPPay_Status!=0) ? '<i class="fa fa-check" style="color: green;"></i>' : '-';
            $CreditPay = ($CreditPay_Status!='' && $CreditPay_Status!='0' && $CreditPay_Status!=0) ? '<i class="fa fa-check" style="color: green;"></i>' : '-';

            $ProdiGroupID = ($row['ProdiGroupID']!='' && $row['ProdiGroupID']!=null) ? $row['ProdiGroupID'] : '-';
            $btnAction = ($BPPPay_Status!='' && $BPPPay_Status!='0' && $BPPPay_Status!=0)
                ? '<a href="'.base_url('academic/study-planning/course-offer/'.$data_arr['SemesterID'].'/'.$ProdiGroupID.'/'.$row['NPM']).'" class="btn btn-sm btn-default btn-default-primary"><i class="fa fa-pencil"></i></a>'
                : '<span style="color: red;">BPP Unpaid</span>';

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:left;"><b>'.$row['Name'].'</b><br/>'
                .$row['NPM'].'<br/><span style="color: #0b97c4;">Last IPS : '.number_format(round($dataCredit['LastIPS'],2),2).
                ' | IPK : '.number_format(round($dataCredit['IPK'],2),2).'</span></div>';
            $nestedData[] = '<div  style="text-align:left;">'.$row['MentorName'].'<br/>'.$row['MentorNIP'].'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$BPPPay.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$CreditPay.'</div>';
            $nestedData[] = '<div  style="text-align:left;">'.$course.'</div>';
            $nestedData[] = '<div  style="text-align:center;"><u style="color: #2196f3;">'.$totalCreditSP.'</u> of '.$dataCredit['MaxCredit']['Credit'].'</div>';
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

    }

    public function getListStudentKrsOnline(){

        $requestData= $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $w_status = ($data_arr['Status']!='') ? ' AND auts.StatusStudentID = "'.$data_arr['Status'].'"' : '';
        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( auts.Name LIKE "%'.$search.'%" OR auts.NPM LIKE "%'.$search.'%") ';
        }

        $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiGroupID, ps.NameEng AS Prodi, ss.Description AS StatusStudent  
                              FROM db_academic.mentor_academic ma
                              LEFT JOIN db_academic.auth_students auts ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              WHERE ( ma.NIP = "'.$data_arr['NIP'].'" '.$w_status.' ) '.$dataSearch.' ORDER BY ma.NPM ASC';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start']+1;
        $data = array();
        for($i=0;$i<count($query);$i++) {
            $nestedData = array();
            $row = $query[$i];

            // Get Photo
            $DBStudent = 'ta_'.$row['Year'];
            $dataPhoto = $this->db->select('Photo')->get_where($DBStudent.'.students',array('NPM' => $row['NPM']),1)->result_array();
            $path = 'uploads/students/'.$DBStudent.'/'.$dataPhoto[0]['Photo'];
            $Photo = base_url('images/icon/student.png');
            if(file_exists($path) && $dataPhoto[0]['Photo']!=''){
                $Photo = base_url('uploads/students/'.$DBStudent.'/'.$dataPhoto[0]['Photo']);
            }

            // Semester
            $Semester = $this->m_api->getSemesterStudentByYear($data_arr['SemesterID'],$row['Year']);

            $token_npm = $this->jwt->encode(array('SemesterID' => $data_arr['SemesterID'],'NPM' => $row['NPM'],'ProdiGroupID' => $row['ProdiGroupID']),'UAP)(*');
            $btn = ($data_arr['Status']!='' && $data_arr['Status']==3) ? '<a href="'.url_sign_in_lecturers.'krs-online/list-student/approved-mentor/'.$token_npm.'" class="btn btn-sm btn-default">
                        <i class="fa fa-edit"></i>
                      </a>' : '-';

            $dataCourse = $this->db->query('SELECT mk.NameEng AS CourseEng, mk.MKCode, s.ClassGroup, stdk.Status FROM db_academic.std_krs stdk 
                                                      LEFT JOIN db_academic.schedule s ON (s.ID = stdk.ScheduleID)
                                                      LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = stdk.ScheduleID)
                                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                      WHERE stdk.SemesterID = "'.$data_arr['SemesterID'].'" 
                                                      AND stdk.NPM = "'.$row['NPM'].'"
                                                       GROUP BY stdk.ScheduleID
                                                       ORDER BY mk.MKCode ')->result_array();

            $course = '';
            if(count($dataCourse)>0){
                foreach ($dataCourse AS $item){

                    $Status = '-';
                    if($item['Status']==0 || $item['Status']=='0'){
                        $Status = '<span style="color: #9e9e9e;"><i class="fa fa-hourglass-half"></i> Student not yet send</span>';
                    }
                    else if($item['Status']==1 || $item['Status']=='1'){
                        $Status = '<span style="color: #00bcd4;"><i class="fa fa-clock-o"></i> Waiting approval</span>';
                    }
                    else if($item['Status']==-2 || $item['Status']=='-2'){
                        $Status = '<span style="color: darkred;"><i class="fa fa-repeat margin-right"></i> Rejected by Mentor</span>';
                    }
                    else if($item['Status']==-3 || $item['Status']=='-3'){
                        $Status = '<span style="color: darkred;"><i class="fa fa-repeat margin-right"></i> Rejected by Kaprodi</span>';
                    }
                    else if($item['Status']==2 || $item['Status']=='2'){
                        $Status = '<span style="color: green;"><i class="fa fa-check-circle"></i> Approved by Mentor</span>';
                    }
                    else if($item['Status']==3 || $item['Status']=='3'){
                        $Status = '<span style="color: green;"><i class="fa fa-check-circle"></i> Approved by Kaprodi</span>';
                    }

                    $course = $course.'<div>'.$item['ClassGroup'].' | <span style="color: #2196f3;">'.$item['MKCode'].' - '.$item['CourseEng'].'</span> | Status : '.$Status.'</div>';
                }
            } else {
                $course = '-';
            }

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:center;"><img data-src="'.$Photo.'" class="img-rounded img-fitter" width="40" height="50"></div>';
            $nestedData[] = '<div  style="text-align:left;"><b>'.$row['Name'].'</b><br/><span>'.$row['NPM'].'</span><br/>'.$row['Prodi'].'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$Semester.'</div>';
            $nestedData[] = '<div  style="text-align:left;">'.$course.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$btn.'</div>';
            $nestedData[] = '<div  style="text-align:center;font-size: 12px;">'.ucwords(strtolower($row['StatusStudent'])).'</div>';

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

    }

    public function getListStudentKrsOnlineKaprodi(){
        $requestData= $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $w_status = ($data_arr['Status']!='') ? ' AND auts.StatusStudentID = "'.$data_arr['Status'].'"' : '';
        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( auts.Name LIKE "%'.$search.'%" OR auts.NPM LIKE "%'.$search.'%") ';
        }

        $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiGroupID, ps.NameEng AS Prodi, ss.Description AS StatusStudent, em.Name AS MentorName,   
                              ma.NIP AS MentorNIP
                              FROM  db_academic.auth_students auts 
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                              WHERE ( auts.ProdiID = "'.$data_arr['ProdiID'].'" '.$w_status.' ) '.$dataSearch.' ORDER BY auts.NPM ASC';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start']+1;
        $data = array();
        for($i=0;$i<count($query);$i++) {
            $nestedData = array();
            $row = $query[$i];

            // Get Photo
            $DBStudent = 'ta_'.$row['Year'];
            $dataPhoto = $this->db->select('Photo')->get_where($DBStudent.'.students',array('NPM' => $row['NPM']),1)->result_array();
            $path = 'uploads/students/'.$DBStudent.'/'.$dataPhoto[0]['Photo'];
            $Photo = base_url('images/icon/student.png');
            if(file_exists($path) && $dataPhoto[0]['Photo']!=''){
                $Photo = base_url('uploads/students/'.$DBStudent.'/'.$dataPhoto[0]['Photo']);
            }

            // Semester
            $Semester = $this->m_api->getSemesterStudentByYear($data_arr['SemesterID'],$row['Year']);


            // Course
            $dataCourse = $this->db->query('SELECT mk.NameEng AS CourseEng, mk.MKCode, s.ClassGroup, stdk.Status FROM db_academic.std_krs stdk 
                                                      LEFT JOIN db_academic.schedule s ON (s.ID = stdk.ScheduleID)
                                                      LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = stdk.ScheduleID)
                                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                      WHERE stdk.SemesterID = "'.$data_arr['SemesterID'].'" 
                                                      AND stdk.NPM = "'.$row['NPM'].'"
                                                       GROUP BY stdk.ScheduleID
                                                       ORDER BY mk.MKCode ')->result_array();

            $course = '';
            if(count($dataCourse)>0){
                foreach ($dataCourse AS $item){

                    $Status = '-';
                    if($item['Status']==0 || $item['Status']=='0'){
                        $Status = '<span style="color: #9e9e9e;"><i class="fa fa-hourglass-half"></i> Student not yet send</span>';
                    }
                    else if($item['Status']==1 || $item['Status']=='1'){
                        $Status = '<span style="color: #9e9e9e;"><i class="fa fa-clock-o"></i> Waiting approval <b>mentor</b></span>';
                        if($row['MentorNIP'] == $data_arr['NIP']){
                            $Status = '<span style="color: #00bcd4;"><i class="fa fa-clock-o"></i> Waiting approval <b>kaprodi</b></span>';
                        }

                    }
                    else if($item['Status']==-2 || $item['Status']=='-2'){
                        $Status = '<span style="color: darkred;"><i class="fa fa-repeat margin-right"></i> Rejected by Mentor</span>';
                    }
                    else if($item['Status']==-3 || $item['Status']=='-3'){
                        $Status = '<span style="color: darkred;"><i class="fa fa-repeat margin-right"></i> Rejected by Kaprodi</span>';
                    }
                    else if($item['Status']==2 || $item['Status']=='2'){
                        $Status = '<span style="color: #00bcd4;"><i class="fa fa-clock-o"></i> Waiting approval <b>kaprodi</b></span>';
                    }
                    else if($item['Status']==3 || $item['Status']=='3'){
                        $Status = '<span style="color: green;"><i class="fa fa-check-circle"></i> Approved by Kaprodi</span>';
                    }

                    $course = $course.'<div>'.$item['ClassGroup'].' | <span style="color: #2196f3;">'.$item['MKCode'].' - '.$item['CourseEng'].'</span> | Status : '.$Status.'</div>';
                }
            } else {
                $course = '-';
            }

            $token_npm = $this->jwt->encode(array('SemesterID' => $data_arr['SemesterID'],'NPM' => $row['NPM'], 'ProdiGroupID' => $row['ProdiGroupID']),'UAP)(*');
            $btn = ($data_arr['Status']!='' && $data_arr['Status']==3) ? '<a href="'.url_sign_in_lecturers.'krs-online/list-student/approved-kaprodi/'.$token_npm.'" class="btn btn-sm btn-default">
                        <i class="fa fa-edit"></i>
                      </a>' : '-';

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:center;"><img data-src="'.$Photo.'" class="img-rounded img-fitter" width="40" height="50"></div>';
            $nestedData[] = '<div  style="text-align:left;"><b>'.$row['Name'].'</b><br/><span>'.$row['NPM'].'</span><br/><span class="label label-info" style="position: relative;left: 0px;">Mentor : '.$row['MentorName'].'</span></div>';
            $nestedData[] = '<div  style="text-align:center;">'.$Semester.'</div>';
            $nestedData[] = '<div  style="text-align:left;">'.$course.'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$btn.'</div>';
            $nestedData[] = '<div  style="text-align:center;font-size: 12px;">'.ucwords(strtolower($row['StatusStudent'])).'</div>';

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

    }

    public function crudNotification(){

        $data_arr = $this->getInputToken();

        if($data_arr['action']=='addNewNotification'){
            // Get Personal
            $IDDivision = $this->session->userdata('IDdepartementNavigation');
            $dataUser = $this->db->select('NIP')->get_where('db_employees.rule_users',array('IDDivision' => $IDDivision))->result_array();

            if(count($dataUser)>0){

                $dataInsert = (array) $data_arr['dataInsert'];

                $this->db->insert('db_notifikasi.notification',$dataInsert);
                $insert_id = $this->db->insert_id();

                // Add in n personal
                for($i=0;$i<count($dataUser);$i++){
                    $insert_n_personal = array(
                        'ID_notification' => $insert_id,
                        'Div' => $IDDivision,
                        'People' => $dataUser[$i]['NIP']
                    );
                    $this->db->insert('db_notifikasi.n_personal',$insert_n_personal);
                }

            }

            return print_r(1);

        }
        else if($data_arr['action']=='hideNotifBrowser'){

            $this->db->set('ShowNotif', '1');
            $this->db->where('ID', $data_arr['IDUser']);
            $this->db->update('db_notifikasi.n_personal');

            return print_r(1);
        }




    }

    public function crudLog(){
        $data_arr = $this->getInputToken();

        if($data_arr['action']=='readLog'){
            $UserID = $data_arr['UserID'];
            $dataLog = $this->m_log->readDataLog($UserID);
            return print_r(json_encode($dataLog));
        }
        else if($data_arr['action']=='getTotalUnreadLog'){

            $UserID = $data_arr['UserID'];
            $data = $this->db->select('ID')->get_where('db_notifikasi.logging_user',
                array('UserID' => $UserID, "StatusRead" => "0"))->result_array();
            return print_r(json_encode(count($data)));
        }

    }

    public function crudTransferStudent(){

        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if($data_arr['action'] == 'readFromStudentTransfer'){
                $data = $this->db->select('NPM,Name')->get_where('db_academic.auth_students'
                    ,array('Year' => $data_arr['ClassOf'] ,'ProdiID' => $data_arr['ProdiID']))->result_array();

                return print_r(json_encode($data));
            }
            else if($data_arr['action'] == 'readReason'){
                $data = $this->db->get('db_academic.transfer_type')->result_array();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='checkNPMTransferStudent'){
                $data = $this->db->select('NPM')->get_where('db_academic.auth_students'
                    ,array('NPM' => $data_arr['NPM']))->result_array();

                $dataEmb = $this->db->select('NIP')->get_where('db_employees.employees'
                    ,array('NIP' => $data_arr['NPM']))->result_array();


                if(count($data)>0 || count($dataEmb)>0){
                    $result = array('Status' => 0);
                } else {
                    $result = array('Status' => 1);
                }

                return print_r(json_encode($result));
            }

            else if($data_arr['action']=='addingTransferStudent'){

                $StatusBefore = '';

                // DB TA - Get Data Student Lama
                $db_f = 'ta_'.$data_arr['fromClassOf'];
                $dataStd_f = $this->db->limit(1)->get_where($db_f.'.students',array('NPM' => $data_arr['fromStudent']))->result_array();

                $Possword_Old = '';
                if(count($dataStd_f)){
                    $d = $dataStd_f[0];
                    $StatusBefore = $dataStd_f[0]['StatusStudentID'];
                    unset($d['ID']);
                    $d['ProdiID'] = $data_arr['toProdi'];
                    $d['NPM'] = $data_arr['toNewNPM'];
                    $d['ClassOf'] = $data_arr['toClassOf'];
                    $d['Address'] = trim($d['Address']);
                    $d['StatusStudentID'] = 3;

                    if($d['DateOfBirth']!=null && $d['DateOfBirth']!='' && $d['DateOfBirth']!='0000-00-00'){
                        $d_exp = explode('-',$d['DateOfBirth']);
                        $Possword_Old = md5($d_exp[2].''.$d_exp[1].''.substr($d_exp[0],2,2));
                    }

                    // Insert To
                    $db_t = 'ta_'.$data_arr['toClassOf'];
                    $this->db->insert($db_t.'.students',$d);

                    // Update Data Lama
                    $this->db->set('StatusStudentID', 15);
                    $this->db->where('ID', $dataStd_f[0]['ID']);
                    $this->db->update($db_f.'.students');
                    $this->db->reset_query();

                }



                // DB AUTH Student - Get data lama
                $d_aut_f = $this->db->get_where('db_academic.auth_students',array('NPM' => $data_arr['fromStudent']))->result_array();
                if(count($d_aut_f)>0){
                    $d = $d_aut_f[0];
                    unset($d['ID']);
                    unset($d['ProdiGroupID']);
                    unset($d['Password']);
                    $d['NPM'] = $data_arr['toNewNPM'];
                    $d['Name'] = trim(ucwords($d['Name']));
                    $d['ProdiID'] = $data_arr['toProdi'];
                    $d['Year'] = $data_arr['toClassOf'];
                    $d['Status'] = '-1';
                    $d['EmailPU'] = $data_arr['toNewNPM'].'@podomorouniversity.ac.id';
                    $d['Password_Old'] = $Possword_Old;
                    $d['StatusStudentID'] = 3;

                    if($data_arr['TransferTypeID']!=1 || $data_arr['TransferTypeID']!='1'){
                        unset($d['Pay_Cond']);
                    }

                    // Insert Ke DB Auth
                    $this->db->insert('db_academic.auth_students',$d);

                    // Update Data Lama
                    $this->db->set('StatusStudentID', 15);
                    $this->db->where('ID', $d_aut_f[0]['ID']);
                    $this->db->update('db_academic.auth_students');
                    $this->db->reset_query();
                }


                // Insert Ke dalam tabel transfer
                $dataIns = array(
                    'TransferTypeID' => $data_arr['TransferTypeID'],
                    'ClassOfBefore' => $data_arr['fromClassOf'],
                    'StatusBefore' => $StatusBefore,
                    'ProdiBefore' => $data_arr['fromProdi'],
                    'Before' => $data_arr['fromStudent'],
                    'ClassOfAfter' => $data_arr['toClassOf'],
                    'ProdiAfter' => $data_arr['toProdi'],
                    'After' => $data_arr['toNewNPM'],
                    'CreateBy' => $data_arr['CreateBy'],
                    'CreateAt' => $data_arr['CreateAt']
                );

                $this->db->insert('db_academic.transfer_student',$dataIns);

                return print_r(1);

            }
            else if($data_arr['action']=='removeTransverStudent'){

                $dataTS = $this->db->get_where('db_academic.transfer_student',array('ID' => $data_arr['ID']))
                    ->result_array();

                if(count($dataTS)>0){
                    $d = $dataTS[0];
                    // Data After
                    $After_NPM = $d['After'];
                    $After_DB =  'ta_'.$d['ClassOfAfter'];

                    $tables = array($After_DB.'.students', 'db_academic.auth_students');
                    $this->db->where('NPM', $After_NPM);
                    $this->db->delete($tables);
                    $this->db->reset_query();

                    $this->db->set('StatusStudentID', $d['StatusBefore']);
                    $this->db->where('NPM', $d['Before']);
                    $this->db->update('db_academic.auth_students');
                    $this->db->reset_query();

                    $Before_DB =  'ta_'.$d['ClassOfBefore'];
                    $this->db->set('StatusStudentID', $d['StatusBefore']);
                    $this->db->where('NPM', $d['Before']);
                    $this->db->update($Before_DB.'.students');
                    $this->db->reset_query();

                    $this->db->where('ID', $data_arr['ID']);
                    $this->db->delete('db_academic.transfer_student');
                }

                return print_r(1);

            }
            else if($data_arr['action']=='readDataTransferStudent'){
                $TSID = $data_arr['TSID'];

                // Get Data Transfer
                $data = $this->db->query('SELECT ts.*, ps_b.Code AS CodeProdi_B,
                                                      ps_a.Code AS CodeProdi_A, auth.Name AS StudentName
                                                       FROM db_academic.transfer_student ts
                                                      LEFT JOIN db_academic.program_study ps_b ON (ps_b.ID = ts.ProdiBefore)
                                                      LEFT JOIN db_academic.program_study ps_a ON (ps_a.ID = ts.ProdiAfter)
                                                      LEFT JOIN db_academic.auth_students auth ON (auth.NPM = ts.After)
                                                      WHERE ts.ID = "'.$TSID.'" LIMIT 1
                                                      ')->result_array();

                if(count($data)>0){
                    $dt = $data[0];

                    // Get Semester Before
                    $C_O_Before = $dt['ClassOfBefore'];
                    $DB_B = 'ta_'.$dt['ClassOfBefore'];
                    $NPM_B = $dt['Before'];
                    $dSem_B = $this->db->get_where('db_academic.semester',array('Year >=' => $C_O_Before))->result_array();
                    $arrSemester_B = [];
                    $NoSem_B = 0;
                    if(count($dSem_B)>0){
                        for($i=0;$i<count($dSem_B);$i++){
                            $dt_s = $dSem_B[$i];
                            $NoSem_B ++;
//                            $course = $this->db->get_where($DB_B.'.study_planning',array('NPM' => $NPM_B, 'SemesterID' => $dt_s['ID']))->result_array();
                            $System = ($dt_s['ID']>=13) ? 1 : 0;
                            $course = $this->m_rest->getDataKHS($DB_B,$NPM_B,$dt_s['ID'],'',$System);

                            if(count($course)>0){
                                $arr = array(
                                    'Semester' => $NoSem_B,
                                    'SemesterID' => $dt_s['ID'],
                                    'SemesterName' => $dt_s['Name'],
                                    'Course' => $course
                                );

                                array_push($arrSemester_B,$arr);
                            }


                            if($dt_s['Status']==1 || $dt_s['Status']=='1'){
                                break;
                            }

                        }
                    }



                    // Get Semester After
                    $C_O_After = $dt['ClassOfAfter'];
                    $DB_A = 'ta_'.$dt['ClassOfAfter'];
                    $NPM_A = $dt['After'];

                    // Get MHSWID
                    $dataMhsw = $this->db->select('ID')->get_where($DB_A.'.students',array('NPM' => $NPM_A))->result_array();
                    $data[0]['MhswID'] = $dataMhsw[0]['ID'];

                    $dSem_A = $this->db->get_where('db_academic.semester',array('Year >=' => $C_O_After))->result_array();
                    $arrSemester_A = [];
                    $NoSem_A = 0;
                    if(count($dSem_A)>0){
                        for($i=0;$i<count($dSem_A);$i++){
                            $dt_s = $dSem_A[$i];
                            $NoSem_A ++;

                            $System = ($dt_s['ID']>=13) ? 1 : 0;
                            $course = $this->m_rest->getDataKHS($DB_A,$NPM_A,$dt_s['ID'],'',$System);

                            $arr = array(
                                'Semester' => $NoSem_A,
                                'SemesterID' => $dt_s['ID'],
                                'SemesterName' => $dt_s['Name'],
                                'Course' => $course
                            );

                            array_push($arrSemester_A,$arr);

                            if($dt_s['Status']==1 || $dt_s['Status']=='1'){
                                break;
                            }


                        }

                    }

                    $result = array(
                        'DataTransfer' => $data,
                        'Before' => $arrSemester_B,
                        'After' => $arrSemester_A
                    );
                }


                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='getCourseTransferStudent'){

                $ClassOf = $data_arr['ClassOf'];
                $Semester = $data_arr['Semester'];
                $ProdiID = $data_arr['ProdiID'];

                $dataCourse = $this->db->query('SELECT cd.ID AS CDID, cd.MKID, cd.TotalSKS AS Credit, mk.MKCode, mk.Name, mk.NameEng  
                                                                FROM db_academic.curriculum_details cd
                                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                                LEFT JOIN db_academic.curriculum c ON (c.ID =  cd.CurriculumID)
                                                                WHERE cd.Semester = "'.$Semester.'" 
                                                                 AND c.Year = "'.$ClassOf.'" 
                                                                 AND cd.ProdiID = "'.$ProdiID.'" ORDER BY cd.MKID ASC')->result_array();


                return print_r(json_encode($dataCourse));

            }
            else if($data_arr['action']=='replaceCourseTransferStd'){

                // Get data coursenya
                $DB_B = $data_arr['DB_B'];
                $dataBefore = $this->db->limit(1)->get_where($DB_B.'.study_planning',array('ID' => $data_arr['SPID']))->result_array();

                $insert_id = 0;
                if(count($dataBefore)>0){
                    $d = $dataBefore[0];
                    $d['SemesterID'] = $data_arr['SemesterID'];
                    $d['MhswID'] = $data_arr['MhswID'];
                    $d['NPM'] = $data_arr['NPM_A'];
                    $d['CDID'] = $data_arr['CDID'];
                    $d['MKID'] = $data_arr['MKID'];
                    $d['Credit'] = $data_arr['Credit'];

                    unset($d['ID']);
                    $this->db->insert($data_arr['DB_A'].'.study_planning',$d);
                    $insert_id = $this->db->insert_id();

                    $insertHistory = (array) $data_arr['insertHistory'];
                    $insertHistory['SPID_After'] = $insert_id;

                    $this->db->insert('db_academic.transfer_history_conversion'
                        ,$insertHistory);

                }

                return print_r($insert_id);
            }
            else if($data_arr['action']=='removeDataTransferStudent'){

                $DB_A = 'ta_'.$data_arr['ClassOf'];
                $SPID = $data_arr['SPID'];

                // Search History
                $NPM = $data_arr['NPM_A'];
                $dataSearch = $this->db->select('ID')->get_where('db_academic.transfer_history_conversion'
                    ,array('NPM_After' => $NPM, 'TA_After' => $data_arr['ClassOf']
                    , 'SPID_After' => $SPID))->result_array();

                if(count($dataSearch)>0){
                    $THCID = $dataSearch[0]['ID'];
                    $this->db->where('ID', $THCID);
                    $this->db->delete('db_academic.transfer_history_conversion');
                }

                // Search History
                $this->db->where('ID', $SPID);
                $this->db->delete($DB_A.'.study_planning');




                return print_r(1);

            }

        }
    }

}