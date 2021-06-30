<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_divisi extends Webdivisi_Controler {
  public $data = array();

  function __construct()
  {
    parent::__construct();
    $this->load->model('m_sendemail');        
    $this->data['department'] = parent::__getDepartement(); 
    $this->load->model(array('m_api','master/m_master','General_model'));
    $this->session->set_userdata('db_select','db_webdivisi');
    $this->data['db_select'] = $this->session->userdata('db_select');
    $this->load->model('webdivisi/beranda/m_home');
  }

  public function temp($content)
  {
    parent::template($content);
  }

  public function dashboard()
  {
    $data['department'] = parent::__getDepartement();
    $content = $this->load->view('page/webdivisi/dashboard',$data,true);
    $this->temp($content);
  }

  public function menu_request($page){
    $PositionMain = $this->session->userdata('PositionMain');
    if ($PositionMain['IDDivision'] != 12 && $this->uri->segment(3) !='user_access' ) {
      redirect(base_url().'webdivisi/setting/user_access');
    }
    else
    {
      $data['page'] = $page;
      $content = $this->load->view('page/'.$this->data['department'].'/menu_previleges/menu_choose',$data,true);
      $this->temp($content);
    }

  }

  public function setting(){
   $sql = 'select a.* from '.$this->data['db_select'].'.cfg_menu as a 
   order by a.Sort asc';
   $query=$this->db->query($sql, array())->result_array();
   $this->data['Arr_Menu'] =$query;
   $content = $this->load->view('page/'.$this->data['department'].'/menu_previleges/menu',$this->data,true);
   $this->menu_request($content);
 }

 public function save_menu()
 {
   $Input = $this->getInputToken();
   $action = $Input['action'];
   $rs = array();
   switch ($action) {
    case 'read':
    $sql = 'select a.* from '.$this->data['db_select'].'.cfg_menu as a 
    order by a.Sort asc';
    $query=$this->db->query($sql, array())->result_array();
    echo json_encode($query);
    break;
    case 'add':
    unset($Input['ID']);
    unset($Input['action']);
    $this->db->insert($this->data['db_select'].'.cfg_menu',$Input);
    $sql = 'select a.* from '.$this->data['db_select'].'.cfg_menu as a 
    order by a.Sort asc';
    $query=$this->db->query($sql, array())->result_array();
    $rs['data'] = $query;
    echo json_encode($rs);
    break;
    case 'edit':
    $ID = $Input['ID'];
    unset($Input['action']);
    unset($Input['ID']);
    $this->db->where('ID',$ID);
    $this->db->update($this->data['db_select'].'.cfg_menu',$Input);
    echo json_encode(1);
    break;
    case 'delete':
    $ID = $Input['ID'];
    $this->db->where('ID',$ID);
    $this->db->delete($this->data['db_select'].'.cfg_menu');
    echo json_encode(1);
    break;	
    default:
    			# code...
    break;
  }
}

public function sub_menu()
{
  $sql = 'select a.Menu,a.Sort,b.* from '.$this->data['db_select'].'.cfg_menu as a
  join '.$this->data['db_select'].'.cfg_sub_menu as b 
  on a.ID = b.ID_Menu order by a.Sort asc,b.Sort1 asc,b.Sort2 asc
  ';
  $query=$this->db->query($sql, array())->result_array();
  $this->data['Arr_dt'] =$query;
  $this->data['Arr_Menu'] =$this->m_master->showData_array($this->data['db_select'].'.cfg_menu');				
  $content = $this->load->view('page/'.$this->data['department'].'/menu_previleges/sub_menu',$this->data,true);
  $this->menu_request($content);
}

public function save_sub_menu()
{
 $Input = $this->getInputToken();
 $action = $Input['action'];
 $rs = array();
 switch ($action) {
  case 'add':
  unset($Input['ID']);
  unset($Input['action']);
    			$actionCh = $Input['actionCh']; // for akses
    			unset($Input['actionCh']);
    			$t = array(
    				'read' => 1,
    				'write' => 0,
    				'update' => 0,
    				'delete' => 0,
    			);
    			if ($actionCh == 1) {
    				$t = array(
    					'read' => 1,
    					'write' => 1,
    					'update' => 1,
    					'delete' => 1,
    				);
    			}

    			$Input = $Input + $t;
    			$this->db->insert($this->data['db_select'].'.cfg_sub_menu',$Input);
          $sql = 'select a.Menu,a.Sort,b.* from '.$this->data['db_select'].'.cfg_menu as a
          join '.$this->data['db_select'].'.cfg_sub_menu as b 
          on a.ID = b.ID_Menu order by a.Sort asc,b.Sort1 asc,b.Sort2 asc
          ';
          $query=$this->db->query($sql, array())->result_array();
          $rs['data'] = $query;
          echo json_encode($rs);
          break;
          case 'edit':
          $ID = $Input['ID'];
          unset($Input['ID']);
          unset($Input['action']);
    			$actionCh = $Input['actionCh']; // for akses
    			unset($Input['actionCh']);
    			$t = array(
    				'read' => 1,
    				'write' => 0,
    				'update' => 0,
    				'delete' => 0,
    			);
    			if ($actionCh == 1) {
    				$t = array(
    					'read' => 1,
    					'write' => 1,
    					'update' => 1,
    					'delete' => 1,
    				);
    			}

    			$Input = $Input + $t;
    			$this->db->where('ID',$ID);
    			$this->db->update($this->data['db_select'].'.cfg_sub_menu',$Input);
          echo json_encode(1);
          break;
          case 'delete':
          $ID = $Input['ID'];
          $this->db->where('ID',$ID);
          $this->db->delete($this->data['db_select'].'.cfg_sub_menu');
          echo json_encode(1);
          break;	
          default:
    			# code...
          break;
        }
      }

      public function groupuser()
      {
       $content = $this->load->view('page/'.$this->data['department'].'/menu_previleges/group_user',$this->data,true);
       $this->menu_request($content);
     }

     public function group_previleges_crud()
     {
       $Input = $this->getInputToken();
       $action = $Input['action'];
       switch ($action) {
        case 'read':
        $generate = $this->m_master->showData_array($this->data['db_select'].'.cfg_group_user');
        echo json_encode($generate);
        break;
        case 'add':
        $dataSave = array(
         'GroupAuth' => $Input['groupName'],
       );
        $this->db->insert($this->data['db_select'].'.cfg_group_user', $dataSave);
        break;
        case 'edit':
        $dataSave = array(
         'GroupAuth' => $Input['GroupAuth'],
       );
        $this->db->where('ID',$Input['ID']);
        $this->db->update($this->data['db_select'].'.cfg_group_user', $dataSave);
        break;	
        case 'delete':
        $this->db->where('ID',$Input['ID']);
        $this->db->delete($this->data['db_select'].'.cfg_group_user');
        break;	
        default:
    			# code...
        break;
      }
    }

    public function get_submenu_by_menu()
    {
     $Input = $this->getInputToken();
     $generate = $this->m_menu2->get_submenu_by_menu($Input,$this->data['db_select'].'');
     echo json_encode($generate);
   }

   public function save_submenu_by_menu()
   {
     $Input = $this->getInputToken();
     $cfg_group_user = $Input['ID_GroupUSer'];
     $getData = $Input['getData'];
     $getData = json_decode(json_encode($getData),true);
     $t = array(
      'read' => 1,
      'write' => 0,
      'update' => 0,
      'delete' => 0,
    );
     for ($i=0; $i < count($getData); $i++) { 
      if ($getData[$i]['value'] == 1) {
       $t = array(
        'read' => 1,
        'write' => 1,
        'update' => 1,
        'delete' => 1,
      );
     }

     $dataSave = array(
       'cfg_group_user' => $cfg_group_user,
       'ID_cfg_sub_menu' => $getData[$i]['ID'],
     );

     $dataSave = $dataSave + $t;
     $this->db->insert($this->data['db_select'].'.cfg_rule_g_user', $dataSave);
   }

 }

 public function group_previleges_rud()
 {
   $Input = $this->getInputToken();
   $action = $Input['action'];
   switch ($action) {
    case 'read':
    $GroupID = $Input['Nama_search'];
    $generate = $this->m_menu2->get_previleges_group_show($GroupID,$this->data['db_select'].'');
    echo json_encode($generate);
    break;
    case 'edit':
    $t = array(
      'read' => 1,
      'write' => 0,
      'update' => 0,
      'delete' => 0,
    );
    if ($Input['actionCh'] == 1) {
      $t = array(
       'read' => 1,
       'write' => 1,
       'update' => 1,
       'delete' => 1,
     );
    }
    $ID = $Input['ID'];
    $this->db->where('ID',$ID);
    $this->db->update($this->data['db_select'].'.cfg_rule_g_user',$t);
    break;
    case 'delete':
    $ID = $Input['ID'];
    $this->db->where('ID',$ID);
    $this->db->delete($this->data['db_select'].'.cfg_rule_g_user');
    break;    
    default:
    			# code...
    break;
  }
}

public function getAuthDataTables()
{
  $requestData= $_REQUEST;
        // print_r($requestData);
  $DivisiID = $this->session->userdata('IDdepartementNavigation');
  $totalData = $this->m_master->getCountAllDataAuth($this->data['db_select'].'.previleges_guser',$DivisiID);

        // get NIP
  $NIP = $this->session->userdata('NIP');
  $get = $this->m_master->caribasedprimary($this->data['db_select'].'.previleges_guser','NIP',$NIP);
  if ($get[0]['G_user'] == 1) {
    if( !empty($requestData['search']['value']) ) {
      $sql = 'SELECT a.NIP,b.Name,a.G_user FROM '.$this->data['db_select'].'.previleges_guser as a join db_employees.employees as b
      on a.NIP = b.NIP  left join '.$this->data['db_select'].'.cfg_group_user as cgu on a.G_user = cgu.ID';

      $sql.= ' where (a.NIP LIKE "'.$requestData['search']['value'].'%" or b.Name LIKE "%'.$requestData['search']['value'].'%" or cgu.GroupAuth LIKE "%'.$requestData['search']['value'].'%") and a.DivisiID ='.$DivisiID;
      $sql.= ' ORDER BY a.NIP ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

    }
    else {
     $sql = 'SELECT a.NIP,b.Name,a.G_user FROM '.$this->data['db_select'].'.previleges_guser as a join db_employees.employees as b
     on a.NIP = b.NIP  left join '.$this->data['db_select'].'.cfg_group_user as cgu on a.G_user = cgu.ID
     where a.DivisiID = '.$DivisiID.'
     ';
     $sql.= ' ORDER BY a.NIP ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

   }
 }
 else
 {
  if( !empty($requestData['search']['value']) ) {
    $sql = 'SELECT a.NIP,b.Name,a.G_user FROM '.$this->data['db_select'].'.previleges_guser as a join db_employees.employees as b
    on a.NIP = b.NIP  left join '.$this->data['db_select'].'.cfg_group_user as cgu on a.G_user = cgu.ID';

    $sql.= ' where (a.NIP LIKE "'.$requestData['search']['value'].'%" or b.Name LIKE "%'.$requestData['search']['value'].'%" and a.G_user != 1 or cgu.GroupAuth LIKE "%'.$requestData['search']['value'].'%") and a.DivisiID ='.$DivisiID;
    $sql.= ' ORDER BY a.NIP ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

  }
  else {
   $sql = 'SELECT a.NIP,b.Name,a.G_user FROM '.$this->data['db_select'].'.previleges_guser as a join db_employees.employees as b
   on a.NIP = b.NIP and a.G_user != 1 left join '.$this->data['db_select'].'.cfg_group_user as cgu on a.G_user = cgu.ID
   where a.DivisiID = '.$DivisiID.'
   ';
   $sql.= ' ORDER BY a.NIP ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

 }
}

$query = $this->db->query($sql)->result_array();

if ($get[0]['G_user'] == 1) {
  $getGroupUser = $this->m_master->showData_array($this->data['db_select'].'.cfg_group_user');
}
else
{
  $getGroupUser = $this->m_master->getDataWithoutSuperAdminGlobal($this->data['db_select'].'.cfg_group_user');
}

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

$btn = '<button class="btn btn-primary btn-sm btn-write btn-save-groupauth" NIP = "'.$row['NIP'].'"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>&nbsp<button class="btn btn-danger btn-sm btn-write btn-delete-groupauth" NIP = "'.$row['NIP'].'"><i class="fa fa-trash" aria-hidden="true"></i></button>';  

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

public function authUser_cud()
{
 $Input = $this->getInputToken();
 $action = $Input['action'];
 $DivisiID = $this->session->userdata('IDdepartementNavigation');
 switch ($action) {
  case 'add':
    			// check NIP existing
  $sql = 'select * from '.$this->data['db_select'].'.previleges_guser where NIP = "'.$Input['NIP'].'" and DivisiID = '.$DivisiID.' ';
  $G_ = $this->db->query($sql,array())->result_array();
  if (count($G_) == 0) {
    $dataSave = array(
      'NIP' => $Input['NIP'],
      'G_user' => $Input['GroupUser'],
      'DivisiID' => $DivisiID
    );
    $this->db->insert($this->data['db_select'].'.previleges_guser', $dataSave);

                    // insert auth prodi
    $this->m_webdivisi->__process_auth_divisi('add',$Input['NIP'],$DivisiID);
    echo json_encode(1);
  }
  else
  {
    echo json_encode('User Already exist');
  }

  break;
  case 'edit':
  $input = $this->getInputToken();
  $dataSave = array(
   'G_user' => $input['valuee'],
   'DivisiID' => $DivisiID,
 );
  $this->db->where('NIP', $input['NIP']);
  $this->db->where('DivisiID', $DivisiID);
  $this->db->update($this->data['db_select'].'.previleges_guser', $dataSave);

                // insert auth prodi
  $this->m_prodi->__process_auth_prodi('edit',$Input['NIP'],$DivisiID);
  break;
  case 'delete':
  $input = $this->getInputToken();
  $sql = "delete from ".$this->data['db_select'].".previleges_guser where NIP = '".$input['NIP']."'  and DivisiID = ".$DivisiID." ";
  $query=$this->db->query($sql, array());

                // insert auth prodi
  $this->m_prodi->__process_auth_prodi('delete',$Input['NIP'],$DivisiID);
  break;	
  default:
    			# code...
  break;
}
}


public function ajax_list()
{
        // print_r('k');die();
  $type=$this->uri->segment(2);
        // print_r($type);die();
  $list = $this->m_home->get_datatables($type);

  $data = array();        
  $no = $_POST['start'];
  foreach ($list as $m) {
    $no++;
    $row = array();
    $row[]=$no;
    $row[] = $m->Title;
    $row[] = $m->Name;
            // $row[] = $m->File;
    $row[] = $m->Language;
    $row[] = $m->UpdatedAt;



            //add html for action
    $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_prodi('."'".$m->ID."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_prodi('."'".$m->ID."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';

    $data[] = $row;
  }

  $output = array(
    "draw" => $_POST['draw'],
    "recordsTotal" => $this->m_home->count_all(),
    "recordsFiltered" => $this->m_home->count_filtered(),
    "data" => $data,
  );
        //output to json format
  echo json_encode($output);
}


public function ajax_edit($id)
{
  $data = $this->m_home->get_by_id($id);
  echo json_encode($data);
}

public function ajax_add()
{
        // print_r($this->session->userdata('IDdepartementNavigation'));die();
  $this->_validate();
  $data = array(
                // 'Type' => $this->input->post('type'),
    'DivisiID' => $this->session->userdata('IDdepartementNavigation'),
    'LangID' =>$this->input->post('lang'),                
    'ID_CatBase' => $this->input->post('category'), 
    'Type' => "knowledge",
    'Title' => $this->input->post('title'),
                // 'Description' => '',
    'UpdatedAt' => date('Y-m-d H:i:s'),
    'UpdatedBy' => $this->session->userdata('NIP'),
  );
        // print_r($data);die();
  if(!empty($_FILES['photo']['name']))
  {
    $upload = $this->_do_upload();
    $data['File'] = $upload;
  }

  $insert = $this->m_home->save($data);
  echo json_encode(array("status" => TRUE));
}


public  function list_category(){
  $data=$this->m_home->get_category();
  echo json_encode($data);
}

public function get_category(){
  $this->db->from("db_prodi.category");
  $this->db->order_by('ID','desc');
  $q = $this->db->get();
  return $q->result();  
}

public function getCatByLang(){

  $getidlang =  $this->input->post('idlang');
        // print_r($getidlang);
  $q = $this->m_home->getCategory($getidlang);        
  echo json_encode($q);  
}

public function ajax_addCat()
{
        // $this->_validate();
  $data = array(     
    'DivisiID' => $this->session->userdata('IDdepartementNavigation'),           
    'Name' => $this->input->post('category'),
    'Lang' => $this->input->post('lang'),
    'CreateAt' => date('Y-m-d H:i:s'),
    'CreateBy' => $this->session->userdata('NIP'),
  );

        // print_r($data);die();        
  $insert = $this->m_home->saveCat($data);
  echo json_encode(array("status" => TRUE));
}

public function ajax_editCat($id)
{
  $data = $this->m_home->get_by_idCat($id);
  echo json_encode($data);
}

public function ajax_updateCat()
{
        // $this->_validate();
  $data = array(    
    'DivisiID' => $this->session->userdata('IDdepartementNavigation'),            
    'Name' => $this->input->post('category'),
    'Lang' => $this->input->post('lang'),
    'CreateAt' => date('Y-m-d H:i:s'),
    'CreateBy' => $this->session->userdata('NIP'),
  );

        // print_r($data);die();
  $insert = $this->m_home->updateCat(array('ID' => $this->input->post('idcat')),$data);
  echo json_encode(array("status" => TRUE));
}

public function ajax_deleteCat($id)
{
  $this->m_home->delete_by_idCat($id);
  echo json_encode(array("status" => TRUE));
}

    // Content 
public function ajax_update()
{
  $this->_validate();
  $data = array(
    'Title' => $this->input->post('title'),
    'ID_CatBase' => $this->input->post('category'),
                // 'Description' => $this->input->post('description'),
                // 'Meta_description' => $this->input->post('meta_des'),
                // 'Meta_keywords' => $this->input->post('meta_key'),
    'LangID' => $this->input->post('lang'),
                // 'AddDate' => $this->input->post('date'),
                // 'Status' => $this->input->post('status'),
    'UpdatedAt' => date('Y-m-d H:i:s'),
    'UpdatedBy' => $this->session->userdata('NIP'),
  );

  if(!empty($_FILES['photo']['name']))
  {
    $upload = $this->_do_upload();

            //delete file
    $home = $this->m_home->get_by_id($this->input->post('id'));
    if(file_exists('./uploads/prodi/'.$home->File) && $home->File)
      unlink('./uploads/prodi/'.$home->File);

    $data['File'] = $upload;
  }

  $this->m_home->update(array('ID' => $this->input->post('id')), $data);
  echo json_encode(array("status" => TRUE));
}

public function ajax_delete($id)
{
        //delete file
  $home = $this->m_home->get_by_id($id);
  if(file_exists('./uploads/prodi/'.$home->File) && $home->File)
    unlink('./uploads/prodi/'.$home->File);

  $this->m_home->delete_by_id($id);
  echo json_encode(array("status" => TRUE));
}

private function _do_upload()
{
  $config['upload_path']          = './uploads/prodi';
  $config['allowed_types']        = 'gif|jpg|png|jpeg|pdf';
        $config['max_size']             = 2048000; //set max size allowed in Kilobyte 2mb
        // $config['max_width']            = 1000; // set max width image allowed
        // $config['max_height']           = 1000; // set max height allowed
        $config['file_name']            = round(microtime(true) * 1000); //just milisecond timestamp fot unique name
        
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if(!$this->upload->do_upload('photo')) //upload and validate
        {
          $data['inputerror'][] = 'photo';
            $data['error_string'][] = 'Upload error: '.$this->upload->display_errors('',''); //show ajax error
            $data['status'] = FALSE;
            echo json_encode($data);
            exit();
          }
          return $this->upload->data('file_name');
        }

        private function _validate()
        {
          $data = array();
          $data['error_string'] = array();
          $data['inputerror'] = array();
          $data['status'] = TRUE;

          if($this->input->post('title') == '')
          {
            $data['inputerror'][] = 'title';
            $data['error_string'][] = 'Title is required';
            $data['status'] = FALSE;
          }

        // if($this->input->post('description') == '')
        // {
        //     $data['inputerror'][] = 'description';
        //     $data['error_string'][] = 'Description is required';
        //     $data['status'] = FALSE;
        // }
          if($data['status'] === FALSE)
          {
            echo json_encode($data);
            exit();
          }

        }


      }
