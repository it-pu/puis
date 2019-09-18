<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_cooperation extends Cooperation_Controler {
    public $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('master/m_master');
        $this->data['department'] = parent::__getDepartement();
    }

    public function kerja_sama_perguruan_tinggi()
    {
      $content = $this->load->view('page/'.$this->data['department'].'/kerjasama-perguruan-tinggi/index',$this->data,true);
      $this->temp($content);
    }

    public function kerja_sama_perguruan_tinggi_submit()
    {
        $rs = ['Status' => 0,'msg' => ''];
        $Input = $this->getInputToken();
        $mode = $Input['mode'];
        switch ($mode) {
            case 'add':
                // Save data kerjasama
                $kerjasama = json_decode(json_encode($Input['kerjasama']),true);
                $Upload = $this->m_master->uploadDokumenMultiple(uniqid(),'BuktiUpload',$path = './uploads/cooperation');
                $Upload = json_encode($Upload);
                $kerjasama['BuktiUpload'] = $Upload;
                // add upload bukti kerjasama
                $this->db->insert('db_cooperation.kerjasama',$kerjasama);
                $insert_id = $this->db->insert_id();
                $ID = $insert_id;

                $Perjanjian = json_decode(json_encode($Input['k_perjanjian']),true);
                $arr_post_file_perjanjian = ['Upload_MOU','Upload_MOA','Upload_IA'];
                $k_perjanjian = [];
                for ($i=0; $i < count($arr_post_file_perjanjian); $i++) { 
                    $PostName = $arr_post_file_perjanjian[$i];
                    $ex = explode('_', $PostName);
                    for ($j=0; $j < count($Perjanjian); $j++) { 
                        if ($ex[1] == $Perjanjian[$j]) {

                            // upload file
                            $Upload = $this->m_master->uploadDokumenMultiple(uniqid(),$PostName,$path = './uploads/cooperation');
                            $Upload = json_encode($Upload); 

                            $k_perjanjian[] = array(
                                'KerjasamaID' => $ID,
                                'Type' => $Perjanjian[$j],
                                'Upload' => $Upload,
                            );    
                            break;
                        }
                    }
                }
                
                $this->db->insert_batch('db_cooperation.k_perjanjian', $k_perjanjian);
                // insert department
                $DepartmentSelected = json_decode(json_encode($Input['k_department']),true);
                $k_department = [];
                for ($i=0; $i < count($DepartmentSelected); $i++) { 
                    $k_department[] = array(
                        'KerjasamaID' => $ID,
                        'Departement' => $DepartmentSelected[$i],
                    );
                }
                $this->db->insert_batch('db_cooperation.k_department', $k_department);
                $rs['Status'] = 1;
                break;
            case 'edit':
                $ID = $Input['ID'];
                $kerjasama = json_decode(json_encode($Input['kerjasama']),true);
                // kerjasama
                $G_kerjasama = $this->m_master->caribasedprimary('db_cooperation.kerjasama','ID',$ID);
                if (array_key_exists('BuktiUpload', $_FILES)) {
                    if ($G_kerjasama[0]['BuktiUpload'] != '' && $G_kerjasama[0]['BuktiUpload'] != null) {
                        $arr_file = (array) json_decode($G_kerjasama[0]['BuktiUpload'],true);
                        $filePath = 'cooperation\\'.$arr_file[0]; // pasti ada file karena required
                        $path = FCPATH.'uploads\\'.$filePath;
                        unlink($path);
                    }

                    $Upload = $this->m_master->uploadDokumenMultiple(uniqid(),'BuktiUpload',$path = './uploads/cooperation');
                    $Upload = json_encode($Upload);
                    $kerjasama['BuktiUpload'] = $Upload;
                }

                $this->db->where('ID',$ID);
                $this->db->update('db_cooperation.kerjasama',$kerjasama);

                // perjanjian
                $Perjanjian = json_decode(json_encode($Input['k_perjanjian']),true);
                $arr_post_file_perjanjian = ['Upload_MOU','Upload_MOA','Upload_IA'];
                $G_k_perjanjian = $this->m_master->caribasedprimary('db_cooperation.k_perjanjian','KerjasamaID',$ID);
                for ($i=0; $i < count($arr_post_file_perjanjian); $i++) { 
                    $PostName = $arr_post_file_perjanjian[$i];
                    $ex = explode('_', $PostName);
                    $Type = $ex[1];
                    $FindToDel = false;
                    for ($j=0; $j < count($Perjanjian); $j++) { 
                        if ($Type == $Perjanjian[$j]) {
                            $FindToDel = true;
                            // check dengan data db
                            $ID_Perjanjian = '';
                            $FileDt = '';
                            $find = false; // search to existing data
                            for ($k=0; $k < count($G_k_perjanjian); $k++) { 
                                $TypeDB = $G_k_perjanjian[$k]['Type'];
                                if ($Type == $TypeDB) {
                                    $find = true;
                                    $ID_Perjanjian = $G_k_perjanjian[$k]['ID'];
                                    $FileDt = $G_k_perjanjian[$k]['Upload'];
                                    // do edit
                                    $dataSave = [];
                                    if (array_key_exists($PostName, $_FILES)) {
                                        if ($FileDt != '' && $FileDt != null) {
                                            $arr_file = (array) json_decode($FileDt,true);
                                            $filePath = 'cooperation\\'.$arr_file[0]; // pasti ada file karena required
                                            $path = FCPATH.'uploads\\'.$filePath;
                                            unlink($path);
                                        }

                                        $Upload = $this->m_master->uploadDokumenMultiple(uniqid(),$PostName,$path = './uploads/cooperation');
                                        $Upload = json_encode($Upload);
                                        $dataSave['Upload'] = $Upload;

                                        $this->db->where('ID',$ID_Perjanjian);
                                        $this->db->update('db_cooperation.k_perjanjian',$dataSave);
                                    }

                                    break;
                                }
                            }

                            if (!$find) { // insert
                                $dataSave = [];
                                // upload file
                                $Upload = json_encode('');
                                if (array_key_exists($PostName, $_FILES)) {
                                    $Upload = $this->m_master->uploadDokumenMultiple(uniqid(),$PostName,$path = './uploads/cooperation');
                                    $Upload = json_encode($Upload);
                                }    
                                $dataSave = array(
                                    'KerjasamaID' => $ID,
                                    'Type' => $Perjanjian[$j],
                                    'Upload' => $Upload,
                                );
                                $this->db->insert('db_cooperation.k_perjanjian',$dataSave); 
                            }

                            break;

                        }
                    }

                    if (!$FindToDel) {
                       // delete data existing
                        for ($k=0; $k < count($G_k_perjanjian); $k++) { 
                            $TypeDB = $G_k_perjanjian[$k]['Type'];
                            if ($Type == $TypeDB) {
                                // print_r('TypeDB');
                               // delete data
                                $ID_Perjanjian = $G_k_perjanjian[$k]['ID'];
                                $FileDt = $G_k_perjanjian[$k]['Upload'];
                                if ($FileDt != '' && $FileDt != null) {
                                    $arr_file = (array) json_decode($FileDt,true);
                                    $filePath = 'cooperation\\'.$arr_file[0]; // pasti ada file karena required
                                    $path = FCPATH.'uploads\\'.$filePath;
                                    unlink($path);
                                }

                                $this->db->where('ID',$ID_Perjanjian);
                                $this->db->delete('db_cooperation.k_perjanjian');

                            }
                        }
                    }
                }

                // Department
                // delete && insert
                    // delete
                    $this->db->where('KerjasamaID',$ID);
                    $this->db->delete('db_cooperation.k_department');

                    // insert
                    $DepartmentSelected = json_decode(json_encode($Input['k_department']),true);
                    $k_department = [];
                    for ($i=0; $i < count($DepartmentSelected); $i++) { 
                        $k_department[] = array(
                            'KerjasamaID' => $ID,
                            'Departement' => $DepartmentSelected[$i],
                        );
                    }
                    $this->db->insert_batch('db_cooperation.k_department', $k_department);
                    $rs['Status'] = 1;

                break;
            case 'delete':
                $ID = $Input['ID'];
                // kerjasama
                $G_kerjasama = $this->m_master->caribasedprimary('db_cooperation.kerjasama','ID',$ID);
                if ($G_kerjasama[0]['BuktiUpload'] != '' && $G_kerjasama[0]['BuktiUpload'] != null) {
                    $arr_file = (array) json_decode($G_kerjasama[0]['BuktiUpload'],true);
                    $filePath = 'cooperation\\'.$arr_file[0]; // pasti ada file karena required
                    $path = FCPATH.'uploads\\'.$filePath;
                    unlink($path);
                }

                $this->db->where('ID',$ID);
                $this->db->delete('db_cooperation.kerjasama');

                $G_k_perjanjian = $this->m_master->caribasedprimary('db_cooperation.k_perjanjian','KerjasamaID',$ID);
                for ($i=0; $i < count($G_k_perjanjian); $i++) { 
                    $ID_Perjanjian = $G_k_perjanjian[$i]['ID'];
                    $FileDt = $G_k_perjanjian[$i]['Upload'];
                    if ($FileDt != '' && $FileDt != null) {
                        $arr_file = (array) json_decode($FileDt,true);
                        $filePath = 'cooperation\\'.$arr_file[0]; // pasti ada file karena required
                        $path = FCPATH.'uploads\\'.$filePath;
                        unlink($path);
                    }

                    $this->db->where('ID',$ID_Perjanjian);
                    $this->db->delete('db_cooperation.k_perjanjian');    
                }

                $this->db->where('KerjasamaID',$ID);
                $this->db->delete('db_cooperation.k_department');

                $rs['Status'] = 1;
            break;
            default:
                # code...
                break;
        }

        echo json_encode($rs);


    }

}