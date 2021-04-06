<!-- UPDATED CODE BY FEBRI @ JUNE 2020 -->
<?php if(!empty($G_data)){ $num=1;
 foreach ($G_data as $key => $value) { ?>
    <li class="list-group-item item-head">
      <a href="javascript:void(0)" data-toggle="collapse" data-target="#kb-<?=$num ?>">
          <span class="numbering"><b><?=$num; ?></b></span>
          <span class="info"><b><?=$key?></b></span>
      </a>

      <div id="kb-<?=$num?>" class="collapse detailKB">
        <ul class="list-group">
          <?php foreach ($value as $v) {?>
          <li class="list-group-item" data-contentid="<?=$v->KBID?>" data-type="knowledge_base">
            <a href="javascript:void(0)" data-toggle="collapse" data-target="#KBC-<?=$v->KBID?>">
                <b><?=$v->Desc?> (<span style="color: orange;"><?php echo $v->Status ?></span>) </b>
                <span class="pull-right viewers">
                <?php if(!empty($v->CountRead->Total)){ ?>
                <span class="text-success"><i class="fa fa-check-square"></i> has been read <span class="total-read"><?=$v->CountRead->Total?></span> times</span>
                <?php } ?>
                </span>
            </a>

            <div id="KBC-<?=$v->KBID ?>" class="collapse">
              <div style="margin-top: 15px;margin-bottom: 15px;">
                <a class="btn btn-default <?php if(empty($v->File) ? 'hide':'') ?>" style="display: inline;" href="<?= serverRoot.'/fileGetAny/kb-'.$v->File ?>" target="_blank">
                  <i class="fa fa-download margin-right"></i> File
                </a>
                <?php
                      if ($this->session->userdata('IDdepartementNavigation')== 12) // IT
                      { ?>
                        <br/>
                        <br><a href="javascript:void(0);" class="btnActRemove" data-id="<?=$v->KBID?>" data-no="'+i+'">Remove</a>
                <?php }
                      else
                      {
                        // read navigasi department
                        $inArrDiv = [15,34]; // prodi dan faculty
                        if (!in_array($this->session->userdata('IDdepartementNavigation'), $inArrDiv) ) {
                          if ('NA.'.$this->session->userdata('IDdepartementNavigation') == $this->session->userdata('kb_div'))
                          { ?>
                            <br/>
                            <br><a href="javascript:void(0);" class="btnActRemove" data-id="<?=$v->KBID?>" data-no="'+i+'">Remove</a>

                   <?php  }

                        }
                        else
                        {
                          // prodi dan faculty
                          if($this->session->userdata('IDdepartementNavigation') == 15) // prodi
                          {
                            if ('AC.'.$this->session->userdata('prodi_active_id') == $this->session->userdata('kb_div') )
                            { ?>
                                <br/>
                                <br><a href="javascript:void(0);" class="btnActRemove" data-id="<?=$v->KBID?>" data-no="'+i+'">Remove</a>

                      <?php }
                          }
                          else
                          {
                            // faculty
                            if ('FT.'.$this->session->userdata('faculty_active_id') == $this->session->userdata('kb_div') )
                            { ?>
                                <br/>
                                <br><a href="javascript:void(0);" class="btnActRemove" data-id="<?=$v->KBID?>" data-no="'+i+'">Remove</a><br>

                      <?php }

                          }

                        }
                      }

                     ?>
                <?php if ($selected ==$this->session->userdata('PositionMain')['IDDivision']): ?>
                  <!-- <a href="javascript:void(0);" class="btnActRemove" data-id="<?=$v->KBID?>" data-no="'+i+'">Remove</a> -->
                <?php endif; ?>
              </div>
            </div>
          </li>
          <?php } ?>
        </ul>
      </div>
    </li>
<?php $num++; } }else{echo '<li>No data available</li>';} ?>
<!-- END UPDATED CODE BY FEBRI @ JUNE 2020 -->
