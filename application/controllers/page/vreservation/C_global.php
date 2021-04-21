<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_global extends Vreservation_Controler {

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_sendemail');
        $this->load->model('m_api');
        $this->load->library('JWT');
    }


    public function index()
    {
        $content = $this->load->view($this->pathView.'dashboard/dashboard','',true);
        $this->temp($content);
    }

    public function getschedule($date = null)
    {
        $PostCategory = '0';
        // get room
            // get categoryRoom based Policy
            $getCfgPolicy = $this->m_master->caribasedprimary('db_reservation.cfg_policy','ID_group_user',$this->session->userdata('ID_group_user'));
            $CategoryRoom = json_decode($getCfgPolicy[0]['CategoryRoom']);
            $CategoryRoom = implode(',', $CategoryRoom);
            $OpCategory = $this->m_reservation->OpCategorybyIN($CategoryRoom);
        if (empty($_POST) || $this->input->post('CategoryRoom') == 0) {
            // $sql = 'select * from db_academic.classroom where ID_CategoryRoom in ('.$CategoryRoom.')';
            // $getRoom = $this->db->query($sql, array())->result_array();
            $getRoom = array();
            $endTime = '0';
            $data1 = array();
            $date = date('Y-m-d');
        }
        else if($this->input->post('CategoryRoom') != 0)
        {
            $PostCategory = $this->input->post('CategoryRoom');
            $sql = 'select * from db_academic.classroom where ID_CategoryRoom in ('.$PostCategory.')';
            $getRoom = $this->db->query($sql, array())->result_array();

            if ($date== null) {
                $date = date('Y-m-d');
            }
                $datetime = DateTime::createFromFormat('Y-m-d', $date);
                $NameDay = $datetime->format('l');
                $data1 = $this->m_reservation->getDataClassroomAcademic($NameDay,$date);

            $endTime = '20';
            $getHoursNow = date('H');
            $getHoursNow = ($date == date('Y-m-d')) ? (int)$getHoursNow : 7;
        }
            

        // get data classroom
        $NextDate = '';
        $PreviousDate = '';
        $data['getRoom'] = $getRoom;
        $arrHours = array();

        $data_pass = $data1;
        // SORTING ASC
            usort($data_pass, function($a, $b) {
                return  (int)$a['room'] - (int)$b['room'];
            });
        // print_r($data_pass);
        // die();

        for ($i=7; $i < $endTime; $i++) { 
            // check len
            $a = $i;
            for ($j=0; $j < 2 - strlen($i); $j++) { 
                $a = '0'.$a;
            }
            $d = $a.':30';
            $a = $a.':00';
            $arrHours[] = date("h:i a", strtotime($a));
            //$arrHours[] = date("h:i a", strtotime($d));
            if ($i != $endTime) {
                $arrHours[] = date("h:i a", strtotime($d));
            }
        }
        $data['arrHours'] = $arrHours;
        $data['data_pass'] = $data_pass;
        $data['date'] = $date;
        // previous & next
        $NextDate = date('Y-m-d', strtotime($date . ' +1 day'));
        $PreviousDate = date('Y-m-d', strtotime($date . ' -1 day'));
        $data['NextDate'] = $NextDate;
        $data['PreviousDate'] = $PreviousDate;
        $data['OpCategory'] = $OpCategory;
        $data['PostCategory'] = $PostCategory;
        $chkDate = 0;
        // get booking day
        $DateTPolicy = $this->data['dateDay'];
        if(strtotime($date) >= strtotime($DateTPolicy) )
        {
            $chkDate =  1;
        }
        $data['chkDate'] = $chkDate;
        $content = $this->load->view($this->pathView.'schedule',$data,true);
        echo json_encode($content);
    }

    public function modal_form()
    {
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        $this->data['room'] = $input['room'];
        $this->data['time'] = $input['time'];
        $this->data['user'] = $input['user'];

        // print_r(strlen($input['time']));die();

        if (strlen($input['time']) == 8) {
            $End = date("Y-m-d H:i:s", strtotime($input['tgl'].$input['time']));
            // $End = date("Y-m-d H:i:s", strtotime($input['time']));\
        }
        else
        {
            $input['tgl'] = date('Y-m-d');
            $End = date("Y-m-d H:i:s", strtotime($input['time']));
        }
        $this->data['tgl'] = $input['tgl'];
        // print_r($End);
        // print_r($input['time']);
        // cek time telah melewati waktu sekarang
        $Start = date("Y-m-d H:i:s");
        $time = $this->m_master->countTimeQuery($End, $Start);
        $time = $time[0]['time'];
        $time = explode(':', $time);
        
        if (strpos($time[0], '-00') !== false) {
            $time = ($time[0] * 60) + $time[1];
            $time = '-'.$time;
            $time = (int)$time;
        }
        else
        {
            $time0 = (int)$time[0];
            $time1 = ($time0 * 60);
            if (strpos($time1, '-') !== false) {
                $time = $time1 - $time[1];
            }
            else
            {
                $time = $time1 + $time[1];
            }
            $time = (int)$time;
        }

        $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$input['room']);
        $this->data['RoomDB'] = $getRoom;
        $file = $getRoom[0]['Layout'];
        $this->data['Layout'] = $file;
            switch ($input['Action']) {
                case 'add':
                    if ($time > -30) {
                        echo $this->load->view($this->pathView.'modal_form',$this->data,true);
                    }
                    else
                    {
                            $html = '<div>Time date selected is less than present time</div><br><div style="text-align: center;">       
                            <div class="col-sm-12" id="BtnFooter">
                                <button type="button" id="ModalbtnCancleForm" data-dismiss="modal" class="btn btn-default">Cancel</button>
                            </div>
                        </div>';
                        echo $html;
                    }
                    
                    break;
                case 'view':
                    $data = $input['dt'];
                    $ID = $data[9];
                    $get = $this->m_master->caribasedprimary('db_reservation.t_booking','ID',$ID);
                    // cek ApproveAccess
                        $ApproveAccess = function($getRoom,$get){
                            // get Category Room to approver
                                $ApproveAccess = 0;
                                $ID_group_user = $this->session->userdata('ID_group_user');
                                $getPolicy = $this->m_master->caribasedprimary('db_reservation.cfg_policy','ID_group_user',$ID_group_user);
                                $CategoryRoom = $getPolicy[0]['CategoryRoom'];
                                $CategoryRoom = json_decode($CategoryRoom);
                                $CategoryRoomByRoom = $getRoom[0]['ID_CategoryRoom'];
                                $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
                                // find access
                                    $find = 0;
                                        for ($l=0; $l < count($CategoryRoom); $l++) { 
                                            if ($CategoryRoomByRoom == $CategoryRoom[$l]) {
                                                $find++;    
                                                break;
                                            }
                                        }

                                        if ($find == 1) {
                                            // get status 
                                            $Status1 = $get[0]['Status1'];
                                            if ($Status1 == 0) {
                                               // find approver1
                                                   $Approver1 = $getDataCategoryRoom[0]['Approver1'];
                                                   $Approver1 = json_decode($Approver1);
                                                   $NIP = $this->session->userdata('NIP');
                                                   for ($l=0; $l < count($Approver1); $l++) { 
                                                       if ($NIP == $Approver1[$l]) {
                                                           $find++;    
                                                           break;
                                                       }
                                                   }
                                            }
                                            else
                                            {
                                                $find = $find + 2;  
                                            }
                                        }

                                        if ($find == 3) {
                                           // find approver2
                                               $Approver2 = $getDataCategoryRoom[0]['Approver2'];
                                               $Approver2 = json_decode($Approver2);
                                               $DivisionID = $this->session->userdata('PositionMain');
                                               $DivisionID = $DivisionID['IDDivision'];
                                               $Status = $get[0]['Status'];
                                               if ($Status == 0) {
                                                   for ($l=0; $l < count($Approver2); $l++) { 
                                                       if ($DivisionID == $Approver2[$l]) {
                                                           $find++;    
                                                           break;
                                                       }
                                                   }
                                               }
                                               {
                                                $find = $find + 2;
                                               }
                                               
                                        }
                            $ApproveAccess = $find;
                            return $ApproveAccess;            
                        };

                    $DivisionID = $this->session->userdata('PositionMain');
                    $DivisionID = $DivisionID['IDDivision'];    
                    $this->data['ApproveAccess'] = $ApproveAccess($getRoom,$get);
                    $this->data['DivisionID'] = $DivisionID;


                    $Start = $data[1];
                    $End = $data[2];
                    $this->data['End'] = date("h:i a", strtotime($End));
                    $this->data['Start'] = date("h:i a", strtotime($Start));
                    $this->data['Agenda'] = $data[5];
                    $this->data['User'] = $data[0];
                    $this->data['ParticipantQty'] = $get[0]['ParticipantQty'];
                    // get data Equipment Additional
                    $Name_equipment_add = '-';
                    $keq_add = $get[0]['ID_equipment_add'];
                    if ($keq_add != '' && $keq_add != NULL) {
                        $keq_add = explode(",",$keq_add);
                    }
                    $e_div = array();
                    if (is_array($keq_add)) {
                        // save data t_booking_eq_additional
                        $Name_equipment_add = '<ul>';
                        $xx = $keq_add;
                        $ID_t_booking = $get[0]['ID'];
                        for ($i=0; $i < count($xx); $i++) { 
                            $gett_booking_eq_additional = $this->m_reservation->gett_booking_eq_additional($xx[$i],$ID_t_booking);
                            if ($gett_booking_eq_additional[0]['Status'] == 0) {
                                $Status_eq_additional = '{Not Confirm}';
                            }
                            elseif ($gett_booking_eq_additional[0]['Status'] == 1) {
                                $Status_eq_additional = '{Confirm}';
                            }
                            else
                            {
                                $Status_eq_additional = '{Reject}';
                            }
                            $Qty = $gett_booking_eq_additional[0]['Qty'];
                            $ID_equipment_additional = $gett_booking_eq_additional[0]['ID_equipment_additional'];
                            $get_eq_add = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
                            $OwnerID = $get_eq_add[0]['Owner'];
                            $getX = $this->m_master->caribasedprimary('db_employees.division','ID',$OwnerID);
                            $e_div[] = $getX[0]['Email'];
                            $Owner = $getX[0]['Division'];
                            $ID_m_equipment = $get_eq_add[0]['ID_m_equipment'];
                            $ge_eq_m = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                            $Name_equipment_add .= '<li>'.$ge_eq_m[0]['Equipment'].' by '.$Owner.'['.$Qty.']'.$Status_eq_additional.'</li>';

                        }
                        $Name_equipment_add .= '</ul>';
                    }
                    $this->data['Name_equipment_add'] = $Name_equipment_add;

                    // get personel
                    $ID_add_personel = '-';
                    $Name_add_personel = '-';
                    if ($get[0]['ID_add_personel'] != '' || $get[0]['ID_add_personel'] != null) {
                        $Name_add_personel = $get[0]['ID_add_personel'];
                    }
                    $this->data['Name_add_personel'] = $Name_add_personel;

                    
                    if ($get[0]['Req_layout'] != '' || $get[0]['Req_layout'] != null) {
                         $Req_layout = '<a href="'.base_url("fileGetAny/vreservation-".$get[0]['Req_layout']."").'" target="_blank"></i>Layout</a>';
                    }
                    else
                    {
                        $Req_layout = '<a href="'.base_url("fileGetAny/vreservation-".$file).'" target="_blank"></i>Default Layout</a>';
                    }
                    
                    $MarkomSupport = '<label>No</Label>';
                    $mks = $get[0]['MarcommSupport'];
                    if ($mks != '' && $mks != NULL ) {
                        $mks = explode(",", $mks); 
                    }
                    // $MarkomEmail ='';
                    if (is_array($mks)) {
                        $xx = $mks;
                        $MarkomSupport ='<ul>';
                        for ($i=0; $i < count($xx); $i++) { 
                            if(strpos($xx[$i], 'Note') === false) {
                                $g_markom = $this->m_reservation->g_markom($xx[$i],$get[0]['ID']);
                                if ($g_markom[0]['StatusMarkom'] == 0) {
                                    $Status_markom = '{Not Confirm}';
                                }
                                elseif ($g_markom[0]['StatusMarkom'] == 1) {
                                    $Status_markom = '{Confirm}';
                                }
                                else
                                {
                                    $Status_markom = '{Reject}';
                                }
                                $MarkomSupport .='<li>'.$g_markom[0]['Name'].$Status_markom.'</li>';
                            }
                            else
                            {
                                $MarkomSupport .='<li>'.nl2br($xx[$i]).'</li>';
                            }
                        }
                        $MarkomSupport .= '</ul>';
                    }

                    $KetAdditional = $get[0]['KetAdditional'];
                    $KetAdditional = json_decode($KetAdditional);
                    $files_invitation = $get[0]['Invitation'];
                    $Email_invitation = $this->m_reservation->Email_invitation($files_invitation);
                    $this->data['Email_invitation'] = $Email_invitation;
                    $this->data['KetAdditional'] = $KetAdditional;
                    $this->data['MarkomSupport'] = $MarkomSupport;
                    $this->data['Req_layout'] = $Req_layout;
                    $this->data['ID'] = $get[0]['ID'];
                    // print_r($this->data);die();
                    echo $this->load->view($this->pathView.'modal_form_view',$this->data,true);
                    break;
                default:
                    # code...
                    break;
            }
        
    }

    public function getCountApprove()
    {
        //$getData= $this->m_reservation->getCountApprove();
        $getData= count($this->m_reservation->getDataT_booking(null,'',2));
        echo json_encode($getData);
    }

    public function vreservation_report_page()
    {
        $content = $this->load->view($this->pathView.'report/page','',true);
        $this->temp($content);
    }

    public function report($page)
    {
        $arr_result = array('html' => '','jsonPass' => '');
        $uri = $this->uri->segment(3);
        $content = $this->load->view($this->pathView.'report/'.$uri,'',true);
        $arr_result['html'] = $content;
        echo json_encode($arr_result);
    }

    public function datafeedback()
    {
        $requestData= $_REQUEST;
        // print_r($requestData);
        $s = 'select count(*) as total from db_reservation.t_booking where Status = 1';
        $query = $this->db->query($s)->result_array();
        $totalData = $query[0]['total'];

        $sql = 'select a.*,b.Name from db_reservation.t_booking as a join db_employees.employees as b on a.CreatedBy = b.NIP 
                where a.Status = 1 and (a.CreatedBy LIKE "'.$requestData['search']['value'].'%" or b.Name LIKE "%'.$requestData['search']['value'].'%"  or  a.Room LIKE "'.$requestData['search']['value'].'%" or a.Start LIKE "'.$requestData['search']['value'].'%" )
                order by a.FeedbackAt desc,a.Feedback desc,a.Start Desc LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';
        $query = $this->db->query($sql)->result_array();

        $data = array();
        $No = $requestData['start'] + 1;
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];
            $Detail = $this->m_reservation->getDataPass($row['ID']);
            $Detail = implode('@@', $Detail);
            $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['Start']);
            $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['End']);
            $StartNameDay = $Startdatetime->format('l');
            $EndNameDay = $Enddatetime->format('l');
            $Time = $query[$i]['Time'].' Minutes';
            $Reqdatetime = DateTime::createFromFormat('Y-m-d', $query[$i]['Req_date']);
            $ReqdateNameDay = $Reqdatetime->format('l');

            $nestedData[] = $No;
            $nestedData[] = $StartNameDay.', '.$query[$i]['Start'];
            $nestedData[] = $EndNameDay.', '.$query[$i]['End'];
            $nestedData[] = $query[$i]['Agenda'];
            $nestedData[] = $query[$i]['Room'];
            $nestedData[] = $query[$i]['Name'].'<br>'.$ReqdateNameDay.', '.$query[$i]['Req_date'];
            $nestedData[] = nl2br($query[$i]['Feedback']);
            $nestedData[] = $query[$i]['FeedbackAt'];
            $nestedData[] = $Detail;
            $data[] = $nestedData;
            $No++;
        }

        // print_r($data);

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function loadScheduleEquipment()
    {
        $input = $this->getInputToken();
        $rs = array();
        $date = $input['date'];
        $dateNext = date('Y-m-d', strtotime($date . ' +1 day'));
        $datePrev = date('Y-m-d', strtotime($date . ' -1 day'));
        // get time hours
            $arrHours = array();
            $endTime = '20';
            for ($i=7; $i < $endTime; $i++) { 
                // check len
                $a = $i;
                for ($j=0; $j < 2 - strlen($i); $j++) { 
                    $a = '0'.$a;
                }
                $d = $a.':30';
                $a = $a.':00';
                $arrHours[] = date("h:i a", strtotime($a));
                //$arrHours[] = date("h:i a", strtotime($d));
                if ($i != $endTime) {
                    $arrHours[] = date("h:i a", strtotime($d));
                }
            }

        // Get Data
            $dt = array();
            $sql = 'select a.ID as ID_add,a.*,b.*,c.Division from db_reservation.m_equipment_additional as a join db_reservation.m_equipment as b
                    on a.ID_m_equipment = b.ID join db_employees.division as c on a.Owner = c.ID';
            $G_eq = $this->db->query($sql, array())->result_array();
            for ($i=0; $i < count($G_eq); $i++) { 
                $ID = $G_eq[$i]['ID_add'];
                $Name = $G_eq[$i]['Equipment'];
                $Qty0 = $G_eq[$i]['Qty'];
                for ($j=0; $j < count($arrHours); $j++) { 
                    $st = $arrHours[$j]; 
                    $Start = date("Y-m-d H:i:s", strtotime($date.$st));
                    $k = $j+1;
                    if ($k == count($arrHours)) {
                        break;
                    }
                    $en = $arrHours[$k];
                    $End = date("Y-m-d H:i:s", strtotime($date.$en)); 
                    $Qty = $this->m_reservation->getQtyperDateTime($ID,$Qty0,$Start,$End,'c.Status = 1');
                    $temp = array(
                        'ID' => $ID,
                        'Name' => $Name.'['.$Qty0.']',
                        'Start' => date("h:i a", strtotime($Start)),
                        'End' => date("h:i a", strtotime($End)),
                        'Qty' => $Qty,
                        'Qty0' => $Qty0,
                    );
                    $dt[] = $temp;
                }
            }
        //    for ($i=0; $i < count($arrHours); $i= $i+2) {
        //         $st = $arrHours[$i]; 
        //         $Start = date("Y-m-d H:i:s", strtotime($date.$st));
        //         $j = $i+1;
        //         if ($j == count($arrHours)) {
        //             break;
        //         }
        //         $en = $arrHours[$j]; 
        //         $End = date("Y-m-d H:i:s", strtotime($date.$en));

        //         // add equipment
        //              $sql = 'select a.ID as ID_add,a.*,b.*,c.Division from db_reservation.m_equipment_additional as a join db_reservation.m_equipment as b
        // on a.ID_m_equipment = b.ID join db_employees.division as c on a.Owner = c.ID';
        //             $G_eq = $this->db->query($sql, array())->result_array();
        //             for ($k=0; $k < count($G_eq); $k++) { 
        //                 $ID = $G_eq[$k]['ID_add'];
        //                 $Qty0 = $G_eq[0]['Qty'];
        //                 $Qty = $this->m_reservation->getQtyperDateTime($ID,$Qty0,$Start,$End);
        //                 $temp = array(
        //                     'ID' => $ID,
        //                     'Name' => $G_eq[$k]['Equipment'],
        //                     'Start' => date("h:i a", strtotime($Start)),
        //                     'End' => date("h:i a", strtotime($End)),
        //                     'Qty' => $Qty,
        //                 );

        //                 $dt[] = $temp;

        //             }
        //    }

           // usort($dt, function($a, $b) {
           //     return $a['ID'] - $b['ID'];
           // });

        $rs['arrHours'] = $arrHours;
        $rs['data'] = $dt;        
        $rs['dateNext'] = $dateNext;        
        $rs['datePrev'] = $datePrev;        
        echo json_encode($rs);

    }

}
