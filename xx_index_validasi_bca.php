<?php
    ini_set('max_execution_time', 0); // 120 (seconds) = 2 Minutes
    //$sql_tgl="SELECT distinct txndate from payment_bni_posting";
    $sql_tgl="SELECT Financore.dbo.f_GetApplDate() txndate";
    $exec_tgl=mssql_query($sql_tgl);
    $res_tgl=mssql_fetch_assoc($exec_tgl);

    $sql_tagihan="SELECT jml_account=count(*),total_amount=sum(amount) from payment_bni_tagihan where expireddate > getdate()";
    $exec_tagihan=mssql_query($sql_tagihan);
    $res_tagihan=mssql_fetch_assoc($exec_tagihan);

    $sql_posting="SELECT jml_account=count(*),total_amount=sum(trx_amount) from payment_bni where datetime_expired > getdate()";
    $exec_posting=mssql_query($sql_posting);
    $res_posting=mssql_fetch_assoc($exec_posting);
    
    $sql_validate="SELECT top 1 * from payment_bni_validasi order by txndate desc";
    $exec_validate=mssql_query($sql_validate);
    $res_validate=mssql_fetch_assoc($exec_validate);

    $selisih=abs($res_posting['jml_account']-$res_tagihan['jml_account']);    
    $selisih_amount=abs($res_posting['total_amount']-$res_tagihan['total_amount']); 

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
      .btn-success:disabled{
        background-color: gray;
      }
      .color-orange{
        background-color: #dd7312;
      }
      .notice{
        color:red;
      }
      .logo-bank{
        width: 100px;
        height: 50px;
      }
    </style>
  </head>
  <body>
    <div class="container-fluid">
      <a href="javascript:void(0);" id="return-to-top"><i class="glyphicon glyphicon-arrow-up"></i></a>
      <input type="hidden" name="tgl_sistem" value="<?php echo dateSQLKACO($res_tgl['tglsistem']); ?>"/>   
      <div class="panel-heading" style="background-color: #f1ebf1; border: none;">
              <img class="logo-bank" src="<?php echo HOSTNAME();?>/module/payment/briva/pict/logo-bni.png">&nbsp;&nbsp;<strong style="color: black; font-size: 13px;">VALIDASI DATA POSTING</strong>
      </div>                
      <hr />
      <!-- 1. AWAL MENU NAVIGASI -->
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3">
          <div class="info-box" target-container="data-kredit" style="width: 120%;">
            <div class="icon">
                <i class="glyphicon glyphicon-refresh"> </i>
            </div>

            <div class="content">
              COMPARE DATA TAGIHAN NASABAH PER TANGGAL <b><?php echo dateSQLKACO($res_tgl['txndate']); ?></b>
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

            <div class="row">

              <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <div class="panel panel-default" style="width: 100%;">
                  <table class="table table-hover" style="border-color: grey;">
                    <thead>                  
                      <tr style="background: #29aab3; color: #fff;">
                        <th class="text-center" style="background: #29aab3; color: #fff; width: 2%;">No.</th>
                        <th class="text-center" style="background: #29aab3; color: #fff; width: 15%; border-left: 1px solid #ddd; ">Keterangan</th>
                        <th class="text-center" style="background: #29aab3; color: #fff; width: 3%; border-left: 1px solid #ddd;">Jumlah Account</th>                    
                        <th class="text-center" style="background: #29aab3; color: #fff; width: 3%; border-left: 1px solid #ddd;">Total Amount</th>                    
                      </tr>
                    </thead>
                    <tbody>                  
                        <tr>
                          <td>1</td>
                          <td style="border-left: 1px solid #ddd;">DATA TAGIHAN NASABAH</td>
                          <td style="border-left: 1px solid #ddd;" align="right"><?php echo number_format($res_tagihan['jml_account']); ?></td>
                          <td style="border-left: 1px solid #ddd; text-align: right;"><?php echo number_format($res_tagihan['total_amount']); ?></td>
                        </tr>
                        <tr>
                          <td>2</td>
                          <td style="border-left: 1px solid #ddd;">DATA POSTING VA BNI</td>
                          <td style="border-left: 1px solid #ddd;" align="right"><?php echo number_format($res_posting['jml_account']); ?></td>
                          <td style="border-left: 1px solid #ddd; text-align: right;"><?php echo number_format($res_posting['total_amount']); ?></td>
                        </tr>
                        <tr>
                          <td></td>
                          <td style="border-left: 1px solid #ddd;">SELISIH</td>
                          <td style="border-left: 1px solid #ddd; text-align: right;"><?php echo number_format($selisih); ?></td>
                          <td style="border-left: 1px solid #ddd; text-align: right;"><?php echo number_format($selisih_amount); ?></td>
                        </tr>                                                          
                    </tbody>
                  </table>
            </div>
          </div> 
          <!-- JIKA ADA SELISIH -->
          <?php if($selisih>0){ ?>
          <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
            <div class="panel panel-default">
              <table class="table table-hover" style="border-color: grey;">
                <thead>                  
                  <tr style="background: #dd7312; color: #fff;">
                    <th class="text-center" style="background: #dd7312; color: #fff; width: 1%;">No.</th>
                    <th class="text-center" style="background: #dd7312; color: #fff; width: 3%; border-left: 1px solid #ddd; ">Txndate</th>
                    <th class="text-center" style="background: #dd7312; color: #fff; width: 4%; border-left: 1px solid #ddd;">No Account</th>                    
                    <th class="text-center" style="background: #dd7312; color: #fff; width: 10%; border-left: 1px solid #ddd;">Nama</th>                    
                    <th class="text-center" style="background: #dd7312; color: #fff; width: 5%; border-left: 1px solid #ddd;">Amount</th>                    
                    <th class="text-center" style="background: #dd7312; color: #fff; width: 20%; border-left: 1px solid #ddd;">Keterangan</th>                    
                  </tr>
                </thead>
                <tbody>                  
                    <?php 
                      $sql_blm_posting="select noaccount,txndate,prefix_va,client_va,custcode,nama,amount,keterangan,angs_ke,angs_dari,angs_sd,expireddate
                                        from payment_bni_tagihan with (nolock) 
                                        where expireddate > getdate()
                                            and custcode not in (select custcode from payment_bni with (nolock) where datetime_expired > getdate())";
                      $exec_blm_posting=mssql_query($sql_blm_posting);
                      $nomer=1;
                      while ($res_blm_posting=mssql_fetch_assoc($exec_blm_posting)) {
                        ?>
                        <tr>
                          <td><?php echo $nomer; ?></td>
                          <td align="center"><?php echo dateSQLKACO($res_blm_posting['txndate']); ?></td>
                          <td align="center"><?php echo $res_blm_posting['noaccount']; ?></td>
                          <td align="left"><?php echo $res_blm_posting['nama']; ?></td>
                          <td align="right"><?php echo number_format($res_blm_posting['amount']); ?></td>
                          <td align="left"><?php echo $res_blm_posting['keterangan']; ?></td>
                        </tr>
                    <?php
                      $nomer++;
                      }
                    ?>                                                         
                </tbody>
              </table>
            </div>
          </div>
          <?php } ?>
          <!----->          
        </div>
        <div class="row">
            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                <div class="panel panel-default" style="width: 100%;">
                    <div class="panel-body text-center">
                        <div class="" style="padding: 20px 0px;">
                            <button class="btn btn-sm btn-success" disabled="" style="width: 50%; height: 80px; font-size: 20px;" type="button" id="appv-val" value="approve" name="appv-val">APPROVE</button>
                        </div>
                    </div>
                </div>
             </div> 
        </div> 
        <span class="notice"></span>                      
      </div>
    </div>
  </div>

      <!-- ************************************************************************************************************************************************************* -->
      <!-- END MAIN CONTENT -->
      <!-- ************************************************************************************************************************************************************* -->

      <!-- AKHIR MAIN CONTENT -->
    </div>
  </div>
  </body>
 
