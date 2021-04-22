<?php
    ini_set('max_execution_time', 0); // 120 (seconds) = 2 Minutes

    $sql_tgl="SELECT tgl_sistem=dbo.f_getappldate_28()";
    // $exec_tgl=mssql_query($sql_tgl);
    // $res_tgl=mssql_fetch_assoc($exec_tgl);

    // $sql_01="SELECT     txndate,valuedate,lastupdate,lastuserid,period,*
    //           FROM         financore.dbo.corPayment 
    //           WHERE     accountno='260'
    //           and txndate<>Financore.dbo.f_getappldate()
    //           and lastupdate>=Financore.dbo.f_getappldate()";
    // $exec_01=mssql_query($sql_01);
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
          view_data_coraccount: 0,
          view_data_cshjurnal: 0,
          view_data_cshtxn: 0,
          view_data_vatemptxn: 0,
          view_data_vatemprekon: 0,

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
      .readonly{
        background-color: #e3e9e3;
      }
      .logo-bank{
        width: 70px;
        height: 50px;
      }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
          var d = new Date();
          var curr_year = d.getFullYear(); 
          $("#tanggal").datepicker({
            dateFormat  : 'dd/mm/yy',
            yearRange   : '1900:'+curr_year,
            changeMonth : true,
            changeYear  : true
          });          
        });

        </script>
  </head>
  <body>
    <div class="container-fluid">
      <a href="javascript:void(0);" id="return-to-top"><i class="glyphicon glyphicon-arrow-up"></i></a>    
      <div class="panel panel-default" style="margin-top: 5px; margin-bottom: 5px; background-color: #e5ff33;">
          <div class="panel-heading" style="background-color: #ccdfe5;">
            <img style="width:100px;" src="<?php echo HOSTNAME();?>/module/payment/bcava/pict/logo-bca.png">&nbsp;&nbsp;<strong style="font-size: 16px;vertical-align:middle;color:#006280;">UPDATE TXNDATE PAYMENT VA BCA</strong>
          </div>
      </div>     
      <hr>
      <!-- 1. AWAL MENU NAVIGASI -->
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <div class="info-box" target-container="data-kredit">
            <div class="icon">
                <i class="glyphicon glyphicon-usd"> </i>
            </div>

            <div class="content">
              CORPAYMENT
            </div>
          </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <div class="info-box" target-container="view-data-coraccount">
            <div class="icon">
                <i class="glyphicon glyphicon-collapse-down"> </i>
            </div>

            <div class="content">
              CORACCOUNT DETAIL
            </div>
          </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <div class="info-box" target-container="view-data-cshjurnal">
            <div class="icon">
                <i class="glyphicon glyphicon-book"> </i>
            </div>

            <div class="content">
              CSH JURNAL DETAIL
            </div>
          </div>
        </div>        

        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <div class="info-box" target-container="view-data-cshtxn">
            <div class="icon">
                <i class="glyphicon glyphicon-tag"> </i>
            </div>

            <div class="content">
              CSH TXN
            </div>
          </div>
        </div>       

        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <div class="info-box" target-container="view-data-vatemptxn">
            <div class="icon">
                <i class="glyphicon glyphicon-bookmark"> </i>
            </div>

            <div class="content">
              VATEMP TXN
            </div>
          </div>
        </div>         

        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <div class="info-box" target-container="view-data-vatemprekon">
            <div class="icon">
                <i class="glyphicon glyphicon-refresh"> </i>
            </div>

            <div class="content">
              VATEMP REKON
            </div>
          </div>
        </div>        

      </div>
      <!-- AKHIR MENU NAVIGASI -->


      <!-- 2. AWAL MAIN CONTENT -->
      <!-- ************************************************************************************************************************************************************* -->
      <!-- MAIN CONTENT CREATE-->
      <!-- ************************************************************************************************************************************************************* -->

      <div class="main-container data-kredit active">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
          <div class="row">
          
          <div class="panel panel-default">
            <div class="panel-body" style="background: #0f3057; color: #fff; font-size: 17px; padding: 10px;">
              <i class="glyphicon glyphicon-usd"> </i> CORPAYMENT
            </div>
          </div>            
            <div class="row">

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="panel panel-default">
                  <div class="panel-body text-center">
                    <table class="table table-hover" style="border-color: grey;">
                      <thead>                  
                        <tr style="background: #29aab3; color: #fff;">
                          <th class="text-center" style="background: #29aab3; color: #fff; width: 1%; border-left: 1px solid #ddd;">No.</th>
                          <th class="text-center" style="background: #29aab3; color: #fff; width: 5%; border-left: 1px solid #ddd; ">Norek</th>
                          <th class="text-center" style="background: #29aab3; color: #fff; width: 5%; border-left: 1px solid #ddd; ">Nopin</th>
                          <th class="text-center" style="background: #29aab3; color: #fff; width: 5%; border-left: 1px solid #ddd;">Txn Date</th>                    
                          <th class="text-center" style="background: #29aab3; color: #fff; width: 5%; border-left: 1px solid #ddd;">Value Date</th>
                          <th class="text-center" style="background: #29aab3; color: #fff; width: 5%; border-left: 1px solid #ddd;">Last User ID</th>
                          <th class="text-center" style="background: #29aab3; color: #fff; width: 5%; border-left: 1px solid #ddd; border-right: 1px solid #ddd;">Last Update</th>
                        </tr>
                      </thead>
                      <tbody>                  
                      <?php 
                        $no_01=1;
                        while ($res_01=mssql_fetch_assoc($exec_01)) {
                      ?>
                        <tr>
                          <td style="border-left: 1px solid #ddd;"><?php echo $no_01; ?></td>
                          <td style="border-left: 1px solid #ddd; text-align: center;"><?php echo $res_01['NoRek']; ?></td>
                          <td style="border-left: 1px solid #ddd; text-align: center;"><?php echo $res_01['NoPin']; ?></td>
                          <td style="border-left: 1px solid #ddd; text-align: center;"><?php echo dateSQLKACO($res_01['txndate']); ?></td>
                          <td style="border-left: 1px solid #ddd; text-align: center;"><?php echo dateSQLKACO($res_01['ValueDate']); ?></td>
                          <td style="border-left: 1px solid #ddd; text-align: center;"><?php echo $res_01['LastUserId']; ?></td>
                          <td style="border-left: 1px solid #ddd; text-align: center; border-right: 1px solid #ddd;"><?php echo dateSQLKACOTime($res_01['LastUpdate']); ?> WIB</td>
                        </tr>
                      <?php 
                          $no_01++;
                        }
                      ?>                                                                
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              

              <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <div class="panel panel-default">                  
                  <div class="panel-body text-center">
                    <form class="" action="" id="form_update_corpayment" method="post">
                    <table style="width: 100%;">                        
                        <tr>
                            <td style="width: 40%;">Txndate</td>
                            <td>:</td>
                            <td><input  name="txndate_input" type="text" id="txndate_input" readonly="" class="inpSmall readonly" maxlength="50" size="15" value="<?php echo dateSQLKACO($res_tgl['tgl_sistem']); ?>" style="padding:5px 10px; text-align: center;" /></td>
                        </tr>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td align="center">
                                <button class="btn btn-sm btn-success" style="width: 65%; height: 35px; font-size: 12px;" id="trf_post_klik" value="post_klik" name="post_klik"><b>UPDATE TXNDATE</b></button>            
                            </td>
                            <td></td>
                        </tr>
                    </table>
                  </form>
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

      <div class="main-container view-data-coraccount hide">

      </div>

      <!-- ************************************************************************************************************************************************************* -->
      <!-- END MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->

      <!-- ************************************************************************************************************************************************************* -->
      <!-- MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->

      <div class="main-container view-data-cshjurnal hide">

      </div>

      <!-- ************************************************************************************************************************************************************* -->
      <!-- END MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->

      <!-- ************************************************************************************************************************************************************* -->
      <!-- MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->

      <div class="main-container view-data-cshtxn hide">

      </div>

      <!-- ************************************************************************************************************************************************************* -->
      <!-- END MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->

      <!-- ************************************************************************************************************************************************************* -->
      <!-- MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->

      <div class="main-container view-data-vatemptxn hide">

      </div>

      <!-- ************************************************************************************************************************************************************* -->
      <!-- END MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->

      <!-- ************************************************************************************************************************************************************* -->
      <!-- MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->

      <div class="main-container view-data-vatemprekon hide">

      </div>

      <!-- ************************************************************************************************************************************************************* -->
      <!-- END MAIN CONTENT VIEW DATA-->
      <!-- ************************************************************************************************************************************************************* -->

      <!-- ************************************************************************************************************************************************************* -->
      <!-- AKHIR MAIN CONTENT -->
    </div>
  </div>
  </body>
  <script type="text/javascript">
  $(document).ready(function(){

    var defaultButton;    
    $("#form_update_corpayment").submit(function(e){      
          $.ajax({
            url: "module/payment/ecollbni/script.php?act=update_txn_corpayment",
            type: "POST",
            // dataType: "JSON",
            data: 
            $("#form_update_corpayment").serialize(),
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
                     if (data == 1) {
                      swal({
                          title:"Sukses",
                          text: "Update Txndate Success",
                          icon: "success"
                        }).then(function(){                          
                              window.location.reload();
                              e.preventDefault();
                        });
                    }else{
                      swal({
                          icon: 'error',
                          title: 'Oops...',
                          text: "Update Txndate Failed"
                        }).then(function(){
                               window.location.reload();
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
