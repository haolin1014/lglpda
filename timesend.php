
<?php
include_once("../dataserve/conn_mysql.php");

//timesendDatatoLgl();

//---------------定时发送函数-------------------------------------
function  timesendDatatoLgl()
{
    $db=conn();
    $result= mysql_query("SELECT * FROM  dyhawk.logistics  where  uplglflg>0  limit  100",$db);  	
    $num= mysql_numrows($result);
	for($i=0;$i<$num;$i++)
	{
	   	$trackNo=mysql_result($result,$i,"expressno");
	   	$receiverTel=mysql_result($result,$i,"phonenumber");
	   	$logisticNo=mysql_result($result,$i,"expressname");
		$logisticNo=changeExpresscodetolglcode($logisticNo);  //转换成0公里代号	   
	   	$storageTime=mysql_result($result,$i,"diandantime");
		if($storageTime!=0)
		{
		   $storageTime=date("YmdHis",$storageTime);
		}
		else
		{
		   $storageTime="";
		}
			
       	$storagePhone=mysql_result($result,$i,"diandanuser");

       	$orderStatus=mysql_result($result,$i,"phase");
		//echo "aaa=$storagePhone";	
		// whl如果是外派则deliveryTime为外派时间，如果是上人工货架或者智能柜，则为分配时间
		$deliveryPhone = '';
		if($orderStatus==1){
			$deliveryTime=mysql_result($result,$i,"waipaitime");
			$deliveryPhone=mysql_result($result,$i,"waipaiuser");
		}elseif($orderStatus==5){
			$deliveryTime=mysql_result($result,$i,"distributetime");
			$deliveryPhone=mysql_result($result,$i,"distributeuser");
		}		
	 	
		if($deliveryTime)
		{
		    $deliveryTime=date("YmdHis",$deliveryTime);
		}
		else
		{
		   $deliveryTime="";
		}
		//echo "aa=$deliveryTime   ";
	 	
	 	$overTime=mysql_result($result,$i,"signingtime");
		if($overTime!=0)
		{
		    $overTime=date("YmdHis",$overTime); 
		}
		else
		{
		    $overTime="";
		}

	 	$overPhone=mysql_result($result,$i,"signinguser");
			
		
		
	 	$collectedFee=mysql_result($result,$i,"daofuprice");	//$collected="1"; 不需要是否到付，空没有，不空需要到付
		if($collectedFee!="")
		{
		   $collectedFee=$collectedFee*100;  //变成分 （原来是小数点元单位）
		   $collected=1; 
		}
		else
		{
		   $collectedFee=0;
		   $collected=0; 
		}
		          	
	    $diandantime=mysql_result($result,$i,"diandantime");
		$waipaitime=mysql_result($result,$i,"waipaitime");
		$signingtime=mysql_result($result,$i,"signingtime");
		$distributetime=mysql_result($result,$i,"distributetime");
	    if($orderStatus==0)
		{
		   $optime=$diandantime;
		}
	    else if($orderStatus==1)
		{
		   $optime=$waipaitime;
		}	
	    else if($orderStatus==5)
		{
		   $optime=$distributetime;
		}
		else
		{
		   $optime=$signingtime;
		}
		$operationTime=date("YmdHis",$optime);//"20160522101011"; 
			
	 	$paidStatus=mysql_result($result,$i,"payway"); 

		$paidStatus=changepaidStatustolgl($paidStatus,$collectedFee);
		  
		  
		$picstatus=mysql_result($result,$i,"picstatus");  //改为是否有图片状态
		$imgUrl="";
		if($picstatus==1)
		{
		    $stationaccount=mysql_result($result,$i,"stationaccount");
			$stationaccount="/".$stationaccount;
		    $signingtime=mysql_result($result,$i,"signingtime");
			$signingtime=date("/Y-m-d/",$signingtime);
			$expressname=mysql_result($result,$i,"expressname");     	
		    $imgUrl="kd-image.lgl360.com".$stationaccount.$signingtime.$expressname."_".$trackNo.".jpg";		
		}

	 	$customerId=mysql_result($result,$i,"paycontent"); //大客户id
		$customerId=changeDakehutolglcode($customerId);//转换成0公里代号
	 	$logisticMemberId=""; //快递员编号？
	 	$shippingType=mysql_result($result,$i,"distributeway"); 
	  	$slelvesNo=mysql_result($result,$i,"huohao"); 
	 	$slelvesPassword="";//mysql_result($result,$i,"huohao"); ? 	 	 	 
	 	$note=mysql_result($result,$i,"direction");			
		
		$id=mysql_result($result,$i,"id"); 
		$outTradeNo=$id;
		
		$ret=sendtoLglData($trackNo,$receiverTel,$logisticNo,$operationTime,$orderStatus,$storageTime,$storagePhone,$deliveryTime,$deliveryPhone,$overTime,$overPhone,$collected,$collectedFee,$paidStatus,$imgUrl,$customerId,$logisticMemberId,$shippingType,$slelvesNo,$slelvesPassword,$note,$outTradeNo);
	
	   // echo "</br></br>$ret</br></br>";
	    if($ret!="")   //无论什么情况，记录只要更新都会传送， 其结果无论对错都将status写入errorcode。
		{
		  $ret=json_decode($ret,true);
		  $status=$ret["data"][0]["status"];
		 // if($status!=0)
		  {
		    // $outTradeNo=$ret["data"][0]["outTradeNo"];
		     mysql_query("UPDATE dyhawk.logistics SET `errorcode`='$status'  where  id='$id'",$db);
		  
		  }
		  $errortime=time();
		  mysql_query("UPDATE dyhawk.logistics SET  `errortime` = '$errortime', `uplglflg`=`uplglflg`-1  where  id='$id'",$db);  //只要更新就发送
		  
		}
	}

}

