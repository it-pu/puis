<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_rest3 extends CI_Controller {
    public $data = [];

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

        // auth
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if (!$auth) {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
                die();
            }
            $this->data['dataToken'] = $dataToken;
        } catch (Exception $e) {
           // handling orang iseng
           echo '{"status":"999","message":"Not Authorize"}'; 
           die();
        }
        
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

    private function getInputToken3() // adhi to array 2020-02-13
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = json_decode(json_encode($this->jwt->decode($token,$key)),true) ;
        return $data_arr;
    }

    public function is_url_exist($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code == 200){
            $status = true;
        }else{
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

    public function APS_CrudAgregatorTB3()
    {
       $this->load->model('research/m_research');
        $dataToken = $this->data['dataToken'];
        $mode = $dataToken['mode'];
        switch ($mode) {
            case 'JudulPenelitian&JudulPKM':
                //get header and body
                $rs = ['header' => [],'body' => [] ];
                $header = [];
                $header[] = ['Name' => 'No','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Sumber Pembiayaan','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $Year = Date('Y');
                $Year3 = $Year - 4;
                $arr_year = [];
                for ($i=$Year3; $i<= $Year; $i++) { 
                   $arr_year[] = $i;
                }
                $header[] = ['Name' => 'Jumlah Judul Penelitian','rowspan' => 1,'Sub' => $arr_year,'colspan' => count($arr_year) ];
                $header[] = ['Name' => 'Jumlah Penelitian','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Jumlah Judul PkM','rowspan' => 1,'Sub' => $arr_year,'colspan' => count($arr_year) ];
                $header[] = ['Name' => 'Jumlah PkM','rowspan' => 2,'Sub' => [],'colspan' => 1 ];

                $G_SumberDana = $this->m_master->caribasedprimary('db_agregator.sumber_dana','Status','1');
                $body = [];
                $ProdiID = $dataToken['ProdiID'];
                for ($i=0; $i < count($G_SumberDana); $i++) { 
                    $No = $i+1;
                    $ID_sumberdana = $G_SumberDana[$i]['ID'];
                    $Name = $G_SumberDana[$i]['SumberDana'];
                    $temp = [];
                    $temp[] = array('show' => $No ,'data' => []); // $j = 0 adalah No 
                    $temp[] = array('show' => $Name ,'data' => []); // $j = 1 adalah Name Sumber Dana 
                    $JmlPenelitian = 0;
                    $JmlPKM = 0;
                    for ($j=2; $j < count($header); $j++) {
                        switch ($j) {
                             case 2: // ambil dari table lintabmas
                                 // cek sub
                                 $sub = $header[$j]['Sub'];
                                 if (count($sub) > 0 ) {
                                    for ($k=0; $k < count($sub); $k++) { 
                                        $Y = $sub[$k];
                                        $sql = 'select a.*,b.Name from db_research.litabmas as a
                                               join db_employees.employees as b on a.NIP = b.NIP
                                               where a.ID_sumberdana = ? and a.ID_thn_laks = ?
                                               and b.ProdiID = ?     
                                                ';
                                        $query=$this->db->query($sql, array($ID_sumberdana,$Y,$ProdiID))->result_array();
                                        for ($lt=0; $lt < count($query); $lt++) { 
                                          $sqlGetAnggota = 'select usrr.Nama from db_research.list_anggota_penelitian as a
                                                            join db_research.master_anggota_penelitian as b on a.ID_anggota = b.ID
                                                            '.$this->m_research->QueryUserReserachJoin('b.ID_user').'
                                                            where a.ID_Litabmas = '.$query[$lt]['ID_litabmas'].'
                                           ';
                                           $dataAnggota = $this->db->query($sqlGetAnggota,array())->result_array();
                                           $query[$lt]['dataAnggota'] = $dataAnggota;
                                        }
                                        $tot = count($query);
                                        // encode token
                                        $token = $this->jwt->encode($query,"UAP)(*");
                                        $temp[] = array('show' => $tot ,'data' => $token);
                                        $JmlPenelitian += count($query);
                                    }     
                                 }
                                 else
                                 {
                                    $temp[] = array('show' => 0 ,'data' => []);
                                 }
                                 break;
                             case 3: // Jumlah Penelitian
                                $temp[] = array('show' => $JmlPenelitian ,'data' => []);
                                break;
                             case 4: // Judul PKM
                                // cek sub
                                $sub = $header[$j]['Sub'];
                                if (count($sub) > 0 ) {
                                   for ($k=0; $k < count($sub); $k++) { 
                                       $Y = $sub[$k];
                                       $sql = 'select a.*,b.Name from db_research.pengabdian_masyarakat as a
                                               join db_employees.employees as b on a.NIP = b.NIP
                                              where a.ID_sumberdana = ? and a.ID_thn_laks = ?
                                              and b.ProdiID = ?     
                                               ';
                                       $query=$this->db->query($sql, array($ID_sumberdana,$Y,$ProdiID))->result_array();
                                       for ($lt=0; $lt < count($query); $lt++) { 
                                         $sqlGetAnggota = 'select usrr.Nama from db_research.list_anggota_pkm as a
                                                           join db_research.master_anggota_pkm as b on a.ID_anggota = b.ID
                                                           '.$this->m_research->QueryUserReserachJoin('b.ID_user').'
                                                           where a.ID_PKM = '.$query[$lt]['ID_PKM'].'
                                          ';
                                          $dataAnggota = $this->db->query($sqlGetAnggota,array())->result_array();
                                          $query[$lt]['dataAnggota'] = $dataAnggota;
                                       }
                                       $tot = count($query);
                                       // encode token
                                       $token = $this->jwt->encode($query,"UAP)(*");
                                       $temp[] = array('show' => $tot ,'data' => $token);
                                       $JmlPKM += count($query);
                                   }     
                                }
                                else
                                {
                                   $temp[] = array('show' => 0 ,'data' => []);
                                }
                                break;
                            case 5: // Jumlah Judul PKM
                               $temp[] = array('show' => $JmlPKM ,'data' => []);
                               break;
                             default:
                                 # code...
                                 break;
                         } 
                    }
                    $body[] = $temp;

                }
                $rs['header'] = $header;
                $rs['body'] = $body;
                echo json_encode($rs);
                break;
            case 'RekognisiDosenKaryaIlmiah_old':
                $rs = ['header' => [],'body' => [] ];
                $header = [];
                $header[] = ['Name' => 'No','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Nama Dosen','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Rekognisi dan Bukti Pendukung','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Tingkat','rowspan' => 1,'Sub' => ['Wilayah','Nasional','Internasional'],'colspan' => 3 ];
                $header[] = ['Name' => 'Tahun Rekognisi (YYYY)','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Judul Artikel yang Disitasi (Jurnal, Volume, Tahun, Nomor, Halaman)','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Jumlah Sitasi','rowspan' => 2,'Sub' => [],'colspan' => 1 ];

                $body = [];
                $ProdiID = $dataToken['ProdiID'];
                $filterTahun = $dataToken['filterTahun'];
                $sql = 'select a.*,b.Name from db_agregator.rekognisi_dosen as a 
                        join db_employees.employees as b on a.NIP = b.NIP
                        where b.ProdiID = ? and a.Tahun = ?
                        order by a.NIP,a.ID desc limit 1000
                        ';
                $query=$this->db->query($sql, array($ProdiID,$filterTahun))->result_array();
                for ($i=0; $i < count($query); $i++) { 
                    $No = $i + 1;
                    $NIP = $query[$i]['NIP'];
                    $NameDosen = $query[$i]['Name'];
                    $G_dt_artikel = $this->m_master->caribasedprimary('db_agregator.sitasi_karya','NIP_penulis',$NIP);
                    $temp = [];
                    for ($k=0; $k < count($header); $k++) { 
                        switch ($k) {
                            case 0: // No
                               $temp[] = $No;
                                break;
                            case 1: // Name
                               $temp[] = $NameDosen;
                                break;
                            case 2: // Rekognisi dan Bukti Pendukung
                               $Rekognisi = $query[$i]['Rekognisi'];
                               $BuktiName = $query[$i]['BuktiPendukungName'];
                               $BuktiPendukungUpload = $query[$i]['BuktiPendukungUpload'];
                               $arr_file = (array) json_decode($BuktiPendukungUpload,true);
                               $wr = $Rekognisi;
                               if ($BuktiName != '' && count($arr_file) > 0 ) {
                                   $wr .= '<br/>'.$BuktiName.'<br/>'.'<a href="'.base_url().'fileGetAny/Agregator-Aps-'.$arr_file[0].'" target="_blank" class="Fileexist">Attachment</a>';
                               }
                               $temp[] = $wr;
                                break;
                            case 3: // Tingkat
                                $Sub = ['Wilayah','Nasional','Internasional'];
                                for ($l=0; $l < count($Sub); $l++) {
                                    $wr = '';
                                   if ($Sub[$l] == $query[$i]['Tingkat']) {
                                       $wr = 'V'; 
                                   }
                                   $temp[] = $wr;
                                }
                               break;
                            case 4: // Tahun
                               $wr = $query[$i]['Tahun'];
                               $temp[] = $wr;
                               break;
                            case 5: // Judul Artikel
                               $wr = '';
                               if (count($G_dt_artikel) > 0) {
                                   $wr .= $G_dt_artikel[0]['Judul_artikel'];
                                   for ($l=1; $l < count($G_dt_artikel); $l++) { 
                                      $wr .= '<br/>'.$G_dt_artikel[$l]['Judul_artikel'];
                                   }
                               }
                               $temp[] = $wr;
                               break;
                            case 6: // Jumlah sitasi
                               $wr = '';
                               if (count($G_dt_artikel) > 0) {
                                   $wr .= $G_dt_artikel[0]['Banyak_artikel'];
                                   for ($l=1; $l < count($G_dt_artikel); $l++) { 
                                      $wr .= '<br/>'.$G_dt_artikel[$l]['Banyak_artikel'];
                                   }
                               }
                               $temp[] = $wr;
                               break;
                            default:
                                # code...
                                break;
                        }
                    }

                    $body[] = $temp;
                }

                $rs['header'] = $header;
                $rs['body'] = $body;
                echo json_encode($rs);    
                break;
            case 'RekognisiDosenKaryaIlmiah':
                $rs = ['header' => [],'body' => [] ];
                $header = [];
                $header[] = ['Name' => 'No','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Nama Dosen','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Rekognisi dan Bukti Pendukung','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Tingkat','rowspan' => 1,'Sub' => ['Wilayah','Nasional','Internasional'],'colspan' => 3 ];
                $header[] = ['Name' => 'Tahun Rekognisi (YYYY)','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Judul Artikel yang Disitasi (Jurnal, Volume, Tahun, Nomor, Halaman)','rowspan' => 2,'Sub' => [],'colspan' => 1 ];
                $header[] = ['Name' => 'Jumlah Sitasi','rowspan' => 2,'Sub' => [],'colspan' => 1 ];

                $body = [];
                $ProdiID = $dataToken['ProdiID'];
                $filterTahun = $dataToken['filterTahun'];
                $sql = 'select * from (
                        select a.NIP,b.Name,a.EntredAt from db_agregator.rekognisi_dosen as a
                                join db_employees.employees as b on a.NIP = b.NIP
                                where b.ProdiID = ? and a.Tahun = ? and a.isApproved = 2
                        UNION
                        select sk.User_create,b.Name,sk.User_create from db_agregator.sitasi_karya as sk
                        join db_employees.employees as b on sk.User_create = b.NIP
                        where b.ProdiID = ? and sk.year = ?
                        ) xx
                        GROUP BY NIP
                        order by EntredAt desc limit 1000
                        ';
                $query=$this->db->query($sql, array($ProdiID,$filterTahun,$ProdiID,$filterTahun))->result_array();
                for ($i=0; $i < count($query); $i++) { 
                  $temp = [];
                    // check rowspan
                    $sql_rekognisi = 'select * from db_agregator.rekognisi_dosen
                                      where NIP = "'.$query[$i]['NIP'].'" and Tahun = '.$filterTahun.' and isApproved = 2
                                    ';
                    $queryRekognisi = $this->db->query($sql_rekognisi,array())->result_array();
                    $rowspan = (count($queryRekognisi) == 0) ? 1 : count($queryRekognisi);
                    $temp = [ 
                      [
                      'rowspan' => $rowspan,
                      'value' => $i+1,
                      ],
                      [
                      'rowspan' => $rowspan,
                      'value' => $query[$i]['Name'],
                      ],
                    ];

                    // Rekognisi , BuktiPendukungName , BuktiPendukungUpload
                      $arr_rekog = [];
                      $Rekognisi =  '';
                      for ($j=0; $j < count($queryRekognisi); $j++) { 
                        $Rekognisi = $queryRekognisi[$j]['Rekognisi'];
                        $BuktiName = $queryRekognisi[$j]['BuktiPendukungName'];
                        $BuktiPendukungUpload = $queryRekognisi[$j]['BuktiPendukungUpload'];
                        $arr_file = (array) json_decode($BuktiPendukungUpload,true);
                        $wr = $Rekognisi;
                        if ($BuktiName != '' && count($arr_file) > 0 ) {
                            $wr .= '<br/>'.$BuktiName.'<br/>'.'<a href="'.base_url().'fileGetAny/Agregator-Aps-'.$arr_file[0].'" target="_blank" class="Fileexist">Attachment</a>';
                        }

                        $arr_rekog[] = $wr;
                      }

                    $temp[] = $arr_rekog;

                    // Wilayah
                    $arr_rekog = [];
                    for ($j=0; $j < count($queryRekognisi); $j++) {
                      $wr = ''; 
                      if ('Wilayah' == $queryRekognisi[$j]['Tingkat']) {
                          $wr = 'V'; 
                      }
                      $arr_rekog[] = $wr;
                    }

                    $temp[] = $arr_rekog;  
                    
                    // Nasional
                    $arr_rekog = [];
                    for ($j=0; $j < count($queryRekognisi); $j++) {
                      $wr = ''; 
                      if ('Nasional' == $queryRekognisi[$j]['Tingkat']) {
                          $wr = 'V'; 
                      }
                      $arr_rekog[] = $wr;
                    }

                    $temp[] = $arr_rekog;

                    // Internasional
                    $arr_rekog = [];
                    for ($j=0; $j < count($queryRekognisi); $j++) {
                      $wr = ''; 
                      if ('Internasional' == $queryRekognisi[$j]['Tingkat']) {
                          $wr = 'V'; 
                      }
                      $arr_rekog[] = $wr;
                    }

                    $temp[] = $arr_rekog;

                    $temp[] = [
                      'rowspan' => $rowspan,
                      'value' => ($Rekognisi == '') ? '' : $filterTahun,
                    ];

                    $sql_dt_artikel = 'select * from db_agregator.sitasi_karya where User_create = "'.$query[$i]['NIP'].'" and year = '.$filterTahun.' ';
                    $G_dt_artikel = $this->db->query($sql_dt_artikel,array())->result_array();

                    // Judul Artikel
                    $wr = '';
                    if (count($G_dt_artikel) > 0) {
                      $wr = '<ul style = "margin-left:-20px;">';
                      for ($l=0; $l < count($G_dt_artikel); $l++) { 
                         $wr .= '<li>'.$G_dt_artikel[$l]['Title'].'</li>';
                      }
                      $wr .= '</ul>';
                    }

                    $temp[] = [
                      'rowspan' => $rowspan,
                      'value' => $wr,
                    ];

                    // Jumlah sitasi
                    $wr = '';
                    if (count($G_dt_artikel) > 0) {
                      $wr = '<ul style = "margin-left:-20px;">';
                      for ($l=0; $l < count($G_dt_artikel); $l++) { 
                         $wr .= '<li>'.$G_dt_artikel[$l]['Citation'].'</li>';
                      }
                      $wr .= '</ul>';
                    }
                    
                    $temp[] = [
                      'rowspan' => $rowspan,
                      'value' => $wr,
                    ];

                    $body[] = $temp;
                }

                $rs['header'] = $header;
                $rs['body'] = $body;
                echo json_encode($rs);    
                break;
            case 'DataDosen':
                $rs = [];
                $ProdiID = $dataToken['ProdiID'];
                $F_SemesterID = function($dataToken)
                {
                    if (array_key_exists('SemesterID', $dataToken)) {
                        $SemesterID = $dataToken['SemesterID'];
                    }
                    else
                    {
                        $G_smt = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
                        $SemesterID = $G_smt[0]['ID'];
                    }

                    return $SemesterID;
                };

                $SemesterID = $F_SemesterID($dataToken);
                // ts berdasarkan semester
                $G_smt = $this->m_master->caribasedprimary('db_academic.semester','ID',$SemesterID);
                $Year = $G_smt[0]['Year'];
                $Year3 = $Year - 2;
                $arr_year = [];
                for ($i=$Year3; $i <= $Year; $i++) { 
                   $arr_year[] = $i;
                }

                $AndWhere = '';
                if (array_key_exists('StatusForlap',$dataToken) ) {
                    if ($dataToken['StatusForlap'] == '%') {
                        $AndWhere = '';
                    }
                    else {
                        $AndWhere = ' AND StatusForlap = "'.$dataToken['StatusForlap'].'" ';
                    }
                }

                $sql = 'select a.*,b.Position as JabatanAkademik from db_employees.employees as a
                    left join db_employees.lecturer_academic_position as b on a.LecturerAcademicPositionID = b.ID  
                     where ( 
                                #SPLIT_STR(a.PositionMain, ".", 2) = 7 or 
                                #SPLIT_STR(a.PositionOther1, ".", 2) = 7 or
                                #SPLIT_STR(a.PositionOther2, ".", 2) = 7 or
                                #SPLIT_STR(a.PositionOther3, ".", 2) = 7

                                SPLIT_STR(a.PositionMain, ".", 1) = 14 or 
                                SPLIT_STR(a.PositionOther1, ".", 1) = 14 or
                                SPLIT_STR(a.PositionOther2, ".", 1) = 14 or
                                SPLIT_STR(a.PositionOther3, ".", 1) = 14
                            ) and StatusForlap  is not null and ProdiID = ? and StatusForlap != "" '.$AndWhere;
                $query=$this->db->query($sql, array($ProdiID))->result_array();
                for ($i=0; $i < count($query); $i++) { 
                    $NIP = $query[$i]['NIP'];
                    $LevelEducationName = function($ID){
                        $rs = '';
                        $G= $this->m_master->caribasedprimary('db_employees.level_education','ID',$ID);
                        if (count($G) > 0) {
                            $rs = $G[0]['Level'];
                        }
                        return $rs;
                    };

                    $LevelEdu = $LevelEducationName($query[$i]['LevelEducationID']);

                    $PendidikanPascaSarjana = function($NIP,$LevelEdu)
                    {
                        $rs = array('PendidikanPascaSarjana' => '' ,'BidKeahlian' => '');
                        $M_q = '(3,4)';
                        if ($LevelEdu == 'S3') {
                           $M_q = '(5,6)';
                        }
                        elseif ($LevelEdu == 'S1') {
                           $M_q = '(1,2)';
                        }
                        $sql = 'select b.Name_University as NameUniversity,c.Name_MajorProgramstudy as Major from db_employees.files as a
                          join db_research.university as b on a.NameUniversity = b.ID
                          join db_employees.major_programstudy_employees as c on c.ID = a.Major
                          where TypeFiles in'.$M_q.' and NIP = "'.$NIP.'"  order by a.ID desc limit 1';
                        // print_r($sql);
                        $query=$this->db->query($sql, array())->result_array();
                        if (count($query) > 0) {
                            $rs['PendidikanPascaSarjana'] = $query[0]['NameUniversity'];
                            $rs['BidKeahlian'] = $query[0]['Major'];
                        }
                        return $rs;
                    };
                    
                    $SertifikatPendidikProfesional = function($NIP){
                        return '';
                    };

                    $MKPSAkreditasi = function($NIP,$ProdiID,$SemesterID){
                        $rs = '<ul style = "margin-left:-20px;" >';
                        $sql = 'select a.ID as SdcID,a.ScheduleID,a.MKID,b.ClassGroup,c.NameEng
                                from db_academic.schedule_details_course as a 
                                join db_academic.schedule as b on a.ScheduleID = b.ID
                                join db_academic.mata_kuliah as c on a.MKID = c.ID
                                where a.ProdiID = ? and b.Coordinator = ? and b.SemesterID = ?
                                group by b.ID,a.MKID    
                                 ';
                        $query=$this->db->query($sql, array($ProdiID,$NIP,$SemesterID))->result_array();
                        if (count($query) > 0) {
                            $rs .= '<li>'.$query[0]['ClassGroup'].' - '.$query[0]['NameEng'].'</li>';
                            for ($i=1; $i < count($query); $i++) { 
                                $rs .= '<li>'.$query[$i]['ClassGroup'].' - '.$query[$i]['NameEng'].'</li>';
                            }
                        }
                        $rs .= '</ul>';
                        return $rs;
                    };

                    // get dt academic except prodi
                    $dtAcademic = function($NIP,$ProdiID,$SemesterID){
                        $rs = array('MK' => '','SKS' => '');
                        $sql = 'select a.ID as SdcID,a.ScheduleID,a.MKID,b.ClassGroup,c.NameEng,d.TotalSKS
                                from db_academic.schedule_details_course as a 
                                join db_academic.schedule as b on a.ScheduleID = b.ID
                                join db_academic.mata_kuliah as c on a.MKID = c.ID
                                join db_academic.curriculum_details as d on a.CDID = d.ID
                                where a.ProdiID != ? and b.Coordinator = ? and b.SemesterID = ?
                                group by b.ID,a.MKID    
                                 ';
                        $query=$this->db->query($sql, array($ProdiID,$NIP,$SemesterID))->result_array();
                        $MK = '<ul style = "margin-left:-20px;" >';
                        // $SKS = '';
                        $SKS = 0;
                        $arr_SKS = ['value' => 0,'data' => ''];
                        $dataSKS = [];
                        if (count($query) > 0) {
                            $MK .= '<li>'.$query[0]['ClassGroup'].' - '.$query[0]['NameEng'].'</li>';
                            $dataSKS[] =array('data' => $query[0]['ClassGroup'].' - '.$query[0]['NameEng'],'SKS' => $query[0]['TotalSKS']) ;
                            $SKS += $query[0]['TotalSKS'];
                            for ($i=1; $i < count($query); $i++) { 
                                $MK .= '<li>'.$query[$i]['ClassGroup'].' - '.$query[$i]['NameEng'].'</li>';
                                $dataSKS[] =array('data' => $query[$i]['ClassGroup'].' - '.$query[$i]['NameEng'],'SKS' => $query[$i]['TotalSKS']) ;
                                // $SKS .= ', '.$query[$i]['TotalSKS'];
                                $SKS += $query[$i]['TotalSKS'];
                            }

                            $MK .= '</ul>';
                        }
                        // gabung untuk bobot kredit
                        $sql2 = 'select a.ID as SdcID,a.ScheduleID,a.MKID,b.ClassGroup,c.NameEng,d.TotalSKS
                                from db_academic.schedule_details_course as a 
                                join db_academic.schedule as b on a.ScheduleID = b.ID
                                join db_academic.mata_kuliah as c on a.MKID = c.ID
                                join db_academic.curriculum_details as d on a.CDID = d.ID
                                where a.ProdiID = ? and b.Coordinator = ? and b.SemesterID = ?
                                group by b.ID,a.MKID    
                                 ';
                        $query2=$this->db->query($sql2, array($ProdiID,$NIP,$SemesterID))->result_array();
                        if (count($query2) > 0) {
                            $SKS += $query2[0]['TotalSKS'];
                            $dataSKS[] =array('data' => $query2[0]['ClassGroup'].' - '.$query2[0]['NameEng'],'SKS' => $query2[0]['TotalSKS']) ;
                            for ($i=1; $i < count($query2); $i++) { 
                                $SKS += $query2[$i]['TotalSKS'];
                                $dataSKS[] =array('data' => $query2[$i]['ClassGroup'].' - '.$query2[$i]['NameEng'],'SKS' => $query2[$i]['TotalSKS']) ;
                            }
                        }
                        $arr_SKS = ['value' => $SKS,'data' =>  $this->jwt->encode($dataSKS,"UAP)(*")];
                        $rs['MK'] = $MK;
                        $rs['SKS'] = $arr_SKS;
                        return $rs;
                    };

                    $dtAcademic_get = $dtAcademic($NIP,$ProdiID,$SemesterID);

                    // get jumlah mahasiswa dengan tiga ts terakhir
                    $JMLDibimbingBy_PS = array();
                    $JMLDibimbingBy_LainPS = array();
                    $SumBimbingan_tahun = 0;
                    $SumProgram_tahun = 0;
                    for ($k=0; $k < count($arr_year); $k++) { 
                        $Y = $arr_year[$k];
                        $sql_ = 'select a.NPM,b.Name from db_academic.mentor_academic as a
                                join db_academic.auth_students as b on a.NPM = b.NPM
                                where a.Status = ? and a.ProdiID = ? and a.Year = ? and a.NIP = ?
                                    ';
                        $query_=$this->db->query($sql_, array("1",$ProdiID,$Y,$NIP))->result_array();
                        $tot = count($query_);
                        $SumBimbingan_tahun += $tot;
                        $temp = [
                            'tot' => $tot,
                            'Year' => $Y,
                            'data' =>  $this->jwt->encode($query_,"UAP)(*"),
                        ];
                        $JMLDibimbingBy_PS[] = $temp;

                        $sql_lain = 'select a.NPM,b.Name from db_academic.mentor_academic as a
                                join db_academic.auth_students as b on a.NPM = b.NPM
                                where a.Status = ? and a.ProdiID != ? and a.Year = ? and a.NIP = ?
                                    ';
                        $query_lain=$this->db->query($sql_lain, array("1",$ProdiID,$Y,$NIP))->result_array();

                        $tot1 = count($query_lain);
                        $c_tot_all = $tot + $tot1;
                        $SumProgram_tahun += $c_tot_all;
                        $temp = [
                            'tot' => $tot1,
                            'Year' => $Y,
                            'data' =>  $this->jwt->encode($query_lain,"UAP)(*"),
                        ];

                        $JMLDibimbingBy_LainPS[] = $temp;
                    }
                    $C_year = count($arr_year);
                    $C_year2 = $C_year * 2;
                    $rata2BimBingan = $SumBimbingan_tahun / $C_year;
                    $rata2BimBinganAll = $SumProgram_tahun / $C_year2;

                    $G_master_academic = $PendidikanPascaSarjana($NIP,$LevelEdu);
                    $G_master_academic2 = $PendidikanPascaSarjana($NIP,'S2');
                    $G_master_academic3 = $PendidikanPascaSarjana($NIP,'S3');
                   
                    $temp = ['No'  =>  ($i+1),
                             'NameDosen' => $query[$i]['Name'],
                             'NIDN' => $query[$i]['NIDN'],
                             'NIDK' => $query[$i]['NIDK'],
                             'PendidikanPascaSarjana' => $G_master_academic2['PendidikanPascaSarjana'],
                             'PendidikanPascaSarjana2' => $G_master_academic3['PendidikanPascaSarjana'],
                             'PerusahaanIndustri' => '',
                             // 'PendidikanTertinggi' => $LevelEducationName($query[$i]['LevelEducationID']),
                             'PendidikanTertinggi' => $LevelEdu,
                             'BidKeahlian' => $G_master_academic['BidKeahlian'],
                             'KesesuaianKompetensiIntiPS' => '',
                             'JabatanAkademik' => $query[$i]['JabatanAkademik'],
                             'SertifikatPendidikProfesional' => $SertifikatPendidikProfesional($NIP),
                             'SertifikatPendidikProfesi' => '',
                             'MKPSAkreditasi' => $MKPSAkreditasi($NIP,$ProdiID,$SemesterID),
                             'KesesuaianBidKeahlian' => '',
                             'MKPS_lain' => $dtAcademic_get['MK'],
                             'BobotKredit_lain' => $dtAcademic_get['SKS'],
                             'JMLDibimbingBy_PS' => $JMLDibimbingBy_PS,
                             'JMLDibimbingBy_LainPS' => $JMLDibimbingBy_LainPS,
                             'rata2BimBingan' => $rata2BimBingan,
                             'rata2BimBinganAll' => $rata2BimBinganAll,
                             // 'SumBimbingan_tahun' => $SumBimbingan_tahun,
                             // 'SumProgram_tahun' => $SumProgram_tahun,
                          ];
                    $rs[] = $temp;      
                }
                echo json_encode($rs);    
                break;
                case 'EWMP':
                    $rs = [];
                    $ProdiID = $dataToken['ProdiID'];
                    $FilterTahun = $dataToken['FilterTahun'];
                    // get SemesterID in dalam tahun
                    $G_yearSMT = $this->m_master->caribasedprimary('db_academic.semester','Year',$FilterTahun);
                    $TahunFilter = [];
                    for ($i=0; $i < count($G_yearSMT); $i++) { 
                        $TahunFilter[] = $G_yearSMT[$i]['ID'];
                    }
                    
                    $TahunFilter = implode(',',$TahunFilter);

                    // Dosen tetap dengan status pada db_employees.employees StatusForlap = "1" dan "2"
                    $sqlDosen = 'select * from db_employees.employees where ProdiID = ? and StatusForlap in ("1","2") ';
                    $queryDosen=$this->db->query($sqlDosen, array($ProdiID))->result_array();
                    for ($i=0; $i < count($queryDosen); $i++) {
                        $temp = [];
                        $No = $i+1;
                        $temp[] = $No; 
                        $Name = $queryDosen[$i]['Name'];
                        $NIP = $queryDosen[$i]['NIP'];
                        $temp[] = $Name;
                        $temp[] = ''; // DTPS
                        $arr_get = [];
                        // total ps akreditasi
                        $sqlPSAkreditasi = '
                                            select SUM(Credit) as TotalCredit from (
                                                select a.ID as SdcID,a.ScheduleID,a.MKID,b.ClassGroup,c.NameEng,sd.Credit
                                                from db_academic.schedule_details_course as a 
                                                join db_academic.schedule as b on a.ScheduleID = b.ID
                                                join db_academic.mata_kuliah as c on a.MKID = c.ID
                                                join db_academic.schedule_details as sd on sd.ScheduleID = b.ID
                                                where a.ProdiID = ? and b.Coordinator = ? and b.SemesterID in('.$TahunFilter.')
                                                group by b.ID,a.MKID
                                            )xx   
                                            ';
                        $queryPSAkreditasi=$this->db->query($sqlPSAkreditasi, array($ProdiID,$NIP))->result_array();

                        // get mentor utama sks
                          $arr_get_mhs_by_mentor = [];
                          $sql_get_mhs_by_mentor = 'select ats.NPM,ats.Name as Nama_Mahasiswa,ats.Year from db_academic.auth_students as ats
                                                    join db_employees.employees as emp on ats.MentorFP1 = emp.NIP
                                                    where ats.MentorFP1 = ? and emp.ProdiID = ? and ats.ProdiID = ?
                                                    ';
                          $query_get_mhs_by_mentor=$this->db->query($sql_get_mhs_by_mentor, array($NIP,$ProdiID,$ProdiID))->result_array();
                          for ($z=0; $z < count($query_get_mhs_by_mentor); $z++) { 
                            $ta_db = 'ta_'.$query_get_mhs_by_mentor[$z]['Year'];
                            $NPMByMentor = $query_get_mhs_by_mentor[$z]['NPM'];
                            $sql_stp = 'select stp.NPM from '.$ta_db.'.study_planning as stp
                                        join db_academic.mata_kuliah as mtk on stp.MKID = mtk.ID
                                        where stp.NPM = "'.$NPMByMentor.'" and stp.SemesterID in('.$TahunFilter.')
                                              and mtk.Yudisium = "1"
                            ';
                            $querySTP = $this->db->query($sql_stp,array())->result_array();
                            if (count($querySTP) > 0) {
                              // hasil seleksi
                              $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                              // utama field SKS
                              $ID_mentor_type_sks =  $G_prodi[0]['ID_mentor_type_sks'];
                              $G_mentor_type_sks = $this->m_master->caribasedprimary('db_rektorat.mentor_type_sks','ID',$ID_mentor_type_sks);
                              if (count($G_mentor_type_sks) > 0) {
                                $arr_get_mhs_by_mentor[] = array(
                                    'NPM' => $NPMByMentor,
                                    'Nama_Mahasiswa' => $query_get_mhs_by_mentor[$z]['Nama_Mahasiswa'],
                                    'SKS' => $G_mentor_type_sks[0]['SKS'],
                                );
                              }
                            }
                          }
                        // end get mentor utama sks

                        // get mentor pendamping sks
                          $arr_get_mhs_by_mentor_pendamping = [];
                          $sql_get_mhs_by_mentor = 'select ats.NPM,ats.Name as Nama_Mahasiswa,ats.Year from db_academic.auth_students as ats
                                                    join db_employees.employees as emp on ats.MentorFP1 = emp.NIP
                                                    where ats.MentorFP2 = ? and emp.ProdiID = ? and ats.ProdiID = ?
                                                    ';
                          $query_get_mhs_by_mentor=$this->db->query($sql_get_mhs_by_mentor, array($NIP,$ProdiID,$ProdiID))->result_array();
                          for ($z=0; $z < count($query_get_mhs_by_mentor); $z++) { 
                            $ta_db = 'ta_'.$query_get_mhs_by_mentor[$z]['Year'];
                            $NPMByMentor = $query_get_mhs_by_mentor[$z]['NPM'];
                            $sql_stp = 'select stp.NPM from '.$ta_db.'.study_planning as stp
                                        join db_academic.mata_kuliah as mtk on stp.MKID = mtk.ID
                                        where stp.NPM = "'.$NPMByMentor.'" and stp.SemesterID in('.$TahunFilter.')
                                              and mtk.Yudisium = "1"
                            ';
                            $querySTP = $this->db->query($sql_stp,array())->result_array();
                            if (count($querySTP) > 0) {
                              // hasil seleksi
                              $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                              // utama field SKS
                              $ID_mentor_type_sks =  $G_prodi[0]['ID_mentor_type_sks'];
                              $G_mentor_type_sks = $this->m_master->caribasedprimary('db_rektorat.mentor_type_sks','ID',$ID_mentor_type_sks);
                              if (count($G_mentor_type_sks) > 0) {
                                $arr_get_mhs_by_mentor_pendamping[] = array(
                                    'NPM' => $NPMByMentor,
                                    'Nama_Mahasiswa' => $query_get_mhs_by_mentor[$z]['Nama_Mahasiswa'],
                                    'SKS' => $G_mentor_type_sks[0]['SKSPendamping'],
                                );
                              }
                            }
                          }
                        // end get mentor pendamping sks

                        // get data 
                        $sqlDataAkreditasi = 'select a.ID as SdcID,a.ScheduleID,a.MKID,b.ClassGroup,c.NameEng,sd.Credit
                                                from db_academic.schedule_details_course as a 
                                                join db_academic.schedule as b on a.ScheduleID = b.ID
                                                join db_academic.mata_kuliah as c on a.MKID = c.ID
                                                join db_academic.schedule_details as sd on sd.ScheduleID = b.ID
                                                where a.ProdiID = ? and b.Coordinator = ? and b.SemesterID in('.$TahunFilter.')
                                                group by b.ID,a.MKID';
                        $queryDataPSAkreditasi=$this->db->query($sqlDataAkreditasi, array($ProdiID,$NIP))->result_array(); 
                        // get sum count mentor utama sks
                        $tot = ($queryPSAkreditasi[0]['TotalCredit'] == null ) ? 0 : $queryPSAkreditasi[0]['TotalCredit'];
                        for ($z=0; $z < count($arr_get_mhs_by_mentor); $z++) { 
                          $tot += $arr_get_mhs_by_mentor[$z]['SKS'];
                          $arr_adding = [
                            'SdcID' => '',
                            'ScheduleID' => '',
                            'MKID' => '',
                            'ClassGroup' => $arr_get_mhs_by_mentor[$z]['NPM'],
                            'NameEng' => $arr_get_mhs_by_mentor[$z]['Nama_Mahasiswa'],
                            'Credit' => $arr_get_mhs_by_mentor[$z]['SKS'],
                          ];

                          $queryDataPSAkreditasi[] = $arr_adding;
                        }

                        for ($z=0; $z < count($arr_get_mhs_by_mentor_pendamping); $z++) { 
                          $tot += $arr_get_mhs_by_mentor_pendamping[$z]['SKS'];
                          $arr_adding = [
                            'SdcID' => '',
                            'ScheduleID' => '',
                            'MKID' => '',
                            'ClassGroup' => $arr_get_mhs_by_mentor_pendamping[$z]['NPM'],
                            'NameEng' => $arr_get_mhs_by_mentor_pendamping[$z]['Nama_Mahasiswa'],
                            'Credit' => $arr_get_mhs_by_mentor_pendamping[$z]['SKS'],
                          ];

                          $queryDataPSAkreditasi[] = $arr_adding;
                        }

                        // encode token
                        $token = $this->jwt->encode($queryDataPSAkreditasi,"UAP)(*");
                        $temp[] = array('count' => $tot ,'data' => $token);  
                        $arr_get[] = $tot;

                        

                        // Total PS lain di dalam pt
                        $sqlPSLainDalamPT = '
                                            select SUM(Credit) as TotalCredit from (
                                                select a.ID as SdcID,a.ScheduleID,a.MKID,b.ClassGroup,c.NameEng,sd.Credit
                                                from db_academic.schedule_details_course as a 
                                                join db_academic.schedule as b on a.ScheduleID = b.ID
                                                join db_academic.mata_kuliah as c on a.MKID = c.ID
                                                join db_academic.schedule_details as sd on sd.ScheduleID = b.ID
                                                where a.ProdiID != ? and b.Coordinator = ? and b.SemesterID in('.$TahunFilter.')
                                                group by b.ID,a.MKID
                                            )xx   
                                            ';
                        $queryPSLainDalamPT=$this->db->query($sqlPSLainDalamPT, array($ProdiID,$NIP))->result_array();
                        $tot = ($queryPSLainDalamPT[0]['TotalCredit'] == null) ? 0 : $queryPSLainDalamPT[0]['TotalCredit'] ;

                        // get mentor utama sks
                          $arr_get_mhs_by_mentor = [];
                          $sql_get_mhs_by_mentor = 'select ats.NPM,ats.Name as Nama_Mahasiswa,ats.Year from db_academic.auth_students as ats
                                                    join db_employees.employees as emp on ats.MentorFP1 = emp.NIP
                                                    where ats.MentorFP1 = ? and emp.ProdiID = ? and ats.ProdiID != ?
                                                    ';
                          $query_get_mhs_by_mentor=$this->db->query($sql_get_mhs_by_mentor, array($NIP,$ProdiID,$ProdiID))->result_array();
                          for ($z=0; $z < count($query_get_mhs_by_mentor); $z++) { 
                            $ta_db = 'ta_'.$query_get_mhs_by_mentor[$z]['Year'];
                            $NPMByMentor = $query_get_mhs_by_mentor[$z]['NPM'];
                            $sql_stp = 'select stp.NPM from '.$ta_db.'.study_planning as stp
                                        join db_academic.mata_kuliah as mtk on stp.MKID = mtk.ID
                                        where stp.NPM = "'.$NPMByMentor.'" and stp.SemesterID in('.$TahunFilter.')
                                              and mtk.Yudisium = "1"
                            ';
                            $querySTP = $this->db->query($sql_stp,array())->result_array();
                            if (count($querySTP) > 0) {
                              // hasil seleksi
                              $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                              // utama field SKS
                              $ID_mentor_type_sks =  $G_prodi[0]['ID_mentor_type_sks'];
                              $G_mentor_type_sks = $this->m_master->caribasedprimary('db_rektorat.mentor_type_sks','ID',$ID_mentor_type_sks);
                              if (count($G_mentor_type_sks) > 0) {
                                $arr_get_mhs_by_mentor[] = array(
                                    'NPM' => $NPMByMentor,
                                    'Nama_Mahasiswa' => $query_get_mhs_by_mentor[$z]['Nama_Mahasiswa'],
                                    'SKS' => $G_mentor_type_sks[0]['SKS'],
                                );
                              }
                            }
                          }
                        // end get mentor utama sks

                        // get mentor pendamping sks
                          $arr_get_mhs_by_mentor_pendamping = [];
                          $sql_get_mhs_by_mentor = 'select ats.NPM,ats.Name as Nama_Mahasiswa,ats.Year from db_academic.auth_students as ats
                                                    join db_employees.employees as emp on ats.MentorFP1 = emp.NIP
                                                    where ats.MentorFP2 = ? and emp.ProdiID = ? and ats.ProdiID != ?
                                                    ';
                          $query_get_mhs_by_mentor=$this->db->query($sql_get_mhs_by_mentor, array($NIP,$ProdiID,$ProdiID))->result_array();
                          for ($z=0; $z < count($query_get_mhs_by_mentor); $z++) { 
                            $ta_db = 'ta_'.$query_get_mhs_by_mentor[$z]['Year'];
                            $NPMByMentor = $query_get_mhs_by_mentor[$z]['NPM'];
                            $sql_stp = 'select stp.NPM from '.$ta_db.'.study_planning as stp
                                        join db_academic.mata_kuliah as mtk on stp.MKID = mtk.ID
                                        where stp.NPM = "'.$NPMByMentor.'" and stp.SemesterID in('.$TahunFilter.')
                                              and mtk.Yudisium = "1"
                            ';
                            $querySTP = $this->db->query($sql_stp,array())->result_array();
                            if (count($querySTP) > 0) {
                              // hasil seleksi
                              $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                              // utama field SKS
                              $ID_mentor_type_sks =  $G_prodi[0]['ID_mentor_type_sks'];
                              $G_mentor_type_sks = $this->m_master->caribasedprimary('db_rektorat.mentor_type_sks','ID',$ID_mentor_type_sks);
                              if (count($G_mentor_type_sks) > 0) {
                                $arr_get_mhs_by_mentor_pendamping[] = array(
                                    'NPM' => $NPMByMentor,
                                    'Nama_Mahasiswa' => $query_get_mhs_by_mentor[$z]['Nama_Mahasiswa'],
                                    'SKS' => $G_mentor_type_sks[0]['SKSPendamping'],
                                );
                              }
                            }
                          }
                        // end get mentor pendamping sks

                        $sqlDataPSLainDalamPT = '
                        select a.ID as SdcID,a.ScheduleID,a.MKID,b.ClassGroup,c.NameEng,sd.Credit
                        from db_academic.schedule_details_course as a 
                        join db_academic.schedule as b on a.ScheduleID = b.ID
                        join db_academic.mata_kuliah as c on a.MKID = c.ID
                        join db_academic.schedule_details as sd on sd.ScheduleID = b.ID
                        where a.ProdiID != ? and b.Coordinator = ? and b.SemesterID in('.$TahunFilter.')
                        group by b.ID,a.MKID
                        ';
                        $queryDataPSLainDalamPT=$this->db->query($sqlDataPSLainDalamPT, array($ProdiID,$NIP))->result_array();

                        for ($z=0; $z < count($arr_get_mhs_by_mentor); $z++) { 
                          $tot += $arr_get_mhs_by_mentor[$z]['SKS'];
                          $arr_adding = [
                            'SdcID' => '',
                            'ScheduleID' => '',
                            'MKID' => '',
                            'ClassGroup' => $arr_get_mhs_by_mentor[$z]['NPM'],
                            'NameEng' => $arr_get_mhs_by_mentor[$z]['Nama_Mahasiswa'],
                            'Credit' => $arr_get_mhs_by_mentor[$z]['SKS'],
                          ];

                          $queryDataPSLainDalamPT[] = $arr_adding;
                        }

                        for ($z=0; $z < count($arr_get_mhs_by_mentor_pendamping); $z++) { 
                          $tot += $arr_get_mhs_by_mentor_pendamping[$z]['SKS'];
                          $arr_adding = [
                            'SdcID' => '',
                            'ScheduleID' => '',
                            'MKID' => '',
                            'ClassGroup' => $arr_get_mhs_by_mentor_pendamping[$z]['NPM'],
                            'NameEng' => $arr_get_mhs_by_mentor_pendamping[$z]['Nama_Mahasiswa'],
                            'Credit' => $arr_get_mhs_by_mentor_pendamping[$z]['SKS'],
                          ];

                          $queryDataPSLainDalamPT[] = $arr_adding;
                        }

                        // encode token
                        $token = $this->jwt->encode($queryDataPSLainDalamPT,"UAP)(*");
                        $temp[] = array('count' => $tot ,'data' => $token);
                        $arr_get[] = $tot;                        

                        $temp[] = 0; // PS lain di luar PT
                        $arr_get[] = 0;  

                        // Penelitian 
                        //Note : Convert to sks untuk mendapatkan satu penelitian
                        // $sqlPenelitian = 'select *,1 as Credit from db_research.litabmas where ID_thn_laks = ? and NIP = ? '; 
                        $sqlPenelitian = 'select a.Judul,jp.Nm_jns_pub,Year(a.Tgl_terbit) as Year,a.Ket,b.NIP,b.Name as NameDosen,jp.SKS as Credit
                          from db_research.publikasi as a
                          join db_research.jenis_publikasi as jp on jp.ID_jns_pub = a.ID_jns_pub
                          join db_employees.employees as b on a.NIP = b.NIP
                          where Year(a.Tgl_terbit) = ? and a.NIP = ?
                          UNION
                          select a.Judul,jp.Nm_jns_pub,Year(a.Tgl_terbit) as Year,a.Ket,d.NIP,d.Name as NameDosen,jp.SKS as Credit
                          from db_research.publikasi as a 
                          join db_research.jenis_publikasi as jp on jp.ID_jns_pub = a.ID_jns_pub
                          join db_research.list_anggota_publikasi as b on a.ID_publikasi = b.ID_publikasi
                          join db_research.master_anggota_publikasi as c on b.ID = c.ID
                          join db_employees.employees as d on c.NIP = d.NIP
                           where Year(a.Tgl_terbit) = ? and d.NIP = ?
                           '; 
                        $queryPenelitian =$this->db->query($sqlPenelitian, array($FilterTahun,$NIP,$FilterTahun,$NIP))->result_array();
                        // $tot = count($queryPenelitian);
                        $tot = 0;
                        for ($z=0; $z < count($queryPenelitian); $z++) { 
                          $tot += $queryPenelitian[$z]['Credit'];
                        }
                        // encode token
                        $token = $this->jwt->encode($queryPenelitian,"UAP)(*");
                        $temp[] = array('count' => $tot ,'data' => $token); 
                        $arr_get[] = $tot;                       
                        // End Penelitian                        
                        
                        // PKM Note : Convert to sks untuk mendapatkan satu PKM
                        // $sqlPKM = 'select *,1 as Credit from db_research.pengabdian_masyarakat where ID_thn_laks = ? and NIP = ? '; 
                        $sqlPKM = 'select a.*,1 as Credit from db_research.pengabdian_masyarakat as a 
                                  join db_research.list_anggota_pkm as b on a.ID_PKM = b.ID_PKM
                                  join db_research.master_anggota_pkm as c on b.ID_anggota = c.ID
                                  where a.ID_thn_laks = ? and c.NIP = ? '; 
                        $queryPKM =$this->db->query($sqlPKM, array($FilterTahun,$NIP))->result_array();
                        // encode token
                        $tot = count($queryPKM);
                        $token = $this->jwt->encode($queryPKM,"UAP)(*");
                        $temp[] = array('count' => $tot ,'data' => $token); 
                        $arr_get[] = $tot; 
                        // End PKM

                        // tugas tambahan
                        $SqlSumSKSTugas = 'select SUM(SKS) as TotalCredit from (
                                    select SKS from db_rektorat.tugas_tambahan
                                    where NIP = ? and SemesterID in('.$TahunFilter.')
                        )xx
                            ';
                        $querySumSKSTugas =$this->db->query($SqlSumSKSTugas, array($NIP))->result_array();    

                        $SqlDataSKSTugas = 'select a.*,b.Position,b.Description,c.Name as NameSemester,"'.$Name.'" as NameEmployee from
                                            db_rektorat.tugas_tambahan as a 
                                            join db_employees.position as b on a.PositionID = b.ID
                                            join db_academic.semester as c on a.SemesterID = c.ID
                                            where a.NIP = ? and a.SemesterID in('.$TahunFilter.')
                                            ';
                        $queryDataSKSTugas =$this->db->query($SqlDataSKSTugas, array($NIP))->result_array();
                         // encode token
                         $tot = ($querySumSKSTugas[0]['TotalCredit'] == null ) ? 0 : $querySumSKSTugas[0]['TotalCredit']; 
                         $token = $this->jwt->encode($queryDataSKSTugas,"UAP)(*");
                         $temp[] = array('count' => $tot ,'data' => $token);
                         $arr_get[] = $tot;     
                         // End Tugas Tambahan       

                        // Jumlah SKS
                        $temp[] = array_sum($arr_get);

                        // rata-rata per semester
                        $temp[] = array_sum($arr_get)/count($arr_get);

                        $rs[] = $temp;
                    }
                    echo json_encode($rs);
                break;
            case 'produk_jasa_dtps' :
               $AddWhere = '';
               $ProdiID = '';
               $Year = '';
               if (array_key_exists('ProdiID', $dataToken)) {
                  $P = $dataToken['ProdiID'];
                  $P = explode('.', $P);
                  $ProdiID = $P[0];
                  $WhereOrAnd = ($AddWhere != '' && $AddWhere  != null) ? ' and' : ' where';
                   $AddWhere .= $WhereOrAnd.' b.ProdiID ='.$ProdiID; 
                } 
               if (array_key_exists('Year', $dataToken)) {
                  $P = $dataToken['ProdiID'];
                  $P = explode('.', $P);
                  $ProdiID = $P[0];
                  $Year = $dataToken['Year'];
                  $WhereOrAnd = ($AddWhere != '' && $AddWhere  != null) ? ' and' : ' where';
                  $AddWhere .= $WhereOrAnd.' a.Year ='.$Year;  
                }
               
               $sql  = 'select a.*,b.Name,b.ProdiID,b.NIP
                       from db_agregator.produk_jasa as a 
                       join db_employees.employees as b on a.Updated_by = b.NIP
                       '.$AddWhere.'
               ';

               $query = $this->db->query($sql,array())->result_array();
               $data = array();
               for ($i=0; $i < count($query); $i++) { 
                 $nestedData = array();
                 $row = $query[$i]; 
                 $nestedData[] = $i+1;
                 $nestedData[] = $row['NIP'].'-'.$row['Name'];
                 $nestedData[] = $row['NamaProdukJasa'];
                 $nestedData[] = $row['DeskripsiProdukJasa'];
                 $UploadBukti =  '';
                 if ($row['UploadBukti'] != '' && $row['UploadBukti'] != null) {
                   $UP = json_decode($row['UploadBukti'],true);
                   if (count($UP) > 0) {
                      $UploadBukti = '<br/><a href="'.url_sign_in_lecturers.'uploads/produk_jasa/'.$UP[0].'" target = "_blank">Attachment</a>';
                   }
                 }
                 $nestedData[] = $row['Bukti'].$UploadBukti;
                 $data[] = $nestedData;
               }

               $json_data = array(
                   "draw"            => intval( 0 ),
                   "recordsTotal"    => intval(count($query)),
                   "recordsFiltered" => intval( count($query) ),
                   "data"            => $data
               );
               echo json_encode($json_data);
               break;
            case 'luaran_penelitan_dtps' :
                $P = $dataToken['ProdiID'];
                $P = explode('.', $P);
                $ProdiID = $P[0];
                $arr_ID_kat_capaian = [3,7,1,4];
                $rs = [];
                for ($i=0; $i < count($arr_ID_kat_capaian); $i++) {
                  $arr_group = [];
                  $get_kat_capaian = $this->m_master->caribasedprimary('db_research.kategori_capaian_luaran','ID_kat_capaian',$arr_ID_kat_capaian[$i]);
                  $Nm_kat_capaian = $get_kat_capaian[0]['Nm_kat_capaian'];
                  $arr_group = ['Name' => $Nm_kat_capaian,'Data' => [] ];
                  $row = [];
                  $nestedData = array();
                  $nestedData[] = ['text' => $this->m_master->romawiNumber($i+1),'colspan' => 1,'style' => '"font-weight:600;background-color: lightyellow;"'] ;
                  $nestedData[] = ['text' => $Nm_kat_capaian ,'colspan' => 4,'style' => '"font-weight:600;background-color: lightyellow;"'];
                  // $nestedData[] = ['text' => '' ,'colspan' => 0,'style' => '""'];
                  // $nestedData[] = ['text' => '' ,'colspan' => 0,'style' => '""'];
                  $row[] = $nestedData;
                  $sql = 'select a.Judul,Year(a.Tgl_terbit) as Year,a.Ket,b.NIP,b.Name as NameDosen
                          from db_research.publikasi as a 
                          join db_employees.employees as b on a.NIP = b.NIP
                          where b.ProdiID = '.$ProdiID.' and a.ID_kat_capaian = '.$arr_ID_kat_capaian[$i].'
                          UNION
                          select a.Judul,Year(a.Tgl_terbit) as Year,a.Ket,d.NIP,d.Name as NameDosen
                          from db_research.publikasi as a 
                          join db_research.list_anggota_publikasi as b on a.ID_publikasi = b.ID_publikasi
                          join db_research.master_anggota_publikasi as c on b.ID_anggota = c.ID
                           '.$this->m_research->QueryUserReserachJoin('c.ID_user').'
                          join db_employees.employees as d on usrr.NIPorNPM = d.NIP
                           where d.ProdiID = '.$ProdiID.' and a.ID_kat_capaian = '.$arr_ID_kat_capaian[$i].'
                           and c.Type_anggota = "DSN"
                         ';
                  $query = $this->db->query($sql,array())->result_array();
                  for ($j=0; $j < count($query); $j++) { 
                    $nestedData = array();
                    $nestedData[] = ['text' => $j+1 ,'colspan' => 1,'style' => '"text-align:right"'];
                    $nestedData[] = ['text' => $query[$j]['Judul'] ,'colspan' => 1,'style' => '""'];
                    $nestedData[] = ['text' => $query[$j]['NIP'].' - '.$query[$j]['NameDosen'] ,'colspan' => 1,'style' => '""'];
                    $nestedData[] = ['text' => $query[$j]['Year'] ,'colspan' => 1,'style' => '""'];
                    $nestedData[] = ['text' => $query[$j]['Ket'] ,'colspan' => 1,'style' => '""'];
                    $row[] = $nestedData;
                  }

                  $arr_group['Data'] = $row;
                  $rs[] = $arr_group;
                }

                echo json_encode($rs);
                break;
            case 'Publikasi_ilmiah_dtps' :
                $rs = [];
                $P = $dataToken['ProdiID'];
                $P = explode('.', $P);
                $ProdiID = $P[0];

                $arr_ts = json_decode(json_encode($dataToken['arr_ts']),true);
                $G_jns_forlap_publikasi = $this->m_master->showData_array('db_research.jenis_forlap_publikasi');
                for ($i=0; $i < count($G_jns_forlap_publikasi); $i++) {
                  $data = []; 
                  $data[] = $i+1;
                  $data[] = $G_jns_forlap_publikasi[$i]['NamaForlap_publikasi'];
                  $jumlah = 0;
                  $ID_forlap_publikasi = $G_jns_forlap_publikasi[$i]['ID'];
                  for ($j=0; $j < count($arr_ts) ; $j++) { 
                    $sql = 'select a.ID_publikasi,Judul,Tgl_terbit,a.NIP,b.Name as NameDosen from db_research.publikasi as a
                    join db_employees.employees as b on a.NIP = b.NIP
                            where Year(a.Tgl_terbit) = '.$arr_ts[$j].' and a.ID_forlap_publikasi = "'.$ID_forlap_publikasi.'"
                            and b.ProdiID = '.$ProdiID.'
                      ';
                    $query = $this->db->query($sql,array())->result_array();
                    for ($lt=0; $lt < count($query); $lt++) { 
                      $sqlGetAnggota = 'select usrr.Nama from db_research.list_anggota_publikasi as a
                                        join db_research.master_anggota_publikasi as b on a.ID_anggota = b.ID
                                        '.$this->m_research->QueryUserReserachJoin('b.ID_user').'
                                        where a.ID_publikasi = '.$query[$lt]['ID_publikasi'].' and b.Type_anggota  != "MHS"
                       ';
                       $dataAnggota = $this->db->query($sqlGetAnggota,array())->result_array();
                       $query[$lt]['dataAnggota'] = $dataAnggota;
                    }
                    $tot = count($query);
                    $data[] =  array('token' => $this->jwt->encode($query,"UAP)(*"),'total' => $tot) ;
                    $jumlah += $tot;
                  }
                  $data[] = $jumlah;
                  $rs[] = $data;
                }
                echo json_encode($rs);
                break;
            default:
                echo '{"status":"999","message":"Not Authorize"}'; 
                break;
        }
    }

    public function APS_CrudAgregatorTB7_()
    {
      $dataToken = $this->data['dataToken'];
      $mode = $dataToken['mode'];
      switch ($mode) {
        case 'pkm_melibatkan_mhs':
          $rs = [];
          $P = $dataToken['ProdiID'];
          $P = explode('.', $P);
          $ProdiID = $P[0];
          $sql = 'select a.ID_PKM,b.Name as NamaDosen,"" as RoadMap,a.Judul_PKM,a.ID_thn_laks, 
                      (select COUNT(*) from db_research.list_anggota_pkm as x
                        join db_research.master_anggota_pkm as y on x.ID_anggota = y.ID
                        where x.ID_PKM = a.ID_PKM and y.Type_anggota = "MHS") AS totalstudent 
                  from db_research.pengabdian_masyarakat as a
                  join db_employees.employees as b on a.NIP = b.NIP
                  #join db_research.list_anggota_pkm as c on c.ID_PKM = a.ID_PKM
                  #join db_research.master_anggota_pkm as d on d.ID = c.ID_anggota
                  where b.ProdiID = '.$ProdiID.'
                  group by a.NIP, a.ID_PKM
                 ';
          $query = $this->db->query($sql,array())->result_array();
          $data = [];
          for ($i=0; $i < count($query); $i++) { 
            if (!empty($query[$i]['totalstudent'])){
            
            $nestedData = [];
            $row = $query[$i];
            $nestedData[] = $i+1;
            $nestedData[] = $row['NamaDosen'];
            $nestedData[] = $row['RoadMap'];
            $sql_MHS = 'select usrr.Nama as Name_mahasiswa from db_research.list_anggota_pkm as a
                         join db_research.master_anggota_pkm as b on a.ID_anggota = b.ID
                         '.$this->m_research->QueryUserReserachJoin('b.ID_user').'
                         where a.ID_PKM = '.$query[$i]['ID_PKM'].' and b.Type_anggota = "MHS"
                                          ';
            $q_MHS = $this->db->query($sql_MHS,array())->result_array();
            $MHSName = '<ul style = "margin-left:-20px;">';
            for ($j=0; $j < count($q_MHS); $j++) { 
              $MHSName .=  '<li>'.$q_MHS[$j]['Name_mahasiswa'].'</li>';
            }

            $MHSName .= '</ul>';      

            // $nestedData[] = $row['Name_mahasiswa'];
            $nestedData[] = $MHSName;
            $nestedData[] = $row['Judul_PKM'];
            $nestedData[] = $row['ID_thn_laks'];
            $data[] = $nestedData;
            }
          }
          $rs = array(
              "draw"            => intval( 0 ),
              "recordsTotal"    => intval(count($query)),
              "recordsFiltered" => intval( count($query) ),
              "data"            => $data
          );       
          echo json_encode($rs);
          break;
        
        default:
          # code...
          break;
      }
    }

    public function APS_CrudAgregatorTB6()
    {
      $this->load->model('research/m_research');
      $dataToken = $this->data['dataToken'];
      $mode = $dataToken['mode'];
      switch ($mode) {
        case 'penelitian_melibatkan_mhs':
          $rs = [];
          $P = $dataToken['ProdiID'];
          $P = explode('.', $P);
          $ProdiID = $P[0];
          $sql = 'select a.ID_litabmas, b.Name as NamaDosen,"" as RoadMap,a.Judul_litabmas,a.ID_thn_laks ,
                        (select COUNT(*) from db_research.list_anggota_penelitian as x
                        join db_research.master_anggota_penelitian as y on x.ID_anggota = y.ID
                        where x.ID_Litabmas = a.ID_litabmas and y.Type_anggota = "MHS") AS totalstudent
                  from db_research.litabmas as a
                  join db_employees.employees as b on a.NIP = b.NIP
                 
                  #join db_research.litabmas_list_mahasiswa as c on c.ID_litabmas = a.ID_litabmas
                  #join db_research.anggota_panitia_mahasiswa as d on d.ID_ang_mahasiswa = c.ID_ang_mahasiswa

                  where b.ProdiID = '.$ProdiID.' 
                  group by a.NIP, a.ID_litabmas;
                 ';
          $query = $this->db->query($sql,array())->result_array();
          $data = [];
          for ($i=0; $i < count($query); $i++) { 
            if (!empty($query[$i]['totalstudent'])){
            $nestedData = [];
            $row = $query[$i];
            $nestedData[] = $i+1;
            $nestedData[] = $row['NamaDosen'];
            $nestedData[] = $row['RoadMap'];
            $sql_MHS = 'select usrr.Nama as Name_mahasiswa from db_research.list_anggota_penelitian as a
                        join db_research.master_anggota_penelitian as b on a.ID_anggota = b.ID
                        '.$this->m_research->QueryUserReserachJoin('b.ID_user').'
                        where a.ID_Litabmas = '.$query[$i]['ID_litabmas'].' and b.Type_anggota = "MHS"
                  
                  ';
            $q_MHS = $this->db->query($sql_MHS,array())->result_array();
            $MHSName = '<ul style = "margin-left:-20px;">';
            for ($j=0; $j < count($q_MHS); $j++) { 
              $MHSName .=  '<li>'.$q_MHS[$j]['Name_mahasiswa'].'</li>';
            }

            $MHSName .= '</ul>';      

            // $nestedData[] = $row['Name_mahasiswa'];
            $nestedData[] = $MHSName;
            $nestedData[] = $row['Judul_litabmas'];
            $nestedData[] = $row['ID_thn_laks'];
            $data[] = $nestedData;
          }
        }
          $rs = array(
              "draw"            => intval( 0 ),
              "recordsTotal"    => intval(count($query)),
              "recordsFiltered" => intval( count($query) ),
              "data"            => $data
          );       
          echo json_encode($rs);
          break;
        
        default:
          # code...
          break;
      }
    }

    public function APS_CrudAgregatorTB8()
    {
      $this->load->model('research/m_research');
      $dataToken = $this->data['dataToken'];
      $mode = $dataToken['mode'];
      switch ($mode) {
        case 'luaran_penelitian_pkm_mhs':
          $P = $dataToken['ProdiID'];
          $P = explode('.', $P);
          $ProdiID = $P[0];
          $arr_ID_kat_capaian = [3,7,1,4];
          $rs = [];
          for ($i=0; $i < count($arr_ID_kat_capaian); $i++) {
            $arr_group = [];
            $get_kat_capaian = $this->m_master->caribasedprimary('db_research.kategori_capaian_luaran','ID_kat_capaian',$arr_ID_kat_capaian[$i]);
            $Nm_kat_capaian = $get_kat_capaian[0]['Nm_kat_capaian'];
            $arr_group = ['Name' => $Nm_kat_capaian,'Data' => [] ];
            $row = [];
            $nestedData = array();
            $nestedData[] = ['text' => $this->m_master->romawiNumber($i+1),'colspan' => 1,'style' => '"font-weight:600;background-color: lightyellow;"'] ;
            $nestedData[] = ['text' => $Nm_kat_capaian ,'colspan' => 4,'style' => '"font-weight:600;background-color: lightyellow;"'];
            // $nestedData[] = ['text' => '' ,'colspan' => 0,'style' => '""'];
            // $nestedData[] = ['text' => '' ,'colspan' => 0,'style' => '""'];
            $row[] = $nestedData;
            // $sql = 'select a.Judul,Year(a.Tgl_terbit) as Year,a.Ket
            //         from db_research.publikasi as a 
            //         join db_employees.employees as b on a.NIP = b.NIP
            //         where b.ProdiID = '.$ProdiID.' and a.ID_kat_capaian = '.$arr_ID_kat_capaian[$i].'
            //         UNION
            //         select a.Judul,Year(a.Tgl_terbit) as Year,a.Ket
            //         from db_research.publikasi as a 
            //         join db_research.publikasi_list_mahasiswa as b on a.ID_publikasi = b.ID_publikasi
            //         join db_research.penulis_mahasiswa as c on b.ID_Penulis_Mahasiswa = c.ID_Penulis_Mahasiswa
            //         join db_academic.auth_students as d on c.NIM = d.NPM
            //          where d.ProdiID = '.$ProdiID.' and a.ID_kat_capaian = '.$arr_ID_kat_capaian[$i].'
            //        ';
            $sql = 'select a.Judul,Year(a.Tgl_terbit) as Year,a.Ket,usrr.Nama
                    from db_research.publikasi as a 
                    join db_research.list_anggota_publikasi as b on a.ID_publikasi = b.ID_publikasi
                    join db_research.master_anggota_publikasi as c on b.ID_anggota = c.ID
                    '.$this->m_research->QueryUserReserachJoin('c.ID_user').'
                    join db_academic.auth_students as d on usrr.NIPorNPM = d.NPM
                     where d.ProdiID = '.$ProdiID.' and a.ID_kat_capaian = '.$arr_ID_kat_capaian[$i].' and c.Type_anggota = "MHS"
                   '; 
            $query = $this->db->query($sql,array())->result_array();
            for ($j=0; $j < count($query); $j++) { 
              $nestedData = array();
              $nestedData[] = ['text' => $j+1 ,'colspan' => 1,'style' => '"text-align:right"'];
              $nestedData[] = ['text' => $query[$j]['Judul'] ,'colspan' => 1,'style' => '""'];
              $nestedData[] = ['text' => $query[$j]['Nama'] ,'colspan' => 1,'style' => '""'];
              $nestedData[] = ['text' => $query[$j]['Year'] ,'colspan' => 1,'style' => '""'];
              $nestedData[] = ['text' => $query[$j]['Ket'] ,'colspan' => 1,'style' => '""'];
              $row[] = $nestedData;
            }

            $arr_group['Data'] = $row;
            $rs[] = $arr_group;
          }

          echo json_encode($rs);
          break;
        
        case 'produk_jasa_mhs':
          $AddWhere = '';
          $ProdiID = '';
          $Year = '';
          if (array_key_exists('ProdiID', $dataToken)) {
             $P = $dataToken['ProdiID'];
             $P = explode('.', $P);
             $ProdiID = $P[0];
             $WhereOrAnd = ($AddWhere != '' && $AddWhere  != null) ? ' and' : ' where';
              $AddWhere .= $WhereOrAnd.' b.ProdiID ='.$ProdiID; 
           } 
          if (array_key_exists('Year', $dataToken)) {
             $P = $dataToken['ProdiID'];
             $P = explode('.', $P);
             $ProdiID = $P[0];
             $Year = $dataToken['Year'];
             $WhereOrAnd = ($AddWhere != '' && $AddWhere  != null) ? ' and' : ' where';
             $AddWhere .= $WhereOrAnd.' a.Year ='.$Year;  
           }
          
          $sql  = 'select a.*,b.Name,b.ProdiID,b.NPM
                  from db_agregator.produk_jasa_mhs as a 
                  join db_academic.auth_students as b on a.Updated_by = b.NPM
                  '.$AddWhere.'
          ';

          $query = $this->db->query($sql,array())->result_array();
          $data = array();
          for ($i=0; $i < count($query); $i++) { 
            $nestedData = array();
            $row = $query[$i]; 
            $nestedData[] = $i+1;
            $nestedData[] = $row['NPM'].'-'.$row['Name'];
            $nestedData[] = $row['NamaProdukJasa'];
            $nestedData[] = $row['DeskripsiProdukJasa'];
            $UploadBukti =  '';
            if ($row['UploadBukti'] != '' && $row['UploadBukti'] != null) {
              $UP = json_decode($row['UploadBukti'],true);
              if (count($UP) > 0) {
                 $UploadBukti = '<br/><a href="'.url_sign_in_students.'uploads/produk_jasa/'.$UP[0].'" target = "_blank">Attachment</a>';
              }
            }
            $nestedData[] = $row['Bukti'].$UploadBukti;
            $data[] = $nestedData;
          }

          $json_data = array(
              "draw"            => intval( 0 ),
              "recordsTotal"    => intval(count($query)),
              "recordsFiltered" => intval( count($query) ),
              "data"            => $data
          );
          echo json_encode($json_data);
          break;
        case 'alumni_mahasiswa' :
          $rs = [];
          $YearNow = date('Y');
          $YearTahunLulusAwal = $YearNow - 2;
          $arr_tahun_lulus;
          for ($i=$YearTahunLulusAwal; $i <= $YearNow; $i++) { 
            $arr_tahun_lulus[] = $i;
          }
          $P = $dataToken['ProdiID'];
          $P = explode('.', $P);
          $ProdiID = $P[0];
          for ($i=0; $i < count($arr_tahun_lulus); $i++) { 
            $data = [];
            $data[] = $arr_tahun_lulus[$i];
            $sqlJumlahLulusan = 'select NPM,Name from db_academic.auth_students
                                where GraduationYear = '.$arr_tahun_lulus[$i].' and ProdiID = '.$ProdiID.'
                                ';
            $queryJumlahLulusan = $this->db->query($sqlJumlahLulusan,array())->result_array();
            $arr_pass = [
                'total' => count($queryJumlahLulusan),
                'dt' => $this->jwt->encode($queryJumlahLulusan,"UAP)(*"),
            ];
            $data[] = $arr_pass;

            $sqlLulusanTerlacak = 'select a.NPM,a.Name from db_academic.auth_students as a 
                                  join db_studentlife.alumni_experience as b on a.NPM = b.NPM
                                  where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.'
                                  group by a.NPM
                                  '; 
            $queryLulusanTerlacak = $this->db->query($sqlLulusanTerlacak,array())->result_array();
            $arr_pass = [
                'total' => count($queryLulusanTerlacak),
                'dt' => $this->jwt->encode($queryLulusanTerlacak,"UAP)(*"),
            ];
            $data[] = $arr_pass;

            // Jumlah lulusan dengan waktu tunggu mendapatkan pekerjaan (Sarjana) hanya s1 saja
            $sqlWaktuTungguKecil6Bulan = '
                                        select * from (
                                          select NPM,Name,GraduationDate,TglKerja,(12 * (YEAR(TglKerja) 
                                                                                  - YEAR(GraduationDate)) 
                                                                           + (MONTH(TglKerja) 
                                                                               - MONTH(GraduationDate)) ) AS Bulan 
                                           from (
                                               select a.NPM,a.Name,a.GraduationDate,
                                                 (select concat(StartYear,"-",StartMonth,"-","01") as TglKerja from db_studentlife.alumni_experience
                                                     where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                 ) as TglKerja
                                               from db_academic.auth_students as a 
                                               join db_academic.program_study as b on a.ProdiID = b.ID   
                                               where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.' and b.EducationLevelID = 3
                                            ) subprocess
                                            
                                        ) xx
                                        where Bulan is not null and Bulan < 6
                                          ';
          $queryWaktuTungguKecil6Bulan = $this->db->query($sqlWaktuTungguKecil6Bulan,array())->result_array();
          $arr_pass = [
              'total' => count($queryWaktuTungguKecil6Bulan),
              'dt' => $this->jwt->encode($queryWaktuTungguKecil6Bulan,"UAP)(*"),
          ];
          $data[] = $arr_pass;

            $sqlWaktuTungguKecil6BulanUntil18 = '
                                        select * from (
                                          select NPM,Name,GraduationDate,TglKerja,(12 * (YEAR(TglKerja) 
                                                                                  - YEAR(GraduationDate)) 
                                                                           + (MONTH(TglKerja) 
                                                                               - MONTH(GraduationDate)) ) AS Bulan 
                                           from (
                                               select a.NPM,a.Name,a.GraduationDate,
                                                 (select concat(StartYear,"-",StartMonth,"-","01") as TglKerja from db_studentlife.alumni_experience
                                                     where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                 ) as TglKerja
                                               from db_academic.auth_students as a 
                                               join db_academic.program_study as b on a.ProdiID = b.ID   
                                               where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.' and b.EducationLevelID = 3
                                            ) subprocess
                                            
                                        ) xx
                                        where Bulan is not null and Bulan >= 6 and Bulan <= 18
                                          ';
          $queryWaktuTungguKecil6BulanUntil18 = $this->db->query($sqlWaktuTungguKecil6BulanUntil18,array())->result_array();
          $arr_pass = [
              'total' => count($queryWaktuTungguKecil6BulanUntil18),
              'dt' => $this->jwt->encode($queryWaktuTungguKecil6BulanUntil18,"UAP)(*"),
          ];
          $data[] = $arr_pass;

            $sqlWaktuTungguKecilBesar18 = '
                                        select * from (
                                           select NPM,Name,GraduationDate,TglKerja,(12 * (YEAR(TglKerja) 
                                                                                  - YEAR(GraduationDate)) 
                                                                           + (MONTH(TglKerja) 
                                                                               - MONTH(GraduationDate)) ) AS Bulan 
                                           from (
                                               select a.NPM,a.Name,a.GraduationDate,
                                                 (select concat(StartYear,"-",StartMonth,"-","01") as TglKerja from db_studentlife.alumni_experience
                                                     where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                 ) as TglKerja
                                               from db_academic.auth_students as a 
                                               join db_academic.program_study as b on a.ProdiID = b.ID   
                                               where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.' and b.EducationLevelID = 3
                                            ) subprocess
                                            
                                        ) xx
                                        where Bulan is not null and Bulan > 18
                                          ';
          $queryWaktuTungguKecilBesar18 = $this->db->query($sqlWaktuTungguKecilBesar18,array())->result_array();
          $arr_pass = [
              'total' => count($queryWaktuTungguKecilBesar18),
              'dt' => $this->jwt->encode($queryWaktuTungguKecilBesar18,"UAP)(*"),
          ];
          $data[] = $arr_pass;

            $sqlWaktuTungguKecil3bulan = '
                                        select * from (
                                          select NPM,Name,GraduationDate,TglKerja,(12 * (YEAR(TglKerja) 
                                                                                  - YEAR(GraduationDate)) 
                                                                           + (MONTH(TglKerja) 
                                                                               - MONTH(GraduationDate)) ) AS Bulan 
                                           from (
                                               select a.NPM,a.Name,a.GraduationDate,
                                                 (select concat(StartYear,"-",StartMonth,"-","01") as TglKerja from db_studentlife.alumni_experience
                                                     where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                 ) as TglKerja
                                               from db_academic.auth_students as a 
                                               join db_academic.program_study as b on a.ProdiID = b.ID   
                                               where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.' and b.EducationLevelID = 9
                                            ) subprocess
                                            
                                        ) xx
                                        where Bulan is not null and Bulan < 3
                                          ';
          $queryWaktuTungguKecil3bulan = $this->db->query($sqlWaktuTungguKecil3bulan,array())->result_array();
          $arr_pass = [
              'total' => count($queryWaktuTungguKecil3bulan),
              'dt' => $this->jwt->encode($queryWaktuTungguKecil3bulan,"UAP)(*"),
          ];
          $data[] = $arr_pass;

           $sqlWaktuTungguKecil3bulan6Bulan = '
                                       select * from (
                                         select NPM,Name,GraduationDate,TglKerja,(12 * (YEAR(TglKerja) 
                                                                                 - YEAR(GraduationDate)) 
                                                                          + (MONTH(TglKerja) 
                                                                              - MONTH(GraduationDate)) ) AS Bulan 
                                          from (
                                              select a.NPM,a.Name,a.GraduationDate,
                                                (select concat(StartYear,"-",StartMonth,"-","01") as TglKerja from db_studentlife.alumni_experience
                                                    where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                ) as TglKerja
                                              from db_academic.auth_students as a 
                                              join db_academic.program_study as b on a.ProdiID = b.ID   
                                              where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.' and b.EducationLevelID = 9
                                           ) subprocess
                                           
                                       ) xx
                                       where Bulan is not null and Bulan >= 3 and Bulan <= 6
                                         ';
                $queryWaktuTungguKecil3bulan6Bulan = $this->db->query($sqlWaktuTungguKecil3bulan6Bulan,array())->result_array();
                $arr_pass = [
                    'total' => count($queryWaktuTungguKecil3bulan6Bulan),
                    'dt' => $this->jwt->encode($queryWaktuTungguKecil3bulan6Bulan,"UAP)(*"),
                ];
                $data[] = $arr_pass;

                $sqlWaktuTungguKecilBesar6Bulan = '
                                            select * from (
                                              select NPM,Name,GraduationDate,TglKerja,(12 * (YEAR(TglKerja) 
                                                                                      - YEAR(GraduationDate)) 
                                                                               + (MONTH(TglKerja) 
                                                                                   - MONTH(GraduationDate)) ) AS Bulan 
                                               from (
                                                   select a.NPM,a.Name,a.GraduationDate,
                                                     (select concat(StartYear,"-",StartMonth,"-","01") as TglKerja from db_studentlife.alumni_experience
                                                         where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                     ) as TglKerja
                                                   from db_academic.auth_students as a 
                                                   join db_academic.program_study as b on a.ProdiID = b.ID   
                                                   where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.' and b.EducationLevelID = 9
                                                ) subprocess
                                                
                                            ) xx
                                            where Bulan is not null and Bulan > 6
                                              ';
                     $queryWaktuTungguKecilBesar6Bulan = $this->db->query($sqlWaktuTungguKecilBesar6Bulan,array())->result_array();
                     $arr_pass = [
                         'total' => count($queryWaktuTungguKecilBesar6Bulan),
                         'dt' => $this->jwt->encode($queryWaktuTungguKecilBesar6Bulan,"UAP)(*"),
                     ];
                     $data[] = $arr_pass;

            $sqlLulusanSesuaiBidangKerjaRendah = '
                                  select * from (
                                    select a.NPM,a.Name,(select count(*) as total from db_studentlife.alumni_experience
                                                         where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                  ) as count,
                                                  (select WorkSuitability from db_studentlife.alumni_experience
                                                         where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                  ) as WorkSuitability
                                    from db_academic.auth_students as a  
                                    where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.'
                                  )xx
                                  where count > 0 and WorkSuitability = "0"
                                  '; 
            $queryLulusanSesuaiBidangKerjaRendah = $this->db->query($sqlLulusanSesuaiBidangKerjaRendah,array())->result_array();
            $arr_pass = [
                'total' => count($queryLulusanSesuaiBidangKerjaRendah),
                'dt' => $this->jwt->encode($queryLulusanSesuaiBidangKerjaRendah,"UAP)(*"),
            ];
            $data[] = $arr_pass;

            $sqlLulusanSesuaiBidangKerjaSedang = '
                                  select * from (
                                    select a.NPM,a.Name,(select count(*) as total from db_studentlife.alumni_experience
                                                         where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                  ) as count,
                                                  (select WorkSuitability from db_studentlife.alumni_experience
                                                         where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                  ) as WorkSuitability
                                    from db_academic.auth_students as a  
                                    where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.'
                                  )xx
                                  where count > 0 and WorkSuitability = "1"
                                  '; 
            $queryLulusanSesuaiBidangKerjaSedang = $this->db->query($sqlLulusanSesuaiBidangKerjaSedang,array())->result_array();
            $arr_pass = [
                'total' => count($queryLulusanSesuaiBidangKerjaSedang),
                'dt' => $this->jwt->encode($queryLulusanSesuaiBidangKerjaSedang,"UAP)(*"),
            ];
            $data[] = $arr_pass;

            $sqlLulusanSesuaiBidangKerjaTinggi = '
                                   select * from (
                                    select a.NPM,a.Name,(select count(*) as total from db_studentlife.alumni_experience
                                                         where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                  ) as count,
                                                  (select WorkSuitability from db_studentlife.alumni_experience
                                                         where NPM = a.NPM and JobType = "1" order by StartYear asc,StartMonth asc limit 1             
                                                  ) as WorkSuitability
                                    from db_academic.auth_students as a  
                                    where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.'
                                  )xx
                                  where count > 0 and WorkSuitability = "2"
                                  '; 
            $queryLulusanSesuaiBidangKerjaTinggi = $this->db->query($sqlLulusanSesuaiBidangKerjaTinggi,array())->result_array();
            $arr_pass = [
                'total' => count($queryLulusanSesuaiBidangKerjaTinggi),
                'dt' => $this->jwt->encode($queryLulusanSesuaiBidangKerjaTinggi,"UAP)(*"),
            ];
            $data[] = $arr_pass;

            $sqlLulusanTelahBekerjaUsaha = '
                                  select * from (
                                    select a.NPM,a.Name,(select count(*) as total from db_studentlife.alumni_experience
                                                         where NPM = a.NPM order by StartYear asc,StartMonth asc limit 1             
                                                  ) as count
                                    from db_academic.auth_students as a  
                                    where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.'
                                  )xx
                                  where count > 0
                                  '; 
            $queryLulusanTelahBekerjaUsaha = $this->db->query($sqlLulusanTelahBekerjaUsaha,array())->result_array();
            $arr_pass = [
                'total' => count($queryLulusanTelahBekerjaUsaha),
                'dt' => $this->jwt->encode($queryLulusanTelahBekerjaUsaha,"UAP)(*"),
            ];
            $data[] = $arr_pass;

            $sqlLulusanUkuranLokal = '
                                  select * from (
                                    select a.NPM,a.Name,(select count(*) as total from db_studentlife.alumni_experience
                                                         where NPM = a.NPM and JobLevelID in(1,4) order by StartYear asc,StartMonth asc limit 1             
                                                  ) as count
                                    from db_academic.auth_students as a  
                                    where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.'
                                  )xx
                                  where count > 0
                                  '; 
            $queryLulusanUkuranLokal = $this->db->query($sqlLulusanUkuranLokal,array())->result_array();
            $arr_pass = [
                'total' => count($queryLulusanUkuranLokal),
                'dt' => $this->jwt->encode($queryLulusanUkuranLokal,"UAP)(*"),
            ];
            $data[] = $arr_pass;

            $sqlLulusanUkuranNasional = '
                                  select * from (
                                    select a.NPM,a.Name,(select count(*) as total from db_studentlife.alumni_experience
                                                         where NPM = a.NPM  and JobLevelID in(2,5) order by StartYear asc,StartMonth asc limit 1             
                                                  ) as count
                                    from db_academic.auth_students as a  
                                    where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.'
                                  )xx
                                  where count > 0
                                  '; 
            $queryLulusanUkuranNasional = $this->db->query($sqlLulusanUkuranNasional,array())->result_array();
            $arr_pass = [
                'total' => count($queryLulusanUkuranNasional),
                'dt' => $this->jwt->encode($queryLulusanUkuranNasional,"UAP)(*"),
            ];
            $data[] = $arr_pass;

            $sqlLulusanUkuranInternasional = '
                                  select * from (
                                    select a.NPM,a.Name,(select count(*) as total from db_studentlife.alumni_experience
                                                         where NPM = a.NPM  and JobLevelID in(2,5) order by StartYear asc,StartMonth asc limit 1             
                                                  ) as count
                                    from db_academic.auth_students as a  
                                    where a.GraduationYear = '.$arr_tahun_lulus[$i].' and a.ProdiID = '.$ProdiID.'
                                  )xx
                                  where count > 0
                                  '; 
            $queryLulusanUkuranInternasional = $this->db->query($sqlLulusanUkuranInternasional,array())->result_array();
            $arr_pass = [
                'total' => count($queryLulusanUkuranInternasional),
                'dt' => $this->jwt->encode($queryLulusanUkuranInternasional,"UAP)(*"),
            ];
            $data[] = $arr_pass;

            $arr_pass = [
                'total' => count($queryLulusanTerlacak),
                'dt' => $this->jwt->encode($queryLulusanTerlacak,"UAP)(*"),
            ];
            $data[] = $arr_pass;

            $rs[] = $data;
          }

          echo json_encode($rs);
          break;

        case 'Publikasi_ilmiah_mhs':
          $rs = [];
          $P = $dataToken['ProdiID'];
          $P = explode('.', $P);
          $ProdiID = $P[0];

          $arr_ts = json_decode(json_encode($dataToken['arr_ts']),true);
          $G_jns_forlap_publikasi = $this->m_master->showData_array('db_research.jenis_forlap_publikasi');
          for ($i=0; $i < count($G_jns_forlap_publikasi); $i++) {
            $data = []; 
            $data[] = $i+1;
            $data[] = $G_jns_forlap_publikasi[$i]['NamaForlap_publikasi'];
            $jumlah = 0;
            $ID_forlap_publikasi = $G_jns_forlap_publikasi[$i]['ID'];
            for ($j=0; $j < count($arr_ts) ; $j++) { 
              $sql = 'select a.ID_publikasi,Judul,Tgl_terbit,a.NIP from db_research.publikasi as a
                      #join db_research.publikasi_list_mahasiswa as b on a.ID_publikasi = b.ID_publikasi
                      #join db_research.penulis_mahasiswa as c on b.ID_Penulis_Mahasiswa = c.ID_Penulis_Mahasiswa
                      join db_research.list_anggota_publikasi as b on a.ID_publikasi = b.ID_publikasi
                      #join db_research.master_anggota_publikasi as c on b.ID_anggota = c.ID
                      join db_employees.employees as d on a.NIP = d.NIP
                      where Year(a.Tgl_terbit) = '.$arr_ts[$j].' and a.ID_forlap_publikasi = "'.$ID_forlap_publikasi.'"
                      and d.ProdiID = '.$ProdiID.'
                      and b.ID_anggota in (select ID from db_research.master_anggota_publikasi)
                      group by a.ID_publikasi
                ';



              $query = $this->db->query($sql,array())->result_array();
              for ($lt=0; $lt < count($query); $lt++) { 
                $sqlGetAnggota = 'select usrr.Nama from db_research.list_anggota_publikasi as a
                                  join db_research.master_anggota_publikasi as b on a.ID_anggota = b.ID
                                  '.$this->m_research->QueryUserReserachJoin('b.ID_user').'
                                  where a.ID_publikasi = '.$query[$lt]['ID_publikasi'].' and b.Type_anggota = "MHS"
                 ';
                 $dataAnggota = $this->db->query($sqlGetAnggota,array())->result_array();
                 $query[$lt]['dataAnggota'] = $dataAnggota;
              }
              $tot = count($query);
              $data[] =  array('token' => $this->jwt->encode($query,"UAP)(*"),'total' => $tot) ;
              $jumlah += $tot;
            }
            $data[] = $jumlah;
            $rs[] = $data;
          }
          echo json_encode($rs);
          break;
          
        case 'kepuasan-lulusan':
          $Year = $dataToken['Year'];
          $ProdiID = $dataToken['ProdiID'];
          $dataAspek = $this->db->query('SELECT * FROM db_studentlife.aspek_penilaian_kepuasan')->result_array();

          if(count($dataAspek)>0){

              for($i=0;$i<count($dataAspek);$i++){
                 $dataDetails = $this->db->query('SELECT afd.*, ats.Name, ats.NPM, mc.Name AS Company FROM db_studentlife.alumni_form_details afd 
                                                            LEFT JOIN db_studentlife.alumni_form af ON (af.ID = afd.FormID)
                                                            LEFT JOIN db_studentlife.alumni_experience ae ON (ae.ID = af.IDAE)
                                                            LEFT JOIN db_studentlife.master_company mc ON (mc.ID = ae.CompanyID)
                                                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM = af.NPM)
                                                            WHERE af.Year = "'.$Year.'"  and ats.ProdiID = '.$ProdiID.'
                                                            AND afd.APKID = "'.$dataAspek[$i]['ID'].'" ')->result_array();


                 $Total_SB_D = [];
                 $Total_B_D = [];
                 $Total_C_D = [];
                 $Total_K_D = [];
                 if(count($dataDetails)>0){
                     for($a=0;$a<count($dataDetails);$a++){
                          $d = $dataDetails[$a];
                         if($d['Rate']=='1'){
                             array_push($Total_K_D,$d);
                         } else if($d['Rate']=='2'){
                             array_push($Total_C_D,$d);
                         } else if($d['Rate']=='3'){
                             array_push($Total_B_D,$d);
                         } else if($d['Rate']=='4'){
                             array_push($Total_SB_D,$d);
                         }

                     }
                 }

                  $dataAspek[$i]['Total_SB_D'] = $Total_SB_D;
                  $dataAspek[$i]['Total_B_D'] = $Total_B_D;
                  $dataAspek[$i]['Total_C_D'] = $Total_C_D;
                  $dataAspek[$i]['Total_K_D'] = $Total_K_D;
                  $dataAspek[$i]['Details'] = $dataDetails;
              }

          }

          echo json_encode($dataAspek);  
          break; 
        case 'karya_ilmiah_mhs_sitasi' : 
          $rs = [];
          $P = $dataToken['ProdiID'];
          $P = explode('.', $P);
          $ProdiID = $P[0];
          $Year = $dataToken['Year'];
          $sql = 'select b.Name as NameMHS,a.judul_sitasi,a.jumlah_sitasi 
                  from db_agregator.sitasi_karya_mhs as a 
                  join db_academic.auth_students as b on b.NPM = a.Updated_by
                  where b.ProdiID = '.$ProdiID.' and a.year = '.$Year.'

                ';
          $query = $this->db->query($sql,array())->result_array();
          $data = array();
          for ($i=0; $i < count($query); $i++) { 
              $nestedData = array();
              $row = $query[$i];
              $nestedData[] = $i+1;
              $nestedData[] = $row['NameMHS'];
              $nestedData[] = $row['judul_sitasi'];
              $nestedData[] = $row['jumlah_sitasi'];
              $data[] = $nestedData;
          }
           $rs = array(
               "draw"            => intval( 0 ),
               "recordsTotal"    => intval(count($query)),
               "recordsFiltered" => intval( count($query) ),
               "data"            => $data
           );          
          echo json_encode($rs);
          break; 
        default:
          echo '{"status":"999","message":"Not Authorize"}'; 
          break;
      }
    }

    public function APS_CrudAgregatorTB5()
    {
      $dataToken = $this->data['dataToken'];
      $mode = $dataToken['mode'];
      switch ($mode) {
        case 'Integrasi_penelitian_dkm':
          $rs = [];
          $P = $dataToken['ProdiID'];
          $P = explode('.', $P);
          $ProdiID = $P[0];

          $S = $dataToken['FilterSemester'];
          $S = explode('.', $S);
          $SemesterID = $S[0];
          $sql = '
                  select  * from (
                    select a.Judul_litabmas,b.Name as NameDosen,c.Name as NameMataKuliah,a.Bentuk_integrasi,a.ID_thn_laks
                    from db_research.litabmas as a 
                    join db_employees.employees as b on a.NIP = b.NIP
                    join db_academic.mata_kuliah as c on c.MKCode = a.MKCode
                    where a.SemesterID = '.$SemesterID.' and b.ProdiID = '.$ProdiID.'
                    UNION 
                    select a.Judul_PKM,b.Name as NameDosen,c.Name as NameMataKuliah,a.Bentuk_integrasi,a.ID_thn_laks
                    from db_research.pengabdian_masyarakat as a 
                    join db_employees.employees as b on a.NIP = b.NIP
                    join db_academic.mata_kuliah as c on c.MKCode = a.MKCode
                    where a.SemesterID = '.$SemesterID.' and b.ProdiID = '.$ProdiID.'
                  ) as xx
                  order by ID_thn_laks desc
                ';
            $query = $this->db->query($sql,array())->result_array();
            for ($i=0; $i < count($query); $i++) { 
                $data = [];
                $data[] = $i+1;
                $row = $query[$i];
                foreach ($row as $key => $value) {
                  $data[] = $value;
                }
                $rs[] = $data;
            }    
            echo json_encode($rs);
          break;
        case 'LoadSelectOptionAspekRatio' :
          $rs = [];
          $Get_dt = $this->m_master->caribasedprimary('db_agregator.m_aspek_ratio','Status','1');
          echo json_encode($Get_dt);
          break;
        case 'kepuasan_mhs':
          $rs = [];
          $P = $dataToken['ProdiID'];
          $P = explode('.', $P);
          $ProdiID = $P[0];
          $S = $dataToken['FilterSemester'];
          $S = explode('.', $S);
          $SemesterID = $S[0];
          $sql = 'select a.ID_m_aspek_ratio, b.Aspek,a.k_sangat_baik,a.k_baik,a.k_cukup,a.k_kurang,a.RencanaTindakLanjut,a.SemesterID,a.ProdiID,a.Updated_at,a.Updated_by,a.ID
                  from db_agregator.kepuasan_mhs as a 
                  join db_agregator.m_aspek_ratio as b on a.ID_m_aspek_ratio = b.ID
                  where a.ProdiID = '.$ProdiID.' and a.SemesterID = '.$SemesterID.'
                  ';
          $query = $this->db->query($sql,array())->result_array();
          $data = [];
          for ($i=0; $i < count($query); $i++) { 
            $nestedData = [];
            $row = $query[$i];
            $nestedData[] = $i+1;
            $nestedData[] = $row['Aspek'];
            $nestedData[] = $row['k_sangat_baik'].'%';
            $nestedData[] = $row['k_baik'].'%';
            $nestedData[] = $row['k_cukup'].'%';
            $nestedData[] = $row['k_kurang'].'%';
            $nestedData[] = $row['RencanaTindakLanjut'];
            $nestedData[] = $row['ID'];
            $token = $this->jwt->encode($row,"UAP)(*");
            $nestedData[] = $token;
            $nestedData[] = $row['SemesterID'];
            $nestedData[] = $row['ID_m_aspek_ratio'];
            $data[] = $nestedData;
          }
          $rs = array(
              "draw"            => intval( 0 ),
              "recordsTotal"    => intval(count($query)),
              "recordsFiltered" => intval( count($query) ),
              "data"            => $data
          );
          echo json_encode($rs);
          break; 

        case 'kepuasan-mhs-add' :
          $dataSave = $dataToken['data'];
          $dataSave = json_decode(json_encode($dataSave),true);
          $dataSave['Updated_at'] = date('Y-m-d H:i:s');
          $dataSave['Updated_by'] = $this->session->userdata('NIP');
          $this->db->insert('db_agregator.kepuasan_mhs',$dataSave);
          echo json_encode(1);
          break;
        case 'kepuasan-mhs-edit' :
          $ID = $dataToken['ID'];
          $dataSave = $dataToken['data'];
          $dataSave = json_decode(json_encode($dataSave),true);
          $dataSave['Updated_at'] = date('Y-m-d H:i:s');
          $dataSave['Updated_by'] = $this->session->userdata('NIP');
          $this->db->where('ID',$ID);
          $this->db->update('db_agregator.kepuasan_mhs',$dataSave);
          echo json_encode(1);
          break;
        case 'kepuasan-mhs-delete' :
          $ID = $dataToken['ID'];
          $this->db->where('ID',$ID);
          $this->db->delete('db_agregator.kepuasan_mhs');
          echo json_encode(1);
          break;    
        default:
          echo '{"status":"999","message":"Not Authorize"}'; 
          break;
      }
    }

    public function get_roolback_door_to_be_mhs_admission()
    {
        $dataToken = $this->data['dataToken'];
        $action = $dataToken['action'];
        if ($action == 'read') {
            $TA = $dataToken['ta'];
            $DBTA = 'ta_'.$TA;
            $sql = 'select a.NPM,a.Name,a.ProdiID,b.Name as NameProdi,c.FormulirCode,if(y.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,(select No_Ref from db_admission.formulir_number_online_m where FormulirCode = c.FormulirCode limit 1)  ) as No_Ref
            from '.$DBTA.'.students as a 
            join db_academic.program_study as b on a.ProdiID = b.ID
            join db_admission.to_be_mhs as c on a.NPM = c.NPM
            join db_admission.register_verified as z on c.FormulirCode = z.FormulirCode
            join db_admission.register_verification as x on z.RegVerificationID = x.ID
            join db_admission.register as y on x.RegisterID = y.ID
            where a.NPM NOT IN (
                select NPM from '.$DBTA.'.study_planning
                group by NPM
                order by ID desc
            )
            ';
            $query=$this->db->query($sql, array())->result_array();

            echo json_encode($query);
        }
        elseif ($action == 'roolback') {
            $this->load->model('admission/m_admission');
            $rs = ['Status' => 1,'Dt' => [] ];
            // $path = FCPATH.'upload\\document\\'.'11190042';
            
            // if (!file_exists('./uploads/document/'.'111900424')) {
            //     print_r('Not Directory');
            // }
            // else {
            //     print_r('Directory');
            // }

            // die();

            /*
                1.Cek Koneksi DB Library
                2.Cek Data telah diinput pada library atau belum
                3.Cek Koneksi AD
                4.Delete user AD first
                5.Remove file di folder upload/document/{NPM}
                6.Remove file photo di folder upload/students/{ta}/{NPM}{ekstension}
                7.Remove data di db_finance.payment_students by ID payment
                8.Remove data di db_finance.payment
                9.Remove data di db_finance.m_tuition_fee
                10.Remove data di db_academic.auth_students
                11.Remove data di db_academic.auth_parents
                12.Remove data di db_admission.doc_mhs
                13.Remove data di db_admission.to_be_mhs
                14.Remove data di {ta}.students by NPM
            */

            $DataSelected = $dataToken['DataSelected'];
            $ta =  $dataToken['ta'];
            $taDB =  'ta_'.$ta;
            $DataSelected = json_decode(json_encode($DataSelected),true);
            for ($i=0; $i < count($DataSelected); $i++) {
                $NPM = $DataSelected[$i]['NPM']; 
                $Name = $DataSelected[$i]['Name'];
                $MSG = ''; 
                // library conn
                $cekLib = $this->m_master->cekConectDb();
                if (!$cekLib) {
                    $MSG .= 'Library Koneksi : Koneksi Library bermasalah';
                    $DataSelected[$i]['Status'] = $MSG;
                    $rs['Status'] = 0;
                    $rs['Dt'] = $DataSelected;
                    break;
                }
                else
                {
                    $MSG .= 'Library Koneksi : Koneksi Library Success';
                    $DataSelected[$i]['Status'] = $MSG;
                    // Cek data telah diinput pada library
                    $CekLibStd = $this->m_admission->cekDBLibraryExistSTD($NPM);
                    if ($CekLibStd) {
                        $MSG .= '<br/>Library Data : Ada';
                        // delete data  pada library jika live
                        $DelDtLib = $this->m_admission->DelDBLibraryExistSTD($NPM);
                        if ($DelDtLib) {
                            $MSG .= '<br/>Library Data : Data berhasil dihapus';
                        }
                        else {
                            $MSG .= '<br/>Library Data : Data tidak dihapus';
                        }

                    }
                    else{
                        $MSG .= '<br/>Library Data : Tidak Ada';
                    }

                    // cek koneksi AD
                    $urlAD = URLAD.'__api/Create';
                    $is_url_exist = $this->m_master->is_url_exist($urlAD);
                    if (!$is_url_exist) {
                        $MSG .= '<br/>AD Koneksi : Windows active directory server not connected';
                        $DataSelected[$i]['Status'] = $MSG;
                        $rs['Status'] = 0;
                        $rs['Dt'] = $DataSelected;
                        break;
                    }
                    else {

                      $urlADCheckExist = URLAD.'__api/check-student/'.$NPM;
                      $get_ad = $this->m_master->apiservertoserver_NotWaitResponse($urlADCheckExist);
                      if ($get_ad['status'] == 1 || $get_ad['status'] == '1') {
                          // api delete
                          // dsrm -noprompt CN="test ad",OU=Ldap,DC=pu,DC=local
                          $cn = $get_ad['callback'];
                           $script = 'dsrm -noprompt CN="'.$cn.'",OU=Ldap,DC=pu,DC=local';
                           $data = array(
                               'auth' => 's3Cr3T-G4N',
                               'Type' => 'Student',
                               'script' => $script,
                           );
                           
                           if($_SERVER['SERVER_NAME']=='pcam.podomorouniversity.ac.id') { // AD jalannya hanya di live
                               $url = URLAD.'__api/Delete';
                               $token = $this->jwt->encode($data,"UAP)(*");
                               $this->m_master->apiservertoserver_NotWaitResponse($url,$token);
                               $MSG .= '<br/>AD Proses Delete : Finish';
                           }

                          // $MSG .= '<br/>AD Proses Delete : Skip - Manually (Temporary)';

                      }
                      else
                      {
                        $MSG .= '<br/>AD Data : Tidak Ada';
                      }

                         // Remove file di folder upload/document/{NPM}
                         $path = FCPATH.'uploads/document/'.$NPM;
                         if (file_exists('./uploads/document/'.$NPM)) {
                            $this->m_master->deleteDir($path);
                            $MSG .= '<br/>Remove Folder upload/document/'.$NPM.' : Selesai';
                          } else {
                            $MSG .= '<br/>Remove Folder upload/document/'.$NPM.' : Directory tidak ada';
                          }

                          // Remove file photo di folder upload/students/{ta}/{NIP}{ekstension}
                          $G_dtMHS = $this->m_master->caribasedprimary($taDB.'.students','NPM',$NPM);
                          if (count($G_dtMHS) > 0) {
                            $Photo = $G_dtMHS[0]['Photo'];
                            if (file_exists('./upload/students/'.$ta.'/'.$Photo)) {
                                $path = FCPATH.'uploads/students/'.$ta.'/'.$Photo;
                                unlink($path);
                                $MSG .= '<br/>File Photo  : Berhasil Dihapus';
                            }
                            else {
                                $MSG .= '<br/>File Photo  : tidak ada';
                            }
                          }
                          else {
                            $MSG .= '<br/>Data Student  : tidak ada';
                          }

                          // remove di db_finance
                          $G_dtFin = $this->m_master->caribasedprimary('db_finance.payment','NPM',$NPM);
                          $countCek = 0;
                          for ($z=0; $z < count($G_dtFin); $z++) { 
                              // delete di payment student first
                              $this->db->where('ID_payment',$G_dtFin[$z]['ID']);
                              $this->db->delete('db_finance.payment_students');

                              // delete di payment
                              $this->db->where('ID',$G_dtFin[$z]['ID']);
                              $this->db->delete('db_finance.payment');
                              if ($this->db->affected_rows() > 0 )
                              {
                                $countCek++;
                              }
                          }
                          
                          if ($countCek == count($G_dtFin)) {
                            $MSG .= '<br/>Data Student -- Finance  : Berhasil dihapus';
                          }
                          else {
                            $MSG .= '<br/>Data Student -- Finance  : Gagal dihapus';
                          }

                          // delete di m_tuition fee
                          $this->db->where('NPM',$NPM);
                          $this->db->delete('db_finance.m_tuition_fee');
                          if ($this->db->affected_rows() > 0 )
                          {
                            $MSG .= '<br/>Data Student -- m_tuition_fee  : Berhasil dihapus';
                          }
                          else {
                            $MSG .= '<br/>Data Student -- m_tuition_fee  : Gagal dihapus';
                          }

                          // Remove data di db_academic.auth_students
                          $this->db->where('NPM',$NPM);
                          $this->db->delete('db_academic.auth_students');
                          if ($this->db->affected_rows() > 0 )
                          {
                            $MSG .= '<br/>Data Student -- auth_students  : Berhasil dihapus';
                          }
                          else {
                            $MSG .= '<br/>Data Student -- auth_students  : Gagal dihapus';
                          }

                          // Remove data di db_academic.auth_students
                          $this->db->where('NPM',$NPM);
                          $this->db->delete('db_academic.auth_parents');
                          if ($this->db->affected_rows() > 0 )
                          {
                            $MSG .= '<br/>Data Student -- auth_parents  : Berhasil dihapus';
                          }
                          else {
                            $MSG .= '<br/>Data Student -- auth_parents  : Gagal dihapus';
                          }

                          // Remove data di db_admission.doc_mhs
                          $this->db->where('NPM',$NPM);
                          $this->db->delete('db_admission.doc_mhs');
                          if ($this->db->affected_rows() > 0 )
                          {
                            $MSG .= '<br/>Data Student -- doc_mhs  : Berhasil dihapus';
                          }
                          else {
                            $MSG .= '<br/>Data Student -- doc_mhs  : Gagal dihapus';
                          }

                          // Remove data di db_admission.to_be_mhs
                          $this->db->where('NPM',$NPM);
                          $this->db->delete('db_admission.to_be_mhs');
                          if ($this->db->affected_rows() > 0 )
                          {
                            $MSG .= '<br/>Data Student -- to_be_mhs  : Berhasil dihapus';
                          }
                          else {
                            $MSG .= '<br/>Data Student -- to_be_mhs  : Gagal dihapus';
                          }

                          // Remove data di {ta}.students by NPM
                          $this->db->where('NPM',$NPM);
                          $this->db->delete($taDB.'.students');
                          if ($this->db->affected_rows() > 0 )
                          {
                            $MSG .= '<br/>Data Student -- {ta}.students  : Berhasil dihapus';
                          }
                          else {
                            $MSG .= '<br/>Data Student -- {ta}.students  : Gagal dihapus';
                          }
                    }
                }

                $DataSelected[$i]['Status'] = $MSG;
            }

            $rs['Dt'] = $DataSelected;
            echo json_encode($rs);
            
        }     
        else {
            
        }
        
    }

   
    public function Config_Jabatan_SKS() {
      $dataToken = $this->data['dataToken'];
      $mode = $dataToken['mode'];
      if ($mode=='showDataDosen') {
        $sql = 'select a.NIP,a.Name,a.PositionMain,a.PositionOther1,a.PositionOther2,a.PositionOther3 from db_employees.employees as a
            where ( 
                      StatusEmployeeID != -1
                    ) ';
        $query=$this->db->query($sql, array())->result_array();
        echo json_encode($query);
      }elseif ($mode=='showJabatan') {
        $sql = 'select a.ID,a.Position from db_employees.position as a;';
        $query=$this->db->query($sql, array())->result_array();
        echo json_encode($query);
      }elseif ($mode=='showSemester') {
        $sql = 'select a.ID,a.Name, a.Status from db_academic.semester as a;';
        $query=$this->db->query($sql, array())->result_array();
        echo json_encode($query);
      }

        else if($mode=='saveDataJabatanSKS'){

                          $dataForm = (array) $dataToken['dataForm'];
                          $dataForm['EntredAt'] = $this->m_rest->getDateTimeNow();
                          $dataForm['EntredBy'] = $this->session->userdata('NIP');
                          $this->db->insert('db_rektorat.tugas_tambahan',$dataForm);

                          echo json_encode(1);
        }
        else if ($mode == 'listJabatanSKS') {
          $SemesterID = $dataToken['filterPeriod'];

          $sql = 'select a.ID, a.NIP,b.Name,c.Position,d.Name as SemesterName ,a.SKS
                  from db_rektorat.tugas_tambahan as a join db_employees.employees as b on a.NIP = b.NIP
                  join  db_employees.position as c on a.positionID = c.ID
                  join db_academic.semester as d on d.ID = a.SemesterID
                  where a.semesterID = ?
                  ';
          $query=$this->db->query($sql, array($SemesterID))->result_array();

          echo json_encode($query);
        }
        elseif ($mode == 'deletelistSKS') {
          $ID=$dataToken['ID'];
          $this->db->where('ID', $ID);
          $this->db->delete('db_rektorat.tugas_tambahan');
          echo json_encode(1);
        }

        elseif ($mode == 'deletelistSKS') {
          $ID=$dataToken['ID'];
          $this->db->where('ID', $ID);
          $this->db->delete('db_rektorat.tugas_tambahan');
          echo json_encode(1);
        }


    }

    public function APS_CrudAgregatorTB7()
    {
        $dataToken = $this->data['dataToken'];
        $mode = $dataToken['mode'];
        if ($mode == 'IPKLulusan') {
            $rs = [];
            $ProdiID = $dataToken['ProdiID'];
            $G_smt = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
            $YearNow = $G_smt[0]['Year'];
            $YearSt = $YearNow - 2;

            $arr_year = [];

            for ($i=$YearSt; $i <= $YearNow ; $i++) { 
                $arr_year[] = $i;
            }

            for ($i=0; $i < count($arr_year); $i++) { 
                $temp = [];
                $temp[] = $arr_year[$i];
                // get lulusan
                $sql = 'select NPM,Name,Year from db_academic.auth_students where GraduationYear = '.$arr_year[$i].' AND ProdiID = '.$ProdiID;
                $query=$this->db->query($sql, array())->result_array();
                // $token = $this->jwt->encode($query,"UAP)(*");

                

                $temp2 = [];
                for ($k=0; $k < count($query); $k++) { 
                    $TADB = 'ta_'.$query[$k]['Year'];
                    $NPM = $query[$k]['NPM'];
                    $sql1 = 'select * from '.$TADB.'.study_planning where NPM = ?';
                    $query1=$this->db->query($sql1, array($NPM))->result_array();
                    $GradeValueCredit = 0;
                    $Credit = 0;
                    $IPK = 0;
                    for ($l=0; $l < count($query1); $l++) {
                        $GradeValue = $query1[$l]['GradeValue'];
                        $CreditSub = $query1[$l]['Credit'];
                        $GradeValueCredit = $GradeValueCredit + ($GradeValue * $CreditSub);
                        $Credit = $Credit + $CreditSub;
                    }

                    $IPK = ($Credit == 0) ? 0 : $GradeValueCredit / $Credit;
                    $query[$k]['IPK']=$IPK;
                    $temp2[] = $IPK;
                }
                $token = $this->jwt->encode($query,"UAP)(*");
                $temp[] = ['Count'=> count($query),'token' => $token ];
                if (count($temp2) > 0) {
                    $temp[] = min($temp2);
                    $temp[] = array_sum($temp2)/count($temp2);
                    $temp[] = max($temp2);
                }
                else {
                    $temp[] = 0;
                    $temp[] = 0;
                    $temp[] = 0;
                }
                
                $rs[] = $temp;
            }

            echo json_encode($rs);

        }

        elseif ($mode == 'MasaStudiLulusan') {
          $ProdiID = $dataToken['ProdiID'];
          $ex = explode('.', $ProdiID);
          $ProdiID = $ex[0];
          $ProdiName = $dataToken['ProdiName'];
          /*  
              Tahun Masuk = ta
              ta awal adalah = 2014, 2014 kebawah tidak ada
          */
          $YearMin = 2014;
          $Year = date('Y');
          $YearTS6 = $Year - 6;
          $YearMinRow = $Year - 3;
          $arr_y_temp = [];
          $arr_row = [];
          $TaSelection = [];
          for ($i=$YearTS6; $i <= $Year; $i++) { 
            $arr_y_temp[] = $i;
          }

          for ($i=$YearTS6; $i <= $YearMinRow; $i++) { 
            $arr_row[] = $i;
          }

          $rs = array('header' => array(),'body' => array() );
          $temp = [
              [
                 'Name' => 'Tahun Masuk',
                 'colspan' => 1,
                 'rowspan' => 2,
                 'dt' => [], 
              ],
              [
                'Name' => 'Jumlah Mahasiswa  Diterima',
                'colspan' => 1,
                'rowspan' => 2,
                'dt' => [], 
              ],
              [
                'Name' => 'Jumlah Mahasiswa yang lulus pada',
                'colspan' => count($arr_y_temp),
                'rowspan' => 1,
                'dt' => $arr_y_temp, 
              ],
              [
                'Name' => 'Jumlah Lulusan s.d. akhir TS',
                'colspan' => 1,
                'rowspan' => 2,
                'dt' => [], 
              ],
              [
                'Name' => 'Rata-rata Masa Studi',
                'colspan' => 1,
                'rowspan' => 2,
                'dt' => [], 
              ],
          ];

          $rs['header'] = $temp;
          $body = [];
          for ($i=0; $i < count($arr_row); $i++) {
            $temp =  []; 
            $TahunMasuk = $arr_row[$i];
            $temp[] = $TahunMasuk;
            $JumlahMhsDiterima = 0;
            if ($TahunMasuk >= $YearMin) {

              $sql = 'select count(*) as total from ta_'.$TahunMasuk.'.students where ProdiID ='.$ProdiID;

              $query =$this->db->query($sql, array())->result_array();
              $JumlahMhsDiterima = $query[0]['total'];
            }
            $temp[] = $JumlahMhsDiterima;
            $JmlLulus = 0;
            $arr_rata2studi = [];
            if ($JumlahMhsDiterima > 0) { // jumlah mahasiswa yg lulus pada
              for ($k=0; $k < count($arr_y_temp); $k++) {
                $get_tayear= $arr_y_temp[$k]; 
                $sql1 = 'select NPM,Name,Year,GraduationYear from db_academic.auth_students where GraduationYear = "'.$get_tayear.'" and StatusStudentID = ? and Year = '.$TahunMasuk.' and ProdiID = '.$ProdiID;
                $query1=$this->db->query($sql1, array(1))->result_array();
                // $sql1 = 'select NPM,Name,Year,GraduationYear from db_academic.auth_students where GraduationYear = "'.$get_tayear.'" and Year = '.$TahunMasuk.' and ProdiID = '.$ProdiID;
                // $query1=$this->db->query($sql1, array())->result_array();
                $tot = count($query1);
                $token = $this->jwt->encode($query1,"UAP)(*");
                $s = ['dt' => $token,'count' => $tot];
                $temp[] = $s;
                $JmlLulus += $tot;

                for ($l=0; $l < count($query1); $l++) { 
                  $Co = $query1[$l]['GraduationYear'] - $query1[$l]['Year'];
                  $arr_rata2studi[] = $Co;
                }

              }
            }
            else
            { // tahun 2014 kebawah
              for ($k=0; $k < count($arr_y_temp); $k++) {
                $s = ['dt' => '','count' => 0];
                $temp[] = $s;
              }
            }

            // jumlah lulusan sd akhir ts
            $temp[] = $JmlLulus;
            // rata-rata masa studi
            if ($JumlahMhsDiterima > 0) {
              if (count($arr_rata2studi) ==0) {
                $temp[] = 0;
              }
              else
              {
                $rata_rata = array_sum($arr_rata2studi)/count($arr_rata2studi);
                $temp[] = $rata_rata;
              }
            }
            else
            {
              $temp[] = 0;
            }
            $body[] = $temp;
          }

          $rs['body'] = $body;
          echo json_encode($rs);
            
        }

        elseif ($mode == 'KurikulumCapaianRencana') {
          $rs = [];
          $ProdiID = $dataToken['ProdiID'];
          $P = explode('.', $ProdiID);
          $ProdiID = $P[0];
          $Kurikulum = $dataToken['Kurikulum'];
          $K = explode('.', $Kurikulum);
          $Kurikulum = $K[0];
          $sql = 'select a.Semester,b.MKCode,b.NameEng as NameMataKuliah,if(b.TypeMK = "1","V","") as TypeMatakuliah,
                  if(b.CourseType = "1","V","") as Kuliah,if(b.CourseType = "4","V","") as Seminar,if(b.CourseType = "3" or b.CourseType = "2","V","") as Pratikum,
                  (a.TotalSKS * c.SKSPerMinutes) as Konversi,z.Year,a.MKID
                  from db_academic.curriculum_details as a 
                  join db_academic.curriculum as z on z.ID = a.CurriculumID
                  join db_academic.mata_kuliah as b on a.MKID = b.ID
                  join db_rektorat.credit_type_courses as c on c.ID = b.CourseType
                  where a.ProdiID = '.$ProdiID.' and a.CurriculumID = '.$Kurikulum.'
                 ';
                 //print_r($sql);die();
          $query = $this->db->query($sql,array())->result_array();
          $data = array();
          // get semester
          $Get_semester = $this->m_master->showData_array('db_academic.semester');
          for ($i=0; $i < count($query); $i++) { 
            $nestedData = array();
            $row = $query[$i];
            $nestedData[] = $i+1;
            $nestedData[] = $row['Semester'];
            $nestedData[] = $row['MKCode'];
            $nestedData[] = $row['NameMataKuliah'];
            $nestedData[] = $row['TypeMatakuliah'];
            $nestedData[] = $row['Kuliah'];
            $nestedData[] = $row['Seminar'];
            $nestedData[] = $row['Pratikum'];
            $nestedData[] = $row['Konversi'];
            $nestedData[] = ''; // Sikap
            $nestedData[] = ''; // Pengetahun
            $nestedData[] = ''; // Keteram-pilan Umum
            $nestedData[] = ''; // Keteram-pilan Khusus
            // get Dokumen Rencana Pembela-jaran
            $Semester = $row['Semester'];
            $Year = $row['Year'];
            $MKID = $row['MKID'];
            // get SemesterID
            $CountSemester = 0;
            $SemesterID = '';
            for ($j=0; $j < count($Get_semester); $j++) { 
              if ($Get_semester[$j]['Year'] == $Year) {
                $CountSemester++;
                if ($Semester == $CountSemester) {
                  $SemesterID =  $Get_semester[$j]['ID'];
                  break;
                }
              }
            }
            $Dokumen = '';
            if ($SemesterID != '') {
              $sql2 = 'select b.ScheduleID,c.SAP
                      from db_academic.schedule as a 
                      join db_academic.schedule_details_course as b on a.ID = b.ScheduleID
                      join db_academic.grade_course as c on c.ScheduleID = a.ID
                      where c.SemesterID = '.$SemesterID.' and b.ProdiID = '.$ProdiID.'
                      and b.MKID = '.$MKID.'
                      group by a.ID
              ';
              $query2 = $this->db->query($sql2,array())->result_array();
              if (count($query2) > 0) {
                $Dokumen = '<a href = "'.url_sign_in_lecturers.'uploads/sap/'.$query2[0]['SAP'].'" target = "_blank">'.$query2[0]['SAP'].'</a>';
              }
            }
            $nestedData[] = $Dokumen;
            $nestedData[] = ''; // Unit Penyeleng-gara
            $data[] = $nestedData;
          }
           $rs = array(
               "draw"            => intval( 0 ),
               "recordsTotal"    => intval(count($query)),
               "recordsFiltered" => intval( count($query) ),
               "data"            => $data
           );       
          echo json_encode($rs);
        }
        else
        {
          echo '{"status":"999","message":"Not Authorize"}'; 
        }

       
    }

    public function submit_console_developer()
    {
        $dataToken = $this->data['dataToken'];
        $mode = $dataToken['mode'];
        if ($mode == 'read') {
            $getDt = $this->m_master->showData_array('db_it.m_config');
            echo json_encode($getDt);
        }
        elseif ($mode == 'update') {
            $ID = $dataToken['ID'];
           $DataForm = json_decode(json_encode($dataToken['DataForm']),true);
           $this->db->where('ID',$ID);
           $this->db->update('db_it.m_config',$DataForm);
           
           echo json_encode(1);
        }
        elseif ($mode == 'insert') {
            $DataForm = json_decode(json_encode($dataToken['DataForm']),true);
            $this->db->insert('db_it.m_config',$DataForm);
            echo json_encode(1);
        } 
    }
    public function crud_calendar()
    {
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $Input = $this->getInputToken2();
        $action = $Input['action'];
        if ($action == 'add') {
            $dataSave = [];
            if (array_key_exists('FileUpload', $_FILES)) {
                // do upload file
                $FileUpload = $this->m_master->uploadDokumenMultiple(uniqid(),'FileUpload',$path = './uploads/calendar');
                $FileUpload = json_encode($FileUpload); 
                $dataSave['FileUpload'] = $FileUpload; 
            }

            $data = $Input['data'];
            $data = json_decode(json_encode($data),true);
            $dataSave = $dataSave + $data;
            $dataSave['Entered_at'] = date('Y-m-d H:i:s');
            // $dataSave['Entered_by'] = $this->session->userdata('NIP');
            $this->db->insert('db_academic.calendar_academic',$dataSave);
            echo json_encode(1);

        }
       
       elseif ($action == 'delete') {
           $ID = $Input['ID'];
           $G_data_ = $this->m_master->caribasedprimary('db_academic.calendar_academic','ID',$ID);
           if ($G_data_[0]['FileUpload'] != '' && $G_data_[0]['FileUpload'] != null) {
               $arr_file = (array) json_decode($G_data_[0]['FileUpload'],true);
               $filePath = 'calendar\\'.$arr_file[0]; // pasti ada file karena required
               $path = FCPATH.'uploads\\'.$filePath;
               if (file_exists($path)) {
                   unlink($path);
               }
           }
           $this->db->where('ID',$ID);
           $this->db->delete('db_academic.calendar_academic');
           echo json_encode(1);


       }
       elseif ($action == 'publishcalendar') {
           $ID = $Input['ID'];
           $G_data_ = $this->m_master->caribasedprimary('db_academic.calendar_academic','ID',$ID);
           // $dataSave = $dataSave + $data;
           $dataSave['Update_at'] = date('Y-m-d H:i:s');
           $dataSave['Update_by'] = $this->session->userdata('NIP');
           // $this->db->where('ID',$ID);
           $this->db->query('UPDATE db_academic.calendar_academic s SET s.StatusPublish=2 where ID ='.$ID);
           $this->db->query('UPDATE db_academic.calendar_academic s SET s.StatusPublish=1 where ID !='.$ID);
           echo json_encode(1);


       }
        elseif ($action == 'edit') {
            $dataSave = [];
            $ID = $Input['ID'];
            $data = $Input['data'];
            $data = json_decode(json_encode($data),true);
            $G_data_ = $this->m_master->caribasedprimary('db_academic.calendar_academic','ID',$ID);

            $dataSave = $dataSave + $data;
            $dataSave['Update_at'] = date('Y-m-d H:i:s');
            $dataSave['Update_by'] = $this->session->userdata('NIP');
            $this->db->where('ID',$ID);
            $this->db->update('UPDATE db_academic.calendar_academic s SET s.Status=IF(s.ID="'.$ID.'","1","2"), $dataSave ');
           echo json_encode(1);
        }
        elseif ($action == 'read') {
            $sql = 'select a.*,b.name as NameEmp from db_academic.calendar_academic as a
                    join db_employees.employees as b on a.Entered_by = b.NIP
                    ';
            $query = $this->db->query($sql)->result_array();
            $data = array();
            for ($i=0; $i <count($query) ; $i++) { 
                $nestedData = array();
                $row = $query[$i];
                $nestedData[] = $i+1;
                $nestedData[] = $row['Year'];
                $nestedData[] = $row['YearName'];
                $nestedData[] = $row['FileUpload'];
                $nestedData[] = $row['NameEmp'];
                $nestedData[] = $row['Entered_at'];
                $nestedData[] = $row['StatusPublish'];
                
                $nestedData[] = $row['ID'];
                $token = $this->jwt->encode($row,"UAP)(*");
                $nestedData[] = $token;
                $data[] = $nestedData;
            }

            $json_data = array(
                "draw"            => intval( 0 ),
                "recordsTotal"    => intval(count($query)),
                "recordsFiltered" => intval( count($query) ),
                "data"            => $data
            );
            echo json_encode($json_data);
        }
    }


    public function LoadEmployees_server_side(){
      $requestData = $_REQUEST;
      $sqlTotalData = 'select count(*) as total from (
              select a.NIP,
                          SPLIT_STR(a.PositionMain, ".", 1) as DivisionID,c.Division as DepartmentName,
                          SPLIT_STR(a.PositionMain, ".", 2) as PosistionID,d.Position as PositionName
                          from db_employees.employees as a
                          left join db_academic.program_study as b on a.ProdiID = b.ID
                          left join db_employees.division as c on SPLIT_STR(a.PositionMain, ".", 1) = c.ID
                          left join db_employees.position as d on SPLIT_STR(a.PositionMain, ".", 2) = d.ID
              where 
              (
                a.Name LIKE "'.$requestData['search']['value'].'%" OR
                a.NIP LIKE "'.$requestData['search']['value'].'%" OR
                c.Division LIKE "'.$requestData['search']['value'].'%" OR
                d.Position LIKE "'.$requestData['search']['value'].'%" 
              )
          )xx';
      $queryTotal = $this->db->query($sqlTotalData)->result_array();
      $totalData = $queryTotal[0]['total'];

      $sql = 'select a.*,b.Name as ProdiName,b.Name as NameProdiEng,
            SPLIT_STR(a.PositionMain, ".", 1) as DivisionID,c.Division as DepartmentName,
            SPLIT_STR(a.PositionMain, ".", 2) as PosistionID,d.Position as PositionName
            from db_employees.employees as a
            left join db_academic.program_study as b on a.ProdiID = b.ID
            left join db_employees.division as c on SPLIT_STR(a.PositionMain, ".", 1) = c.ID
            left join db_employees.position as d on SPLIT_STR(a.PositionMain, ".", 2) = d.ID
            where 
            (
              a.Name LIKE "'.$requestData['search']['value'].'%" OR
              a.NIP LIKE "'.$requestData['search']['value'].'%" OR
              c.Division LIKE "'.$requestData['search']['value'].'%" OR
              d.Position LIKE "'.$requestData['search']['value'].'%" 
            )
            ';
      $sql.= ' ORDER BY a.ID desc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
      $query = $this->db->query($sql)->result_array();
      $No = $requestData['start'] + 1;
      $data = array();
      for ($i=0; $i < count($query); $i++) {
          $nestedData=array();
          $row = $query[$i];
          $nestedData[] = $No;
          // foreach ($row as $key => $value) {
          //   $nestedData[] = $value;
          // }
          $nestedData[] = $row['NIP'];
          $nestedData[] = $row['Name'];
          $nestedData[] = $row['DepartmentName'];
          $nestedData[] = $row['PositionName'];
          $tokenRow = $this->jwt->encode($row,"UAP)(*");
          $nestedData['data'] = $tokenRow;
          $data[] = $nestedData;
          $No++;
      }

      $json_data = array(
          "draw"            => intval( $requestData['draw'] ),
          "recordsTotal"    => intval($totalData),
          "recordsFiltered" => intval($totalData ),
          "data"            => $data,
      );

      echo json_encode($json_data);

    }

   

    public function LoadStudents_server_side(){
      $input = $this->getInputToken2();
      $this->load->model("global-informations/Globalinformation_model");
      $requestData = $_REQUEST;
      $param= [];
      if(!empty($requestData['search']['value']) ) {
          $search = $requestData['search']['value'];
          $param[] = array("field"=>"(ta.`Name`","data"=>" like '%".$search."%' ","filter"=>"AND",);    
          $param[] = array("field"=>"ta.`NPM`","data"=>" like '%".$search."%' ","filter"=>"OR",);    
          $param[] = array("field"=>"ps.`NameEng`","data"=>" like '%".$search."%' ","filter"=>"OR",);    
          $param[] = array("field"=>"ps.`Name`","data"=>" like '%".$search."%' )","filter"=>"OR",);    
      }

      // Filter Portal Alumni is active or not
      if(array_key_exists('isPortalAlumi', $input)){
          if ($input['isPortalAlumi'] == 1) {
              $param[] = array("field"=>"(select count(*) as total from `db_alumni`.`registration` as alm where alm.NPM = ta.NPM limit 1  )","data"=>" > 0","filter"=>"AND",); 
          }
          else if ($input['isPortalAlumi'] == 0) {
              $param[] = array("field"=>"(select count(*) as total from `db_alumni`.`registration` as alm where alm.NPM = ta.NPM limit 1  )","data"=>" = 0","filter"=>"AND",);
          }
      }

      // filtering selection
      if (array_key_exists('Selection', $input)) {
        $strFilter = '';
        $Selection = $input['Selection'];
        if (count($Selection) > 0) {
          $strFilter = "'".$Selection[0]."'";
          for ($i=1; $i < count($Selection); $i++) { 
            $strFilter .= ",'".$Selection[$i]."'";
          }
          $param[] = array("field"=>"ta.`NPM`","data"=>" not in (".$strFilter.") ","filter"=>" AND",);
        }
      }
      
      $totalData = $this->Globalinformation_model->fetchTotalDataStudent($param)->Total;
      $result = $this->Globalinformation_model->fetchStudentsPS(false,false,$param,$requestData['start'],$requestData['length'],'');
      $No = (int)$requestData['start'] + 1;
      $data = array();
      // print_r($totalData);die();
      foreach ($result as $key ) {
         $nestedData = array();
         $nestedData[] = $No;
         $No++;
         $nestedData[] = $key->NPM;
         $nestedData[] = $key->Name;
         $nestedData[] = $key->ProdiName;
         $arr = (array) json_decode(json_encode($key),true);
         $tokenRow = $this->jwt->encode($arr,"UAP)(*");
         $nestedData['data'] = $tokenRow;
         $data[] = $nestedData;

      }

      $json_data = array(
          "draw"            => intval( $requestData['draw'] ),
          "recordsTotal"    => intval($totalData ),
          "recordsFiltered" => intval( $totalData ),
          "data"            => $data,
      );

      echo json_encode($json_data);
    }

    public function lecturer_salary_sks(){
      $requestData = $_REQUEST;
      $Input = $this->getInputToken3();
      $action = $Input['action'];
      switch ($action) {
        case 'read_server_side':
              $AddwherePost = [];
              $where='';
              // filter by manually
                foreach ($Input as $key => $value) {
                  $SubArr = $Input[$key];
                  if ($key == 'data') {
                     foreach ($SubArr as $keySub => $valueSub) {
                       switch ($keySub) {
                         case 'SemesterID':
                            $AddwherePost[] = array('field'=>'csl.`'.$keySub.'`','data'=>' = '.$valueSub ,'filter' =>' AND '); 
                           break;
                         
                         default:
                           # code...
                           break;
                       }
                       
                     }
                      
                  }
                }
              // end filter by manually

              if(!empty($requestData['search']['value']) ) {
                  $search = $requestData['search']['value'];
                  $AddwherePost[] = array('field'=>'(emp.`'.'NIP'.'`','data'=>' like "'.$search.'%"' ,'filter' =>' AND ');     
                  $AddwherePost[] = array('field'=>'emp.`'.'Name'.'`','data'=>' like "'.$search.'%" )' ,'filter' =>' OR ');     
              }

              $sql_select = 'select csl.ID,csl.SemesterID,csl.NIP,emp.Name as LecturerName,sms.Name as SemesterName,csl.Money,csl.Allowance,csl.Allowance_NIDN,csl.UpdateAt,csl.UpdatedBy,empUpd.Name as UpdateByName';
              $sql_from = ' from db_employees.credit_salary_lecturer as csl
                                join db_academic.semester as sms on sms.ID = csl.SemesterID
                                join db_employees.employees as emp on emp.NIP = csl.NIP
                                join db_employees.employees as empUpd on empUpd.NIP = csl.UpdatedBy';
              
              if(!empty($AddwherePost)){
                $where = ' WHERE ';
                $counter = 0;
                foreach ($AddwherePost as $key => $value) {
                    if($counter==0){
                        $where = $where.$value['field']." ".$value['data'];
                    }
                    else{
                        $where = $where.$value['filter']." ".$value['field']." ".$value['data'];
                    }
                    $counter++;
                }
              }

              $Totaldata = $this->db->query('select count(*) as total from (
                                                    select 1 '.$sql_from.$where.'

                                              ) temp

                       ')->row()->total;
              $queryData = $this->db->query($sql_select.$sql_from.$where.' LIMIT '.$requestData['start'].' , '.$requestData['length'].' ')->result_array();
              // print_r($this->db->last_query());die();
              $No = (int)$requestData['start'] + 1;
              $data = array();
              for ($i=0; $i < count($queryData); $i++) { 
                $row = $queryData[$i];
                $nestedData = array();
                $nestedData[] = $No;
                $nestedData[] = $row['SemesterName'];
                $nestedData[] = $row['LecturerName'];
                $nestedData[] = $row['Money'];
                $nestedData[] = $row['Allowance'];
                $nestedData[] = $row['Allowance_NIDN'];
                $nestedData[] = $row['ID'];
                $tokenRow = $this->jwt->encode($row,"UAP)(*");
                $nestedData['data'] = $tokenRow;
                $data[] = $nestedData;
                $No++;
              }

              $json_data = array(
                  "draw"            => intval( $requestData['draw'] ),
                  "recordsTotal"    => intval($Totaldata ),
                  "recordsFiltered" => intval( $Totaldata ),
                  "data"            => $data,
              );

              echo json_encode($json_data);
          break;
        case 'add' :
           $rs = ['status' => 0,'msg' => '' ];
           $this->db->db_debug=false;
           $dataSave = $Input['data'];
           $dataSave['UpdateAt'] = date('Y-m-d H:i:s');
           $sql = $this->db->insert('db_employees.credit_salary_lecturer',$dataSave);
           if( !$sql )
           {
              $rs['msg'] = json_encode($this->db->error());
           }
           else
           {
            $rs['status'] = 1; 
           }
           $this->db->db_debug=true;
           echo json_encode($rs);
          break;
        case 'edit' :
           $rs = ['status' => 0,'msg' => '' ];
           $this->db->db_debug=false;
           $dataSave = $Input['data'];
           $dataSave['UpdateAt'] = date('Y-m-d H:i:s');
           $this->db->where('ID',$Input['ID']);
           $sql = $this->db->update('db_employees.credit_salary_lecturer',$dataSave);
           if( !$sql )
           {
              $rs['msg'] = json_encode($this->db->error());
           }
           else
           {
            $rs['status'] = 1; 
           }
           $this->db->db_debug=true;
           echo json_encode($rs);
          break;
        case 'delete' :
          $rs = ['status' => 0,'msg' => '' ];
          $this->db->db_debug=false;
          $this->db->where('ID',$Input['ID']);
          $sql = $this->db->delete('db_employees.credit_salary_lecturer');
          if( !$sql )
          {
             $rs['msg'] = json_encode($this->db->error());
          }
          else
          {
           $rs['status'] = 1; 
          }

          $this->db->db_debug=true;
          echo json_encode($rs);
          break;
        case 'selection_read_server_side' :
            $AddwherePost = [];
            $where='';
            // filter by manually
              foreach ($Input as $key => $value) {
                $SubArr = $Input[$key];
                if ($key == 'data') {
                   foreach ($SubArr as $keySub => $valueSub) {
                     switch ($keySub) {
                       case 'smt_active':
                          $AddwherePost[] = array('field'=>'csl.`'.'SemesterID'.'`','data'=>' = '.($valueSub - 1) ,'filter' =>' AND '); 
                         break;
                       case 'SelectionCopy':
                          $arr_SelectionCopy = $valueSub;
                          $strValue = '';
                          if (count($arr_SelectionCopy) > 0) {
                            $strValue = '(';
                            $strValue .= '"'.$arr_SelectionCopy[0].'"';
                            for ($i=1; $i < count($arr_SelectionCopy); $i++) { 
                              $strValue .= ',"'.$arr_SelectionCopy[$i].'"';
                            }

                             $strValue .= ')';
                          }
                          if ($strValue != '') {
                             $AddwherePost[] = array('field'=>'csl.`'.'NIP'.'`','data'=>' not in'.$strValue ,'filter' =>' AND '); 
                          }
                         
                         break;
                       default:
                         # code...
                         break;
                     }
                     
                   }
                    
                }
              }
            // end filter by manually

            if(!empty($requestData['search']['value']) ) {
                $search = $requestData['search']['value'];
                $AddwherePost[] = array('field'=>'(emp.`'.'NIP'.'`','data'=>' like "'.$search.'%"' ,'filter' =>' AND ');     
                $AddwherePost[] = array('field'=>'emp.`'.'Name'.'`','data'=>' like "'.$search.'%" )' ,'filter' =>' OR ');     
            }

            $sql_select = 'select csl.ID,csl.SemesterID,csl.NIP,emp.Name as LecturerName,sms.Name as SemesterName,csl.Money,csl.Money,csl.Allowance,csl.Allowance_NIDN,csl.UpdateAt,csl.UpdatedBy,empUpd.Name as UpdateByName, 
                          if( (select count(*) as total from db_employees.credit_salary_lecturer where SemesterID = '.$Input['smt_active'].' and NIP = csl.NIP limit 1 ) > 0 ,1,0 ) as SelectionThisSMT  ';
            $sql_from = ' from db_employees.credit_salary_lecturer as csl
                              join db_academic.semester as sms on sms.ID = csl.SemesterID
                              join db_employees.employees as emp on emp.NIP = csl.NIP
                              join db_employees.employees as empUpd on empUpd.NIP = csl.UpdatedBy';
            
            if(!empty($AddwherePost)){
              $where = ' WHERE ';
              $counter = 0;
              foreach ($AddwherePost as $key => $value) {
                  if($counter==0){
                      $where = $where.$value['field']." ".$value['data'];
                  }
                  else{
                      $where = $where.$value['filter']." ".$value['field']." ".$value['data'];
                  }
                  $counter++;
              }
            }

            $Totaldata = $this->db->query('select count(*) as total from (
                                                  select 1 '.$sql_from.$where.'

                                            ) temp

                     ')->row()->total;
            $queryData = $this->db->query($sql_select.$sql_from.$where.' LIMIT '.$requestData['start'].' , '.$requestData['length'].' ')->result_array();
            // print_r($this->db->last_query());die();
            $No = (int)$requestData['start'] + 1;
            $data = array();
            for ($i=0; $i < count($queryData); $i++) { 
              $row = $queryData[$i];
              $nestedData = array();
              $nestedData[] = $No;
              $nestedData[] = $row['SemesterName'];
              $nestedData[] = $row['LecturerName'];
              $nestedData[] = $row['Money'];
              $nestedData[] = $row['Allowance'];
              $nestedData[] = $row['Allowance_NIDN'];
              $nestedData[] = $row['ID'];
              $tokenRow = $this->jwt->encode($row,"UAP)(*");
              $nestedData['data'] = $tokenRow;
              $data[] = $nestedData;
              $No++;
            }

            $json_data = array(
                "draw"            => intval( $requestData['draw'] ),
                "recordsTotal"    => intval($Totaldata ),
                "recordsFiltered" => intval( $Totaldata ),
                "data"            => $data,
            );

            echo json_encode($json_data);
          break;
        case 'submit_selection' :
          $data = $Input['data'];
          // get semester active
          $G_smt = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
          $SemesterID = $G_smt[0]['ID'];
          $sessionNIP = $Input['sessionNIP'];
          $this->db->db_debug=false;
          for ($i=0; $i < count($data); $i++) { 
            $dataSave = [
              'SemesterID' => $SemesterID,
              'NIP' => $data[$i]['NIP'],
              'Money' => $data[$i]['Money'],
              'Allowance' => $data[$i]['Allowance'],
              'Allowance_NIDN' => $data[$i]['Allowance_NIDN'],
              'UpdateAt' => date('Y-m-d H:i:s'),
              'UpdatedBy' => $sessionNIP,
            ];

            
            $sql = $this->db->insert('db_employees.credit_salary_lecturer',$dataSave);
            $FromID = $data[$i]['ID'];
            $ToID = $this->db->insert_id();
            if( !$sql )
            {
               // update
               $sqlQuery = $this->db->query('select * from db_employees.credit_salary_lecturer where NIP = "'.$data[$i]['NIP'].'" and SemesterID = '.$SemesterID.' ')->result_array();
               $FromID = $sqlQuery[0]['ID'];
               $ToID = $FromID;
               $this->db->where('ID',$FromID);
               $this->db->update('db_employees.credit_salary_lecturer',$dataSave);
            }

            // insert to table copy_credit_salary_lecturer
            $dataSave2 = [
              'FromID' => $FromID,
              'ToID' => $ToID,
              'UpdateAt' => date('Y-m-d H:i:s'),
              'UpdatedBy' => $sessionNIP,
            ];
            $this->db->insert('db_employees.copy_credit_salary_lecturer',$dataSave2);
             

          }
          $this->db->db_debug=true;
          echo json_encode(1);
          break;
        default:
          # code...
          break;
      }
    }

    public function HistoryDeposit(){
      $dataToken = $this->getInputToken3();
      $action = $dataToken['action'];
      switch ($action) {
        case 'history':
          $queryData = $this->db->query(
            'select a.*,b.Name as NameEMP from db_finance.trans_deposit as a 
              join db_employees.employees as b on a.FillBy = b.NIP
              where a.NPM = "'.$dataToken['data']['NPM'].'"
              order by a.ID desc
             '
          )->result_array();
          $data = array();
          for ($i=0; $i < count($queryData); $i++) { 
            $row = $queryData[$i];
            $nestedData = array();
            $nestedData[] = ($i+1);
            $nestedData[] = $row['Credit'];
            $nestedData[] = $row['Debit'];
            $nestedData[] = $row['FillBy'];
            $nestedData[] = $row['Desc'];
            $nestedData[] = $row['FillAt'];
            $nestedData[] = $row['NameEMP'];
            $data[] = $nestedData;
          }

          $json_data = array(
              "draw"            => intval(0),
              "recordsTotal"    => intval(count($queryData) ),
              "recordsFiltered" => intval( count($queryData)),
              "data"            => $data,
          );

          echo json_encode($json_data);
          break;
        case 'DepositLeft' :
          echo json_encode(
              $this->db->query(
                'select Deposit from db_academic.auth_students where NPM  = "'.$dataToken['data']['NPM'].'"  '
              )->result_array()[0]['Deposit']
          );
        default:
          # code...
          break;
      }
      
    }

}