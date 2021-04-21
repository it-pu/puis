<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<title>Podomoro University</title>
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo base_url('images/icon/favicon.png'); ?>">
    <?php echo $include; ?>
</head>

<body class="theme-dark">
	<?php echo $header; ?>
	<div id="container" <?php echo (isset($ClassContainer)) ? 'class ="'.$ClassContainer.'"' : '' ?> >


		<?php echo $navigation; ?>
		<!-- <div id="navigation">

		</div> -->

		<div id="content">
			<div class="container" style="position:relative;">

				<!-- Breadcrumbs line && Page Header -->
				<?php echo $crumbs; ?>
				<!-- /Breadcrumbs line && /Page Header -->


				<!--=== Page Content ===-->
				<?php echo $content; ?>

<!--                <div style="position: absolute;-->
<!--                    right: 0;-->
<!--                  bottom: 0px;-->
<!--                  left: 0;text-align: center;">-->
<!--                    <div class="row">-->
<!--                        <div class="col-md-12">-->
<!---->
<!--                            <p style="border-top: 1px solid #ccc;padding-top: 10px;font-style: italic;">-->
<!--                                --- IT PU, We Made With-->
<!--                                <i class="fa fa-heart" style="color: red;" aria-hidden="true"></i> And-->
<!--                                <i class="fa fa-coffee bs-tooltip" aria-hidden="true"  data-placement="top"-->
<!--                                   data-original-title="udah pada ngopi belon? diem diem bae"></i> ----->
<!--                            </p>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->

			</div>
			<!-- /.container -->

		</div>
	</div>

    <form id="formGlobalToken" action="" target="_blank" hidden method="post">
        <textarea id="dataToken" class="hide" hidden readonly name="token"></textarea>
    </form>

    <?php

    $ServerName = $_SERVER['SERVER_NAME'];
    if($ServerName=='localhost'){

    ?>

    <style>
        div#ex {
            width:535px;
            white-space:nowrap;
            overflow-x:scroll;
            border: none;
        }

        div#ex::-webkit-scrollbar {
            width: 6px;
            height: 10px;
            background-color: #F5F5F5;
        }
        div#ex::-webkit-scrollbar-thumb {
            background-color: #607d8b;
        }
        div#ex::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #F5F5F5;
        }

        div#ex ul {
            list-style-type:none;
            margin-bottom: 0px;
            padding-inline-start: 0px;
        }
        div#ex ul li {
            border:1px solid black;
            display:inline-block;
            width:176px;
        }
    </style>


    <style>
        .live-chat {
            position: fixed;
            bottom: 0px;
            right: 265px;
            z-index: 3;
        }
        .user-chat-box {
            width: 250px;
            min-height: 100px;
            background: #293541;
            padding-top: 3px;
            color: #ffffff;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            font-size: 12px;
        }
    </style>

    <style>
        .chat-box {
            position: fixed;
            bottom: 0px;
            right: 5px;
            width: 100px;
            min-height: 30px;
            background: #293541;
            text-align: center;
            padding-top: 9px;
            z-index: 10;
            color: #ffffff;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            font-size: 12px;
            cursor: pointer;
        }
        .online-box {
            position: fixed;
            bottom: 0px;
            right: 5px;
            width: 250px;
            min-height: 100px;
            background: #293541;
            padding-top: 3px;
            z-index: 10;
            color: #ffffff;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
            font-size: 12px;
        }

        .online-box-body .list-group-item {
            background-color: #334251;
            border: 1px solid #252d38;
            color: #d4d4d4 !important;
        }
        .online-box-body .list-group-item:hover, .online-box .list-group-item:focus {
            /*background-color: #fff0;*/
            background-color: #246cca;
        }

        .online-box-body .list-group-item .fa {
            position: absolute;
            top: 17.5px;
            right: 10px;
            color: #04c304;
        }

        .online-box-header {
            padding: 5px 10px 5px 15px;
        }
        .online-box-header button {

            padding: 1px 4px 0px 4px;
            border-radius: 16px;
            color: #ffffff;
            background: #151b22;
        }
        .online-box-header .btn-act-header {
            position: absolute;
            top: 5px;
            right: 9px;
        }
        .online-box-img-profile {
            width: 28px;
            border-radius: 26px;
            margin-right: 5px;
            border: 1px solid #eee;
        }
    </style>

    <div class="chat-box">
        <i class="fa fa-circle" style="color: green;margin-right: 3px;"></i> Live Chat
    </div>

    <div id="onlineList" class="online-box hide">
        <div class="online-box-header">
            <b>Contact</b>
            <div class="btn-act-header">
                <button class="btn btn-default btn-search-box"><i class="fa fa-cog"></i></button>
                <button class="btn btn-default btn-close-box"><i class="fa fa-minus"></i></button>
            </div>

        </div>
        <div class="online-box-body" style="padding: 0px;height: 300px;">
            <div class="list-group" id="listOnlineChat" style="margin-bottom: 0px;"></div>
        </div>

        <div class="online-box-footer">
            <textarea class="form-control form-message" placeholder="Search..." rows="1"></textarea>
        </div>

    </div>

    <style>
        .box-chat {
            /*right: 200px !important;*/
        }
        .online-box-footer {
            padding: 10px;
        }

        .online-box-footer .btn-act {
            margin-top: 10px;
            text-align: right;
        }
        .online-box-footer .btn-act span {
            margin-right: 10px;
            color: #d5e7ff;
        }
        .online-box-footer .btn-act a {
            margin-right: 10px;
            color: #a9caf7;
        }
        .online-box-footer .btn-act button {
            padding: 2px 15px 2px 15px;
            border-radius: 15px;
            color: #fff;
            background: #1174f7;
        }
        .online-box-footer .form-message {
            resize: none;
            background-color: #334251;
            border: 1px solid #293541;
            border-radius: 10px;
            color: #FFFFFF;
        }
    </style>

    <div class="live-chat hide">

        <div id="ex">
            <ul>
                <li>HEADER</li>
                <li>Item1</li>
                <li>Item2</li>
                <li>Item3</li>
                <li>Item4</li>
                <li>Item5</li>
                <li>Item6</li>
                <li>Item7</li>
                <li>Item8</li>
                <li>Item9</li>
                <li>Item10</li>
                <li>Item11</li>
                <li>Item12</li>
                <li>Item13</li>
                <li>Item14</li>
                <li>Item15</li>
                <li>Item16</li>
                <li>Item17</li>
                <li>Item18</li>
                <li>Item19</li>
                <li>Item20</li>
                <li>Item21</li>
                <li>Item22</li>
                <li>Item23</li>
                <li>Item24</li>
                <li>Item25</li>
                <li>Item26</li>
                <li>Item27</li>
                <li>Item28</li>
                <li>Item29</li>
                <li>Item30</li>
                <li>Item31</li>
                <li>Item32</li>
                <li>Item33</li>
                <li>Item34</li>
                <li>Item35</li>
                <li>Item36</li>
                <li>Item37</li>
                <li>Item38</li>
                <li>Item39</li>
                <li>Item40</li>
                <li>Item41</li>
                <li>Item42</li>
                <li>Item43</li>
                <li>Item44</li>
                <li>Item45</li>
                <li>Item46</li>
                <li>Item47</li>
                <li>Item48</li>
                <li>Item49</li>
                <li>Item50</li>
            </ul>
        </div>

        <div class="hide user-chat-box box-chat">
            <div class="online-box-header">
                <b>Nandang Mulyadi</b>
                <div class="btn-act-header">
                    <button class="btn btn-default btn-search-box"><i class="fa fa-times"></i></button>
                    <button class="btn btn-default btn-close-box"><i class="fa fa-minus"></i></button>
                </div>

            </div>

            <style>
                .online-box-body {
                    overflow: auto;
                    background: #334251;
                    height: 200px;
                    padding: 9px;
                }

                .online-box-body::-webkit-scrollbar {
                    width: 6px;
                    height: 6px;
                    background-color: #F5F5F5;
                }
                .online-box-body::-webkit-scrollbar-thumb {
                    background-color: #607d8b;
                }
                .online-box-body::-webkit-scrollbar-track {
                    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
                    background-color: #F5F5F5;
                }

                .online-box-body .chat-box-left {
                    float: left;
                    background: #293541;
                    padding: 10px;
                    border-radius: 19px;
                    width: 193px;
                    margin-bottom: 10px;
                    border-top-left-radius: 0px;
                }
                .online-box-body .chat-box-right {
                    float: right;
                    background: #246cca;
                    padding: 10px;
                    border-radius: 19px;
                    width: 193px;
                    margin-bottom: 10px;
                    border-bottom-right-radius: 0px;
                }
            </style>

            <div class="online-box-body">
                <div class="chat-box-left">
                    Untuk kamu aja yah gan
                </div>
                <div class="chat-box-left">
                    Untuk kamu aja yah gan
                </div>
                <div class="chat-box-right">
                    Untuk kamu aja yah gan
                </div>
            </div>

            <div class="online-box-footer">
                <textarea class="form-control form-message" placeholder="Type your message..."></textarea>
                <div class="btn-act">
                    <span>Attachment : </span>
                    <a href="#"><i class="fa fa-picture-o"></i></a>
                    <a href="#"><i class="fa fa-link"></i></a>
                    <a href="#"><i class="fa fa-file"></i></a>

                    <button class="btn btn-default"><i class="fa fa-paper-plane"></i></button>
                </div>
            </div>

        </div>

        <div class="hide user-chat-box box-chat">
            <div class="online-box-header">
                <b>Nandang Mulyadi</b>
                <div class="btn-act-header">
                    <button class="btn btn-default btn-search-box"><i class="fa fa-times"></i></button>
                    <button class="btn btn-default btn-close-box"><i class="fa fa-minus"></i></button>
                </div>

            </div>

            <style>
                .online-box-body {
                    overflow: auto;
                    background: #334251;
                    height: 200px;
                    padding: 9px;
                }

                .online-box-body::-webkit-scrollbar {
                    width: 6px;
                    height: 6px;
                    background-color: #F5F5F5;
                }
                .online-box-body::-webkit-scrollbar-thumb {
                    background-color: #607d8b;
                }
                .online-box-body::-webkit-scrollbar-track {
                    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
                    background-color: #F5F5F5;
                }

                .online-box-body .chat-box-left {
                    float: left;
                    background: #293541;
                    padding: 10px;
                    border-radius: 19px;
                    width: 193px;
                    margin-bottom: 10px;
                    border-top-left-radius: 0px;
                }
                .online-box-body .chat-box-right {
                    float: right;
                    background: #246cca;
                    padding: 10px;
                    border-radius: 19px;
                    width: 193px;
                    margin-bottom: 10px;
                    border-bottom-right-radius: 0px;
                }
            </style>

            <div class="online-box-body">
                <div class="chat-box-left">
                    Untuk kamu aja yah gan
                </div>
                <div class="chat-box-left">
                    Untuk kamu aja yah gan
                </div>
                <div class="chat-box-right">
                    Untuk kamu aja yah gan
                </div>
            </div>

            <div class="online-box-footer">
                <textarea class="form-control form-message" placeholder="Type your message..."></textarea>
                <div class="btn-act">
                    <span>Attachment : </span>
                    <a href="#"><i class="fa fa-picture-o"></i></a>
                    <a href="#"><i class="fa fa-link"></i></a>
                    <a href="#"><i class="fa fa-file"></i></a>

                    <button class="btn btn-default"><i class="fa fa-paper-plane"></i></button>
                </div>
            </div>

        </div>
    </div>

    <script>

        $(document).ready(function () {
            getUserOnlineChat();
        });

        function getUserOnlineChat(){
            var token = jwt_encode({action:'getUserOnlineChat'},'UAP)(*');
            var url = base_url_js+'api4/__crudLiveChat';
            $.post(url,{token:token},function (jsonResult) {
                if(jsonResult.length>0){
                    $('#listOnlineChat').empty();
                    $.each(jsonResult,function (i,v) {
                        $('#listOnlineChat').append('<a href="javascript:void(0);" data-nip="'+v.NIP+'" class="list-group-item user-live-chat">' +
                            '                    <img data-src="https://pcam.podomorouniversity.ac.id/uploads/employees/'+v.Photo+'" class="online-box-img-profile img-fitter">' +
                            '                    '+v.Name+' ' +
                            '                    <i class="fa fa-circle"></i>' +
                            '                </a>');
                    });

                    $('.img-fitter').imgFitter({
                        // CSS background position
                        backgroundPosition: 'center center',
                        // for image loading effect
                        fadeinDelay: 400,
                        fadeinTime: 1200
                    });;
                }
            });
        }

        $('.chat-box').click(function () {
            $('#onlineList').removeClass('hide');
            $('#onlineList').animateCss('slideInUp',function () {
                $('.chat-box').addClass('hide');
            });
        });

        $('.btn-close-box').click(function () {
            $('#onlineList').animateCss('slideOutDown',function () {
                $('#onlineList').addClass('hide');
                $('.chat-box').removeClass('hide');
            });
        });

        // $('.trigger').click(function() {
        //     if ($("#Fader").hasClass("slider"))
        //         $("#Fader").removeClass("close").addClass("slider");
        //     else
        //         $("#Fader").removeClass("slider").addClass("close");
        // });
    </script>

    <?php } ?>


    <!-- ADDED BY FEBRI @ MARCH 2020 -->
    <?php if(!empty($showNotif)){
    if($showNotif){ ?>
    <style>
        .box-notif.ntf-1{background: #f3e8af85;}
        .box-notif{cursor: pointer;border-bottom:1px solid #eaeaea;display: inline-flex;width: 100%;margin-bottom: 10px;padding: 10px;}
        .box-notif > .picture{width:50px;max-height:50px;margin-right: 10px}
        .box-notif > .info > .created{font-weight: bold;}
        .box-notif > .info{width: 100%}
    </style>
	
	<script>
    $(document).ready(function(){
    	var url = base_url_js+'api/__crudLog';
        var data = {
            action : 'readLog',
            UserID : sessionNIP,
            StatusRead : true
        };

        var token = jwt_encode(data,'UAP)(*');
        $.post(url,{token:token},function(response) {
			if(!jQuery.isEmptyObject(response)){
				console.log(response.Details);
				if(response.Details.length > 0){
					var appendList = "";
					$.each(response.Details,function(k,v){
						appendList += '<div class="box-notif NotificationLinkRead " id_logging_user = "'+v.ID_logging_user+'" onClick="location.href=\''+base_url_js+v.URLDirect+'\'"><img src="'+v.Icon+'" class="picture img-rounded pull-left"><div class="info"><span class="date pull-right"><i class="fa fa-clock-o"></i> '+moment(v.CreatedAt).format('dddd, DD MMM YYYY HH:mm:ss')+'</span><p class="created">'+v.CreatedName+'</p><p class="title">'+v.Title+'</p></div></div>';
					});
					$("#GlobalModal .modal-header").html('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> <h4 class="modal-title" id="exampleModalLabel">Notifications</h4>');
					$("#GlobalModal .modal-body").html('<div style="overflow:auto;max-height:400px;">'+appendList+'</div>');
					$("#GlobalModal .modal-footer").html('<a href="'+base_url_js+'ShowLoggingNotification">View all notifications</a>');
					$("#GlobalModal").modal("show");
				}
			}
        });
    });
    </script>
    <?php } } ?>
    
    <!-- END ADDED BY FEBRI @ MARCH 2020 -->

<script>
        <?php if (isset($dataMenuShare)): ?>
            var data = '<?php if(count($dataMenuShare)>0) { ?>'+
            '<div class="sidebar-title">'+
                          '<span>Shared Menu</span>'+
                        '</div>'+
                        '<ul id="nav">'+
                        '<?php foreach ($dataMenuShare AS $item){ ?>'+
                        '<li class="<?php if($this->uri->segment(2)== strtolower(str_replace(" ", "-",$item['Name']))){echo"current";}?>">'+
                           ' <?php if(count($item['DataLevel_1'])>0){ ?>'+
                                '<a href="javascript:void(0);">'+
                                '    <i class="<?= $item['Icon']; ?>"></i>'+
                                '    <?= $item['Name']; ?>'+
                                '    <i class="arrow <?= ($this->uri->segment(2)==strtolower(str_replace(" ", "-",$item['Name']))) ? 'icon-angle-down' : 'icon-angle-left'?>"></i>'+
                                '</a>'+
                                '<ul class="sub-menu">'+
                                '    <?php foreach ($item['DataLevel_1'] AS $item1) { ?>'+
                                '        <li class="<?= ($this->uri->segment(3)==strtolower(str_replace(" ", "-",$item1['Name']))) ? "current open" : ""?>">'+
                                '            <a href="<?= base_url( $item1['Route']) ?>">'+
                                '                <i class="icon-angle-right"></i>'+
                                '                <?= $item1['Name']; ?>'+
                                '            </a>'+
                                '        </li>'+
                                '    <?php } ?>'+
                                '</ul>'+
                            '<?php } else { ?>'+
                                '<a href="<?php echo base_url($item['Route']);?>">'+
                                   ' <i class="<?= $item['Icon']; ?>"></i>'+
                                    '<?= $item['Name']; ?>'+
                               ' </a>'+
                           '<?php  } ?>'+
                        '</li>'+
                    '<?php } ?>'+
                '</ul>'+
                 '<?php } ?>';
                 $('#nav').after('<div>'+data+'</div>');
                // document.getElementById("shareMenu").innerHTML=data;
        <?php endif ?>
</script>

</body>
</html>
