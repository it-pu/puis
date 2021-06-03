<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_jadwal_ujian extends Academic_Controler
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('akademik/m_jadwal_ujian');
        $this->load->model('m_rest');
    }


    public function temp($content)
    {
        parent::template($content);
    }

    public function menu_jadwalUjian($page)
    {
        $data['department'] = parent::__getDepartement();
        $data['page'] = $page;
        $content = $this->load->view('page/' . $data['department'] . '/jadwalujian/menu_jadwal_ujian', $data, true);
        $this->temp($content);
    }


    public function list_exam()
    {
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/' . $data['department'] . '/jadwalujian/list_exam', $data, true);
        $this->menu_jadwalUjian($page);
    }

    // added by adhi 2020-03-30
    public function editExamSubmited($token)
    {
        try {
            $key = "UAP)(*";
            $data_arr = (array) $this->jwt->decode($token, $key);
            $data['department'] = parent::__getDepartement();
            $data['NPM'] = $data_arr['NPM'];
            $data['ExamID'] = $data_arr['ExamID'];
            $data['dataExisting'] = $this->db->query(
                'select * from db_academic.exam_student_online where ExamID = ' . $data_arr['ExamID'] . ' and NPM = "' . $data_arr['NPM'] . '" '
            )->result_array();
            $page = $this->load->view('page/' . $data['department'] . '/jadwalujian/editExamSubmited', $data, true);
            $this->menu_jadwalUjian($page);
        } catch (Exception $e) {
            redirect(base_url() . 'academic/exam-schedule');
        }
    }

    public function set_exam_schedule()
    {
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/' . $data['department'] . '/jadwalujian/set_exam_schedule', $data, true);
        $this->menu_jadwalUjian($page);
    }

    public function exam_setting()
    {
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/' . $data['department'] . '/jadwalujian/exam_setting', $data, true);
        $this->menu_jadwalUjian($page);
    }

    public function exam_quiz()
    {
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/' . $data['department'] . '/jadwalujian/exam_quiz', $data, true);
        $this->menu_jadwalUjian($page);
    }

    public function exam_quiz_create()
    {
        $data['department'] = parent::__getDepartement();

        $dataQuiz = [];

        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $dataQuiz = $this->db->query('SELECT * FROM db_academic.q_quiz q WHERE q.ID = "' . $id . '"')
                ->result_array();

            if (count($dataQuiz) > 0) {
                $dataExamQuiz = $this->db->get_where('db_academic.q_exam', array('QuizID' => $id))->result_array();
                $valueExamQuiz = [];
                if (count($dataExamQuiz) > 0) {
                    foreach ($dataExamQuiz as $item) {
                        array_push($valueExamQuiz, json_decode($item['ExamDetail']));
                    }
                }
                $dataQuiz[0]['ExamQuiz'] = $valueExamQuiz;
            }
        }

        $data['dataQuiz'] = $dataQuiz;

        $page = $this->load->view('page/' . $data['department'] . '/jadwalujian/exam_quiz_create', $data, true);
        $this->menu_jadwalUjian($page);
    }

    public function list_waiting_approve()
    {
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/' . $data['department'] . '/jadwalujian/list_waiting_approve', $data, true);
        $this->menu_jadwalUjian($page);
    }

    public function edit_exam_schedule($ExamID)
    {
        $data['department'] = parent::__getDepartement();

        $arrExam = $this->m_jadwal_ujian->__getExam($ExamID);

        if (count($arrExam) > 0) {

            $data['arrExam'] = $arrExam;

            $dateNow = $this->m_rest->getDateNow();
            $viewPage = ($dateNow < $arrExam[0]['ExamDate']) ? 1 : 0;
            $data['ViewPage'] = $viewPage;

            $page = $this->load->view('page/' . $data['department'] . '/jadwalujian/edit_exam_schedule', $data, true);
            $this->menu_jadwalUjian($page);
        } else {
            echo "Not authorized";
        }
    }



    // Jadwal Ujian Lama
    public function index()
    {
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/' . $data['department'] . '/jadwalujian/tab_jadwalujian', $data, true);
        $this->temp($content);
    }

    public function setPageJadwal()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);

        $page = $data_arr['page'];
        $ScheduleID = $data_arr['ScheduleID'];

        $department = parent::__getDepartement();
        $path = 'page/' . $department . '/jadwalujian';

        $this->cekFileView($path, $page, $ScheduleID);
    }

    private function cekFileView($path, $file, $ScheduleID)
    {

        $data = false;
        if (file_exists(APPPATH . "views/" . $path . "/{$file}.php")) {
            $dataView['ScheduleID'] = $ScheduleID;
            $data = $this->load->view($path . '/' . $file, $dataView);
        }

        return $data;
    }

    // ==== Barcode ===

    public function exam_barcode()
    {

        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/' . $data['department'] . '/jadwalujian/exam_barcode', $data, true);
        $this->menu_jadwalUjian($page);
    }


    public function live_chat($ExamID)
    {

        $data['department'] = parent::__getDepartement();
        $data['ExamID'] = $ExamID;

        $page = $this->load->view('page/' . $data['department'] . '/jadwalujian/live_chat', $data, true);
        $this->menu_jadwalUjian($page);
    }

    public function submiteditExamSubmited()
    {
        header('Content-Type: application/json');
        $dataToken = $this->getInputToken();
        $dataToken = json_decode(json_encode($dataToken), true);
        $action = $dataToken['action'];
        $rs = ['status' => 0, 'msg' => '', 'callback' => []];
        switch ($action) {
            case 'add_or_edit':
                $dataSave = $dataToken['data'];
                if (array_key_exists('Files', $_FILES)) {
                    $filename = str_replace(' ', '_', $_FILES['Files']['name'][0]);
                    $path = './uploads/task-exam/';
                    if ($_SERVER['SERVER_NAME'] != 'pcam.podomorouniversity.ac.id') {
                        $uploadFile = $this->m_master->uploadDokumenSetFileName($filename, 'Files', $path);
                    }
                    else
                    {
                        // upload files.podomoro
                        $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                        $path = 'task-exam';
                        $TheFile = 'Files';
                        $uploadFile = $this->m_master->UploadManyFilesToNas($headerOrigin, $filename, $TheFile, $path, 'array');
                    }

                    $dataSave['File'] = $uploadFile[0];
                }

                $dataSave['SavedAt'] = date('Y-m-d H:i:s');

                // check add or edit
                $total = $this->db->query(
                    'select count(*) as total  from 
                        (
                            select 1 from db_academic.exam_student_online where
                            ExamID = ' . $dataToken['ExamID'] . ' and NPM = "' . $dataToken['NPM'] . '"
                        )xx
                         
                        '
                )->row()->total;
                // print_r($total);die();
                if ($total > 0) { // edit
                    $this->db->where('NPM', $dataToken['NPM']);
                    $this->db->where('ExamID', $dataToken['ExamID']);
                    $this->db->update('db_academic.exam_student_online', $dataSave);
                } else {
                    $dataSave['NPM'] = $dataToken['NPM'];
                    $dataSave['ExamID'] = $dataToken['ExamID'];
                    $dataSave['StartWorking'] = $dataSave['SavedAt'];
                    // print_r($dataSave);die();
                    $this->db->insert('db_academic.exam_student_online', $dataSave);
                }
                $rs['status'] = 1;
                break;

            default:
                # code...
                break;
        }
        echo json_encode($rs);
    }
}
