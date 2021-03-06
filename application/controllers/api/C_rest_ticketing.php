<?php
header('Content-Type: application/json');
defined('BASEPATH') OR exit('No direct script access allowed');

class C_rest_ticketing extends CI_Controller {
    public $data = [];
    private $keyToken = 's3Cr3T-G4N';
    function __construct(){
        parent::__construct();
        $this->load->model('master/m_master');
        $this->load->model('ticketing/m_general');
        $this->load->model('ticketing/m_setting');
        $this->load->model('ticketing/m_ticketing');
        $this->load->library('JWT');
        try {
          $G_setting = $this->m_master->showData_array('db_ticketing.rest_setting');
          if (!$this->auth($G_setting)) {
            echo '{"status":"999","message":"Not Authenfication"}'; 
            die();
          }
          else
          {
            header('Access-Control-Allow-Origin: *');
          }
        } catch (Exception $e) {
          echo json_encode($e);
          die();
        }
    }

    private function auth($G_setting){
      $Bool = false;
      try {
        $dataToken = $this->getInputToken();
        $getallheaders = getallheaders();
        foreach ($getallheaders as $name  => $value) {
          if ($name == 'Hjwtkey' && $value == $G_setting[0]['Hjwtkey']) {
            // cek api get
            if(isset($_GET['apikey']) && $_GET['apikey'] == $G_setting[0]['Apikey'] && array_key_exists("auth",$dataToken) &&  $dataToken['auth'] == $this->keyToken ) {
                $Bool = true;
                break;
            } 
          }
        }

        return $Bool;
      } catch (Exception $e) {
         echo json_encode($e);
         die();
      }

      return false;
    }

    private function __addHttpOrhttps($arr){
      $rs = [];
      for ($i=0; $i < count($arr); $i++) { 
        $rs[] = 'http://'.$arr[$i];
        $rs[] = 'https://'.$arr[$i];
      }
      return $rs;
    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        $data_arr = json_decode(json_encode($data_arr),true);
        return $data_arr;
    }

