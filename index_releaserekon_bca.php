<?php
  // $sql_01 = "SELECT min(txndate)txndate FROM payment_bca_paid where ISNULL(Status,1)=1";
  // $exec_01 = mssql_query($sql_01);
  // $res_01 = mssql_fetch_assoc($exec_01);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <!--title>.: Sistem Informasi MAF :.</title-->

    <link href="<?php echo HOSTNAME();?>/css/bootstrap/css/bootstrap.css" rel="stylesheet" />
    <script src="<?php echo HOSTNAME();?>/css/bootstrap/js/jquery.min.js"></script>
    <script src="<?php echo HOSTNAME();?>/css/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo HOSTNAME();?>/js/jquery-ui-1.8.16.custom.min.js"></script>

    <script type="text/javascript" src="<?php echo HOSTNAME();?>/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="<?php echo HOSTNAME();?>/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="<?php echo HOSTNAME(); ?>/css/bootstrap/bootstrap.min.js"></script>

    <link rel="stylesheet" href="<?php echo HOSTNAME(); ?>/css/bootstrap/bootstrap.min.css">    

    <!-- HoldOn loading animation -->
    <link rel="stylesheet" href="<?php echo HOSTNAME(); ?>/css/HoldOn.min.css">
    <script type="text/javascript" src="<?php echo HOSTNAME(); ?>/css/HoldOn.min.js"></script>

    <!-- Sweetalert -->
    <link rel="stylesheet" type="text/css" href="<?php echo hostname(); ?>/plugins/sweetalert/sweetalert2.css">
    <script type="text/javascript" src="<?php echo hostname(); ?>/plugins/sweetalert/sweetalert.min.js"></script>

    <!-- Datatables -->
    <link rel="stylesheet" type="text/css" href="<?php echo hostname(); ?>/css/datatables-1.10.16/datatables.min.css">
    <script type="text/javascript" src="<?php echo hostname(); ?>/css/datatables-1.10.16/datatables.min.js"></script>

    <link href="<?php echo HOSTNAME();?>/css/table.css" rel="stylesheet" type="text/css"/>
    
    <style media="screen">
      body{
        font-size: 11px;
        font-family: Arial;
      }
      .ui-datepicker-header, .ui-widget-header{
        color: rgb(37,37,37);
      }
      #top-table tr td{
        padding: 2px 3px;
      }
      #data-table thead tr th{
        background-color: rgb(69,107,119);
        color: #fff;
        text-align: center;
      }
      #data-table tbody tr:nth-child(even){
        background-color: rgb(240,240,240);
      }
      #data-table tbody tr:hover{
        background-color: rgb(255,252,204);
      }
      
      .logo-bank{
        width: 70px;
        height: 50px;
      }

      td.details-control {
          background: url('<?php echo HOSTNAME(); ?>/images/details_open.png') no-repeat center center;
          cursor: pointer;
      }
      tr.shown td.details-control {
          background: url('<?php echo HOSTNAME(); ?>/images/details_close.png') no-repeat center center;
      }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
          var d = new Date();
          var curr_year = d.getFullYear(); 
          $("#tgl_release").datepicker({
            dateFormat  : 'dd/mm/yy',
            yearRange   : '1900:'+curr_year,
            changeMonth : true,
            changeYear  : true,
            minDate     : 0
          });          
          // $("#tgl_release_end").datepicker({
          //   dateFormat  : 'dd/mm/yy',
          //   yearRange   : '1900:'+curr_year,
          //   changeMonth : true,
          //   changeYear  : true
          // });  

        });


    </script>
