<?php
set_time_limit(6000);
require_once "../../../include/fungsi.php";
$action = $_POST['action'] ? $_POST['action'] : $_GET['action'];
$user_nik = $_SESSION['nik'];
//echo $action;
$mitraid_beta = '44444444';
$mitraid_prod = '50000004';

switch ($action) {
    

    case "request.settlement":
        $transDate = $_POST['tgl_release'];
        $year = date('Y');
        $result = array();

        $query_mindate = "select min(TransactionDate)transdate FROM payment_bca_paid with(nolock) where settlementdate is null";
        $exec_mindate = mssql_query($query_mindate);
        $result_mindate = mssql_fetch_assoc($exec_mindate);

        //** PAKSA BERHENTI JIKA TANGGAL TRANSAKSI LEBIH DARI SEKARANG */
        if (date('Y-m-d', strtotime(dateSQL($transDate))) > date("Y-m-d H:i:s")) {
            die(json_encode(
                array(
                    "status" => -1,
                    "desc" => "Tanggal Transaksi tidak lebih dari sekarang."
                )
            ));
        }

        $minTransDate = dateSQLKaco($result_mindate['transdate']);
        $array_sattlement = array();
        
        /** REQUEST API SATTLEMENT BCA */
        $url = "http://192.168.1.221/MultindoMobile/zrest/payment/aksesbca/getMutasiRekening";
        $par = array(
            "company_code"      => "KBCMULTIND",
            "customer_number"   => "0097817000",
            "start_date"        => $minTransDate,
            "end_date"          => $transDate,
            "user"              => $user_nik

        );

        $req = request_curl($url, "POST", json_encode($par));

        $dataSattlement = json_decode($req, true);
        $pattern = "/KR OTOMATIS/i";
        $pattern2 = "/BCA VA/i";

        for ($i = 0; $i < count($dataSattlement['Data']); $i++) {
            if (preg_match($pattern, $dataSattlement['Data'][$i]['TransactionName']) && preg_match($pattern2, $dataSattlement['Data'][$i]['Trailer'])) {
                // $array_sattlement[$dataSattlement['Data'][$i]['TransactionDate']] = (int)$dataSattlement['Data'][$i]['TransactionAmount'];
                $n_array = array(
                    "transaksidate" => $dataSattlement['Data'][$i]['TransactionDate'],
                    "amount" => (int)$dataSattlement['Data'][$i]['TransactionAmount']
                );
                $array_sattlement[] = $n_array;
            }
        }


        /* --------------------------- */
        /** TABLE VA BCA */
        $array_va_paid = array();
        $query = "
                select dbo.tanggal_saja(transactiondate)transaksidate, sum(paidamount)paidamount from payment_bca_paid 
                where settlementdate is null
                group by dbo.tanggal_saja(transactiondate)
            ";
        $exec = mssql_query($query);
        while ($res = mssql_fetch_assoc($exec)) {
            $dayformat = substr(dateSQLKaco($res['transaksidate']), 0, 5);
            // $array_va_paid[$dayformat] = $res['paidamount'];
            $n_array = array(
                "transaksidate" => $dayformat,
                "amount" => $res['paidamount']
            );
            $array_va_paid[] = $n_array;
        }

        if (count($array_sattlement) == 0 && count($array_va_paid) == 0) {
            die(json_encode(array(
                "status" => -1,
                "desc" => "Tidak ada data transaksi"
            )));
        } else {
            $temp = array();
            $j = 0;
            $sum_temp = 0;
            $date = "";

            $i = 0;            
            while ($i < count($array_va_paid)) {

                if(date('Y-m-d', strtotime(dateSQL($array_va_paid[$i]['transaksidate']."/".$year))) < date('Y-m-d', strtotime(dateSQL($array_sattlement[$j]['transaksidate']."/".$year)))){
                    $temp[] = array(
                        "transaksidate" => $array_va_paid[$i]['transaksidate'],
                        "amount" => $array_va_paid[$i]['amount'],
                    );
                    $date = $date == ""? $array_va_paid[$i]['transaksidate'] : $date.", ".$array_va_paid[$i]['transaksidate'];
                }
                else {
                    foreach ($temp as $key => $value) {
                        $sum_temp += $value['amount'];
                    }
                    
                    if(count($temp) > 0){
                        $sum_temp += $array_va_paid[$i]['amount'];
                    }

                    $sum_temp = $sum_temp == 0?$array_va_paid[$i]['amount']:$sum_temp;
                    $date = $date == ""?$array_va_paid[$i]['transaksidate']:$date;
                    $array_sattlement[$j]['transaksidate'] = isset($array_sattlement[$j]['transaksidate'])?$array_sattlement[$j]['transaksidate']:'PEND';
                    $array_sattlement[$j]['amount'] = isset($array_sattlement[$j]['amount'])?$array_sattlement[$j]['amount']:0;

                    $result[] = array(
                        "transDate" => $date,
                        "sattlementDate" => $array_sattlement[$j]['transaksidate'],
                        "vaAmount" => $sum_temp,
                        "sattlementAmount" => $array_sattlement[$j]['amount']
                    );

                    
                    $j++;
                    $temp = array();
                    $date = "";
                    $sum_temp = 0;
                }
                $i++;
            }
        }

        echo json_encode($result);
        break;


    case "getpayva.vabca":
        // $date = date_create_from_format('d/m/Y', $_POST['tgl_transfer']);
        // $tgl_transfer = date_format($date, 'Y-m-d');
        $tgl_transfer = DateSQL($_POST['tgl_transfer']);
        $tgl_transfer_end = DateSQL($_POST['tgl_transfer_end']);
        // $replay['tgl_start'] = $tgl_transfer;
        // $replay['tgl_end'] = $tgl_transfer_end;
        mssql_query("SET ANSI_NULLS ON; SET ANSI_WARNINGS ON;");
        // $sql = "exec bni_dataPostingITDeptKeFinancore '260','".$tgl_transfer."','$user_nik'";
        $sql = "exec bca_dataPostingITDeptKeFinancore '" . $tgl_transfer . "','" . $tgl_transfer_end . "','$user_nik'";
        $exec = mssql_query($sql) or die("Error Query [" . $sql . "]");
        //$numbRows = mssql_num_rows($exec);

        $count_a = 1;
        $row = array();
        while ($hsl = mssql_fetch_assoc($exec)) {
            $col = array();
            $col['no'] = $count_a;
            $col['trxdate']     = dateSQLKaco($hsl['trxdate']);
            $col['payment_date']     = dateSQLKacoTime($hsl['paymentdate']);
            $col['vano']        = $hsl['vano'];
            $col['nama']        = $hsl['nama'];
            $col['amount']      = $hsl['amount'];
            $col['angsuranround']      = $hsl['angsuranround'];
            $col['keterangan']  = $hsl['keterangan'];
            $col['traceno']     = $hsl['traceno'];
            $col['delchannel']  = $hsl['delchannel'];
            array_push($row, $col);
            $count_a++;
        }
        if (count($row) > 0) {
            $replay['status_code'] = 1;
            $replay['status_desc'] = "Transfer payment Financore ";
            $replay['total_results'] = count($row);
            $replay['results'] = $row;
        } else {
            $replay['status_code'] = 2;
            $replay['status_desc'] = "Tidak ada data transfer Financore";
        }
        mssql_query("SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF;");
        echo json_encode($replay);
        break;

    case "dopaidfin.vabca":
        // $date = date_create_from_format('d/m/Y', $_POST['tgl_transfer']);
        // $tgl_transfer = date_format($date, 'Y-m-d');
        // $data_array = $_POST['data_array'];

        // foreach ($data_array as $data) {
        //     for ($i = 0; $i < count($data); $i++) {
        //         $data[$i];
        //     }
        // }

        // $trxid = $data[0];
        // $noreknopin = $data[1];
        // $tgl_transaksi = dateSQL($data[2]);

        $tgl_transfer = DateSQL($_POST['tgl_transfer']);
        $tgl_transfer_end = DateSQL($_POST['tgl_transfer_end']);


        mssql_query("SET ANSI_NULLS ON; SET ANSI_WARNINGS ON;");
        // $sql = "exec bni_dataPostingITDeptKeFinancore '260','".$tgl_transfer."','$user_nik'";

        $sql = "exec bca_dataPostingITDeptKeFinancore '".$tgl_transfer."','".$tgl_transfer_end."','$user_nik'";
        $exec = mssql_query($sql)or die ("Error Query [".$sql."]");
        $numbRows = mssql_num_rows($exec);

        $count_a = 1;
        $row = array();
        while($hsl = mssql_fetch_assoc($exec)){
            //$sql_do="exec bni_postingITDeptKeFinancore '260','0012051899001','2020-10-05','122041282'";
            // $sql_do = "exec bni_postingITDeptKeFinancore '".$hsl['delchannel']."','$hsl[vano]','$hsl[trxdate]','$user_nik'";
            $sql_do = "exec bca_postingITDeptKeFinancore '" . $hsl['delchannel'] . "','" . $hsl['vano'] . "','" . $hsl['trxdate'] . "','" . $hsl['traceno'] . "','$user_nik'";
            $exec_do = mssql_query($sql_do);

            // $col = array();
            // $col['no'] = $count_a;
            // $col['trxdate']     = dateSQLz2($hsl['trxdate']);
            // $col['vano']        = $hsl['vano'];
            // $col['nama']        = $hsl['nama'];
            // $col['amount']      = $hsl['amount'];
            // $col['keterangan']  = $hsl['keterangan'];
            // $col['traceno']     = $hsl['traceno'];
            // $col['delchannel']  = $hsl['delchannel'];
            array_push($row, $hsl['vano']);
            // $count_a++;
            // echo $sql_do;
        }
        if (count($row) > 0) {
            $replay['status_code'] = 1;
            $replay['status_desc'] = "Transfer payment Financore ";
            $replay['total_results'] = count($row);
            $replay['results'] = $row;
        } else {
            $replay['status_code'] = 2;
            $replay['status_desc'] = "Tidak ada data transfer Financore";
        }
        mssql_query("SET ANSI_NULLS OFF; SET ANSI_WARNINGS OFF;");
        echo json_encode($replay);
        break;

    case "submit_release":
        $sql_apldate = "select top 1 apldate from core_nasabah";
        $exec_apldate = mssql_query($sql_apldate);
        $data_apldate = mssql_fetch_assoc($exec_apldate);

        $checkAmount = $_POST['hiddenCheck'];
        $tgl_apldate = dateSQL(dateSQLKaco($data_apldate['apldate']));
        $tgl_settlement = $_POST['h_tgl_settlement'];

        $array_release=array();
        foreach($checkAmount as $requestID => $customerNumber){
            // echo $key."<br>";
            $array_release[] = array(
                "query" => "bca_release_reconcile '$tgl_settlement','$tgl_apldate','$customerNumber','$requestID','H2H'"
            );

            print_r($array_release);
                
        }
        $i = 0;
        $gagal = 0;
        while(count($array_release) < $i){
            $exec = "exec ".$array_release[$i]["query"];

            if(!$exec){
                $i=count($array_release);
                $gagal++;
            }
            $i++;
            
        }
        
        alert("Sukses ".$i);
        redirMeta("../../../isi.php?pid=1357");
        
        break;
}



// function dateSQLz2($stringDate){
//     if($stringDate!=''){
//         $time = strtotime($stringDate);
//         $dateSQL = date("d-m-Y",$time);
//         return $dateSQL;
//     }else{
//         $dateSQL    = "";
//         return $dateSQL;
//     }
// }	
