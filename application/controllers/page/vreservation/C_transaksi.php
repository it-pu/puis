<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_transaksi extends Vreservation_Controler {

    // private $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_sendemail');
        $this->load->model('m_api');
        $this->load->library('JWT');
    }


    public function booking_create()
    {
        $content = $this->load->view($this->pathView.'transaksi/booking',$this->data,true);
        $this->temp($content);
    }

    public function uploadDokumenMultiple($filename,$ggFiles = 'fileDataMarkomm')
    {
        $path = './uploads/vreservation';
        // Count total files
        $countfiles = count($_FILES[$ggFiles ]['name']);
      
      $output = array();
      // Looping all files
      for($i=0;$i<$countfiles;$i++){
            $config = array();
            if(!empty($_FILES[$ggFiles ]['name'][$i])){
     
              // Define new $_FILES array - $_FILES['file']
              $_FILES['file']['name'] = $_FILES[$ggFiles]['name'][$i];
              $_FILES['file']['type'] = $_FILES[$ggFiles]['type'][$i];
              $_FILES['file']['tmp_name'] = $_FILES[$ggFiles]['tmp_name'][$i];
              $_FILES['file']['error'] = $_FILES[$ggFiles]['error'][$i];
              $_FILES['file']['size'] = $_FILES[$ggFiles]['size'][$i];

              // Set preference
              $config['upload_path'] = $path.'/';
              $config['allowed_types'] = '*';
              $config['overwrite'] = TRUE; 
              $no = $i + 1;
              $config['file_name'] = $filename.'_'.$no;

              $filenameUpload = $_FILES['file']['name'];
              $ext = pathinfo($filenameUpload, PATHINFO_EXTENSION);

              // $filenameNew = $filename.'_'.$no.'.pdf';
              $filenameNew = $filename.'_'.$no.'_'.mt_rand().'.'.$ext;
              // print_r($_FILES['file']['type']);

     
              //Load upload library
              $this->load->library('upload',$config); 
              $this->upload->initialize($config);
     
              // File upload
              if($this->upload->do_upload('file')){
                // Get data about the file
                $uploadData = $this->upload->data();
                $filePath = $uploadData['file_path'];
                $filename_uploaded = $uploadData['file_name'];
                // rename file
                $old = $filePath.'/'.$filename_uploaded;
                $new = $filePath.'/'.$filenameNew;

                rename($old, $new);

                $output[] = $filenameNew;
              }
            }
        }
        return $output;
    }

    public function add_save_transaksi()
    {
        $input = $this->getInputToken();
        $filename = '';
        if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
            if (array_key_exists('fileData', $_FILES)) {
                $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                $path = 'vreservation';
                $fileName = md5(mt_rand());
                $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin,$fileName,'fileData',$path,'string');
                $filename = $uploadNas;
            }
        }
        else
        {
           $uploadFile = $this->uploadFfile(mt_rand());
           if (is_array($uploadFile)) {
               $filename = $uploadFile['file_name'];
           } 
        }
       
        
        $filenamemarkomm = ''; 
        if (array_key_exists('fileDataMarkomm',$_FILES)) {
            // upload file markomm
            $uploadFile2 = $this->uploadDokumenMultiple('GraphicDesign');
            if (is_array($uploadFile2)) {
                $filenamemarkomm = implode(';', $uploadFile2);
            }
        }


        // attachment invitation
        $files_invitation = '';
        if (array_key_exists('files_invitation',$_FILES)) {
            // upload file markomm
            $uploadFile2 = $this->uploadDokumenMultiple('attachment','files_invitation');
            if (is_array($uploadFile2)) {
                $files_invitation = implode(';', $uploadFile2);
            }
        }

        $Start = date("Y-m-d H:i:s", strtotime($input['date'].$input['Start']));
        $End = date("Y-m-d H:i:s", strtotime($input['date'].$input['End']));
        $time = $this->m_master->countTimeQuery($End, $Start);
        $time = $time[0]['time'];
        $time = explode(':', $time);
        $time = ($time[0] * 60) + $time[1];
        $Colspan = $time / 30;
        $Colspan = (int)$Colspan;
        $a = $time % 30;
        if ($a > 0) {
            $Colspan++;
        }

        $ID_equipment_add = '';
        if (is_array($input['chk_e_additional'])) {
            $xx = $input['chk_e_additional'];
            $yy = array();
            for ($i=0; $i < count($xx); $i++) { 
                $yy[] = $xx[$i]->ID_equipment_add;
            }
            $ID_equipment_add = implode(',', $yy);
        }

        $ID_add_personel = $input['chk_person_support'];

        $chk_markom_support = '';
        if (is_array($input['chk_markom_support'])) {
            $s = $input['chk_markom_support'];
            $chk_markom_support = implode(',', $s);
        }

        $KetAdditional = $input['KetAdditional'];
        $chk_markom_support = $chk_markom_support; // for text area

        // check data bentrok dengan jam lain
        $chk = $this->m_reservation->checkBentrok($Start,$End,'',$input['Room']);
        if ($chk) {
            $dataSave = array(
                'Start' => $Start,
                'End' => $End,
                'Time' => $time,
                'Colspan' => $Colspan,
                'Agenda' => $input['Agenda'],
                'Room' => $input['Room'],
                'ID_equipment_add' => $ID_equipment_add,
                'ID_add_personel' => $ID_add_personel,
                'Req_date' => date('Y-m-d'),
                'CreatedBy' => $this->session->userdata('NIP'),
                'Req_layout' => $filename,
                'ParticipantQty' => $input['Participant'],
                'MarcommSupport' => $chk_markom_support,
                'KetAdditional' => json_encode($KetAdditional),
                'Invitation' => $files_invitation,
            );
            $this->db->insert('db_reservation.t_booking', $dataSave);
            $ID_t_booking = $this->db->insert_id();

            $Email_invitation = $this->m_reservation->Email_invitation($files_invitation);
            
            // insert into t_markom_support
            $MarkomEmail ='';
            if (is_array($input['chk_markom_support'])) {
                $xx = $input['chk_markom_support'];
                $MarkomEmail ='<li>Documentation<ul>';
                for ($i=0; $i < count($xx); $i++) { 
                    if(strpos($xx[$i], 'Note') === false) {
                        $data_save_mar = array(
                            'ID_t_booking' => $ID_t_booking,
                            'ID_m_markom_support' => $xx[$i],
                        );
                        $this->db->insert('db_reservation.t_markom_support', $data_save_mar);

                        $c = $this->m_master->caribasedprimary('db_reservation.m_markom_support','ID',$xx[$i]);
                        $MarkomEmail .='<li>'.$c[0]['Name'].'</li>';
                    }
                    else
                    {
                        $MarkomEmail .='<li>'.nl2br($xx[$i]).'</li>';
                    }
                }
                $MarkomEmail .= '</ul></li>';
                
            }

            if (is_array($input['chk_e_additional'])) {
                // save data t_booking_eq_additional
                $xx = $input['chk_e_additional'];
                $yy = array();
                for ($i=0; $i < count($xx); $i++) { 
                    $yy[] = array('ID_t_booking' =>$ID_t_booking,'ID_equipment_additional' => $xx[$i]->ID_equipment_add,'Qty' =>  $xx[$i]->Qty);
                }    
                $this->db->insert_batch('db_reservation.t_booking_eq_additional', $yy);
            }

            $Email_add_person = ($ID_add_personel == '') ? '' : '<li>Person Support : '.$ID_add_personel.'</li>';

            $KetAdditional_eq = '';
            $arr_to_eq_add = array();
            $arr_to_eq_add_ID_DIV = array();
            if (is_array($input['chk_e_additional'])) {
                // save data t_booking_eq_additional
                $KetAdditional_eq = '<br><br>*  Equipment Additional<ul>';
                $xx = $input['chk_e_additional'];
                $yy = array();
                for ($i=0; $i < count($xx); $i++) { 
                    $gett_booking_eq_additional = $this->m_reservation->gett_booking_eq_additional($xx[$i]->ID_equipment_add,$ID_t_booking);
                    $Qty = $gett_booking_eq_additional[0]['Qty'];
                    $ID_equipment_additional = $gett_booking_eq_additional[0]['ID_equipment_additional'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
                    $OwnerID = $get[0]['Owner'];
                    $getX = $this->m_master->caribasedprimary('db_employees.division','ID',$OwnerID);
                    if (!in_array($OwnerID, $arr_to_eq_add_ID_DIV)) {
                        $arr_to_eq_add_ID_DIV[] = $OwnerID;
                        $arr_to_eq_add[] = $getX[0]['Email'];
                    }
                    $Owner = $getX[0]['Division'];
                    $ID_m_equipment = $get[0]['ID_m_equipment'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                    $KetAdditional_eq .= '<li>'.$get[0]['Equipment'].' by '.$Owner.'['.$Qty.']</li>';

                }
                $KetAdditional_eq .= '</ul>';
            }

            $EmailKetAdditional = '';
            if ($KetAdditional != '') {
                foreach ($KetAdditional as $key => $value) {
                    if ($value != "" || $value != null) {
                        $EmailKetAdditional .= '<br>*   '.str_replace("_", " ", $key).' : '.$value;  
                        // $EmailKetAdditional .= '<br>*   '.$key.'('.$value.')';  
                    }  
                }
            }

            // Start define for email
                $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $Start);
                $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $End);
                $StartNameDay = $Startdatetime->format('l');
                $EndNameDay = $Enddatetime->format('l');

                    // email to approval 1
                        $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$input['Room']);
                        $CategoryRoomByRoom = $getRoom[0]['ID_CategoryRoom'];
                        $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
                        $Approver1 = $getDataCategoryRoom[0]['Approver1'];
                        $Approver1 = json_decode($Approver1);
                        // get user type
                        $ID_group_user = $this->session->userdata('ID_group_user');
                        $dataApprover = array();
                        for ($l=0; $l < count($Approver1); $l++) {
                            // find by ID_group_user
                                if ($ID_group_user == $Approver1[$l]->UserType) {
                                    // get TypeApprover
                                    $TypeApprover = $Approver1[$l]->TypeApprover;
                                    switch ($TypeApprover) {
                                        case 'Position':
                                            // get Division to access position approval
                                                $PositionMain = $this->session->userdata('PositionMain');
                                                $IDDivision = $PositionMain['IDDivision'];
                                                $IDPositionApprover = $Approver1[$l]->Approver; 
                                                if ($IDDivision == 15 || $IDDivision == 33) { // if prodi
                                                    $sqlgg = 'select * from db_academic.program_study where AdminID = ? or KaprodiID = ? or Laboran = ?';
                                                    $gg=$this->db->query($sqlgg, array($this->session->userdata('NIP'),$this->session->userdata('NIP'),$this->session->userdata('NIP')))->result_array();
                                                    if (count($gg) > 0) {
                                                        for ($k=0; $k < count($gg); $k++) { 
                                                            $Kaprodi = $gg[$k]['KaprodiID'];
                                                            $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Kaprodi);
                                                            for ($m=0; $m < count($getApprover1); $m++) { 
                                                                if ($getApprover1[$m]['StatusEmployeeID'] > 0) {
                                                                     $dataApprover[] = array('Email' => $getApprover1[$m]['EmailPU'],'Name' => $getApprover1[$m]['Name'],'Code' => $Kaprodi,'TypeApprover' => $TypeApprover);
                                                                }
                                                            }
                                                        }
                                                        
                                                    }
                                                }
                                                elseif ($IDDivision == 34 || $IDDivision == 33) {
                                                    $sqlgg = 'select * from db_academic.faculty where AdminID = ? or NIP = ? or Laboran = ?';
                                                    $gg=$this->db->query($sqlgg, array($this->session->userdata('NIP'),$this->session->userdata('NIP'),$this->session->userdata('NIP')))->result_array();
                                                    if (count($gg) > 0) {
                                                        for ($k=0; $k < count($gg); $k++) { 
                                                            $Dekan = $gg[$k]['NIP'];
                                                            $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Dekan);
                                                            for ($m=0; $m < count($getApprover1); $m++) { 
                                                                if ($getApprover1[$m]['StatusEmployeeID'] > 0) {
                                                                     $dataApprover[] = array('Email' => $getApprover1[$m]['EmailPU'],'Name' => $getApprover1[$m]['Name'],'Code' => $Dekan,'TypeApprover' => $TypeApprover);
                                                                }
                                                            }
                                                        }
                                                        
                                                    }
                                                }
                                                elseif ($IDDivision == 14) { // lecturer
                                                    $EM = $this->m_master->caribasedprimary('db_employees.employees','NIP',$this->session->userdata('NIP'));
                                                    $ProdiID = $EM[0]['ProdiID'];
                                                    $G_Prodi = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                                                    $Kaprodi = $G_Prodi[0]['KaprodiID'];
                                                    $G_Kaprodi = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Kaprodi);
                                                    $dataApprover[0] = array('Email' => $G_Kaprodi[0]['EmailPU'],'Name' => $G_Kaprodi[0]['Name'],'Code' => $G_Kaprodi[0]['NIP'],'TypeApprover' => $TypeApprover);
                                                }
                                                else
                                                {
                                                    // find by division and position
                                                    $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionMain',$IDDivision.'.'.$IDPositionApprover);
                                                    $arr = array();
                                                    for ($k=0; $k < count($getApprover1); $k++) {
                                                        if ($getApprover1[$k]['StatusEmployeeID'] > 0) {
                                                            $arr[] =  $getApprover1[$k];
                                                        } 
                                                       
                                                    }

                                                    $getApprover1 = $arr;
                                                    
                                                    if (count($getApprover1) == 0) {
                                                        $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionOther1',$IDDivision.'.'.$IDPositionApprover);
                                                        if (count($getApprover1) == 0) {
                                                            $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionOther2',$IDDivision.'.'.$IDPositionApprover);
                                                            if (count($getApprover1) == 0) {
                                                                $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionOther3',$IDDivision.'.'.$IDPositionApprover);
                                                            }
                                                        }
                                                    }
                                                    for ($k=0; $k < count($getApprover1); $k++) {
                                                        if ($getApprover1[$k]['StatusEmployeeID'] > 0) {
                                                             $dataApprover[] = array('Email' => $getApprover1[$k]['EmailPU'],'Name' => $getApprover1[$k]['Name'],'Code' => $getApprover1[$k]['NIP'],'TypeApprover' => $TypeApprover);
                                                        } 
                                                       
                                                    }
                                                }
                                            break;
                                        
                                        case 'Division':
                                            $getApprover1 = $this->m_master->caribasedprimary('db_employees.division','ID',$Approver1[$l]->Approver);
                                            for ($k=0; $k < count($getApprover1); $k++) { 
                                               $dataApprover[] = array('Email' => $getApprover1[$k]['Email'],'Name' => $getApprover1[$k]['Division'],'Code' => $getApprover1[$k]['ID'],'TypeApprover' => $TypeApprover);
                                            }
                                            break;

                                        case 'Employees':
                                            $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Approver1[$l]->Approver);
                                            for ($k=0; $k < count($getApprover1); $k++) { 
                                               $dataApprover[] = array('Email' => $getApprover1[$k]['EmailPU'],'Name' => $getApprover1[$k]['Name'],'Code' => $getApprover1[$k]['NIP'],'TypeApprover' => $TypeApprover);
                                            }
                                            break;    
                                    }
                                }
                        } // end loop for
                        
                        // print_r($dataApprover);die();
                        if($_SERVER['SERVER_NAME']!='localhost') {
                            // send email
                            $EmailPU = '';
                            $NameEmail = '';
                            $Code = '';
                            if (count($dataApprover) > 0) {
                                $temp = array();
                                $temp2 = array();
                                $temp3 = array();
                                // for ($k=0; $k < count($dataApprover); $k++) { 
                                //     $EM = $dataApprover[$k]['Email'];
                                //     $NM = $dataApprover[$k]['Name'];
                                //     $Code = $dataApprover[$k]['Code'];
                                //     $temp[] = $EM;
                                //     $temp2[] = $NM;
                                //     $temp3[] = $Code;
                                // }
                                $EM = $dataApprover[0]['Email'];
                                $NM = $dataApprover[0]['Name'];
                                $Code = $dataApprover[0]['Code'];
                                $temp[] = $EM;
                                $temp2[] = $NM;
                                $temp3[] = $Code;
                                 
                                $EmailPU = implode(",", $temp);
                                $NameEmail = implode(" / ", $temp2);
                                $Code = implode(";", $temp3);
                            }
                            
                            if ($EmailPU != '' || $EmailPU != null) {
                                $token = array(
                                    'EmailPU' => $EmailPU,
                                    'Code' => $Code,
                                    'ID_t_booking' => $ID_t_booking,
                                    'approvalNo' => 1,
                                    'Email_add_person' => $Email_add_person,
                                    'MarkomEmail' => $MarkomEmail,
                                    'EmailKetAdditional' => $EmailKetAdditional,
                                    'KetAdditional_eq' => $KetAdditional_eq,
                                );
                                $token = $this->jwt->encode($token,'UAP)(*');
                                $Email = $EmailPU;
                                $text = 'Dear Mr/Mrs '.$NameEmail.',<br><br>
                                            Please help to approve Venue Reservation requested by '.$this->session->userdata('Name').',<br><br>
                                            Details Schedule : <br><ul>
                                            <li>Start  : '.$StartNameDay.', '.$Start.'</li>
                                            <li>End  : '.$EndNameDay.', '.$End.'</li>
                                            <li>Room  : '.$input['Room'].'</li>
                                            <li>Agenda  : '.$input['Agenda'].'</li>
                                            '.$Email_add_person.'
                                            '.$MarkomEmail.'
                                            </ul>
                                            '.$EmailKetAdditional.'
                                            '.$KetAdditional_eq.
                                            $Email_invitation.'</br>
                                            <table width="100" cellspacing="0" cellpadding="12" border="0">
                                                <tbody>
                                                <tr>
                                                    <td bgcolor="#51a351" align="center">
                                                        <a href="'.url_pas.'approve_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >Approve</a>
                                                    </td>
                                                    <td align="center">
                                                       -
                                                    </td>
                                                    <td bgcolor="#de4341" align="center">
                                                        <a href="'.url_pas.'cancel_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #de4341;" target="_blank" >Reject</a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        ';        
                                $to = $Email;
                                $subject = "Podomoro University Venue Reservation Approval 1";
                                $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);

                                // equipment additional
                                for ($zn=0; $zn < count($arr_to_eq_add_ID_DIV); $zn++) { 
                                    $token = array(
                                        'EmailPU' => $EmailPU,
                                        'ID_t_booking' => $ID_t_booking,
                                        'DivisionID' => $arr_to_eq_add_ID_DIV[$zn],
                                    );
                                    $token = $this->jwt->encode($token,'UAP)(*');
                                    $Email = $arr_to_eq_add[$zn];
                                    $text = 'Dear Team,<br><br>
                                                Please help to Confirm Equipment Additional from Venue Reservation requested by '.$this->session->userdata('Name').',<br><br>
                                                Details Schedule : <br><ul>
                                                <li>Start  : '.$StartNameDay.', '.$Start.'</li>
                                                <li>End  : '.$EndNameDay.', '.$End.'</li>
                                                <li>Room  : '.$input['Room'].'</li>
                                                <li>Agenda  : '.$input['Agenda'].'</li>
                                                '.$Email_add_person.'
                                                '.$MarkomEmail.'
                                                </ul>
                                                '.$EmailKetAdditional.'
                                                '.$KetAdditional_eq.
                                                $Email_invitation.'</br>
                                                <table width="100" cellspacing="0" cellpadding="12" border="0">
                                                    <tbody>
                                                    <tr>
                                                        <td bgcolor="#51a351" align="center">
                                                            <a href="'.url_pas.'view_eq_additional/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >View</a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            ';        
                                    $to = $Email;
                                    $subject = "Podomoro University Venue Reservation Equipment Additional";
                                    $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                                }
                                // equipment additional
                            }
                        }
                        else
                        {
                            // send email
                            $EmailPU = '';
                            $NameEmail = '';
                            $Code = '';
                            if (count($dataApprover) > 0) {
                                $temp = array();
                                $temp2 = array();
                                $temp3 = array();
                                // for ($k=0; $k < count($dataApprover); $k++) { 
                                //     $EM = $dataApprover[$k]['Email'];
                                //     $NM = $dataApprover[$k]['Name'];
                                //     $Code = $dataApprover[$k]['Code'];
                                //     $temp[] = $EM;
                                //     $temp2[] = $NM;
                                //     $temp3[] = $Code;
                                // }
                                $EM = $dataApprover[0]['Email'];
                                $NM = $dataApprover[0]['Name'];
                                $Code = $dataApprover[0]['Code'];
                                $temp[] = $EM;
                                $temp2[] = $NM;
                                $temp3[] = $Code;
                                 
                                $EmailPU = implode(",", $temp);
                                $NameEmail = implode(" / ", $temp2);
                                $Code = implode(";", $temp3);
                            }
                            
                            if ($EmailPU != '' || $EmailPU != null) {
                                $token = array(
                                    'EmailPU' => $EmailPU,
                                    'Code' => $Code,
                                    'ID_t_booking' => $ID_t_booking,
                                    'approvalNo' => 1,
                                    'Email_add_person' => $Email_add_person,
                                    'MarkomEmail' => $MarkomEmail,
                                    'EmailKetAdditional' => $EmailKetAdditional,
                                    'KetAdditional_eq' => $KetAdditional_eq,
                                );
                                $token = $this->jwt->encode($token,'UAP)(*');
                                $Email = 'alhadi.rahman@podomorouniversity.ac.id';
                                $text = 'Dear Mr/Mrs '.$NameEmail.',<br><br>
                                            Please help to approve Venue Reservation requested by '.$this->session->userdata('Name').',<br><br>
                                            Details Schedule : <br><ul>
                                            <li>Start  : '.$StartNameDay.', '.$Start.'</li>
                                            <li>End  : '.$EndNameDay.', '.$End.'</li>
                                            <li>Room  : '.$input['Room'].'</li>
                                            <li>Agenda  : '.$input['Agenda'].'</li>
                                            '.$Email_add_person.'
                                            '.$MarkomEmail.'
                                            </ul>
                                            '.$EmailKetAdditional.'
                                            '.$KetAdditional_eq.
                                            $Email_invitation.'</br>
                                            <table width="200" cellspacing="0" cellpadding="12" border="0">
                                                <tbody>
                                                <tr>
                                                    <td bgcolor="#51a351" align="center">
                                                        <a href="'.url_pas.'approve_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >Approve</a>
                                                    </td>
                                                    <td align="center">
                                                      -
                                                    </td>
                                                    <td bgcolor="#de4341" align="center">
                                                        <a href="'.url_pas.'cancel_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #de4341;" target="_blank" >Reject</a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        ';        
                                $to = $Email;
                                $subject = "Podomoro University Venue Reservation Approval 1";
                                $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);

                                // equipment additional
                                 for ($zn=0; $zn < count($arr_to_eq_add_ID_DIV); $zn++) { 
                                    $token = array(
                                        'EmailPU' => $EmailPU,
                                        'ID_t_booking' => $ID_t_booking,
                                        'DivisionID' => $arr_to_eq_add_ID_DIV[$zn],
                                    );
                                    $token = $this->jwt->encode($token,'UAP)(*');
                                    $Email = $arr_to_eq_add[$zn];
                                    $text = 'Dear Team,<br><br>
                                                Please help to Confirm Equipment Additional from Venue Reservation requested by '.$this->session->userdata('Name').',<br><br>
                                                Details Schedule : <br><ul>
                                                <li>Start  : '.$StartNameDay.', '.$Start.'</li>
                                                <li>End  : '.$EndNameDay.', '.$End.'</li>
                                                <li>Room  : '.$input['Room'].'</li>
                                                <li>Agenda  : '.$input['Agenda'].'</li>
                                                '.$Email_add_person.'
                                                '.$MarkomEmail.'
                                                </ul>
                                                '.$EmailKetAdditional.'
                                                '.$KetAdditional_eq.
                                                $Email_invitation.'</br>
                                                <table width="100" cellspacing="0" cellpadding="12" border="0">
                                                    <tbody>
                                                    <tr>
                                                        <td bgcolor="#51a351" align="center">
                                                            <a href="'.url_pas.'view_eq_additional/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >View</a>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            ';        
                                    $to = $Email;
                                    $subject = "Podomoro University Venue Reservation Equipment Additional";
                                    $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                                }
                                // equipment additional

                            }
                        }

                        if ($MarkomEmail != '') {
                           $MarkomSupport = '';
                           if($_SERVER['SERVER_NAME']!='localhost') {
                                 $getDataDB = $this->m_master->caribasedprimary('db_reservation.email_to','Ownership','Markom');
                                 $Email = $getDataDB[0]['Email'];
                                 $token = array(
                                     'EmailPU' => $Email,
                                     'Code' => 'Auth-Markom',
                                     'ID_t_booking' => $ID_t_booking,
                                 );
                                 $token = $this->jwt->encode($token,'UAP)(*');
                                 $text = 'Dear Team,<br><br>
                                             Venue Reservation request by '.$this->session->userdata('Name').',<br><br>
                                             Details Schedule : <br><ul>
                                             <li>Start                       : '.$StartNameDay.', '.$Start.'</li>
                                             <li>End                         : '.$EndNameDay.', '.$End.'</li>
                                             <li>Room                        : '.$input['Room'].'</li>
                                             <li>Agenda                      : '.$input['Agenda'].'</li>
                                             '.$MarkomEmail.'
                                             '.$Email_add_person.'
                                             '.$MarkomSupport.'
                                             </ul>
                                             '.$EmailKetAdditional.'
                                             '.$KetAdditional_eq.
                                             $Email_invitation.'</br>
                                               <table width="100" cellspacing="0" cellpadding="12" border="0">
                                                   <tbody>
                                                   <tr>
                                                       <td bgcolor="#51a351" align="center">
                                                           <a href="'.url_pas.'view_venue_markom/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >View</a>
                                                       </td>
                                                   </tr>
                                                   </tbody>
                                               </table>
                                         ';        
                                 $to = $Email;
                                 $subject = "Podomoro University Venue Reservation Marcomm Support";
                                 $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                           }
                           else{
                               $Email = 'alhadi.rahman@podomorouniversity.ac.id';
                               $token = array(
                                   'EmailPU' => $Email,
                                   'Code' => 'Auth-Markom',
                                   'ID_t_booking' => $ID_t_booking,
                               );
                               $token = $this->jwt->encode($token,'UAP)(*');
                               $text = 'Dear Team,<br><br>
                                           Venue Reservation request by '.$this->session->userdata('Name').',<br><br>
                                           Details Schedule : <br><ul>
                                           <li>Start                       : '.$StartNameDay.', '.$Start.'</li>
                                           <li>End                         : '.$EndNameDay.', '.$End.'</li>
                                           <li>Room                        : '.$input['Room'].'</li>
                                           <li>Agenda                      : '.$input['Agenda'].'</li>
                                           '.$MarkomEmail.'
                                           '.$Email_add_person.'
                                           '.$MarkomSupport.'
                                           </ul>
                                           '.$EmailKetAdditional.'
                                            '.$KetAdditional_eq.
                                            $Email_invitation.'</br>
                                           <table width="100" cellspacing="0" cellpadding="12" border="0">
                                               <tbody>
                                               <tr>
                                                   <td bgcolor="#51a351" align="center">
                                                       <a href="'.url_pas.'view_venue_markom/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >View</a>
                                                   </td>
                                               </tr>
                                               </tbody>
                                           </table>
                                       ';        
                               $to = $Email;
                               $subject = "Podomoro University Venue Reservation Marcomm Support";
                               $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                           } 
                        }
                // end
            // end start define email        
            echo json_encode(array('msg' => 'The Proses Finish','status' => 1));
        }
        else
        {
            echo json_encode(array('msg' => 'Your schedule is Conflict Please check.','status' => 0));
        }
    }

    // mt_rand()

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

    public function vr_request()
    {
        $content = $this->load->view($this->pathView.'transaksi/page_approve','',true);
        $this->temp($content);
    }

    public function json_list_approve()
    {
        $getData = $this->m_reservation->getDataT_booking();
        echo json_encode($getData);
    }

    public function json_list_booking_by_user()
    {
        $getData = $this->m_reservation->getDataT_bookingByUser(null,'',2);
        echo json_encode($getData);
    }

    public function json_list_booking()
    {
        $getData = $this->m_reservation->getDataT_booking(null,'',2);
        echo json_encode($getData);
    }

    public function approve_submit()
    {
        $msg = '';
        $input = $this->getInputToken();
        $ID = $input['ID_tbl'];
        //$ListDelEquipment = $input['ListDelEquipment'];
        $ListDelMarkom = $input['ListDelMarkom'];
        $get = $this->m_master->caribasedprimary('db_reservation.t_booking','ID',$ID);
        $EmailAdd = '';
        $arr_add = array();
        $approveaccess = $input['approveaccess'];
        $arr_status = array();
        $ApprovalWr = '';
        $ID_t_booking = $ID;
        
        switch ($approveaccess) {
            case 0:
                $arr_status = array('MarcommStatus' => 2);
                $ApprovalWr = ' as Marcomm Division';
                break;
            case 2:
            case 1:
                $arr_status = array('Status1' => 1,'ApprovedAt1' => date('Y-m-d H:i:s'),'ApprovedBy1' => $this->session->userdata('NIP') );
                $ApprovalWr = ' as Approval 1';

                // find approval 1 sama dengan approval 2
                $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$get[0]['Room']);
                $CategoryRoomByRoom = $getRoom[0]['ID_CategoryRoom'];
                $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
                $NIP = $this->session->userdata('NIP');
                // find approver 2
                    $find = 0;
                    $Approver2 = $getDataCategoryRoom[0]['Approver2'];
                    $Approver2 = json_decode($Approver2);
                    for ($zz=0; $zz < count($Approver2); $zz++) { 
                        $rdata = $Approver2[$zz];
                        $TypeApprover = $rdata->TypeApprover;
                        switch ($TypeApprover) {
                            case 'Division':
                                $DivisionID = $this->session->userdata('PositionMain');
                                $DivisionID = $DivisionID['IDDivision'];
                                if ($DivisionID == $rdata->Approver) {
                                    $find++;    
                                    break;
                                }
                                break;
                            case 'Employees':
                                $NIP = $this->session->userdata('NIP');
                                if ($NIP == $rdata->Approver) {
                                    $find++;    
                                    break;
                                }
                                break;

                        }
                    }
             
                if ($find == 1) {
                    $arr_status = $arr_status + array('Status' => 1,'ApprovedAt' => date('Y-m-d H:i:s'),'ApprovedBy' => $this->session->userdata('NIP') );
                    $ApprovalWr = ' as Approval 1 & Approval 2';
                    $approveaccess = 4;
                }
                break;
            case 3:
                $PositionMain = $this->session->userdata('PositionMain');
                $IDDivision = $PositionMain['IDDivision'];
                if ($IDDivision == 12) {
                    $arr_status = array('Status' => 1,'ApprovedAt' => date('Y-m-d H:i:s'),'ApprovedBy' => $this->session->userdata('NIP') );
                    $ApprovalWr = ' as Approval 2';
                }
                
                break;
            case 4:
                $arr_status = array('Status' => 1,'ApprovedAt' => date('Y-m-d H:i:s'),'ApprovedBy' => $this->session->userdata('NIP') );
                $ApprovalWr = ' as Approval 2';
                break;
        }

        $KetAdditional = json_decode($get[0]['KetAdditional']);    
        $EmailKetAdditional = '<br>';
        if ($KetAdditional != '') {
            if ( is_array($KetAdditional) && count($KetAdditional) > 0) {
                foreach ($KetAdditional as $key => $value) {
                    if ($value != "" || $value != null) {
                        // $EmailKetAdditional .= '<br>*   '.$key.' : '.$value;
                        $EmailKetAdditional .= '<br>*   '.str_replace("_", " ", $key).' : '.$value;    
                    }  
                }
            }
            
        }

        $files_invitation = $get[0]['Invitation'];
        $Email_invitation = $this->m_reservation->Email_invitation($files_invitation);

        // check approve bentrok
        $Start = $get[0]['Start'];$End = $get[0]['End'];$chk_e_multiple = '';$Room = $get[0]['Room'];
        $chk = $this->m_reservation->checkBentrok($Start,$End,$chk_e_multiple,$Room,$ID);
        // $chk =true;
        if ($chk) {
            $dataSave = $arr_status;
            $dataSave = $dataSave + $arr_add;
            // print_r($dataSave);die();
            $this->db->where('ID',$ID);
            $this->db->update('db_reservation.t_booking', $dataSave);

            // send email approve to user
            $getUser = $this->m_master->caribasedprimary('db_employees.employees','NIP',$get[0]['CreatedBy']);
            $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['Start']);
            $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['End']);
            $StartNameDay = $Startdatetime->format('l');
            $EndNameDay = $Enddatetime->format('l');

            $mks = $get[0]['MarcommSupport'];
            if ($mks != '' && $mks != NULL ) {
                $mks = explode(",", $mks); 
            }
            
            $MarkomEmail ='';
            if (is_array($mks)) {
                $xx = $mks;
                $dzx = array();
                for ($xz=0; $xz < count($xx); $xz++) { 
                    $pos1 = stripos($xx[$xz], 'Note');
                    $exitLoop = false;
                    if ($pos1 !== false) {
                        $temp = array();
                        for ($ixx = $xz; $ixx < count($xx); $ixx++) { 
                            $temp[] = $xx[$ixx];
                        }
                        $dzx[] = implode(',', $temp);
                        $exitLoop = true;
                    }
                    else
                    {
                        $dzx[] = $xx[$xz];
                    }

                    if ($exitLoop) {
                        break;
                    }
                }

                $xx = $dzx;


                $MarkomEmail ='<li>Documentation<ul>';
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
                        $MarkomEmail .='<li>'.$g_markom[0]['Name'].$Status_markom.'</li>';
                    }
                    else
                    {
                        $MarkomEmail .='<li>'.nl2br($xx[$i]).'</li>';
                    }
                }
                $MarkomEmail .= '</ul></li>';
            }

            $Email_add_person = ($get[0]['ID_add_personel'] == '') ? '' : '<li>Person Support : '.$get[0]['ID_add_personel'].'</li>';

            $KetAdditional_eq = '';
            $keq_add = $get[0]['ID_equipment_add'];
            if ($keq_add != '' && $keq_add != NULL) {
                $keq_add = explode(",",$keq_add);
            }
            $e_div = array();
            if (is_array($keq_add)) {
                // save data t_booking_eq_additional
                $KetAdditional_eq = '<br><br>*  Equipment Additional<ul>';
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
                    $get_m_eq = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                    $KetAdditional_eq .= '<li>'.$get_m_eq[0]['Equipment'].' by '.$Owner.'['.$Qty.']'.$Status_eq_additional.'</li>';

                }
                $KetAdditional_eq .= '</ul>';
            }
            
            if ($approveaccess == 4) {
                // send by Ical
                $Email = $getUser[0]['EmailPU'];
                $StartIcal = date("Ymd", strtotime($get[0]['Start']));
                $EndIcal = date("Ymd", strtotime($get[0]['End']));
                $place  = $get[0]['Room'];
                //sent for reminder
                   $to = $Email;
                   $StartTimeIcal = '073000';
                   $EndTimeIcal = '083000';
                   $subject = "Reminder Venue Reservation";
                   $text = 'Dear Mr/Mrs '.$getUser[0]['Name'].',<br><br>
                               Reminder Venue Reservation,<br><br>
                               Details Schedule : <br><ul>
                               <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                               <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                               <li>Agenda  : '.$get[0]['Agenda'].'</li>
                               <li>Room  : '.$get[0]['Room'].'</li>
                               '.$Email_add_person.'
                               '.$MarkomEmail.'
                               </ul>
                               '.$EmailKetAdditional.'
                               '.$KetAdditional_eq.
                               $Email_invitation.'</br>
                           ';
                   $sendEmail = $this->m_sendemail->sendEmailIcal($to,$subject,$text, $place,$StartIcal,$StartTimeIcal,$EndIcal,$EndTimeIcal);

                $text = 'Dear Mr/Mrs '.$getUser[0]['Name'].',<br><br>
                            Your Venue Reservation approved by '.$this->session->userdata('Name').$ApprovalWr.',<br><br>
                            Details Schedule : <br><ul>
                            <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                            <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                            <li>Room  : '.$get[0]['Room'].'</li>
                            <li>Agenda  : '.$get[0]['Agenda'].'</li>
                            </ul>
                            '.$Email_add_person.'
                            '.$MarkomEmail.'
                            </ul>
                            '.$EmailKetAdditional.'
                            '.$KetAdditional_eq.
                            $Email_invitation.'</br>
                        ';        
                $to = $Email;
                $subject = "Podomoro University Venue Reservation Approved";
                $StartTimeIcal = date("His", strtotime($get[0]['Start']));
                $EndTimeIcal = date("His", strtotime($get[0]['End']));
                // print_r($EndTimeIcal);die();
                $sendEmail = $this->m_sendemail->sendEmailIcal($to,$subject,$text, $place,$StartIcal,$StartTimeIcal,$EndIcal,$EndTimeIcal);

                // // markom & equipment
                    if ($MarkomEmail != '') {
                        $e_markom = $this->m_master->caribasedprimary('db_employees.division','ID',17);
                        $e_markom = $e_markom[0]['Email'];
                        $Email = $e_markom;
                        $to = $Email;

                        // reminder
                        $StartTimeIcal = '073000';
                        $EndTimeIcal = '083000';
                        $subject = "Reminder Venue Reservation";
                        $text = 'Dear Team,<br><br>
                                    Reminder Venue Reservation,<br><br>
                                    Details Schedule : <br><ul>
                                    <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                                    <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                                    <li>Agenda  : '.$get[0]['Agenda'].'</li>
                                    <li>Room  : '.$get[0]['Room'].'</li>
                                    '.$Email_add_person.'
                                    '.$MarkomEmail.'
                                    </ul>
                                    '.$EmailKetAdditional.'
                                    '.$KetAdditional_eq.
                                    $Email_invitation.'</br>
                                ';
                        $sendEmail = $this->m_sendemail->sendEmailIcal($to,$subject,$text, $place,$StartIcal,$StartTimeIcal,$EndIcal,$EndTimeIcal);


                        $text = 'Dear Team,<br><br>
                                    Venue Reservation schedule,<br><br>
                                    Details Schedule : <br><ul>
                                    <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                                    <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                                    <li>Agenda  : '.$get[0]['Agenda'].'</li>
                                    <li>Room  : '.$get[0]['Room'].'</li>
                                    '.$Email_add_person.'
                                    '.$MarkomEmail.'
                                    </ul>
                                    '.$EmailKetAdditional.'
                                    '.$KetAdditional_eq.
                                    $Email_invitation.'</br>
                                '; 
                        $subject = "Podomoro University Venue Reservation Approved";
                        $StartTimeIcal = date("His", strtotime($get[0]['Start']));
                        $EndTimeIcal = date("His", strtotime($get[0]['End']));               
                        $sendEmail = $this->m_sendemail->sendEmailIcal($to,$subject,$text, $place,$StartIcal,$StartTimeIcal,$EndIcal,$EndTimeIcal);

                    }

                    if ($KetAdditional_eq != '') {
                       for ($n=0; $n < count($e_div); $n++) { 
                           $Email = $e_div[$n];
                           $to = $Email;

                           // reminder
                           $subject = "Reminder Venue Reservation";
                           $text = 'Dear Team,<br><br>
                                       Reminder Venue Reservation,<br><br>
                                       Details Schedule : <br><ul>
                                       <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                                       <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                                       <li>Agenda  : '.$get[0]['Agenda'].'</li>
                                       <li>Room  : '.$get[0]['Room'].'</li>
                                       '.$Email_add_person.'
                                       '.$MarkomEmail.'
                                       </ul>
                                       '.$EmailKetAdditional.'
                                       '.$KetAdditional_eq.
                                       $Email_invitation.'</br>
                                   ';
                           $StartTimeIcal = '073000';
                           $EndTimeIcal = '083000';
                           $subject = "Reminder Venue Reservation";
                           $sendEmail = $this->m_sendemail->sendEmailIcal($to,$subject,$text, $place,$StartIcal,$StartTimeIcal,$EndIcal,$EndTimeIcal);


                           $text = 'Dear Team,<br><br>
                                       Venue Reservation schedule by '.$getUser[0]['Name'].' as Requester,<br><br>
                                       Details Schedule : <br><ul>
                                       <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                                       <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                                       <li>Agenda  : '.$get[0]['Agenda'].'</li>
                                       <li>Room  : '.$get[0]['Room'].'</li>
                                       '.$Email_add_person.'
                                       '.$MarkomEmail.'
                                       </ul>
                                       '.$EmailKetAdditional.'
                                       '.$KetAdditional_eq.
                                       $Email_invitation.'</br>
                                   ';
                            $StartTimeIcal = date("His", strtotime($get[0]['Start']));
                            $EndTimeIcal = date("His", strtotime($get[0]['End']));   
                            $subject = "Podomoro University Venue Reservation Approved";     
                           $sendEmail = $this->m_sendemail->sendEmailIcal($to,$subject,$text, $place,$StartIcal,$StartTimeIcal,$EndIcal,$EndTimeIcal);
                        }
                    }

               // closed approve all     
            }
            else
            {
                $Email = $getUser[0]['EmailPU'];
                $text = 'Dear Mr/Mrs '.$getUser[0]['Name'].',<br><br>
                            Your Venue Reservation approved by '.$this->session->userdata('Name').$ApprovalWr.',<br><br>
                            Details Schedule : <br><ul>
                            <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                            <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                            <li>Room  : '.$get[0]['Room'].'</li>
                            <li>Agenda  : '.$get[0]['Agenda'].'</li>
                            </ul>
                            '.$Email_add_person.'
                            '.$MarkomEmail.'
                            </ul>
                            '.$EmailKetAdditional.'
                            '.$KetAdditional_eq.
                            $Email_invitation.'</br>
                        ';        
                $to = $Email;
                $subject = "Podomoro University Venue Reservation Approved";
                $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
            }

            // email to division choice
            $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$get[0]['Room']);
            $CategoryRoomByRoom = $getRoom[0]['ID_CategoryRoom'];
            $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
            $Approver2Div = $getDataCategoryRoom[0]['Approver2'];
            $Approver2Div = json_decode($Approver2Div);

            $EmailPUAPP2 = '';
            $CodeAPP2 = '';
            $NameWR = '';
            for ($zz=0; $zz < count($Approver2Div); $zz++) { 
                $rdata = $Approver2Div[$zz];
                $TypeApprover = $rdata->TypeApprover;
                $bool = false;
                switch ($TypeApprover) {
                    case 'Division':
                        $DivisionApprove = $this->m_master->caribasedprimary('db_employees.division','ID',$rdata->Approver);
                        $EmailPUAPP2 = $DivisionApprove[0]['Email'];
                        $CodeAPP2 = $DivisionApprove[0]['ID'];
                        $NameWR = $DivisionApprove[0]['Division'].' Team';
                        $bool = true;
                        break;
                    case 'Employees':
                        $NIPAPP2 = $rdata->Approver;
                        $G_emp = $this->m_master->caribasedprimary('db_employees.employees','NIP',$NIPAPP2);
                        $EmailPUAPP2 = $G_emp[0]['EmailPU'];
                        $CodeAPP2 = $NIPAPP2;
                        $NameWR = 'Mr/Mrs '.$G_emp[0]['Name'];
                        $bool = true;
                        break;

                }

                if ($bool) {
                    break;
                }
            }


            $token = array(
                    'EmailPU' => $EmailPUAPP2,
                    'Code' => $CodeAPP2,
                    'ID_t_booking' => $ID,
                    'approvalNo' => 2,
                    'Email_add_person' => $Email_add_person,
                    'MarkomEmail' => $MarkomEmail,
                    'EmailKetAdditional' => $EmailKetAdditional,
                    'KetAdditional_eq' => $KetAdditional_eq,
            );
            
            $token = $this->jwt->encode($token,'UAP)(*');

            if ($approveaccess == 2) {
                if($_SERVER['SERVER_NAME']!='localhost') {
                    $Email = $EmailPUAPP2;
                    $text = 'Dear '.$NameWR.',<br><br>
                                Venue Reservation has been approved by '.$this->session->userdata('Name').$ApprovalWr.',<br><br>
                                Please help to approve Venue Reservation,<br><br>
                                Details Schedule : <br><ul>
                                <li>Start  : '.$StartNameDay.', '.$Start.'</li>
                                <li>End  : '.$EndNameDay.', '.$End.'</li>
                                <li>Room  : '.$get[0]['Room'].'</li>
                                <li>Agenda  : '.$get[0]['Agenda'].'</li>
                                </ul>
                                '.$Email_add_person.'
                                '.$MarkomEmail.'
                                </ul>
                                '.$EmailKetAdditional.'
                                '.$KetAdditional_eq.
                                $Email_invitation.'</br>
                                <table width="200" cellspacing="0" cellpadding="12" border="0">
                                    <tbody>
                                    <tr>
                                        <td bgcolor="#51a351" align="center">
                                            <a href="'.url_pas.'approve_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >Approve</a>
                                        </td>
                                        <td>
                                           -
                                        </td>
                                        <td bgcolor="#e98180" align="center">
                                            <a href="'.url_pas.'cancel_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #e98180;" target="_blank" >Reject</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            ';        
                    $to = $Email;
                    $subject = "Podomoro University Venue Reservation Approval 2";
                    $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);

                }
                else
                {
                    $Email = 'alhadi.rahman@podomorouniversity.ac.id';
                    $text = 'Dear '.$NameWR.',<br><br>
                                Venue Reservation has been approved by '.$this->session->userdata('Name').$ApprovalWr.',<br><br>
                                Please help to approve Venue Reservation,<br><br>
                                Details Schedule : <br><ul>
                                <li>Start  : '.$StartNameDay.', '.$Start.'</li>
                                <li>End  : '.$EndNameDay.', '.$End.'</li>
                                <li>Room  : '.$get[0]['Room'].'</li>
                                <li>Agenda  : '.$get[0]['Agenda'].'</li>
                                </ul>
                                '.$Email_add_person.'
                                '.$MarkomEmail.'
                                </ul>
                                '.$EmailKetAdditional.'
                                '.$KetAdditional_eq.
                                $Email_invitation.'</br>
                                <table width="200" cellspacing="0" cellpadding="12" border="0">
                                    <tbody>
                                    <tr>
                                        <td bgcolor="#51a351" align="center">
                                            <a href="'.url_pas.'approve_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >Approve</a>
                                        </td>
                                        <td>
                                           -
                                        </td>
                                        <td bgcolor="#e98180" align="center">
                                            <a href="'.url_pas.'cancel_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #e98180;" target="_blank" >Reject</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            ';        
                    $to = $Email;
                    $subject = "Podomoro University Venue Reservation Approval 2";
                    $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                }
            }
            else if($approveaccess == 0) // send to approver 1
            {
                $ID_t_booking = $ID;
               $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['Start']);
               $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['End']);
               $StartNameDay = $Startdatetime->format('l');
               $EndNameDay = $Enddatetime->format('l');

               // send email to approval 1
                  // email to approval 1
                        $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$get[0]['Room']);
                        $CategoryRoomByRoom = $getRoom[0]['ID_CategoryRoom'];
                        $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
                        $Approver1 = $getDataCategoryRoom[0]['Approver1'];
                        $Approver1 = json_decode($Approver1);
                        // get user type
                            $CreatedBy = $get[0]['CreatedBy'];
                            $getCreatedBy = $this->m_master->caribasedprimary('db_employees.employees','NIP',$CreatedBy);
                            $sql = 'select a.* from db_reservation.cfg_policy as a join db_reservation.cfg_group_user as b on a.ID_group_user = b.ID join db_reservation.previleges_guser as c 
                                    on b.ID = c.G_user where c.NIP = ? limit 1';
                            $query=$this->db->query($sql, array($CreatedBy))->result_array();
                            
                        $ID_group_user = $query[0]['ID_group_user'];
                        $dataApprover = array();
                        for ($l=0; $l < count($Approver1); $l++) {
                            // find by ID_group_user
                                if ($ID_group_user == $Approver1[$l]->UserType) {
                                    // get TypeApprover
                                    $TypeApprover = $Approver1[$l]->TypeApprover;
                                    switch ($TypeApprover) {
                                        case 'Position':
                                            // get Division to access position approval
                                                $PositionMain = $getCreatedBy[0]['PositionMain'];
                                                $PositionMain = explode('.', $PositionMain);
                                                $IDDivision = $PositionMain[0];
                                                $IDPositionApprover = $Approver1[$l]->Approver; 
                                                if ($IDDivision == 15) { // if prodi
                                                    // find prodi
                                                    $sqlgg = 'select * from db_academic.program_study where AdminID = ? or KaprodiID = ?';
                                                    $gg=$this->db->query($sql, array($CreatedBy,$CreatedBy))->result_array();
                                                    if (count($gg) > 0) {
                                                        for ($k=0; $k < count($gg); $k++) { 
                                                            $Kaprodi = $gg[$k]['KaprodiID'];
                                                            $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Kaprodi);
                                                            for ($m=0; $m < count($getApprover1); $m++) { 
                                                                if ($getApprover1[$m]['StatusEmployeeID'] > 0) {
                                                                     $dataApprover[] = array('Email' => $getApprover1[$m]['EmailPU'],'Name' => $getApprover1[$m]['Name'],'Code' => $Kaprodi,'TypeApprover' => $TypeApprover);
                                                                }
                                                            }
                                                        }
                                                        
                                                    }
                                                }
                                                else
                                                {
                                                    // find by division and position
                                                    $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionMain',$IDDivision.'.'.$IDPositionApprover);
                                                    if (count($getApprover1) == 0) {
                                                        $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionOther1',$IDDivision.'.'.$IDPositionApprover);
                                                        if (count($getApprover1) == 0) {
                                                            $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionOther2',$IDDivision.'.'.$IDPositionApprover);
                                                            if (count($getApprover1) == 0) {
                                                                $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionOther3',$IDDivision.'.'.$IDPositionApprover);
                                                            }
                                                        }
                                                    }
                                                    for ($k=0; $k < count($getApprover1); $k++) {
                                                        if ($getApprover1[$k]['StatusEmployeeID'] > 0) {
                                                             $dataApprover[] = array('Email' => $getApprover1[$k]['EmailPU'],'Name' => $getApprover1[$k]['Name'],'Code' => $getApprover1[$k]['NIP'],'TypeApprover' => $TypeApprover);
                                                        } 
                                                       
                                                    }
                                                }
                                            break;
                                        
                                        case 'Division':
                                            $getApprover1 = $this->m_master->caribasedprimary('db_employees.division','ID',$Approver1[$l]->Approver);
                                            for ($k=0; $k < count($getApprover1); $k++) { 
                                               $dataApprover[] = array('Email' => $getApprover1[$k]['Email'],'Name' => $getApprover1[$k]['Division'],'Code' => $getApprover1[$k]['ID'],'TypeApprover' => $TypeApprover);
                                            }
                                            break;

                                        case 'Employees':
                                            $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Approver1[$l]->Approver);
                                            for ($k=0; $k < count($getApprover1); $k++) { 
                                               $dataApprover[] = array('Email' => $getApprover1[$k]['EmailPU'],'Name' => $getApprover1[$k]['Name'],'Code' => $getApprover1[$k]['NIP'],'TypeApprover' => $TypeApprover);
                                            }
                                            break;    
                                    }
                                }
                        } // end loop for
                
                        if($_SERVER['SERVER_NAME']!='localhost') {
                            // send email
                            $EmailPU = '';
                            $NameEmail = '';
                            $Code = '';
                            $TypeApproverE = '';
                            if (count($dataApprover) > 0) {
                                $temp = array();
                                $temp2 = array();
                                $temp3 = array();
                                for ($k=0; $k < count($dataApprover); $k++) { 
                                    $EM = $dataApprover[$k]['Email'];
                                    $NM = $dataApprover[$k]['Name'];
                                    $Code = $dataApprover[$k]['Code'];
                                    $temp[] = $EM;
                                    $temp2[] = $NM;
                                    $temp3[] = $Code;
                                }
                                 
                                $EmailPU = implode(",", $temp);
                                $NameEmail = implode(" / ", $temp2);
                                $Code = implode(";", $temp3);
                            }
                            
                            if ($EmailPU != '' || $EmailPU != null) {
                                $token = array(
                                    'EmailPU' => $EmailPU,
                                    'Code' => $Code,
                                    'ID_t_booking' => $ID_t_booking,
                                    'approvalNo' => 1,
                                    'Email_add_person' => $Email_add_person,
                                    'MarkomEmail' => $MarkomEmail,
                                    'EmailKetAdditional' => $EmailKetAdditional,
                                    'KetAdditional_eq' => $KetAdditional_eq,
                                );
                                $token = $this->jwt->encode($token,'UAP)(*');
                                $Email = $EmailPU;
                                $text = 'Dear Mr/Mrs '.$NameEmail.',<br><br>
                                            Please help to approve Venue Reservation requested by '.$getCreatedBy[0]['Name'].',<br><br>
                                            Details Schedule : <br><ul>
                                            <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                                            <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                                            <li>Room  : '.$get[0]['Room'].'</li>
                                            <li>Agenda  : '.$get[0]['Agenda'].'</li>
                                            '.$Email_add_person.'
                                            '.$MarkomEmail.'
                                            </ul>
                                            '.$EmailKetAdditional.'
                                            '.$KetAdditional_eq.
                                            $Email_invitation.'</br>
                                            <table width="100" cellspacing="0" cellpadding="12" border="0">
                                                <tbody>
                                                <tr>
                                                    <td bgcolor="#51a351" align="center">
                                                        <a href="'.url_pas.'approve_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >Approve</a>
                                                    </td>
                                                    <td align="center">
                                                       -
                                                    </td>
                                                    <td bgcolor="#red" align="center">
                                                        <a href="'.url_pas.'cancel_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #red;" target="_blank" >Reject</a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        ';        
                                $to = $Email;
                                $subject = "Podomoro University Venue Reservation Approval 1";
                                $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);

                            }
                        }
                        else
                        {
                            // send email
                            $EmailPU = '';
                            $NameEmail = '';
                            $Code = '';
                            if (count($dataApprover) > 0) {
                                $temp = array();
                                $temp2 = array();
                                $temp3 = array();
                                for ($k=0; $k < count($dataApprover); $k++) { 
                                    $EM = $dataApprover[$k]['Email'];
                                    $NM = $dataApprover[$k]['Name'];
                                    $Code = $dataApprover[$k]['Code'];
                                    $temp[] = $EM;
                                    $temp2[] = $NM;
                                    $temp3[] = $Code;
                                }
                                 
                                $EmailPU = implode(",", $temp);
                                $NameEmail = implode(" / ", $temp2);
                                $Code = implode(";", $temp3);
                            }
                            
                            if ($EmailPU != '' || $EmailPU != null) {
                                $token = array(
                                    'EmailPU' => $EmailPU,
                                    'Code' => $Code,
                                    'ID_t_booking' => $ID_t_booking,
                                    'approvalNo' => 1,
                                    'Email_add_person' => $Email_add_person,
                                    'MarkomEmail' => $MarkomEmail,
                                    'EmailKetAdditional' => $EmailKetAdditional,
                                    'KetAdditional_eq' => $KetAdditional_eq,
                                );
                                $token = $this->jwt->encode($token,'UAP)(*');
                                $Email = 'alhadi.rahman@podomorouniversity.ac.id';
                                $text = 'Dear Mr/Mrs '.$NameEmail.',<br><br>
                                            Please help to approve Venue Reservation requested by '.$getCreatedBy[0]['Name'].',<br><br>
                                            Details Schedule : <br><ul>
                                            <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                                            <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                                            <li>Room  : '.$get[0]['Room'].'</li>
                                            <li>Agenda  : '.$get[0]['Agenda'].'</li>
                                            '.$Email_add_person.'
                                            '.$MarkomEmail.'
                                            </ul>
                                            '.$EmailKetAdditional.'
                                            '.$KetAdditional_eq.
                                            $Email_invitation.'</br>
                                            <table width="200" cellspacing="0" cellpadding="12" border="0">
                                                <tbody>
                                                <tr>
                                                    <td bgcolor="#51a351" align="center">
                                                        <a href="'.url_pas.'approve_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #51a351;" target="_blank" >Approve</a>
                                                    </td>
                                                    <td align="center">
                                                      -
                                                    </td>
                                                    <td bgcolor="#e98180" align="center">
                                                        <a href="'.url_pas.'cancel_venue/'.$token.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color: #e98180;" target="_blank" >Reject</a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        ';        
                                $to = $Email;
                                $subject = "Podomoro University Venue Reservation Approval 1";
                                $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);

                            }
                        }

            }
        }
        else
        {
            $msg = 'This schedule conflict, Please check';
        }

        echo json_encode($msg);
        
    }

    public function cancel_submit()
    {
        $msg = '';
            $input = $this->getInputToken();

            $get = $this->m_master->caribasedprimary('db_reservation.t_booking','ID',$input['ID_tbl']);
            $getUser = $this->m_master->caribasedprimary('db_employees.employees','NIP',$get[0]['CreatedBy']);
            $getE_additional = $this->m_master->caribasedprimary('db_reservation.t_booking_eq_additional','ID_t_booking',$get[0]['ID']);

            $KetAdditional = json_decode($get[0]['KetAdditional']);    
            $EmailKetAdditional = '';
            if ($KetAdditional != '') {
                if (count($KetAdditional) > 0) {
                    foreach ($KetAdditional as $key => $value) {
                        if ($value != "" || $value != null) {
                            // $EmailKetAdditional .= '<br>*   '.$key.'('.$value.')';
                            $EmailKetAdditional .= '<br>*   '.str_replace("_", " ", $key).' : '.$value;    
                        }  
                    }
                }
                
            }

            $Reason = 'Cancel by yourself';
            if(array_key_exists("Reason",$input))
            {
                $Reason = $input['Reason'];
            }

            $KetAdditional_eq = '';
            for ($i=0; $i < count($getE_additional); $i++) { 
                if ($i == 0) {
                    $KetAdditional_eq = '<br><br>*  Equipment Additional<ul>';
                }
                $gett_booking_eq_additional = $this->m_reservation->gett_booking_eq_additional($getE_additional[$i]['ID_equipment_additional'],$get[0]['ID']);
                $Qty = $gett_booking_eq_additional[0]['Qty'];
                $ID_equipment_additional = $gett_booking_eq_additional[0]['ID_equipment_additional'];
                $get123 = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
                $OwnerID = $get123[0]['Owner'];
                $getX = $this->m_master->caribasedprimary('db_employees.division','ID',$OwnerID);
                $Owner = $getX[0]['Division'];
                $ID_m_equipment = $get123[0]['ID_m_equipment'];
                $get123 = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                $KetAdditional_eq .= '<li>'.$get123[0]['Equipment'].' by '.$Owner.'['.$Qty.']</li>';


                $dataSave = array(
                    'ID_t_booking_eq_add' => $getE_additional[$i]['ID'],
                    'ID_t_booking' => $get[0]['ID'],
                    'ID_equipment_additional' => $getE_additional[$i]['ID_equipment_additional'],
                    'Qty' => $getE_additional[$i]['Qty'],
                );
                $this->db->insert('db_reservation.t_booking_eq_additional_delete', $dataSave); 
            }
            $KetAdditional_eq .= '</ul>';
            
            $sql = "delete from db_reservation.t_booking_eq_additional where ID_t_booking = ".$get[0]['ID'];
            $query=$this->db->query($sql, array());

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
                'Note_deleted' => 'Cancel By User##'.$Reason,
                'DeletedBy' => $this->session->userdata('NIP'),
                'Req_layout' => $get[0]['Req_layout'],
                'Status' => $get[0]['Status'],
                'MarcommSupport' => $get[0]['MarcommSupport'],
                'KetAdditional' => $get[0]['KetAdditional'],
            );
            $this->db->insert('db_reservation.t_booking_delete', $dataSave); 

            $this->m_master->delete_id_table_all_db($get[0]['ID'],'db_reservation.t_booking');
            // $this->m_master->delete_id_table_all_db($get[0]['ID'],'db_reservation.t_booking_eq_additional');
            $this->db->where('ID_t_booking', $get[0]['ID']);
            $this->db->delete('db_reservation.t_booking_eq_additional');
