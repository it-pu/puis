<?php
defined('BASEPATH') or exit('No direct script access allowed');
include_once APPPATH . 'vendor/autoload.php';

use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;

class C_api extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        /*UPDATED BY FEBRI @ NOV 2019*/
        $this->load->model(array('m_api', 'm_rest', 'master/m_master', 'hr/m_hr', 'vreservation/m_reservation', 'akademik/m_tahun_akademik', 'notification/m_log', 'General_model'));
        $this->load->library(array('JWT', 'google'));
        /*END UPDATED BY FEBRI @ NOV 2019*/

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
        $data_arr = (array) $this->jwt->decode($token, $key);
        return $data_arr;
    }



    public function getKurikulumByYear()
    {

        //        $year = $this->input->get('year');

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        $result = $this->m_api->__getKurikulumByYear($data_arr['SemesterSearch'], $data_arr['year'], $data_arr['ProdiID']);

        return print_r(json_encode($result));
    }

    public function getProdi()
    {
        $data = $this->m_api->__getBaseProdi();
        return print_r(json_encode($data));
    }

    public function getProdiSelectOption()
    {
        $data = $this->m_api->__getBaseProdiSelectOption();
        return print_r(json_encode($data));
    }

    public function getProdiSelectOptionAll()
    {
        $data = $this->m_api->__getBaseProdiSelectOptionAll();
        return print_r(json_encode($data));
    }

    public function getKurikulumSelectOption()
    {
        $data = $this->m_api->__getKurikulumSelectOption();
        return print_r(json_encode($data));
    }

    public function getKurikulumSelectOptionASC()
    {
        $data = $this->m_api->__getKurikulumSelectOptionASC();
        return print_r(json_encode($data));
    }

    public function getKurikulumSelectOptionDSC()
    {
        $data = $this->m_api->__getKurikulumSelectOptionDSC();
        return print_r(json_encode($data));
    }

    public function getMKByID()
    {
        $ID = $this->input->post('idMK');
        $data = $this->m_api->__getMKByID($ID);
        return print_r(json_encode($data));
    }

    public function getSemester()
    {
        $data = $this->m_tahun_akademik->__getSemester();
        return print_r(json_encode($data));
    }

    public function getLecturer2()
    {
        $data = $this->m_api->__getLecturer();
        return print_r(json_encode($data));
    }

    public function getLecturer()
    {
        $requestData = $_REQUEST;


        $whereDef = 'em.PositionMain = "14.5" OR em.PositionMain = "14.6" OR em.PositionMain = "14.7"
        OR em.PositionOther1 = "14.7" OR em.PositionOther2 = "14.7" OR em.PositionOther3 = "14.7"';

        $totalData = $this->db->query('SELECT * FROM db_employees.employees WHERE PositionMain = "14.7"')->result_array();


        $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng
                        FROM db_employees.employees em
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        LEFT JOIN db_employees.tmp_employees te on (te.NIP = em.NIP) /*UPDATED BY FEBRI @ NOV 2019*/
                        WHERE (' . $whereDef . ')';

        if ($requestData['isappv'] === 'true') {
            $sql .= " AND (te.isApproval = 1) ";
        }
        if (!empty($requestData['search']['value'])) {
            $sql .= ' AND ( '; //UPDATED BY FEBRI @ NOV 2019
            $sql .= ' em.NIP LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR em.Name LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR ps.NameEng LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ') ORDER BY em.PositionMain, NIP ASC';
        } else {
            $sql .= 'ORDER BY em.PositionMain, NIP ASC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        }

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $jb = explode('.', $row["PositionMain"]);
            $Division = '';
            $Position = '';

            if (count($jb) > 1) {
                $dataDivision = $this->db->select('Division')->get_where('db_employees.division', array('ID' => $jb[0]), 1)->result_array()[0];
                $dataPosition = $this->db->select('Position')->get_where('db_employees.position', array('ID' => $jb[1]), 1)->result_array()[0];
                $Division = $dataDivision['Division'];
                $Position = $dataPosition['Position'];
            }

            $imgEmp = url_img_employees . '' . $row["Photo"];

            /*ADEDD BY FEBRI @ NOV 2019*/
            $isRequested = $this->General_model->fetchData("db_employees.tmp_employees", array("NIP" => $row["NIP"], "isApproval" => 1))->row();
            $needAppv = "";
            if (!empty($isRequested)) {
                if ($this->session->userdata('IDdepartementNavigation') == 13) {
                    $needAppv = '<button class="btn btn-xs btn-info btn-appv" type="button" data-nip="' . $row["NIP"] . '" title="Need approving for biodata" ><i class="fa fa-warning"></i> Need Approval</button>';
                }
            }
            /*END ADEDD BY FEBRI @ NOV 2019*/

            $nestedData[] = $row["NIP"] . $needAppv;
            $nestedData[] = $row["NIDN"];
            $nestedData[] = '<div style="text-align: center;"><img src="' . $imgEmp . '" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="' . base_url('database/lecturer-details/' . $row["NIP"]) . '" style="font-weight: bold;">' . $row["Name"] . '</a>';
            $nestedData[] = ($row["Gender"] == 'P') ? 'Female' : 'Male';
            $nestedData[] = $Division . ' - ' . $Position;
            $nestedData[] = $row["ProdiNameEng"];

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getLecturermengajar()
    {
        $requestData = $_REQUEST;

        $totalData = $this->db->query('SELECT *  FROM db_employees.employees WHERE PositionMain = "14.7"')->result_array();

        if (!empty($requestData['search']['value'])) {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng
                        FROM db_employees.employees em
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain = "14.5" OR em.PositionMain = "14.6" OR em.PositionMain = "14.7")  AND ( ';

            $sql .= ' em.NIP LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR em.Name LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR ps.NameEng LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ') ORDER BY em.PositionMain, NIP ASC';
        } else {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng
                        FROM db_employees.employees em
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain = "14.5" OR em.PositionMain = "14.6" OR em.PositionMain = "14.7")';
            $sql .= 'ORDER BY em.PositionMain, NIP ASC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        }

        $query = $this->db->query($sql)->result_array();

        // Get Semester
        $dataSemesterOption = $this->db->order_by('ID', 'DESC')->get('db_academic.semester')->result_array();

        $option = '';
        foreach ($dataSemesterOption as $item) {
            $sc = ($item['Status'] == '1') ? 'selected' : '';
            $option = $option . '<option value="' . $item['ID'] . '" ' . $sc . '>' . $item['Name'] . '</option>';
        }

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $jb = explode('.', $row["PositionMain"]);
            $Division = '';
            $Position = '';

            if (count($jb) > 1) {
                $dataDivision = $this->db->select('Division')->get_where('db_employees.division', array('ID' => $jb[0]), 1)->result_array()[0];
                $dataPosition = $this->db->select('Position')->get_where('db_employees.position', array('ID' => $jb[1]), 1)->result_array()[0];
                $Division = $dataDivision['Division'];
                $Position = $dataPosition['Position'];
            }

            $imgEmp = url_img_employees . '' . $row["Photo"];
            $tokenPDF = '';
            $NIP = $row["NIP"];



            $nestedData[] = '<div style="text-align: center;"><img src="' . $imgEmp . '" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="' . base_url('database/lecturer-details/' . $row["NIP"]) . '" style="font-weight: bold;">' . $row["Name"] . '</a>';
            $nestedData[] = $row["NIP"] . ' - ' . $row["NIDN"];
            //$nestedData[] = $row["NIDN"];

            $nestedData[] = ($row["Gender"] == 'P') ? 'Female' : 'Male';
            $nestedData[] = $Division . ' - ' . $Position;
            $nestedData[] = $row["ProdiNameEng"];
            $nestedData[] = '<div><select class="form-control option-filter filterSemester" id="filterSemester_' . $NIP . '">' . $option . '</select></div>';
            $nestedData[] = '<center><div><a href="javascript:void(0);" type="button" id="btnDownloadTugasMengajar" NIP="' . $NIP . '" class="btn btn-sm btn-success btn-round btn-action"> <i class="fa fa-download"></i> Download </a> </div></center>';
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getreqdocument()
    {
        $requestData = $_REQUEST;

        $totalData = $this->db->query('SELECT a.*, b.ID, b.TypeFiles, b.NameFiles, c.Name, c.TitleAhead, c.TitleBehind, d.Name AS Namaconfirm
                FROM db_employees.request_document AS a
                LEFT JOIN db_employees.master_files AS b ON (a.IDTypeFiles = b.ID)
                LEFT JOIN db_employees.employees AS c ON (a.NIP = c.NIP)
                LEFT JOIN db_employees.employees AS d ON (a.UserConfirm = d.NIP)
                WHERE b.RequestDocument = 1 ')->result_array();

        if (!empty($requestData['search']['value'])) {
            $sql = 'SELECT a.*, b.ID, b.TypeFiles, b.NameFiles, c.Name, c.TitleAhead, c.TitleBehind, d.Name AS Namaconfirm
                    FROM db_employees.request_document AS a
                    LEFT JOIN db_employees.master_files AS b ON (a.IDTypeFiles = b.ID)
                    LEFT JOIN db_employees.employees AS c ON (a.NIP = c.NIP) AND (a.UserConfirm = c.NIP)
                    LEFT JOIN db_employees.employees AS d ON (a.UserConfirm = d.NIP)
                    WHERE b.RequestDocument = 1  AND ( ';

            $sql .= ' c.Name LIKE "' . $requestData['search']['value'] . '%" ';
            //$sql.= ' OR b.NameFiles LIKE "'.$requestData['search']['value'].'%" ';
            //$sql.= ' OR ps.NameEng LIKE "'.$requestData['search']['value'].'%" ';
            $sql .= ') ORDER BY a.IDRequest DESC ';
        } else {
            $sql = 'SELECT a.*, b.ID, b.TypeFiles, b.NameFiles, c.Name, c.TitleAhead, c.TitleBehind, d.Name AS Namaconfirm
                    FROM db_employees.request_document AS a
                    LEFT JOIN db_employees.master_files AS b ON (a.IDTypeFiles = b.ID)
                    LEFT JOIN db_employees.employees AS c ON (a.NIP = c.NIP)
                    LEFT JOIN db_employees.employees AS d ON (a.UserConfirm = d.NIP)
                    WHERE b.RequestDocument = 1 ';
            $sql .= 'ORDER BY a.IDRequest DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        }

        $query = $this->db->query($sql)->result_array();
        $no = $requestData['start'] + 1;

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $endtimex = date('H:i', strtotime($row['EndDate']));
            $statime = 'Selesai';

            if ($endtimex == '00:00') {
                $endtimes = '<div style="text-align:center;">' . date('d M Y', strtotime($row['EndDate'])) . '</div>';
                $endtimesz = $endtimes . ' - ' . $statime;
            } else {
                $endtimesz = '<div style="text-align:center;">' . date('d M Y H:i', strtotime($row['EndDate'])) . '</div>';
            }

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = $row["NIP"] . ' - ' . $row["Name"];
            $nestedData[] = $row["NameFiles"];
            $nestedData[] = '<div style="text-align:center;">' . date('d M Y H:i', strtotime($row['RequestDate'])) . '</div>';
            $nestedData[] = $row["ForTask"];
            $nestedData[] = '<div style="text-align:center;">' . date('d M Y H:i', strtotime($row['StartDate'])) . '</div>';
            $nestedData[] = $endtimesz;
            $nestedData[] = $row["DescriptionAddress"];

            $NIP = $row["NIP"];
            $IDRequest = $row["IDRequest"];

            $tokenPDF = $this->jwt->encode(array('NIP' => $NIP, 'IDRequest' => $IDRequest), 'UAP)(*');

            if ($row['ConfirmStatus'] == 0) {
                $status = '<div class="btn-group"><button type="button" class="btn btn-sm btn-success btn-round btn-action btnapproved" requestid="' . $row["IDRequest"] . '"> <i class="glyphicon glyphicon-ok-sign"></i> Approved </button> <button type="button" class="btn btn-sm btn-danger btn-round btn-addgroup btnrejected" requestid="' . $row["IDRequest"] . '"> <i class="glyphicon glyphicon-remove-sign"></i> Rejected</button></div>';
            } else if ($row['ConfirmStatus'] == 1) {
                $status = '<a target="_blank" href="' . base_url('save2pdf/suratTugasKeluar/' . $tokenPDF) . '" type="button" class="btn btn-sm btn-success btn-round btn-action"> <i class="fa fa-download"></i> Download </a> <div> <p style="color:blue;font-size:11px;"> Approved By ' . $row["Namaconfirm"] . '</p> </div>';
            } else {

                $status = '<label class="text-danger"> Rejected <div> <p style="color:blue;font-size:11px;"> Rejected By ' . $row["Namaconfirm"] . '</p> </div></label>';
            }


            $nestedData[] = '<center>' . $status . ' </center>';
            //$nestedData[] = '<center><div class="btn-group">
            //  <button type="button" class="btn btn-sm btn-success btn-round btn-action btnapproved" requestid="'.$row["IDRequest"].'"> <i class="glyphicon glyphicon-ok-sign"></i> Approved </button>
            //  <button type="button" class="btn btn-sm btn-danger btn-round btn-addgroup btnrejected" requestid="'.$row["IDRequest"].'"> <i class="glyphicon glyphicon-remove-sign"></i> Rejected</button></div></center>';

            $data[] = $nestedData;
            $no++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getEmployees()
    {
        $requestData = $_REQUEST;

        $totalData = $this->db->query('SELECT *  FROM db_employees.employees WHERE PositionMain not like "%14%"')->result_array();

        if (!empty($requestData['search']['value'])) {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status
                        FROM db_employees.employees em
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain not like "%14%")  AND ( ';

            $sql .= ' em.NIP LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR em.Name LIKE "%' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR ps.NameEng LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ') ORDER BY NIP,em.PositionMain  ASC';
        } else {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status
                        FROM db_employees.employees em
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        WHERE (em.PositionMain not like "%14%")';
            $sql .= 'ORDER BY NIP,em.PositionMain ASC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        }

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $jb = explode('.', $row["PositionMain"]);
            $Division = '';
            $Position = '';

            if (count($jb) > 1) {
                $dataDivision = $this->db->select('Division')->get_where('db_employees.division', array('ID' => $jb[0]), 1)->result_array()[0];
                $dataPosition = $this->db->select('Position')->get_where('db_employees.position', array('ID' => $jb[1]), 1)->result_array()[0];
                $Division = $dataDivision['Division'];
                $Position = $dataPosition['Position'];
            }

            $nestedData[] = $row["NIP"];
            $nestedData[] = '<div style="text-align: center;"><img src="http://siak.podomorouniversity.ac.id/includes/foto/' . $row["Photo"] . '" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="' . base_url('database/lecturer-details/' . $row["NIP"]) . '" style="font-weight: bold;">' . $row["Name"] . '</a>';
            $nestedData[] = ($row["Gender"] == 'P') ? 'Female' : 'Male';
            $nestedData[] = $Division . ' - ' . $Position;
            $nestedData[] = $row["EmailPU"];
            $nestedData[] = ($row["Status"] == '1') ? 'Aktif' : 'Tidak Aktif';
            $nestedData[] = '<span data-smt="' . $row["NIP"] . '" class="btn btn-xs btn-edit">
                                    <i class="fa fa-pencil-square-o"></i> Edit
                                   </span>
                                   <span data-smt="' . $row["NIP"] . '" class="btn btn-xs btn-Active" data-active = "' . $row["Status"] . '">
                                    <i class="fa fa-pencil-square-o"></i> ChangeStatus
                                   </span>';
            $data[] = $nestedData;
        }

        // print_r($data);

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }


    public function getEmployeesHR()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();

        /*UPDATED BY FEBRI @  JAN 2020*/
        $dataWhere = "";
        $dataArrStatus = array();
        $dataArrGender = array();
        $dataArrReligion = array();
        $dataArrLevel = array();
        if (!empty($data_arr['Filter'])) {
            $parse = parse_str($data_arr['Filter'], $output);
            if (!empty($output['staff'])) {
                $dataWhere .= " AND (em.NIP like '%" . $output['staff'] . "%' OR em.Name  like '%" . $output['staff'] . "%' ) ";
            }
            if (!empty($output['division'])) {
                $conditionDivPo = " AND (em.PositionMain like '" . $output['division'] . ".%')";
                if (!empty($output['position'])) {
                    $conditionDivPo = " AND (em.PositionMain = '" . $output['division'] . "." . $output['position'] . "')";
                }
                $dataWhere .= $conditionDivPo;
            }
            if (!empty($output['birthdate_start'])) {
                $conditionBirthDate = " AND (em.DateOfBirth >= '" . date("Y-m-d", strtotime($output['birthdate_start'])) . "')";
                if (!empty($output['birthdate_end'])) {
                    $conditionBirthDate = " AND (em.DateOfBirth >= '" . date("Y-m-d", strtotime($output['birthdate_start'])) . "' AND em.DateOfBirth <= '" . date("Y-m-d", strtotime($output['birthdate_end'])) . "' )";
                }
                $dataWhere .= $conditionBirthDate;
            }
            if (!empty($output['statusstd'])) {
                $dataArrStatus = $output['statusstd'];
                $conditionStatus = " AND (";
                $sn = 1;
                foreach ($output['statusstd'] as $s) {
                    if ($s == '-1') {
                        $conditionStatus .= "(em.StatusEmployeeID = '" . $s . "' OR em.StatusLecturerID = '" . $s . "' )" . (($sn < count($output['statusstd'])) ? ' OR ' : '');
                    } else if ($s == 3 || $s == 4 || $s == 5 || $s == 6) {
                        $conditionStatus .= "em.StatusLecturerID = '" . $s . "' " . (($sn < count($output['statusstd'])) ? ' OR ' : '');
                    } else {
                        $conditionStatus .= "em.StatusEmployeeID = '" . $s . "'" . (($sn < count($output['statusstd'])) ? ' OR ' : '');
                    }
                    $sn++;
                }
                $conditionStatus .= ")";
                $dataWhere .= $conditionStatus;
            }
            if (!empty($output['religion'])) {
                $dataArrReligion = $output['religion'];
                $conditionReligion = " AND (";
                $rn = 1;
                foreach ($output['religion'] as $r) {
                    $conditionReligion .= "em.ReligionID = '" . $r . "' " . (($rn < count($output['religion'])) ? ' OR ' : '');
                    $rn++;
                }
                $conditionReligion .= ")";
                $dataWhere .= $conditionReligion;
            }
            if (!empty($output['gender'])) {
                $dataArrGender = $output['gender'];
                $conditionGender = " AND (";
                $gn = 1;
                foreach ($output['gender'] as $g) {
                    $conditionGender .= "em.Gender = '" . $g . "' " . (($gn < count($output['gender'])) ? ' OR ' : '');
                    $gn++;
                }
                $conditionGender .= ")";
                $dataWhere .= $conditionGender;
            }
            if (!empty($output['level_education'])) {
                $dataArrLevel = $output['level_education'];
                $conditionLevelEdu = " AND (";
                $ln = 1;
                foreach ($output['level_education'] as $l) {
                    $conditionLevelEdu .= "em.LevelEducationID = '" . $l . "' " . (($ln < count($output['level_education'])) ? ' OR ' : '');
                    $ln++;
                }
                $conditionLevelEdu .= ")";
                $dataWhere .= $conditionLevelEdu;
            }

            if (!empty($output['isapprove'])) {
                if ($output['isapprove']) {
                    $dataWhere .= "AND em.isApproved = 1 ";
                }
            }

            /*SORTING*/
            if (!empty($output['sortby']) && !empty($output['orderby'])) {
                $orderBy = $output['sortby'] . " " . $output['orderby'];
            } else {
                $orderBy = "em.ID DESC";
            }
            /*END SORTING*/
        }
        /*END UPDATED BY FEBRI @  JAN 2020*/

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( em.Name LIKE "%' . $search . '%" OR em.NIP LIKE "%' . $search . '%" )';
        }

        $queryDefault = 'SELECT em.*, ems.Description AS StatusEmployees, rl.Religion as EmpReligion, le.Level as EmpEdu
                        FROM db_employees.employees em
                        LEFT JOIN db_employees.employees_status ems ON (em.StatusEmployeeID = ems.IDStatus)
                        LEFT JOIN db_employees.religion rl ON (em.ReligionID = rl.IDReligion)
                        LEFT JOIN db_employees.level_education le ON (em.LevelEducationID = le.ID)
                        LEFT JOIN db_employees.tmp_employees te on (te.NIP = em.NIP)
                        WHERE ( em.StatusEmployeeID != -2  ' . $dataWhere . ' ) ' . $dataSearch . ' ORDER BY ' . $orderBy;
        //echo $queryDefault;
        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();
        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $division = '-';
            $position = '-';
            if ($row['PositionMain'] != '' && $row['PositionMain'] != null) {
                $PositionMain = explode('.', $row['PositionMain']);

                $dataDivisi = $this->db->select('Division,Description')->get_where(
                    'db_employees.division',
                    array('ID' => $PositionMain[0]),
                    1
                )->result_array();
                $division = $dataDivisi[0]['Division'];

                $dataPosition = $this->db->select('Position,Description')->get_where(
                    'db_employees.position',
                    array('ID' => $PositionMain[1]),
                    1
                )->result_array();

                $position = $dataPosition[0]['Position'];
            }

            $gender = ($row['Gender'] == 'L') ? 'Male' : 'Female';

            $url_image = './uploads/employees/' . $row['Photo'];
            $srcImg =  base_url('images/icon/userfalse.png');
            if ($row['Photo'] != '' && $row['Photo'] != null) {
                $srcImg = (file_exists($url_image)) ? base_url('uploads/employees/' . $row['Photo'])
                    : base_url('images/icon/userfalse.png');
            }


            $EmailSelect = ($row['StatusEmployeeID'] == 4 || $row['StatusEmployeeID'] == '4') ? $row['Email'] : $row['EmailPU'];

            $Email = (($row['StatusEmployeeID'] == 4 || $row['StatusEmployeeID'] == '4') && count(explode(',', $EmailSelect)) > 1) ? '' : $EmailSelect;

            $disBtnEmail = ($Email == '' || $Email == null) ? 'disabled' : '';
            $dataToken = array(
                'Type' => 'emp',
                'Name' => $row['Name'],
                'NIP' => $row['NIP'],
                'Email' => $Email
            );

            $token = $this->jwt->encode($dataToken, 'UAP)(*');

            $DateOfBirth = ($row['DateOfBirth'] != '' && $row['DateOfBirth'] != null) ? $row['DateOfBirth'] : '';
            $disDateOfBirth = ($row['DateOfBirth'] != '' && $row['DateOfBirth'] != null) ? '' : 'disabled';


            $btnAct = '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="' . $disBtnEmail . '"><a href="javascript:void(0);" ' . $disBtnEmail . ' id="btnResetPass' . $row['NIP'] . '" class="btn-reset-password ' . $disBtnEmail . '" data-token="' . $token . '">Reset Password (Send Email)</a></li>
                            <li class="' . $disDateOfBirth . '"><a href="javascript:void(0);" ' . $disDateOfBirth . ' class="resetpassBirthDay ' . $disDateOfBirth . '" data-nip="' . $row['NIP'] . '" data-day="' . $DateOfBirth . '">Reset Password (DDMMYY)</a></li>
                            <li><a href="javascript:void(0);" class="btn-update-email" id="btnUpdateEmail' . $row['NIP'] . '" data-name="' . $row['Name'] . '" data-nip="' . $row['NIP'] . '" data-empid="' . $row['StatusEmployeeID'] . '" data-email="' . $Email . '">Update Email</a></li>
                            <li><a class = "PrintIDCard" href="javascript:void(0);" type = "employees" data-npm="' . $row['NIP'] . '" data-name="' . ucwords(strtolower($row['Name'])) . '" path = ' . $srcImg . ' email = "' . $row['EmailPU'] . '">Print ID Card</a></li>
                          </ul>
                        </div>';

            //$isRequested = $this->General_model->fetchData("db_employees.tmp_employees",array("NIP"=>$row["NIP"],"isApproval"=>1))->row();
            $needAppv = "";
            if ($row['isApproved'] == 1) {
                if ($this->session->userdata('IDdepartementNavigation') == 13) {
                    $needAppv = '<p><button class="btn btn-xs btn-info btn-appv" type="button" data-nip="' . $row["NIP"] . '" title="Need approving for biodata" ><i class="fa fa-warning"></i> Need Approval</button></p>';
                }
            }

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:left;"><a href="' . base_url('human-resources/employees/edit-employees/' . $row["NIP"]) . '" style="font-weight: bold;">' . $row['NIP'] . '</a>' . $needAppv . '</div>';
            $nestedData[] = '<a class="card-link" href="' . base_url('human-resources/employees/edit-employees/' . $row["NIP"]) . '"><div style="margin-bottom:10px"><div  style="float:left;margin-right:10px;"><img src="' . $srcImg . '" style="max-width: 35px;" class="img-rounded"></div>' .
                '<span class="pull-right">View</span>' .
                '<div  style="text-align:left;"><span class="regular name">' . $row['Name'] . '</span><br/><span id="viewEmail' . $row['NIP'] . '" style="color: #2196f3;">' . (!empty($Email) ? $Email : '-') . '</span></div>' .
                '</div><p><span class="regular ' . ((in_array($row['Gender'], $dataArrGender)) ? 'bg-primary' : '') . '"><i class="fa fa-' . (($row['Gender'] == 'L') ? 'mars' : 'venus') . '"></i> ' . $gender . '</span> &nbsp;
                                <span class="regular ' . ((in_array($row['ReligionID'], $dataArrReligion)) ? 'bg-primary' : '') . '"><i class="fa fa-star"></i> ' . $row['EmpReligion'] . '</span> &nbsp;
                                <span class="regular ' . ((in_array($row['LevelEducationID'], $dataArrLevel)) ? 'bg-primary' : '') . '"><i class="fa fa-graduation-cap"></i> ' . (!empty($row['EmpEdu']) ? $row['EmpEdu'] : '-') . '</span> </p></a>';
            $nestedData[] = '<div  style="text-align:left;">' . (!empty($row['PlaceOfBirth']) ? $row['PlaceOfBirth'] . ', ' : '') . date("d F Y", strtotime($row['DateOfBirth'])) . '</div>';
            $nestedData[] = '<div  style="text-align:left;"><b>' . ucwords(strtoupper($division)) . '</b><br/>- ' . ucwords(strtolower($position)) . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . $row['Address'] . '</div>';
            /*$nestedData[] = '<div  style="text-align:left;">'.$row['Address'].'</div>';*/
            $statusLect = $this->General_model->fetchData("db_employees.employees_status", "IDStatus='" . $row['StatusLecturerID'] . "'")->row();
            $nestedData[] = '<button type="button"  title="Status Employee" class="btn btn-xs btn-' . (in_array($row['StatusEmployeeID'], $dataArrStatus) ? 'info' : 'default') . '">' . $row['StatusEmployees'] . '</button><br><button type="button" title="Status Lecturer" class="btn btn-xs btn-' . (in_array($row['StatusLecturerID'], $dataArrStatus) ? 'info' : 'default') . '">' . (!empty($statusLect) ? $statusLect->Description : '-') . '</button></div>';

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
        /*END UPDATED BY FEBRI @ JAN 2020*/
    }

    public function getStudents()
    {
        $requestData = $_REQUEST;

        $dataYear = $this->input->get('dataYear');
        $dataProdiID = $this->input->get('dataProdiID');
        $dataStatus = $this->input->get('s');

        $db_ = 'ta_' . $dataYear;

        $dataWhere = 's.ProdiID = "' . $dataProdiID . '"';
        $arryWhere = array('ProdiID' => $dataProdiID);
        if ($dataStatus != '' && $dataStatus != null) {
            $arryWhere = array(
                'ProdiID' => $dataProdiID,
                'StatusStudentID' => $dataStatus
            );
            $dataWhere = 's.ProdiID = "' . $dataProdiID . '" AND s.StatusStudentID = "' . $dataStatus . '" ';
        }

        $totalData = $this->db->get_where(
            $db_ . '.students',
            $arryWhere
        )->result_array();

        $sql = 'SELECT s.NPM, s.Photo, s.Name, s.Gender, s.ClassOf, ps.NameEng AS ProdiNameEng, s.StatusStudentID,
                          ss.Description AS StatusStudent, ast.Password, ast.Password_Old, ast.Status AS StatusAuth,
                          ast.EmailPU
                          FROM ' . $db_ . '.students s
                          LEFT JOIN db_academic.program_study ps ON (ps.ID = s.ProdiID)
                          LEFT JOIN db_academic.status_student ss ON (ss.ID = s.StatusStudentID)
                          LEFT JOIN db_academic.auth_students ast ON (ast.NPM = s.NPM)';

        if (!empty($requestData['search']['value'])) {
            $sql .= ' WHERE ' . $dataWhere . ' AND ( s.NPM LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR s.Name LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR s.ClassOf LIKE "' . $requestData['search']['value'] . '%" )';
            $sql .= ' ORDER BY s.NPM, s.ProdiID ASC';
        } else {
            $sql .= 'WHERE ' . $dataWhere . ' ORDER BY s.NPM, s.ProdiID ASC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        }

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $Gender = ($row["Gender"] == 'P') ? 'Female' : 'Male';

            $label = '';
            if ($row['StatusStudentID'] == 7 || $row['StatusStudentID'] == 6 || $row['StatusStudentID'] == 4) {
                $label = 'style="color: red;"';
            } else if ($row['StatusStudentID'] == 2) {
                $label = 'style="color: #ff9800;"';
            } else if ($row['StatusStudentID'] == 3) {
                $label = 'style="color: green;"';
            } else if ($row['StatusStudentID'] == 1) {
                $label = 'style="color: #03a9f4;"';
            }

            //            $nestedData[] = '<div style="text-align: center;">'.$row["NPM"].'</div>';
            $nestedData[] = '<div style="text-align: center;"><img src="' . base_url('uploads/students/') . $db_ . '/' . $row["Photo"] . '" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="javascript:void(0);" data-npm="' . $row["NPM"] . '" data-ta="' . $row["ClassOf"] . '" class="btnDetailStudent"><b>' . $row["Name"] . '</b></a><br/>' . $row["NPM"];
            $nestedData[] = '<div style="text-align: center;">' . $Gender . '</div>';
            $nestedData[] = '<div class="dropdown">
                                  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="fa fa-pencil-square-o"></i>
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a href="javascript:void(0);" class="btn-edit-student " data-npm="' . $row["NPM"] . '" ta = "' . $db_ . '">Edit Student</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="javascript:void(0);" class="btn-reset-password " data-npm="' . $row["NPM"] . '" data-name="' . $row["Name"] . '" data-statusid="' . $row['StatusStudentID'] . '">Reset Password</a></li>
                                    <li><a href="javascript:void(0);" class="btn-change-status " data-emailpu="' . $row["EmailPU"] . '" data-year="' . $dataYear . '" data-npm="' . $row["NPM"] . '" data-name="' . $row["Name"] . '" data-statusid="' . $row['StatusStudentID'] . '">Change Status</a></li>

                                  </ul>
                                </div>';
            $nestedData[] = '<div style="text-align: center;"><button class="btn btn-sm btn-primary btnLoginPortalStudents " data-npm="' . $row["NPM"] . '"><i class="fa fa-sign-in right-margin"></i> Login Portal</button></div>';
            //            $nestedData[] = $row["ProdiNameEng"];
            $nestedData[] = '<div style="text-align: center;"><i class="fa fa-circle" ' . $label . '></i></div>';

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getStudentsServerSide()
    {

        $key = $this->input->post('key');

        $data = $this->db->query('SELECT NPM, Name FROM db_academic.auth_students ats
                                                    WHERE ats.NPM LIKE "%' . $key . '%"
                                                    OR ats.Name LIKE "%' . $key . '%"
                                                    ORDER BY Name ASC LIMIT 5 ')->result_array();

        return print_r(json_encode($data));
    }

    public function getStudentsAdmission()
    {
        $requestData = $_REQUEST;

        $dataYear = $this->input->get('dataYear');
        $dataProdiID = $this->input->get('dataProdiID');
        $dataStatus = $this->input->get('s');

        $db_ = 'ta_' . $dataYear;

        $dataWhere = '';
        $arryWhere = array('ProdiID' => $dataProdiID);

        if ($dataStatus != '' && $dataStatus != null) {
            $WhereOrAnd = (empty($dataWhere)) ? ' where ' : ' and ';
            $dataWhere = $WhereOrAnd . ' s.StatusStudentID = "' . $dataStatus . '" ';
        }

        if ($dataProdiID != '' && $dataProdiID != null && !empty($dataProdiID)) {
            $WhereOrAnd = (empty($dataWhere)) ? ' where ' : ' and ';
            $dataWhere = $WhereOrAnd . ' s.ProdiID = "' . $dataProdiID . '" ';
        }

        // print_r($dataWhere);die();

        // -------------total data---------- //
        $sqlSelectData  = 'SELECT asx.FormulirCode, s.NPM, s.Photo, s.Name, s.Gender, s.ClassOf, ps.NameEng AS ProdiNameEng, ps.Name AS ProdiNameInd,s.StatusStudentID,
                          ss.Description AS StatusStudent, ast.Password, ast.Password_Old, ast.Status AS StatusAuth,
                          ast.EmailPU,asx.GeneratedBy,emp.Name as NameGeneratedBy,asx.No_Ref';
        $sqlSelectTotal = 'select 1';
        $sql = '
                          FROM ' . $db_ . '.students s
                          LEFT JOIN db_academic.program_study ps ON (ps.ID = s.ProdiID)
                          LEFT JOIN db_academic.status_student ss ON (ss.ID = s.StatusStudentID)
                          LEFT JOIN db_academic.auth_students ast ON (ast.NPM = s.NPM)
                          LEFT JOIN (
                            select a.NPM,a.FormulirCode,a.GeneratedBy,a.DateTime as DateTimeGeneratedBy,dd.No_Ref
                            from db_admission.to_be_mhs as a
                            left join (
                                select FormulirCode,No_Ref from db_admission.formulir_number_offline_m
                                UNION
                                select FormulirCode,No_Ref from db_admission.formulir_number_online_m
                            ) dd on a.FormulirCode = dd.FormulirCode
                          ) as asx on ast.NPM = asx.NPM
                          LEFT JOIN db_employees.employees emp ON asx.GeneratedBy = emp.NIP
                          ';
        if (!empty($requestData['search']['value'])) {
            $WhereOrAnd = (empty($dataWhere)) ? ' where ' : ' and ';
            $sql .= '  ' . $dataWhere . ' ' . $WhereOrAnd . ' ( s.NPM LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR s.Name LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR s.ClassOf LIKE "' . $requestData['search']['value'] . '%"';
            $sql .= ' OR asx.FormulirCode LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR asx.No_Ref LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR emp.Name LIKE "' . $requestData['search']['value'] . '%" )';
            // $sql.= ' ORDER BY s.NPM, s.ProdiID ASC';
        } else {
            $sql .= ' ' . $dataWhere;
        }

        $OrderLimit = ' ORDER BY s.NPM, s.ProdiID ASC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';

        // print_r($sql);die();

        $query = $this->db->query($sqlSelectData . $sql . $OrderLimit)->result_array();

        $totalData = $this->db->query(
            'select count(*) as total from (
                ' . $sqlSelectTotal . $sql . '
            )xx
             '
        )->row()->total;

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $Gender = ($row["Gender"] == 'P') ? 'Female' : 'Male';

            $label = '';
            if ($row['StatusStudentID'] == 7 || $row['StatusStudentID'] == 6 || $row['StatusStudentID'] == 4) {
                $label = 'style="color: red;"';
            } else if ($row['StatusStudentID'] == 2) {
                $label = 'style="color: #ff9800;"';
            } else if ($row['StatusStudentID'] == 3) {
                $label = 'style="color: green;"';
            } else if ($row['StatusStudentID'] == 1) {
                $label = 'style="color: #03a9f4;"';
            }

            //            $nestedData[] = '<div style="text-align: center;">'.$row["NPM"].'</div>';
            // $nestedData[] = '<div style="text-align: center;"><img src="'.base_url('uploads/students/').$db_.'/'.$row["Photo"].'" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';

            // show formulir number
            $StrFM = ($row['FormulirCode'] != null && $row['FormulirCode'] != 'null' && $row['FormulirCode'] != "" && (!empty($row['FormulirCode']))) ? '<span style="color: #20525a;">' . $row['FormulirCode'] . ' / ' . $row['No_Ref'] . '</span>' : '';

            $nestedData[] = $StrFM;
            $nestedData[] = '<a href="javascript:void(0);" data-npm="' . $row["NPM"] . '" data-ta="' . $row["ClassOf"] . '" class="btnDetailStudent"><b>' . $row["Name"] . '</b></a><br/>' . $row["NPM"];
            $nestedData[] = '<div style="text-align: center;"><button class="btn btn-inverse btn-notification btn-show" NPM="' . $row["NPM"] . '" Name = "' . $row["Name"] . '">Show</button></div>';
            $nestedData[] = '<div style="text-align: center;">' . $Gender . '</div>';
            $nestedData[] = $row["ProdiNameInd"];
            $nestedData[] = '<div style="text-align: center;"><i class="fa fa-circle" ' . $label . '></i></div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["NameGeneratedBy"] . '</div>';

            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    // add bismar
    public function getfileEmployees()
    {

        $status = $this->input->get('s');
        $requestData = $_REQUEST;

        $whereStatus = ($status != '') ? ' AND StatusEmployeeID = "' . $status . '" ' : '';
        $totalData = $this->db->query('SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status, em.StatusEmployeeID, xx.Totalfiles
                        FROM db_employees.employees em
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        LEFT JOIN db_employees.employees_status ems ON (ems.IDStatus = em.StatusEmployeeID)
                        LEFT JOIN (SELECT fs.NIP, COUNT(fs.LinkFiles) AS Totalfiles FROM db_employees.files fs INNER JOIN db_employees.master_files mf ON (fs.TypeFiles = mf.ID)
            WHERE fs.Active = 1 AND fs.LinkFiles IS NOT NULL GROUP BY fs.NIP) xx ON (em.NIP = xx.NIP)
                        WHERE em.StatusEmployeeID != -2 ' . $whereStatus)->result_array();

        if (!empty($requestData['search']['value'])) {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status, em.StatusEmployeeID, xx.Totalfiles
                        FROM db_employees.employees em
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        LEFT JOIN db_employees.employees_status ems ON (ems.IDStatus = em.StatusEmployeeID)
                        LEFT JOIN (SELECT fs.NIP, COUNT(fs.LinkFiles) AS Totalfiles FROM db_employees.files fs INNER JOIN db_employees.master_files mf ON (fs.TypeFiles = mf.ID)
            WHERE fs.Active = 1 AND fs.LinkFiles IS NOT NULL GROUP BY fs.NIP) xx ON (em.NIP = xx.NIP)
                        WHERE em.StatusEmployeeID != -2 ' . $whereStatus . ' AND ( ';
            $sql .= ' em.NIP LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR em.Name LIKE "' . $requestData['search']['value'] . '%" ';
            //$sql.= ' OR ps.NameEng LIKE "'.$requestData['search']['value'].'%" ';
            $sql .= ') ORDER BY xx.Totalfiles DESC';
        } else {
            $sql = 'SELECT em.NIP, em.NIDN, em.Photo, em.Name, em.Gender, em.PositionMain, em.ProdiID,
                        ps.NameEng AS ProdiNameEng,em.EmailPU,em.Status, em.StatusEmployeeID, xx.Totalfiles
                        FROM db_employees.employees em
                        LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                        LEFT JOIN db_employees.employees_status ems ON (ems.IDStatus = em.StatusEmployeeID)
                        LEFT JOIN (SELECT fs.NIP, COUNT(fs.LinkFiles) AS Totalfiles FROM db_employees.files fs INNER JOIN db_employees.master_files mf ON (fs.TypeFiles = mf.ID)
            WHERE fs.Active = 1 AND fs.LinkFiles IS NOT NULL GROUP BY fs.NIP) xx ON (em.NIP = xx.NIP)
                        WHERE em.StatusEmployeeID != -2 ' . $whereStatus;
            $sql .= 'ORDER BY xx.Totalfiles DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        }

        $query = $this->db->query($sql)->result_array();
        $no = $requestData['start'] + 1;

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $jb = explode('.', $row["PositionMain"]);
            $Division = '';
            $Position = '';


            if (count($jb) > 1) {
                $dataDivision = $this->db->select('Division')->get_where('db_employees.division', array('ID' => $jb[0]), 1)->result_array()[0];
                $dataPosition = $this->db->select('Position')->get_where('db_employees.position', array('ID' => $jb[1]), 1)->result_array()[0];
                $Division = $dataDivision['Division'];
                $Position = $dataPosition['Position'];
            }

            $photo = (file_exists('./uploads/employees/' . $row["Photo"]) && $row["Photo"] != '' && $row["Photo"] != null)
                ? base_url('uploads/employees/' . $row["Photo"])
                : base_url('images/icon/userfalse.png');

            $NIP = $row['NIP'];
            $getotfiles = 0;

            $StatusFiles = array();
            $Get_MasterFiles = $this->m_master->showDataFiles_array('db_employees.master_files');
            $StatusFiles = '';
            for ($j = 0; $j < count($Get_MasterFiles); $j++) {
                //$stDefault =' <span class="label label-danger"> '.$Get_MasterFiles[$j]['TypeFiles'].'</span>';
                $stDefault = ' <span class="badge progress-bar-danger btn-round">' . $Get_MasterFiles[$j]['TypeFiles'] . '</span> ';

                $sql2 = 'select count(*) as total, LinkFiles from db_employees.files where NIP = ? and TypeFiles = ? and Active = 1 and LinkFiles IS NOT NULL ';
                $query2 = $this->db->query($sql2, array($NIP, $Get_MasterFiles[$j]['ID']))->result_array();
                if ($query2[0]['total'] > 0) {
                    $getotfiles = $getotfiles + ($query2[0]['total']);

                    if ($query2[0]['LinkFiles'] != null) {
                        $stDefault = ' <span class="label label-success btn-round"> ' . $Get_MasterFiles[$j]['TypeFiles'] . '</span>';
                    } else {
                        //$stDefault =' <span class="label label-danger btn-round"> '.$Get_MasterFiles[$j]['TypeFiles'].'</span>';
                        $stDefault = ' <span class="badge progress-bar-danger btn-round"> ' . $Get_MasterFiles[$j]['TypeFiles'] . '</span> ';
                    }
                }
                $StatusFiles .= $stDefault;
            }

            $status = '-';
            if ($row['StatusEmployeeID'] == 1) {
                $status = '<i class="fa fa-circle" style="color: #4CAF50;"></i>';
            } else if ($row['StatusEmployeeID'] == 2) {
                $status = '<i class="fa fa-circle" style="color: #FF9800;"></i>';
            } else if ($row['StatusEmployeeID'] == 3) {
                $status = '<i class="fa fa-circle" style="color: #03A9F4;"></i>';
            } else if ($row['StatusEmployeeID'] == 4) {
                $status = '<i class="fa fa-circle" style="color: #9e9e9e;"></i>';
            } else if ($row['StatusEmployeeID'] == -1) {
                $status = '<i class="fa fa-warning" style="color: #F44336;"></i>';
            }

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div style="text-align: center;"><img src="' . $photo . '" class="img-rounded" width="30" height="30"  style="max-width: 30px;object-fit: scale-down;"></div>';
            $nestedData[] = '<a href="' . base_url('human-resources/academic-details/' . $row["NIP"]) . '" style="font-weight: bold;">' . $row["Name"] . '</a>';
            //$nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = $Division . ' - ' . $Position;
            $nestedData[] = '<div style="text-align: center;">' . $status . '</div>';
            $nestedData[] = $StatusFiles;
            $nestedData[] = '<center><button type="button" style="text-align: center;" class="btn btn-primary btn-round">Files <span class="badge progress-bar-success btn-round"> ' . $getotfiles . ' </span></button> </center>';

            $nestedData[] = '<a class="btn btn-primary btn-round" href="' . base_url('human-resources/academic-details/' . $row["NIP"]) . '">
  <i class="icon-list icon-large"></i> Detail</a>';

            $no++;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }


    public function getAllMK()
    {
        $data = $this->m_api->__getAllMK();
        return print_r(json_encode($data));
    }

    public function setLecturersAvailability()
    {

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if ($data_arr['action'] == 'add') {
            $dataInsert = (array) $data_arr['dataForm'];
            $this->db->insert('db_academic.lecturers_availability', $dataInsert);
            return print_r($this->db->insert_id());
        } else if ($data_arr['action'] == 'edit') {

            $update_lad = (array) $data_arr['dataForm_lad'];
            $this->db->where('ID', $data_arr['ladID']);
            $this->db->update('db_academic.lecturers_availability_detail', $update_lad);

            return print_r(1);
        } else if ($data_arr['action'] == 'delete') {

            // Cek apakah ID lebih dari satu
            $dataCek = $this->m_api->__cekTotalLAD($data_arr['laID']);

            if (count($dataCek) == 1) {
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

    public function getdivisiversion()
    {
        $generate = $this->db->query('SELECT DISTINCT X.IDDivision, Z.Division
                FROM db_it.group_module AS X
                LEFT JOIN db_employees.division Z ON (X.IDDivision = Z.ID)')->result_array();
        echo json_encode($generate);
    }

    public function getdocumenttype()
    {
        $generate = $this->db->query('SELECT * FROM db_employees.master_files WHERE RequestDocument = 1')->result_array();
        echo json_encode($generate);
    }


    public function getstatusversion()
    {
        $generate = $this->db->query('SELECT ID, Division FROM db_employees.division ORDER BY division ASC ')->result_array();
        echo json_encode($generate);
    }

    public function getstatusversion2()
    {
        $generate = $this->db->query('SELECT DISTINCT a.IDDivision, b.Division
                FROM db_it.group_module AS a
                LEFT JOIN db_employees.division AS b ON (a.IDDivision = b.ID)
                WHERE b.Division IS NOT NULL
                ORDER BY a.IDDivision ASC ')->result_array();
        echo json_encode($generate);
    }

    public function getstatusmodule()
    {
        $generate = $this->db->query('SELECT IDModule, NameModule FROM db_it.module ORDER BY NameModule ASC ')->result_array();
        echo json_encode($generate);
    }

    public function getversionpic()
    {
        $generate = $this->db->query('SELECT DISTINCT a.PIC, b.Name
                    FROM db_it.version AS a
                    LEFT JOIN db_employees.employees AS b ON (a.PIC = b.NIP) ')->result_array();
        echo json_encode($generate);
    }


    public function delelelistacaemployee()
    {

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if ($data_arr['action'] == 'deleteacademic') {
            $ID1 = $data_arr['ID1'];
            $ID2 = $data_arr['ID2'];

            $sql = 'SELECT ID,LinkFiles FROM db_employees.files
                    WHERE ID IN ("' . $ID1 . '", "' . $ID2 . '") ';
            $data = $this->db->query($sql, array())->result_array();
            // print_r($sql);die();
            if (count($data) > 0) {

                for ($d = 0; $d < count($data); $d++) {
                    $LinkFiles = $data[$d]['LinkFiles'];
                    $pathPhoto = './uploads/files/' . $LinkFiles;
                    if (file_exists($pathPhoto)) {
                        unlink($pathPhoto);
                    }
                    $ID = $data[$d]['ID'];
                    $this->db->where('ID', $ID);
                    $this->db->delete('db_employees.files');
                }

                return print_r(1);
            }
        } else if ($data_arr['action'] == 'deleteother') {
            $ID1 = $data_arr['otfile1'];
            //$dataCek = $this->m_api->delistotherfiles($ID1);

            $sql = 'SELECT ID, LinkFiles FROM db_employees.files
                    WHERE ID = "' . $ID1 . '" ';
            $data = $this->db->query($sql, array())->result_array();

            if (count($data) > 0) {
                for ($d = 0; $d < count($data); $d++) {
                    $LinkFiles = $data[$d]['LinkFiles'];
                    $pathPhoto = './uploads/files/' . $LinkFiles;
                    if (file_exists($pathPhoto)) {
                        unlink($pathPhoto);
                    }
                    $ID = $data[$d]['ID'];
                    $this->db->where('ID', $ID);
                    $this->db->delete('db_employees.files');
                }
                return print_r(1);
            }
        }
    }

    public function delversiondata()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if ($data_arr['action'] == 'deleteversion') { //delete version data
            $versionid = $data_arr['versionid'];
            //$dataCek = $this->m_api->deletelistversion($versionid);
            $this->db->where('IDVersion', $versionid);
            $this->db->delete('db_it.version');

            $this->db->where('IDVersion', $versionid);
            $this->db->delete('db_it.version_detail');
            return print_r(1);
        } else if ($data_arr['action'] == 'deletegroupmod') {   //delete module
            $IDModule = $data_arr['versionid'];
            $value = "0";
            $check = $this->db->get_where('db_it.version_detail', array('IDModule' => $data_arr['versionid']))->result_array();

            if (count($check) > 0) {
                return print_r(0);
            } else {
                $this->db->where('IDModule', $IDModule);
                $this->db->delete('db_it.module');
                return print_r(1);
            }
        } else if ($data_arr['action'] == 'delegroups') {   //delete group

            $groupid = $data_arr['versionid'];
            $value = "0";
            $check = $this->db->get_where('db_it.module', array('IDGroup' => $data_arr['versionid']))->result_array();

            if (count($check) > 0) {
                return print_r(0);
            } else {
                $this->db->where('IDGroup', $data_arr['versionid']);
                $this->db->delete('db_it.group_module');
                return print_r(1);
            }
        }
    }


    public function setLecturersAvailabilityDetail($action)
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = $this->jwt->decode($token, $key);

        if ($action == 'insert') {
            $this->db->insert('db_academic.lecturers_availability_detail', $data_arr);
            return $this->db->insert_id();
        }
    }

    public function changeTahunAkademik()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);


        $data['department'] = $this->session->userdata('departementNavigation');
        $data['dosen'] = $this->m_tahun_akademik->__getKetersediaanDosenByTahunAkademik($data_arr['ID']);
        print_r(json_encode($data['dosen']));
        //        $this->load->view('page/'.$data['department'].'/ketersediaan_dosen_detail',$data);
    }


    //-------- Kurikulum -----
    public function insertKurikulum()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        // Cek Tahun
        $data = $this->m_api->cekTahunKurikulum($data_arr['Year']);
        if (count($data) > 0) {
            return print_r(0);
        } else {
            $this->db->insert('db_academic.curriculum', $data_arr);
            return print_r(1);
        }
    }

    public function geteducationLevel()
    {
        $data = $this->m_api->__geteducationLevel();
        return print_r(json_encode($data));
    }

    public function getDosenSelectOption()
    {
        $data = $this->m_api->__getDosenSelectOption();
        return print_r(json_encode($data));
    }



    public function crudKurikulum()
    {

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        //        print_r($data_arr);
        //        exit;
        if ($data_arr['action'] == 'add') {
            $insert = (array) $data_arr['data_insert'];
            $this->db->insert('db_academic.' . $data_arr['table'], $insert);
            $insert_id = $this->db->insert_id();
            return print_r($insert_id);
        } else if ($data_arr['action'] == 'edit') {
            $dataupdate = (array) $data_arr['data_insert'];
            $this->db->where('ID', $data_arr['ID']);
            $this->db->update('db_academic.' . $data_arr['table'], $dataupdate);
            return print_r(1);
        } else if ($data_arr['action'] == 'delete') {
            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_academic.' . $data_arr['table']);
            return print_r(1);
        } else if ($data_arr['action'] == 'read') {
            $data = $this->m_api->__getItemKuriklum($data_arr['table']);
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getDateKRSOnline') {

            $data = $this->checkDateKRSOnlineToEditTimetable($data_arr['SemesterID']);

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateStatusEditTimeTable') {

            //updateStatusEditTimeTable

            $this->db->set('EditTimeTable', $data_arr['EditTimeTable']);
            $this->db->where('SemesterID', $data_arr['SemesterID']);
            $this->db->update('db_academic.academic_years');
            return print_r(1);
        }
    }

    private function checkDateKRSOnlineToEditTimetable($SemesterID)
    {


        $data = $this->db->query('SELECT ay.krsStart, ay.krsEnd, ay.EditTimeTable, s.Status
                                            FROM db_academic.academic_years ay
                                            LEFT JOIN db_academic.semester s ON (s.ID = ay.SemesterID)
                                            WHERE s.ID = "' . $SemesterID . '" ')->result_array();

        //        if($data[0]['EditTimeTable']=='0'){
        //            $dateNow = date('Y-m-d');
        //            $data[0]['D'] = $dateNow;
        //            if($data[0]['krsStart']<=$dateNow && $data[0]['krsEnd']>=$dateNow){
        //                $data[0]['EditTimeTable'] = '0';
        //            } else {
        //                if($data[0]['Status']==1){
        //                    $data[0]['EditTimeTable'] = '1';
        //                } else {
        //                    $data[0]['EditTimeTable'] = '0';
        //                }
        //
        //            }
        //        }

        return $data;
    }

    public function crudDetailMK()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if ($data_arr['action'] == 'add') {
            $insert = (array) $data_arr['dataForm'];

            // Cek apakah sudah dimasukan ke detail kurikulum

            $where = array(
                'CurriculumID' => $insert['CurriculumID'],
                'ProdiID' => $insert['ProdiID'],
                //                'EducationLevelID' => $insert['EducationLevelID'],
                'MKID' => $insert['MKID']
            );
            $this->db->select('Semester');
            $dataSmt = $this->db->get_where('db_academic.curriculum_details', $where)->result_array();

            if (count($dataSmt) > 0) {
                $result = array(
                    'msg' => 0,
                    'Semester' => $dataSmt[0]['Semester']
                );
                return print_r(json_encode($result));
            } else {


                $this->db->insert('db_academic.curriculum_details', $insert);
                $insert_id = $this->db->insert_id();
                $result = array(
                    'msg' => $insert_id
                );
                return print_r(json_encode($result));
            }
        } else if ($data_arr['action'] == 'edit') {
            $update = (array) $data_arr['dataForm'];
            $this->db->where('ID', $data_arr['ID']);
            $this->db->update('db_academic.curriculum_details', $update);
            //            print_r($data_arr);

            //            $this->db->where('CurriculumDetailID', $data_arr['ID']);
            //            $this->db->delete('db_academic.precondition');

            $insert_id = $data_arr['ID'];
            return print_r($insert_id);
        } else if ($data_arr['action'] == 'delete') {
            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_academic.curriculum_details');
            return print_r(1);
        }
    }

    public function getdetailKurikulum()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            $CDID = $data_arr['CDID'];
            $data = $this->m_api->__getdetailKurikulum($CDID);

            return print_r(json_encode($data));
        }
    }

    public function genrateMKCode()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            $ID = $data_arr['ID'];
            $data = $this->m_api->__genrateMKCode($ID);

            return print_r(json_encode($data));
        }
    }

    public function cekMKCode()
    {
        $MKCode = $this->input->post('MKCode');
        $data = $this->m_api->__cekMKCode($MKCode);
        return print_r(json_encode($data));
    }

    public function crudMataKuliah()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'add') {
                $dataInsert = (array) $data_arr['dataForm'];
                $this->db->insert('db_academic.mata_kuliah', $dataInsert);
                $insert_id = $this->db->insert_id();

                return print_r($insert_id);
            } else if ($data_arr['action'] == 'edit') {
                $dataInsert = (array) $data_arr['dataForm'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.mata_kuliah', $dataInsert);

                return print_r(1);
            } else if ($data_arr['action'] == 'delete') {
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.mata_kuliah');
                return print_r(1);
            } else if ($data_arr['action'] == 'read') {
                $ID = $data_arr['ID'];
                $MKCode = $data_arr['MKCode'];
                $data = $this->m_api->getMataKuliahSingle($ID, $MKCode);

                if (count($data) > 0) {
                    return print_r(json_encode($data[0]));
                }
            } else if ($data_arr['action'] == 'readOfferings') {
                $dataForm = (array) $data_arr['dataForm'];
                $data = $this->m_api->getMatakuliahOfferings($dataForm['SemesterID'], $dataForm['MKID'], $dataForm['MKCode']);

                return print_r(json_encode($data[0]));
            }
        }
    }

    public function crudTahunAkademik()
    {
        //        $token = $this->input->post('token');
        //        $key = "UAP)(*";
        //        $data_arr = (array) $this->jwt->decode($token,$key);

        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {

            if ($data_arr['action'] == 'add') {
                $dataForm = (array) $data_arr['dataForm'];
                // Cek
                $check = $this->db->get_where('db_academic.semester', array('Year' => $dataForm['Year'], 'Code' => $dataForm['Code']))
                    ->result_array();

                //                print_r($check);
                //                exit;
                if (count($check) > 0) {
                    return print_r(0);
                } else {
                    $this->db->insert('db_academic.semester', $dataForm);
                    $insert_id = $this->db->insert_id();

                    $this->db->insert(
                        'db_academic.academic_years',
                        array('SemesterID' => $insert_id)
                    );

                    return print_r($insert_id);
                }
            } else if ($data_arr['action'] == 'edit') {
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.semester', $dataForm);
                return print_r(1);
            } else if ($data_arr['action'] == 'delete') {
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.semester');
                return print_r(1);
            } else if ($data_arr['action'] == 'read') {

                $data = $this->db->order_by('ID', 'DESC')
                    ->get('db_academic.semester')
                    ->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'addSemesterAntara') {
                $dataForm = (array) $data_arr['dataForm'];
                // Cek
                $check = $this->db->get_where('db_academic.semester_antara', array('Year' => $dataForm['Year'], 'Code' => $dataForm['Code']))
                    ->result_array();

                if (count($check) > 0) {
                    return print_r(0);
                } else {
                    $this->db->insert('db_academic.semester_antara', $dataForm);
                    $insert_id = $this->db->insert_id();

                    $this->db->insert(
                        'db_academic.sa_academic_years',
                        array('SASemesterID' => $insert_id)
                    );

                    return print_r($insert_id);
                }
            } else if ($data_arr['action'] == 'readSemesterAntara') {

                $data = $this->db->query('SELECT * FROM db_academic.semester_antara ORDER BY SemesterID DESC')->result_array();

                if (count($data) > 0) {
                    for ($i = 0; $i < count($data); $i++) {

                        // Get Student
                        $dataStd = $this->db->query('SELECT count(*) AS Total FROM db_academic.sa_student
                                                        WHERE SASemesterID = "' . $data[$i]['ID'] . '" ')->result_array();

                        $data[$i]['TotalStudent'] = $dataStd[0]['Total'];
                    }
                }

                //                $data = $this->db
                //                    ->select('semester_antara.*')
                //                    ->join('db_academic.semester','semester.ID = semester_antara.SemesterID')
                //                    ->order_by('semester_antara.Year', 'DESC')
                //                    ->get('db_academic.semester_antara')
                //                    ->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'publishSemesterAntara') {

                $ID = $data_arr['ID'];
                $this->db->query('UPDATE db_academic.semester_antara s SET s.Status=IF(s.ID="' . $ID . '","1","0")');
                return print_r($ID);
            } else if ($data_arr['action'] == 'UnpublishSemesterAntara') {

                $ID = $data_arr['ID'];
                $this->db->query('UPDATE db_academic.semester_antara s SET s.Status= "0" WHERE s.ID="' . $ID . '"');
                return print_r($ID);
            } else if ($data_arr['action'] == 'checkSemesterAntara') {
                $data = $this->db
                    ->get_where('db_academic.semester_antara', array('Status' => '1'))
                    ->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'DataSemester') {

                $data = $this->m_api->getSemesterCurriculum($data_arr['SemesterID'], $data_arr['IsSemesterAntara']);

                return print_r(json_encode($data));
            }
        }
    }

    public function crudStatusStudents()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {

            if ($data_arr['action'] == 'read') {
                $data = $this->db->order_by('ID', 'ASC')->get('db_academic.status_student')->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'resetPassword') {

                $dataUpdate = array(
                    'Password_Old' => md5($data_arr['NewPassword']),
                    'Status' => '-1'
                );
                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update('db_academic.auth_students', $dataUpdate);
                return print_r(1);
            } else if ($data_arr['action'] == 'changeStatus') {

                // Cek apakah data NPM ada di auth_students
                $dataAuth = $this->db->get_where('db_academic.auth_students', array(
                    'NPM' => $data_arr['NPM']
                ), 1)->result_array();


                $statusLogin = ($data_arr['StatusID'] == '3') ? '1' : '0';
                if (count($dataAuth) > 0) {
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
                    $this->db->insert('db_academic.auth_students', $dataInsert);
                }

                // Update di table students
                $da_ = "ta_" . $data_arr['dataYear'];
                $arrUpdateStd = array(
                    'StatusStudentID' => $data_arr['StatusID']
                );
                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update($da_ . '.students', $arrUpdateStd);

                // adding by adhi 20201109 : add EffectiveDateStatus
                $arrUpdateStd['EffectiveDateStatus'] = $data_arr['EffectiveDateStatus'];

                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update('db_academic.auth_students', $arrUpdateStd);

                return print_r(1);
            }
        }
    }

    public function crudSpecialCaseKRS()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'insertSP_KRS') {
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->insert('db_academic.academic_years_sp_krs_1', $dataForm);
                return print_r(1);
            } else if ($data_arr['action'] == 'readSP_KRS') {

                $data = $this->db->query('SELECT aysk.*, ps.NameEng AS ProdiEng, pg.Code AS ProdiGroup FROM db_academic.academic_years_sp_krs_1 aysk
                                                          LEFT JOIN db_academic.program_study ps ON (ps.ID = aysk.ProdiID)
                                                          LEFT JOIN db_academic.prodi_group pg ON (pg.ID = aysk.ProdiGroupID)
                                                          WHERE aysk.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                          ORDER BY aysk.ID ASC ')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'deleteSP_KRS') {
                $ID = $data_arr['ID'];
                $this->db->delete(
                    'db_academic.academic_years_sp_krs_1',
                    array('ID' => $ID)
                );
                return print_r(1);
            }
        }
    }

    public function crudProdiGroup()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'readProdiGroup') {
                $data = $this->db->order_by('Code', 'ASC')->get_where('db_academic.prodi_group', array('ProdiID' => $data_arr['ProdiID']))
                    ->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readMentorProdi') {

                $ProdiID = $data_arr['ProdiID'];
                $data = $this->db->select('NIP,Name')->order_by('NIP', 'ASC')->get_where('db_employees.employees', array(
                    'ProdiID' => $ProdiID,
                    'StatusLecturerID' => '3'
                ))->result_array();

                return print_r(json_encode($data));
            }
        }
    }

    public function crudDataDetailTahunAkademik()
    {

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        //        print_r($data_arr);
        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'readDetailAcademicYear') {

                $data = $this->m_api->__crudDataDetailTahunAkademik($data_arr['ID']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'editDetailAcademicYear') {
                $SemesterID = $data_arr['SemesterID'];

                $dataForm = (array) $data_arr['dataForm'];
                $this->db->where('SemesterID', $SemesterID);
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->update('db_academic.academic_years', $dataForm);

                $this->db->reset_query();

                // Cek apakah sudah ada atau belum jika belum maka insert
                $cekSet = $this->db->get_where(
                    'db_academic.attendance_setting',
                    array('SemesterID' => $SemesterID)
                )->result_array();
                $dataFormAttd = (array) $data_arr['dataFormAttd'];
                if (count($cekSet) > 0) {
                    $this->db->where('SemesterID', $SemesterID);
                    $this->db->update('db_academic.attendance_setting', $dataFormAttd);
                } else {
                    $dataFormAttd['SemesterID'] = $SemesterID;
                    $this->db->insert('db_academic.attendance_setting', $dataFormAttd);
                }

                $this->db->reset_query();

                // Class Online
                $dataOnlineClass = (array) $data_arr['dataOnlineClass'];
                $cekOnlineClass = $this->db->select('ID')
                    ->get_where(
                        'db_academic.setting_online_class',
                        array('SemesterID' => $SemesterID)
                    )
                    ->result_array();

                if (count($cekOnlineClass) > 0) {
                    $this->db->where('SemesterID', $SemesterID);
                    $this->db->update('db_academic.setting_online_class', $dataOnlineClass);
                } else {
                    $dataOnlineClass['SemesterID'] = $SemesterID;
                    $this->db->insert('db_academic.setting_online_class', $dataOnlineClass);
                }



                return print_r($SemesterID);
            } else if ($data_arr['action'] == 'publish') {
                $ID = $data_arr['ID'];
                $this->db->query('UPDATE db_academic.semester s SET s.Status=IF(s.ID="' . $ID . '",1,0)');
                return print_r($ID);
            } else if ($data_arr['action'] == 'schedule') {
                $SemesterID = $data_arr['SemesterID'];
                $NIP = $data_arr['NIP'];
                $data = $this->m_api->__getScheduleTeacher($SemesterID, $NIP);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'insertSC') {
                $dataForm = (array) $data_arr['dataForm'];

                $result = [];
                for ($i = 0; $i < count($dataForm); $i++) {
                    $dataM = (array) $dataForm[$i];
                    $dataInsert = (array) $dataM['Details'];

                    $dataWhere = array(
                        'SemesterID' => $dataInsert['SemesterID'],
                        'AcademicDescID' => $dataInsert['AcademicDescID'],
                        'UserID' => $dataInsert['UserID'],
                        'DataID' => $dataInsert['DataID']
                    );

                    $dataCek = $this->db->get_where('db_academic.academic_years_special_case', $dataWhere, 1)->result_array();

                    if (count($dataCek) > 0) {
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
                                            WHERE aysc.ID = "' . $insert_id . '"
                                            GROUP BY sdc.ScheduleID')->result_array();

                        $msg = array(
                            'Details' => $dataDetails[0],
                            'Course' => $dataM['Course'],
                            'Msg' => 'Saved',
                            'Status' => 1
                        );
                    }

                    array_push($result, $msg);
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'dataSC') {
                $SemesterID = $data_arr['SemesterID'];
                $AcademicDescID = $data_arr['AcademicDescID'];
                $data = $this->m_api->__getSpecialCase($SemesterID, $AcademicDescID);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'deleteSC') {
                $id = $data_arr['ID'];
                $this->db->where('ID', $id);
                $this->db->delete('db_academic.academic_years_special_case');

                return print_r(1);
            } else if ($data_arr['action'] == 'insertSCKRS') {
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->insert('db_academic.academic_years_special_case', $dataForm);
                return print_r(1);
            } else if ($data_arr['action'] == 'readSCKRS') {
                $SemesterID = $data_arr['SemesterID'];

                $data = $this->db->query('SELECT aysc.ID,ps.ID AS ProdiID,ps.Code, aysc.Start, aysc.End
                                              FROM db_academic.academic_years_special_case aysc
                                              LEFT JOIN db_academic.program_study ps ON (ps.ID = aysc.UserID)
                                              WHERE aysc.SemesterID = "' . $SemesterID . '"
                                              AND aysc.Status = "2" ')->result_array();


                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'deleteSCKRS') {
                $ID = $data_arr['ID'];
                $this->db->delete(
                    'db_academic.academic_years_special_case',
                    array('ID' => $ID)
                );
                return print_r(1);
            }
        }
    }

    public function getAcademicYearOnPublish()
    {

        $smt = $this->input->get('smt');

        if ($smt == 'SemesterAntara') {
            $data = $this->db
                ->get_where('db_academic.semester_antara', array('Status' => '1'))
                ->result_array();
        } else {
            $data = $this->m_api->__getAcademicYearOnPublish();
        }

        //        $dataSMT = $this->m_api->getSemesterCurriculum();

        //        $data[0]['Semester'] = $dataSMT[0]['Semester'];


        return print_r(json_encode($data[0]));
    }

    public function crudSchedule()
    {

        $data_arr = $this->getInputToken();

        //        print_r($data_arr);
        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];

                // Scedule
                $insertSchedule = (array) $formData['schedule'];
                $this->db->insert('db_academic.schedule', $insertSchedule);
                $insert_id = $this->db->insert_id();

                // schedule_details
                $CreditDefaultMK = 0;
                $dataScheduleDetails = (array) $formData['schedule_details'];
                for ($s = 0; $s < count($dataScheduleDetails); $s++) {
                    $arr = (array) $dataScheduleDetails[$s];
                    $arr['ScheduleID'] = $insert_id;
                    $CreditDefaultMK = $arr['Credit'];
                    $this->db->insert('db_academic.schedule_details', $arr);
                    $insert_id_SD = $this->db->insert_id();

                    // Insert Attd
                    $dataInsetAttd = array(
                        'SemesterID' => $insertSchedule['SemesterID'],
                        'ScheduleID' => $insert_id,
                        'SDID' => $insert_id_SD
                    );

                    $this->db->insert('db_academic.attendance', $dataInsetAttd);
                }


                // schedule_details_course
                $dataScheduleDetailsCourse = (array) $formData['schedule_details_course'];
                for ($sdc = 0; $sdc < count($dataScheduleDetailsCourse); $sdc++) {
                    $arr = (array) $dataScheduleDetailsCourse[$sdc];
                    $arr['ScheduleID'] = $insert_id;
                    $this->db->insert('db_academic.schedule_details_course', $arr);
                }


                //schedule_team_teaching
                $dataTemaTeaching = (array) $formData['schedule_team_teaching'];

                // ==== Insert to pembagian BKD ====
                $TotalDosen = 1 + count($dataTemaTeaching);
                $ShareCredit = 100 / $TotalDosen;
                $CreditOri = $CreditDefaultMK / $TotalDosen;
                $CreditResult = round($CreditOri, 2);

                $insertShareCoord = array(
                    'ScheduleID' => $insert_id,
                    'NIP' => $insertSchedule['Coordinator'],
                    'ShareCredit' => $ShareCredit,
                    'CreditOri' => $CreditOri,
                    'Credit' => $CreditResult
                );
                $this->db->insert('db_academic.schedule_share_credit', $insertShareCoord);


                if ($insertSchedule['TeamTeaching'] == 1) {
                    for ($t = 0; $t < count($dataTemaTeaching); $t++) {
                        $arr = (array) $dataTemaTeaching[$t];
                        $arr['ScheduleID'] = $insert_id;
                        $this->db->insert('db_academic.schedule_team_teaching', $arr);

                        // ==== Insert to pembagian BKD ====
                        $insertShareCoord = array(
                            'ScheduleID' => $insert_id,
                            'NIP' => $arr['NIP'],
                            'ShareCredit' => $ShareCredit,
                            'CreditOri' => $CreditOri,
                            'Credit' => $CreditResult
                        );
                        $this->db->insert('db_academic.schedule_share_credit', $insertShareCoord);
                    }
                }







                return print_r($insert_id);
            } else if ($data_arr['action'] == 'addAttendanceFromTimetables') {
                $dataForm = $data_arr['dataForm'];

                if (count($dataForm) > 0) {
                    for ($i = 0; $i < count($dataForm); $i++) {
                        $d = (array) $dataForm[$i];
                        $check = $this->db->select('ID')
                            ->get_where(
                                'db_academic.attendance_students',
                                array(
                                    'ID_Attd' => $d['ID_Attd'],
                                    'NPM' => $d['NPM']
                                )
                            )->result_array();
                        if (count($check) <= 0) {
                            $this->db->insert('db_academic.attendance_students', $d);
                        }
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'removeAttendanceFromTimetables') {
                $dataForm = $data_arr['dataForm'];

                if (count($dataForm) > 0) {
                    for ($i = 0; $i < count($dataForm); $i++) {
                        $d = (array) $dataForm[$i];

                        $this->db->delete('db_academic.attendance_students', array(
                            'ID_Attd' => $d['ID_Attd'],
                            'NPM' => $d['NPM']
                        ));
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'read') {
                $dataWhere = (array) $data_arr['dataWhere'];

                $days = $this->db->get_where('db_academic.days', array('ID' => $data_arr['DayID']), 1)->result_array();


                $data[0]['Day'] = $days[0];
                $data[0]['Details'] = $this->m_api->getSchedule($data_arr['DayID'], $dataWhere);

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readOneSchedule') {

                $data = $this->m_api->getOneSchedule($data_arr['ScheduleID']);

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readProdiGroup') {
                $dataGroupProdi = $this->db->order_by('Code', 'ASC')
                    ->get_where(
                        'db_academic.prodi_group',
                        array('ProdiID' => $data_arr['ProdiID'])
                    )->result_array();

                return print_r(json_encode($dataGroupProdi));
            } else if ($data_arr['action'] == 'delete') {
                $ID = $data_arr['ScheduleID'];

                // Get Attendance
                $dataAttd = $this->db->get_where(
                    'db_academic.attendance',
                    array('ScheduleID' => $ID)
                )->result_array();

                // Delete Attendance Students
                $this->db->delete('db_academic.attendance_students', array('ID_Attd' => $dataAttd[0]['ID']));

                // Delete Attendance
                $this->db->delete('db_academic.attendance', array('ScheduleID' => $ID));

                $tables = array(
                    'db_academic.schedule_details',
                    'db_academic.schedule_details_course', 'db_academic.schedule_team_teaching'
                );
                $this->db->where('ScheduleID', $ID);
                $this->db->delete($tables);

                $this->db->reset_query();
                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.schedule');


                return print_r(1);
            } else if ($data_arr['action'] == 'deleteSubSesi') {
                $ID = $data_arr['sdID'];

                $dataSD = $this->db->get_where(
                    'db_academic.schedule_details',
                    array('ID' => $ID),
                    1
                )->result_array();

                $whereAttd = array(
                    'ScheduleID' => $dataSD[0]['ScheduleID'],
                    'SDID' => $ID
                );

                $dataAttd = $this->db->get_where('db_academic.attendance', $whereAttd, 1)->result_array();

                $this->db->delete('db_academic.attendance_students', array('ID_Attd' => $dataAttd[0]['ID']));
                $this->db->delete('db_academic.attendance', $whereAttd);

                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.schedule_details');

                // Update Subsesi jika tinggal 1
                $dataSubSesi = $this->db->get_where(
                    'db_academic.schedule_details',
                    array('ScheduleID' => $dataSD[0]['ScheduleID'])
                )->result_array();
                $SubSesi = (count($dataSubSesi) > 1) ? '1' : '0';
                $this->db->set('SubSesi', $SubSesi);
                $this->db->where('ID', $dataSD[0]['ScheduleID']);
                $this->db->update('db_academic.schedule');

                return print_r(1);
            } else if ($data_arr['action'] == 'edit') {

                $formData = (array) $data_arr['formData'];
                $schedule_details = (array) $formData['schedule_details'];

                // Update Schedule
                $ScheduleID = $data_arr['ID'];
                $ScheduleUpdate = (array) $formData['schedule'];
                $this->db->where('ID', $ScheduleID);
                $this->db->update('db_academic.schedule', $ScheduleUpdate);
                $this->db->reset_query();

                // Update Schedule Detail
                $dataScheduleDetailsArray = (array) $schedule_details['dataScheduleDetailsArray'];
                for ($d = 0; $d < count($dataScheduleDetailsArray); $d++) {
                    $ds = (array) $dataScheduleDetailsArray[$d];
                    $this->db->where('ID', $ds['sdID']);
                    $this->db->update('db_academic.schedule_details', (array) $ds['update']);
                    $this->db->reset_query();
                }

                // Insert Schedule Detail
                $dataScheduleDetailsArrayNew = (array) $schedule_details['dataScheduleDetailsArrayNew'];
                for ($d2 = 0; $d2 < count($dataScheduleDetailsArrayNew); $d2++) {

                    $dataNewSesi = (array) $dataScheduleDetailsArrayNew[$d2];

                    $this->db->insert('db_academic.schedule_details', $dataNewSesi);
                    $insert_id_SD = $this->db->insert_id();

                    // Get Schedule
                    $dataSch = $this->db->get_where(
                        'db_academic.schedule',
                        array('ID' => $dataNewSesi['ScheduleID']),
                        1
                    )->result_array();

                    // Insert Attd
                    $dataInsetAttd = array(
                        'SemesterID' => $dataSch[0]['SemesterID'],
                        'ScheduleID' => $dataNewSesi['ScheduleID'],
                        'SDID' => $insert_id_SD
                    );
                    $this->db->insert('db_academic.attendance', $dataInsetAttd);
                    $insert_id_attd = $this->db->insert_id();


                    // Cek Mahasiswa Yang Ngambil
                    $dataMhs = $this->m_api->getDataStudents_Schedule($dataSch[0]['SemesterID'], $dataNewSesi['ScheduleID']);

                    for ($m = 0; $m < count($dataMhs); $m++) {
                        $data_attd_s = array(
                            'ID_Attd' => $insert_id_attd,
                            'NPM' => $dataMhs[$m]['NPM']
                        );
                        $this->db->insert('db_academic.attendance_students', $data_attd_s);
                    }

                    $this->db->reset_query();
                }

                $this->db->where('ScheduleID', $ScheduleID);
                $this->db->delete('db_academic.schedule_team_teaching');
                $this->db->reset_query();
                // Team Teaching
                if ($ScheduleUpdate['TeamTeaching'] == 1) {
                    $dataTemaTeaching = (array) $formData['schedule_team_teaching'];
                    for ($t = 0; $t < count($dataTemaTeaching['teamTeachingArray']); $t++) {

                        $arr = (array) $dataTemaTeaching['teamTeachingArray'][$t];
                        $this->db->insert('db_academic.schedule_team_teaching', $arr);
                        $this->db->reset_query();
                    }
                }


                // Update Subsesi jika tinggal 1
                $dataSubSesi = $this->db->get_where(
                    'db_academic.schedule_details',
                    array('ScheduleID' => $ScheduleID)
                )->result_array();
                $SubSesi = (count($dataSubSesi) > 1) ? '1' : '0';
                $this->db->set('SubSesi', $SubSesi);
                $this->db->where('ID', $ScheduleID);
                $this->db->update('db_academic.schedule');

                return print_r(1);
            } else if ($data_arr['action'] == 'readDetail') {

                $data = $this->m_api->getScheduleDetails($data_arr['ScheduleID']);

                return print_r(json_encode($data));
            }

            // Cek Group
            else if ($data_arr['action'] == 'checkGroup') {
                //                $dataG = $this->db->get_where('',
                //                        array('ClassGroup' => ))->result_array();

                $dataG = $this->db->query('SELECT s.ID FROM db_academic.schedule s
                                                WHERE s.ClassGroup LIKE "' . $data_arr['Group'] . '"
                                                AND s.SemesterID = "' . $data_arr['SemesterID'] . '" ')
                    ->result_array();
                return print_r(json_encode($dataG));
            } else if ($data_arr['action'] == 'getDataStudents') {

                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                // sp => Study_planning
                if ($data_arr['Flag'] == 'sp') {
                    $data = $this->m_api->getStudentByScheduleID($SemesterID, $ScheduleID, $data_arr['CDID']);
                } else if ($data_arr['Flag'] == 'std') {
                    $data = $this->m_api->getTotalStdNotYetApprovePerDay($SemesterID, $ScheduleID, $data_arr['CDID']);
                }

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'updateStudyPlanningResignStatus') {

                $ShowTranscript = ($data_arr['StatusResign'] == 0 || $data_arr['StatusResign'] == '0') ? '1' : '0';

                $arrUpdate = array(
                    'StatusResign' => $data_arr['StatusResign'],
                    'ShowTranscript' => $ShowTranscript
                );

                $this->db->where('ID', $data_arr['SPID']);
                $this->db->update($data_arr['DB_Student'] . '.study_planning', $arrUpdate);

                return print_r(1);
            } else if ($data_arr['action'] == 'getClassGroup') {
                $data = $this->db->query('SELECT s.ID AS ScheduleID,s.ClassGroup, em.Name, mk.NameEng AS CourseEng FROM db_academic.schedule s
                                                  LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                  LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                  LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                  WHERE s.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                  GROUP BY s.ID
                                                   ORDER BY s.ClassGroup ASC ')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'checkStudentToDelete') {
                $ScheduleID = $data_arr['ScheduleID'];
                $SemesterID = $data_arr['SemesterID'];
                $dataApprove = $this->m_api->__getStudentByScheduleIDApproved($SemesterID, $ScheduleID);
                $dataPlan = $this->m_api->__getStudentByScheduleIDInStudyPlanning($SemesterID, $ScheduleID);

                $dataKRS = $this->checkDateKRSOnlineToEditTimetable($SemesterID);

                $result = array(
                    'Approve' => $dataApprove,
                    'Plan' => $dataPlan,
                    'KRS' => $dataKRS
                );



                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'deleteTimettables') {
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $arrWhere = array('SemesterID' => $SemesterID, 'ScheduleID' => $ScheduleID);
                $this->db->where($arrWhere);
                $this->db->delete('db_academic.std_krs');
                $this->db->reset_query();

                // == Approved ==
                $dataCL = $this->m_api->getClassOf();

                for ($c = 0; $c < count($dataCL); $c++) {
                    $d = $dataCL[$c];
                    $db_ = 'ta_' . $d['Year'];

                    // Cek DB Exist
                    $dbExist = $this->db->query('SELECT SCHEMA_NAME
                                                    FROM INFORMATION_SCHEMA.SCHEMATA
                                                    WHERE SCHEMA_NAME = "' . $db_ . '" ')->result_array();

                    if (count($dbExist) > 0) {
                        $this->db->where($arrWhere);
                        $this->db->delete($db_ . '.study_planning');
                        $this->db->reset_query();
                    }
                }

                // Delete Schedulenya
                $this->db->where(array('ID' => $ScheduleID, 'SemesterID' => $SemesterID));
                $this->db->delete('db_academic.schedule');
                $this->db->reset_query();

                $tables = array(
                    'db_academic.schedule_details', 'db_academic.schedule_details_course',
                    'db_academic.schedule_material', 'db_academic.schedule_team_teaching'
                );
                $this->db->where('ScheduleID', $ScheduleID);
                $this->db->delete($tables);
                $this->db->reset_query();

                // Delete Attendance
                $dataAttd = $this->db->query('SELECT attd.ID FROM db_academic.attendance attd
                                                            WHERE attd.SemesterID = "' . $SemesterID . '"
                                                            AND attd.ScheduleID = "' . $ScheduleID . '" ')->result_array();

                if (count($dataAttd) > 0) {
                    for ($i = 0; $i < count($dataAttd); $i++) {
                        // Delete Attendance Students
                        $this->db->where('ID_Attd', $dataAttd[$i]['ID']);
                        $this->db->delete('db_academic.attendance_students');
                        $this->db->reset_query();
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'loadEditCourse') {
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
                                                    WHERE sdc.ScheduleID = "' . $ScheduleID . '" AND s.SemesterID = "' . $SemesterID . '"
                                                    ORDER BY sdc.ProdiID, cd.Semester ASC ')->result_array();

                if (count($data) > 0) {
                    for ($i = 0; $i < count($data); $i++) {
                        $dataApprove = $this->m_api->__getStudentByScheduleIDApproved_details($SemesterID, $ScheduleID, $data[$i]['CDID']);
                        $dataPlan = $this->m_api->__getStudentByScheduleIDInStudyPlanning_details($SemesterID, $ScheduleID, $data[$i]['CDID']);

                        $data[$i]['TotalStd_Approve'] = $dataApprove;
                        $data[$i]['TotalStd_Plan'] = $dataPlan;
                    }
                }

                $result = array(
                    'ScheduleDetails' => $data
                );
                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'loadEditCourseSchedule') {
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $dataProgram = $this->db->query('SELECT s.ID AS ScheduleID, s.ProgramsCampusID, sem.Name AS SemesterName,
                                                              s.ClassGroup, s.Coordinator, em.Name AS CoordinatorName, 
                                                              ssc.ShareCredit AS CoordinatorCreditPoint, s.TeamTeaching,
                                                             s.SemesterID, s.Attendance, s.OnlineLearning, mk.NameEng AS CourseEng, cd.TotalSKS AS TotalCredit
                                                            FROM db_academic.schedule s
                                                            LEFT JOIN db_academic.semester sem ON (sem.ID = s.SemesterID)
                                                            LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                            LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                            LEFT JOIN db_academic.schedule_share_credit ssc ON (ssc.ScheduleID = sdc.ScheduleID AND ssc.NIP = s.Coordinator)
                                                            WHERE s.ID = "' . $ScheduleID . '" AND SemesterID = "' . $SemesterID . '"
                                                             GROUP BY s.ID ')
                    ->result_array();


                if (count($dataProgram) > 0) {
                    $detailTeamTeaching = [];
                    $dataTTC = $this->db->query('SELECT em.NIP, em.Name, ssc.ShareCredit FROM db_academic.schedule_team_teaching stt  
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                    LEFT JOIN db_academic.schedule_share_credit ssc ON (stt.ScheduleID = ssc.ScheduleID AND ssc.NIP = stt.NIP)
                                                    WHERE stt.ScheduleID = "' . $dataProgram[0]['ScheduleID'] . '" ')
                        ->result_array();
                    if (count($dataTTC) > 0) {
                        foreach ($dataTTC as $item) {
                            array_push($detailTeamTeaching, $item['NIP']);
                        }
                    }
                    $dataProgram[0]['TeamTeachingNIP'] = $detailTeamTeaching;

                    $dataProgram[0]['TeamTeachingDetail'] = $dataTTC;
                }

                $result = array(
                    'Schedule' => $dataProgram
                );
                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'checktoAddNewCourse') {
                $dataWhere = (array) $data_arr['dataWhere'];
                $data = $this->db->get_where('db_academic.schedule_details_course', $dataWhere)->result_array();

                if (count($data) > 0) {
                    $result = array(
                        'Status' => '0'
                    );
                } else {
                    // Insert
                    $this->db->insert('db_academic.schedule_details_course', $dataWhere);

                    // Update Action
                    $UpdateLog = (array) $data_arr['UpdateLog'];
                    $this->db->where('ID', $dataWhere['ScheduleID']);
                    $this->db->update('db_academic.schedule', $UpdateLog);

                    $result = array(
                        'Status' => '1'
                    );
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'deleteInEditCourse') {
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $SDCID = $data_arr['SDCID'];

                // GET CDID
                $dataCDID = $this->db->select('CDID')->get_where('db_academic.schedule_details_course', array('ID' => $SDCID), 1)->result_array()[0];

                // Delete Planning
                $whereDelete = array('SemesterID' => $SemesterID, 'ScheduleID' => $ScheduleID, 'CDID' => $dataCDID['CDID']);
                $this->db->where($whereDelete);
                $this->db->delete('db_academic.std_krs');
                $this->db->reset_query();

                // Delete KRS yang sudah approve
                $dataCL = $this->m_api->getClassOf();
                foreach ($dataCL as $itm) {
                    $db_ = 'ta_' . $itm['Year'];
                    // Cek DB Exist
                    $dbExist = $this->db->query('SELECT SCHEMA_NAME
                                                    FROM INFORMATION_SCHEMA.SCHEMATA
                                                    WHERE SCHEMA_NAME = "' . $db_ . '" ')->result_array();
                    if (count($dbExist) > 0) {
                        $this->db->where($whereDelete);
                        $this->db->delete($db_ . '.study_planning');
                        $this->db->reset_query();
                    }
                }

                // Delete
                $this->db->where('ID', $SDCID);
                $this->db->delete('db_academic.schedule_details_course');
                $this->db->reset_query();

                // Delete Attendance
                $dataAttd = $this->db->query('SELECT attd.ID FROM db_academic.attendance attd
                                                            WHERE attd.SemesterID = "' . $SemesterID . '"
                                                            AND attd.ScheduleID = "' . $ScheduleID . '" ')->result_array();

                if (count($dataAttd) > 0) {
                    for ($i = 0; $i < count($dataAttd); $i++) {
                        // Delete Attendance Students
                        $this->db->where('ID_Attd', $dataAttd[$i]['ID']);
                        $this->db->delete('db_academic.attendance_students');
                        $this->db->reset_query();
                    }
                }

                // Update Action
                $UpdateLog = (array) $data_arr['UpdateLog'];
                $this->db->where('ID', $data_arr['ScheduleID']);
                $this->db->update('db_academic.schedule', $UpdateLog);

                return print_r(1);
            } else if ($data_arr['action'] == 'updateInfoInEditCourse') {

                // Cek apakah Class Group Sudah digunakan
                $UpdateForm = (array) $data_arr['UpdateForm'];
                $dataG = $this->db->query('SELECT ID FROM db_academic.schedule s
                                                      WHERE s.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                      AND s.ClassGroup LIKE "' . $UpdateForm['ClassGroup'] . '"
                                                       AND s.ID != "' . $data_arr['ScheduleID'] . '" ')->result_array();

                if (count($dataG) > 0) {
                    $result = array(
                        'Status' => 0
                    );
                } else {

                    // Update Schedule
                    $this->db->where('ID', $data_arr['ScheduleID']);
                    $this->db->update('db_academic.schedule', $UpdateForm);
                    $this->db->reset_query();

                    // Update Untuk Team Teachingnya
                    // -- Delete Team --
                    $this->db->where('ScheduleID', $data_arr['ScheduleID']);
                    $this->db->delete('db_academic.schedule_team_teaching');
                    $this->db->reset_query();

                    // -- Insert yg baru --
                    for ($i = 0; $i < count($data_arr['dataTeamTeaching']); $i++) {
                        $f_i = (array) $data_arr['dataTeamTeaching'][$i];
                        $this->db->insert('db_academic.schedule_team_teaching', $f_i);
                    }

                    // Update Action
                    $UpdateLog = (array) $data_arr['UpdateLog'];
                    $this->db->where('ID', $data_arr['ScheduleID']);
                    $this->db->update('db_academic.schedule', $UpdateLog);
                    $this->db->reset_query();


                    $dataCreditSahre = $data_arr['dataCreditSahre'];
                    if (count($dataCreditSahre) > 0) {
                        // Update Credit Share
                        $this->db->where('ScheduleID', $data_arr['ScheduleID']);
                        $this->db->delete('db_academic.schedule_share_credit');
                        $this->db->reset_query();

                        for ($i = 0; $i < count($dataCreditSahre); $i++) {
                            $dataInsertCreditShare = (array) $dataCreditSahre[$i];
                            $this->db->insert('db_academic.schedule_share_credit', $dataInsertCreditShare);
                        }
                    }



                    $result = array(
                        'Status' => 1
                    );
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'loadEditSchedule') {
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $dataSchedule = $this->db->query('SELECT sd.ClassroomID, sd.Credit, sd.DayID, sd.EndSessions,
                                                              sd.StartSessions, sd.TimePerCredit, sd.ID AS SDID, cl.Room,
                                                              d.NameEng AS DayEng, s.ClassGroup
                                                              FROM db_academic.schedule_details sd
                                                              LEFT JOIN db_academic.schedule s ON (s.ID = sd.ScheduleID)
                                                              LEFT JOIN db_academic.days d ON (d.ID=sd.DayID)
                                                              LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                              WHERE s.ID = "' . $ScheduleID . '"
                                                              AND s.SemesterID = "' . $SemesterID . '"
                                                              ORDER BY d.ID ASC ')->result_array();

                $result = array(
                    'dataSchedule' => $dataSchedule
                );
                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'updateCourse_Edit') {

                $arrUpdate = (array) $data_arr['formInsert'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.schedule_details', $arrUpdate);
                $this->db->reset_query();

                // Update Action
                $UpdateLog = (array) $data_arr['UpdateLog'];
                $this->db->where('ID', $data_arr['ScheduleID']);
                $this->db->update('db_academic.schedule', $UpdateLog);

                return print_r(1);
            } else if ($data_arr['action'] == 'updateCourse_Add') {
                $formInsert = (array) $data_arr['formInsert'];

                // Add TO schedule_details
                $formInsert['ScheduleID'] = $data_arr['ScheduleID'];
                $this->db->insert('db_academic.schedule_details', $formInsert);
                $insert_id = $this->db->insert_id();

                // Add to attendance
                $dataInsertAttd = array(
                    'SemesterID' => $data_arr['SemesterID'],
                    'ScheduleID' => $data_arr['ScheduleID'],
                    'SDID' => $insert_id
                );
                $this->db->insert('db_academic.attendance', $dataInsertAttd);
                $insert_id_attdID = $this->db->insert_id();

                // Cek student yang sudah ngambil siapa aja lalu insert semua
                // Get Student
                $data_attd = $this->db->select('ID')->get_where(
                    'db_academic.attendance',
                    array(
                        'SemesterID' => $data_arr['SemesterID'],
                        'ScheduleID' => $data_arr['ScheduleID']
                    ),
                    1
                )->result_array();

                $dataStudnet = $this->db->select('NPM')->get_where('db_academic.attendance_students', array('ID_Attd' => $data_attd[0]['ID']))->result_array();

                // insert student to attendance
                if (count($dataStudnet) > 0) {
                    foreach ($dataStudnet as $itemS) {
                        $arrIns = array(
                            'ID_Attd' => $insert_id_attdID,
                            'NPM' => $itemS['NPM']
                        );
                        $this->db->insert('db_academic.attendance_students', $arrIns);
                    }
                }


                // Update Action
                $UpdateLog = (array) $data_arr['UpdateLog'];
                $this->db->where('ID', $data_arr['ScheduleID']);
                $this->db->update('db_academic.schedule', $UpdateLog);

                return print_r(1);
            } else if ($data_arr['action'] == 'deleteScheduleCourse') {

                // Get ID Attendance
                $data_attd = $this->db->select('ID')->get_where(
                    'db_academic.attendance',
                    array(
                        'SemesterID' => $data_arr['SemesterID'],
                        'ScheduleID' => $data_arr['ScheduleID'],
                        'SDID' => $data_arr['SDID']
                    ),
                    1
                )->result_array();

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
                $this->db->update('db_academic.schedule', $UpdateLog);

                return print_r(1);
            }
        }
    }

    public function getlistversion()
    {
        //$formInsert = (array) $data_arr['formInsert'];

        $status = $this->input->get('s');
        $requestData = $_REQUEST;

        //$whereStatus = ($status!='') ? ' AND StatusEmployeeID = "'.$status.'" ' : '';
        $totalData = $this->db->query('SELECT aa.IDVersion, aa.Version, dd.Division, dd.NameModule, bb.Description, aa.UpdateAt, cc.Name AS NamePIC, dd.NameGroup
                    FROM db_it.version AS aa
                    INNER JOIN db_it.version_detail AS bb ON (aa.IDVersion = bb.IDVersion)
                    LEFT JOIN db_employees.employees AS cc ON (aa.PIC = cc.NIP)
                    LEFT JOIN (SELECT a.IDGroup, a.NameGroup, b.IDModule, b.NameModule, c.ID AS IDDivision,c.Division
                    FROM db_it.group_module AS a
                    LEFT JOIN db_it.module AS b ON (a.IDGroup = b.IDGroup)
                    LEFT JOIN db_employees.division AS c ON a.IDDivision = c.ID) AS dd ON (bb.IDModule = dd.IDModule) WHERE aa.Active= 1')->result_array();

        if (!empty($requestData['search']['value'])) {
            $sql = 'SELECT aa.IDVersion, aa.Version, dd.Division, dd.NameModule, bb.Description, aa.UpdateAt, cc.Name AS NamePIC, dd.NameGroup
                    FROM db_it.version AS aa
                    INNER JOIN db_it.version_detail AS bb ON (aa.IDVersion = bb.IDVersion)
                    LEFT JOIN db_employees.employees AS cc ON (aa.PIC = cc.NIP)
                    LEFT JOIN (SELECT a.IDGroup, a.NameGroup, b.IDModule, b.NameModule, c.ID AS IDDivision,c.Division
                    FROM db_it.group_module AS a
                    LEFT JOIN db_it.module AS b ON (a.IDGroup = b.IDGroup)
                    LEFT JOIN db_employees.division AS c ON a.IDDivision = c.ID) AS dd ON (bb.IDModule = dd.IDModule)';
            $sql .= ' WHERE aa.Active= 1 AND cc.Name LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR dd.Division LIKE "%' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR dd.NameModule LIKE "' . $requestData['search']['value'] . '%" ';
            //$sql.= ') ORDER BY NIP,em.PositionMain ASC';
            $sql .= 'ORDER BY aa.IDVersion DESC';
        } else {
            $sql = 'SELECT aa.IDVersion, aa.Version, dd.Division, dd.NameModule, bb.Description, aa.UpdateAt, cc.Name AS NamePIC, dd.NameGroup
                    FROM db_it.version AS aa
                    INNER JOIN db_it.version_detail AS bb ON (aa.IDVersion = bb.IDVersion)
                    LEFT JOIN db_employees.employees AS cc ON (aa.PIC = cc.NIP)
                    LEFT JOIN (SELECT a.IDGroup, a.NameGroup, b.IDModule, b.NameModule, c.ID AS IDDivision,c.Division
                    FROM db_it.group_module AS a
                    LEFT JOIN db_it.module AS b ON (a.IDGroup = b.IDGroup)
                    LEFT JOIN db_employees.division AS c ON a.IDDivision = c.ID) AS dd ON (bb.IDModule = dd.IDModule) WHERE aa.Active= 1 ';
            //$sql.= 'ORDER BY NIP,em.PositionMain ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';
            $sql .= 'ORDER BY aa.IDVersion DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        }

        $query = $this->db->query($sql)->result_array();
        //$sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';
        $no = $requestData['start'] + 1;

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            //$jb = explode('.',$row["PositionMain"]);
            $Division = '';
            $Position = '';
            //$nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["Version"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["Division"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["NameGroup"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["NameModule"] . '</div>';
            $nestedData[] = '<div style="text-align: left;">' . $row["Description"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["UpdateAt"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["NamePIC"] . '</div>';
            $nestedData[] = '<div style="text-align: center;"><button type="button" class="btn btn-sm btn-primary btn-circle btnviewversion" versionid="' . $row["IDVersion"] . '" data-toggle="tooltip" data-placement="top" title="Details"><i class="glyphicon glyphicon-th-list"></i></button> <button class="btn btn-sm btn-circle btn-danger btndeleteversion" data-toggle="tooltip" versionid="' . $row["IDVersion"] . '" data-placement="top" title="Delete"><i class="fa fa-trash"></i> </button> <button class="btn btn-sm btn-success btn-circle btneditversion" data-toggle="tooltip" versionid="' . $row["IDVersion"] . '" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button> </div>';
            $no++;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getlistgroupmodule()
    {  //get data module

        $status = $this->input->get('s');
        $requestData = $_REQUEST;

        $totalData = $this->db->query('SELECT X.IDGroup, X.NameGroup, Y.IDModule, Y.NameModule, Z.ID, Z.Division, Z.ID, Y.Description
        FROM db_it.group_module AS X
        LEFT JOIN db_employees.division Z ON (X.IDDivision = Z.ID)
        LEFT JOIN db_it.module AS Y ON (X.IDGroup = Y.IDGroup) WHERE Y.Active = 1 AND X.Active = 1')->result_array();

        if (!empty($requestData['search']['value'])) {
            $sql = 'SELECT X.IDGroup, X.NameGroup, Y.IDModule, Y.NameModule, Z.ID, Z.Division, Z.ID, Y.Description
                    FROM db_it.group_module AS X
                    LEFT JOIN db_employees.division Z ON (X.IDDivision = Z.ID)
                    LEFT JOIN db_it.module AS Y ON (X.IDGroup = Y.IDGroup) WHERE Y.Active = 1 AND X.Active = 1 AND (';
            $sql .= ' X.NameGroup LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR Z.Division LIKE "%' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR Y.NameModule LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' ) ORDER BY X.IDGroup DESC';
        } else {
            $sql = 'SELECT X.IDGroup, X.NameGroup, Y.IDModule, Y.NameModule, Z.ID, Z.Division, Z.ID, Y.Description
                    FROM db_it.group_module AS X
                    LEFT JOIN db_employees.division Z ON (X.IDDivision = Z.ID)
                    LEFT JOIN db_it.module AS Y ON (X.IDGroup = Y.IDGroup) WHERE Y.Active = 1 AND X.Active = 1 ';
            $sql .= 'ORDER BY X.IDGroup DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        }

        $query = $this->db->query($sql)->result_array();
        $no = $requestData['start'] + 1;

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $Division = '';
            $Position = '';
            //$nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["Division"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["NameGroup"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["NameModule"] . '</div>';
            $nestedData[] = '<div style="text-align: left;">' . $row["Description"] . '</div>';
            $nestedData[] = '<div style="text-align: center;"><button type="button" class="btn btn-sm btn-primary btn-circle btnviewgroupmodule" versionid="' . $row["IDModule"] . '" data-toggle="tooltip" data-placement="top" title="Details"><i class="glyphicon glyphicon-th-list"></i></button> <button class="btn btn-sm btn-circle btn-danger btndeletegroup" data-toggle="tooltip" versionid="' . $row["IDModule"] . '" data-placement="top" title="Delete"><i class="fa fa-trash"></i> </button> <button class="btn btn-sm btn-success btn-circle btneditgroupmodule" data-toggle="tooltip" groupid="' . $row["IDModule"] . '" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button> </div>';
            $no++;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function rektorat_listacademicfile()
    {  //menu rektorat academic files
        $typefile = $this->input->get('type');
        $otherfile = $this->input->get('other');
        $requestData = $_REQUEST;

        if ($typefile == "0") {

            $totalData = $this->db->query('SELECT f.*, mf.TypeFiles AS M_TypeFiles,  p.Name_other_files, xx.Name, ab.Name AS NamaCreate
                                            FROM db_employees.files f
                                            LEFT JOIN db_employees.master_files mf ON (mf.ID = f.TypeFiles)
                                            LEFT JOIN db_employees.master_other_files AS p ON (f.ID_OtherFiles = p.ID)
                                            LEFT JOIN db_employees.employees AS xx ON (f.NIP = xx.NIP)
                                            LEFT JOIN db_employees.employees AS ab ON (f.UserCreate = ab.NIP)
                                            WHERE f.LinkFiles IS NOT NULL')->result_array();

            if (!empty($requestData['search']['value'])) {
                $sql = 'SELECT f.*, mf.TypeFiles AS M_TypeFiles, p.Name_other_files, xx.Name, ab.Name AS NamaCreate
                        FROM db_employees.files f
                        LEFT JOIN db_employees.master_files mf ON (mf.ID = f.TypeFiles)
                        LEFT JOIN db_employees.master_other_files AS p ON (f.ID_OtherFiles = p.ID)
                        LEFT JOIN db_employees.employees AS xx ON (f.NIP = xx.NIP)
                        LEFT JOIN db_employees.employees AS ab ON (f.UserCreate = ab.NIP)
                        WHERE f.LinkFiles IS NOT NULL AND (';
                $sql .= 'xx.Name LIKE "' . $requestData['search']['value'] . '%" ';
                $sql .= ' OR f.NIP LIKE "%' . $requestData['search']['value'] . '%" ';
                $sql .= ') ORDER BY f.ID DESC';
            } else {
                $sql = 'SELECT f.*, mf.TypeFiles AS M_TypeFiles,  p.Name_other_files, xx.Name, ab.Name AS NamaCreate
                        FROM db_employees.files f
                        LEFT JOIN db_employees.master_files mf ON (mf.ID = f.TypeFiles)
                        LEFT JOIN db_employees.master_other_files AS p ON (f.ID_OtherFiles = p.ID)
                        LEFT JOIN db_employees.employees AS xx ON (f.NIP = xx.NIP)
                        LEFT JOIN db_employees.employees AS ab ON (f.UserCreate = ab.NIP)
                        WHERE f.LinkFiles IS NOT NULL ';
                $sql .= 'ORDER BY f.ID DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            }
        } else {

            $whereType = ($typefile != '') ? 'AND f.TypeFiles = "' . $typefile . '" ' : '';
            //$whereOtherFile = ($otherfile!='') ? 'AND f.ID_OtherFiles = "'.$otherfile.'" ' : '';
            $uniowhere = $whereType;

            $totalData = $this->db->query('SELECT f.*, mf.TypeFiles AS M_TypeFiles,  p.Name_other_files, xx.Name, ab.Name AS NamaCreate
                                            FROM db_employees.files f
                                            LEFT JOIN db_employees.master_files mf ON (mf.ID = f.TypeFiles)
                                            LEFT JOIN db_employees.master_other_files AS p ON (f.ID_OtherFiles = p.ID)
                                            LEFT JOIN db_employees.employees AS xx ON (f.NIP = xx.NIP)
                                            LEFT JOIN db_employees.employees AS ab ON (f.UserCreate = ab.NIP)
                                            WHERE f.LinkFiles IS NOT NULL ' . $uniowhere)->result_array();

            if (!empty($requestData['search']['value'])) {
                $sql = 'SELECT f.*, mf.TypeFiles AS M_TypeFiles, p.Name_other_files, xx.Name, ab.Name AS NamaCreate
                            FROM db_employees.files f
                            LEFT JOIN db_employees.master_files mf ON (mf.ID = f.TypeFiles)
                            LEFT JOIN db_employees.master_other_files AS p ON (f.ID_OtherFiles = p.ID)
                            LEFT JOIN db_employees.employees AS xx ON (f.NIP = xx.NIP)
                            LEFT JOIN db_employees.employees AS ab ON (f.UserCreate = ab.NIP)
                            WHERE f.LinkFiles IS NOT NULL ' . $uniowhere;
                $sql .= 'AND (xx.Name LIKE "' . $requestData['search']['value'] . '%" ';
                $sql .= ' OR f.NIP LIKE "%' . $requestData['search']['value'] . '%" ';
                $sql .= ') ORDER BY f.ID DESC';
            } else {
                $sql = 'SELECT f.*, mf.TypeFiles AS M_TypeFiles,  p.Name_other_files, xx.Name, ab.Name AS NamaCreate
                            FROM db_employees.files f
                            LEFT JOIN db_employees.master_files mf ON (mf.ID = f.TypeFiles)
                            LEFT JOIN db_employees.master_other_files AS p ON (f.ID_OtherFiles = p.ID)
                            LEFT JOIN db_employees.employees AS xx ON (f.NIP = xx.NIP)
                            LEFT JOIN db_employees.employees AS ab ON (f.UserCreate = ab.NIP)
                            WHERE f.LinkFiles IS NOT NULL ' . $uniowhere;
                $sql .= 'ORDER BY f.ID DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            }
        }

        $query = $this->db->query($sql)->result_array();
        $no = $requestData['start'] + 1;

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $date_upload = date('d M Y H:i', strtotime($row['DateCreate']));

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div style="text-align: left;">' . $row["NIP"] . ' - ' . $row["Name"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["M_TypeFiles"] . '</div>';
            $nestedData[] = '<div style="text-align: left;">' . $row["Description_Files"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["NamaCreate"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $date_upload . '</div>';
            $nestedData[] = '<div style="text-align: center;"><button type="button" class="btn btn-primary btn-round btnviewlistsrata" filesub="' . $row["LinkFiles"] . '" data-toggle="tooltip" data-placement="top" title="Preview"><i class="fa fa-eye"></i> Preview</button></div>';
            $nestedData[] = '<div style="text-align: center;"><button type="button" class="btn btn-primary btn-round btnviewgroupmodule" file_id="' . $row["ID"] . '" data-toggle="tooltip" data-placement="top" title="Details"><i class="glyphicon glyphicon-th-list"></i> Details</button></div>';
            $no++;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }


    public function getlistmodule()
    {

        $status = $this->input->get('s');
        $requestData = $_REQUEST;

        $totalData = $this->db->query(' SELECT a.IDGroup,a.NameGroup, a.IDDivision, b.Division, a.Active
                     FROM db_it.group_module AS a
                     LEFT JOIN db_employees.division AS b ON a.IDDivision = b.ID
                     WHERE a.Active = 1 ')->result_array();

        if (!empty($requestData['search']['value'])) {
            $sql = ' SELECT a.IDGroup,a.NameGroup, a.IDDivision, b.Division, a.Active
                     FROM db_it.group_module AS a
                     LEFT JOIN db_employees.division AS b ON a.IDDivision = b.ID
                     WHERE a.Active = 1 AND (';
            $sql .= ' a.NameGroup LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR b.Division LIKE "%' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR a.IDDivision LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' ) ORDER BY a.IDGroup DESC';
        } else {
            $sql = 'SELECT a.IDGroup,a.NameGroup, a.IDDivision, b.Division, a.Active
                 FROM db_it.group_module AS a
                 LEFT JOIN db_employees.division AS b ON a.IDDivision = b.ID
                 WHERE a.Active = 1 ';
            $sql .= 'ORDER BY a.IDGroup DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        }

        $query = $this->db->query($sql)->result_array();
        $no = $requestData['start'] + 1;

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $stats = ($row["Active"] == '1') ? 'Active' : 'Non Active';

            //$nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["Division"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["NameGroup"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $stats . '</div>';
            $nestedData[] = '<div style="text-align: center;"><button class="btn btn-sm btn-circle btn-danger btndeletemodule" data-toggle="tooltip" versionid="' . $row["IDGroup"] . '" data-placement="top" title="Delete"><i class="fa fa-trash"></i> </button> <button class="btn btn-sm btn-success btn-circle btneditgroups" data-toggle="tooltip" groupid="' . $row["IDGroup"] . '" data-placement="top" title="Edit"><i class="fa fa-edit"></i></button> </div>';
            $no++;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getdatarequestdocument()
    {
        //$status = $this->input->get('s');
        $requestData = $_REQUEST;
        $NIP = $this->session->userdata('NIP');

        $totalData = $this->db->query('SELECT a.*, b.TypeFiles, b.NameFiles, c.Name
                    FROM db_employees.request_document AS a
                    LEFT JOIN db_employees.master_files AS b ON (a.IDTypeFiles = b.ID)
                    LEFT JOIN db_employees.employees AS c ON (c.NIP = a.NIP)
                    WHERE a.NIP = "' . $NIP . '" AND a.Active = 1 AND b.RequestDocument = 1')->result_array();

        if (!empty($requestData['search']['value'])) {
            $sql = 'SELECT a.*, b.TypeFiles, b.NameFiles, c.Name
                    FROM db_employees.request_document AS a
                    LEFT JOIN db_employees.master_files AS b ON (a.IDTypeFiles = b.ID)
                    LEFT JOIN db_employees.employees AS c ON (c.NIP = a.NIP)
                    WHERE a.NIP = "' . $NIP . '" AND a.Active = 1 AND b.RequestDocument = 1 AND (';
            $sql .= ' b.NameFiles LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' OR c.Name LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= ' ) ORDER BY a.IDRequest DESC';
        } else {
            $sql = 'SELECT a.*, b.TypeFiles, b.NameFiles, c.Name
                    FROM db_employees.request_document AS a
                    LEFT JOIN db_employees.master_files AS b ON (a.IDTypeFiles = b.ID)
                    LEFT JOIN db_employees.employees AS c ON (c.NIP = a.NIP)
                    WHERE a.NIP = "' . $NIP . '" AND a.Active = 1 AND b.RequestDocument = 1 ';
            $sql .= 'ORDER BY a.IDRequest DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
        }

        $query = $this->db->query($sql)->result_array();
        $no = $requestData['start'] + 1;

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $stats = ($row["Active"] == '1') ? 'Active' : 'Non Active';

            $StartDate = date('d M Y H:i', strtotime($row['StartDate']));
            $EndDate = date('d M Y H:i', strtotime($row['EndDate']));

            if ($row['ConfirmStatus'] == 1) {

                $token = $this->jwt->encode(array(
                    'NIP' => $row['NIP'],
                    'IDRequest' => $row['IDRequest']
                ), 'UAP)(*');
                $linksurat = base_url('save2pdf/suratTugasKeluar/' . $token);
                $buttonlink = '<a href="' . $linksurat . '" class="btn btn-success btn-circle" target="_blank" data-toggle="tooltip" data-placement="top" title="Download"><i class="fa fa-download"></i></a> ';
            } else if ($row['ConfirmStatus'] == -1) {
                $buttonlink = '<p style="color:red;"> Rejected </p>';
            } else {
                $buttonlink = '<p style="color:blue;"> Waiting Confirmation </p>';
            }

            if ($row['DateConfirm'] == '0000-00-00 00:00:00') {
                $dateconfirms = '';
            } else {
                $datex = $row['DateConfirm'];
                $dateconfirms = date("d M Y H:i", strtotime($row['DateConfirm']));
            }

            //$nestedData[] = ($row["Gender"]=='P') ? 'Female' : 'Male';
            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $row["NIP"] . ' - ' . $row["Name"] . '</div>';
            $nestedData[] = '<div style="text-align: left;">' . $row["ForTask"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $StartDate . '  -  ' . $EndDate . '</div>';
            $nestedData[] = '<div style="text-align: left;">' . $row["DescriptionAddress"] . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $dateconfirms . '</div>';
            $nestedData[] = '<div style="text-align: center;">' . $buttonlink . '</div>';
            $nestedData[] = '<div style="text-align: center;">
            <button class="btn btn-sm btn-circle btn-primary btndetailrequest" requestid ="' . $row["IDRequest"] . '" data-toggle="tooltip" data-placement="top" title="Detail"><i class="icon-list icon-large"></i> </button>
            <button class="btn btn-sm btn-circle btn-danger btndeleterequest" requestid ="' . $row["IDRequest"] . '" data-toggle="tooltip" data-placement="top" title="Delete"><i class="fa fa-trash"></i> </button></div>';
            $no++;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }


    public function getversiondetail()
    {

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if ($data_arr['action'] == 'getdetail') {
            $idversion = $this->input->get('s');
            $details = $this->db->query('SELECT aa.IDVersion, aa.Version, dd.Division, dd.NameModule, bb.Description, aa.UpdateAt, cc.Name AS NamePIC, dd.IDDivision
                    FROM db_it.version AS aa
                    INNER JOIN db_it.version_detail AS bb ON (aa.IDVersion = bb.IDVersion)
                    LEFT JOIN db_employees.employees AS cc ON (aa.PIC = cc.NIP)
                    LEFT JOIN (SELECT a.IDGroup, a.NameGroup, b.IDModule, b.NameModule, c.ID AS IDDivision,c.Division
                    FROM db_it.group_module AS a
                    LEFT JOIN db_it.module AS b ON (a.IDGroup = b.IDGroup)
                    LEFT JOIN db_employees.division AS c ON a.IDDivision = c.ID) AS dd ON (bb.IDModule = dd.IDModule)
                    WHERE aa.IDVersion = "' . $idversion . '" ')->result_array();
            echo json_encode($details);
        } else if ($data_arr['action'] == 'getedit') {

            $idversion = $this->input->get('s');
            $details = $this->db->query('SELECT aa.IDVersion, aa.Version, dd.Division, dd.IDGroup, dd.Namegroup, dd.NameModule, bb.Description, aa.UpdateAt, cc.Name AS NamePIC, cc.NIP, dd.IDDivision
                    FROM db_it.version AS aa
                    INNER JOIN db_it.version_detail AS bb ON (aa.IDVersion = bb.IDVersion)
                    LEFT JOIN db_employees.employees AS cc ON (aa.PIC = cc.NIP)
                    LEFT JOIN (SELECT a.IDGroup, a.NameGroup, b.IDModule, b.NameModule, c.ID AS IDDivision,c.Division
                    FROM db_it.group_module AS a
                    LEFT JOIN db_it.module AS b ON (a.IDGroup = b.IDGroup)
                    LEFT JOIN db_employees.division AS c ON a.IDDivision = c.ID) AS dd ON (bb.IDModule = dd.IDModule)
                    WHERE aa.IDVersion = "' . $idversion . '" ')->result_array();
            echo json_encode($details);
        }
    }

    public function getgroupmoddetail()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if ($data_arr['action'] == 'getdetail') {
            $idgroup = $this->input->get('s');
            $details = $this->db->query('SELECT X.IDGroup, X.NameGroup, Y.IDModule, Y.NameModule, Z.ID, Z.Division, Z.ID, Y.Description
                    FROM db_it.group_module AS X
                    LEFT JOIN db_employees.division Z ON (X.IDDivision = Z.ID)
                    LEFT JOIN db_it.module AS Y ON (X.IDGroup = Y.IDGroup)
                    WHERE Y.IDModule = "' . $idgroup . '" ')->result_array();
            echo json_encode($details);
        } else if ($data_arr['action'] == 'getedit') {  //get data module

            $idmodules = $this->input->get('s');
            $details = $this->db->query('SELECT X.IDGroup, X.NameGroup, Y.IDModule, Y.NameModule, Z.ID, Z.Division, Z.ID, Y.Description
                    FROM db_it.group_module AS X
                    LEFT JOIN db_employees.division Z ON (X.IDDivision = Z.ID)
                    LEFT JOIN db_it.module AS Y ON (X.IDGroup = Y.IDGroup)
                    WHERE Y.IDModule = "' . $idmodules . '" ')->result_array();

            echo json_encode($details);
        } else if ($data_arr['action'] == 'geteditgroupdata') {

            $idgroup = $this->input->get('s');
            $details = $this->db->query('SELECT a.IDGroup, a.NameGroup, a.IDDivision, b.Division, a.Active
                         FROM db_it.group_module AS a
                         LEFT JOIN db_employees.division AS b ON a.IDDivision = b.ID
                         WHERE a.IDGroup = "' . $idgroup . '" ')->result_array();
            echo json_encode($details);
        }
    }




    function search_module()
    {
        // if (isset($_REQUEST['q'])) {

        //    $results = $this->db->query('SELECT * FROM db_it.group_module WHERE NameGroup LIKE "%'.$_REQUEST['q'].'%" ')->result_array();

        //$results = $this->data_model->get_data($_REQUEST['q']);
        //    echo json_encode($results);
        //}

        $keyword = $this->input->post('term');
        $data['response'] = 'false'; //Set default response
        $query = $this->m_api->getRowsmodule($keyword); //Search DB
        if (!empty($query)) {
            $data['response'] = 'true'; //Set response
            $data['message'] = array(); //Create array

            foreach ($query as $row) {
                $data['message'][] = array(
                    'id' => $row->id,
                    'NameGroup' => $row->printable_name,
                    ''
                );  //Add a row to array
            }
        }

        //echo json_encode($data); //echo json string if ajax request
        echo json_encode($data);
        die;
    }

    public function getSchedulePerDay()
    {
        $requestData = $_REQUEST;

        $token = $this->input->get('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        $dataWhere = (array) $data_arr['dataWhere'];

        if (!empty($requestData['search']['value'])) {
            $sql = $this->m_api->getSchedulePerDaySearch($data_arr['DayID'], $dataWhere, $requestData['search']['value']);
            $query = $this->db->query($sql)->result_array();
            $totalData = $query;
        } else {
            $totalData = $this->m_api->getTotalPerDay($data_arr['DayID'], $dataWhere);
            $sql = $this->m_api->getSchedulePerDayLimit($data_arr['DayID'], $dataWhere, $requestData['start'], $requestData['length']);

            $query = $this->db->query($sql)->result_array();
        }



        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            // Mendapatkan Jumlah Students
            $Students = $this->m_api->getTotalStdPerDay($row['SemesterID'], $row['ID'], $row['CDID']);
            $total_std_KRS = $this->m_api->getTotalStdNotYetApprovePerDay($row['SemesterID'], $row['ID'], $row['CDID']);

            $StudentsNY = count($total_std_KRS);

            $btnDelMK = ($Students > 0) ? 1 : 0;
            // Group Kelas
            $groupClass = '<b><a href="javascript:void(0)" class="btn-action" data-page="editjadwal" data-btndel="' . $btnDelMK . '" data-id="' . $row['ID'] . '">' . $row["ClassGroup"] . '</a></b>';
            $sbSesi = ($row['SubSesi'] == '1' || $row['SubSesi'] == 1) ? '<br/><span class="label label-warning">Sub-Sesi</span>' : '';

            $TeamTeaching = '';
            if ($row["TeamTeaching"] == 1) {
                $TeamTeaching = $this->m_api->getTeamTeachingPerDay($row['ID']);
            }

            $coor = '<div style="color: #427b44;margin-bottom: 10px;"><b>' . $row["Lecturer"] . '</b></div>';

            // Mendapatkan matakuliah
            $courses = $this->m_api->getCoursesPerDay($row['ID']);

            $nestedData[] = '<div style="text-align:center;">' . $groupClass . '' . $sbSesi . '</div>';
            $nestedData[] = $courses;
            $nestedData[] = '<div style="text-align:center;">' . $row["Credit"] . '</div>';
            $nestedData[] = $coor . '' . $TeamTeaching;
            $nestedData[] = '<div style="text-align:center;"><a href="javascript:void(0)"
                                class="btn-sw-std" data-smtid="' . $row['SemesterID'] . '"
                                data-scheduleid="' . $row['ID'] . '" data-flag="sp"
                                data-cdid="' . $row['CDID'] . '">' . $Students . '</a> of
                                <a href="javascript:void(0)" class="btn-sw-std" data-smtid="' . $row['SemesterID'] . '"
                                data-scheduleid="' . $row['ID'] . '" data-flag="std"
                                data-cdid="' . $row['CDID'] . '">' . $StudentsNY . '</a></div>';

            $nestedData[] = '<div style="text-align:center;">' . substr($row["StartSessions"], 0, 5) . ' - ' . substr($row["EndSessions"], 0, 5) . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . $row["Room"] . '</div>';

            $data[] = $nestedData;
        }


        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getSchedulePerSemester()
    {
        $requestData = $_REQUEST;

        $token = $this->input->get('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        $dataWhere = (array) $data_arr['dataWhere'];

        if (!empty($requestData['search']['value'])) {
            $query = $this->m_api->getTotalPerSemesterSearch($dataWhere, $requestData['search']['value']);
            //            $query = $this->db->query($sql)->result_array();
            $totalData = $query;
        } else {
            $totalData = $this->m_api->getTotalPerSemester($dataWhere);
            $query = $this->m_api->getTotalPerSemesterLimit($dataWhere, $requestData['start'], $requestData['length']);
        }


        $data = array();
        $no = 1;
        for ($i = 0; $i < count($query); $i++) {

            $nestedData = array();
            $row = $query[$i];

            $Students = $this->m_api->getTotalStdPerDay($row['SemesterID'], $row['ID'], $row['CDID']);
            $total_std_KRS = $this->m_api->getTotalStdNotYetApprovePerDay($row['SemesterID'], $row['ID'], $row['CDID']);

            $StudentsNY = count($total_std_KRS);

            // Group Kelas
            $groupClass = '<b><a href="javascript:void(0)" class="btn-action" data-page="editjadwal" data-id="' . $row['ID'] . '">' . $row["ClassGroup"] . '</a></b>';
            $sbSesi = ($row['SubSesi'] == '1' || $row['SubSesi'] == 1) ? '<br/><span class="label label-warning">Sub-Sesi</span>' : '';

            $TeamTeaching = '';
            if ($row["TeamTeaching"] == 1) {
                $TeamTeaching = $this->m_api->getTeamTeachingPerDay($row['ID']);
            }

            $coor = '<div style="color: #427b44;margin-bottom: 10px;"><b>' . $row["Lecturer"] . '</b></div>';

            // Mendapatkan matakuliah
            $courses = $this->m_api->getCoursesPerDay($row['ID']);

            // Mendapatkan Jumlah Students
            //            $Students = $this->m_api->getTotalStdPerDay($row['SemesterID'],$row['ID']);


            //            $nestedData[] = '<div style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div style="text-align:center;">' . $groupClass . '' . $sbSesi . '</div>';
            $nestedData[] = $courses;
            $nestedData[] = '<div style="text-align:center;">' . $row["Credit"] . '</div>';
            $nestedData[] = $coor . '' . $TeamTeaching;
            $nestedData[] = '<div style="text-align:center;"><a href="javascript:void(0)" class="btn-sw-std" data-smtid="' . $row['SemesterID'] . '" data-scheduleid="' . $row['ID'] . '" data-flag="sp" data-cdid="' . $row['CDID'] . '">' . $Students . '</a> of <a href="javascript:void(0)" class="btn-sw-std" data-smtid="' . $row['SemesterID'] . '" data-scheduleid="' . $row['ID'] . '" data-flag="std" data-cdid="' . $row['CDID'] . '">' . $StudentsNY . '</a></div>';
            $nestedData[] = '<div style="text-align:center;"><b>' . $row['DayEng'] . '</b><br/>' . substr($row["StartSessions"], 0, 5) . ' - ' . substr($row["EndSessions"], 0, 5) . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . $row["Room"] . '</div>';

            $no++;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getScheduleExam()
    {
        $requestData = $_REQUEST;

        $data_arr = $this->getInputToken();

        $dateNow = $this->m_rest->getDateNow();

        $whereP = ($data_arr['ExamDate'] != null && $data_arr['ExamDate'] != '')
            ? 'ex.SemesterID = "' . $data_arr['SemesterID'] . '" AND ex.Type LIKE "' . $data_arr['Type'] . '" AND ex.Status = "1" AND ex.ExamDate LIKE "' . $data_arr['ExamDate'] . '" '
            : 'ex.SemesterID = "' . $data_arr['SemesterID'] . '" AND ex.Type LIKE "' . $data_arr['Type'] . '" AND ex.Status = "1"';

        $orderBy = ' ORDER BY ex.ExamDate, ex.ExamStart, ex.ExamEnd ASC ';
        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {

            $search = $requestData['search']['value'];
            $dataSearch = ' AND
                                 (d.NameEng LIKE "%' . $search . '%" OR cl.Room LIKE "%' . $search . '%"
                                 OR p1.Name LIKE "%' . $search . '%" OR p2.Name LIKE "%' . $search . '%"
                                 OR p1.NIP LIKE "%' . $search . '%" OR p2.NIP LIKE "%' . $search . '%"
                                 ) ';
        }

        $queryDefault = 'SELECT ex.ID, ex.ExamDate, ex.ExamStart, ex.ExamEnd, ex.OnlineLearning, cl.Room, p1.Name AS P_Name1, p2.Name AS P_Name2,
                                et.ID AS ExamTaskID, et.Description, et.File,
                                p1.NIP AS P_NIP1, p2.NIP AS P_NIP2, em.Name AS InsertByName, ex.InsertAt
                                FROM db_academic.exam ex
                                LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                LEFT JOIN db_employees.employees p1 ON (p1.NIP = ex.Pengawas1)
                                LEFT JOIN db_employees.employees p2 ON (p2.NIP = ex.Pengawas2)
                                LEFT JOIN db_employees.employees em ON (em.NIP = ex.InsertBy)
                                LEFT JOIN db_academic.days d ON (d.ID = ex.DayID)
                                LEFT JOIN db_academic.exam_task et ON (et.ExamID = ex.ID)
                                WHERE ( ' . $whereP . ' ) ' . $dataSearch . ' ' . $orderBy;

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';



        $dataTable = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        if (count($dataTable) > 0) {
            for ($i = 0; $i < count($dataTable); $i++) {
                $dataC = $this->db->query('SELECT exg.ID, exg.ScheduleID, s.ClassGroup, mk.NameEng AS CourseEng, mk.MKCode, em.Name AS Coordinator
                                                        FROM db_academic.exam_group exg
                                                        LEFT JOIN db_academic.schedule s ON (s.ID = exg.ScheduleID)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                        lEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        WHERE exg.ExamID = "' . $dataTable[$i]['ID'] . '"
                                                        GROUP BY exg.ScheduleID ORDER BY s.ClassGroup ASC')->result_array();

                if (count($dataC) > 0) {
                    for ($s = 0; $s < count($dataC); $s++) {
                        $dataSt = $this->db->query('SELECT NPM,DB_Students FROM db_academic.exam_details exd
                                                              WHERE exd.ExamID = "' . $dataTable[$i]['ID'] . '"
                                                               AND exd.ExamGroupID = "' . $dataC[$s]['ID'] . '"
                                                               AND exd.ScheduleID = "' . $dataC[$s]['ScheduleID'] . '"
                                                                ')->result_array();

                        // Details Students
                        if (count($dataSt) > 0) {
                            for ($ss = 0; $ss < count($dataSt); $ss++) {
                                $ddst = $this->db->select('Name')->get_where(
                                    $dataSt[$ss]['DB_Students'] . '.students',
                                    array('NPM' => $dataSt[$ss]['NPM']),
                                    1
                                )->result_array();
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
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $p = ($row['P_Name2'] != '' && $row['P_Name2'] != null)
                ? ' - ' . $row['P_NIP1'] . ' | ' . $row['P_Name1'] . '<br/> - ' . $row['P_NIP2'] . ' | ' . $row['P_Name2']
                : ' - ' . $row['P_NIP1'] . ' | ' . $row['P_Name1'];

            $course = '';
            $totalStudent = 0;
            for ($c = 0; $c < count($row['Course']); $c++) {
                $d = $row['Course'][$c];
                $totalStudent = $totalStudent + count($d['DetailsStudent']);
                $course = $course . ' <div style="margin-top: 1px;"><b>' . $d['ClassGroup'] . ' | ' . $d['CourseEng'] . '</b><br/><p style="color: #2196F3;font-size: 11px;">(Co) ' . $d['Coordinator'] . '</p></div> ';
            }


            $exam_date = date("l, d M Y", strtotime($row['ExamDate']));
            $exam_time = substr($row['ExamStart'], 0, 5) . ' - ' . substr($row['ExamEnd'], 0, 5);
            $exam_room = $row['Room'];
            $data_token_soal_jawaban = array(
                'Semester' => strtoupper($data_arr['Semester']),
                'Exam' => strtoupper($data_arr['Type']),
                'Date' => $exam_date,
                'Time' => $exam_time,
                'Room' => $exam_room,
                'Pengawas_1' => $row['P_NIP1'] . ' - ' . $row['P_Name1'],
                'Pengawas_2' => ($row['P_Name2'] != '' && $row['P_Name2'] != null) ? $row['P_NIP2'] . ' - ' . $row['P_Name2'] : '-',
                'Course' => $row['Course']

            );

            $tkn_soal_jawaban = $this->jwt->encode($data_token_soal_jawaban, 'UAP)(*');

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
            $tkn_attendance_std = $this->jwt->encode($data_token_attendance_std, 'UAP)(*');


            //            <li><a class="btnSave2PDF_Exam" href="javascript:void(0);" data-url="save2pdf/attendance-list" data-token="'.$tkn_attendance_std.'">Exam Attendance</a></li>
            //                    <li><a target="_blank" href="'.base_url('save2pdf/news-event').'">Berita Acara</a></li>
            $re = ($data_arr['Type'] == 're_uts' || $data_arr['Type'] == 're_uas') ? 'hide' : '';

            $actDelete = ($row['ExamDate'] > $dateNow)
                ? '<li role="separator" class="divider"></li><li><a class="btnDeleteExam" data-id="' . $row['ID'] . '" href="javascript:void(0);" style="color: red;">Delete</a></li>'
                : '';

            $actUploadTask = ($row['ExamDate'] > $dateNow) ? '1' : '0';

            $act = (!isset($data_arr['ForQuiz'])) ? '<div  style="text-align:center;"><div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li class="' . $re . '"><a target="_blank" href="' . base_url('academic/exam-schedule/edit-exam-schedule/' . $row['ID']) . '">Edit</a></li>
                    <li><a href="javascript:void(0);" class="uploadSoal" data-id="' . $row['ID'] . '" data-act="' . $actUploadTask . '">Upload Exam Task</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a target="_blank" href="' . base_url('save2pdf/exam-layout/' . $row['ID']) . '">Layout</a></li>
                    <li><a class="btnSave2PDF_Exam" href="javascript:void(0);" data-url="save2pdf/draft_questions_answer_sheet" data-token="' . $tkn_soal_jawaban . '">Draft Questions  & Answer Sheet</a></li>
                  ' . $actDelete . '  
                  </ul>
                </div>
                </div>' : '<button class="btn btn-sm btn-success btnActAddToQuiz" data-id="' . $row['ID'] . '"><i class="fa fa-arrow-right"></i></button>';

            $dateInsert = ($row['InsertAt'] != '' && $row['InsertAt'] != null) ? date('l, d M Y h:i', strtotime($row['InsertAt'])) : '-';

            // Cek apakah jadwal online atau bukan
            $isOnline = ($row['OnlineLearning'] == 1 || $row['OnlineLearning'] == '1')
                ? '<div><span class="label label-success">Online</span></div>' : '';
            $isOnlineExam = ($row['ExamTaskID'] != '' && $row['ExamTaskID'] != null)
                ? '<div><i style="color: green;" class="fa fa-check-square"></i> Exam file uploaded</div>' : '';






            $nestedData[] = '<div>' . ($no++) . '</div>';
            $nestedData[] = $course;
            $nestedData[] = $p;
            $nestedData[] = '<div><a href="javascript:void(0);" class="btnShowDetailStdExam" data-examid="' . $row['ID'] . '">' . $totalStudent . '</a>
                                <br/><a href="' . base_url('academic/exam-schedule/live-chat/' . $row['ID']) . '" target="_blank" class="btn btn-sm btn-success btn-live-chat hide">Live Chat</a></div>';
            $nestedData[] = $act;
            $nestedData[] = '<div>' . $exam_date . '<br/>' . $exam_time . '</div>';
            $nestedData[] = '<div>' . $exam_room . $isOnline . '</div>';

            if (!isset($data_arr['ForQuiz'])) {
                // Cek apakah exam menggunakan quiz atau tidak
                $dataCkQuiz = $this->db->get_where('db_academic.q_exam', array('ExamID' => $row['ID']))->result_array();
                $isUseQuiz = (count($dataCkQuiz) > 0)
                    ? '<div><i style="color: green;" class="fa fa-check-square"></i> Use quiz <a href="' . base_url('academic/exam-schedule/exam-quiz-create?id=' . $dataCkQuiz[0]['QuizID']) . '" target="_blank">Show quiz</a></div>'
                    : '';

                $nestedData[] = $isOnlineExam . $isUseQuiz;
            }


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

    public function getScheduleExamWaitingApproval()
    {
        $requestData = $_REQUEST;

        $token = $this->input->get('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        $whereP = (isset($data_arr['ProdiID']) && $data_arr['ProdiID'] != '' && $data_arr['ProdiID'] != null)
            ? ' ex.SemesterID = "' . $data_arr['SemesterID'] . '" AND ex.Type LIKE "' . $data_arr['Type'] . '"
            AND ex.Status = "0" AND ex.InsertByProdiID = "' . $data_arr['ProdiID'] . '" '
            : ' ex.SemesterID = "' . $data_arr['SemesterID'] . '" AND ex.Type LIKE "' . $data_arr['Type'] . '" AND ex.Status = "0" ';

        if (!empty($requestData['search']['value'])) {

            $search = $requestData['search']['value'];
            $sql = 'SELECT ex.ID, ex.ExamDate, ex.ExamStart, ex.ExamEnd, cl.Room , p1.Name AS P_Name1, p2.Name AS P_Name2,
                                em.Name AS NameInsertBy
                                FROM db_academic.exam ex
                                LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                LEFT JOIN db_academic.days d On (d.ID = ex.DayID)
                                LEFT JOIN db_employees.employees p1 ON (p1.NIP = ex.Pengawas1)
                                LEFT JOIN db_employees.employees p2 ON (p2.NIP = ex.Pengawas2)
                                LEFT JOIN db_employees.employees em ON (em.NIP = ex.InsertBy)
                                WHERE ( ' . $whereP . ' ) AND
                                 (d.NameEng LIKE "%' . $search . '%" OR cl.Room LIKE "%' . $search . '%"
                                 OR p1.Name LIKE "%' . $search . '%" OR p2.Name LIKE "%' . $search . '%"
                                 OR p1.NIP LIKE "%' . $search . '%" OR p2.NIP LIKE "%' . $search . '%"
                                 ) ORDER BY ex.ExamDate, ex.ExamStart, ex.ExamEnd ASC ';
        } else {

            $sql = 'SELECT ex.ID, ex.ExamDate, ex.ExamStart, ex.ExamEnd, cl.Room, p1.Name AS P_Name1, p2.Name AS P_Name2,
                                p1.NIP AS P_NIP1, p2.NIP AS P_NIP2, em.Name AS NameInsertBy
                                FROM db_academic.exam ex
                                LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                LEFT JOIN db_employees.employees p1 ON (p1.NIP = ex.Pengawas1)
                                LEFT JOIN db_employees.employees p2 ON (p2.NIP = ex.Pengawas2)
                                LEFT JOIN db_employees.employees em ON (em.NIP = ex.InsertBy)
                                WHERE ' . $whereP . ' ORDER BY ex.ExamDate, ex.ExamStart, ex.ExamEnd ASC ';
        }

        $dataTable = $this->db->query($sql)->result_array();

        if (count($dataTable) > 0) {
            for ($i = 0; $i < count($dataTable); $i++) {
                $dataC = $this->db->query('SELECT exg.ID, exg.ScheduleID, s.ClassGroup, mk.NameEng AS CourseEng, mk.MKCode, em.Name AS Coordinator
                                                        FROM db_academic.exam_group exg
                                                        LEFT JOIN db_academic.schedule s ON (s.ID = exg.ScheduleID)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                        lEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        WHERE exg.ExamID = "' . $dataTable[$i]['ID'] . '"
                                                        GROUP BY exg.ScheduleID ORDER BY s.ClassGroup ASC')->result_array();

                if (count($dataC) > 0) {
                    for ($s = 0; $s < count($dataC); $s++) {
                        $dataSt = $this->db->query('SELECT NPM,DB_Students FROM db_academic.exam_details exd
                                                              WHERE exd.ExamID = "' . $dataTable[$i]['ID'] . '"
                                                               AND exd.ExamGroupID = "' . $dataC[$s]['ID'] . '"
                                                               AND exd.ScheduleID = "' . $dataC[$s]['ScheduleID'] . '"
                                                                ')->result_array();

                        // Details Students
                        if (count($dataSt) > 0) {
                            for ($ss = 0; $ss < count($dataSt); $ss++) {
                                $ddst = $this->db->select('Name')->get_where(
                                    $dataSt[$ss]['DB_Students'] . '.students',
                                    array('NPM' => $dataSt[$ss]['NPM']),
                                    1
                                )->result_array();
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
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $course = '';
            $totalStudent = 0;
            for ($c = 0; $c < count($row['Course']); $c++) {
                $d = $row['Course'][$c];
                $totalStudent = $totalStudent + count($d['DetailsStudent']);
                $course = $course . ' <div style="margin-top: 1px;"><b>' . $d['ClassGroup'] . ' | ' . $d['CourseEng'] . '</b><br/><p style="color: #2196F3;font-size: 11px;">(Co) ' . $d['Coordinator'] . '</p></div> ';
            }


            $exam_date = date("D, d M Y", strtotime($row['ExamDate']));
            $exam_time = substr($row['ExamStart'], 0, 5) . ' - ' . substr($row['ExamEnd'], 0, 5);
            $exam_room = $row['Room'];


            //            <li><a class="btnSave2PDF_Exam" href="javascript:void(0);" data-url="save2pdf/attendance-list" data-token="'.$tkn_attendance_std.'">Exam Attendance</a></li>
            //                    <li><a target="_blank" href="'.base_url('save2pdf/news-event').'">Berita Acara</a></li>
            $act = '<div  style="text-align:center;"><div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="' . base_url('academic/exam-schedule/edit-exam-schedule/' . $row['ID']) . '">Approved</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a class="btnDeleteExam" data-id="' . $row['ID'] . '" href="javascript:void(0);" style="color: red;">Delete</a></li>
                  </ul>
                </div>
                </div>';


            $nestedData[] = $course;
            $nestedData[] = '<div style="text-align:center;">' . $totalStudent . '</div>';
            $nestedData[] = $act;
            $nestedData[] = '<div  style="text-align:center;">' . $exam_date . '<br/>' . $exam_time . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $exam_room . '</div>';
            $nestedData[] = $row['NameInsertBy'];

            $data[] = $nestedData;
        }


        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($query)),
            "recordsFiltered" => intval(count($query)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getScheduleExamLecturer()
    {
        $requestData = $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        $status = ($data_arr['Status'] != '') ? ' AND ex.Status = "' . $data_arr['Status'] . '" ' : '';
        $whereP = ' ex.SemesterID = "' . $data_arr['SemesterID'] . '"
                        AND ex.Type = "' . strtolower($data_arr['Type']) . '"
                        AND ex.InsertByProdiID = "' . $data_arr['ProdiID'] . '"
                         ' . $status;

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( em1.Name LIKE "%' . $search . '%" OR em2.Name LIKE "%' . $search . '%" OR
                              em3.Name LIKE "%' . $search . '%" OR cl.Room LIKE "%' . $search . '%" ) ';
        }

        $queryDefault = 'SELECT ex.*, cl.Room, em1.Name AS P_Name1, em2.Name AS P_Name2, em3.Name AS Name_InsertBy
                              FROM db_academic.exam ex
                              LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                              LEFT JOIN db_employees.employees em1 ON (em1.NIP = ex.Pengawas1)
                              LEFT JOIN db_employees.employees em2 ON (em2.NIP = ex.Pengawas2)
                              LEFT JOIN db_employees.employees em3 ON (em3.NIP = ex.InsertBy)
                              WHERE ( ' . $whereP . ' ) ' . $dataSearch . ' ';

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            // Get Course
            $dataC = $this->db->query('SELECT exg.*, mk.NameEng AS CourseEng, mk.MKCode, s.ClassGroup, em.Name AS Lecturer
                                                    FROM db_academic.exam_group exg
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = exg.ScheduleID)
                                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                    WHERE exg.ExamID = "' . $row['ID'] . '"
                                                     GROUP BY s.ID ORDER BY s.ClassGroup ASC ')->result_array();

            $viewCourse = '';
            $totalStudent = 0;
            if (count($dataC) > 0) {
                for ($s = 0; $s < count($dataC); $s++) {
                    $br = ($s != 0) ? '' : '';
                    $viewCourse = $viewCourse . '' . $br . '' . $dataC[$s]['MKCode'] . ' - ' . $dataC[$s]['CourseEng'] .
                        '<br/><p style="font-size:12px;color: #009688;">Group : <b>' . $dataC[$s]['ClassGroup'] .
                        '</b> | <i class="fa fa-user margin-right"></i> ' . $dataC[$s]['Lecturer'] . '</p>';

                    // Get Students
                    $dataStd = $this->db->query('SELECT * FROM db_academic.exam_details exd
                                                  WHERE exd.ExamID = "' . $row['ID'] . '"
                                                   AND exd.ExamGroupID = "' . $dataC[$s]['ID'] . '"
                                                   AND exd.ScheduleID = "' . $dataC[$s]['ScheduleID'] . '" ')
                        ->result_array();
                    $dataC[$s]['DetailStudent'] = $dataStd;
                    $totalStudent = $totalStudent + count($dataStd);
                }
            }




            $time = substr($row['ExamStart'], 0, 5) . ' - ' . substr($row['ExamEnd'], 0, 5);

            $status = ($row['Status'] == '0' || $row['Status'] == 0)
                ? '<i class="fa fa-circle" style="color:#d8d8d8;"></i>'
                : '<i class="fa fa-check-circle" style="color: #369c3a;"></i>';

            $p1 = ($row['P_Name1'] != '' && $row['P_Name1'] != null) ? $row['P_Name1'] : '';
            $p2 = ($row['P_Name2'] != '' && $row['P_Name2'] != null) ? $row['P_Name2'] : '';
            $invigilator = ($p2 != '') ? '- ' . $p1 . '<br/>- ' . $p2 : '- ' . $p1;

            $tokenID = $this->jwt->encode(array(
                'ExamID' => $row['ID']
            ), 'UAP)(*');
            $act = '-';

            //
            if ($row['Status'] == '0' || $row['Status'] == 0) {
                $act = '<div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="' . url_sign_in_lecturers . 'exam/edit-schedule-exam/' . $tokenID . '">Edit</a></li>
                    <li><a class="btnDeleteExamFromLecturer" data-id="' . $row['ID'] . '" href="javascript:void(0);" style="color: red;">Delete</a></li>
                  </ul>
                </div>';
            }

            $nestedData[] = $viewCourse;
            $nestedData[] = $invigilator;
            $nestedData[] = '<div  style="text-align:center;">' . $totalStudent . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $act . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . date('l, d M Y', strtotime($row['ExamDate'])) . '<br/>' . $time . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $row['Room'] . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $status . '</div>';
            $nestedData[] = '<p style="font-size: 12px;"><i class="fa fa-user margin-right"></i><b>' . $row['Name_InsertBy'] .
                '</b><br/>' . date('l, d M Y h:s:i', strtotime($row['InsertAt'])) . '</p>';

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

    public function checkSchedule()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0 && $data_arr['action'] == 'check') {
            $dataFilter = (array) $data_arr['formData'];
            $data = $this->m_api->__checkSchedule($dataFilter);

            return print_r(json_encode($data));
        }
    }

    public function crudProgramCampus()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->m_api->getProgramCampus();
                return print_r(json_encode($data));
            }
        }
    }

    public function crudSemester()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->m_api->getSemester($data_arr['order']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readAntara') {
                $data = $this->m_api->getSemesterAntara($data_arr['order']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'ReadSemesterActive') {
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->getSemesterActive(
                    $formData['SemesterID'],
                    $formData['CurriculumID'],
                    $formData['ProdiID'],
                    $formData['Semester'],
                    $formData['IsSemesterAntara']
                );
                return print_r(json_encode($data));
            }
        }
    }

    public function crudCourseOfferings()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];
                $this->db->insert('db_academic.course_offerings', $formData);
                $insert_id = $this->db->insert_id();
                return print_r($insert_id);
            } else if ($data_arr['action'] == 'edit') {
                $formData = (array) $data_arr['formData'];

                $this->db->where('ID', $data_arr['OfferID']);
                $this->db->update('db_academic.course_offerings', $formData);

                return print_r($data_arr['OfferID']);
            } else if ($data_arr['action'] == 'read') {
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->getAllCourseOfferings(
                    $formData['SemesterID'],
                    $formData['CurriculumID'],
                    $formData['ProdiID'],
                    $formData['Semester'],
                    $formData['IsSemesterAntara']
                );
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readgabungan') {
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->getAllCourseOfferingsMKU($formData['SemesterID']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'editSemester') {
                //                $formData = (array) $data_arr['formData'];
                $this->db->set('ToSemester', $data_arr['ToSemester']);
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.course_offerings');

                return print_r(1);
            }
            // Untuk mengecek apakah MK Offering sudah dibuatkan jadwal atau belum
            else if ($data_arr['action'] == 'checkCourse') {
                $dataWhere = (array) $data_arr['dataWhere'];
                //                $query = $this->db
                //                    ->get_where('db_academic.schedule', $dataWhere)
                //                    ->result_array();

                $query = $this->m_api->__checkCourse($dataWhere['SemesterID'], $dataWhere['MKID']);

                if (count($query) > 0) {
                    return print_r(0);
                } else {
                    return print_r(1);
                }
            } else if ($data_arr['action'] == 'delete') {

                $query = $this->db->get_where('db_academic.course_offerings', array('ID' => $data_arr['OfferID']), 1)->result_array();

                if (count($query) > 0) {
                    $Arr_CDID = json_decode($query[0]['Arr_CDID']);

                    //                    print_r($Arr_CDID);
                    //
                    //                    exit;

                    if (count($Arr_CDID) > 1) {
                        $result = [];
                        if (($key = array_search($data_arr['CDID'], $Arr_CDID)) !== false) {
                            for ($a = 0; $a < count($Arr_CDID); $a++) {
                                if ($a != $key) {
                                    array_push($result, $Arr_CDID[$a]);
                                }
                            }
                        }

                        $this->db->set('Arr_CDID', json_encode($result));
                        $this->db->where('ID', $data_arr['OfferID']);
                        $this->db->update('db_academic.course_offerings');

                        return print_r(1);
                    } else if (count($Arr_CDID) == 1) {
                        $this->db->where('ID', $data_arr['OfferID']);
                        $this->db->delete('db_academic.course_offerings');
                        return print_r(1);
                    }


                    //                    print_r(json_encode($r));


                }
            } else if ($data_arr['action'] == 'readToSchedule') {
                $formData = (array) $data_arr['formData'];

                $data = $this->m_api->getOfferingsToSetSchedule($formData);
                return print_r(json_encode($data));
            }
        }
    }


    public function getAllStudents()
    {

        $data = $this->m_api->__getTahunAngkatan();

        return print_r(json_encode($data));
    }

    public function crudeStudent()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $formData = (array) $data_arr['formData'];
                $data = $this->m_api->__getStudentByNPM($formData['ta'], $formData['NPM']);

                return print_r(json_encode($data));
            }
        }
    }


    public function getClassGroup()
    {

        $data_arr = $this->getInputToken();

        $data = $this->m_api->__checkClassGroup(
            $data_arr['ProgramsCampusID'],
            $data_arr['SemesterID'],
            $data_arr['ProdiCode'],
            $data_arr['IsSemesterAntara']
        );

        $result = array(
            'Group' => $data_arr['ProdiCode'] . '-' . (count($data) + 1)
        );

        return print_r(json_encode($result));
    }

    public function getClassGroupParalel()
    {
        $data_arr = $this->getInputToken();
        $data = $this->m_api->__checkClassGroupParalel(
            $data_arr['ProgramsCampusID'],
            $data_arr['SemesterID'],
            $data_arr['ProdiCode'],
            $data_arr['IsSemesterAntara']
        );

        return print_r(json_encode($data));;
    }

    public function crudClassroom()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllClassRoom();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];

                // Cek Apakah ruangan sudah di input
                $this->db->where('Room', $formData['Room']);
                $room = $this->db->get('db_academic.classroom')->result_array();


                if (count($room) > 0) {
                    $result = array(
                        'inserID' => 0
                    );
                } else {
                    $this->db->insert('db_academic.classroom', $formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'edit') {
                $formData = (array) $data_arr['formData'];

                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.classroom', $formData);
                $result = array(
                    'inserID' => $ID
                );

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'delete') {
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

        if (!$this->upload->do_upload('fileData')) {
            return $error = $this->upload->display_errors();
            //$this->load->view('upload_form', $error);
        } else {
            return $data =  $this->upload->data();
            //$this->load->view('upload_success', $data);
        }
    }

    public function crudClassroomVreservation()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllClassRoomCategory();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];

                // Cek Apakah ruangan sudah di input
                $this->db->where('Room', $formData['Room']);
                $room = $this->db->get('db_academic.classroom')->result_array();


                if (count($room) > 0) {
                    $result = array(
                        'inserID' => 0
                    );
                } else {

                    $filename = NULL;
                    if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                        if (array_key_exists('fileData', $_FILES)) {
                            // Upload file to NAS
                            $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                            $path = 'vreservation';
                            $FileName = md5(mt_rand());
                            $TheFile = 'fileData';
                            $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin, $FileName, $TheFile, $path, 'string');
                            $filename = $uploadNas;
                        }
                    } else {

                        $uploadFile = $this->uploadFfile(mt_rand());
                        if (is_array($uploadFile)) {
                            $filename = $uploadFile['file_name'];
                        }
                    }

                    $file = array('Layout' => $filename);
                    $formData = $formData + $file;
                    $this->db->insert('db_academic.classroom', $formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'edit') {
                $formData = (array) $data_arr['formData'];
                $ID = $data_arr['ID'];
                // get data
                $G_data = $this->m_master->caribasedprimary('db_academic.classroom', 'ID', $ID);
                $filename = NULL;
                if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                    if (array_key_exists('fileData', $_FILES)) {
                        // delete data
                        $TheFileSelected = $G_data[0]['Layout'];
                        $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                        $path_delete = ($_SERVER['SERVER_NAME'] == 'localhost') ? "localhost/vreservation/" . $TheFileSelected : "pcam/vreservation/" . $TheFileSelected;
                        $this->m_master->DeleteFileToNas($headerOrigin, $path_delete);

                        // Upload file to NAS
                        $path = 'vreservation';
                        $FileName = md5(mt_rand());
                        $TheFile = 'fileData';
                        $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin, $FileName, $TheFile, $path, 'string');
                        $filename = $uploadNas;
                    }
                } else {
                    $path = FCPATH . 'uploads/vreservation/' . $G_data[0]['Layout'];
                    if (file_exists($path)) {
                        unlink($path);
                    }

                    $uploadFile = $this->uploadFfile(mt_rand());
                    $filename = '';
                    if (is_array($uploadFile)) {
                        $filename = $uploadFile['file_name'];
                    }
                }

                $file = array('Layout' => $filename);
                $formData = $formData + $file;

                $this->db->where('ID', $ID);
                $this->db->update('db_academic.classroom', $formData);
                $result = array(
                    'inserID' => $ID
                );

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'delete') {
                $ID = $data_arr['ID'];
                $G_data = $this->m_master->caribasedprimary('db_academic.classroom', 'ID', $ID);

                // delete file too
                if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                    // delete data
                    $TheFileSelected = $G_data[0]['Layout'];
                    $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                    $path_delete = ($_SERVER['SERVER_NAME'] == 'localhost') ? "localhost/vreservation/" . $TheFileSelected : "pcam/vreservation/" . $TheFileSelected;
                    $this->m_master->DeleteFileToNas($headerOrigin, $path_delete);
                } else {
                    $path = FCPATH . 'uploads/vreservation/' . $G_data[0]['Layout'];
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }

                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.classroom');
                return print_r($ID);
            }
        }
    }

    public function crudGrade()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllGrade();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];
                // Cek grade
                $this->db->where('Grade', $formData['Grade']);
                $grade = $this->db->get('db_academic.grade')->result_array();

                if (count($grade) > 0) {
                    $result = array(
                        'inserID' => 0
                    );
                } else {
                    $this->db->insert('db_academic.grade', $formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'edit') {
                $formData = (array) $data_arr['formData'];
                // Cek grade
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.grade', $formData);
                $result = array(
                    'inserID' => $ID
                );

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'delete') {
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.grade');
                return print_r($ID);
            }
        }
    }

    public function crudRangeCredits()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->m_api->__getRangeCredits();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'delete') {
                //                print_r($data_arr);
                //                exit;
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.range_credits');
                return print_r(1);
            } else if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];
                $this->db->insert('db_academic.range_credits', $formData);
                $insert_id = $this->db->insert_id();
                return print_r($insert_id);
            } else if ($data_arr['action'] == 'edit') {
                $ID = $data_arr['ID'];
                $formData = (array) $data_arr['formData'];
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.range_credits', $formData);

                return print_r($ID);
            }
        }
    }

    public function crudTimePerCredit()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->m_api->__getAllTimePerCredit();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];
                // Cek Time
                $this->db->where('Time', $formData['Time']);
                $time = $this->db->get('db_academic.time_per_credits')->result_array();

                if (count($time) > 0) {
                    $result = array(
                        'inserID' => 0
                    );
                } else {
                    $this->db->insert('db_academic.time_per_credits', $formData);
                    $insert_id = $this->db->insert_id();
                    $result = array(
                        'inserID' => $insert_id
                    );
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'delete') {
                $time = $this->db->get('db_academic.time_per_credits')->result_array();

                if (count($time) > 1) {
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

    public function crudLecturer()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $NIP = $data_arr['NIP'];
                $data = $this->m_api->__getLecturerDetail($NIP);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'getLecturerStatus') {

                $data = $this->db->query('SELECT * FROM db_employees.employees_status WHERE Type = "lec" OR Type = "both" ORDER BY IDStatus DESC ')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readMini') {
                $NIP = $data_arr['NIP'];
                $data = $this->db->select('NIP,NIDN,Name,TitleAhead,TitleBehind,PositionMain,Phone,
                                        HP,Email,EmailPU,Password,Address,Photo,Photo_new')
                    ->get_where('db_employees.employees', array('NIP' => $NIP), 1)
                    ->result_array();


                if (count($data) > 0) {
                    $sp = explode('.', $data[0]['PositionMain']);
                    $DiviosionID = $sp[0];
                    $PositionID = $sp[1];

                    $div = $this->db->get_where('db_employees.division', array('ID' => $DiviosionID), 1)->result_array();
                    $data[0]['Division'] = $div[0]['Division'];

                    $pos = $this->db->get_where('db_employees.position', array('ID' => $PositionID), 1)->result_array();
                    $data[0]['Position'] = $pos[0]['Position'];

                    return print_r(json_encode($data[0]));
                } else {
                    return print_r(json_encode($data));
                }
            }
        }
    }

    //add bismar
    public function crudAcademic()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $NIP = $data_arr['NIP'];
                $data = $this->m_api->__getLecturerDetail($NIP);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readMini') {
                $NIP = $data_arr['NIP'];
                $data = $this->db->select('NIP,NIDN,Name,TitleAhead,TitleBehind,PositionMain,Phone,
                                        HP,Email,EmailPU,Password,Address,Photo,Photo_new')
                    ->get_where('db_employees.employees', array('NIP' => $NIP), 1)
                    ->result_array();


                if (count($data) > 0) {
                    $sp = explode('.', $data[0]['PositionMain']);
                    $DiviosionID = $sp[0];
                    $PositionID = $sp[1];

                    $div = $this->db->get_where('db_employees.division', array('ID' => $DiviosionID), 1)->result_array();
                    $data[0]['Division'] = $div[0]['Division'];

                    $pos = $this->db->get_where('db_employees.position', array('ID' => $PositionID), 1)->result_array();
                    $data[0]['Position'] = $pos[0]['Position'];

                    return print_r(json_encode($data[0]));
                } else {
                    return print_r(json_encode($data));
                }
            }
        }
    }

    public function review_academicdetail()
    {
        $NIP = $this->input->get('NIP');
        $viewacademic = $this->m_api->views_academic($NIP);
        echo json_encode($viewacademic);
    }

    public function review_otherfile()
    {
        //$NIP = $this->session->userdata('NIP');
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {

            if ($data_arr['action'] == 'readlist_otherfile') {

                $NIP = $data_arr['NIP'];
                $requestData = $_REQUEST;

                $dataSearch = '';
                if (!empty($requestData['search']['value'])) {
                    $search = $requestData['search']['value'];
                    $dataSearch = 'AND m.NameFiles LIKE "%' . $search . '%" ORDER BY ID DESC';
                }

                $queryDefault = 'SELECT ea.*, m.NameFiles
                FROM db_employees.files AS ea
                LEFT JOIN db_employees.master_files AS m ON (ea.TypeFiles = m.ID)
                WHERE ea.NIP = "' . $NIP . '" AND m.Type = "1" AND ea.LinkFiles IS NOT NULL ' . $dataSearch;

                $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

                $query = $this->db->query($sql)->result_array();
                $queryDefaultRow = $this->db->query($queryDefault)->result_array();

                $no = $requestData['start'] + 1;
                $data = array();

                for ($i = 0; $i < count($query); $i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $btnAction = '<button type="button" class="btn btn-sm btn-primary btn-circle btnviewlistsrata" data-toggle="tooltip" data-placement="top" title="Review Files" filesub="' . $row['LinkFiles'] . '"><i class="fa fa-eye"></i></button>
                        <button class="btn btn-sm btn-success btn-circle testEditdocument" data-toggle="tooltip" data-placement="top" title="Edit File" filesnametype="' . $row['NameFiles'] . '" idtypex="' . $row['TypeFiles'] . '" idfiles="' . $row['ID'] . '" linkfileother="' . $row['LinkFiles'] . '" namedoc ="' . $row['No_Document'] . '"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm btn-circle btn-danger btndelotherfile" data-toggle="tooltip" data-placement="top" title="Delete File" Idotherfile="' . $row['ID'] . '"><i class="fa fa-trash"></i></button> ';

                    if ($row['No_Document'] == null) {
                        $nodoc = '<center> - </center>';
                    } else {
                        $nodoc = $row['No_Document'];
                    }

                    if ($row['Date_Files'] == null) {
                        $datadate = '<center> - </center>';
                    } else {
                        $datadate = date('d M Y', strtotime($row['Date_Files']));
                    }

                    if ($row['Description_Files'] == null) {
                        $datadesc = '<center> - </center>';
                    } else {
                        $datadesc = $row['Description_Files'];
                    }

                    $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                    $nestedData[] = '<div style="text-align:left;">' . $row['NameFiles'] . ' </div>';
                    $nestedData[] = '<div style="text-align:left;">' . $nodoc . ' </div>';
                    $nestedData[] = '<div style="text-align:center;">' . $datadate . ' </div>';
                    $nestedData[] = '<div style="text-align:left;">' . $datadesc . ' </div>';
                    $nestedData[] = '<div style="text-align:center;">' . $btnAction . '</div>';
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
            } else if ($data_arr['action'] == 'readlist_university') {

                $requestData = $_REQUEST;

                $dataSearch = '';
                if (!empty($requestData['search']['value'])) {
                    $search = $requestData['search']['value'];
                    $dataSearch = 'WHERE Name_University LIKE "%' . $search . '%" ORDER BY ID DESC';
                }

                $queryDefault = 'SELECT * FROM db_research.university ' . $dataSearch;

                $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

                $query = $this->db->query($sql)->result_array();
                $queryDefaultRow = $this->db->query($queryDefault)->result_array();

                $no = $requestData['start'] + 1;
                $data = array();

                for ($i = 0; $i < count($query); $i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $btnAction = '<button class="btn btn-sm btn-circle btn-danger btndelotherfile" data-toggle="tooltip" data-placement="top" title="Delete File" Idotherfile="' . $row['ID'] . '" typedata="university" disabled ><i class="fa fa-trash"></i></button> ';

                    $nestedData[] = '<div style="text-align:center; color: #000000;">' . $no . '</div>';
                    $nestedData[] = '<div style="text-align:left; color: #000000;">' . $row['Name_University'] . '</div>';
                    $nestedData[] = '<div style="text-align:center;">' . $btnAction . '</div>';
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
            } else if ($data_arr['action'] == 'readlist_major') {
                $requestData = $_REQUEST;

                $dataSearch = '';
                if (!empty($requestData['search']['value'])) {
                    $search = $requestData['search']['value'];
                    $dataSearch = 'WHERE Name_MajorProgramstudy LIKE "%' . $search . '%" ORDER BY ID DESC';
                }

                $queryDefault = 'SELECT * FROM db_employees.major_programstudy_employees ' . $dataSearch;

                $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

                //print_r($sql); exit();

                $query = $this->db->query($sql)->result_array();
                $queryDefaultRow = $this->db->query($queryDefault)->result_array();

                $no = $requestData['start'] + 1;
                $data = array();

                for ($i = 0; $i < count($query); $i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $btnAction = '<button class="btn btn-sm btn-circle btn-danger btndelotherfile" data-toggle="tooltip" data-placement="top" title="Delete File" Idotherfile="' . $row['ID'] . '" typedata="major" disabled><i class="fa fa-trash"></i></button>';

                    $nestedData[] = '<div style="text-align:center; color: #000000;">' . $no . '</div>';
                    $nestedData[] = '<div style="text-align:left; color: #000000;">' . $row['Name_MajorProgramstudy'] . '</div>';
                    $nestedData[] = '<div style="text-align:center;">' . $btnAction . '</div>';
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
            } else if ($data_arr['action'] == 'readlist_katotherfiles') {
                $requestData = $_REQUEST;

                $dataSearch = '';
                if (!empty($requestData['search']['value'])) {
                    $search = $requestData['search']['value'];
                    $dataSearch = 'WHERE TypeFiles LIKE "%' . $search . '%" OR NameFiles LIKE "%' . $search . '%" ORDER BY ID DESC';
                }

                $queryDefault = 'SELECT * FROM db_employees.master_files ' . $dataSearch;
                $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

                $query = $this->db->query($sql)->result_array();
                $queryDefaultRow = $this->db->query($queryDefault)->result_array();

                $no = $requestData['start'] + 1;
                $data = array();

                for ($i = 0; $i < count($query); $i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    if ($row['ID'] == "13") {
                        $btnAction = '<button class="btn btn-sm btn-circle btn-danger" data-toggle="tooltip" data-placement="top" title="Delete File" disabled><i class="fa fa-trash"></i></button> ';
                    } else {
                        $btnAction = '<button class="btn btn-sm btn-circle btn-danger btndelotherfile" data-toggle="tooltip" data-placement="top" title="Delete File" Idotherfile="' . $row['ID'] . '" TypeFiles="' . $row['ID'] . '" typedata="otherfile" disabled><i class="fa fa-trash"></i></button> ';
                    }

                    $nestedData[] = '<div style="text-align:center; color: #000000;">' . $no . '</div>';
                    $nestedData[] = '<div style="text-align:left; color: #000000;">' . $row['TypeFiles'] . '</div>';
                    $nestedData[] = '<div style="text-align:left; color: #000000;">' . $row['NameFiles'] . '</div>';
                    $nestedData[] = '<div style="text-align:center;">' . $btnAction . '</div>';
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
            } else if ($data_arr['action'] == 'get_katotherfiles') {

                $data = $this->db->query('SELECT ID, NameFiles
                        FROM db_employees.master_files
                        WHERE Type = "1" AND ID NOT IN ("13") ')->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'get_typefiles_rektorat') {

                $data = $this->db->query('SELECT ID, NameFiles FROM db_employees.master_files WHERE ID <= 13')->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'get_detailfiles_rektorat') {

                $id_files = $this->input->get('s');

                $data = $this->db->query('SELECT f.*, mf.TypeFiles AS M_TypeFiles,  p.Name_other_files, xx.Name, xy.Name_University, aa.Name_MajorProgramstudy, ab.Name_MajorProgramstudy AS NamaProgramStudy
                                         FROM db_employees.files f
                                         LEFT JOIN db_employees.master_files mf ON (mf.ID = f.TypeFiles)
                                         LEFT JOIN db_employees.master_other_files AS p ON (f.ID_OtherFiles = p.ID)
                                         LEFT JOIN db_employees.employees AS xx ON (f.NIP = xx.NIP)
                                         LEFT JOIN db_research.university AS xy ON (f.NameUniversity = xy.ID)
                                         LEFT JOIN db_employees.major_programstudy_employees AS aa ON (f.Major = aa.ID)
                                         LEFT JOIN db_employees.major_programstudy_employees AS ab ON(f.ProgramStudy = ab.ID)
                                         WHERE f.ID = "' . $id_files . '" ')->result_array();
                return print_r(json_encode($data));
                // echo json_encode($details);
            } else if ($data_arr['action'] == 'deleteother_master') {

                $id_otherfile = $data_arr['id_otherfile'];
                //$TypeFiles = $data_arr['TypeFiles'];
                $typedata = $data_arr['typedata'];

                if ($typedata == "university") {

                    $dataAttUniv = $this->db->query('SELECT * FROM db_employees.files WHERE NameUniversity = "' . $id_otherfile . '" ')->result_array();

                    if (count($dataAttUniv) > 0) {
                        return print_r(0);
                    } else {
                        $this->db->where('ID', $id_otherfile);
                        $this->db->delete('db_research.university');
                        $this->db->reset_query();
                    }
                } else if ($typedata == "major") {

                    $dataAttMajor = $this->db->query('SELECT * FROM db_employees.files WHERE Major = "' . $id_otherfile . '" OR ProgramStudy = "' . $id_otherfile . '" ')->result_array();

                    if (count($dataAttMajor) > 0) {
                        return print_r(0);
                    } else {
                        $this->db->where('ID', $id_otherfile);
                        $this->db->delete('db_employees.major_programstudy_employees');
                        $this->db->reset_query();
                    }
                } else {

                    $dataAttdS = $this->db->query('SELECT * FROM db_employees.files WHERE TypeFiles = "' . $id_otherfile . '" ')->result_array();

                    if (count($dataAttdS) > 0) {
                        return print_r(0);
                    } else {
                        $this->db->where('ID', $id_otherfile);
                        $this->db->delete('db_employees.master_files');
                        $this->db->reset_query();
                    }
                }
                return print_r(1);
            }
        }
    }

    public function review_academics1()
    {

        $NIP = $this->input->get('NIP');
        $srata = $this->input->get('s');
        $viewfiles1 = $this->m_api->views_files1($NIP, $srata);
        echo json_encode($viewfiles1);
    }


    public function getedit_datas1()
    {

        $academic = $this->input->get('s');

        if ($academic == 'S1') {
            $NIP = $this->input->get('n');
            $fileijazah = $this->input->get('j');
            $filetranscripts = $this->input->get('t');
            $nameuniv = $this->input->get('x');

            $viewfiles1 = $this->m_api->views_editacademic($NIP, $fileijazah, $filetranscripts, $nameuniv);
            echo json_encode($viewfiles1);
        } else if ($academic == 'S2') {
            $NIP = $this->input->get('n');
            $fileijazahs1 = $this->input->get('j');
            $filetranscripts1 = $this->input->get('t');
            $nameuniv = $this->input->get('x');

            $viewfiles1 = $this->m_api->views_editacademic($NIP, $fileijazahs1, $filetranscripts1, $nameuniv);
            echo json_encode($viewfiles1);
        } else if ($academic == 'S3') {
            $NIP = $this->input->get('n');
            $fileijazahs1 = $this->input->get('j');
            $filetranscripts1 = $this->input->get('t');
            $nameuniv = $this->input->get('x');

            $viewfiles1 = $this->m_api->views_editacademic($NIP, $fileijazahs1, $filetranscripts1, $nameuniv);
            echo json_encode($viewfiles1);
        } else if ($academic == 'OTF') {
            $NIP = $this->input->get('n');
            $IDfiles = $this->input->get('t');
            //$filetranscripts1 = $this->input->get('t');

            $viewfiles1 = $this->m_api->views_editotfiles($NIP, $IDfiles);
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
        $data_arr = (array) $this->jwt->decode($token, $key);

        $result = $this->m_api->__getSMAWilayah($data_arr['wilayah']);

        return print_r(json_encode($result));
    }

    public function getSchoolByCityID($CityID)
    {

        $sql = "select * from db_admission.school as a where a.CityID = ? and Approved = 1";
        $data = $this->db->query($sql, array($CityID))->result_array();

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['Contact'] = $this->db->query('SELECT c.*, em.Name AS CreatedBy_Name FROM db_admission.contact c
                                            LEFT JOIN db_employees.employees em ON (c.CreatedBy = em.NIP)
                                            WHERE c.SchoolID = "' . $data[$i]['ID'] . '" ')->result_array();
            }
        }

        return print_r(json_encode($data));
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

    public function getClassGroupAutoComplete($SemesterID)
    {
        $term = $this->input->get('term');
        $data = $this->db->query('SELECT ID,ClassGroup FROM db_academic.schedule
                                      WHERE SemesterID = "' . $SemesterID . '" AND ClassGroup LIKE "%' . $term . '%" LIMIT 7 ')->result_array();

        $dataRes = [];
        for ($i = 0; $i < count($data); $i++) {
            $arrp = array(
                'ID' => $data[$i]['ID'],
                'label' => $data[$i]['ClassGroup'],
                'value' => $data[$i]['ClassGroup']
            );
            array_push($dataRes, $arrp);
        }

        return print_r(json_encode($dataRes));
    }

    public function getScheduleIDByClassGroup($SemesterID, $Group)
    {
        $data = $this->db->query('SELECT ID FROM db_academic.schedule WHERE SemesterID = "' . $SemesterID . '" AND ClassGroup LIKE "' . $Group . '" LIMIT 1 ')->result_array();
        $res = (count($data) > 0) ? $data[0]['ID'] : 0;

        return print_r($res);
    }

    public function crudStudyPlanning()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'readAvailableKRS') {
                $SemesterID = $data_arr['SemesterID'];
                $NPM = $data_arr['NPM'];

                // Load Auth
                $dataAuth = $this->m_api->getDataAuthStudent($NPM);

                // Load Credit
                $dataCredit = $this->m_api->getMaxCredit('ta_' . $dataAuth[0]['Year'], $NPM, $dataAuth[0]['Year'], $SemesterID, $dataAuth[0]['ProdiID']);
                $dataAuth[0]['dataCredit'] = $dataCredit;

                $Semester = $this->m_api->getSemesterStudentByYear($SemesterID, $dataAuth[0]['Year']);

                $dataSc = $this->m_api->__getAvailabelCourse(
                    $SemesterID,
                    $NPM,
                    $dataAuth[0]['ProgramID'],
                    $dataAuth[0]['ProdiID'],
                    $Semester,
                    '0',
                    $dataAuth[0]['Year']
                );

                $result = array(
                    'Student' => $dataAuth[0],
                    'Course' => $dataSc
                );

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'readAvailableKRSOnBatalTambah') {
                $SemesterID = $data_arr['SemesterID'];
                $NPM = $data_arr['NPM'];

                // Load Auth
                $dataAuth = $this->m_api->getDataAuthStudent($NPM);

                // Load Credit
                $dataCredit = $this->m_api->getMaxCredit('ta_' . $dataAuth[0]['Year'], $NPM, $dataAuth[0]['Year'], $SemesterID, $dataAuth[0]['ProdiID']);
                $dataAuth[0]['dataCredit'] = $dataCredit;

                $Semester = $this->m_api->getSemesterStudentByYear($SemesterID, $dataAuth[0]['Year']);

                $dataSc = $this->m_api->__getAvailabelCourse(
                    $SemesterID,
                    $NPM,
                    $dataAuth[0]['ProgramID'],
                    $dataAuth[0]['ProdiID'],
                    $Semester,
                    '0',
                    $dataAuth[0]['Year']
                );

                $result = array(
                    'Student' => $dataAuth[0],
                    'Course' => $dataSc
                );

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'chekSeat') {
                $dataToken = $data_arr;
                $dataWhere = (array) $dataToken['dataWhere'];
                $querySeat = $this->db->get_where('db_academic.std_krs', $dataWhere)->result_array();
                $countStudent = count($querySeat);

                // Get room
                $data = $this->db->query('SELECT cl.Room, cl.Seat FROM db_academic.schedule_details sd
                                                LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                WHERE sd.ScheduleID = "' . $dataWhere['ScheduleID'] . '" ')->result_array();

                $result = array('Status' => 1);
                if (count($data) > 0) {
                    foreach ($data as $item) {
                        if ($item['Seat'] <= $countStudent) {
                            $result = array('Status' => 0);
                        }
                    }
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'addAvailabelCourse') {

                $DBStudent = $data_arr['DBStudent'];
                $dataInsert = (array) $data_arr['dataInsert'];

                // Cek apakah sudah di insert atau belum, jika sudah langsung return 1
                // Cek apakah schedule sudah ada atau belum jika ada maka cukup return saja
                $dataWhere = array(
                    'SemesterID' => $dataInsert['SemesterID'],
                    'NPM' => $dataInsert['NPM'],
                    'ScheduleID' => $dataInsert['ScheduleID'],
                    'CDID' => $dataInsert['CDID'],
                );

                $dataCheck = $this->db->get_where('db_academic.std_krs', $dataWhere)->result_array();
                if (count($dataCheck) <= 0) {

                    // Insert ke std krs
                    $this->db->insert('db_academic.std_krs', $dataInsert);

                    // Get Attendance Attendance
                    $dataAttd = $this->db->get_where(
                        'db_academic.attendance',
                        array(
                            'SemesterID' => $dataInsert['SemesterID'],
                            'ScheduleID' => $dataInsert['ScheduleID']
                        )
                    )->result_array();

                    // Insert Ke Attendance
                    foreach ($dataAttd as $itemA) {
                        $dataAins = array(
                            'ID_Attd' => $itemA['ID'],
                            'NPM' => $dataInsert['NPM']
                        );
                        $this->db->insert('db_academic.attendance_students', $dataAins);
                    }

                    // - get MKID
                    $dataC = $this->db->select('MKID,TotalSKS')->get_where(
                        'db_academic.curriculum_details',
                        array('ID' => $dataInsert['CDID']),
                        1
                    )->result_array();

                    $dataUpdateKRS = array(
                        'SemesterID' => $dataInsert['SemesterID'],
                        'MhswID' => $data_arr['MhswID'],
                        'NPM' => $dataInsert['NPM'],
                        'ScheduleID' => $dataInsert['ScheduleID'],
                        'TypeSchedule' => $dataInsert['TypeSP'],
                        'CDID' => $dataInsert['CDID'],
                        'MKID' => $dataC[0]['MKID'],
                        'Credit' => $dataC[0]['TotalSKS'],
                        'Approval' => '0',
                        'StatusSystem' => '1',
                        'Status' => '1'
                    );

                    $this->db->insert($DBStudent . '.study_planning', $dataUpdateKRS);
                    $SPID = $this->db->insert_id();


                    $dataUpdateKRS_std = array(
                        'SPID' => $SPID,
                        'ClassOf' => trim(explode('_', $DBStudent)[1]),
                        'SemesterID' => $dataInsert['SemesterID'],
                        'NPM' => $dataInsert['NPM'],
                        'ScheduleID' => $dataInsert['ScheduleID'],
                        'TypeSchedule' => $dataInsert['TypeSP'],
                        'CDID' => $dataInsert['CDID'],
                        'MKID' => $dataC[0]['MKID'],
                        'Credit' => $dataC[0]['TotalSKS'],
                        'EntredBy' => $this->session->userdata('NIP')
                    );

                    $this->db->insert('db_academic.std_study_planning', $dataUpdateKRS_std);
                }
                return print_r(1);
            } else if ($data_arr['action'] == 'addAvailabelInBatalTambah') {

                $dataInsert = (array) $data_arr['dataInsert'];
                $this->db->insert('db_academic.std_krs_batal_tambah', $dataInsert);

                return print_r(1);
            } else if ($data_arr['action'] == 'ApprovedByMentorAll') {

                $ArrToApproveAll = (array) $data_arr['ArrToApproveAll'];
                $ArrToApproveAll_Logging = (array) $data_arr['ArrToApproveAll_Logging'];



                if (count($ArrToApproveAll) > 0) {
                    for ($i = 0; $i < count($ArrToApproveAll); $i++) {
                        $arrUpdate = array(
                            'Status' => '2',
                            'ApprovalPA_At' => $data_arr['ApprovalPA_At']
                        );
                        $this->db->where('ID', $ArrToApproveAll[$i]);
                        $this->db->update('db_academic.std_krs', $arrUpdate);

                        // Insert Logging
                        $Log_dataInsert = (array) $ArrToApproveAll_Logging[$i];

                        $this->db->insert('db_notifikasi.logging', $Log_dataInsert);
                        $insert_id = $this->db->insert_id();

                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id,
                            'UserID' => $data_arr['NPM']
                        );
                        $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins);

                        // Cek Kaprodi
                        $dataKaprodi = $this->db->query('SELECT ps.KaprodiID FROM db_academic.auth_students auts
                                                                    LEFT JOIN db_academic.program_study ps
                                                                    ON (ps.ID = auts.ProdiID)
                                                                    WHERE auts.NPM = "' . $data_arr['NPM'] . '" ')->result_array();

                        if (count($dataKaprodi) > 0) {
                            if ($dataKaprodi[0]['KaprodiID'] != $data_arr['ApprovedBy']) {
                                $Log_arr_ins_kaprodi = array(
                                    'IDLogging' => $insert_id,
                                    'UserID' => $dataKaprodi[0]['KaprodiID']
                                );
                                $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins_kaprodi);
                            }
                        }
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'ApprovedByMentor') {
                $arrUpdate = array(
                    'Status' => '2',
                    'ApprovalPA_At' => $data_arr['ApprovalPA_At']
                );
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.std_krs', $arrUpdate);

                // Insert Logging
                $Log_dataInsert = (array) $data_arr['Logging'];
                $this->db->insert('db_notifikasi.logging', $Log_dataInsert);
                $insert_id = $this->db->insert_id();

                $Log_arr_ins = array(
                    'IDLogging' => $insert_id,
                    'UserID' => $data_arr['NPM']
                );

                // Send Notif To Kaprodi

                $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins);

                // Cek Kaprodi
                $dataKaprodi = $this->db->select('KaprodiID')->get_where('db_academic.program_study', array('ID' => $data_arr['ProdiID']))->result_array();
                $Log_arr_ins_KA = array(
                    'IDLogging' => $insert_id,
                    'UserID' => $dataKaprodi[0]['KaprodiID']
                );
                $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins_KA);

                return print_r(1);
            } else if ($data_arr['action'] == 'ApprovedByKaprodiAll') {

                $ArrToApproveAll = (array) $data_arr['ArrToApproveAll'];
                $ArrToApproveAll_Logging = (array) $data_arr['ArrToApproveAll_Logging'];

                if (count($ArrToApproveAll) > 0) {
                    for ($i = 0; $i < count($ArrToApproveAll); $i++) {



                        // Insert Logging
                        $Log_dataInsert = (array) $ArrToApproveAll_Logging[$i];
                        $this->db->insert('db_notifikasi.logging', $Log_dataInsert);
                        $insert_id = $this->db->insert_id();


                        $this->approveByKaprodi(
                            $ArrToApproveAll[$i],
                            $data_arr['MhswID'],
                            $data_arr['ApprovalAt'],
                            $Log_dataInsert['CreatedBy']
                        );

                        $Log_dataUser = $this->db->select('NIP')->get_where(
                            'db_employees.rule_users',
                            array('IDDivision' => '6')
                        )->result_array();

                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id,
                            'UserID' => $data_arr['NPM']
                        );
                        $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins);

                        if (count($Log_dataUser) > 0) {

                            for ($l = 0; $l < count($Log_dataUser); $l++) {
                                $d = $Log_dataUser[$l]['NIP'];
                                $Log_arr_ins = array(
                                    'IDLogging' => $insert_id,
                                    'UserID' => $d
                                );
                                $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins);
                            }
                        }
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'ApprovedByKaprodi') {


                // Insert Logging
                $Log_dataInsert = (array) $data_arr['Logging'];
                $this->db->insert('db_notifikasi.logging', $Log_dataInsert);
                $insert_id = $this->db->insert_id();

                $this->approveByKaprodi(
                    $data_arr['ID'],
                    $data_arr['MhswID'],
                    $data_arr['ApprovalAt'],
                    $Log_dataInsert['CreatedBy']
                );

                $Log_dataUser = $this->db->select('NIP')->get_where(
                    'db_employees.rule_users',
                    array('IDDivision' => '6')
                )->result_array();

                $Log_arr_ins = array(
                    'IDLogging' => $insert_id,
                    'UserID' => $data_arr['NPM']
                );
                $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins);
                if (count($Log_dataUser) > 0) {
                    for ($i = 0; $i < count($Log_dataUser); $i++) {
                        $d = $Log_dataUser[$i]['NIP'];
                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id,
                            'UserID' => $d
                        );
                        $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins);
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'ApproveAllStudentByKaprodi') {

                $NIP = $data_arr['NIP'];
                $SemesterID = $data_arr['SemesterID'];
                $ProdiID = $data_arr['ProdiID'];
                $ApprovalAt = $data_arr['ApprovalAt'];

                $data = $this->db->query('SELECT sk.*, auts.Year, cd.MKID, cd.TotalSKS FROM db_academic.std_krs sk
                                                            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sk.CDID)
                                                            LEFT JOIN db_academic.auth_students auts ON (auts.NPM = sk.NPM)
                                                            WHERE sk.SemesterID = "' . $SemesterID . '"
                                                            AND cd.ProdiID = "' . $ProdiID . '"
                                                            AND sk.Status = "2"  ORDER BY sk.NPM ASC')->result_array();

                // Get Anak Bimbingan
                $dataBim = $this->db->query('SELECT sk.*, auts.Year, cd.MKID, cd.TotalSKS FROM db_academic.mentor_academic ma
                                                          LEFT JOIN db_academic.std_krs sk ON (sk.NPM = ma.NPM)
                                                          LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sk.CDID)
                                                          LEFT JOIN db_academic.auth_students auts ON (auts.NPM = sk.NPM)
                                                          WHERE ma.NIP = "' . $NIP . '"
                                                          AND sk.SemesterID = "' . $SemesterID . '"

                                                          AND cd.ProdiID = "' . $ProdiID . '"
                                                           AND sk.Status = "1"  ORDER BY sk.NPM ASC ')->result_array();

                if (count($dataBim) > 0) {
                    for ($i = 0; $i < count($dataBim); $i++) {
                        array_push($data, $dataBim[$i]);
                    }
                }

                if (count($data) > 0) {
                    foreach ($data as $item) {

                        // Get Attendance Attendance
                        $dataAttd = $this->db->get_where(
                            'db_academic.attendance',
                            array(
                                'SemesterID' => $SemesterID,
                                'ScheduleID' => $item['ScheduleID']
                            )
                        )->result_array();

                        // Insert Student Ke Attendance
                        foreach ($dataAttd as $itemA) {
                            $dataAins = array(
                                'ID_Attd' => $itemA['ID'],
                                'NPM' => $item['NPM']
                            );
                            $this->db->insert('db_academic.attendance_students', $dataAins);
                            $this->db->reset_query();
                        }

                        $dataUpdateKRS = array(
                            'SemesterID' => $SemesterID,
                            'MhswID' => 0,
                            'NPM' => $item['NPM'],
                            'ScheduleID' => $item['ScheduleID'],
                            'TypeSchedule' => $item['TypeSP'],
                            'CDID' => $item['CDID'],
                            'MKID' => $item['MKID'],
                            'Credit' => $item['TotalSKS'],
                            'Approval' => '0',
                            'StatusSystem' => '1',
                            'Status' => '1'
                        );

                        $DBStudent = 'ta_' . $item['Year'];
                        $this->db->insert($DBStudent . '.study_planning', $dataUpdateKRS);
                        $this->db->reset_query();


                        $arrUpdate = array(
                            'Status' => '3',
                            'ApprovalKaprodi_At' => $ApprovalAt
                        );
                        $this->db->where('ID', $item['ID']);
                        $this->db->update('db_academic.std_krs', $arrUpdate);
                        $this->db->reset_query();
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'RejectedByMentor') {

                $arrUpdate = array(
                    'Status' => '-2'
                );
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.std_krs', $arrUpdate);

                $dataInsert = array(
                    'KRSID' => $data_arr['ID'],
                    'Reason' => $data_arr['Reason'],
                    'UpdateBy' => $data_arr['UpdateBy'],
                    'UpdateAt' => $data_arr['UpdateAt']
                );
                $this->db->insert('db_academic.std_krs_comment', $dataInsert);

                // Insert Logging
                $Log_dataInsert = (array) $data_arr['dataLogging'];
                $this->db->insert('db_notifikasi.logging', $Log_dataInsert);
                $insert_id = $this->db->insert_id();

                $Log_dataUser = $this->db->select('NIP')->get_where(
                    'db_employees.rule_users',
                    array('IDDivision' => '6')
                )->result_array();

                $Log_arr_ins = array(
                    'IDLogging' => $insert_id,
                    'UserID' => $data_arr['NPM']
                );
                $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins);

                if (count($Log_dataUser) > 0) {
                    for ($i = 0; $i < count($Log_dataUser); $i++) {
                        $d = $Log_dataUser[$i]['NIP'];
                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id,
                            'UserID' => $d
                        );
                        $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins);
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'RejectedByKaprodi') {

                $arrUpdate = array(
                    'Status' => '-3'
                );
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.std_krs', $arrUpdate);

                $dataInsert = array(
                    'KRSID' => $data_arr['ID'],
                    'Reason' => $data_arr['Reason'],
                    'UpdateBy' => $data_arr['UpdateBy'],
                    'UpdateAt' => $data_arr['UpdateAt']
                );
                $this->db->insert('db_academic.std_krs_comment', $dataInsert);

                // Insert Logging
                $Log_dataInsert = (array) $data_arr['dataLogging'];
                $this->db->insert('db_notifikasi.logging', $Log_dataInsert);
                $insert_id = $this->db->insert_id();

                $Log_dataUser = $this->db->select('NIP')->get_where(
                    'db_employees.rule_users',
                    array('IDDivision' => '6')
                )->result_array();

                $Log_arr_ins = array(
                    'IDLogging' => $insert_id,
                    'UserID' => $data_arr['NPM']
                );
                $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins);

                if (count($Log_dataUser) > 0) {
                    for ($i = 0; $i < count($Log_dataUser); $i++) {
                        $d = $Log_dataUser[$i]['NIP'];
                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id,
                            'UserID' => $d
                        );
                        $this->db->insert('db_notifikasi.logging_user', $Log_arr_ins);
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'deleteAvailabelCourse') {
                $SKID = $data_arr['SKID'];
                $dataStd = $this->db->query('SELECT sk.SemesterID, sk.NPM, sk.ScheduleID, sk.CDID,
                                                          auts.Year
                                                          FROM db_academic.std_krs sk
                                                          LEFT JOIN db_academic.auth_students auts ON (auts.NPM = sk.NPM)
                                                          WHERE sk.ID = "' . $SKID . '" ')->result_array();

                if (count($dataStd) > 0) {
                    foreach ($dataStd as $std) {
                        // === Hapus Attendance ===
                        // Get ATTD ID
                        $dataAttd = $this->db->select('ID')->get_where('db_academic.attendance', array(
                            'SemesterID' => $std['SemesterID'],
                            'ScheduleID' => $std['ScheduleID']
                        ))->result_array();

                        if (count($dataAttd) > 0) {
                            foreach ($dataAttd as $attd) {
                                $this->db->where(array(
                                    'ID_Attd' => $attd['ID'],
                                    'NPM' => $std['NPM']
                                ));
                                $this->db->delete('db_academic.attendance_students');
                                $this->db->reset_query();
                            }
                        }

                        // ===== Hapus di Study plan =====
                        $DBStudent = 'ta_' . $std['Year'];
                        $this->db->where(array(
                            'SemesterID' => $std['SemesterID'],
                            'ScheduleID' => $std['ScheduleID'],
                            'NPM' => $std['NPM']
                        ));
                        $this->db->delete($DBStudent . '.study_planning');
                        $this->db->reset_query();

                        $this->db->where(array(
                            'SemesterID' => $std['SemesterID'],
                            'ScheduleID' => $std['ScheduleID'],
                            'NPM' => $std['NPM']
                        ));
                        $this->db->delete('db_academic.std_study_planning');
                        $this->db->reset_query();

                        // Trakhir hapus di std krs
                        $this->db->where('ID', $SKID);
                        $this->db->delete('db_academic.std_krs');
                        $this->db->reset_query();
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'deleteAvailabelInBatalTambah') {
                $SKID = $data_arr['SKID'];
                // Trakhir hapus di std krs
                $this->db->where('ID', $SKID);
                $this->db->delete('db_academic.std_krs_batal_tambah');
                $this->db->reset_query();

                return print_r(1);
            } else if ($data_arr['action'] == 'setAsTimetable') {

                $DBStudent = $data_arr['DBStudent'];
                $SemesterID = $data_arr['SemesterID'];
                $NPM = $data_arr['NPM'];

                // Get Data Lama
                $dataSTDLama = $this->db->get_where('db_academic.std_krs', array('SemesterID' => $SemesterID, 'NPM' => $NPM))
                    ->result_array();

                // Get Email
                $dataEmail = $this->db->get('db_academic.std_krs_batal_tambah_email')->result_array();

                if (count($dataSTDLama) > 0) {
                    foreach ($dataSTDLama as $std) {
                        // === Hapus Attendance ===
                        // Get ATTD ID
                        $dataAttd = $this->db->select('ID')->get_where('db_academic.attendance', array(
                            'SemesterID' => $std['SemesterID'],
                            'ScheduleID' => $std['ScheduleID']
                        ))->result_array();
                        if (count($dataAttd) > 0) {
                            foreach ($dataAttd as $attd) {
                                $this->db->where(array(
                                    'ID_Attd' => $attd['ID'],
                                    'NPM' => $std['NPM']
                                ));
                                $this->db->delete('db_academic.attendance_students');
                                $this->db->reset_query();
                            }
                        }

                        // ===== Hapus di Study plan =====
                        $this->db->where(array(
                            'SemesterID' => $std['SemesterID'],
                            'ScheduleID' => $std['ScheduleID'],
                            'NPM' => $std['NPM']
                        ));
                        $this->db->delete($DBStudent . '.study_planning');
                        $this->db->reset_query();

                        // Trakhir hapus di std krs
                        $this->db->where('ID', $std['ID']);
                        $this->db->delete('db_academic.std_krs');
                        $this->db->reset_query();

                        // Remove di STD Study Planning
                        $this->db->where(array(
                            'SemesterID' => $std['SemesterID'],
                            'ScheduleID' => $std['ScheduleID'],
                            'NPM' => $std['NPM']
                        ));
                        $this->db->delete('db_academic.std_study_planning');
                        $this->db->reset_query();
                    }
                }


                // Insert yang baru
                if (count($data_arr['arrToken']) > 0) {
                    for ($i = 0; $i < count($data_arr['arrToken']); $i++) {
                        $dInsert = (array) $data_arr['arrToken'][$i];

                        $this->db->insert('db_academic.std_krs', $dInsert);


                        // Get Attendance Attendance
                        $dataAttd = $this->db->get_where(
                            'db_academic.attendance',
                            array(
                                'SemesterID' => $dInsert['SemesterID'],
                                'ScheduleID' => $dInsert['ScheduleID']
                            )
                        )->result_array();

                        // Insert Ke Attendance
                        foreach ($dataAttd as $itemA) {
                            $dataAins = array(
                                'ID_Attd' => $itemA['ID'],
                                'NPM' => $dInsert['NPM']
                            );
                            $this->db->insert('db_academic.attendance_students', $dataAins);
                        }

                        // - get MKID
                        $dataC = $this->db->select('MKID,TotalSKS')->get_where(
                            'db_academic.curriculum_details',
                            array('ID' => $dInsert['CDID']),
                            1
                        )->result_array();

                        $dataUpdateKRS = array(
                            'SemesterID' => $dInsert['SemesterID'],
                            'MhswID' => $data_arr['MhswID'],
                            'NPM' => $dInsert['NPM'],
                            'ScheduleID' => $dInsert['ScheduleID'],
                            'TypeSchedule' => $dInsert['TypeSP'],
                            'CDID' => $dInsert['CDID'],
                            'MKID' => $dataC[0]['MKID'],
                            'Credit' => $dataC[0]['TotalSKS'],
                            'Approval' => '0',
                            'StatusSystem' => '1',
                            'Status' => '1'
                        );

                        $this->db->insert($DBStudent . '.study_planning', $dataUpdateKRS);
                        $SPID = $this->db->insert_id();


                        $dataUpdateKRS_std = array(
                            'SPID' => $SPID,
                            'ClassOf' => trim(explode('_', $DBStudent)[1]),
                            'SemesterID' => $dInsert['SemesterID'],
                            'NPM' => $dInsert['NPM'],
                            'ScheduleID' => $dInsert['ScheduleID'],
                            'TypeSchedule' => $dInsert['TypeSP'],
                            'CDID' => $dInsert['CDID'],
                            'MKID' => $dataC[0]['MKID'],
                            'Credit' => $dataC[0]['TotalSKS'],
                            'EntredBy' => $this->session->userdata('NIP')
                        );

                        $this->db->insert('db_academic.std_study_planning', $dataUpdateKRS_std);
                    }
                }

                $arrToHistory = (array) $data_arr['arrToHistory'];
                $this->db->insert('db_academic.std_krs_batal_tambah_history', $arrToHistory);


                $result = array(
                    'Email' => $dataEmail
                );

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'read') {
                $dataWhere = (array) $data_arr['dataWhere'];
                $data = $this->m_api->__getStudyPlanning($dataWhere);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'detailStudent') {
                $data = $this->m_api->getDetailStudyPlanning($data_arr['NPM'], $data_arr['ta']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'detailKRSStudents') {

                $NPM = $this->session->userdata('student_NPM');
                $ProgramCampusID = $this->session->userdata('student_ProgramCampusID');
                $Semester = $this->session->userdata('student_Semester');
                $IsSemesterAntara = '0';
                $ClassOf = $this->session->userdata('student_ClassOf');

                $data = $this->m_academic->__getKRS(
                    $data_arr['SemesterID'],
                    $ProgramCampusID,
                    $data_arr['ProdiID'],
                    $Semester,
                    $IsSemesterAntara,
                    $NPM,
                    $ClassOf
                );
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];
                $this->db->insert('db_academic.std_krs', $formData);
                $insert_id = $this->db->insert_id();

                return print_r($insert_id);
            } else if ($data_arr['action'] == 'searchByGroup') {
                $data = $this->db->query('SELECT sdc.ID AS SDCID, sdc.ScheduleID, ps.NameEng,s.ID AS ProdiEng,sdc.CDID ,
                                                    cd.Semester, cr.Year, mk.MKCode, s.ClassGroup, mk.ID AS MKID
                                                    FROM db_academic.schedule_details_course sdc
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                                    LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                                    LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                    LEFT JOIN db_academic.curriculum cr ON (cr.ID = cd.CurriculumID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                    WHERE s.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                    AND s.ClassGroup
                                                    LIKE "%' . $data_arr['ClassGroup'] . '%" ORDER BY cr.Year DESC LIMIT 10')->result_array();

                if (count($data) > 0) {
                    for ($c = 0; $c < count($data); $c++) {
                        // Semester Saat Ini


                        $smt = $this->m_api->_getSeemsterByClassOf($data[$c]['Year']);

                        $data[$c]['Semester'] = $smt;

                        $aarw = array(
                            'SDCID' => $data[$c]['SDCID'],
                            'ScheduleID' => $data[$c]['ScheduleID']
                        );
                        $data[$c]['D'] = $this->m_api->getStdCombinedClass($aarw, $smt);
                    }
                }



                return print_r(json_encode($data));
            }
        }
    }

    public function approveByKaprodi($SKID, $MhswID, $ApprovalAt, $EntredBy)
    {
        $dataInsert = $this->db->query('SELECT sk.*, auts.Year FROM db_academic.std_krs sk
                                                                    LEFT JOIN db_academic.auth_students auts ON (auts.NPM = sk.NPM)
                                                                    WHERE sk.ID = "' . $SKID . '" LIMIT 1 ')->result_array()[0];

        // Get Attendance Attendance
        $dataAttd = $this->db->get_where(
            'db_academic.attendance',
            array(
                'SemesterID' => $dataInsert['SemesterID'],
                'ScheduleID' => $dataInsert['ScheduleID']
            )
        )->result_array();

        // Insert Ke Attendance
        foreach ($dataAttd as $itemA) {
            $dataAins = array(
                'ID_Attd' => $itemA['ID'],
                'NPM' => $dataInsert['NPM']
            );
            $this->db->insert('db_academic.attendance_students', $dataAins);
            $this->db->reset_query();
        }


        // - get MKID
        $dataC = $this->db->select('MKID,TotalSKS')->get_where(
            'db_academic.curriculum_details',
            array('ID' => $dataInsert['CDID']),
            1
        )->result_array();

        $dataUpdateKRS = array(
            'SemesterID' => $dataInsert['SemesterID'],
            'MhswID' => $MhswID,
            'NPM' => $dataInsert['NPM'],
            'ScheduleID' => $dataInsert['ScheduleID'],
            'TypeSchedule' => $dataInsert['TypeSP'],
            'CDID' => $dataInsert['CDID'],
            'MKID' => $dataC[0]['MKID'],
            'Credit' => $dataC[0]['TotalSKS'],
            'Approval' => '0',
            'StatusSystem' => '1',
            'Status' => '1'
        );

        $DBStudent = 'ta_' . $dataInsert['Year'];
        $this->db->insert($DBStudent . '.study_planning', $dataUpdateKRS);
        $insert_id = $this->db->insert_id();

        // insert to std_study_planning
        $arrStdP = array(
            'SPID' =>   $insert_id,
            'ClassOf' => $dataInsert['Year'],
            'SemesterID' => $dataInsert['SemesterID'],
            'NPM' => $dataInsert['NPM'],
            'ScheduleID' => $dataInsert['ScheduleID'],
            'TypeSchedule' => $dataInsert['TypeSP'],
            'CDID' => $dataInsert['CDID'],
            'MKID' => $dataC[0]['MKID'],
            'Credit' => $dataC[0]['TotalSKS'],
            'EntredBy' => $EntredBy
        );
        $this->db->insert('db_academic.std_study_planning', $arrStdP);
        $this->db->reset_query();

        $arrUpdate = array(
            'Status' => '3',
            'ApprovalKaprodi_At' => $ApprovalAt
        );
        $this->db->where('ID', $SKID);
        $this->db->update('db_academic.std_krs', $arrUpdate);
        $this->db->reset_query();
    }

    public function crudYearAcademic()
    {

        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->db->order_by('YearAcademic', 'ASC')->get('db_academic.std_ta')
                    ->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'add') {
                $dataInsert = (array) $data_arr['dataInsert'];
                $this->db->insert('db_academic.std_ta', $dataInsert);
                $insert_id = $this->db->insert_id();

                $db_new = 'ta_' . $dataInsert['YearAcademic'];

                $this->m_api->createDBYearAcademicNew($db_new);

                return print_r($insert_id);
            }
        }
    }

    public function filterStudents()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'readStudents') {
                $filter = (array) $data_arr['dataFilter'];

                //                $data = $this->m_api->__filterStudents($filter);
                $data = $this->m_api->__filterStudents($filter);

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'delete') {
                $this->db->where('ID', $data_arr['IDMA']);
                $this->db->delete('db_academic.mentor_academic');
                return print_r(1);
            } else if ($data_arr['action'] == 'add') {
                $dataForm = (array) $data_arr['dataForm'];
                $dataNPM = (array) $data_arr['dataNPM'];

                for ($i = 0; $i < count($dataNPM); $i++) {
                    $dataForm['NPM'] = $dataNPM[$i];
                    //                    print_r($dataForm);
                    $this->db->insert('db_academic.mentor_academic', $dataForm);
                }

                return print_r(1);
                //                print_r($dataNPM);
            }
        }
    }

    public function crudTuitionFee()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $ClassOf = $data_arr['ClassOf'];

                $prodi = $this->db->select('ID,NameEng')->get('db_academic.program_study')->result_array();

                $result = [];

                for ($i = 0; $i < count($prodi); $i++) {
                    $data = $this->db->query('SELECT tf.ID,tf.PTID,tf.Cost,pt.Description,pt.Abbreviation,tf.Pay_Cond FROM db_finance.tuition_fee tf
                                                    LEFT JOIN db_finance.payment_type pt ON (tf.PTID = pt.ID)
                                                    LEFT JOIN db_academic.program_study ps ON (tf.ProdiID = ps.ID)
                                                    WHERE tf.ClassOf = "' . $ClassOf . '" AND tf.ProdiID = "' . $prodi[$i]['ID'] . '" and tf.Pay_Cond = 1
                                                    ORDER BY tf.ProdiID, tf.PTID ASC, tf.Pay_Cond ASC ')->result_array();
                    if (count($data) > 0) {
                        $data_p = array(
                            'ProdiID' => $prodi[$i]['ID'],
                            'ProdiName' => $prodi[$i]['NameEng'],
                            'Detail' => $data
                        );
                        array_push($result, $data_p);
                    }
                }

                for ($i = 0; $i < count($prodi); $i++) {
                    $data = $this->db->query('SELECT tf.ID,tf.PTID,tf.Cost,pt.Description,pt.Abbreviation,tf.Pay_Cond FROM db_finance.tuition_fee tf
                                                    LEFT JOIN db_finance.payment_type pt ON (tf.PTID = pt.ID)
                                                    LEFT JOIN db_academic.program_study ps ON (tf.ProdiID = ps.ID)
                                                    WHERE tf.ClassOf = "' . $ClassOf . '" AND tf.ProdiID = "' . $prodi[$i]['ID'] . '" and tf.Pay_Cond = 2
                                                    ORDER BY tf.ProdiID, tf.PTID ASC, tf.Pay_Cond ASC ')->result_array();
                    if (count($data) > 0) {
                        $data_p = array(
                            'ProdiID' => $prodi[$i]['ID'],
                            'ProdiName' => $prodi[$i]['NameEng'],
                            'Detail' => $data
                        );
                        array_push($result, $data_p);
                    }
                }

                return print_r(json_encode($result));
            }
        }
    }

    public function getEmployeesBy($division = null, $position = null)
    {
        try {
            $key = "UAP)(*";
            $division = $this->jwt->decode($division, $key);
            $position = $this->jwt->decode($position, $key);
            $getData = $this->m_api->getEmployeesBy($division, $position);
            echo json_encode($getData);
        } catch (Exception $e) {
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
        for ($i = 0; $i < count($getData); $i++) {
            $data['message'][] = array(
                'label' => $getData[$i]['SchoolName'],
                'value' => $getData[$i]['ID']
            );
        }
        echo json_encode($data);
    }

    public function getSemesterActive()
    {
        $data = $this->m_api->_getSemesterActive();
        return print_r(json_encode($data));
    }

    public function getSumberIklan()
    {
        $getData = $this->m_master->showDataActive_array('db_admission.source_from_event', 1);
        echo json_encode($getData);
    }

    public function getPriceFormulirOffline()
    {
        $getData = $this->m_master->showDataActive_array('db_admission.price_formulir_offline', 1);
        echo json_encode($getData);
    }

    public function getEvent()
    {
        $getData = $this->m_master->showDataActive_array('db_admission.price_event', 1);
        echo json_encode($getData);
    }

    public function getDocument()
    {
        $input = $this->getInputToken();
        $this->load->model('admission/m_admission');
        $getData = $this->m_admission->getDataDokumentRegister($input['ID_register_formulir']);
        echo json_encode($getData);
    }

    public function getDocument2()
    {
        $input = $this->getInputToken();
        $this->load->model('admission/m_admission');
        $arr = array('doc' => array(), 'ujian' => array(), 'kelulusan' => array());
        $getData = $this->m_admission->getDataDokumentRegister($input['ID_register_formulir']);
        // get nilai ujian jika ada
        $getUjian = $this->m_admission->getHasilUjian($input['ID_register_formulir']);
        $arr['doc'] = $getData;
        if (count($getUjian) > 0) {
            $arr['ujian'] = $getUjian;
            $kelulusan = $this->m_admission->getkelulusan($input['ID_register_formulir']);
            $arr['kelulusan'] = $kelulusan;
        }

        echo json_encode($arr);
    }

    public function getDocumentAdmisiMHS()
    {
        $input = $this->getInputToken();
        $this->load->model('admission/m_admission');
        $getData = $this->m_admission->getDocumentAdmisiMHS($input['NPM']);
        echo json_encode($getData);
    }

    public function crudJadwalUjian()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->m_api->getJadwalUjian();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'checkDateExam') {
                $data = $this->m_api->getDateExam($data_arr['SemesterID'], $data_arr['Type']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'checkDateExam4Edit') {
                $data = $this->m_api->getDateExamInEdit($data_arr['SemesterID']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'checkDateExam4Input') {
                $data = $this->m_api->getDateExam4input($data_arr['SemesterID']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'checkCourse4Exam') {
                $data = $this->m_api
                    ->__checkDataCourseForExam($data_arr['ScheduleID'], $data_arr['Type']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'showDataClassGroupInExam') {

                // Get Exam ID

                $dataExam = $this->db->query('SELECT ex.ID AS ExamID, s.ID AS ScheduleID, s.ClassGroup, mk.NameEng AS CourseEng, mk.Name AS Course FROM db_academic.exam_group exg
                                                            LEFT JOIN db_academic.exam ex ON (ex.ID = exg.ExamID)
                                                            LEFT JOIN db_academic.schedule s ON (s.ID = exg.ScheduleID)
                                                            LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = exg.ScheduleID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                            WHERE ex.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                             AND ex.Type = "' . strtolower($data_arr['Type']) . '" GROUP BY ex.ID')->result_array();

                return print_r(json_encode($dataExam));
            } else if ($data_arr['action'] == 'showDataExamByGroup') {
                $ExamID = $data_arr['ExamID'];

                $data = $this->db->query('SELECT ex.ExamDate, cl.Room, ex.ExamStart, ex.ExamEnd,
                                                          em1.Name AS Inv1, em2.Name AS Inv2 FROM db_academic.exam ex
                                                          LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                                          LEFT JOIN db_employees.employees em1 ON (em1.NIP = ex.Pengawas1)
                                                          LEFT JOIN db_employees.employees em2 ON (em2.NIP = ex.Pengawas2)
                                                          WHERE ex.ID = "' . $ExamID . '" LIMIT 1 ')->result_array();

                if (count($data) > 0) {
                    $dataCourse = $this->db->query('SELECT mk.MKCode, mk.NameEng AS CourseEng, mk.Name AS Course, s.ClassGroup
                                                                        FROM db_academic.exam_group exd
                                                                        LEFT JOIN db_academic.schedule s ON (s.ID = exd.ScheduleID)
                                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = exd.ScheduleID)
                                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                                        WHERE exd.ExamID = "' . $ExamID . '" GROUP BY exd.ScheduleID')->result_array();
                    $data[0]['Course'] = $dataCourse;
                }

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];
                $dataStudents = (array) $data_arr['dataStudents'];

                print_r($data_arr);
                exit;

                $this->db->insert('db_academic.exam', $formData);
                $insert_id = $this->db->insert_id();

                for ($e = 0; $e < count($dataStudents); $e++) {
                    $dataM = (array) $dataStudents[$e];
                    $dataInsert = array(
                        'ExamID' => $insert_id,
                        'MhswID' => $dataM['MhswID'],
                        'NPM' => $dataM['NPM'],
                        'DB_Students' => $dataM['DB_Students']
                    );
                    $this->db->insert('db_academic.exam_details', $dataInsert);
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'readSchedule') {

                $data = $this->m_api->getScheduleExam(
                    $data_arr['SemesterID'],
                    $data_arr['Type']
                );

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readDetailStudent') {
                $ExamID = $data_arr['ExamID'];
                $data = $this->m_api->getExamStudent($ExamID);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'updateAttendanceExamSAS') {
                $ID = $data_arr['ID'];
                $ExamID = $data_arr['ExamID'];
                $Status = $data_arr['Status'];

                $this->db->set('Status', '' . $Status);
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.exam_details');
                $this->db->reset_query();

                // set -1
                $this->db->set('Status', '-1');
                $this->db->where(array(
                    'ExamID' => $ExamID,
                    'Status' => '0'
                ));
                $this->db->update('db_academic.exam_details');
                $this->db->reset_query();


                return print_r(1);
            }

            // ===== Save 2 PDF Exam ======
            else if ($data_arr['action'] == 'save2pdf_Layout') {
                $IDExam = $data_arr['IDExam'];
            } else if ($data_arr['action'] == 'save2pdf_DraftQuestions') {
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
                                                      WHERE ex.ID = "' . $IDExam . '" ')->result_array();
                print_r($dataPdf);
            } else if ($data_arr['action'] == 'save2pdf_AnswerSheet') {
                $IDExam = $data_arr['IDExam'];
            } else if ($data_arr['action'] == 'save2pdf_NewsEvent') {
                $IDExam = $data_arr['IDExam'];
            } else if ($data_arr['action'] == 'save2pdf_AttendanceList') {
                $IDExam = $data_arr['IDExam'];
            } else if ($data_arr['action'] == 'deleteExamInExamList') {
                $ExamID = $data_arr['ExamID'];

                // Delete Exam
                $this->db->where('ID', $ExamID);
                $this->db->delete('db_academic.exam');

                $this->db->where('ExamID', $ExamID);
                $this->db->delete(array('db_academic.exam_group', 'db_academic.exam_details'));

                // Remove file
                $dataFl = $this->db->get_where(
                    'exam_task',
                    array('ExamID' => $ExamID)
                )->result_array();

                if (count($dataFl) > 0) {
                    $path = './uploads/task-exam/' . $dataFl[0]['File'];
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    $this->db->where('ExamID', $ExamID);
                    $this->db->delete('exam_task');
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'deleteStuden4EditExam') {

                $this->db->where('ID', $data_arr['Exam_detail_ID']);
                $this->db->delete('db_academic.exam_details');

                return print_r(1);
            } else if ($data_arr['action'] == 'editGroupExam') {

                // Update Exam
                $whereExam = array('ID' => $data_arr['ExamID'], 'SemesterID' => $data_arr['SemesterID']);

                $updateExam = (array) $data_arr['updateExam'];

                $this->db->where($whereExam);
                $this->db->update('db_academic.exam', $updateExam);

                // Update Group Jika ada
                if (count($data_arr['insert_group']) > 0) {
                    for ($g = 0; $g < count($data_arr['insert_group']); $g++) {
                        if ($data_arr['insert_group'][$g] != null && $data_arr['insert_group'][$g] != '') {
                            $arrInsert = array(
                                'ExamID' => $data_arr['ExamID'],
                                'ScheduleID' => $data_arr['insert_group'][$g]
                            );

                            $dataCk = $this->db->get_where('db_academic.exam_group', $arrInsert)->result_array();

                            if (count($dataCk) <= 0) {
                                $this->db->insert('db_academic.exam_group', $arrInsert);
                            }
                        }
                    }
                }

                // Update Details kalo ada
                if (count($data_arr['insert_details']) > 0) {
                    for ($d = 0; $d < count($data_arr['insert_details']); $d++) {
                        $n = (array) $data_arr['insert_details'][$d];

                        // Get Exam ID
                        $dg = $this->db->select('ID')->get_where(
                            'db_academic.exam_group',
                            array('ExamID' => $data_arr['ExamID'], 'ScheduleID' => $n['ScheduleID']),
                            1
                        )->result_array();

                        $arrInsD = array(
                            'ExamID' =>  $data_arr['ExamID'],
                            'ExamGroupID' => $dg[0]['ID'],
                            'ScheduleID' => $n['ScheduleID'],
                            'MhswID' => $n['MhswID'],
                            'NPM' => $n['NPM'],
                            'DB_Students' => $n['DB_Students']
                        );

                        // Cek apakah mahasiswa sudah ada atau belum
                        $cekMhs = $this->db->get_where('db_academic.exam_details', $arrInsD, 1)->result_array();
                        if (count($cekMhs) <= 0) {
                            $this->db->insert('db_academic.exam_details', $arrInsD);
                        }
                    }
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'checkBentrokExam') {

                $data = $this->db->query('SELECT ex.*,cl.Room FROM db_academic.exam ex
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                                    WHERE
                                                    ex.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                    AND ex.Type LIKE "' . $data_arr['Type'] . '"
                                                    AND ex.ExamDate = "' . $data_arr['Date'] . '"
                                                    AND ex.ExamClassroomID = "' . $data_arr['RoomID'] . '"
                                                    AND ( ("' . $data_arr['Start'] . '" >= ex.ExamStart AND "' . $data_arr['Start'] . '" <= ex.ExamEnd) OR
                                                        ("' . $data_arr['End'] . '" >= ex.ExamStart AND "' . $data_arr['End'] . '" <= ex.ExamEnd) OR
                                                        ("' . $data_arr['Start'] . '" <= ex.ExamStart AND "' . $data_arr['End'] . '" >= ex.ExamEnd)
                                                    )
                                                     ')->result_array();

                if (count($data) > 0) {
                    for ($c = 0; $c < count($data); $c++) {
                        $dataC = $this->db->query('SELECT exg.*,mk.NameEng FROM db_academic.exam_group exg
                                                            LEFT JOIN db_academic.schedule s ON (s.ID = exg.ScheduleID)
                                                            LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                            WHERE exg.ExamID = "' . $data[$c]['ID'] . '" GROUP BY exg.ScheduleID ')->result_array();
                        $data[$c]['Course'] = $dataC;
                    }
                }


                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'getDataExamTask') {

                $ExamID = $data_arr['ExamID'];

                $data = $this->db->get_where('db_academic.exam_task', array(
                    'ExamID' => $ExamID
                ))->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'deleteGroupExam') {
                $ExamID = $data_arr['ExamID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $res = 1;
                $dEx = $this->db->get_where('exam_group', array('ExamID' => $ExamID))->result_array();
                if (count($dEx) <= 1) {
                    $res = -1;
                    $this->db->where('ID', $ExamID);
                    $this->db->delete('exam');
                }


                $this->db->where(array('ExamID' => $ExamID, 'ScheduleID' => $ScheduleID));
                $this->db->delete(array('exam_group', 'exam_details'));

                return print_r($res);
            } else if ($data_arr['action'] == 'setExamSchedule') {

                $insert_exam = (array) $data_arr['insert_exam'];

                $this->db->insert('db_academic.exam', $insert_exam);
                $insert_exam_id = $this->db->insert_id();

                $ex_g = (array) $data_arr['insert_group'];

                for ($g = 0; $g < count($ex_g); $g++) {

                    $ar = array(
                        'ExamID' => $insert_exam_id,
                        'ScheduleID' => $ex_g[$g]
                    );

                    $this->db->insert('db_academic.exam_group', $ar);
                }


                $ex_g_d = (array) $data_arr['insert_details'];

                for ($s = 0; $s < count($ex_g_d); $s++) {
                    $ds = (array) $ex_g_d[$s];
                    $dg = $this->db->select('ID')->get_where(
                        'db_academic.exam_group',
                        array(
                            'ExamID' => $insert_exam_id,
                            'ScheduleID' => $ds['ScheduleID']
                        ),
                        1
                    )->result_array();

                    $ds['ExamID'] = $insert_exam_id;
                    $ds['ExamGroupID'] = $dg[0]['ID'];

                    $this->db->insert('db_academic.exam_details', $ds);
                }

                return print_r(1);
            }
        }
    }

    public function crudEmployees()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                // $data = $this->db->select('NIP,Name')->get_where('db_employees.employees',
                //     array('StatusEmployeeID !=' => -2))->result_array();
                $sql = 'select NIP,Name from db_employees.employees
                        where StatusEmployeeID != -2
                        UNION
                        select NIK as NIP,Name from db_employees.holding
                        ';
                $data = $this->db->query($sql, array())->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'resetPassword2BirthDay') {
                $PasswordOld = $data_arr['PasswordOld'];
                $NIP = $data_arr['NIP'];
                $dataUpd = array(
                    'Password_Old' => md5($PasswordOld),
                    'Status' => '-1'
                );

                $this->db->where('NIP', $NIP);
                $this->db->update('db_employees.employees', $dataUpd);

                return print_r(1);
            } else if ($data_arr['action'] == 'UpdateCertificateLec') {

                $ID = $data_arr['ID'];
                $dataForm = (array) $data_arr['dataForm'];
                $FileName = '';

                if ($ID != '') {
                    // Update
                    $this->db->where('ID', $ID);
                    $this->db->update('db_employees.employees_certificate', $dataForm);
                    $FileName = $this->db->select('File')
                        ->get_where('db_employees.employees_certificate', array('ID' => $ID))->result_array()[0]['File'];
                } else {
                    // Insert
                    $this->db->insert('db_employees.employees_certificate', $dataForm);
                    $ID = $this->db->insert_id();
                }

                return print_r(json_encode(array(
                    'ID' => $ID,
                    'FileName' => $FileName
                )));
            } else if ($data_arr['action'] == 'removeCertificateLec') {

                // Remove File
                if ($data_arr['File'] != '') {
                    unlink('./uploads/certificate/' . $data_arr['File']);
                }

                // Remove DB
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_employees.employees_certificate');

                return print_r(1);
            } else if ($data_arr['action'] == 'readCertificateLec') {
                $data = $this->db->get_where('db_employees.employees_certificate', array(
                    'NIP' => $data_arr['NIP']
                ))->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readCertificateLec_request') {
                $rs = ['dataApprove' => [], 'dataRequest' => []];
                $data = $this->db->get_where('db_employees.employees_certificate', array(
                    'NIP' => $data_arr['NIP']
                ))->result_array();
                $rs['dataApprove'] = $data;
                // get certificate request di log field and adding
                $dataEMP = $this->db->get_where('db_employees.employees', array(
                    'NIP' => $data_arr['NIP']
                ))->result_array()[0];


                if (!empty($dataEMP['Logs'])) {
                    $jsonExtract = json_decode($dataEMP['Logs'], true);
                    $rs['dataRequest'] = $jsonExtract['certificate_request'];
                }

                return print_r(json_encode($rs));
            } else if ($data_arr['action'] == 'addEmployees') {
                $rs = array('msg' => '', 'status' => 1);
                $formInsert = (array) $data_arr['formInsert'];
                $raw_formInsert = (array) $data_arr['formInsert'];
                $formInsert['Password_Old'] = md5($formInsert['Password_Old']);

                // Cek apakah NIP sudah digunakan atau belum
                $NIP = $formInsert['NIP'];
                $dataN = $this->db->select('NIP')->get_where(
                    'db_employees.employees',
                    array('NIP' => $NIP)
                )->result_array();

                if (count($dataN) > 0) {
                    $rs['msg'] = 'NIK / NIP is exist';
                    $rs['status'] = 0;
                    // return print_r(0);
                } else {
                    // check fill admin Prodi
                    $ProdiArr = (array) $data_arr['arr_Prodi'];
                    $PositionMain = $formInsert['PositionMain'];
                    $PositionMain = explode('.', $PositionMain);
                    $Position = $PositionMain[1];
                    $Division = $PositionMain[0];
                    // for AD
                    $Password = $raw_formInsert['Password_Old'];
                    $Password_md5 = $formInsert['Password_Old'];
                    $Name = $formInsert['Name'];
                    $G_div = $this->m_master->caribasedprimary('db_employees.division', 'ID', $Division);
                    $description = $G_div[0]['Description'];
                    $arr_callback = array(
                        'UsernamePCam' => $formInsert['NIP'],
                        'UsernamePC' => '',
                        'Password' => $Password,
                        'EmailPU' => $formInsert['EmailPU'],
                    );
                    if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                        // if(true) {
                        // check url exist
                        $urlAD = URLAD . '__api/Create';
                        $is_url_exist = $this->m_master->is_url_exist($urlAD);
                        if ($is_url_exist) {
                            // insert to make AD
                            $data_arr = [
                                [
                                    'Name' => $Name,
                                    'Password' => $Password,
                                    'description' => $description,
                                ],
                            ];
                            $data = array(
                                'auth' => 's3Cr3T-G4N',
                                'Type' => 'Employee',
                                'data_arr' => $data_arr,
                            );

                            $url = $urlAD;
                            $token = $this->jwt->encode($data, "UAP)(*");
                            $callback = $this->m_master->apiservertoserver($url, $token);
                            if (array_key_exists('email', $callback) && !empty($callback['email'])) {
                                // print_r($callback);die();
                                // get email from AD
                                $arr_email = $callback['email'];
                                $formInsert['EmailPU'] = $arr_email[0];
                                $UserID = explode('@', $formInsert['EmailPU']);
                                $UserID = $UserID[0];
                                $arr_callback = array(
                                    'UsernamePCam' => $formInsert['NIP'],
                                    'UsernamePC' => $UserID,
                                    'Password' => $Password,
                                    'EmailPU' => $formInsert['EmailPU'],
                                );

                                // insert card number
                                if (array_key_exists('Access_Card_Number', $formInsert)) {
                                    $pager = $formInsert['Access_Card_Number'];
                                    if ($pager != '' && $pager != null) {
                                        $data_arr = [
                                            'pager' => $pager,
                                        ];
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Type' => 'Employee',
                                            'UserID' => $UserID,
                                            'data_arr' => $data_arr,
                                        );

                                        $url = URLAD . '__api/Edit';
                                        $token = $this->jwt->encode($data, "UAP)(*");
                                        $this->m_master->apiservertoserver($url, $token);
                                    }
                                }
                            } else {
                                $rs['msg'] = 'Lost packet from Windows active directory, try again';
                                $rs['status'] = 0;
                                echo json_encode($rs);
                                die(); // stop script
                            }
                        } else {
                            $rs['msg'] = 'Windows active directory server not connected';
                            $rs['status'] = 0;
                            echo json_encode($rs);
                            die(); // stop script
                        }
                    }
                    // end AD
                    $rs['arr_callback'] = $arr_callback; // for callback
                    $formInsert['Password_Old'] = $Password_md5;

                    /*ADDED BY FEBRI @ FEB 2020*/
                    $myNIP = $this->session->userdata('NIP');
                    $myName = $this->session->userdata('Name');
                    $formInsert['EnteredBy'] = $myNIP . '/' . $myName;
                    $formInsert['isApproved'] = null;
                    /*END ADDED BY FEBRI @ FEB 2020*/

                    $insert = $this->db->insert('db_employees.employees', $formInsert);


                    //insert career
                    $division = 0;
                    $position = 0;

                    if (strpos($formInsert['PositionMain'], '.') !== false) {
                        $splitPositionMain = explode(".", $formInsert['PositionMain']);
                        $division = $splitPositionMain[0];
                        $position = $splitPositionMain[1];
                    }
                    $currentDate = date("Y-m-d");
                    $plus1Year = date('Y-m-d', strtotime('-1 day -1 month +1 years'));
                    $isStatusID = (($formInsert['StatusEmployeeID'] == 2) ? 3 : (($formInsert['StatusEmployeeID'] == 7) ? 6 : 0));
                    $dataCareer = array("NIP" => $formInsert['NIP'], "StartJoin" => $currentDate, "EndJoin" => $plus1Year, "LevelID" => 1, "DepartmentID" => $division, "PositionID" => $position, "StatusID" => $isStatusID, "isShowSTO" => 0);
                    $insertCareer = $this->db->insert('db_employees.employees_career', $dataCareer);
                    $dataJoin = array("NIP" => $formInsert['NIP'], "JoinDate" => $currentDate, "StatusEmployeeID" => $formInsert['StatusEmployeeID']);
                    $insertJoin = $this->db->insert('db_employees.employees_joindate', $dataJoin);
                    $dataEducation = $this->db->insert('db_employees.employees_educations', array("NIP" => $formInsert['NIP'], "LevelEduID" => $formInsert['LevelEducationID']));
                    //end career

                    if ($Position == 6 || $Division == 15) {
                        if ($Division == 15) {
                            for ($i = 0; $i < count($ProdiArr); $i++) {
                                // update Per ID Prodi
                                $dataSave = array(
                                    'AdminID' => $NIP,
                                );
                                $this->db->where('ID', $ProdiArr[$i]);
                                $this->db->update('db_academic.program_study', $dataSave);
                            }
                        } else {
                            for ($i = 0; $i < count($ProdiArr); $i++) {
                                // update Per ID Prodi
                                $dataSave = array(
                                    'KaprodiID' => $NIP,
                                );
                                $this->db->where('ID', $ProdiArr[$i]);
                                $this->db->update('db_academic.program_study', $dataSave);
                            }
                        }
                    }

                    // send email to IT
                    $DescriptionWr = '<p>Dear Team,<br/> Berikut dibawah ini Create Employee  by ' . $this->session->userdata('Name') . ', <br/>
                                         <p>Username PC : ' . $arr_callback['UsernamePC'] . '</p>
                                         <p>Username Aplikasi PCAM : ' . $arr_callback['UsernamePCam'] . '</p>
                                         <p>Password : ' . $arr_callback['Password'] . '</p>
                                         <p>Email PU : ' . $arr_callback['EmailPU'] . '</p>
                                        ';
                    $ToEmail = ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') ? array(
                        'Div' => array(12, 13),
                    ) : array(
                        'NIP' => array('2018018'),
                    );
                    $data = array(
                        'auth' => 's3Cr3T-G4N',
                        'Logging' => array(
                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Create Employee ' . $Name . ' by ' . $this->session->userdata('Name'),
                            'Description' => $DescriptionWr,
                            'URLDirect' => 'ShowLoggingNotification',
                            'CreatedBy' => $this->session->userdata('NIP'),
                        ),
                        'To' => $ToEmail,
                        'Email' => 'Yes',
                    );

                    $url = url_pas . 'rest2/__send_notif_browser';
                    $token = $this->jwt->encode($data, "UAP)(*");
                    $this->m_master->apiservertoserver($url, $token);
                    // send email to IT

                    // insert to db venue for user access
                    $dataSave = array(
                        'NIP' => $NIP,
                        'G_user' => 4,
                    );
                    $this->db->insert('db_reservation.previleges_guser', $dataSave);

                    // return print_r(1);
                }

                echo json_encode($rs);
            } else if ($data_arr['action'] == 'UpdateEmployees') {
                $rs = array('msg' => '', 'status' => 1);
                $formUpdate = (array) $data_arr['formUpdate'];
                //check Access card number is changing
                /*ADDED BY FEBRI */
                $isAccessCard = false;
                $conditions = array("NIP" => $data_arr['NIP']);
                $isCheck = $this->General_model->fetchData("db_employees.employees", $conditions)->row();
                if (!empty($isCheck)) {
                    $AccessCardExisting = $isCheck->Access_Card_Number;
                    if ($AccessCardExisting != $formUpdate['Access_Card_Number']) {
                        $isAccessCard = true;
                    }
                }
                if ($isAccessCard) {
                    /*END ADDED BY FEBRI */
                    $formUpdate['Password_Old'] = md5($formUpdate['Password_Old']);
                    if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                        // if(true) {
                        $urlAD = URLAD . '__api/Edit';
                        $is_url_exist = $this->m_master->is_url_exist($urlAD);
                        if ($is_url_exist) {
                            if (array_key_exists('Access_Card_Number', $formUpdate)) {
                                $pager = $formUpdate['Access_Card_Number'];
                                if ($pager != '' && $pager != null) {
                                    $data_arr1 = [
                                        'pager' => $pager,
                                    ];
                                    $UserID = explode('@', $formUpdate['EmailPU']);
                                    $UserID = $UserID[0];
                                    $data = array(
                                        'auth' => 's3Cr3T-G4N',
                                        'Type' => 'Employee',
                                        'UserID' => $UserID,
                                        'data_arr' => $data_arr1,
                                    );

                                    $url = URLAD . '__api/Edit';
                                    $token = $this->jwt->encode($data, "UAP)(*");
                                    $this->m_master->apiservertoserver($url, $token);
                                }
                            }
                        } else {
                            $rs['msg'] = 'Windows active directory server not connected';
                            $rs['status'] = 0;
                            echo json_encode($rs);
                            die(); // stop script
                        }
                    }
                }

                // disabled enabled AD
                if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                    // if(true) {
                    $urlAD = URLAD . '__api/DisableEnable';
                    $is_url_exist = $this->m_master->is_url_exist($urlAD);

                    if (array_key_exists('StatusEmployeeID', $formUpdate) && $is_url_exist) {
                        $UserID = explode('@', $formUpdate['EmailPU']);
                        $UserID = $UserID[0];
                        $statusGet = ($formUpdate['StatusEmployeeID'] == -1 || $formUpdate['StatusEmployeeID'] == '-1') ? 'yes' : 'no';
                        $data = array(
                            'auth' => 's3Cr3T-G4N',
                            'UserID' => $UserID,
                            'statusGet' => $statusGet,
                        );

                        $urlAD = URLAD . '__api/DisableEnable';
                        $token = $this->jwt->encode($data, "UAP)(*");
                        $this->m_master->apiservertoserver($urlAD, $token);
                    }
                }

                // Cek apakah delete photo atau tidak
                if ($data_arr['DeletePhoto'] == 1 || $data_arr['DeletePhoto'] == '1') {
                    $pathPhoto = './uploads/employees/' . $data_arr['LastPhoto'];
                    if (file_exists($pathPhoto)) {
                        unlink($pathPhoto);
                    }
                }



                // $formUpdate = (array) $data_arr['formUpdate'];
                // $formUpdate['Password_Old'] = md5($formUpdate['Password_Old']);

                /*ADDED BY FEBRI @ FEB 2020*/
                $myNIP = $this->session->userdata('NIP');
                $myName = $this->session->userdata('Name');
                $formUpdate['UpdatedBy'] = $myNIP . '/' . $myName;
                /*END ADDED BY FEBRI @ FEB 2020*/

                $this->db->where('NIP', $data_arr['NIP']);
                $this->db->update('db_employees.employees', $formUpdate);

                // check fill admin Prodi / Ka prodi
                //UPDATED BY FEBRI @ JULY 2020
                $PositionMain = $formUpdate['PositionMain'];
                $PositionMain = explode('.', $PositionMain);
                $Position = $PositionMain[1];
                $Division = $PositionMain[0];

                if (!empty($formUpdate['PositionOther1'])) {
                    $PositionOTH1 = $formUpdate['PositionOther1'];
                    $PositionOTH1 = explode('.', $PositionOTH1);
                    $Position_OTH1 = $PositionOTH1[1];
                    $Division_OTH1 = $PositionOTH1[0];
                } else {
                    $Position_OTH1 = 0;
                    $Division_OTH1 = 0;
                }

                if (!empty($formUpdate['PositionOther2'])) {
                    $PositionOTH2 = $formUpdate['PositionOther2'];
                    $PositionOTH2 = explode('.', $PositionOTH2);
                    $Position_OTH2 = $PositionOTH2[1];
                    $Division_OTH2 = $PositionOTH2[0];
                } else {
                    $Position_OTH2 = 0;
                    $Division_OTH2 = 0;
                }

                if (!empty($formUpdate['PositionOther3'])) {
                    $PositionOTH3 = $formUpdate['PositionOther3'];
                    $PositionOTH3 = explode('.', $PositionOTH3);
                    $Position_OTH3 = $PositionOTH3[1];
                    $Division_OTH3 = $PositionOTH3[0];
                } else {
                    $Position_OTH3 = 0;
                    $Division_OTH3 = 0;
                }



                //if ($Position == 6 || $Division == 15) { => OLD script
                if ((($Division == 14 || $Division == 15) && $Position == 6) ||
                    (($Division_OTH1 == 14 || $Division_OTH1 == 15) && $Position_OTH1 == 6) ||
                    (($Division_OTH2 == 14 || $Division_OTH2 == 15) && $Position_OTH2 == 6) ||
                    (($Division_OTH3 == 14 || $Division_OTH3 == 15) && $Position_OTH3 == 6)
                ) {
                    //END UPDATED BY FEBRI @ JULY 2020
                    $ProdiArr = (array) $data_arr['arr_Prodi'];
                    if ($Division == 15) {
                        $dataSave = array(
                            'AdminID' => null,
                        );
                        $this->db->where('AdminID', $data_arr['NIP']);
                        $this->db->update('db_academic.program_study', $dataSave);
                        for ($i = 0; $i < count($ProdiArr); $i++) {
                            // update Per ID Prodi
                            $dataSave = array(
                                'AdminID' => $data_arr['NIP'],
                            );
                            $this->db->where('ID', $ProdiArr[$i]);
                            $this->db->update('db_academic.program_study', $dataSave);
                        }
                    } else {
                        $dataSave = array(
                            'KaprodiID' => null,
                        );
                        $this->db->where('KaprodiID', $data_arr['NIP']);
                        $this->db->update('db_academic.program_study', $dataSave);
                        for ($i = 0; $i < count($ProdiArr); $i++) {
                            // update Per ID Prodi
                            $dataSave = array(
                                'KaprodiID' => $data_arr['NIP'],
                            );
                            $this->db->where('ID', $ProdiArr[$i]);
                            $this->db->update('db_academic.program_study', $dataSave);
                        }
                    }
                }


                $this->db->reset_query();
                $this->db->where('NIP', $data_arr['NIP']);
                $this->db->delete('db_employees.employees_sub_prodi');
                $this->db->reset_query();
                // Update sub prodi
                if (count($data_arr['listCheckedSubProdi']) > 0) {
                    for ($i = 0; $i < count($data_arr['listCheckedSubProdi']); $i++) {
                        $arrIns = array(
                            'NIP' => $data_arr['NIP'],
                            'ProdiID' => $data_arr['listCheckedSubProdi'][$i]
                        );
                        $this->db->insert('db_employees.employees_sub_prodi', $arrIns);
                    }
                }

                echo json_encode($rs);
                // return print_r(1);

            } else if ($data_arr['action'] == 'deleteEmployees') {
                $this->db->set('StatusEmployeeID', -2);
                $this->db->where('NIP', $data_arr['NIP']);
                $this->db->update('db_employees.employees');

                return print_r(1);
            } else if ($data_arr['action'] == 'deletePermanantEmployees') {
                $this->db->where('NIP', $data_arr['NIP']);
                $this->db->delete('db_employees.employees');
                return print_r(1);
            } else if ($data_arr['action'] == 'updateEmailEmployees') {

                $fillM = ($data_arr['StatusEmployeeID'] == 4 || $data_arr['StatusEmployeeID'] == '4') ? 'Email' : 'EmailPU';
                $this->db->set($fillM, $data_arr['Email']);
                $this->db->where('NIP', $data_arr['NIP']);
                $this->db->update('db_employees.employees');

                return print_r(1);
            } else if ($data_arr['action'] == 'showLecturerMonitoring') {

                $SemesterID = $data_arr['SemesterID'];
                $StatusEmployeeID = $data_arr['StatusEmployeeID'];
                $StatusLecturerID = $data_arr['StatusLecturerID'];
                $Start = $data_arr['Start'];
                $End = $data_arr['End'];

                $data = $this->m_api->showLecturerMonitoring($SemesterID, $StatusEmployeeID, $StatusLecturerID, $Start, $End);

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'getEmployeesFiles') {
                $NIP = $data_arr['NIP'];
                $data = $this->db->query('SELECT em.NIP AS NIPLec, em.Name, em.Photo, f.* FROM db_employees.employees em
                                                  LEFT JOIN db_employees.files f ON (f.NIP = em.NIP)
                                                  WHERE em.NIP = "' . $NIP . '" ')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'getDataRecapitulation') {

                $data = $this->m_hr->getDataRecapitulation($data_arr);

                return print_r(json_encode($data));
            }
        }
    }


    public function editAcademicData()
    {

        $data_arr = $this->getInputToken();
        $IDuser = $this->session->userdata('NIP');

        if ($data_arr['action'] == 'editAcademicS1') {
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
            $id_linkijazahs1 = $formInsert['id_linkijazahs1'];
            $id_linktranscripts1 = $formInsert['id_linktranscripts1'];

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
                'DateUpdate' => date('Y-m-d H:i:s'),
                'UserUpdate' => $IDuser
                //'LinkFiles' => $fileName
            );
            $this->db->where('NIP', $NIP);
            $this->db->where('ID', $id_linkijazahs1);  //file ijazah
            $this->db->update('db_employees.files', $dataUpdate);
            //------------------------------------------------\\
            $dataUpdate2 = array(
                'TypeAcademic' => $type,
                'NoIjazah' => $NoIjazah,
                'DateIjazah' => $DateIjazah,
                'NameUniversity' => $NameUniversity,
                'Major' => $Major,
                'ProgramStudy' => $ProgramStudy,
                'Grade' => $Grade,
                'TotalCredit' => $TotalCredit,
                'TotalSemester' => $TotalSemester,
                'DateUpdate' => date('Y-m-d H:i:s'),
                'UserUpdate' => $IDuser
                //'LinkFiles' => $file_trans
            );
            $this->db->where('NIP', $NIP);
            $this->db->where('ID', $id_linktranscripts1); //file transcript
            $this->db->update('db_employees.files', $dataUpdate2);

            return print_r(1);
        } else if ($data_arr['action'] == 'editAcademicS2') {
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
                //'LinkFiles' => $fileName,
                'DateUpdate' => date('Y-m-d H:i:s'),
                'UserUpdate' => $IDuser
            );
            $this->db->where('NIP', $NIP);
            $this->db->where('LinkFiles', $fileName);
            $this->db->update('db_employees.files', $dataUpdate);
            //------------------------------------------------\\
            $dataUpdate2 = array(
                'TypeAcademic' => $type,
                'NoIjazah' => $NoIjazah,
                'DateIjazah' => $DateIjazah,
                'NameUniversity' => $NameUniversity,
                'Major' => $Major,
                'ProgramStudy' => $ProgramStudy,
                'Grade' => $Grade,
                'TotalCredit' => $TotalCredit,
                'TotalSemester' => $TotalSemester,
                //'LinkFiles' => $file_trans
                'DateUpdate' => date('Y-m-d H:i:s'),
                'UserUpdate' => $IDuser
            );
            $this->db->where('NIP', $NIP);
            $this->db->where('LinkFiles', $file_trans);
            $this->db->update('db_employees.files', $dataUpdate2);
            return print_r(1);
        } else if ($data_arr['action'] == 'editAcademicS3') {

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
                //'LinkFiles' => $fileName
                'DateUpdate' => date('Y-m-d H:i:s'),
                'UserUpdate' => $IDuser
            );
            $this->db->where('NIP', $NIP);
            $this->db->where('LinkFiles', $fileName);
            $this->db->update('db_employees.files', $dataUpdate);
            //------------------------------------------------\\
            $dataUpdate2 = array(
                'TypeAcademic' => $type,
                'NoIjazah' => $NoIjazah,
                'DateIjazah' => $DateIjazah,
                'NameUniversity' => $NameUniversity,
                'Major' => $Major,
                'ProgramStudy' => $ProgramStudy,
                'Grade' => $Grade,
                'TotalCredit' => $TotalCredit,
                'TotalSemester' => $TotalSemester,
                //'LinkFiles' => $file_trans
                'DateUpdate' => date('Y-m-d H:i:s'),
                'UserUpdate' => $IDuser
            );
            $this->db->where('NIP', $NIP);
            $this->db->where('LinkFiles', $file_trans);
            $this->db->update('db_employees.files', $dataUpdate2);
            return print_r(1);
        } else if ($data_arr['action'] == 'EditFilesDocument') {

            $formInsert = (array) $data_arr['formInsert'];

            $NIP = $formInsert['formNIP'];
            $type_files = $formInsert['type_files'];
            $NoDocument = strtoupper($formInsert['NoDocument']);
            $DescriptionFile = $formInsert['DescriptionFile'];
            $DateDocument = $formInsert['DateDocument'];
            $type = $formInsert['typeotherfiles'];
            $idlinkfiles = $formInsert['idlinkfiles'];
            $linkotherfile = $formInsert['linkotherfile'];

            $dataUpdate = array(
                'No_Document' => $NoDocument,
                'TypeFiles' => $type_files,
                'Date_Files' => $DateDocument,
                'Description_Files' => $DescriptionFile,
                'LinkFiles' => $linkotherfile,
                'DateUpdate' => date('Y-m-d H:i:s'),
                'UserUpdate' => $IDuser
            );
            $this->db->where('NIP', $NIP);
            $this->db->where('ID', $idlinkfiles);
            $this->db->update('db_employees.files', $dataUpdate);
            return print_r(1);
        }
    }

    public function crudAcademicData()
    {

        $data_arr = $this->getInputToken();
        $IDuser = $this->session->userdata('NIP');

        if (count($data_arr) > 0) {

            if ($data_arr['action'] == 'addAcademicS1') {

                $formInsert = (array) $data_arr['formInsert'];
                $type = 'S1';
                $Colomija = 'IjazahS1';
                $Colomtra = 'TranscriptS1';
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

                $Get_MasterFiles = $this->m_master->MasterfileStatus($Colomija);
                $Get_MasterFiles2 = $this->m_master->MasterfileStatus($Colomtra);
                $dataSave = array(
                    'NIP' => $NIP,
                    'TypeAcademic' => $type,
                    'TypeFiles' => $Get_MasterFiles[0]['ID'],
                    'NoIjazah' => $NoIjazah,
                    'DateIjazah' => $DateIjazah,
                    'NameUniversity' => $NameUniversity,
                    'Major' => $Major,
                    'ProgramStudy' => $ProgramStudy,
                    'Grade' => $Grade,
                    'TotalCredit' => $TotalCredit,
                    'TotalSemester' => $TotalSemester,
                    'LinkFiles' => $fileName,
                    //'TranscriptFile' => $file_trans,
                    'UserCreate' => $IDuser
                );
                $this->db->insert('db_employees.files', $dataSave);
                $dataSave2 = array(
                    'NIP' => $NIP,
                    'TypeAcademic' => $type,
                    'TypeFiles' => $Get_MasterFiles2[0]['ID'],
                    'NoIjazah' => $NoIjazah,
                    'DateIjazah' => $DateIjazah,
                    'NameUniversity' => $NameUniversity,
                    'Major' => $Major,
                    'ProgramStudy' => $ProgramStudy,
                    'Grade' => $Grade,
                    'TotalCredit' => $TotalCredit,
                    'TotalSemester' => $TotalSemester,
                    'LinkFiles' => $file_trans,
                    'UserCreate' => $IDuser
                );
                $this->db->insert('db_employees.files', $dataSave2);
                return print_r(1);
            } else if ($data_arr['action'] == 'addAcademicS2') {

                $formInsert = (array) $data_arr['formInsert'];
                $type = 'S2';
                $Colomija = 'IjazahS2';
                $Colomtra = 'TranscriptS2';
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
                $Get_MasterFiles = $this->m_master->MasterfileStatus($Colomija);
                $Get_MasterFiles2 = $this->m_master->MasterfileStatus($Colomtra);
                $dataSave = array(
                    'NIP' => $NIP,
                    'TypeAcademic' => $type,
                    'TypeFiles' => $Get_MasterFiles[0]['ID'],
                    'NoIjazah' => $NoIjazah,
                    'DateIjazah' => $DateIjazah,
                    'NameUniversity' => $NameUniversity,
                    'Major' => $Major,
                    'ProgramStudy' => $ProgramStudy,
                    'Grade' => $Grade,
                    'TotalCredit' => $TotalCredit,
                    'TotalSemester' => $TotalSemester,
                    'LinkFiles' => $fileName,
                    'UserCreate' => $IDuser
                );
                $this->db->insert('db_employees.files', $dataSave);
                $dataSave2 = array(
                    'NIP' => $NIP,
                    'TypeAcademic' => $type,
                    'TypeFiles' => $Get_MasterFiles2[0]['ID'],
                    'NoIjazah' => $NoIjazah,
                    'DateIjazah' => $DateIjazah,
                    'NameUniversity' => $NameUniversity,
                    'Major' => $Major,
                    'ProgramStudy' => $ProgramStudy,
                    'Grade' => $Grade,
                    'TotalCredit' => $TotalCredit,
                    'TotalSemester' => $TotalSemester,
                    'LinkFiles' => $file_trans,
                    'UserCreate' => $IDuser
                );
                $this->db->insert('db_employees.files', $dataSave2);
                return print_r(1);
            } else if ($data_arr['action'] == 'addAcademicS3') {

                $formInsert = (array) $data_arr['formInsert'];
                $type = 'S3';
                $Colomija = 'IjazahS3';
                $Colomtra = 'TranscriptS3';
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
                $Get_MasterFiles = $this->m_master->MasterfileStatus($Colomija);
                $Get_MasterFiles2 = $this->m_master->MasterfileStatus($Colomtra);
                $dataSave = array(
                    'NIP' => $NIP,
                    'TypeAcademic' => $type,
                    'TypeFiles' => $Get_MasterFiles[0]['ID'],
                    'NoIjazah' => $NoIjazah,
                    'DateIjazah' => $DateIjazah,
                    'NameUniversity' => $NameUniversity,
                    'Major' => $Major,
                    'ProgramStudy' => $ProgramStudy,
                    'Grade' => $Grade,
                    'TotalCredit' => $TotalCredit,
                    'TotalSemester' => $TotalSemester,
                    'LinkFiles' => $fileName,
                    'UserCreate' => $IDuser
                );
                $this->db->insert('db_employees.files', $dataSave);
                $dataSave2 = array(
                    'NIP' => $NIP,
                    'TypeAcademic' => $type,
                    'TypeFiles' => $Get_MasterFiles2[0]['ID'],
                    'NoIjazah' => $NoIjazah,
                    'DateIjazah' => $DateIjazah,
                    'NameUniversity' => $NameUniversity,
                    'Major' => $Major,
                    'ProgramStudy' => $ProgramStudy,
                    'Grade' => $Grade,
                    'TotalCredit' => $TotalCredit,
                    'TotalSemester' => $TotalSemester,
                    'LinkFiles' => $file_trans,
                    'UserCreate' => $IDuser
                );
                $this->db->insert('db_employees.files', $dataSave2);
                return print_r(1);
            } else if ($data_arr['action'] == 'AddFilesDocument') {

                $formInsert = (array) $data_arr['formInsert'];
                $NIP = $formInsert['NIP'];
                $IDuser = $this->session->userdata('NIP');
                $NoDocument = strtoupper($formInsert['NoDocument']);
                $DateDocument = $formInsert['DateDocument'];
                $Colom = $formInsert['type'];
                //$kat_otherfiles = $formInsert['kat_otherfiles'];
                $DescriptionFile = $formInsert['DescriptionFile'];
                $fileName = $formInsert['fileName'];
                $Get_MasterFiles = $this->m_master->MasterfileStatus($Colom);
                $dataSave = array(
                    'NIP' => $NIP,
                    'TypeFiles' => $Get_MasterFiles[0]['ID'],
                    //'ID_OtherFiles' => $kat_otherfiles,
                    'No_Document' => $NoDocument,
                    'Date_Files' => $DateDocument,
                    'Description_Files' => $DescriptionFile,
                    'LinkFiles' => $fileName,
                    'UserCreate' => $IDuser
                );
                $this->db->insert('db_employees.files', $dataSave);
                return print_r(1);
            }
        } //end count
    }


    public function loadUniversity()
    {

        $term = $this->input->get('term');
        $data_result = $this->db->query('SELECT * FROM db_research.university auts WHERE auts.Name_University LIKE "%' . $term . '%" ')->result_array();

        return print_r(json_encode($data_result));
    }

    public function loadMajorEmployee()
    {

        $term = $this->input->get('term');
        $data_result = $this->db->query('SELECT * FROM db_employees.major_programstudy_employees auts WHERE auts.Name_MajorProgramstudy LIKE "%' . $term . '%" ')->result_array();

        return print_r(json_encode($data_result));
    }

    public function loadmasteruniversity()
    {

        $data_arr = $this->getInputToken();
        $IDuser = $this->session->userdata('NIP');

        if ($data_arr['action'] == 'readmasterunivxxx') {

            $data = $this->db->query('SELECT ID, Name_University, Code_University FROM db_research.university')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'readmasteruniv') {

            $requestData = $_REQUEST;

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' WHERE ea.Name_University LIKE "%' . $search . '%" ';
            }

            $queryDefault = 'SELECT ea.ID, ea.Name_University FROM db_research.university ea ' . $dataSearch;

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $btnAction = '<div class="btn-group btnAction">
                                    <button type="button" class="btn btn-sm btn-default dropdown-toggle dropdown-menu-left" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="fa fa-pencil"></i> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0);" class="btnEditAE" data-no="' . $no . '" disabled> <i class="fa fa fa-edit"></i> Edit</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="javascript:void(0);" class="btnRemove" data-id="' . $row['ID'] . '" disabled> <i class="fa fa fa-trash"></i> Delete</a></li>
                                    </ul>
                                    </div>';

                $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Name_University'] . '</div>';
                //$nestedData[] = '<div style="text-align:center;">'.$btnAction.'</div>';

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
        } else if ($data_arr['action'] == 'update_mstruniv') {

            $master_nameuniv = ucwords($data_arr['master_nameuniv']);

            $dataAttdS = $this->db->query('SELECT * FROM db_research.university
                                          WHERE Name_University = "' . $master_nameuniv . '" ')->result_array();

            if (count($dataAttdS) > 0) {
                return print_r(0);
            } else {

                $dataSave = array(
                    'Name_University' => $master_nameuniv,
                    'UserCreate' => $IDuser
                );
                $this->db->insert('db_research.university', $dataSave);
                return print_r(1);
            }
        } else if ($data_arr['action'] == 'update_mstermajor') {

            $master_namemajor = ucwords($data_arr['master_namemajor']);
            $dataAttdS = $this->db->query('SELECT * FROM db_employees.major_programstudy_employees
                                          WHERE Name_MajorProgramstudy = "' . $master_namemajor . '" ')->result_array();

            if (count($dataAttdS) > 0) {
                return print_r(0);
            } else {
                $dataSave = array(
                    'Name_MajorProgramstudy' => $master_namemajor,
                    'UserCreate' => $IDuser
                );
                $this->db->insert('db_employees.major_programstudy_employees', $dataSave);
                return print_r(1);
            }
        } else if ($data_arr['action'] == 'update_mster_katother') {

            $name_katother = ucwords($data_arr['name_katother']);
            $typefiles = preg_replace('/\s+/', '_', $data_arr['name_sort']);

            $dataAttdS = $this->db->query('SELECT * FROM db_employees.master_files
                                          WHERE TypeFiles = "' . $typefiles . '" OR NameFiles = "' . $name_katother . '" ')->result_array();

            if (count($dataAttdS) > 0) {
                return print_r(0);
            } else {
                $dataSave = array(
                    'TypeFiles' => $typefiles,
                    'NameFiles' => $name_katother
                );
                $this->db->insert('db_employees.master_files', $dataSave);
                return print_r(1);
            }
        } else if ($data_arr['action'] == 'readmastermajor') {

            $requestData = $_REQUEST;

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = 'WHERE ea.Name_MajorProgramstudy LIKE "%' . $search . '%" ';
            }

            $queryDefault = 'SELECT ea.ID, ea.Name_MajorProgramstudy FROM db_employees.major_programstudy_employees ea ' . $dataSearch;
            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $btnAction = '<div class="btn-group btnAction">
                                    <button type="button" class="btn btn-sm btn-default dropdown-toggle dropdown-menu-left" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                       <i class="fa fa-pencil"></i> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0);" class="btnEditAE" data-no="' . $no . '" disabled> <i class="fa fa fa-edit"></i> Edit</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="javascript:void(0);" class="btnRemove" data-id="' . $row['ID'] . '" disabled> <i class="fa fa fa-trash"></i> Delete</a></li>
                                    </ul>
                                    </div>';

                $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Name_MajorProgramstudy'] . '</div>';
                //$nestedData[] = '<div style="text-align:center;">'.$btnAction.'</div>';

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
        }
    }

    public function confirm_requestdocument()
    {

        $data_arr = $this->getInputToken();
        $IDuser = $this->session->userdata('NIP');

        if ($data_arr['action'] == 'Approved') {
            $formInsert = (array) $data_arr['formInsert'];
            $requestID = $formInsert['requestID'];
            $status = '1';
            $dates = date("Y-m-d H:i:s");

            $dataSave = array(
                'ConfirmStatus' => $status,
                'DateConfirm' => $dates,
                'UserConfirm' => $IDuser
            );
            $this->db->where('IDRequest', $requestID);
            $this->db->update('db_employees.request_document', $dataSave);
            return print_r(1);
        }
        if ($data_arr['action'] == 'Rejected') {
            $formInsert = (array) $data_arr['formInsert'];
            $requestID = $formInsert['requestID'];
            $status = '-1';
            $dates = date("Y-m-d H:i:s");

            $dataSave = array(
                'ConfirmStatus' => $status,
                'DateConfirm' => $dates,
                'UserConfirm' => $IDuser
            );
            $this->db->where('IDRequest', $requestID);
            $this->db->update('db_employees.request_document', $dataSave);
            return print_r(1);
        }
    }


    public function crudversion()
    {

        $data_arr = $this->getInputToken();
        $IDuser = $this->session->userdata('NIP');

        if ($data_arr['action'] == 'AddGroupModule') {
            $formInsert = (array) $data_arr['formInsert'];

            $Namegroup = strtoupper($formInsert['Namegroup']);
            $division = $formInsert['division'];

            $getgroupmodule = $this->db->get_where('db_it.group_module', array('NameGroup' => $Namegroup, 'IDDivision' => $division))->result_array();

            if (count($getgroupmodule) > 0) {
                return print_r(0);
            } else {
                $dataSave1 = array(
                    'NameGroup' => $Namegroup,
                    'IDDivision' => $division
                );
                $this->db->insert('db_it.group_module', $dataSave1);
                return print_r(1);
            }
        } else if ($data_arr['action'] == 'AddModule') {

            $formInsert = (array) $data_arr['formInsert'];

            $IDGroups = $formInsert['IDGroups'];
            $Namemodule = strtoupper($formInsert['Namemodule']);
            $Description = $formInsert['Descriptiongroup'];

            $getdatamodules = $this->db->get_where('db_it.module', array('NameModule' => $Namemodule))->result_array();
            if (count($getdatamodules) > 0) {
                return print_r(0);
            } else {
                $dataSave1 = array(
                    'NameModule' => $Namemodule,
                    'IDGroup' => $IDGroups,
                    'Description' => $Description
                );
                $this->db->insert('db_it.module', $dataSave1);
                return print_r(1);
            }
        } else if ($data_arr['action'] == 'AddVersion') {

            $formInsert = (array) $data_arr['formInsert'];

            $filternamepic = $formInsert['filternamepic'];
            $filterStatusModule = $formInsert['filterStatusModule'];
            $Noversion = strtoupper($formInsert['Noversion']);
            $Descriptionversion = $formInsert['Descriptionversion'];

            $getdataversion = $this->db->get_where('db_it.version', array('Version' => $Noversion))->result_array();
            if (count($getdataversion) > 0) {
                return print_r(0);
            } else {
                $dataSave1 = array(
                    'Version' => $Noversion,
                    'PIC' => $filternamepic,
                    'UpdateBy' => $IDuser
                );
                $this->db->insert('db_it.version', $dataSave1);
                $insert_id_logging = $this->db->insert_id();

                $dataSave2 = array(
                    'IDVersion' => $insert_id_logging,
                    'IDModule' => $filterStatusModule,
                    'Description' => $Descriptionversion
                );
                $this->db->insert('db_it.version_detail', $dataSave2);
                return print_r(1);
            }
        } else if ($data_arr['action'] == 'EditVersion') {

            $formInsert = (array) $data_arr['formInsert'];

            $selectmodule = $formInsert['selectmodule'];
            $selectpic = $formInsert['selectpic'];
            $noversion = $formInsert['noversion'];
            $VersionID = strtoupper($formInsert['VersionID']);
            $Descriptionversion = $formInsert['Descriptionversion'];

            $dataSave1 = array(
                'PIC' => $selectpic,
                'Version' => $noversion,
                'UpdateBy' => $IDuser
            );
            $this->db->where('IDVersion', $VersionID);
            $this->db->update('db_it.version', $dataSave1);

            $dataSave2 = array(
                'IDModule' => $selectmodule,
                'Description' => $Descriptionversion
            );
            $this->db->where('IDVersion', $VersionID);
            $this->db->update('db_it.version_detail', $dataSave2);
            return print_r(1);
        } else if ($data_arr['action'] == 'EditGroupModule') {  //edit data module

            $formInsert = (array) $data_arr['formInsert'];

            $idnamegroup = $formInsert['idnameegroup'];
            $idmodule = $formInsert['idmodule'];
            $IDGroupedit = $formInsert['IDGroupedit'];
            $Description = $formInsert['Descriptiongroup'];

            $dataUpdate = array(
                'IDGroup' => $idnamegroup,
                'NameModule' => $idmodule,
                'Description' => $Description

            );
            $this->db->where('IDModule', $IDGroupedit);
            $this->db->update('db_it.module', $dataUpdate);
            return print_r(1);
        } else if ($data_arr['action'] == 'EditGroups') {  //edit data group

            $formInsert = (array) $data_arr['formInsert'];

            $iddivision = $formInsert['iddivision'];
            $IDGroup = $formInsert['IDGroupedit'];
            $Namegroup = $formInsert['Namegroup'];

            $dataUpdate = array(
                'NameGroup' => $Namegroup,
                'IDDivision' => $iddivision

            );
            $this->db->where('IDGroup', $IDGroup);
            $this->db->update('db_it.group_module', $dataUpdate);
            return print_r(1);
        }
    }


    public function upload_fileAcademic($fileName, $formData)
    {

        //$fileName = $this->input->get('fileName');
        $Colom = $this->input->get('c');
        $User = $this->input->get('u');
        //print_r($fileName);
        //print_r($formData);

        $config['upload_path']          = './uploads/files/';
        $config['allowed_types']        = '*';
        $config['max_size']             = 8000; // 8 mb
        $config['file_name']            = $fileName;

        if (is_file('./uploads/files/' . $fileName)) {
            unlink('./uploads/files/' . $fileName);
        }
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('userfile')) {
            $error = array('error' => $this->upload->display_errors());
            return print_r(json_encode($error));
        } else {

            $success = array('success' => $this->upload->data());
            $success['success']['formGrade'] = 0;
            // Cek apakah di db sudah ada
            $dataNIP = $this->db->get_where('db_employees.files', array('NIP' => $User))->result_array();
            $dataUpdate = array(
                $Colom => $fileName
            );
            if (count($dataNIP) > 0) {
                $this->db->where('NIP', $User);
                $this->db->update('db_employees.files', $dataUpdate);
            } else {
                $dataUpdate['NIP'] = $User;
                $this->db->insert('db_employees.files', $dataUpdate);
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
        echo json_encode(array('count' => $generateCount, 'data' => $generate));
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

    public function crudScore()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $ScheduleID = $data_arr['ScheduleID'];
                $SemesterID = $data_arr['SemesterID'];
                $data = $this->m_api->__getScore($SemesterID, $ScheduleID);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'grade') {
                $Score = $data_arr['Score'];
                $data = $this->m_api->__getGrade($Score);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'checkGrade') {
                $data = $this->db->get_where(
                    'db_academic.grade_course',
                    array('ScheduleID' => $data_arr['ID']),
                    1
                )->result_array();

                $result = array(
                    'Status' => 0,
                    'Details' => $data
                );
                if (count($data) > 0) {
                    if (
                        $data[0]['Silabus'] != '' && $data[0]['Silabus'] != null &&
                        $data[0]['SAP'] != '' && $data[0]['SAP'] != null &&
                        $data[0]['Assigment'] != '' && $data[0]['Assigment'] != null &&
                        $data[0]['UTS'] != '' && $data[0]['UTS'] != null &&
                        $data[0]['UAS'] != '' && $data[0]['UAS'] != null &&
                        $data[0]['Status'] == '2'
                    ) {
                        $result['Status'] = 1;
                        $result['Details'] = $data[0];
                    }
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'update') {

                // Update Schedule
                $this->db->set('TotalAssigment', $data_arr['TotalAssigment']);
                $this->db->where('ID', $data_arr['ScheduleID']);
                $this->db->update('db_academic.schedule');

                $formUpdate = (array) $data_arr['formUpdate'];
                for ($s = 0; $s < count($formUpdate); $s++) {
                    $dataF = (array) $formUpdate[$s];

                    $DB_Student = $dataF['DB_Student'];
                    $ID = $dataF['ID'];

                    $dataToUpdate = (array)$dataF['dataForm'];

                    //                    print_r($dataToUpdate);

                    $this->db->where('ID', $ID);
                    $this->db->update($DB_Student . '.study_planning', $dataToUpdate);
                    $this->db->reset_query();

                    // get2Log
                    $dataLog = $this->db->get_where(
                        $DB_Student . '.study_planning',
                        array('ID' => $ID)
                    )->result_array();
                    $ins = array(
                        'NPM' => $dataLog[0]['NPM'],
                        'SemesterID' => $dataLog[0]['SemesterID'],
                        'ScheduleID' => $dataLog[0]['ScheduleID'],
                        'Description' => json_encode($dataToUpdate),
                        'UpdatedBy' => $this->session->userdata('NIP'),
                    );
                    $this->db->insert('db_academic.log_score', $ins);
                    $this->db->reset_query();
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'getGrade') {
                $ScheduleID = $data_arr['ScheduleID'];
                $data = $this->m_api->__getGradeSchedule($ScheduleID);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'gradeUpdate') {
                //                $this->db->set('Status', $data_arr['Status']);
                $data_update = array('ReasonNotApprove' => '', 'Status' => $data_arr['Status']);
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.grade_course', $data_update);
                return print_r(1);
            } else if ($data_arr['action'] == 'dataCourse') {

                $data = $this->m_api->getDataCourse2Score(
                    $data_arr['SemesterID'],
                    $data_arr['ProdiID'],
                    $data_arr['CombinedClasses'],
                    $data_arr['IsSemesterAntara']
                );
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'updateNotApprove') {

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

    public function crudAttendance()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->m_api->__getDataAttendance($data_arr['ScheduleID']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'getAttendance') {

                $data = $this->m_api->__getAttendanceSchedule($data_arr['AttendanceID']);

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'getAttdLecturers') {
                $ID = $data_arr['ID'];
                $No = $data_arr['No'];
                $data = $this->db->get_where(
                    'db_academic.attendance',
                    array('ID' => $ID)
                )->result_array();


                $coor = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule s
                                            LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                            WHERE s.ID = "' . $data[0]['ScheduleID'] . '"
                                              ')->result_array();

                $teamt = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule_team_teaching stt
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                    WHERE stt.ScheduleID = "' . $data[0]['ScheduleID'] . '"
                                                    ')->result_array();

                if (count($teamt) > 0) {
                    for ($t = 0; $t < count($teamt); $t++) {
                        array_push($coor, $teamt[$t]);
                    }
                }

                $res = array(
                    //                    'NIP' => $data[0]['NIP'.$No],
                    'BAP' => $data[0]['BAP' . $No],
                    //                    'Date' => $data[0]['Date'.$No],
                    //                    'In' => $data[0]['In'.$No],
                    //                    'Out' => $data[0]['Out'.$No],
                    'Details' => $data,
                    'DetailLecturers' => $coor
                );
                return print_r(json_encode($res));
            } else if ($data_arr['action'] == 'UpdtAttdLecturers') {

                $ID = $data_arr['ID'];
                $No = $data_arr['No'];

                // Cek apakah sudah ada diabsen atau belum
                $insertAttdLecturer = (array) $data_arr['insertAttdLecturer'];
                $dataAttdLec = $this->db->get_where(
                    'db_academic.attendance_lecturers',
                    array(
                        'ID_Attd' => $insertAttdLecturer['ID_Attd'],
                        'NIP' => $insertAttdLecturer['NIP'],
                        'Meet' => $insertAttdLecturer['Meet']
                    ),
                    1
                )->result_array();

                if (count($dataAttdLec) <= 0) {
                    $this->db->insert('db_academic.attendance_lecturers', (array) $data_arr['insertAttdLecturer']);
                } else {
                    // Update Attendance Lecturer
                    $this->db->where('ID', $dataAttdLec[0]['ID']);
                    $this->db->update('db_academic.attendance_lecturers', (array) $data_arr['insertAttdLecturer']);
                }

                $dataUpdate = array(
                    'Meet' . $No => '1',
                    'BAP' . $No => $data_arr['BAP']
                );

                $this->db->where('ID', $ID);
                $this->db->update('db_academic.attendance', $dataUpdate);

                return print_r(1);
            } else if ($data_arr['action'] == 'DeleteAttdLecturers') {
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.attendance_lecturers');
                return print_r(1);
            } else if ($data_arr['action'] == 'filterPresensi') {

                if ($data_arr['CombinedClasses'] == '0') {

                    $data = $this->db->query('SELECT s.* FROM db_academic.schedule s
                                              LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                              WHERE s.SemesterID = "' . $data_arr['SemesterID'] . '"
                                              AND CombinedClasses = "0"
                                              AND sdc.ProdiID = "' . $data_arr['ProdiID'] . '"
                                              ORDER BY s.ClassGroup ASC')->result_array();


                    $result = $data;
                } else {
                    $data_where = array(
                        'SemesterID' => $data_arr['SemesterID'],
                        'CombinedClasses' => '1'
                    );
                    $data = $this->db->order_by('ClassGroup', 'ASC')
                        ->get_where(
                            'db_academic.schedule',
                            $data_where
                        )->result_array();

                    $result = $data;
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'getAttdStudents') {
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $SDID = $data_arr['SDID'];
                $Meeting = $data_arr['Meeting'];
                $data = $this->m_api->__getStudensAttd2Edit($SemesterID, $ScheduleID, $SDID, $Meeting);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'getAttdStudentsToEdit') {
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $SDID = $data_arr['SDID'];
                $Meeting = $data_arr['Meeting'];
                $data = $this->m_api->__getStudensAttd($SemesterID, $ScheduleID, $SDID, $Meeting);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'addAttdStudents') {
                $dataUpdate = (array) $data_arr['dataUpdate'];
                //                print_r($dataUpdate);
                for ($u = 0; $u < count($dataUpdate); $u++) {
                    $dataObj = (array) $dataUpdate[$u];

                    $d_updt = array(
                        'M' . $dataObj['Meeting'] => $dataObj['Status'],
                        'D' . $dataObj['Meeting'] => $dataObj['Description']
                    );

                    $this->db->where('ID', $dataObj['ID']);
                    $this->db->update('db_academic.attendance_students', $d_updt);
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'blastAttendance') {
                $SemesterID = $data_arr['SemesterID'];
                $Meet = $data_arr['Meet'];
                $Status = $data_arr['Status'];

                $set = ' M' . $Meet . ' = ' . $Status . ' ';

                $this->db->query('UPDATE db_academic.attendance_students
                                    SET ' . $set . '
                                    WHERE ID_Attd IN (SELECT ID FROM db_academic.attendance
                                    WHERE SemesterID = "' . $SemesterID . '")');

                return print_r(1);
            } else if ($data_arr['action'] == 'monitoringLecturer') {
                $SemesterID = $data_arr['SemesterID'];
                $ProdiID = $data_arr['ProdiID'];

                $data = $this->m_api->__getMonitoringAttdLecturer($SemesterID, $ProdiID);

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'DeleteAttendance') {

                // Delete Attendance Lecturer
                $this->db->where(array('ID_Attd' => $data_arr['ID_Attd'], 'Meet' => $data_arr['Meet']));
                $this->db->delete('db_academic.attendance_lecturers');

                // Set Null di Student
                $this->db->set('M' . $data_arr['Meet'], null);
                $this->db->where('ID_Attd', $data_arr['ID_Attd']);
                $this->db->update('db_academic.attendance_students');

                // Set Null di Attendance
                $this->db->set('Meet' . $data_arr['Meet'], null);
                $this->db->where('ID', $data_arr['ID_Attd']);
                $this->db->update('db_academic.attendance');

                return print_r(1);
            } else if ($data_arr['action'] == 'getStdAttendance') {

                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                $data = $this->m_api->getStudentsAttendance($SemesterID, $ScheduleID);

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'delAttendanceStudents') {
                $NPM = $data_arr['NPM'];
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $ID_Attd = $data_arr['ID_Attd'];
                $ID_Attd_S = $data_arr['ID_Attd_S'];
                $db_student = $data_arr['db_student'];

                // Cek apakah double di attendance
                $dataAtdStd = $this->db
                    ->get_where('db_academic.attendance_students', $arrCheck = array(
                        'NPM' => $NPM, 'ID_Attd' => $ID_Attd
                    ))->result_array();
                if (count($dataAtdStd) > 1) {
                    $this->db->where('ID', $ID_Attd_S);
                    $this->db->delete('db_academic.attendance_students');
                    $res = array(
                        'Status' => 1,
                        'Msg' => 'Delete Success'
                    );
                } else {
                    // Cek apakah di KSM tidak ada, jika ada maka tidak dapat di hapus
                    $dataKSM = $this->db->get_where($db_student . '.study_planning', array(
                        'NPM' => $NPM, 'SemesterID' => $SemesterID, 'ScheduleID' => $ScheduleID
                    ))->result_array();
                    if (count($dataKSM) > 0) {
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
            } else if ($data_arr['action'] == 'addAttendanceStudents') {

                $NPM = $data_arr['NPM'];
                $DB_Student = $data_arr['DB_Student'];
                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];

                // Cek apakah di KSM ada
                $dataKSM = $this->db->get_where($DB_Student . '.study_planning', array(
                    'NPM' => $NPM,
                    'SemesterID' => $SemesterID,
                    'ScheduleID' => $ScheduleID
                ))->result_array();

                if (count($dataKSM) > 0) {
                    // Cek apakah sudah ada di attendance
                    $dataAttdS = $this->db->query('SELECT * FROM db_academic.attendance attd
                                                      LEFT JOIN db_academic.attendance_students attds ON (attds.ID_Attd = attd.ID)
                                                      WHERE attd.SemesterID = "' . $SemesterID . '"
                                                      AND attd.ScheduleID = "' . $ScheduleID . '"
                                                       AND attds.NPM = "' . $NPM . '" ')->result_array();

                    if (count($dataAttdS) > 0) {
                        $res = array(
                            'Status' => 0,
                            'Msg' => 'Student Attendance is Exist'
                        );
                    } else {
                        $dataAttd = $this->db->query('SELECT * FROM db_academic.attendance attd
                                                      WHERE attd.SemesterID = "' . $SemesterID . '"
                                                      AND attd.ScheduleID = "' . $ScheduleID . '"
                                                       ')->result_array();
                        if (count($dataAttd) > 0) {
                            for ($r = 0; $r < count($dataAttd); $r++) {
                                $arrIns = array(
                                    'ID_Attd' => $dataAttd[$r]['ID'],
                                    'NPM' => $NPM
                                );
                                $this->db->insert('db_academic.attendance_students', $arrIns);
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

            // Modify === 9 Feb 2019
            else if ($data_arr['action'] == 'readAttendance') {
                $ID_Attd = $data_arr['ID_Attd'];

                // Get Attendance Student
                $dataSet = $this->db->query('SELECT attd_s.*, atus.Name FROM db_academic.attendance_students attd_s
                                                    LEFT JOIN db_academic.auth_students atus ON (atus.NPM = attd_s.NPM)
                                                    WHERE attd_s.ID_Attd = "' . $ID_Attd . '" ')->result_array();


                return print_r(json_encode($dataSet));
            }
        }
    }


    public function crudScheduleExchange()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'readExchange') {
                $ID_Attd = $data_arr['ID_Attd'];
                $ScheduleID = $data_arr['ScheduleID'];
                $SDID = $data_arr['SDID'];
                $Meeting = $data_arr['Meeting'];

                $data = $this->m_api->__getdataExchange($ID_Attd, $ScheduleID, $SDID, $Meeting);

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'addSceduleEx') {
                $dataInsert = (array) $data_arr['dataInsert'];

                // Cek Apakah sudah ada atau belum
                $dataWhere = array(
                    'ID_Attd' => $dataInsert['ID_Attd'],
                    'Meeting' => $dataInsert['Meeting']
                );

                $dataC = $this->db->get_where(
                    'db_academic.schedule_exchange',
                    $dataWhere,
                    1
                )->result_array();

                if (count($dataC) > 0) {
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
                    $this->db->insert('db_academic.schedule_exchange', $dataInsert);
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'deleteSceduleEx') {
                $dataWhere = array(
                    'ID_Attd' => $data_arr['ID_Attd'],
                    'Meeting' => $data_arr['Meeting']
                );
                $dataC = $this->db->get_where(
                    'db_academic.schedule_exchange',
                    $dataWhere
                )->result_array();

                if (count($dataC) > 0) {
                    $this->db->delete('db_academic.schedule_exchange', $dataWhere);
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'readBySemesterID') {
                $SemesterID = $data_arr['SemesterID'];
                $Status = $data_arr['Status'];
                $Start = $data_arr['Start'];
                $End = $data_arr['End'];
                $data = $this->m_api->getExchangeBySmtID($SemesterID, $Status, $Start, $End);
                return print_r(json_encode($data));
            }
        }
    }

    public function crudKRSOnline()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'checkCountSeat') {
                $whereCheck = (array) $data_arr['whereCheck'];
                $query = $this->db->get_where('db_academic.std_krs', $whereCheck)->result_array();
                return print_r(json_encode($query));
            } else if ($data_arr['action'] == 'add') {
                $formData = (array) $data_arr['formData'];
                $this->db->insert('db_academic.std_krs', $formData);
                $insert_id = $this->db->insert_id();

                // Masukan ke dalam attd_students
                $SemesterID = $formData['SemesterID'];
                $ScheduleID = $formData['ScheduleID'];
                $dataAttd = $this->db->get_where(
                    'db_academic.attendance',
                    array(
                        'SemesterID' => $SemesterID,
                        'ScheduleID' => $ScheduleID
                    )
                )->result_array();

                if (count($dataAttd) > 0) {
                    $NPM = $formData['NPM'];

                    for ($a = 0; $a < count($dataAttd); $a++) {
                        $dataInsAttd = array(
                            'ID_Attd' => $dataAttd[$a]['ID'],
                            'NPM' => $NPM
                        );
                        $this->db->insert('db_academic.attendance_students', $dataInsAttd);
                    }
                }

                // Tambahkan ke KSMnya
                $this->m_api->insertToMainKRS($insert_id, $formData['TypeSP'], $data_arr['Student_DB']);
                return print_r($insert_id);
            } else if ($data_arr['action'] == 'deleteKRS') {

                $SemesterID = $data_arr['SemesterID'];
                $ScheduleID = $data_arr['ScheduleID'];
                $NPM = $data_arr['NPM'];
                $Student_DB = $data_arr['Student_DB'];

                // Cek di attendance untuk di delete
                $dataAttd = $this->db->get_where(
                    'db_academic.attendance',
                    array(
                        'SemesterID' => $SemesterID,
                        'ScheduleID' => $ScheduleID
                    )
                )->result_array();

                if (count($dataAttd) > 0) {
                    for ($a = 0; $a < count($dataAttd); $a++) {
                        $this->db->where(array('ID_Attd' => $dataAttd[0]['ID'], 'NPM' => $NPM));
                        $this->db->delete('db_academic.attendance_students');
                    }
                }

                // tabel std_krs
                $dataStdKRS = $this->db->get_where('db_academic.std_krs', array(
                    'SemesterID' => $SemesterID,
                    'ScheduleID' => $ScheduleID,
                    'NPM' => $NPM
                ))->result_array();
                if (count($dataStdKRS) > 0) {
                    for ($k = 0; $k < count($dataStdKRS); $k++) {
                        $this->db->where('KRSID', $dataStdKRS[$k]['ID']);
                        $this->db->delete('db_academic.std_krs_comment');

                        $this->db->where('ID', $dataStdKRS[$k]['ID']);
                        $this->db->delete('db_academic.std_krs');
                    }
                }

                // Cek apakah sudah jadi KSM ataubelum
                $this->db->where(array('SemesterID' => $SemesterID, 'NPM' => $NPM, 'ScheduleID' => $ScheduleID));
                $this->db->delete($Student_DB . '.study_planning');

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

    public function getStatusEmployee2()
    {
        $generate = $this->db->query('SELECT * FROM db_employees.employees_status
              WHERE IDStatus != -2 AND (Type = "emp" OR Type = "both") ORDER BY IDStatus DESC')->result_array();
        echo json_encode($generate);
    }

    public function getStatusLecturer2()
    {
        $generate = $this->db->query('SELECT * FROM db_employees.employees_status
              WHERE IDStatus != -2 AND (Type = "lec" OR Type = "both") ORDER BY IDStatus DESC')->result_array();
        echo json_encode($generate);
    }

    public function searchnip_employees($NIP)
    {
        $generate = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $NIP);
        echo json_encode($generate);
    }

    public function cek_deadlineBPPSKS()
    {
        $this->load->model('finance/m_finance');
        $arr = array('result' => '', 'msg' => '');
        $input = $this->getInputToken();
        $fieldCek = $input['fieldCek'];
        $SemesterID = $input['Semester'];
        // $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
        // $SemesterID = $SemesterID[0]['ID'];
        $getDeadlineTagihanDB = $this->m_finance->getDeadlineTagihanDB($fieldCek, $SemesterID);
        $dateFieldCek = $getDeadlineTagihanDB . ' 23:59:00';
        $aaa = $this->m_master->chkTgl(date('Y-m-d H:i:s'), $dateFieldCek);
        if ($aaa) {
            $arr['result'] = 'Tgl Deadline ; ' . $dateFieldCek;
        } else {
            $arr['msg'] = 'Tanggal Akademik : ' . $dateFieldCek . ' melewati tanggal sekarang, Mohon cek inputan tanggal akademik';
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
        catch (Exception $e) {
            echo json_encode($arr);
        }
    }


    public function crudLimitCredit()
    {

        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'getStudents') {

                $dataStd = $this->db->query('SELECT s.NPM,s.Name, lc.ID AS LCID FROM ' . $data_arr['DB_Student'] . '.students s
                                                      LEFT JOIN db_academic.limit_credit lc ON (s.NPM=lc.NPM AND lc.SemesterID = "' . $data_arr['SemesterID'] . '")
                                                      WHERE s.ProdiID = "' . $data_arr['ProdiID'] . '"
                                                      ORDER BY s.NPM ASC')->result_array();

                $dataLC = $this->db->query('SELECT s.NPM, s.Name, lc.Credit, lc.ID AS LCID FROM db_academic.limit_credit lc
                                                    LEFT JOIN ' . $data_arr['DB_Student'] . '.students s ON (s.NPM = lc.NPM)
                                                    WHERE s.ProdiID = "' . $data_arr['ProdiID'] . '"
                                                    AND lc.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                    ORDER BY s.NPM ASC')->result_array();

                $res = array(
                    'Students' => $dataStd,
                    'dataLC' => $dataLC
                );

                return print_r(json_encode($res));
            } else if ($data_arr['action'] == 'deleteLC') {
                $this->db->where('ID', $data_arr['LCID']);
                $this->db->delete('db_academic.limit_credit');

                return print_r(1);
            } else if ($data_arr['action'] == 'addLC') {
                $dataInsert = (array) $data_arr['dataInsert'];
                $this->db->insert('db_academic.limit_credit', $dataInsert);
                return print_r(1);
            } else if ($data_arr['action'] == 'add_std_krs_exclude') {
                $dataInsert = (array) $data_arr['dataInsert'];

                $ProdiID = $dataInsert['ProdiID'];
                $Semester = $dataInsert['Semester'];

                $dataCk = $this->db->get_where('db_academic.std_krs_exclude', array('ProdiID' => $ProdiID, 'Semester' => $Semester))->result_array();

                $result = 0;
                if (count($dataCk) <= 0) {
                    $this->db->insert('db_academic.std_krs_exclude', $dataInsert);
                    $result = 1;
                }

                return print_r($result);
            } else if ($data_arr['action'] == 'read_std_krs_exclude') {
                $result = $this->db->query('SELECT st.*, ps.NameEng AS Prodi FROM db_academic.std_krs_exclude st
                                                        LEFT JOIN db_academic.program_study ps ON (ps.ID = st.ProdiID)')->result_array();
                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'remove_std_krs_exclude') {
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.std_krs_exclude');
                return print_r(1);
            }
        }
    }


    public function crudCombinedClass()
    {

        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'readGroupCalss') {
                $data = $this->m_api->__getCC_GroupCalss($data_arr['ProdiID']);
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'addCombine') {
                $dataInsert = (array) $data_arr['dataInsert'];


                // Cek apakah sudah di masukan apa belum
                $dataS = $this->db->query('SELECT * FROM db_academic.schedule_details_course sdc WHERE
                                                                    sdc.ScheduleID = "' . $dataInsert['ScheduleID'] . '"
                                                                    AND sdc.ProdiID = "' . $dataInsert['ProdiID'] . '"
                                                                    AND sdc.CDID = "' . $dataInsert['CDID'] . '" ')->result_array();


                if (count($dataS) <= 0) {

                    // Hapus STD Dari KRS Online
                    $this->m_api->kickStudent($data_arr['SemesterID'], $dataInsert['ScheduleID'], $dataInsert['CDID'], $dataInsert['ProdiID']);


                    $this->db->insert('db_academic.schedule_details_course', $dataInsert);

                    // Update CombineCLass
                    $this->db->set('CombinedClasses', '1');
                    $this->db->where('ID', $dataInsert['ScheduleID']);
                    $this->db->update('db_academic.schedule');
                    return print_r(1);
                } else {
                    return print_r(0);
                }
            } else if ($data_arr['action'] == 'getScheduleGC') {

                $dataDel = $this->db->select('ID')
                    ->get_where(
                        'db_academic.schedule_details_course',
                        array('ScheduleID' => $data_arr['ScheduleID'])
                    )->result_array();

                $dataCourse = $this->db->query('SELECT sdc.ScheduleID,sdc.ID AS SDCID, mk.NameEng AS MKNameEng, mk.MKCode FROM db_academic.schedule_details_course sdc
                                                          LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                          WHERE sdc.ScheduleID = "' . $data_arr['ScheduleID'] . '"
                                                          AND sdc.ProdiID = "' . $data_arr['ProdiID'] . '" LIMIT 1')->result_array();

                if (count($dataCourse) > 0) {
                    $dataCourse[0]['TotalProdi'] = count($dataDel);
                }

                return print_r(json_encode($dataCourse));
            } else if ($data_arr['action'] == 'delSDCGL') {

                $data = $this->m_api->delCombinedClass($data_arr);

                return print_r(json_encode($data));
            }
        }
    }

    public function m_equipment_additional()
    {
        $data_arr = $this->getInputToken();
        $Start = date("Y-m-d H:i:s", strtotime($data_arr['date'] . $data_arr['Start']));
        $End = date("Y-m-d H:i:s", strtotime($data_arr['date'] . $data_arr['End']));
        $arr = $this->m_reservation->get_m_equipment_additional_check_date($Start, $End);
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

        $xx = date("H:i", strtotime($start));
        $time = strtotime($xx);
        $time = date("H:i", strtotime('+30 minutes', $time));
        $bool = true;
        $arr = array();
        $inc = 0;
        while ($bool) {
            if (count($arr) == 0) {
                $arr[] = $time;
            } else {
                $xx = date("H:i", strtotime($arr[$inc]));
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

        for ($i = 0; $i < count($arr); $i++) {
            $arrHours[] = date("h:i a", strtotime($arr[$i]));
        }

        echo json_encode($arrHours);
    }

    public function m_additional_personel()
    {
        $arr = $this->m_reservation->get_m_additional_personel();
        echo json_encode($arr);
    }


    public function getSimpleSearch()
    {
        $key = $this->input->get('key');
        $data = $this->m_api->__getSimpleSearch($key);
        return print_r(json_encode($data));
    }

    public function getSimpleSearchStudents()
    {
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
    public function getStudentByScheduleID($ScheduleID)
    {
        $data = $this->m_api->__getStudentByScheduleID($ScheduleID);
        return print_r(json_encode($data));
    }

    public function checkBentrokScheduleAPI()
    {
        $chk = $this->m_reservation->checkBentrokScheduleAPI();
        $DeletedBy = 0;
        $wrADDNotesDelete = '';
        $NameApproved = '';
        $wrADDNotesDelete_email = '';
        if (array_key_exists('token', $_POST)) {
            $input = $this->getInputToken();
            if (array_key_exists('EXID', $input)) {
                $EXID = $input['EXID'];
                $DeletedBy = $this->session->userdata('NIP');
                $G_EMP = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $DeletedBy);
                $NameApproved = $G_EMP[0]['Name'];
                $wrADDNotesDelete = 'Schedule Exchange';
                $wrADDNotesDelete_email = 'by ' . $wrADDNotesDelete . ' that approved by ' . $NameApproved;
            }
        }

        if (!$chk['bool']) {
            // insert table to t_booking_delete
            $this->load->model('m_sendemail');
            // get data user
            $get = $this->m_master->caribasedprimary('db_reservation.t_booking', 'ID', $chk['ID']);
            $getUser = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $get[0]['CreatedBy']);

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
                'Note_deleted' => 'Conflict' . '##' . $wrADDNotesDelete,
                'DeletedBy' => $DeletedBy,
                'Req_layout' => $get[0]['Req_layout'],
                'Status' => $get[0]['Status'],
                'MarcommSupport' => $get[0]['MarcommSupport'],
            );
            $this->db->insert('db_reservation.t_booking_delete', $dataSave);


            $this->m_master->delete_id_table_all_db($get[0]['ID'], 'db_reservation.t_booking');
            $this->m_master->delete_id_table_all_db($get[0]['ID'], 'db_reservation.t_booking_eq_additional');

            // send email and update notification
            // broadcase update js
            /* if($_SERVER['SERVER_NAME'] =='localhost') {
                 $client = new Client(new Version1X('//10.1.10.230:3000'));
             }
             else{
                 $client = new Client(new Version1X('//10.1.30.17:3000'));

             }
                 $client->initialize();
                 // send message to connected clients
                 $client->emit('update_schedule_notifikasi', ['update_schedule_notifikasi' => '1','date' => '']);
                 $client->close();*/

            $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['Start']);
            $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['End']);
            $StartNameDay = $Startdatetime->format('l');
            $EndNameDay = $Enddatetime->format('l');


            //suggestion room
            $ParticipantQty = $get[0]['ParticipantQty'];
            //find room besar >= ParticipantQty and category room sama
            $sg_room = function ($ParticipantQty, $Room) {
                $result = '';
                $r = array();
                $a = $this->m_master->caribasedprimary('db_academic.classroom', 'Room', $Room);
                $ID_CategoryRoom = $a[0]['ID_CategoryRoom'];
                $b = $this->m_master->caribasedprimary('db_academic.classroom', 'ID_CategoryRoom', $ID_CategoryRoom);
                for ($i = 0; $i < count($b); $i++) {
                    if ($b[$i]['Seat'] > $ParticipantQty) {
                        $r[] = $b[$i]['Room'];
                    }
                }

                if (count($r) > 0) {
                    $result = 'Following suggestion from our room :<ul>';
                    for ($i = 0; $i < count($r); $i++) {
                        $result .= '<li>' . $r[$i] . '</li>';
                    }
                    $result .= '</ul>';
                }

                return $result;
            };
            $sg_room = $sg_room($ParticipantQty, $get[0]['Room']);
            //suggestion room

            // send email
            $Email = $getUser[0]['EmailPU'];
            $text = 'Dear ' . $getUser[0]['Name'] . ',<br><br>

                            Your Venue Reservation was conflict ' . $wrADDNotesDelete_email . ',<br><br>
                            <strong>Your schedule automated delete by System</strong>,<br><br>
                            Details Schedule : <br><ul>
                            <li>Start  : ' . $StartNameDay . ', ' . $get[0]['Start'] . '</li>
                            <li>End  : ' . $EndNameDay . ', ' . $get[0]['End'] . '</li>
                            <li>Room  : ' . $get[0]['Room'] . '</li>
                            </ul>
                            <br>
                            Please Create new schedule, if you need it and ' . $sg_room . ' <br>

                        ';
            $to = $Email;
            $subject = "Podomoro University Venue Reservation";
            $sendEmail = $this->m_sendemail->sendEmail($to, $subject, null, null, null, null, $text);


            // send email to Adum
            $text = 'Dear Team,<br><br>
                            Venue Reservation was conflict,<br><br>
                            <strong>The schedule automated delete by System</strong>,<br><br>
                            Details Schedule : <br><ul>
                            <li>Start       : ' . $StartNameDay . ', ' . $get[0]['Start'] . '</li>
                            <li>End         : ' . $EndNameDay . ', ' . $get[0]['End'] . '</li>
                            <li>Room        : ' . $get[0]['Room'] . '</li>
                            <li>Request BY  : ' . $getUser[0]['Name'] . '</li>
                            </ul>
                            <br>
                            Please Create new schedule, if you need it and ' . $sg_room . ' <br>

                        ';
            $eAdum = $this->m_master->caribasedprimary('db_reservation.email_to', 'Ownership', 'Adum');
            $to = $eAdum[0]['Email'];
            $subject = "Podomoro University Venue Reservation";
            $sendEmail = $this->m_sendemail->sendEmail($to, $subject, null, null, null, null, $text);

            echo json_encode(0);
        } else {
            echo json_encode(1);
        }
    }

    function crudPartime()
    {
        $data_arr = $this->getInputToken();

        if ($data_arr['action'] == 'readPartime') {
            $data = $this->m_hr->getLecPartime();
            return print_r(json_encode($data));
        }
    }

    public function crudConfig()
    {

        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {

            if ($data_arr['action'] == 'readConfig') {
                $data = $this->db->get_where('db_academic.config', array('ConfigID' => $data_arr['ConfigID']))->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'updateConfig') {

                $this->db->set('Status', $data_arr['Status']);
                $this->db->where('ConfigID', $data_arr['ConfigID']);
                $this->db->update('db_academic.config');

                return print_r(1);
            } else if ($data_arr['action'] == 'updateExamSetting') {

                $formData = $data_arr['formData'];

                $ceckData = $this->db->select('ID')->get('db_academic.exam_setting')
                    ->result_array();

                if (count($ceckData) > 0) {
                    // Update
                    $ID = $ceckData[0]['ID'];
                    $this->db->where('ID', $ID);
                    $this->db->update('db_academic.exam_setting', $formData);
                } else {
                    // Insert
                    $this->db->insert('db_academic.exam_setting', $formData);
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'readExamSetting') {
                $data = $this->db->get('db_academic.exam_setting')
                    ->result_array();

                return print_r(json_encode($data));
            }
        }
    }

    public function crudInvigilator()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {

            if ($data_arr['action'] == 'readScheduleInvigilator') {

                $dateTimeNow = $this->m_rest->getDateTimeNow();

                if ($data_arr['TypeSemester'] == 1 || $data_arr['TypeSemester'] == '1') {
                    $data = $this->m_api->getInvigilatorSch(
                        $data_arr['SemesterID'],
                        $data_arr['TypeExam'],
                        $data_arr['NIP'],
                        $dateTimeNow
                    );
                } else {
                    $data = $this->m_api->getInvigilatorSchAntara(
                        $data_arr['SemesterID'],
                        $data_arr['TypeExam'],
                        $data_arr['NIP'],
                        $dateTimeNow
                    );
                }

                return print_r(json_encode($data));
            }
        }
    }


    public function getListCourseInScore()
    {
        $requestData = $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if ($data_arr['StatusGrade'] == 'null') {
            $whereStatusGrade = ' AND gc.Status IS NULL ';
        } else {
            $whereStatusGrade = ($data_arr['StatusGrade'] != '' && $data_arr['StatusGrade'] != null)
                ? ' AND gc.Status = "' . $data_arr['StatusGrade'] . '" ' : '';
        }


        $whereP = ($data_arr['ProdiID'] != '' && $data_arr['ProdiID'] != null)
            ? ' sc.SemesterID = "' . $data_arr['SemesterID'] . '" ' . $whereStatusGrade . ' AND sc.IsSemesterAntara = "' . $data_arr['IsSemesterAntara'] . '" AND sdc.ProdiID = "' . $data_arr['ProdiID'] . '" '
            : ' sc.SemesterID = "' . $data_arr['SemesterID'] . '" ' . $whereStatusGrade . ' AND sc.IsSemesterAntara = "' . $data_arr['IsSemesterAntara'] . '" ';
        $orderBy = ' GROUP BY sc.ID ORDER BY sc.ClassGroup, sdc.ID ASC ';
        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( sc.ClassGroup LIKE "%' . $search . '%"
            OR mk.Name LIKE "%' . $search . '%"
             OR mk.NameEng LIKE "%' . $search . '%"
              OR em.Name LIKE "%' . $search . '%" ) ';
        }

        // Load Type
        $whereType = '';
        if ($data_arr['Type'] == 10) {
            $whereType = ' AND (ris_uts.NIP IS NULL)';
        } else if ($data_arr['Type'] == 11) {
            $whereType = ' AND (ris_uts.NIP IS NOT NULL)';
        } else if ($data_arr['Type'] == 12) {
            $whereType = ' AND (ris_uts.Status = "1")';
        } else if ($data_arr['Type'] == 13) {
            $whereType = ' AND (ris_uts.Status = "0")';
        } else if ($data_arr['Type'] == 20) {
            $whereType = ' AND (ris_uas.NIP IS NULL)';
        } else if ($data_arr['Type'] == 21) {
            $whereType = ' AND (ris_uas.NIP IS NOT NULL)';
        } else if ($data_arr['Type'] == 22) {
            $whereType = ' AND (ris_uas.Status = "1")';
        } else if ($data_arr['Type'] == 22) {
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
                                        WHERE (' . $whereP . ' ) ' . $whereType . ' ' . $dataSearch . ' ' . $orderBy . ' ';


        $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (SELECT sdc.ID
                                                                FROM db_academic.schedule_details_course sdc
                                                                LEFT JOIN db_academic.schedule sc ON (sc.ID = sdc.ScheduleID)
                                                                LEFT JOIN db_employees.employees em ON (em.NIP = sc.Coordinator)
                                                                LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                                LEFT JOIN db_academic.grade_course gc
                                                                ON (gc.SemesterID = sc.SemesterID AND gc.ScheduleID = sc.ID)
                                                                LEFT JOIN db_academic.record_input_score ris_uts 
                                                                ON (ris_uts.ScheduleID = sc.ID AND ris_uts.Type="uts")
                                                                LEFT JOIN db_academic.record_input_score ris_uas 
                                                                ON (ris_uas.ScheduleID = sc.ID AND ris_uas.Type="uas")
                                                                WHERE (' . $whereP . ' ) ' . $whereType . ' ' . $dataSearch . ') xx';

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $StatusGrade = '-';
            if ($row['StatusGrade'] == '2') {
                $StatusGrade = '<i class="fa fa-check-circle" style="color: green;"></i>';
            } else if ($row['StatusGrade'] == '0') {
                $StatusGrade = '<i class="fa fa-repeat"></i>';
            } else if ($row['StatusGrade'] == '1') {
                $StatusGrade = '<i class="fa fa-question-circle" style="color: blue;"></i>';
            } else if ($row['StatusGrade'] == '-2') {
                $StatusGrade = '<i class="fa fa-times-circle" style="color: darkred;"></i>';
            }

            $data_input_score = array(
                'NIP' => $row['Coordinator'],
                'SemesterID' => $data_arr['SemesterID'],
                'ScheduleID' => $row['ScheduleID'],

            );

            $tkn = $this->jwt->encode($data_input_score, 'UAP)(*');
            $url_input_score = base_url() . 'academic/score/inputScore/' . $tkn;

            $btnAct = '<div class="btn-group"><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <span class="caret"></span></button>
                <ul class="dropdown-menu">
                <li><a href="' . $url_input_score . '" target="_blank">Input Score</a></li>
                <li><a href="javascript:void(0);" class="btnGrade" data-page="InputGrade1" data-group="' . $row['ClassGroup'] . '" data-id="' . $row['ScheduleID'] . '">Approval - Score Weighted</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="javascript:void(0);" class="inputScheduleExchange" data-no="' . $no . '" data-id="">Cetak Report UTS</a></li>
                <li><a href="javascript:void(0);" class="inputScheduleExchange" data-no="' . $no . '" data-id="">Cetak Report UAS</a></li>
                </ul>
                </div>';

            // Student
            $dataStudent = $this->m_api->getDataStudents_Schedule($data_arr['SemesterID'], $row['ScheduleID']);

            $inputUTS = ($row['uts_UpdateBy'] != null && $row['uts_UpdateBy'] != '') ? '<i class="fa fa-check-square-o" style="color: forestgreen;"></i>' : '-';
            $inputUAS = ($row['uas_UpdateBy'] != null && $row['uas_UpdateBy'] != '') ? '<i class="fa fa-check-square-o" style="color: forestgreen;"></i>' : '-';

            $AppUTS = ($row['uts_Status'] != null && $row['uts_Status'] != '' && $row['uts_Status'] != '0') ? '<i class="fa fa-check-square-o" style="color: forestgreen;"></i>' : '-';
            $AppUAS = ($row['uas_Status'] != null && $row['uas_Status'] != '' && $row['uas_Status'] != '0') ? '<i class="fa fa-check-square-o" style="color: forestgreen;"></i>' : '-';


            $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div style="text-align:left;"><b>' . $row['MKNameEng'] . '</b><br/>' . $row['MKName'] . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . $row['ClassGroup'] . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . $row['Credit'] . '</div>';
            $nestedData[] = '<div style="text-align:left;">' . $row['CoordinatorName'] . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . count($dataStudent) . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . $inputUTS . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . $AppUTS . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . $inputUAS . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . $AppUAS . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . $btnAct . '</div>';
            $nestedData[] = '<div style="text-align:center;">' . $StatusGrade . '</div>';

            $data[] = $nestedData;
            $no++;
        }


        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($queryDefaultRow),
            "recordsFiltered" => intval($queryDefaultRow),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getMonScoreStd()
    {
        $requestData = $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        $queryDefaultRow = [];
        $data = [];

        if ($data_arr['SemesterID'] >= 13) {
            $w_prodi = ($data_arr['ProdiID'] != '') ? ' AND auts.ProdiID = "' . $data_arr['ProdiID'] . '"' : '';

            $whereType = '';
            if ($data_arr['Type'] == 10) {
                $whereType = ' AND (sp.UTS IS NULL OR sp.UTS=0 OR sp.UTS="")';
            } else if ($data_arr['Type'] == 11) {
                $whereType = ' AND (sp.UTS IS NOT NULL AND sp.UTS!=0 AND sp.UTS != "")';
            } else if ($data_arr['Type'] == 20) {
                $whereType = ' AND (sp.UAS IS NULL OR sp.UAS=0 OR sp.UAS="")';
            } else if ($data_arr['Type'] == 21) {
                $whereType = ' AND (sp.UAS IS NOT NULL AND sp.UAS!=0 AND sp.UAS != "")';
            }

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' AND ( s.ClassGroup LIKE "%' . $search . '%"
            OR auts.Name LIKE "%' . $search . '%"
             OR auts.NPM LIKE "%' . $search . '%"
              OR em.NIP LIKE "%' . $search . '%"
               OR em.Name LIKE "%' . $search . '%") ';
            }


            $DB_ = 'ta_' . $data_arr['Year'];
            $queryDefault = 'SELECT s.ID,auts.NPM,auts.Name, s.ClassGroup, s.TotalAssigment, em.Name AS CoordinatorName,
                                     sp.Evaluasi1, sp.Evaluasi2, sp.Evaluasi3, sp.Evaluasi4, sp.Evaluasi5, sp.UTS, sp.UAS,
                                      sp.Score, sp.Grade
                                    FROM ' . $DB_ . '.study_planning sp
                                    LEFT JOIN db_academic.auth_students auts ON (auts.NPM = sp.NPM)
                                    LEFT JOIN db_academic.schedule s ON (s.ID = sp.ScheduleID)
                                    LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                    WHERE ( sp.SemesterID = "' . $data_arr['SemesterID'] . '" ' . $w_prodi . ' ) ' . $whereType . ' ' . $dataSearch . ' ORDER BY sp.NPM ASC
                                    ';

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();
            for ($i = 0; $i < count($query); $i++) {
                $nestedData = array();

                $row = $query[$i];

                $rowMK = $this->db->query('SELECT  mk.Name AS MKName, mk.NameEng AS MKNameEng, mk.MKCode
                                                              FROM db_academic.schedule_details_course sdc
                                                              LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                              WHERE sdc.ScheduleID = "' . $row['ID'] . '"  GROUP BY sdc.ScheduleID LIMIT 1')->result_array()[0];

                $ev1 = ($row['Evaluasi1'] != null && $row['Evaluasi1'] != '') ? $row['Evaluasi1'] : '-';
                $ev2 = ($row['Evaluasi2'] != null && $row['Evaluasi2'] != '') ? $row['Evaluasi2'] : '-';
                $ev3 = ($row['Evaluasi3'] != null && $row['Evaluasi3'] != '') ? $row['Evaluasi3'] : '-';
                $ev4 = ($row['Evaluasi4'] != null && $row['Evaluasi4'] != '') ? $row['Evaluasi4'] : '-';
                $ev5 = ($row['Evaluasi5'] != null && $row['Evaluasi5'] != '') ? $row['Evaluasi5'] : '-';
                $UTS = ($row['UTS'] != null && $row['UTS'] != '') ? $row['UTS'] : '-';
                $UAS = ($row['UAS'] != null && $row['UAS'] != '') ? $row['UAS'] : '-';


                $ev1 = (1 <= $row['TotalAssigment']) ? $ev1 : 'not set';
                $ev2 = (2 <= $row['TotalAssigment']) ? $ev2 : 'not set';
                $ev3 = (3 <= $row['TotalAssigment']) ? $ev3 : 'not set';
                $ev4 = (4 <= $row['TotalAssigment']) ? $ev4 : 'not set';
                $ev5 = (5 <= $row['TotalAssigment']) ? $ev5 : 'not set';



                $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;"><b><i class="fa fa-user margin-right"></i>' . $row['Name'] . '</b><br/>' . $row['NPM'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $rowMK['MKCode'] . '</div>';
                $nestedData[] = '<div style="text-align:left;"><span style="color: #009688;">' . $rowMK['MKNameEng'] . '</span><br/><i style="color: #9e9e9e;">' . $rowMK['MKName'] . '</i></div>';
                $nestedData[] = '<div style="text-align:center;">' . $row['ClassGroup'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['CoordinatorName'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $ev1 . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $ev2 . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $ev3 . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $ev4 . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $ev5 . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $UTS . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $UAS . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row['Score'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row['Grade'] . '</div>';

                $data[] = $nestedData;
                $no++;
            }
        }



        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval(count($queryDefaultRow)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }


    // ====== Transcript Nilai =========
    public function getTranscript()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();

        $dataWhere = ($data_arr['ProdiID'] != '' && $data_arr['ProdiID'] != null)
            ? 'aut_s.Year = "' . $data_arr['Year'] . '" AND ( aut_s.StatusStudentID = "3" OR aut_s.StatusStudentID = "1" ) AND aut_s.ProdiID = "' . $data_arr['ProdiID'] . '" '
            : 'aut_s.Year = "' . $data_arr['Year'] . '" AND ( aut_s.StatusStudentID = "3" OR aut_s.StatusStudentID = "1" ) ';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = 'AND ( aut_s.Name LIKE "%' . $search . '%" OR aut_s.NPM LIKE "%' . $search . '%"
                           OR ps.Name LIKE "%' . $search . '%"  OR ps.NameEng LIKE "%' . $search . '%" )';
        }

        $queryDefault = 'SELECT aut_s.*, ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng FROM db_academic.auth_students aut_s
                                      LEFT JOIN db_academic.program_study ps ON (ps.ID = aut_s.ProdiID)
                                      WHERE ( ' . $dataWhere . ' ) ' . $dataSearch . ' ORDER BY aut_s.NPM ASC ';

        $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (SELECT aut_s.ID 
                                                        FROM db_academic.auth_students aut_s
                                                        LEFT JOIN db_academic.program_study ps 
                                                        ON (ps.ID = aut_s.ProdiID)
                                                        WHERE ( ' . $dataWhere . ' ) ' . $dataSearch . ') xx';

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $db_ = 'ta_' . $row['Year'];

            $btnSKPI = '<div  style="text-align:center;">
                            <a href="' . base_url('save2pdf/diploma_supplement') . '" target="_blank" class="btn btn-default btn-sm btn-default-warning btn-round btnDownloadSKPI" disabled><i class="fa fa-download margin-right"></i> SKPI</a>
                            <a class="btn btn-default btn-sm btn-default-warning btn-round btnDownloadSkls"data-db="' . $db_ . '" data-npm="' . $row['NPM'] . '"><i class="fa fa-download margin-right"></i> SKL</a>

                            </div>';


            $btnTranscript = '<div class="btn-group btn-sm" role="group" aria-label="...">
                              <button type="button" class="btn btn-sm btn-default btn-default-danger btnDowloadTempTranscript" data-db="' . $db_ . '" data-npm="' . $row['NPM'] . '"><i class="fa fa-hourglass-half margin-right"></i> Temp.</button>
                              <button type="button" class="btn btn-sm btn-default btn-default-primary btnDowloadTranscript" data-db="' . $db_ . '" data-npm="' . $row['NPM'] . '">
                              <i class="fa fa-download margin-right"></i> Final</button>
                            </div>';

            $btnIjazah = '<div  style="text-align:center;">
                            <button class="btn btn-sm btn-default btn-default-success btn-round btnDownloadIjazah" data-db="' . $db_ . '" data-npm="' . $row['NPM'] . '"><i class="fa fa-download margin-right"></i> Ijazah</button>
                            </div>';


            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:left;">
                                    <b><i class="fa fa-user margin-right"></i> ' . ucwords(strtolower($row['Name'])) . '</b><br/>
                                        ' . $row['NPM'] . ' | ' . $row['ProdiNameEng'] . '<br/>
                                        <a>' . $row['EmailPU'] . '</a></div>';

            $nestedData[] = '<div  style="text-align:left;">
                                    <div class="row">
                                        <div class="col-md-12">
                                                <label  class="text-primary">National Certificate Number</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-10" style="padding-right: 0px;">
                                            <input id="formCNN' . $row['NPM'] . '" class="form-control hide" value="' . $row['CertificateNationalNumber'] . '"/>
                                            <span id="viewCNN' . $row['NPM'] . '">' . $row['CertificateNationalNumber'] . '</span>
                                        </div>
                                        <div class="col-xs-2">
                                            <button class="btn btn-sm btn-success btn-block btnSaveCNN btn-circle hide" data-npm="' . $row['NPM'] . '"><i class="fa fa-check-circle"></i></button>
                                            <button class="btn btn-sm btn-default btn-block btn-circle btnEditCNN" data-npm="' . $row['NPM'] . '"><i class="fa fa-pencil-square-o"></i></button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                                <label  class="text-primary">Certificate Serial Number</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-10" style="padding-right: 0px;">
                                            <input id="formCSN' . $row['NPM'] . '" class="form-control hide" value="' . $row['CertificateSerialNumber'] . '"/>
                                            <span id="viewCSN' . $row['NPM'] . '">' . $row['CertificateSerialNumber'] . '</span>
                                        </div>
                                        <div class="col-xs-2">
                                            <button class="btn btn-sm btn-success btn-block btnSaveCSN btn-circle hide" data-npm="' . $row['NPM'] . '"><i class="fa fa-check-circle"></i></button>
                                            <button class="btn btn-sm btn-default btn-block btn-circle btnEditCSN" data-npm="' . $row['NPM'] . '"><i class="fa fa-pencil-square-o"></i></button>
                                        </div>
                                    </div>

                                    </div>';

            $nestedData[] = '<div  style="text-align:left;">
                                    <div class="">
                                        <div class="col-xs-9" style="padding-right: 0px;">
                                            <input id="formSKLN' . $row['NPM'] . '" class="form-control hide" value="' . $row['SklNumber'] . '"/>
                                            <span id="viewSKLN' . $row['NPM'] . '">' . $row['SklNumber'] . '</span>
                                        </div>
                                        <div class="col-xs-3">

                                            <button class="btn btn-sm btn-success btn-block btn-circle btnSaveSKLN hide" data-npm="' . $row['NPM'] . '"><i class="fa fa-check-circle"></i></button>
                                            <button class="btn btn-sm btn-default btn-block btn-circle btnEditSKLN" data-npm="' . $row['NPM'] . '"><i class="fa fa-pencil-square-o"></i></button>
                                        </div>
                                    </div>

                                    </div>';

            $nestedData[] = $btnSKPI;
            $nestedData[] = $btnTranscript;
            //            $nestedData[] = $btnIjazah;

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

    public function crudTranscript()
    {

        $data_arr = $this->getInputToken();

        if (count($data_arr > 0)) {
            if ($data_arr['action'] == 'readStudent') {
                $Year = $data_arr['Year'];
                $ProdiID = $data_arr['ProdiID'];
                $data = $this->db->get_where('db_academic.auth_students', array('ProdiID' => $ProdiID, 'Year' => $Year))->result_array();
                print_r($data);
            } else if ($data_arr['action'] == 'updateGrade') {

                $dataIns = (array) $data_arr['dataForm'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.graduation', $dataIns);
                return print_r(1);
            } else if ($data_arr['action'] == 'updateSettingTranscript') {
                $dataIns = (array) $data_arr['dataForm'];
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.setting_transcript', $dataIns);
                return print_r(1);
            } else if ($data_arr['action'] == 'updateEducation') {

                $this->db->set('DescriptionEng', $data_arr['DescriptionEng']);
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.education_level');
                return print_r(1);
            } else if ($data_arr['action'] == 'updateCSN') {
                $this->db->set('CertificateSerialNumber', $data_arr['CSN']);
                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update('db_academic.auth_students');
                return print_r(1);
            } else if ($data_arr['action'] == 'updateCNN') {
                $this->db->set('CertificateNationalNumber', $data_arr['CNN']);
                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update('db_academic.auth_students');
                return print_r(1);
            } else if ($data_arr['action'] == 'updateSKLN') {
                $this->db->set('SklNumber', $data_arr['SKLN']);
                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update('db_academic.auth_students');
                return print_r(1);
            } else if ($data_arr['action'] == 'updateTempTranscript') {

                $dataUpdate = (array) $data_arr['dataForm'];
                $this->db->where('ID', 1);
                $this->db->update('db_academic.setting_temp_transcript', $dataUpdate);
                return print_r(1);
            } else if ($data_arr['action'] == 'updateStudyAcc') {

                $dataForm = (array) $data_arr['dataForm'];

                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.program_study', $dataForm);

                return print_r(1);
            }
        }
    }
    // ==========

    // ====== Final Project =======
    public function getFinalProject()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();

        $dataWhere = ($data_arr['ProdiID'] != '' && $data_arr['ProdiID'] != null)
            ? 'aut_s.Year = "' . $data_arr['Year'] . '" AND ( aut_s.StatusStudentID = "3" OR aut_s.StatusStudentID = "1" )  AND aut_s.ProdiID = "' . $data_arr['ProdiID'] . '" '
            : 'aut_s.Year = "' . $data_arr['Year'] . '" AND ( aut_s.StatusStudentID = "3" OR aut_s.StatusStudentID = "1" )  ';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = 'AND ( aut_s.Name LIKE "%' . $search . '%" OR aut_s.NPM LIKE "%' . $search . '%"
                           OR ps.Name LIKE "%' . $search . '%"  OR ps.NameEng LIKE "%' . $search . '%" )';
        }

        $queryDefault = 'SELECT aut_s.*, ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng, ps.Code,
                                      fp.TitleInd, fp.TitleEng
                                      FROM db_academic.auth_students aut_s
                                      LEFT JOIN db_academic.program_study ps ON (ps.ID = aut_s.ProdiID)
                                      LEFT JOIN db_academic.final_project fp ON (fp.NPM = aut_s.NPM)
                                      WHERE ( ' . $dataWhere . ' ) ' . $dataSearch . ' ORDER BY aut_s.NPM ASC ';

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $row['NPM'] . '</div>';
            $nestedData[] = '<div  style="text-align:left;"><b><i class="fa fa-user margin-right"></i> ' . $row['Name'] . '</b></div>';
            $nestedData[] = '<div  style="text-align:center;">' . $row['Code'] . '</div>';
            $nestedData[] = '<div  style="text-align:left;"><span id="viewTitleInd' . $row['ID'] . '">' . $row['TitleInd'] . '</span><input class="form-control hide fmFP' . $row['ID'] . '" value="' . $row['TitleInd'] . '" id="formTitleInd' . $row['ID'] . '"></div>';
            $nestedData[] = '<div  style="text-align:left;"><span id="viewTitleEng' . $row['ID'] . '">' . $row['TitleEng'] . '</span><input class="form-control hide fmFP' . $row['ID'] . '" value="' . $row['TitleEng'] . '" id="formTitleEng' . $row['ID'] . '"></div>';
            $nestedData[] = '<div  style="text-align:center;">
                                    <button class="btn btn-success btn-sm hide btnSaveEditFP" data-id="' . $row['ID'] . '" data-npm="' . $row['NPM'] . '" id="btnSaveFP' . $row['ID'] . '">Save</button>
                                    <button class="btn btn-default btn-sm btnEditFP" data-id="' . $row['ID'] . '" data-npm=""' . $row['NPM'] . ' id="btnEditFP' . $row['ID'] . '">Edit</button>
                                </div>';

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

    public function crudFinalProject()
    {

        $data_arr = $this->getInputToken();

        if (count($data_arr > 0)) {
            if ($data_arr['action'] == 'updateFP') {

                // Cek apakah NIM sudah ada
                $dataNIM = $this->db->get_where('db_academic.final_project', array('NPM' => $data_arr['NPM']), 1)->result_array();
                $dataForm = (array) $data_arr['dataForm'];
                if (count($dataNIM) > 0) {
                    // Update
                    $this->db->where('NPM', $data_arr['NPM']);
                    $this->db->update('final_project', $dataForm);
                } else {
                    $dataForm['NPM'] = $data_arr['NPM'];
                    $this->db->insert('final_project', $dataForm);
                }


                return print_r(1);
            } else if ($data_arr['action'] == 'viewInformation') {
                $data = $this->db->query('SELECT ats.*,
                                        em1.Name AS MentorFP1Name, em1.TitleAhead AS TitleAhead1, em1.TitleBehind AS TitleBehind1,
                                        em2.Name AS MentorFP2Name, em2.TitleAhead AS TitleAhead2, em2.TitleBehind AS TitleBehind2,
                                        fp.TitleInd, fp.TitleEng,
                                        fpc.Cl_Library, fpc.Cl_Library_By, fpc.Cl_Library_At, emp1.Name AS Cl_Library_Name,
                                        fpc.Cl_Finance, fpc.Cl_Finance_By, fpc.Cl_Finance_At, emp2.Name AS Cl_Finance_Name,
                                        fpc.Cl_Kaprodi, fpc.Cl_Kaprodi_By, fpc.Cl_Kaprodi_At, emp3.Name AS Cl_Kaprodi_Name,
                                        fpc.Cl_Academic, fpc.Cl_Academic_By, fpc.Cl_Academic_At, emp6.Name AS Cl_Academic_Name,
                                        fpc.Cl_StdLife, fpc.Cl_StdLife_By, fpc.Cl_StdLife_At, emp7.Name AS Cl_StdLife_Name,
                                        
                                        fpn.finance AS Finance_Note,
                                        
                                        j.Title AS JudiciumPeriode, j.JudiciumsDate AS JudiciumDate
              
                                        FROM db_academic.auth_students ats
                                        LEFT JOIN db_employees.employees em1 ON (em1.NIP = ats.MentorFP1)
                                        LEFT JOIN db_employees.employees em2 ON (em2.NIP = ats.MentorFP2)
                                        LEFT JOIN db_academic.final_project fp ON (ats.NPM = fp.NPM)
                                        
                                        LEFT JOIN db_academic.final_project_clearance fpc ON (fpc.NPM = ats.NPM)
                                        LEFT JOIN db_academic.final_project_note fpn ON (fpn.NPM = ats.NPM)
                                        

                                        LEFT JOIN db_employees.employees emp1 ON (fpc.Cl_Library_By = emp1.NIP)
                                        LEFT JOIN db_employees.employees emp2 ON (fpc.Cl_Finance_By = emp2.NIP)
                                        LEFT JOIN db_employees.employees emp3 ON (fpc.Cl_Kaprodi_By = emp3.NIP)
                                        LEFT JOIN db_employees.employees emp6 ON (fpc.Cl_Academic_By = emp6.NIP)
                                        LEFT JOIN db_employees.employees emp7 ON (fpc.Cl_StdLife_By = emp7.NIP)
                                        LEFT JOIN db_employees.employees emp8 ON (fpc.Cl_Academic_By = emp8.NIP)
                                        
                                        LEFT JOIN db_academic.judiciums_list jl ON (jl.NPM = ats.NPM)
                                        LEFT JOIN db_academic.judiciums j ON (j.ID = jl.JID)

                                        WHERE ats.NPM = "' . $data_arr['NPM'] . '" ')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'viewMyScheduleSeminar') {

                $NPM = $data_arr['NPM'];

                $data = $this->db->query('SELECT fps.*, cl.Room, em1.Name AS Mentor1, em2.Name AS Mentor2 FROM db_academic.final_project_schedule_student fpss
                                                      LEFT JOIN db_academic.final_project_schedule fps ON (fps.ID = fpss.FPSID)
                                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = fps.ClassroomID)

                                                      LEFT JOIN db_academic.auth_students ats ON (ats.NPM = fpss.NPM)
                                                      LEFT JOIN db_employees.employees em1 ON (em1.NIP = ats.MentorFP1)
                                                      LEFT JOIN db_employees.employees em2 ON (em2.NIP = ats.MentorFP2)

                                                      WHERE fpss.NPM = "' . $NPM . '" ORDER BY fps.ID ASC ')->result_array();

                if (count($data) > 0) {
                    for ($i = 0; $i < count($data); $i++) {

                        // Get Pengawas
                        $data[$i]['Examiner'] = $this->db->query('SELECT em.Name, em.NIP, fpsl.Type FROM db_academic.final_project_schedule_lecturer fpsl
                                                                                LEFT JOIN db_employees.employees em ON (em.NIP = fpsl.NIP)
                                                                                WHERE fpsl.FPSID = "' . $data[$i]['ID'] . '" ORDER BY fpsl.Type DESC ')->result_array();

                        $data[$i]['Student'] = $this->db->query('SELECT ats.NPM, ats.Name, fpss.Notes FROM db_academic.final_project_schedule_student fpss
                                                                                LEFT JOIN db_academic.auth_students ats ON (ats.NPM = fpss.NPM)
                                                                                WHERE fpss.FPSID = "' . $data[$i]['ID'] . '"')->result_array();
                    }
                }

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'viewDocumentSkripsi') {

                $NPM = $data_arr['NPM'];
                $data = $this->db->query('SELECT fpf.*, fp.Status AS StatusFinalProject
                                                                FROM db_academic.final_project_files fpf
                                                                LEFT JOIN db_academic.final_project fp
                                                                ON (fp.NPM = fpf.NPM)
                                                                WHERE fpf.NPM = "' . $NPM . '"')->result_array();

                if (count($data) <= 0) {
                    $this->db->insert('db_academic.final_project_files', array('NPM' => $NPM));

                    $data = $this->db->query('SELECT fpf.*, fp.Status AS StatusFinalProject
                                                                FROM db_academic.final_project_files fpf
                                                                LEFT JOIN db_academic.final_project fp
                                                                ON (fp.NPM = fpf.NPM)
                                                                WHERE fpf.NPM = "' . $NPM . '"')->result_array();
                }

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'viewCheckStatusDocumentSkripsi') {
                $NPM = $data_arr['NPM'];
                $data = $this->db->query('SELECT fpf.Status, fpf.Noted FROM db_academic.final_project_files fpf 
                                                                WHERE fpf.NPM = "' . $NPM . '"')->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'updateDocumentSkripsi') {

                $NPM = $data_arr['NPM'];
                $dataForm = (array) $data_arr['dataForm'];

                // Cek ada atau tidak
                $dataCheck = $this->db->get_where(
                    'db_academic.final_project_files',
                    array('NPM' => $NPM)
                )->result_array();

                if (count($dataCheck) > 0) {
                    $this->db->where('NPM', $NPM);
                    $this->db->update('db_academic.final_project_files', $dataForm);
                } else {
                    $this->db->insert('db_academic.final_project_files', $dataForm);
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'updateTitlePlanSkripsi') {
                $NPM = $data_arr['NPM'];
                $dataCheckTitle = $this->db->get_where(
                    'db_academic.final_project',
                    array('NPM' => $NPM)
                )->result_array();

                $dataForm = (array) $data_arr['dataForm'];
                $dataForm['NPM'] = $NPM;

                if (count($dataCheckTitle) > 0) {
                    $this->db->where('NPM', $NPM);
                    $this->db->update('db_academic.final_project', $dataForm);
                } else {
                    $this->db->insert('db_academic.final_project', $dataForm);
                }

                //                print_r($dataForm);exit;

                return print_r(1);
            } else if ($data_arr['action'] == 'viewScheduleStdSeminar') {

                $NPM = $data_arr['NPM'];

                // Get data Final Project

                $dataFinalProject = $this->db->query('SELECT fp.*, em1.Name AS M1_Name, em1.TitleAhead AS M1_TitleAhead, em1.TitleBehind AS M1_TitleBehind,
                                                             em2.Name AS M2_Name, em2.TitleAhead AS M2_TitleAhead, em2.TitleBehind AS M2_TitleBehind
                                                             FROM db_academic.final_project fp
                                                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM = fp.NPM)
                                                            LEFT JOIN db_employees.employees em1 ON (em1.NIP = ats.MentorFP1)
                                                            LEFT JOIN db_employees.employees em2 ON (em2.NIP = ats.MentorFP2)
                                                            WHERE fp.NPM="' . $NPM . '"')->result_array();

                // Jadwal Sidang Proposal Pertama
                $sidang1 = $this->db->query('SELECT fps.*, cl.Room, fpss.Notes, fpss.Status
                                                    FROM db_academic.final_project_schedule fps
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = fps.ClassroomID)
                                                    LEFT JOIN db_academic.final_project_schedule_student fpss ON (fpss.FPSID = fps.ID)
                                                    WHERE fps.Type = "1" AND fpss.NPM = "' . $NPM . '" ')->result_array();

                if (count($sidang1) > 0) {
                    $sidang1[0]['Examiner'] = $this->db->query('SELECT em.NIP, em.Name, em.TitleAhead, em.TitleBehind FROM db_academic.final_project_schedule_lecturer fpsl
                                                              LEFT JOIN db_employees.employees em ON (em.NIP = fpsl.NIP)
                                                              WHERE fpsl.FPSID = "' . $sidang1[0]['ID'] . '"')->result_array();
                }

                // Jadwal sidang 1 remidial
                $sidang1_remidi = $this->db->query('SELECT fps.*, cl.Room, fpss.Notes, fpss.Status
                                                    FROM db_academic.final_project_schedule fps
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = fps.ClassroomID)
                                                    LEFT JOIN db_academic.final_project_schedule_student fpss ON (fpss.FPSID = fps.ID)
                                                    WHERE fps.Type = "3" AND fpss.NPM = "' . $NPM . '" ')->result_array();

                if (count($sidang1_remidi) > 0) {
                    for ($i = 0; $i < count($sidang1_remidi); $i++) {
                        $sidang1_remidi[$i]['Examiner'] = $this->db->query('SELECT em.NIP, em.Name, em.TitleAhead, em.TitleBehind FROM db_academic.final_project_schedule_lecturer fpsl
                                                              LEFT JOIN db_employees.employees em ON (em.NIP = fpsl.NIP)
                                                              WHERE fpsl.FPSID = "' . $sidang1_remidi[$i]['ID'] . '"')->result_array();
                    }
                }



                // Jadwal Sidang Hasil
                $sidang2 = $this->db->query('SELECT fps.*, cl.Room, fpss.Notes, fpss.Status
                                                    FROM db_academic.final_project_schedule fps
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = fps.ClassroomID)
                                                    LEFT JOIN db_academic.final_project_schedule_student fpss ON (fpss.FPSID = fps.ID)
                                                    WHERE fps.Type = "2" AND fpss.NPM = "' . $NPM . '" ')->result_array();

                if (count($sidang2) > 0) {
                    $sidang2[0]['Examiner'] = $this->db->query('SELECT em.NIP, em.Name, em.TitleAhead, em.TitleBehind FROM db_academic.final_project_schedule_lecturer fpsl
                                                              LEFT JOIN db_employees.employees em ON (em.NIP = fpsl.NIP)
                                                              WHERE fpsl.FPSID = "' . $sidang2[0]['ID'] . '"')->result_array();
                }

                // Jadwal sidang 2 remidial
                $sidang2_remidi = $this->db->query('SELECT fps.*, cl.Room, fpss.Notes, fpss.Status
                                                    FROM db_academic.final_project_schedule fps
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = fps.ClassroomID)
                                                    LEFT JOIN db_academic.final_project_schedule_student fpss ON (fpss.FPSID = fps.ID)
                                                    WHERE fps.Type = "4" AND fpss.NPM = "' . $NPM . '" ')->result_array();

                if (count($sidang2_remidi) > 0) {
                    for ($i = 0; $i < count($sidang2_remidi); $i++) {
                        $sidang2_remidi[$i]['Examiner'] = $this->db->query('SELECT em.NIP, em.Name, em.TitleAhead, em.TitleBehind FROM db_academic.final_project_schedule_lecturer fpsl
                                                              LEFT JOIN db_employees.employees em ON (em.NIP = fpsl.NIP)
                                                              WHERE fpsl.FPSID = "' . $sidang2_remidi[$i]['ID'] . '"')->result_array();
                    }
                }

                $result = array(
                    'dataTA' => $dataFinalProject,
                    'Sidang1' => $sidang1,
                    'Sidang1_Remidi' => $sidang1_remidi,
                    'Sidang2' => $sidang2,
                    'Sidang2_Remidi' => $sidang2_remidi
                );







                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'checkRegistration') {
                $data = $this->db->get_where('db_academic.final_project', array(
                    'NPM' => $data_arr['NPM']
                ))->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'updateFinalProject') {
                $ID = $data_arr['ID'];
                $formData = (array) $data_arr['formData'];
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();

                if ($ID != '') {
                    $this->db->where('ID', $ID);
                    $this->db->update('db_academic.final_project', $formData);
                } else {
                    $this->db->insert('db_academic.final_project', $formData);
                    $ID = $this->db->insert_id();
                }

                return print_r(json_encode(array('ID' => $ID)));
            } else if ($data_arr['action'] == 'getAllStdReg') {

                $SemesterID = $data_arr['SemesterID'];

                $queryDefault = 'SELECT ats.Name, ats.NPM, em4.Name AS Mentor1, em5.Name AS Mentor2
                                        FROM db_academic.std_study_planning ssp
                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssp.MKID)
                                        LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssp.NPM)

                                        LEFT JOIN db_employees.employees em4 ON (ats.MentorFP1 = em4.NIP)
                                        LEFT JOIN db_employees.employees em5 ON (ats.MentorFP2 = em5.NIP)                                        
                                        WHERE mk.Yudisium = "1" AND ssp.SemesterID = "' . $SemesterID . '" GROUP BY ats.NPM 
                                        ORDER BY ats.NPM';


                $data = $this->db->query($queryDefault)->result_array();



                //                $Status = $data_arr['Status'];
                //                $data = $this->db->query('SELECT ats.Name, ats.NPM, em1.Name AS Mentor1, em2.Name AS Mentor2
                //                                                    FROM db_academic.final_project fp
                //                                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = fp.NPM)
                //                                                    LEFT JOIN db_employees.employees em1 ON (em1.NIP = ats.MentorFP1)
                //                                                    LEFT JOIN db_employees.employees em2 ON (em2.NIP = ats.MentorFP2)
                //                                                    WHERE fp.Status = "'.$Status.'" ')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readDataSchFP') {

                $SemesterID = $data_arr['SemesterID'];
                $Type = $data_arr['Type'];

                $data = $this->db->query('SELECT fpc.*, cl.Room FROM db_academic.final_project_schedule fpc
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = fpc.ClassroomID)
                                                    WHERE fpc.SemesterID = "' . $SemesterID . '" AND fpc.Type = "' . $Type . '"
                                                    ORDER BY fpc.Date, fpc.Start ASC ')->result_array();

                if (count($data) > 0) {
                    for ($i = 0; $i < count($data); $i++) {
                        // Get Std

                        $data[$i]['Student'] = $this->db->query('SELECT sp.*, ats.Name, ats.MentorFP1, ats.MentorFP2, em1.Name AS MentorFP1Name, em2.Name AS MentorFP2Name   
                                                        FROM db_academic.final_project_schedule_student sp
                                                        LEFT JOIN db_academic.auth_students ats ON (ats.NPM = sp.NPM)
                                                        LEFT JOIN db_employees.employees em1 ON (em1.NIP = ats.MentorFP1)
                                                        LEFT JOIN db_employees.employees em2 ON (em2.NIP = ats.MentorFP2)
                                                        WHERE sp.FPSID = "' . $data[$i]['ID'] . '" ')->result_array();

                        $data[$i]['Examiner'] = $this->db->query('SELECT sp.*, em.Name  FROM db_academic.final_project_schedule_lecturer sp 
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = sp.NIP)
                                                        WHERE sp.FPSID = "' . $data[$i]['ID'] . '" ')->result_array();
                    }
                }

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'removeDataSchFP') {

                $ID = $data_arr['ID'];

                $tables = array('db_academic.final_project_schedule_student', 'db_academic.final_project_schedule_lecturer');
                $this->db->where('FPSID', $ID);
                $this->db->delete($tables);
                $this->db->reset_query();

                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.final_project_schedule');

                return print_r(1);
            } else if ($data_arr['action'] == 'updateDataSchFP') {

                // 0 = blm daftar, 1 = sudah daftar, 2 = Sudah terjadwal sidang proposal, 3 = Lulus Sidang Proposal,
                // -3 = Tidak Lulus Sidang Proposal, 4 = Sudah terjadwal sidang hasil, 5 = Lulus sidang hasil, -5 = Tidak Lulus sidang hasil

                $ID = $data_arr['ID'];
                $dataForm = (array) $data_arr['dataForm'];
                $Lecturer = (array) $data_arr['Lecturer'];
                $Student = (array) $data_arr['Student'];

                if ($ID != '') {

                    $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                    $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->where('ID', $ID);
                    $this->db->update('db_academic.final_project_schedule', $dataForm);

                    $tables = array('db_academic.final_project_schedule_lecturer');
                    $this->db->where('FPSID', $ID);
                    $this->db->delete($tables);
                } else {
                    $dataForm['EntredBy'] = $this->session->userdata('NIP');
                    $this->db->insert('db_academic.final_project_schedule', $dataForm);
                    $ID = $this->db->insert_id();
                }


                // Adding Lecturer
                if (count($Lecturer) > 0) {
                    for ($i = 0; $i < count($Lecturer); $i++) {
                        $tp = ($i == 0) ? '1' : '0';
                        $arr = array(
                            'FPSID' => $ID,
                            'NIP' => $Lecturer[$i],
                            'Type' => $tp
                        );
                        $this->db->insert('db_academic.final_project_schedule_lecturer', $arr);
                    }
                }

                // Adding Std
                if (count($Student) > 0) {
                    for ($i = 0; $i < count($Student); $i++) {
                        $arr = array(
                            'FPSID' => $ID,
                            'NPM' => $Student[$i]
                        );
                        $this->db->insert('db_academic.final_project_schedule_student', $arr);

                        // Cek apakah sudah ada di tabel final projek apa blm jika belum maka akan di tambahkan
                        $ckstd = $this->db->get_where('db_academic.final_project', array('NPM' => $Student[$i]))->result_array();

                        if (count($ckstd) <= 0) {
                            $this->db->insert('db_academic.final_project', array(
                                'NPM' => $Student[$i],
                                'Status' => $data_arr['StatusStd']
                            ));
                        } else {
                            // Update Status
                            $this->db->where('NPM', $Student[$i]);
                            $this->db->update('db_academic.final_project', array(
                                'Status' => $data_arr['StatusStd']
                            ));
                        }
                    }
                }




                return print_r(1);
            } else if ($data_arr['action'] == 'removeStudentSchFP') {
                $ID = $data_arr['ID'];
                $NPM = $data_arr['NPM'];

                $this->db->where('NPM', $NPM);
                $this->db->update('db_academic.final_project', array(
                    'Status' => '1'
                ));

                $this->db->reset_query();

                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.final_project_schedule_student');

                return print_r(1);
            } else if ($data_arr['action'] == 'loadScheduleFPLeccturer') {
                $NIP = $data_arr['NIP'];
                $SemesterID = $data_arr['SemesterID'];
                $Type = $data_arr['Type'];

                $data = $this->db->query('SELECT fps.*, cl.Room FROM db_academic.final_project_schedule_lecturer fpsl
                                                            LEFT JOIN db_academic.final_project_schedule fps ON (fps.ID = fpsl.FPSID)
                                                            LEFT JOIN db_academic.classroom cl ON (cl.ID = fps.ClassroomID)
                                                            WHERE fpsl.NIP = "' . $NIP . '" AND fps.SemesterID = "' . $SemesterID . '"
                                                            AND fps.Type = "' . $Type . '" ')->result_array();

                if (count($data) > 0) {
                    for ($i = 0; $i < count($data); $i++) {
                        // Get Examiner
                        $data[$i]['Examiner'] = $this->db->query('SELECT fpsl.*, em.Name FROM db_academic.final_project_schedule_lecturer fpsl
                                                                              LEFT JOIN db_employees.employees em ON (em.NIP = fpsl.NIP)
                                                                              WHERE fpsl.FPSID = "' . $data[$i]['ID'] . '"
                                                                              ORDER BY fpsl.Type DESC ')->result_array();

                        // Get Student

                        $data[$i]['Students'] = $this->db->query('SELECT fpss.*, ats.Name, fp.Status AS StatusFinalProject, ats.MentorFP1, ats.MentorFP2, em1.Name AS MentorFP1Name, em2.Name AS MentorFP2Name  
                                                                            FROM db_academic.final_project_schedule_student fpss
                                                                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM = fpss.NPM)
                                                                            LEFT JOIN db_employees.employees em1 ON (em1.NIP = ats.MentorFP1)
                                                                            LEFT JOIN db_employees.employees em2 ON (em2.NIP = ats.MentorFP2)
                                                                            LEFT JOIN db_academic.final_project fp ON(fp.NPM = fpss.NPM)
                                                                            WHERE fpss.FPSID = "' . $data[$i]['ID'] . '"
                                                                            ORDER BY fpss.NPM ASC ')->result_array();
                    }
                }

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readStudentFromLCFP') {
                $ID = $data_arr['ID'];
                $data = $this->db->query('SELECT fpss.*, fp.Status AS StatusFinalProject, ats.Name FROM db_academic.final_project_schedule_student fpss
                                                        LEFT JOIN db_academic.final_project fp ON (fp.NPM = fpss.NPM)
                                                        LEFT JOIN db_academic.auth_students ats ON (ats.NPM = fp.NPM)
                                                        WHERE fpss.FPSID = "' . $ID . '" ')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'updateStudentFromLCFP') {

                $this->db->set(array(
                    'Notes' => $data_arr['Notes'],
                    'Status' => $data_arr['Status']
                ));
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.final_project_schedule_student');
                $this->db->reset_query();

                $this->db->set('Status', $data_arr['Status']);
                $this->db->where('NPM', $data_arr['NPM']);
                $this->db->update('db_academic.final_project');

                return print_r(1);
            } else if ($data_arr['action'] == 'nowGenrateStudent') {
                $dataForm = $data_arr['dataForm'];
                for ($i = 0; $i < count($dataForm); $i++) {
                    $d = (array) $dataForm[$i];
                    $arr = array(
                        'NPM' => $d['NPM'],
                        'Code' => $this->m_api->checkCodeIjazah(),
                        'EntredBy' => $this->session->userdata('NIP')
                    );
                    $this->db->insert('db_academic.ijazah', $arr);
                }
                return print_r(1);
            }
        }
    }

    public function getAllDepartementPU()
    {
        $arr_result = $this->m_master->getAllDepartementPU();
        echo json_encode($arr_result);
    }

    public function crudConfigSKPI()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr > 0)) {
            if ($data_arr['action'] == 'readData') {
                $ID = $data_arr['ID'];
                $data = $this->db->get_where(
                    'db_academic.ds_detail',
                    array('DS_TypeID' => $ID),
                    1
                )->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'updateSKPI') {

                $ID = $data_arr['ID'];
                $data = $this->db->get_where(
                    'db_academic.ds_detail',
                    array('DS_TypeID' => $ID),
                    1
                )->result_array();

                $dataUpdate = (array) $data_arr['dataUpdate'];
                // Jika Null Maka Insert
                if (count($data) > 0) {

                    $this->db->where('ID', $data[0]['ID']);
                    $this->db->update('db_academic.ds_detail', $dataUpdate);
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
            } else if ($data_arr['action'] == 'readStudentFromSKPI') {

                $requestData = $_REQUEST;
                $ClassOf = $data_arr['ClassOf'];
                $StatusStudentID = ($data_arr['StatusStudentID'] != '') ? ' AND ats.StatusStudentID = "' . $data_arr['StatusStudentID'] . '" ' : '';
                $WhereProdiID = ($data_arr['ProdiID'] != '') ? ' AND ats.ProdiID = "' . $data_arr['ProdiID'] . '"' : '';


                $dataSearch = '';
                if (!empty($requestData['search']['value'])) {
                    $search = $requestData['search']['value'];
                    $dataSearch = ' AND (  ats.Name LIKE "%' . $search . '%"
                                OR ats.NPM LIKE "%' . $search . '%" )';
                }

                $queryDefault = 'SELECT ats.Name, ats.NPM, ps.Name AS ProdiName, ss.Description FROM db_academic.auth_students ats
                                                LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                LEFT JOIN db_academic.status_student ss ON (ss.ID = ats.StatusStudentID)
                                                WHERE ats.Year = "' . $ClassOf . '" ' . $StatusStudentID . $WhereProdiID . ' ' . $dataSearch . ' ORDER BY ats.NPM ';

                $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (SELECT ats.NPM FROM db_academic.auth_students ats
                                                LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                LEFT JOIN db_academic.status_student ss ON (ss.ID = ats.StatusStudentID)
                                                WHERE ats.Year = "' . $ClassOf . '" ' . $StatusStudentID . $WhereProdiID . ' ' . $dataSearch . ') xx';




                $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

                $query = $this->db->query($sql)->result_array();
                $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

                $no = $requestData['start'] + 1;
                $data = array();

                for ($i = 0; $i < count($query); $i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $dataJudiciums = $this->db->query('SELECT j.* FROM db_academic.judiciums_list jl
                                                        LEFT JOIN db_academic.judiciums j ON (j.ID = jl.JID)
                                                        WHERE jl.NPM = "' . $row['NPM'] . '" ')->result_array();

                    $token = $this->jwt->encode(array('NPM' => $row['NPM']), "UAP)(*");
                    $btnDownloadSKPI = (count($dataJudiciums) > 0)
                        ? '<a href="javascript:void(0);" data-npm="' . $row['NPM'] . '" data-href="' . base_url('save2pdf/cetakSKPI/' . $token) . '"  class="btn btn-sm btn-default btn-default-primary btnDownloadSKPI">Show SKPI</a>' : '-';
                    $JudiciumDate = (count($dataJudiciums) > 0)
                        ? $dataJudiciums[0]['JudiciumsDate'] : '';

                    $viewJD = ($JudiciumDate != '') ? date('d M Y', strtotime($JudiciumDate)) : '<span style="color: red;font-size: 11px;">Students have not been registered in the judicium list</span>';



                    $nestedData[] = '<div>' . $no . '</div>';
                    $nestedData[] = '<div style="text-align: left;"><b>' . $row['Name'] . '</b> - ' . $row['NPM'] . '<br/><span style="color: #2196f3;font-size: 11px;">Status : ' . ucwords(strtolower($row['Description'])) . '</span></div>';
                    $nestedData[] = '<div style="text-align: left;">' . $row['ProdiName'] . '</div>';
                    $nestedData[] = '<div>' . $viewJD . '</div>';
                    $nestedData[] = '<div>' . $btnDownloadSKPI . '</div>';

                    $data[] = $nestedData;
                    $no++;
                }

                $json_data = array(
                    "draw"            => intval($requestData['draw']),
                    "recordsTotal"    => intval($queryDefaultRow),
                    "recordsFiltered" => intval($queryDefaultRow),
                    "data"            => $data
                );
                echo json_encode($json_data);
            }
        }
    }

    public function getListStudent()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();

        $dataWhere = '';

        if (!empty($data_arr['Year']) || !empty($data_arr['ProdiID']) || !empty($data_arr['GroupProdiID']) || !empty($data_arr['StatusStudents']) || !empty($data_arr['approvalStudentReq'])) {

            $w_Year = ($data_arr['Year'] != '') ?  ' AND aut_s.Year = "' . $data_arr['Year'] . '"' : '';
            $w_ProdiID = ($data_arr['ProdiID'] != '') ?  ' AND aut_s.ProdiID = "' . $data_arr['ProdiID'] . '"' : '';
            $w_GroupProdiID = ($data_arr['GroupProdiID'] != '') ?  ' AND aut_s.ProdiGroupID = "' . $data_arr['GroupProdiID'] . '"' : '';
            $w_StatusStudents = ($data_arr['StatusStudents'] != '') ?  ' AND aut_s.StatusStudentID = "' . $data_arr['StatusStudents'] . '"' : '';
            $w_NeedApprovalReq = ($data_arr['approvalStudentReq'] != '') ?  ' AND (ts.ID is not null  and ts.isApproval = 1)' : '';

            $dataWherePlan = 'WHERE (' . $w_Year . '' . $w_ProdiID . '' . $w_GroupProdiID . '' . $w_StatusStudents . '' . $w_NeedApprovalReq . ')';

            $exp_w = explode(' ', $dataWherePlan);
            if (count($exp_w) > 0) {
                for ($i = 0; $i < count($exp_w); $i++) {
                    if ($i != 2) {
                        $dataWhere = $dataWhere . ' ' . trim($exp_w[$i]);
                    }
                }
            }
        }


        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];

            if ($dataWhere != '') {
                $dataSearch = 'AND ( aut_s.Name LIKE "%' . $search . '%" OR aut_s.NPM LIKE "%' . $search . '%"
                           OR ps.Name LIKE "%' . $search . '%"  OR ps.NameEng LIKE "%' . $search . '%" ';
            } else {
                $dataSearch = 'WHERE ( aut_s.Name LIKE "%' . $search . '%" OR aut_s.NPM LIKE "%' . $search . '%"
                           OR ps.Name LIKE "%' . $search . '%"  OR ps.NameEng LIKE "%' . $search . '%" ';
            }

            // adding search Formulir Number
            $dataSearch .= ' or fma.FormulirCode LIKE "%' . $search . '%" or fma.No_Ref LIKE "%' . $search . '%" )';
        }

        $queryDefault = 'SELECT aut_s.*, ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng, ss.Description AS StatusStudent,
                                      pg.Code AS ProdiGroup,fma.FormulirCode,fma.No_Ref
                                      FROM db_academic.auth_students aut_s
                                      LEFT JOIN db_academic.program_study ps ON (ps.ID = aut_s.ProdiID)
                                      LEFT JOIN db_academic.prodi_group pg ON (pg.ID = aut_s.ProdiGroupID)
                                      LEFT JOIN db_academic.status_student ss ON (ss.ID = aut_s.StatusStudentID)
                                      LEFT JOIN (
                                        select a.NPM,a.FormulirCode,a.GeneratedBy,a.DateTime as DateTimeGeneratedBy,dd.No_Ref
                                        from db_admission.to_be_mhs as a
                                        left join (
                                            select FormulirCode,No_Ref from db_admission.formulir_number_offline_m
                                            UNION
                                            select FormulirCode,No_Ref from db_admission.formulir_number_online_m
                                        ) dd on a.FormulirCode = dd.FormulirCode
                                      ) as fma on fma.NPM = aut_s.NPM
        /*Added by Febri @ Nov 2019*/ LEFT JOIN db_academic.tmp_students ts on (ts.NPM = aut_s.NPM) /*End Added by Febri @ Nov 2019*/
                                      ' . $dataWhere . ' ' . $dataSearch . ' ORDER BY aut_s.NPM ASC ';

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();
        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $db_ = 'ta_' . $row['Year'];
            $dataDetailStd = $this->db->select('Photo,Gender')->get_where($db_ . '.students', array('NPM' => $row['NPM']), 1)->result_array();

            $dataToken = array(
                'Type' => 'std',
                'Name' => $row['Name'],
                'NPM' => $row['NPM'],
                'Email' => $row['EmailPU']
            );

            $token = $this->jwt->encode($dataToken, 'UAP)(*');

            $disBtnEmail = ($row['EmailPU'] == '' || $row['EmailPU'] == '') ? 'disabled' : '';

            $nameS = str_replace(' ', '-', ucwords(strtolower($row['Name'])));
            $srcImage = base_url('images/icon/userfalse.png');
            if ($dataDetailStd[0]["Photo"] != '' && $dataDetailStd[0]["Photo"] != null) {
                $urlImg = './uploads/students/' . $db_ . '/' . $dataDetailStd[0]["Photo"];
                $srcImage = (file_exists($urlImg)) ? base_url('uploads/students/' . $db_ . '/' . $dataDetailStd[0]["Photo"]) : base_url('images/icon/userfalse.png');
            }
            $btnAct = '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="' . $disBtnEmail . '"><a href="javascript:void(0);" ' . $disBtnEmail . ' class="btn-reset-password ' . $disBtnEmail . '" data-token="' . $token . '">Reset Password</a></li>
                            <li><a href="' . base_url('database/students/edit-students/' . $db_ . '/' . $row['NPM'] . '/' . $nameS) . '">Edit (Coming Soon)</a></li>';

            /*UPDATED BY FEBRI @ NOV 2019*/
            $isRequested = $this->General_model->fetchData("db_academic.tmp_students", array("NPM" => $row['NPM'], "isApproval" => 1))->row();
            $requested = (!empty($isRequested) ? '' : 'disabled');
            $btnAct .=      '<li class="' . $requested . '"><a  href="javascript:void(0);" class="show-request" data-npm="' . $row['NPM'] . '" data-ta="' . $row['Year'] . '">Request Approval</a></li>';
            /*END UPDATED BY FEBRI @ NOV 2019*/

            $btnAct .=      '<li role="separator" class="divider"></li>
                            <li><a href="javascript:void(0);" class="btn-change-status " data-emailpu="' . $row['EmailPU'] . '"
                            data-year="' . $row['Year'] . '" data-npm="' . $row['NPM'] . '" data-name="' . ucwords(strtolower($row['Name'])) . '"
                            data-statusid="' . $row['StatusStudentID'] . '">Change Status</a>
                            </li>
                            <li><a class = "PrintIDCard" href="javascript:void(0);" type = "student" data-npm="' . $row['NPM'] . '" data-name="' . ucwords(strtolower($row['Name'])) . '" path = ' . $srcImage . ' email = "' . $row['EmailPU'] . '">Print ID Card</a></li>
                          </ul>
                        </div>';

            $fm = '<input id="formTypeImage' . $row['NPM'] . '" class="hide" />
            <form id="fmPhoto' . $row['NPM'] . '" enctype="multipart/form-data" accept-charset="utf-8" method="post" action="">
                                <input id="formPhoto" class="hide" value="" hidden />
                                <div class="form-group"><label class="btn btn-sm btn-default btn-default-warning btn-upload">
                                        <i class="fa fa-upload"></i>
                                        <input type="file" id="filePhoto" name="userfile" data-db="' . $db_ . '" data-npm="' . $row['NPM'] . '" class="uploadPhotoEmp"
                                               style="display: none;" accept="image/*">
                                    </label>
                                </div>
                            </form>';

            $gp = ($row['ProdiGroupID'] != '' && $row['ProdiGroupID'] != null) ? ' - ' . $row['ProdiGroup'] : '';

            // show formulir number
            $StrFM = ($row['FormulirCode'] != null && $row['FormulirCode'] != 'null' && $row['FormulirCode'] != "" && (!empty($row['FormulirCode']))) ? '<br/><span style="color: #20525a;">' . $row['FormulirCode'] . ' / ' . $row['No_Ref'] . '</span>' : '';

            $IDCard = ($row['Access_Card_Number'] != '' && $row['Access_Card_Number'] != null) ? $row['Access_Card_Number'] : '-';


            // Cuma akademik yang bisa edit dan upload foto
            $DeptID = $this->session->userdata('IDdepartementNavigation');
            $btnAct = ($DeptID == 6 || $DeptID == '6') ? $btnAct : '-';
            $fm = ($DeptID == 6 || $DeptID == '6') ? $fm : '-';



            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            /*UPDATED BY FEBRI @ NOV 2019*/
            if ($DeptID == 6 || $DeptID == '6') {
                $needAppv = (!empty($isRequested) ? '<br><span class="btn btn-xs btn-info show-request" title="Need Accepting Request" data-npm="' . $row['NPM'] . '" data-ta="' . $row['Year'] . '" > <i class="fa fa-warning"></i> Need Approval</span>' : '');
            } else {
                $needAppv = "";
            }
            $nestedData[] = '<div  style="text-align:center;">' . $row['NPM'] . $needAppv   . '</div>';
            /*END UPDATED BY FEBRI @ NOV 2019*/
            $nestedData[] = '<div  style="text-align:center;"><img id="imgThum' . $row['NPM'] . '" src="' . $srcImage . '" style="max-width: 35px;" class="img-rounded"></div>';
            $nestedData[] = '<div  style="text-align:left;"><a href="javascript:void(0);" data-npm="' . $row['NPM'] . '" data-ta="' . $row['Year'] . '" class="btnDetailStudent"><b>' . ucwords(strtolower($row['Name'])) . '</b></a>
                                                            <br/><span style="color: #c77905;">' . $row['EmailPU'] . '</span>' . $StrFM . '</div>
                                                            <hr style="margin-top: 3px;margin-bottom: 5px;"/>IDCard : ' . $IDCard;
            $nestedData[] = '<div  style="text-align:center;">' . $row['Year'] . '' . $gp . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $row['ProdiNameEng'] . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $fm . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $btnAct . '</div>';
            $nestedData[] = '<div  style="text-align:center;"><button class="btn btn-sm btn-default btn-default-primary btnLoginPortalStudents" data-npm="' . $row['NPM'] . '">Login Portal</button></div>';
            $nestedData[] = '<div  style="text-align:center;">' . ucwords(strtolower($row['StatusStudent'])) . '</div>';
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


    /*ADDED BY FEBRI @ JAN 2020*/
    public function getListStudentPS()
    {
        $this->load->model("global-informations/Globalinformation_model");
        $reqdata = $this->input->post();
        if ($reqdata) {
            $key = "UAP)(*";
            $data_arr = (array) $this->jwt->decode($reqdata['token'], $key);
            $param = array();
            $orderBy = " NPM DESC ";
            if (!empty($data_arr['Filter'])) {
                $parse = parse_str($data_arr['Filter'], $output);
                if (!empty($output['student'])) {
                    $param[] = array("field" => "(ta.`Name`", "data" => " like '%" . $output['student'] . "%' ", "filter" => "AND",);
                    $param[] = array("field" => "ta.`NPM`", "data" => " like '%" . $output['student'] . "%' ", "filter" => "OR",);
                    $param[] = array("field" => "ath.`EmailPU`", "data" => " like '%" . $output['student'] . "%') ", "filter" => "OR",);
                }
                if (!empty($output['Year'])) {
                    $param[] = array("field" => "ta.`ClassOf`", "data" => " =" . $output['Year'] . " ", "filter" => "AND",);
                }/*else{
                    $getCurrentSemes = $this->General_model->fetchData("db_academic.semester",array("status"=>1))->row();
                    $param[] = array("field"=>"ta.`ClassOf`","data"=>" =".$getCurrentSemes->Year." ","filter"=>"AND",);
                }*/
                if (!empty($output['ProdiID'])) {
                    $param[] = array("field" => "ta.`ProdiID`", "data" => " =" . $output['ProdiID'] . " ", "filter" => "AND",);
                }
                if (!empty($output['GroupProdiID'])) {
                    $param[] = array("field" => "ath.`ProdiGroupID`", "data" => " =" . $output['GroupProdiID'] . " ", "filter" => "AND",);
                }

                if (!empty($output['status'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['status']) == 1) {
                        $param[] = array("field" => "ss.`CodeStatus`", "data" => " ='" . $output['status'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['status'] as $s) {
                            $param[] = array("field" => "ss.`CodeStatus`", "data" => " ='" . $s . "' " . ((($sn < count($output['status'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }
                if (!empty($output['religion'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['religion']) == 1) {
                        $param[] = array("field" => "ag.`ID`", "data" => " ='" . $output['religion'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['religion'] as $s) {
                            $param[] = array("field" => "ag.`ID`", "data" => " ='" . $s . "' " . ((($sn < count($output['religion'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }
                if (!empty($output['gender'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['gender']) == 1) {
                        $param[] = array("field" => "ta.`Gender`", "data" => " ='" . $output['gender'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['gender'] as $s) {
                            $param[] = array("field" => "ta.`Gender`", "data" => " ='" . $s . "' " . ((($sn < count($output['gender'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }
                if (!empty($output['graduation_year'])) {
                    $param[] = array("field" => "ath.`GraduationYear`", "data" => " =" . $output['graduation_year'] . " ", "filter" => "AND",);
                }
                if (!empty($output['graduation_start'])) {
                    if (!empty($output['graduation_end'])) {
                        $param[] = array("field" => "(ath.`GraduationDate`", "data" => " >= '" . date("Y-m-d", strtotime($output['graduation_start'])) . "' ", "filter" => "AND",);
                        $param[] = array("field" => "ath.`GraduationDate`", "data" => " <= '" . date("Y-m-d", strtotime($output['graduation_end'])) . "' )", "filter" => "AND",);
                    } else {
                        $param[] = array("field" => "ath.`GraduationDate`", "data" => " >= '" . date("Y-m-d", strtotime($output['graduation_start'])) . "' ", "filter" => "AND",);
                    }
                }
                if (!empty($output['birthdate_start'])) {
                    if (!empty($output['birthdate_end'])) {
                        $param[] = array("field" => "(ta.DateOfBirth", "data" => " >= '" . date("Y-m-d", strtotime($output['birthdate_start'])) . "' ", "filter" => "AND",);
                        $param[] = array("field" => "ta.DateOfBirth", "data" => " <= '" . date("Y-m-d", strtotime($output['birthdate_end'])) . "' )", "filter" => "AND",);
                    } else {
                        $param[] = array("field" => "ta.DateOfBirth", "data" => " >= '" . date("Y-m-d", strtotime($output['birthdate_start'])) . "' ", "filter" => "AND",);
                    }
                }

                // Filter Portal Alumni is active or not
                if (!empty($output['isPortalAlumi'])) {
                    if ($output['isPortalAlumi'] == 1) {
                        $param[] = array("field" => "(select count(*) as total from `db_alumni`.`registration` as alm where alm.NPM = ta.NPM limit 1  )", "data" => " > 0 ", "filter" => "AND",);
                    } else if ($output['isPortalAlumi'] == 0) {
                        $param[] = array("field" => "(select count(*) as total from `db_alumni`.`registration` as alm where alm.NPM = ta.NPM limit 1  )", "data" => " = 0 ", "filter" => "AND",);
                    }
                }

                /*SORTING*/
                if (!empty($output['sortby']) && !empty($output['orderby'])) {
                    $orderBy = $output['sortby'] . " " . $output['orderby'];
                }
                /*END SORTING*/

                /*NEED APPROVED PROFILE*/
                if (!empty($output['isapprove'])) {
                    if ($output['isapprove']) {
                        $param[] = array("field" => "ts.isApproval", "data" => " = 1 ", "filter" => "AND",);
                    }
                }
                /*END NEED APPROVED PROFILE*/
            }

            if (!empty($reqdata['search']['value'])) {
                $search = $reqdata['search']['value'];

                $param[] = array("field" => "(ta.`Name`", "data" => " like '%" . $search . "%' ", "filter" => "AND",);
                $param[] = array("field" => "ta.`NPM`", "data" => " like '%" . $search . "%' ", "filter" => "OR",);
                $param[] = array("field" => "ps.`NameEng`", "data" => " like '%" . $search . "%' )", "filter" => "OR",);
            }

            $data = array();
            $totalData = $this->Globalinformation_model->fetchStudentsPS(true, false, $param);
            $TotalDataPS = (!empty($totalData) ? $totalData->Total : 0);
            $result = $this->Globalinformation_model->fetchStudentsPS(false, false, $param, $reqdata['start'], $reqdata['length'], $orderBy);
            $no = $reqdata['start'] + 1;
            if ($result) {
                foreach ($result as $v) {
                    // $url_image = 'uploads/students/ta_'.$v->ClassOf.'/'.$v->Photo;
                    // $srcImg =  base_url('images/icon/userfalse.png');
                    // if($v->Photo != '' || $v->Photo != null || !empty($v->Photo)){
                    //     $srcImg = (file_exists($url_image)) ? base_url($url_image) : base_url('images/icon/userfalse.png') ;
                    // }

                    $url_image = base_url() . 'uploads/students/ta_' . $v->ClassOf . '/' . $v->Photo;
                    $srcImg =  base_url('images/icon/userfalse.png');
                    if ($v->Photo != '' || $v->Photo != null || !empty($v->Photo)) {
                        if (!$this->m_master->is_url_exist($url_image)) {
                            $srcImg = (@file_get_contents($url_image, 0, NULL, 0, 1)) ? $url_image : base_url('images/icon/userfalse.png');
                        } else {
                            $srcImg = $url_image;
                        }
                    }

                    $dataToken = array(
                        'Type' => 'std',
                        'Name' => $v->Name,
                        'NPM' => $v->NPM,
                        'Email' => $v->EmailPU
                    );

                    $token = $this->jwt->encode($dataToken, 'UAP)(*');
                    $nameS = str_replace(' ', '-', ucwords(strtolower($v->Name)));

                    $disBtnEmail = (!empty($v->EmailPU) ? '' : 'disabled');

                    $btnAct = '<div class="btn-group">
                                  <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu">
                                    <li class="' . $disBtnEmail . '"><a href="javascript:void(0);" ' . $disBtnEmail . ' class="btn-reset-password ' . $disBtnEmail . '" data-token="' . $token . '">Reset Password</a></li>
                                    <li><a href="' . base_url('database/students/edit-students/ta_' . $v->ClassOf . '/' . $v->NPM . '/' . $nameS) . '">Edit</a></li>';
                    $ActSetAlumni = ($v->StatusStudentID == 1) ? '<li role="separator" class="divider"></li>
                                    <li><a class = "BtnSetAlumni" href = "javascript:void(0);" data-npm="' . $v->NPM . '" data-alumni = "' . $v->StatusPortalAlumni . '" data-name = "' . $v->Name . '" > Set Portal Alumni </a>
                                    </li>' : '';
                    $btnAct .=      '<li role="separator" class="divider"></li>
                                    <li><a href="javascript:void(0);" class="btn-change-status " data-emailpu="' . $v->EmailPU . '"
                                    data-year="' . $v->ClassOf . '" data-npm="' . $v->NPM . '" data-name="' . ucwords(strtolower($v->Name)) . '"
                                    data-statusid="' . $v->StatusStudentID . '">Change Status</a>
                                    </li>
                                    <li><a class = "PrintIDCard" href="javascript:void(0);" type = "student" data-npm="' . $v->NPM . '" data-name="' . ucwords(strtolower($v->Name)) . '" path = ' . $srcImg . ' email = "' . $v->EmailPU . '">Print ID Card</a>
                                    </li>
                                    ' . $ActSetAlumni . '
                                  </ul>
                                </div>';

                    $fm = '<input id="formTypeImage' . $v->NPM . '" class="hide" />
                    <form id="fmPhoto' . $v->NPM . '" enctype="multipart/form-data" accept-charset="utf-8" method="post" action="">
                                        <input id="formPhoto" class="hide" value="" hidden />
                                        <div class="form-group"><label class="btn btn-sm btn-default btn-default-warning btn-upload">
                                                <i class="fa fa-upload"></i>
                                                <input type="file" id="filePhoto" name="userfile" data-db="ta_' . $v->ClassOf . '" data-npm="' . $v->NPM . '" class="uploadPhotoEmp"
                                                       style="display: none;" accept="image/*">
                                            </label>
                                        </div>
                                    </form>';

                    $gp = (!empty($v->ProdiGroupID)) ? ' - ' . $v->ProdiGroup : '';

                    // show formulir number
                    $StrFM = (!empty($v->FormulirCode)) ? '<br/><span style="color: #20525a;">' . $v->FormulirCode . ' / ' . $v->No_Ref . '</span>' : '';

                    $IDCard = (!empty($v->Access_Card_Number)) ? $v->Access_Card_Number : '-';


                    // Cuma akademik yang bisa edit dan upload foto
                    $DeptID = $this->session->userdata('IDdepartementNavigation');
                    $btnAct = ($DeptID == 6 || $DeptID == '6') ? $btnAct : '-';
                    $fm = ($DeptID == 6 || $DeptID == '6') ? $fm : '-';


                    $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
                    /*UPDATED BY FEBRI @ NOV 2019*/
                    if ($DeptID == 6 || $DeptID == '6') {
                        $needAppv = (($v->isApproved == 1) ? '<span style="width:100%" class="btn btn-sm btn-info show-request" title="Need Accepting Request" data-npm="' . $v->NPM . '" data-ta="' . $v->ClassOf . '" > <i class="fa fa-warning"></i> Need Approval</span>' : '');
                    } else {
                        $needAppv = "";
                    }

                    $studentBox = '<div class="detail-user" data-user="' . $v->NPM . '">
                                    <img class="std-img img-rounded" src="' . $srcImg . '" >
                                    <p class="npm">' . $v->NPM . '</p>
                                    <p class="name">' . $v->Name . '</p>
                                    <p class="email"><i class="fa fa-envelope-o"></i> ' . (!empty($v->EmailPU) ? $v->EmailPU : "-") . '</p>
                                   </div><div style="margin-top:10px">' . $needAppv . '</div>';
                    $nestedData = array();
                    $nestedData[] = ($no++);
                    $nestedData[] = $studentBox;
                    $nestedData[] = "<p class='text-left'>" . (!empty($v->PlaceOfBirth) ? $v->PlaceOfBirth . ",<br>" : "") . date("d F Y", strtotime($v->DateOfBirth)) . "</p>";
                    $nestedData[] = "<p class='text-center'>" . $v->religionName . "</p>";
                    $nestedData[] = "<p class='text-center'>" . (($v->Gender == "L") ? 'Male' : 'Female') . "</p>";
                    $nestedData[] = "<center>" . $v->ClassOf . "</center>";
                    $nestedData[] = $v->ProdiNameEng;

                    // check Direct Portal ALumni
                    $PortalAlumniLabel = ($v->StatusPortalAlumni > 0) ? '<p style = "color:blue;">Direct Portal Alumni</p>' : '<p style = "color:blue;">Direct Portal Student</p>';
                    $nestedData[] = (($v->StatusStudentID == 1) ? $v->StatusStudent . "<p>Graduated in " . (!empty($v->GraduationYear) ? $v->GraduationYear : date('Y', strtotime($v->GraduationDate))) . ", <br><small><i class='fa fa-graduation-cap'></i> " . date('D,d F Y', strtotime($v->GraduationDate)) . "</small></p>
                        " . $PortalAlumniLabel . "
                        " : $v->StatusStudent);
                    $nestedData[] = '<div style="text-align:center;">' . $fm . '</div>';
                    $nestedData[] = $btnAct;
                    $nestedData[] = '<div style="text-align:center;"><button class="btn btn-sm btn-default btn-default-primary btnLoginPortalStudents" data-npm="' . $v->NPM . '">Login Portal</button></div>';
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($reqdata['draw']),
                "recordsTotal"    => intval($TotalDataPS),
                "recordsFiltered" => intval($TotalDataPS),
                "data"            => $data
            );
        } else {
            $json_data = null;
        }

        echo json_encode($json_data);
    }


    public function fetchStudentsObj()
    {
        $this->load->model("global-informations/Globalinformation_model");
        $reqdata = $this->input->post();
        if ($reqdata) {
            $key = "UAP)(*";
            $data_arr = (array) $this->jwt->decode($reqdata['token'], $key);
            $param = array();
            $orderBy = " NPM DESC ";
            if (!empty($data_arr['Filter'])) {
                $parse = parse_str($data_arr['Filter'], $output);
                if (!empty($output['student'])) {
                    $param[] = array("field" => "(ta.`Name`", "data" => " like '%" . $output['student'] . "%' ", "filter" => "AND",);
                    $param[] = array("field" => "ta.`NPM`", "data" => " like '%" . $output['student'] . "%' ", "filter" => "OR",);
                    $param[] = array("field" => "ath.`EmailPU`", "data" => " like '%" . $output['student'] . "%') ", "filter" => "OR",);
                }
                if (!empty($output['Year'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['Year']) == 1) {
                        $param[] = array("field" => "ta.`ClassOf`", "data" => " ='" . $output['Year'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['Year'] as $s) {
                            $param[] = array("field" => "ta.`ClassOf`", "data" => " ='" . $s . "' " . ((($sn < count($output['Year'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }

                if (!empty($output['ProdiID'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['ProdiID']) == 1) {
                        $param[] = array("field" => "ta.`ProdiID`", "data" => " ='" . $output['ProdiID'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['ProdiID'] as $s) {
                            $param[] = array("field" => "ta.`ProdiID`", "data" => " ='" . $s . "' " . ((($sn < count($output['ProdiID'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }

                if (!empty($output['status'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['status']) == 1) {
                        $param[] = array("field" => "ss.`CodeStatus`", "data" => " ='" . $output['status'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['status'] as $s) {
                            $param[] = array("field" => "ss.`CodeStatus`", "data" => " ='" . $s . "' " . ((($sn < count($output['status'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }
                if (!empty($output['religion'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['religion']) == 1) {
                        $param[] = array("field" => "ag.`ID`", "data" => " ='" . $output['religion'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['religion'] as $s) {
                            $param[] = array("field" => "ag.`ID`", "data" => " ='" . $s . "' " . ((($sn < count($output['religion'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }
                if (!empty($output['gender'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['gender']) == 1) {
                        $param[] = array("field" => "ta.`Gender`", "data" => " ='" . $output['gender'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['gender'] as $s) {
                            $param[] = array("field" => "ta.`Gender`", "data" => " ='" . $s . "' " . ((($sn < count($output['gender'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }
                if (!empty($output['graduation_year'])) {
                    $param[] = array("field" => "ath.`GraduationYear`", "data" => " =" . $output['graduation_year'] . " ", "filter" => "AND",);
                }
                if (!empty($output['graduation_start'])) {
                    if (!empty($output['graduation_end'])) {
                        $param[] = array("field" => "(ath.`GraduationDate`", "data" => " >= '" . date("Y-m-d", strtotime($output['graduation_start'])) . "' ", "filter" => "AND",);
                        $param[] = array("field" => "ath.`GraduationDate`", "data" => " <= '" . date("Y-m-d", strtotime($output['graduation_end'])) . "' )", "filter" => "AND",);
                    } else {
                        $param[] = array("field" => "ath.`GraduationDate`", "data" => " >= '" . date("Y-m-d", strtotime($output['graduation_start'])) . "' ", "filter" => "AND",);
                    }
                }
                if (!empty($output['birthdate_start'])) {
                    if (!empty($output['birthdate_end'])) {
                        $param[] = array("field" => "(ta.DateOfBirth", "data" => " >= '" . date("Y-m-d", strtotime($output['birthdate_start'])) . "' ", "filter" => "AND",);
                        $param[] = array("field" => "ta.DateOfBirth", "data" => " <= '" . date("Y-m-d", strtotime($output['birthdate_end'])) . "' )", "filter" => "AND",);
                    } else {
                        $param[] = array("field" => "ta.DateOfBirth", "data" => " >= '" . date("Y-m-d", strtotime($output['birthdate_start'])) . "' ", "filter" => "AND",);
                    }
                }

                /*SORTING*/
                if (!empty($output['sortby']) && !empty($output['orderby'])) {
                    $orderBy = $output['sortby'] . " " . $output['orderby'];
                }
                /*END SORTING*/

                /*NEED APPROVED PROFILE*/
                if (!empty($output['isapprove'])) {
                    if ($output['isapprove']) {
                        $param[] = array("field" => "ts.isApproval", "data" => " = 1 ", "filter" => "AND",);
                    }
                }
                /*END NEED APPROVED PROFILE*/
            }

            if (!empty($reqdata['search']['value'])) {
                $search = $reqdata['search']['value'];

                $param[] = array("field" => "(ta.`Name`", "data" => " like '%" . $search . "%' ", "filter" => "AND",);
                $param[] = array("field" => "ta.`NPM`", "data" => " like '%" . $search . "%' ", "filter" => "OR",);
                $param[] = array("field" => "ps.`NameEng`", "data" => " like '%" . $search . "%' )", "filter" => "OR",);
            }

            $data = array();
            $totalData = $this->Globalinformation_model->fetchStudentsPS(true, false, $param);
            $TotalDataPS = (!empty($totalData) ? $totalData->Total : 0);
            $result = $this->Globalinformation_model->fetchStudentsPS(false, false, $param, (!empty($reqdata['start']) ? $reqdata['start'] : 0), (!empty($reqdata['length']) ? $reqdata['length'] : 0), $orderBy);

            $json_data = array(
                "draw"            => intval($reqdata['draw']),
                "recordsTotal"    => intval($TotalDataPS),
                "recordsFiltered" => intval($TotalDataPS),
                "data"            => (!empty($result) ? $result : 0)
            );
        } else {
            $json_data = null;
        }

        $response = $json_data;
        echo json_encode($response);
    }


    public function fetchEmployeeObj()
    {
        $this->load->model("global-informations/Globalinformation_model");
        $reqdata = $this->input->post();
        if ($reqdata) {
            $key = "UAP)(*";
            $data_arr = (array) $this->jwt->decode($reqdata['token'], $key);
            $param = array();
            $orderBy = " em.ID DESC ";

            if (!empty($reqdata['search']['value'])) {
                $search = $reqdata['search']['value'];

                $param[] = array("field" => "(em.NIP", "data" => " like '%" . $search . "%' ", "filter" => "AND",);
                $param[] = array("field" => "em.Name", "data" => " like '%" . $search . "%' ", "filter" => "OR",);
                $param[] = array("field" => "em.NIDN", "data" => " like '%" . $search . "%' )", "filter" => "OR",);
            }
            if (!empty($data_arr['Filter'])) {
                $parse = parse_str($data_arr['Filter'], $output);

                //check data emp if lecturers
                if (!empty($output['isLecturer'])) {
                    $divLect = '14';
                    $param[] = array("field" => "(em.PositionMain", "data" => " like'" . $divLect . ".%' ", "filter" => "AND",);
                    $param[] = array("field" => "em.PositionOther1", "data" => " like'" . $divLect . ".%' ", "filter" => "OR",);
                    $param[] = array("field" => "em.PositionOther2", "data" => " like'" . $divLect . ".%' ", "filter" => "OR",);
                    $param[] = array("field" => "em.PositionOther3", "data" => " like'" . $divLect . ".%' )", "filter" => "OR",);
                    if (!empty($output['position'])) {
                        $param[] = array("field" => "em.PositionMain", "data" => " = '" . $divLect . "." . $output['position'] . "' ", "filter" => "AND",);
                    }
                    if (!empty($output['status'])) {
                        $sn = 1;
                        $dataArrStatus = array();
                        $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                        if (count($output['status']) == 1) {
                            $param[] = array("field" => "em.`StatusLecturerID`", "data" => " ='" . $output['status'][0] . "' ", "filter" => "");
                        } else {
                            foreach ($output['status'] as $s) {
                                $param[] = array("field" => "em.`StatusLecturerID`", "data" => " ='" . $s . "' " . ((($sn < count($output['status'])) ? ' OR ' : '')), "filter" => null);
                                $sn++;
                            }
                        }
                        $param[] = array("field" => ")", "data" => null, "filter" => null);
                    }
                    if (!empty($output['study_program'])) {
                        $sn = 1;
                        $dataArrStatus = array();
                        $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                        if (count($output['study_program']) == 1) {
                            $param[] = array("field" => "em.ProdiID", "data" => " ='" . $output['study_program'][0] . "' ", "filter" => "");
                        } else {
                            foreach ($output['study_program'] as $s) {
                                $param[] = array("field" => "em.ProdiID", "data" => " ='" . $s . "' " . ((($sn < count($output['study_program'])) ? ' OR ' : '')), "filter" => null);
                                $sn++;
                            }
                        }
                        $param[] = array("field" => ")", "data" => null, "filter" => null);
                    }
                }
                //check data for employee
                else {
                    if (!empty($output['division'])) {
                        $param[] = array("field" => "em.PositionMain", "data" => " like '" . $output['division'] . ".%' ", "filter" => "AND",);
                    }
                    if (!empty($output['division']) && !empty($output['position'])) {
                        $param[] = array("field" => "em.PositionMain", "data" => " = '" . $output['division'] . "." . $output['position'] . "' ", "filter" => "AND",);
                    }

                    if (!empty($output['status'])) {
                        $sn = 1;
                        $dataArrStatus = array();
                        $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                        if (count($output['status']) == 1) {
                            $param[] = array("field" => "em.`StatusEmployeeID`", "data" => " ='" . $output['status'][0] . "' ", "filter" => "");
                        } else {
                            foreach ($output['status'] as $s) {
                                $param[] = array("field" => "em.`StatusEmployeeID`", "data" => " ='" . $s . "' " . ((($sn < count($output['status'])) ? ' OR ' : '')), "filter" => null);
                                $sn++;
                            }
                        }
                        $param[] = array("field" => ")", "data" => null, "filter" => null);
                    }
                }


                if (!empty($output['staff'])) {
                    $param[] = array("field" => "(em.NIP", "data" => " like '%" . $output['staff'] . "%' ", "filter" => "AND",);
                    $param[] = array("field" => "ps.NameEng", "data" => " like '%" . $output['staff'] . "%' ", "filter" => "OR",);
                    $param[] = array("field" => "em.Name", "data" => " like '%" . $output['staff'] . "%' )", "filter" => "OR",);
                }
                if (!empty($output['religion'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['religion']) == 1) {
                        $param[] = array("field" => "em.ReligionID", "data" => " ='" . $output['religion'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['religion'] as $s) {
                            $param[] = array("field" => "em.ReligionID", "data" => " ='" . $s . "' " . ((($sn < count($output['religion'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }
                if (!empty($output['gender'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['gender']) == 1) {
                        $param[] = array("field" => "em.Gender", "data" => " ='" . $output['gender'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['gender'] as $s) {
                            $param[] = array("field" => "em.Gender", "data" => " ='" . $s . "' " . ((($sn < count($output['gender'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }
                if (!empty($output['level_education'])) {
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field" => "(", "data" => null, "filter" => "AND");
                    if (count($output['level_education']) == 1) {
                        $param[] = array("field" => "em.LevelEducationID", "data" => " ='" . $output['level_education'][0] . "' ", "filter" => "");
                    } else {
                        foreach ($output['level_education'] as $s) {
                            $param[] = array("field" => "em.LevelEducationID", "data" => " ='" . $s . "' " . ((($sn < count($output['level_education'])) ? ' OR ' : '')), "filter" => null);
                            $sn++;
                        }
                    }
                    $param[] = array("field" => ")", "data" => null, "filter" => null);
                }

                if (!empty($output['birthdate_start'])) {
                    if (!empty($output['birthdate_end'])) {
                        $param[] = array("field" => "(em.DateOfBirth", "data" => " >= '" . date("Y-m-d", strtotime($output['birthdate_start'])) . "' ", "filter" => "AND",);
                        $param[] = array("field" => "em.DateOfBirth", "data" => " <= '" . date("Y-m-d", strtotime($output['birthdate_end'])) . "' )", "filter" => "AND",);
                    } else {
                        $param[] = array("field" => "em.DateOfBirth", "data" => " >= '" . date("Y-m-d", strtotime($output['birthdate_start'])) . "' ", "filter" => "AND",);
                    }
                }

                if (!empty($output['sorted'])) {
                    $orderBy = $output['sorted'];
                }
            }

            $totalData = $this->Globalinformation_model->fetchEmployee(true, $param)->row();
            $TotalData = (!empty($totalData) ? $totalData->Total : 0);
            if (!empty($reqdata['length'])) {
                $result = $this->Globalinformation_model->fetchEmployee(false, $param, $reqdata['start'], $reqdata['length'], $orderBy)->result();
            } else {
                $result = $this->Globalinformation_model->fetchEmployee(false, $param)->result();
            }

            $json_data = array(
                "draw"            => intval((!empty($reqdata['draw']) ? $reqdata['draw'] : null)),
                "recordsTotal"    => intval($TotalData),
                "recordsFiltered" => intval($TotalData),
                "data"            => (!empty($result) ? $result : 0)
            );
        } else {
            $json_data = null;
        }
        $response = $json_data;
        echo json_encode($response);
    }


    /*END ADDED BY FEBRI @ JAN 2020*/

    public function getListEmployees()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();

        /*$dataWhere = ($data_arr['StatusEmployeeID']!='' && $data_arr['StatusEmployeeID']!=null) ?
            'AND StatusEmployeeID = "'.$data_arr['StatusEmployeeID'].'" '
            : '' ;
        */
        /*UPDATED BY FEBRI @  JAN 2020*/
        $dataWhere = "";
        $dataArrStatus = array();
        $dataArrGender = array();
        $dataArrReligion = array();
        $dataArrLevel = array();
        if (!empty($data_arr['Filter'])) {
            $parse = parse_str($data_arr['Filter'], $output);
            if (!empty($output['staff'])) {
                $dataWhere .= " AND (em.NIP like '%" . $output['staff'] . "%' OR em.Name  like '%" . $output['staff'] . "%' ) ";
            }
            if (!empty($output['division'])) {
                $conditionDivPo = " AND (em.PositionMain like '" . $output['division'] . ".%')";
                if (!empty($output['position'])) {
                    $conditionDivPo = " AND (em.PositionMain = '" . $output['division'] . "." . $output['position'] . "')";
                }
                $dataWhere .= $conditionDivPo;
            }
            if (!empty($output['birthdate_start'])) {
                $conditionBirthDate = " AND (em.DateOfBirth >= '" . date("Y-m-d", strtotime($output['birthdate_start'])) . "')";
                if (!empty($output['birthdate_end'])) {
                    $conditionBirthDate = " AND (em.DateOfBirth >= '" . date("Y-m-d", strtotime($output['birthdate_start'])) . "' AND em.DateOfBirth <= '" . date("Y-m-d", strtotime($output['birthdate_end'])) . "' )";
                }
                $dataWhere .= $conditionBirthDate;
            }
            if (!empty($output['statusstd'])) {
                $dataArrStatus = $output['statusstd'];
                $conditionStatus = " AND (";
                $sn = 1;
                foreach ($output['statusstd'] as $s) {
                    if ($s == '-1') {
                        $conditionStatus .= "(em.StatusEmployeeID = '" . $s . "' OR em.StatusLecturerID = '" . $s . "' )" . (($sn < count($output['statusstd'])) ? ' OR ' : '');
                    } else if ($s == 3 || $s == 4 || $s == 5 || $s == 6) {
                        $conditionStatus .= "em.StatusLecturerID = '" . $s . "' " . (($sn < count($output['statusstd'])) ? ' OR ' : '');
                    } else {
                        $conditionStatus .= "em.StatusEmployeeID = '" . $s . "'" . (($sn < count($output['statusstd'])) ? ' OR ' : '');
                    }
                    $sn++;
                }
                $conditionStatus .= ")";
                $dataWhere .= $conditionStatus;
            }
            if (!empty($output['religion'])) {
                $dataArrReligion = $output['religion'];
                $conditionReligion = " AND (";
                $rn = 1;
                foreach ($output['religion'] as $r) {
                    $conditionReligion .= "ReligionID = '" . $r . "' " . (($rn < count($output['religion'])) ? ' OR ' : '');
                    $rn++;
                }
                $conditionReligion .= ")";
                $dataWhere .= $conditionReligion;
            }
            if (!empty($output['gender'])) {
                $dataArrGender = $output['gender'];
                $conditionGender = " AND (";
                $gn = 1;
                foreach ($output['gender'] as $g) {
                    $conditionGender .= "Gender = '" . $g . "' " . (($gn < count($output['gender'])) ? ' OR ' : '');
                    $gn++;
                }
                $conditionGender .= ")";
                $dataWhere .= $conditionGender;
            }
            if (!empty($output['level_education'])) {
                $dataArrLevel = $output['level_education'];
                $conditionLevelEdu = " AND (";
                $ln = 1;
                foreach ($output['level_education'] as $l) {
                    $conditionLevelEdu .= "LevelEducationID = '" . $l . "' " . (($ln < count($output['level_education'])) ? ' OR ' : '');
                    $ln++;
                }
                $conditionLevelEdu .= ")";
                $dataWhere .= $conditionLevelEdu;
            }

            /*SORTING*/
            if (!empty($output['sortby']) && !empty($output['orderby'])) {
                $orderBy = $output['sortby'] . " " . $output['orderby'];
            } else {
                $orderBy = "em.ID DESC";
            }
            /*END SORTING*/
        }
        /*END UPDATED BY FEBRI @  JAN 2020*/

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( em.Name LIKE "%' . $search . '%" OR em.NIP LIKE "%' . $search . '%" )';
        }

        $queryDefault = 'SELECT em.*, ems.Description AS StatusEmployees, rl.Religion as EmpReligion, le.Level as EmpEdu
                        FROM db_employees.employees em
                        LEFT JOIN db_employees.employees_status ems ON (em.StatusEmployeeID = ems.IDStatus)
                        LEFT JOIN db_employees.religion rl ON (em.ReligionID = rl.IDReligion)
                        LEFT JOIN db_employees.level_education le ON (em.LevelEducationID = le.ID)
                        WHERE ( em.StatusEmployeeID != -2  ' . $dataWhere . ' ) ' . $dataSearch . ' ORDER BY ' . $orderBy;

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();
        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $division = '-';
            $position = '-';
            if ($row['PositionMain'] != '' && $row['PositionMain'] != null) {
                $PositionMain = explode('.', $row['PositionMain']);

                $dataDivisi = $this->db->select('Division,Description')->get_where(
                    'db_employees.division',
                    array('ID' => $PositionMain[0]),
                    1
                )->result_array();
                $division = $dataDivisi[0]['Division'];

                $dataPosition = $this->db->select('Position,Description')->get_where(
                    'db_employees.position',
                    array('ID' => $PositionMain[1]),
                    1
                )->result_array();

                $position = $dataPosition[0]['Position'];
            }

            $gender = ($row['Gender'] == 'L') ? 'Male' : 'Female';

            $url_image = './uploads/employees/' . $row['Photo'];
            $srcImg =  base_url('images/icon/userfalse.png');
            if ($row['Photo'] != '' && $row['Photo'] != null) {
                $srcImg = (file_exists($url_image)) ? base_url('uploads/employees/' . $row['Photo'])
                    : base_url('images/icon/userfalse.png');
            }


            $EmailSelect = ($row['StatusEmployeeID'] == 4 || $row['StatusEmployeeID'] == '4') ? $row['Email'] : $row['EmailPU'];

            $Email = (($row['StatusEmployeeID'] == 4 || $row['StatusEmployeeID'] == '4') && count(explode(',', $EmailSelect)) > 1) ? '' : $EmailSelect;

            $disBtnEmail = ($Email == '' || $Email == null) ? 'disabled' : '';
            $dataToken = array(
                'Type' => 'emp',
                'Name' => $row['Name'],
                'NIP' => $row['NIP'],
                'Email' => $Email
            );

            $token = $this->jwt->encode($dataToken, 'UAP)(*');

            $DateOfBirth = ($row['DateOfBirth'] != '' && $row['DateOfBirth'] != null) ? $row['DateOfBirth'] : '';
            $disDateOfBirth = ($row['DateOfBirth'] != '' && $row['DateOfBirth'] != null) ? '' : 'disabled';


            $btnAct = '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-pencil-square-o"></i> <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="' . $disBtnEmail . '"><a href="javascript:void(0);" ' . $disBtnEmail . ' id="btnResetPass' . $row['NIP'] . '" class="btn-reset-password ' . $disBtnEmail . '" data-token="' . $token . '">Reset Password (Send Email)</a></li>
                            <!--<li class="' . $disDateOfBirth . '"><a href="javascript:void(0);" ' . $disDateOfBirth . ' class="resetpassBirthDay ' . $disDateOfBirth . '" data-nip="' . $row['NIP'] . '" data-day="' . $DateOfBirth . '">Reset Password (DDMMYY)</a></li>-->
                            <li><a href="javascript:void(0);" class="btn-update-email" id="btnUpdateEmail' . $row['NIP'] . '" data-name="' . $row['Name'] . '" data-nip="' . $row['NIP'] . '" data-empid="' . $row['StatusEmployeeID'] . '" data-email="' . $Email . '">Update Email</a></li>
                            <li><a class = "PrintIDCard" href="javascript:void(0);" type = "employees" data-npm="' . $row['NIP'] . '" data-name="' . ucwords(strtolower($row['Name'])) . '" path = ' . $srcImg . ' email = "' . $row['EmailPU'] . '">Print ID Card</a></li>
                          </ul>
                        </div>';

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . $row['NIP'] . '</div>';
            $nestedData[] = '<div style="margin-bottom:10px"><div  style="float:left;margin-right:10px;"><img src="' . $srcImg . '" style="max-width: 35px;" class="img-rounded"></div>' .
                '<div  style="text-align:left;"><b>' . $row['Name'] . '</b><br/><span id="viewEmail' . $row['NIP'] . '" style="color: #2196f3;">' . (!empty($Email) ? $Email : '-') . '</span></div>' .
                '</div><p><span class="' . ((in_array($row['Gender'], $dataArrGender)) ? 'bg-primary' : '') . '"><i class="fa fa-' . (($row['Gender'] == 'L') ? 'mars' : 'venus') . '"></i> ' . $gender . '</span> &nbsp;
                                <span class="' . ((in_array($row['ReligionID'], $dataArrReligion)) ? 'bg-primary' : '') . '"><i class="fa fa-star"></i> ' . $row['EmpReligion'] . '</span> &nbsp;
                                <span class="' . ((in_array($row['LevelEducationID'], $dataArrLevel)) ? 'bg-primary' : '') . '"><i class="fa fa-graduation-cap"></i> ' . (!empty($row['EmpEdu']) ? $row['EmpEdu'] : '-') . '</span> </p>';
            $nestedData[] = '<div  style="text-align:left;">' . (!empty($row['PlaceOfBirth']) ? $row['PlaceOfBirth'] . ', ' : '') . date("d F Y", strtotime($row['DateOfBirth'])) . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . ucwords(strtolower($division)) . '<br/>- ' . ucwords(strtolower($position)) . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . $row['Address'] . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $btnAct . '</div>';
            $statusLect = $this->General_model->fetchData("db_employees.employees_status", "IDStatus='" . $row['StatusLecturerID'] . "'")->row();
            $nestedData[] = '<button type="button"  title="Status Employee" class="btn btn-xs btn-' . (in_array($row['StatusEmployeeID'], $dataArrStatus) ? 'info' : 'default') . '">' . $row['StatusEmployees'] . '</button><br><button type="button" title="Status Lecturer" class="btn btn-xs btn-' . (in_array($row['StatusLecturerID'], $dataArrStatus) ? 'info' : 'default') . '">' . (!empty($statusLect) ? $statusLect->Description : '-') . '</button></div>';

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

    public function crudLecturerEvaluation()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr > 0)) {
            if ($data_arr['action'] == 'readQuestion') {
                $dataQ = $this->db->query('SELECT ec.Category, eq.* FROM db_academic.edom_question eq
                                                      LEFT JOIN db_academic.edom_category ec
                                                      ON (ec.ID = eq.CategoryID)
                                                      WHERE ec.IDDivision = 6
                                                      ORDER BY eq.Order ASC ')->result_array();

                return print_r(json_encode($dataQ));
            } else if ($data_arr['action'] == 'readLECategory') {
                $data = $this->db->order_by('ID', 'ASC')
                    ->get_where('db_academic.edom_category', array('IDDivision' => 6))->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'insertQuestion') {
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->insert('db_academic.edom_question', $dataForm);

                return print_r(1);
            } else if ($data_arr['action'] == 'loadToEdit') {
                $ID = $data_arr['ID'];
                $data = $this->db->get_where('db_academic.edom_question', array('ID' => $ID), 1)->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'editQuestion') {

                $dataForm = (array) $data_arr['dataForm'];

                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_academic.edom_question', $dataForm);
                return print_r(1);
            } else if ($data_arr['action'] == 'deleteQuestion') {
                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_academic.edom_question');
                return print_r(1);
            }
        }
    }

    public function getLecturerEvaluation()
    {

        $max_execution_time = 3600;
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', $max_execution_time); //60 seconds = 1 minutes

        $SemesterID = $this->input->get('SemesterID');
        $ProdiID = $this->input->get('ProdiID');

        $dataWhere = 's.SemesterID = "' . $SemesterID . '"';

        $dataSelect = 'SELECT s.ID, s.SemesterID, s.ClassGroup, mk.ID AS MKID, mk.MKCode, mk.Name AS Course, mk.NameEng AS CourseEng';

        $queryDefault = 'SELECT em.NIP, em.Name FROM db_employees.employees em WHERE em.ProdiID = "' . $ProdiID . '"
                                                    AND em.StatusEmployeeID != "-2"
                                                   AND em.StatusEmployeeID != "-1" ';
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $result = [];

        if (count($queryDefaultRow) > 0) {
            for ($i = 0; $i < count($queryDefaultRow); $i++) {
                $d = $queryDefaultRow[$i];
                $queryCourse = $dataSelect . ' FROM db_academic.schedule s
                                                  LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                  LEFT JOIN db_academic.mata_kuliah mk ON (sdc.MKID = mk.ID)
                                                  WHERE  ' . $dataWhere . ' AND s.Coordinator = "' . $d['NIP'] . '" GROUP BY s.ID
                                                  UNION ALL
                                                  ' . $dataSelect . '
                                                  FROM db_academic.schedule_team_teaching stt
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = stt.ScheduleID)
                                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (sdc.MKID = mk.ID)
                                                    WHERE ' . $dataWhere . ' AND stt.NIP = "' . $d['NIP'] . '" GROUP BY s.ID
                                                  ';
                $dataCourse = $this->db->query($queryCourse)->result_array();

                if (count($dataCourse) > 0) {
                    for ($c = 0; $c < count($dataCourse); $c++) {
                        // Data Student
                        $dataStd = $this->m_api->getStudentByScheduleID($dataCourse[$c]['SemesterID'], $dataCourse[$c]['ID'], '');
                        $dataCourse[$c]['TotalStudent'] = count($dataStd);
                        $dataCourse[$c]['DetailsStudent'] = $dataStd;

                        // Data Edom Answer
                        $dataEd = $this->db->query('SELECT * FROM db_academic.edom_answer ea
                                                                      WHERE ea.SemesterID = "' . $dataCourse[$c]['SemesterID'] . '"
                                                                      AND ea.ScheduleID = "' . $dataCourse[$c]['ID'] . '"
                                                                      AND ea.NIP = "' . $d['NIP'] . '"
                                                                       AND ea.Type = "1" ')->result_array();

                        // Read Details
                        if (count($dataEd) > 0) {
                            for ($e = 0; $e < count($dataEd); $e++) {
                                $dataDetails = $this->db->get_where('db_academic.edom_answer_details', array(
                                    'EAID' => $dataEd[$e]['ID'],
                                    'QuestionID !=' => '12'
                                ))->result_array();

                                $dataEd[$e]['Details'] = $dataDetails;
                            }
                        }

                        $dataCourse[$c]['TotalAnswer'] = count($dataEd);
                        $dataCourse[$c]['DataAnswer'] = $dataEd;
                    }

                    $queryDefaultRow[$i]['Course'] = $dataCourse;

                    array_push($result, $queryDefaultRow[$i]);
                }
            }
        }

        return print_r(json_encode($result));
    }

    public function getLecturerEvaluation2()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();

        $dataWhere = 's.SemesterID = "' . $data_arr['SemesterID'] . '"';

        $dataSelect = 'SELECT s.ID, s.SemesterID, mk.ID AS MKID, mk.MKCode, mk.Name AS Course, mk.NameEng AS CourseEng';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( em.Name LIKE "%' . $search . '%" OR em.NIP LIKE "%' . $search . '%" )';
        }


        $queryDefault = 'SELECT em.NIP, em.Name FROM db_employees.employees em WHERE em.ProdiID = "' . $data_arr['ProdiID'] . '"
                                                    AND em.StatusEmployeeID != "-2"
                                                   AND em.StatusEmployeeID != "-1" ';


        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        if (count($queryDefaultRow) > 0) {
            for ($i = 0; $i < count($queryDefaultRow); $i++) {
                $d = $queryDefaultRow[$i];
                $queryCourse = $dataSelect . ' FROM db_academic.schedule s
                                                  LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                  LEFT JOIN db_academic.mata_kuliah mk ON (sdc.MKID = mk.ID)
                                                  WHERE  ' . $dataWhere . ' AND s.Coordinator = "' . $d['NIP'] . '" GROUP BY s.ID
                                                  UNION ALL
                                                  ' . $dataSelect . '
                                                  FROM db_academic.schedule_team_teaching stt
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = stt.ScheduleID)
                                                    LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (sdc.MKID = mk.ID)
                                                    WHERE ' . $dataWhere . ' AND stt.NIP = "' . $d['NIP'] . '" GROUP BY s.ID
                                                  ';
                $dataCourse = $this->db->query($queryCourse)->result_array();

                if (count($dataCourse) > 0) {
                    for ($c = 0; $c < count($dataCourse); $c++) {
                        // Data Student
                        $dataStd = $this->m_api->getStudentByScheduleID($dataCourse[$c]['SemesterID'], $dataCourse[$c]['ID'], '');
                        $dataCourse[$c]['TotalStudent'] = count($dataStd);

                        // Data Edom Answer
                        $dataEd = $this->db->query('SELECT * FROM db_academic.edom_answer ea
                                                                      WHERE ea.SemesterID = "' . $dataCourse[$c]['SemesterID'] . '"
                                                                      AND ea.ScheduleID = "' . $dataCourse[$c]['ID'] . '"
                                                                      AND ea.NIP = "' . $d['NIP'] . '" ')->result_array();

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

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:left;"><b>' . $row['Name'] . '</b><br/>' . $row['NIP'] . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . $row['Course'] . '</div>';
            $nestedData[] = '<div  style="text-align:center;">-</div>';
            $nestedData[] = '<div  style="text-align:center;">-</div>';

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

    public function crudStudent()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr > 0)) {
            if ($data_arr['action'] == 'readDataStudent') {

                $DB_Student = $data_arr['DB_Student'];
                $NPM = $data_arr['NPM'];
                /*
                    Note :
                    Graduation Year ambil yang dari auth_students
                */
                $data = $this->db->query('SELECT s.*, au.EmailPU, p.Name AS ProdiName, p.NameEng AS ProdiNameEng,
                                      ss.Description AS StatusStudentDesc, au.KTPNumber, au.Access_Card_Number,au.GraduationDate,au.YudisiumDate,au.Tgl_msk,
                                      em.Name AS Mentor, em.NIP, em.EmailPU AS MentorEmailPU
                                      FROM ' . $DB_Student . '.students s
                                      LEFT JOIN db_academic.program_study p ON (s.ProdiID = p.ID)
                                      LEFT JOIN db_academic.status_student ss ON (s.StatusStudentID = ss.ID)
                                      LEFT JOIN db_academic.auth_students au ON (s.NPM = au.NPM)
                                      LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM=s.NPM)
                                      LEFT JOIN db_employees.employees em ON (em.NIP=ma.NIP)
                                      WHERE s.NPM = "' . $NPM . '" LIMIT 1')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'updateBiodataStudent') {
                $rs = array('msg' => '', 'status' => 1);
                $dataUpdtAuth = (array) $data_arr['dataAuth'];
                $NPM = $data_arr['NPM'];
                // send to AD if input Access_Card_Number
                if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                    // if(true) {
                    // check url exist
                    $urlAD = URLAD . '__api/Create';
                    $is_url_exist = $this->m_master->is_url_exist($urlAD);
                    if ($is_url_exist) {
                        if (array_key_exists('Access_Card_Number', $dataUpdtAuth)) {
                            if ($dataUpdtAuth['Access_Card_Number'] != '' && $dataUpdtAuth['Access_Card_Number'] != null) {
                                // update to AD
                                $data_arr1 = [
                                    'pager' => $dataUpdtAuth['Access_Card_Number'],
                                ];
                                $data = array(
                                    'auth' => 's3Cr3T-G4N',
                                    'Type' => 'Student',
                                    'UserID' => $NPM,
                                    'data_arr' => $data_arr1,
                                );

                                $url = URLAD . '__api/Edit';
                                $token = $this->jwt->encode($data, "UAP)(*");
                                $this->m_master->apiservertoserver_NotWaitResponse($url, $token);
                            }
                        }
                    } else {
                        $rs['msg'] = 'Windows active directory server not connected';
                        $rs['status'] = 0;
                        echo json_encode($rs);
                        die(); // stop script
                    }
                }

                $DB_Student = $data_arr['DB_Student'];

                $dataUpdate = $data_arr['dataForm'];
                $GraduationYear = null;
                $GraduationDate = $dataUpdtAuth['GraduationDate'];
                if ($GraduationDate != '' && $GraduationDate != null) {
                    $GraduationYear = date('Y', strtotime($GraduationDate));
                }
                $dataUpdate->GraduationYear = $GraduationYear;

                // dataTAStd
                $dataTAStd = json_decode(json_encode($data_arr['dataTAStd']), true);
                $dataUpdate->NationalityID = $dataTAStd['NationalityID'];
                $dataUpdate->ReligionID = $dataTAStd['ReligionID'];


                $this->db->where('NPM', $NPM);
                $this->db->update($DB_Student . '.students', $dataUpdate);
                $this->db->reset_query();

                $dataUpdtAuth['LastUpdate'] = date('Y-m-d H:i:s');
                $dataUpdtAuth['UpdatedBy'] = $this->session->userdata('NIP');
                $dataUpdtAuth['GraduationYear'] = $GraduationYear;

                $this->db->where('NPM', $NPM);
                $this->db->update('db_academic.auth_students', $dataUpdtAuth);
                $this->db->reset_query();

                echo json_encode($rs);
            }
        }
    }

    public function getTimetables()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();

        $whereProdi = ($data_arr['ProdiID'] != '') ? ' AND sdc.ProdiID = "' . $data_arr['ProdiID'] . '" ' : '';
        $whereCombinedClasses = ($data_arr['CombinedClasses']) ? ' AND s.CombinedClasses = "' . $data_arr['CombinedClasses'] . '" ' : '';
        $whereDay = ($data_arr['DayID']) ? ' AND sd.DayID = "' . $data_arr['DayID'] . '" ' : '';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $wl = 'LIKE "%' . $search . '%"';
            $dataSearch = ' AND (s.ClassGroup ' . $wl . '
                                    OR s.Coordinator ' . $wl . '
                                    OR em.Name ' . $wl . '
                                     OR mk.MKCode ' . $wl . '
                                      OR mk.Name ' . $wl . '
                                      OR mk.NameEng ' . $wl . ')';
        }

        $queryDefault = 'SELECT s.ID, s.CombinedClasses, s.ClassGroup, s.Coordinator, em.Name AS CoordinatorName,
                                      s.TeamTeaching, s.SubSesi, s.Attendance, cd.TotalSKS AS Credit, s.OnlineLearning,
                                       mk.MKCode, mk.Name AS MKName, mk.NameEng AS MKNameEng,
                                       cd.ID AS CDID
                                      FROM db_academic.schedule s
                                      LEFT JOIN db_academic.schedule_details sd ON (sd.ScheduleID = s.ID)
                                      LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                      LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                      LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)

                                      LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)

                                      WHERE ( s.ProgramsCampusID = "' . $data_arr['ProgramCampusID'] . '"
                                      AND s.SemesterID = "' . $data_arr['SemesterID'] . '" ' . $whereProdi . ' ' . $whereDay . ' ' . $whereCombinedClasses . ' )
                                       ' . $dataSearch . '
                                      GROUP BY s.ID
                                      ORDER BY d.ID,sd.StartSessions, sd.EndSessions, s.ClassGroup ASC ';

        $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM ( SELECT s.ID FROM db_academic.schedule s
                                      LEFT JOIN db_academic.schedule_details sd ON (sd.ScheduleID = s.ID)
                                      LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                      LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                      LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                      LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                      WHERE ( s.ProgramsCampusID = "' . $data_arr['ProgramCampusID'] . '"
                                      AND s.SemesterID = "' . $data_arr['SemesterID'] . '" ' . $whereProdi . ' ' . $whereDay . ' ' . $whereCombinedClasses . ' )
                                       ' . $dataSearch . '
                                      GROUP BY s.ID) xx ';

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $SubSesi = ($row['SubSesi'] == '1') ? '<br/><span class="label label-warning">Sub-Sesi</span>' : '';
            $Attendance = ($row['Attendance'] == '0') ? '<br/><span class="label label-danger"><i class="fa fa-filter margin-right"></i> No Attd</span>' : '';
            $Attendance = ($row['OnlineLearning'] == '1') ? '<br/><span class="label label-success">Online</span>' : '';

            $dataSchedule = $this->db->query('SELECT cl.Room, d.NameEng AS DayEng, sd.StartSessions, sd.EndSessions, attd.ID AS ID_Attd
                                                                      FROM db_academic.schedule_details sd
                                                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                                      LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                                      LEFT JOIN db_academic.attendance attd ON (attd.ScheduleID = sd.ScheduleID
                                                                      AND attd.SDID = sd.ID)
                                                                        WHERE sd.ScheduleID = "' . $row['ID'] . '"
                                                                         ORDER BY sd.DayID ASC ')->result_array();
            $ScheduleDetails = '';
            if (count($dataSchedule) > 0) {
                foreach ($dataSchedule as $item) {
                    $ScheduleDetails = $ScheduleDetails . '' . $item['DayEng'] . ', <span style="color: #0b97c4;">' . substr($item['StartSessions'], 0, 5) . ' - ' . substr($item['EndSessions'], 0, 5) . '</span><br/>Room : ' . $item['Room'] . '<br/>';
                }
            }

            $TeamTeaching = '';
            if ($row['TeamTeaching'] == '1') {
                $dataTeamTeaching = $this->db->query('SELECT stt.NIP, em.Name FROM db_academic.schedule_team_teaching stt
                                                                LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                                WHERE stt.ScheduleID = "' . $row['ID'] . '" ')->result_array();

                if (count($dataTeamTeaching) > 0) {
                    for ($t = 0; $t < count($dataTeamTeaching); $t++) {
                        $TeamTeaching = $TeamTeaching . '<br/> - ' . $dataTeamTeaching[$t]['Name'];
                    }
                }
            }

            // Daftar Prodi
            $dataProdi = $this->db->query('SELECT ps.ID AS ProdiID, ps.Code FROM db_academic.schedule_details_course sdc
                                                    LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                                    WHERE sdc.ScheduleID = "' . $row['ID'] . '"
                                                    GROUP BY sdc.ProdiID ORDER BY ps.Code ASC ')->result_array();
            $Prodi = '';
            if (count($dataProdi) > 0) {
                for ($p = 0; $p < count($dataProdi); $p++) {

                    // Get Semester
                    $dataSMT = $this->db->query('SELECT co.Semester
                                                    FROM db_academic.schedule_details_course sdc
                                                    LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                    LEFT JOIN db_academic.course_offerings co ON (co.CurriculumID = cd.CurriculumID)
                                                    WHERE sdc.ScheduleID = "' . $row['ID'] . '" AND sdc.ProdiID = "' . $dataProdi[$p]['ProdiID'] . '"
                                                    AND co.ProdiID = "' . $dataProdi[$p]['ProdiID'] . '" AND co.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                    ORDER BY co.Semester ASC ')->result_array();
                    $smt = '';
                    if (count($dataSMT) > 0) {
                        $k = true;
                        foreach ($dataSMT as $itsm) {
                            $smt = $smt . '' . (($k == true) ? "" : ", ") . '' . $itsm['Semester'];
                            $k = false;
                        }
                    }

                    $koma = ($p != 0) ? ',' : '';
                    $Prodi = $Prodi . '' . $koma . ' ' . $dataProdi[$p]['Code'] . ' (' . $smt . ')';
                }
            }

            $Student = $this->m_api->__getStudentApprovedKRS($data_arr['SemesterID'], $row['ID']);

            $Student_plan = $this->m_api->__getStudentNotYetApprovedKRS($data_arr['SemesterID'], $row['ID']);

            // Keadaan di attendance
            for ($st = 0; $st < count($Student_plan); $st++) {
                $dstd = $Student_plan[$st];
                $attd_s = [];
                $totalAttd = count($dataSchedule);
                $temp_total = 0;
                if (count($dataSchedule) > 0) {
                    foreach ($dataSchedule as $item2) {
                        $dataCheckAttd = $this->db->select('ID')->get_where('db_academic.attendance_students', array(
                            'ID_Attd' => $item2['ID_Attd'],
                            'NPM' => $dstd['NPM']
                        ))->result_array();

                        if (count($dataCheckAttd) > 0) {
                            $temp_total += 1;
                        }

                        $atrt = array(
                            'ID_Attd' => $item2['ID_Attd'],
                            'ID_Attd_Student' => (count($dataCheckAttd) > 0) ? $dataCheckAttd[0]['ID'] : ''
                        );
                        array_push($attd_s, $atrt);
                    }
                }

                $Student_plan[$st]['Attendance'] = $attd_s;
                $Student_plan[$st]['TotalAttd'] = $temp_total;
            }


            $stdDetails_cuy = json_encode(array(
                'Approve' => $Student,
                'Planning' => $Student_plan
            ));

            $btnAct = '<div class="btn-group">
                      <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-edit"></i> <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a target="_blank" href="' . base_url('academic/timetables/list/edit/' . $data_arr['SemesterID'] . '/' . $row['ID'] . '/' . str_replace(" ", "-", $row['MKNameEng'])) . '">Edit Course</a></li>
                        <li><a target="_blank" href="' . base_url('academic/timetables/list/edit-schedule/' . $data_arr['SemesterID'] . '/' . $row['ID'] . '/' . str_replace(" ", "-", $row['MKNameEng'])) . '">Edit Schedule</a></li>

                        <li role="separator" class="divider"></li>
                        <li><a href="javascript:void(0);" class="btnTimetablesEditDelete" data-group="' . $row['ClassGroup'] . '" data-id="' . $row['ID'] . '">Delete</a></li>
                      </ul>
                    </div>';

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $row['ClassGroup'] . '' . $SubSesi . '' . $Attendance . '</div>';
            $nestedData[] = '<div  style="text-align:left;"><b>' . $row['MKCode'] . ' - ' . $row['MKName'] . '</b><br/><i style="color: #9e9e9e;">' . $row['MKNameEng'] . '</i>
                                                    <br/><span>Prodi : ' . $Prodi . '</span></div>';
            $nestedData[] = '<div  style="text-align:center;">' . $row['Credit'] . '</div>';
            $nestedData[] = '<div  style="text-align:left;"><span style="color:#0968b3;">(Co) ' . $row['CoordinatorName'] . '</span>' . $TeamTeaching . '</div>';
            $nestedData[] = '<div  style="text-align:center;"><textarea id="detailsStudentCuy' . $no . '" class="hide">' . $stdDetails_cuy . '</textarea>
                                <a href="javascript:void(0);" data-no="' . $no . '" data-course="' . $row['ClassGroup'] . ' | ' . $row['MKCode'] . ' - ' . $row['MKNameEng'] . '" class="showStudentCuy">' .
                count($Student) . ' of ' . count($Student_plan) . '</a>
                             </div>';

            $nestedData[] = '<div  style="text-align:center;">' . $btnAct . '</div>';
            $nestedData[] = '<div  style="text-align:right;">' . $ScheduleDetails . '</div>';
            //            $nestedData[] = '<div  style="text-align:center;">'.$row['Room'].'</div>';

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

    public function getMonitoringAllStudent()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();


        $w_year = ($data_arr['Year'] != '' && $data_arr['Year'] != null) ? ' AND auts.Year = "' . $data_arr['Year'] . '" ' : '';
        $w_prodi = ($data_arr['ProdiID'] != '' && $data_arr['ProdiID'] != null) ? ' AND auts.ProdiID = "' . $data_arr['ProdiID'] . '" ' : '';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $wl = 'LIKE "%' . $search . '%"';
            $dataSearch = ' AND (auts.NPM ' . $wl . '
                                    OR auts.Name ' . $wl . ')';
        }

        $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year
                                          FROM db_academic.auth_students auts
                                          WHERE ( auts.StatusStudentID = "3" ' . $w_year . ' ' . $w_prodi . ' ) ' . $dataSearch . '
                                          ORDER BY NPM ASC';

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $db_ = 'ta_' . $row['Year'];
            $dataCourse = $this->db->query('SELECT mk.MKCode, mk.NameEng, s.ClassGroup, s.ID AS ScheduleID, em.Name AS Lecturer  FROM ' . $db_ . '.study_planning sp
                                                        LEFT JOIN db_academic.schedule s ON (s.ID = sp.ScheduleID)
                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                        WHERE sp.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                        AND sp.NPM = "' . $row['NPM'] . '"
                                                         GROUP BY sp.ScheduleID ORDER BY mk.MKCode ASC ')->result_array();

            $course = '';
            if (count($dataCourse) > 0) {
                for ($c = 0; $c < count($dataCourse); $c++) {
                    $d = $dataCourse[$c];

                    // Get Attendance
                    $dataAttd = $this->db->query('SELECT attd_s.M1, attd_s.M2, attd_s.M3, attd_s.M4, attd_s.M5, attd_s.M6, attd_s.M7, attd_s.M8, attd_s.M9,
                                                            attd_s.M10, attd_s.M11, attd_s.M12, attd_s.M13, attd_s.M14
                                                            FROM  db_academic.attendance_students attd_s
                                                            LEFT JOIN db_academic.attendance attd ON (attd_s.ID_Attd = attd.ID)
                                                            WHERE attd_s.NPM = "' . $row['NPM'] . '" AND attd.ScheduleID = "' . $d['ScheduleID'] . '" ')->result_array();

                    $dataCourse[$c]['Details'] = $dataAttd;

                    $MaxMeet = 14 * count($dataAttd);
                    $TotalMeet = 0;
                    foreach ($dataAttd as $item) {

                        $TotalMeet = ($item['M1'] == '1' || $item['M1'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M2'] == '1' || $item['M2'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M3'] == '1' || $item['M3'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M4'] == '1' || $item['M4'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M5'] == '1' || $item['M5'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M6'] == '1' || $item['M6'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M7'] == '1' || $item['M7'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M8'] == '1' || $item['M8'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet = ($item['M9'] == '1' || $item['M9'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet =  ($item['M10'] == '1' || $item['M10'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet =  ($item['M11'] == '1' || $item['M11'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet =  ($item['M12'] == '1' || $item['M12'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet =  ($item['M13'] == '1' || $item['M13'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                        $TotalMeet =  ($item['M14'] == '1' || $item['M14'] == 1) ? $TotalMeet + 1 : $TotalMeet + 0;
                    }

                    $PersenHadir = ($TotalMeet != 0) ? round($TotalMeet / $MaxMeet, 2) * 100 : 0;

                    if ($PersenHadir <= $data_arr['Percentage']) {
                        $course = $course . '<div> - ' . $d['ClassGroup'] . ' | <span style="color:#03a9f4;">' . $d['MKCode'] . ' - ' . $d['NameEng'] . '</span> | <span style="color:#009688;"><i class="fa fa-user margin-right"></i> ' . $d['Lecturer'] . ' </span>| Attendance : <b>' . $PersenHadir . ' %</b></div>';
                    } else if ($PersenHadir > $data_arr['Percentage']) {
                        $course = $course . ' <div style="color:#ccc;">- ' . $d['ClassGroup'] . ' | ' . $d['MKCode'] . ' - ' . $d['NameEng'] . ' | <i class="fa fa-user margin-right"></i> ' . $d['Lecturer'] . ' | Attendance : <b>' . $PersenHadir . ' %</b></div>';
                    }
                }
            }

            //            print_r($dataCourse);
            //            exit;

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $row['NPM'] . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . $row['Name'] . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . $course . '</div>';

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

    public function crudCategoryClassroomVreservation()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'read') {
                $data = $this->m_master->showData_array('db_reservation.category_room');
                for ($i = 0; $i < count($data); $i++) {
                    if ($data[$i]['Approver1']) {
                        $y = json_decode($data[$i]['Approver1']);
                        $z = $data[$i]['Approver1'];
                        $x = array();
                        for ($l = 0; $l < count($y); $l++) {
                            // Find User Type
                            $UserType = $this->m_master->caribasedprimary('db_reservation.cfg_group_user', 'ID', $y[$l]->UserType);
                            $UserType = $UserType[0]['GroupAuth'];

                            // cek Type Approver
                            $TypeApprover = $y[$l]->TypeApprover;
                            $ApproverGet = $y[$l]->Approver;
                            switch ($TypeApprover) {
                                case 'Division':
                                    $Approver = $this->m_master->caribasedprimary('db_employees.division', 'ID', $ApproverGet);
                                    $Approver = $Approver[0]['Division'];
                                    break;

                                case 'Position':
                                    $Approver = $this->m_master->caribasedprimary('db_employees.position', 'ID', $ApproverGet);
                                    $Approver = $Approver[0]['Position'];
                                    break;
                                case 'Employees':
                                    $Approver = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $ApproverGet);
                                    $Approver = $Approver[0]['NIP'] . ' - ' . $Approver[0]['Name'];
                                    break;
                            }

                            $tanda = ($l == 0) ? '*   ' : '';
                            $x[] = $tanda . $UserType . ' -> ' . $TypeApprover . ' -> ' . $Approver;
                        }
                        $data[$i]['Approver1'] = implode('<br>*  ', $x);
                        $data[$i]['Approver1_ori'] = str_replace('"', "'", $z);
                    }
                    if ($data[$i]['Approver2']) {
                        $y = json_decode($data[$i]['Approver2']);
                        $z = $data[$i]['Approver2'];
                        $x = array();
                        for ($l = 0; $l < count($y); $l++) {

                            // cek Type Approver
                            $TypeApprover = $y[$l]->TypeApprover;
                            $ApproverGet = $y[$l]->Approver;
                            switch ($TypeApprover) {
                                case 'Division':
                                    $Approver = $this->m_master->caribasedprimary('db_employees.division', 'ID', $ApproverGet);
                                    $Approver = $Approver[0]['Division'];
                                    break;

                                case 'Position':
                                    $Approver = $this->m_master->caribasedprimary('db_employees.position', 'ID', $ApproverGet);
                                    $Approver = $Approver[0]['Position'];
                                    break;
                                case 'Employees':
                                    $Approver = $this->m_master->caribasedprimary('db_employees.employees', 'NIP', $ApproverGet);
                                    $Approver = $Approver[0]['NIP'] . ' - ' . $Approver[0]['Name'];
                                    break;
                            }

                            $tanda = ($l == 0) ? '*   ' : '';
                            $x[] = $tanda . $TypeApprover . ' -> ' . $Approver;
                        }
                        $data[$i]['Approver2'] = implode('<br>*  ', $x);
                        $data[$i]['Approver2_ori'] = str_replace('"', "'", $z);
                    }
                }

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'add') {
                $result = '';
                $formData = (array) $data_arr['formData'];
                $Approver1 = json_encode($data_arr['Approver1']);
                $Approver2 = json_encode($data_arr['Approver2']);
                $formData = $formData + array('Approver1' => $Approver1, 'Approver2' => $Approver2);
                $this->db->insert('db_reservation.category_room', $formData);
                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'edit') {
                $formData = (array) $data_arr['formData'];
                $Approver1 = json_encode($data_arr['Approver1']);
                $Approver2 = json_encode($data_arr['Approver2']);
                $formData = $formData + array('Approver1' => $Approver1, 'Approver2' => $Approver2);
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->update('db_reservation.category_room', $formData);
                $result = array(
                    'inserID' => $ID
                );

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'delete') {
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->delete('db_reservation.category_room');
                return print_r($ID);
            }
        }
    }

    public function getDataStudyPlanning()
    {
        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();

        $w_year = ($data_arr['Year'] != '') ? ' AND auts.Year = "' . $data_arr['Year'] . '" ' : '';
        $w_prodi = ($data_arr['ProdiID'] != '') ? ' AND auts.ProdiID = "' . $data_arr['ProdiID'] . '" ' : '';
        $w_StatusKRS = ($data_arr['StatusKRS'] != '' && $data_arr['StatusKRS'] != 'NOT_EXISTS')
            ? ' AND stdk.Status = "' . $data_arr['StatusKRS'] . '" ' : '';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $wl = 'LIKE "%' . $search . '%"';
            $dataSearch = ' AND (auts.NPM ' . $wl . '
                                    OR auts.Name ' . $wl . ')';
        }

        if ($data_arr['StatusKRS'] == 'NOT_EXISTS') {

            $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiID, auts.ProdiGroupID, em.Name AS MentorName, em.NIP AS MentorNIP
                                          FROM db_academic.auth_students auts
                                          LEFT JOIN db_academic.mentor_academic mac ON (mac.NPM = auts.NPM)
                                          LEFT JOIN db_employees.employees em ON (em.NIP = mac.NIP)
                                          LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                                          WHERE ( auts.StatusStudentID = "3" ' . $w_year . ' ' . $w_prodi . '
                                          AND auts.NPM NOT IN (SELECT auts_1.NPM FROM  db_academic.auth_students auts_1
                                                                LEFT JOIN db_academic.std_krs stdk_1 ON (stdk_1.NPM = auts_1.NPM)
                                                                WHERE stdk_1.SemesterID = "' . $data_arr['SemesterID'] . '" GROUP BY auts_1.NPM )
                                          ) ' . $dataSearch . '
                                          GROUP BY auts.NPM ORDER BY auts.NPM ASC';
        } else if ($data_arr['StatusKRS'] == '') {
            $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiID, auts.ProdiGroupID, em.Name AS MentorName, em.NIP AS MentorNIP
                                          FROM db_academic.auth_students auts
                                          LEFT JOIN db_academic.mentor_academic mac ON (mac.NPM = auts.NPM)
                                          LEFT JOIN db_employees.employees em ON (em.NIP = mac.NIP)
                                          WHERE ( auts.StatusStudentID = "3" ' . $w_year . ' ' . $w_prodi . ' ) ' . $dataSearch . '
                                          GROUP BY auts.NPM ORDER BY auts.NPM ASC';
        } else {

            $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiID, auts.ProdiGroupID, em.Name AS MentorName, em.NIP AS MentorNIP
                                          FROM db_academic.auth_students auts
                                          LEFT JOIN db_academic.mentor_academic mac ON (mac.NPM = auts.NPM)
                                          LEFT JOIN db_employees.employees em ON (em.NIP = mac.NIP)
                                          LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                                          WHERE (stdk.SemesterID = "' . $data_arr['SemesterID'] . '" AND auts.StatusStudentID = "3" ' . $w_year . ' ' . $w_prodi . $w_StatusKRS . ' )
                                          ' . $dataSearch . '
                                          GROUP BY auts.NPM ORDER BY auts.NPM ASC';
        }



        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();


        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            // Credit
            $dataCredit = $this->m_api->getMaxCredit('ta_' . $row['Year'], $row['NPM'], $row['Year'], $data_arr['SemesterID'], $row['ProdiID']);

            // Get Course in std_krs
            $dataCourse = $this->db->query('SELECT sk.Status, mk.NameEng AS CourseEng, cd.TotalSKS AS Credit FROM db_academic.std_krs sk
                                                            LEFT JOIN db_academic.schedule s ON (s.ID = sk.ScheduleID)
                                                            LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                            WHERE sk.NPM = "' . $row['NPM'] . '" AND sk.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                             GROUP BY sk.ScheduleID ORDER BY mk.MKCode ASC')
                ->result_array();

            $totalCreditSP = 0;
            $course = '';
            if (count($dataCourse) > 0) {
                foreach ($dataCourse as $item) {

                    $statusKRS = '<span style="color: #9e9e9e;"><i class="fa fa-question-circle"></i> Student not yet sent</span>';
                    if ($item['Status'] == '1') {
                        $statusKRS = '<span style="color:#2196f3;"><i class="fa fa-clock-o"></i> Waiting approved by Mentor</span>';
                    } else if ($item['Status'] == '2') {
                        $statusKRS = '<span style="color:#2196f3;"><i class="fa fa-clock-o"></i> Waiting approved by Kaprodi</span>';
                    } else if ($item['Status'] == '3') {
                        $statusKRS = '<span style="color:green;"><i class="fa fa-check-circle"></i> Study Plan has been approved</span>';
                    } else if ($item['Status'] == '-2') {
                        $statusKRS = '<span style="color: #d03126;"><i class="fa fa-repeat"></i> Rejected by Mentor</span>';
                    } else if ($item['Status'] == '-3') {
                        $statusKRS = '<span style="color: #d03126;"><i class="fa fa-repeat"></i> Rejected by Kaprodi</span>';
                    }

                    $course = $course . '<div><b>' . $item['CourseEng'] . ' (' . $item['Credit'] . ')</b> | Status : ' . $statusKRS . '</div>';
                    $totalCreditSP = $totalCreditSP + $item['Credit'];
                }
            }

            // Get Payment
            $datapayment = $this->m_api->getPayment($data_arr['SemesterID'], $row['NPM']);

            $BPPPay_Status = '';
            $CreditPay_Status = '';

            if (count($datapayment) > 0) {
                foreach ($datapayment as $item) {
                    if ($item['PTID'] == 2 || $item['PTID'] == '2') {
                        $BPPPay_Status = $item['Status'];
                    } else if ($item['PTID'] == 3 || $item['PTID'] == '3') {
                        $CreditPay_Status = $item['Status'];
                    }
                }
            }

            $BPPPay = ($BPPPay_Status != '' && $BPPPay_Status != '0' && $BPPPay_Status != 0) ? '<i class="fa fa-check" style="color: green;"></i>' : '-';
            $CreditPay = ($CreditPay_Status != '' && $CreditPay_Status != '0' && $CreditPay_Status != 0) ? '<i class="fa fa-check" style="color: green;"></i>' : '-';

            $ProdiGroupID = ($row['ProdiGroupID'] != '' && $row['ProdiGroupID'] != null && $data_arr['SemesterID'] > 13) ? $row['ProdiGroupID'] : '-';
            $btnAction = ($BPPPay_Status != '' && $BPPPay_Status != '0' && $BPPPay_Status != 0)
                ? '<a href="' . base_url('academic/study-planning/course-offer/' . $data_arr['SemesterID'] . '/' . $ProdiGroupID . '/' . $row['NPM']) . '" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a>'
                : '<span style="color: red;">BPP Unpaid</span>';

            $btnBatalTambah = ($BPPPay_Status != '' && $BPPPay_Status != '0' && $BPPPay_Status != 0)
                ? '<a href="' . base_url('academic/study-planning/batal-tambah/' . $data_arr['SemesterID'] . '/' . $ProdiGroupID . '/' . $row['NPM']) . '" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></a>'
                : '<span style="color: red;">BPP Unpaid</span>';

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:left;"><b>' . $row['Name'] . '</b><br/>'
                . $row['NPM'] . '<br/><span style="color: #0b97c4;">Last IPS : ' . number_format(round($dataCredit['LastIPS'], 2), 2) .
                ' | IPK : ' . number_format(round($dataCredit['IPK'], 2), 2) . '</span></div>';
            $nestedData[] = '<div  style="text-align:left;">' . $row['MentorName'] . '<br/>' . $row['MentorNIP'] . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $BPPPay . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $CreditPay . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . $course . '</div>';
            $nestedData[] = '<div  style="text-align:center;"><u style="color: #2196f3;">' . $totalCreditSP . '</u> of ' . $dataCredit['MaxCredit']['Credit'] . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $btnAction . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $btnBatalTambah . '</div>';


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

    public function getListStudentKrsOnline()
    {

        $requestData = $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        $w_ClassOf = ($data_arr['ClassOf'] != '') ? ' AND auts.Year = "' . $data_arr['ClassOf'] . '" ' : '';
        $w_StatusKRS = ($data_arr['StatusKRS'] != '' && $data_arr['StatusKRS'] != 'NOT_EXISTS') ? ' AND stdk.Status = "' . $data_arr['StatusKRS'] . '" ' : '';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( auts.Name LIKE "%' . $search . '%" OR auts.NPM LIKE "%' . $search . '%") ';
        }

        if ($data_arr['StatusKRS'] == 'NOT_EXISTS') {

            $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiGroupID, ps.NameEng AS Prodi, ss.Description AS StatusStudent,
                              stdk.Input_At
                              FROM db_academic.mentor_academic ma
                              LEFT JOIN db_academic.auth_students auts ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                              WHERE ( ma.NIP = "' . $data_arr['NIP'] . '"
                              AND auts.StatusStudentID = "' . $data_arr['Status'] . '" ' . $w_ClassOf . $w_StatusKRS . ' AND ma.NPM NOT IN
                            (SELECT auts_1.NPM
                              FROM db_academic.mentor_academic ma_1
                              LEFT JOIN db_academic.auth_students auts_1 ON (ma_1.NPM = auts_1.NPM)
                              LEFT JOIN db_academic.std_krs stdk_1 ON (stdk_1.NPM = auts_1.NPM)
                              WHERE (stdk_1.SemesterID = "' . $data_arr['SemesterID'] . '" AND ma_1.NIP = "' . $data_arr['NIP'] . '"
                               ) GROUP BY auts_1.NPM) ) ' . $dataSearch . '  GROUP BY auts.NPM ORDER BY stdk.Input_At DESC, ma.NPM ASC';
        } else if ($data_arr['StatusKRS'] == '') {
            $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiGroupID, ps.NameEng AS Prodi, ss.Description AS StatusStudent,
                              stdk.Input_At
                              FROM db_academic.mentor_academic ma
                              LEFT JOIN db_academic.auth_students auts ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                              WHERE (ma.NIP = "' . $data_arr['NIP'] . '"
                              AND auts.StatusStudentID = "' . $data_arr['Status'] . '" ' . $w_ClassOf . $w_StatusKRS . ' )
                              ' . $dataSearch . '  GROUP BY auts.NPM ORDER BY stdk.Input_At DESC, ma.NPM ASC';
        } else {
            $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiGroupID, ps.NameEng AS Prodi, ss.Description AS StatusStudent,
                              stdk.Input_At
                              FROM db_academic.mentor_academic ma
                              LEFT JOIN db_academic.auth_students auts ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                              WHERE (stdk.SemesterID = "' . $data_arr['SemesterID'] . '" AND ma.NIP = "' . $data_arr['NIP'] . '"
                              AND auts.StatusStudentID = "' . $data_arr['Status'] . '" ' . $w_ClassOf . $w_StatusKRS . ' )
                              ' . $dataSearch . '  GROUP BY auts.NPM ORDER BY stdk.Input_At DESC, ma.NPM ASC';
        }






        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            // Get Photo
            $DBStudent = 'ta_' . $row['Year'];
            $dataPhoto = $this->db->select('Photo')->get_where($DBStudent . '.students', array('NPM' => $row['NPM']), 1)->result_array();
            $path = 'uploads/students/' . $DBStudent . '/' . $dataPhoto[0]['Photo'];
            $Photo = base_url('images/icon/student.png');
            if (file_exists($path) && $dataPhoto[0]['Photo'] != '') {
                $Photo = base_url('uploads/students/' . $DBStudent . '/' . $dataPhoto[0]['Photo']);
            }

            // Semester
            $Semester = $this->m_api->getSemesterStudentByYear($data_arr['SemesterID'], $row['Year']);

            $token_npm = $this->jwt->encode(array('SemesterID' => $data_arr['SemesterID'], 'NPM' => $row['NPM'], 'ProdiGroupID' => $row['ProdiGroupID']), 'UAP)(*');
            $btn = ($data_arr['Status'] != '' && $data_arr['Status'] == 3)
                ? '<button data-url="' . url_sign_in_lecturers . 'krs-online/list-student/approved-mentor/' . $token_npm . '" class="btn btn-sm btn-default btnShowKRS">
                        <i class="fa fa-edit"></i>
                      </button>'
                : '-';

            $dataCourse = $this->db->query('SELECT mk.NameEng AS CourseEng, mk.MKCode, s.ClassGroup, stdk.Status FROM db_academic.std_krs stdk
                                                      LEFT JOIN db_academic.schedule s ON (s.ID = stdk.ScheduleID)
                                                      LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = stdk.ScheduleID)
                                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                      WHERE stdk.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                      AND stdk.NPM = "' . $row['NPM'] . '"
                                                       GROUP BY stdk.ScheduleID
                                                       ORDER BY mk.MKCode ')->result_array();

            $course = '';
            if (count($dataCourse) > 0) {
                foreach ($dataCourse as $item) {

                    $Status = '-';
                    if ($item['Status'] == 0 || $item['Status'] == '0') {
                        $Status = '<span style="color: #9e9e9e;"><i class="fa fa-hourglass-half"></i> Student not yet send</span>';
                    } else if ($item['Status'] == 1 || $item['Status'] == '1') {
                        $Status = '<span style="color: #00bcd4;"><i class="fa fa-clock-o"></i> Waiting approval</span>';
                    } else if ($item['Status'] == -2 || $item['Status'] == '-2') {
                        $Status = '<span style="color: darkred;"><i class="fa fa-repeat margin-right"></i> Rejected by Mentor</span>';
                    } else if ($item['Status'] == -3 || $item['Status'] == '-3') {
                        $Status = '<span style="color: darkred;"><i class="fa fa-repeat margin-right"></i> Rejected by Kaprodi</span>';
                    } else if ($item['Status'] == 2 || $item['Status'] == '2') {
                        $Status = '<span style="color: green;"><i class="fa fa-check-circle"></i> Approved by Mentor</span>';
                    } else if ($item['Status'] == 3 || $item['Status'] == '3') {
                        $Status = '<span style="color: green;"><i class="fa fa-check-circle"></i> Approved by Kaprodi</span>';
                    }

                    $course = $course . '<div>' . $item['ClassGroup'] . ' | <span style="color: #2196f3;">' . $item['MKCode'] . ' - ' . $item['CourseEng'] . '</span> | Status : ' . $Status . '</div>';
                }
            } else {
                $course = '-';
            }

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:center;"><img data-src="' . $Photo . '" class="img-rounded img-fitter" width="40" height="50"></div>';
            $nestedData[] = '<div  style="text-align:left;"><b>' . $row['Name'] . '</b><br/><span>' . $row['NPM'] . '</span><br/>' . $row['Prodi'] . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $Semester . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . $course . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $btn . '</div>';
            $nestedData[] = '<div  style="text-align:center;font-size: 12px;">' . ucwords(strtolower($row['StatusStudent'])) . '</div>';

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

    public function getListStudentKrsOnlineKaprodi()
    {
        $requestData = $_REQUEST;

        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);


        $w_ClassOf = ($data_arr['ClassOf'] != '') ? ' AND auts.Year = "' . $data_arr['ClassOf'] . '" ' : '';
        $w_StatusKRS = ($data_arr['StatusKRS'] != '' && $data_arr['StatusKRS'] != 'NOT_EXISTS') ? ' AND stdk.Status = "' . $data_arr['StatusKRS'] . '" ' : '';
        $w_Mentor = ($data_arr['MentorNIP'] != '') ? ' AND ma.NIP = "' . $data_arr['MentorNIP'] . '" ' : '';


        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( auts.Name LIKE "%' . $search . '%" OR auts.NPM LIKE "%' . $search . '%") ';
        }

        if ($data_arr['StatusKRS'] == 'NOT_EXISTS') {

            $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiGroupID, ps.NameEng AS Prodi, ss.Description AS StatusStudent, em.Name AS MentorName,
                              ma.NIP AS MentorNIP, stdk.Input_At
                              FROM  db_academic.auth_students auts
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                              LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                              WHERE (auts.ProdiID = "' . $data_arr['ProdiID'] . '"  AND auts.StatusStudentID = "' . $data_arr['Status'] . '" ' . $w_ClassOf . $w_StatusKRS . $w_Mentor . '
                              AND auts.NPM NOT IN (SELECT auts_1.NPM FROM  db_academic.auth_students auts_1
                                                  LEFT JOIN db_academic.std_krs stdk_1 ON (stdk_1.NPM = auts_1.NPM)
                                                  WHERE (stdk_1.SemesterID = "' . $data_arr['SemesterID'] . '" AND auts_1.ProdiID = "' . $data_arr['ProdiID'] . '"
                                                  AND auts_1.StatusStudentID = "' . $data_arr['Status'] . '" )
                                                  GROUP BY auts_1.NPM)
                              ) ' . $dataSearch . '
                              GROUP BY auts.NPM ORDER BY auts.NPM ASC';

            $queryDefaultTotal = 'SELECT COUNT(*) Total FROM (SELECT auts.NPM FROM  db_academic.auth_students auts
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                              LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                              WHERE (auts.ProdiID = "' . $data_arr['ProdiID'] . '"  AND auts.StatusStudentID = "' . $data_arr['Status'] . '" ' . $w_ClassOf . $w_StatusKRS . $w_Mentor . '
                              AND auts.NPM NOT IN (SELECT auts_1.NPM FROM  db_academic.auth_students auts_1
                                                  LEFT JOIN db_academic.std_krs stdk_1 ON (stdk_1.NPM = auts_1.NPM)
                                                  WHERE (stdk_1.SemesterID = "' . $data_arr['SemesterID'] . '" AND auts_1.ProdiID = "' . $data_arr['ProdiID'] . '"
                                                  AND auts_1.StatusStudentID = "' . $data_arr['Status'] . '" )
                                                  GROUP BY auts_1.NPM)
                              ) ' . $dataSearch . '
                              GROUP BY auts.NPM) xx';
        } else if ($data_arr['StatusKRS'] == '') {

            $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiGroupID, ps.NameEng AS Prodi, ss.Description AS StatusStudent, em.Name AS MentorName,
                              ma.NIP AS MentorNIP, stdk.Input_At
                              FROM  db_academic.auth_students auts
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                              LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                              WHERE (auts.ProdiID = "' . $data_arr['ProdiID'] . '"  AND auts.StatusStudentID = "' . $data_arr['Status'] . '" ' . $w_ClassOf . $w_StatusKRS . $w_Mentor . ' ) ' . $dataSearch . '
                              GROUP BY auts.NPM ORDER BY auts.NPM ASC';

            $queryDefaultTotal = 'SELECT COUNT(*) Total FROM (SELECT auts.NPM FROM  db_academic.auth_students auts
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                              LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                              WHERE (auts.ProdiID = "' . $data_arr['ProdiID'] . '"  AND auts.StatusStudentID = "' . $data_arr['Status'] . '" ' . $w_ClassOf . $w_StatusKRS . $w_Mentor . ' ) ' . $dataSearch . '
                              GROUP BY auts.NPM) xx';
        } else {

            $queryDefault = 'SELECT auts.NPM, auts.Name, auts.Year, auts.ProdiGroupID, ps.NameEng AS Prodi, ss.Description AS StatusStudent, em.Name AS MentorName,
                              ma.NIP AS MentorNIP, stdk.Input_At
                              FROM  db_academic.auth_students auts
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                              LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                              WHERE (stdk.SemesterID = "' . $data_arr['SemesterID'] . '" AND auts.ProdiID = "' . $data_arr['ProdiID'] . '"
                              AND auts.StatusStudentID = "' . $data_arr['Status'] . '" ' . $w_ClassOf . $w_StatusKRS . $w_Mentor . ' ) ' . $dataSearch . '
                              GROUP BY auts.NPM ORDER BY stdk.Input_At ASC, auts.NPM ASC';

            $queryDefaultTotal = 'SELECT COUNT(*) Total FROM (SELECT auts.NPM FROM  db_academic.auth_students auts
                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                              LEFT JOIN db_academic.status_student ss ON (ss.ID = auts.StatusStudentID)
                              LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = auts.NPM)
                              LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                              LEFT JOIN db_academic.std_krs stdk ON (stdk.NPM = auts.NPM)
                              WHERE (stdk.SemesterID = "' . $data_arr['SemesterID'] . '" AND auts.ProdiID = "' . $data_arr['ProdiID'] . '"
                              AND auts.StatusStudentID = "' . $data_arr['Status'] . '" ' . $w_ClassOf . $w_StatusKRS . $w_Mentor . ' ) ' . $dataSearch . '
                              GROUP BY auts.NPM) xx';
        }

        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            // Get Photo
            $DBStudent = 'ta_' . $row['Year'];
            $dataPhoto = $this->db->select('Photo')->get_where($DBStudent . '.students', array('NPM' => $row['NPM']), 1)->result_array();
            $path = 'uploads/students/' . $DBStudent . '/' . $dataPhoto[0]['Photo'];
            $Photo = base_url('images/icon/student.png');
            if (file_exists($path) && $dataPhoto[0]['Photo'] != '') {
                $Photo = base_url('uploads/students/' . $DBStudent . '/' . $dataPhoto[0]['Photo']);
            }

            // Semester
            $Semester = $this->m_api->getSemesterStudentByYear($data_arr['SemesterID'], $row['Year']);

            // Course
            $dataCourse = $this->db->query('SELECT mk.NameEng AS CourseEng, mk.MKCode, s.ClassGroup, stdk.Status FROM db_academic.std_krs stdk
                                                      LEFT JOIN db_academic.schedule s ON (s.ID = stdk.ScheduleID)
                                                      LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = stdk.ScheduleID)
                                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                      WHERE stdk.SemesterID = "' . $data_arr['SemesterID'] . '"
                                                      AND stdk.NPM = "' . $row['NPM'] . '"
                                                       GROUP BY stdk.ScheduleID
                                                       ORDER BY mk.MKCode ')->result_array();

            $course = '';
            if (count($dataCourse) > 0) {
                foreach ($dataCourse as $item) {

                    $Status = '-';
                    if ($item['Status'] == 0 || $item['Status'] == '0') {
                        $Status = '<span style="color: #9e9e9e;"><i class="fa fa-hourglass-half"></i> Student not yet send</span>';
                    } else if ($item['Status'] == 1 || $item['Status'] == '1') {
                        $Status = '<span style="color: #9e9e9e;"><i class="fa fa-clock-o"></i> Waiting approval <b>mentor</b></span>';
                        if ($row['MentorNIP'] == $data_arr['NIP']) {
                            $Status = '<span style="color: #00bcd4;"><i class="fa fa-clock-o"></i> Waiting approval <b>kaprodi</b></span>';
                        }
                    } else if ($item['Status'] == -2 || $item['Status'] == '-2') {
                        $Status = '<span style="color: darkred;"><i class="fa fa-repeat margin-right"></i> Rejected by Mentor</span>';
                    } else if ($item['Status'] == -3 || $item['Status'] == '-3') {
                        $Status = '<span style="color: darkred;"><i class="fa fa-repeat margin-right"></i> Rejected by Kaprodi</span>';
                    } else if ($item['Status'] == 2 || $item['Status'] == '2') {
                        $Status = '<span style="color: #00bcd4;"><i class="fa fa-clock-o"></i> Waiting approval <b>kaprodi</b></span>';
                    } else if ($item['Status'] == 3 || $item['Status'] == '3') {
                        $Status = '<span style="color: green;"><i class="fa fa-check-circle"></i> Approved by Kaprodi</span>';
                    }

                    $course = $course . '<div>' . $item['ClassGroup'] . ' | <span style="color: #2196f3;">' . $item['MKCode'] . ' - ' . $item['CourseEng'] . '</span> | Status : ' . $Status . '</div>';
                }
            } else {
                $course = '-';
            }

            $token_npm = $this->jwt->encode(array('SemesterID' => $data_arr['SemesterID'], 'NPM' => $row['NPM'], 'ProdiGroupID' => $row['ProdiGroupID']), 'UAP)(*');
            $btn = ($data_arr['Status'] != '' && $data_arr['Status'] == 3)
                ? '<button data-url="' . url_sign_in_lecturers . 'krs-online/list-student/approved-kaprodi/' . $token_npm . '" class="btn btn-sm btn-default btnShowKRS">
                        <i class="fa fa-edit"></i>
                      </button>'
                : '-';

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = '<div  style="text-align:center;"><img data-src="' . $Photo . '" class="img-rounded img-fitter" width="40" height="50"></div>';
            $nestedData[] = '<div  style="text-align:left;"><b>' . $row['Name'] . '</b><br/><span>' . $row['NPM'] . '</span><br/><span class="label label-info" style="position: relative;left: 0px;">Mentor : ' . $row['MentorName'] . '</span></div>';
            $nestedData[] = '<div  style="text-align:center;">' . $Semester . '</div>';
            $nestedData[] = '<div  style="text-align:left;">' . $course . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $btn . '</div>';
            $nestedData[] = '<div  style="text-align:center;font-size: 12px;">' . ucwords(strtolower($row['StatusStudent'])) . '</div>';

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

    public function crudNotification()
    {

        $data_arr = $this->getInputToken();

        if ($data_arr['action'] == 'addNewNotification') {
            // Get Personal
            $IDDivision = $this->session->userdata('IDdepartementNavigation');
            $dataUser = $this->db->select('NIP')->get_where('db_employees.rule_users', array('IDDivision' => $IDDivision))->result_array();

            if (count($dataUser) > 0) {

                $dataInsert = (array) $data_arr['dataInsert'];

                $this->db->insert('db_notifikasi.notification', $dataInsert);
                $insert_id = $this->db->insert_id();

                // Add in n personal
                for ($i = 0; $i < count($dataUser); $i++) {
                    $insert_n_personal = array(
                        'ID_notification' => $insert_id,
                        'Div' => $IDDivision,
                        'People' => $dataUser[$i]['NIP']
                    );
                    $this->db->insert('db_notifikasi.n_personal', $insert_n_personal);
                }
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'hideNotifBrowser') {

            $this->db->set('ShowNotif', '1');
            $this->db->where('ID', $data_arr['IDUser']);
            $this->db->update('db_notifikasi.n_personal');

            return print_r(1);
        }
    }

    public function crudLog()
    {
        $data_arr = $this->getInputToken();

        if ($data_arr['action'] == 'readLog') {
            $UserID = $data_arr['UserID'];
            //UPDATED BY FEBRI
            if (!empty($data_arr['StatusRead'])) {
                $dataLog = $this->m_log->readDataLog($UserID, $data_arr['StatusRead']);
            } else {
                $dataLog = $this->m_log->readDataLog($UserID);
            }
            //END UPDATED
            return print_r(json_encode($dataLog));
        } else if ($data_arr['action'] == 'getTotalUnreadLog') {

            $UserID = $data_arr['UserID'];
            $where = 'where b.UserID =  "' . $UserID . '" and b.StatusRead  = "0" ';
            if (array_key_exists('Alumni', $data_arr) && $data_arr['Alumni'] == 'yes') {
                $where .= ' And a.URLDirectAlumni is NOT NULL and a.URLDirectAlumni != ""';
            }

            $data = $this->db->query('
                                        select b.ID from db_notifikasi.logging as a
                                        join db_notifikasi.logging_user as b on a.ID = b.IDLogging
                                        ' . $where . '
                                    ')->result_array();

            // $data = $this->db->select('ID')->get_where('db_notifikasi.logging_user',
            // array('UserID' => $UserID, "StatusRead" => "0"))->result_array();
            return print_r(json_encode(count($data)));
        } else if ($data_arr['action'] == 'readLogUser') {
            $this->db->where('ID', $data_arr['ID_logging_user']);
            $this->db->update('db_notifikasi.logging_user', array('StatusRead' => '1'));
        } elseif ($data_arr['action'] == 'ReadAllLog') {
            $UserID = $data_arr['UserID'];
            $this->db->where('UserID', $UserID);
            if (array_key_exists('Alumni', $data_arr) && $data_arr['Alumni'] == 'yes') {
                $this->db->where('IDLogging in (select ID from db_notifikasi.logging where ID = IDLogging ) ');
            }
            $this->db->update('db_notifikasi.logging_user', array('StatusRead' => '1', 'ShowNotif' => '1'));
            return print_r(json_encode(1));
        }
    }

    public function dropdowngroupmodule()
    {

        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {

            if ($data_arr['action'] == 'getLastgroupmodule') {
                $filterDivisi = $data_arr['filterDivisi'];

                //$data = $this->db->get_where('db_it.group_module',array('IDDivision' => $data_arr['filterDivisi']))->result_array();
                $data = $this->db->query('SELECT NameGroup, IDGroup FROM db_it.group_module WHERE IDDivision = "' . $data_arr['filterDivisi'] . '" ORDER BY IDDivision ASC ')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'getLastmodule') {

                $filterDivisi = $data_arr['filterDivisi'];

                //$data = $this->db->get_where('db_it.group_module',array('IDDivision' => $data_arr['filterDivisi']))->result_array();
                $data = $this->db->query('SELECT NameGroup, IDGroup FROM db_it.group_module WHERE IDDivision = "' . $data_arr['filterDivisi'] . '" ORDER BY IDDivision ASC ')->result_array();

                return print_r(json_encode($data));
            }
        }
    }

    public function dropdownlistmodule()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'getListgroupmodule') {
                $filterGroups = $data_arr['filterGroups'];
                $data = $this->db->query('SELECT IDModule, NameModule FROM db_it.module WHERE IDGroup = "' . $data_arr['filterGroups'] . '" ')->result_array();
                return print_r(json_encode($data));
            }
        }
    }

    public function dropeditgroupmodule()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'getLastdiversion') {
                $filterGroups = $data_arr['IDDivision'];

                $data = $this->db->query('SELECT DISTINCT a.NameGroup, b.IDGroup
                FROM db_it.group_module AS a
                LEFT JOIN db_it.module AS b ON (a.IDGroup = b.IDGroup)
                WHERE a.IDDivision = "' . $data_arr['IDDivision'] . '" ')->result_array();
                return print_r(json_encode($data));
            }
        }
    }

    public function dropeditmodule()
    {
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'geteditLastmodule') {
                $filterGroups = $data_arr['filtereditgroup'];
                $data = $this->db->query('SELECT b.IDModule, b.NameModule
                                        FROM db_it.group_module AS a
                                        LEFT JOIN db_it.module AS b ON (a.IDGroup = b.IDGroup)
                                        WHERE a.IDGroup = "' . $data_arr['filtereditgroup'] . '" ')->result_array();
                return print_r(json_encode($data));
            }
        }
    }





    public function crudTransferStudent()
    {

        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {
            if ($data_arr['action'] == 'readFromStudentTransfer') {
                $data = $this->db->select('NPM,Name')->order_by('NPM', 'ASC')->get_where(
                    'db_academic.auth_students',
                    array('Year' => $data_arr['ClassOf'], 'ProdiID' => $data_arr['ProdiID'])
                )->result_array();

                return print_r(json_encode($data));
            }
            if ($data_arr['action'] == 'addNoteInTransferStd') {

                $dataUpdate = (array) $data_arr['dataUpdate'];
                $TSID = $data_arr['TSID'];

                $this->db->where('ID', $TSID);
                $this->db->update('db_academic.transfer_student', $dataUpdate);

                return print_r(1);
            } else if ($data_arr['action'] == 'readReason') {
                $data = $this->db->get('db_academic.transfer_type')->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'checkNPMTransferStudent') {
                $data = $this->db->select('NPM')->get_where(
                    'db_academic.auth_students',
                    array('NPM' => $data_arr['NPM'])
                )->result_array();

                $dataEmb = $this->db->select('NIP')->get_where(
                    'db_employees.employees',
                    array('NIP' => $data_arr['NPM'])
                )->result_array();


                if (count($data) > 0 || count($dataEmb) > 0) {
                    $result = array('Status' => 0);
                } else {
                    $result = array('Status' => 1);
                }

                return print_r(json_encode($result));
            } else if ($data_arr['action'] == 'readClassOfTransferStd') {
                $data = $this->db->query('SELECT ClassOf FROM db_finance.tuition_fee
                                                    WHERE ProdiID = "' . $data_arr['ProdiID'] . '"
                                                    GROUP BY ClassOf ORDER BY ClassOf ASC ')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'getLastNIMTransferStudent') {
                $ProdiID = $data_arr['ProdiID'];
                $ClassOf = $data_arr['ClassOf'];

                $db = 'ta_' . $ClassOf;
                $data = $this->db->select('NPM')->order_by('NPM', 'DESC')->limit(1)
                    ->get_where($db . '.students', array('ProdiID' => $ProdiID))->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'readBintangTransferStd') {
                $data = $this->db->query('SELECT Pay_Cond FROM db_finance.tuition_fee
                                                        WHERE ProdiID = "' . $data_arr['ProdiID'] . '"
                                                         AND ClassOf = "' . $data_arr['ClassOf'] . '"
                                                         GROUP BY Pay_Cond ORDER BY Pay_Cond ASC ')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'addingTransferStudent') {

                $StatusBefore = '';

                // DB TA - Get Data Student Lama
                $db_f = 'ta_' . $data_arr['fromClassOf'];
                $dataStd_f = $this->db->limit(1)->get_where($db_f . '.students', array('NPM' => $data_arr['fromStudent']))->result_array();

                $Possword_Old = '';
                if (count($dataStd_f)) {
                    $d = $dataStd_f[0];
                    $StatusBefore = $dataStd_f[0]['StatusStudentID'];
                    unset($d['ID']);
                    $d['ProdiID'] = $data_arr['toProdi'];
                    $d['NPM'] = $data_arr['toNewNPM'];
                    $d['ClassOf'] = $data_arr['toClassOf'];
                    $d['Address'] = trim($d['Address']);
                    $d['StatusStudentID'] = 3;

                    if ($d['DateOfBirth'] != null && $d['DateOfBirth'] != '' && $d['DateOfBirth'] != '0000-00-00') {
                        $d_exp = explode('-', $d['DateOfBirth']);
                        $Possword_Old = md5($d_exp[2] . '' . $d_exp[1] . '' . substr($d_exp[0], 2, 2));
                    }

                    // Insert To
                    $db_t = 'ta_' . $data_arr['toClassOf'];
                    $this->db->insert($db_t . '.students', $d);

                    // Update Data Lama
                    $this->db->set('StatusStudentID', 9);
                    $this->db->where('ID', $dataStd_f[0]['ID']);
                    $this->db->update($db_f . '.students');
                    $this->db->reset_query();
                }


                // DB AUTH Student - Get data lama
                $d_aut_f = $this->db->get_where('db_academic.auth_students', array('NPM' => $data_arr['fromStudent']))->result_array();
                if (count($d_aut_f) > 0) {
                    $d = $d_aut_f[0];
                    unset($d['ID']);
                    unset($d['ProdiGroupID']);
                    unset($d['Password']);
                    $d['NPM'] = $data_arr['toNewNPM'];
                    $d['Name'] = trim(ucwords($d['Name']));
                    $d['ProdiID'] = $data_arr['toProdi'];
                    $d['Year'] = $data_arr['toClassOf'];
                    $d['Status'] = '-1';
                    $d['EmailPU'] = $data_arr['toNewNPM'] . '@podomorouniversity.ac.id';
                    $d['Password_Old'] = $Possword_Old;
                    $d['StatusStudentID'] = 3;

                    if ($data_arr['TransferTypeID'] != 1 || $data_arr['TransferTypeID'] != '1') {
                        unset($d['Pay_Cond']);
                    }

                    // Insert Ke DB Auth
                    $this->db->insert('db_academic.auth_students', $d);

                    // Update Data Lama
                    $this->db->set('StatusStudentID', 9);
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

                $this->db->insert('db_academic.transfer_student', $dataIns);


                $TransferTypeID = $data_arr['TransferTypeID'];
                // Jika transfer ID == 1, maka tagihan dan biaya kuliah prodi baru sesia dengan prodi lama
                if ($TransferTypeID == 1 || $TransferTypeID == '1') {

                    // m_tition_feee
                    $dataT = $this->db->get_where('db_finance.m_tuition_fee', array('NPM' => $data_arr['fromStudent']))->result_array();
                    if (count($dataT) > 0) {
                        for ($t = 0; $t < count($dataT); $t++) {
                            $dIns = $dataT[$t];
                            unset($dIns['ID']);
                            $dIns['NPM'] = $data_arr['toNewNPM'];
                            $this->db->insert('db_finance.m_tuition_fee', $dIns);
                        }
                    }
                } else {
                    $sql = 'select * from db_finance.payment_type';
                    $query = $this->db->query($sql, array())->result_array();

                    for ($i = 0; $i < count($query); $i++) {
                        $PTID = $query[$i]['ID'];

                        $sql1 = 'SELECT * FROM db_finance.tuition_fee tf
                                                            WHERE
                                                            tf.ProdiID = "' . $data_arr['PaymentProdiID'] . '"
                                                            AND tf.ClassOf = "' . $data_arr['PaymentClassOf'] . '"
                                                            AND tf.Pay_Cond = "' . $data_arr['PaymentBintang'] . '"
                                                            AND tf.PTID = "' . $PTID . '" ';


                        $query1 = $this->db->query($sql1, array())->result_array();


                        $Invoice = 0;

                        for ($k = 1; $k <= 14; $k++) {
                            $st = $k;
                            switch ($PTID) {
                                case 1:
                                case 4:
                                    if ($k == 1) {
                                        $Invoice = $query1[0]['Cost'];
                                        $st = 15;
                                    }
                                    break;
                                case 2:
                                case 3:
                                    $Invoice = $query1[0]['Cost'];
                                    break;
                                default:
                                    $Invoice = 0;
                                    break;
                            }
                            $Semester = $k;

                            $dataSave = array(
                                'Semester' => $Semester,
                                'PTID' => $PTID,
                                'NPM' => $data_arr['toNewNPM'],
                                'Invoice' => $Invoice,
                            );
                            $this->db->insert('db_finance.m_tuition_fee', $dataSave);
                            $k = $st;
                        }
                    }
                }


                // Payment
                $dataP = $this->db->get_where('db_finance.payment', array('NPM' => $data_arr['fromStudent']))->result_array();
                if (count($dataP) > 0) {
                    for ($p = 0; $p < count($dataP); $p++) {
                        $dIns = $dataP[$p];

                        // Get payment Student
                        $dataPS = $this->db->get_where('db_finance.payment_students', array('ID_payment' => $dIns['ID']))
                            ->result_array();


                        unset($dIns['ID']);
                        $dIns['NPM'] = $data_arr['toNewNPM'];
                        $this->db->insert('db_finance.payment', $dIns);
                        $insert_id = $this->db->insert_id();

                        if (count($dataPS) > 0) {
                            for ($ps = 0; $ps < count($dataPS); $ps++) {
                                $dsInsert = $dataPS[$ps];
                                unset($dsInsert['ID']);
                                $dsInsert['ID_payment'] = $insert_id;
                                $this->db->insert('db_finance.payment_students', $dsInsert);
                            }
                        }
                    }
                }


                return print_r(1);
            } else if ($data_arr['action'] == 'removeTransverStudent') {

                $dataTS = $this->db->get_where('db_academic.transfer_student', array('ID' => $data_arr['ID']))
                    ->result_array();

                if (count($dataTS) > 0) {
                    $d = $dataTS[0];
                    // Data After
                    $After_NPM = $d['After'];
                    $After_DB =  'ta_' . $d['ClassOfAfter'];

                    $tables = array(
                        $After_DB . '.students', $After_DB . '.study_planning', 'db_academic.auth_students'
                    );
                    $this->db->where('NPM', $After_NPM);
                    $this->db->delete($tables);
                    $this->db->reset_query();

                    $this->db->where('TSID', $d['ID']);
                    $this->db->delete('db_academic.transfer_history_conversion');
                    $this->db->reset_query();



                    $this->db->set('StatusStudentID', $d['StatusBefore']);
                    $this->db->where('NPM', $d['Before']);
                    $this->db->update('db_academic.auth_students');
                    $this->db->reset_query();

                    $Before_DB =  'ta_' . $d['ClassOfBefore'];
                    $this->db->set('StatusStudentID', $d['StatusBefore']);
                    $this->db->where('NPM', $d['Before']);
                    $this->db->update($Before_DB . '.students');
                    $this->db->reset_query();

                    $this->db->where('ID', $data_arr['ID']);
                    $this->db->delete('db_academic.transfer_student');

                    // Delete Pembayaran
                    $dataWherePayment = $this->db->select('ID')->get_where('db_finance.payment', array('NPM' => $After_NPM))->result_array();
                    if (count($dataWherePayment) > 0) {
                        for ($p = 0; $p < count($dataWherePayment); $p++) {

                            $this->db->where('ID_payment', $dataWherePayment[$p]['ID']);
                            $this->db->delete('db_finance.payment_students');
                            $this->db->reset_query();

                            $this->db->where('ID', $dataWherePayment[$p]['ID']);
                            $this->db->delete('db_finance.payment');
                            $this->db->reset_query();
                        }
                    }

                    $this->db->where('NPM', $After_NPM);
                    $this->db->delete('db_finance.m_tuition_fee');
                    $this->db->reset_query();
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'readDataTransferStudent') {
                $TSID = $data_arr['TSID'];

                // Get Data Transfer
                $data = $this->db->query('SELECT ts.*, ps_b.Code AS CodeProdi_B,
                                                      ps_a.Code AS CodeProdi_A, auth.Name AS StudentName
                                                       FROM db_academic.transfer_student ts
                                                      LEFT JOIN db_academic.program_study ps_b ON (ps_b.ID = ts.ProdiBefore)
                                                      LEFT JOIN db_academic.program_study ps_a ON (ps_a.ID = ts.ProdiAfter)
                                                      LEFT JOIN db_academic.auth_students auth ON (auth.NPM = ts.After)
                                                      WHERE ts.ID = "' . $TSID . '" LIMIT 1
                                                      ')->result_array();

                if (count($data) > 0) {
                    $dt = $data[0];

                    // Get Semester Before
                    $C_O_Before = $dt['ClassOfBefore'];
                    $DB_B = 'ta_' . $dt['ClassOfBefore'];
                    $NPM_B = $dt['Before'];
                    $dSem_B = $this->db->get_where('db_academic.semester', array('Year >=' => $C_O_Before))->result_array();
                    $arrSemester_B = [];
                    $NoSem_B = 0;
                    if (count($dSem_B) > 0) {
                        for ($i = 0; $i < count($dSem_B); $i++) {
                            $dt_s = $dSem_B[$i];
                            $NoSem_B++;
                            //                            $course = $this->db->get_where($DB_B.'.study_planning',array('NPM' => $NPM_B, 'SemesterID' => $dt_s['ID']))->result_array();
                            $System = ($dt_s['ID'] >= 13) ? 1 : 0;
                            $course = $this->m_rest->getDataKHS($DB_B, $NPM_B, $dt_s['ID'], '', $System);

                            if (count($course) > 0) {

                                for ($c = 0; $c < count($course); $c++) {
                                    $dataHistory = $this->db->query('SELECT cd.Semester FROM db_academic.transfer_history_conversion thc
                                                                                LEFT JOIN db_academic.curriculum_details cd
                                                                                ON (cd.ID = thc.CDID_After)
                                                                                WHERE thc.TSID = "' . $dt['ID'] . '"
                                                                                AND thc.CDID_Before = "' . $course[$c]['CDID'] . '"  ')->result_array();

                                    $course[$c]['TransferToSemester'] = $dataHistory;
                                }

                                $arr = array(
                                    'Semester' => $NoSem_B,
                                    'SemesterID' => $dt_s['ID'],
                                    'SemesterName' => $dt_s['Name'],
                                    'Course' => $course
                                );


                                array_push($arrSemester_B, $arr);
                            }


                            if ($dt_s['Status'] == 1 || $dt_s['Status'] == '1') {
                                break;
                            }
                        }
                    }

                    // Get Semester After
                    $C_O_After = $dt['ClassOfAfter'];
                    $DB_A = 'ta_' . $dt['ClassOfAfter'];
                    $NPM_A = $dt['After'];

                    // Get MHSWID
                    $dataMhsw = $this->db->select('ID')->get_where($DB_A . '.students', array('NPM' => $NPM_A))->result_array();
                    $data[0]['MhswID'] = $dataMhsw[0]['ID'];

                    $dSem_A = $this->db->get_where('db_academic.semester', array('Year >=' => $C_O_After))->result_array();
                    $arrSemester_A = [];
                    $NoSem_A = 0;
                    if (count($dSem_A) > 0) {
                        for ($i = 0; $i < count($dSem_A); $i++) {
                            $dt_s = $dSem_A[$i];
                            $NoSem_A++;

                            $System = ($dt_s['ID'] >= 13) ? 1 : 0;
                            $course = $this->m_rest->getDataKHS($DB_A, $NPM_A, $dt_s['ID'], '', $System);

                            $arr = array(
                                'Semester' => $NoSem_A,
                                'SemesterID' => $dt_s['ID'],
                                'SemesterName' => $dt_s['Name'],
                                'Course' => $course
                            );

                            array_push($arrSemester_A, $arr);

                            if ($dt_s['Status'] == 1 || $dt_s['Status'] == '1') {
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
            } else if ($data_arr['action'] == 'getCourseTransferStudent') {

                $ClassOf = $data_arr['ClassOf'];
                $Semester = $data_arr['Semester'];
                $ProdiID = $data_arr['ProdiID'];

                $dataCourse = $this->db->query('SELECT cd.ID AS CDID, cd.MKID, cd.TotalSKS AS Credit, mk.MKCode, mk.Name, mk.NameEng
                                                                FROM db_academic.curriculum_details cd
                                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                                LEFT JOIN db_academic.curriculum c ON (c.ID =  cd.CurriculumID)
                                                                WHERE cd.Semester = "' . $Semester . '"
                                                                 AND c.Year = "' . $ClassOf . '"
                                                                 AND cd.ProdiID = "' . $ProdiID . '" ORDER BY cd.MKID ASC')->result_array();


                return print_r(json_encode($dataCourse));
            } else if ($data_arr['action'] == 'replaceCourseTransferStd') {

                // Get data coursenya
                $DB_B = $data_arr['DB_B'];
                $dataBefore = $this->db->limit(1)->get_where($DB_B . '.study_planning', array('ID' => $data_arr['SPID']))->result_array();

                $insert_id = 0;
                if (count($dataBefore) > 0) {
                    $d = $dataBefore[0];
                    $d['SemesterID'] = $data_arr['SemesterID'];
                    $d['MhswID'] = $data_arr['MhswID'];
                    $d['NPM'] = $data_arr['NPM_A'];
                    $d['CDID'] = $data_arr['CDID'];
                    $d['MKID'] = $data_arr['MKID'];
                    $d['Credit'] = $data_arr['Credit'];
                    $d['TransferCourse'] = '1';

                    unset($d['ID']);
                    $this->db->insert($data_arr['DB_A'] . '.study_planning', $d);
                    $insert_id = $this->db->insert_id();

                    $insertHistory = (array) $data_arr['insertHistory'];
                    $insertHistory['SPID_After'] = $insert_id;

                    $this->db->insert(
                        'db_academic.transfer_history_conversion',
                        $insertHistory
                    );
                }

                return print_r($insert_id);
            } else if ($data_arr['action'] == 'removeDataTransferStudent') {

                $DB_A = 'ta_' . $data_arr['ClassOf'];
                $SPID = $data_arr['SPID'];

                // Search History
                $NPM = $data_arr['NPM_A'];
                $dataSearch = $this->db->select('ID')->get_where(
                    'db_academic.transfer_history_conversion',
                    array(
                        'NPM_After' => $NPM, 'TA_After' => $data_arr['ClassOf'], 'SPID_After' => $SPID
                    )
                )->result_array();

                if (count($dataSearch) > 0) {
                    $THCID = $dataSearch[0]['ID'];
                    $this->db->where('ID', $THCID);
                    $this->db->delete('db_academic.transfer_history_conversion');
                }

                // Search History
                $this->db->where('ID', $SPID);
                $this->db->delete($DB_A . '.study_planning');




                return print_r(1);
            }
        }
    }

    public function test_data()
    {
        // $arr = array();
        // for ($i=0; $i < 15; $i++) {
        //     $arr[] = 'A'.$i;
        // }

        echo json_encode('Alhadi Rahman');
    }

    public function test_data2()
    {
        $arr = array();
        for ($i = 0; $i < 15; $i++) {
            $arr[] = 'A' . $i;
        }

        echo json_encode($arr);
    }

    public function getLevelEducation()
    {
        $data = $this->db->order_by('ID', 'ASC')->get('db_employees.level_education')->result_array();
        return print_r(json_encode($data));
    }

    public function getLecturerAcademicPosition()
    {
        $data = $this->db->order_by('ID', 'ASC')->get('db_employees.lecturer_academic_position')->result_array();
        return print_r(json_encode($data));
    }

    public function getStudentYear()
    {
        $data = $this->db->query('SELECT Year FROM db_academic.auth_students GROUP BY Year ORDER BY Year DESC')->result_array();

        return print_r(json_encode($data));
    }

    public function getCompanyType()
    {

        $data = $this->db->order_by('name', 'ASC')->get('db_employees.master_industry_type')->result_array();
        return print_r(json_encode($data));
    }
    public function getProvince()
    {

        $data = $this->db->order_by('ProvinceName', 'ASC')->get('db_admission.province')->result_array();
        return print_r(json_encode($data));
    }

    public function getRegions($ProvinceID)
    {

        $data = $this->db->query('SELECT r.* FROM db_admission.region r LEFT JOIN db_admission.province_region pr ON (pr.RegionID = r.RegionID)
                                    WHERE pr.ProvinceID = "' . $ProvinceID . '" 
                                    ORDER BY r.RegionName ASC')->result_array();
        return print_r(json_encode($data));
    }

    public function getDistric($RegionID)
    {

        $data = $this->db->query('SELECT d.* FROM db_admission.district d LEFT JOIN db_admission.region_district rd ON (rd.DistrictID = d.DistrictID)
                                    WHERE rd.RegionID = "' . $RegionID . '" 
                                    ORDER BY d.DistrictName ASC')->result_array();
        return print_r(json_encode($data));
    }

    public function getRangeEmployees()
    {

        $data = $this->db->order_by('ID', 'ASC')->get('db_studentlife.master_range_employees')->result_array();
        return print_r(json_encode($data));
    }

    public function getGrossRevenue()
    {
        $data = $this->db->order_by('ID', 'ASC')->get('db_studentlife.master_range_gross_revenue')->result_array();
        return print_r(json_encode($data));
    }


    /*ADDED BY FEBRI @ FEB 2020*/
    public function getCountry()
    {
        $data = $this->db->order_by('ctr_name', 'ASC')->get('db_admission.country')->result_array();
        return print_r(json_encode($data));
    }

    public function getMaritalStatus()
    {
        $data = $this->db->order_by('ID', 'ASC')->get('db_employees.master_marital_status')->result_array();
        return print_r(json_encode($data));
    }

    /*END ADDED BY FEBRI @ FEB 2020*/

    public function getIjazah()
    {

        $requestData = $_REQUEST;
        $data_arr = $this->getInputToken();
        $showGenrateOption = $data_arr['showGenrateOption'];

        $dataWhereGeneratd = '';
        if ($data_arr['Generated'] != '' && $data_arr['Generated'] == '1') {
            $dataWhereGeneratd = ' AND i.Code IS NOT NULL';
        } else if ($data_arr['Generated'] != '' && $data_arr['Generated'] == '0') {
            $dataWhereGeneratd = ' AND i.Code IS NULL';
        }
        $dataWhereProdi = ($data_arr['ProdiID'] != '') ? 'AND aut_s.ProdiID = "' . $data_arr['ProdiID'] . '"' : '';
        $dataWhere = ' AND aut_s.Year = "' . $data_arr['Year'] . '" ' . $dataWhereProdi . $dataWhereGeneratd;
        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];
            $dataSearch = 'AND ( aut_s.Name LIKE "%' . $search . '%" OR aut_s.NPM LIKE "%' . $search . '%"
                           OR ps.Name LIKE "%' . $search . '%"  OR ps.NameEng LIKE "%' . $search . '%" )';
        }

        $queryDefault = 'SELECT aut_s.*, ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng, 
                                      j.JudiciumsDate, i.Code, i.GenrateDate
                                      FROM db_academic.auth_students aut_s
                                      LEFT JOIN db_academic.program_study ps ON (ps.ID = aut_s.ProdiID)
                                      LEFT JOIN db_academic.judiciums_list jl ON (jl.NPM = aut_s.NPM)
                                      LEFT JOIN db_academic.judiciums j ON (j.ID = jl.JID)
                                      LEFT JOIN db_academic.ijazah i ON (i.NPM = aut_s.NPM)
                                      WHERE aut_s.StatusStudentID = 1 ' . $dataWhere . ' ' . $dataSearch . ' ';

        //        print_r($queryDefault);exit;

        $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';

        $sql = $queryDefault . ' ORDER BY aut_s.NPM ASC LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();
        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];


            $dateJudicium = ($row['JudiciumsDate'] != null && $row['JudiciumsDate'] != '')
                ? date('l, d F Y', strtotime($row['JudiciumsDate']))
                : '';


            $showGenrateOption = ($showGenrateOption == 1
                || $showGenrateOption == '1')
                ? '' : 'hide';

            $viewCheckBox = ($row['GenrateDate'] != '' && $row['GenrateDate'] != null)
                ? ''
                : '<br/><div class="checkbox"><label>
                   <input type="checkbox" class="ckstd ' . $showGenrateOption . '" data-npm="' . $row['NPM'] . '" id="ck_' . $row['NPM'] . '">
                            </label></div>';
            $GenrateDate = ($row['GenrateDate'] != null && $row['GenrateDate'] != '')
                ? date('l, d F Y', strtotime($row['GenrateDate']))
                : '';

            $viewTokenGenrate = ($row['GenrateDate'] != null && $row['GenrateDate'] != '')
                ? $row['Code']
                : '';

            $db_ = 'ta_' . $row['Year'];
            $viewDownloadIjazah = ($row['GenrateDate'] != null && $row['GenrateDate'] != '')
                ? '<button class="btn btn-success btn-sm btnDownloadIjazah" data-db="' . $db_ . '" data-npm="' . $row['NPM'] . '">Download</button>'
                : '';

            $nestedData[] = '<div  style="text-align:center;">' . $no . $viewCheckBox . '</div>';
            $nestedData[] = '<div  style="text-align:left;">
                                <b id="showName_' . $row['NPM'] . '">' . $row['Name'] . '</b><br/>' . $row['NPM'] . '
                             </div>';
            $nestedData[] = '<div  style="text-align:center;">' . $row['ProdiName'] . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $dateJudicium . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $GenrateDate . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $viewTokenGenrate . '</div>';
            $nestedData[] = '<div  style="text-align:center;">' . $viewDownloadIjazah . '</div>';

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
}
