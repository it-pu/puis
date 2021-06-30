<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_menuwebdivisi extends CI_Model {

  public $data = array();
  private $NameSess = '';
  private $AuthNameSession = '';
  private $MenuSess = '';
  private $MenuSessGrouping = '';
  private $dbAuth = '';


  public function __construct()
  {
      parent::__construct();
  }

  public function set_model($NameSess = 'NameSess',$AuthNameSession = 'AuthNameSession',$MenuSess = 'MenuSess',$MenuSessGrouping = 'MenuSessGrouping',$dbAuth= 'db_webdivisi')
  {
    $this->NameSess = $NameSess;
    $this->AuthNameSession = $AuthNameSession;
    $this->MenuSess = $MenuSess;
    $this->MenuSessGrouping = $MenuSessGrouping;
    $this->dbAuth = $dbAuth;
    $this->getParentConstruct();
  }

  private function getParentConstruct()
  {
      // check user auth
      if (!$this->session->userdata($this->NameSess)) {
          $check = $this->auth();
          if (!$check) {
              // not authorize
              redirect(base_url().'/');
          }
          else
          {
              if (!$this->session->userdata($this->AuthNameSession)) {
                  $this->getAuth();
              }
          }
      }
  }

  private function auth()
  {
      $NIP = $this->session->userdata('NIP');
      $getData = $this->getUserAuth($NIP);
      if (count($getData) > 0) {
          $this->session->set_userdata($this->NameSess,1);
          return true;
      }

      return false;
  }

  private function getAuth()
  {
      $data = array();
      $getDataMenu = $this->getMenuGroupUser();
      $data_sess = array();
      if (count($getDataMenu) > 0) {
          $this->session->set_userdata($this->AuthNameSession,1);
          $this->session->set_userdata($this->MenuSess,$getDataMenu);
          $this->session->set_userdata($this->MenuSessGrouping,$this->groupBYMenu_sess());
      }
  }

  private function getMenuGroupUser()
  {
    $NIP = $this->session->userdata('NIP');
    $db = $this->dbAuth;
    $OrderBy = ($db == 'db_webdivisi') ? 'order by b.Sort asc' : '';
      $sql = 'SELECT b.ID as ID_menu,b.Icon,c.ID,b.Menu,c.SubMenu1,c.SubMenu2,x.`read`,x.`update`,x.`write`,x.`delete`,c.Slug,c.Controller 
              from db_employees.employees as a
              join '.$db.'.previleges_guser as d
              on a.NIP = d.NIP
              join '.$db.'.cfg_rule_g_user as x
              on d.G_user = x.cfg_group_user
              join '.$db.'.cfg_sub_menu as c
              on x.ID_cfg_sub_menu = c.ID
              join '.$db.'.cfg_menu as b
              on b.ID = c.ID_Menu where a.NIP = ? GROUP by b.id '.$OrderBy;
      $query=$this->db->query($sql, array($NIP))->result_array();
      return $query;
  }

  public function getUserAuth($NIP)
  {
      $dpt = $this->session->userdata('IDdepartementNavigation');
      $sql = 'select CONCAT(a.Name," | ",a.NIP) as Name, a.NIP from db_employees.employees as a 
        join db_employees.rule_users as b
        on a.NIP = b.NIP
        where b.IDDivision = ? and a.NIP = ?
        GROUP BY a.NIP';
      $query=$this->db->query($sql, array($dpt,$NIP))->result_array();
      return $query;
  }

  private function groupBYMenu_sess()
  {
      $DataDB = $this->session->userdata($this->MenuSess);
      $arr = array();
      for ($i=0; $i < count($DataDB); $i++) {
          $submenu1 = $this->getSubmenu1BaseMenu_grouping($DataDB[$i]['ID_menu'],$this->dbAuth);
          $arr2 = array();
          for ($k=0; $k < count($submenu1); $k++) { 
              $submenu2 = $this->getSubmenu2BaseSubmenu1_grouping($submenu1[$k]['SubMenu1'],$this->dbAuth,$DataDB[$i]['ID_menu']);
              $arr2[] = array(
                  'SubMenu1' => $submenu1[$k]['SubMenu1'],
                  'Submenu' => $submenu2,
              );
          }
          if ($i == 0) {
              // SORTING ASC
                  usort($arr2, function($a, $b) {
                      return (int)$a['SubMenu1'] - (int)$b['SubMenu1'];
                  });
          }
          

          $arr[] =array(
              'Menu' => $DataDB[$i]['Menu'],
              'Icon' => $DataDB[$i]['Icon'],
              'Submenu' => $arr2

          );
          
      }

      return $arr;
  }

  public function getSubmenu1BaseMenu_grouping($ID_Menu,$db='db_webdivisi')
  {
      $sql = 'SELECT a.ID,a.ID_Menu,a.SubMenu1,a.SubMenu2,a.Slug,a.Controller,b.read,b.write,b.update,b.delete 
      from '.$db.'.cfg_sub_menu as a join '.$db.'.cfg_rule_g_user as b on a.ID = b.ID_cfg_sub_menu
      join '.$db.'.previleges_guser as c on b.cfg_group_user = c.G_user  where a.ID_Menu = ? and c.NIP = ? group by a.SubMenu1 order by a.Sort1 asc';
      $query=$this->db->query($sql, array($ID_Menu,$this->session->userdata('NIP')))->result_array();
      return $query;
  }

  public function getSubmenu2BaseSubmenu1_grouping($submenu1,$db='db_webdivisi',$IDmenu = null)
  {
      if ($IDmenu != null) {
          $sql = 'SELECT a.ID,a.ID_Menu,a.SubMenu1,a.SubMenu2,a.Slug,a.Controller,b.read,b.write,b.update,b.delete 
          from '.$db.'.cfg_sub_menu as a  join '.$db.'.cfg_rule_g_user as b on a.ID = b.ID_cfg_sub_menu
          join '.$db.'.previleges_guser as c on b.cfg_group_user = c.G_user
           where a.SubMenu1 = ? and c.NIP = ? and a.ID_Menu = ? order by a.Sort2 asc';
          $query=$this->db->query($sql, array($submenu1,$this->session->userdata('NIP'),$IDmenu))->result_array();
      }
      else
      {
          $sql = 'SELECT a.ID,a.ID_Menu,a.SubMenu1,a.SubMenu2,a.Slug,a.Controller,b.read,b.write,b.update,b.delete 
          from '.$db.'.cfg_sub_menu as a  join '.$db.'.cfg_rule_g_user as b on a.ID = b.ID_cfg_sub_menu
          join '.$db.'.previleges_guser as c on b.cfg_group_user = c.G_user
           where a.SubMenu1 = ? and c.NIP = ? order by a.Sort2 asc';
          $query=$this->db->query($sql, array($submenu1,$this->session->userdata('NIP')))->result_array();
      }
      
      return $query;
  }

  private function chkAuthDB_Base_URL($URL,$db = 'db_webdivisi')
  {
      $a = explode('/', $URL);
      $b = count($a) - 1;
      $URISlug = 'and a.Slug = "'.$URL.'"';
      if ($a[$b] == 1) {
          $URISlug = '';
          for ($i=0; $i < $b ; $i++) {
              if ($i != ($b) ) {
                 $URISlug .= $a[$i].'/';
              }
              else{
                $URISlug .= $a[$i];
              } 
             
          }
          $URISlug = 'and a.Slug like "%'.$URISlug.'%"';
      }
      $sql = "select b.read,b.write,b.update,b.delete from ".$db.".cfg_sub_menu as a join ".$db.".cfg_rule_g_user as b on a.ID = b.ID_cfg_sub_menu
      join ".$db.".previleges_guser as c on c.G_user = b.cfg_group_user
      where c.NIP = ? ".$URISlug;
      $query=$this->db->query($sql, array($this->session->userdata('NIP')))->result_array();
      if (count($query) == 0) { // digunakan untuk horizontal URL
        /*
          URL  : purchasing/transaction/po/list
          jika URL = purchasing/transaction/po/open tidak ditemukan pada query, maka ambil URl terdekat.  
        */
          $b = count($a);
          $URISlug = '';
          // hilangkan satu segment url terakhir
          for ($i=0; $i < ($b - 1) ; $i++) {
              if ($i != ($b - 2) ) {
                 $URISlug .= $a[$i].'/';
              }
              else{
                $URISlug .= $a[$i];
              } 
             
          }
          
          $URISlug = 'and a.Slug = "'.$URISlug.'"';
          $sql = "select b.read,b.write,b.update,b.delete from ".$db.".cfg_sub_menu as a join ".$db.".cfg_rule_g_user as b on a.ID = b.ID_cfg_sub_menu
          join ".$db.".previleges_guser as c on c.G_user = b.cfg_group_user
          where c.NIP = ? ".$URISlug;
          $query=$this->db->query($sql, array($this->session->userdata('NIP')))->result_array();
      }
      return $query;
  }

  public function checkAuth_user($db)
  {
      $base_url = base_url();
      $currentURL = current_url();
      $URL = str_replace($base_url,"",$currentURL);
      // get Access URL
      $getDataSess  = $this->session->userdata($this->MenuSessGrouping);
      $access = array(
          'read' => 0,
          'write' => 0,
          'update' => 0,
          'delete' => 0,
      );

      $p = $this->chkAuthDB_Base_URL($URL,$db);
      if (count($p) > 0 ) {
          $access = array(
              'read' => $p[0]['read'],
              'write' => $p[0]['write'],
              'update' => $p[0]['update'],
              'delete' => $p[0]['delete'],
          );
      }

      $html = '<script type="text/javascript">
        var MyVarEbombAccess;
      </script>
      ';
      if ($access['read'] == 0) {
          $html .= '<script type="text/javascript">
               var waitForEl = function(selector, callback) {
                 if (jQuery(selector).length) {
                   callback();
                 } else {
                   MyVarEbombAccess = setTimeout(function() {
                     waitForEl(selector, callback);
                   }, 100);
                 }
               };

               waitForEl(".btn-read", function() {
                 $(".btn-read").remove();
               });

               $(document).ready(function () {
                   $(".btn-read").remove();
                   $(document).ajaxComplete(function () {
                       $(".btn-read").remove();
                   });
               });
               setTimeout(function () {
                   clearTimeout(MyVarEbombAccess);
               },20000);
               </script>
          ';
          echo $html;
      }

      if ($access['write'] == 0) {
          $html .= '<script type="text/javascript">
               var waitForEl = function(selector, callback) {
                 if (jQuery(selector).length) {
                   callback();
                   // console.log("__Length__");
                 } else {
                  //console.log("Not Length__");
                    MyVarEbombAccess = setTimeout(function() {
                     waitForEl(selector, callback);
                   }, 100);
                 }
               };

               waitForEl(".btn-add", function() {
                 $(".btn-add").remove();
               });

               waitForEl(".btn-write", function() {
                 $(".btn-write").remove();
               });

               $(document).ready(function () {
                   $(".btn-add").remove();
                   $(".btn-write").remove();
                   $(document).ajaxComplete(function () {
                      $(".btn-add").remove();
                      $(".btn-write").remove();
                   });
               });
               setTimeout(function () {
                   clearTimeout(MyVarEbombAccess);
               },20000);
               </script>
          ';
          echo $html;
      }
      if ($access['update'] == 0) {
          $html .= '<script type="text/javascript">
               var waitForEl = function(selector, callback) {
                 if (jQuery(selector).length) {
                   callback();
                 } else {
                  // console.log("Not Length__");
                    MyVarEbombAccess = setTimeout(function() {
                     waitForEl(selector, callback);
                   }, 100);
                 }
               };

               waitForEl(".btn-edit", function() {
                 $(".btn-edit").remove();
               });

               $(document).ready(function () {
                   $(".btn-edit").remove();
                   $(document).ajaxComplete(function () {
                            $(".btn-edit").remove();
                   });

               });
               setTimeout(function () {
                   clearTimeout(MyVarEbombAccess);
               },20000);
               </script>
          ';
          echo $html;
      }
      if ($access['delete'] == 0) {
          $html .= '<script type="text/javascript">
               var waitForEl = function(selector, callback) {
                 if (jQuery(selector).length) {
                   callback();
                 } else {
                    MyVarEbombAccess = setTimeout(function() {
                     waitForEl(selector, callback);
                   }, 100);
                 }
               };

               waitForEl(".btn-delete", function() {
                 $(".btn-delete").remove();
               });

               waitForEl(".btn-Active", function() {
                 $(".btn-Active").remove();
               });

               $(document).ready(function () {
                  $(".btn-delete").remove();
                  $(".btn-Active").remove();
                  $(document).ajaxComplete(function () {
                      $(".btn-delete").remove();
                      $(".btn-Active").remove();
                  });
                   
               });
               setTimeout(function () {
                   clearTimeout(MyVarEbombAccess);
               },20000);
               </script>
          ';
          echo $html;
      }


      // special menu & group
      $bool = true;
      foreach ($access as $key => $value) {
          if ($value == 0) {
              $bool = false;
              break;
          }
      }

      if (!$bool) {
          $html .= '<script type="text/javascript">
               var waitForEl = function(selector, callback) {
                 if (jQuery(selector).length) {
                   callback();
                 } else {
                    MyVarEbombAccess = setTimeout(function() {
                     waitForEl(selector, callback);
                   }, 100);
                 }
               };

               waitForEl(".btn-delete-menu-auth", function() {
                  $(".btn-delete-menu-auth").remove();
               });

               waitForEl(".btn-edit-menu-auth", function() {
                 $(".btn-edit-menu-auth").remove();
               });

               waitForEl(".btn-edit-menu-auth", function() {
                 $(".btn-edit-menu-auth").remove();
               })

               waitForEl(".btn-add-menu-auth", function() {
                 $(".btn-add-menu-auth").remove();
               });

               waitForEl(".btn-delete-menu-auth", function() {
                 $(".btn-delete-menu-auth").remove();
               });
               setTimeout(function () {
                   clearTimeout(MyVarEbombAccess);
               },20000);
               </script>
          ';
          echo $html;
      }
      return $html;
  }

  public function get_submenu_by_menu($input,$db)
  {
      $ID_Menu = $input['Menu'];
      $GroupUser = $input['GroupUser'];
      // print_r($input);die();
      $sql = "select a.Menu,b.* from ".$db.".cfg_menu as a
    join ".$db.".cfg_sub_menu as b
    on a.ID = b.ID_Menu where b.ID_Menu = ?
    and b.ID not in (select ID_cfg_sub_menu from ".$db.".cfg_rule_g_user where cfg_group_user = ?)";
    // print_r($sql);die();
      $query=$this->db->query($sql, array($ID_Menu,$GroupUser))->result_array();
      return $query;
  }

  public function get_previleges_group_show($GroupID,$db)
    {
        $sql = 'SELECT d.GroupAuth, b.Menu,c.SubMenu1,c.SubMenu2,c.ID_Menu,a.ID_cfg_sub_menu,a.ID as ID_previleges,a.`read`,a.`write`,a.`update`,
a.`delete`,c.`read` as readMenu,c.`update` as updateMenu,c.`write` as writeMenu,c.`delete` as deleteMenu from '.$db.'.cfg_rule_g_user as a
            join '.$db.'.cfg_group_user as d
            on a.cfg_group_user = d.ID
            join '.$db.'.cfg_sub_menu as c
            on a.ID_cfg_sub_menu = c.ID
            join '.$db.'.cfg_menu as b
            on b.ID = c.ID_Menu where d.ID = ? ';
        $query=$this->db->query($sql, array($GroupID))->result_array();
        return $query;
    }

}
