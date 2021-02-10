<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_api_survey extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('m_search');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('akademik/m_onlineclass', 'm_oc');
        $this->load->model('master/m_master');
        $this->load->library('JWT');
        $this->load->library('google');

        date_default_timezone_set("Asia/Jakarta");
        setlocale(LC_ALL, 'id_ID.UTF8', 'id_ID.UTF-8', 'id_ID.8859-1', 'id_ID', 'IND.UTF8', 'IND.UTF-8', 'IND.8859-1', 'IND', 'Indonesian.UTF8', 'Indonesian.UTF-8', 'Indonesian.8859-1', 'Indonesian', 'Indonesia', 'id', 'ID', 'en_US.UTF8', 'en_US.UTF-8', 'en_US.8859-1', 'en_US', 'American', 'ENG', 'English');
    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "s3Cr3T-G4N";
        $data_arr = (array) $this->jwt->decode($token, $key);
        return $data_arr;
    }

    private function getInputToken2()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);
        return $data_arr;
    }

    public function crudSurvey()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'getDataSurvey') {
            $SurveyID = $data_arr['SurveyID'];
            $data = $this->db->get_where('db_it.surv_survey', array('ID' => $SurveyID))->result_array();

            $result = array('Status' => 0);
            if (count($data) > 0) {

                $data[0]['Quesions'] = $this->db->query('SELECT sq.Question, sq.IsRequired, sq.AnswerType, 
                                                        sq.QTID, ssd.QuestionID
                                                        FROM db_it.surv_survey_detail ssd 
                                                        LEFT JOIN db_it.surv_question sq ON (sq.ID = ssd.QuestionID)
                                                        WHERE ssd.SurveyID = "' . $SurveyID . '" 
                                                        ORDER BY ssd.Queue ASC ')->result_array();

                $result = array('Status' => 1, 'Data' => $data[0]);
            }

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'setDataSurvey') {

            // Insert to survey answer
            $InsAnswer = (array) $data_arr['InsAnswer'];
            $this->db->insert('db_it.surv_answer', $InsAnswer);
            $insert_id = $this->db->insert_id();

            $dataAnsw = (array) $data_arr['dataAnsw'];

            for ($i = 0; $i < count($dataAnsw); $i++) {
                $d = (array) $dataAnsw[$i];
                $d['AnswerID'] = $insert_id;
                $this->db->insert('db_it.surv_answer_detail', $d);
            }

            // Send email
            if (isset($InsAnswer['SendFeedbackToEmail']) && ($InsAnswer['SendFeedbackToEmail'] == 1 || $InsAnswer['SendFeedbackToEmail'] == '1')) {
                $Email = $InsAnswer['Email'];
                if ($Email != '' && $Email != null) {

                    $this->load->model('m_sendemail');

                    $to = $Email;
                    //                    $to = 'nndg.ace3@gmail.com';
                    $SurveyID = $InsAnswer['SurveyID'];

                    // Get survey title
                    $dataSurvey = $this->db->get_where(
                        'db_it.surv_survey',
                        array('ID' => $SurveyID)
                    )->result_array();

                    $subject = 'Feedback - ' . $dataSurvey[0]['Title'] . ' | ' . $this->m_rest->getDateTimeNow();

                    $key = "s3Cr3T-G4N";
                    $tokenUser = $this->jwt->encode(array('AnswerID' => $insert_id), $key);

                    $text = '<p style="color: #673AB7;text-align: center;"><strong>"' . $dataSurvey[0]['Title'] . '"</strong></p>
                        <table width="178" cellspacing="0" cellpadding="12" border="0">
                            <tbody>
                            <tr>
                                <td bgcolor="#4caf50" align="center">
                                    <a href="' . url_sign_out . 'survey/my-answer/' . $tokenUser . '" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color:#4caf50" target="_blank" >Show My Answer</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <br/>';

                    $this->m_sendemail->sendEmail($to, $subject, null, null, null, null, $text, null, 'Survey Result');
                }
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'getEmailUserSurvey') {

            $Type = $data_arr['Type'];

            $Email = '';

            if ($Type == 'emp') {
                $data = $this->db->query('SELECT CASE 
                                            WHEN EmailPU IS NOT NULL AND EmailPU != "" THEN EmailPU
                                            WHEN Email IS NOT NULL  AND Email != "" THEN  Email
                                            ELSE "" END AS Email
                                            FROM db_employees.employees 
                        WHERE NIP = "' . $data_arr['Username'] . '" ')->result_array();

                $Email = (count($data) > 0) ? $data[0]['Email'] : '';
            } else if ($Type == 'std') {

                $data = $this->db->select('EmailPU')->get_where('db_academic.auth_students', array(
                    'NPM' => $data_arr['Username']
                ))->result_array();

                $Email = (count($data) > 0) ? $data[0]['EmailPU'] : '';
            } else if ($Type == 'other') {

                $data = $this->db->select('Email')
                    ->get_where(
                        'db_it.surv_external_user',
                        array('ID' => $data_arr['Username'])
                    )->result_array();

                $Email = (count($data) > 0) ? $data[0]['Email'] : '';
            }

            $Status = ($Email != '') ? 1 : 0;

            return print_r(json_encode(array(
                'Status' => $Status,
                'Email' => $Email
            )));
        } else if ($data_arr['action'] == 'getListDirection') {

            $data = $this->db->get_where('db_it.surv_direct', array('Username' => $data_arr['Username']))->result_array()[0];

            return print_r(json_encode($data));
        }
    }
}
