<?php 
include_once("../../../include/fungsi.php");

$txndate = $_POST['trxdate'];
$settlementdate = $_POST['settlementdate'];
$tgltransaksi = substr(dateSQL($_POST['tgltransaksi']),0,4);
$stscek = $_POST['stscek'];
$settlementAmount = $_POST['settlement'];


$year_tgltransaksi = substr(dateSQL($_POST['tgltransaksi']),0,4)."<br>";

$tgl_transaksi = dateSQL($txndate.'/'.$year_tgltransaksi);
$tgl_settlement = dateSQL($settlementdate.'/'.$year_tgltransaksi);

$new_tgl = validateDate($tgl_settlement, 'Y-m-d') !== FALSE ? $tgl_settlement : $tgl_transaksi;

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

$query = "SELECT *,sts=case when status=0 then 'UNPAID' when status=1 then 'PAID' when status=2 then 'RELEASE' end from payment_bca_paid where dbo.tanggal_saja(TransactionDate) <= dbo.tanggal_saja('$new_tgl') and Settlementdate is null ";
$exec = mssql_query($query);
// echo $query;

?>
<style>
  /* The container */
.container-cekbox {
  display: block;
  position: relative;
  padding-left: 35px;
  /* margin-bottom: 12px; */
  cursor: pointer;
  /* font-size: 22px; */
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container-cekbox input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}
  /* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #aaa;
  border-radius:7px;
}

/* On mouse-over, add a grey background color */
.container-cekbox:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container-cekbox input:checked ~ .checkmark {
  background-color: #28a745;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container-cekbox input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container-cekbox .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>
<!-- onsubmit="return confirm('Do you really want to Release Payment?');" -->
<form action="module/payment/bcava/action_api.php?action=submit_release" id="form_rekonrinci" method="POST" >
<input type="hidden" name="h_tgl_settlement" id="h_tgl_settlement" value="<?php echo $new_tgl; ?>">
<div id="panel-data" class="panel panel-primary">
  <table class="table table-bordered" id="data-table" border="1px solid rgb(190,190,190);">
      <thead>
          <tr>
            <th style="vertical-align:middle; width: 1%;">NO</th>
            <th style="vertical-align:middle; width: 8%;">REQUEST ID</th>
            <th style="vertical-align:middle; width: 5%;">TRANSACTION DATE</th>
            <th style="vertical-align:middle; width: 3%;">CONTRACT NO</th>
            <th style="vertical-align:middle; width: 20%;">CUSTOMER NAME</th>
            <th style="vertical-align:middle; width: 8%;">PAYMENT AMOUNT</th>
            <th style="vertical-align:middle; width: 8%;">TOTAL AMOUNT</th>
            <th style="vertical-align:middle; width: 3%;">STATUS</th>
            <th style="vertical-align:middle; width: 5%;">
              <!-- <input type="checkbox" onClick="toggle(this)" /> Toggle All -->
              <label class="container-cekbox">Check All
                <input type="checkbox" class="custom-control-input" id="checkAll" />
                <span class="checkmark"></span>
              </label>
              <!-- <input type="checkbox" class="custom-control-input checkAmountAll" id="checkAmountAll" onclick="checkedAll(checkAmountAll)" value=""> -->
            </th>
          </tr>
      </thead>
      <tbody>
          <?php 
            $no = 1;
            $totalAmount = 0;
            $counter = 0;
            $sts_unpaid = 0;
            $sts_release = 0;
            while ($data = mssql_fetch_assoc($exec)) { ?>
              <tr>
                <td style="text-align: center;"><?php echo $no; ?></td>
                <td style="text-align: center;"><?php echo $data['RequestID']; ?></td>
                <td style="text-align: center;"><?php echo dateSQLKacoTime($data['TransactionDate']); ?></td>
                <td style="text-align: center;"><?php echo $data['CustomerNumber']; ?></td>
                <td style="text-align: left;"><?php echo $data['CustomerName']; ?></td>
                <td style="text-align: right;"><?php echo number_format($data['PaidAmount']); ?></td>
                <td style="text-align: right;"><?php echo number_format($data['TotalAmount']); ?></td>
                <td style="text-align: center;"><?php echo $data['sts']; ?></td>
                <td >
                <?php 
                //if($data['Status']!=2){ ?>
                <label class="container-cekbox">Check
                  <input type="checkbox" class="custom-control-input checkAmount" id="checkAmount" name="checkAmount[<?php echo $data['RequestID'].":".$data['CustomerNumber']; ?>]" value="<?php echo $data['PaidAmount']; ?>" >
                  <span class="checkmark"></span>
                </label>
                  
                <?php //} ?>

                  <!-- <input type="hidden" class="custom-control-input hiddenCheck" id="hiddenCheck" name="hiddenCheck[<?php echo $data['RequestID']; ?>]" value="<?php //echo $data['CustomerNumber']; ?>" > -->
                </td>
              </tr>
          <?php
            $no++;
            $totalAmount += $data['PaidAmount'];
            $statusRelease = $data['sts'];
              if($data['Status']==1){
                $counter++;
              }
              if($data['Status']==0){
                $sts_unpaid++;
              }
              if($data['Status']==2){
                $sts_release++;
              }
            }
          ?>
          <!-- <tr>
            <td colspan="5" style="text-align:center;"><strong>Total</strong></td>
            <td style="text-align:right;"><strong><?php echo number_format($totalAmount); ?></strong></td>
            <td colspan="2"></td>
          </tr> -->

      </tbody>
  </table>
  
</div>

<?php 
/**ALERT JIKA ADA PAYMENT YANG BELUM DITARIK */
if($sts_unpaid>0){ ?>
  <div class="alert alert-danger">
    <strong>Perhatian!</strong> ada Transaksi yang belum ditarik, harap tarik payment di menu Tarik Payment.
  </div>
<?php } ?>

<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 pull-right" style="padding-bottom: 10px;margin-left: -15px !important;">
  <div id="panel-data" class="panel panel-primary">
    <table class="table table-bordered" id="" border="1px solid rgb(190,190,190);">
      <tr>
        <td style="width:560px;"><strong>Total VA Amount</strong></td>
        <td style="text-align:right; width:250px;">
          <!-- <strong><?php echo number_format($totalAmount); ?></strong> -->
          <strong><input type="text" class="form-control text-right total_amountva" style="height:auto;padding:3px 5px;font-size:11px;color:#333;" id="total_amountva" readonly></strong>
          <input type="hidden" name="h_total_amountva" class="h_total_amountva" value="0">
        </td>
      </tr>
      <tr>
        <td><strong>Total Settlement Amount</strong></td>
        <td style="text-align:right;">
          <strong><?php echo number_format($settlementAmount); ?></strong>
          <input type="hidden" name="h_settlementAmount" id="h_settlementAmount" class="h_settlementAmount" value="<?php echo $settlementAmount; ?>">
        </td>
      </tr>
      <tr>
        <td></td>
        <td style="text-align:right;">


        <?php 
          /**JIKA TRANSAKSI SUDAH RELEASE SEMUA */
          if($counter==0){ ?>
            <?php echo $counter; ?> belum Release.  
          <?php 
          /**JIKA ADA TRANSAKSI BELUM DITARIK */
          } elseif($sts_unpaid>0) {
            echo "ADA TRANSAKSI YANG BELUM DITARIK";
            
          } else { ?>
            <!-- <button class="btn btn-sm btn-success" disabled="" style="height: 35px; font-size: 12px;" onclick="getDoRelease(0)" id="release_post_klik" value="post_klik" name="post_klik"><i class="glyphicon glyphicon-check" style="width: 20px;"> </i><b>RELEASE</b></button>  -->

            <button type="submit" class="btn btn-sm btn-success" disabled="" style="height: 35px; font-size: 12px;" id="release_post_klik" value="post_klik" name="post_klik"><i class="glyphicon glyphicon-check" style="width: 20px;"> </i><b>RELEASE</b></button>
          <?php
          }
        ?>
        </td>
      </tr>
    </table>
  </div>
</div>

</form>



<script type="text/javascript">
$(document).ready(function(){
    
    /**Check all */
    $("#checkAll").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);

        var total = 0;
        $('input:checkbox:checked').each(function(){
          total += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
        }); 
        console.log(total);
        $(".total_amountva").val(Comma(total));
        $(".h_total_amountva").val(total);

        /**Control tombol release */
        if ($(".h_total_amountva").val() == $(".h_settlementAmount").val() && $(".total_amountva").val()!=0) {
          $('#release_post_klik').prop('disabled',false);
        } else { 
          $('#release_post_klik').prop('disabled',true);
        }
    });

    /**Loading when release */
    $("form#form_rekonrinci").on("submit", function(){
      HoldOn.open({
          theme: "sk-dot",
          message: "PROCESSING RELEASE... ",
          backgroundColor: "#fcf7f7",
          textColor: "#000"
      });
    });

    /**Ceklist hitung amount */
    $('table#data-table').on("change",".checkAmount", function (){
      // alert('tes');
      var total = 0;
      $('input:checkbox:checked').each(function(){
        total += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val());
      });   
      // console.log(total);
  
      $(".total_amountva").val(Comma(total));
      $(".h_total_amountva").val(total);
      console.log($(".h_total_amountva").val());
      console.log($(".h_settlementAmount").val());
      
      /**Control tombol release */
      if ($(".h_total_amountva").val() == $(".h_settlementAmount").val() && $(".total_amountva").val()!=0) {
        
        $('#release_post_klik').prop('disabled',false);
      } else {
        
        $('#release_post_klik').prop('disabled',true);
      }
      
    });



    // $('table#data-table').on("click","tr", function (){
    //   // alert('tes');
    //   ini = $(this);

    //   let nilai = ini.find("input[type='checkbox']:checked").length;

    //   var total = 0;

    //   if(nilai > 0){
    //     ini.find(".checkAmount").prop('checked',false);

    //     /**Kurangi nominal jika uncek */
    //     let d = parseInt(ini.find("input[type='checkbox']").val());
    //     let cNom = isNaN(d)?0:d;
    //     let nominal = parseInt($(".h_total_amountva").val()) - cNom;
        
    //     $(".h_total_amountva").val(nominal);
    //     $(".total_amountva").val(Comma(nominal));
    //     // alert('tes');
    //   }
    //   else{
    //     ini.find(".checkAmount").prop('checked',true);

    //     /**Tambah nominal di diceklist */
    //     let d = parseInt(ini.find("input[type='checkbox']").val());
    //     let cNom = isNaN(d)?0:d;
    //     let nominal = parseInt($(".h_total_amountva").val()) + cNom;
        

    //     $(".h_total_amountva").val(nominal);
    //     $(".total_amountva").val(Comma(nominal));
    //     console.log(parseInt($(".h_settlementAmount").val()));
    //     console.log(parseInt(ini.find("input[type='checkbox']").val()));

    //   }
      
    // });

    
});



