<?php   session_start();?>
<?php
include_once("../dataserve/conn_mysql.php");
include_once("hmainfunction.php");
include_once("hclientinterface.php");
include_once("hfunction.php");

$json=$_POST["dy"];

set_time_limit(120); //2·ÖÖÓ
//$time=microtime(true)*1000;
//$db4=conn4();
//$sqlstr="INSERT INTO `test` (`c1`,`time`) VALUES ( '$json','$time')";			
//mysql_query($sqlstr,$db4);  

$res=hclientinterface($json);

//   $a= base64_decode($json);
// $r=gzcompress($res, 9); 
//$r=gzdeflate($res,9);
// $r= gzcompress($res, 9);
// $b=gzuncompress($a); 

//$res= gzcompress($res, 9);
//$res=$json;
//echo "asd123";

echo    base64_encode(urldecode(json_encode($res))); //  

//print_r($res);


?>