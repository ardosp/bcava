<?php
$sql = "select a.trx_id,a.custcode,a.trx_amount,a.datetime_expired,a.virtual_account,a.customer_name,a.customer_email,a.customer_phone,
        a.description,a.angs_ke,a.angs_dari,a.angs_sd
        from ITDept.dbo.payment_bni_posting a
        left join ITDept.dbo.payment_bni b on a.trx_id = b.trx_id
        where b.trx_id is null order by a.custcode asc";
$exec = mssql_query($sql)or die ("Error Query [".$sql."]");
$Num_Rows = mssql_num_rows($exec);

$Per_Page = 500;
if($Num_Rows<=$Per_Page)  
{  
    $Num_Pages =1;  
}  
else if(($Num_Rows % $Per_Page)==0)  
{  
    $Num_Pages =($Num_Rows/$Per_Page) ;  
}  
else  
{  
    $Num_Pages =($Num_Rows/$Per_Page)+1;  
    $Num_Pages = (int)$Num_Pages;  
} 

/** pagging JSON */
$idxPage = array();
if($Num_Rows > 0){
    for($Page=1; $Page<=$Num_Pages; $Page++){
        $Page_Start = (($Per_Page*$Page)-$Per_Page); 
        $Page_End = $Per_Page * $Page;  
        if ($Page_End > $Num_Rows){  
            $Page_End = $Num_Rows;  
        }  
        
        $row_page = array();
        
        for($i=$Page_Start;$i<$Page_End;$i++){ 
            $col = array();
            $col['no'] = ($i+1);
            $col['trx_id'] = mssql_result($exec,$i,"trx_id");
            $col['custcode'] = mssql_result($exec,$i,"custcode");
            $col['trx_amount'] = mssql_result($exec,$i,"trx_amount");
            $col['datetime_expired'] = dateTimeSQL(isnull(mssql_result($exec,$i,"datetime_expired"),""));
            $col['virtual_account'] = trim(mssql_result($exec,$i,"virtual_account"));
            $col['customer_name'] = trim(mssql_result($exec,$i,"customer_name"));
            $col['customer_email'] = trim(mssql_result($exec,$i,"customer_email"));
            $col['customer_phone'] = trim(mssql_result($exec,$i,"customer_phone"));
            $col['description'] = substr(trim(mssql_result($exec,$i,"description")),0,99);
            $col['angs_ke'] = trim(mssql_result($exec,$i,"angs_ke"));
            $col['angs_dari'] = trim(mssql_result($exec,$i,"angs_dari"));
            $col['angs_sd'] = trim(mssql_result($exec,$i,"angs_sd"));
            array_push($row_page,$col);
        }
        $idxPage[$Page] = $row_page;
    }
}
$jsonIdxPage = json_encode($idxPage);
//echo  $jsonIdxPage ;
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
    
    <!-- Sweetalert -->
    <link rel="stylesheet" type="text/css" href="<?php echo hostname(); ?>/plugins/sweetalert/sweetalert2.css">
    <script type="text/javascript" src="<?php echo hostname(); ?>/plugins/sweetalert/sweetalert.min.js"></script>
    
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
        text-align: center
      }
      #data-table tbody tr:nth-child(even){
        background-color: rgb(240,240,240);
      }
      #data-table tbody tr:hover{
        background-color: rgb(255,252,204);
      }
      
      /*.stopwatch {
          font-size: 30px;
          height: 100%;
          line-height: 30px;
          text-align: center;
        }*/

    #stopwatch {
      font-size: 30px;
      height: 100%;
      line-height: 30px;
      text-align: center;
    }
    #sw-time {
      font-size: 48px;
      font-weight: bold;
      text-align: center;
    }
    #sw-rst, #sw-go {
      width: 45%;
      box-sizing: padding-box;
      padding: 10px;
      display: inline-block;
      background: #aa2f2f;
      border: 0;
      color: #fff;
    }
    .logo-bank{
        width: 100px;
        height: 50px;
    }
    </style>
    <script type="text/javascript">
    /** POSTING VERSI 3 */
    const TOTAL_ROW = <?php echo $Num_Rows; ?>;
    const MAX_PAGE = <?php echo $Num_Pages; ?>;
    prosCencel = false;
    prosSelesai = false;
    
    nowPage = 1; 
    idxPerPage = 0;
    /** fungsi jquery */
    $(document).ready(function(){
        post_klik = $("button#post_klik");
        cancel_klik = $("button#cancel_klik");
        cancel_klik.prop('disabled', true); 
        
        post_klik.click(function(){ 
            jsonPHP = <?php echo $jsonIdxPage; ?>;
            if(!prosSelesai && Object.keys(jsonPHP).length > 0){
                $(this).prop('disabled', true);     
                cancel_klik.prop('disabled', false);
                sw.start();
            }
            //popProsesV3(1,MAX_PAGE)
            popProsesV3(nowPage,MAX_PAGE)
        });
        
        cancel_klik.click(function(){
            $(this).prop('disabled', true);     
            post_klik.prop('disabled', false);  
            prosesCancel()
            sw.stop();
        });
        
    });
    /** fungsi jquery */
    
    function prosesCancel(){
        prosCencel = true;
    }
    
    function popProsesV3(Page,maxPeage){
        if(prosSelesai){
            swal({
              title:"Sorry",
              text: "Proses posting selesai dijalankan,\nsilahkan refresh terlebih dahulu sebelum menjalankan proses",
              icon: "warning"
            }).then(function(){      
                
            });
        }else{
            prosCencel = false;
            nowPage = Page;
            //console.log(Page+"|"+maxPeage)
            jsonPHP = <?php echo $jsonIdxPage; ?>;
            if(Object.keys(jsonPHP).length > 0){      
                pandata = $("div#panel-data > table#data-table > tbody").children()
                if(Page == 1){
                    pandata.find(".status").css("background-color", "grey").css("color", "white").text("Menunggu")
                }
                if(pandata.length > 0){
                    $.each(pandata, function(index, value) {
                        noPage = $(this).children().eq(0).text()
                        if(noPage == Page){
                            $(this).children().eq(5).css("background-color", "#ebf642").css("color", "black").text("Proses")
                            return false;
                        }
                    });
                }
                resObj = jsonPHP[Page];
                postingV3(Page,maxPeage,idxPerPage,resObj.length,resObj);
            }else{
                alert("Data posting kosong")
            }       
        }         
    }
    
    function postingV3(Page,maxPeage,idx,max,rqsData){
        idxPerPage=idx;
        if(idx == (max-1))idxPerPage = 0;
        if(prosCencel){
            pandata = $("div#panel-data > table#data-table > tbody").children()
            if(pandata.length > 0){
                $.each(pandata, function(index, value) {
                    noPage = $(this).children().eq(0).text()
                    if(noPage == Page){
                        $(this).children().eq(5).css("background-color", "#f81a52").css("color", "white").text("Berhenti")
                        return false;
                    }
                });
            }   
            return false;
        }else{
            jsnRqs = JSON.stringify(rqsData[idx])
            if(idx < max){
                $.ajax({
                    queue: true,
                    cache: false,
                    type: 'POST',
                    url: 'module/payment/ecollbni/action_api.php',
                    data: {'action':'posting.vabni','jsnRqs':jsnRqs},
                    beforeSend:function(){
                        //console.log("beforeSend")
                        pandata = $("div#panel-data > table#data-table > tbody").children()
                            if(pandata.length > 0){
                                $.each(pandata, function(index, value) {
                                    noPage = $(this).children().eq(0).text()
                                    if(noPage == Page){
                                        $(this).children().eq(1).text(rqsData[idx].custcode)
                                        $(this).children().eq(2).text(rqsData[idx].customer_name)
                                        $(this).children().eq(3).text(parseInt(rqsData[idx].trx_amount).toLocaleString())
                                        $(this).children().eq(4).text(rqsData[idx].description.substring(0, 39))
                                        return false;
                                    }
                                });
                            }
                       
                    },
                    success: function(response) {
                        console.log(response) 
                        /**********************************************/
                        respParse = parseJson(response);
                        if(respParse){             
                            if(respParse.status_code == 1){
                                setSucs = $("span#count-success");
                                countSucc = parseInt(setSucs.text()) + 1;
                                setSucs.text(countSucc)
                            }else{
                                setFail = $("span#count-failed");
                                countFail = parseInt(setFail.text()) + 1;
                                setFail.text(countFail)
                            }
                        }else{
                            setFail = $("span#count-failed");
                            countFail = parseInt(setFail.text()) + 1;
                            setFail.text(countFail)
                        }
                        
                        setPros = $("span#count-proses");
                        countPros = parseInt(setPros.text()) + 1;
                        setPros.text(countPros)
                        setSisa = $("span#count-sisa");
                        countSis = TOTAL_ROW - countPros;
                        setSisa.text(countSis)
                        /**********************************************/
                        if(idx === (max-1)){
                            pandata = $("div#panel-data > table#data-table > tbody").children()
                            if(pandata.length > 0){
                                $.each(pandata, function(index, value) {
                                    noPage = $(this).children().eq(0).text()
                                    if(noPage == Page){
                                        $(this).children().eq(5).css("background-color", "#aaf086").css("color", "black").text("Selesai")
                                        return false;
                                    }
                                });
                            }   
                            if(Page < maxPeage){
                                setTimeout(function(){
                                    popProsesV3((Page+1),maxPeage); 
                                }, 1000);
                            }else{
                                sw.stop();
                                prosSelesai = true;
                                $("button#cancel_klik").prop('disabled', true);     
                                $("button#post_klik").prop('disabled', false);  
                            }
                        }else{
                            setTimeout(function(){
                                postingV3(Page,maxPeage,(idx+1),max,rqsData); 
                            }, 100);
                        }
                    }
                }); 
             }
         }
    }
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
</head>
<body>
    <div class="panel col-sm-12 col-md-12">
        <!-- HEADER -->
        <div class="panel panel-primary" style="margin-top: 5px; margin-bottom: 5px; border: none;">
            <div class="panel-heading" style="background-color: #B8FEF7; border: none;">
              <img class="logo-bank" src="<?php echo HOSTNAME();?>/module/payment/briva/pict/logo-bni.png">&nbsp;&nbsp;<strong style="color: black; font-size: 13px;">POSTING VIRTUAL ACCOUNT BNI</strong>
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
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="width: 14%;">
                        <div class="panel panel-default" style="background-color: #eba9dc;">
                          <div class="panel-body text-center">
                            <div class="" style="padding: 20px 0px;">
                              <h5>Total Account</h5>
                              <h3>
                                <strong><?php echo $Num_Rows; ?></strong>
                              </h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="width: 14%;">
                        <div class="panel panel-default" style="background-color: #aef872">
                          <div class="panel-body text-center">
                            <div class="" style="padding: 20px 0px;">
                              <h5>Total Sukses</h5>
                              <h3>
                                <strong><span id="count-success">0</span></strong>
                              </h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="width: 14%;">
                        <div class="panel panel-default" style="background-color: #f0ad98;">
                          <div class="panel-body text-center">
                            <div class="" style="padding: 20px 0px;">
                              <h5>Total Gagal</h5>
                              <h3>
                                <strong><span id="count-failed">0</span></strong>
                              </h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="width: 14%;">
                        <div class="panel panel-default" style="background-color: #98dbf0;">
                          <div class="panel-body text-center">
                            <div class="" style="padding: 20px 0px;">
                              <h5>Total Proses</h5>
                              <h3>
                                <strong><span id="count-proses">0</span></strong>
                              </h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="width: 14%;">
                        <div class="panel panel-default" style="background-color: #f7e86e;">
                          <div class="panel-body text-center">
                            <div class="" style="padding: 20px 0px;">
                              <h5>Sisa Proses</h5>
                              <h3>
                                <strong><span id="count-sisa">0</span></strong>
                              </h3>
                            </div>
                          </div>
                        </div>
                      </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="width: 14%;">
                        <div class="panel panel-default" style="border: none;">
                          <div class="panel-body text-center">
                            <div class="" style="padding: 20px 0px;">
                              <!--<button class="btn btn-sm btn-info" style="width: 100%; height: 40px; font-size: 12px;" onclick="popProsesV3(1,<?php echo $Num_Pages; ?>); sw.start();" id="post_klik" value="post_klik" name="post_klik"><b>POSTING</b></button>-->
                              <button class="btn btn-sm btn-info" style="width: 100%; height: 40px; font-size: 12px;" id="post_klik" value="post_klik" name="post_klik"><b>POSTING</b></button>
                            </div>
                          </div>
                        </div>
                      </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2" style="width: 14%;">
                        <div class="panel panel-default" style="border: none;">
                          <div class="panel-body text-center">
                            <div class="" style="padding: 20px 0px;">
                              <!--<button class="btn btn-sm btn-danger" style="width: 100%; height: 40px; font-size: 12px;" onclick="prosesCancel(); sw.stop();" id="post_klik" value="post_klik" name="post_klik"><b>CANCEL</b></button>-->
                              <button class="btn btn-sm btn-danger" style="width: 100%; height: 40px; font-size: 12px;"  id="cancel_klik" value="cancel_klik" name="cancel_klik"><b>BERHENTI</b></button>
                            </div>
                          </div>
                        </div>
                      </div>
                    <!--button id="post_klik" onclick="popProses(1,<?php echo $Per_Page; ?>,<?php echo $Num_Pages; ?>)" >POSTING</button>
                    <button id="post_klik" onclick="popProsesV2(1,<?php echo $Num_Pages; ?>)" >POSTING V2</button-->

                    <!-- <button id="post_klik" onclick="popProsesV3(1,<?php echo $Num_Pages; ?>)" >POSTING V3</button> -->

                    <!-- <button class="btn btn-sm btn-success" style="width: 8%; height: 30px; font-size: 12px;" onclick="popProsesV3(1,<?php echo $Num_Pages; ?>);" id="post_klik" value="post_klik" name="post_klik">POSTING</button>

                    <button id="post_klik" onclick="prosesCancel()" >CANCEL</button>
                    <div>Total account  : <?php echo $Num_Rows; ?></div>
                    <div>Total sukses : <span id="count-success">0</span></div>
                    <div>Total gagal : <span id="count-failed">0</span></div>
                    <div>Total proses : <span id="count-proses">0</span></div>
                    <div>Sisa proses : <span id="count-sisa">0</span></div> -->
                    <!-- <div class="stopwatch"></div> -->
                    <div id="stopwatch">
                      <div id="sw-time">00:00:00</div>
                    </div>
                </div>
            </div>
            </div>
        </div>
        
        <!-- TABEL DATA -->
        <div id="panel-data" class="panel panel-primary">
            <table class="table table-bordered" id="data-table" border="1px solid rgb(190,190,190);">
                <thead>
                    <tr>
                      <th style="width: 60px;">Page</th>
                      <th style="width: 130px;">CUSTCODE</th>
                      <th style="width: 400px;">NAMA</th>
                      <th style="width: 120px;">AMOUNT</th>
                      <th>KETERANGAN</th>
                      <th style="width: 150px;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for($z = 1; $z <= $Num_Pages; $z++){
                    ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $z;?></td>
                        <td style="text-align: center;"></td>
                        <td></td>
                        <td style="text-align: right;"></td>
                        <td></td>
                        <td class="status"></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            
        </div>
        
    </div>
