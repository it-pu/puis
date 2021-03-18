<div id="sidebar" class="sidebar-fixed">
    <div id="sidebar-content">

        <!--=== Navigation ===-->
        <ul id="nav">
            <?php
            $getData  = $this->session->userdata('menu_lpmi_grouping');
            ?>
            <!-- <pre><?php //print_r($getData);exit; 
                        ?></pre> -->
            <?php
            for ($i = 0; $i < count($getData); $i++) {
                $temp = array();
                $chkSubMenu1 = 0;
                $Slug = '';
                $uri = '';
                $uriSubMenu1 = '';
                $uriSubMenu2 = '';
                // check data  memiliki submenu1 dan submenu2
                #Submenu 1
                $SubMenu1_arr = $getData[$i]['Submenu'];
                for ($j = 0; $j < count($SubMenu1_arr); $j++) {
                    $temp2 = array();
                    if ($SubMenu1_arr[$j]['SubMenu1']  != 'Empty') {
                        $chkSubMenu1++;
                        $temp2 = array('SubMenu1' => $SubMenu1_arr[$j]['SubMenu1']);
                    }

                    #Submenu2
                    $chkSubMenu2 = 0;
                    $SubMenu2_arr = $SubMenu1_arr[$j]['Submenu'];
                    $data = array();
                    for ($k = 0; $k < count($SubMenu2_arr); $k++) {
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
                        $temp2 = $temp2 + array('CountSubMenu2' => $chkSubMenu2, 'data' => $data);
                    } else {
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
                        $temp2 = $temp2 + array('CountSubMenu2' => $chkSubMenu2, 'data' => $data);
                    }

                    $temp[] = $temp2;
                }

                $open = (count($temp) > 0) ? 'open' : '';
                // closed php tag
            ?>
                <?php $uriSubMenu1 = $SubMenu1_arr[0]['Submenu'][0]['Slug'] ?>
                <?php $uriSubMenu1 = explode('/', $uriSubMenu1)  ?>
                <?php //echo json_encode($uriSubMenu1) 
                ?>
                <?php if (array_key_exists(2, $uriSubMenu1)) : ?>
                    <?php $uriSubMenu1 = $uriSubMenu1[1]  ?>
                    <li segment2="<?php echo $uriSubMenu1 ?>" class="<?php if ($this->uri->segment(2) == $uriSubMenu1) {
                                                                            echo "current " . $open;
                                                                        } ?>">
                        <a href="<?php echo $a = ($open == 'open') ? '#' : $uri ?>">
                            <i class="<?php echo ($getData[$i]['Menu'] == '' || $getData[$i]['Menu'] == null) ? 'fa fa-globe' :  $getData[$i]['Icon'] ?>" aria-hidden="true"></i>
                            <?php echo $getData[$i]['Menu'] ?>
                        </a>
                        <?php if (count($temp) > 0) : ?>
                            <ul class="sub-menu">
                                <?php for ($z = 0; $z < count($temp); $z++) : ?>
                                    <?php if ($temp[$z]['CountSubMenu2'] > 0) : ?>
                                        <?php $uriSubMenu2 = $temp[$z]['data'][0]['Slug'] ?>
                                        <?php $uriSubMenu2 = explode('/', $temp[$z]['data'][0]['Slug'])  ?>
                                        <?php $uriSubMenu2 = $uriSubMenu2[2]  ?>
                                        <li class="<?php if ($this->uri->segment(2) == $uriSubMenu1 && $this->uri->segment(3) == $uriSubMenu2) {
                                                        echo "open-default";
                                                    } ?>">
                                            <a href="javascript:void(0);">
                                                <i class="icon-angle-right"></i>
                                                <?php echo $temp[$z]['SubMenu1'] ?>
                                            </a>
                                            <ul class="sub-menu">
                                                <?php $countS2 = $temp[$z]['data'] ?>
                                                <?php for ($x = 0; $x < count($countS2); $x++) : ?>
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
                                                        for ($ii = 0; $ii < $a; $ii++) {
                                                            $URISlug .= $URI_Slug[$ii] . '/';
                                                        }
                                                        $URISlug = $URISlug . '1';
                                                    }

                                                    ?>
                                                    <li segment2="<?php echo $uriSubMenu1 ?>" segment3="<?php echo $Uri3 ?>" class="<?php if ($this->uri->segment(2) == $uriSubMenu1 && $this->uri->segment(3) == $uriSubMenu2 && $this->uri->segment(4) == $Uri3 && $this->uri->segment(5) == "") {
                                                                                                                                        echo "current";
                                                                                                                                    } ?>">
                                                        <a href="<?php echo base_url($URISlug); ?>">
                                                            <i class="icon-angle-right"></i>
                                                            <?php echo $countS2[$x]['SubMenu2'] ?>
                                                        </a>
                                                    </li>
                                                <?php endfor ?>
                                            </ul>
                                        </li>
                                    <?php else : ?>
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
                                            for ($i = 0; $i < $a; $i++) {
                                                $URISlug .= $URI_Slug[$i] . '/';
                                            }
                                            $URISlug = $URISlug . '1';
                                        }

                                        ?>
                                        <li segment2="<?php echo $uriSubMenu1 ?>" segment3="<?php echo $uriSubMenu2 ?>" class="<?php if ($this->uri->segment(2) == $uriSubMenu1 && $this->uri->segment(3) == $uriSubMenu2) {
                                                                                                                                    echo "current";
                                                                                                                                } ?>">
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
                <?php else : ?>
                    <?php //echo json_encode($uriSubMenu1) 
                    ?>
                    <?php $uri1 = $uriSubMenu1[1];   ?>
                    <?php $uriSubMenu1 = $uriSubMenu1[0] . '/' . $uriSubMenu1[1]  ?>
                    <li segment1="<?php echo $uriSubMenu1 ?>" class="<?php if ($this->uri->segment(2) == $uri1) {
                                                                            echo "current ";
                                                                        } ?>">
                        <a href="<?php echo base_url($uriSubMenu1); ?>">
                            <i class="<?php echo ($getData[$i]['Menu'] == '' || $getData[$i]['Menu'] == null) ? 'fa fa-globe' :  $getData[$i]['Icon'] ?>" aria-hidden="true"></i>
                            <?php echo $getData[$i]['Menu'] ?>
                        </a>
                    </li>
                <?php endif ?>
            <?php

            }

            ?>

            <li class="<?php if ($this->uri->segment(2) == 'slider') {
                            echo "current";
                        } ?>">
                <a href="<?php echo base_url('lpmi/slider'); ?>">
                    <i class="fa fa-desktop"></i>
                    Home Banner
                </a>
            </li>
            <li class="<?php if ($this->uri->segment(2) == 'vision' || $this->uri->segment(2) == 'mission' || $this->uri->segment(2) == 'target' || $this->uri->segment(2) == 'program' || $this->uri->segment(2) == 'event') {
                            echo "current";
                        } ?>">
                <a href="">
                    <i class="fa fa-home"></i>
                    About
                </a>
                <ul class="sub-menu">
                    <li class="<?php if ($this->uri->segment(2) == 'vision') {
                                    echo "current";
                                } ?>">
                        <a href="<?php echo base_url('lpmi/vision'); ?>">
                            <i class="fa fa-low-vision"></i>
                            Vision
                        </a>
                    </li>
                    <li class="<?php if ($this->uri->segment(2) == 'mission') {
                                    echo "current";
                                } ?>">
                        <a href="<?php echo base_url('lpmi/mission'); ?>">
                            <i class="fa fa-lightbulb-o"></i>
                            Mission
                        </a>
                    </li>
                    <!-- <li class="<?php if ($this->uri->segment(2) == 'committee') {
                                        echo "current";
                                    } ?>">
                        <a href="<?php echo base_url('lpmi/committee'); ?>">
                            <i class="fa fa-group"></i>
                            SPMI Committee
                        </a>
                    </li> -->
                    <li class="<?php if ($this->uri->segment(2) == 'target') {
                                    echo "current";
                                } ?>">
                        <a href="<?php echo base_url('lpmi/target'); ?>">
                            <i class="fa fa-bolt"></i>
                            Traget
                        </a>
                    </li>
                    <li class="<?php if ($this->uri->segment(2) == 'program') {
                                    echo "current";
                                } ?>">
                        <a href="<?php echo base_url('lpmi/program'); ?>">
                            <i class="fa fa-flag"></i>
                            Program
                        </a>
                    </li>
                    <li class="<?php if ($this->uri->segment(2) == 'event') {
                                    echo "current";
                                } ?>">
                        <a href="<?php echo base_url('lpmi/event'); ?>">
                            <i class="fa fa-tags"></i>
                            Event
                        </a>
                    </li>
                </ul>
            </li>
            <li class="<?php if ($this->uri->segment(2) == 'news') {
                            echo "current";
                        } ?>">
                <a href="<?php echo base_url('lpmi/news'); ?>">
                    <i class="fa fa-drivers-license"></i>
                    News
                </a>
            </li>
            <li class="<?php if ($this->uri->segment(2) == 'knowledge') {
                            echo "current";
                        } ?>">
                <a href="#">
                    <i class="fa fa-folder"></i>
                    Document
                </a>
                <ul class="sub-menu">
                    <li class="<?php if ($this->uri->segment(2) == 'knowledge') {
                                    echo "current";
                                } ?>">
                        <a href="<?php echo base_url('lpmi/knowledge'); ?>">
                            <i class="fa fa-download"></i>
                            Knowledge Base
                        </a>
                    </li>
                    <!-- <li class="<?php if ($this->uri->segment(2) == 'accreditation') {
                                        echo "current";
                                    } ?>">
                        <a href="<?php echo base_url('lpmi/accreditation'); ?>">
                            <i class="fa fa-pie-chart"></i>
                            Accriditations
                        </a>
                    </li> -->
                </ul>
            </li>
            <li class="<?php if ($this->uri->segment(2) == 'testimonials') {
                            echo "current";
                        } ?>">
                <a href="<?php echo base_url('lpmi/testimonials'); ?>">
                    <i class="fa fa-comments"></i>
                    Testimonials
                </a>
            </li>
            <li class="<?php if ($this->uri->segment(2) == 'partner') {
                            echo "current";
                        } ?>">
                <a href="<?php echo base_url('lpmi/partner'); ?>">
                    <i class="fa fa-handshake-o"></i>
                    Partner
                </a>
            </li>


            <li class="<?php if ($this->uri->segment(2) == 'list-lecturer') {
                            echo "current";
                        } ?>">
                <a href="<?php echo base_url('lpmi/lecturer-evaluation/list-lecturer'); ?>">
                    <i class="fa fa-pie-chart"></i>
                    Lecturer Evaluation
                </a>
            </li>


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
$this->m_menu3lpmi->checkAuth_user('db_lpmi');
?>