</head>
<body>
    <div class="panel col-sm-12 col-md-12">
        <!-- HEADER -->
        <div class="panel panel-default" style="margin-top: 5px; margin-bottom: 5px; background-color: #e5ff33;">
            <div class="panel-heading" style="background-color: #ccdfe5;">
              <img style="width:100px;" src="<?php echo HOSTNAME();?>/module/payment/bcava/pict/logo-bca.png">&nbsp;&nbsp;<strong style="font-size: 16px;vertical-align:middle;color:#006280">REKONSILIASI VIRTUAL ACCOUNT BCA</strong>
            </div>
        </div>
        <div class="panel panel-default" id="top-table">
            <div class="panel-body">
            <div class="container-fluid">
                <div class="row" style="display: none;">
                    <div class="progress">
                        <div class="progress">
                          <div class="progress-bar" role="progressbar" aria-valuenow="70"
                          aria-valuemin="0" aria-valuemax="100" style="width:70%">
                            70%
                          </div>
                        </div>
                      </div>
                </div>
                <div class="row">
                    <!-- <input name="tgl_release" type="text" id="tgl_release" readonly="" class="inpSmall" maxlength="50" size="15" value="<?php //echo dateSQLKACO($res_01['txndate']);?>" style="padding:5px 10px; width: 10%; background-color: #dfdddd;" /> s/d 
                    <input name="tgl_release_end" type="text" id="tgl_release_end" class="inpSmall" maxlength="50" size="15" value="" style="padding:5px 10px; width: 10%;" />
                    &nbsp;&nbsp;&nbsp; -->


                    <span>Tanggal Transaksi :</span>&nbsp; &nbsp; &nbsp;
                    <input  name="tgl_release" type="text" id="tgl_release"  class="inpSmall" maxlength="50" size="15" value="<?php echo $_POST['tgl_release'];?>" style="padding:5px 10px; width: 10%; height: ;" /> &nbsp;&nbsp;&nbsp;
                    <!-- <span>Status :</span>&nbsp; &nbsp; &nbsp; -->
                    <?php
                        //echo createcombotanpapilih("maf_parameter", "nilai", "parameter", "where keterangan='sts.release.bri'", "status_rilis", $_POST['status_rilis'], "theme-ijo");
                      ?> &nbsp;&nbsp;&nbsp;

                    <button class="btn btn-sm btn-info" style="height: 35px; font-size: 12px;" onclick="getTglRelease()" id="post_klik" value="post_klik" name="post_klik"><i class="glyphicon glyphicon-search" > </i>&nbsp;<b>CARI</b></button>
                    &nbsp;&nbsp;&nbsp;
                    
                    <div class="pull-right">
                    APLDATE : <strong><?php
                    $sql_apldate = "select top 1 apldate from core_nasabah with(nolock)";
                    $exec_apldate = mssql_query($sql_apldate);
                    $data_apldate = mssql_fetch_assoc($exec_apldate);
                      echo dateSQLKaco($data_apldate['apldate']); ?></strong>

                      <input type="hidden" name="h_apldate" id="h_apldate" value="<?php echo dateSQL(dateSQLKaco($data_apldate['apldate'])); ?>">
                    </div>
                    
                </div>
            </div>
            </div>
        </div>
        
        <!-- TABEL DATA -->
        <div class="container-fluid" style="margin-left: -30px !important; margin-right: -30px !important;">
          <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" style="padding-bottom: 10px;">
          
              <div id="panel-data" class="panel panel-primary">
                <table class="table table-bordered" id="data-table" border="1px solid rgb(190,190,190);">
                    <thead>
                        <tr>
                          <th style="vertical-align:middle;">NO</th>
                          <th style="vertical-align:middle;">TRANSACTION DATE</th>
                          <th style="vertical-align:middle;">TANGGAL SETTLEMENT</th>
                          <th style="vertical-align:middle;">SETTLEMENT</th>
                          <th style="vertical-align:middle;">PAYMENT VA</th>
                          <th style="vertical-align:middle;"></th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        <!-- data tabel -->
                        <tr>
                          <td colspan="6">Tidak ada data yang ditampilkan.</td>
                        </tr>
                        
                    </tbody>
                </table>
              </div>

          </div>

          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 " style="padding-bottom: 10px;">
            <div class="card">
                <div id="loading" class="loading hide" style=" vertical-align: middle; text-align: center;">
                    <img src="images/ajax-loader.gif" alt="spinner"><br><br>
                    <span>Loading Data...</span>
                </div>
                <div class="body" id="rinci_waivedenda">
                    
                <!-- isi -->
                </div>
            </div>
            
          </div>
        </div>
        
