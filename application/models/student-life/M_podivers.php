<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_podivers extends CI_Model {


    var $table = 'db_podivers.set_list_member';
    var $tableset = 'db_blogs.set_group';
    var $column_order = array('po.ID_set_list_member','Name',null); //set column field database for datatable orderable
    var $column_search = array('NIPNPM'); //set column field database for datatable searchable just firstname , lastname , address are searchable

    var $order = array('po.ID_set_list_member' => 'desc'); // default order 

    private function _get_datatables_query()
    {
        // if($this->input->post('type')){
        //     $sql = 'select ci.* from db_podivers.content_index AS ci
        //                     WHERE ci.SegmentMenu="'.$this->input->post('type').'" ';
        //     $query=$this->db->query($sql, array())->result_array();
        //     $getvaID= $query[0]['ID'];  
        //     $this->db->where('IDindex', $getvaID);
        // }
        
        $this->db->from('db_podivers.set_list_member as po'); 
        $this->db->join('db_blogs.set_list_member as bl', 'po.NIPNPM = bl.NIPNPM');
        $this->db->join('db_academic.auth_students', 'auth_students.NPM = po.NIPNPM', 'left');
        $this->db->join('db_blogs.set_master_group', 'set_master_group.ID_master_group = po.ID_master_group', 'left');
        $this->db->join('db_blogs.set_group', 'po.ID_set_group = set_group.ID_set_group', 'left');
        $i = 0;
        
        // if(!isset($_POST['category']))
        // {
        //     $this->db->join('db_podivers.category', 'db_podivers.content.IDCat = db_podivers.category.ID');

        // }

        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                 
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.

                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
         
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }

    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }    
    
    public function get_by_id($id)
    {
        $this->db->from('db_podivers.set_list_member'); 
        $this->db->where('ID_set_group',$id);     
        $query = $this->db->get(); 
        return $query->row();
    }  

    public function save($data,$datablog)
    {   
        // print_r($data);die();
        $this->db->insert($this->table, $data);
        $this->db->insert('db_blogs.set_list_member', $datablog);
        return $this->db->insert_id();
    }

 
    public function update($where, $data,$datablog,$wherenlog)
    {
        $this->db->update($this->table, $data, $where);
        $this->db->update('db_blogs.set_list_member', $datablog, $wherenlog);
        return $this->db->affected_rows();
    }    
    

    public function delete_by_id($id)
    {
        $this->db->where('NIPNPM', $id);
        if($this->db->delete($this->table)){
            $this->db->where('NIPNPM', $id);
            $this->db->delete('db_blogs.set_list_member');
        }
    }

    public function getSetMasterGroup(){
        $data = $this->db->query('SELECT * FROM db_blogs.set_master_group where Active = 1 ORDER BY ID_master_group ASC');

        return $data->result_array();
    }

    public function getSetGroup(){
        $data = $this->db->query('SELECT * FROM db_blogs.set_group where Active = 1 ORDER BY ID_set_group ASC');

        return $data->result_array();
    }

    public function getSetGroupSelect($id){
        $data = $this->db->query('SELECT * FROM db_blogs.set_group where ID_master_group="'.$id.'" and Active = 1');

        return $data->result_array();
    }

    public function getSetMember(){
        $data = $this->db->query('SELECT * FROM db_blogs.set_member where Active = 1 ORDER BY ID_set_member ASC');

        return $data->result_array();
    }   


    public function get_by_idSet($id)
    {
        $this->db->from('db_podivers.set_group');
        $this->db->where('ID_set_group',$id);
        $query = $this->db->get(); 
        return $query->row();
    }

    public function saveSet($data)
    {   
       
         // print_r($data);die();
        $this->db->insert('db_blogs.set_group', $data);
        return $this->db->insert_id();
    }

 
    public function updateSet($where, $data)
    {	
    	$this->db->where('ID_set_group', $where);
		$this->db->update('db_blogs.set_group', $data);
        return $this->db->affected_rows();
    }    
    

    public function delete_by_idSet($id)
    {
        $this->db->where('ID_set_group', $id);
        $this->db->delete('db_blogs.set_group');
    }
}