    public function CRUDCategory(){
      try {
        $dataToken = $this->getInputToken();
        $action = $dataToken['action'];
        switch ($action) {
          case 'read':
            $rs = $this->m_setting->LoadDataCategory($dataToken);
            echo json_encode($rs);
            break;
          case 'add':
            $rs = $this->m_setting->ActionTable('add',$dataToken,'db_ticketing.category');
            echo json_encode($rs);
            break;
          case 'delete' :
             // check ID_category sudah digunakan atau belum
             $ID = $dataToken['ID'];
             $sqlChek = $this->db->query(
              'select count(*) as total from (
                select 1 from db_ticketing.ticket where CategoryID = '.$ID.'
                UNION ALL
                select 1 from db_ticketing.received where CategoryReceivedID = '.$ID.'
              )xx

              '
             )->result_array();
             if ($sqlChek[0]['total'] == 0) {
               $rs = $this->m_setting->ActionTable('delete',$dataToken,'db_ticketing.category');
             }
             else
             {
                $rs = ['status' => 0,'msg' => 'Data has been using transaction'];
             }
             
             echo json_encode($rs);
            break;
          case 'edit' : 
             $rs = $this->m_setting->ActionTable('edit',$dataToken,'db_ticketing.category');
             echo json_encode($rs);
            break;
        }
        // end switch

      } catch (Exception $e) {
        echo json_encode($e);
      }
    }

    public function AutocompleteEmployees()
    {
      try {
         $dataToken = $this->getInputToken();
         $data['response'] = 'true'; //mengatur response
         $data['message'] = array(); //membuat array 
         $getData = $this->m_general->getAllUserAutoComplete($dataToken);
         for ($i=0; $i < count($getData); $i++) {
             $data['message'][] = array(
                 'label' => $getData[$i]['Name'],
                 'value' => $getData[$i]['NIP']
             );
         }
         echo json_encode($data);
      } catch (Exception $e) {
        echo json_encode($e);
      }
    }

    public function CRUDAdmin(){
      try {
        $dataToken = $this->getInputToken();
        $dataToken = json_decode(json_encode($dataToken),true);
        $action = $dataToken['action'];
        switch ($action) {
          case 'read':
            $rs = $this->m_setting->LoadDataAdmin($dataToken);
            echo json_encode($rs);
            break;
          case 'add':
            $rs = $this->m_setting->ActionTable('add',$dataToken,'db_ticketing.admin_register');
            echo json_encode($rs);
            break;
          case 'delete' : 
             $rs = $this->m_setting->ActionTable('remove',$dataToken,'db_ticketing.admin_register');
             echo json_encode($rs);
            break;
          case 'edit' : 
             $rs = $this->m_setting->ActionTable('edit',$dataToken,'db_ticketing.admin_register');
             echo json_encode($rs);
            break;
          case 'read_auth_dashboard' : 
             $rs = $this->m_setting->LoadDataAuthDashboard($dataToken);
             echo json_encode($rs);
            break;
          case 'auth_dashboard' : 
             $subdata = $dataToken['subdata'];
             $actionSubdata = $subdata['action'];
             switch ($actionSubdata) {
               case 'edit':
                 $rs = $this->m_setting->ActionTable('edit',$subdata,'db_ticketing.auth_dashboard');
                 echo json_encode($rs);
                 break;
               case 'add':
                 $rs = $this->m_setting->ActionTable('add',$subdata,'db_ticketing.auth_dashboard');
                 echo json_encode($rs);
                 break;
                case 'delete' : 
                   $rs = $this->m_setting->ActionTable('remove',$subdata,'db_ticketing.auth_dashboard');
                   echo json_encode($rs);
                  break;
             }
            break;
        }
        // end switch

      } catch (Exception $e) {
        echo json_encode($e);
      }
    }

    private function jwt_url_action($CategoryID){
      $G_category = $this->m_master->caribasedprimary('db_ticketing.category','ID',$CategoryID);
      $DepartmentID = $G_category[0]['DepartmentID'];
      $token = $this->jwt->encode($DepartmentID,"UAP)(*");
      return $token;
    }

    public function event_ticketing(){
      try {
        $dataToken = $this->getInputToken();
        $action = $dataToken['action'];
        switch ($action) {
          case 'create':
            $rs = [];
            $rs = $this->m_ticketing->create_ticketing($dataToken);
            $CategoryID = $rs['callback']['CategoryID'];
            $url_action = $this->jwt_url_action($CategoryID);
            $getAdminTicketing = $this->m_ticketing->getAdminTicketing($CategoryID);
            if (count($getAdminTicketing)>0) {
              // send notification
              $array_send_notification=[
                'NameRequested' => $rs['callback']['NameRequested'],
                'Description' => 'Number Ticket '.$rs['callback']['NoTicket'],
                'URLDirect' => 'ticket/set_action_first/'.$rs['callback']['NoTicket'].'/'.$url_action,
                'CreatedBy' => $rs['callback']['RequestedBy'],
                'To' => $getAdminTicketing,
                'NeedEmail' => 'No',
              ];

              $this->m_ticketing->send_notification_ticketing($array_send_notification);

            }

            echo json_encode($rs);
            break;
          case 'received':
            $rs = [];
            try {
              $dataToken = json_decode(json_encode($dataToken),true);
              $data = $dataToken['data'];
              if (array_key_exists('received', $data)) {
                $TableReceived = $this->m_ticketing->TableReceivedAction($data['received']);
              }

              if (array_key_exists('received_details', $data)) {
                $notifParams = [];
                if (array_key_exists('notifParams', $dataToken)) {
                  $notifParams = $dataToken['notifParams'];
                }
                $TableReceived_Details = $this->m_ticketing->TableReceived_DetailsAction($data['received_details'],$notifParams);
                if (array_key_exists('received', $data)) {
                  $this->m_ticketing->UpdateCategoryRequested($data['received']);
                }
              }
              
              if (array_key_exists('transfer_to', $data)) {
                $ProcessTransferTo = $this->m_ticketing->ProcessTransferTo($data['transfer_to']);
              }
              
              if (array_key_exists('update_ticket', $data)) {
                $update_ticket = $this->m_ticketing->process_ticket($data['update_ticket']);
              }
              
              $rs = ['status' => 1,'msg' => ''];
              // callback
              if (array_key_exists('datacallback', $dataToken)) {
                $datacallback = $dataToken['datacallback'];
                $NoTicket = $datacallback['NoTicket'];
                $DepartmentID = $datacallback['DepartmentID'];
                $NIP = $datacallback['NIP'];
                $data['Authent'] =$this->m_ticketing->auth_action_tickets($NoTicket,$NIP,$DepartmentID,'no');
                $data['DataTicket'] = $this->m_ticketing->getDataTicketBy(['NoTicket' => $NoTicket]); // get just data ticket
                $dataToken2 = [
                  'NIP' => $NIP,
                  'DepartmentID' => $DepartmentID,
                ];
                $data['DataAll'] = $this->m_ticketing->rest_progress_ticket($dataToken2,' and a.NoTicket = "'.$NoTicket.'" ')['data']; // get data all ticket
                $data['DataReceivedSelected'] = $this->m_ticketing->DataReceivedSelected($data['DataTicket'][0]['ID'],$DepartmentID); // receive selected
                $rs['callback'] = $data;
              }
            } catch (Exception $e) {
              $rs = ['status' => 0,'msg' => $e];
            }
            
            echo json_encode($rs);
            break;
          case 'update_received':
            $rs = [];
            $dataToken = json_decode(json_encode($dataToken),true);
            $dataToken['action'] = 'update';
            $this->m_ticketing->TableReceivedAction($dataToken);
            $this->m_ticketing->UpdateCategoryRequested($dataToken);
            $rs = ['status' => 1,'msg' => ''];
            echo json_encode($rs);
            break;
          case 'update_worker':
            $rs = [];
            $dataToken = json_decode(json_encode($dataToken),true);
            $action = $dataToken['action'];
            $ac = explode('_', $action);
            $dataToken['action'] = (count($ac)>0) ? $ac[0]  :'update';
            $this->m_ticketing->TableReceived_DetailsAction($dataToken);
            $rs = ['status' => 1,'msg' => ''];
            // callback
            if (array_key_exists('datacallback', $dataToken)) {
              $datacallback = $dataToken['datacallback'];
              $NoTicket = $datacallback['NoTicket'];
              $DepartmentID = $datacallback['DepartmentID'];
              $NIP = $datacallback['NIP'];
              $data['Authent'] =$this->m_ticketing->auth_action_tickets($NoTicket,$NIP,$DepartmentID,'no');
              $data['DataTicket'] = $this->m_ticketing->getDataTicketBy(['NoTicket' => $NoTicket]); // get just data ticket
              $dataToken2 = [
                'NIP' => $NIP,
                'DepartmentID' => $DepartmentID,
              ];
              $data['DataAll'] = $this->m_ticketing->rest_progress_ticket($dataToken2,' and a.NoTicket = "'.$NoTicket.'" ')['data']; // get data all ticket
              $data['DataReceivedSelected'] = $this->m_ticketing->DataReceivedSelected($data['DataTicket'][0]['ID'],$DepartmentID); // receive selected
              $rs['callback'] = $data;
            }
            echo json_encode($rs);
            break;
          case 'insert_worker':
            $rs = [];
            $dataToken = json_decode(json_encode($dataToken),true);
            $action = $dataToken['action'];
            $ac = explode('_', $action);
            $dataToken['action'] = (count($ac)>0) ? $ac[0]  :'insert';
            $notifParams = [];
            if (array_key_exists('notifParams', $dataToken) && $dataToken['action'] == 'insert') {
              $notifParams = $dataToken['notifParams'];
            }
            $this->m_ticketing->TableReceived_DetailsAction($dataToken,$notifParams);

            $rs = ['status' => 1,'msg' => ''];
            // callback
            if (array_key_exists('datacallback', $dataToken)) {
              $datacallback = $dataToken['datacallback'];
              $NoTicket = $datacallback['NoTicket'];
              $DepartmentID = $datacallback['DepartmentID'];
              $NIP = $datacallback['NIP'];
              $data['Authent'] =$this->m_ticketing->auth_action_tickets($NoTicket,$NIP,$DepartmentID,'no');
              $data['DataTicket'] = $this->m_ticketing->getDataTicketBy(['NoTicket' => $NoTicket]); // get just data ticket
              $dataToken2 = [
                'NIP' => $NIP,
                'DepartmentID' => $DepartmentID,
              ];
              $data['DataAll'] = $this->m_ticketing->rest_progress_ticket($dataToken2,' and a.NoTicket = "'.$NoTicket.'" ')['data']; // get data all ticket
              $data['DataReceivedSelected'] = $this->m_ticketing->DataReceivedSelected($data['DataTicket'][0]['ID'],$DepartmentID); // receive selected
              $rs['callback'] = $data;

              // updated received if null
              $dataSave2 = [
                'action' =>'update',
                'ID' => $dataToken['data']['ReceivedID'],
                'data' => [
                  'ReceivedBy' => $NIP
                ],
              ];
              $this->m_ticketing->TableReceivedAction($dataSave2);
            }
            echo json_encode($rs);
            break;
          case 'close_project':
            $rs = [];
            $dataToken = json_decode(json_encode($dataToken),true);
            $dataToken['action'] = 'update';
            if (array_key_exists('Files', $_FILES)) {
                $NoTicket = $dataToken['NoTicket'];
                $uploadNas = $this->m_ticketing->Ticketing_uploadNas($NoTicket.'_close');
                $dataToken['data']['FileUpload'] = $uploadNas;
            }
            $this->m_ticketing->TableReceivedAction($dataToken);
            $this->__trigger_close_ticket($dataToken);
            $rs = ['status' => 1,'msg' => ''];
            echo json_encode($rs);
            break;
          case 'rating':
          $rs = [];
          $dataToken = json_decode(json_encode($dataToken),true);
          $this->m_ticketing->__create_rating($dataToken);
          $rs = ['status' => 1,'msg' => ''];
          echo json_encode($rs);
          default:
            # code...
            break;
        }
      } catch (Exception $e) {
          echo json_encode($e);
      }
    }

    private function __trigger_close_ticket($dataToken){
        $ID = $dataToken['ID'];
        $G_dt = $this->m_master->caribasedprimary('db_ticketing.received','ID',$ID);
        $TicketID = $G_dt[0]['TicketID'];
        $this->m_ticketing->trigger_close_ticket($TicketID);
    }

    public function load_ticketing_dashboard()
    {
      try {
        $rs = [];
        $dataToken = $this->getInputToken();
        $action = $dataToken['action'];
        switch ($action) {
          case 'open_ticket':
            $rs = $this->m_ticketing->rest_open_ticket($dataToken);
            echo json_encode($rs);
            break;
          case 'pending_ticket':
            $rs = $this->m_ticketing->rest_pending_ticket($dataToken);
            echo json_encode($rs);
            break;
          case 'progress_ticket':
            $rs = $this->m_ticketing->rest_progress_ticket($dataToken);
            echo json_encode($rs);
            break;
          case 'close_ticket':
            $rs = $this->m_ticketing->rest_close_ticket($dataToken);
            echo json_encode($rs);
            break;
          case 'dashboard_ticket_date':
            $rs = $this->m_ticketing->dashboard_ticket_date($dataToken);
            echo json_encode($rs);
            break;
          case 'dashboard_ticket_all' :
            $rs = $this->m_ticketing->dashboard_ticket_all($dataToken);
            echo json_encode($rs);
            break;
          case 'detail_dashboard' :
            $rs = $this->m_ticketing->dashboard_detail($dataToken);
            echo json_encode($rs);
            break;
          case 'dashboard_graph_ticket_date' :
            $rs = $this->m_ticketing->dashboard_graph_ticket_date($dataToken);
            echo json_encode($rs);
            break;
          case 'dashboard_graph_ticket_all' :
            $rs = $this->m_ticketing->dashboard_graph_ticket_all($dataToken);
            echo json_encode($rs);
            break;
          default:
            # code...
            break;
        }
      } catch (Exception $e) {
        echo json_encode($e);
      }
    }

    public function CRUDStatusTicket()
    {
      try {
        $dataToken = $this->getInputToken();
        $action = $dataToken['action'];
        switch ($action) {
          case 'read':
            $rs = $this->m_ticketing->LoadDataTicketStatus($dataToken);
            echo json_encode($rs);
            break;
        }
        // end switch

      } catch (Exception $e) {
        echo json_encode($e);
      }
    }

    public function LoadTicketList(){
      $dataToken = $this->getInputToken();
      $rs =  $this->m_ticketing->LoadTicketListServerSide($dataToken);
      echo json_encode($rs);
    }

    public function ticketing_GiveRatingCheck(){
      $dataToken = $this->getInputToken();
      $rs =  $this->m_ticketing->ticketing_GiveRatingCheck($dataToken);
      echo json_encode($rs);
    }

    public function load_ticketing_report(){
      try {
        $rs = [];
        $dataToken = $this->getInputToken();
        $action = $dataToken['action'];
        switch ($action) {
          case 'category':
            $rs = $this->m_ticketing->report_category($dataToken);
            echo json_encode($rs);
            break;
          case 'worker' : 
            $rs = $this->m_ticketing->report_worker($dataToken);
            echo json_encode($rs);
            break;
          default:
            # code...
            break;
        }
      } catch (Exception $e) {
        echo json_encode($e);
      }
    }

}