<!-- /***************************************************** **/ -->

    </div>


    <script type="text/javascript">
      
      $(document).ready(function(){

        $("#panel-data #data-table").on("click", "tbody td button[name='btn_rinci']", function(e){
            e.preventDefault();
            // alert('tes');
            let tr = $(this).closest('tr');
            let td = tr.find("td");

            let trxdate = $(this).attr("data-trxdate");
            let settlementdate = $(this).attr("data-settlementdate");
            let tgltransaksi = $(this).attr("data-tgltransaksi");
            let stscek = $(this).attr("data-stscek");
            let settlement = $(this).attr("data-settlement");


            // let txndate = td[1].innerHTML;
            // let settlement = td[2].innerHTML;
            // let payment = td[3].innerHTML;


            console.log(trxdate, tgltransaksi, stscek);

            $.ajax({
              type: "POST",
              url: "module/payment/bcava/releaserekon_rinci.php",
              datatype: "php",
              data: {
                trxdate: trxdate,
                settlementdate: settlementdate,
                tgltransaksi: tgltransaksi,
                stscek: stscek,
                settlement: settlement
              },
              cache: false,
              beforeSend: function() {
                // $(".loading").css('display', 'inline-block');
                $(".loading").removeClass("hide");
                $(".loading").addClass("active");
              },
              success: function(html) {
                // $(".loading").css('display', 'none');
                $(".loading").removeClass("active");
                $(".loading").addClass("hide");
                $("#rinci_waivedenda").html(html);
              }
            });
        });

      });

      globResObj= null;

      /* Mengambil tanggal untuk parameter penarikan report */
      function getTglRelease(){ 
          tgl_release = $("input[name=tgl_release]").val();
          // var e = document.getElementById("status_rilis");
          // var status_rilis = e.options[e.selectedIndex].value;
          
          // if (status_rilis=='R') {
          //     document.getElementById('row_form').style.display = "none";
          // }else{
          //     document.getElementById('row_form').style.display = "block";
          // }
          
          if(tgl_release==""){
              alert("Tanggal tidak boleh kosong")
          }else{
            $.ajax({
              queue: true,
              cache: false,
              //dataType:"json",
              type: 'POST',
              url: 'module/payment/bcava/action_api.php',
              data: {
                'action':'request.settlement',
                'tgl_release':tgl_release
              },
              beforeSend:function(){
                  console.log("beforeSend")
                  HoldOn.open({
                      theme: "sk-dot",
                      message: "PLEASE WAIT... ",
                      backgroundColor: "#fcf7f7",
                      textColor: "#000"
                  });
              },
              success: function(response) {
                HoldOn.close();
                  // console.log("response : "+response) 
                  spcData = $("table#data-table > tbody:last-child");
                  spcData.children("tr").remove();
                  resObj = JSON.parse(response);
                  globResObj= resObj;        
                  console.log(globResObj);

                  console.log(resObj.length);
                  
                  var no=1;
                  var vaAmount_total=0;
                  var settlementAmount_total=0;
                  for (i = 0; i < resObj.length; i++) {
                  // if (resObj.length>0) {
                    if (resObj[i].sattlementAmount !== resObj[i].vaAmount) {
                        var merah = "color:red;";
                        var sts_cek = "0";
                    } else{
                        var sts_cek = "1";
                    }
                    
                      markup = "<tr>"+
                                  "<td style=\"text-align: center;\">"+no+"</td>"+
                                  "<td style=\"text-align: center;\">"+resObj[i].transDate+"</td>"+
                                  "<td style=\"text-align: center;\">"+resObj[i].sattlementDate+"</td>"+
                                  
                                  "<td style=\"text-align: right;"+merah+"\" >"+parseInt(resObj[i].sattlementAmount).toLocaleString()+"</td>"+
                                  "<td style=\"text-align: right;"+merah+"\" >"+parseInt(resObj[i].vaAmount).toLocaleString()+"</td>"+
                                  "<td style=\"text-align: center;\">"+"<button style=\"padding-top:0;font-family:sans-serif;\" class=\"btn btn-sm btn-primary\" id=\"btn_rinci\" name=\"btn_rinci\" data-trxdate=\""+resObj[i].transDate+"\" data-settlementdate=\""+resObj[i].sattlementDate+"\" data-settlement=\""+resObj[i].sattlementAmount+"\" data-tgltransaksi="+tgl_release+" data-stscek="+sts_cek+"><i class=\"glyphicon glyphicon-info-sign\" > </i>&nbsp;Rinci</button>"+"</td>"+ 
                                  
                                  // "<td style=\"text-align: center;\">id:"+resObj.data[i].id+"</td>"+
                                                        
                                  "</tr>";
                      spcData.append(markup)
                      no++;
                      // amount_total=amount_total+resObj.Data[i].TransactionAmount;
                      // vaAmount_total = vaAmount_total+resObj[i].vaAmount;
                      // settlementAmount_total = settlementAmount_total+resObj[i].sattlementAmount;
                  }
                  // document.getElementById('total_amount').value=parseInt(amount_total).toLocaleString();                
                  HoldOn.close();
                  // if(amount_total > 0){
                  //     document.getElementById("trf_post_klik").disabled = false;
                  // }else{
                  //     document.getElementById("trf_post_klik").disabled = true;
                  // }
              }
            });
          }
      }

      

      /* Membuat format tanggal jam */
      function formatDate(date) {
          var d = new Date(date),
              month = '' + (d.getMonth() + 1),
              day = '' + d.getDate(),
              year = d.getFullYear(),
              hour = ''+d.getHours(),
              minute = ''+d.getMinutes(),
              second = ''+d.getSeconds();
      
          if (month.length < 2) month = '0' + month;
          if (day.length < 2) day = '0' + day;
          if (hour.length < 2) hour = '0' + hour;
          if (minute.length < 2) minute = '0' + minute;
          if (second.length < 2) second = '0' + second;
      
          return [year, month, day].join('-')+" "+[hour, minute, second].join(':');
          
          //var DateCreated = new Date(Date.parse(Your Date Here)).format("MM/dd/yyyy");
      }

    </script>


</body>
</html>
