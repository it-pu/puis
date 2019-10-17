<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_api_prodi extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('master/m_master');
        $this->load->model('admin-prodi/beranda/m_home');

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
    function crudDataProdi(){

        $data_arr = $this->getInputToken2();

        $prodi_active_id = $this->session->userdata('prodi_active_id');

        if($data_arr['action']=='viewDataProdi')
        {
            $data=$this->m_home->getTableProdi();
            echo json_encode($data);
        } else if($data_arr['action']=='updateDataProdi')
        {
            $data=$this->m_home->updateTableProdi($data_arr);
            return print_r(1);
        }
        else if ($data_arr['action']=='viewDataTestimoni') 
        {
            $data=$this->m_home->getDataTestimoni();
            echo json_encode($data);
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
            $dataform['ProdiID'] = $this->session->userdata('prodi_active_id');
            $dataform['UploadBy'] = $this->session->userdata('NIP');
            $dataform['UploadAt'] = $this->m_rest->getDateTimeNow();
            
            $dataSave2 = $dataform;
            // echo print_r($dataSave2);

            // Search Sorting
            $Sorting = 1;
            $ProdiID = $this->session->userdata('prodi_active_id');
            $sql = 'select * from db_prodi.slider where ProdiID = ? order by Sorting desc limit 1';
            $G_sorting = $this->db->query($sql, array($ProdiID))->result_array();
            if (count($G_sorting) > 0) { // jika data ada
                $Sorting = $G_sorting[0]['Sorting'] + 1;
            }
            $dataSave2['Sorting'] = $Sorting;
            $this->db->insert('db_prodi.slider',$dataSave2);
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
                $dataform['ProdiID'] = $this->session->userdata('prodi_active_id');
                $dataform['UploadBy'] = $this->session->userdata('NIP');
                $dataform['UploadAt'] = $this->m_rest->getDateTimeNow();
                
                $dataSave2 = $dataform;
                $sql = $this->db->get_where('db_prodi.slider',array(
                'ID' => $dataSave2['ID']))->result_array();

                $arr_file =  $sql[0]['Images'];
                $path = './images/Slider/'. $arr_file;

                if(is_file($path)){
                    $this->db->where('ID',$dataSave2['ID']);
                    $this->db->update('db_prodi.slider',$dataSave2);
                    unlink($path);
                }
           }

            return print_r(1);
        }
        else if ($data_arr['action']=='deleteDataslider') 
        {
            $ID = $data_arr['ID'];
            $this->db->where('ID', $ID);
            $this->db->delete('db_prodi.slider'); 
            return print_r(1);
        }
        elseif ($data_arr['action'] == 'change_sorting'){
            $ID = $data_arr['ID'];
            $Sorting = $data_arr['Sorting'];
            $sortex = $data_arr['sortex'];

            $ProdiID = $this->session->userdata('prodi_active_id');
            $sql = 'select * from db_prodi.slider where ProdiID = ? and Sorting = ? ';
            $G_sorting = $this->db->query($sql, array($ProdiID,$Sorting))->result_array();

            // $G_sorting = $this->m_master->caribasedprimary('db_prodi.slider','Sorting',$Sorting);

            $this->db->where('ID',$ID);
            $this->db->update('db_prodi.slider',array('Sorting' => $Sorting ));

            $ID_change = $G_sorting[0]['ID'];

            $this->db->where('ID',$ID_change);
            $this->db->update('db_prodi.slider',array('Sorting' => $sortex ));

            return print_r(1);

        }
        else if($data_arr['action']=='readLanguageProdi'){

            $data = $this->db->get('db_prodi.language')->result_array();

            return print_r(json_encode($data));
        }

        // Add by Nandang =====
        else if($data_arr['action']=='updateProdiTexting'){

            $dataForm = (array) $data_arr['dataForm'];

            $dataForm['ProdiID'] = $prodi_active_id;
            $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();

            // Cek apakah udah di input atau blm
            $dataCk = $this->db->get_where('db_prodi.prodi_texting',array(
                'ProdiID' => $prodi_active_id,
                'Type' => $dataForm['Type'],
                'LangID' => $dataForm['LangID'],
            ))->result_array();

            if(count($dataCk)>0){
                $this->db->where('ID', $dataCk[0]['ID']);
                $this->db->update('db_prodi.prodi_texting',$dataForm);
            } else {
                $this->db->insert('db_prodi.prodi_texting',$dataForm);
            }

            return print_r(1);

        }
        else if($data_arr['action']=='readProdiTexting'){

            $Type = $data_arr['Type'];
            $data = $this->db->query('SELECT pt.*, l.Language ,st.Photo,ast.Name,ast.NPM,c.Tlp 
                                                FROM db_prodi.prodi_texting pt 
                                                LEFT JOIN db_prodi.language l ON (pt.LangID = l.ID)
                                                LEFT JOIN db_prodi.student_testimonials_details std ON (std.IDProdiTexting = pt.ID)
                                                LEFT JOIN db_prodi.student_testimonials st ON (st.ID = std.IDStudentTexting)
                                                LEFT JOIN db_academic.auth_students ast ON (ast.NPM = st.NPM)
                                                LEFT JOIN db_prodi.calldetail c ON (c.IDProdiTexting = pt.ID)

                                                WHERE pt.ProdiID = "'.$prodi_active_id.'" AND pt.Type="'.$Type.'" ')->result_array();

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='readDataProdiTexting'){
            $Type = $data_arr['Type'];
            $LangID = $data_arr['LangID'];

            $data = $this->db->query('SELECT pt.*, l.Language ,st.Photo,ast.Name,ast.NPM,c.Tlp 
                                                FROM db_prodi.prodi_texting pt 
                                                LEFT JOIN db_prodi.language l ON (pt.LangID = l.ID)
                                                LEFT JOIN db_prodi.student_testimonials_details std ON (std.IDProdiTexting = pt.ID)
                                                LEFT JOIN db_prodi.student_testimonials st ON (st.ID = std.IDStudentTexting)
                                                LEFT JOIN db_academic.auth_students ast ON (ast.NPM = st.NPM)
                                                LEFT JOIN db_prodi.calldetail c ON (c.IDProdiTexting = pt.ID)
                                                WHERE pt.ProdiID = "'.$prodi_active_id.'" AND pt.Type="'.$Type.'" and pt.LangID="'.$LangID.'" ')->result_array();


            // $data = $this->db->get_where('db_prodi.prodi_texting',array(
            //     'ProdiID' => $prodi_active_id,
            //     'Type' => $Type,
            //     'LangID' => $LangID
            // ))->result_array();


            return print_r(json_encode($data));

        }

        // Add by yamin =====
        
        else if($data_arr['action']=='saveDataTestimonials'){

            if (array_key_exists('uploadFile', $_FILES)) { // jika file di upload
                $upload = $this->m_master->uploadDokumenMultiple(uniqid(),'uploadFile',$path = './images/Testimonials');
                $upload = json_encode($upload); 
                // convert file
                $upload = json_decode($upload,true);
                $upload = $upload[0];

                $dataForm = $data_arr; 

                $student_testimonials = $dataForm['student_testimonials'];
                $student_testimonials = json_decode( json_encode($student_testimonials),true);

                $prodi_texting = $dataForm['prodi_texting'];
                $prodi_texting = json_decode( json_encode($prodi_texting),true);

                // check action insert or update
                $sql = 'select st.NPM,st.Photo,std.IDStudentTexting,std.IDProdiTexting from db_prodi.student_testimonials as st join db_prodi.student_testimonials_details as std on st.ID = std.IDStudentTexting
                    join db_prodi.prodi_texting as pt on pt.ID = std.IDProdiTexting
                    where st.NPM = ? and pt.LangID = ? and pt.Type = ?
                ';

                $NPM = $student_testimonials['NPM'];
                $LangID = $prodi_texting['LangID'];
                $Type = $prodi_texting['Type'];
                $query=$this->db->query($sql, array($NPM,$LangID,$Type))->result_array();
                if (count($query) == 0) { // insert

                    // insert prodi_texting
                    $prodi_texting['ProdiID'] = $prodi_active_id;
                    $prodi_texting['UpdatedAt'] = $this->m_rest->getDateTimeNow();

                    $this->db->insert('db_prodi.prodi_texting',$prodi_texting);
                    $IDProdiTexting = $this->db->insert_id();

                    // insert student_testimonials
                    $student_testimonials['Photo'] = $upload;
                    $student_testimonials['ProdiID'] = $prodi_active_id;

                    $this->db->insert('db_prodi.student_testimonials',$student_testimonials);
                    $IDStudentTexting = $this->db->insert_id();

                    // insert student_testimonials_details
                    $student_testimonials_details = [
                        'IDStudentTexting' => $IDStudentTexting,
                        'IDProdiTexting' => $IDProdiTexting,
                    ];
                    $this->db->insert('db_prodi.student_testimonials_details',$student_testimonials_details);
                }
                else
                {
                    // update student_testimonials
                        // action photo delete dulu file fotonya kalau dia upload foto, baru insert

                    $arr_file =  $query[0]['Photo'];
                    $path = './images/Testimonials/'. $arr_file;

                      if(is_file($path)){

                        $IDStudentTexting = $query[0]['IDStudentTexting'];
                        $student_testimonials['Photo'] = $upload;

                        $this->db->where('ID',$IDStudentTexting);
                        $this->db->update('db_prodi.student_testimonials',$student_testimonials);
                        unlink($path);
                      }

                    
                    // update prodi_textting
                    $IDProdiTexting = $query[0]['IDProdiTexting'];
                    $prodi_texting['ProdiID'] = $prodi_active_id;
                    $prodi_texting['UpdatedAt'] = $this->m_rest->getDateTimeNow();

                    $this->db->where('ID',$IDProdiTexting);
                    $this->db->update('db_prodi.prodi_texting',$prodi_texting);

                }
            }

            return print_r(1);
        }
        else if($data_arr['action']=='saveDataPhoto'){
            if (array_key_exists('uploadFile', $_FILES)) { // jika file di upload
                $upload = $this->m_master->uploadDokumenMultiple(uniqid(),'uploadFile',$path = './images/Kaprodi');
                $upload = json_encode($upload);
                // convert file
                $upload = json_decode($upload,true);
                $upload = $upload[0];

                $dataForm = $data_arr;

                // check action insert or update
                $sql = 'select ps.*from db_prodi.prodi_sambutan as ps 
                    where ps.ProdiID = ?
                ';

                $ProdiID = $prodi_active_id;               
                $query = $this->db->query($sql, array($ProdiID))->result_array();
                if (count($query) == 0) { // insert
                    
                    $datasave['Photo'] = $upload;
                    $datasave['ProdiID'] = $prodi_active_id;
                    $this->db->insert('db_prodi.prodi_sambutan',$datasave);
                   
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
                        $this->db->update('db_prodi.prodi_sambutan',$dataupdate);
                        unlink($path);
                      }
                      else{
                        $ID = $query[0]['ID'];
                        $dataupdate['Photo'] = $upload;
                        $this->db->where('ID',$ID);
                        $this->db->update('db_prodi.prodi_sambutan',$dataupdate);
                      }
                       
                }

            }

            return print_r(1);
        }
        else if($data_arr['action']=='readProdiPhoto'){

            $data = $this->db->query('SELECT ps.* FROM db_prodi.prodi_sambutan ps 
                                                  WHERE ps.ProdiID = '.$prodi_active_id.'
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
            $sql = 'select c.Tlp,c.IDProdiTexting from db_prodi.calldetail as c 
                join db_prodi.prodi_texting as pt on pt.ID = c.IDProdiTexting
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
                $this->db->update('db_prodi.prodi_texting',$prodi_texting);
                // update prodi_texting
                
                $calldetail = $dataForm['calldetail'];
                $this->db->where('IDProdiTexting', $ID);
                $this->db->update('db_prodi.calldetail',$calldetail);
                
            } else {
                // insert prodi_texting
                $prodi_texting['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $prodi_texting['ProdiID'] = $prodi_active_id;;

                $this->db->insert('db_prodi.prodi_texting',$prodi_texting);
                $IDProdiTexting = $this->db->insert_id();
                // insert callaction
                $calldetail = [
                    'IDProdiTexting' => $IDProdiTexting,
                    'Tlp' => $calldetail['Tlp'],
                    'ProdiID' => $prodi_active_id,
                ];

                $this->db->insert('db_prodi.calldetail',$calldetail);
            }

            return print_r(1 );
        }
        else if($data_arr['action']=='readProdiPartner'){

             $data = $this->db->get('db_prodi.partner')->result_array();

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='saveDataPartner'){

            if (array_key_exists('uploadFile', $_FILES)) { // jika file di upload
                $upload = $this->m_master->uploadDokumenMultiple(uniqid(),'uploadFile',$path = './images/Partner');
                $upload = json_encode($upload); 
                // convert file
                $upload = json_decode($upload,true);
                $upload = $upload[0];

                $dataForm = (array) $data_arr['dataForm'];
                
                $dataForm['ProdiID'] = $prodi_active_id;
                $dataForm['CreateAt'] = $this->m_rest->getDateTimeNow();
                $dataform['CreateBy'] = $this->session->userdata('NIP');
                $dataForm['Images']= $upload;
                $this->db->insert('db_prodi.partner',$dataForm);
            }

            return print_r(1);
        }
        else if($data_arr['action']=='readDataPartner'){
            $ID = $data_arr['ID'];
            
            $data = $this->db->get_where('db_prodi.partner',array(
                'ProdiID' => $prodi_active_id,
                'ID' => $ID,
                
            ))->result_array();

            return print_r(json_encode($data));

        }
        
        else if ($data_arr['action']=='deleteDataPartner') 
        {
            $ID = $data_arr['ID'];
            $this->db->where('ID', $ID);
            $this->db->delete('db_prodi.partner'); 
            return print_r(1);
        }

        else if($data_arr['action']=='loadDataLecturer'){

            $data = $this->db->query('SELECT l.* , l.photo,em.Name FROM db_prodi.lecturer l
                                      INNER JOIN db_employees.employees as em ON em.NIP=l.NIP
                                      WHERE l.ProdiID = "'.$prodi_active_id.'" ')->result_array();
            // $data = $this->db->get_where('db_prodi.lecturer',array(
            //     'ProdiID' => $prodi_active_id,
                
            // ))->result_array();

            return print_r(json_encode($data));

        }

        else if($data_arr['action']=='saveDataLecturer'){

            if (array_key_exists('uploadFile', $_FILES)) { // jika file di upload
                $upload = $this->m_master->uploadDokumenMultiple(uniqid(),'uploadFile',$path = './images/Lecturer');
                $upload = json_encode($upload); 
                // convert file
                $upload = json_decode($upload,true);
                $upload = $upload[0];

                $dataForm = (array) $data_arr['dataForm'];
                // check action insert or update
                $sql = 'select l.* from db_prodi.lecturer as l  join db_employees.employees as em on l.NIP = em.NIP
                    where l.NIP = ? and l.ProdiID= ?
                ';

                $NIP = $dataForm['NIP'];
                $ProdiID = $prodi_active_id;
                $query = $this->db->query($sql, array($NIP,$ProdiID))->result_array();
                   if (count($query) == 0) { 
                        $dataForm['ProdiID'] = $prodi_active_id;
                        $dataForm['Photo'] = $upload;
                        $dataForm['CreateAt'] = $this->m_rest->getDateTimeNow();
                        $dataform['CreateBy'] = $this->session->userdata('NIP');
                        
                        $this->db->insert('db_prodi.lecturer',$dataForm);
                        }
                   else{
                        $arr_file =  $query[0]['Photo'];
                        $path = './images/Lecturer/'. $arr_file;

                          if(is_file($path)){
                            $IDLecture = $query[0]['ID'];
                            $dataupdate['Photo'] = $upload;
                            $this->db->where('ID',$IDLecture);
                            $this->db->update('db_prodi.lecturer',$dataupdate);
                            unlink($path);
                          }
                          else{
                            $IDLecture = $query[0]['ID'];
                            $dataupdate['Photo'] = $upload;
                            $this->db->where('ID',$IDLecture);
                            $this->db->update('db_prodi.lecturer',$dataupdate);
                          }
                    }

                
            }

            return print_r(1);
        }
        else if ($data_arr['action']=='deleteDataLecturer') 
        {
            $ID = $data_arr['ID'];
            $this->db->where('ID', $ID);
            $this->db->delete('db_prodi.lecturer'); 
            return print_r(1);
        }

        else if($data_arr['action']=='saveDataFacilities'){

            if (array_key_exists('uploadFile', $_FILES)) { // jika file di upload
                $upload = $this->m_master->uploadDokumenMultiple(uniqid(),'uploadFile',$path = './images/Facilities');
                $upload = json_encode($upload); 
                // convert file
                $upload = json_decode($upload,true);
                $upload = $upload[0];

                $dataForm = (array) $data_arr['dataForm'];
                // check action insert or update
                // $sql = 'select fc.*, fc.Photo,fc.ProdiID from db_prodi.facilities as fc  join db_academic.program_study as ps on fc.ProdiID = ps.ID
                //     where fc.ProdiID = ?
                // ';
                // $ProdiID = $prodi_active_id;
                // $query = $this->db->query($sql, array($ProdiID))->result_array();
                // if (count($query) == 0) { 
                //     $dataForm['ProdiID'] = $prodi_active_id;
                //     $dataForm['Photo'] = $upload;
                //     $dataForm['CreateAt'] = $this->m_rest->getDateTimeNow();
                //     $dataform['CreateBy'] = $this->session->userdata('NIP');
                    
                //     $this->db->insert('db_prodi.facilities',$dataForm);
                //     }
               // else{ // Update
               //      $arr_file =  $query[0]['Photo'];
               //      $path = './images/Facilities/'. $arr_file;

               //        if(is_file($path)){
               //          $ID = $query[0]['ID'];
               //          $dataupdate = $dataForm;
               //          $dataupdate['Photo'] = $upload;
               //          $this->db->where('ID',$ID);
               //          $this->db->update('db_prodi.facilities',$dataupdate);
               //          unlink($path);
               //        }
               //        else{
               //          $ID = $query[0]['ID'];
               //          $dataupdate = $dataForm;
               //          $dataupdate['Photo'] = $upload;
               //          $this->db->where('ID',$ID);
               //          $this->db->update('db_prodi.facilities',$dataupdate);
               //        }
               //  }
                    $dataForm['ProdiID'] = $prodi_active_id;
                    $dataForm['Photo'] = $upload;
                    $dataForm['CreateAt'] = $this->m_rest->getDateTimeNow();
                    $dataform['CreateBy'] = $this->session->userdata('NIP');
                    
                    $this->db->insert('db_prodi.facilities',$dataForm);
                
            }

            return print_r(1);
        }
        else if($data_arr['action']=='readProdiFacilities'){
            
            $data = $this->db->get_where('db_prodi.facilities',array(
                'ProdiID' => $prodi_active_id,
                
            ))->result_array();

            return print_r(json_encode($data));

        }
        else if ($data_arr['action']=='deleteDataFacilities') 
        {
            // check action insert or update
                $sql = 'select fc.*, fc.Photo,fc.ProdiID from db_prodi.facilities as fc  join db_academic.program_study as ps on fc.ProdiID = ps.ID
                    where fc.ProdiID = ?
                ';
                $ProdiID = $prodi_active_id;
                $query = $this->db->query($sql, array($ProdiID))->result_array();

                $ID = $data_arr['ID'];

                $this->db->where('ID', $ID);
                $this->db->delete('db_prodi.facilities'); 
                $arr_file =  $query[0]['Photo'];
                
                $path = './images/Facilities/'. $arr_file;
                unlink($path);
            return print_r(1);
        }

    }


    function getProdiLecturer(){
        $prodi_active_id = $this->session->userdata('prodi_active_id');
        $key = $this->input->post('key');
        $data = 'SELECT em.NIP, em.Name, em.Gender, em.PositionMain, em.ProdiID, ps.NameEng AS         ProdiNameEng
                FROM db_employees.employees em
                INNER JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                WHERE (em.PositionMain = "14.6" OR em.PositionMain = "14.7" )  AND ( ';//dosen kaprodi
        $data.= ' em.NIP LIKE "'.$key.'%" ';
        $data.= ' OR em.Name LIKE "'.$key.'%" ';
        $data.= ' OR ps.ID LIKE "'. $prodi_active_id .'%" ';
        $data.= ') ORDER BY Name ASC';

        $query = $this->db->query($data)->result_array();
        return print_r(json_encode($query));
    }

    
    function getDataProdiTexting(){


        $data_arr = $this->getInputToken2();

        $LangCode = $data_arr['LangCode'];
        $ProdiID = $data_arr['ProdiID'];
        $Type = $data_arr['Type'];

        $data = $this->db->query('SELECT pt.*, l.language FROM db_prodi.prodi_texting pt 
                                            LEFT JOIN db_prodi.language l ON (l.ID = pt.LangID)
                                            WHERE l.Code LIKE "'.$LangCode.'"
                                             AND pt.ProdiID = "'.$ProdiID.'"
                                             AND pt.Type = "'.$Type.'" ')->result_array();

        return print_r(json_encode($data));

    }

    function getDetailProdi(){
        $data_arr = $this->getInputToken2();
        $LangCode = $data_arr['LangCode'];
        $ProdiID = $data_arr['ProdiID'];

        $data = $this->db->query('SELECT ps.Name, ps.NameEng, em.Name AS Kaprodi, em.TitleAhead, em.TitleBehind , psm.Photo  
                                            FROM db_academic.program_study ps 
                                            LEFT JOIN db_employees.employees em ON (em.NIP = ps.KaprodiID)
                                            LEFT JOIN db_prodi.prodi_sambutan psm ON psm.ProdiID = ps.ID
                                            WHERE ps.ID = "'.$ProdiID.'" ')->result_array();

        if(count($data)>0){
            $data[0]['ProdiName'] = ($LangCode=='Ind') ? $data[0]['Name'] : $data[0]['NameEng'];
            // $DefaultPhoto = base_url('images/Kaprodi/default.png');
            $data[0]['Photo'] = ($data[0]['Photo']!='' && $data[0]['Photo']!=null) ? $data[0]['Photo'] :  'default.png';
        }

        return print_r(json_encode($data));
    }

    function getDosenProdi(){
        $data_arr = $this->getInputToken2();
        $ProdiID = $data_arr['ProdiID'];

        $data = $this->db->query('SELECT l.*,em.Name , em.TitleAhead, em.TitleBehind, ps.Name as ProdiName FROM db_academic.program_study ps
                                  INNER JOIN db_prodi.lecturer l ON (l.ProdiID = ps.ID)
                                  LEFT JOIN db_employees.employees em ON (em.NIP = l.NIP)
                                  WHERE l.ProdiID = "'.$ProdiID.'"')->result_array();
        

        return print_r(json_encode($data));
    }
    function getStudentsProdi(){

        $key = $this->input->post('key');
        $data_arr = $this->getInputToken2();
        $ProdiID =  $this->session->userdata('prodi_active_id');

        $data = $this->db->query('SELECT NPM, Name,ProdiID FROM db_academic.auth_students ats 
                                                    WHERE  (ats.ProdiID = "'.$ProdiID.'")
                                                    AND (ats.NPM LIKE "%'.$key.'%" 
                                                    OR ats.Name LIKE "%'.$key.'%")')->result_array();

        return print_r(json_encode($data));

    }

    function getTestiProdi(){

        $data_arr = $this->getInputToken2();
        $LangCode = $data_arr['LangCode'];
        if($LangCode=='Ind'){
            $LangCode1=2;
        }else{
            $LangCode1=1;
        }
        $ProdiID = $data_arr['ProdiID'];
        $Type = $data_arr['Type'];

        $data = $this->db->query('SELECT pt.*, l.Language ,st.Photo,ast.Name,ast.NPM,ps.Name AS Name1, ps.NameEng
                                        FROM db_prodi.prodi_texting pt 
                                        LEFT JOIN db_prodi.language l ON (pt.LangID = l.ID)
                                        LEFT JOIN db_prodi.student_testimonials_details std ON (std.IDProdiTexting = pt.ID)
                                        LEFT JOIN db_prodi.student_testimonials st ON (st.ID = std.IDStudentTexting)
                                        LEFT JOIN db_academic.auth_students ast ON (ast.NPM = st.NPM)
                                        LEFT JOIN db_prodi.calldetail c ON (c.IDProdiTexting = pt.ID)
                                        LEFT JOIN db_academic.program_study ps ON (ps.ID = pt.ProdiID)
                                        WHERE pt.LangID = "'.$LangCode1.'" AND pt.ProdiID = "'.$ProdiID.'" AND pt.Type="'.$Type.'" ')->result_array();
       
        return print_r(json_encode($data));
        

    }
    function getPartnerProdi(){
        $data_arr = $this->getInputToken2();
        $ProdiID = $data_arr['ProdiID'];

        $data = $this->db->query('SELECT * FROM db_prodi.partner prt
                                  WHERE prt.ProdiID = "'.$ProdiID.'"')->result_array();
        
        return print_r(json_encode($data));
    }



}
