<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_api extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_rest');
        $this->load->model('master/m_master');
    }

    public function getClassOf()
    {
        $data = $this->db->query('SELECT ast.Year FROM db_academic.auth_students ast
                                                  GROUP BY ast.Year');

        return $data->result_array();
    }

    public function __getGradeByIDKurikulum($CurriculumID)
    {
        $data = $this->db->query('SELECT * FROM db_academic.grade WHERE CurriculumID = "' . $CurriculumID . '" ');
        return $data->result_array();
    }

    public function __getMataKuliahByIDKurikulum($CurriculumID)
    {
        $data = $this->db->query('SELECT ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng,
                                          mk.MKCode, mk.Name AS NameMK, mk.NameEng AS NameMKEng,
                                          cd.Semester , cd.TotalSKS,
                                          em.Name AS NameLecturer
                                    FROM db_academic.mata_kuliah mk
                                    JOIN db_academic.curriculum_details cd ON (mk.ID = cd.MKID)
                                    JOIN db_academic.program_study ps ON (cd.ProdiID = ps.ID)
                                    JOIN db_employees.employees em ON (cd.LecturerNIP = em.NIP)
                                    WHERE cd.CurriculumID="' . $CurriculumID . '" ORDER BY ProdiName');
        return $data->result_array();
    }

    public function __getBaseProdi()
    {
        $data = $this->db->query('SELECT * FROM db_academic.program_study');
        return $data->result_array();
    }

    public function __getBaseProdiSelectOption()
    {
        //        $data = $this->db->query('SELECT ID,Code,Name,NameEng FROM db_academic.program_study');
        $data = $this->db->query('SELECT ps.ID,ps.Code,ps.Name,ps.NameEng, el.Name as Level
                                                      FROM db_academic.program_study ps
                                                      LEFT JOIN db_academic.education_level el ON (el.ID = ps.EducationLevelID)
                                                      WHERE ps.Status=1
                                                      ORDER BY el.EducationLevelID, ps.Name ASC');
        return $data->result_array();
    }

    public function __getBaseProdiSelectOptionAll()
    {
        $data = $this->db->query('SELECT ps.ID,ps.Code,ps.Name,ps.NameEng, el.Name as Level
                                                      FROM db_academic.program_study ps
                                                      LEFT JOIN db_academic.education_level el ON (el.ID = ps.EducationLevelID)
                                                      ORDER BY el.EducationLevelID, ps.Name ASC');
        //        $data = $this->db->query('SELECT ID,Code,Name,NameEng FROM db_academic.program_study WHERE Status=1');
        return $data->result_array();
    }

    public function __getMKByID($ID)
    {
        $data = $this->db->query('SELECT mk.*, ps.Code AS ProdiCode FROM db_academic.mata_kuliah mk
                                    LEFT JOIN db_academic.program_study ps ON (mk.BaseProdiID = ps.ID)
                                    WHERE mk.ID = "' . $ID . '" LIMIT 1');
        return $data->result_array();
    }

    public function __getLecturer()
    {
        $data = $this->db->query('SELECT * FROM db_employees.employees WHERE StatusEmployeeID != -2 AND PositionMain = "14.7"');
        return $data->result_array();
    }

    public function __getAllMK()
    {
        $data = $this->db->query('SELECT mk.*,pg.Code, pg.Name AS NameProdi
                                    FROM db_academic.mata_kuliah mk
                                    JOIN db_academic.program_study pg
                                    ON (mk.BaseProdiID = pg.ID)');
        return $data->result_array();
    }


    // ==== KURIKULUM ====
    public function __getKurikulumByYear($SemesterSearch, $year, $ProdiID)
    {

        // Mendapatkan Kurikulum
        $detail_kurikulum = $this->Kurikulum($year);

        if ($detail_kurikulum != '') {

            // Mendapatkan Total Semester Yang ada dalam kurikulum ini
            $semester = $this->Semester($detail_kurikulum['ID']);

            for ($i = 0; $i < count($semester); $i++) {
                $semester[$i]['DetailSemester'] = $this->DetailMK($SemesterSearch, $detail_kurikulum['ID'], $ProdiID, $semester[$i]['Semester']);
            }

            $result = array(
                'DetailKurikulum' => $detail_kurikulum,
                'MataKuliah' => $semester
            );
        } else {
            $result = false;
        }

        return $result;
    }

    private function Kurikulum($year)
    {
        $data = $this->db->query('SELECT c.*,e.Name AS CreateByName, e2.Name AS UpdateByName FROM db_academic.curriculum c
                                              JOIN db_employees.employees e ON (c.CreateBy = e.NIP)
                                              JOIN db_employees.employees e2 ON (c.UpdateBy = e2.NIP)
                                              WHERE c.Year ="' . $year . '" LIMIT 1');

        if (count($data->result_array()) > 0) {
            return $data->result_array()[0];
        } else {
            return false;
        }
    }

    private function Semester($CurriculumID)
    {
        $data = $this->db->query('SELECT cd.Semester
                                      FROM db_academic.curriculum_details cd
                                      WHERE cd.CurriculumID="' . $CurriculumID . '" GROUP BY cd.Semester;');

        return $data->result_array();
    }


    private function DetailMK($SemesterSearch, $CurriculumID, $ProdiID, $Semester)
    {
        $select = 'SELECT
                    ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng,
                    mk.MKCode, mk.Name AS NameMK, mk.NameEng AS NameMKEng,
                    cd.ID AS CDID, cd.CurriculumID, cd.Semester , cd.TotalSKS, cd.SKSTeori,
                    cd.SKSPraktikum, cd.SKSPraktikLapangan, cd.MKType, cd.DataPrecondition,
                    cd.Syllabus, cd.SAP, cd.StatusMK, cd.StatusPrecondition,
                    em.Name AS NameLecturer,edu.Name AS EducationLevel';

        if ($ProdiID != '') {
            $data = $this->db->query($select . ' FROM db_academic.curriculum_details cd
                                                LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)
                                                LEFT JOIN db_academic.program_study ps ON (cd.ProdiID = ps.ID)
                                                LEFT JOIN db_employees.employees em ON (cd.LecturerNIP = em.NIP)
                                                LEFT JOIN db_academic.education_level edu ON (edu.ID = cd.EducationLevelID)
                                                WHERE cd.CurriculumID="' . $CurriculumID . '"
                                                AND cd.Semester="' . $Semester . '"
                                                AND cd.ProdiID="' . $ProdiID . '"
                                                ORDER BY mk.MKCode ASC')->result_array();
        } else {
            $data = $this->db->query($select . ' FROM db_academic.curriculum_details cd
                                                LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)
                                                LEFT JOIN db_academic.program_study ps ON (cd.ProdiID = ps.ID)
                                                LEFT JOIN db_employees.employees em ON (cd.LecturerNIP = em.NIP)
                                                LEFT JOIN db_academic.education_level edu ON (edu.ID = cd.EducationLevelID)
                                                WHERE cd.CurriculumID="' . $CurriculumID . '"
                                                AND cd.Semester="' . $Semester . '"
                                                ORDER BY mk.MKCode ASC')->result_array();
        }

        if (count($data) > 0 && $SemesterSearch != '') {
            $dataSMT = $this->db->query('SELECT * FROM db_academic.semester WHERE Status = 1 LIMIT 1')->result_array();
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['Offering'] = false;
                $dataOffering = $this->db->query('SELECT co.Arr_CDID FROM db_academic.course_offerings co
                                    WHERE
                                    co.SemesterID = "' . $dataSMT[0]['ID'] . '"
                                    AND co.CurriculumID = "' . $CurriculumID . '"
                                    AND co.ProdiID = "' . $ProdiID . '"
                                    AND co.Semester = "' . $SemesterSearch . '" LIMIT 1 ')->result_array();


                if (count($dataOffering)) {
                    $dataCourse = json_decode($dataOffering[0]['Arr_CDID']);

                    if (in_array($data[$i]['CDID'], $dataCourse)) {
                        $data[$i]['Offering'] = true;
                    }
                }
            }
        }


        return $data;
    }


    public function cekTahunKurikulum($year)
    {
        $data = $this->db->query('SELECT * FROM db_academic.curriculum WHERE Year = "' . $year . '"');

        return $data->result_array();
    }

    public function __getKurikulumSelectOption()
    {
        $data = $this->db->query('SELECT * FROM db_academic.curriculum ORDER BY Year DESC')->result_array();

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $dataS = $this->db->query('SELECT s.Status FROM db_academic.semester s
                                                      WHERE s.Year = "' . $data[$i]['Year'] . '"
                                                      ORDER BY s.Status DESC LIMIT 1 ')
                    ->result_array();
                $data[$i]['StatusSemester'] = (count($dataS) > 0) ? $dataS[0]['Status'] : '0';
            }
        }

        return $data;
    }

    public function __getKurikulumSelectOptionASC()
    {
        $data = $this->db->query('SELECT * FROM db_academic.curriculum ORDER BY Year ASC');

        return $data->result_array();
    }

    public function __getKurikulumSelectOptionDSC()
    {
        $data = $this->db->query('SELECT * FROM db_academic.curriculum ORDER BY Year DESC');

        return $data->result_array();
    }

    public function __geteducationLevel()
    {
        $data = $this->db->query('SELECT * FROM db_academic.education_level ORDER BY EducationLevelID DESC');

        return $data->result_array();
    }

    public function __getDosenSelectOption()
    {
        $data = $this->db->query('SELECT ID,NIP,NIDN,Name FROM db_employees.employees WHERE StatusEmployeeID != -1 AND StatusEmployeeID != -2 AND
                                                                    (PositionMain = "14.5"
                                                                    OR PositionMain = "14.6"
                                                                    OR PositionMain = "14.7"

                                                                    OR PositionOther1 = "14.5"
                                                                    OR PositionOther1 = "14.6"
                                                                    OR PositionOther1 = "14.7"

                                                                    OR PositionOther2 = "14.5"
                                                                    OR PositionOther2 = "14.6"
                                                                    OR PositionOther2 = "14.7"

                                                                    OR PositionOther3 = "14.5"
                                                                    OR PositionOther3 = "14.6"
                                                                    OR PositionOther3 = "14.7")

                                                                    ');
        return $data->result_array();
    }

    public function __getItemKuriklum($table)
    {

        $data = $this->db->query('SELECT * FROM db_academic.' . $table);
        return $data->result_array();
    }

    public function __getdetailKurikulum($CDID)
    {

        $data = $this->db->query('SELECT cd.*,
                                    ct.Name AS NameCurriculumType,
                                    ps.Name AS NameProdi,
                                    el.Name AS NameEducationLevel,
                                    cg.Name AS NameCoursesGroups,
                                    em.Name AS NameLecturer,
                                    mk.Name AS NameMK,
                                    mk.NameEng AS NameMKEng
                                    FROM db_academic.curriculum_details cd
                                    LEFT JOIN db_academic.curriculum_types ct ON (ct.ID = cd.CurriculumTypeID)
                                    LEFT JOIN db_academic.program_study ps ON (ps.ID = cd.ProdiID)
                                    LEFT JOIN db_academic.education_level el ON (el.ID = cd.EducationLevelID)
                                    LEFT JOIN db_academic.courses_groups cg ON (cg.ID = cd.CoursesGroupsID)
                                    LEFT JOIN db_employees.employees em ON (cd.LecturerNIP = em.NIP)
                                    LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)
                                    WHERE cd.ID = "' . $CDID . '" ')->result_array();


        if ($data[0]['StatusPrecondition'] == 1) {
            $dataPre = json_decode($data[0]['DataPrecondition']);

            $pre_arr = [];
            for ($i = 0; $i < count($dataPre); $i++) {
                $exp = explode('.', $dataPre[$i]);
                $pre = $this->db->query('SELECT ID,MKcode,Name,NameEng FROM db_academic.mata_kuliah
                                            WHERE ID="' . $exp[0] . '"')->result_array();

                array_push($pre_arr, $pre[0]);
            }

            $data[0]['DetailPrecondition'] = $pre_arr;
        }

        //        print_r($data);
        //        exit;

        return $data;
    }


    public function __genrateMKCode($ID)
    {
        $data = $this->db->query('SELECT count(*) AS TotalMK FROM db_academic.mata_kuliah WHERE BaseProdiID="' . $ID . '" ');
        return $data->result_array();
    }

    public function __cekMKCode($MKCode)
    {
        $data = $this->db->query('SELECT MKCode FROM db_academic.mata_kuliah WHERE MKCode LIKE "' . $MKCode . '" ');
        return $data->result_array();
    }

    public function __cekTotalLAD($ladID)
    {
        $data = $this->db->query('SELECT * FROM db_academic.lecturers_availability_detail
                                        WHERE LecturersAvailabilityID="' . $ladID . '" ');

        return $data->result_array();
    }

    public function __crudDataDetailTahunAkademik($id)
    {

        $data = $this->db->query('SELECT * FROM db_academic.semester
                                    WHERE ID = "' . $id . '"')->result_array();

        if (count($data) > 0) {
            $dataDetail = $this->db->query('SELECT * FROM db_academic.academic_years
                                              WHERE SemesterID = "' . $id . '"')->result_array();

            //            $dt = (count($dataDetail)>0) ? $dataDetail[0] : '';

            // Get Setting Attd
            $dataSetting = $this->db->get_where('db_academic.attendance_setting', array('SemesterID' => $id))->result_array();

            if (count($dataDetail) > 0) {
                $result['DetailTA'] = $dataDetail[0];
            }

            // Get Status Employees
            $dataStatus = $this->db->query('SELECT * FROM db_employees.employees_status WHERE IDStatus != -1 AND  IDStatus != -2 ')->result_array();

            // Get setting Online Class
            $dataOnlineClass = $this->db->select('Forum,Task,Quiz')
                ->get_where('db_academic.setting_online_class', array('SemesterID' => $id))
                ->result_array();

            $result['StatusEmployees'] = $dataStatus;
            $result['TahunAkademik'] = $data[0];
            $result['AttdSetting'] = $dataSetting;
            $result['OnlineClass'] = $dataOnlineClass;
        } else {
            $result = false;
        }
        return $result;
    }

    public function __getAcademicYearOnPublish()
    {
        $data = $this->db->query('SELECT * FROM db_academic.semester s WHERE s.Status=1');

        return $data->result_array();
    }

    public function getMataKuliahSingle($ID, $MKCode)
    {
        $data = $this->db->query('SELECT mk.*,cd.Semester,cd.TotalSKS FROM db_academic.mata_kuliah mk
                                      LEFT JOIN db_academic.curriculum_details cd ON (mk.ID = cd.MKID AND mk.MKCode=cd.MKCode)
                                      WHERE mk.ID="' . $ID . '" AND mk.MKCode = "' . $MKCode . '" ');
        return $data->result_array();
    }

    public function getMatakuliahOfferings($SemesterID, $MKID, $MKCode)
    {

        $data = $this->db->query('SELECT cd.Semester, cd.TotalSKS FROM db_academic.course_offerings co
                                           LEFT JOIN db_academic.curriculum_details cd ON (co.CurriculumDetailID = cd.ID)
                                           WHERE co.SemesterID = "' . $SemesterID . '" AND cd.MKID = "' . $MKID . '" AND cd.MKCode = "' . $MKCode . '"
                                           ');

        return $data->result_array();
    }

    public function getProgramCampus()
    {
        $data = $this->db->query('SELECT * FROM db_academic.programs_campus ORDER BY ID ASC');

        return $data->result_array();
    }

    public function getSemester($order)
    {
        $data = $this->db->query('SELECT * FROM db_academic.semester ORDER BY ID ' . $order);

        return $data->result_array();
    }

    public function getSemesterAntara($order)
    {
        $data = $this->db->query('SELECT * FROM db_academic.semester_antara ORDER BY ID ' . $order);

        return $data->result_array();
    }

    public function getSemesterActive($SemesterID, $CurriculumID, $ProdiID, $Semester, $IsSemesterAntara)
    {
        $data = $this->db->query('SELECT * FROM db_academic.semester WHERE ID = "' . $SemesterID . '" ')->result_array();

        $result = array(
            'SemesterActive' => $data[0],
            'DetailCourses' => $this->getDetailCourses($data[0]['ID'], $CurriculumID, $ProdiID, $Semester, $IsSemesterAntara)
        );

        return $result;
    }

    private function getDetailCourses($SemesterID, $CurriculumID, $ProdiID, $Semester, $IsSemesterAntara)
    {

        $data = $this->db->query('SELECT cd.ID AS CurriculumDetailID,cd.Semester, cd.MKType, cd.MKID, mk.MKCode, cd.TotalSKS, cd.StatusMK,
                                    mk.Name AS MKName, mk.NameEng AS MKNameEng,
                                    ps.Code AS ProdiCode, ps.Name AS ProdiName, ps.NameEng AS ProdiNameEng
                                    FROM db_academic.curriculum_details cd
                                    LEFT JOIN db_academic.program_study ps ON (cd.ProdiID = ps.ID)
                                    LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)

                                    WHERE cd.CurriculumID = "' . $CurriculumID . '"
                                    AND cd.ProdiID = "' . $ProdiID . '"
                                    AND cd.Semester = "' . $Semester . '"

                                    ORDER BY cd.Semester , ps.Code ASC')->result_array();



        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['Offering'] = false;
                $dataOffering = $this->db->query('SELECT co.Arr_CDID FROM db_academic.course_offerings co
                                    WHERE
                                    co.SemesterID = "' . $SemesterID . '"
                                    AND co.CurriculumID = "' . $CurriculumID . '"
                                    AND co.ProdiID = "' . $ProdiID . '"
                                    AND co.Semester = "' . $Semester . '"
                                    AND co.IsSemesterAntara = "' . $IsSemesterAntara . '" LIMIT 1 ')->result_array();

                if (count($dataOffering)) {
                    $dataCourse = json_decode($dataOffering[0]['Arr_CDID']);

                    if (in_array($data[$i]['CurriculumDetailID'], $dataCourse)) {
                        $data[$i]['Offering'] = true;
                    }
                }
            }
        }

        //        print_r($data);

        return $data;
    }

    public function getAllCourseOfferings($SemesterID, $CurriculumID, $ProdiID, $Semester, $IsSemesterAntara)
    {

        $dataProdi = $this->db->query('SELECT * FROM db_academic.program_study WHERE Status = 1 AND ID = "' . $ProdiID . '" ORDER BY ID ASC ')->result_array();

        $result = [];
        for ($i = 0; $i < count($dataProdi); $i++) {
            $dataOfferings = $this->getDetailAllOfferings($SemesterID, $CurriculumID, $ProdiID, $Semester, $IsSemesterAntara);
            $data = array(
                'Prodi' => array(
                    'ID' => $dataProdi[$i]['ID'],
                    'Code' => $dataProdi[$i]['Code'],
                    'Name' => $dataProdi[$i]['Name'],
                    'NameEng' => $dataProdi[$i]['NameEng'],
                ),
                'Offerings' => $dataOfferings
            );

            array_push($result, $data);
        }

        return $result;
    }

    private function getDetailAllOfferings($SemesterID, $CurriculumID, $ProdiID, $Semester, $IsSemesterAntara)
    {

        $data = $this->db->query('SELECT * FROM db_academic.course_offerings co
                                        WHERE co.SemesterID = "' . $SemesterID . '"
                                        AND co.CurriculumID = "' . $CurriculumID . '"
                                        AND co.ProdiID = "' . $ProdiID . '"
                                        AND co.Semester = "' . $Semester . '"
                                        AND co.IsSemesterAntara = "' . $IsSemesterAntara . '"
                                         LIMIT 1 ')->result_array();

        $result = [];
        if (count($data) > 0) {
            $Course = json_decode($data[0]['Arr_CDID']);

            $CourseArr = [];

            for ($i = 0; $i < count($Course); $i++) {
                $dataCourse = $this->db->query('SELECT cd.ID AS CDID, cd.ProdiID, cd.Semester, cd.MKType, cd.TotalSKS, cd.StatusMK, cd.MKID,
						                              mk.NameEng AS MKNameEng,
                                                      mk.Name AS MKName, mk.MKCode
                                                      FROM db_academic.curriculum_details cd
                                                      LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)
                                                      WHERE cd.ID = "' . $Course[$i] . '"
                                                      LIMIT 1')->result_array();

                $dataCekInSchedule = $this->db->query('SELECT s1.ID AS ScheduleID
                                                      FROM db_academic.curriculum_details cd
                                                      LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)
                                                      LEFT JOIN db_academic.schedule_details_course sdc1 ON (sdc1.MKID = mk.ID)
                                                      LEFT JOIN db_academic.schedule s1 ON (sdc1.ScheduleID = s1.ID)
                                                      WHERE s1.SemesterID = "' . $SemesterID . '" AND cd.ID = "' . $Course[$i] . '"
                                                      AND cd.ID IN (
                                                            SELECT sdc.CDID FROM db_academic.schedule_details_course sdc
                                                            LEFT JOIN db_academic.schedule s ON (sdc.ScheduleID = s.ID)
                                                            )
                                                      LIMIT 1')->result_array();

                if (count($dataCourse) > 0) {
                    $dataCourse[0]['ScheduleID'] = (count($dataCekInSchedule) > 0) ? $dataCekInSchedule[0]['ScheduleID'] : null;
                }

                $dataPush = (count($dataCourse) > 0) ? $dataCourse[0] : $dataCourse;
                array_push($CourseArr, $dataPush);
            }

            $result = $data;

            $result[0]['Details'] = $CourseArr;
        }

        return $result;
    }

    private function getDetailOfferings($SemesterID, $ProdiID)
    {

        //        $data = $this->db->query('SELECT co.ID, cd.Semester, cd.MKType, cd.MKID, cd.MKCode, cd.TotalSKS, cd.StatusMK,
        //                                          mk.Name AS MKName, mk.NameEng AS MKNameEng, s.ID AS ScheduleID
        //                                            FROM db_academic.course_offerings co
        //                                            LEFT JOIN db_academic.curriculum_details cd ON (co.CurriculumDetailID = cd.ID)
        //                                            LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID AND cd.MKCode = mk.MKCode)
        //                                            LEFT JOIN db_academic.schedule s ON (s.SemesterID = co.SemesterID AND cd.MKID = s.MKID AND cd.MKCode = s.MKCode)
        //                                            WHERE  co.SemesterID = "'.$SemesterID.'" AND co.ProdiID = "'.$ProdiID.'"
        //                                   ');

        // Load Mata Kuliah Saat Input Jadwal Tanpa Mata Kuliah Umum
        $data = $this->db->query('SELECT co.ID, co.ToSemester, cd.ProdiID, cd.Semester, cd.MKType, cd.MKID, mk.MKCode, cd.TotalSKS, cd.StatusMK,
                                          mk.Name AS MKName, mk.NameEng AS MKNameEng, s.ID AS ScheduleID
                                            FROM db_academic.course_offerings co
                                            LEFT JOIN db_academic.curriculum_details cd ON (co.CurriculumDetailID = cd.ID)
                                            LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)
                                            LEFT JOIN db_academic.schedule s ON (s.SemesterID = co.SemesterID AND cd.MKID = s.MKID)
                                            WHERE  co.SemesterID = "' . $SemesterID . '" AND co.ProdiID = "' . $ProdiID . '" AND mk.BaseProdiID != 7
                                   ');
        return $data->result_array();
    }

    public function getAllCourseOfferingsMKU($SemesterID)
    {

        $data = $this->db->query('SELECT co.ID, cd.Semester, cd.MKType, cd.MKID, mk.MKCode, cd.TotalSKS, cd.StatusMK,
                                          mk.Name AS MKName, mk.NameEng AS MKNameEng , s.ID AS ScheduleID
                                        FROM db_academic.course_offerings co
                                        LEFT JOIN db_academic.curriculum_details cd ON (co.CurriculumDetailID = cd.ID)
                                        LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)
                                        LEFT JOIN db_academic.schedule s ON (s.SemesterID = co.SemesterID AND cd.MKID = s.MKID AND cd.MKCode = s.MKCode)
                                        WHERE co.SemesterID = "' . $SemesterID . '" AND mk.BaseProdiID = 7 GROUP BY cd.MKCode
                                        ');

        return $data->result_array();
    }


    public function getOfferingsToSetSchedule($dataForm)
    {

        //        print_r($dataForm);

        $query = $this->db
            ->order_by('Semester', 'ASC')
            ->get_where('db_academic.course_offerings', $dataForm)->result_array();

        $result = [];
        if (count($query) > 0) {
            for ($i = 0; $i < count($query); $i++) {
                $dt = $this->getOfferingsToSetScheduleDetails($query[$i], $query[$i]['Arr_CDID']);
                $dataRes = array(
                    'Offerings' => $query[$i],
                    'Details' => $dt
                );

                array_push($result, $dataRes);
            }
        }

        return $result;
    }

    private function getOfferingsToSetScheduleDetails($query, $Arr_CDID)
    {
        $data_CDID = json_decode($Arr_CDID);
        $result = [];
        for ($i = 0; $i < count($data_CDID); $i++) {
            $data = $this->db->query('SELECT cd.ID AS CDID, cd.TotalSKS, mk.ID,mk.MKCode,mk.Name AS MKName,
                                                mk.NameEng AS MKNameEng, cd.Semester
                                                FROM db_academic.curriculum_details cd
                                                LEFT JOIN db_academic.mata_kuliah mk ON (cd.MKID = mk.ID)
                                                WHERE cd.ID = "' . $data_CDID[$i] . '" LIMIT 1')->result_array();



            if (count($data) > 0) {
                $sc = $this->db->query('SELECT s.ID AS ScheduleID FROM db_academic.schedule_details_course sdc
                                                  JOIN db_academic.schedule s ON (s.ID=sdc.ScheduleID)
                                                  WHERE s.SemesterID = "' . $query['SemesterID'] . '"
                                                  AND s.ProgramsCampusID = "' . $query['ProgramsCampusID'] . '"
                                                  AND sdc.ProdiID = "' . $query['ProdiID'] . '"
                                                  AND sdc.CDID = "' . $data_CDID[$i] . '"
                                                  AND sdc.MKID = "' . $data[0]['ID'] . '"
                                                  LIMIT 1
                                                   ')->result_array();
                $data[0]['ScheduleID'] = '';
                if (count($sc) > 0) {
                    $data[0]['ScheduleID'] = $sc[0]['ScheduleID'];
                }
                array_push($result, $data[0]);
            }
        }
        return $result;
    }

    public function getSemesterCurriculum($SemesterID, $IsSemesterAntara)
    {

        if ($IsSemesterAntara == 0 || $IsSemesterAntara == '0') {
            $where = ($SemesterID != '' && $SemesterID != 0) ? 's.ID = ' . $SemesterID : 's.Status = 1';

            $dataCurriculum = $this->db->query('SELECT * FROM db_academic.curriculum c
                                                    WHERE c.Year <= (
                                                      SELECT Year FROM db_academic.semester s WHERE ' . $where . ' LIMIT 1)
                                                      ORDER BY c.Year DESC ')
                ->result_array();

            $result = [];

            for ($s = 0; $s < count($dataCurriculum); $s++) {
                $data = $this->db->query('SELECT s.* FROM db_academic.semester s
                                                    WHERE s.Year>="' . $dataCurriculum[$s]['Year'] . '" ')
                    ->result_array();

                $smt = 1;


                for ($i = 0; $i < count($data); $i++) {

                    if ($SemesterID != '' && $SemesterID != 0) {
                        if ($data[$i]['ID'] != $SemesterID) {
                            $smt = $smt + 1;
                        } else {
                            break;
                        }
                    } else {
                        if ($data[$i]['Status'] == 0) {
                            $smt = $smt + 1;
                        } else {
                            break;
                        }
                    }
                }

                $d = array(
                    'Curriculum' => $dataCurriculum[$s],
                    'Semester' => $smt
                );
                array_push($result, $d);
            }
        } else {
            $dataCurriculum = $this->db->query()->result_array();
        }


        return $result;
    }

    public function getSchedule($DayID, $dataWhere)
    {


        $ProgramsCampusID = ($dataWhere['ProgramsCampusID'] != '') ? ' AND s.ProgramsCampusID = "' . $dataWhere['ProgramsCampusID'] . '" ' : '';
        $SemesterID = ($dataWhere['SemesterID'] != '') ? ' AND s.SemesterID = "' . $dataWhere['SemesterID'] . '" ' : '';
        $CombinedClasses = ($dataWhere['CombinedClasses'] != '') ? ' AND s.CombinedClasses = "' . $dataWhere['CombinedClasses'] . '" ' : '';
        $IsSemesterAntara = ($dataWhere['IsSemesterAntara'] != '') ? ' AND s.IsSemesterAntara = "' . $dataWhere['IsSemesterAntara'] . '" ' : '';

        $data = $this->db->query('SELECT s.*,
                                          sd.ClassroomID,sd.Credit,sd.DayID,sd.TimePerCredit,sd.StartSessions,sd.EndSessions,
                                          em.Name AS Lecturer,
                                          cl.Room
                                          FROM db_academic.schedule_details sd
                                          LEFT JOIN db_academic.schedule s ON (s.ID=sd.ScheduleID)
                                          LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                          LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                          WHERE sd.DayID = "' . $DayID . '" ' . $ProgramsCampusID . ' ' . $SemesterID . ' ' . $CombinedClasses . ' ' . $IsSemesterAntara . '
                                          ORDER BY sd.StartSessions, sd.EndSessions ASC');

        $result = $data->result_array();


        if (count($result) > 0) {

            $CO_SemesterID = ($dataWhere['SemesterID'] != '') ? ' AND co.SemesterID = "' . $dataWhere['SemesterID'] . '" ' : '';
            $CO_ProdiID = ($dataWhere['ProdiID'] != '') ? ' AND co.ProdiID = "' . $dataWhere['ProdiID'] . '" ' : '';
            $CO_IsSemesterAntara = ($dataWhere['IsSemesterAntara'] != '') ? ' AND co.IsSemesterAntara = "' . $dataWhere['IsSemesterAntara'] . '" ' : '';
            $CO_Semester = ($dataWhere['Semester'] != '') ? ' AND co.Semester = "' . $dataWhere['Semester'] . '" ' : '';

            $ClassOf = $this->getClassOf();

            // Get Course
            for ($c = 0; $c < count($result); $c++) {
                //                $ProdiIDsdc = ($dataWhere['ProdiID']!='') ? ' AND sdc.ProdiID = "'.$dataWhere['ProdiID'].'" ' : '';


                $dataOffering = $this->db->query('SELECT * FROM db_academic.course_offerings co
                                                          WHERE co.ProgramsCampusID = "' . $dataWhere['ProgramsCampusID'] . '" ' . $CO_Semester . '
                                                           ' . $CO_SemesterID . ' ' . $CO_ProdiID . ' ' . $CO_IsSemesterAntara . ' ')->result_array();


                $dataCourse = [];

                if (count($dataOffering) > 0) {
                    for ($f = 0; $f < count($dataOffering); $f++) {
                        $Arr_CDID = json_decode($dataOffering[$f]['Arr_CDID']);

                        for ($s = 0; $s < count($Arr_CDID); $s++) {

                            $__course = $this->db->query('SELECT sdc.CDID, mk.ID, mk.MKCode, mk.NameEng AS MKNameEng, mk.Name AS MKName,
                                                          ps.NameEng AS ProdiEng, ps.name AS Prodi, ps.Code AS ProdiCode, cd.Semester AS BaseSemester
                                                          FROM db_academic.schedule_details_course sdc
                                                          LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                          LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                                          LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                          WHERE sdc.ScheduleID="' . $result[$c]['ID'] . '" AND sdc.CDID = "' . $Arr_CDID[$s] . '" LIMIT 1')->result_array();

                            if (count($__course) > 0) {
                                //                                $__course[0]['Semester'] = $dataWhere['Semester'];
                                $__course[0]['Semester'] = $dataOffering[$f]['Semester'];
                                array_push($dataCourse, $__course[0]);
                            }
                        }
                    }
                }

                $result[$c]['SemesterDetails'] = $dataOffering;
                $result[$c]['DetailCourse'] = $dataCourse;

                $Students = [];
                for ($st = 0; $st < count($ClassOf); $st++) {
                    $db_ = 'ta_' . $ClassOf[$st]['Year'];

                    $dataStdCourse = $this->db->query('SELECT s.Name, s.NPM FROM ' . $db_ . '.study_planning sp
                                                            LEFT JOIN ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                            WHERE sp.SemesterID = "' . $dataWhere['SemesterID'] . '"
                                                            AND sp.ScheduleID = "' . $result[$c]['ID'] . '" ')->result_array();

                    if (count($dataStdCourse) > 0) {
                        array_push($Students, $dataStdCourse);
                    }
                }

                $result[$c]['StudentsDetails'] = (count($Students) > 0) ? $Students[0] : $Students;
            }

            for ($i = 0; $i < count($result); $i++) {
                if ($result[$i]['TeamTeaching'] == 1) {
                    $result[$i]['DetailTeamTeaching'] = $this->getTeamTeaching($result[$i]['ID']);
                }
            }
        }

        return $result;
    }

    public function views_datarequestdoc($NIP)
    {
        $sql = "SELECT a.*, b.TypeFiles, b.NameFiles, c.Name
                    FROM db_employees.request_document AS a
                    LEFT JOIN db_employees.master_files AS b ON (a.IDTypeFiles = b.ID)
                    LEFT JOIN db_employees.employees AS c ON (c.NIP = a.NIP)
                    WHERE a.NIP = '" . $NIP . "' AND a.Active = 1 AND b.RequestDocument = 1
                    ORDER BY a.IDRequest DESC";

        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    // ====== Get Academic Employee =======

    public function views_academic($NIP)
    {

        $sql = "SELECT * FROM db_employees.files AS em WHERE em.NIP = '" . $NIP . "' AND em.TypeFiles IN ('1','2','3','4','5','6') AND em.Active = '1' AND em.LinkFiles NOT IN ('') AND em.LinkFiles IS NOT NULL ORDER BY em.ID ASC";
        $query = $this->db->query($sql, array());

        $TypeAcademic = ['S1', 'S2', 'S3'];
        $rs = [];
        for ($i = 0; $i < count($TypeAcademic); $i++) {
            $TAca = $TypeAcademic[$i];
            $temp = ['TypeAcademic' => $TAca, 'Dt' => []];
            $Dt = [];
            // find ijazah dan transcript
            $sqlIjazah = 'SELECT df.*, b.Name_University, c.Name_MajorProgramstudy AS NamaJurusan, d.Name_MajorProgramstudy AS NamaProgramStudi
                        FROM db_employees.files AS df
                        JOIN db_employees.master_files AS mf ON df.TypeFiles = mf.ID
                        JOIN db_research.university AS b ON (df.NameUniversity = b.ID)
                        JOIN db_employees.major_programstudy_employees AS c ON (c.ID = df.Major)
                        JOIN db_employees.major_programstudy_employees AS d ON (d.ID = df.ProgramStudy)
                        WHERE mf.TypeFiles = "Ijazah' . $TAca . '" AND df.NIP = "' . $NIP . '"
                        AND df.LinkFiles NOT IN ("") AND df.LinkFiles IS NOT NULL
                        AND df.NameUniversity NOT IN ("") AND df.NameUniversity IS NOT NULL
                        AND df.Major NOT IN ("") AND df.Major IS NOT NULL
                        ORDER BY df.TypeFiles ASC
                        ';
            $queryIjazah = $this->db->query($sqlIjazah, array())->result_array();

            if (count($queryIjazah) > 0) {
                for ($k = 0; $k < count($queryIjazah); $k++) {
                    $NameUniversity = $queryIjazah[$k]['NameUniversity'];
                    $Major = $queryIjazah[$k]['Major'];
                    $sqlTranscript = 'SELECT df.*, b.Name_University, c.Name_MajorProgramstudy AS NamaJurusan, d.Name_MajorProgramstudy AS NamaProgramStudi
                                    FROM db_employees.files AS df
                                    JOIN db_employees.master_files AS mf ON df.TypeFiles = mf.ID
                                    JOIN db_research.university AS b ON (df.NameUniversity = b.ID)
                                    JOIN db_employees.major_programstudy_employees AS c ON (c.ID = df.Major)
                                    JOIN db_employees.major_programstudy_employees AS d ON (d.ID = df.ProgramStudy)
                                    WHERE mf.TypeFiles = "Transcript' . $TAca . '" AND df.NIP = "' . $NIP . '"
                                    AND df.LinkFiles NOT IN ("") AND df.LinkFiles IS NOT NULL
                                    AND df.NameUniversity NOT IN ("") AND df.NameUniversity IS NOT NULL
                                    AND df.Major NOT IN ("") AND df.Major IS NOT NULL
                                    AND df.NameUniversity = "' . $NameUniversity . '" AND df.Major = "' . $Major . '"
                                    ORDER BY df.TypeFiles ASC ';
                    //print_r($sqlTranscript); die();
                    $queryTranscript = $this->db->query($sqlTranscript, array())->result_array();

                    if (count($queryTranscript) > 0) {
                        $queryIjazah[$k]['LinkFiles_tr'] = $queryTranscript[0]['LinkFiles'];
                        // cek File exist ijazah
                        if (file_exists('./uploads/files/' . $queryIjazah[$k]['LinkFiles'])) {
                            $queryIjazah[$k]['LinkFiles_st'] = 1;
                        } else {
                            $queryIjazah[$k]['LinkFiles_st'] = 0;
                        }

                        // cek File exist transcript
                        if (file_exists('./uploads/files/' . $queryIjazah[$k]['LinkFiles_tr'])) {
                            $queryIjazah[$k]['LinkFiles_tr_st'] = 1;
                        } else {
                            $queryIjazah[$k]['LinkFiles_tr_st'] = 0;
                        }

                        $queryIjazah[$k]['ID2'] = $queryTranscript[0]['ID'];
                    }
                }
            }

            $Dt = $queryIjazah;

            $temp['Dt'] = $Dt;
            $rs[] = $temp;
        }

        return $rs;
    }

    // ====== Get Academic Employee Files =======
    public function views_otherfile($NIP)
    {
        $sql = "SELECT f.*, m.NameFiles
            FROM db_employees.files AS f
            LEFT JOIN db_employees.master_files AS m ON (f.TypeFiles = m.ID)
            WHERE f.NIP = '" . $NIP . "' AND m.Type ='1' AND f.Active = '1' AND f.LinkFiles NOT IN ('') AND f.LinkFiles IS NOT NULL";

        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function views_files1($NIP, $srata)
    {

        $sql = "SELECT a.ID, a.NIP, a.TypeAcademic, a.NameUniversity, a.TypeFiles, a.LinkFiles, b.Name_University, c.Name_MajorProgramstudy AS NamaJurusan, d.Name_MajorProgramstudy AS NamaProgramStudi
                    FROM db_employees.files AS a
                    LEFT JOIN db_research.university AS b ON (a.NameUniversity = b.ID)
                    LEFT JOIN db_employees.major_programstudy_employees AS c ON (c.ID = a.Major)
                    LEFT JOIN db_employees.major_programstudy_employees AS d ON (d.ID = a.ProgramStudy)
                    WHERE a.NIP ='" . $NIP . "' AND a.TypeAcademic ='" . $srata . "' AND a.LinkFiles IS NOT NULL ";
        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function views_editacademic($NIP, $fileijazah, $filetranscripts, $nameuniv)
    {

        $sql = "SELECT a.*, b.Name_University, c.Name_MajorProgramstudy AS NamaJurusan, d.Name_MajorProgramstudy AS NamaProgramStudi
                FROM db_employees.files AS a
                LEFT JOIN db_research.university AS b ON (a.NameUniversity = b.ID)
                LEFT JOIN db_employees.major_programstudy_employees AS c ON (c.ID = a.Major)
                LEFT JOIN db_employees.major_programstudy_employees AS d ON (d.ID = a.ProgramStudy)
                WHERE a.NIP= '" . $NIP . "' AND a.ID IN ('" . $fileijazah . "','" . $filetranscripts . "')  AND a.LinkFiles IS NOT NULL";
        $query = $this->db->query($sql, array());
        return $query->result_array();
    }

    public function views_editotfiles($NIP, $IDfiles)
    {

        $sql = "SELECT a.*, b.TypeFiles AS NameFiles
                FROM db_employees.files AS a
                LEFT JOIN db_employees.master_files AS b ON (a.TypeFiles = b.ID)
                WHERE a.NIP= '" . $NIP . "' AND a.ID= '" . $IDfiles . "' AND a.Active = '1' ";

        $query = $this->db->query($sql, array());
        return $query->result_array();
    }


    public function edit_academicemployee()
    {
        $sql = "UPDATE db_employees.employees_academic SET NoIjazah='" . $NIP . "', DateIjazah='" . $NIP . "',NameUniversity='" . $NIP . "',Major='" . $NIP . "',ProgramStudy='" . $NIP . "', Grade='" . $NIP . "',TotalCredit='" . $NIP . "', TotalSemester='" . $NIP . "'
            WHERE NIP ='" . $NIP . "' AND IjazahFile='" . $NIP . "' AND TranscriptFile='" . $NIP . "' ";
        $query = $this->db->query($sql);
    }


    public function delistacademicemployee($ID1, $ID2)
    {

        $sql = "DELETE  WHERE ID IN ('" . $ID1 . "', '" . $ID2 . "') ";
        $query = $this->db->query($sql);
    }

    public function delistotherfiles($ID1)
    {

        $sql = "DELETE db_employees.files WHERE ID = '" . $ID1 . "' ";
        $query = $this->db->query($sql);
    }

    public function deletelistversion($versionid)
    {

        $this->db->where('IDVersion', $versionid);
        $this->db->delete('db_it.module');

        //$sql = "UPDATE db_it.version SET Active='0' WHERE IDVersion = '".$versionid."' ";
        //$query=$this->db->query($sql);

    }


    function getRowsmodule($keyword)
    {

        $this->db->select('*')->from('db_it.group_module');
        $this->db->like('NameGroup', $keyword);
        $query = $this->db->get();

        return $query->result();
    }

    // ====== Get Jadwal Per Day =======

    public function getTotalPerDay($DayID, $dataWhere)
    {

        $ProgramsCampusID = ($dataWhere['ProgramsCampusID'] != '') ? ' AND s.ProgramsCampusID = "' . $dataWhere['ProgramsCampusID'] . '" ' : '';
        $SemesterID = ($dataWhere['SemesterID'] != '') ? ' AND s.SemesterID = "' . $dataWhere['SemesterID'] . '" ' : '';
        $CombinedClasses = ($dataWhere['CombinedClasses'] != '') ? ' AND s.CombinedClasses = "' . $dataWhere['CombinedClasses'] . '" ' : '';
        $IsSemesterAntara = ($dataWhere['IsSemesterAntara'] != '') ? ' AND s.IsSemesterAntara = "' . $dataWhere['IsSemesterAntara'] . '" ' : '';

        $ProdiID = ($dataWhere['ProdiID'] != '') ? ' AND sdc.ProdiID = "' . $dataWhere['ProdiID'] . '" ' : '';

        $dataDay = $this->db->query('SELECT sdc.CDID FROM db_academic.schedule_details sd
                                          LEFT JOIN db_academic.schedule s ON (s.ID = sd.ScheduleID)
                                          LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = sd.ScheduleID)
                                          WHERE sd.DayID = ' . $DayID . '  ' . $ProgramsCampusID . '
                                           ' . $SemesterID . ' ' . $CombinedClasses . '
                                           ' . $IsSemesterAntara . '
                                           ' . $ProdiID . '
                                           GROUP BY s.ID ')->result_array();

        $res = $dataDay;
        return $res;
    }

    public function getSchedulePerDayLimit($DayID, $dataWhere, $start, $length)
    {


        $ProgramsCampusID = ($dataWhere['ProgramsCampusID'] != '') ? ' AND s.ProgramsCampusID = "' . $dataWhere['ProgramsCampusID'] . '" ' : '';
        $SemesterID = ($dataWhere['SemesterID'] != '') ? ' AND s.SemesterID = "' . $dataWhere['SemesterID'] . '" ' : '';
        $CombinedClasses = ($dataWhere['CombinedClasses'] != '') ? ' AND s.CombinedClasses = "' . $dataWhere['CombinedClasses'] . '" ' : '';
        $IsSemesterAntara = ($dataWhere['IsSemesterAntara'] != '') ? ' AND s.IsSemesterAntara = "' . $dataWhere['IsSemesterAntara'] . '" ' : '';

        $ProdiID = ($dataWhere['ProdiID'] != '') ? ' AND sdc.ProdiID = "' . $dataWhere['ProdiID'] . '" ' : '';

        $q = 'SELECT s.*, sd.ClassroomID,sd.Credit,sd.DayID,sd.TimePerCredit,sd.StartSessions,sd.EndSessions,
                                          em.Name AS Lecturer,
                                          cl.Room, sdc.CDID, cd.Semester
                                          FROM db_academic.schedule_details sd
                                          LEFT JOIN db_academic.schedule s ON (s.ID = sd.ScheduleID)
                                          LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = sd.ScheduleID)
                                          LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                          LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                          LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                          WHERE sd.DayID = ' . $DayID . '  ' . $ProgramsCampusID . '
                                           ' . $SemesterID . ' ' . $CombinedClasses . '
                                           ' . $IsSemesterAntara . '
                                           ' . $ProdiID . '
                                           GROUP BY s.ID
                                           ORDER BY sd.StartSessions, sd.EndSessions ASC
                                           LIMIT ' . $start . ' , ' . $length . '
                                           ';



        return $q;
    }

    public function getSchedulePerDaySearch($DayID, $dataWhere, $search)
    {
        $ProgramsCampusID = ($dataWhere['ProgramsCampusID'] != '') ? ' AND s.ProgramsCampusID = "' . $dataWhere['ProgramsCampusID'] . '" ' : '';
        $SemesterID = ($dataWhere['SemesterID'] != '') ? ' AND s.SemesterID = "' . $dataWhere['SemesterID'] . '" ' : '';
        $CombinedClasses = ($dataWhere['CombinedClasses'] != '') ? ' AND s.CombinedClasses = "' . $dataWhere['CombinedClasses'] . '" ' : '';
        $IsSemesterAntara = ($dataWhere['IsSemesterAntara'] != '') ? ' AND s.IsSemesterAntara = "' . $dataWhere['IsSemesterAntara'] . '" ' : '';

        $ProdiID = ($dataWhere['ProdiID'] != '') ? ' AND sdc.ProdiID = "' . $dataWhere['ProdiID'] . '" ' : '';

        $q = 'SELECT s.*, sd.ClassroomID,sd.Credit,sd.DayID,sd.TimePerCredit,sd.StartSessions,sd.EndSessions,
                                          em.Name AS Lecturer,
                                          cl.Room, sdc.CDID, cd.Semester
                                          FROM db_academic.schedule_details sd
                                          LEFT JOIN db_academic.schedule s ON (s.ID = sd.ScheduleID)
                                          LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = sd.ScheduleID)
                                          LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                          LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                          LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                          LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                          WHERE ( sd.DayID = ' . $DayID . '  ' . $ProgramsCampusID . '
                                           ' . $SemesterID . ' ' . $CombinedClasses . '
                                           ' . $IsSemesterAntara . '
                                           ' . $ProdiID . ' ) AND (
                                           s.ClassGroup LIKE "%' . $search . '%" OR
                                           em.Name LIKE "%' . $search . '%" OR
                                           cl.Room LIKE "%' . $search . '%"
                                            )
                                           GROUP BY s.ID
                                           ORDER BY sd.StartSessions, sd.EndSessions ASC
                                           ';



        return $q;
    }

    public function getTeamTeachingPerDay($ScheduleID)
    {
        $data = $this->db->query('SELECT em.Name AS Lecturer FROM db_academic.schedule_team_teaching stt
                                            LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                            WHERE stt.ScheduleID = "' . $ScheduleID . '" ')->result_array();

        $res = '';
        if (count($data) > 0) {

            for ($i = 0; $i < count($data); $i++) {

                $lec = '<div style="margin-bottom: 7px;"><span class="label label-info-inline"><b>' . $data[$i]['Lecturer'] . '</b></span></div>';

                $res = $res . '' . $lec;
            }
        }

        return $res;
    }

    public function getCoursesPerDay($ScheduleID)
    {
        $dataCourse = $this->db->query('SELECT mk.MKCode, mk.Name AS MKName, mk.NameEng AS MKNameEng ,
                                            ps.Code AS CodeProdi, ps.NameEng AS ProdiEng, cr.Year
                                            FROM db_academic.schedule_details_course sdc
                                            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                            LEFT JOIN db_academic.curriculum cr ON (cr.ID = cd.CurriculumID)
                                            LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                            WHERE sdc.ScheduleID = "' . $ScheduleID . '" ORDER BY cd.ID DESC ')->result_array();


        $resCourse = '';
        if (count($dataCourse) > 0) {
            for ($r = 0; $r < count($dataCourse); $r++) {
                $d = $dataCourse[$r];

                $smt = $this->_getSeemsterByClassOf($dataCourse[$r]['Year']);

                $cc = '<div style="margin-bottom: 10px;"><b>' . $d['MKNameEng'] . '</b><br/>
                            <i>' . $d['MKName'] . '</i><br/>
                            <span class="label label-default">' . $d['MKCode'] . '</span> |
                            <span class="label label-danger-inline"><b>' . $d['CodeProdi'] . '</b></span> |
                            <i style="color:blue;">Semester : ' . $smt . '</i></div>';
                $resCourse = $resCourse . '' . $cc;
            }
        }

        return $resCourse;
    }

    public function getTotalStdPerDay($SemesterID, $ScheduleID, $CDID)
    {
        $ClassOf = $this->getClassOf();

        $totalStd = 0;
        for ($st = 0; $st < count($ClassOf); $st++) {
            $db_ = 'ta_' . $ClassOf[$st]['Year'];

            $dataStdCourse = $this->db->query('SELECT s.NPM FROM ' . $db_ . '.study_planning sp
                                                            LEFT JOIN ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                            WHERE sp.SemesterID = "' . $SemesterID . '"
                                                            AND sp.ScheduleID = "' . $ScheduleID . '"

                                                             ')->result_array();
            $totalStd = $totalStd + count($dataStdCourse);
        }

        return $totalStd;
    }

    public function getTotalStdNotYetApprovePerDay($SemesterID, $ScheduleID, $CDID)
    {
        $data = $this->db->query('SELECT sk.NPM,ast.Year FROM db_academic.std_krs sk
                                          LEFT JOIN db_academic.auth_students ast ON (ast.NPM=sk.NPM)
                                          WHERE sk.SemesterID = "' . $SemesterID . '"
                                           AND sk.ScheduleID = "' . $ScheduleID . '"
                                             ORDER BY sk.NPM ASC
                                            ')
            ->result_array();


        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $db_ = 'ta_' . $data[$i]['Year'];
                $dataN = $this->db->select('Name')->get_where(
                    $db_ . '.students',
                    array('NPM' => $data[$i]['NPM']),
                    1
                )->result_array();
                $data[$i]['Name'] = $dataN[0]['Name'];
                $dSP = $this->db->select('ID')->get_where(
                    $db_ . '.study_planning',
                    array(
                        'NPM' => $data[$i]['NPM'],
                        'SemesterID' => $SemesterID,
                        'ScheduleID' => $ScheduleID
                    )
                )->result_array();
                $status = (count($dSP) > 0) ? '1' : '0';

                $data[$i]['Status'] = $status;
            }
        }

        return $data;
    }

    // =======================================

    // ====== Get Jadwal Per Semester =======
    public function getTotalPerSemester($dataWhere)
    {

        $CO_SemesterID = ($dataWhere['SemesterID'] != '') ? ' AND co.SemesterID = "' . $dataWhere['SemesterID'] . '" ' : '';
        $CO_ProdiID = ($dataWhere['ProdiID'] != '') ? ' AND co.ProdiID = "' . $dataWhere['ProdiID'] . '" ' : '';
        $CO_IsSemesterAntara = ($dataWhere['IsSemesterAntara'] != '') ? ' AND co.IsSemesterAntara = "' . $dataWhere['IsSemesterAntara'] . '" ' : '';
        $CO_Semester = ($dataWhere['Semester'] != '') ? ' AND co.Semester = "' . $dataWhere['Semester'] . '" ' : '';


        $dataOffering = $this->db->query('SELECT * FROM db_academic.course_offerings co
                                                          WHERE co.ProgramsCampusID = "' . $dataWhere['ProgramsCampusID'] . '"
                                                           ' . $CO_SemesterID . '
                                                           ' . $CO_ProdiID . '
                                                           ' . $CO_Semester . '
                                                           ' . $CO_IsSemesterAntara . ' ')->result_array();

        $res = [];
        if (count($dataOffering) > 0) {
            for ($r = 0; $r < count($dataOffering); $r++) {
                $Arr_CDID = json_decode($dataOffering[$r]['Arr_CDID']);
                for ($s = 0; $s < count($Arr_CDID); $s++) {
                    array_push($res, $Arr_CDID[$s]);
                }
            }
        }

        return $res;
    }

    public function getTotalPerSemesterLimit($dataWhere, $start, $length)
    {

        $dataCDID = $this->getTotalPerSemester($dataWhere);

        $arrLim = array_slice($dataCDID, $start, $length);

        $res = [];
        if (count($arrLim) > 0) {
            for ($c = 0; $c < count($arrLim); $c++) {
                $dataC = $this->db->query('SELECT s.*, sd.ClassroomID,sd.Credit,sd.DayID,sd.TimePerCredit,sd.StartSessions,sd.EndSessions,
                                                  em.Name AS Lecturer,
                                                  cl.Room, sdc.CDID, d.NameEng AS DayEng
                                                FROM db_academic.schedule_details_course sdc
                                                LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                                LEFT JOIN db_academic.schedule_details sd ON (s.ID = sd.ScheduleID)
                                                LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                WHERE s.SemesterID = "' . $dataWhere['SemesterID'] . '"
                                                AND sdc.CDID = "' . $arrLim[$c] . '"
                                                ')->result_array();

                if (count($dataC) > 0) {
                    for ($dc = 0; $dc < count($dataC); $dc++) {
                        array_push($res, $dataC[$dc]);
                    }
                }
            }
        }

        return $res;
    }

    public function getTotalPerSemesterSearch($dataWhere, $search)
    {
        $arrLim = $this->getTotalPerSemester($dataWhere);

        $res = [];
        if (count($arrLim) > 0) {
            for ($c = 0; $c < count($arrLim); $c++) {
                $dataC = $this->db->query('SELECT s.*, sd.ClassroomID,sd.Credit,sd.DayID,sd.TimePerCredit,sd.StartSessions,sd.EndSessions,
                                                  em.Name AS Lecturer,
                                                  cl.Room
                                                FROM db_academic.schedule_details_course sdc
                                                LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                                LEFT JOIN db_academic.schedule_details sd ON (s.ID = sd.ScheduleID)
                                                LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                WHERE ( s.SemesterID = "' . $dataWhere['SemesterID'] . '"
                                                AND sdc.CDID = "' . $arrLim[$c] . '" ) AND (
                                                    s.ClassGroup LIKE "%' . $search . '%" OR
                                                   em.Name LIKE "%' . $search . '%" OR
                                                   cl.Room LIKE "%' . $search . '%"
                                                )
                                                ')->result_array();

                if (count($dataC) > 0) {
                    for ($dc = 0; $dc < count($dataC); $dc++) {
                        array_push($res, $dataC[$dc]);
                    }
                }
            }
        }

        return $res;
    }


    // =======================================
    public function getDetailSc($dataWhere, $result)
    {
        if (count($result) > 0) {
            $CO_SemesterID = ($dataWhere['SemesterID'] != '') ? ' AND co.SemesterID = "' . $dataWhere['SemesterID'] . '" ' : '';
            $CO_ProdiID = ($dataWhere['ProdiID'] != '') ? ' AND co.ProdiID = "' . $dataWhere['ProdiID'] . '" ' : '';
            $CO_IsSemesterAntara = ($dataWhere['IsSemesterAntara'] != '') ? ' AND co.IsSemesterAntara = "' . $dataWhere['IsSemesterAntara'] . '" ' : '';
            $CO_Semester = ($dataWhere['Semester'] != '') ? ' AND co.Semester = "' . $dataWhere['Semester'] . '" ' : '';

            //            $ClassOf = $this->getClassOf();
            for ($c = 0; $c < count($result); $c++) {
                $dataOffering = $this->db->query('SELECT * FROM db_academic.course_offerings co
                                                          WHERE co.ProgramsCampusID = "' . $dataWhere['ProgramsCampusID'] . '" ' . $CO_Semester . '
                                                           ' . $CO_SemesterID . ' ' . $CO_ProdiID . ' ' . $CO_IsSemesterAntara . ' ')->result_array();

                $dataCourse = [];

                if (count($dataOffering) > 0) {
                    for ($f = 0; $f < count($dataOffering); $f++) {
                        $Arr_CDID = json_decode($dataOffering[$f]['Arr_CDID']);

                        for ($s = 0; $s < count($Arr_CDID); $s++) {

                            $__course = $this->db->query('SELECT sdc.CDID, mk.ID, mk.MKCode, mk.NameEng AS MKNameEng, mk.Name AS MKName,
                                                          ps.NameEng AS ProdiEng, ps.name AS Prodi, ps.Code AS ProdiCode, cd.Semester AS BaseSemester
                                                          FROM db_academic.schedule_details_course sdc
                                                          LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                          LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                                          LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                          WHERE sdc.ScheduleID="' . $result[$c]['ID'] . '" AND sdc.CDID = "' . $Arr_CDID[$s] . '" LIMIT 1')->result_array();

                            if (count($__course) > 0) {
                                //                                $__course[0]['Semester'] = $dataWhere['Semester'];
                                $__course[0]['Semester'] = $dataOffering[$f]['Semester'];
                                array_push($dataCourse, $__course[0]);
                            }
                        }
                    }
                }

                $result[$c]['SemesterDetails'] = $dataOffering;
                $result[$c]['DetailCourse'] = $dataCourse;
            }
        }

        return $result;
    }


    public function getOneSchedule($ScheduleID)
    {

        $data = $this->db->query('SELECT s.ID,sm.Name AS semesterName,
                                          sm.ID AS SemesterID, pc.Name AS viewProgramsCampus,
                                          s.CombinedClasses,
                                          s.ClassGroup AS viewClassGroup,

                                          em.Name AS Coordinator,
                                          em.NIP,
                                          s.TeamTeaching,
                                          s.SubSesi
                                          FROM  db_academic.schedule s
                                          LEFT JOIN db_academic.semester sm ON (s.SemesterID = sm.ID)
                                          LEFT JOIN db_academic.programs_campus pc ON (s.ProgramsCampusID = pc.ID)

                                          LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                          WHERE s.ID = "' . $ScheduleID . '" LIMIT 1');

        $result = $data->result_array();

        if (count($result) > 0) {
            if ($result[0]['TeamTeaching'] == 1) {
                $dataTeam = $this->getTeamTeaching($result[0]['ID']);
                for ($i2 = 0; $i2 < count($dataTeam); $i2++) {
                    $result[0]['DetailTeamTeaching'][$i2] = $dataTeam[$i2]['NIP'];
                }
            }

            // Get Sesi
            $dataSesi = $this->db->query('SELECT sd.ID AS sdID ,sd.ClassroomID,sd.Credit,sd.DayID,sd.TimePerCredit,sd.StartSessions,sd.EndSessions,
                                          cl.Room  FROM db_academic.schedule_details sd LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                          WHERE sd.ScheduleID = "' . $ScheduleID . '" ');
            $result[0]['SubSesiDetails'] = $dataSesi->result_array();

            $dataCourse = $this->db->query('SELECT sdc.CDID, mk.ID AS MKID, mk.MKCode, mk.Name, mk.NameEng,
                                                      ps.ID AS ProdiID, ps.Code, ps.Name AS Prodi, ps.NameEng AS ProdiEng
                                                      FROM db_academic.schedule_details_course sdc
                                                      LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                      WHERE sdc.ScheduleID = "' . $ScheduleID . '" ');
            $result[0]['Courses'] = $dataCourse->result_array();

            $result[0]['DataStudent'] = $this->__getStudentByScheduleID($ScheduleID);
        }

        return $result[0];
    }

    private function getTeamTeaching($ScheduleID)
    {
        $data = $this->db->query('SELECT stt.ID,stt.NIP,stt.Status,em.Name AS Lecturer FROM db_academic.schedule_team_teaching stt
                                            LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                            WHERE stt.ScheduleID = "' . $ScheduleID . '" ');

        return $data->result_array();
    }


    public function getSchedule2($DayID, $dataWhere)
    {

        if (count($DayID) > 0) {
        } else {
            for ($i = 0; $i < count(); $i++) {
            }
        }




        if (count($dataWhere) > 0) {
            $where = '';
            for ($i = 0; $i < count($dataWhere); $i++) {
                if ($dataWhere['ProgramCampusID'] != '') {
                    $where = $where . ' AND ProgramCampusID=' . $dataWhere['ProgramCampusID'];
                }

                if ($dataWhere['SemesterID'] != '') {
                    $where = $where . ' AND SemesterID=' . $dataWhere['SemesterID'];
                }
            }
        }
    }


    // Database Mahasiswa
    public function __getTahunAngkatan()
    {
        $data = $this->db->query('SELECT Year FROM db_academic.auth_students
                                                GROUP BY Year ORDER BY Year ASC')->result_array();

        $result = [];
        for ($i = 0; $i < count($data); $i++) {
            $DataStudents = $this->__getStudents($data[$i]['Year']);
            $result[$i]['Angkatan'] = $data[$i]['Year'];
            $result[$i]['DataStudents'] = $DataStudents;
        }
        return $result;
    }

    private function __getStudents($ta)
    {
        $db = 'ta_' . $ta;
        $data = $this->db->query('SELECT s.*, au.EmailPU, p.Name AS ProdiName, p.NameEng AS ProdiNameEng,
                                      ss.Description AS StatusStudentDesc
                                      FROM ' . $db . '.students s
                                      JOIN db_academic.program_study p ON (s.ProdiID = p.ID)
                                      JOIN db_academic.status_student ss ON (s.StatusStudentID = ss.ID)
                                      JOIN db_academic.auth_students au ON (s.NPM = au.NPM)
                                      ORDER BY s.NPM ASC ');

        return $data->result_array();
    }

    public function __getStudentByNPM($ta, $NPM)
    {

        $db = 'ta_' . $ta;
        $data = $this->db->query('SELECT s.*, au.EmailPU, au.KTPNumber, au.Access_Card_Number, p.Name AS ProdiName, p.NameEng AS ProdiNameEng,
                                      ss.Description AS StatusStudentDesc,"' . $db . '" as ta_student,
                                      em.Name AS Mentor, em.NIP, em.EmailPU AS MentorEmailPU
                                      FROM ' . $db . '.students s
                                      LEFT JOIN db_academic.program_study p ON (s.ProdiID = p.ID)
                                      LEFT JOIN db_academic.status_student ss ON (s.StatusStudentID = ss.ID)
                                      LEFT JOIN db_academic.auth_students au ON (s.NPM = au.NPM)
                                      LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM=s.NPM)
                                      LEFT JOIN db_employees.employees em ON (em.NIP=ma.NIP)
                                      WHERE s.NPM = "' . $NPM . '" LIMIT 1');

        return $data->result_array();

        print_r($data);
        exit;
    }

    public function __checkClassGroup($ProgramsCampusID, $SemesterID, $ProdiCode, $IsSemesterAntara)
    {


        $data = $this->db->query('SELECT scg.* FROM db_academic.schedule s
                                                LEFT JOIN db_academic.schedule_class_group scg ON (s.ID=scg.ScheduleID)
                                                WHERE s.ProgramsCampusID = "' . $ProgramsCampusID . '" AND
                                                    s.SemesterID = "' . $SemesterID . '" AND
                                                    s.IsSemesterAntara = "' . $IsSemesterAntara . '" AND
                                                    scg.ProdiCode = "' . $ProdiCode . '"  ');

        return $data->result_array();
    }

    public function __checkClassGroupParalel($ProgramsCampusID, $SemesterID, $ProdiCode, $IsSemesterAntara)
    {

        $data = $this->db->query('SELECT * FROM db_academic.schedule_class_group scg
                                            LEFT JOIN db_academic.schedule s ON (s.ID = scg.ScheduleID)
                                            WHERE scg.ProdiCode LIKE "' . $ProdiCode . '"
                                            AND s.SemesterID = "' . $SemesterID . '"
                                            AND s.ProgramsCampusID = "' . $ProgramsCampusID . '"
                                            AND s.IsSemesterAntara = "' . $IsSemesterAntara . '"
                                            AND scg.Type = "1"
                                            AND scg.Alphabet = 0
                                             ')->result_array();

        if (count($data) > 0) {
            for ($s = 0; $s < count($data); $s++) {
                $data_s = $this->db->query('SELECT COUNT(*) AS alp FROM db_academic.schedule_class_group scg
                                            LEFT JOIN db_academic.schedule s ON (s.ID = scg.ScheduleID)
                                            WHERE scg.ProdiCode LIKE "' . $ProdiCode . '"
                                            AND s.SemesterID = "' . $SemesterID . '"
                                            AND s.ProgramsCampusID = "' . $ProgramsCampusID . '"
                                            AND s.IsSemesterAntara = "' . $IsSemesterAntara . '"
                                            AND scg.Type = "1"
                                            AND scg.Numeric = "' . $data[$s]['Numeric'] . '"
                                             ')->result_array();
                $data[$s]['alp'] = $data_s[0]['alp'];
            }
        }

        return $data;
    }

    public function __getAllClassRoom()
    {
        $data = $this->db->query('SELECT * FROM db_academic.classroom');
        return $data->result_array();
    }

    public function __getAllClassRoomCategory()
    {
        $data = $this->db->query('SELECT a.*,b.Name,b.NameEng FROM db_academic.classroom as a join db_reservation.category_room
            as b on a.ID_CategoryRoom = b.ID');
        return $data->result_array();
    }

    public function __getAllGrade()
    {
        $data = $this->db->query('SELECT * FROM db_academic.grade ORDER BY EndRange DESC');
        return $data->result_array();
    }

    public function __getRangeCredits()
    {
        $data = $this->db->query('SELECT * FROM db_academic.range_credits ORDER BY Credit DESC');
        return $data->result_array();
    }

    public function __getAllTimePerCredit()
    {
        $data = $this->db->query('SELECT * FROM db_academic.time_per_credits ORDER BY Time DESC');
        return $data->result_array();
    }

    public function __getLecturerDetail($NIP)
    {

        //        $data = $this->db->query('SELECT e.* FROM db_employees.employees e WHERE e.NIP="'.$NIP.'" AND e.PositionMain = "14.7"');
        $data = $this->db->query('SELECT e.*, ag.nama AS Religion, dp.Name AS Nameprov
                                    FROM db_employees.employees e
                                    LEFT JOIN db_admission.agama ag ON (e.ReligionID = ag.ID)
                                    LEFT JOIN db_employees.data_province dp ON (e.ProvinceID = dp.IDProvince) WHERE e.NIP="' . $NIP . '" LIMIT 1')->result_array();

        if (count($data) > 0) {
            return $data[0];
        } else {
            return $data;
        }
    }

    public function __checkSchedule($dataFilter)
    {


        $jadwal = $this->db->query('SELECT s.ID AS ScheduleID,s.ClassGroup , sd.ID AS sdID,
                                              sd.DayID,sd.StartSessions, sd.EndSessions, cl.Room, d.NameEng AS DayEng
                                              FROM db_academic.schedule s
                                              RIGHT JOIN db_academic.schedule_details sd ON (s.ID=sd.ScheduleID)
                                              LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                              LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                              WHERE s.SemesterID="' . $dataFilter['SemesterID'] . '"
                                              AND s.IsSemesterAntara="' . $dataFilter['IsSemesterAntara'] . '"
                                              AND sd.ClassroomID="' . $dataFilter['ClassroomID'] . '"
                                              AND sd.DayID="' . $dataFilter['DayID'] . '"
                                              AND (("' . $dataFilter['StartSessions'] . '" >= sd.StartSessions  AND "' . $dataFilter['StartSessions'] . '" <= sd.EndSessions) OR
                                              ("' . $dataFilter['EndSessions'] . '" >= sd.StartSessions AND "' . $dataFilter['EndSessions'] . '" <= sd.EndSessions) OR
                                              ("' . $dataFilter['StartSessions'] . '" <= sd.StartSessions AND "' . $dataFilter['EndSessions'] . '" >= sd.EndSessions)
                                              ) ORDER BY sd.StartSessions ASC
                                              ')->result_array();

        if (count($jadwal) > 0) {
            for ($i = 0; $i < count($jadwal); $i++) {
                $dataCourse = $this->db->query('SELECT sdc.*,mk.NameEng, mk.ID AS MKID, mk.MKCode FROM db_academic.schedule_details_course sdc
                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        WHERE sdc.ScheduleID = "' . $jadwal[$i]['ScheduleID'] . '" GROUP BY sdc.ScheduleID ')->result_array();

                $jadwal[$i]['DetailsCourse'] = $dataCourse;
            }
        }

        return $jadwal;
    }

    public function saveDataWilayah($arr)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes
        $data = $arr['data'];
        $arr_temp = array();
        $sql = "select RegionID from db_admission.region";
        $query = $this->db->query($sql, array())->result();
        foreach ($query as $key) {
            $arr_temp[] =  $key->RegionID;
        }

        $kode_wilayah_arr = array();
        for ($i = 0; $i < count($data); $i++) {
            // find data in array
            $kode_wilayah = $data[$i]['kode_wilayah'];
            $kode_wilayah_arr[] = $kode_wilayah;
            if (!in_array($kode_wilayah, $arr_temp)) {
                $dataSave = array(
                    'RegionID' => $data[$i]['kode_wilayah'],
                    'RegionName' => $data[$i]['nama'],
                    'RegionCodeMst' => $data[$i]['mst_kode_wilayah']
                );

                $this->db->insert('db_admission.region', $dataSave);
            }
        }

        return $kode_wilayah_arr;
    }

    public function saveDataSchool($arr)
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes
        $data = $arr['data'];
        $arr_temp = array();
        $sql = "select SchoolID from db_admission.school";
        $query = $this->db->query($sql, array())->result();
        foreach ($query as $key) {
            $arr_temp[] =  $key->SchoolID;
        }

        $kode_school_arr = array();
        for ($i = 0; $i < count($data); $i++) {
            // find data in array
            $kode_school = $data[$i]['id'];
            $kode_school_arr[] = $kode_school;
            if (!in_array($kode_school, $arr_temp)) {
                $dataSave = array(
                    'ProvinceID' => $data[$i]['kode_prop'],
                    'ProvinceName' => $data[$i]['propinsi'],
                    'CityID' => $data[$i]['kode_kab_kota'],
                    'CityName' => $data[$i]['kabupaten_kota'],
                    'DistrictID' => $data[$i]['kode_kec'],
                    'DistrictName' => $data[$i]['kecamatan'],
                    'SchoolID' => $data[$i]['id'],
                    'npsn' => $data[$i]['npsn'],
                    'SchoolName' => $data[$i]['sekolah'],
                    'SchoolType' => $data[$i]['bentuk'],
                    'Status' => $data[$i]['status'],
                    'SchoolAddress' => $data[$i]['alamat_jalan'],
                    'Latitude' => $data[$i]['lintang'],
                    'Longitude' => $data[$i]['bujur'],
                );

                $this->db->insert('db_admission.school', $dataSave);
            }
        }

        //return $kode_school_arr;
        return "Done";
    }

    public function getdataWilayah()
    {
        $sql = "select * from db_admission.region";
        $query = $this->db->query($sql, array())->result_array();
        return $query;
    }

    public function __getSMAWilayah($kode_wilayah)
    {
        $sql = "select * from db_admission.school as a where a.CityID = ? and Approved = 1";
        $query = $this->db->query($sql, array($kode_wilayah))->result_array();
        return $query;
    }

    public function getDataRegisterBelumBayar()
    {
        $sql = "select a.* from (
                select a.ID,a.Name,a.Email,b.SchoolName,a.PriceFormulir,a.VA_number,a.BilingID,a.Datetime_expired,a.RegisterAT,c.FileUpload,c.CreateAT as uploadAT,c.ID as ver_id
                        from db_admission.register as a LEFT JOIN db_admission.school as b
                        on a.SchoolID = b.ID
                        LEFT JOIN db_admission.register_verification as c
                        on a.ID = c.RegisterID
                ) as a
                where a.ver_id not in (select RegVerificationID from db_admission.register_verified)
                UNION
                select a.ID,a.Name,a.Email,b.SchoolName,a.PriceFormulir,a.VA_number,a.BilingID,a.Datetime_expired,a.RegisterAT,null,null,null
                from db_admission.register as a LEFT JOIN db_admission.school as b
                on a.SchoolID = b.ID
                where a.ID not in(select RegisterID from db_admission.register_verification)
                ORDER BY uploadAT desc";
        $query = $this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getDataRegisterBelumBayar2($Tahun)
    {
        $sql = "select a.* from (
                select a.ID,a.Name,a.Email,a.Phone,b.SchoolName,a.PriceFormulir,a.VA_number,a.BilingID,a.Datetime_expired,a.RegisterAT,c.FileUpload,c.CreateAT as uploadAT,c.ID as ver_id
                        from db_admission.register as a LEFT JOIN db_admission.school as b
                        on a.SchoolID = b.ID
                        LEFT JOIN db_admission.register_verification as c
                        on a.ID = c.RegisterID where a.SetTa = '" . $Tahun . "'
                ) as a
                where a.ver_id not in (select RegVerificationID from db_admission.register_verified)
                UNION
                select a.ID,a.Name,a.Email,a.Phone,b.SchoolName,a.PriceFormulir,a.VA_number,a.BilingID,a.Datetime_expired,a.RegisterAT,null,null,null
                from db_admission.register as a LEFT JOIN db_admission.school as b
                on a.SchoolID = b.ID
                where a.ID not in(select RegisterID from db_admission.register_verification) and a.SetTa = '" . $Tahun . "'
                ORDER BY uploadAT desc";
        // print_r($sql);die();
        $query = $this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getDataRegisterTelahBayar()
    {
        $sql = "select a.ID,a.Name,a.Email,b.SchoolName,a.PriceFormulir,a.VA_number,a.BilingID,a.Datetime_expired,a.RegisterAT,c.FileUpload,c.CreateAT as uploadAT,c.ID as ver_id,d.FormulirCode,d.VerificationAT,e.name as VerificationBY,
                d.ID as verified_id
                from db_admission.register as a LEFT JOIN db_admission.school as b
                on a.SchoolID = b.ID
                JOIN db_admission.register_verification as c
                on a.ID = c.RegisterID
                join db_admission.register_verified as d
                on c.ID = d.RegVerificationID
                LEFT JOIN db_employees.employees as e
                on e.NIP = d.VerificationBY where a.StatusReg = 0";
        $query = $this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getDataRegisterTelahBayar2($Tahun)
    {
        $sql = "select a.ID,a.Name,a.Email,a.Phone,b.SchoolName,a.PriceFormulir,a.VA_number,a.BilingID,a.Datetime_expired,a.RegisterAT,c.FileUpload,c.CreateAT as uploadAT,c.ID as ver_id,d.FormulirCode,d.VerificationAT,e.name as VerificationBY,
                d.ID as verified_id,aa.No_Ref,aa.NoKwitansi
                from db_admission.register as a LEFT JOIN db_admission.school as b
                on a.SchoolID = b.ID
                JOIN db_admission.register_verification as c
                on a.ID = c.RegisterID
                join db_admission.register_verified as d
                on c.ID = d.RegVerificationID
                LEFT JOIN db_employees.employees as e
                on e.NIP = d.VerificationBY
                join (
                    select FormulirCode,No_Ref,NoKwitansi from db_admission.formulir_number_online_m where Years = '" . $Tahun . "'
                    #UNION
                    #select FormulirCode,No_Ref from db_admission.formulir_number_offline_m where Years = '" . $Tahun . "'
                ) aa on aa.FormulirCode = d.FormulirCode
                where a.StatusReg = 0 and a.SetTa = '" . $Tahun . "'";
        $query = $this->db->query($sql, array())->result_array();
        return $query;
    }

    public function __checkDateKRS($SemesterIDActive, $date, $ProdiID, $GroupProdiID, $ClassOf, $NPM, $DB_std)
    {

        // Cek special case ke group prodi
        $dataGroupSP = $this->db->query('SELECT * FROM db_academic.academic_years_sp_krs_1 aysk
                                              WHERE aysk.SemesterID = "' . $SemesterIDActive . '" AND
                                              aysk.ClassOf = "' . $ClassOf . '" AND
                                              aysk.ProdiID = "' . $ProdiID . '" AND
                                              aysk.ProdiGroupID = "' . $GroupProdiID . '"
                                              ORDER BY ID DESC LIMIT 1')
            ->result_array();

        $data[0] = [];

        if (count($dataGroupSP) > 0) {

            $data[0] = array(
                'SemesterID' => $SemesterIDActive,
                'krsStart' => $dataGroupSP[0]['StartDate'],
                'krsEnd' => $dataGroupSP[0]['EndDate'],
                'Description' => 'SC : Group Prodi'
            );
        } else {

            $dataProdi = $this->db->query('SELECT * FROM db_academic.academic_years_sp_krs_1 aysk
                                              WHERE aysk.SemesterID = "' . $SemesterIDActive . '" AND
                                               aysk.ClassOf = "' . $ClassOf . '" AND
                                               aysk.ProdiID = "' . $ProdiID . '" AND
                                               aysk.ProdiGroupID IS NULL
                                                 ORDER BY ID DESC LIMIT 1')
                ->result_array();

            if (count($dataProdi) > 0) {

                $data[0] = array(
                    'SemesterID' => $SemesterIDActive,
                    'krsStart' => $dataProdi[0]['StartDate'],
                    'krsEnd' => $dataProdi[0]['EndDate'],
                    'Description' => 'SC : Prodi'
                );
            } else {
                $dataCL = $this->db->query('SELECT * FROM db_academic.academic_years_sp_krs_1 aysk
                                              WHERE aysk.SemesterID = "' . $SemesterIDActive . '" AND
                                               aysk.ClassOf = "' . $ClassOf . '" AND
                                               aysk.ProdiID IS NULL AND
                                               aysk.ProdiGroupID IS NULL
                                                ORDER BY ID DESC LIMIT 1')
                    ->result_array();
                if (count($dataCL) > 0) {
                    $data[0] = array(
                        'SemesterID' => $SemesterIDActive,
                        'krsStart' => $dataCL[0]['StartDate'],
                        'krsEnd' => $dataCL[0]['EndDate'],
                        'Description' => 'SC : Class Of'
                    );
                } else {
                    $data = $this->db->query('SELECT ay.krsStart,ay.krsEnd,ay.SemesterID FROM  db_academic.academic_years ay
                                            WHERE ay.SemesterID = "' . $SemesterIDActive . '" LIMIT 1')->result_array();
                }
            }
        }



        if (count($data) > 0) {
            $dataCekbayarBPP = $this->db->query('SELECT p.* FROM db_academic.semester s
                                                    LEFT JOIN db_finance.payment p ON (s.ID = p.SemesterID)
                                                    WHERE p.PTID = "2"
                                                    AND p.NPM = "' . $NPM . '"
                                                    AND s.Status = 1 ')->result_array();
            $data[0]['PaymentDetails'] = $dataCekbayarBPP;


            $todayDate = date('Y-m-d');
            $today = date('Y-m-d', strtotime($todayDate));

            $contractDateBegin = date('Y-m-d', strtotime($data[0]['krsStart']));
            $contractDateEnd = date('Y-m-d', strtotime($data[0]['krsEnd']));

            if (($today >= $contractDateBegin) && ($today <= $contractDateEnd)) {
                $data[0]['Status'] = 1;
            } else {
                $data[0]['Status'] = 0;
            }

            //            if($data[0]['krsStart'] <= $data && $data[0]['krsEnd'] >= $data){
            //                $data[0]['Status'] = 1;
            //            } else {
            //                $data[0]['Status'] = 0;
            //            }
        }

        return $data;
    }

    public function __checkDateKRSLecturer($date)
    {
        $data = $this->db->query('SELECT ay.krsStart,ay.krsEnd,ay.SemesterID FROM db_academic.semester s
                                            JOIN db_academic.academic_years ay ON (ay.SemesterID = s.ID)
                                            WHERE ay.krsStart <= "' . $date . '"
                                            AND ay.krsEnd >= "' . $date . '"
                                            AND s.Status = 1 ')->result_array();

        //        $dataCekbayarBPP = $this->db->query('SELECT p.* FROM db_academic.semester s
        //                                                    LEFT JOIN db_finance.payment p ON (s.ID = p.SemesterID)
        //                                                    WHERE p.PTID = "2"
        //                                                    AND p.NPM = "'.$NPM.'"
        //                                                    AND s.Status = 1 ')->result_array();
        //
        //        if(count($data)>0){
        //            $data[0]['PaymentDetails'] = $dataCekbayarBPP;
        //        }
        return $data;
    }

    public function getScheduleDetails($ScheduleID)
    {
        $dataSchedule = $this->db
            ->select('db_academic.schedule.*, db_academic.programs_campus.Name AS ProgramCampus, db_employees.employees.Name AS CoordinatorName')
            ->join('db_academic.programs_campus', 'db_academic.programs_campus.ID = db_academic.schedule.ProgramsCampusID')
            ->join('db_employees.employees', 'db_employees.employees.NIP = db_academic.schedule.Coordinator')
            ->get_where('db_academic.schedule', array('db_academic.schedule.ID' => $ScheduleID), 1)->result_array()[0];

        $result = $dataSchedule;

        // Detail Course
        $dataCourse = $this->db->query('SELECT sdc.*, mk.Name AS MKName, mk.NameEng AS MKNameEng, mk.MKCode,
                                                  ps.Name AS Prodi, ps.NameEng AS ProdiEng, ps.Code AS ProdiCode
                                                  FROM db_academic.schedule_details_course sdc
                                                  LEFT JOIN db_academic.program_study ps ON (sdc.ProdiID = ps.ID)
                                                  LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                  LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                  WHERE sdc.ScheduleID = "' . $ScheduleID . '"
                                                   ')->result_array();
        $result['DetailCourse'] = $dataCourse;

        // Daata Sesi
        $dataSubSesi = $this->db->query('SELECT sd.*, cl.Room, cl.Seat, cl.SeatForExam FROM db_academic.schedule_details sd
                                                  LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                  LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                  WHERE sd.ScheduleID = "' . $ScheduleID . '"
                                                  ')->result_array();

        $result['DetailSubSesi'] = $dataSubSesi;

        $dataTeamTeaching = $this->db->query('SELECT stt.*,e.Name AS Lecturer FROM db_academic.schedule_team_teaching stt
                                                        LEFT JOIN db_employees.employees e ON (stt.NIP = e.NIP)
                                                        WHERE stt.ScheduleID = "' . $ScheduleID . '"
                                                        ')->result_array();

        $result['DetailTeamTeaching'] = $dataTeamTeaching;

        if ($result['IsSemesterAntara'] == '0') {
            $dataCurriculum = $this->db->select('Name')->get_where('db_academic.semester', array('ID' => $result['SemesterID']), 1)->result_array();
        } else {
            $dataCurriculum = $this->db->select('Name')->get_where('db_academic.semester_antara', array('SemesterID' => $result['SemesterID']), 1)->result_array();
        }

        $result['DataCurriculum'] = $dataCurriculum[0];

        return $result;
    }

    public function __checkCourse($SemesterID, $MKID)
    {
        $data = $this->db->query('SELECT * FROM db_academic.schedule s
                                  JOIN db_academic.schedule_details_course sdc
                                  ON (s.ID = sdc.ScheduleID)
                                  WHERE s.SemesterID = "' . $SemesterID . '"
                                  AND sdc.MKID = "' . $MKID . '" ');
        return $data->result_array();
    }

    public function _getSemesterActive()
    {
        $data = $this->db->get_where('db_academic.semester', array('Status' => '1'), 1);

        return $data->result_array()[0];
    }

    private function _getClassStd()
    {
        $data = $this->db->query('SELECT ast.Year FROM db_academic.auth_students ast GROUP BY ast.Year');
        return $data->result_array();
    }

    public function __getStudyPlanning($dataWhere)
    {

        $db_ta = 'ta_' . $dataWhere['ClassOf'];

        $dataSemester = $this->_getSemesterActive();

        $data = $this->db->query('SELECT s.Name,s.NPM,s.ClassOf,ast.EmailPU FROM ' . $db_ta . '.students s
                                                    LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = s.NPM)
                                                    LEFT JOIN db_academic.auth_students ast ON (ast.NPM = s.NPM)
                                                    WHERE s.ProdiID = "' . $dataWhere['ProdiID'] . '"
                                                    AND s.ProgramID = "' . $dataWhere['ProgramID'] . '"
                                                    ORDER BY s.NPM ASC')
            ->result_array();

        $result = [];
        if (count($data) > 0) {
            $smtActID = $dataSemester['ID'];
            for ($i = 0; $i < count($data); $i++) {
                $data_stdCourse = $this->db->query('SELECT cd.Semester,cd.TotalSKS FROM db_academic.std_krs sk
                                                      LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sk.CDID)
                                                      WHERE sk.NPM = "' . $data[$i]['NPM'] . '" ')
                    ->result_array();

                $data_mentor = $this->db->query('SELECT ma.NIP,em.Name AS Mentor FROM db_academic.mentor_academic ma
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                                                    WHERE ma.NPM = "' . $data[$i]['NPM'] . '" ')->result_array();

                $data[$i]['DetailSemester'] = $this->getMaxCredit($db_ta, $data[$i]['NPM'], $dataWhere['ClassOf'], $smtActID, $dataWhere['ProdiID']);
                $data[$i]['DetailPayment'] = $this->getPayment($dataSemester['ID'], $data[$i]['NPM']);
                $data[$i]['DetailMentor'] = $data_mentor;

                $dataRes = array(
                    'Student' => $data[$i],
                    'StudyPlanning' => $data_stdCourse
                );

                array_push($result, $dataRes);
            }
        }

        //        print_r($result);
        //
        //        exit;

        return $result;
    }

    public function getPayment($SemesterID, $NPM)
    {
        $data = $this->db->query('SELECT p.PTID,p.Status FROM db_finance.payment p WHERE p.NPM = "' . $NPM . '"
                                            AND p.SemesterID = "' . $SemesterID . '"');
        return $data->result_array();
    }

    public function getMaxCredit($db_ta, $NPM, $ClassOf, $smtActID, $ProdiID)
    {


        $dataIDLast = $this->db->query('SELECT * FROM db_academic.semester s
                                        WHERE s.ID < "' . $smtActID . '" ORDER BY ID DESC LIMIT 1')
            ->result_array();


        if (count($dataIDLast) > 0) {
            $dataResult = $this->db->query('SELECT s.GradeValue,s.SemesterID,cd.TotalSKS AS Credit, s.Approval FROM ' . $db_ta . '.study_planning s
                                                LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = s.CDID)
                                                WHERE s.NPM = "' . $NPM . '" AND s.SemesterID <= "' . $smtActID . '" ORDER BY s.SemesterID ASC ')->result_array();

            //            print_r($dataResult);
            $TotalSKS = 0;
            $totalGradeValue = 0;

            $TotalSKSSemester = 0;
            $totalGradeValueSemester = 0;

            for ($s = 0; $s < count($dataResult); $s++) {

                // Menghitung IPK
                if ($dataResult[$s]['Approval'] == '2') {
                    $TotalSKS = $TotalSKS + (int) $dataResult[$s]['Credit'];
                    $gradeV = (int) $dataResult[$s]['Credit'] * (float) $dataResult[$s]['GradeValue'];

                    $totalGradeValue = $totalGradeValue + $gradeV;
                }

                if ($dataResult[$s]['SemesterID'] == $dataIDLast[0]['ID']) {
                    $TotalSKSSemester = $TotalSKSSemester + (int) $dataResult[$s]['Credit'];
                    $gradeVSemester = (int) $dataResult[$s]['Credit'] * (float) $dataResult[$s]['GradeValue'];
                    $totalGradeValueSemester = $totalGradeValueSemester + $gradeVSemester;
                }
            }



            $IPK = ($totalGradeValue > 0) ? $totalGradeValue / $TotalSKS : 0;

            $LastIPS = ($totalGradeValueSemester == 0 || $TotalSKSSemester == 0) ? 0 : $totalGradeValueSemester / $TotalSKSSemester;


            // Semester Saat Ini
            $dataTotalSmt = $this->db->query('SELECT s.Status FROM db_academic.semester s
                                                    WHERE s.ID >= (SELECT ID FROM db_academic.semester s2
                                                    WHERE s2.Year="' . $ClassOf . '"
                                                    LIMIT 1)')->result_array();

            $smt = 0;
            for ($s = 0; $s < count($dataTotalSmt); $s++) {
                if ($dataTotalSmt[$s]['Status'] == '1') {
                    $smt += 1;
                    break;
                } else {
                    $smt += 1;
                }
            }

            // Cek semester
            if ($smt == 1 || $smt == '1') {
                $dataCreditDef = $this->db->select('DefaultCredit')->get_where(
                    'db_academic.program_study',
                    array('ID' => $ProdiID),
                    1
                )->result_array();

                $IPK = 0;
                $LastIPS = 0;
                $MaxCredit = array('Credit' => $dataCreditDef[0]['DefaultCredit'], 'Flag' => '0');
                $smt = 1;
            } else {
                //Cek Maksimal Credit apakah di custom atau tidak
                $dataCustomCredit = $this->db->select('Credit')->get_where(
                    'db_academic.limit_credit',
                    array('SemesterID' => $smtActID, 'NPM' => $NPM),
                    1
                )->result_array();

                if (count($dataCustomCredit) > 0) {
                    $MaxCredit = array('Credit' => $dataCustomCredit[0]['Credit'], 'Flag' => '2');
                } else {

                    $pembulatanIPS = round($LastIPS, 2);
                    $pembulatanIPS = str_replace(',', '.', $pembulatanIPS);

                    $dataMakCredit = $this->db->query('SELECT Credit FROM db_academic.range_credits WHERE
                                                      IPSStart <= ' . $pembulatanIPS . '
                                                      AND IPSEnd >= ' . $pembulatanIPS . ' LIMIT 1')->result_array();
                    $MaxCredit = array('Credit' => $dataMakCredit[0]['Credit'], 'Flag' => '1');
                }
            }
        } else {
            $IPK = 0;
            $LastIPS = 0;
            $MaxCredit = array('Credit' => 0, 'Flag' => '-1');
            $smt = 1;
        }



        $result = array(
            //            'LastIPS' => $dataResult[0],
            'IPK' => $IPK,
            'LastIPS' => $LastIPS,
            'MaxCredit' => $MaxCredit,
            'Semester' => $smt
        );


        return $result;
    }

    private function getScheduleByCDID($student_DB, $NPM, $CDID, $smtActID)
    {

        $data = $this->db->query('SELECT s.*,
                                            cd.ID AS CDID, cd.MKType, cd.Semester ,cd.TotalSKS AS Credit, cd.StatusPrecondition, cd.DataPrecondition,
                                            mk.ID AS MKID, mk.MKCode, mk.Name AS MKName, mk.NameEng AS MKNameEng
                                            FROM db_academic.schedule_details_course sdc
                                            LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                            WHERE sdc.CDID = "' . $CDID . '" AND s.SemesterID = "' . $smtActID . '" ')->result_array();

        $result = [];
        if (count($data) > 0) {

            for ($s = 0; $s < count($data); $s++) {
                $d = $data[$s];
                if ($d['StatusPrecondition'] == 1) {
                    $dataPre = json_decode($d['DataPrecondition']);

                    $pre_arr = [];
                    $cek_arr = [];
                    for ($i = 0; $i < count($dataPre); $i++) {
                        $exp = explode('.', $dataPre[$i]);
                        $pre = $this->db->query('SELECT ID,MKcode,Name,NameEng FROM db_academic.mata_kuliah
                                            WHERE ID="' . $exp[0] . '"')->result_array();

                        // Cek apakah prasyarat sudah diambil atau belum
                        $cekPre = $this->db
                            ->get_where($student_DB . '.study_planning', array('NPM' => $NPM, 'MKID' => $exp[0]), 1)
                            ->result_array();

                        if (count($cekPre) > 0) {
                            array_push($cek_arr, 1);
                        } else {
                            array_push($cek_arr, 0);
                        }

                        array_push($pre_arr, $pre[0]);
                    }

                    $d['DetailPrecondition'] = $pre_arr;
                    if (in_array(0, $cek_arr)) {
                        $d['AllowPrecondition'] = 0;
                    } else {
                        $d['AllowPrecondition'] = 1;
                    }
                }

                $dataMK = $this->db
                    ->get_where($student_DB . '.study_planning', array('NPM' => $NPM, 'MKID' => $d['MKID']), 1)
                    ->result_array();

                $d['SPID'] = (count($dataMK) > 0) ? $dataMK[0]['ID'] : null;

                //Status KRS
                $dataStatus = $this->db->get_where('db_academic.std_krs', array('NPM' => $NPM, 'CDID' => $CDID), 1)->result_array();
                $d['DetailKRS'] = (count($dataStatus) > 0) ? $dataStatus[0] : $dataStatus;
                // Get Sesi
                $dataSesi = $this->db->query('SELECT sd.ID, d.Name AS DayName,d.NameEng AS DayNameEng, cl.Room, cl.Seat,
                                                  sd.StartSessions, sd.EndSessions, sd.ClassroomID, sd.DayID, sd.ScheduleID
                                                    FROM db_academic.schedule_details sd
                                                    LEFT JOIN db_academic.days d ON (d.ID=sd.DayID)
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID=sd.ClassroomID)
                                                    WHERE sd.ScheduleID = "' . $d['ID'] . '" ')->result_array();

                for ($s2 = 0; $s2 < count($dataSesi); $s2++) {

                    $querySeat = $this->db->get_where('db_academic.std_krs', array(
                        'SemesterID' => $d['SemesterID'],
                        'ScheduleID' => $d['ID']
                    ))->result_array();
                    $dataSesi[$s2]['CountSeat'] = count($querySeat);
                }

                $d['ScheduleDetails'] = $dataSesi;

                if ($d['TeamTeaching'] == '1') {
                    $dataTT = $this->db->query('SELECT * FROM db_academic.schedule_team_teaching stt WHERE stt.ScheduleID = "' . $d['ID'] . '" ')->result_array();
                    $d['TeamTeachingDetails'] = $dataTT;
                }

                array_push($result, $d);
            }
        }

        return $result;
    }

    public function getDetailStudyPlanning($NPM, $ta)
    {
        $db_ta = 'ta_' . $ta;
        $data = $this->db->query('SELECT s.ProdiID,s.ProgramID,s.NPM,s.Name, s.Photo, ma.NIP AS AcademicMentor,s.Gender,
                                    ast.EmailPU ,
                                    em.Name AS Mentor, em.EmailPU AS MentorEmailPU
                                    FROM ' . $db_ta . '.students s
                                    LEFT JOIN db_academic.auth_students ast ON (s.NPM = ast.NPM)
                                    LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = s.NPM AND ma.Status = "1")
                                    LEFT JOIN db_employees.employees em ON (ma.NIP = em.NIP)
                                    WHERE s.NPM = "' . $NPM . '" AND s.StatusStudentID = "3" ')
            ->result_array();

        $dataSemester = $this->_getSemesterActive();
        $smtActID = $dataSemester['ID'];
        $data[0]['DetailSemester'] = $this->getMaxCredit($db_ta, $NPM, $ta, $smtActID, $data[0]['ProdiID']);

        // Get Mata Kuliah yang ditawarkan
        $dataOfferings = $this->db->query('SELECT co.* FROM db_academic.course_offerings co
                                            LEFT JOIN db_academic.curriculum c ON (c.ID = co.CurriculumID)
                                            WHERE co.SemesterID = "' . $smtActID . '"
                                            AND co.ProgramsCampusID = "' . $data[0]['ProgramID'] . '"
                                             AND co.ProdiID = "' . $data[0]['ProdiID'] . '"
                                              AND co.Semester = "' . $data[0]['DetailSemester']['Semester'] . '"
                                               AND co.IsSemesterAntara = "0"
                                                AND c.Year = "' . $ta . '" LIMIT 1 ')->result_array();

        $result = [];
        if (count($dataOfferings) > 0) {
            $dataCDID = json_decode($dataOfferings[0]['Arr_CDID']);
            $result = $dataOfferings[0];
            $result['Schedule'] = [];
            for ($i = 0; $i < count($dataCDID); $i++) {
                $dataSch = $this->getScheduleByCDID($db_ta, $data[0]['NPM'], $dataCDID[$i], $smtActID);
                if (count($dataSch) > 0) {
                    for ($s = 0; $s < count($dataSch); $s++) {
                        array_push($result['Schedule'], $dataSch[$s]);
                    }
                    //                    array_push($result['Schedule'],$dataSch);
                }
            }


            // DrafKRS
            $DetailDrafKRS = $this->db->query('SELECT sk.*,cd.TotalSKS AS Credit,
                                                          skc.ID AS ReasonID, skc.Reason
                                                          FROM db_academic.std_krs sk
                                                          LEFT JOIN db_academic.curriculum_details cd
                                                          ON (cd.ID = sk.CDID)
                                                          LEFT JOIN db_academic.std_krs_comment skc ON (skc.KRSID = sk.ID)
                                                          WHERE sk.SemesterID = "' . $result['SemesterID'] . '"
                                                          AND sk.NPM = "' . $this->session->userdata('student_NPM') . '" ');
            $result['ScheduleDraf'] = $DetailDrafKRS->result_array();
        }

        $data[0]['DetailOfferings'] = $result;

        $dataPlanning = $this->db->query('SELECT cd.ID AS CDID, s.ID AS ScheduleID, mk.ID AS MKID,
                                                    mk.Name, mk.NameEng, mk.MKCode,
                                                    cd.Semester, cd.TotalSKS AS Credit,
                                                    s.ClassGroup,
                                                    sk.ID AS KRSID, sk.Status AS KRSStatus,
                                                    skc.ID AS ReasonID, skc.Reason, sk.TypeSP
                                                    FROM db_academic.std_krs sk
                                                    LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sk.CDID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = sk.ScheduleID)
                                                    LEFT JOIN db_academic.std_krs_comment skc ON (skc.KRSID = sk.ID)
                                                    WHERE sk.SemesterID = "' . $smtActID . '" AND sk.NPM = "' . $NPM . '" ')->result_array();

        if (count($dataPlanning) > 0) {
            for ($i = 0; $i < count($dataPlanning); $i++) {

                $dataPlanning[$i]['DetailSchedule'] = $this->db->query('SELECT sd.*,cl.Room,d.NameEng AS DayNameEng FROM db_academic.schedule_details sd
                                                                              LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                                              LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                                              WHERE sd.ScheduleID = "' . $dataPlanning[$i]['ScheduleID'] . '" ')
                    ->result_array();
            }
        }

        $data[0]['Student_DB'] = $db_ta;
        $data[0]['Schedule'] = $dataPlanning;

        return $data[0];
    }

    public function createDBYearAcademicNew($db_new)
    {
        $this->load->dbforge();

        if ($this->dbforge->create_database($db_new)) {

            // Student
            $this->db->query('CREATE TABLE ' . $db_new . '.students (
                              `ID` int(11) NOT NULL AUTO_INCREMENT,
                            `ProdiID` int(11) DEFAULT NULL,
                            `ProgramID` int(11) DEFAULT NULL,
                            `LevelStudyID` int(11) DEFAULT NULL,
                            `ReligionID` int(11) DEFAULT NULL,
                            `NationalityID` varchar(5) DEFAULT NULL,
                            `ProvinceID` int(11) DEFAULT NULL,
                            `CityID` int(11) DEFAULT NULL,
                            `HighSchoolID` int(11) DEFAULT NULL,
                            `HighSchool` text,
                            `MajorsHighSchool` varchar(45) DEFAULT NULL,
                            `NPM` varchar(20) DEFAULT NULL,
                            `Name` varchar(200) DEFAULT NULL,
                            `Address` text,
                            `Photo` text,
                            `Gender` varchar(2) DEFAULT NULL,
                            `PlaceOfBirth` varchar(100) DEFAULT NULL,
                            `DateOfBirth` date DEFAULT NULL,
                            `Phone` varchar(50) DEFAULT NULL,
                            `HP` varchar(50) DEFAULT NULL,
                            `ClassOf` varchar(30) DEFAULT NULL,
                            `Email` varchar(100) DEFAULT NULL,
                            `Jacket` varchar(5) DEFAULT NULL,
                            `AnakKe` int(11) DEFAULT NULL,
                            `JumlahSaudara` int(11) DEFAULT NULL,
                            `NationExamValue` decimal(10,0) DEFAULT NULL,
                            `GraduationYear` int(11) DEFAULT NULL,
                            `IjazahNumber` varchar(50) DEFAULT NULL,
                            `Father` varchar(45) DEFAULT NULL,
                            `Mother` varchar(45) DEFAULT NULL,
                            `StatusFather` varchar(2) DEFAULT NULL,
                            `StatusMother` varchar(2) DEFAULT NULL,
                            `PhoneFather` varchar(50) DEFAULT NULL,
                            `PhoneMother` varchar(50) DEFAULT NULL,
                            `OccupationFather` text,
                            `OccupationMother` text,
                            `EducationFather` varchar(45) DEFAULT NULL,
                            `EducationMother` varchar(45) DEFAULT NULL,
                            `AddressFather` text,
                            `AddressMother` text,
                            `EmailFather` varchar(100) DEFAULT NULL,
                            `EmailMother` varchar(100) DEFAULT NULL,
                            `StatusStudentID` int(11) DEFAULT NULL,
                              PRIMARY KEY (`ID`),
                              UNIQUE KEY `NPM` (`NPM`)
                            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1');

            // study_planning
            $this->db->query('CREATE TABLE ' . $db_new . '.study_planning (
                              `ID` int(11) NOT NULL AUTO_INCREMENT,
                                `SemesterID` int(11) DEFAULT NULL,
                                `MhswID` int(11) DEFAULT NULL,
                                `NPM` varchar(30) NOT NULL,
                                `ScheduleID` int(11) NOT NULL,
                                `TypeSchedule` enum("Br","Ul") DEFAULT NULL,
                                `CDID` int(11) DEFAULT NULL,
                                `MKID` int(11) DEFAULT NULL,
                                `Credit` int(11) DEFAULT NULL,
                                `Evaluasi1` float DEFAULT NULL,
                                `Evaluasi2` float DEFAULT NULL,
                                `Evaluasi3` float DEFAULT NULL,
                                `Evaluasi4` float DEFAULT NULL,
                                `Evaluasi5` float DEFAULT NULL,
                                `UTS` float DEFAULT NULL,
                                `UAS` float DEFAULT NULL,
                                `Score` float DEFAULT NULL,
                                `Grade` varchar(3) DEFAULT NULL,
                                `GradeValue` float DEFAULT NULL,
                                `Approval` enum("0","1","2") DEFAULT NULL COMMENT "0 = Blm Approve, 1 = UTS Approve, 2 = UAS Approve",
                                `StatusResign` enum("0","1") DEFAULT "0",
                                `ShowTranscript` enum("0","1") DEFAULT "1" COMMENT "0 = No Showing\n1 = Show",
                                `StatusSystem` enum("1","0") DEFAULT NULL COMMENT "0 = Siak Lama , 1 = Baru",
                                `Glue` varchar(45) DEFAULT NULL,
                                `TransferCourse` enum("0","1") DEFAULT "0" COMMENT "0 = Bukan mata kuliah transfer\n1 = Mata kuliah transfer, maka tidak perlu membaca schedule",
                                `Status` enum("0","1") DEFAULT NULL,
                              PRIMARY KEY (`ID`),
                              KEY `SemesterID` (`SemesterID`) USING BTREE,
                                KEY `NPM` (`NPM`) USING BTREE,
                                KEY `ScheduleID` (`ScheduleID`) USING BTREE,
                                KEY `CDID` (`CDID`) USING BTREE,
                                KEY `MKID` (`MKID`) USING BTREE
                            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1');
        }
    }


    public function __filterStudents($filter)
    {

        $db_s = 'ta_' . $filter['Year'];

        $Year = ($filter['Year'] != '') ? ' ma.Year = "' . $filter['Year'] . '" ' : '';
        $ProdiID = ($filter['ProdiID'] != '') ? ' AND ma.ProdiID = "' . $filter['ProdiID'] . '" ' : '';
        $NIP = ($filter['NIP'] != '') ? 'AND ma.NIP = "' . $filter['NIP'] . '"' : '';

        //        if($filter['NIP']!=''){
        //            $NIP = 'AND ma.NIP = "'.$filter['NIP'].'"';
        //        }


        $StudentsAlredyMentor = $this->db->query('SELECT ma.*,em.Name AS Lecturer FROM db_academic.mentor_academic ma
                                                      LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                                                      WHERE ' . $Year . ' ' . $ProdiID)->result_array();


        $StudentsGuidance = $this->db->query('SELECT ma.*,s.Name  FROM db_academic.mentor_academic ma
                                          LEFT JOIN ' . $db_s . '.students s ON (s.NPM = ma.NPM)
                                        WHERE ' . $Year . ' ' . $ProdiID . ' ' . $NIP . ' ')->result_array();


        $AllStudents = $this->db->query('SELECT s.NPM, s.Name, em.Name AS Lecturer, ma.NIP, ma.ID AS IDMA FROM ' . $db_s . '.students s
                                                        LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM=s.NPM)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                                                        WHERE s.ProdiID = "' . $filter['ProdiID'] . '"  ')->result_array();

        $result = array(
            //            'StudentsAlredyMentor' => $StudentsAlredyMentor,
            //            'StudentsGuidance' => $StudentsGuidance,
            'AllStudents' => $AllStudents
        );

        //        print_r('SELECT ma.* FROM db_academic.mentor_academic ma WHERE '.$Year.' '.$ProdiID.' '.$NIP);

        return $result;
    }

    public function getEmployeesBy($division = null, $position = null)
    {
        if ($division == null) {
            $division = '';
        } else {
            if ($position != null) {
                $position = '.' . $position;
            } else {
                $position = '';
            }
            $division = ' where a.PositionMain like "%' . $division . $position . '%"';
        }
        $sql = "select a.* from db_employees.employees as a " . $division;
        $query = $this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getEmployeesPICAdmissionBy()
    {
        $sql = "select a.* from db_employees.employees as a where a.PositionMain like '%10%'
                UNION
                select a.* from db_employees.employees as a where a.PositionMain like '%18%'
        ";
        $query = $this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getFormulirOfflineAvailable($StatusJual = 0)
    {
        // GET SET TA
        $this->load->model('master/m_master');
        $Years = '';
        if ($StatusJual == 0) {
            $Q_Years = $this->m_master->showData_array('db_admission.set_ta');
            $Years = ' and Years = "' . $Q_Years[0]['Ta'] . '"';
        }

        // $sql = "select FormulirCode from db_admission.formulir_number_offline_m where StatusJual = ".$StatusJual." and Print = 1 ".$Years;
        // $sql = "select FormulirCode from db_admission.formulir_number_offline_m where Status = ".$StatusJual." and StatusJual = ".$StatusJual." ".$Years;
        $sql = "select FormulirCode from db_admission.formulir_number_offline_m where StatusJual = " . $StatusJual . " " . $Years;
        $query = $this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getSchoolbyNameAC($school)
    {
        $sql = 'select ID, SchoolName from db_admission.school where SchoolName like "%' . $school . '%" limit 200';
        $query = $this->db->query($sql, array())->result_array();
        return $query;
    }

    public function __getScheduleTeacher($SemesterID, $NIP)
    {
        $dataCoor = $this->db->query('SELECT s.ClassGroup,sdc.ScheduleID, mk.ID AS MKID, mk.NameEng FROM db_academic.schedule s
                                                RIGHT JOIN db_academic.schedule_details_course sdc ON (s.ID = sdc.ScheduleID)
                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                WHERE s.SemesterID = "' . $SemesterID . '"
                                                AND s.Coordinator = "' . $NIP . '"
                                                GROUP BY sdc.ScheduleID');
        return $dataCoor->result_array();
    }

    public function __getSpecialCase($SemesterID, $AcademicDescID)
    {
        $data = $this->db->query('SELECT s.ClassGroup,aysc.*,mk.NameEng, em.Name AS Lecturers FROM db_academic.academic_years_special_case aysc
                                            LEFT JOIN db_academic.schedule s ON (s.ID=aysc.DataID)
                                            RIGHT JOIN db_academic.schedule_details_course sdc ON (s.ID = sdc.ScheduleID)
                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                            LEFT JOIN  db_employees.employees em ON (em.NIP=aysc.UserID)
                                            WHERE aysc.SemesterID = "' . $SemesterID . '"
                                            AND aysc.AcademicDescID="' . $AcademicDescID . '"
                                            GROUP BY sdc.ScheduleID');

        return $data->result_array();
    }

    public function getJadwalUjian()
    {
        $SemesterActive = $this->_getSemesterActive();
        $data = $this->db->query('SELECT s.ID,s.ClassGroup,em.Name AS CoordinatorName FROM db_academic.schedule s
                                          LEFT JOIN db_employees.employees em ON (s.Coordinator = em.NIP)
                                          WHERE s.SemesterID = "' . $SemesterActive['ID'] . '" ORDER BY s.ClassGroup ASC ')->result_array();

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $course = $this->db->query('SELECT mk.NameEng AS CourseEng FROM db_academic.schedule_details_course sdc
                                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                      WHERE sdc.ScheduleID = "' . $data[$i]['ID'] . '" LIMIT 1')->result_array();
                if (count($course) > 0) {
                    $data[$i]['CourseEng'] = $course[0]['CourseEng'];
                }
            }
        }

        return $data;
    }

    public function getDateExam($SemesterID, $Type)
    {

        //        $SemesterActive = $this->_getSemesterActive();

        $data = $this->db->query('SELECT DISTINCT ex.ExamDate FROM db_academic.exam ex
                                            WHERE ex.SemesterID = "' . $SemesterID . '"
                                            AND ex.Type LIKE "' . $Type . '" ORDER BY ex.ExamDate ASC')->result_array();

        $result = [];
        if (count($data) > 0) {
            foreach ($data as $item) {
                array_push($result, $item['ExamDate']);
            }
        }


        return $result;
    }

    public function getDateExamInEdit($SemesterID)
    {
        $data = $this->db->query('SELECT * FROM db_academic.academic_years
                                          WHERE SemesterID = "' . $SemesterID . '"
                                          LIMIT 1')->result_array();
        return (count($data)) ? $data[0] : [];
    }

    public function getDateExam4input($SemesterID)
    {
        $data = $this->db->query('SELECT * FROM db_academic.academic_years
                                          WHERE SemesterID = "' . $SemesterID . '"
                                          LIMIT 1')->result_array();

        return $data[0];
    }

    public function __checkDataCourseForExam($ScheduleID, $Type)
    {

        $SemesterActive = $this->_getSemesterActive();

        // Cek Schedule Exam
        $dataExam = $this->db->query('SELECT * FROM db_academic.exam ex
                                              LEFT JOIN db_academic.exam_group exg ON (ex.ID = exg.ExamID)
                                              WHERE ex.SemesterID = "' . $SemesterActive['ID'] . '"
                                              AND exg.ScheduleID = "' . $ScheduleID . '"
                                              AND ex.Type = "' . $Type . '" ')->result_array();

        $dataSch = $this->db->query('SELECT em.NIP, em.Name FROM db_academic.schedule s
                                              LEFT JOIN db_employees.employees em
                                              ON (em.NIP = s.Coordinator)
                                              WHERE s.ID = "' . $ScheduleID . '" ');

        $dataCourse = $this->db->query('SELECT sdc.*, mk.MKCode, mk.NameEng AS Course,
                                                ps.NameEng AS ProdiEng, cd.TotalSKS AS Credit
                                                FROM  db_academic.schedule_details_course sdc
                                                LEFT JOIN db_academic.program_study ps ON (ps.ID = sdc.ProdiID)
                                                LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                WHERE sdc.ScheduleID = "' . $ScheduleID . '"');

        $dataTeamTeaching = $this->db->query('SELECT em.NIP,em.Name AS Lecturer
                                                  FROM db_academic.schedule_team_teaching stt
                                                  LEFT JOIN db_employees.employees em ON (stt.NIP = em.NIP)
                                                  WHERE stt.ScheduleID = "' . $ScheduleID . '"');

        $dataTimetable = $this->db->query('SELECT sd.StartSessions AS Start, sd.EndSessions AS End, d.NameEng AS DayEng, cl.Room
                                                        FROM db_academic.schedule_details sd
                                                        LEFT JOIN db_academic.schedule s ON (s.ID = sd.ScheduleID)
                                                        LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                        LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                         WHERE sd.ScheduleID = "' . $ScheduleID . '"
                                                         ORDER BY d.ID, sd.StartSessions, sd.EndSessions ASC');

        $year = $this->_getClassStd();
        $dataStudents = [];
        $dataStudentsDetails = [];
        $TotalStudents = 0;
        for ($y = 0; $y < count($year); $y++) {
            $db_ = 'ta_' . $year[$y]['Year'];

            $dataSt = $this->db->query('SELECT sp.MhswID, s.NPM, s.Name FROM ' . $db_ . '.study_planning sp
                                                            LEFT JOIN ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                            WHERE sp.ScheduleID = "' . $ScheduleID . '"
                                                            AND sp.StatusSystem = "1" ')->result_array();

            if (count($dataSt) > 0) {


                $arr = array(
                    'DB_Students' => $db_,
                    'Details' => $dataSt
                );
                array_push($dataStudents, $arr);

                $TotalStudents = $TotalStudents + count($dataSt);

                for ($s = 0; $s < count($dataSt); $s++) {

                    $TypeSearch = $Type;
                    // Cek Apakah ada Di Exam
                    $dataStdExamCheck = $this->db->query('SELECT exd.* FROM db_academic.exam ex
                                                                    LEFT JOIN db_academic.exam_details exd ON (ex.ID = exd.ExamID)
                                                                    LEFT JOIN db_academic.exam_group exg ON (ex.ID = exg.ExamID)
                                                                    WHERE exg.ScheduleID = "' . $ScheduleID . '"
                                                                    AND exd.NPM = "' . $dataSt[$s]['NPM'] . '"
                                                                    AND ex.Type = "' . $TypeSearch . '"
                                                                    ORDER BY ex.ID DESC LIMIT 1')
                        ->result_array();

                    if (count($dataStdExamCheck) <= 0) {

                        if ($Type == 're_uts') {
                            $TypeSearch = 'uts';
                        } else if ($Type == 're_uas') {
                            $TypeSearch = 'uas';
                        }

                        $dataStdExamCheck = $this->db->query('SELECT exd.* FROM db_academic.exam ex
                                                                    LEFT JOIN db_academic.exam_details exd ON (ex.ID = exd.ExamID)
                                                                    LEFT JOIN db_academic.exam_group exg ON (ex.ID = exg.ExamID)
                                                                    WHERE exg.ScheduleID = "' . $ScheduleID . '"
                                                                    AND exd.NPM = "' . $dataSt[$s]['NPM'] . '"
                                                                    AND ex.Type = "' . $TypeSearch . '"
                                                                    ORDER BY ex.ID DESC LIMIT 1')
                            ->result_array();
                    }




                    $dataSt[$s]['IDEd'] = (count($dataStdExamCheck) > 0) ? $dataStdExamCheck[0]['ID'] : '';
                    $dataSt[$s]['DB_Students'] = $db_;
                    $dataSt[$s]['Status'] = (count($dataStdExamCheck) > 0) ? $dataStdExamCheck[0]['Status'] : '';;
                    array_push($dataStudentsDetails, $dataSt[$s]);
                }
            }
        }

        $res = array(
            'Exam' => $dataExam,
            'Coordinator' => $dataSch->result_array(),
            'Course' => $dataCourse->result_array(),
            'Timetable' => $dataTimetable->result_array(),
            'TeamTeaching' => $dataTeamTeaching->result_array(),
            'Students' => $dataStudents,
            'TotalStudents' => $TotalStudents,
            'StudentsDetails' => $dataStudentsDetails
        );

        return $res;
    }

    public function getScheduleExam($SemesterID, $Type)
    {
        $data = $this->db->query('SELECT e.*,s.ClassGroup,d.NameEng AS DayEng, cl.Room,
                                      em1.Name AS Pengawas1Name, em2.Name AS Pengawas2Name
                                      FROM db_academic.exam e
                                      LEFT JOIN db_academic.schedule s ON (s.ID = e.ScheduleID)
                                      LEFT JOIN db_academic.classroom cl ON (cl.ID=e.ExamClassroomID)
                                      LEFT JOIN db_academic.days d ON (d.ID=e.DayID)
                                      LEFT JOIN db_employees.employees em1 ON (em1.NIP = e.Pengawas1)
                                      LEFT JOIN db_employees.employees em2 ON (em2.NIP = e.Pengawas2)
                                      WHERE e.SemesterID = "' . $SemesterID . '"
                                      AND e.Type = "' . $Type . '"
                                      ORDER BY e.ExamDate ASC
                                      ')->result_array();

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $dataCourse = $this->db->query('SELECT sdc.CDID,mk.MKCode, mk.NameEng AS MKNameEng,
                                                    em.Name AS Coordinator
                                                    FROM db_academic.schedule_details_course sdc
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                                    LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                    WHERE sdc.ScheduleID = "' . $data[$i]['ScheduleID'] . '" GROUP BY sdc.ScheduleID LIMIT 1')->result_array();
                $data[$i]['CourseDetails'] = $dataCourse[0];

                // Get Students
                //                $dataStd = $this->db->query('SELECT * FROM db_academic.exam_details ed
                //                                                      LEFT JOIN db ');
            }
        }

        return $data;
    }

    public function __getScore($SemesterID, $ScheduleID)
    {

        $dataClassOf = $this->getClassOf();

        $dataCourse = $this->db->query('SELECT sdc.*,mk.NameEng AS MKNameEng, mk.MKCode, s.TotalAssigment FROM db_academic.schedule_details_course sdc
                                                  LEFT JOIN db_academic.curriculum_details cd ON (sdc.CDID = cd.ID)
                                                  LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                  LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                                  WHERE sdc.ScheduleID = "' . $ScheduleID . '" ')->result_array();

        $res = [];
        for ($c = 0; $c < count($dataClassOf); $c++) {
            $db_ = 'ta_' . $dataClassOf[$c]['Year'];
            $dataSc = $this->db->query('SELECT sp.*,s.Name FROM ' . $db_ . '.study_planning sp
                                                LEFT JOIN ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                WHERE sp.SemesterID = "' . $SemesterID . '" AND sp.ScheduleID = "' . $ScheduleID . '"
                                                AND sp.StatusSystem = "1" ORDER BY sp.NPM ASC')
                ->result_array();

            if (count($dataSc) > 0) {
                for ($d = 0; $d < count($dataSc); $d++) {
                    $dataSc[$d]['DB_Student'] = $db_;
                    array_push($res, $dataSc[$d]);
                }
            }
        }

        $result = array(
            'Course' => $dataCourse,
            'Students' => $res
        );

        return $result;
    }

    public function __getGrade($Score)
    {
        $data = $this->db->query('SELECT * FROM db_academic.grade g
                                            WHERE g.StartRange<= "' . $Score . '"
                                            AND g.EndRange >= "' . $Score . '" LIMIT 1');
        return $data->result_array()[0];
    }

    public function __getGradeSchedule($ScheduleID)
    {

        $data = $this->db->get_where('db_academic.grade_course', array(
            'ScheduleID' => $ScheduleID
        ), 1)->result_array();

        return $data;
    }

    public function __getDataAttendance($ScheduleID)
    {
        $SemesterActive = $this->_getSemesterActive();
        $SemesterID = $SemesterActive['ID'];

        $data = $this->db->query('SELECT attd.ID AS AttendanceID, sd.*, cl.Room, d.NameEng AS DayNameEng
                                            FROM db_academic.attendance attd
                                            LEFT JOIN db_academic.schedule_details sd ON (sd.ID = attd.SDID)
                                            LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                            LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                            WHERE attd.SemesterID = "' . $SemesterID . '" AND attd.ScheduleID = "' . $ScheduleID . '"
                                            AND attd.ScheduleID ORDER BY sd.DayID, sd.StartSessions ASC')->result_array();

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $data[$i]['DetailStudents'] = $this->db->query('SELECT * FROM db_academic.attendance_students astd
                                                                  WHERE astd.ID = "' . $data[$i]['AttendanceID'] . '" ')
                    ->result_array();
            }
        }

        return $data;
    }

    public function __getStudensAttd($SemesterID, $ScheduleID, $SDID, $Meeting)
    {


        $dataAttd = $this->db->query('SELECT attd_s.*,ast.Year AS ta, attd.SemesterID, attd.ScheduleID FROM db_academic.attendance_students attd_s
                                          LEFT JOIN db_academic.attendance attd ON (attd.ID = attd_s.ID_Attd)
                                          LEFT JOIN db_academic.auth_students ast ON (ast.NPM = attd_s.NPM)
                                          WHERE attd.SemesterID = "' . $SemesterID . '"
                                          AND attd.ScheduleID = "' . $ScheduleID . '"
                                          AND attd.SDID = "' . $SDID . '" ')->result_array();

        $result = [];
        if (count($dataAttd) > 0) {
            for ($s = 0; $s < count($dataAttd); $s++) {
                $db_ = 'ta_' . $dataAttd[$s]['ta'];
                $dataStd = $this->db->select('ID,Name,NPM,ClassOf')
                    ->get_where($db_ . '.students', array('NPM' => $dataAttd[$s]['NPM']))
                    ->result_array();

                $attdStd = ($dataAttd[$s]['M' . $Meeting] != '' && $dataAttd[$s]['M' . $Meeting] != null) ? $dataAttd[$s]['M' . $Meeting] : '0';
                $arr = array(
                    'DetailStudent' => $dataStd[0],
                    'SemesterID' => $dataAttd[$s]['SemesterID'],
                    'ScheduleID' => $dataAttd[$s]['ScheduleID'],
                    'ID_Attd' => $dataAttd[$s]['ID_Attd'],
                    'ID_Attd_S' => $dataAttd[$s]['ID'],
                    'DBStudent' => $db_,
                    'Status' => $attdStd,
                    'Description' => $dataAttd[$s]['D' . $Meeting]
                );

                array_push($result, $arr);
            }
        }

        return $result;
    }

    public function __getStudensAttd2Edit($SemesterID, $ScheduleID, $SDID, $Meeting)
    {
        $dataAttd = $this->db->query('SELECT attd_s.*,ast.Year AS ta FROM db_academic.attendance_students attd_s
                                          LEFT JOIN db_academic.attendance attd ON (attd.ID = attd_s.ID_Attd)
                                          LEFT JOIN db_academic.auth_students ast ON (ast.NPM = attd_s.NPM)
                                          WHERE attd.SemesterID = "' . $SemesterID . '"
                                          AND attd.ScheduleID = "' . $ScheduleID . '"
                                          AND attd.SDID = "' . $SDID . '" ')->result_array();

        $result = [];
        if (count($dataAttd) > 0) {
            for ($s = 0; $s < count($dataAttd); $s++) {
                $db_ = 'ta_' . $dataAttd[$s]['ta'];
                $dataStd = $this->db->select('ID,Name,NPM,ClassOf')
                    ->get_where($db_ . '.students', array('NPM' => $dataAttd[$s]['NPM']))
                    ->result_array();

                $attdStd = ($dataAttd[$s]['M' . $Meeting] != '' && $dataAttd[$s]['M' . $Meeting] != null) ? $dataAttd[$s]['M' . $Meeting] : '0';
                $arr = array(
                    'DetailStudent' => $dataStd[0],
                    'ID_Attd_S' => $dataAttd[$s]['ID'],
                    'DBStudent' => $db_,
                    'Status' => $attdStd,
                    'Description' => $dataAttd[$s]['D' . $Meeting]
                );

                array_push($result, $arr);
            }
        }

        return $result;
    }

    public function __getAttendanceSchedule($AttendanceID)
    {
        $data = $this->db->get_where(
            'db_academic.attendance',
            array('ID' => $AttendanceID),
            1
        )
            ->result_array();

        // Mendapatkan Presensi Students
        $dataStd = $this->db->get_where(
            'db_academic.attendance_students',
            array('ID_Attd' => $AttendanceID)
        )->result_array();

        if (count($data) > 0) {
            for ($s = 1; $s <= 14; $s++) {

                $Lecturers = $this->db->query('SELECT al.*, em.Name FROM db_academic.attendance_lecturers al
                                                         LEFT JOIN db_employees.employees em ON (al.NIP = em.NIP)
                                                         WHERE al.ID_Attd = "' . $data[0]['ID'] . '"
                                                         AND al.Meet = "' . $s . '"
                                                         ORDER BY al.Date, al.In, al.Out ASC ')->result_array();

                $data[0]['AttdLecturers' . $s] = $Lecturers;

                $p = '-';
                $a = '-';

                if ($data[0]['Meet' . $s] == 1 && $data[0]['Meet' . $s] == '1') {

                    $p = 0;
                    $a = 0;
                    for ($st = 0; $st < count($dataStd); $st++) {
                        if ($dataStd[$st]['M' . $s] == '2' || $dataStd[$st]['M' . $s] == 2) {
                            $a += 1;
                        } else if ($dataStd[$st]['M' . $s] == '1' || $dataStd[$st]['M' . $s] == 1) {
                            $p += 1;
                        }
                    }
                }

                $dataWhereS_Ex = $this->db->get_where(
                    'db_academic.schedule_exchange',
                    array(
                        'ID_Attd' => $AttendanceID,
                        'Meeting' => $s
                    ),
                    1
                )->result_array();
                $ScheduleEx_Status = (count($dataWhereS_Ex) > 0) ? $dataWhereS_Ex[0]['Status'] : '-';

                $data[0]['ScheduleExchange_Status' . $s] = $ScheduleEx_Status;
                $data[0]['S_P' . $s] = $p;
                $data[0]['S_A' . $s] = $a;
            }
        }

        return $data;
    }

    public function __getdataExchange($ID_Attd, $ScheduleID, $SDID, $Meeting)
    {

        $dataSD = $this->db->query('SELECT * FROM db_academic.schedule_details sd
                                        WHERE sd.ID = "' . $SDID . '" LIMIT 1 ')->result_array();

        $dataSEx = $this->db->query('SELECT * FROM db_academic.schedule_exchange se
                                              WHERE se.ID_Attd = "' . $ID_Attd . '"
                                              AND se.Meeting = "' . $Meeting . '" LIMIT 1')->result_array();

        $coor = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule s
                                            LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                            WHERE s.ID = "' . $ScheduleID . '"
                                              ')->result_array();

        $teamt = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule_team_teaching stt
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                    WHERE stt.ScheduleID = "' . $ScheduleID . '"
                                                    ')->result_array();

        if (count($teamt) > 0) {
            for ($t = 0; $t < count($teamt); $t++) {
                array_push($coor, $teamt[$t]);
            }
        }

        $res = array(
            'Lecturer' => $coor,
            'S_Details' => $dataSD[0],
            'S_Exchange' => $dataSEx
        );

        return $res;
    }

    public function getDataCourse2Score($SemesterID, $ProdiID, $CombinedClasses, $IsSemesterAntara)
    {

        $dataSch = $this->db->query('SELECT sc.Classgroup,sc.TotalAssigment, sc.TeamTeaching,
                                        sdc.*,cd.TotalSKS AS Credit, mk.MKCode, mk.Name AS MKName,
                                        mk.NameEng AS MKNameEng, sc.Coordinator, em.Name AS CoordinatorName
                                        FROM db_academic.schedule_details_course sdc
                                        LEFT JOIN db_academic.schedule sc ON (sc.ID = sdc.ScheduleID)
                                        LEFT JOIN db_employees.employees em ON (em.NIP = sc.Coordinator)
                                        LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                        WHERE sc.SemesterID = "' . $SemesterID . '"
                                        AND sdc.ProdiID = "' . $ProdiID . '"
                                        AND sc.CombinedClasses = "' . $CombinedClasses . '"
                                        AND sc.IsSemesterAntara = "' . $IsSemesterAntara . '"
                                        ORDER BY sdc.ID ASC')->result_array();

        if (count($dataSch) > 0) {
            for ($i = 0; $i < count($dataSch); $i++) {
                $detailSch = $this->db->query('SELECT cl.Room, d.NameEng AS DayEng, sd.StartSessions, sd.EndSessions FROM db_academic.schedule_details sd
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                    LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                    WHERE sd.ScheduleID = "' . $dataSch[$i]['ScheduleID'] . '"
                                                     ORDER BY sd.DayID, sd.StartSessions ASC')->result_array();

                $team = $this->db->query('SELECT em.NIP, em.Name FROM db_academic.schedule_team_teaching stt
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                    WHERE stt.ScheduleID = "' . $dataSch[$i]['ScheduleID'] . '" ')
                    ->result_array();

                $dataSch[$i]['DetailTeamTeaching'] = $team;
                $dataSch[$i]['DetailSchedule'] = $detailSch;

                // Silabus SAP
                $dataSilabusSAP = $this->db->get_where(
                    'db_academic.grade_course',
                    array(
                        'SemesterID' => $SemesterID,
                        'ScheduleID' => $dataSch[$i]['ScheduleID']
                    ),
                    1
                )->result_array();

                $dataSch[$i]['dataSilabusSAP'] = $dataSilabusSAP;
            }
        }


        return $dataSch;
    }

    public function getDataStudents_Schedule($SemesterID, $ScheduleID)
    {

        $dataClassOf = $this->getClassOf();

        $res = [];
        for ($c = 0; $c < count($dataClassOf); $c++) {
            $db_ = 'ta_' . $dataClassOf[$c]['Year'];
            $dataSc = $this->db->query('SELECT sp.*,s.Name FROM ' . $db_ . '.study_planning sp
                                                LEFT JOIN ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                WHERE sp.SemesterID = "' . $SemesterID . '"
                                                AND sp.ScheduleID = "' . $ScheduleID . '"
                                                AND sp.StatusSystem = "1" ')->result_array();

            if (count($dataSc) > 0) {
                for ($d = 0; $d < count($dataSc); $d++) {
                    $dataSc[$d]['DB_Student'] = $db_;
                    array_push($res, $dataSc[$d]);
                }
            }
        }

        return $res;
    }

    public function getAllStudents()
    {
        $dataClassOf = $this->getClassOf();
        $res = [];
        for ($c = 0; $c < count($dataClassOf); $c++) {
            $db_ = 'ta_' . $dataClassOf[$c]['Year'];
            $dataSc = $this->db->query('SELECT * FROM ' . $db_ . '.students ORDER BY ProdiID, NPM ASC ')->result_array();

            array_push($res, $dataSc);
        }

        print_r($res);

        return $res;
    }

    public function getAgama()
    {
        $sql = "select * from db_employees.religion";
        $query = $this->db->query($sql, array())->result_array();
        return $query;
    }

    public function insertToMainKRS($KRSID, $TypeSP, $db_ta)
    {
        $dataCourse = $this->db->query('SELECT sk.*, s.ID AS MhswID, cd.MKID, cd.TotalSKS AS Credit
                                                FROM db_academic.std_krs sk
                                                LEFT JOIN ' . $db_ta . '.students s ON (s.NPM = sk.NPM)
                                                LEFT JOIN db_academic.curriculum_details cd ON (sk.CDID = cd.ID)
                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                WHERE sk.ID = "' . $KRSID . '" ')
            ->result_array()[0];
        $dataInsert = array(
            'SemesterID' => $dataCourse['SemesterID'],
            'MhswID' => $dataCourse['MhswID'],
            'NPM' => $dataCourse['NPM'],
            'ScheduleID' => $dataCourse['ScheduleID'],
            'TypeSchedule' => $TypeSP,
            'CDID' => $dataCourse['CDID'],
            'MKID' => $dataCourse['MKID'],
            'Credit' => $dataCourse['Credit'],
            'StatusSystem' => '1',
            'Status' => '0'
        );

        $this->db->insert($db_ta . '.study_planning', $dataInsert);

        // Cek Attd dosen
        $dataAttd = $this->db->get_where(
            'db_academic.attendance',
            array(
                'SemesterID' => $dataCourse['SemesterID'],
                'ScheduleID' => $dataCourse['ScheduleID']
            )
        )->result_array();
        if (count($dataAttd) > 0) {
            for ($i = 0; $i < count($dataAttd); $i++) {
                $dataAttdMhs = array(
                    'ID_Attd' => $dataAttd[$i]['ID'],
                    'NPM' => $dataCourse['NPM']
                );
                // Cek apakah ada datanya atau blm
                $dataChek = $this->db->get_where('db_academic.attendance_students', $dataAttdMhs, 1)->result_array();
                if (count($dataChek) <= 0) {
                    $this->db->insert('db_academic.attendance_students', $dataAttdMhs);
                }
            }
        }
    }

    public function cek_deadline_paymentNPM($NPM)
    {
        error_reporting(0);
        $this->load->model('master/m_master');
        $arr = array();
        $SemesterID = $this->m_master->caribasedprimary('db_academic.semester', 'Status', 1);
        // cari data payment yang status = 0 desc limit 1
        $sql = 'select * from db_finance.payment where NPM = ? and SemesterID = ? order by ID ASC';
        $query = $this->db->query($sql, array($NPM, $SemesterID[0]['ID']))->result_array();
        // print_r($query);die();
        if (count($query) > 0) {
            $resFinal = [];
            for ($g = 0; $g < count($query); $g++) {
                $dd = array();
                $dataG = $query[$g];
                $ID_payment = $dataG['ID'];

                $PTID = $this->m_master->caribasedprimary('db_finance.payment_type', 'ID', $dataG['PTID']);

                $get = $this->m_master->caribasedprimary('db_finance.payment_students', 'ID_payment', $ID_payment);

                $getConstVa = $this->m_master->caribasedprimary('db_va.master_va', 'ID', 1);
                $cicilan = (count($get) > 1) ? 1 : 0;
                $dd = array(
                    'Invoice' => $dataG['Invoice'],
                    'Discount' => $dataG['Discount'],
                    'PaymentType' => $PTID[0]['Description'],
                    'Installment' => $cicilan,
                    'Const_VA' => $getConstVa[0]['Const_VA'] . '' . $NPM,
                );

                $arr['Invoice'] = $dd;
                $cc = array();
                for ($i = 0; $i < count($get); $i++) {
                    $cc[] = array('Deadline' => $get[$i]['Deadline'], 'Price' => $get[$i]['Invoice'], 'Status' => $get[$i]['Status'], 'UpdateAt' => $get[$i]['UpdateAt']);
                }
                $arr['DetailPayment'] = $cc;

                array_push($resFinal, $arr);
            }
        }
        return $resFinal;
    }

    public function __getCC_GroupCalss($ProdiID)
    {
        $dataSmtAct = $this->_getSemesterActive();

        $data = $this->db->query('SELECT s.ID, s.ClassGroup FROM db_academic.schedule_details_course sdc
                                                LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                                WHERE s.SemesterID = "' . $dataSmtAct['ID'] . '"
                                                AND sdc.ProdiID = "' . $ProdiID . '"
                                                GROUP BY s.ID ')->result_array();


        return $data;
    }


    public function getStudentByScheduleID($SemesterID, $ScheduleID)
    {

        $dataCl = $this->getClassOf();

        $arrDataStd = [];
        if (count($dataCl) > 0) {
            for ($i = 0; $i < count($dataCl); $i++) {
                $db_ = 'ta_' . $dataCl[$i]['Year'];

                $data = $this->db->query('SELECT sp.ID AS SPID, sp.UTS, sp.UAS, s.NPM,s.Name, sp.StatusResign, ay.totalSession FROM ' . $db_ . '.study_planning sp
                                                    LEFT JOIN ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                    LEFT JOIN db_academic.academic_years ay ON (ay.SemesterID = sp.SemesterID)
                                                    WHERE sp.SemesterID ="' . $SemesterID . '"
                                                    AND sp.ScheduleID = "' . $ScheduleID . '"
                                                    ORDER BY s.NPM ASC
                                                     ')->result_array();

                if (count($data) > 0) {
                    for ($s = 0; $s < count($data); $s++) {
                        $data[$s]['DB_Student'] = $db_;
                        array_push($arrDataStd, $data[$s]);
                    }
                }
            }
        }

        return $arrDataStd;
    }

    public function __getSimpleSearch($key)
    {

        // Student
        $dataClass = $this->getClassOf();
        $selcStd = '';
        $joinStd = '';
        $whereStd = '';
        for ($c = 0; $c < count($dataClass); $c++) {
            $db_ = 'ta_' . $dataClass[$c]['Year'];

            $slc = 's' . $c . '.Name Name' . $c . ',';
            $selcStd = $selcStd . ' ' . $slc;

            $std = ' LEFT JOIN ' . $db_ . '.students s' . $c . ' ON (s' . $c . '.NPM = ast.NPM ) ';
            $joinStd = $joinStd . ' ' . $std;

            $w = 's' . $c . '.Name LIKE "%' . $key . '%" OR ';
            $whereStd = $whereStd . ' ' . $w;
        }

        $dataS = $this->db->query('SELECT ' . $selcStd . ' ast.NPM, ss.Description AS StatusStudent FROM db_academic.auth_students ast ' . $joinStd . '
                                    LEFT JOIN db_academic.status_student ss ON (ss.ID = ast.StatusStudentID)
                                    WHERE  ' . $whereStd . ' ast.NPM LIKE "%' . $key . '%"
                                    LIMIT 5')->result_array();

        $result = [];
        if (count($dataS) > 0) {

            for ($dt = 0; $dt < count($dataS); $dt++) {
                $d = $dataS[$dt];
                $Name = '';
                for ($c = 0; $c < count($dataClass); $c++) {
                    if ($d['Name' . $c] != '' && $d['Name' . $c] != null) {
                        $Name = $d['Name' . $c];
                        break;
                    }
                }
                $arr = array(
                    'Name' => $Name,
                    'Username' => $d['NPM'],
                    'Status' => ucwords(strtolower($d['StatusStudent'])),
                    'Flag' => 'std'
                );

                array_push($result, $arr);
            }
        }

        $dataEm = $this->db->query('SELECT em.NIP, em.Name, em.Password, em.PositionMain, em.PositionOther1, em.PositionOther2, em.PositionOther3, ems.Description AS StatusEmployee FROM db_employees.employees em
                                              LEFT JOIN db_employees.employees_status ems ON (ems.IDStatus = em.StatusEmployeeID)
                                              WHERE em.StatusEmployeeID != -1 AND em.StatusEmployeeID != -2 AND ( em.Name LIKE "%' . $key . '%" OR em.NIP LIKE "%' . $key . '%" )
                                               LIMIT 5')->result_array();

        if (count($dataEm) > 0) {
            for ($e = 0; $e < count($dataEm); $e++) {
                $d = $dataEm[$e];
                $arr = array(
                    'Name' => $d['Name'],
                    'Username' => $d['NIP'],
                    'Token' => $d['Password'],
                    'Status' => ucwords(strtolower($d['StatusEmployee'])),
                    'Flag' => 'emp',
                    'Position' => [$d['PositionMain'], $d['PositionOther1'], $d['PositionOther2'], $d['PositionOther3']]
                );

                array_push($result, $arr);
            }
        }




        return $result;
    }

    public function __getSimpleSearchStudents($key)
    {

        // Student
        $dataClass = $this->getClassOf();
        $selcStd = '';
        $joinStd = '';
        $whereStd = '';
        for ($c = 0; $c < count($dataClass); $c++) {
            $db_ = 'ta_' . $dataClass[$c]['Year'];

            $slc = 's' . $c . '.Name Name' . $c . ',';
            $selcStd = $selcStd . ' ' . $slc;

            $std = ' LEFT JOIN ' . $db_ . '.students s' . $c . ' ON (s' . $c . '.NPM = ast.NPM ) ';
            $joinStd = $joinStd . ' ' . $std;

            $w = 's' . $c . '.Name LIKE "%' . $key . '%" OR ';
            $whereStd = $whereStd . ' ' . $w;
        }

        $dataS = $this->db->query('SELECT ' . $selcStd . ' ast.NPM FROM db_academic.auth_students ast ' . $joinStd . '
                                    WHERE  ' . $whereStd . ' ast.NPM LIKE "%' . $key . '%"
                                    LIMIT 5')->result_array();

        $result = [];
        if (count($dataS) > 0) {

            for ($dt = 0; $dt < count($dataS); $dt++) {
                $d = $dataS[$dt];
                $Name = '';
                $DB_Student = '';
                for ($c = 0; $c < count($dataClass); $c++) {
                    if ($d['Name' . $c] != '' && $d['Name' . $c] != null) {
                        $Name = $d['Name' . $c];
                        $DB_Student = 'ta_' . $dataClass[$c]['Year'];
                        break;
                    }
                }
                $arr = array(
                    'Name' => $Name,
                    'DB_Student' => $DB_Student,
                    'Username' => $d['NPM'],
                    'Flag' => 'std'
                );

                array_push($result, $arr);
            }
        }

        return $result;
    }

    public function delCombinedClass($data_arr)
    {
        // Cek apakah ada student atau tidak
        $dataGetJad = $this->db->get_where(
            'db_academic.schedule_details_course',
            array('ID' => $data_arr['SDCID']),
            1
        )->result_array();

        $getSmtAct = $this->_getSemesterActive();

        $res_std = [];
        $res_sp = [];
        // cek apakah di std KRS ada ?
        if (count($dataGetJad) > 0) {
            for ($i = 0; $i < count($dataGetJad); $i++) {

                $d = $dataGetJad[$i];

                $dataClass = $this->getClassOf();

                for ($c = 0; $c < count($dataClass); $c++) {

                    $semester = $this->_getSeemsterByClassOf($dataClass[$c]['Year']);

                    $db_ = 'ta_' . $dataClass[$c]['Year'];
                    $dataStd = $this->db->query('SELECT s.NPM FROM db_academic.std_krs sk
                                                          LEFT JOIN ' . $db_ . '.students s
                                                          ON (s.NPM = sk.NPM)
                                                          WHERE sk.ScheduleID = "' . $d['ScheduleID'] . '"
                                                          AND sk.CDID = "' . $d['CDID'] . '"
                                                          AND sk.SemesterID = "' . $getSmtAct['ID'] . '"
                                                          AND s.ProdiID = "' . $d['ProdiID'] . '"
                                                          ')->result_array();


                    if (count($dataStd) > 0 && $semester == $data_arr['Semester']) {
                        for ($st = 0; $st < count($dataStd); $st++) {
                            array_push($res_std, $dataStd[$st]['NPM']);
                        }
                    }

                    $dataSp = $this->db->query('SELECT s.NPM FROM ' . $db_ . '.study_planning sp
                                                          LEFT JOIN ' . $db_ . '.students s
                                                          ON (s.NPM = sp.NPM)
                                                          WHERE sp.ScheduleID = "' . $d['ScheduleID'] . '"
                                                          AND sp.SemesterID = "' . $d['CDID'] . '"
                                                          AND sp.CDID = "' . $getSmtAct['ID'] . '"
                                                          AND s.ProdiID = "' . $d['ProdiID'] . '"
                                                          ')->result_array();

                    if (count($dataSp) > 0 && $semester == $data_arr['Semester']) {
                        for ($sp = 0; $sp < count($dataSp); $sp++) {
                            array_push($res_sp, $dataSp[$sp]['NPM']);
                        }
                    }
                }
            }
        }

        // Penggabungan Array
        $result = $res_std;
        if (count($res_std) > 0 && count($res_sp) > 0) {
            for ($sp = 0; $sp < count($res_sp); $sp++) {
                if (!in_array($res_sp[$sp], $result)) {
                    array_push($result, $res_sp[$sp]);
                }
            }
        } else if (count($res_std) <= 0 && count($res_sp) > 0) {
            $result = $res_sp;
        }

        if (count($result) <= 0) {
            $this->db->where('ID', $data_arr['SDCID']);
            $this->db->delete('db_academic.schedule_details_course');

            // cek apakah status combine update atau tidak
            $dataC = $this->db->select('ID')
                ->get_where('db_academic.schedule_details_course', array('ScheduleID' => $data_arr['ScheduleID']))
                ->result_array();

            if (count($dataC) == 1) {

                $this->db->set('CombinedClasses', '0');
                $this->db->where('ID', $data_arr['ScheduleID']);
                $this->db->update('db_academic.schedule');
            }

            $resT = array(
                'Status' => 1,
                'Details' => []
            );
        } else {
            $resT = array(
                'Status' => 0,
                'Details' => $result
            );
        }

        return $resT;
    }

    public function getStdCombinedClass($data_arr, $smt)
    {


        // Cek apakah ada student atau tidak
        $dataGetJad = $this->db->get_where(
            'db_academic.schedule_details_course',
            array('ID' => $data_arr['SDCID']),
            1
        )->result_array();



        $getSmtAct = $this->_getSemesterActive();

        $res_std = [];
        $res_sp = [];
        // cek apakah di std KRS ada ?
        if (count($dataGetJad) > 0) {
            for ($i = 0; $i < count($dataGetJad); $i++) {

                $d = $dataGetJad[$i];

                $dataClass = $this->getClassOf();

                for ($c = 0; $c < count($dataClass); $c++) {
                    $semester = $this->_getSeemsterByClassOf($dataClass[$c]['Year']);
                    $db_ = 'ta_' . $dataClass[$c]['Year'];
                    $dataStd = $this->db->query('SELECT s.NPM,s.Name FROM db_academic.std_krs sk
                                                          LEFT JOIN ' . $db_ . '.students s
                                                          ON (s.NPM = sk.NPM)
                                                          WHERE sk.ScheduleID = "' . $d['ScheduleID'] . '"
                                                          AND sk.CDID = "' . $d['CDID'] . '"
                                                          AND sk.SemesterID = "' . $getSmtAct['ID'] . '"
                                                          AND s.ProdiID = "' . $d['ProdiID'] . '"
                                                          ')->result_array();


                    if (count($dataStd) > 0 && $semester == $smt) {
                        for ($st = 0; $st < count($dataStd); $st++) {
                            array_push($res_std, $dataStd[$st]['NPM']);
                        }
                    }

                    $dataSp = $this->db->query('SELECT s.NPM,s.Name FROM ' . $db_ . '.study_planning sp
                                                          LEFT JOIN ' . $db_ . '.students s
                                                          ON (s.NPM = sp.NPM)
                                                          WHERE sp.ScheduleID = "' . $d['ScheduleID'] . '"
                                                          AND sp.SemesterID = "' . $getSmtAct['ID'] . '"
                                                          AND sp.CDID = "' . $d['CDID'] . '"
                                                          AND s.ProdiID = "' . $d['ProdiID'] . '"
                                                          ')->result_array();

                    if (count($dataSp) > 0 && $semester == $smt) {
                        for ($sp = 0; $sp < count($dataSp); $sp++) {
                            array_push($res_sp, $dataSp[$sp]['NPM']);
                        }
                    }
                }
            }
        }


        // Penggabungan Array
        $result = $res_std;
        if (count($res_std) > 0 && count($res_sp) > 0) {
            for ($sp = 0; $sp < count($res_sp); $sp++) {
                if (!in_array($res_sp[$sp], $result)) {
                    array_push($result, $res_sp[$sp]);
                }
            }
        } else if (count($res_std) <= 0 && count($res_sp) > 0) {
            $result = $res_sp;
        }

        if (count($result) > 0) {
            $resT = array(
                'Status' => 0,
                'Details' => $result
            );
        } else {
            $resT = array(
                'Status' => 1,
                'Details' => []
            );
        }

        return $resT;
    }

    public function kickStudent($SemesterID, $ScheduleID, $CDID, $ProdiID)
    {

        $delW = array(
            'SemesterID' => $SemesterID,
            'CDID' => $CDID
        );


        // Delete dari KSM
        $dataCl = $this->getClassOf();
        for ($l = 0; $l < count($dataCl); $l++) {
            $db_ = 'ta_' . $dataCl[$l]['Year'];

            $this->db->where($delW);
            $this->db->delete($db_ . '.study_planning');
        }

        $ds = $this->db->select('ID,ScheduleID')->get_where('db_academic.std_krs', $delW)->result_array();
        if (count($ds) > 0) {
            for ($c = 0; $c < count($ds); $c++) {
                $d = $ds[$c];
                $this->db->where('KRSID', $d['ID']);
                $this->db->delete('db_academic.std_krs_comment');



                // Delete Presensi
                $Attd = $this->db->select('ID')->get_where('db_academic.attendance', array(
                    'SemesterID' => $SemesterID,
                    'ScheduleID' => $d['ScheduleID']
                ))->result_array();
                if (count($Attd) > 0) {
                    for ($a = 0; $a < count($Attd); $a++) {
                        $this->db->where('ID_Attd', $Attd[$a]['ID']);
                        $this->db->delete('db_academic.attendance_students');
                    }
                }
            }
        }




        // Delete dari KRS
        $this->db->where($delW);
        $this->db->delete('db_academic.std_krs');

        return 1;
    }

    public function __getStudentByScheduleID($ScheduleID)
    {

        // Fungsi ini mengambil student dari yang sudah di approve dan dari yang masih planning

        $getSmtAct = $this->_getSemesterActive();

        $dataCL = $this->getClassOf();

        $selWhere = array(
            'SemesterID' => $getSmtAct['ID'],
            'ScheduleID' => $ScheduleID
        );
        $res = [];
        for ($c = 0; $c < count($dataCL); $c++) {
            $d = $dataCL[$c];
            $db_ = 'ta_' . $d['Year'];
            $dataSP = $this->db->select('NPM')->get_where($db_ . '.study_planning', $selWhere)->result_array();
            if (count($dataSP) > 0) {
                for ($s = 0; $s < count($dataSP); $s++) {
                    array_push($res, $dataSP[$s]['NPM']);
                }
            }
        }

        // Get From Std KRS
        $dataSTD = $this->db->select('NPM')->get_where('db_academic.std_krs', $selWhere)->result_array();

        // Menggabungkan
        for ($m = 0; $m < count($dataSTD); $m++) {
            if (!in_array($dataSTD[$m]['NPM'], $res)) {
                array_push($res, $dataSTD[$m]['NPM']);
            }
        }


        return $res;
    }

    public function __getStudentApprovedKRS($SemesterID, $ScheduleID, $Type = '')
    {
        // Fungsi ini mengambil student dari yang sudah di approve dan dari yang masih planning

        $dataCL = $this->getClassOf();

        $res = [];
        for ($c = 0; $c < count($dataCL); $c++) {
            $d = $dataCL[$c];
            $db_ = 'ta_' . $d['Year'];
            $dataSP = $this->db->query('SELECT sp.NPM,s.Name, sp.UTS, sp.UAS, 
                                                    sp.Evaluasi1, sp.Evaluasi2, sp.Evaluasi3 ,sp.Evaluasi4 ,sp.Evaluasi5,
                                                    sp.Score, sp.Grade FROM ' . $db_ . '.study_planning sp
                                                    LEFT JOIN ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                    WHERE sp.SemesterID = "' . $SemesterID . '"
                                                    AND sp.ScheduleID = "' . $ScheduleID . '"
                                                    ORDER BY sp.NPM ASC ')->result_array();

            if (count($dataSP) > 0) {
                if ($Type == '') {
                    for ($s = 0; $s < count($dataSP); $s++) {
                        array_push($res, $dataSP[$s]['NPM']);
                    }
                } else {
                    for ($s = 0; $s < count($dataSP); $s++) {
                        array_push($res, $dataSP[$s]);
                    }
                }
            }
        }

        return $res;
    }

    public function __getStudentNotYetApprovedKRS($SemesterID, $ScheduleID)
    {

        // Get From Std KRS
        $dataSTD = $this->db->query('SELECT sk.NPM,auts.Name FROM db_academic.std_krs sk
                                              LEFT JOIN db_academic.auth_students auts ON (auts.NPM = sk.NPM)
                                              WHERE sk.SemesterID = "' . $SemesterID . '" AND sk.ScheduleID = "' . $ScheduleID . '"
                                              ORDER BY sk.NPM ASC ')
            ->result_array();


        return $dataSTD;
    }

    public function __getStudentByScheduleIDApproved($SemesterID, $ScheduleID)
    {

        $dataCL = $this->getClassOf();

        $res = [];
        for ($c = 0; $c < count($dataCL); $c++) {
            $d = $dataCL[$c];
            $db_ = 'ta_' . $d['Year'];

            // Cek DB Exist
            $dbExist = $this->db->query('SELECT SCHEMA_NAME
                                                    FROM INFORMATION_SCHEMA.SCHEMATA
                                                    WHERE SCHEMA_NAME = "' . $db_ . '" ')->result_array();

            if (count($dbExist) > 0) {
                $dataSP = $this->db->query('SELECT s.NPM,s.ClassOf,s.Name FROM ' . $db_ . '.study_planning sp
                                                            LEFT JOIN  ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                            WHERE sp.SemesterID = "' . $SemesterID . '"
                                                            AND sp.ScheduleID = "' . $ScheduleID . '" ')->result_array();
                if (count($dataSP) > 0) {
                    for ($s = 0; $s < count($dataSP); $s++) {
                        array_push($res, $dataSP[$s]);
                    }
                }
            }
        }
        return $res;
    }
    public function __getStudentByScheduleIDApproved_details($SemesterID, $ScheduleID, $CDID)
    {

        $dataCL = $this->getClassOf();

        $res = [];
        for ($c = 0; $c < count($dataCL); $c++) {
            $d = $dataCL[$c];
            $db_ = 'ta_' . $d['Year'];

            // Cek DB Exist
            $dbExist = $this->db->query('SELECT SCHEMA_NAME
                                                    FROM INFORMATION_SCHEMA.SCHEMATA
                                                    WHERE SCHEMA_NAME = "' . $db_ . '" ')->result_array();

            if (count($dbExist) > 0) {
                $dataSP = $this->db->query('SELECT s.NPM,s.ClassOf,s.Name FROM ' . $db_ . '.study_planning sp
                                                            LEFT JOIN  ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                            WHERE sp.SemesterID = "' . $SemesterID . '"
                                                            AND sp.ScheduleID = "' . $ScheduleID . '"
                                                             AND sp.CDID = "' . $CDID . '" ')->result_array();
                if (count($dataSP) > 0) {
                    for ($s = 0; $s < count($dataSP); $s++) {
                        array_push($res, $dataSP[$s]);
                    }
                }
            }
        }
        return $res;
    }

    public function __getStudentByScheduleIDInStudyPlanning($SemesterID, $ScheduleID)
    {

        $data = $this->db->query('SELECT aut_s.ID, aut_s.NPM, aut_s.Name, aut_s.Year AS ClassOf FROM db_academic.std_krs std_k
                                                LEFT JOIN db_academic.auth_students aut_s ON (aut_s.NPM = std_k.NPM)
                                                WHERE std_k.SemesterID = "' . $SemesterID . '"
                                                AND std_k.ScheduleID = "' . $ScheduleID . '" ')->result_array();
        return $data;
    }
    public function __getStudentByScheduleIDInStudyPlanning_details($SemesterID, $ScheduleID, $CDID)
    {

        $data = $this->db->query('SELECT aut_s.ID, aut_s.NPM, aut_s.Name, aut_s.Year AS ClassOf FROM db_academic.std_krs std_k
                                                LEFT JOIN db_academic.auth_students aut_s ON (aut_s.NPM = std_k.NPM)
                                                WHERE std_k.SemesterID = "' . $SemesterID . '"
                                                AND std_k.ScheduleID = "' . $ScheduleID . '"
                                                 AND std_k.CDID = "' . $CDID . '" ')->result_array();
        return $data;
    }

    public function _getSeemsterByClassOf($Year)
    {
        $smtAct = $this->_getSemesterActive();
        $data = $this->db->query('SELECT s.* FROM db_academic.semester s
                                            WHERE s.Year>= "' . $Year . '" ')
            ->result_array();

        $smt = 1;
        $SemesterID = $smtAct['ID'];
        for ($i = 0; $i < count($data); $i++) {

            if ($SemesterID != '' && $SemesterID != 0) {
                if ($data[$i]['ID'] != $SemesterID) {
                    $smt = $smt + 1;
                } else {
                    break;
                }
            } else {
                if ($data[$i]['Status'] == 0) {
                    $smt = $smt + 1;
                } else {
                    break;
                }
            }
        }

        return $smt;
    }

    public function __getMonitoringAttdLecturer($SemesterID, $ProdiID)
    {
        $data = $this->db->query('SELECT sdc.ScheduleID, s.ClassGroup,  mk.NameEng AS MKNameEng, mk.MKCode, em.Name AS Lecturer, em.NIP,
                                             ay.totalSession
                                             FROM db_academic.schedule_details_course sdc
                                             LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                             LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                             LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                             LEFT JOIN db_academic.academic_years ay ON (ay.SemesterID = s.SemesterID)
                                             WHERE sdc.ProdiID = "' . $ProdiID . '"
                                             AND s.SemesterID = "' . $SemesterID . '" GROUP BY s.ID ORDER BY s.ClassGroup ASC ')->result_array();


        // Get Schedule && Attendace
        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $d = $data[$i];

                $dataSc = $this->db->query('SELECT sd.ID, d.NameEng AS DayEng, cl.Room, sd.StartSessions, sd.EndSessions
                                                      FROM db_academic.schedule_details sd
                                                      LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                      WHERE sd.ScheduleID = "' . $d['ScheduleID'] . '"
                                                      ')->result_array();

                if (count($dataSc) > 0) {
                    for ($s = 0; $s < count($dataSc); $s++) {
                        $dCourse = $dataSc[$s];

                        $dataAttd = $this->db->select('Meet1,Meet2,Meet3,Meet4,Meet5,Meet6,Meet7,Meet8,Meet9,Meet10,
                        Meet11,Meet12,Meet13,Meet14,Meet15,Meet16')
                            ->get_where(
                                'db_academic.attendance',
                                array('SemesterID' => $SemesterID, 'ScheduleID' => $d['ScheduleID'], 'SDID' => $dCourse['ID']),
                                1
                            )->result_array();

                        // Get Total Attendance
                        $dataAttdTotal = $this->db->query('SELECT COUNT(*) AS TotalPresent FROM db_academic.attendance_lecturers attdl
                                                                      WHERE attdl.NIP = "' . $d['NIP'] . '" AND attdl.ID_Attd =
                                                                      (SELECT ID FROM db_academic.attendance attd
                                                                      WHERE attd.SemesterID = "' . $SemesterID . '"
                                                                       AND attd.ScheduleID = "' . $d['ScheduleID'] . '"
                                                                        AND attd.SDID = "' . $dCourse['ID'] . '" ) ')->result_array();

                        $dCourse['Attendance'] = $dataAttd;
                        $dCourse['TotalPresent'] = $dataAttdTotal[0]['TotalPresent'];
                        $dataSc[$s] = $dCourse;
                    }
                }


                $d['Schedule'] = $dataSc;


                $data[$i] = $d;
            }
        }

        return $data;
    }

    public function getExchangeBySmtID($SemesterID, $Status, $Start, $End)
    {

        $st = ($Status != '') ? ' AND ex.Status = "' . $Status . '" ' : '';

        $range = ($Start != '' && $End != '') ? ' AND ex.Date >= "' . $Start . '" AND ex.Date <= "' . $End . '" ' : '';

        $data = $this->db->query('SELECT attd.ScheduleID, em.Name AS Lecturer, s.ClassGroup, ex.DateOriginal AS A_Date, ex.Meeting AS A_Sesi, sd.StartSessions AS A_StartSessions,
                                            sd.EndSessions AS A_EndSessions, cl1.Room AS A_Room,
                                            ex.Date AS T_Date, ex.StartSessions AS T_StartSessions, ex.EndSessions AS T_EndSessions,
                                            cl2.Room AS T_Room,ex.Reason, ex.Status
                                            FROM db_academic.schedule_exchange ex
                                            LEFT JOIN db_academic.classroom cl2 ON (cl2.ID = ex.ClassroomID)
                                            LEFT JOIN db_academic.attendance attd ON (attd.ID = ex.ID_Attd)
                                            LEFT JOIN db_academic.schedule s ON (s.ID = attd.ScheduleID)
                                            LEFT JOIN db_employees.employees em ON (em.NIP = ex.NIP)
                                            LEFT JOIN db_academic.schedule_details sd ON (sd.ID = attd.SDID)
                                            LEFT JOIN db_academic.classroom cl1 ON (cl1.ID = sd.ClassroomID)
                                            WHERE attd.SemesterID = "' . $SemesterID . '" ' . $st . ' ' . $range . '
                                            ORDER BY ex.Date, ex.DateOriginal ,ex.DayID ASC')->result_array();

        // Get Course
        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $dataC = $this->db->query('SELECT mk.NameEng AS Course FROM db_academic.schedule_details_course sdc
                                                    LEFT JOIN db_academic.mata_kuliah mk
                                                    ON (mk.ID = sdc.MKID)
                                                    WHERE sdc.ScheduleID = "' . $data[$i]['ScheduleID'] . '" LIMIT 1')->result_array();

                $data[$i]['Course'] = $dataC[0]['Course'];
            }
        }

        return $data;
    }

    public function getStudentsAttendance($SemesterID, $ScheduleID)
    {


        $dataCourse = $this->db->query('SELECT mk.NameEng, mk.MKCode, sdc.MKID, s.ClassGroup, smt.Name AS Semester,
                                                  em.Name AS Lecturer
                                                  FROM db_academic.schedule_details_course sdc
                                                 LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                 LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                                 LEFT JOIN db_academic.semester smt ON (smt.ID = s.SemesterID)
                                                 LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                 WHERE sdc.ScheduleID = "' . $ScheduleID . '" AND s.SemesterID = "' . $SemesterID . '" LIMIT 1')->result_array();

        $dataStd = $this->getStudentByScheduleID($SemesterID, $ScheduleID);

        $resStd = [];

        if (count($dataStd) > 0) {
            for ($i = 0; $i < count($dataStd); $i++) {
                $d = $dataStd[$i];
                $dt = $this->db->query('SELECT attds.M1,attds.M2,attds.M3,attds.M4,attds.M5,attds.M6,attds.M7,attds.M8,attds.M9,
                                                attds.M10, attds.M11 ,attds.M12,attds.M13,attds.M14,attds.M15,attds.M16, d.NameEng AS DayEng
                                                FROM db_academic.attendance_students attds
                                                LEFT JOIN db_academic.attendance attd ON (attd.ID = attds.ID_Attd)
                                                LEFT JOIN db_academic.schedule_details sd ON (sd.ID = attd.SDID)
                                                LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                WHERE attds.NPM = "' . $d['NPM'] . '" AND attd.SemesterID = "' . $SemesterID . '"
                                                AND attd.ScheduleID = "' . $ScheduleID . '" ORDER BY sd.DayID ASC ')->result_array();

                if (count($dt) > 0) {
                    $d['Attendance'] = $dt;
                    $dataStd[$i] = $d;
                    array_push($resStd, $dataStd[$i]);
                }
            }
        }

        $res = array(
            'Course' => $dataCourse,
            'Student' => $resStd
        );

        return $res;
    }

    public function showLecturerMonitoring($SemesterID, $StatusEmployeeID, $StatusLecturerID, $Start, $End)
    {

        $dataLecturer = $this->db->query('SELECT NIP, Name FROM db_employees.employees em
                                                  WHERE em.StatusEmployeeID = "' . $StatusEmployeeID . '" AND em.StatusLecturerID = "' . $StatusLecturerID . '"
                                                  ORDER BY em.NIP ASC ')->result_array();

        $res = [];

        if (count($dataLecturer) > 0) {
            for ($i = 0; $i < count($dataLecturer); $i++) {
                $d = $dataLecturer[$i];
                $dataSC = $this->db->query('SELECT sd.ID AS SDID, s.ID AS ScheduleID, s.ClassGroup FROM db_academic.schedule_details sd
                                                          LEFT JOIN db_academic.schedule s ON (s.ID = sd.ScheduleID)
                                                          WHERE
                                                          s.SemesterID = "' . $SemesterID . '" AND
                                                          s.Coordinator = "' . $d['NIP'] . '"
                                                          ORDER BY s.ClassGroup, sd.ID ASC
                                                          ')->result_array();

                $dataSCTeam = $this->db->query('SELECT sd.ID AS SDID,stt.ScheduleID, s.ClassGroup FROM db_academic.schedule_details sd
                                                        LEFT JOIN db_academic.schedule s ON (s.ID = sd.ScheduleID)
                                                        LEFT JOIN db_academic.schedule_team_teaching stt ON (s.ID = stt.ScheduleID)
                                                        WHERE s.SemesterID = "' . $SemesterID . '" AND stt.NIP = "' . $d['NIP'] . '"
                                                         ORDER BY s.ClassGroup, sd.ID ASC ')->result_array();


                if (count($dataSCTeam) > 0) {
                    for ($s = 0; $s < count($dataSCTeam); $s++) {
                        array_push($dataSC, $dataSCTeam[$s]);
                    }
                }

                // Mengambil dosen yang mempunyai jadwal saja
                if (count($dataSC) > 0) {
                    for ($r = 0; $r < count($dataSC); $r++) {
                        $dc = $dataSC[$r];

                        $dataAttd = $this->db->query('SELECT attd.ID FROM db_academic.attendance attd
                                                                  WHERE attd.SemesterID = "' . $SemesterID . '"
                                                                   AND attd.ScheduleID = "' . $dc['ScheduleID'] . '"
                                                                    AND attd.SDID = "' . $dc['SDID'] . '"')
                            ->result_array();

                        $attd_s = [];
                        if (count($dataAttd) > 0) {
                            // Get Attendance
                            for ($a = 0; $a < count($dataAttd); $a++) {
                                $dataAtLec = $this->db->query('SELECT attd_l.Date FROM db_academic.attendance_lecturers attd_l
                                                                    WHERE
                                                                    attd_l.ID_Attd = "' . $dataAttd[$a]['ID'] . '"
                                                                    AND attd_l.Date >= "' . $Start . '"
                                                                    AND attd_l.Date <= "' . $End . '"
                                                                    AND attd_l.NIP = "' . $d['NIP'] . '"
                                                                      ')->result_array();
                                if (count($dataAtLec) > 0) {
                                    for ($t = 0; $t < count($dataAtLec); $t++) {
                                        array_push($attd_s, $dataAtLec[$t]['Date']);
                                    }
                                }
                            }
                        }


                        $c = $this->db->query('SELECT mk.NameEng AS MKNameEng, sd.Credit FROM db_academic.schedule_details_course sdc
                                                        LEFT JOIN db_academic.schedule_details sd ON (sd.ID = "' . $dc['SDID'] . '")
                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        LEFT JOIN db_academic.curriculum_details cd ON (sdc.CDID = cd.ID)
                                                        WHERE sdc.ScheduleID = "' . $dc['ScheduleID'] . '" LIMIT 1')->result_array();

                        $dataSC[$r]['NameEng'] = $c[0]['MKNameEng'];
                        $dataSC[$r]['Credit'] = $c[0]['Credit'];
                        $dataSC[$r]['Attendance'] = $attd_s;
                    }

                    $dataLecturer[$i]['Course'] = $dataSC;
                    array_push($res, $dataLecturer[$i]);
                }
            }
        }

        return $res;
    }

    public function getInvigilatorSch($SemesterID, $Type, $NIP, $dateTimeNow)
    {
        $data = $this->db->query('SELECT ex.ID, ex.ExamDate, ex.ExamStart, ex.ExamEnd, cl.Room,
                                          mk.NameEng AS CourseEng, mk.MKCode, s.ClassGroup,
                                          ex.OnlineLearning
                                          FROM db_academic.exam ex
                                          LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                          LEFT JOIN db_academic.exam_details exd ON (exd.ExamID = ex.ID)
                                          LEFT JOIN db_academic.schedule s ON (s.ID = exd.ScheduleID)
                                          LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                          LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                          WHERE ex.SemesterID = "' . $SemesterID . '" AND
                                          ex.Type = "' . $Type . '" AND
                                          ( ex.Pengawas1 = "' . $NIP . '"
                                           OR ex.Pengawas2 = "' . $NIP . '" )
                                           GROUP BY ex.ID
                                           ORDER BY ex.ExamDate, ex.ExamStart ASC')
            ->result_array();

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {

                $d = $data[$i];

                $start = $d['ExamDate'] . ' ' . $d['ExamStart'];
                $end = $d['ExamDate'] . ' ' . $d['ExamEnd'];

                $n = strtotime($dateTimeNow);

                if (strtotime($start) <= $n && strtotime($end) >= $n) {
                    $data[$i]['ButtonAttendance'] = '1';
                } else {
                    $data[$i]['ButtonAttendance'] = '0';
                }

                $data[$i]['dateTimeNow'] = $dateTimeNow;
            }
        }



        return $data;
    }

    public function getInvigilatorSchAntara($SemesterID, $Type, $NIP, $dateTimeNow)
    {

        $data = $this->db->query('SELECT ex.ID, ex.ExamDate, ex.Start AS ExamStart, ex.END AS ExamEnd, cl.Room,
                                            mk.NameEng AS CourseEng, mk.MKCode, s.ClassGroup
                                            FROM db_academic.sa_exam ex
                                            LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ClassroomID)
                                            LEFT JOIN db_academic.sa_exam_course exc ON (exc.ExamIDSA = ex.ID)
                                            LEFT JOIN db_academic.sa_schedule s ON (s.ID = exc.ScheduleIDSA)
                                            LEFT JOIN db_academic.sa_schedule_course sdc ON (sdc.ScheduleIDSA = s.ID)
                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                            WHERE ex.SASemesterID = "' . $SemesterID . '"
                                            AND ex.Type = "' . $Type . '"
                                            AND
                                            ( ex.Invigilator1 = "' . $NIP . '"
                                                OR ex.Invigilator2 = "' . $NIP . '" )
                                           GROUP BY ex.ID ORDER BY ex.ExamDate, ex.Start ASC')->result_array();

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {

                $d = $data[$i];

                $start = $d['ExamDate'] . ' ' . $d['ExamStart'];
                $end = $d['ExamDate'] . ' ' . $d['ExamEnd'];

                $n = strtotime($dateTimeNow);

                if (strtotime($start) <= $n && strtotime($end) >= $n) {
                    $data[$i]['ButtonAttendance'] = '1';
                } else {
                    $data[$i]['ButtonAttendance'] = '0';
                }
            }
        }

        return $data;
    }


    public function getExamStudent($ExamID)
    {
        $dataExamDetail = $this->db->query('SELECT exd.*,ex.SemesterID, ex.Type, aut.Name, aut.Year FROM db_academic.exam_details exd
                                                      LEFT JOIN db_academic.exam ex ON (ex.ID = exd.ExamID)
                                                      LEFT JOIN db_academic.auth_students aut ON (aut.NPM = exd.NPM)
                                                      WHERE exd.ExamID = "' . $ExamID . '" ORDER BY aut.NPM ASC')->result_array();


        // Cek apakah exam menggunakan quiz
        $checkQuiz = $this->db->get_where('db_academic.q_exam', array('ExamID' => $ExamID))->result_array();


        if (count($dataExamDetail) > 0) {
            for ($i = 0; $i < count($dataExamDetail); $i++) {

                // Cek Semester
                $dataSemester = $this->m_rest->checkSemesterByClassOf($dataExamDetail[$i]['Year'], $dataExamDetail[$i]['SemesterID']);

                if ($dataSemester == 1 || $dataSemester == '1') {
                    $dataPayment = array(
                        'BPP' => array(
                            'Message' => 'BPP payment Paid',
                            'Status' => 1
                        ),
                        'Credit' => array(
                            'Message' => 'Credit payment unset, please contact academic service',
                            'Status' => 1
                        )
                    );
                } else {
                    $dataPayment = $this->m_rest->checkPayment($dataExamDetail[$i]['NPM'], $dataExamDetail[$i]['SemesterID']);
                }

                $dataAttendance = $this->m_rest->getAttendanceStudent($dataExamDetail[$i]['NPM'], $dataExamDetail[$i]['ScheduleID']);

                $dataExamDetail[$i]['DetailPayment'] = $dataPayment;
                $dataExamDetail[$i]['DetailAttendance'] = $dataAttendance;


                if (count($checkQuiz) <= 0) {


                    // Detail Exam Online
                    $dataExamDetail[$i]['DetailExam'] = $this->db->get_where('db_academic.exam_student_online', array(
                        'ExamID' => $ExamID,
                        'NPM' => $dataExamDetail[$i]['NPM']
                    ))->result_array();
                } else {

                    $dataExamDetail[$i]['DetailExam'] = $this->db->get_where('db_academic.q_quiz_students', array(
                        'QuizID' => $checkQuiz[0]['QuizID'],
                        'NPM' => $dataExamDetail[$i]['NPM']
                    ))->result_array();
                }

                $dataExamDetail[$i]['ExamWhitQuiz'] = (count($checkQuiz) <= 0) ? 0 : 1;
            }
        }

        return $dataExamDetail;
    }

    public function getDataAuthStudent($NPM)
    {

        $data = $this->db->query('SELECT auts.NPM,auts.Name,auts.ProgramID,auts.ProdiID,auts.Year, ps.NameEng,
                                              em.Name AS MentorName, em.NIP AS MentorNIP, auts.ProdiGroupID
                                              FROM db_academic.auth_students auts
                                              LEFT JOIN db_academic.program_study ps ON (ps.ID = auts.ProdiID)
                                              LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = auts.NPM)
                                              LEFT JOIN db_employees.employees em ON (em.NIP = ma.NIP)
                                              WHERE auts.NPM = "' . $NPM . '" LIMIT 1 ')->result_array();

        if (count($data) > 0) {
            $DBStudent = 'ta_' . $data[0]['Year'];
            $dataStd = $this->db->select('ID,Photo')->get_where(
                $DBStudent . '.students',
                array('NPM' => $data[0]['NPM']),
                1
            )->result_array();
            $data[0]['MhswID'] = $dataStd[0]['ID'];
            $data[0]['Photo'] = $dataStd[0]['Photo'];

            $path = 'uploads/students/' . $DBStudent . '/' . $dataStd[0]['Photo'];
            $Photo = base_url('images/icon/student.png');
            if (file_exists($path) && $dataStd[0]['Photo'] != '') {
                $Photo = base_url('uploads/students/' . $DBStudent . '/' . $dataStd[0]['Photo']);
            }
            $data[0]['URLPhoto'] = $Photo;
        }

        return $data;
    }

    public function __getAvailabelCourse($SemesterID, $NPM, $ProgramCampusID, $ProdiID, $Semester, $IsSemesterAntara, $ClassOf)
    {
        ini_set('max_execution_time', 300);

        $student_DB = 'ta_' . $ClassOf;

        $data = $this->db->query('SELECT co.Arr_CDID FROM db_academic.course_offerings co
                                            LEFT JOIN db_academic.curriculum c ON (c.ID = co.CurriculumID)
                                            WHERE co.SemesterID = "' . $SemesterID . '"
                                            AND co.ProgramsCampusID = "' . $ProgramCampusID . '"
                                             AND co.ProdiID = "' . $ProdiID . '"
                                              AND co.Semester = "' . $Semester . '"
                                               AND co.IsSemesterAntara = "' . $IsSemesterAntara . '"
                                                AND c.Year = "' . $ClassOf . '" LIMIT 1 ')->result_array();

        $result = [];
        if (count($data) > 0) {
            $dataCDID = json_decode($data[0]['Arr_CDID']);
            $result = $data[0];
            $arrSchedule = [];
            for ($i = 0; $i < count($dataCDID); $i++) {
                $dataSch = $this->getDetailScheduleByCDID($SemesterID, $NPM, $student_DB, $dataCDID[$i]);
                if (count($dataSch) > 0) {
                    for ($s = 0; $s < count($dataSch); $s++) {
                        array_push($arrSchedule, $dataSch[$s]);
                    }
                }
            }

            usort($arrSchedule, array($this, "cmp"));
            $result['Schedule'] = $arrSchedule;

            // DrafKRS
            $DetailDrafKRS = $this->db->query('SELECT sk.ID AS SKID, sk.ScheduleID, sk.CDID, sk.Status, sk.TypeSP, cd.TotalSKS AS Credit,
                                                          skc.ID AS ReasonID, skc.Reason
                                                          FROM db_academic.std_krs sk
                                                          LEFT JOIN db_academic.curriculum_details cd
                                                          ON (cd.ID = sk.CDID)
                                                          LEFT JOIN db_academic.std_krs_comment skc ON (skc.KRSID = sk.ID)
                                                          WHERE sk.SemesterID = "' . $SemesterID . '"
                                                          AND sk.NPM = "' . $NPM . '" ')->result_array();
            $result['ScheduleDraf'] = $DetailDrafKRS;

            $checkBatalTambah = $this->db->select('ID')->get_where('db_academic.std_krs_batal_tambah', array('SemesterID' => $SemesterID, 'NPM' => $NPM))->result_array();



            if (count($checkBatalTambah) <= 0 && count($DetailDrafKRS) > 0) {
                for ($i = 0; $i < count($DetailDrafKRS); $i++) {

                    $dataIns = $this->db->get_where('db_academic.std_krs', array('ID' => $DetailDrafKRS[$i]['SKID']))->result_array();

                    unset($dataIns[0]['ID']);

                    $this->db->insert('db_academic.std_krs_batal_tambah', $dataIns[0]);
                }
            }

            $DetailBatalTambah = $this->db->query('SELECT sk.ID AS SKID, sk.ScheduleID, sk.CDID, sk.Status, sk.TypeSP, cd.TotalSKS AS Credit,
                                                          skc.ID AS ReasonID, skc.Reason
                                                          FROM db_academic.std_krs_batal_tambah sk
                                                          LEFT JOIN db_academic.curriculum_details cd
                                                          ON (cd.ID = sk.CDID)
                                                          LEFT JOIN db_academic.std_krs_comment skc ON (skc.KRSID = sk.ID)
                                                          WHERE sk.SemesterID = "' . $SemesterID . '"
                                                          AND sk.NPM = "' . $NPM . '" ');

            $result['ScheduleBatalTambah'] = $DetailBatalTambah->result_array();
        }

        return $result;
    }

    private function getDetailScheduleByCDID($SemesterID, $NPM, $student_DB, $CDID)
    {

        // Cek group student
        $dataGroupID =
            $this->db->select('ProdiGroupID')
                ->get_where('db_academic.auth_students', array('NPM' => $NPM))->result_array()[0]['ProdiGroupID'];

        $whereProdiGroup = ($dataGroupID != '' && $dataGroupID != null) ? ' AND sdc.ProdiGroupID = "' . $dataGroupID . '" ' : '';

        $data = $this->db->query('SELECT s.ID, s.SemesterID, s.TeamTeaching, s.ClassGroup,
                                            cd.ID AS CDID, cd.MKType, cd.Semester ,cd.TotalSKS AS Credit, cd.StatusPrecondition, cd.DataPrecondition,
                                            mk.ID AS MKID, mk.MKCode, mk.Name AS MKName, mk.NameEng AS MKNameEng,
                                            sdc.ProdiGroupID, pg.Code AS ProdiGroup
                                            FROM db_academic.schedule_details_course sdc
                                            LEFT JOIN db_academic.prodi_group pg ON (pg.ID = sdc.ProdiGroupID)
                                            LEFT JOIN db_academic.schedule s ON (s.ID = sdc.ScheduleID)
                                            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                            WHERE sdc.CDID = "' . $CDID . '"
                                            AND s.SemesterID = "' . $SemesterID . '" ' . $whereProdiGroup)->result_array();

        $result = [];
        if (count($data) > 0) {

            for ($s = 0; $s < count($data); $s++) {
                $d = $data[$s];
                if ($d['StatusPrecondition'] == 1) {
                    $dataPre = json_decode($d['DataPrecondition']);

                    $pre_arr = [];
                    $cek_arr = [];
                    for ($i = 0; $i < count($dataPre); $i++) {
                        $exp = explode('.', $dataPre[$i]);
                        $pre = $this->db->query('SELECT ID,MKcode,Name,NameEng FROM db_academic.mata_kuliah
                                            WHERE ID="' . $exp[0] . '"')->result_array();

                        // Cek apakah prasyarat sudah diambil atau belum
                        $cekPre = $this->db
                            ->get_where($student_DB . '.study_planning', array('NPM' => $NPM, 'MKID' => $exp[0]), 1)
                            ->result_array();

                        if (count($cekPre) > 0) {
                            array_push($cek_arr, 1);
                        } else {
                            array_push($cek_arr, 0);
                        }

                        array_push($pre_arr, $pre[0]);
                    }

                    $d['DetailPrecondition'] = $pre_arr;
                    if (in_array(0, $cek_arr)) {
                        $d['AllowPrecondition'] = 0;
                    } else {
                        $d['AllowPrecondition'] = 1;
                    }
                }

                $dataMK = $this->db
                    ->get_where($student_DB . '.study_planning', array('NPM' => $NPM, 'MKID' => $d['MKID']), 1)
                    ->result_array();

                $d['SPID'] = (count($dataMK) > 0) ? $dataMK[0]['ID'] : null;

                //Status KRS
                $dataStatus = $this->db->get_where('db_academic.std_krs', array('NPM' => $NPM, 'CDID' => $CDID), 1)->result_array();
                $d['DetailKRS'] = (count($dataStatus) > 0) ? $dataStatus[0] : $dataStatus;

                // Get Sesi
                $dataSesi = $this->db->query('SELECT d.Name AS DayName,d.NameEng AS DayNameEng, cl.Room, cl.Seat,
                                                  sd.StartSessions, sd.EndSessions, sd.ClassroomID, sd.DayID
                                                    FROM db_academic.schedule_details sd
                                                    LEFT JOIN db_academic.days d ON (d.ID=sd.DayID)
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID=sd.ClassroomID)
                                                    WHERE sd.ScheduleID = "' . $d['ID'] . '" ')->result_array();

                for ($s2 = 0; $s2 < count($dataSesi); $s2++) {

                    $querySeat = $this->db->get_where('db_academic.std_krs', array(
                        'SemesterID' => $d['SemesterID'],
                        'ScheduleID' => $d['ID']
                    ))->result_array();
                    $dataSesi[$s2]['CountSeat'] = count($querySeat);
                }

                $d['ScheduleDetails'] = $dataSesi;


                if ($d['TeamTeaching'] == '1') {
                    $dataTT = $this->db->query('SELECT * FROM db_academic.schedule_team_teaching stt WHERE stt.ScheduleID = "' . $d['ID'] . '" ')->result_array();
                    $d['TeamTeachingDetails'] = $dataTT;
                }

                array_push($result, $d);
            }
        }

        return $result;
    }

    public function getSemesterStudentByYear($SemesterID, $Year)
    {
        $data = $this->db->get_where(
            'db_academic.semester',
            array('Year >=' => $Year, 'ID <=' => $SemesterID)
        )->result_array();

        return count($data);
    }

    public function cmp($a, $b)
    {
        return strcmp($a['Semester'], $b['Semester']);
    }

    public function __get_mahasiswa_asing()
    {
        $rs = array();
        // get all prodi
        $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study', 'Status', 1);

        // get year
        $year = date('Y'); // 2019
        // $arr_year = array();
        //     for ($i=0; $i < 4; $i++) { 
        //         $arr_year[] = $year - $i;
        // }

        $Years3 = $year - 3;
        for ($i = $Years3; $i <= $year; $i++) {
            $arr_year[] = $i;
        }

        for ($i = 0; $i < count($G_prodi); $i++) {
            $ProdiID = $G_prodi[$i]['ID'];
            $temp = array(
                'ProdiID' => $ProdiID,
                'Name' => $G_prodi[$i]['Name'],
                'Detail' => array(),
            );
            for ($j = 0; $j < count($arr_year); $j++) {
                $Year =  $arr_year[$j];
                $dataMhs = $this->db->query('SELECT COUNT(*) AS Total FROM db_agregator.student_selection_foreign
                                     WHERE Year = ' . $Year . ' AND ProdiID = "' . $ProdiID . '" ')->result_array();

                $detail = array(
                    'Year' => $Year,
                    'Count' => $dataMhs[0]['Total'],
                );

                $temp['Detail'][] = $detail;
            }

            $rs[] = $temp;
        }

        return $rs;
    }


    public function __get_kecukupan_dosen()
    {

        // Get Program Studi
        $data = $this->db->select('ID,Code,Name')->get_where('db_academic.program_study', array('Status' => 1))->result_array();

        if (count($data) > 0) {
            $dataLAP = $this->db->order_by('ID', 'DESC')->get_where('db_employees.level_education', array(
                'ID >' => 8
            ))->result_array();
            for ($i = 0; $i < count($data); $i++) {

                for ($j = 0; $j < count($dataLAP); $j++) {

                    $dataDetails = $this->db->query('SELECT em.NIP, em.Name FROM db_employees.employees em WHERE em.ProdiID = "' . $data[$i]['ID'] . '"
                    AND em.LevelEducationID = "' . $dataLAP[$j]['ID'] . '" ')->result_array();

                    $r = array('Level' => $dataLAP[$j]['Level'], 'Details' => $dataDetails);
                    $data[$i]['dataLecturers'][$j] = $r;
                }


                $dataL = $this->db->query('SELECT em.NIP, em.Name FROM db_employees.employees em WHERE em.ProdiID = "' . $data[$i]['ID'] . '"
                    AND em.Profession <> "" ')->result_array();
                $r = array('Level' => '', 'Details' => $dataL);
                $data[$i]['dataLecturers'][2] = $r;
            }
        }

        return $data;
        ///return print_r(json_encode($data));
    }

    public function getPhotoStudent($NPM)
    {

        $data = $this->db->select('Year')->get_where('db_academic.auth_students', array(
            'NPM' => $NPM
        ))->result_array();

        $result = array(
            'Status' => 0,
            'URLImage' => base_url('images/icon/userfalse.png')
        );

        if (count($data) > 0) {
            $d = $data[0];
            $db = 'ta_' . $d['Year'];

            $dataStd = $this->db->select('Photo')->get_where($db . '.students', array(
                'NPM' => $NPM
            ))->result_array()[0];

            $pathPhoto = './uploads/students/' . $db . '/' . $dataStd['Photo'];

            if (file_exists($pathPhoto)) {
                $result = array(
                    'Status' => 1,
                    'URLImage' => base_url('uploads/students/' . $db . '/' . $dataStd['Photo'])
                );
            }
        }

        return $result;
    }

    public function chekCodeReport()
    {

        $rand = $this->genrateCode(false, 9);

        $d = $this->db->get_where('db_ticketing.ss_report', array('ReportNumber' => $rand))->result_array();
        if (count($d) > 0) {
            $this->chekCodeReport();
        } else {
            return $rand;
        }
    }

    public function checkCodeIjazah()
    {
        $rand = $this->genrateCode(false, 9);

        $d = $this->db->get_where('db_academic.ijazah', array('Code' => $rand))->result_array();
        if (count($d) > 0) {
            $this->checkCodeIjazah();
        } else {
            return $rand;
        }
    }

    public function checkCodeSurvey()
    {
        $rand = $this->genrateCode(true, 15);

        $d = $this->db->get_where('db_it.surv_survey', array('Key' => $rand))->result_array();
        if (count($d) > 0) {
            $this->checkCodeSurvey();
        } else {
            return $rand;
        }
    }

    private function genrateCode($casesensitive, $length)
    {

        $k = ($casesensitive) ? 'abcdefghijklmnopqrstuvwxyz' : '';
        $seed = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789' . $k); // and any other characters

        shuffle($seed); // probably optional since array_is randomized; this may be redundant
        $rand = '';
        foreach (array_rand($seed, $length) as $key) $rand .= $seed[$key];

        return $rand;
    }


    /*ADDED BY FEBRI @ JAN 2020*/
    public function getStdInsurance($data)
    {
        $this->db->select("a.*,b.Name as InsuranceName,b.Address as InsuranceAddress");
        $this->db->from("db_academic.std_insurance a");
        $this->db->join("db_employees.master_company b", "b.ID = a.InsuranceID", "left");
        $this->db->where($data);
        $query = $this->db->get();
        return $query;
    }
    /*END ADDED BY FEBRI @ JAN 2020*/
}