// send email
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
            $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['Start']);
            $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $get[0]['End']);
            $StartNameDay = $Startdatetime->format('l');
            $EndNameDay = $Enddatetime->format('l');

            $Email = 'alhadi.rahman@podomorouniversity.ac.id';
            if($_SERVER['SERVER_NAME']!='localhost') {
                $Email = $getUser[0]['EmailPU'];
            }
            
            $text = 'Dear '.$getUser[0]['Name'].',<br><br>
                        Your Venue Reservation was Cancel by '.$this->session->userdata('Name').',<br><br>
                        Details Schedule : <br><ul>
                        <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                        <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                        <li>Room  : '.$get[0]['Room'].'</li>
                        <li>Agenda  : '.$get[0]['Agenda'].'</li>
                        <li>Reason : '.$Reason.'</li>
                        </ul>

                        '.$EmailKetAdditional.'
                        '.$KetAdditional_eq.'</br>
                        <br>
                            Please Create new schedule, if you need it and '.$sg_room.'
                    ';        
            $to = $Email;
            $subject = "Podomoro University Venue Reservation Cancel Reservation";
            $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);    

            // send email to administratif
            $getEmail = $this->m_master->showData_array('db_reservation.email_to');
            if($_SERVER['SERVER_NAME']!='localhost') {
                for ($i=0; $i < count($getEmail); $i++) {
                    if ($i != 0) {
                         $Email = $getEmail[$i]['Email'];
                         $text = 'Dear Mr/Mrs '.$getEmail[$i]['Ownership'].',<br><br>
                                     Venue Reservation was Cancel by '.$this->session->userdata('Name').',<br><br>
                                     Details Schedule : <br><ul>
                                     <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                                     <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                                     <li>Room  : '.$get[0]['Room'].'</li>
                                     <li>Reason : '.$Reason.'</li>
                                     </ul>

                                     '.$EmailKetAdditional.'
                                     '.$KetAdditional_eq.'</br>
                                 ';        
                         $to = $Email;
                         $subject = "Podomoro University Venue Reservation Cancel Reservation";
                         $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                    } 
                }

            }
            else
            {
                $Email = $getEmail[0]['Email'];
                $text = 'Dear Mr/Mrs '.$getEmail[0]['Ownership'].',<br><br>
                            Venue Reservation was Cancel by '.$this->session->userdata('Name').',<br><br>
                            Details Schedule : <br><ul>
                            <li>Start  : '.$StartNameDay.', '.$get[0]['Start'].'</li>
                            <li>End  : '.$EndNameDay.', '.$get[0]['End'].'</li>
                            <li>Room  : '.$get[0]['Room'].'</li>
                            <li>Agenda  : '.$get[0]['Agenda'].'</li>
                            <li>Reason : '.$Reason.'</li>
                            </ul>

                            '.$EmailKetAdditional.'
                            '.$KetAdditional_eq.'</br>
                        ';        
                $to = $Email;
                $subject = "Podomoro University Venue Reservation Cancel Reservation";
                $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
            }

        echo json_encode($msg);    
    }

    public function booking_cancel()
    {
        $content = $this->load->view($this->pathView.'transaksi/page_booking_cancel','',true);
        $this->temp($content);
    }
    public function data_reservation()
    {
        $content = $this->load->view($this->pathView.'transaksi/page_data_reservation','',true);
        $this->temp($content);
    }


    public function return_eq()
    {
        $content = $this->load->view($this->pathView.'transaksi/return_eq','',true);
        $this->temp($content);
    }

    public function return_eq_show()
    {
        $getData= $this->m_reservation->getForReturn_eq();
        echo json_encode($getData);
    }

    public function modal_form_return_eq()
    {
        $arr = array();
        $input = $this->getInputToken();
        $ID = $input['ID'];
        $query = $this->m_master->caribasedprimary('db_reservation.t_booking','ID',$ID);
        for ($i=0; $i < count($query); $i++) { 
            $ID_equipment_add = explode(',', $query[$i]['ID_equipment_add']);
            for ($j=0; $j < count($ID_equipment_add); $j++) {
                $gett_booking_eq_additional = $this->m_reservation->gett_booking_eq_additional($ID_equipment_add[$j],$query[$i]['ID']);
                if ($gett_booking_eq_additional[0]['Status'] == 1) {
                    // print_r($gett_booking_eq_additional);
                    $Qty = $gett_booking_eq_additional[0]['Qty'];
                    $ID_equipment_additional = $gett_booking_eq_additional[0]['ID_equipment_additional'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
                    $OwnerID = $get[0]['Owner'];
                    $DivisionID = $this->session->userdata('PositionMain');
                        $DivisionID = $DivisionID['IDDivision'];
                        if ($DivisionID == $OwnerID) {
                            // check data existing in t_return_eq
                            $g =$this->m_master->caribasedprimary('db_reservation.t_return_eq','ID_t_booking_eq_additional',$gett_booking_eq_additional[0]['ID']);
                            if (count($g) == 0 ) {
                                $ID_m_equipment = $get[0]['ID_m_equipment'];
                                $get = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                                $temp = array(
                                    'ID_equipment_additional' => $ID_equipment_add[$j],
                                    'Name' => $get[0]['Equipment'],
                                    'Qty' => $Qty,
                                    'IDTable' => $gett_booking_eq_additional[0]['ID'],
                                );
                                $arr[] = $temp;
                            }
                            
                        }
                        else
                        {
                            if ($this->session->userdata('ID_group_user') < 3) {
                                // check data existing in t_return_eq
                                $g =$this->m_master->caribasedprimary('db_reservation.t_return_eq','ID_t_booking_eq_additional',$gett_booking_eq_additional[0]['ID']);
                                if (count($g) == 0 ) {
                                    $ID_m_equipment = $get[0]['ID_m_equipment'];
                                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                                    $temp = array(
                                        'ID_equipment_additional' => $ID_equipment_add[$j],
                                        'Name' => $get[0]['Equipment'],
                                        'Qty' => $Qty,
                                        'IDTable' => $gett_booking_eq_additional[0]['ID'],
                                    );
                                    $arr[] = $temp;
                                }
                            }
                        }
                }
            }
        }

        echo json_encode($arr);
    }

    public function modal_form_return_eq_save()
    {
        $input = $this->getInputToken();
        $ID_t_booking = $input['ID'];
        $ID_tbl_t_booking_eq_additional = $input['IDTable'];
        $Desc = $input['Desc'];

        $dataSave = array(
            'ID_t_booking_eq_additional'=> $ID_tbl_t_booking_eq_additional,
            'Desc' => $Desc,
            'Time' => date('Y-m-d H:i:s'),
            'By' => $this->session->userdata('NIP'),
        );

        $this->db->insert('db_reservation.t_return_eq', $dataSave); 

    }

    public function t_eq($page)
    {
          $arr_result = array('html' => '','jsonPass' => '');
          $uri = $this->uri->segment(3);
          $content = $this->load->view($this->pathView.'transaksi/'.$uri,'',true);
          $arr_result['html'] = $content;
          echo json_encode($arr_result);
    }

    public function list_eq_history()
    {
        $g = $this->m_reservation->get_list_eq_history();
        echo json_encode($g);
    }

    public function detail_historis()
    {
        $input = $this->getInputToken();
        $rs = array();
        $ID_equipment_additional = $input['ID_equipment_additional'];
        $sql = 'select * from db_reservation.t_booking_eq_additional where ID_equipment_additional = "'.$ID_equipment_additional.'" and Status = 1 and ID_t_booking in (select ID from db_reservation.t_booking where Status = 1) group by ID_t_booking';
        $query=$this->db->query($sql, array())->result_array();
        for ($i=0; $i < count($query); $i++) { 
            $g= $this->m_reservation->getDataT_info($query[$i]['ID_t_booking']);
            $rs[] = $g[0];
        }

        echo json_encode($rs);
    }

}
