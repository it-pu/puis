<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_api_webdivisi extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('master/m_master');
        $this->load->model('webdivisi/beranda/m_home');

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

// ==== CRUD DATA PRODI ====== // 
    function crudDataDivisi(){

        $data_arr = $this->getInputToken2();
        $divisi_active_id = $this->session->userdata('IDdepartementNavigation');
        
        if($data_arr['action']=='viewDataProdi')
        {
            $data=$this->m_home->getTableProdi();
            echo json_encode($data);
        } else if($data_arr['action']=='updateDataProdi')
        {
            $data=$this->m_home->updateTableProdi($data_arr);
            return print_r(1);
        }
        else if ($data_arr['action']=='viewDataSlider') 
        {   
            $data=$this->m_home->getDataSlider();
            echo json_encode($data);
        }
        else if($data_arr['action']=='insertDataslider')
        {
           $dataSave2 =[];
           if (array_key_exists('uploadFile1', $_FILES)) { // jika file di upload
            $upload = $this->m_master->uploadDokumenMultiple(uniqid(),'uploadFile1',$path = './images/Slider');
            $upload = json_encode($upload); 
            // convert file
            $upload = json_decode($upload,true);
            $upload = $upload[0];
            // $dataSave2['Images'] = $upload; 
            // get posted data
            $dataform = $data_arr['dataform']; // data di jadikan array
            $dataform = json_decode(json_encode($dataform),true); // convert to array
            $dataform['Images'] = $upload;
            $dataform['DivisiID'] = $this->session->userdata('IDdepartementNavigation');
            $dataform['UploadBy'] = $this->session->userdata('NIP');
            $dataform['UploadAt'] = $this->m_rest->getDateTimeNow();
            
            $dataSave2 = $dataform;
            // echo print_r($dataSave2);

            // Search Sorting
            $Sorting = 1;
            $DivisiID = $this->session->userdata('IDdepartementNavigation');
            $sql = 'select * from db_webdivisi.slider where DivisiID = ? order by Sorting desc limit 1';
            $G_sorting = $this->db->query($sql, array($DivisiID))->result_array();
            if (count($G_sorting) > 0) { // jika data ada
                $Sorting = $G_sorting[0]['Sorting'] + 1;
            }
            $dataSave2['Sorting'] = $Sorting;
            $this->db->insert('db_webdivisi.slider',$dataSave2);
           }

            return print_r(1);

        }
        else if($data_arr['action']=='updateDataslider')
        {   
            $dataSave2 =[];
            
            if (array_key_exists('uploadFile1', $_FILES)) { // jika file di upload
                $upload = $this->m_master->uploadDokumenMultiple(uniqid(),'uploadFile1',$path = './images/Slider');
                $upload = json_encode($upload); 
                // convert file
                $upload = json_decode($upload,true);
                $upload = $upload[0];
                // $dataSave2['Images'] = $upload; 
                // get posted data
                $dataform = $data_arr['dataform']; // data di jadikan array
                $dataform = json_decode(json_encode($dataform),true); // convert to array
                $dataform['Images'] = $upload;
                $dataform['DivisiID'] = $this->session->userdata('IDdepartementNavigation');
                $dataform['UploadBy'] = $this->session->userdata('NIP');
                $dataform['UploadAt'] = $this->m_rest->getDateTimeNow();
                
                $dataSave2 = $dataform;                
                $sql = $this->db->get_where('db_webdivisi.slider',array(
                'ID' => $dataSave2['ID']))->result_array();

                $arr_file =  $sql[0]['Images'];
                $path = './images/Slider/'. $arr_file;

                if(is_file($path)){
                    $this->db->where('ID',$dataSave2['ID']);
                    $this->db->update('db_webdivisi.slider',$dataSave2);
                    unlink($path);
                }
           }

            return print_r(1);
        }
        else if ($data_arr['action']=='deleteDataslider') 
        {
            $sql = 'select * from db_webdivisi.slider  where DivisiID= ?';
            $DivisiID = $divisi_active_id;
            $query = $this->db->query($sql, array($DivisiID))->result_array();

            $ID = $data_arr['ID'];
            if ($ID !=''){
                $this->db->where('ID', $ID);
                $this->db->delete('db_webdivisi.slider'); 
                //delete images
                $arr_file =  $query[0]['Images'];
                $path = './images/Slider/'. $arr_file;
                unlink($path);                
            }
            return print_r(1);
        }
        elseif ($data_arr['action'] == 'change_sorting'){
            $ID = $data_arr['ID'];
            $Sorting = $data_arr['Sorting'];
            $sortex = $data_arr['sortex'];

            $DivisiID = $this->session->userdata('IDdepartementNavigation');
            $sql = 'select * from db_webdivisi.slider where DivisiID = ? and Sorting = ? ';
            $G_sorting = $this->db->query($sql, array($DivisiID,$Sorting))->result_array();

            // $G_sorting = $this->m_master->caribasedprimary('db_webdivisi.slider','Sorting',$Sorting);

            $this->db->where('ID',$ID);
            $this->db->update('db_webdivisi.slider',array('Sorting' => $Sorting ));

            $ID_change = $G_sorting[0]['ID'];

            $this->db->where('ID',$ID_change);
            $this->db->update('db_webdivisi.slider',array('Sorting' => $sortex ));

            return print_r(1);

        }
        else if($data_arr['action']=='readLanguageProdi'){

            $data = $this->db->get('db_webdivisi.language')->result_array();

            return print_r(json_encode($data));
        }

        // Add by Nandang =====
        else if($data_arr['action']=='updateProdiTexting'){

            $dataForm = (array) $data_arr['dataForm'];

            $dataForm['DivisiID'] = $divisi_active_id;
            $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();

            // Cek apakah udah di input atau blm
            $dataCk = $this->db->get_where('db_webdivisi.prodi_texting',array(
                'DivisiID' => $divisi_active_id,
                'Type' => $dataForm['Type'],
                'LangID' => $dataForm['LangID'],
            ))->result_array();

            if(count($dataCk)>0){
                $this->db->where('ID', $dataCk[0]['ID']);
                $this->db->update('db_webdivisi.prodi_texting',$dataForm);
            } else {
                $this->db->insert('db_webdivisi.prodi_texting',$dataForm);
            }

            return print_r(1);

        }
        else if($data_arr['action']=='readProdiTexting'){

            $Type = $data_arr['Type'];
            $data = $this->db->query('SELECT pt.*, l.Language ,st.Photo,ast.Name,ast.NPM,c.Tlp 
                                                FROM db_webdivisi.prodi_texting pt 
                                                LEFT JOIN db_webdivisi.language l ON (pt.LangID = l.ID)
                                                LEFT JOIN db_webdivisi.student_testimonials_details std ON (std.IDProdiTexting = pt.ID)
                                                LEFT JOIN db_webdivisi.student_testimonials st ON (st.ID = std.IDStudentTexting)
                                                LEFT JOIN db_academic.auth_students ast ON (ast.NPM = st.NPM)
                                                LEFT JOIN db_webdivisi.calldetail c ON (c.IDProdiTexting = pt.ID)

                                                WHERE pt.DivisiID = "'.$divisi_active_id.'" AND pt.Type="'.$Type.'" ')->result_array();

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='readDataProdiTexting'){
            $Type = $data_arr['Type'];
            $LangID = $data_arr['LangID'];

            $data = $this->db->query('SELECT pt.*, l.Language ,st.Photo,ast.Name,ast.NPM,c.Tlp 
                                                FROM db_webdivisi.prodi_texting pt 
                                                LEFT JOIN db_webdivisi.language l ON (pt.LangID = l.ID)
                                                LEFT JOIN db_webdivisi.student_testimonials_details std ON (std.IDProdiTexting = pt.ID)
                                                LEFT JOIN db_webdivisi.student_testimonials st ON (st.ID = std.IDStudentTexting)
                                                LEFT JOIN db_academic.auth_students ast ON (ast.NPM = st.NPM)
                                                LEFT JOIN db_webdivisi.calldetail c ON (c.IDProdiTexting = pt.ID)
                                                WHERE pt.DivisiID = "'.$divisi_active_id.'" AND pt.Type="'.$Type.'" and pt.LangID="'.$LangID.'" ')->result_array();


            // $data = $this->db->get_where('db_webdivisi.prodi_texting',array(
            //     'DivisiID' => $divisi_active_id,
            //     'Type' => $Type,
            //     'LangID' => $LangID
            // ))->result_array();


            return print_r(json_encode($data));

        }

        // Add by yamin =====
        
       
        else if($data_arr['action']=='saveDataPhoto'){
            if (array_key_exists('uploadFile', $_FILES)) { // jika file di upload
                $upload = $this->m_master->uploadDokumenMultiple(uniqid(),'uploadFile',$path = './images/Kaprodi');
                $upload = json_encode($upload);
                // convert file
                $upload = json_decode($upload,true);
                $upload = $upload[0];

                $dataForm = $data_arr;

                // check action insert or update
                $sql = 'select ps.*from db_webdivisi.prodi_sambutan as ps 
                    where ps.DivisiID = ?
                ';

                $DivisiID = $divisi_active_id;               
                $query = $this->db->query($sql, array($DivisiID))->result_array();
                if (count($query) == 0) { // insert
                    
                    $datasave['Photo'] = $upload;
                    $datasave['DivisiID'] = $divisi_active_id;
                    $this->db->insert('db_webdivisi.prodi_sambutan',$datasave);
                   
                }
                else
                {
                    // update student_testimonials
                    // action photo delete dulu file fotonya kalau dia upload foto, baru insert

                    $arr_file =  $query[0]['Photo'];
                    $path = './images/Kaprodi/'. $arr_file;

                      if(is_file($path)){
                        $ID = $query[0]['ID'];
                        $dataupdate['Photo'] = $upload;
                        $this->db->where('ID',$ID);
                        $this->db->update('db_webdivisi.prodi_sambutan',$dataupdate);
                        unlink($path);
                      }
                      // else{
                      //   $ID = $query[0]['ID'];
                      //   $dataupdate['Photo'] = $upload;
                      //   $this->db->where('ID',$ID);
                      //   $this->db->update('db_webdivisi.prodi_sambutan',$dataupdate);
                      // }
                       
                }

            }

            return print_r(1);
        }
        else if($data_arr['action']=='readProdiPhoto'){

            $data = $this->db->query('SELECT ps.* FROM db_webdivisi.prodi_sambutan ps 
                                                  WHERE ps.DivisiID = '.$divisi_active_id.'
                                                ')->result_array();

            return print_r(json_encode($data));                
        }
        else if($data_arr['action']=='saveProdiCall'){
            $dataForm = $data_arr;
            $prodi_texting = $dataForm['prodi_texting'];
            $prodi_texting = json_decode( json_encode($prodi_texting),true);
            $calldetail = $dataForm['calldetail'];
            $calldetail = json_decode( json_encode($calldetail),true);
            // Cek apakah udah di input atau blm
            $sql = 'select c.Tlp,c.IDProdiTexting from db_webdivisi.calldetail as c 
                join db_webdivisi.prodi_texting as pt on pt.ID = c.IDProdiTexting
                where pt.LangID = ? and pt.Type = ?
            ';
            
            $LangID = $prodi_texting['LangID'];
            $Type = $prodi_texting['Type'];
            $dataCk=$this->db->query($sql, array($LangID,$Type))->result_array();

            if(count($dataCk)>0){
                // upcada call
                
                $ID = $dataCk[0]['IDProdiTexting'];
                $prodi_texting = $dataForm['prodi_texting'];
                $this->db->where('ID', $ID);
                $this->db->update('db_webdivisi.prodi_texting',$prodi_texting);
                // update prodi_texting
                
                $calldetail = $dataForm['calldetail'];
                $this->db->where('IDProdiTexting', $ID);
                $this->db->update('db_webdivisi.calldetail',$calldetail);
                
            } else {
                // insert prodi_texting
                $prodi_texting['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $prodi_texting['DivisiID'] = $divisi_active_id;;

                $this->db->insert('db_webdivisi.prodi_texting',$prodi_texting);
                $IDProdiTexting = $this->db->insert_id();
                // insert callaction
                $calldetail = [
                    'IDProdiTexting' => $IDProdiTexting,
                    'Tlp' => $calldetail['Tlp'],
                    'DivisiID' => $divisi_active_id,
                ];

                $this->db->insert('db_webdivisi.calldetail',$calldetail);
            }

            return print_r(1 );
        }
        

        
        else if($data_arr['action']=='insertContact'){

                $dataForm = (array) $data_arr['dataForm'];
                $dataForm['CreateAT'] = $this->m_rest->getDateTimeNow();
                $this->db->insert('db_webdivisi.contact',$dataForm);

            return print_r(1);
        }
        else if($data_arr['action']=='readDataContact'){
            $data = $this->db->get_where('db_webdivisi.contact')->result_array();
            return print_r(json_encode($data));
        }
        else if($data_arr['action']=='readLogoProdi'){
            $data = $this->db->get_where('db_academic.program_study_detail')->result_array();
            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='readContactAddress'){
            $data_arr = $this->getInputToken2();
            $webdivisi_active_id = $divisi_active_id;
            // print_r($webdivisi_active_id);die();
            $data = $this->db->query('SELECT cd.* FROM db_webdivisi.contact_detail cd 
                                                  WHERE cd.DivisiID = '.$webdivisi_active_id.'
                                                ')->result_array();
            return print_r(json_encode($data));

        }
        
        else if($data_arr['action']=='saveContactDetail'){
                $dataForm = (array) $data_arr['data'];

                $dataForm['DivisiID'] = $divisi_active_id;
                $dataForm['CreateAT'] = $this->m_rest->getDateTimeNow();
                $dataform['CreateBY'] = $this->session->userdata('NIP');
                // Cek apakah udah di input atau blm
                $dataCk = $this->db->get_where('db_webdivisi.contact_detail',array(
                    'DivisiID' => $divisi_active_id,
                ))->result_array();

                if(count($dataCk)>0){
                    $this->db->where('ID', $dataCk[0]['ID']);
                    $this->db->update('db_webdivisi.contact_detail',$dataForm);
                } else {
                    $this->db->insert('db_webdivisi.contact_detail',$dataForm);
                }

                return print_r(1);
        }
        else if($data_arr['action']=='readContactSosmed'){
            
            $data = $this->db->get_where('db_webdivisi.sosmed',array(
                'DivisiID' => $divisi_active_id,
                
            ))->result_array();

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='saveContactSosmed'){
                // if (array_key_exists('uploadFile', $_FILES)) { // jika file di upload
                //     $upload = $this->m_master->uploadDokumenMultiple(uniqid(),'uploadFile',$path = './images/icon');
                //     $upload = json_encode($upload); 
                //     // convert file
                //     $upload = json_decode($upload,true);
                //     $upload = $upload[0];

                    $dataform = (array) $data_arr['data'];
                    $Icon = $dataform['Icon'];
                    $dataform['DivisiID'] = $divisi_active_id;
                    $dataform['CreateAT'] = $this->m_rest->getDateTimeNow();
                    $dataform['CreateBY'] = $this->session->userdata('NIP');
                    // $dataform['Icon']= $upload;
                     $dataCk = $this->db->get_where('db_webdivisi.sosmed',array(
                    'DivisiID' => $divisi_active_id,'Icon' => $Icon,))->result_array();
                    if(count($dataCk)>0){
                        $this->db->where('Icon', $dataCk[0]['Icon']);
                        $this->db->update('db_webdivisi.sosmed',$dataform);
                    } else {
                        $this->db->insert('db_webdivisi.sosmed',$dataform);
                    }
                    
                // }
                    
            return print_r(1);
        }
        else if ($data_arr['action']=='deleteDatasosmed') 
        {
            // print_r($data_arr);die()
            // check action insert or update
            $sql = 'select * from db_webdivisi.sosmed as s where s.ID = ?';
            $ID = $data_arr['ID'];
            $DivisiID = $divisi_active_id;
            $query = $this->db->query($sql, array($ID))->result_array();
            $this->db->where('ID', $ID);
            $this->db->delete('db_webdivisi.sosmed'); 
            // $arr_file =  $query[0]['Icon'];
            
            // $path = './images/icon/'. $arr_file;
            // unlink($path);
            return print_r(1);
        }

        //======//
    }//and crud

    function getDataDivisiTexting(){


        $data_arr = $this->getInputToken2();

        $LangCode = $data_arr['LangCode'];
        $DivisiID = $data_arr['DivisiID'];
        $Type = $data_arr['Type'];

        $data = $this->db->query('SELECT pt.*, l.language FROM db_webdivisi.prodi_texting pt 
                                            LEFT JOIN db_webdivisi.language l ON (l.ID = pt.LangID)
                                            WHERE l.Code LIKE "'.$LangCode.'"
                                             AND pt.DivisiID = "'.$DivisiID.'"
                                             AND pt.Type = "'.$Type.'" ')->result_array();

        return print_r(json_encode($data));

    }
   
    function getDetailDivisi(){
        $data_arr = $this->getInputToken2();
        $LangCode = $data_arr['LangCode'];
        $DivisiID = $data_arr['DivisiID'];

        $data = $this->db->query('SELECT ps.Name, ps.NameEng, em.Name AS Kaprodi, em.TitleAhead, em.TitleBehind , ps.Photo  
                                            FROM  db_employees.employees em 
                                            LEFT JOIN db_webdivisi.prodi_sambutan ps ON em.DivisiID = ps.ID
                                            WHERE ps.ID = "'.$DivisiID.'" ')->result_array();

        if(count($data)>0){
            $data[0]['ProdiName'] = ($LangCode=='Ind') ? $data[0]['Name'] : $data[0]['NameEng'];
            // $DefaultPhoto = base_url('images/Kaprodi/default.jpg');
            $data[0]['Photo'] = ($data[0]['Photo']!='' && $data[0]['Photo']!=null) ? $data[0]['Photo'] :  'default.jpg';
        }

        return print_r(json_encode($data));
    }

    function getTeamDivisi(){
        $data_arr = $this->getInputToken2();
        $DivisiID = $this->session->userdata('IDdepartementNavigation');
        print_r($DivisiID);die();
        $data = $this->db->query('SELECT * FROM db_employees.employees em
                                  WHERE (em.PositionMain like "'.$DivisiID.'.%" OR em.PositionOther1 like "'.$DivisiID.'.%" OR em.PositionOther2 like "'.$DivisiID.'.%" OR em.PositionOther3 like "'.$DivisiID.'.%") AND em.StatusEmployeeID not in("-1","-2") ORDER BY em.PositionMain ASC')->result_array();
        
        return print_r(json_encode($data));
    }


    function getSliderDivisi(){
        $data_arr = $this->getInputToken2();
        $DivisiID = $this->session->userdata('IDdepartementNavigation');
        $data = $this->db->query('SELECT s.* FROM db_webdivisi.slider s WHERE s.DivisiID = '.$DivisiID.' ORDER BY s.Sorting ASC ')->result_array();
        
        
        return print_r(json_encode($data));
    }

    function getAllCategory(){
        $data_arr = $this->getInputToken2();
        $DivisiID = $data_arr['DivisiID'];

        $data = $this->db->query('SELECT * FROM db_webdivisi.facilities  WHERE DivisiID = '.$DivisiID.'  order by RAND() LIMIT 50')->result_array();
        return print_r(json_encode($data));
    }
    function getCategoryClassroom(){
        $data_arr = $this->getInputToken2();
        $DivisiID = $data_arr['DivisiID'];
        $filter = $data_arr['filter'];
       $data = $this->db->query('SELECT * FROM db_webdivisi.facilities WHERE DivisiID = '.$DivisiID.' AND Category LIKE  "%'.$filter.'%"')->result_array();
        // $sql =  'SELECT * FROM db_webdivisi.facilities WHERE DivisiID = '.$DivisiID.' AND Category LIKE 
        // "%'.$filter.'%"';
        // print_r($sql);
        return print_r(json_encode($data));
    }
    
    function getCategoryLaboratory(){
        $data_arr = $this->getInputToken2();
        $DivisiID = $data_arr['DivisiID'];

        // $data = $this->db->query('SELECT * FROM db_webdivisi.facilities WHERE DivisiID = '.$DivisiID.' AND category = "Laboratory"')->result_array();
        $data = $this->db->query('SELECT * FROM db_webdivisi.facilities  WHERE DivisiID = '.$DivisiID.'  order by RAND() LIMIT 50')->result_array();
        
        return print_r(json_encode($data));
    }
    function getCategoryFacilities(){
        $data_arr = $this->getInputToken2();
        $DivisiID = $data_arr['DivisiID'];

        $data = $this->db->query('SELECT * FROM db_webdivisi.facilities WHERE DivisiID = '.$DivisiID.'')->result_array();
        
        
        return print_r(json_encode($data));
    }
    function getInstaDivisi(){
        $data_arr = $this->getInputToken2();
        $DivisiID = $data_arr['DivisiID'];

        $data = $this->db->query('SELECT * FROM db_webdivisi.host_divisi WHERE DivisiID = '.$DivisiID.'')->result_array();
        return print_r(json_encode($data));
    }




}///
