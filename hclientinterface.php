<?php
//==================================================
// 
//
//==================================================
//   result=2,pda不存在； 3pda密码不正确；4用户不存在，5用户密码不正确； 200正确登录
//----------------------------------------
function  hclientinterface($json)
{

$json1=str_replace(" ","+",$json);
$json2=base64_decode($json1);
$db=conn();
//$json2=mysql_real_escape_string($json2,$db);

$json2=str_replace("\n","",$json2);
$json2=str_replace("\r","",$json2);
$json2=str_replace("\d","",$json2);
$json2=str_replace("'","",$json2);
$json2=str_replace("\t","",$json2);


//$json2='{"method":"login","pdasn":"123456789123456789" ,"pdapassword":"223123","username":"13761633599" ,"password":"111111","cmdresponse":"eeee" }';
$json3=json_decode($json2,true); 


$pdasn=$json3["pdasn"];
$pdapassword=$json3["pdapassword"];

$username=$json3["username"];
$password=$json3["password"];

$db=conn();
$db4=conn4();

//判断PDA设备是否可以登录
$result = mysql_query("SELECT * FROM  stations_manage   where  allpda  like '%$pdasn%' ",$db4);  
$num= mysql_numrows ($result);
if($num!=0) 
{
   $stationaccount=mysql_result($result,0,"account"); //站点账号
   $allpda=mysql_result($result,0,"allpda");
   $pdastr=$pdasn.",".$pdapassword;
   $pdapw=strpos($allpda,"$pdastr");
  if($pdapw===false)//pda登录正常  //一定要=== 区分0和false
   {     
      $errorcode=3; //pda密码不正确
   }
   else
  {  
    //------开始判断用户登录情况------- 
	$result = mysql_query("SELECT * FROM user  where  username='$username' ",$db);  
	$num= mysql_numrows ($result);
	if($num!=0) 
	{   
  		$password0=mysql_result($result,0,"password");
		
  		if($password0==$password)
  		{  
     		$errorcode=200;  //用户登录正常
  		}
  		else
  		{
     		$errorcode=5; //用户密码不正确
  		}
	}
	else  //用户不存在
	{
   		$errorcode=4; //用户不存在
	}    
  }
}
else  //PDA串号不存在
{
   $errorcode=2; //pda不存在
}

//执行函数体：若登录成功，执行函数并返回结果
$response="0"; 
if($errorcode==200)
{	
   $response=domethod($json3,$stationaccount,$pdasn);
}

$errorcode="$errorcode";
$username="$username";
$ret=array("result"=>$errorcode,"pdasn"=>$pdasn,"response"=>$response);
return $ret;
} 


?>
