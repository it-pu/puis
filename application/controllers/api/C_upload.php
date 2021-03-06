<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_upload extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('akademik/m_onlineclass');
        $this->load->model('master/m_master');
        $this->load->library('JWT');
        $this->load->library('google');
    }


    // Upload File Skripsi
    function upload_skripsi(){

        $fileName = $this->input->get('fileName');
        $NPM = $this->input->get('n');
        $column = $this->input->get('c');

        if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
            $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
            $path = 'document/'.$NPM;
            $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin,$fileName,'userfile',$path,'string');
            if (!empty($uploadNas)) {
                $fileName = $uploadNas;
                // Cek mhs
                $getStd = $this->db->get_where('db_academic.final_project_files',array(
                    'NPM' => $NPM
                ))->result_array();

                if(count($getStd)>0){
                    $this->db->where('NPM', $NPM);
                    $this->db->update('db_academic.final_project_files',array(
                        $column => $fileName
                    ));
                } else {
                    $arr = array(
                        'NPM' => $NPM,
                        $column => $fileName
                    );
                    $this->db->insert('db_academic.final_project_files',$arr);
                }

                return print_r(1);
            }
            else
            {
                print_r('Upload to nas failed');die();
            }
        }
        else
        {
            $path = './uploads/document/'.$NPM;
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
                return print_r(0);
            }
            else {

                // Cek mhs
                $getStd = $this->db->get_where('db_academic.final_project_files',array(
                    'NPM' => $NPM
                ))->result_array();

                if(count($getStd)>0){
                    $this->db->where('NPM', $NPM);
                    $this->db->update('db_academic.final_project_files',array(
                        $column => $fileName
                    ));
                } else {
                    $arr = array(
                        'NPM' => $NPM,
                        $column => $fileName
                    );
                    $this->db->insert('db_academic.final_project_files',$arr);
                }

                return print_r(1);


            }
        }

    }

    function remove_skripsi(){
        $fileName = $this->input->get('fileName');
        $NPM = $this->input->get('n');
        $column = $this->input->get('c');
        $result = 0;

        if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
            $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
            $path = ($_SERVER['SERVER_NAME'] == 'localhost') ? "localhost/document/".$NPM.'/'.$fileName : "pcam/document/".$NPM.'/'.$fileName;
            $this->m_master->DeleteFileToNas($headerOrigin,$path);
        }
        else
        {
            $path = './uploads/document/'.$NPM.'/'.$fileName;

            if (file_exists($path)) {

                unlink($path);

                // Update DB


            }
        }

       

        $this->db->where('NPM', $NPM);
        $this->db->update('db_academic.final_project_files',array(
            $column => ''
        ));

        $result = 1;

        return print_r($result);


    }

    // Upload task
    function upload_task(){

        $f = $this->input->get('f');

        $lanjut = true;
        $file_name = '';
        if($f==1 || $f=='1'){

            $unix_name = $this->input->post('formNameFile');
            if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                if (array_key_exists('userfile', $_FILES)) {
                    $path = 'task';
                    $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin,$unix_name,'userfile',$path,'string');
                    $file_name = $uploadNas;
                }
                else
                {
                    $error = array('error' =>'File not selected');
                    $lanjut = false;
                    return print_r(json_encode($error));
                }
            }
            else{
                $config['upload_path']          = './uploads/task/';
                $config['allowed_types']        = 'pdf';
                $config['max_size']             = 8000;
    //        $config['max_width']            = 1024;
    //        $config['max_height']           = 768;
                $config['file_name'] = $unix_name;
                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload('userfile'))
                {
                    $error = array('error' => $this->upload->display_errors());
                    $lanjut = false;
                    return print_r(json_encode($error));
                }
                else
                {
                    $success = array('success' => $this->upload->data());
                    $file_name = $success['success']['file_name'];
                }
            } 

        }

        if($lanjut){

            $data_insert = array(
                'ScheduleID' => $this->input->post('formScheduleID'),
                'Session' => $this->input->post('formSession'),
                'NIP' => $this->input->post('formNIP'),
                'Title' => $this->input->post('formTitle'),
                'Description' => $this->input->post('formDescription'),
                'File' => $file_name,
                'EntredBy' => $this->input->post('formNIP'),
                'EntredAt' => $this->m_rest->getDateTimeNow()
            );

            $this->db->insert('db_academic.schedule_task',$data_insert);
            $idInsert = $this->db->insert_id();
            $success['success']['InsertID'] = $idInsert;

            return print_r(json_encode($success));

        }

    }

    function upload_exam_task(){

        $f = $this->input->get('f');
        $act = $this->input->get('formAction');

        $lanjut = true;
        $file_name = '';
        if($f==1 || $f=='1'){

            $formNameFileOld = $this->input->post('formNameFileOld');
            $unix_name = $this->input->post('formNameFile');

            if($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id'){
                $headerOrigin = serverRoot;
                if (array_key_exists('userfile', $_FILES)) {
                    $path_delete = "pcam/task-exam/".$formNameFileOld;
                    $this->m_master->DeleteFileToNas($headerOrigin,$path_delete);
                    $path = 'task-exam';
                    $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin,$unix_name,'userfile',$path,'string');
                    $file_name = $uploadNas;
                } else {
                    $error = array('error' =>'File not selected');
                    $lanjut = false;
                    return print_r(json_encode($error));
                }
            }
            else {

                $Path = './uploads/task-exam/'.$formNameFileOld;

                if($formNameFileOld!='' && file_exists($Path)){
                    unlink($Path);
                }


                $config['upload_path']          = './uploads/task-exam/';
                $config['allowed_types']        = 'pdf';
                $config['max_size']             = 8000;
//        $config['max_width']            = 1024;
//        $config['max_height']           = 768;
                $config['file_name'] = $unix_name;
                $this->load->library('upload', $config);
                if ( ! $this->upload->do_upload('userfile'))
                {
                    $error = array('error' => $this->upload->display_errors());
                    $lanjut = false;
                    return print_r(json_encode($error));
                }
                else
                {
                    $success = array('success' => $this->upload->data());
                    $file_name = $success['success']['file_name'];
                }

            }



        }


        if($lanjut){
            $data_insert = array(
                'ExamID' => $this->input->post('formExamID'),
                'Description' => $this->input->post('formDescription'),
                'File' => $file_name,
                'UpdatedBy' => $this->input->post('formNIP'),
                'UpdatedAt' => $this->m_rest->getDateTimeNow()
            );

            // Cek apakah examID sudah ada apa blm jika sudah maka upddate
            $dataCk = $this->db->get_where('db_academic.exam_task',
                array('ExamID' => $this->input->post('formExamID')))->result_array();

            if(count($dataCk)<=0){
                $this->db->insert('db_academic.exam_task',$data_insert);
                $idInsert = $this->db->insert_id();


            } else {
                unset($data_insert['ExamID']);
                if($f==0 || $f=='0'){
                    unset($data_insert['File']);
                }
                $this->db->where('ExamID', $this->input->post('formExamID'));
                $this->db->update('db_academic.exam_task',$data_insert);
                $idInsert = 1;
            }

            $success['success']['InsertID'] = $idInsert;
            return print_r(json_encode($success));
        }

    }

    function remove_exam_task($IDExamTask){

        $data = $this->db->get_where('db_academic.exam_task',array('ID' =>
            $IDExamTask))->result_array();
        if(count($data)>0){
            $d = $data[0];
            if($d['File']!='' && $d['File']!=null){

                $path = './uploads/task-exam/'.$d['File'];
                if(file_exists($path)){
                    unlink($path);
                }
            }

            $this->db->where('ID', $IDExamTask);
            $this->db->delete('db_academic.exam_task');

        }

        return print_r(1);

    }

    function upload_task_std(){

        $f = $this->input->get('f');

        $lanjut = true;
        $file_name = '';
        if($f==1 || $f=='1'){

            $unix_name = $this->input->post('formNameFile');
            if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                 $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                 if (array_key_exists('userfile', $_FILES)) {
                    $path = 'task';
                    $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin,$unix_name,'userfile',$path,'string');
                    $file_name = $uploadNas;
                 }
                 else
                 {
                     $error = array('error' =>'File not selected');
                     $lanjut = false;
                     return print_r(json_encode($error));
                 }
            }
            else
            {
               $config['upload_path']          = './uploads/task/';
               $config['allowed_types']        = 'pdf';
               $config['max_size']             = 8000;
   //        $config['max_width']            = 1024;
   //        $config['max_height']           = 768;
               $config['file_name'] = $unix_name;
               $this->load->library('upload', $config);
               if ( ! $this->upload->do_upload('userfile'))
               {
                   $error = array('error' => $this->upload->display_errors());
                   $lanjut = false;
                   return print_r(json_encode($error));
               }
               else
               {
                   $success = array('success' => $this->upload->data());
                   $file_name = $success['success']['file_name'];
               } 
            }
        }

        if($lanjut){

            // Get ScheduleID & Session
            $dataSS = $this->db->get_where('schedule_task',
                array('ID'=>$this->input->post('formIDST')))->result_array();

            $ScheduleID = $dataSS[0]['ScheduleID'];
            $Session = $dataSS[0]['Session'];
            $NPM = $this->input->post('formNPM');

            $data_insert = array(
                'IDST' => $this->input->post('formIDST'),
                'NPM' => $this->input->post('formNPM'),
                'Description' => $this->input->post('formDescription'),
                'File' => $file_name,
                'EntredBy' => $this->input->post('formNPM'),
                'EntredAt' => $this->m_rest->getDateTimeNow()
            );

            $this->db->insert('db_academic.schedule_task_student',$data_insert);
            $idInsert = $this->db->insert_id();
            $success['success']['InsertID'] = $idInsert;

            // Cek attendace online
            $this->m_onlineclass->checkOnlineAttendance($NPM,$ScheduleID,$Session);

            return print_r(json_encode($success));

        }





    }

    function upload_exam_task_std()
    {

        $f = $this->input->get('f');

        $lanjut = true;
        $file_name = '';
        if ($f == 1 || $f == '1') {
            $formNameFileOld = $this->input->post('formNameFileOld');
            $unix_name = $this->input->post('formNameFile');
            if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                // delete first
                $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                $path_delete = ($_SERVER['SERVER_NAME'] == 'localhost') ? "localhost/task-exam/".$formNameFileOld : "pcam/task-exam/".$formNameFileOld;
                $this->m_master->DeleteFileToNas($headerOrigin,$path_delete);
                if (array_key_exists('userfile', $_FILES)) {
                    $path = 'task-exam';
                    $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin,$unix_name,'userfile',$path,'string');
                    $file_name = $uploadNas;
                }
                else
                {
                    $error = array('error' =>'File not selected');
                    $lanjut = false;
                    return print_r(json_encode($error));
                }
            }
            else
            {
                $Path = './uploads/task-exam/' . $formNameFileOld;

                if ($formNameFileOld != '' && file_exists($Path)) {
                    unlink($Path);
                }
                
                $config['upload_path'] = './uploads/task-exam/';
                $config['allowed_types'] = 'pdf';
                $config['max_size'] = 8000;
    //        $config['max_width']            = 1024;
    //        $config['max_height']           = 768;
                $config['file_name'] = $unix_name;
                $this->load->library('upload', $config);
                if (!$this->upload->do_upload('userfile')) {
                    $error = array('error' => $this->upload->display_errors());
                    $lanjut = false;
                    return print_r(json_encode($error));
                } else {
                    $success = array('success' => $this->upload->data());
                    $file_name = $success['success']['file_name'];
                }
            }
        }


        if ($lanjut) {

            if ($f == 1 || $f == '1') {
                $data_insert = array(
                    'Description' => $this->input->post('formDescription'),
                    'File' => $file_name,
                    'SavedAt' => $this->m_rest->getDateTimeNow()
                );
            } else {
                $data_insert = array(
                    'Description' => $this->input->post('formDescription'),
                    'SavedAt' => $this->m_rest->getDateTimeNow()
                );
            }

            $this->db->where(array(
                'ExamID'=> $this->input->post('formExamID'),
                'NPM'=> $this->input->post('formNPM')
            ));
            $this->db->update('db_academic.exam_student_online', $data_insert);

            $success['success']['InsertID'] = $this->input->post('formExamID');
            return print_r(json_encode($success));

        }
    }


    // ==================
    //Upload image summernote
    function summernote_upload_image(){

        header('Content-Type: text/html; charset=UTF-8');

        if(isset($_FILES["image"]["name"])){

            $SummernoteID = $this->input->get('id');
            $unixTime = strtotime($this->m_rest->getDateTimeNow());
            $ext = explode('.',$_FILES["image"]["name"]);
            $unix_name = $SummernoteID.'_'.$unixTime.'.'.$ext[1];

            if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                $path = 'summernote/images';
                $uploadNas = $this->m_master->UploadOneFilesToNas($headerOrigin,$unix_name,'image',$path,'string');
                $fileName = $uploadNas;

                // Update data temporary summernote
                $this->db->insert('db_it.summernote_image',
                    array('Image'=>$fileName,
                        'SummernoteID' => $SummernoteID));

                echo base_url('uploads/summernote/images/').$fileName;

            }
            else {
                $this->load->library('upload');

                $pathFolderUrl = 'uploads/summernote/images/';
                $pathFolder = './'.$pathFolderUrl;

                $config['upload_path'] = $pathFolder;
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['file_name'] = $unix_name;
                $this->upload->initialize($config);
                if(!$this->upload->do_upload('image')){
                    $this->upload->display_errors();
                    return FALSE;
                }
                else{


                    $data = $this->upload->data();
                    //Compress Image
                    $config['image_library']='gd2';
                    $config['source_image']= $pathFolder.$data['file_name'];
                    $config['create_thumb']= FALSE;
                    $config['maintain_ratio']= TRUE;
                    $config['quality']= '60%';
                    $config['width']= 800;
                    $config['height']= 800;
                    $config['new_image']= $pathFolder.$data['file_name'];
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();

                    // Update data temporary summernote
                    $this->db->insert('db_it.summernote_image',
                        array('Image'=>$data['file_name'],
                            'SummernoteID' => $SummernoteID));

                    echo base_url().$pathFolderUrl.$data['file_name'];
                }
            }


        }
    }

    //Delete image summernote
    function summernote_delete_image(){
        header('Content-Type: text/html; charset=UTF-8');
        $src = $this->input->post('src');
        $file_path = str_replace(base_url(), '', $src);

        // Get file name
        $file_name = str_replace(base_url('uploads/summernote/images/'), '', $src);

        if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id'){
            $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
            $path = ($_SERVER['SERVER_NAME'] == 'localhost')
                ? 'localhost/summernote/images/'.$file_name : 'pcam/summernote/images/'.$file_name;

            $this->m_master->DeleteFileToNas($headerOrigin,$path);
            $this->db->where('Image',$file_name);
            $this->db->delete('db_it.summernote_image');
        } else {

            if(unlink($file_path)){
                $this->db->where('Image',$file_name);
                $this->db->delete('db_it.summernote_image');
            }
        }

        echo 'File Delete Successfully';


    }



}