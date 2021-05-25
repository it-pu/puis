<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_rest extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Jakarta");
    }


    public function getDateTimeNow()
    {
        $date = date('Y-m-d H:i:s');
        return $date;
    }

    public function getDateNow()
    {
        $date = date('Y-m-d');
        return $date;
    }

    public function getTimeNow()
    {
        $date = date('H:i:s');
        return $date;
    }

    public function checkImageSummernote($act, $SummernoteID, $table, $column)
    {
        $dataSummernoteImg = $this->db->get_where(
            'db_it.summernote_image',
            array('SummernoteID' => $SummernoteID)
        )->result_array();

        if (count($dataSummernoteImg) > 0) {
            for ($s = 0; $s < count($dataSummernoteImg); $s++) {

                if ($act == 'insert') {
                    $dataCk = $this->db
                        ->query('SELECT COUNT(*) AS Total FROM ' . $table . '
                            WHERE ' . $column . ' LIKE "%' . $dataSummernoteImg[$s]['Image'] . '%" ')
                        ->result_array();

                    if ($dataCk[0]['Total'] <= 0) {

                        if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                            $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                            $path = ($_SERVER['SERVER_NAME'] == 'localhost')
                                ? 'localhost/summernote/images' : 'pcam/summernote/images';
                            $this->m_master->DeleteFileToNas($headerOrigin, $path . '/' . $dataSummernoteImg[$s]['Image']);
                        } else {
                            $file_path = './uploads/summernote/images/' . $dataSummernoteImg[$s]['Image'];
                            if (file_exists($file_path)) {
                                unlink($file_path);
                            }
                        }

                        $this->db->where('Image', $dataSummernoteImg[$s]['Image']);
                        $this->db->delete('db_it.summernote_image');
                    } else {
                        $this->db->where('Image', $dataSummernoteImg[$s]['Image']);
                        $this->db->update('db_it.summernote_image', array('Status' => '1'));
                    }
                } else if ($act == 'delete') {

                    if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {

                        $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                        $path = ($_SERVER['SERVER_NAME'] == 'localhost')
                            ? 'localhost/summernote/images' : 'pcam/summernote/images';
                        $this->m_master->DeleteFileToNas($headerOrigin, $path . '/' . $dataSummernoteImg[$s]['Image']);
                    } else {
                        $file_path = './uploads/summernote/images/' . $dataSummernoteImg[$s]['Image'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }

                    $this->db->where('Image', $dataSummernoteImg[$s]['Image']);
                    $this->db->delete('db_it.summernote_image');
                }
            }
        }
    }

    public function getCustomeDateTimeNow($custom)
    {
        $date = date('' . $custom);
        return $date;
    }

    public function genrateNumberingString($number, $length)
    {

        $lengthStr = strlen($number);

        if ((int)$length > (int) $lengthStr) {

            $m = ((int)$length - (int) $lengthStr);

            $zero = '';
            for ($i = 1; $i <= $m; $i++) {
                $zero = $zero . '0';
            }


            return $zero . '' . $number;
        } else {
            return $number;
        }
    }


    public function _getSemesterActive()
    {
        $data = $this->db->query('SELECT ay.*
                                            FROM db_academic.semester s
                                            LEFT JOIN db_academic.academic_years ay
                                            ON (s.ID = ay.SemesterID)
                                            WHERE s.Status = "1" LIMIT 1 ');
        //        $data = $this->db->get_where('db_academic.semester', array('Status'=>'1'),1);
        return $data->result_array()[0];
    }

    public function _getSemesterAntaraActive()
    {

        $data = $this->db->query('SELECT sa.*, say.MaxCredit, say.Start, say.End, say.StartUTS, say.EndUTS, say.StartKRS, say.EndKRS, say.StartUAS, say.EndUAS
                                              FROM db_academic.semester_antara sa
                                              LEFT JOIN db_academic.sa_academic_years say ON (say.SASemesterID = sa.ID)
                                              WHERE sa.Status = "1" ')->result_array();

        //        $data = $this->db->limit(1)->get_where('db_academic.semester_antara',array('Status' => '1'))->result_array();

        return $data;
    }

    public function __getKSM($db, $ProdiID, $NPM, $ClassOf)
    {
        $dataSemester = $this->db->query('SELECT s.* FROM db_academic.semester s WHERE s.Year >= ' . $ClassOf . ' ORDER BY s.ID ASC')->result_array();

        //        print_r($dataSemester);

        $result = [];
        $smt = 1;
        for ($i = 0; $i < count($dataSemester); $i++) {

            if ($dataSemester[$i]['ID'] < 13) {
                $dataSchedule = $this->db->query('SELECT zc.*,sp.TypeSchedule, mk.MKCode, mk.Name AS MKName, mk.NameEng AS MKNameEng,
                                                            cd.TotalSKS AS Credit, em.Name AS Lecturer, sp.TransferCourse
                                                            FROM ' . $db . '.study_planning sp
                                                            LEFT JOIN db_academic.z_schedule zc ON (zc.Glue = sp.Glue)
                                                            LEFT JOIN db_employees.employees em ON (em.NIP = zc.NIP)
                                                            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sp.CDID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sp.MKID)
                                                            WHERE sp.NPM = "' . $NPM . '"
                                                            AND sp.SemesterID = "' . $dataSemester[$i]['ID'] . '"
                                                            GROUP BY mk.MKCode
                                                            ORDER BY mk.MKCode ASC
                                                            ')->result_array();

                if (count($dataSchedule) > 0) {

                    for ($s = 0; $s < count($dataSchedule); $s++) {
                        $dataDateTime = [];
                        if ($dataSchedule[$s]['IsTeamTeaching'] == '1') {
                            $dataTc = $this->db->query('SELECT ztt.*,em.Name AS Lecturer, em.TitleAhead, em.TitleBehind FROM db_academic.z_team_teaching ztt
                                                            LEFT JOIN db_employees.employees em ON (ztt.NIP = em.NIP)
                                                            WHERE ztt.Glue = "' . $dataSchedule[$s]['Glue'] . '" ')->result_array();


                            $dataSchedule[$s]['TeamTeaching'] = $dataTc;

                            $dataDateTime = $this->db->query('SELECT zc.Day, zc.Start, zc.End, zc.Classroom FROM db_academic.z_schedule zc
                                                                  WHERE
                                                                  zc.SemesterID = "' . $dataSemester[$i]['ID'] . '"
                                                                  AND zc.ProdiID = "' . $ProdiID . '"
                                                                  AND zc.Glue = "' . $dataSchedule[$s]['Glue'] . '" ')->result_array();
                        }
                        $dataSchedule[$s]['DetailDateSchedule'] = $dataDateTime;
                    }
                } else {
                    $dataSchedule = [];
                }

                $dataArr = array(
                    'SemesterID' => $dataSemester[$i]['ID'],
                    'Semester' => $smt,
                    'SemesterName' => $dataSemester[$i]['Name'],
                    'StatusSystem' => '0',
                    'Schedule' => $dataSchedule
                );
                array_push($result, $dataArr);
                $smt += 1;
            } else {

                $data = $this->db->query('SELECT sp.ScheduleID,sp.TypeSchedule,mk.MKCode,mk.Name AS MKName,mk.nameEng AS MKNameEng,cd.TotalSKS AS Credit,
                                                sp.StatusSystem,sc.ClassGroup, sc.TeamTeaching,
                                                em.NIP,em.Name,em.TitleAhead, em.TitleBehind, em.EmailPU, sp.TransferCourse, sc.OnlineLearning
                                                FROM ' . $db . '.study_planning sp
                                                LEFT JOIN db_academic.semester s ON (s.ID = sp.SemesterID)
                                                LEFT JOIN db_academic.schedule sc ON (sc.ID = sp.ScheduleID)
                                                LEFT JOIN db_employees.employees em ON (em.NIP = sc.Coordinator)
                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sp.MKID)
                                                LEFT JOIN db_academic.curriculum_details cd ON (sp.CDID=cd.ID)
                                                WHERE sp.NPM = "' . $NPM . '" AND s.ID = "' . $dataSemester[$i]['ID'] . '"
                                                ORDER BY mk.MKCode ASC ')->result_array();

                if (count($data) > 0) {
                    for ($sc = 0; $sc < count($data); $sc++) {
                        $LecturerCoor = $data[$sc]['TitleAhead'] . ' ' . $data[$sc]['Name'] . ' ' . $data[$sc]['TitleBehind'];
                        $data[$sc]['Lecturer'] = trim($LecturerCoor);

                        $dataSchedule = $this->db->query('SELECT sd.ID AS SDID, sd.StartSessions,sd.EndSessions,cl.Room,d.Name AS Day, d.NameEng AS DayEng
                                                                      FROM db_academic.schedule_details sd
                                                                      LEFT JOIN db_academic.classroom cl ON (cl.ID=sd.ClassroomID)
                                                                      LEFT JOIN db_academic.days d ON (d.ID=sd.DayID)
                                                                      WHERE sd.ScheduleID = "' . $data[$sc]['ScheduleID'] . '"
                                                                       ORDER BY d.ID ASC')->result_array();

                        $dataGrade = $this->db->query('SELECT * FROM db_academic.grade_course gc
                                                            WHERE gc.ScheduleID = "' . $data[$sc]['ScheduleID'] . '"
                                                             ')->result_array();

                        $meeting = 0;
                        $Totalpresen = 0;
                        // Get Attendance
                        if (count($dataSchedule) > 0) {

                            for ($sds = 0; $sds < count($dataSchedule); $sds++) {
                                $dataAttd = $this->db->query('SELECT attd_s.* FROM db_academic.attendance_students attd_s
                                                          LEFT JOIN db_academic.attendance attd ON (attd.ID = attd_s.ID_Attd)
                                                          WHERE attd.SemesterID = "' . $dataSemester[$i]['ID'] . '"
                                                          AND attd.ScheduleID = "' . $data[$sc]['ScheduleID'] . '"
                                                          AND attd.ScheduleID = "' . $data[$sc]['ScheduleID'] . '"
                                                          AND attd.SDID = "' . $dataSchedule[$sds]['SDID'] . '"
                                                           AND attd_s.NPM = "' . $NPM . '" ')->result_array();

                                if (count($dataAttd) > 0) {
                                    $presen = 0;
                                    $ArrPresensi = [];
                                    for ($m = 1; $m <= 14; $m++) {
                                        $meeting += 1;
                                        if ($dataAttd[0]['M' . $m] == '1') {
                                            $presen += 1;
                                            $Totalpresen += 1;
                                        }
                                        array_push($ArrPresensi, $dataAttd[0]['M' . $m]);
                                    }


                                    $dataSchedule[$sds]['Presensi'] = $presen;
                                    $dataSchedule[$sds]['AttendanceStudentDetails'] = $ArrPresensi;
                                }
                            }
                        }

                        $data[$sc]['GradeCourse'] = $dataGrade;
                        $data[$sc]['Schedule'] = $dataSchedule;

                        // Menghitung preseni
                        $PresensiArg = ($Totalpresen == 0) ? 0 : ($Totalpresen / $meeting) * 100;
                        $data[$sc]['AttendanceStudent'] = $PresensiArg;

                        $data[$sc]['TeamTeachingDetails'] = [];


                        if ($data[$sc]['TeamTeaching'] == 1) {
                            $dataTT = $this->db->query('SELECT e.NIP,e.Name,e.TitleAhead, e.TitleBehind FROM db_academic.schedule_team_teaching stt
                                                          LEFT JOIN db_employees.employees e ON (e.NIP = stt.NIP) WHERE stt.ScheduleID = "' . $data[$sc]['ScheduleID'] . '" ')->result_array();
                            for ($t = 0; $t < count($dataTT); $t++) {
                                $Lecturer = $dataTT[$t]['TitleAhead'] . ' ' . $dataTT[$t]['Name'] . ' ' . $dataTT[$t]['TitleBehind'];
                                $dataTT[$t]['Lecturer'] = trim($Lecturer);
                            }
                            $data[$sc]['TeamTeachingDetails'] = $dataTT;
                        }
                    }
                }

                $dataArr = array(
                    'SemesterID' => $dataSemester[$i]['ID'],
                    'Semester' => $smt,
                    'SemesterName' => $dataSemester[$i]['Name'],
                    'StatusSystem' => '1',
                    'Schedule' => $data
                );
                array_push($result, $dataArr);
                $smt += 1;
            }
        }

        return $result;
    }

    public function __getExamScheduleForStudent($db, $SemesterID, $NPM, $ClassOf, $ExamType)
    {
        $dataSemester = $this->db->query('SELECT s.*, ay.utsStart, ay.utsEnd, ay.uasStart, ay.uasEnd
                                                        FROM db_academic.semester s
                                                        LEFT JOIN db_academic.academic_years ay ON (ay.SemesterID = s.ID)
                                                        WHERE s.ID = ' . $SemesterID . '
                                                        ORDER BY s.ID ASC')->result_array();

        // Get setting exam
        $dataExamSetting = $this->db->get('db_academic.exam_setting')->result_array();

        $result = [];
        for ($i = 0; $i < count($dataSemester); $i++) {

            $Semester = $this->checkSemesterByClassOf($ClassOf, $SemesterID);

            if ($dataSemester[$i]['ID'] >= 13) {

                $checkBPP = true;
                $checkCredit = true;

                // Cek attendance
                $checkAttendance = false;
                $checkAttendanceValue = 0;

                // Cek apakah sudah masuk pada periode ujian atau belum (UTS / UAS)
                $checkDateExam = true;
                $dateExamStart = ($ExamType == 'uts' || $ExamType == 'UTS')
                    ? $dataSemester[$i]['utsStart']
                    : $dataSemester[$i]['uasStart'];


                if ($ExamType == 'uts' || $ExamType == 'UTS') {
                    // Cek Setting
                    if (count($dataExamSetting) > 0) {
                        $ExamSetting = $dataExamSetting[0];

                        if ($Semester > 1 || $Semester > '1') {
                            // Apakah ada setting pengecekan pembayaran BPP
                            if ($ExamSetting['UTSPaymentBPP'] == '1' || $ExamSetting['UTSPaymentBPP'] == 1) {
                                $datacheckBPP = $this->checkBPPPayment($NPM, $dataSemester[$i]['ID']);
                                if ($datacheckBPP['Status'] == 1) {
                                    $checkBPP = true;
                                } else {
                                    $checkBPP = false;
                                }
                            }

                            // Apakah ada setting pengecekan pembayaran credit
                            if ($ExamSetting['UTSPaymentCredit'] == '1' || $ExamSetting['UTSPaymentCredit'] == 1) {
                                $datacheckCredit = $this->checkCreditPayment($NPM, $dataSemester[$i]['ID']);
                                if ($datacheckCredit['Status'] == 1) {
                                    $checkCredit = true;
                                } else {
                                    $checkCredit = false;
                                }
                            }
                        }


                        if ($ExamSetting['UTSAttd'] == 1 || $ExamSetting['UTSAttd'] == '1') {
                            $checkAttendance = true;
                            $checkAttendanceValue = $ExamSetting['UTSAttdValue'];
                        }
                    }
                } else if ($ExamType == 'uas' || $ExamType == 'UAS') {
                    if (count($dataExamSetting) > 0) {
                        $ExamSetting = $dataExamSetting[0];

                        if ($Semester > 1 || $Semester > '1') {
                            // Apakah ada setting pengecekan pembayaran BPP
                            if ($ExamSetting['UASPaymentBPP'] == '1' || $ExamSetting['UASPaymentBPP'] == 1) {
                                $datacheckBPP = $this->checkBPPPayment($NPM, $dataSemester[$i]['ID']);
                                if ($datacheckBPP['Status'] == 1) {
                                    $checkBPP = true;
                                } else {
                                    $checkBPP = false;
                                }
                            }

                            // Apakah ada setting pengecekan pembayaran credit
                            if ($ExamSetting['UASPaymentCredit'] == '1' || $ExamSetting['UASPaymentCredit'] == 1) {
                                $datacheckCredit = $this->checkCreditPayment($NPM, $dataSemester[$i]['ID']);
                                if ($datacheckCredit['Status'] == 1) {
                                    $checkCredit = true;
                                } else {
                                    $checkCredit = false;
                                }
                            }
                        }

                        if ($ExamSetting['UASAttd'] == 1 || $ExamSetting['UASAttd'] == '1') {
                            $checkAttendance = true;
                            $checkAttendanceValue = $ExamSetting['UASAttdValue'];
                        }
                    }
                }

                if ($ExamType == 'uts' || $ExamType == 'UTS' || $ExamType == 'uas' || $ExamType == 'UAS') {

                    $day = 7;
                    if (count($dataExamSetting) > 0) {
                        $ExamSetting = $dataExamSetting[0];
                        $day = ($ExamType == 'uts' || $ExamType == 'UTS')
                            ? $ExamSetting['UTSShown']
                            : $ExamSetting['UASShown'];
                    }

                    $dateShow = date('Y-m-d', strtotime($this->getDateNow() . '+ ' . $day . ' days'));
                    $AvailableDate = date('Y-m-d', strtotime($dateExamStart . '- ' . $day . ' days'));

                    if ($dateExamStart > $dateShow) {
                        $checkDateExam = false;
                    }
                }



                if ($checkBPP && $checkCredit && $checkDateExam) {
                    $ExamSchedule = $this->getDetailsScheduleExam($db, $NPM, $dataSemester[$i]['ID'], $ExamType);

                    // Status (StatusExam)
                    // 1 = tidak ada masalah
                    // -1 = attendance tidak memenuhi

                    $detailExam = [];

                    if (count($ExamSchedule) > 0) {
                        for ($i = 0; $i < count($ExamSchedule); $i++) {
                            $ExamSchedule[$i]['StatusExam'] = 1;
                            $dc = $ExamSchedule[$i];

                            if ($checkAttendance && ($dc['Attendance'] == 1 || $dc['Attendance'] == '1')) {
                                if ($dc['AttendancePercentage'] < $checkAttendanceValue) {
                                    $ExamSchedule[$i]['ExamDate'] = '';
                                    $ExamSchedule[$i]['ExamStart'] = '';
                                    $ExamSchedule[$i]['ExamEnd'] = '';
                                    $ExamSchedule[$i]['Room'] = '';
                                    $ExamSchedule[$i]['StatusExam'] = -1;
                                }
                            }

                            array_push($detailExam, $ExamSchedule[$i]);
                        }
                    }


                    $result = array(
                        'Status' => 1,
                        'ExamSchedule' => $detailExam
                    );
                } else if ($checkBPP == false) {
                    $result = array(
                        'Status' => -1,
                        'Message' => 'BPP Payment Unpaid'
                    );
                } else if ($checkCredit == false) {
                    $result = array(
                        'Status' => -2,
                        'Message' => 'Credit Payment Unpaid'
                    );
                } else if ($checkDateExam == false) {
                    $result = array(
                        'Status' => -3,
                        'Message' => 'The exam is out of date',
                        'AvailableDate' => $AvailableDate
                    );
                }
            } else {
                $result = array(
                    'Status' => -4,
                    'Message' => 'Schedule Exam Not Available'
                );
            }
        }

        return $result;
    }

    public function getListStudentExam($ExamID, $order = '')
    {

        $orderby = 'ORDER BY exd.NPM ASC';

        if ($order != '') {
            $orderby = 'ORDER BY ' . $order;
        }

        $dataStudents = $this->db->query('SELECT exd.ID AS EXDID, exd.ExamID, exd.ScheduleID, exd.DB_Students, exd.Status, exd.NPM, auts.Name,
                                                    ex.Type AS ExamType, ex.SemesterID, auts.Year, s.Attendance FROM db_academic.exam_details exd
                                                    LEFT JOIN db_academic.exam ex ON (ex.ID = exd.ExamID)
                                                    LEFT JOIN db_academic.auth_students auts ON (exd.NPM = auts.NPM)
                                                    LEFT JOIN db_academic.schedule s ON (s.ID = exd.ScheduleID)
                                                    WHERE exd.ExamID = "' . $ExamID . '" ' . $orderby)->result_array();

        // Get setting exam
        $dataExamSetting = $this->db->get('db_academic.exam_setting')->result_array();


        if (count($dataStudents) > 0) {

            $students = [];

            for ($i = 0; $i < count($dataStudents); $i++) {

                $item = $dataStudents[$i];

                // Get Semester Student


                $checkBPP = true;
                $checkCredit = true;

                // Cek attendance
                $checkAttendance = false;
                $checkAttendanceValue = 0;

                $ExamType = $item['ExamType'];
                $NPM = $item['NPM'];
                $SemesterID = $item['SemesterID'];

                $Semester = $this->checkSemesterStudent($item['Year']);

                if ($ExamType == 'uts' || $ExamType == 'UTS') {
                    // Cek Setting
                    if (count($dataExamSetting) > 0) {
                        $ExamSetting = $dataExamSetting[0];

                        if ($Semester > 1 || $Semester > '1') {
                            // Apakah ada setting pengecekan pembayaran BPP
                            if ($ExamSetting['UTSPaymentBPP'] == '1' || $ExamSetting['UTSPaymentBPP'] == 1) {
                                $datacheckBPP = $this->checkBPPPayment($NPM, $SemesterID);
                                if ($datacheckBPP['Status'] == 1) {
                                    $checkBPP = true;
                                } else {
                                    $checkBPP = false;
                                }
                            }

                            // Apakah ada setting pengecekan pembayaran credit
                            if ($ExamSetting['UTSPaymentCredit'] == '1' || $ExamSetting['UTSPaymentCredit'] == 1) {
                                $datacheckCredit = $this->checkCreditPayment($NPM, $SemesterID);
                                if ($datacheckCredit['Status'] == 1) {
                                    $checkCredit = true;
                                } else {
                                    $checkCredit = false;
                                }
                            }
                        }


                        if ($ExamSetting['UTSAttd'] == 1 || $ExamSetting['UTSAttd'] == '1') {
                            $checkAttendance = true;
                            $checkAttendanceValue = $ExamSetting['UTSAttdValue'];
                        }
                    }
                } else if ($ExamType == 'uas' || $ExamType == 'UAS') {
                    if (count($dataExamSetting) > 0) {
                        $ExamSetting = $dataExamSetting[0];

                        if ($Semester > 1 || $Semester > '1') {
                            // Apakah ada setting pengecekan pembayaran BPP
                            if ($ExamSetting['UASPaymentBPP'] == '1' || $ExamSetting['UASPaymentBPP'] == 1) {
                                $datacheckBPP = $this->checkBPPPayment($NPM, $SemesterID);
                                if ($datacheckBPP['Status'] == 1) {
                                    $checkBPP = true;
                                } else {
                                    $checkBPP = false;
                                }
                            }

                            // Apakah ada setting pengecekan pembayaran credit
                            if ($ExamSetting['UASPaymentCredit'] == '1' || $ExamSetting['UASPaymentCredit'] == 1) {
                                $datacheckCredit = $this->checkCreditPayment($NPM, $SemesterID);
                                if ($datacheckCredit['Status'] == 1) {
                                    $checkCredit = true;
                                } else {
                                    $checkCredit = false;
                                }
                            }
                        }

                        if ($ExamSetting['UASAttd'] == 1 || $ExamSetting['UASAttd'] == '1') {
                            $checkAttendance = true;
                            $checkAttendanceValue = $ExamSetting['UASAttdValue'];
                        }
                    }
                }

                if ($checkBPP && $checkCredit) {

                    if ($checkAttendance && ($item['Attendance'] == 1 || $item['Attendance'] == '1')) {
                        // Cek Attendance
                        $attd = $this->checkPercentageAttendance($NPM, $item['ScheduleID']);
                        if ($attd >= $checkAttendanceValue) {
                            array_push($students, $item);
                        }
                    } else {
                        array_push($students, $item);
                    }
                }
            }

            $result = array(
                'Status' => 1,
                'Message' => 'Student not yet',
                'DetailStudents' => $students
            );
        } else {
            $result = array(
                'Status' => -1,
                'Message' => 'Student not yet'
            );
        }

        return $result;
    }

    public function getListStudentExamAntara($CDID, $order = '')
    {
        $orderby = 'ORDER BY ast.NPM ASC';

        if ($order != '') {
            $orderby = 'ORDER BY ' . $order;
        }

        $resStd = $this->db->query('SELECT ast.NPM, ast.Name, p1.Status AS StatusBPP, p2.Status AS StatusCredit
                                                                    FROM db_academic.sa_student_details ssd
                                                                    LEFT JOIN db_academic.auth_students ast ON (ast.NPM = ssd.NPM)
                                                                    LEFT JOIN db_finance.payment p1 ON (p1.NPM = ssd.NPM AND p1.PTID = "5")
                                                                    LEFT JOIN db_finance.payment p2 ON (p2.NPM = ssd.NPM AND p2.PTID = "6")
                                                                    WHERE ssd.CDID = "' . $CDID . '" AND ssd.Status = "3" ' . $orderby)->result_array();

        $result = [];
        if (count($resStd) > 0) {
            foreach ($resStd as $item) {
                if ($item['StatusBPP'] == '1' && $item['StatusCredit'] == '1') {
                    array_push($result, $item);
                }
            }
        }

        return $result;
    }

    public function getListStudentExamAntara2($ExamIDSA)
    {



        $dataStd = $this->db->query('SELECT exg.ID AS EXDID, exg.ExamIDSA AS ExamID, exg.Status,ast.Name, ast.NPM,
                                                          p1.Status AS StatusBPP, p2.Status AS StatusCredit
                                                          FROM db_academic.sa_exam_student exg
                                                          LEFT JOIN db_academic.auth_students ast ON (ast.NPM = exg.NPM)
                                                          LEFT JOIN db_finance.payment p1 ON (p1.NPM = ast.NPM AND p1.PTID = "5")
                                                          LEFT JOIN db_finance.payment p2 ON (p2.NPM = ast.NPM AND p2.PTID = "6")
                                                          WHERE exg.ExamIDSA = "' . $ExamIDSA . '" ORDER BY ast.NPM ASC
                                          ')->result_array();


        $result = [];
        if (count($dataStd) > 0) {
            foreach ($dataStd as $item) {
                if ($item['StatusBPP'] == '1' && $item['StatusCredit'] == '1') {
                    array_push($result, $item);
                }
            }
        }

        return $result;
    }

    public function checkSemesterStudent($Year)
    {

        $data = $this->db->query('SELECT Status FROM db_academic.semester WHERE Year >= "' . $Year . '" ')->result_array();

        $Semester = 0;
        if (count($data) > 0) {
            foreach ($data as $item) {
                if ($item['Status'] == 0 || $item['Status'] == '0') {
                    $Semester = $Semester + 1;
                } else {
                    $Semester = $Semester + 1;
                    break;
                }
            }
        }

        return $Semester;
    }

    public function checkSemesterByClassOf($ClassOf, $SemesterID)
    {
        $dataSemester = $this->db->query('SELECT s.* FROM db_academic.semester s
                                                        WHERE s.Year >= "' . $ClassOf . '"
                                                        AND s.id <= "' . $SemesterID . '"
                                                        ORDER BY s.ID ASC')->result_array();


        $smt_now = count($dataSemester);

        return $smt_now;
    }

    public function checkPayment($NPM, $SemesterID)
    {
        // BPP
        $dataBpp = $this->db->select('Status')->get_where(
            'db_finance.payment',
            array('NPM' => $NPM, 'PTID' => 2, 'SemesterID' => $SemesterID),
            1
        )->result_array();

        if (count($dataBpp) > 0) {
            if ($dataBpp[0]['Status'] == '1' || $dataBpp[0]['Status'] == 1) {
                $StatusBPP = 1;
                $MessageBPP = 'BPP payment Paid';
            } else {
                $StatusBPP = 0;
                $MessageBPP = 'BPP payment Unpaid';
            }
        } else {
            $StatusBPP = -1;
            $MessageBPP = 'BPP payment unset, please contact academic service';
        }


        // Credit
        $dataSKS = $this->db->select('Status')->get_where(
            'db_finance.payment',
            array('NPM' => $NPM, 'PTID' => 3, 'SemesterID' => $SemesterID),
            1
        )->result_array();
        if (count($dataSKS) > 0) {
            if ($dataSKS[0]['Status'] == '1' || $dataSKS[0]['Status'] == 1) {
                $StatusCredit = 1;
                $MessageCredit = 'Credit payment Paid';
            } else {
                $StatusCredit = 0;
                $MessageCredit = 'Credit payment Unpaid';
            }
        } else {
            $StatusCredit = -1;
            $MessageCredit = 'Credit payment unset, please contact academic service';
        }

        $result = array(
            'BPP' => array('Status' => $StatusBPP, 'Message' => $MessageBPP),
            'Credit' => array('Status' => $StatusCredit, 'Message' => $MessageCredit)
        );

        return $result;
    }

    public function checkBPPPayment($NPM, $SemesterID)
    {
        // BPP
        $dataBpp = $this->db->select('Status')->get_where(
            'db_finance.payment',
            array('NPM' => $NPM, 'PTID' => 2, 'SemesterID' => $SemesterID),
            1
        )->result_array();

        if (count($dataBpp) > 0) {
            if ($dataBpp[0]['Status'] == '1' || $dataBpp[0]['Status'] == 1) {
                $StatusBPP = 1;
                $MessageBPP = 'BPP payment Paid';
            } else {
                $StatusBPP = 0;
                $MessageBPP = 'BPP payment Unpaid';
            }
        } else {
            $StatusBPP = -1;
            $MessageBPP = 'BPP payment unset, please contact academic service';
        }

        return array('Status' => $StatusBPP, 'Message' => $MessageBPP);
    }

    public function checkCreditPayment($NPM, $SemesterID)
    {
        // Credit
        $dataSKS = $this->db->select('Status')->get_where(
            'db_finance.payment',
            array('NPM' => $NPM, 'PTID' => 3, 'SemesterID' => $SemesterID),
            1
        )->result_array();
        if (count($dataSKS) > 0) {
            if ($dataSKS[0]['Status'] == '1' || $dataSKS[0]['Status'] == 1) {
                $StatusCredit = 1;
                $MessageCredit = 'Credit payment Paid';
            } else {
                $StatusCredit = 0;
                $MessageCredit = 'Credit payment Unpaid';
            }
        } else {
            $StatusCredit = -1;
            $MessageCredit = 'Credit payment unset, please contact academic service';
        }

        return array('Status' => $StatusCredit, 'Message' => $MessageCredit);
    }

    public function checkPercentageAttendance($NPM, $ScheduleID)
    {

        $dataSD = $this->db->select('ID')
            ->get_where(
                'db_academic.schedule_details',
                array('ScheduleID' => $ScheduleID)
            )->result_array();

        $arrDataAttd = [];
        for ($t = 0; $t < count($dataSD); $t++) {
            // Get Attendance
            $dataAttd = $this->db->query('SELECT attd_s.* FROM db_academic.attendance_students attd_s
                                                          LEFT JOIN db_academic.attendance attd ON (attd.ID = attd_s.ID_Attd)
                                                          WHERE attd.ScheduleID = "' . $ScheduleID . '"
                                                          AND attd.SDID = "' . $dataSD[$t]['ID'] . '"
                                                           AND attd_s.NPM = "' . $NPM . '" ')->result_array();
            array_push($arrDataAttd, $dataAttd);
        }

        $result = 0;

        // Count Attendance
        if (count($arrDataAttd) > 0) {
            $meeting = 0;
            $Totalpresen = 0;
            for ($a = 0; $a < count($arrDataAttd); $a++) {
                $dataAttd = $arrDataAttd[$a];
                for ($m = 1; $m <= 14; $m++) {
                    $meeting += 1;
                    if ($dataAttd[0]['M' . $m] == '1') {
                        $Totalpresen += 1;
                    }
                }
            }

            $PresensiArg = ($Totalpresen == 0) ? 0 : ($Totalpresen / $meeting) * 100;
            $result = round($PresensiArg);
        }

        return $result;
    }

    public function getDetailsScheduleExam($db, $NPM, $SemesterID, $ExamType)
    {
        // Get data jadwal

        $q = 'SELECT sc.ID AS ScheduleID, mk.MKCode, mk.Name AS Course,
                       mk.NameEng AS CourseEng, ex.ID AS ExamID, ex.ExamDate, ex.ExamStart, ex.ExamEnd, ex.OnlineLearning,
                       cl.Room,sc.ClassGroup, sc.Attendance
                       FROM ' . $db . '.study_planning sp
                       LEFT JOIN db_academic.exam_details exd ON (exd.ScheduleID = sp.ScheduleID AND exd.NPM = sp.NPM)
                       LEFT JOIN db_academic.exam ex ON (ex.ID = exd.ExamID)
                       LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                       LEFT JOIN db_academic.schedule sc ON (sc.ID = sp.ScheduleID)
                       LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sp.MKID)
                       WHERE sp.SemesterID = "' . $SemesterID . '"
                       AND ex.Type LIKE "' . $ExamType . '"
                       AND sp.NPM = "' . $NPM . '"
                       GROUP BY ex.ID
                       ORDER BY mk.MKCode ASC';

        $ExamSchedule = $this->db->query($q)->result_array();

        if (count($ExamSchedule) > 0) {
            for ($g = 0; $g < count($ExamSchedule); $g++) {

                $examD = $ExamSchedule[$g];

                $btnOnlineExamStart = 0;
                $rangeTime = 0;
                // Mengecek persamaan tanggal
                if (
                    $examD['ExamDate'] == $this->getDateNow()
                    && $examD['OnlineLearning'] == '1'
                ) {
                    $timeStart = strtotime($examD['ExamStart']);
                    $timeEnd = strtotime($examD['ExamEnd']);
                    $time1 = strtotime($this->getTimeNow());

                    if ($timeStart <= $time1 && $time1 <= $timeEnd) {
                        $btnOnlineExamStart = 1;

                        $to_time = strtotime("2011-01-12 " . $this->getTimeNow());
                        $from_time = strtotime("2011-01-12 " . $examD['ExamEnd']);
                        $rangeTime = round(abs($to_time - $from_time) / 60, 0);
                    }
                }

                $ExamSchedule[$g]['btnOnlineExamStart'] = $btnOnlineExamStart;
                $ExamSchedule[$g]['rangeTime'] = $rangeTime;
                $ExamSchedule[$g]['rangeTimeJAMSEKARANG'] = $this->getDateNow();

                // Get Schedule Detail
                $dataSD = $this->db->select('ID')->get_where('db_academic.schedule_details', array('ScheduleID' => $examD['ScheduleID']))->result_array();

                $arrDataAttd = [];
                for ($t = 0; $t < count($dataSD); $t++) {
                    // Get Attendance
                    $dataAttd = $this->db->query('SELECT attd_s.* FROM db_academic.attendance_students attd_s
                                                          LEFT JOIN db_academic.attendance attd ON (attd.ID = attd_s.ID_Attd)
                                                          WHERE attd.SemesterID = "' . $SemesterID . '"
                                                          AND attd.ScheduleID = "' . $examD['ScheduleID'] . '"
                                                          AND attd.SDID = "' . $dataSD[$t]['ID'] . '"
                                                           AND attd_s.NPM = "' . $NPM . '" ')->result_array();
                    array_push($arrDataAttd, $dataAttd);
                }


                if (count($arrDataAttd) > 0) {
                    $meeting = 0;
                    $Totalpresen = 0;
                    for ($a = 0; $a < count($arrDataAttd); $a++) {
                        $dataAttd = $arrDataAttd[$a];
                        for ($m = 1; $m <= 14; $m++) {
                            $meeting += 1;
                            if ($dataAttd[0]['M' . $m] == '1') {
                                $Totalpresen += 1;
                            }
                        }
                    }

                    $PresensiArg = ($Totalpresen == 0) ? 0 : ($Totalpresen / $meeting) * 100;
                    $ExamSchedule[$g]['AttendancePercentage'] = round($PresensiArg);

                    // UAS
                    //                    if($ExamType=='uas' || $ExamType=='UAS'){
                    //                        if($PresensiArg<75 && $ExamSchedule[$g]['Attendance']=='1'){
                    //                            $ExamSchedule[$g]['ExamDate'] = null;
                    //                            $ExamSchedule[$g]['ExamEnd'] = null;
                    //                            $ExamSchedule[$g]['ExamStart'] = null;
                    //                            $ExamSchedule[$g]['Room'] = null;
                    //                        }
                    //                    }
                }
            }
        }

        return $ExamSchedule;
    }

    public function getAttendanceStudent($NPM, $ScheduleID)
    {
        $dataSD = $this->db->query('SELECT sd.ID AS SDID, s.SemesterID FROM db_academic.schedule_details sd
                                              LEFT JOIN db_academic.schedule s ON (s.ID = sd.ScheduleID)
                                              WHERE s.ID = "' . $ScheduleID . '" ')->result_array();


        $arrDataAttd = [];
        if (count($dataSD) > 0) {
            for ($s = 0; $s < count($dataSD); $s++) {

                // Get Attendance
                $dataAttd = $this->db->query('SELECT attd_s.* FROM db_academic.attendance_students attd_s
                                                          LEFT JOIN db_academic.attendance attd ON (attd.ID = attd_s.ID_Attd)
                                                          WHERE attd.SemesterID = "' . $dataSD[$s]['SemesterID'] . '"
                                                          AND attd.ScheduleID = "' . $ScheduleID . '"
                                                          AND attd.SDID = "' . $dataSD[$s]['SDID'] . '"
                                                           AND attd_s.NPM = "' . $NPM . '" ')->result_array();

                array_push($arrDataAttd, $dataAttd);
            }
        }

        $meeting = 0;
        $Totalpresen = 0;
        $Percentage = 0;

        if (count($arrDataAttd) > 0) {

            for ($a = 0; $a < count($arrDataAttd); $a++) {
                $dataAttd = $arrDataAttd[$a];
                for ($m = 1; $m <= 14; $m++) {
                    $meeting += 1;
                    if ($dataAttd[0]['M' . $m] == '1') {
                        $Totalpresen += 1;
                    }
                }
            }

            $Percentage = ($Totalpresen == 0) ? 0 : ($Totalpresen / $meeting) * 100;
        }

        $result = array(
            'Session' => $meeting,
            'TotalPresent' => $Totalpresen,
            'Percentage' => $Percentage
        );

        return $result;
    }

    public function newSystem($data, $ProdiID)
    {


        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $scDetail = $this->db->query('SELECT sd.ClassroomID, sd.DayID, sd.StartSessions, sd.EndSessions, cl.Room, d.NameEng
                                                    FROM db_academic.schedule_details sd
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID=sd.ClassRoomID)
                                                    LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                    WHERE sd.ScheduleID = "' . $data[$i]['ScheduleID'] . '" ORDER BY sd.DayID ASC ')->result_array();

                $data[$i]['DetailSchedule'] = $scDetail;

                $scCourse = $this->db->query('SELECT sdc.ProdiID,sdc.CDID, sdc.MKID,mk.MKCode, mk.NameEng, cd.TotalSKS AS Credit FROM db_academic.schedule_details_course sdc
                                                        LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                        WHERE sdc.ScheduleID = "' . $data[$i]['ScheduleID'] . '" AND sdc.ProdiID = "' . $ProdiID . '" ')->result_array();

                $data[$i]['DetailCourse'] = $scCourse[0];

                if ($data[$i]['TeamTeaching'] == '1') {
                    $scTeam = $this->db->query('SELECT tc.NIP,em.Name,tc.Status FROM db_academic.schedule_team_teaching tc
                                                          LEFT JOIN db_employees.employees em
                                                          ON (em.NIP = tc.NIP)
                                                          WHERE tc.ScheduleID = "' . $data[$i]['ScheduleID'] . '" ')->result_array();

                    $data[$i]['DetailTeamTeaching'] = $scTeam;
                }
            }
        }

        return $data;
    }

    public function __geTimetable($NIP, $SemesterID = '')
    {

        $WhereSmt = ($SemesterID != '') ? ' WHERE s.ID = "' . $SemesterID . '" ' : '';

        $dataSemester = $this->db->query('SELECT s.*, ay.totalSession FROM db_academic.semester s 
                                LEFT JOIN db_academic.academic_years ay ON (s.ID = ay.SemesterID) ' . $WhereSmt . ' ORDER BY s.ID ASC')->result_array();


        $result = [];
        for ($i = 0; $i < count($dataSemester); $i++) {
            if ($dataSemester[$i]['ID'] < 13) {
                // Koordinator
                $Coordinator = $this->db->query('SELECT s.*, mk.MKCode, mk.Name AS MKName, mk.NameEng AS MKNameEng, ps.NameEng AS ProdiEng, ps.Code AS ProdiCode
                                                          FROM db_academic.z_schedule s
                                                          LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID=s.MKID)
                                                          LEFT JOIN db_academic.program_study ps ON (ps.ID=s.ProdiID)
                                                          WHERE s.SemesterID="' . $dataSemester[$i]['ID'] . '"
                                                          AND s.NIP = "' . $NIP . '" GROUP BY mk.MKCode ')->result_array();

                if (count($Coordinator) > 0) {
                    for ($t = 0; $t < count($Coordinator); $t++) {
                        if ($Coordinator[$t]['IsTeamTeaching'] == '1') {

                            $ttc = $this->db->query('SELECT ttc.*,em.Name,em.TitleAhead,em.TitleBehind FROM db_academic.z_team_teaching ttc
                                                          LEFT JOIN db_employees.employees em ON (em.NIP=ttc.NIP)
                                                          WHERE ttc.Glue = "' . $Coordinator[$t]['Glue'] . '" AND ttc.Pengampu = "TIDAK" ')
                                ->result_array();
                            if (count($ttc)) {
                                for ($l = 0; $l < count($ttc); $l++) {
                                    $Lecturer = $ttc[$l]['TitleAhead'] . ' ' . $ttc[$l]['Name'] . ' ' . $ttc[$l]['TitleBehind'];
                                    $ttc[$l]['Lecturer'] = trim($Lecturer);
                                }
                                $Coordinator[$t]['DetailTeamTeaching'] = $ttc;
                            }
                        }
                    }
                }

                $TeamTeaching = $this->db->query('SELECT s.*,em.Name,em.TitleAhead,em.TitleBehind, mk.MKCode, mk.Name AS MKName,
                                                          mk.NameEng AS MKNameEng, ps.NameEng AS ProdiEng, ps.Code AS ProdiCode
                                                          FROM db_academic.z_team_teaching ttc
                                                          LEFT JOIN db_academic.z_schedule s ON (s.Glue = ttc.Glue)
                                                          LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID=s.MKID)
                                                          LEFT JOIN db_academic.program_study ps ON (ps.ID=s.ProdiID)
                                                          LEFT JOIN db_employees.employees em ON (em.NIP=s.NIP)
                                                          WHERE s.SemesterID="' . $dataSemester[$i]['ID'] . '"
                                                          AND ttc.NIP = "' . $NIP . '" AND ttc.Pengampu = "TIDAK" GROUP BY mk.MKCode')->result_array();

                if (count($TeamTeaching) > 0) {
                    for ($ttc = 0; $ttc < count($TeamTeaching); $ttc++) {
                        $CoordinatorTcc = $TeamTeaching[$ttc]['TitleAhead'] . ' ' . $TeamTeaching[$ttc]['Name'] . ' ' . $TeamTeaching[$ttc]['TitleBehind'];
                        $TeamTeaching[$ttc]['Coordinator'] = trim($CoordinatorTcc);
                    }
                }

                $arr_p = array(
                    'SemesterID' => $dataSemester[$i]['ID'],
                    'Semester' => $dataSemester[$i]['Name'],
                    'Status' => $dataSemester[$i]['Status'],
                    'totalSession' => $dataSemester[$i]['totalSession'],
                    'DetailsCoordinator' => $Coordinator,
                    'DetailsTeamTeaching' => $TeamTeaching
                );

                array_push($result, $arr_p);
            }
            // Sistem Baru
            else {

                $Coordinator = $this->db->query('SELECT s.*,em.Name AS CoordinatorName, cd.TotalSKS AS CourseCredit
                                              FROM db_academic.schedule s
                                              LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                              LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                              LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                              WHERE s.SemesterID = "' . $dataSemester[$i]['ID'] . '"
                                              AND s.Coordinator = "' . $NIP . '" AND s.IsSemesterAntara = "0" GROUP BY s.ID')->result_array();

                $TeamTheaching = $this->db->query('SELECT s.*,em.Name AS CoordinatorName, stt.Status AS StatusTeamTeaching, cd.TotalSKS AS CourseCredit
                                                        FROM db_academic.schedule_team_teaching stt
                                                        LEFT JOIN db_academic.schedule s ON (s.ID=stt.ScheduleID)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                        LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                        WHERE s.SemesterID ="' . $dataSemester[$i]['ID'] . '"
                                                        AND stt.NIP = "' . $NIP . '"
                                                        AND s.IsSemesterAntara = "0" GROUP BY s.ID')->result_array();

                $arr_p = array(
                    'SemesterID' => $dataSemester[$i]['ID'],
                    'Semester' => $dataSemester[$i]['Name'],
                    'Status' => $dataSemester[$i]['Status'],
                    'totalSession' => $dataSemester[$i]['totalSession'],
                    'DetailsCoordinator' => $this->getDetailTimeTable($Coordinator, 'Coordinator'),
                    'DetailsTeamTeaching' => $this->getDetailTimeTable($TeamTheaching, '')
                );

                array_push($result, $arr_p);
            }


            if ($dataSemester[$i]['Status'] == 1 || $dataSemester[$i]['Status'] == '1') {
                break;
            }
        }

        return $result;
    }

    private function getDetailTimeTable($dataSch, $param)
    {

        if (count($dataSch)) {
            $this->load->model('m_api');
            for ($s = 0; $s < count($dataSch); $s++) {
                $sesi = $this->db->query('SELECT sd.ScheduleID, cl.Room, d.NameEng, sd.StartSessions, sd.EndSessions,
                                                   attd.ID AS ID_Attd, attd.SemesterID
                                                   FROM db_academic.schedule_details sd
                                                   LEFT JOIN db_academic.classroom cl ON (cl.ID=sd.ClassroomID)
                                                   LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                   LEFT JOIN db_academic.attendance attd
                                                   ON (attd.SemesterID = ' . $dataSch[$s]['SemesterID'] . '
                                                   AND attd.ScheduleID = sd.ScheduleID AND attd.SDID = sd.ID)
                                                    WHERE sd.ScheduleID = "' . $dataSch[$s]['ID'] . '" ')->result_array();
                $dataSch[$s]['detailSesi'] = $sesi;

                $course = $this->db->query('SELECT sdc.ScheduleID, mk.ID AS MKID, cd.TotalSKS AS Credit, mk.MKCode, mk.Name, mk.NameEng
                                                   FROM db_academic.schedule_details_course sdc
                                                   LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                   LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                    WHERE sdc.ScheduleID = "' . $dataSch[$s]['ID'] . '" GROUP BY  sdc.ScheduleID ')->result_array();
                //                                                    WHERE sdc.ScheduleID = "' . $dataSch[$s]['ID'] . '" AND  sdc.ProdiID = "'.$ProdiID.'"  ')->result_array();
                $dataSch[$s]['detailCourse'] = $course;
                //                $dataSch[$s]['Students'] = $this->m_api->getStudentByScheduleID($dataSch[$s]['SemesterID'],$dataSch[$s]['ID'],'');
                $dataSch[$s]['TotalStudents'] = $this->m_api->getTotalStdPerDay($dataSch[$s]['SemesterID'], $dataSch[$s]['ID'], '');

                //                if($param=='Coordinator'){
                $team = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule_team_teaching stt
                                                       LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                       WHERE stt.ScheduleID="' . $dataSch[$s]['ID'] . '"
                                                       ')->result_array();
                $dataSch[$s]['detailTeamTeaching'] = $team;

                $silabus = $this->db->query('SELECT gc.*
                                                            FROM  db_academic.grade_course gc
                                                            WHERE gc.ScheduleID = "' . $dataSch[$s]['ID'] . '" ')->result_array();

                $dataSch[$s]['detailSilabusSAP'] = $silabus;
                //                }

            }
        }

        return $dataSch;
    }

    public function __getStudentsDetails($SemesterID, $ScheduleID)
    {

        $this->load->model('m_api');
        $dataCl = $this->m_api->getClassOf();

        $arrDataStd = [];
        if (count($dataCl) > 0) {
            for ($i = 0; $i < count($dataCl); $i++) {
                $db_ = 'ta_' . $dataCl[$i]['Year'];

                $data = $this->db->query('SELECT s.NPM, s.Name, sp.Evaluasi1, sp.Evaluasi2,
                                                    sp.Evaluasi3, sp.Evaluasi4, sp.Evaluasi5, sp.UTS, sp.UAS,
                                                    sp.Score, sp.Grade, sp.Approval
                                                    FROM ' . $db_ . '.study_planning sp
                                                    LEFT JOIN ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                    WHERE sp.SemesterID ="' . $SemesterID . '"
                                                    AND sp.ScheduleID = "' . $ScheduleID . '"
                                                    ORDER BY s.NPM ASC
                                                     ')->result_array();

                if (count($data) > 0) {
                    for ($s = 0; $s < count($data); $s++) {
                        array_push($arrDataStd, $data[$s]);
                    }
                }
            }
        }


        // Get Total Assigment Aktif
        $dataAssg = $this->db->select('ID AS ScheduleID, ClassGroup, TotalAssigment')->get_where('db_academic.schedule', array(
            'SemesterID' => $SemesterID,
            'ID' => $ScheduleID
        ), 1)->result_array();

        $dataGrade = $this->db->get_where('db_academic.grade_course', array(
            'SemesterID' => $SemesterID,
            'ScheduleID' => $ScheduleID
        ), 1)->result_array();;


        if (count($dataAssg) > 0) {
            $dataAssg[0]['DetailStudent'] = $arrDataStd;
            $dataAssg[0]['Weightages'] = $dataGrade;
        }



        return $dataAssg;
    }

    public function __getStudentByScheduleID($SemesterID, $ScheduleID)
    {
        $this->load->model('m_api');
        $dataCl = $this->m_api->getClassOf();

        $arrDataStd = [];
        if (count($dataCl) > 0) {
            for ($i = 0; $i < count($dataCl); $i++) {
                $db_ = 'ta_' . $dataCl[$i]['Year'];

                $data = $this->db->query('SELECT s.NPM, s.Name, sp.Evaluasi1, sp.Evaluasi2,
                                                    sp.Evaluasi3, sp.Evaluasi4, sp.Evaluasi5, sp.UTS, sp.UAS,
                                                    sp.Score, sp.Grade, sp.Approval
                                                    FROM ' . $db_ . '.study_planning sp
                                                    LEFT JOIN ' . $db_ . '.students s ON (s.NPM = sp.NPM)
                                                    WHERE sp.SemesterID ="' . $SemesterID . '"
                                                    AND sp.ScheduleID = "' . $ScheduleID . '"
                                                    ORDER BY s.NPM ASC
                                                     ')->result_array();

                if (count($data) > 0) {
                    for ($s = 0; $s < count($data); $s++) {
                        array_push($arrDataStd, $data[$s]);
                    }
                }
            }
        }

        return $arrDataStd;
    }


    public function __getExamSchedule($NIP, $Type)
    {

        $SemesterActive = $this->_getSemesterActive();
        $SemesterID = $SemesterActive['SemesterID'];

        $dataSemester = $this->db->query('SELECT s.ID, s.ProgramCampusID, s.Name, s.Status, ay.utsStart, ay.utsEnd, ay.uasStart, ay.uasEnd FROM db_academic.semester s
                                                      LEFT JOIN db_academic.academic_years ay ON (ay.SemesterID = s.ID)
                                                      ORDER BY s.ID ASC')->result_array();

        $result = [];
        for ($i = 0; $i < count($dataSemester); $i++) {
            if ($dataSemester[$i]['ID'] < 13) {

                $arr_p = array(
                    'DataSemester' => $dataSemester[$i],
                    'SemesterID' => $dataSemester[$i]['ID'],
                    'Semester' => $dataSemester[$i]['Name'],
                    'Status' => $dataSemester[$i]['Status'],
                    'DataExamSchedule' => []
                );

                array_push($result, $arr_p);
            }
            // Sistem Baru
            else {

                $Coordinator = $this->db->query('SELECT s.ID,s.ClassGroup, mk.NameEng AS CourseEng, mk.MKCode
                                              FROM db_academic.schedule s
                                              LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                              LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                              LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                              WHERE s.SemesterID = "' . $dataSemester[$i]['ID'] . '"
                                              AND s.Coordinator = "' . $NIP . '" AND s.IsSemesterAntara = "0"
                                               GROUP BY s.ID')->result_array();

                $TeamTheaching = $this->db->query('SELECT s.ID,s.ClassGroup, mk.NameEng AS CourseEng, mk.MKCode
                                                        FROM db_academic.schedule_team_teaching stt
                                                        LEFT JOIN db_academic.schedule s ON (s.ID=stt.ScheduleID)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                      LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        WHERE s.SemesterID ="' . $SemesterID . '"
                                                        AND stt.NIP = "' . $NIP . '"
                                                        AND s.IsSemesterAntara = "0" ')->result_array();

                $dataCourse = $Coordinator;
                if (count($TeamTheaching) > 0) {
                    for ($t = 0; $t < count($TeamTheaching); $t++) {
                        array_push($dataCourse, $TeamTheaching[$t]);
                    }
                }

                // Load Exam
                if (count($dataCourse) > 0) {
                    for ($r = 0; $r < count($dataCourse); $r++) {
                        $d = $dataCourse[$r];
                        $dataExam = $this->db->query('SELECT ex.Type, ex.ExamDate, ex.ExamStart, ex.ExamEnd, cl.Room
                                                                , em1.Name AS P_Name1, em2.Name AS P_Name2
                                                                FROM db_academic.exam_group exg
                                                                LEFT JOIN db_academic.exam ex ON (ex.ID = exg.ExamID)
                                                                LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                                                LEFT JOIN db_employees.employees em1 ON (em1.NIP = ex.Pengawas1)
                                                                LEFT JOIN db_employees.employees em2 ON (em2.NIP = ex.Pengawas2)
                                                                WHERE exg.ScheduleID = "' . $d['ID'] . '" AND ex.Type = "' . $Type . '"
                                                                 ORDER BY ExamDate, ExamStart, ExamEnd ASC')->result_array();
                        $dataCourse[$r]['ExamSchedule'] = $dataExam;
                    }
                }


                $arr_p = array(
                    'DataSemester' => $dataSemester[$i],
                    'SemesterID' => $dataSemester[$i]['ID'],
                    'Semester' => $dataSemester[$i]['Name'],
                    'Status' => $dataSemester[$i]['Status'],
                    'DataExamSchedule' => $dataCourse
                );

                array_push($result, $arr_p);
            }
        }

        return $result;
    }

    public function __getExamSchedule4Lecturer($SemesterID, $NIP, $ExamType)
    {

        $dataSemester = $this->db->query('SELECT s.*, ay.utsStart, ay.utsEnd, ay.uasStart, ay.uasEnd FROM db_academic.semester s
                                                        LEFT JOIN db_academic.academic_years ay ON (ay.SemesterID = s.ID)
                                                        WHERE s.ID = ' . $SemesterID . '
                                                        ORDER BY s.ID ASC')->result_array();

        // Get setting exam
        $dataExamSetting = $this->db->get('db_academic.exam_setting')->result_array();

        if ($SemesterID >= 13) {

            $i = 0;

            // Cek apakah sudah masuk pada periode ujian atau belum (UTS / UAS)
            $checkDateExam = true;
            $dateExamStart = ($ExamType == 'uts' || $ExamType == 'UTS')
                ? $dataSemester[$i]['utsStart']
                : $dataSemester[$i]['uasStart'];

            if ($ExamType == 'uts' || $ExamType == 'UTS' || $ExamType == 'uas' || $ExamType == 'UAS') {

                $day = 7;
                if (count($dataExamSetting) > 0) {
                    $ExamSetting = $dataExamSetting[0];
                    $day = ($ExamType == 'uts' || $ExamType == 'UTS')
                        ? $ExamSetting['UTSShown']
                        : $ExamSetting['UASShown'];
                }

                $dateShow = date('Y-m-d', strtotime($this->getDateNow() . '+ ' . $day . ' days'));
                $AvailableDate = date('Y-m-d', strtotime($dateExamStart . '- ' . $day . ' days'));

                if ($dateExamStart > $dateShow) {
                    $checkDateExam = false;
                }
            }

            if ($checkDateExam) {

                $Coordinator = $this->db->query('SELECT s.ID AS ScheduleID, s.ClassGroup, mk.NameEng AS CourseEng, mk.MKCode
                                              FROM db_academic.schedule s
                                              LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                              LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                              LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                              WHERE s.SemesterID = "' . $SemesterID . '"
                                              AND s.Coordinator = "' . $NIP . '" AND s.IsSemesterAntara = "0"
                                               GROUP BY s.ID')->result_array();

                $TeamTheaching = $this->db->query('SELECT s.ID AS ScheduleID, s.ClassGroup, mk.NameEng AS CourseEng, mk.MKCode
                                                        FROM db_academic.schedule_team_teaching stt
                                                        LEFT JOIN db_academic.schedule s ON (s.ID=stt.ScheduleID)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                      LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        WHERE s.SemesterID ="' . $SemesterID . '"
                                                        AND stt.NIP = "' . $NIP . '"
                                                        AND s.IsSemesterAntara = "0" ')->result_array();

                $dataCourse = $Coordinator;
                if (count($TeamTheaching) > 0) {
                    for ($t = 0; $t < count($TeamTheaching); $t++) {
                        array_push($dataCourse, $TeamTheaching[$t]);
                    }
                }

                if (count($dataCourse) > 0) {
                    $i = 0;
                    foreach ($dataCourse as $item) {
                        $dataExam = $this->db->query('SELECT ex.*, cl.Room, em1.Name AS Inv1, em2.Name AS Inv2, qe.QuizID
                                                                    FROM db_academic.exam ex
                                                                    LEFT JOIN db_academic.q_exam qe ON (qe.ExamID = ex.ID)
                                                                    LEFT JOIN db_academic.exam_group eg ON (eg.ExamID = ex.ID)
                                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = ex.ExamClassroomID)
                                                                    LEFT JOIN db_employees.employees em1 ON (em1.NIP = ex.Pengawas1)
                                                                    LEFT JOIN db_employees.employees em2 ON (em2.NIP = ex.Pengawas2)
                                                                    WHERE ex.SemesterID = "' . $SemesterID . '"
                                                                    AND ex.Type = "' . strtolower($ExamType) . '"
                                                                    AND eg.ScheduleID = "' . $item['ScheduleID'] . '"
                                                                    GROUP BY ex.ID ')->result_array();

                        if (count($dataExam) > 0) {
                            for ($s = 0; $s < count($dataExam); $s++) {
                                $dataStd = $this->db->get_where(
                                    'db_academic.exam_details',
                                    array('ExamID' => $dataExam[$s]['ID'])
                                )->num_rows();

                                $dataExam[$s]['TotalStudent'] = $dataStd;
                            }
                        }


                        $dataCourse[$i]['ExamSchedule'] = $dataExam;
                        $i += 1;
                    }

                    $result = array(
                        'Status' => 1,
                        'DetailExam' => $dataCourse
                    );
                } else {
                    $result = array(
                        'Status' => -5,
                        'Message' => 'You don\'t have a teaching schedule'
                    );
                }
            } else {
                $result = array(
                    'Status' => -3,
                    'Message' => 'The exam is out of date',
                    'AvailableDate' => $AvailableDate
                );
            }
        } else {
            $result = array(
                'Status' => -4,
                'Message' => 'Schedule Exam Not Available'
            );
        }




        return $result;
    }

    public function __getListExamDate($SemesterID, $ExamType)
    {
        # code...
        $data = $this->db->select('ExamDate')
            ->group_by("ExamDate")
            ->order_by('ExamDate', 'ASC')
            ->get_where(
                'db_academic.exam',
                array('SemesterID' => $SemesterID, 'Type' => $ExamType)
            )->result_array();

        return $data;
    }

    public function __getListScheduleExamSemesterActive($SemesterID, $ExamType, $Date, $OnlineLearning)
    {

        $data = $this->db->query('SELECT e.*, em1.Name AS Pengawas1Name, em2.Name AS Pengawas2Name, qe.QuizID FROM db_academic.exam e
                                            LEFT JOIN db_employees.employees em1 ON (em1.NIP = e.Pengawas1)
                                            LEFT JOIN db_employees.employees em2 ON (em2.NIP = e.Pengawas2)
                                            LEFT JOIN db_academic.q_exam qe ON (qe.ExamID = e.ID)
                                            WHERE e.SemesterID = "' . $SemesterID . '"
                                            AND e.Type = "' . $ExamType . '" AND e.ExamDate = "' . $Date . '"
                                            AND e.OnlineLearning = "' . $OnlineLearning . '"
                                            ORDER BY e.ExamDate ASC, e.ExamStart ASC, e.ExamEnd ASC')->result_array();

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {

                $Schedule = $this->db->query('SELECT ex.ScheduleID, s.ClassGroup, mk.MKCode, mk.Name, mk.NameEng, s.Coordinator FROM db_academic.schedule s
                                                            LEFT JOIN db_academic.exam_group ex ON (s.ID = ex.ScheduleID)
                                                            LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                            WHERE ex.ExamID = "' . $data[$i]['ID'] . '" GROUP BY s.ID')->result_array();

                $data[$i]['Schedule'] = $Schedule;

                $listLecturer = [];
                if (count($Schedule) > 0) {
                    for ($l = 0; $l < count($Schedule); $l++) {
                        array_push($listLecturer, $Schedule[$l]['Coordinator']);
                        $dataTeamTeach = $this->getLecturerTeamListSchedule($Schedule[$l]['ScheduleID'], 'NIP_Only');
                        array_merge($listLecturer, $dataTeamTeach);
                    }
                }
                $data[$i]['ListLecturer'] = $listLecturer;
            }
        }


        return $data;
    }

    private function getLecturerTeamListSchedule($ScheduleID, $resultType)
    {
        // coordinator
        $data = $this->db->query('SELECT * FROM db_academic.schedule_team_teaching stt
                                    WHERE stt.ScheduleID = "' . $ScheduleID . '" ')->result_array();

        $result = $data;
        if ($resultType == 'NIP_Only') {
            $ArrNIP = [];
            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    array_push($ArrNIP, $data[$i]['NIP']);
                }
            }

            $result = $ArrNIP;
        }

        return $result;
    }

    public function getDetailStudyResultByNPM($ClassOf, $NPM)
    {
        $order = 'ASC';

        $data = $this->db->query('SELECT s.*, ay.showNilai_H, ay.showNilai_T FROM db_academic.semester s
                                            LEFT JOIN db_academic.academic_years ay ON (s.ID = ay.SemesterID)
                                            WHERE s.ID >= (SELECT s2.ID FROM db_academic.semester s2
                                                                  WHERE s2.Year="' . $ClassOf . '" LIMIT 1)
                                            ORDER BY s.ID ' . $order)->result_array();

        $db = 'ta_' . $ClassOf;
        $smt = 1;
        $res = [];
        for ($i = 0; $i < count($data); $i++) {

            $System = ($data[$i]['ID'] >= 13) ? 1 : 0;
            $khs = $this->getDataKHS($db, $NPM, $data[$i]['ID'], $data[$i]['Status'], $System);

            //            if(count($khs)>0){
            $result[$i]['semester'] = $smt;
            $result[$i]['SemesterID'] = $data[$i]['ID'];
            $result[$i]['SemesterName'] = $data[$i]['Name'];
            $result[$i]['Show_H'] = $data[$i]['showNilai_H'];
            $result[$i]['Show_T'] = $data[$i]['showNilai_T'];
            $result[$i]['semesterDetail'] = $khs;

            array_push($res, $result[$i]);
            //            }
            $smt += 1;
            if ($data[$i]['Status'] == '1' || $data[$i]['Status'] == 1) {
                break;
            }
        }
        return $res;
    }

    public function getTranscript($ClassOf, $NPM, $order)
    {


        $data = $this->db->query('SELECT s.* FROM db_academic.semester s WHERE s.ID >= (SELECT s2.ID FROM db_academic.semester s2
                                        WHERE s2.Year="' . $ClassOf . '" LIMIT 1) ORDER BY s.ID ' . $order)->result_array();

        $db = 'ta_' . $ClassOf;
        $dataSmtActive = $this->_getSemesterActive();

        $transcript = [];
        $arrTranscriptID = [];
        $dateNow = $this->getDateNow();
        $showNilaiSemesterActive = ($dataSmtActive['updateTranscript'] <= $dateNow) ? 1 : 0;

        $smt = ($order == 'ASC') ? 0 : count($data) + 1;
        for ($i = 0; $i < count($data); $i++) {

            $System = ($data[$i]['ID'] >= 13) ? 1 : 0;
            $khs = $this->getDataKHS($db, $NPM, $data[$i]['ID'], '', $System);

            if (count($khs) > 0) {
                $smt = ($order == 'ASC') ? $smt + 1 : $smt - 1;

                // Cek apakah ada mata kuliha ngulang apa engga
                for ($k = 0; $k < count($khs); $k++) {
                    $d = $khs[$k];
                    if ($d['ShowTranscript'] == 1 && $d['ShowTranscript'] == '1') {
                        // cek apakah sudah ada di list transcript atau belum, jika belim lanjutkan
                        if (in_array($d['MKID'], $arrTranscriptID) == false) {

                            // cek apakah MKID punya lebih dari 1 jika maka ambil nilai tertingginya
                            if ($showNilaiSemesterActive == 1 || $showNilaiSemesterActive == '1') {
                                $dataScore = $this->db->order_by('Score', 'DESC')
                                    ->get_where($db . '.study_planning', array('NPM' => $NPM, 'MKID' => $d['MKID']))->result_array();
                            } else {
                                $dataScore = $this->db->order_by('Score', 'DESC')
                                    ->get_where($db . '.study_planning', array(
                                        'NPM' => $NPM, 'MKID' => $d['MKID'], 'SemesterID !=' => $dataSmtActive['SemesterID']
                                    ))->result_array();
                            }

                            if (count($dataScore) > 0) {
                                $Score = (isset($dataScore[0]['Score']) && $dataScore[0]['Score'] != '' && $dataScore[0]['Score'] != null && $dataScore[0]['Score'] != '-') ? $dataScore[0]['Score'] : 0;
                                $Grade = (isset($dataScore[0]['Grade']) && $dataScore[0]['Grade'] != '' && $dataScore[0]['Grade'] != null && $dataScore[0]['Grade'] != '-') ? $dataScore[0]['Grade'] : 'E';
                                $GradeValue = (isset($dataScore[0]['GradeValue']) && $dataScore[0]['GradeValue'] != '' && $dataScore[0]['GradeValue'] != null && $dataScore[0]['GradeValue'] != '-') ? $dataScore[0]['GradeValue'] : 0;

                                $dataTRXc = [];
                                if ($d['TransferCourse'] == '1') {
                                    // get matakuliah asal
                                    $dataTRXc = $this->db->query('SELECT cd.TotalSKS, mk.Name AS MKName, mk.NameEng AS MKNameEng, mk.MKCode
                                                                    FROM db_academic.transfer_history_conversion thc
                                                                    LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = thc.CDID_Before)
                                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                                    WHERE thc.NPM_After = "' . $NPM . '"
                                                                    AND thc.CDID_After = "' . $d['CDID'] . '" ')->result_array();
                                }

                                $arrTr = array(
                                    'SemesterID' => $data[$i]['ID'],
                                    'CDID' => $d['CDID'],
                                    'MKType' => $d['MKType'],
                                    'MKID' => $d['MKID'],
                                    'MKCode' => $d['MKCode'],
                                    'Course' => $d['Name'],
                                    'CourseEng' => $d['NameEng'],
                                    'Credit' => $d['Credit'],
                                    'TypeSchedule' => $dataScore[0]['TypeSchedule'],
                                    'TransferCourse' => $d['TransferCourse'],
                                    'TransferCourseDetails' => $dataTRXc,
                                    'Score' => $Score,
                                    'Grade' => $Grade,
                                    'GradeValue' => $GradeValue,
                                    'Point' => round(($d['Credit'] * $GradeValue), 2)
                                );
                                array_push($arrTranscriptID, $d['MKID']);
                                array_push($transcript, $arrTr);
                            }
                        }
                    }
                }
            }



            // Cek semester antara
            $dataSemesterAntara = $this->db->query('SELECT say.SASemesterID, say.UpdateTranscript FROM db_academic.semester_antara sa
                                                  LEFT JOIN db_academic.sa_academic_years say ON (say.SASemesterID = sa.ID)
                                                  WHERE sa.SemesterID = "' . $data[$i]['ID'] . '" ')->result_array();


            if (count($dataSemesterAntara) > 0) {

                $dt = $dataSemesterAntara[0];

                if ($dateNow >= $dt['UpdateTranscript']) {
                    // Cek apakah ada npmnya atau tidak
                    $dataStd = $this->db->query('SELECT ssd.*,mk.MKCode, mk.Name, mk.NameEng, cd.MKType FROM db_academic.sa_student_details ssd
                                                LEFT JOIN db_academic.sa_student ss ON (ss.ID = ssd.IDSAStudent)
                                                LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = ssd.CDID)
                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                WHERE ss.SASemesterID = "' . $dt['SASemesterID'] . '" AND ss.NPM = "' . $NPM . '" ')
                        ->result_array();


                    if (count($dataStd) > 0) {
                        for ($s = 0; $s < count($dataStd); $s++) {
                            $d_sa = $dataStd[$s];
                            if ($d_sa['Type'] == 'Br' && in_array($d_sa['MKID'], $arrTranscriptID) == false) {

                                $Score = (isset($d_sa['ScoreNew']) && $d_sa['ScoreNew'] != '' && $d_sa['ScoreNew'] != null && $d_sa['ScoreNew'] != '-')
                                    ? $d_sa['ScoreNew'] : 0;
                                $Grade = (isset($d_sa['GradeNew']) && $d_sa['GradeNew'] != '' && $d_sa['GradeNew'] != null && $d_sa['GradeNew'] != '-')
                                    ? $d_sa['GradeNew'] : 'E';
                                $GradeValue = (isset($d_sa['GradeValueNew']) && $d_sa['GradeValueNew'] != '' && $d_sa['GradeValueNew'] != null && $d_sa['GradeValueNew'] != '-')
                                    ? $d_sa['GradeValueNew'] : 0;


                                $arrTr = array(
                                    'SemesterID' => $data[$i]['ID'],
                                    'MKID' => $d_sa['MKID'],
                                    'CDID' => $d_sa['CDID'],
                                    'MKType' => $d_sa['MKType'],
                                    'MKCode' => $d_sa['MKCode'],
                                    'Course' => $d_sa['Name'],
                                    'CourseEng' => $d_sa['NameEng'],
                                    'Credit' => $d_sa['Credit'],
                                    'TypeSchedule' => $d_sa['Type'],
                                    'TransferCourse' => '0',
                                    'Score' => $Score,
                                    'Grade' => $Grade,
                                    'GradeValue' => $GradeValue,
                                    'Point' => ($d_sa['Credit'] * $GradeValue),
                                    'Source' => 'Semester Antara'


                                );
                                array_push($arrTranscriptID, $d_sa['MKID']);
                                array_push($transcript, $arrTr);
                            } else {

                                for ($i2 = 0; $i2 < count($transcript); $i2++) {

                                    $item = $transcript[$i2];

                                    if ($item['MKID'] == $d_sa['MKID']) {
                                        if ($item['Score'] < $d_sa['ScoreNew']) {
                                            $transcript[$i2]['Grade'] = $d_sa['GradeNew'];
                                            $transcript[$i2]['Score'] = $d_sa['ScoreNew'];
                                            $transcript[$i2]['GradeValue'] = $d_sa['GradeValueNew'];
                                            $transcript[$i2]['Credit'] = $d_sa['Credit'];
                                            $transcript[$i2]['Point'] = $d_sa['Credit'] * $d_sa['GradeValueNew'];
                                            $transcript[$i2]['Source'] = 'Semester Antara';
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $dataIPK = $this->getIPK4Transcript($transcript);

        $result = array(
            'dataIPK' => $dataIPK,
            'dataCourse' => $transcript
        );

        return $result;
    }

    public function getDetailsCourse($ClassOf, $NPM)
    {

        $db = 'ta_' . $ClassOf;

        $data = $this->db->query('SELECT sp.*, mk.MKCode, mk.Name, mk.NameEng, s.Name AS SemesterName, sspd.NextSemester FROM ' . $db . '.study_planning sp
                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sp.MKID)
                                            LEFT JOIN db_academic.semester s ON (s.ID = sp.SemesterID)
                                            LEFT JOIN db_academic.std_study_planning_details sspd ON (sspd.ClassOf = "' . $ClassOf . '" AND sspd.SPID = sp.ID)
                                            WHERE sp.NPM = "' . $NPM . '"
                                            ORDER BY sp.SemesterID DESC, mk.MKCode ASC ')->result_array();

        return $data;
    }

    public function getIPK4Transcript($dataTranscript)
    {


        $data_TotalPoint = 0;
        $data_TotalSKS = 0;
        $dataMK_D = [];
        $data_MK_Wajib = [];
        $data_MK_Wajib_SKS = 0;

        $LastSemesterID = 0;

        if (count($dataTranscript) > 0) {

            foreach ($dataTranscript as $item) {
                $data_TotalSKS = $data_TotalSKS + (float) $item['Credit'];
                $data_TotalPoint = $data_TotalPoint + $item['Point'];

                if ($item['Grade'] == 'D') {
                    array_push($dataMK_D, $item);
                }

                if ($item['MKType'] == '1') {
                    array_push($data_MK_Wajib, $item);
                    $data_MK_Wajib_SKS = $data_MK_Wajib_SKS + (int) $item['Credit'];
                }

                if ($LastSemesterID < $item['SemesterID']) {
                    $LastSemesterID = $item['SemesterID'];
                }
            }
        }

        $IPK_Ori = (count($dataTranscript) > 0) ? $data_TotalPoint / $data_TotalSKS : 0;
        $data_ipk = round($IPK_Ori, 2);

        // Menghitung IPS trakhir
        $last_IPS_TotalSKS = 0;
        $last_IPS__TotalPoint = 0;

        if ($LastSemesterID > 0) {
            foreach ($dataTranscript as $item) {

                if ($LastSemesterID == $item['SemesterID']) {
                    $last_IPS_TotalSKS = $last_IPS_TotalSKS + (float) $item['Credit'];
                    $last_IPS__TotalPoint = $last_IPS__TotalPoint + $item['Point'];
                }
            }
        }

        $last_IPS_Ori = ($LastSemesterID > 0) ? $last_IPS__TotalPoint / $last_IPS_TotalSKS : 0;
        $last_IPS = round($last_IPS_Ori, 2);


        $result = array(
            'IPK_Ori' => $IPK_Ori,
            'IPK' => number_format($data_ipk, 2, '.', ''),
            'TotalSKS' => $data_TotalSKS,
            'TotalPoint' => number_format($data_TotalPoint, 2, '.', ''),
            'MK_D' => $dataMK_D,
            'MK_Wajib' => $data_MK_Wajib,
            'MK_Wajib_SKS' => $data_MK_Wajib_SKS,
            'Last_SemesterID' => $LastSemesterID,
            'Last_IPS_Ori' => $last_IPS_Ori,
            'Last_IPS' => $last_IPS
        );

        return $result;
    }


    public function getDataKHS($db, $NPM, $SemesterID, $Status, $System)
    {

        $ClassOf = explode('_', $db)[1];
        $data = $this->db->query('SELECT sp.*,mk.MKCode, mk.Name, mk.NameEng, s.TotalAssigment, cd.MKType, sspd.NextSemester
                                        FROM ' . $db . '.study_planning sp
                                        LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sp.CDID)
                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                        LEFT JOIN db_academic.schedule s ON (s.ID = sp.ScheduleID)
                                        LEFT JOIN db_academic.std_study_planning_details sspd ON (sspd.ClassOf = "' . $ClassOf . '" AND sspd.SPID = sp.ID)
                                        WHERE sp.NPM = "' . $NPM . '"
                                        AND sp.SemesterID="' . $SemesterID . '"
                                        AND sp.CDID IS NOT NULL
                                        ORDER BY mk.MKCode ASC ')->result_array();

        $dateNow = $this->getDateNow();
        $showUTS = false;
        $showUAS = false;
        if ($System == '1') {
            // cek tanggal show nilai UTS & UAS
            $dataAY_UTS = $this->db->query('SELECT * FROM db_academic.academic_years
                            WHERE SemesterID = "' . $SemesterID . '" AND showNilaiUts <= "' . $dateNow . '" ')->result_array();
            if (count($dataAY_UTS) > 0) {
                $showUTS = true;
            }

            $dataAY_UAS = $this->db->query('SELECT * FROM db_academic.academic_years
                            WHERE SemesterID = "' . $SemesterID . '" AND showNilaiUas <= "' . $dateNow . '" ')->result_array();

            if (count($dataAY_UAS) > 0) {
                $showUAS = true;
            }
        }

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {
                $dt = $data[$i];

                if ($System == 1) {

                    if ($dt['StatusSystem'] == '1') {
                        if (($showUTS && $dt['Approval'] == '1') || ($showUTS && $dt['Approval'] == '2') || ($showUAS && $dt['Approval'] == '2')) {

                            for ($d = 1; $d <= 5; $d++) {
                                $n = '-';
                                if ($d <= $dt['TotalAssigment'] && $dt['Evaluasi' . $d] != null && $dt['Evaluasi' . $d] != '') {
                                    $n = $data[$i]['Evaluasi' . $d];
                                }
                                $data[$i]['Evaluasi' . $d] = $n;
                            }

                            if ($dt['UTS'] == null || $dt['UTS'] == '') {
                                $data[$i]['UTS'] = 0;
                            }

                            // ======== UAS =========
                            if ($showUAS && $dt['Approval'] == '2') {
                                if ($dt['UAS'] == null || $dt['UAS'] == '') {
                                    $data[$i]['UAS'] = 0;
                                }

                                if ($dt['Score'] == null || $dt['Score'] == '') {
                                    $data[$i]['Score'] = 0;
                                }
                                if ($dt['Grade'] == null || $dt['Grade'] == '') {
                                    $data[$i]['Grade'] = 'E';
                                }
                                if ($dt['GradeValue'] == null || $dt['GradeValue'] == '') {
                                    $data[$i]['GradeValue'] = 0;
                                }
                            } else {
                                $data[$i]['UAS'] = '-';
                                $data[$i]['Score'] = '-';
                                $data[$i]['Grade'] = '-';
                                $data[$i]['GradeValue'] = 0;
                            }
                            // ======== UAS =========

                        } else {

                            $data[$i]['Evaluasi1'] = '-';
                            $data[$i]['Evaluasi2'] = '-';
                            $data[$i]['Evaluasi3'] = '-';
                            $data[$i]['Evaluasi4'] = '-';
                            $data[$i]['Evaluasi5'] = '-';
                            $data[$i]['UTS'] = '-';
                            $data[$i]['UAS'] = '-';
                            $data[$i]['Score'] = '-';
                            $data[$i]['Grade'] = '-';
                            $data[$i]['GradeValue'] = 0;
                        }
                    } else {
                        if ($dt['Evaluasi1'] == null || $dt['Evaluasi1'] == '') {
                            $data[$i]['Evaluasi1'] = '-';
                        }
                        if ($dt['Evaluasi2'] == null || $dt['Evaluasi2'] == '') {
                            $data[$i]['Evaluasi2'] = '-';
                        }
                        if ($dt['Evaluasi3'] == null || $dt['Evaluasi3'] == '') {
                            $data[$i]['Evaluasi3'] = '-';
                        }
                        if ($dt['Evaluasi4'] == null || $dt['Evaluasi4'] == '') {
                            $data[$i]['Evaluasi4'] = '-';
                        }
                        if ($dt['Evaluasi5'] == null || $dt['Evaluasi5'] == '') {
                            $data[$i]['Evaluasi5'] = '-';
                        }

                        if ($dt['UTS'] == null || $dt['UTS'] == '') {
                            $data[$i]['UTS'] = '-';
                        }


                        if ($dt['UAS'] == null || $dt['UAS'] == '') {
                            $data[$i]['UAS'] = 0;
                        }
                        if ($dt['Score'] == null || $dt['Score'] == '') {
                            $data[$i]['Score'] = 0;
                        }
                        if ($dt['Grade'] == null || $dt['Grade'] == '') {
                            $data[$i]['Grade'] = 'E';
                        }
                        if ($dt['GradeValue'] == null || $dt['GradeValue'] == '') {
                            $data[$i]['GradeValue'] = 0;
                        }
                    }
                } else {
                    if ($dt['Evaluasi1'] == null || $dt['Evaluasi1'] == '') {
                        $data[$i]['Evaluasi1'] = '-';
                    }
                    if ($dt['Evaluasi2'] == null || $dt['Evaluasi2'] == '') {
                        $data[$i]['Evaluasi2'] = '-';
                    }
                    if ($dt['Evaluasi3'] == null || $dt['Evaluasi3'] == '') {
                        $data[$i]['Evaluasi3'] = '-';
                    }
                    if ($dt['Evaluasi4'] == null || $dt['Evaluasi4'] == '') {
                        $data[$i]['Evaluasi4'] = '-';
                    }
                    if ($dt['Evaluasi5'] == null || $dt['Evaluasi5'] == '') {
                        $data[$i]['Evaluasi5'] = '-';
                    }

                    if ($dt['UTS'] == null || $dt['UTS'] == '') {
                        $data[$i]['UTS'] = '-';
                    }


                    if ($dt['UAS'] == null || $dt['UAS'] == '') {
                        $data[$i]['UAS'] = 0;
                    }
                    if ($dt['Score'] == null || $dt['Score'] == '') {
                        $data[$i]['Score'] = 0;
                    }
                    if ($dt['Grade'] == null || $dt['Grade'] == '') {
                        $data[$i]['Grade'] = 'E';
                    }
                    if ($dt['GradeValue'] == null || $dt['GradeValue'] == '') {
                        $data[$i]['GradeValue'] = 0;
                    }
                }
            }
        }

        return $data;
    }



    public function getTopicByUserID($UserID)
    {

        $data = $this->db->query('SELECT cu.ReadComment, ct.* FROM db_academic.counseling_user cu
                                              LEFT JOIN db_academic.counseling_topic ct
                                              ON (ct.ID = cu.TopicID)
                                              WHERE cu.UserID = "' . $UserID . '"
                                               ORDER BY cu.TopicID DESC')->result_array();

        return $data;
    }

    public function uploadDokumenMultiple($filename, $ggFiles = 'fileData', $path = './uploads/document')
    {
        $output = array();
        // Count total files
        if (count($_FILES) > 0) {
            $countfiles = count($_FILES[$ggFiles]['name']);
            // Looping all files
            for ($i = 0; $i < $countfiles; $i++) {
                $config = array();
                if (!empty($_FILES[$ggFiles]['name'][$i])) {

                    // Define new $_FILES array - $_FILES['file']
                    $_FILES['file']['name'] = $_FILES[$ggFiles]['name'][$i];
                    $_FILES['file']['type'] = $_FILES[$ggFiles]['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES[$ggFiles]['tmp_name'][$i];
                    $_FILES['file']['error'] = $_FILES[$ggFiles]['error'][$i];
                    $_FILES['file']['size'] = $_FILES[$ggFiles]['size'][$i];

                    // Set preference
                    $config['upload_path'] = $path . '/';
                    $config['allowed_types'] = '*';
                    $config['overwrite'] = TRUE;
                    $no = $i + 1;
                    $config['file_name'] = $filename . '_' . $no;

                    $filenameUpload = $_FILES['file']['name'];
                    $ext = pathinfo($filenameUpload, PATHINFO_EXTENSION);

                    // $filenameNew = $filename.'_'.$no.'.pdf';
                    $filenameNew = $filename . '_' . $no . '_' . mt_rand() . '.' . $ext;
                    // print_r($_FILES['file']['type']);


                    //Load upload library
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    // File upload
                    if ($this->upload->do_upload('file')) {
                        // Get data about the file
                        $uploadData = $this->upload->data();
                        $filePath = $uploadData['file_path'];
                        $filename_uploaded = $uploadData['file_name'];
                        // rename file
                        $old = $filePath . '/' . $filename_uploaded;
                        $new = $filePath . '/' . $filenameNew;

                        rename($old, $new);

                        $output[] = $filenameNew;
                    }
                }
            }
        }

        return $output;
    }

    public function count_get_schedule_exchange_by_status($Status, $Semester)
    {
        $Status = ($Status == '') ? '' : ' where a.Status = "' . $Status . '"';
        $Semester = ($Semester == '') ? '' : ($Status == '') ? ' where b.SemesterID = "' . $Semester . '"' : ' and b.SemesterID = "' . $Semester . '"';
        $sql = 'select count(*) as total from db_academic.schedule_exchange as a left join db_academic.attendance as b
                on a.ID_Attd = b.ID
                 ' . $Status . $Semester;
        $query = $this->db->query($sql)->result_array();
        return $query[0]['total'];
    }

    public function getSessionByID_Attd($ID_Attd)
    {

        $dataAttendance = $this->db->limit(1)->get_where(
            'db_academic.attendance',
            array('ID' => $ID_Attd)
        )->result_array();

        $Meet = 1;
        if (count($dataAttendance) > 0) {
            $dAttd = $dataAttendance[0];

            for ($m = 1; $m <= 14; $m++) {
                $Meet = $m;
                if ($dAttd['Meet' . $m] == null || $dAttd['Meet' . $m] == 0 || $dAttd['Meet' . $m] == '0') {

                    // Cek apakah ada kelas pengganti atau tidak
                    $dataExc = $this->db->limit(1)->get_where(
                        'db_academic.schedule_exchange',
                        array('ID_Attd' => $ID_Attd, 'Meeting' => $Meet, 'Status' => '2')
                    )->result_array();

                    if (count($dataExc) <= 0) {
                        break;
                    }
                } else if ($dAttd['Meet' . $m] == '1' || $dAttd['Meet' . $m] == 1) {
                    // cek apakah ada dosen yang sudah terlebih dahulu absen
                    $dataLect = $this->db->get_where(
                        'db_academic.attendance_lecturers',
                        array(
                            'ID_Attd' => $ID_Attd,
                            'Meet' => $Meet,
                            'Date' => $this->getDateNow()
                        )
                    )->result_array();

                    if (count($dataLect) > 0) {
                        break;
                    }
                }
            }
        }

        return $Meet;
    }

    public function getScheduleBYNPM($SemesterID, $DB_, $NPM)
    {

        //        $NPM = $this->session->userdata('student_NPM');
        //        $SemesterID = $this->session->userdata('student_SemesterID');
        //        $DB_ = $this->session->userdata('student_DB');

        $data = $this->db->select('ScheduleID')->get_where(
            $DB_ . '.study_planning',
            array('NPM' => $NPM, 'SemesterID' => $SemesterID)
        )->result_array();

        $result = [];
        if (count($data) > 0) {
            foreach ($data as $item) {
                $dataSch = $this->db->query('SELECT sdc.MKID,sdc.ScheduleID,mk.MKCode, mk.Name, mk.NameEng FROM db_academic.schedule_details_course sdc
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                    WHERE sdc.ScheduleID = "' . $item['ScheduleID'] . '" GROUP BY sdc.ScheduleID ')->result_array();


                $dsn = $this->getLecturerByScheduleID($item['ScheduleID']);

                $totalStatus = 0;
                // cek apakah mhs sudah mengisi edom untuk dosen
                if (count($dsn) > 0) {
                    for ($c = 0; $c < count($dsn); $c++) {
                        $dataEdom = $this->db->get_where(
                            'db_academic.edom_answer',
                            array(
                                'SemesterID' => $SemesterID, 'ScheduleID' => $item['ScheduleID'],
                                'NPM' => $NPM, 'NIP' => $dsn[$c]['NIP']
                            ),
                            1
                        )->result_array();

                        $sts = (count($dataEdom) > 0) ? 1 : 0;
                        $dsn[$c]['Status'] = $sts;

                        $totalStatus = $totalStatus + $sts;
                    }
                }



                $dataSch[0]['TotalLecturer'] = count($dsn);
                $dataSch[0]['EdomAnswer'] = $totalStatus;
                $dataSch[0]['Lecturer'] = $dsn;
                array_push($result, $dataSch[0]);
            }
        }

        return $result;
    }

    private function getLecturerByScheduleID($ScheduleID)
    {

        //        $ScheduleID = $this->input->get('ScheduleID');
        $data = $this->db->query('SELECT em.NIP, em.Name, s.TeamTeaching FROM db_academic.schedule s
                                            LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                            WHERE s.ID="' . $ScheduleID . '" LIMIT 1')->result_array();

        if (count($data) > 0 && $data[0]['TeamTeaching'] == '1') {
            $dataTeam = $this->db->query('SELECT em.NIP, em.Name FROM db_academic.schedule_team_teaching stt
                                                    LEFT JOIN db_employees.employees em ON (em.NIP=stt.NIP)
                                                    WHERE stt.ScheduleID = "' . $ScheduleID . '" ORDER BY stt.NIP ASC ')->result_array();
            if (count($dataTeam) > 0) {
                for ($i = 0; $i < count($dataTeam); $i++) {
                    array_push($data, $dataTeam[$i]);
                }
            }
        }

        return $data;
    }


    public function getRangeDateLearningOnlinePerSession($ScheduleID, $Session)
    {

        $data = $this->getRangeDateLearningOnline($ScheduleID);

        $result = [];
        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['Session'] == $Session) {

                $dataRest = $this->getDetailLearningOnline($ScheduleID, $Session);

                $data[$i]['TotalComment'] = $dataRest['TotalComment'];
                $data[$i]['CheckTopik'] = $dataRest['CheckTopik'];
                $data[$i]['TotalTask'] = $dataRest['TotalTask'];
                $data[$i]['CheckTask'] = $dataRest['CheckTask'];
                $data[$i]['dataMaterial'] = $dataRest['dataMaterial'];

                $result = $data[$i];

                break;
            }
        }

        return $result;
    }

    private function getDetailLearningOnline($ScheduleID, $Session)
    {
        $i = 0;
        // Comment
        $data[$i]['TotalComment'] = $this->db->query('SELECT COUNT(*) AS Total
                                                    FROM db_academic.counseling_comment cc
                                                    LEFT JOIN db_academic.counseling_topic ct ON (ct.ID = cc.TopicID)
                                                    WHERE ct.ScheduleID = "' . $ScheduleID . '"
                                                    AND ct.Sessions = "' . $Session . '" ')->result_array()[0]['Total'];

        // cek apakah topik sudah dibuat atau blm
        $data[$i]['CheckTopik'] = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.counseling_topic ct
                                                                        WHERE ct.ScheduleID = "' . $ScheduleID . '"
                                                                        AND ct.Sessions = "' . $Session . '" ')->result_array()[0]['Total'];

        // Task
        $data[$i]['TotalTask'] = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.schedule_task_student std
                                                                    LEFT JOIN db_academic.schedule_task st ON (st.ID = std.IDST)
                                                                    WHERE st.ScheduleID = "' . $ScheduleID . '"
                                                                     AND st.Session = "' . $Session . '" ')->result_array()[0]['Total'];

        // cek apakah task sudah dibuat atau blm
        $data[$i]['CheckTask'] = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.schedule_task st
                                                                    WHERE st.ScheduleID = "' . $ScheduleID . '"
                                                                     AND st.Session = "' . $Session . '" ')->result_array()[0]['Total'];

        // Material
        $data[$i]['dataMaterial'] = $this->db->query('SELECT sm.File FROM db_academic.schedule_material sm
                                                                    WHERE sm.ScheduleID = "' . $ScheduleID . '"
                                                                     AND sm.Session = "' . $Session . '" ')->result_array();

        return $data[$i];
    }

    public function getRangeDateLearningOnline($ScheduleID)
    {

        $dataSch = $this->db->query('SELECT d.NumberOfDay, ay.kuliahStart , ay.utsEnd, s.SemesterID FROM db_academic.schedule_details sd
                                        LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                        LEFT JOIN db_academic.schedule s ON (s.ID = sd.ScheduleID)
                                        LEFT JOIN db_academic.academic_years ay ON (ay.SemesterID = s.SemesterID)
                                        WHERE sd.ScheduleID = "' . $ScheduleID . '"
                                         ORDER BY sd.DayID ASC LIMIT 1 ')->result_array();


        $Day = $dataSch[0]['NumberOfDay'];



        $dateRangeStart = $dataSch[0]['kuliahStart']; // Start kuliah
        $dateRangeStart_AfterUTS = $dataSch[0]['utsEnd']; // End of UTS


        $dateStart = $this->getFirstDatelearningOnline($dateRangeStart, $Day);
        $f = $this->getRangeDateMidSemester(0, $dateStart, $dataSch[0]['SemesterID']);

        $dateStart_AfterUTS = $this->getFirstDatelearningOnline($dateRangeStart_AfterUTS, $Day);
        $f2 = $this->getRangeDateMidSemester(1, $dateStart_AfterUTS, $dataSch[0]['SemesterID']);
        if (count($f2) > 0) {
            for ($i = 0; $i < count($f2); $i++) {
                $f2[$i]['Session'] = $i + 8;
                array_push($f, $f2[$i]);
            }
        }


        $dateNow = date('Y-m-d');
        // Cek per session
        for ($c = 0; $c < count($f); $c++) {

            // Cek apakah ada di setting manual atau tidak
            $dataManual = $this->db->get_where('db_academic.schedule_online', array(
                'ScheduleID' => $ScheduleID,
                'Session' => $f[$c]['Session']
            ))->result_array();

            if (count($dataManual) > 0) {

                $Status = 0;
                if ($dataManual[0]['DateStart'] <= $dateNow && $dataManual[0]['DateEnd'] >= $dateNow) {
                    $Status = 1;
                } else if ($dataManual[0]['DateEnd'] <= $dateNow) {
                    $Status = 2;
                }

                $f[$c]['RangeStart'] =  $dataManual[0]['DateStart'];
                $f[$c]['RangeEnd'] =  $dataManual[0]['DateEnd'];
                $f[$c]['Status'] =  $Status;
            }

            $f[$c]['ManualSet'] = (count($dataManual) > 0) ? 1 : 0;
        }

        return $f;
    }

    public function getRangeDateMidSemester($afterUTS, $dateStart, $SemesterID)
    {

        // Cek apakah sedang dalam Ujian Atau tidak
        $isUTS = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.academic_years ay
                                                            LEFT JOIN db_academic.semester s
                                                            ON (s.ID = ay.SemesterID)
                                                            WHERE s.Status = 1 AND
                                                            (CURDATE() BETWEEN ay.utsStart AND ay.utsEnd)')
            ->result_array()[0]['Total'];

        // Get setting total session
        $totalSession = $this->db->query('SELECT ay.totalSession FROM db_academic.academic_years ay 
                                                WHERE ay.SemesterID = "' . $SemesterID . '"')->result_array()[0]['totalSession'];

        $dateNow = date('Y-m-d');
        $newStartDate = $dateStart;
        $arrResult = [];

        $forLoop = ($afterUTS == 1) ? floor((int) $totalSession / 2) : ceil((int) $totalSession / 2);

        // print_r(floor(16 / 2));
        //         print_r(ceil(16 / 2));
        //         exit();

        for ($s = 0; $s < $forLoop; $s++) {
            $RangeEnd = date("Y-m-d", strtotime($newStartDate . " +6 days"));

            $Status = 0;
            if ($newStartDate <= $dateNow && $RangeEnd >= $dateNow) {
                $Status = 1;
            } else if ($RangeEnd <= $dateNow) {
                $Status = 2;
            }



            $arr = array(
                'Session' => ($s + 1),
                'RangeStart' => $newStartDate,
                'RangeEnd' => $RangeEnd,
                'Status' => $Status,
                'isUTS' => $isUTS
            );
            $newStartDate = date("Y-m-d", strtotime($RangeEnd . " +1 days"));
            array_push($arrResult, $arr);
        }
        return $arrResult;
    }

    public function getFirstDatelearningOnline($StartDate, $DayNumber)
    {
        $result = '';
        for ($i = 0; $i <= 7; $i++) {
            $dtNow = date("Y-m-d", strtotime($StartDate . " +" . $i . " days"));
            $dtNumber = date("N", strtotime($StartDate . " +" . $i . " days"));
            if ($DayNumber == $dtNumber) {
                $result = $dtNow;
                break;
            }
        }
        return $result;
    }

    public function checkKelengkapanLearningOnline($ScheduleID, $Session)
    {
        $i = 0;
        // Material
        $data[$i]['dataMaterial'] = $this->db->query('SELECT sm.File FROM db_academic.schedule_material sm
                                                                    WHERE sm.ScheduleID = "' . $ScheduleID . '"
                                                                     AND sm.Session = "' . $Session . '" ')->result_array();

        // cek apakah topik sudah dibuat atau blm
        $data[$i]['CheckTopik'] = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.counseling_topic ct
                                                                        WHERE ct.ScheduleID = "' . $ScheduleID . '"
                                                                        AND ct.Sessions = "' . $Session . '" ')->result_array()[0]['Total'];

        // Task
        $data[$i]['CheckTask'] = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.schedule_task st
                                                                    WHERE st.ScheduleID = "' . $ScheduleID . '"
                                                                     AND st.Session = "' . $Session . '" ')->result_array()[0]['Total'];

        // Quiz
        $data[$i]['CheckQuiz'] = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.q_quiz st
                                                                    WHERE st.ScheduleID = "' . $ScheduleID . '"
                                                                     AND st.Session = "' . $Session . '" ')->result_array()[0]['Total'];

        return $data[0];
    }

    public function getAllLecturerByScheduleID($ScheuldeID)
    {

        $data = $this->db->query('SELECT em.NIP, em.Name FROM db_academic.schedule s
                                            LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                            WHERE s.ID = "' . $ScheuldeID . '"
                                             UNION ALL
                                             SELECT em.NIP, em.Name FROM db_academic.schedule_team_teaching stt
                                             LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                             WHERE stt.ScheduleID = "' . $ScheuldeID . '"
                                             ')->result_array();

        return $data;
    }
}