//-----------------发送函数------------------------------------
function  sendtoLglData($trackNo,$receiverTel,$logisticNo,$operationTime,$orderStatus,$storageTime,$storagePhone,$deliveryTime,$deliveryPhone,$overTime,$overPhone,$collected,$collectedFee,$paidStatus,$imgUrl,$customerId,$logisticMemberId,$shippingType,$slelvesNo,$slelvesPassword,$note,$outTradeNo)
{

	 $data=array();
	 $data["trackNo"]=$trackNo;
	 $data["receiverTel"]=$receiverTel;
	 $data["logisticNo"]=$logisticNo;
	 $data["operationTime"]=$operationTime;
	 $data["orderStatus"]=$orderStatus;
	 $data["storageTime"]=$storageTime;	 
	 $data["storagePhone"]=$storagePhone;	 
	 $data["deliveryTime"]=$deliveryTime; 
	 $data["deliveryPhone"]=$deliveryPhone;	 
	 $data["overTime"]=$overTime;
	 $data["overPhone"]=$overPhone;	 
	 $data["collected"]=$collected;
	 $data["collectedFee"]=$collectedFee;  
	 $data["paidStatus"]=$paidStatus;	 	  
	 $data["imgUrl"]=$imgUrl;
	 $data["customerId"]=$customerId; 
	 $data["logisticMemberId"]=$logisticMemberId;
	 $data["shippingType"]=$shippingType; 	 
	 $data["slelvesNo"]=$slelvesNo;
	 $data["slelvesPassword"]=$slelvesPassword; 	 	 
	 $data["note"]=$note;
	 $data["outTradeNo"]=$outTradeNo;	 		 	

     $datajson=json_encode($data);	 
	 $timestr=date("YmdHis",time());
	// $key="123456";
	 $key="123456"; 
	 $aa=$datajson.$timestr.$key;
	 $aa="[".$datajson."]".$timestr.$key;
	 $bb="[".$datajson."]";
	 $datasend=md5($aa);
     $datasend = strtoupper($datasend);
	 
	 $dd=array();
	 $dd["time"]=$timestr;
	 $dd["sign"]=$datasend;
	 $dd["data"]=$bb;

	 $url="http://testweixin.tg-lgl.com/lgl-web/open/operation.html";
	// $url="http://10.47.177.190/lgl-web/open/operation.html";
	 //$url="http://10.25.21.45:30001/open/operation.html";
	 
     $ret=post_lgl($url,$dd);

	// print_r($ret);

     return  $ret;
}


function post_lgl($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;    
 }
 

//-----------子函数-----------------
function  changeExpresscodetolglcode($code)   //快递代号转换到0公里code
{
   $db=conn();
   $result = mysql_query("SELECT * FROM  dyhawk.expresscompany  where  code='$code' ",$db); 
   $num= mysql_numrows($result); 
   $name="";  
   if($num!=0)
   {
       $name=mysql_result($result,0,"zerocode");
   }
   return $name;
}


function  changeDakehutolglcode($dakehucode)  //转换大客户代号到0公里code
{
   $db=conn();
   $result = mysql_query("SELECT * FROM  dyhawk.jijiandakehu  where  id='$dakehucode' ",$db);  
   $num= mysql_numrows($result); 
   $name="";  
   if($num!=0)
   {
       $name=mysql_result($result,0,"zerocode");
   }
   return $name;
}

//转换付款状态代号到0公里
//付款状态，0为签单之前的状态。 1为现金，2为月结，3为不收费 ,4到货拒付
//0公里： 0无付款  1未结算  2现结  3月结  4到付拒收	  
function  changepaidStatustolgl($paidStatus,$collectedFee)  
{
   $db=conn();

   $name="";  
   if(($paidStatus==0)&&($collectedFee!=""))
   {
       $name='1';
   }
   if($collectedFee=="")
   {
       $name='0';
   }    
   else if($paidStatus==1)
   {
       $name='2';  //现金
   }
   else if($paidStatus==2)
   {
       $name='3';  //月结
   }
   else if($paidStatus==3)
   {
       $name='0';  //无付款
   }
   else if($paidStatus==4)
   {
       $name='4';  //现金
   }
  
   return $name;
}







?>



