<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_studentlife extends Student_Life {

    private $varClass = [];

    function __construct()
    {
        parent::__construct();
        $this->load->model('student-life/m_studentlife','stdlife');
        $this->load->model('student-life/m_alumni');
        $this->load->model('student-life/m_podivers');

    }

    private function __setting_rest_alumni(){
        $G_setting = $this->m_master->showData_array('db_alumni.rest_setting');
        $this->varClass = $G_setting[0];
        $this->varClass['customPost'] = [
            'get' => '?apikey='.$this->varClass['Apikey'],
            'header' => [
                'Hjwtkey' => $this->varClass['Hjwtkey'],
            ],
            
        ];
    }


    public function temp($content)
    {
        parent::template($content);
    }

    public function menu_diploma_supplement($page){
        $data['page'] = $page;
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/'.$data['department'].'/diploma-supplement/menu_diploma_supplement',$data,true);
        $this->temp($content);
    }

    public function diploma_supplement()
    {
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$data['department'].'/diploma-supplement/list_student',$data,true);
        $this->menu_diploma_supplement($page);
    }

    // ===== MASTER ====

    // ++ Master Company ++
    public function menu_master_company($page){
        $data['page'] = $page;
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/'.$data['department'].'/master/company/menu_master_company',$data,true);
        $this->temp($content);
    }

    public function master_company(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$data['department'].'/master/company/master_company',$data,true);
        $this->menu_master_company($page);
    }

    public function add_master_company(){

        $ID = $this->input->get('id');

        $detailCompany = [];
        if($ID!='' && $ID!=null){
            $detailCompany = $this->stdlife->getDetailCompanyByID($ID);
        }


        $data['detailCompany'] = $detailCompany;

        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$data['department'].'/master/company/add_master_company',$data,true);
        $this->menu_master_company($page);
    }





    // ===== SKPI ====
    private function menu_skpi($page){
        $data['page'] = $page;
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/'.$data['department'].'/skpi/menu_skpi',$data,true);
        $this->temp($content);
    }

    public function skpi(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$data['department'].'/skpi/list_student',$data,true);
        $this->menu_skpi($page);
    }

    public function judiciums_monitoring(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$data['department'].'/skpi/monitoring_yudisium',$data,true);
        $this->menu_skpi($page);
    }


    // ===== student_achievement ====


    private function menu_student_achievementc($page){
        $data['page'] = $page;
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/'.$data['department'].'/student-achievement/menu_student_achievement',$data,true);
        $this->temp($content);
    }

    public function student_achievement()
    {
        $data['department'] = parent::__getDepartement();
        $data['categories'] = $this->db->get_where('db_studentlife.categories_achievement',array('isActive'=>1))->result();
        $page = $this->load->view('page/'.$data['department'].'/student-achievement/student_achievement',$data,true);
        $this->menu_student_achievementc($page);
    }

    public function update_data_achievement()
    {

        $ID = $this->input->get('id');
        /*ADDED BY FEBRI @ NOV 2019 */
        $this->load->model("General_model");
        $data['categories'] = $this->General_model->fetchData("db_studentlife.categories_achievement",array("isActive"=>1))->result();
        /*END ADDED BY FEBRI @ NOV 2019 */
        $data['department'] = parent::__getDepartement();
        $data['ID'] = ($ID!='' && $ID!=null && isset($ID)) ? $ID : '';
        $page = $this->load->view('page/'.$data['department'].'/student-achievement/update_data_achievement',$data,true);
        $this->menu_student_achievementc($page);
    }

    // ===== tracert_alumni ====

    private function menu_stracert_alumni($page){
        $data['page'] = $page;
        $data['department'] = parent::__getDepartement();
        $content = $this->load->view('page/'.$data['department'].'/tracer-alumni/menu_stracer_alumni',$data,true);
        $this->temp($content);
    }

    public function list_alumni(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$data['department'].'/tracer-alumni/list_alumni',$data,true);
        $this->menu_stracert_alumni($page);
    }

    public function form_accreditation(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$data['department'].'/tracer-alumni/form_accreditation',$data,true);
        $this->menu_stracert_alumni($page);
    }

    public function forum_alumni(){
        $this->__setting_rest_alumni();
        $this->varClass['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$this->varClass['department'].'/tracer-alumni/forum_alumni',$this->varClass,true);
        $this->menu_stracert_alumni($page);
    }

    public function forum_alumni_detail($token){
        $this->__setting_rest_alumni();
        $this->varClass['ForumID'] = $this->decodeToken($token);
        $this->varClass['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$this->varClass['department'].'/tracer-alumni/forum_alumni_detail',$this->varClass,true);
        $this->menu_stracert_alumni($page);
    }

    public function testimony(){
        $this->__setting_rest_alumni();
        $this->varClass['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$this->varClass['department'].'/tracer-alumni/testimony',$this->varClass,true);
        $this->menu_stracert_alumni($page);
    }


    // ------------Menu Alumni Live---------//
    // Yamin
    // 03/08/2020

    public function menu_content(){
        // $data['page'] = $page;
        $data['department'] = parent::__getDepartement();
        // $data['category'] = $this->m_alumni->get_category();
        $content = $this->load->view('page/'.$data['department'].'/portal-alumni/menu_content',$data,true);
        $this->temp($content);
    }

    ## table load /list

    public function ajax_list()
    {
        $type=$this->uri->segment(2);
        $list = $this->m_alumni->get_datatables($type);
        $data = array();        
        $no = $_POST['start'];
       
        foreach ($list as $m) {
            $no++;
            $row = array();
            $row[] = $m->TitleContent;
            
            $row[] = $m->Description;
            $row[] = $m->Status;
            $row[] = $m->UpdatedAt; 
            //add html for action
            $row[] = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Edit" onclick="edit_alumni('."'".$m->ID_Content."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
                <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_alumni('."'".$m->ID_Content."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
 
            $data[] = $row;
        }
       
        $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_alumni->count_all(),
                "recordsFiltered" => $this->m_alumni->count_filtered(),
                "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }

    public function ajax_edit($id)
    {
        $data = $this->m_alumni->get_by_id($id);
        // print_r($data);die();
        echo json_encode($data);
    }
 
    public function ajax_add()
    {
        $this->_validate();
        $data = array(
                // 'IDType' => $this->input->post('type'),
                'TitleContent' => $this->input->post('title'),
                'IDSubCat' => $this->input->post('idsubcategory'),
                'Description' => $this->input->post('description'),
                'Meta_description' => $this->input->post('meta_des'),
                'Meta_keywords' => $this->input->post('meta_key'),
                'Lang' => $this->input->post('lang'),
                'AddDate' => $this->input->post('date'),
                'Status' => $this->input->post('status'),
                'CreateAt' => date('Y-m-d H:i:s'),
                'CreateBy' => $this->session->userdata('NIP'),
            );

        if(!empty($_FILES['photo']['name']))
        {
            $upload = $this->_do_upload();
            $data['File'] = $upload;
        }
        $type = $this->input->post('type');
        $insert = $this->m_alumni->save($data,$type);
        echo json_encode(array("status" => TRUE));
    }

    // Content 
    public function ajax_update()
    {
        $this->_validate();
        $data = array(
                'TitleContent' => $this->input->post('title'),
                'IDSubCat' => $this->input->post('category'),
                'Description' => $this->input->post('description'),
                'Meta_description' => $this->input->post('meta_des'),
                'Meta_keywords' => $this->input->post('meta_key'),
                // 'Lang' => $this->input->post('lang'),
                'AddDate' => $this->input->post('date'),
                'Status' => $this->input->post('status'),
                'UpdatedAt' => date('Y-m-d H:i:s'),
                'UpdatedBy' => $this->session->userdata('NIP'),
            );

        if(!empty($_FILES['photo']['name']))
        {
            $upload = $this->_do_upload();
             
            //delete file
            $alumni = $this->m_alumni->get_by_id($this->input->post('id'));
            if(file_exists('./uploads/alumni/'.$alumni->File) && $alumni->File)
                unlink('./uploads/alumni/'.$alumni->File);
 
            $data['File'] = $upload;
        }

        $this->m_alumni->update(array('ID_Content' => $this->input->post('id')), $data);
        echo json_encode(array("status" => TRUE));
    }
    
    public function ajax_delete($id)
    {
        //delete file
        $alumni = $this->m_alumni->get_by_id($id);
        if(file_exists('./uploads/alumni/'.$alumni->File) && $alumni->File)
            unlink('./uploads/alumni/'.$alumni->File);

        $this->m_alumni->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }

    private function _do_upload()
    {
        $config['upload_path']          = './uploads/alumni';
        $config['allowed_types']        = 'gif|jpg|png|jpeg|pdf';
        $config['max_size']             = 2048000; //set max size allowed in Kilobyte 2mb
        $config['max_width']            = 1600; // set max width image allowed
        $config['max_height']           = 600; // set max height allowed
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
 
        if($this->input->post('status') == '')
        {
            $data['inputerror'][] = 'status';
            $data['error_string'][] = 'Status is required';
            $data['status'] = FALSE;
        }
 
        // if($this->input->post('lang') == '')
        // {
        //     $data['inputerror'][] = 'lang';
        //     $data['error_string'][] = 'Please select language';
        //     $data['status'] = FALSE;
        // }
        
 
        if($data['status'] === FALSE)
        {
            echo json_encode($data);
            exit();
        }
    }


    public function fetchData()
    {
      // $rs = ['status' => 0,'msg' => '','callback' => [] ]; 
      $datatoken =  $this->getInputToken();
      // $datatoken = json_decode(json_encode($datatoken),true);

        if($datatoken['action']=='viewData'){  
            $requestData = $_REQUEST;
            $approvedBy = $this->session->userdata('NIP');
            $param =  array();
  

        /*FILTER*/
        if(!empty($datatoken['EventName'])){
            $param[] = array("field"=>"a.Event","data"=>" like '%".$datatoken['EventName']."%' ","filter"=>"AND",);
        }       
        if(!empty($datatoken['lvl'])){
            $param[] = array("field"=>"a.Level","data"=>" ='".$datatoken['lvl']."'","filter"=>"AND",);
        }
        if(!empty($datatoken['type'])){
            $param[] = array("field"=>"a.Type","data"=>" ='".$datatoken['type']."'","filter"=>"AND",);
        }
        if(!empty($datatoken['categ'])){
            $param[] = array("field"=>"a.CategID","data"=>" ='".$datatoken['categ']."'","filter"=>"AND",);
        }
        if(!empty($datatoken['sDate'])){
            $param[] = array("field"=>"a.StartDate","data"=>"  >= '".$datatoken['sDate']."'","filter"=>"AND",);
        }
        if(!empty($datatoken['eDate'])){
            $param[] = array("field"=>"a.EndDate","data"=>"  <= '".$datatoken['eDate']."'","filter"=>"AND",);
        }
        if(!empty($datatoken['std'])){
            $param[] = array("field"=>"(e.NPM","data"=>" like '%".$datatoken['std']."%' ","filter"=>"AND",);
            $param[] = array("field"=>"e.Name","data"=>" like '%".$datatoken['std']."%') ","filter"=>"OR",);
        }
        if(!empty($datatoken['isAppr'])){
            $param[] = array("field"=>"a.isApproved","data"=>"  = '".$datatoken['isAppr']."'","filter"=>"AND",);
        }   
        if( !empty($requestData['search']['value']) ) {            
            $param[] = array("field"=>"(a.Event","data"=>" like '%".$requestData['search']['value']."%' ","filter"=>"AND",);
            $param[] = array("field"=>"e.NPM","data"=>" like '%".$requestData['search']['value']."%' ","filter"=>"OR",);
            $param[] = array("field"=>"e.Name","data"=>" like '%".$requestData['search']['value']."%') ","filter"=>"OR",);
        }

         $where='';
        if(!empty($param)){
            $where = 'WHERE ';
            $counter = 0;
            foreach ($param as $key => $value) {
                if($counter==0){
                    $where = $where.$value['field']." ".$value['data'];
                }
                else{
                    $where = $where.$value['filter']." ".$value['field']." ".$value['data'];
                }
                $counter++;
            }

        }


            $queryDefault = 'SELECT a.*,b.Name as categName , e.NPM as NPM,e.Name as Name, (select approvedBy from db_studentlife.student_achievement c where c.approvedBy like "'.$approvedBy.'%" and c.ID = a.ID) as isAbble
                                      FROM db_studentlife.student_achievement  a
                                      left join db_studentlife.student_achievement_student d on d.SAID = a.ID 
                                      left join db_academic.auth_students e on e.NPM = d.NPM 
                                      left join db_studentlife.categories_achievement b on b.ID = a.CategID
                                      '.$where.'
                                      group by a.ID
                                      ORDER BY Year, StartDate ASC';
                    

          
            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM ('.$queryDefault.') xx';

            $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for($i=0;$i<count($query);$i++) {
                    
                $nestedData = array();
                $row = $query[$i];
                $tokenID = $this->jwt->encode(array('ID'=>$row['ID']),'UAP)(*');
                                         
                    $ID = $row['ID'];

                    $disabled = (!$row['isAbble']) ? 'disabled':'';
                    $btnAct = '<div class="btn-group">
                          <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-edit"></i> <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu">
                            <li><a href="'.base_url('student-life/student-achievement/update-data-achievement?id='.$ID).'">Edit</a></li>
                            <li role="separator" class="divider"></li>
                            <li class="'.$disabled.'"><a href="javascript:void(0);" class="actRemove" data-id="'.$ID.'">Remove</a></li>
                          </ul>
                        </div>';

                    $lbl = ($row['Type']=='1' || $row['Type']==1)
                        ? '<span class="label label-success">Academic</span>'
                        : '<span class="label label-default">Non Academic</span>';

                    $viewEvent = '<a><b>'.$row['Event'].'</b></a>';
                    $labelStatusApv = "";
                    if($row['isApproved'] == 1) {$labelStatusApv="<span class='label label-info'>Wait approval</span>";}
                    else if($row['isApproved'] == 2) {$labelStatusApv="<span class='label label-primary'>Approved</span>";}
                    else if($row['isApproved'] == 3) {$labelStatusApv="<span class='label label-danger'>Rejected</span>";}
                    else{$labelStatusApv="UNKNOW";}
                    $labelApvBy = "";
                    if($row['isApproved'] == 2 &&($row['approvedBy'] != "" || $row['approvedBy'])){$labelApvBy = "<br><small>Approved by ".$row['approvedBy']."</small>";}
            
   
            
                $nestedData[] = '<div>'.$no.'</div>';
                $nestedData[] = '<div style="text-align: left;">'.$viewEvent."<br>".(($row['isSKPI'] == 1) ? '<span class="label label-warning">SKPI</span>':'').'</div>';
                $nestedData[] = '<div style="text-align: left;">'.date('D, d M Y',strtotime($row['StartDate'])).'<br/>'.date('D, d M Y',strtotime($row['EndDate'])).'</div>';
                $nestedData[] = '<div style="text-align: left;">'.$row['categName'].'</div>';
                $nestedData[] = '<div style="text-align: left;">'.$row['Level'].'</div>';
                $nestedData[] = '<div style="text-align: left;">'.$lbl.'</div>';
                $nestedData[] = '<div style="text-align: left;">'.$row['Achievement'].'</div>';
                $nestedData[] = '<div style="text-align: left;"><a class="btn btn-xs btn-primary" target="_blank" href="'.base_url('uploads/certificate/'.$row['Certificate']).'">View PDF</a></div>';
                $nestedData[] = '<div style="text-align: center;">'.$labelStatusApv.$labelApvBy.'</div>';
                $nestedData[] = $btnAct;
                $nestedData[] = '<div style="text-align: left;">'.ucwords($row['Name']).' ('.$row['NPM'].')</div>'; 
         

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval($queryDefaultRow),
                "recordsFiltered" => intval($queryDefaultRow),
                "data"            => $data,
                // "dataQuery"       => $query
            );
            echo json_encode($json_data);
        
        } 
    }

    // Podivers
    // 10/14/2020
    // Yamin
    private function menu_podivers($page){
        $data['page'] = $page;
        $data['department'] = parent::__getDepartement();
        // $data['setgroup'] = $this->m_podivers->getSetGroup();
        $content = $this->load->view('page/'.$data['department'].'/podivers/menu_podivers',$data,true);
        $this->temp($content);
    }

    public function list_podivers(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$data['department'].'/podivers/list_podivers',$data,true);
        // $page = $this->load->view('page/'.$data['department'].'/podivers/v_setting',$data,true);
        $this->menu_podivers($page);
    }

    public function set_group(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$data['department'].'/podivers/set_group',$data,true);
        $this->menu_podivers($page);
    }

    public function user_access(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$data['department'].'/podivers/user_access',$data,true);
        $this->menu_podivers($page);
    }

    // get set set_group
    public function ajax_listPodivers()
    {
        $type=$this->uri->segment(2);
        $list = $this->m_podivers->get_datatables($type);

        $data = array();        
        $no = $_POST['start'];
       
        foreach ($list as $m) {
            $no++;
            $row = array();
            $row[] = $m->NIPNPM;
            $row[] = $m->Name;
            $row[] = $m->MasterGroupName;
            $row[] = $m->GroupName;
            $row[] = $m->UpdateAT; 
            //add html for action
            $row[] = '
                    <a class="btn btn-sm btn-primary hide" href="javascript:void(0)" title="Edit" onclick="edit_podivers('."'".$m->ID_set_list_member."'".')"><i class="glyphicon glyphicon-pencil"></i> Edit</a>
                    <a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Hapus" onclick="delete_podivers('."'".$m->NIPNPM."'".')"><i class="glyphicon glyphicon-trash"></i> Delete</a>';
 
            $data[] = $row;
        }
       
        $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_podivers->count_all(),
                "recordsFiltered" => $this->m_podivers->count_filtered(),
                "data" => $data,
                );
        //output to json format
        echo json_encode($output);
    }

    public function crudSetMasterGroup(){
        // $id = $this->input->post('id',TRUE);
        $data = $this->m_podivers->getSetMasterGroup();
        echo json_encode($data);
    }

    public function crudSetGroup(){
        $id = $this->input->post('id',TRUE);
        if($id){
            $data = $this->m_podivers->getSetGroupSelect($id);
        }else{
            $data = $this->m_podivers->getSetGroup();
        }
        echo json_encode($data);
    }

    public function crudSetMember(){
        // $id = $this->input->post('id',TRUE);
        $data = $this->m_podivers->getSetMember();
        echo json_encode($data);
    }

    public function ajax_editpodivers($id)
    {
        $data = $this->m_podivers->get_by_id($id);
        // print_r($data);die();
        echo json_encode($data);
    }
 
    public function ajax_addpodivers()
    {
        // $this->_validate();
        $data = array(
                // 'IDType' => $this->input->post('type'),
                'ID_master_group' => $this->input->post('ID_master_group'),
                'ID_set_group' => $this->input->post('ID_set_group'),
                'ID_set_member' => $this->input->post('ID_set_member'),
                'NIPNPM' => $this->input->post('npm'),                
                'UpdateAT' => date('Y-m-d H:i:s'),
                'UpdateBY' => $this->session->userdata('NIP'),
            );
        $datablog = array(
                // 'IDType' => $this->input->post('type'),
                'ID_master_group' => $this->input->post('ID_master_group'),
                'ID_set_group' => $this->input->post('ID_set_group'),
                'ID_set_member' => $this->input->post('ID_set_member'),
                'NIPNPM' => $this->input->post('npm'),                
                'UpdateAT' => date('Y-m-d H:i:s'),
                'UpdateBY' => $this->session->userdata('NIP'),
            );
        $insert = $this->m_podivers->save($data,$datablog);
        echo json_encode(array("status" => TRUE));
    }

    // Content 
    public function ajax_updatepodivers()
    {
        $data = array(
                'ID_master_group' => $this->input->post('ID_master_group'),
                'ID_set_group' => $this->input->post('ID_set_group'),
                'ID_set_member' => $this->input->post('ID_set_member'),
                'NIPNPM' => $this->input->post('npm'),                
                'UpdateAT' => date('Y-m-d H:i:s'),
                'UpdateBY' => $this->session->userdata('NIP'),
            );
        $datablog = array(
                'ID_master_group' => $this->input->post('ID_master_group'),
                'ID_set_group' => $this->input->post('ID_set_group'),
                'ID_set_member' => $this->input->post('ID_set_member'),
                'NIPNPM' => $this->input->post('npm'),                
                'UpdateAT' => date('Y-m-d H:i:s'),
                'UpdateBY' => $this->session->userdata('NIP'),
            );
        

        $this->m_podivers->update(array('ID_master_group' => $this->input->post('ID_master_group')), $data);
        echo json_encode(array("status" => TRUE));
    }
    
    public function ajax_deletepodivers($id)
    {
        //delete file
        // $alumni = $this->m_podivers->get_by_id($id);
        $this->m_podivers->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }


    // set group
    // public  function list_setgroup(){
    //     $data=$this->m_podivers->get_setgroup();
    //     echo json_encode($data);
    // }

    public function ajax_addSet()
    {
        // $this->_validate();
        $data = array(                
                'GroupName' => $this->input->post('groupname'),
                'Active' => 1,
            );
        
        // print_r($data);die();
        $insert = $this->m_podivers->saveSet($data);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_editSet($id)
    {
        $data = $this->m_podivers->get_by_idSet($id);
        echo json_encode($data);
    }

    public function ajax_updateSet()
    {
        // $this->_validate();
        $data = array(                
                'GroupName' => $this->input->post('groupname'), 
                'Active' => 1,               
            );
        
        $update = $this->m_podivers->updateSet($this->input->post('idSetGroup'),$data);       
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_deleteset($id)
    {
        $this->m_podivers->delete_by_idSet($id);
        echo json_encode(array("status" => TRUE));
    }




}