</html>
<script type="text/javascript">
$(document).ready(function(){
    /* start document ready */
    approve = $("#appv-val");
    stsvalidasi = '<?php echo $res_validate['stsvalidasi']; ?>';
    selisih = '<?php echo $selisih; ?>';
    //console.log("selisih"+selisih+",stsvalidasi"+stsvalidasi)
    if(selisih != 0 || stsvalidasi == 1){
        approve.attr("disabled","disabled");
        if(stsvalidasi == 1){
            $(".notice").text("*) Data Sudah Diapprove")
        }else{
            $(".notice").text("*) Data Tidak Dapat Diapprove Karena Ada Selisih")
        }
    }else{
        approve.removeAttr("disabled");
    }
    
    approve.click(function(){
        tag_jml = '<?php echo $res_tagihan['jml_account']; ?>';
        pos_jml = '<?php echo $res_posting['jml_account']; ?>';
        tag_amt = '<?php echo $res_tagihan['total_amount']; ?>';
        pos_amt = '<?php echo $res_posting['total_amount']; ?>';
        $.ajax({
            type: 'POST',
            url: 'module/payment/ecollbni/action_api.php',
            data: {'action':'validasi.vabni', 'tag_jml':tag_jml, 'pos_jml':pos_jml, 'tag_amt':tag_amt, 'pos_amt':pos_amt},
        }).done(function(respon) {
            //console.log(respon)
            resPars = parseJson(respon);
            if(resPars.status_code==1){
                icon = "success";
                approve.attr("disabled","disabled");
                $(".notice").text("*) Data Sudah Diapprove")
            }else{
                icon = "warning";
            }
            
            swal({
              title:"Sukses",
              text: resPars.status_desc,
              icon: icon
            }).then(function(){                          
                  
            });   
        });
    });
    
    /* end document ready */
}).ajaxStart(function() {
    HoldOn.open({
        theme: "sk-dot",
        message: "PLEASE WAIT... ",
        backgroundColor: "#fcf7f7",
        textColor: "#000"
    });
}).ajaxComplete(function( event, request, settings ) {
     HoldOn.close();
});

/* custom parse json */
function parseJson(str){
    jsn = "";
    try{
        jsn = JSON.parse(str);
    }catch(e){
        return false;
    }
    return jsn;
}
</script>
