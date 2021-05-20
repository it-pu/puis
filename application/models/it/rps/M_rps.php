<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_rps extends CI_Model {

    // function __construct()
    // {
    //     parent::__construct();
    //     $this->load->model('m_rest');
    //     $this->load->model('master/m_master');
    // }

    public function __getKurikulumByYear($SemesterSearch,$year,$ProdiID){

        // Mendapatkan Kurikulum
        $detail_kurikulum = $this->Kurikulum($year);

        if($detail_kurikulum!=''){

            // Mendapatkan Total Semester Yang ada dalam kurikulum ini
            $semester = $this->Semester($detail_kurikulum['ID']);

            for($i=0;$i<count($semester);$i++){
                $semester[$i]['DetailSemester'] = $this->DetailMK($SemesterSearch,$detail_kurikulum['ID'],$ProdiID,$semester[$i]['Semester']);
            }

            

            $result = array(
                'DetailKurikulum' => $detail_kurikulum,
                'MataKuliah' => $semester,
            );
        } else {
            $result = false;
        }

        return $result;
    }

    
    private function Kurikulum($year){
        $data = $this->db->query('SELECT c.*,e.Name AS CreateByName, e2.Name AS UpdateByName FROM db_academic.curriculum c
                                              JOIN db_employees.employees e ON (c.CreateBy = e.NIP)
                                              JOIN db_employees.employees e2 ON (c.UpdateBy = e2.NIP)
                                              WHERE c.Year ="'.$year.'" LIMIT 1');

        if(count($data->result_array())>0){
            return $data->result_array()[0];
        } else {
            return false;
        }

    }

    private function Semester($CurriculumID){
        $data = $this->db->query('SELECT cd.Semester
                                      FROM db_academic.curriculum_details cd
                                      WHERE cd.CurriculumID="'.$CurriculumID.'" GROUP BY cd.Semester;');

        return $data->result_array();
    }


    private function DetailMK($SemesterSearch,$CurriculumID,$ProdiID,$Semester){
        $select = 'SELECT
                    ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng,
                    mk.MKCode, mk.Name AS NameMK, mk.NameEng AS NameMKEng,
                    cd.ID AS CDID, cd.CurriculumID, cd.Semester , cd.TotalSKS, cd.SKSTeori,
                    cd.SKSPraktikum, cd.SKSPraktikLapangan, cd.MKType, cd.DataPrecondition,
                    cd.Syllabus, cd.SAP, cd.StatusMK, cd.StatusPrecondition,
                    em.Name AS NameLecturer,edu.Name AS EducationLevel, (SELECT count(rb.CDID) 
                    FROM db_academic.rps_basic rb WHERE rb.CDID=cd.ID) AS RPS, (SELECT count(rcpmk.CDID) 
                    FROM db_academic.rps_cpmk rcpmk WHERE rcpmk.CDID=cd.ID) AS CPMK, (SELECT count(rcpl.CDID) 
                    FROM db_academic.rps_cpl rcpl WHERE rcpl.CDID=cd.ID) AS CPL, (SELECT count(rdm.CDID) 
                    FROM db_academic.rps_desc_mk rdm WHERE rdm.CDID=cd.ID) AS DESCMK, (SELECT count(rm.CDID) 
                    FROM db_academic.rps_material rm WHERE rm.CDID=cd.ID) AS material';

        if($ProdiID!=''){
            $data = $this->db->query($select.' FROM db_academic.curriculum_details cd
                                                LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)
                                                LEFT JOIN db_academic.program_study ps ON (cd.ProdiID = ps.ID)
                                                LEFT JOIN db_employees.employees em ON (cd.LecturerNIP = em.NIP)
                                                LEFT JOIN db_academic.education_level edu ON (edu.ID = cd.EducationLevelID)
                                                WHERE cd.CurriculumID="'.$CurriculumID.'"
                                                AND cd.Semester="'.$Semester.'"
                                                AND cd.ProdiID="'.$ProdiID.'"
                                                ORDER BY mk.MKCode ASC')->result_array();
        } else {
            $data = $this->db->query($select.' FROM db_academic.curriculum_details cd
                                                LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)
                                                LEFT JOIN db_academic.program_study ps ON (cd.ProdiID = ps.ID)
                                                LEFT JOIN db_employees.employees em ON (cd.LecturerNIP = em.NIP)
                                                LEFT JOIN db_academic.education_level edu ON (edu.ID = cd.EducationLevelID)
                                                WHERE cd.CurriculumID="'.$CurriculumID.'"
                                                AND cd.Semester="'.$Semester.'"
                                                ORDER BY mk.MKCode ASC')->result_array();
        }

        if(count($data)>0 && $SemesterSearch!=''){
            $dataSMT = $this->db->query('SELECT * FROM db_academic.semester WHERE Status = 1 LIMIT 1')->result_array();
            for($i=0;$i<count($data);$i++){
                $data[$i]['Offering'] = false;
                $dataOffering = $this->db->query('SELECT co.Arr_CDID FROM db_academic.course_offerings co
                                    WHERE
                                    co.SemesterID = "'.$dataSMT[0]['ID'].'"
                                    AND co.CurriculumID = "'.$CurriculumID.'"
                                    AND co.ProdiID = "'.$ProdiID.'"
                                    AND co.Semester = "'.$SemesterSearch.'" LIMIT 1 ')->result_array();


                if(count($dataOffering)){
                    $dataCourse = json_decode($dataOffering[0]['Arr_CDID']);

                    if(in_array($data[$i]['CDID'],$dataCourse)){
                        $data[$i]['Offering'] = true;
                    }

                }

            }
        }


        return $data;
    }
    
}