</body>

<script type="text/javascript">
    var sw = {
  /* [INIT] */
  etime : null, // holds HTML time display
  erst : null, // holds HTML reset button
  ego : null, // holds HTML start/stop button
  timer : null, // timer object
  now : 0, // current timer
  init : function () {
    // Get HTML elements
    sw.etime = document.getElementById("sw-time");
    // sw.erst = document.getElementById("sw-rst");
    // sw.ego = document.getElementById("sw-go");

    // // Attach listeners
    // sw.erst.addEventListener("click", sw.reset);
    // sw.erst.disabled = false;
    // sw.ego.addEventListener("click", sw.start);
    // sw.ego.disabled = false;
  },

  /* [ACTIONS] */
  tick : function () {
  // tick() : update display if stopwatch running

    // Calculate hours, mins, seconds
    sw.now++;
    var remain = sw.now;
    var hours = Math.floor(remain / 3600);
    remain -= hours * 3600;
    var mins = Math.floor(remain / 60);
    remain -= mins * 60;
    var secs = remain;

    // Update the display timer
    if (hours<10) { hours = "0" + hours; }
    if (mins<10) { mins = "0" + mins; }
    if (secs<10) { secs = "0" + secs; }
    sw.etime.innerHTML = hours + ":" + mins + ":" + secs;
  },

  start : function () {
  // start() : start the stopwatch

    sw.timer = setInterval(sw.tick, 1000);
    //sw.ego.value = "Stop";
//    sw.ego.removeEventListener("click", sw.start);
//    sw.ego.addEventListener("click", sw.stop);
  },

  stop  : function () {
  // stop() : stop the stopwatch

    clearInterval(sw.timer);
    sw.timer = null;
    //sw.ego.value = "Start";
//    sw.ego.removeEventListener("click", sw.stop);
//    sw.ego.addEventListener("click", sw.start);
  },

  reset : function () {
  // reset() : reset the stopwatch

    // Stop if running
    if (sw.timer != null) { sw.stop(); }

    // Reset time
    sw.now = -1;
    sw.tick();
  }
};

window.addEventListener("load", sw.init);
</script>