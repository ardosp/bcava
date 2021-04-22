<?php
    // ini_set('max_execution_time', 600); // 120 (seconds) = 2 Minutes
    set_time_limit(1000);

    include_once ("../../../include/fungsi.php");
    include_once ("../../../include/dbconfig.php");

    $user_nik = $_SESSION['nik'];
    $nik = $_SESSION['nik'];

    $act = $_GET['act'];
    if(empty($act)){$act=$_POST['act'];}

    //declare variable ==============================================
    $arr = array(",","-","/",":");         
    // =====================================================================

    switch ($act) {
        case 'create_bcava':
            // echo "001";
        // $sql="exec Financore.dbo.payment_upload_briva '$user_nik'";  
        $sql = "exec payment_upload_bca_sismaf '$user_nik'";  

        if (mssql_query($sql)) {        
            echo "1";
        }else {
            echo "0";
        }
        // echo $sql;
        break;

    
   

    }
    function dateSQLz2($stringDate){
    if($stringDate!=''){
        $time = strtotime($stringDate);
        $dateSQL = date("d-m-Y",$time);
        return $dateSQL;
    }else{
        $dateSQL    = "";
        return $dateSQL;
    }
}
 ?>
