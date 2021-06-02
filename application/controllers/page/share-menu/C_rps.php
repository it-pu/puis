<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class C_rps extends Globalclass {

    function __construct()
    {
        parent::__construct();
        
        $this->load->model(array('master/m_master','General_model', 'it/rps/m_rps',
            'General_model','global-informations/Globalinformation_model',
            'hr/m_hr','m_log_content'));
        $this->load->helper("General_helper");
        $this->load->library('google');
        $this->load->library('JWT');

    }
    public function temp($content)
    {
        parent::template($content);
    }

    public function menu_rps($page){
        $data['page'] = $page;
        $content = $this->load->view('page/share-menu/rps/menu_rps',$data,true);
        $this->temp($content);
    }

    public function data_curriculum(){
        $page = $this->load->view('page/share-menu/rps/data_curriculum','',true);
        $this->menu_rps($page);
    }

    public function curriculum_detail(){

        $token = $this->input->post('token');
        $data['token'] = $token;
        $this->load->view('page/share-menu/rps/curriculum_detail',$data);
    }

    public function getKurikulumByYearforRPS()
    {
        
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        $result = $this->m_rps->__getKurikulumByYear($data_arr['SemesterSearch'], $data_arr['year'], $data_arr['ProdiID']);

        return print_r(json_encode($result));
    }


    public function list_rps($token){
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        
        $data['CDID'] = $data_arr['CDID'];
        $data['MKCode'] = $data_arr['MKCode'];
        $data['Semester'] = $data_arr['Semester'];
        $data['curriculumYear'] = $data_arr['curriculumYear'];
        $data['Prodi'] = $data_arr['Prodi'];
        $data['Course'] = $data_arr['Course'];
        $page = $this->load->view('page/share-menu/rps/list_rps',$data,true);
        $this->menu_rps($page);
    }

    public function crud_RPS()
    {

        $datatoken =  $this->getInputToken();
        $datatoken = json_decode(json_encode($datatoken),true);

        if($datatoken['action']=='getDataRPS'){  
            $requestData = $_REQUEST;
            $CDID = $datatoken['CDID'];
            $url = base_url();

            $dataSearch = '';
            if( !empty($requestData['search']['value']) ) {
                $search = $requestData['search']['value'];
                $dataScr = ' AND rb.SubCPMK LIKE "%'.$search.'%" OR Description LIKE "%'.$search.'%" OR rc.Code LIKE "%'.$search.'%"';

                $dataSearch = $dataScr;
            }

            $queryDefault = 'SELECT rb.*, cd.MKID, cd.ProdiID, mk.MKCode, mk.NameEng, ps.Name AS ProdiName
            FROM db_academic.rps_basic rb 
            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = rb.CDID)
            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
            LEFT JOIN db_academic.program_study ps ON (ps.ID = cd.ProdiID)
            WHERE rb.CDID='.$CDID.$dataSearch;                                        

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM ('.$queryDefault.') xx';

            $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

                for($i=0;$i<count($query);$i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $tokenID = $this->jwt->encode(array('ID'=>$row['ID']),'UAP)(*');
                    
                    $btnAct = '<div class="btn-group" style="display:flex;">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-pencil"></i> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" style="min-width:0;">
                                    <li><a href="javascript:void(0);" class="btnEditRPS" data-id="'.$row['ID'].'"  data-week="'.$row['Week'].'" data-subcpmk="'.$row['SubCPMK'].'" data-material="'.$row['Material'].'" data-indikator="'.$row['Indikator'].'" data-kriteria="'.$row['Kriteria'].'" data-desc="'.$row['Description'].'" data-nilai="'.$row['Value'].'" data-descnilai="'.$row['ValueDesc'].'" data-file="'.$row['File'].'"><i class="fa fa fa-edit"></i> Edit</a></li>
                                    <li><a href="javascript:void(0);" class="btnDeleteRPS" data-id="'.$row['ID'].'"><i class="fa fa fa-remove"></i> Remove</a></li>
                                </ul>
                                </div>';
                    $nilai = '';
                    if ($row['ValueDesc']=='') {
                        $nilai = '<div style="text-align: left;">'.$row['Value'].'%</div>';
                    } else {
                        $nilai = '<div style="text-align: left;">'.$row['Value'].'% ('.$row['ValueDesc'].')</div>';

                    }
                    
                    
                    $nestedData[] = '<div>'.$row['Week'].'</div>';
                    // $nestedData[] = '<div style="text-align: center; color:green;">'.$row['MKCode'].'</div>';
                    // $nestedData[] = '<div style="text-align: left;">'.$row['NameEng'].'</div>';
                    // $nestedData[] = '<div style="text-align: left;">'.$row['ProdiName'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['SubCPMK'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Material'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Indikator'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Kriteria'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Description'].'</div>';
                    $nestedData[] = $nilai;
                    $nestedData[] = '<div style="text-align: left;"><a href ="'.$url.'fileGetAny/document-RPS_'.$row['CDID'].'_'.$row['Week'].'-'.$row['File'].'" target="_blank">'.$row['File'].'</a></div>';

                    $nestedData[] = '<div style="text-align: left;">'.$row['EntredAt'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['EntredBy'].'</div>';
                   
                    $nestedData[] = $btnAct;
            

                    $data[] = $nestedData;
                    $no++;
                    }

                $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),
                    "recordsTotal"    => intval($queryDefaultRow),
                    "recordsFiltered" => intval( $queryDefaultRow),
                    "data"            => $data,
                    "dataQuery"       => $query
                );
                echo json_encode($json_data);
        }

        else if($datatoken['action']=='showModalRPS'){

            $CDID = $datatoken['CDID'];

            $data = $this->db->query('SELECT rb.*, cd.MKID, cd.ProdiID, mk.MKCode, mk.NameEng, ps.Name AS ProdiName
            FROM db_academic.rps_basic rb 
            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = rb.CDID)
            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
            LEFT JOIN db_academic.program_study ps ON (ps.ID = cd.ProdiID)
            WHERE rb.CDID='.$CDID.' ORDER BY rb.ID ASC ')
                                    ->result_array();


            return print_r(json_encode($data));


        }

        else if($datatoken['action']=='updateQueueRPS'){
            $this->db->where('ID', $datatoken['ID']);
            $this->db->update('db_academic.rps_basic'
                ,array('Order' => $datatoken['Queue']));
            return print_r(1);
        }

        else if($datatoken['action']=='AddRPS'){
       
            $formData = $datatoken['dataAdd'];
            $ActionBy = $this->session->userdata('Name');
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            $fileName = $formData['filesname'];
    
            
            $dataInsrt = array(
                'CDID' => $formData['CDID'],
                'Week' => $formData['RPSMinggu'],
            );

            $dataCk = $this->db->get_where('db_academic.rps_basic',$dataInsrt)->result_array();
           
            $result = array('Status'=>0);
            
            if(count($dataCk)<=0){

                $dataInsrt['SubCPMK'] = $formData['RPSSubCPMK'];
                $dataInsrt['Material'] = $formData['RPSBahanKajian'];
                $dataInsrt['Indikator'] = $formData['RPSPenilaianIndikator'];
                $dataInsrt['Kriteria'] = $formData['RPSPenilaianKriteria'];
                $dataInsrt['Description'] = $formData['RPSPenilaianMetodePembelajaran'];
                $dataInsrt['Value'] = $formData['RPSNilai'];
                $dataInsrt['ValueDesc'] = $formData['RPSDescNilai'];
                $dataInsrt['EntredAt'] = date('Y-m-d H:i:s');
                $dataInsrt['EntredBy'] = $ActionBy;
                if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                    // if (true) {
                    $this->db->insert('db_academic.rps_basic',$dataInsrt);

                    $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                    $path = 'document/'.$fileName;
                    $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin,$fileName,'userfile',$path,'string');
                    
                    if (!empty($uploadNas)) {
                        $fileName = $uploadNas;
                        // Cek rps
                        $getRps = $this->db->get_where('db_academic.rps_basic',array(
                            'CDID' => $formData['CDID'],
                            'Week' => $formData['RPSMinggu'],
                        ))->result_array();
        
                        if(count($getRps)>0){
                            $this->db->where('CDID', $formData['CDID']);
                            $this->db->where('Week', $formData['RPSMinggu']);
                            $this->db->update('db_academic.rps_basic',array(
                                'File' => $fileName
                            ));
                        }
        
                        $result = array('Status'=>1);
                    }
                    else
                    {
                        //print_r('Upload to nas failed');die();
                        $result = array('Status'=>0);

                    }
                
                }
                else
                {
                    $path = './uploads/document/'.$fileName;
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
        
                    $config['upload_path']          = $path;
                    $config['allowed_types']        = '*';
                    $config['max_size']             = 8000; // 8 mb
                    $config['file_name']            = $fileName;
                
                    //        if($old!='' && is_file('./uploads/agregator/'.$old)){
                    //            unlink('./uploads/agregator/'.$old);
                    //        }
        
        
                    $this->load->library('upload', $config);
                    if ( ! $this->upload->do_upload('userfile')){
                        $error = array('error' => $this->upload->display_errors());
                        //            return print_r(json_encode($error));
                        $result = array('Status'=>0);

                    }
                    else {
        
                        $this->db->insert('db_academic.rps_basic',$dataInsrt);

                        //cekrps
                            $getRps = $this->db->get_where('db_academic.rps_basic',array(
                                'CDID' => $formData['CDID'],
                                'Week' => $formData['RPSMinggu'],
                            ))->result_array();
        
                        if(count($getRps)>0){
                            $this->db->where('CDID', $formData['CDID']);
                            $this->db->where('Week', $formData['RPSMinggu']);
                            $this->db->update('db_academic.rps_basic',array(
                                'File' => $fileName
                            ));
                        }
        
                        $result = array('Status'=>1);
        
                    }
                }
            }
            return print_r(json_encode($result));
        }

        else if($datatoken['action']=='EditRPS'){
            $formData = $datatoken['dataEdit'];
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');
            $editIDRPS = $formData['idRPS'];
            $editRPSSubCPMK = $formData['RPSSubCPMK'];
            $editRPSBahanKajian = $formData['RPSBahanKajian'];
            // $editRPSMinggu = $formData['RPSMinggu'];
            $editRPSPenilaianIndikator = $formData['RPSPenilaianIndikator'];
            $editRPSPenilaianKriteria = $formData['RPSPenilaianKriteria'];
            $editRPSPenilaianMetodePembelajaran = $formData['RPSPenilaianMetodePembelajaran'];
            $editRPSNilai = $formData['RPSNilai'];
            $editRPSDescNilai = $formData['RPSDescNilai'];
            $ActionBy = $this->session->userdata('Name');
            $fileName = $formData['filesname'];


            

            if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                // if (true) {

                $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                $path = 'document/'.$fileName;
                $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin,$fileName,'userfile',$path,'string');
                
                if (!empty($uploadNas)) {
                    $fileName = $uploadNas;
                   
                    $updates = array(
                        'SubCPMK' => $editRPSSubCPMK,
                        'Material' => $editRPSBahanKajian,
                        'Indikator' => $editRPSPenilaianIndikator,
                        'Kriteria' => $editRPSPenilaianKriteria,
                        'Description' => $editRPSPenilaianMetodePembelajaran,
                        'Value' => $editRPSNilai,
                        'ValueDesc' => $editRPSDescNilai,
                        // 'Week' => $editRPSMinggu,
                        'EntredAt' => date('Y-m-d H:i:s'),
                        'EntredBy' => $ActionBy,
                        'File' => $fileName
                    );
                    
                    $this->db->where('ID', $editIDRPS);
                    $this->db->update('db_academic.rps_basic', $updates);
    
                    $result = array('Status'=>1);
                }
                else
                {
                    //print_r('Upload to nas failed');die();
                    $result = array('Status'=>0);

                }
            
            }
            else
            {
                $path = './uploads/document/'.$fileName;
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
    
                $config['upload_path']          = $path;
                $config['allowed_types']        = '*';
                $config['max_size']             = 8000; // 8 mb
                $config['file_name']            = $fileName;
    
                //        if($old!='' && is_file('./uploads/agregator/'.$old)){
                //            unlink('./uploads/agregator/'.$old);
                //        }
            
    
                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload('userfile')){
                    $error = array('error' => $this->upload->display_errors());
                //            return print_r(json_encode($error));
                    $result = array('Status'=>0);

                }
                else {
                    $updates = array(
                        'SubCPMK' => $editRPSSubCPMK,
                        'Material' => $editRPSBahanKajian,
                        'Indikator' => $editRPSPenilaianIndikator,
                        'Kriteria' => $editRPSPenilaianKriteria,
                        'Description' => $editRPSPenilaianMetodePembelajaran,
                        'Value' => $editRPSNilai,
                        'ValueDesc' => $editRPSDescNilai,
                        // 'Week' => $editRPSMinggu,
                        'EntredAt' => date('Y-m-d H:i:s'),
                        'EntredBy' => $ActionBy,
                        'File' => $fileName
                    );
                    
                    $this->db->where('ID', $editIDRPS);
                    $this->db->update('db_academic.rps_basic', $updates);
    
                    $result = array('Status'=>1);
    
                }
            }
            return print_r(json_encode($result));
        }

        else if($datatoken['action']=='DeleteRPS'){
            $formData = $datatoken['dataRemove'];
            $IDRPS = $formData['idRPS'];
            header('Access-Control-Allow-Origin: *');
            header('Content-Type: application/json');

            $data = $this->db->where('ID',$IDRPS)->get('db_academic.rps_basic');
            if ($data) {
                $datas = $data->row();
                if (!empty($datas->File)) {

                    // print_r('pcam/document/RPS_'.$datas->CDID.'_'.$datas->Week.'/'.$datas->File);
                    if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') { //live
                // if (true) {

                        // $headerOrigin = serverRoot; //live
                        $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                    
                        // $headerOrigin = "http://localhost";
                        $p = $this->m_master->DeleteFileToNas($headerOrigin,'pcam/document/RPS_'.$datas->CDID.'_'.$datas->Week.'/'.$datas->File); //live
                        // $p = $this->m_master->DeleteFileToNas($headerOrigin,'localhost/document/RPS_'.$datas->CDID.'_'.$datas->Week.'/'.$datas->File);
                     

                        if (  $p['Status'] != '1') {
                            $result = array('Status'=>0);
                        }
                        else
                        {
                            $this->db->where('ID',$IDRPS);
                            $this->db->delete('db_academic.rps_basic');
                            $result = array('Status'=>1);
                        }
                    }
                    else
                    {
                        if (file_exists('./uploads/document/RPS_'.$datas->CDID.'_'.$datas->Week.'/'.$datas->File)) {
                           unlink('./uploads/document/RPS_'.$datas->CDID.'_'.$datas->Week.'/'.$datas->File);
                        }
                        $this->db->where('ID',$IDRPS);
                        $this->db->delete('db_academic.rps_basic');
                        $result = array('Status'=>1);
                    }


                }
                else
                {
                    // $rs['status'] = 0;
                    // $rs['msg'] = 'No file found';

                    $result = array('Status'=>1);
                }
            }


            return print_r(json_encode($result));
        }
    
    }

    public function list_cpmk($token){
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        
        $data['CDID'] = $data_arr['CDID'];
        $data['MKCode'] = $data_arr['MKCode'];
        $data['Semester'] = $data_arr['Semester'];
        $data['curriculumYear'] = $data_arr['curriculumYear'];
        $data['Prodi'] = $data_arr['Prodi'];
        $data['Course'] = $data_arr['Course'];
        
        $page = $this->load->view('page/share-menu/rps/list_cpmk',$data,true);
        $this->menu_rps($page);
    }

    public function crud_cpmk()
    {
        $datatoken =  $this->getInputToken();
        $datatoken = json_decode(json_encode($datatoken),true);

        if($datatoken['action']=='getDataCPMK'){  
            $requestData = $_REQUEST;
            $CDID = $datatoken['CDID'];
           
            $dataSearch = '';
            if( !empty($requestData['search']['value']) ) {
                $search = $requestData['search']['value'];
                $dataScr = ' AND rc.Type LIKE "%'.$search.'%" OR Description LIKE "%'.$search.'%" OR rc.Code LIKE "%'.$search.'%"';

                $dataSearch = $dataScr;
            }

            $queryDefault = 'SELECT rc.*, cd.MKID, cd.ProdiID, mk.MKCode, mk.NameEng, ps.Name AS ProdiName
            FROM db_academic.rps_cpmk rc 
            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = rc.CDID)
            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
            LEFT JOIN db_academic.program_study ps ON (ps.ID = cd.ProdiID)
            WHERE rc.CDID='.$CDID.$dataSearch.' ORDER BY rc.Order ASC';                                        

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM ('.$queryDefault.') xx';

            $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

                for($i=0;$i<count($query);$i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $tokenID = $this->jwt->encode(array('ID'=>$row['ID']),'UAP)(*');
                    
                    $btnAct = '<div class="btn-group" style="display:flex;">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-pencil"></i> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" style="min-width:0;">
                                    <li><a href="javascript:void(0);" class="btnEditCPMK" data-id="'.$row['ID'].'" data-mkcode="'.$row['MKCode'].'"  data-type="'.$row['Type'].'"  data-code="'.$row['Code'].'" data-description="'.$row['Description'].'" data-order="'.$row['Order'].'"><i class="fa fa fa-edit"></i> Edit</a></li>
                                    <li><a href="javascript:void(0);" class="btnDeleteCPMK" data-id="'.$row['ID'].'"><i class="fa fa fa-remove"></i> Remove</a></li>
                                </ul>
                                </div>';

                    
                    $nestedData[] = '<div>'.$no.'</div>';
                    // $nestedData[] = '<div style="text-align: center; color:green;">'.$row['MKCode'].'</div>';
                    // $nestedData[] = '<div style="text-align: left;">'.$row['NameEng'].'</div>';
                    // $nestedData[] = '<div style="text-align: left;">'.$row['ProdiName'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Type'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Code'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Description'].'</div>';
                    //$nestedData[] = '<div style="text-align: left;">'.$row['Order'].'</div>';

                    $nestedData[] = '<div style="text-align: left;">'.$row['EntredAt'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['EntredBy'].'</div>';
                    $nestedData[] = $btnAct;
            

                    $data[] = $nestedData;
                    $no++;
                    }

                $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),
                    "recordsTotal"    => intval($queryDefaultRow),
                    "recordsFiltered" => intval( $queryDefaultRow),
                    "data"            => $data,
                    "dataQuery"       => $query
                );
                echo json_encode($json_data);
        }

        else if($datatoken['action']=='showModalCPMK'){

            $CDID = $datatoken['CDID'];

            $data = $this->db->query('SELECT rc.*, cd.MKID, cd.ProdiID, mk.MKCode, mk.NameEng, ps.Name AS ProdiName
            FROM db_academic.rps_cpmk rc 
            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = rc.CDID)
            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
            LEFT JOIN db_academic.program_study ps ON (ps.ID = cd.ProdiID)
            WHERE rc.CDID='.$CDID.' ORDER BY rc.Order ASC ')
                                    ->result_array();


            return print_r(json_encode($data));


        }

        else if($datatoken['action']=='updateQueueCPMK'){
            $this->db->where('ID', $datatoken['ID']);
            $this->db->update('db_academic.rps_cpmk'
                ,array('Order' => $datatoken['Queue']));
            return print_r(1);
        }

        else if($datatoken['action']=='AddCPMK'){
            $formData = $datatoken['dataAdd'];
            $TotalQuestion = $this->db->query('SELECT COUNT(*) AS Total 
            FROM db_academic.rps_cpmk
            WHERE CDID = "'.$formData['CDID'].'" ')->result_array()[0]['Total'];

            $addCDID = $formData['CDID'];
            $addTypeCPMK = $formData['subCPMK'];
            $addCodeCPMK = $formData['codeCPMK'];
            $addDescCPMK = $formData['descCPMK'];
            $ActionBy = $this->session->userdata('Name');

            $inserts = array(
                'CDID' => $addCDID,
                'Type' => $addTypeCPMK,
                'Code' => $addCodeCPMK,
                'Description' => $addDescCPMK,
                'Order' => $TotalQuestion+1,
                'EntredAt' => date('Y-m-d H:i:s'),
                'EntredBy' => $ActionBy,
            );

            $this->db->insert('db_academic.rps_cpmk',$inserts);
            $result = array('Status'=>1);
            
            return print_r(json_encode($result));
        }

        else if($datatoken['action']=='EditCPMK'){
            $formData = $datatoken['dataEdit'];

            $editIDCPMK = $formData['CPMKID'];
            $editCPMKType = $formData['CPMKType'];
            $editCPMKCode = $formData['CPMKCode'];
            $editCPMKDesc = $formData['CPMKDesc'];
            $ActionBy = $this->session->userdata('Name');

            $updates = array(
                'Type' => $editCPMKType,
                'Code' => $editCPMKCode,
                'Description' => $editCPMKDesc,
                'EntredAt' => date('Y-m-d H:i:s'),
                'EntredBy' => $ActionBy,
            );

            $this->db->where('ID', $editIDCPMK);
            $this->db->update('db_academic.rps_cpmk', $updates);
            $result = array('Status'=>1);
           

            return print_r(json_encode($result));
        }

        else if($datatoken['action']=='DeleteCPMK'){
            $formData = $datatoken['dataRemove'];
            $idCPMK = $formData['idCPMK'];
            $this->db->where('ID',$idCPMK);
            $this->db->delete('db_academic.rps_cpmk');

            $result = array('Status'=>1);

            return print_r(json_encode($result));
        }
    
    }

    public function desc_MK($token){
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        
        $data['CDID'] = $data_arr['CDID'];
        $data['MKCode'] = $data_arr['MKCode'];
        $data['Semester'] = $data_arr['Semester'];
        $data['curriculumYear'] = $data_arr['curriculumYear'];
        $data['Prodi'] = $data_arr['Prodi'];
        $data['Course'] = $data_arr['Course'];
        
        $page = $this->load->view('page/share-menu/rps/desc_mk',$data,true);
        $this->menu_rps($page);
    }

    public function crud_desc_MK()
    {
        $datatoken =  $this->getInputToken();
        $datatoken = json_decode(json_encode($datatoken),true);

        if($datatoken['action']=='getDataDescMK'){  
            $requestData = $_REQUEST;
            $CDID = $datatoken['CDID'];

            $dataSearch = '';
            if( !empty($requestData['search']['value']) ) {
                $search = $requestData['search']['value'];
                $dataScr = ' AND MKCode LIKE "%'.$search.'%" OR Description LIKE "%'.$search.'%" OR mk.NameEng LIKE "%'.$search.'%"';

                $dataSearch = $dataScr;
            }

            $queryDefault = 'SELECT dmk.*, cd.MKID, cd.ProdiID, mk.MKCode, mk.NameEng, ps.Name AS ProdiName
            FROM db_academic.rps_desc_mk dmk 
            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = dmk.CDID)
            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
            LEFT JOIN db_academic.program_study ps ON (ps.ID = cd.ProdiID)
            WHERE dmk.CDID='.$CDID.$dataSearch.' ORDER BY dmk.Order ASC' ;                                        

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM ('.$queryDefault.') xx';

            $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

                for($i=0;$i<count($query);$i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $tokenID = $this->jwt->encode(array('ID'=>$row['ID']),'UAP)(*');
                    
                    $btnAct = '<div class="btn-group" style="display:flex;">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-pencil"></i> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" style="min-width:0;">
                                    <li><a href="javascript:void(0);" class="btnEditDescMK" data-id="'.$row['ID'].'" data-mkcode="'.$row['MKCode'].'" data-description="'.$row['Description'].'" data-order="'.$row['Order'].'"><i class="fa fa fa-edit"></i> Edit</a></li>
                                    <li><a href="javascript:void(0);" class="btnDeleteDescMK" data-id="'.$row['ID'].'"><i class="fa fa fa-remove"></i> Remove</a></li>
                                </ul>
                                </div>';

                    
                    $nestedData[] = '<div>'.$no.'</div>';
                    // $nestedData[] = '<div style="text-align: center; color:green;">'.$row['MKCode'].'</div>';
                    // $nestedData[] = '<div style="text-align: left;">'.$row['NameEng'].'</div>';
                    // $nestedData[] = '<div style="text-align: left;">'.$row['ProdiName'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Description'].'</div>';
                    //$nestedData[] = '<div style="text-align: left;">'.$row['Order'].'</div>';

                    $nestedData[] = '<div style="text-align: left;">'.$row['EntredAt'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['EntredBy'].'</div>';
                    $nestedData[] = $btnAct;
            

                    $data[] = $nestedData;
                    $no++;
                    }

                $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),
                    "recordsTotal"    => intval($queryDefaultRow),
                    "recordsFiltered" => intval( $queryDefaultRow),
                    "data"            => $data,
                    "dataQuery"            => $query
                );
                echo json_encode($json_data);
        }

        else if($datatoken['action']=='showModalDescMK'){

            $CDID = $datatoken['CDID'];

            $data = $this->db->query('SELECT dmk.*, cd.MKID, cd.ProdiID, mk.MKCode, mk.NameEng, ps.Name AS ProdiName
            FROM db_academic.rps_desc_mk dmk 
            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = dmk.CDID)
            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
            LEFT JOIN db_academic.program_study ps ON (ps.ID = cd.ProdiID)
            WHERE dmk.CDID='.$CDID.' ORDER BY dmk.Order ASC ')
                                    ->result_array();


            return print_r(json_encode($data));


        }

        else if($datatoken['action']=='updateQueueDescMK'){
            $this->db->where('ID', $datatoken['ID']);
            $this->db->update('db_academic.rps_desc_mk'
                ,array('Order' => $datatoken['Queue']));
            return print_r(1);
        }

        else if($datatoken['action']=='AddDescMK'){
            $formData = $datatoken['dataAdd'];

            $TotalQuestion = $this->db->query('SELECT COUNT(*) AS Total 
            FROM db_academic.rps_desc_mk
            WHERE CDID = "'.$formData['CDID'].'" ')->result_array()[0]['Total'];

            $addCDID = $formData['CDID'];
            $addDescMK = $formData['descMK'];
            $ActionBy = $this->session->userdata('Name');

            $inserts = array(
                'CDID' => $addCDID,
                'Description' => $addDescMK,
                'Order' => $TotalQuestion+1,
                'EntredAt' => date('Y-m-d H:i:s'),
                'EntredBy' => $ActionBy,
            );

            $this->db->insert('db_academic.rps_desc_mk',$inserts);
            $result = array('Status'=>1);
            

            return print_r(json_encode($result));
        }

        else if($datatoken['action']=='EditDescMK'){
            $formData = $datatoken['dataEdit'];
            $editIDDescMK = $formData['descMKID'];
            $editDescMK = $formData['descMKDesc'];
            $ActionBy = $this->session->userdata('Name');
           
            $updates = array(
            'Description' => $editDescMK,
            'EntredAt' => date('Y-m-d H:i:s'),
            'EntredBy' => $ActionBy,
            );

            $this->db->where('ID', $editIDDescMK);
            $this->db->update('db_academic.rps_desc_mk', $updates);
            $result = array('Status'=>1);
            

            return print_r(json_encode($result));
        }

        else if($datatoken['action']=='DeleteDescMK'){
            $formData = $datatoken['dataRemove'];
            $idDescMK = $formData['idDescMK'];
            $this->db->where('ID',$idDescMK);
            $this->db->delete('db_academic.rps_desc_mk');
            $result = array('Status'=>1);

            return print_r(json_encode($result));
        }
    
    }

    public function bahan_kajian($token){
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        
        $data['CDID'] = $data_arr['CDID'];
        $data['MKCode'] = $data_arr['MKCode'];
        $data['Semester'] = $data_arr['Semester'];
        $data['curriculumYear'] = $data_arr['curriculumYear'];
        $data['Prodi'] = $data_arr['Prodi'];
        $data['Course'] = $data_arr['Course'];
        
        $page = $this->load->view('page/share-menu/rps/bahan_kajian',$data,true);
        $this->menu_rps($page);
    }

    public function crud_bahan_kajian()
    {
        $datatoken =  $this->getInputToken();
        $datatoken = json_decode(json_encode($datatoken),true);

        if($datatoken['action']=='getDataMaterial'){  
            $requestData = $_REQUEST;
            $CDID = $datatoken['CDID'];

            $dataSearch = '';
            if( !empty($requestData['search']['value']) ) {
                $search = $requestData['search']['value'];
                $dataScr = ' AND MKCode LIKE "%'.$search.'%" OR Description LIKE "%'.$search.'%" OR mk.NameEng LIKE "%'.$search.'%"';

                $dataSearch = $dataScr;
            }

            $queryDefault = 'SELECT rm.*, cd.MKID, cd.ProdiID, mk.MKCode, mk.NameEng, ps.Name AS ProdiName
            FROM db_academic.rps_material rm
            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = rm.CDID)
            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
            LEFT JOIN db_academic.program_study ps ON (ps.ID = cd.ProdiID)
            WHERE rm.CDID = '.$CDID.$dataSearch.' ORDER BY rm.Order ASC';                                        

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM ('.$queryDefault.') xx';

            $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

                for($i=0;$i<count($query);$i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $tokenID = $this->jwt->encode(array('ID'=>$row['ID']),'UAP)(*');
                    
                    $btnAct = '<div class="btn-group" style="display:flex;">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-pencil"></i> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" style="min-width:0;">
                                    <li><a href="javascript:void(0);" class="btnEditMaterial" data-id="'.$row['ID'].'" data-mkscode="'.$row['MKCode'].'" data-description="'.$row['Description'].'" data-order="'.$row['Order'].'"><i class="fa fa fa-edit"></i> Edit</a></li>
                                    <li><a href="javascript:void(0);" class="btnDeleteMaterial" data-id="'.$row['ID'].'"><i class="fa fa fa-remove"></i> Remove</a></li>
                                </ul>
                                </div>';

                    
                    $nestedData[] = '<div>'.$no.'</div>';
                    // $nestedData[] = '<div style="text-align: center; color:green;">'.$row['MKCode'].'</div>';
                    // $nestedData[] = '<div style="text-align: left;">'.$row['NameEng'].'</div>';
                    // $nestedData[] = '<div style="text-align: left;">'.$row['ProdiName'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Description'].'</div>';
                    //$nestedData[] = '<div style="text-align: left;">'.$row['Order'].'</div>';

                    $nestedData[] = '<div style="text-align: left;">'.$row['EntredAt'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['EntredBy'].'</div>';
                    $nestedData[] = $btnAct;
            

                    $data[] = $nestedData;
                    $no++;
                    }

                $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),
                    "recordsTotal"    => intval($queryDefaultRow),
                    "recordsFiltered" => intval( $queryDefaultRow),
                    "data"            => $data,
                    "dataQuery"            => $query
                );
                echo json_encode($json_data);
        }

        else if($datatoken['action']=='showModalMaterial'){

            $CDID = $datatoken['CDID'];

            $data = $this->db->query('SELECT rm.*, cd.MKID, cd.ProdiID, mk.MKCode, mk.NameEng, ps.Name AS ProdiName
            FROM db_academic.rps_material rm
            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = rm.CDID)
            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
            LEFT JOIN db_academic.program_study ps ON (ps.ID = cd.ProdiID)
            WHERE rm.CDID = '.$CDID.' ORDER BY rm.Order ASC ')
                                    ->result_array();


            return print_r(json_encode($data));


        }

        else if($datatoken['action']=='updateQueueMaterial'){
            $this->db->where('ID', $datatoken['ID']);
            $this->db->update('db_academic.rps_material'
                ,array('Order' => $datatoken['Queue']));
            return print_r(1);
        }

        else if($datatoken['action']=='AddMaterial'){
            $formData = $datatoken['dataAdd'];

            
            $TotalQuestion = $this->db->query('SELECT COUNT(*) AS Total 
            FROM db_academic.rps_material
            WHERE CDID = "'.$formData['CDID'].'" ')->result_array()[0]['Total'];

            $addCDID = $formData['CDID'];
            $addDescMaterial = $formData['descMaterial'];
            $addOrderMaterial = $formData['orderMaterial'];
            $ActionBy = $this->session->userdata('Name');
          
            $inserts = array(
                'CDID' => $addCDID,
                'Description' => $addDescMaterial,
                'Order' => $TotalQuestion + 1,
                'EntredAt' => date('Y-m-d H:i:s'),
                'EntredBy' => $ActionBy,
            );

            $this->db->insert('db_academic.rps_material',$inserts);
            $result = array('Status'=>1);
           

            return print_r(json_encode($result));
        }

        else if($datatoken['action']=='EditMaterial'){
            $formData = $datatoken['dataEdit'];
            $editIDMaterial = $formData['MaterialID'];
            $editDescMaterial = $formData['MaterialDesc'];
            $ActionBy = $this->session->userdata('Name');
        
            $updates = array(
            'Description' => $editDescMaterial,
            'EntredAt' => date('Y-m-d H:i:s'),
            'EntredBy' => $ActionBy,
            );

            $this->db->where('ID', $editIDMaterial);
            $this->db->update('db_academic.rps_material', $updates);
            $result = array('Status'=>1);

            return print_r(json_encode($result));
        }

        else if($datatoken['action']=='DeleteMaterial'){
            $formData = $datatoken['dataRemove'];
            $idMaterial = $formData['idMaterial'];
            
            $this->db->where('ID',$idMaterial);
            $this->db->delete('db_academic.rps_material');
            $result = array('Status'=>1);

            return print_r(json_encode($result));
        }

        
    
    }


    public function master_CPL(){
        $page = $this->load->view('page/share-menu/rps/master_cpl','',true);
        $this->menu_rps($page);
    }

    public function manage_CPL($token){
        // $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        
        $data['CDID'] = $data_arr['CDID'];
        $data['MKCode'] = $data_arr['MKCode'];
        $data['Semester'] = $data_arr['Semester'];
        $data['curriculumYear'] = $data_arr['curriculumYear'];
        $data['Prodi'] = $data_arr['Prodi'];
        $data['Course'] = $data_arr['Course'];
        
        $page = $this->load->view('page/share-menu/rps/manage_cpl',$data,true);
        $this->menu_rps($page);
    }
    
    public function crud_CPL()
    {
        $datatoken =  $this->getInputToken();
        $datatoken = json_decode(json_encode($datatoken),true);

        if($datatoken['action']=='getDataCPL'){  
            $requestData = $_REQUEST;

            $dataSearch = '';
            if( !empty($requestData['search']['value']) ) {
                $search = $requestData['search']['value'];
                $dataScr = ' WHERE Code LIKE "%'.$search.'%" OR Description LIKE "%'.$search.'%"';

                $dataSearch = $dataScr;
            }

            $queryDefault = 'SELECT * FROM db_academic.rps_cpl_master'.$dataSearch;                                        

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM ('.$queryDefault.') xx';

            $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

                for($i=0;$i<count($query);$i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $tokenID = $this->jwt->encode(array('ID'=>$row['ID']),'UAP)(*');
                    
                    $btnAct = '<div class="btn-group" style="display:flex;">
                                <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-pencil"></i> <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" style="min-width:0;">
                                    <li><a href="javascript:void(0);" class="btnEditCPLMaster" data-id="'.$row['ID'].'" data-code="'.$row['Code'].'" data-description="'.$row['Description'].'"><i class="fa fa fa-edit"></i> Edit</a></li>
                                    <li><a href="javascript:void(0);" class="btnDeleteCPLMaster" data-id="'.$row['ID'].'"><i class="fa fa fa-remove"></i> Remove</a></li>
                                </ul>
                                </div>';

                    
                    $nestedData[] = '<div>'.$no.'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Code'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Description'].'</div>';
                    //$nestedData[] = '<div style="text-align: left;">'.$row['Order'].'</div>';

                    $nestedData[] = '<div style="text-align: left;">'.$row['EntredAt'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['EntredBy'].'</div>';
                    $nestedData[] = $btnAct;
            

                    $data[] = $nestedData;
                    $no++;
                    }

                $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),
                    "recordsTotal"    => intval($queryDefaultRow),
                    "recordsFiltered" => intval( $queryDefaultRow),
                    "data"            => $data,
                    "dataQuery"            => $query
                );
                echo json_encode($json_data);
        }

        else if($datatoken['action']=='updateQueueCPL'){
            $this->db->where('ID', $datatoken['ID']);
            $this->db->update('db_academic.rps_cpl'
                ,array('Order' => $datatoken['Queue']));
            return print_r(1);
        }

        else if($datatoken['action']=='manageDataCPL'){  
            $requestData = $_REQUEST;

            $dataSearch = '';
            if( !empty($requestData['search']['value']) ) {
                $search = $requestData['search']['value'];
                $dataScr = ' WHERE Code LIKE "%'.$search.'%" OR Description LIKE "%'.$search.'%"';

                $dataSearch = $dataScr;
            }

            $queryDefault = 'SELECT * FROM db_academic.rps_cpl_master'.$dataSearch;                                        

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM ('.$queryDefault.') xx';

            $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

                for($i=0;$i<count($query);$i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $tokenID = $this->jwt->encode(array('ID'=>$row['ID']),'UAP)(*');
                    
                    $btnAct = '<div style="margin-bottom: 10px;">
                                <button class="btn btn-info btn-sm btnAddToCPL" data-id="'.$row['ID'].'"><i class="fa fa-arrow-left margin-right"></i> Add to CPL</button></div>';

                    
                    $nestedData[] = '<div>'.$no.'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$row['Code'].'</div>';
                    $nestedData[] = '<div style="text-align: left;">'.$btnAct.$row['Description'].'</div>';
            

                    $data[] = $nestedData;
                    $no++;
                    }

                $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),
                    "recordsTotal"    => intval($queryDefaultRow),
                    "recordsFiltered" => intval( $queryDefaultRow),
                    "data"            => $data,
                    "dataQuery"            => $query
                );
                echo json_encode($json_data);
        }

        else if($datatoken['action']=='DataInMyCPL'){

            $CDID = $datatoken['CDID'];

            $data = $this->db->query('SELECT c.*, cm.Code, cm.Description
                                            FROM db_academic.rps_cpl c
                                            LEFT JOIN db_academic.rps_cpl_master cm ON (cm.ID = c.CPLMasterID)
                                            WHERE c.CDID = "'.$CDID.'" ORDER BY c.Order')->result_array();
            // print_r($data);
            // die();
            return print_r(json_encode($data));

        }

        else if($datatoken['action']=='addListToCPL'){

            $ActionBy = $this->session->userdata('Name');

            $dataInsrt = array(
                'CDID' => $datatoken['CDID'],
                'CPLMasterID' => $datatoken['ID'],
            );

            // Cek apakah sudah ada atau blm
            $dataCk = $this->db->get_where('db_academic.rps_cpl',$dataInsrt)->result_array();
           
            $result = array('Status'=>0);

            if(count($dataCk)<=0){
                // Dapetin urutan
                $TotalQuestion = $this->db->query('SELECT COUNT(*) AS Total 
                                        FROM db_academic.rps_cpl
                                        WHERE CDID = "'.$datatoken['CDID'].'" ')
                    ->result_array()[0]['Total'];

                $dataInsrt['Order'] = $TotalQuestion + 1;
                $dataInsrt['EntredAt'] = date('Y-m-d H:i:s');
                $dataInsrt['EntredBy'] = $ActionBy;
                $this->db->insert('db_academic.rps_cpl',$dataInsrt);
                $result = array('Status'=>1);
            }
            return print_r(json_encode($result));

        }
        else if($datatoken['action']=='removeFromListCPL'){

            $this->db->where('ID',$datatoken['ID']);
            $this->db->delete('db_academic.rps_cpl');

            return print_r(1);

        }
        else if($datatoken['action']=='AddCPL'){
            $formData = $datatoken['dataAdd'];

            $addCodeCPL = $formData['codeCPL'];
            $addDescCPL = $formData['descCPL'];
            $ActionBy = $this->session->userdata('Name');
            
            $inserts = array(
            'Code' => $addCodeCPL,
            'Description' => $addDescCPL,
            'EntredAt' => date('Y-m-d H:i:s'),
            'EntredBy' => $ActionBy,
            );
            

            $this->db->insert('db_academic.rps_cpl_master',$inserts);
            $result = array('Status'=>1);

            return print_r(json_encode($result));
        }

        else if($datatoken['action']=='EditCPL'){
            $formData = $datatoken['dataEdit'];
            $editIDCPL = $formData['idCPL'];
            $editCodeCPL = $formData['codeCPL'];
            $editDescCPL = $formData['descCPL'];
            $ActionBy = $this->session->userdata('Name');
            
            $updates = array(
            'Code' => $editCodeCPL,
            'Description' => $editDescCPL,
            'EntredAt' => date('Y-m-d H:i:s'),
            'EntredBy' => $ActionBy,
            );

            $this->db->where('ID', $editIDCPL);
            $this->db->update('db_academic.rps_cpl_master', $updates);

            $result = array('Status'=>1);

            return print_r(json_encode($result));
        }

        else if($datatoken['action']=='DeleteCPL'){
            $formData = $datatoken['dataRemove'];
            $IDCPL = $formData['idCPL'];
            $this->db->where('ID',$IDCPL);
            $this->db->delete('db_academic.rps_cpl_master');

            $result = array('Status'=>1);

            return print_r(json_encode($result));
        }
    
    }

    public function loadPageModal(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        $listdata = (array) $data_arr['data'];

 
        if(count($data_arr)>0){
            // $data['department'] = parent::__getDepartement();
            $data['CDID'] = $listdata['CDID'];
            $data['action'] = $data_arr['Action'];
            $data['semester'] = $listdata['Semester'];
            $data['Course'] = $listdata['Course'];
            $data['Prodi'] = $listdata['Prodi'];
            $data['MKCode'] = $listdata['MKCode'];
            $data['curriculumYear'] = $listdata['curriculumYear'];


            $this->load->view('page/share-menu/rps/action_modal',$data);
        } else {
            echo '<h3>Data Is Empty!</h3>';
        }

    }

    public function loadPageModalView(){
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        $listdata = (array) $data_arr['data'];

 
        if(count($data_arr)>0){
            // $data['department'] = parent::__getDepartement();
            $data['CDID'] = $listdata['CDID'];
            $data['action'] = $data_arr['Action'];
            $data['semester'] = $listdata['Semester'];
            $data['Course'] = $listdata['Course'];
            $data['Prodi'] = $listdata['Prodi'];
            $data['MKCode'] = $listdata['MKCode'];
            $data['curriculumYear'] = $listdata['curriculumYear'];


            $this->load->view('page/share-menu/rps/action_modal',$data);
        } else {
            echo '<h3>Data Is Empty!</h3>';
        }

    }


}
