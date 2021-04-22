<?php
    // ini_set('max_execution_time', 600); // 120 (seconds) = 2 Minutes
    set_time_limit(1000);

    $sql_tgl="SELECT Financore.dbo.f_GetApplDate() tglsistem";
    $exec_tgl=mssql_query($sql_tgl);
    $res_tgl=mssql_fetch_assoc($exec_tgl);

 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Sistem Informasi MAF</title>
    <link rel="stylesheet" href="<?php echo HOSTNAME(); ?>/css/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo HOSTNAME(); ?>/css/bootstrap-4.2.1/css/bootstrap-datepicker3.css">
    <link rel="stylesheet" href="<?php echo HOSTNAME(); ?>/js/magnify/jquery.magnify.min.css">

    <script type="text/javascript" src="<?php echo HOSTNAME();?>/js/jquery.min.3.1.1.js"></script>
    <script type="text/javascript" src="<?php echo HOSTNAME(); ?>/js/jquery-2.11.1.validate.min.js"></script>
    <script type="text/javascript" src="<?php echo HOSTNAME(); ?>/module/hrd/hrd/function.js"></script>

    <script type="text/javascript" src="<?php echo HOSTNAME(); ?>/css/bootstrap/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo HOSTNAME(); ?>/css/bootstrap-4.2.1/js/bootstrap-datepicker.min.js"></script>

    <script type="text/javascript" src="<?php echo HOSTNAME(); ?>/js/select2.min.js"></script>
    <script type="text/javascript" src="<?php echo HOSTNAME(); ?>/js/magnify/jquery.magnify.min.js"></script>

    <link rel="stylesheet" href="<?php echo HOSTNAME(); ?>/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo HOSTNAME(); ?>/css/inquiry-nasabah.css">

    <!-- HoldOn loading animation -->
    <link rel="stylesheet" href="<?php echo HOSTNAME(); ?>/css/HoldOn.min.css">
    <script type="text/javascript" src="<?php echo HOSTNAME(); ?>/css/HoldOn.min.js"></script>

    <!-- Sweetalert -->
    <link rel="stylesheet" type="text/css" href="<?php echo hostname(); ?>/plugins/sweetalert/sweetalert2.css">
    <script type="text/javascript" src="<?php echo hostname(); ?>/plugins/sweetalert/sweetalert.min.js"></script>

    <script type="text/javascript">
      $(document).ready(function() {
        var tgl_sistem = $("input:hidden[name='tgl_sistem']").val();
        /* 3. DATA SET VARIABLE UNTUK REQUEST */
        var data_set = {
          data_kredit: 1,
          view_data: 0,
          view_compare: 0,          
        };

        $(".info-box").on('click', function(event) {
          // alert("tes");
          event.preventDefault();
          var active_div = $("div.active");
          var target = $(this).attr('target-container');  
          // alert("."+target);
          var file_target = target.replace(/-/g, '_');
          active_div.addClass('hide');
          active_div.removeClass('active');

          if(data_set[file_target] == 0){
            $("."+target).html("<h4>LOADING DATA....</h4>");

            /* 4. FILE PHP - TAMBAH FILE SESUAI NAMA*/
            $("."+target).load('module/payment/bcava/path_show_'+file_target+'.php',{tgl_sistem: tgl_sistem },
              function(){

              data_set[file_target] = 1;
            });
          }

          $("."+target).addClass('active');
          $("."+target).removeClass('hide');
        });

        ///* SCROLL WINDOWS *///
        $(window).scroll(function() {
            if ($(this).scrollTop() >= 50) {
                $('#return-to-top').fadeIn(200);
            } else {
                $('#return-to-top').fadeOut(200);
            }
        });
        $('#return-to-top').click(function() {
            $('body,html').animate({
                scrollTop : 0
            }, 200);
        });

      });
    </script>
    <style type="text/css">
      body{
        font-family: 'Roboto', Arial, Tahoma, sans-serif;
        background: #f9f9f929;
        /* background: #e9e9e9; */
        font-size: 11px;
      }
      .tab-menu{
        border: 1px solid black;
        padding: .5em;
      }
      table.table-hover tbody tr:hover{
        background-color: rgba(255, 192, 69, 0.19);
      }      
      .btn-success{
        background-color: #143c94;
      }
      .btn-success:hover{
        background-color: #1a86c1;
      }
      .btn-success:focus{
        background-color: #1a86c1;
      }
      .logo-bank{
        width: 70px;
        height: 50px;
      }
    </style>
  </head>
  <body>
    <div class="container-fluid">
      <a href="javascript:void(0);" id="return-to-top"><i class="glyphicon glyphicon-arrow-up"></i></a>
      <input type="hidden" name="tgl_sistem" value="<?php echo dateSQLKACO($res_tgl['tglsistem']); ?>"> 
      <div class="panel-heading" style="background-color: #e6eff8; border: none; padding-top:20px;padding-bottom:20px;">
              <img style="width:100px;" src="<?php echo HOSTNAME();?>/module/payment/bcava/pict/logo-bca.png">&nbsp;&nbsp;<strong style="color: black; font-size: 13px;">CREATE VIRTUAL ACCOUNT BCA</strong>
      </div>      
      <hr>
      <!-- 1. AWAL MENU NAVIGASI -->
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <div class="info-box" target-container="data-kredit">
            <div class="icon">
                <i class="glyphicon glyphicon-pencil"> </i>
            </div>

            <div class="content">
              CREATE
            </div>
          </div>
        </div>

        <!-- <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <div class="info-box" target-container="view-data">
            <div class="icon">
                <i class="glyphicon glyphicon-tasks"> </i>
            </div>

            <div class="content">
              VIEW DATA
            </div>
          </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <div class="info-box" target-container="view-compare">
            <div class="icon">
                <i class="glyphicon glyphicon-refresh"> </i>
            </div>

            <div class="content">
              VIEW COMPARE
            </div>
          </div>
        </div> -->

      </div>
      <!-- AKHIR MENU NAVIGASI -->


      <!-- 2. AWAL MAIN CONTENT -->
      <!-- ************************************************************************************************************************************************************* -->
      <!-- MAIN CONTENT CREATE-->
      <!-- ************************************************************************************************************************************************************* -->

      <div class="main-container data-kredit active">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="row">          

            <div class="row">

              <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <div class="" style="padding: 20px 0px;">
                      <h5>Tanggal Sistem</h5>
                      <h3>
                        <strong><?php echo datesqlKACO($res_tgl['tglsistem']); ?></strong>
                      </h3>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <div class="" style="padding: 20px 0px;">
                      <form class="" action="" id="form_create" method="post">
                        <button class="btn btn-sm btn-success" style="width: 25%; height: 80px; font-size: 20px;" type="submit" onclick="return confirm('Apakah Anda Yakin Create Tagihan BCAVA?');" id="create" value="Create" name="create">CREATE</button>   
                      </form> 
                    </div>
                  </div>
                </div>
              </div>                         
        </div>

      </div>
    </div>
  </div>

      <!-- ************************************************************************************************************************************************************* -->
      <!-- END MAIN CONTENT -->
      <!-- ************************************************************************************************************************************************************* -->


      <!-- ************************************************************************************************************************************************************* -->
      <!-- MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->

      <div class="main-container view-data hide">

      </div>

      <!-- ************************************************************************************************************************************************************* -->
      <!-- END MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->


      <!-- ************************************************************************************************************************************************************* -->
      <!-- MAIN CONTENT VIEW COMPARE-->
      <!-- ************************************************************************************************************************************************************* -->

      <div class="main-container view-compare hide">

      </div>

      <!-- ************************************************************************************************************************************************************* -->
      <!-- END MAIN CONTENT VIEW COMPARE-->
      <!-- ************************************************************************************************************************************************************* -->
      <!-- AKHIR MAIN CONTENT -->
    </div>
  </div>
  </body>
  <script type="text/javascript">
  $(document).ready(function(){

    var defaultButton;    
    $("#form_create").submit(function(e){      
          $.ajax({
            url: "module/payment/bcava/script.php?act=create_bcava",
            type: "POST",
            timeout: 600000,
            // dataType: "JSON",
            data: 
            $("#form_create").serialize(),
            cache: false,
            beforeSend: function(){
              // $body.addClass("loading");
              HoldOn.open({
                  theme: "sk-dot",
                  message: "PLEASE WAIT... ",
                  backgroundColor: "#fcf7f7",
                  textColor: "#000"
              });
              e.preventDefault();
            },
            success: function(data){
              // console.log(data)
              if (data == 1) {
                  swal({
                    title:"Sukses",
                    text: "Create Success",
                    icon: "success"
                  }).then(function(){            
                        HoldOn.close();              
                        // window.location.reload();
                        e.preventDefault();
                  });
              }else{
                  swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: "Create Failed"
                  }).then(function(){
                        HoldOn.close();
                        // window.location.reload();
                        e.preventDefault();
                });
              }
            }
          });
        e.preventDefault();
      });

  });
</script>
</html>
