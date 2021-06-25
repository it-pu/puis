<div id="sidebar" class="sidebar-fixed">
    <div id="sidebar-content">

        <!--=== Navigation ===-->

        <ul id="nav">
            <?php 
                $getData  = $this->session->userdata('menu_it_grouping');
                for ($i=0; $i < count($getData); $i++) {
                    $temp = array();
                    $chkSubMenu1 = 0;
                    $Slug = '';
                    $uri = '';
                    $uriSubMenu1 = ''; 
                    $uriSubMenu2 = ''; 
                    // check data  memiliki submenu1 dan submenu2
                        #Submenu 1
                        $SubMenu1_arr = $getData[$i]['Submenu'];
                        for ($j=0; $j < count($SubMenu1_arr); $j++) {
                            $temp2 = array(); 
                            if ($SubMenu1_arr[$j]['SubMenu1']  != 'Empty') {
                                $chkSubMenu1++;
                                $temp2 = array('SubMenu1' => $SubMenu1_arr[$j]['SubMenu1']);
                            }

                            #Submenu2
                            $chkSubMenu2 = 0;
                            $SubMenu2_arr = $SubMenu1_arr[$j]['Submenu'];
                            $data = array();
                            for ($k=0; $k < count($SubMenu2_arr); $k++) { 
                                if ($SubMenu2_arr[$k]['SubMenu2'] != 'Empty') {
                                    $data[] = array(
                                        'ID' => $SubMenu2_arr[$k]['ID'],
                                        'ID_Menu' => $SubMenu2_arr[$k]['ID_Menu'],
                                        'SubMenu1' => $SubMenu2_arr[$k]['SubMenu1'],
                                        'SubMenu2' => $SubMenu2_arr[$k]['SubMenu2'],
                                        'Slug' => $SubMenu2_arr[$k]['Slug'],
                                        'Controller' => $SubMenu2_arr[$k]['Controller'],
                                        'read' => $SubMenu2_arr[$k]['read'],
                                        'write' => $SubMenu2_arr[$k]['write'],
                                        'update' => $SubMenu2_arr[$k]['update'],
                                        'delete' => $SubMenu2_arr[$k]['delete'],

                                    );
                                    $uri = $SubMenu2_arr[$k]['Slug'];
                                    $t = explode('/', $uri);
                                    $uriSubMenu1 = $t[1];
                                    $uriSubMenu2 = $t[2];
                                    $chkSubMenu2++;
                                }
                            }

                            if ($chkSubMenu2 > 0) {
                               $temp2 = $temp2 + array('CountSubMenu2' => $chkSubMenu2,'data' =>$data);
                            }
                            else
                            {
                                $data[] = array(
                                        'ID' => $SubMenu2_arr[0]['ID'],
                                        'ID_Menu' => $SubMenu2_arr[0]['ID_Menu'],
                                        'SubMenu1' => $SubMenu2_arr[0]['SubMenu1'],
                                        'SubMenu2' => $SubMenu2_arr[0]['SubMenu2'],
                                        'Slug' => $SubMenu2_arr[0]['Slug'],
                                        'Controller' => $SubMenu2_arr[0]['Controller'],
                                        'read' => $SubMenu2_arr[0]['read'],
                                        'write' => $SubMenu2_arr[0]['write'],
                                        'update' => $SubMenu2_arr[0]['update'],
                                        'delete' => $SubMenu2_arr[0]['delete'],

                                );
                                $temp2 = $temp2 + array('CountSubMenu2' => $chkSubMenu2,'data' =>$data);
                            }

                            $temp[] = $temp2;   
                            
                        }

                        $open = (count($temp) > 0) ? 'open' : '';
                    // closed php tag    
                    ?>
                    <?php $uriSubMenu1 = $SubMenu1_arr[0]['Submenu'][0]['Slug'] ?>
                    <?php $uriSubMenu1 = explode('/', $uriSubMenu1)  ?>
                    <?php //echo json_encode($uriSubMenu1) ?>
                    <?php if (array_key_exists(2, $uriSubMenu1)): ?>
                        <?php $uriSubMenu1 = $uriSubMenu1[1]  ?>
                        <li segment2 = "<?php echo $uriSubMenu1 ?>" class="<?php if($this->uri->segment(2)==$uriSubMenu1){echo "current ".$open;} ?>">
                            <a href="<?php echo $a = ($open == 'open') ? '#' : $uri ?>">
                                <i class="<?php echo ($getData[$i]['Menu'] == '' || $getData[$i]['Menu'] == null) ? 'fa fa-globe' :  $getData[$i]['Icon'] ?>" aria-hidden="true"></i>
                                    <?php echo $getData[$i]['Menu'] ?>
                            </a>
                            <?php if (count($temp) > 0 ): ?>
                                <ul class="sub-menu">
                                    <?php for($z = 0; $z < count($temp); $z++): ?>
                                        <?php if ($temp[$z]['CountSubMenu2'] > 0): ?>
                                            <?php $uriSubMenu2 = $temp[$z]['data'][0]['Slug'] ?>
                                            <?php $uriSubMenu2 = explode('/', $temp[$z]['data'][0]['Slug'])  ?>
                                            <?php $uriSubMenu2 = $uriSubMenu2[2]  ?>
                                            <li class="<?php if($this->uri->segment(2)==$uriSubMenu1 && $this->uri->segment(3) == $uriSubMenu2 ){echo "open-default";} ?>">
                                                <a href="javascript:void(0);">
                                                    <i class="icon-angle-right"></i>
                                                        <?php echo $temp[$z]['SubMenu1'] ?>
                                                </a>
                                                <ul class="sub-menu">
                                                    <?php $countS2 = $temp[$z]['data'] ?>
                                                    <?php for($x = 0; $x < count($countS2); $x++): ?>
                                                        <?php $Uri3 = $countS2[$x]['Slug'] ?>
                                                        <?php $t = explode('/', $Uri3)  ?>
                                                        <?php $Uri3 = $t[3]  ?>
                                                        <?php 
                                                        $URI_Slug = $countS2[$x]['Slug'];
                                                        $URI_Slug = explode('/', $URI_Slug);
                                                        $URISlug = $countS2[$x]['Slug'];
                                                        if (in_array('(:any)', $URI_Slug)) {
                                                           $a = count($URI_Slug) - 1;
                                                           $URISlug = '';
                                                           for ($ii=0; $ii < $a; $ii++) { 
                                                            $URISlug .= $URI_Slug[$ii].'/';
                                                           }
                                                           $URISlug = $URISlug.'1';
                                                        }

                                                         ?>
                                                        <li segment2 = "<?php echo $uriSubMenu1 ?>" segment3  = "<?php echo $Uri3 ?>" class="<?php if($this->uri->segment(2)==$uriSubMenu1 && $this->uri->segment(3) == $uriSubMenu2 && $this->uri->segment(4) == $Uri3 && $this->uri->segment(5) == ""){echo "current";} ?>">
                                                            <a href="<?php echo base_url($URISlug); ?>">
                                                                <i class="icon-angle-right"></i>
                                                                <?php echo $countS2[$x]['SubMenu2'] ?>
                                                            </a>
                                                        </li>
                                                    <?php endfor ?>      
                                                </ul>
                                            </li>
                                        <?php else: ?>
                                            <?php $uriSubMenu2 = $temp[$z]['data'][0]['Slug'] ?>
                                            <?php $uriSubMenu2 = explode('/', $temp[$z]['data'][0]['Slug'])  ?>
                                            <?php $uriSubMenu2 = $uriSubMenu2[2]  ?>
                                            <?php 
                                            $URI_Slug = $temp[$z]['data'][0]['Slug'];
                                            $URI_Slug = explode('/', $URI_Slug);
                                            $URISlug = $temp[$z]['data'][0]['Slug'];
                                            if (in_array('(:any)', $URI_Slug)) {
                                               $a = count($URI_Slug) - 1;
                                               $URISlug = '';
                                               for ($i=0; $i < $a; $i++) { 
                                                $URISlug .= $URI_Slug[$i].'/';
                                               }
                                               $URISlug = $URISlug.'1';
                                            }

                                             ?>
                                            <li segment2 = "<?php echo $uriSubMenu1 ?>" segment3  = "<?php echo $uriSubMenu2 ?>" class="<?php if($this->uri->segment(2)==$uriSubMenu1 && $this->uri->segment(3) == $uriSubMenu2 ){echo "current";} ?>">
                                                <a href="<?php echo base_url($URISlug); ?>"> 
                                                    <i class="icon-angle-right"></i>
                                                        <?php echo $temp[$z]['SubMenu1'] ?>
                                                </a>
                                            </li>                           
                                        <?php endif ?>
                                    <?php endfor ?>    
                                </ul>        
                            <?php endif ?>
                        </li>     
                    <?php else: ?>
                        <?php //echo json_encode($uriSubMenu1) ?>
                        <?php $uri1 = $uriSubMenu1[1];   ?>
                        <?php $uriSubMenu1 = $uriSubMenu1[0].'/'.$uriSubMenu1[1]  ?>
                        <li segment1 = "<?php echo $uriSubMenu1 ?>" class="<?php if($this->uri->segment(2)==$uri1){echo "current ";} ?>">
                            <a href="<?php echo base_url($uriSubMenu1); ?>">
                                <i class="<?php echo ($getData[$i]['Menu'] == '' || $getData[$i]['Menu'] == null) ? 'fa fa-globe' :  $getData[$i]['Icon'] ?>" aria-hidden="true"></i>
                                    <?php echo $getData[$i]['Menu'] ?>
                            </a>
                        </li>
                    <?php endif ?>
            <?php         

                }    

             ?>


            <div class="sidebar-title">
                <span>Manual Menu</span>
            </div>

