<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_config extends Vreservation_Controler {

    // private $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_sendemail');
        $this->load->model('m_api');
        $this->load->library('JWT');
        $this->checkAuth_user();
    }

    public function menu()
    {
        $content = $this->load->view($this->pathView.'config/menu','',true);
        $this->temp($content);
    }

    public function modal_form_previleges()
    {
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        echo $this->load->view($this->pathView.'config/modal_menu_previleges',$this->data,true);
    }

    public function get_menu()
    {
        $generate = $this->m_reservation->getdataMenu();
        echo json_encode($generate);
    }

    public function get_menu_save_menu()
    {
        $input = $this->getInputToken();
        $menu = $input['InputJenisMenu'];
        $this->m_reservation->saveMenu($menu);
    }

    public function get_submenu_save_menu()
    {
        $input = $this->getInputToken();
        $menu = $input['selectMenu'];
        $sub_menu1 = $input['sub_menu1'];
        $sub_menu2 = $input['sub_menu2'];
        $Slug = $input['Slug'];
        $Controller = $input['Controller'];
        $chkPrevileges = $input['chkPrevileges'];
        $this->m_reservation->saveSubMenu($menu,$sub_menu1,$sub_menu2,$chkPrevileges,$Slug,$Controller);
    }

    public function get_submenu_show()
    {
        $generate = $this->m_reservation->showSubmenu();
        echo json_encode($generate);
    }

    public function get_submenu_update()
    {
        $input = $this->getInputToken();
        $this->m_reservation->updateSubMenu($input);

    }

    public function get_submenu_delete()
    {
        $input = $this->getInputToken();
        $this->m_reservation->deleteSubMenu($input);
    }

    public function getGroupPrevileges()
    {
        // get NIP
        $NIP = $this->session->userdata('NIP');
        $get = $this->m_master->caribasedprimary('db_reservation.previleges_guser','NIP',$NIP);
        if ($get[0]['G_user'] == 1) {
            $generate = $this->m_master->showData_array('db_reservation.cfg_group_user');
        }
        else
        {
            $generate = $this->m_reservation->getDataWithoutSuperAdmin2($get[0]['G_user']);
        }
        
        echo json_encode($generate);
    }

    public function getMenu()
    {
        $generate = $this->m_master->showData_array('db_reservation.cfg_menu');
        echo json_encode($generate);
    }

    public function get_submenu_by_menu()
    {
        $input = $this->getInputToken();
        $generate = $this->m_reservation->get_submenu_by_menu($input);
        echo json_encode($generate);
    }

    public function get_previleges_group_show()
    {
        $input = $this->getInputToken();
        $GroupID = $input['Nama_search'];
        $generate = $this->m_reservation->get_previleges_group_show($GroupID);
        echo json_encode($generate);
    }

    public function save_groupuser_previleges()
    {
        $input = $this->getInputToken();
        $this->m_reservation->save_groupuser_previleges($input);
    }

    public function previleges_groupuser_update()
    {
        $input = $this->getInputToken();
        $this->m_reservation->previleges_groupuser_update($input);
    }

    public function previleges_groupuser_delete()
    {
        $input = $this->getInputToken();
        $this->m_reservation->previleges_groupuser_delete($input);
    }

    public function modalform_group_user()
    {
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        echo $this->load->view($this->pathView.'config/modal_group_user',$this->data,true);
    }

    public function save_group_user()
    {
        $input = $this->getInputToken();
        $dataSave = array(
            'GroupAuth' => $input['groupName'],
        );
        $this->db->insert('db_reservation.cfg_group_user', $dataSave);
    }

    public function update_group_user()
    {
        $input = $this->getInputToken();
        $ID = $input['ID'];
        $GroupAuth = $input['GroupAuth'];
        $sql = "update db_reservation.cfg_group_user set GroupAuth = ? where ID = ? ";
        $query=$this->db->query($sql, array($GroupAuth,$ID));
    }

    public function delete_group_user()
    {
        $input = $this->getInputToken();
        $sql = "delete from db_reservation.cfg_group_user where ID = ".$input['ID'];
        $query=$this->db->query($sql, array());
    }

    public function g_previleges()
    {
        $content = $this->load->view($this->pathView.'config/previleges','',true);
        $this->temp($content);
    }

    public function getAuthDataTables()
    {
        $requestData= $_REQUEST;
        $ID_group_user = $this->session->userdata('ID_group_user');
        // print_r($requestData);
        $totalData = $this->m_reservation->getCountAllDataAuth();

        // get NIP
        $NIP = $this->session->userdata('NIP');
        $get = $this->m_master->caribasedprimary('db_reservation.previleges_guser','NIP',$NIP);
        $sql = 'SELECT a.NIP,b.Name,a.G_user FROM db_reservation.previleges_guser as a join db_employees.employees as b
                on a.NIP = b.NIP join db_reservation.cfg_group_user as c on a.G_user = c.ID';

        $sql.= ' where (a.NIP LIKE "'.$requestData['search']['value'].'%" or b.Name LIKE "%'.$requestData['search']['value'].'%" or c.GroupAuth LIKE "%'.$requestData['search']['value'].'%") and a.G_user >= "'.$ID_group_user.'"';
        $sql.= ' ORDER BY a.NIP ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();

        $sqlGroupuser = 'select * from db_reservation.cfg_group_user where ID >= "'.$ID_group_user.'"';
        $getGroupUser = $this->db->query($sqlGroupuser, array())->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $nestedData[] = $row['NIP'];
            $nestedData[] = $row['Name'];

            $combo = '<select class="full-width-fix select grouPAuth btn-edit" NIP = "'.$row['NIP'].'">';
            for ($j=0; $j < count($getGroupUser); $j++) { 
                if ($getGroupUser[$j]['ID'] == $row['G_user']) {
                     $combo .= '<option value = "'.$getGroupUser[$j]['ID'].'" selected>'.$getGroupUser[$j]['GroupAuth'].'</option>';
                }
                else
                {
                    $combo .= '<option value = "'.$getGroupUser[$j]['ID'].'">'.$getGroupUser[$j]['GroupAuth'].'</option>';
                }
            }

            $combo .= '</select>';

            $nestedData[] = $combo;

            $btn = '<button class="btn btn-danger btn-sm btn-delete btn-delete-group" NIP = "'.$row['NIP'].'"><i class="fa fa-trash" aria-hidden="true"></i></button>';  

            $nestedData[] = $btn;
            $data[] = $nestedData;
        }

        // print_r($data);

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function add_auth_user()
    {
        error_reporting(0);
        $input = $this->getInputToken();
        $dataSave = array(
            'NIP' => $input['NIP'],
            'G_user' => $input['GroupUser'],
        );
        $this->db->insert('db_reservation.previleges_guser', $dataSave);

    }

    public function delete_authUser()
    {
        $input = $this->getInputToken();
        $sql = "delete from db_reservation.previleges_guser where NIP = '".$input['NIP']."'";
        $query=$this->db->query($sql, array());
    }

    public function edit_auth_user()
    {
        $input = $this->getInputToken();
        $dataSave = array(
            'G_user' => $input['valuee'],
        );
        $this->db->where('NIP', $input['NIP']);
        $this->db->update('db_reservation.previleges_guser', $dataSave);
    }

    public function policy()
    {
        $content = $this->load->view($this->pathView.'config/policy','',true);
        $this->temp($content);
    }

    public function policy_json_data()
    {
        // cek auth
        $arr_temp = array();
        $ID_group_user = $this->session->userdata('ID_group_user');
        $QueryAdd = ' where a.ID_group_user >= "'.$ID_group_user.'"';
        $sql = 'select a.*,b.GroupAuth from db_reservation.cfg_policy as a join db_reservation.cfg_group_user as b on a.ID_group_user = b.ID '.$QueryAdd;
        $query=$this->db->query($sql, array())->result_array();
        $Q_catetegory = $this->m_master->showData_array('db_reservation.category_room');
        for ($i=0; $i < count($query); $i++) { 
            $CategoryRoom = $query[$i]['CategoryRoom'];
            $CategoryRoom = json_decode($CategoryRoom);
            $NameCategory = array();
            for ($l=0; $l < count($CategoryRoom); $l++) { 
                
                for ($k=0; $k < count($Q_catetegory); $k++) { 
                    if ($CategoryRoom[$l] ==  $Q_catetegory[$k]['ID'] ) {
                        $NameCategory[] = $Q_catetegory[$k]['NameEng'];
                        break; 
                    }
                }
            }
            $NameCategory = implode("<br>", $NameCategory);
            $query[$i] = $query[$i] + array('NameCategory' => $NameCategory );
        }
        echo json_encode($query);
    }

    public function policy_modalform()
    {
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        $this->data['id'] = $input['CDID'];
        if ($input['Action'] == 'edit') {
            $this->data['getDataEdit'] =  $this->m_master->caribasedprimary('db_reservation.cfg_policy','ID',$input['CDID']);
        }
        echo $this->load->view($this->pathView.'config/modalform_policy',$this->data,true);
    }

    public function policy_submit()
    {
        $input = $this->getInputToken();
        $CategoryRoom = json_encode($input['CategoryRoom']);
        switch ($input['Action']) {
            case 'add':
            $dataSave = array(
                'ID_group_user' => $input['selectGroupuUser'],
                'BookingDay' => $input['BookingDay'],
                'CategoryRoom' => $CategoryRoom,
            );
            $this->db->insert('db_reservation.cfg_policy', $dataSave);
                break;
            case 'edit':
                $dataSave = array(
                    'ID_group_user' => $input['selectGroupuUser'],
                    'BookingDay' => $input['BookingDay'],
                    'CategoryRoom' => $CategoryRoom,
                );
                $this->db->where('ID', $input['CDID']);
                $this->db->update('db_reservation.cfg_policy', $dataSave);
                break;
            case 'delete':
                $this->m_master->delete_id_table_all_db($input['CDID'],'db_reservation.cfg_policy');
                break;
            default:
                # code...
                break;
        }
    }

}
