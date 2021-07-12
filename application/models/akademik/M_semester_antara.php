<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_semester_antara extends CI_Model
{


    function __construct()
    {
        parent::__construct();
    }

    public function getRecapAttendance($ScheduleIDSA)
    {

        // Get matakuliah
        $data['dataCourse'] = $this->db->query('SELECT mk.NameEng AS CourseEng, mk.MKCode FROM db_academic.sa_schedule_course ssc 
                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssc.MKID)
                                                WHERE ssc.ScheduleIDSA = "' . $ScheduleIDSA . '" LIMIT 1')->result_array();



        $dataLec = $this->db->query('SELECT sat.Meet, sat.Status, sat.EntredAt, em.Name FROM db_academic.sa_attendance sat 
                                                LEFT JOIN db_employees.employees em ON (em.NIP = sat.UserID)
                                                WHERE sat.ScheduleIDSA = "' . $ScheduleIDSA . '" AND sat.Type = "lec" ')
            ->result_array();

        $arrAttdLec = [];
        for ($i = 1; $i <= 14; $i++) {
            $arrMeet = [];
            if (count($dataLec) > 0) {
                foreach ($dataLec as $item) {
                    if ($i == $item['Meet']) {
                        $arr = array(
                            'Name' => $item['Name'],
                            'EntredAt' => $item['EntredAt']
                        );

                        array_push($arrMeet, $arr);
                    }
                }
            }

            array_push($arrAttdLec, $arrMeet);
        }



        $data['dataLec']  = $arrAttdLec;




        // Cek Student
        $Student = [];
        $dataCD = $this->db->query('SELECT * FROM db_academic.sa_schedule_course ssc 
                                                                                WHERE ssc.ScheduleIDSA = "' . $ScheduleIDSA . '" ')->result_array();

        if (count($dataCD) > 0) {
            foreach ($dataCD as $item) {
                // Student
                // $dataStd = $this->db->query('SELECT ssd.NPM, ats.Name, p1.Status AS StatusBPP, p2.Status AS StatusCredit FROM db_academic.sa_student_details ssd
                //                                               LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssd.NPM)
                //                                               LEFT JOIN db_finance.payment p1 ON (p1.NPM = ssd.NPM AND p1.PTID = "5")
                //                                               LEFT JOIN db_finance.payment p2 ON (p2.NPM = ssd.NPM AND p2.PTID = "6")
                //                                               WHERE ssd.CDID = "'.$item['CDID'].'" AND ssd.Status = "3" AND p1.Status = "1" AND p2.Status = "1"
                //                                               ORDER BY ssd.NPM ASC')->result_array();

                $dataStd = $this->db->query('SELECT ssd.NPM, ats.Name, p1.Status AS StatusBPP, p2.Status AS StatusCredit FROM db_academic.sa_student_details ssd
                                                              LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssd.NPM)
                                                              LEFT JOIN  db_academic.sa_student ss ON (ss.ID = ssd.IDSAStudent)
                                                              LEFT JOIN db_academic.sa_schedule sc ON (sc.SASemesterID = ss.SASemesterID)
                                                              LEFT JOIN db_academic.semester_antara sa ON (sa.ID = ss.SASemesterID)
                                                              LEFT JOIN db_finance.payment p1 ON (p1.NPM = ssd.NPM AND p1.PTID = "5" AND p1.SemesterID = sa.ID)
                                                              LEFT JOIN db_finance.payment p2 ON (p2.NPM = ssd.NPM AND p2.PTID = "6" AND p2.SemesterID = sa.ID)
                                                              WHERE  sc.ID = "' . $ScheduleIDSA . '" AND ssd.CDID = "' . $item['CDID'] . '" AND ssd.Status = "3" AND p1.Status = "1" AND p2.Status = "1"
                                                              ORDER BY ssd.NPM ASC')->result_array();
                if (count($dataStd) > 0) {
                    for ($s = 0; $s < count($dataStd); $s++) {
                        $itm = $dataStd[$s];

                        $arrDataAttd = [];

                        for ($a = 1; $a <= 14; $a++) {

                            $sts = $this->db->query('SELECT sat.Status FROM db_academic.sa_attendance sat 
                                                LEFT JOIN db_academic.auth_students ast ON (ast.NPM = sat.UserID)
                                                WHERE sat.ScheduleIDSA = "' . $ScheduleIDSA . '" AND sat.UserID = "' . $itm['NPM'] . '" 
                                                AND sat.Meet = "' . $a . '" AND sat.Type = "std" ')->result_array();

                            $status = 0;
                            if (count($sts) > 0) {
                                $status = $sts[0]['Status'];
                            }

                            array_push($arrDataAttd, $status);
                        }



                        $dataStd[$s]['DataAttd'] = $arrDataAttd;

                        array_push($Student, $dataStd[$s]);
                    }
                }
            }
        }

        usort($Student, function ($a, $b) {
            return strcmp($a['NPM'], $b['NPM']);
        });

        $dataSchedule[$i]['Students'] = $Student;

        $data['dataStd'] = $Student;

        return $data;
    }
}