<!--            <li class="--><?php //if($this->uri->segment(2)=='version'){echo"current";}?><!--">-->
<!--                <a href="--><?php //echo base_url('survey/list-survey');?><!--">-->
<!--                    <i class="fa fa-copyright"></i>-->
<!--                    Survey-->
<!--                </a>-->
<!--            </li>-->

            <li class="<?php if($this->uri->segment(2)=='version'){echo"current";}?>">
                <a href="<?php echo base_url('it/version');?>">
                    <i class="fa fa-copyright"></i>
                    Master Version
                </a>
            </li>

            <li class="<?= ($this->uri->segment(2)=='academic') ? 'current open' : ''?>">
                <a href="javascript:void(0);">
                    <i class="icon-edit"></i>
                    Academic Back Door
                    <i class="arrow <?= ($this->uri->segment(2)=='academic') ? 'icon-angle-down' : 'icon-angle-left'?>"></i></a>
                <ul class="sub-menu">
                    <li class="<?= ($this->uri->segment(3)=='redundancy-krs-online') ? "current open" : ""?>">
                        <a href="<?= base_url('it/academic/redundancy-krs-online') ?>">
                            <i class="icon-angle-right"></i>
                            Redundancy Krs Online
                        </a>
                    </li>
                    <li class="<?= ($this->uri->segment(3)=='overwrite-course') ? "current open" : ""?>">
                        <a href="<?= base_url('it/academic/overwrite-course') ?>">
                            <i class="icon-angle-right"></i>
                            Overwrite Course
                        </a>
                    </li>
                    <li class="<?= ($this->uri->segment(3)=='timetable') ? "current open" : ""?>">
                        <a href="<?= base_url('it/academic/timetable') ?>">
                            <i class="icon-angle-right"></i>
                            Timetable
                        </a>
                    </li>
                </ul>
            </li>

            <li class="<?= ($this->uri->segment(2)=='agregator') ? 'current open' : ''?>">
                <a href="javascript:void(0);">
                    <i class="fa fa-flag"></i>
                    Agregator
                    <i class="arrow <?= ($this->uri->segment(2)=='agregator') ? 'icon-angle-down' : 'icon-angle-left'?>"></i></a>
                <ul class="sub-menu">
                    <li class="<?= ($this->uri->segment(3)=='agregator-menu') ? "current open" : ""?>">
                        <a href="<?= base_url('it/agregator/agregator-menu') ?>">
                            <i class="icon-angle-right"></i>
                            Menu Agregator
                        </a>
                    </li>
                </ul>
            </li>

            <li class="<?php if($this->uri->segment(2)=='user-activity'){echo"current";}?>">
                <a href="<?php echo base_url('it/user-activity');?>">
                    <i class="fa fa-angellist"></i>
                    User Activity
                </a>
            </li>
            <li class="<?= ($this->uri->segment(2)=='admission') ? 'current open' : ''?>">
                <a href="javascript:void(0);">
                    <i class="fa fa-user-circle"></i>
                    Admission Back Door
                    <i class="arrow <?= ($this->uri->segment(2)=='admission') ? 'icon-angle-down' : 'icon-angle-left'?>"></i></a>
                <ul class="sub-menu">
                    <li class="<?= ($this->uri->segment(3)=='change-kode-formulir-online') ? "current open" : ""?>">
                        <a href="<?= base_url('it/admission/change-kode-formulir-online') ?>">
                            <i class="icon-angle-right"></i>
                            Formulir Online
                        </a>
                    </li>
                    <li class="<?= ($this->uri->segment(3)=='roolback-to-be-mhs') ? "current open" : ""?>">
                        <a href="<?= base_url('it/admission/roolback-to-be-mhs') ?>">
                            <i class="icon-angle-right"></i>
                            Roolback To Be MHS
                        </a>
                    </li>
                </ul>
            </li>
            <li class="<?php if($this->uri->segment(2)=='console-developer'){echo"current";}?>">
                <a href="<?php echo base_url('it/console-developer');?>">
                    <i class="fa fa-codiepie"></i>
                    Console Developer
                </a>
            </li>
            <li class="<?php if($this->uri->segment(2)=='eula'){echo"current";}?>">
                <a href="<?php echo base_url('it/eula/date-eula');?>">
                    <i class="fa fa-file-text-o"></i>
                    Eula
                </a>
            </li>
            <!-- ADDED BY FEBRI @ JAN 2020 -->
            <li class="<?php if($this->uri->segment(2)=='generate-edom'){echo"current";}?>">
                <a href="<?php echo base_url('it/generate-edom');?>">
                    <i class="fa fa-tasks"></i>
                    Generate EDOM
                </a>
            </li>
            <!-- END ADDED BY FEBRI @ JAN 2020 -->
            <li class="<?php if($this->uri->segment(2)=='request-change-password'){echo"current";}?>">
                <a href="<?php echo base_url('it/request-change-password');?>">
                   <i class="fa fa-envelope"><span class="badge" style="background-color: red;"><?php echo $resetpassmenu; ?></span></i>
                    Request Change Password
                </a>
            </li>
            <li class="<?php if($this->uri->segment(2)=='data-curriculum'){echo"current";}?>">
                <a href="<?php echo base_url('rps/data-curriculum');?>">
                   <i class="fa fa-file-text-o"></i>
                   Rencana Pembelajaran Semester
                </a>
            </li>
            <!-- <li class="<?php if($this->uri->segment(2)=='summary_knowledgebase'){echo"current";}?>">
                <a href="<?php echo base_url('it/summary_knowledgebase');?>">
                   <i class="fa fa-area-chart"><span class="badge" style="background-color: red;"></span></i>
                    Summary Knowledgebase
                </a>
            </li> -->
        </ul>



        <div class="sidebar-widget align-center">
            <div class="btn-group" data-toggle="buttons" id="theme-switcher">
                <label class="btn active">
                    <input type="radio" name="theme-switcher" data-theme="bright"><i class="fa fa-sun-o"></i> Bright
                </label>
                <label class="btn">
                    <input type="radio" name="theme-switcher" data-theme="dark"><i class="fa fa-moon-o"></i> Dark
                </label>
            </div>
        </div>

    </div>
    <div id="divider" class="resizeable"></div>
</div>
<!-- /Sidebar -->
<?php
$this->m_menu2->checkAuth_user('db_it');
?>

