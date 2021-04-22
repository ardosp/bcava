<?php
set_time_limit(6000);
require_once "../../../include/fungsi.php";
$action = $_POST['action'] ? $_POST['action'] : $_GET['action'];
$user_nik = $_SESSION['nik'];
//echo $action;
$mitraid_beta = '44444444';
$mitraid_prod = '50000004';

switch ($action) {
    case "request.settlement.vabca":
        /**$_POST */
        $tgl_transaksi = $_POST['tgl_release'];
        $tgl_transaksi_sql = dateSQL($_POST['tgl_release']);

        /**INISIASI ARRAY */
        $array_hasil = array();
        $array_hasil['total_amount_maf'] = 0;
        $array_hasil['data'] = array();
        // $array_hasil['data_query'] = array();
        $array_hasil['info'] = '';

        /**MINDATE */
        $sql_01 = "SELECT min(TransactionDate)TransactionDate FROM payment_bca_paid where status in ('0','1')";
        $exec_01 = mssql_query($sql_01);
        $res_01 = mssql_fetch_assoc($exec_01);

        /**DATA DARI MAF */
        // $sql = "SELECT * from payment_bca_paid where status in ('0','1') and dbo.tanggal_saja(TransactionDate)<='$tgl_transaksi_sql' order by TransactionDate desc";
        // $exec = mssql_query($sql);
        // // echo $array_hasil['info'] = $sql;
        // while($data = mssql_fetch_assoc($exec)){
        //     $transdate = dateSQLKaco($data['TransactionDate']);
        //     if(empty($data['TransactionDate']))
        //     {
        //         $transdate = $tgl_transaksi;
        //     }

        //     $result = array(
        //         "Norek" => $data['norek'],
        //         "Nopin" => $data['nopin'],
        //         "TxnDate" => $data['txndate'],
        //         "CustomerName" => $data['CustomerName'],
        //         "PaidAmount" => $data['PaidAmount'],
        //         "TotalAmount" => $data['TotalAmount'],
        //         "TransactionDate" => $transdate,
        //         "Status" => $data['Status'],
        //         "trxdate" => "",
        //         "trxamount" => "",
        //         "trxname" => "",
        //         "trailer" => ""
        //     );
        //     $transactiondate = substr(dateSQLKaco($data['TransactionDate']),0,5);
        //     $array_hasil['data'][$transactiondate] = $result;
        // }

        $tahun = date('Y');
        // echo $tahun;
        /**DATA DARI BCA */
        $url = "http://192.168.1.221/MultindoMobile/zrest/payment/aksesbca/getMutasiRekening";
        $par = array(
            "company_code"      => "KBCMULTIND",
            "customer_number"   => "0097817000",
            "start_date"        => "02/04/2021", //$transdate
            "end_date"          => "05/04/2021", //$tgl_transaksi
            "user"              => $user_nik

        );
        // echo request_curl($url,"POST",json_encode($par));
        $req = request_curl($url, "POST", json_encode($par));

        $decode = json_decode($req, true);
        // echo $decode->Data[0]->TransactionDate;
        // echo $req["Data"][0]["TransactionDate"];
        // echo count($decode['Data']);


        $startdate = dateSQL($res_01['TransactionDate']);
        $endate = "";

        $i = 0;
        for ($i = 0; $i < count($decode['Data']); $i++) {
            $str = $decode['Data'][$i]['TransactionName'];
            $pattern = "/KR OTOMATIS/i";
            $str2 = $decode['Data'][$i]['Trailer'];
            $pattern2 = "/BCA VA/i";

            $tgl_transaksi = $tgl_transaksi;
            $tgl_settlement = $decode['Data'][$i]['TransactionDate'] . "/" . $tahun;



            $array_hasil['info'] = $tgl_settlement;

            // && preg_match($pattern2, $str2)
            /**PENGECEKAN BCA VA DI SETTLEMENT */
            if (preg_match($pattern, $str) && preg_match($pattern2, $str2)) {


                $sql = "SELECT sum(PaidAmount)PaidAmount from payment_bca_paid where status in ('0','1') and dbo.tanggal_saja(TransactionDate) between '$startdate' and '$tgl_transaksi'";
                $e = mssql_query($sql);
                echo $sql;
                $data = mssql_fetch_assoc($e);

                $array_hasil['total_amount_maf'] = $data['PaidAmount'];



                // if ("05/04/2021" <= "15/04/2021") {
                //     // $array_hasil['data'][$transactiondate]['trxdate'] = "tes";
                //     // $array_hasil['data'][$transactiondate]['trailer'] = "coba";
                //     $array_hasil['total_amount_maf'] += $array_hasil['data'][$transactiondate]['PaidAmount'];
                // }


                $hasil = array(
                    "id" => $i,
                    "trxdate" => $decode['Data'][$i]['TransactionDate'],
                    "trxamount" => $decode['Data'][$i]['TransactionAmount'],
                    "trxname" => $decode['Data'][$i]['TransactionName'],
                    "trailer" => $decode['Data'][$i]['Trailer']
                );
                array_push($array_hasil['data'], $hasil);
            }

            /**JIKA data settlement belum masuk dan tgl transaksi sudah masuk */
            $sql2 = "SELECT * from payment_bca_paid where status in ('0','1') and dbo.tanggal_saja(TransactionDate)>='$tgl_settlement' order by TransactionDate desc";
            $exec2 = mssql_query($sql2);
            echo $sql2;

            $startdate = $tgl_settlement;
        }
        // echo json_encode($array_hasil);

        break;

    case "request.settlement":
        $transDate = $_POST['tgl_release'];
        $year = date('Y');
        $result = array();

        $query_mindate = "select min(TransactionDate)transdate FROM payment_bca_paid_tes with(nolock) where status in ('0','1')";
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
                select dbo.tanggal_saja(transactiondate)transaksidate, sum(paidamount)paidamount from payment_bca_paid group by dbo.tanggal_saja(transactiondate)
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
                else if(date('Y-m-d', strtotime(dateSQL($array_va_paid[$i]['transaksidate']."/".$year))) > date('Y-m-d', strtotime(dateSQL($array_sattlement[$j]['transaksidate']."/".$year)))){                    
                    foreach ($temp as $key => $value) {
                        $sum_temp += $value['amount'];
                    }

                    if(isset($array_sattlement[$j]['amount'])){
                        $result[] = array(
                            "transDate" => $date,
                            "sattlementDate" => $array_sattlement[$j]['transaksidate'],
                            "vaAmount" => $sum_temp,
                            "sattlementAmount" => $array_sattlement[$j]['amount']
                        );
                    }
                    else{
                        $result[] = array(
                            "transDate" => $array_va_paid[$i]['transaksidate'],
                            "sattlementDate" => "PEND",
                            "vaAmount" => $array_va_paid[$i]['amount'],
                            "sattlementAmount" => 0
                        );
                    }
                    $j++;
                    $temp = array();
                    $date = "";
                    $sum_temp = 0;
                    
                    if(date('Y-m-d', strtotime(dateSQL($array_va_paid[$i]['transaksidate']."/".$year))) == date('Y-m-d', strtotime(dateSQL($array_sattlement[$j]['transaksidate']."/".$year)))){
                        $result[] = array(
                            "transDate" => $array_va_paid[$i]['transaksidate'],
                            "sattlementDate" => $array_sattlement[$j]['transaksidate'],
                            "vaAmount" => $array_va_paid[$i]['amount'],
                            "sattlementAmount" => $array_sattlement[$j]['amount']
                        );
                        $j++;
                        $temp = array();
                    }
                }
                else{
                    
                    $result[] = array(
                        "transDate" => $array_va_paid[$i]['transaksidate'],
                        "sattlementDate" => $array_sattlement[$j]['transaksidate'],
                        "vaAmount" => $array_va_paid[$i]['amount'],
                        "sattlementAmount" => $array_sattlement[$j]['amount']
                    );
                    $j++;
                    $temp = array();
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