/**Fungsi Release */
function getDoRelease(id_x){ 
    let tgl_release = $("#h_apldate").val();
    let tgl_settlement = $("#h_tgl_settlement").val();
    
    // alert(tgl_settlement);
    alert(globResObj);

    // if(tgl_settlement==""){
    //     alert("Tanggal tidak boleh kosong");
    // }else{

    //   if (id_x==0) {
    //       HoldOn.open({
    //         theme: "sk-dot",
    //         message: "PLEASE WAIT... ",
    //         backgroundColor: "#fcf7f7",
    //         textColor: "#000"
    //       });
    //   }
    //   if (id_x<globResObj.length) {
    //     $.ajax({
    //         queue: true,
    //         cache: false,
    //         type: 'POST',
    //         url: 'module/payment/bcava/action_api.php',
    //         data: {
    //           'action':'rilis.vabni',
    //           'tgl_release':tgl_release,
    //           'custcode':globResObj[id_x].contractno, 
    //           'traceno':globResObj[id_x].TraceNo
    //         },
    //         beforeSend:function(){
              
              
    //         },
    //         success: function(response) {
    //             console.log(response) 
                
    //             if(id_x === (globResObj.length-1)){
    //               HoldOn.close();                    
    //               swal({
    //                   title:"Sukses",
    //                   text: "Release Success",
    //                   icon: "success"
    //                 }).then(function(){      
    //                     document.getElementById('total_amount').value = "";                
    //                     document.getElementById("release_post_klik").disabled = true;
    //                 });
                    
    //             }else{
    //                 setTimeout(function(){
    //                     getDoRelease(id_x+1); 
    //                 }, 100);
    //             }
    //         }
    //     }); 
    //   }            
    // }   
    
}



function HapusKoma(n){
    return parseFloat(n.replace(/,/g, ''));
}

function numberWithCommas(n) {
    var parts = n.toString().split(".");
    return parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (parts[1] ? "." + parts[1] : "");
}

function Comma(Num) {
    Num += '';
    Num = Num.replace(/,/g, '');

    x = Num.split('.');
    x1 = x[0];

    x2 = x.length > 1 ? '.' + x[1] : '';


    var rgx = /(\d)((\d{3}?)+)$/;

    while (rgx.test(x1))

        x1 = x1.replace(rgx, '$1' + ',' + '$2');

    return x1 + x2;

}

</script>