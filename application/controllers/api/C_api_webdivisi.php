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
            $this->db->where('ID', $ID);
            $this->db->delete('db_webdivisi.slider'); 
            //delete images
            $arr_file =  $query[0]['Images'];
            $path = './images/Slider/'. $arr_file;
            unlink($path);
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
                $sql = 'select st.NPM,st.Photo,std.IDStudentTexting,std.IDProdiTexting from db_webdivisi.student_testimonials as st join db_webdivisi.student_testimonials_details as std on st.ID = std.IDStudentTexting
                    join db_webdivisi.prodi_texting as pt on pt.ID = std.IDProdiTexting
                    where st.NPM = ? and pt.LangID = ? and pt.Type = ?
                ';

                $NPM = $student_testimonials['NPM'];
                $LangID = $prodi_texting['LangID'];
                $Type = $prodi_texting['Type'];
                $query=$this->db->query($sql, array($NPM,$LangID,$Type))->result_array();
                if (count($query) == 0) { // insert

                    // insert prodi_texting
                    $prodi_texting['DivisiID'] = $divisi_active_id;
                    $prodi_texting['UpdatedAt'] = $this->m_rest->getDateTimeNow();

                    $this->db->insert('db_webdivisi.prodi_texting',$prodi_texting);
                    $IDProdiTexting = $this->db->insert_id();

                    // insert student_testimonials
                    $student_testimonials['Photo'] = $upload;
                    $student_testimonials['DivisiID'] = $divisi_active_id;

                    $this->db->insert('db_webdivisi.student_testimonials',$student_testimonials);
                    $IDStudentTexting = $this->db->insert_id();

                    // insert student_testimonials_details
                    $student_testimonials_details = [
                        'IDStudentTexting' => $IDStudentTexting,
                        'IDProdiTexting' => $IDProdiTexting,
                    ];
                    $this->db->insert('db_webdivisi.student_testimonials_details',$student_testimonials_details);
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
                        $this->db->update('db_webdivisi.student_testimonials',$student_testimonials);
                        unlink($path);
                      }

                    
                    // update prodi_textting
                    $IDProdiTexting = $query[0]['IDProdiTexting'];
                    $prodi_texting['DivisiID'] = $divisi_active_id;
                    $prodi_texting['UpdatedAt'] = $this->m_rest->getDateTimeNow();

                    $this->db->where('ID',$IDProdiTexting);
                    $this->db->update('db_webdivisi.prodi_texting',$prodi_texting);

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
        else if($data_arr['action']=='readProdiPartner'){
            $data_arr = $this->getInputToken2();
            $DivisiID = $divisi_active_id;
            // print_r($DivisiID);
            $data = $this->db->query('SELECT * FROM db_webdivisi.partner WHERE DivisiID = '.$DivisiID.'')->result_array();   
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
                
                $dataForm['DivisiID'] = $divisi_active_id;
                $dataForm['CreateAt'] = $this->m_rest->getDateTimeNow();
                $dataform['CreateBy'] = $this->session->userdata('NIP');
                $dataForm['Images']= $upload;
                $this->db->insert('db_webdivisi.partner',$dataForm);
            }

            return print_r(1);
        }
        else if($data_arr['action']=='readDataPartner'){
            $ID = $data_arr['ID'];
            
            $data = $this->db->get_where('db_webdivisi.partner',array(
                'DivisiID' => $divisi_active_id,
                'ID' => $ID,
                
            ))->result_array();

            return print_r(json_encode($data));

        }
        
        else if ($data_arr['action']=='deleteDataPartner') 
        {
            $sql = 'select * from db_webdivisi.partner  where DivisiID= ?';
            $DivisiID = $divisi_active_id;
            $query = $this->db->query($sql, array($DivisiID))->result_array();

            $ID = $data_arr['ID'];
            $this->db->where('ID', $ID);
            $this->db->delete('db_webdivisi.partner'); 
            //delete images
            $arr_file =  $query[0]['Images'];
            $path = './images/Partner/'. $arr_file;
            unlink($path);

            return print_r(1);
        }

        else if($data_arr['action']=='loadDataLecturer'){

            $data = $this->db->query('SELECT l.* , l.photo,em.Name FROM db_webdivisi.lecturer l
                                      INNER JOIN db_employees.employees as em ON em.NIP=l.NIP
                                      WHERE l.DivisiID = "'.$divisi_active_id.'" ')->result_array();
            // $data = $this->db->get_where('db_webdivisi.lecturer',array(
            //     'DivisiID' => $divisi_active_id,
                
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
                $sql = 'select l.* from db_webdivisi.lecturer as l  join db_employees.employees as em on l.NIP = em.NIP
                    where l.NIP = ? and l.DivisiID= ?
                ';

                $NIP = $dataForm['NIP'];
                $DivisiID = $divisi_active_id;
                $query = $this->db->query($sql, array($NIP,$DivisiID))->result_array();
                   if (count($query) == 0) { 
                        $dataForm['DivisiID'] = $divisi_active_id;
                        $dataForm['Photo'] = $upload;
                        $dataForm['CreateAt'] = $this->m_rest->getDateTimeNow();
                        $dataform['CreateBy'] = $this->session->userdata('NIP');
                        
                        $this->db->insert('db_webdivisi.lecturer',$dataForm);
                        }
                   else{
                        $arr_file =  $query[0]['Photo'];
                        $path = './images/Lecturer/'. $arr_file;

                          if(is_file($path)){
                            $IDLecture = $query[0]['ID'];
                            $dataupdate['Photo'] = $upload;
                            $this->db->where('ID',$IDLecture);
                            $this->db->update('db_webdivisi.lecturer',$dataupdate);
                            unlink($path);
                          }
                          else{
                            $IDLecture = $query[0]['ID'];
                            $dataupdate['Photo'] = $upload;
                            $this->db->where('ID',$IDLecture);
                            $this->db->update('db_webdivisi.lecturer',$dataupdate);
                          }
                    }

                
            }

            return print_r(1);
        }
        else if ($data_arr['action']=='deleteDataLecturer') 
        {
            $sql = 'select * from db_webdivisi.lecturer  where ID= ?';
            $DivisiID = $divisi_active_id;
            $ID = $data_arr['ID'];
            $query = $this->db->query($sql, array($ID))->result_array();

            $ID = $query[0]['ID'];
            $arr_file =  $query[0]['Photo'];
            
            $this->db->where('ID', $ID);
            $this->db->delete('db_webdivisi.lecturer'); 
            //delete images
            
            $path = './images/Lecturer/'. $arr_file;
            unlink($path);
           
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
                
                $dataForm['DivisiID'] = $divisi_active_id;
                $dataForm['Photo'] = $upload;
                $dataForm['CreateAt'] = $this->m_rest->getDateTimeNow();
                $dataform['CreateBy'] = $this->session->userdata('NIP');
                
                $this->db->insert('db_webdivisi.facilities',$dataForm);
                
            }

            return print_r(1);
        }
        else if($data_arr['action']=='readProdiFacilities'){
            
            $data = $this->db->get_where('db_webdivisi.facilities',array(
                'DivisiID' => $divisi_active_id,
                
            ))->result_array();

            return print_r(json_encode($data));

        }
        else if ($data_arr['action']=='deleteDataFacilities') 
        {
            // check action insert or update
            $sql = 'select fc.*, fc.Photo,fc.DivisiID from db_webdivisi.facilities as fc  join db_academic.program_study as ps on fc.DivisiID = ps.ID
                where fc.DivisiID = ?
            ';
            $DivisiID = $divisi_active_id;
            $query = $this->db->query($sql, array($DivisiID))->result_array();

            $ID = $data_arr['ID'];

            $this->db->where('ID', $ID);
            $this->db->delete('db_webdivisi.facilities'); 
            $arr_file =  $query[0]['Photo'];
            
            $path = './images/Facilities/'. $arr_file;
            unlink($path);
            return print_r(1);
        }
        else if ($data_arr['action']=='deleteTestimonials') 
        {
            $sql = 'SELECT pt.*, l.Language ,st.Photo,ast.Name,ast.NPM,c.Tlp,std.IDStudentTexting,std.IDProdiTexting
                                FROM db_webdivisi.prodi_texting pt 
                                LEFT JOIN db_webdivisi.language l ON (pt.LangID = l.ID)
                                LEFT JOIN db_webdivisi.student_testimonials_details std ON (std.IDProdiTexting = pt.ID)
                                LEFT JOIN db_webdivisi.student_testimonials st ON (st.ID = std.IDStudentTexting)
                                LEFT JOIN db_academic.auth_students ast ON (ast.NPM = st.NPM)
                                LEFT JOIN db_webdivisi.calldetail c ON (c.IDProdiTexting = pt.ID) 
                                where pt.ID = ?';

            $ID = $data_arr['ID'];
            $query = $this->db->query($sql, array($ID))->result_array();
            $ID = $query[0]['ID'];
            $IDProdiTexting=$query[0]['IDProdiTexting'];
            $IDStudentTexting=$query[0]['IDStudentTexting'];
            //prodi_texting
            $this->db->where('ID', $ID);
            $this->db->delete('db_webdivisi.prodi_texting'); 
            //student_testimonials
            $this->db->where('ID', $IDStudentTexting);
            $this->db->delete('db_webdivisi.student_testimonials');
            //student_testimonials_details
            $this->db->where('IDProdiTexting', $IDProdiTexting);
            $this->db->delete('db_webdivisi.student_testimonials_details'); 

            $arr_file =  $query[0]['Photo'];
            $path = './images/Testimonials/'. $arr_file;
            unlink($path);
            return print_r(1);
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
            // $data_arr = $this->getInputToken2();
            // $divisi_active_id = $data_arr['DivisiID'];
            $data = $this->db->query('SELECT cd.* FROM db_webdivisi.contact_detail cd 
                                                  WHERE cd.DivisiID = '.$divisi_active_id.'
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


    function getDivisiLecturer(){
        $divisi_active_id = $this->session->userdata('IDdepartementNavigation');
        $key = $this->input->post('key');
        $data = 'SELECT em.NIP, em.Name, em.Gender, em.PositionMain, em.DivisiID, ps.NameEng AS  ProdiNameEng
                FROM db_employees.employees em
                INNER JOIN db_academic.program_study ps ON (ps.ID = em.DivisiID)
                WHERE (em.PositionMain = "14.6" OR em.PositionMain = "14.7" )  AND ( ';//dosen kaprodi
        $data.= ' em.NIP LIKE "'.$key.'%" ';
        $data.= ' OR em.Name LIKE "'.$key.'%" ';
        $data.= ' OR ps.ID LIKE "'. $divisi_active_id .'%" ';
        $data.= ') ORDER BY Name ASC';

        $query = $this->db->query($data)->result_array();
        return print_r(json_encode($query));
    }

    
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
    public function GetDataContentKnowledge(){
        $data_arr = $this->getInputToken2();       
        $LangCode = $data_arr['LangCode'];        
        $DivisiID = $data_arr['DivisiID'];
        $Type = $data_arr['Type'];
        $IDCat = $data_arr['IDCat'];
        // print_r($LangCode);

        $rs= [];
        $dataKnowledge = $this->db->query(
            'select * from db_webdivisi.category_knowledge'
        )->result_array();

        for ($i=0; $i < count($dataKnowledge) ; $i++) { 
            $dataProdiText = $this->db->query(
                'select Title,File from db_webdivisi.prodi_texting as pt
                where pt.ID_CatBase = '.$IDCat.' and 

                '
            )->result_array();
        }

        if($IDCat==''){

            $data = $this->db->query('SELECT cat.ID as IDCat,cat.Name,pt.DivisiID,COUNT(pt.ID_CatBase) jml, pt.* FROM db_webdivisi.category_knowledge cat 
                                        LEFT JOIN ( select pt2.* from db_webdivisi.prodi_texting as pt2 
                                        LEFT JOIN db_webdivisi.language l ON (l.ID = pt2.LangID) 
                                        WHERE l.Code LIKE "'.$LangCode.'" AND pt2.DivisiID="'.$DivisiID.'" AND pt2.Type = "'.$Type.'") pt on cat.ID=pt.ID_CatBase
                                        GROUP BY cat.ID,cat.Name')->result_array();
        }else{
            
            $data = $this->db->query('SELECT cat.ID as IDCat,cat.Name,pt.DivisiID,COUNT(pt.ID_CatBase) jml, pt.* FROM db_webdivisi.category_knowledge cat 
                                        LEFT JOIN ( select pt2.* from db_webdivisi.prodi_texting as pt2 
                                        LEFT JOIN db_webdivisi.language l ON (l.ID = pt2.LangID) 
                                        WHERE l.Code LIKE "'.$LangCode.'" AND pt2.DivisiID="'.$DivisiID.'" AND pt2.Type = "'.$Type.'" 
                                        AND pt2.ID_CatBase="'.$IDCat.'") pt on cat.ID=pt.ID_CatBase
                                        GROUP BY cat.ID,cat.Name')->result_array();
        }
        // print_r($data);die();
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

    function getDosenDivisi(){
        $data_arr = $this->getInputToken2();
        $LangCode = $data_arr['LangCode'];
        $DivisiID = $data_arr['DivisiID'];

        $data = $this->db->query('SELECT * FROM db_employees.employees em
                                  WHERE em.PositionMain like "%'.$DivisiID.'.%" AND em.Status="1" ORDER BY em.PositionMain ASC')->result_array();
        
        return print_r(json_encode($data));
    }

    function getStudentsDivisi(){

        $key = $this->input->post('key');
        $data_arr = $this->getInputToken2();
        $DivisiID =  $this->session->userdata('IDdepartementNavigation');

        $data = $this->db->query('SELECT NPM, Name,DivisiID FROM db_academic.auth_students ats 
                                                    WHERE  (ats.DivisiID = "'.$DivisiID.'")
                                                    AND (ats.NPM LIKE "%'.$key.'%" 
                                                    OR ats.Name LIKE "%'.$key.'%")')->result_array();

        return print_r(json_encode($data));

    }

    function getTestiDivisi(){

        $data_arr = $this->getInputToken2();
        $LangCode = $data_arr['LangCode'];
        if($LangCode=='Ind'){
            $LangCode1=2;
        }else{
            $LangCode1=1;
        }
        $DivisiID = $data_arr['DivisiID'];
        $Type = $data_arr['Type'];

        $data = $this->db->query('SELECT pt.*, l.Language ,st.Photo,ast.Name,ast.NPM,ps.Name AS Name1, ps.NameEng
                                        FROM db_webdivisi.prodi_texting pt 
                                        LEFT JOIN db_webdivisi.language l ON (pt.LangID = l.ID)
                                        LEFT JOIN db_webdivisi.student_testimonials_details std ON (std.IDProdiTexting = pt.ID)
                                        LEFT JOIN db_webdivisi.student_testimonials st ON (st.ID = std.IDStudentTexting)
                                        LEFT JOIN db_academic.auth_students ast ON (ast.NPM = st.NPM)
                                        LEFT JOIN db_webdivisi.calldetail c ON (c.IDProdiTexting = pt.ID)
                                        LEFT JOIN db_academic.program_study ps ON (ps.ID = pt.DivisiID)
                                        WHERE pt.LangID = "'.$LangCode1.'" AND pt.DivisiID = "'.$DivisiID.'" AND pt.Type="'.$Type.'" ')->result_array();
       
        return print_r(json_encode($data));
        

    }
    function getPartnerDivisi(){
        $data_arr = $this->getInputToken2();
        $DivisiID = $data_arr['DivisiID'];

        $data = $this->db->query('SELECT * FROM db_webdivisi.partner WHERE DivisiID = '.$DivisiID.'')->result_array();
        if(count($data)>0){
            // $DefaultPhoto = base_url('images/Slider/default.jpg');
            $data[0]['Images'] = ($data[0]['Images']!='' && $data[0]['Images']!=null) ? $data[0]['Images'] :  'default.png';
            // $data[0]['Images'] = base_url('images/slider/default.jpg');
        }
        return print_r(json_encode($data));
    }

    function getSliderDivisi(){
        $data_arr = $this->getInputToken2();
        $DivisiID = $data_arr['DivisiID'];

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
