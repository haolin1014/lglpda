<?php
include_once ("../weixinserve/smsservep.php");  //微信接口
include_once ("../sms/sendSMS1.php");        //短信接口
//========================================    
/******************
执行函数入口
*******************/
function  domethod($json3,$stationaccount,$pdasn)
{
    

	$method=$json3["method"]; //得到函数名称
	switch($method)
	{
		case  "login":
      	$response=login($json3["username"],$json3["password"]);
		break;
		case  "diandan":
      	$response=diandan($json3["username"],$stationaccount,$pdasn,$json3["diandanstr"]);
		break;		
		case  "bangding":
      	$response=bangding($json3["username"],$stationaccount,$pdasn,$json3["bangdingstr"]);
		break;				
		case  "shujuxiazai":
      	$response=shujuxiazai($json3["username"],$stationaccount);
		break;							
		case  "duanxin":
      	$response=duanxin($json3["username"],$stationaccount,$pdasn,$json3["duanxinstr"]);
		break;
		case  "uploadduanyu":
      	$response=uploadduanyu($json3["username"],$stationaccount,$json3["duanyustr"]);
		break;			
		case  "shujutongbu":
      	$response=shujutongbu($json3["username"],$stationaccount);
		break;
		case  "qiandan":
      	$response=qiandan($json3["username"],$stationaccount,$pdasn,$json3["qiandanstr"]);
		break;						
		case  "yundangenzong":
      	$response=yundangenzong($json3["username"],$stationaccount,$json3["yundangenzongstr"]);
		break;			
		case  "shujutongji":
      	$response=shujutongji($json3["username"],$stationaccount,$json3["shujutongjistr"]);
		break;		
		case  "yundanchaxun":
      	$response=yundanchaxun($json3["username"],$stationaccount,$json3["yundanchaxunstr"]);
		break;	
		case  "duanxinchaxun":
      	$response=duanxinchaxun($json3["username"],$stationaccount,$json3["duanxinchaxunstr"]);
		break;						
		case  "jijian":
      	$response=jijian($json3["username"],$stationaccount,$json3["jijianstr"]);
		break;
		case  "jijiantongji":
      	$response=jijiantongji($json3["username"],$stationaccount,$json3["jijiantongjistr"]);
		break;
		case  "jijianchaxun":
      	$response=jijianchaxun($json3["username"],$stationaccount,$json3["jijianchaxunstr"]);
		break;							
		case  "expressbill":
      	$response=expressbill($json3["username"],$stationaccount,$pdasn,$json3["miandanstr"]);
		break;
		
		case  "miandanqingqiu":
      	$response=miandanqingqiu($json3["username"],$stationaccount,$pdasn,$json3["miandanstr"]);
		break;
			
	    case  "piliangmiandan":
      	$response=piliangmiandan($json3["username"],$stationaccount,$pdasn,$json3["piliangmiandanstr"]);
		break;			
		
	    case  "chaoqitixing":
      	$response=chaoqitixing($json3["username"],$stationaccount,$pdasn,$json3["chaoqitixingstr"]);
		break;
			
		case  "pdapicture":
      	$response=pdapicture($json3["username"],$stationaccount,$pdasn,$json3["picturestr"]);
		break;	

		case  "uploadsignurl":
      	$response=uploadsignurl($json3["username"],$stationaccount,$pdasn,$json3["uploadsignurlstr"]);
		break;	
				
		case  "waipai":
      	$response=waipai($json3["username"],$stationaccount,$pdasn,$json3["waipaistr"]);
		break;			
							
		case  "paijianxiangdan":
      	$response=paijianxiangdan($json3["username"],$stationaccount,$pdasn,$json3["paijianxiangdanstr"]);
		break;
		
		case  "jijianxiangdan":
      	$response=jijianxiangdan($json3["username"],$stationaccount,$pdasn,$json3["jijianxiangdanstr"]);
		break;	

		case  "tuijian":
      	$response=tuijian($json3["username"],$stationaccount,$pdasn,$json3["tuijianstr"]);
		break;
																																														
	}
   

  return  $response;
}

//======================================== 
//--------------登录-------------------------- 
	function  login($username,$password)
	{	
        
        $db=conn();
        $result = mysql_query("SELECT * FROM user  where  username='$username' ",$db);  
		$num= mysql_numrows ($result);
		if($num!=0)
		{	
		  $activation=mysql_result($result,0,"activation");	
		  $fund=mysql_result($result,0,"fund");
		  $rate=mysql_result($result,0,"rate");
		  $name=mysql_result($result,0,"name");
		}
		 $response="$activation"."pxp"."$fund"."pxp"."$rate"."pxp"."$name"."pxp"."0"."pxp";
		return  $response;			
	}
//--------------点单----------------------------------
//说明：点单分两种情况，已经外派过和没有外派。如果外派，将点单的用户改变同时将外派的人和时间清零，同时外派计数加1.	
	function  diandan($username,$stationaccount,$pdasn,$diandanstr)
	{	$res="ok";
	    $db4=conn4();		
		$yundan=split(",",$diandanstr);
		$len=count($yundan)/16-1;   //7为pda运单表的有效字段数
		for($i=0;$i<$len;$i++)
		{
		   $expressno=$yundan[$i*16+0];
		   $expressname=$yundan[$i*16+1];
		   $phonenumber=$yundan[$i*16+2];   
		   $daofuprice=$yundan[$i*16+3];
		   $daifuprice=$yundan[$i*16+4];
		   $diandantime=$yundan[$i*16+5];
		   $diandanuser=$yundan[$i*16+6];
		   $homenumber=$yundan[$i*16+7];
		   $homename=$yundan[$i*16+8];
		   $homeway=$yundan[$i*16+9];
		   $reason=$yundan[$i*16+10];

		   // 添加快递员姓名、手机号、品名、选择快递公司备注
		   $kdy_tel = $yundan[$i*16+11];		   
		   $kdy_name = $yundan[$i*16+12];		   
		   $product_name = $yundan[$i*16+13];
		   $dd_sign_url = $yundan[$i*16+14]		   
		   $expressname_remark = $yundan[$i*16+15];		   
		   		   
		   $expresstype="0";
		   //判断该运单是否存在,并判断外派是否存在waipaiuser,如果外派不为空，
		   $result = mysql_query("SELECT * FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		   $num= mysql_numrows ($result);
		   if($num==0)
		   {
		        $sqlstr="INSERT INTO `logistics` (`pdasn`, `stationaccount`,`expressno`,`expressname`,`phonenumber`,`expresstype`,`daofuprice`,`daifuprice`,`diandantime`,`diandanuser`,`homenumber`,`homename`,`homeway`,`reason`,`uplglflg`,`phase`,`kdy_tel`,`kdy_name`,`product_name`,`dd_sign_url`,`expressname_remark`) 
VALUES ('$pdasn','$stationaccount', '$expressno', '$expressname','$phonenumber', '$expresstype', '$daofuprice', '$daifuprice', '$diandantime', '$diandanuser', '$homenumber', '$homename', '$homeway', '$reason','1','0','$kdy_tel','$kdy_name','$product_name','$dd_sign_url','$expressname_remark')";
				mysql_query($sqlstr,$db4);  
 		
		   }
		   else
		   {
		         //隔天的重复点单时间要重新开始
		        $time=time();
  				$y=date("Y",$time);
  				$m=date("m",$time);
  				$d=date("d",$time);
  				$t0=mktime(0,0,0,$m,$d,$y);  //今天凌晨
				$diandantime0=mysql_result($result,0,"diandantime");
		        if($t0>$diandantime0)
				{
				  $diandantimestr=",diandantime=$diandantime";
				}
				else
				{
				  $diandantimestr="";
				}
   
		   
		        if($phonenumber=="")
				{
				  $phonenumber=mysql_result($result,0,"phonenumber");
				}
				$id=mysql_result($result,0,"id");
				$waipaiuser=mysql_result($result,0,"waipaiuser");  //是否已经外派
				if($waipaiuser!="")
				{
				    $sqlstr="UPDATE `logistics` SET  `expressno` = '$expressno',`expressname` = '$expressname',`phonenumber` = '$phonenumber',`expresstype` = '$expresstype',`daofuprice` = '$daofuprice',`daifuprice` = '$daifuprice' ,`homenumber` = '$homenumber',`homename` = '$homename',`homeway` = '$homeway' ,`reason` = '$reason',`diandanuser` = '$username',`phase` = '0', `waipaiuser` = '',`waipaitime` = '0',`waipain` =`waipain`+1,`uplglflg` =`uplglflg`+1,`kdy_tel`='$kdy_tel',`kdy_name`='$kdy_name',`product_name`='$product_name',`expressname_remark`='$expressname_remark'  $diandantimestr WHERE `id` ='$id' LIMIT 1";			
				} 
				else
				{
				   $sqlstr="UPDATE `logistics` SET  `expressno` = '$expressno',`expressname` = '$expressname',`phonenumber` = '$phonenumber',`expresstype` = '$expresstype',`daofuprice` = '$daofuprice',`daifuprice` = '$daifuprice' ,`homenumber` = '$homenumber',`homename` = '$homename',`homeway` = '$homeway' ,`reason` = '$reason',`diandanuser` = '$username' ,`phase` = '0' ,`uplglflg` =`uplglflg`+1 ,`kdy_tel`='$kdy_tel',`kdy_name`='$kdy_name',`product_name`='$product_name',`dd_sign_url`='$dd_sign_url',`expressname_remark`='$expressname_remark'   $diandantimestr WHERE `id` ='$id' LIMIT 1";
				}
									  							              
				if($diandantime!="null")  //消除把枪的bug
				{
				  mysql_query($sqlstr,$db4); 
				} 			      			   
		   } 	
		}
		
		 $response=$res;//"ok";
		return  $response;			
	}

//--------------绑定号码(暂不用)----------------------------------	
	function  bangding($username,$stationaccount,$pdasn,$bangdingstr)
	{	
	    $db4=conn4();
        $yundan=split(",",$bangdingstr);
		$len=count($yundan)/4-1;   //4为pda绑定表的有效字段数
		for($i=0;$i<$len;$i++)
		{
		   $expressno=$yundan[$i*4+0];
		   $phonenumber=$yundan[$i*4+1];
		   $bangdingtime=$yundan[$i*4+2];
		   $bangdinguser=$yundan[$i*4+3];
		   //判断该运单是否存在
		   $result = mysql_query("SELECT id FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		   $num= mysql_numrows ($result);
		   if($num==0)
		   {
		        $sqlstr="INSERT INTO `logistics` (`pdasn`, `stationaccount`,`expressno`,`phonenumber`,`bangdinguser`,`bangdingtime`) 
VALUES ('$pdasn','$stationaccount', '$expressno', '$phonenumber', '$bangdinguser','$bangdingtime')";								  								                mysql_query($sqlstr,$db4);  	
		   }
		   else
		   {
		        $id=mysql_result($result,0,"id");	
		        $sqlstr="UPDATE `logistics` SET  `phonenumber` = '$phonenumber',`bangdingtime` = '$bangdingtime',`bangdinguser` = '$bangdinguser' WHERE `id` ='$id' LIMIT 1";								  							              
				 mysql_query($sqlstr,$db4);  			   
		   } 	
		}
		
		 $response="ok";
		return  $response;			
	}

//----------------数据下载---------------------------------
function  shujuxiazai($username,$stationaccount)
{
   $db=conn();
   $db4=conn4();
   //$username="18321745565";
   $responsex="";
   $response="";
   //logistics
    $result = mysql_query("SELECT phonenumber,homenumber,homename,homeway FROM  logistics  where  stationaccount='$stationaccount'   group by phonenumber",$db4);  
    $num= mysql_numrows($result);
	$str=array();
   for($i=0;$i<$num;$i++)
   {  
  	 $str[$i*4+0]=urlencode(mysql_result($result,$i,"phonenumber"))."pxp";
	 $str[$i*4+1]=urlencode(mysql_result($result,$i,"homenumber"))."pxp";	
  	 $str[$i*4+2]=urlencode(mysql_result($result,$i,"homename"))."pxp";
	 $str[$i*4+3]=urlencode(mysql_result($result,$i,"homeway"))."pxp";		  	 	  	
   }
    $response=$response.implode('',$str);
		
	//logistics_a
	$responsex="";
    $result = mysql_query("SELECT phonenumber,homenumber,homename,homeway FROM  logistics_a  where  stationaccount='$stationaccount'    group by phonenumber",$db4);  
    $num= mysql_numrows($result);
	
	$str=array();
   for($i=0;$i<$num;$i++)
   {  
  	 $str[$i*4+0]=urlencode(mysql_result($result,$i,"phonenumber"))."pxp";
	 $str[$i*4+1]=urlencode(mysql_result($result,$i,"homenumber"))."pxp";	
  	 $str[$i*4+2]=urlencode(mysql_result($result,$i,"homename"))."pxp";
	 $str[$i*4+3]=urlencode(mysql_result($result,$i,"homeway"))."pxp";		  	 	  	
   }
    $response=$response.implode('',$str);
	
	//logistics_b
	$responsex="";
	$timecx=time()-3600*24*365;  //一年的时间
    $result = mysql_query("SELECT phonenumber,homenumber,homename,homeway FROM  logistics_b  where  stationaccount='$stationaccount'  and diandantime>$timecx   group by phonenumber  ",$db4);  
    $num= mysql_numrows($result);
	$str=array();
   for($i=0;$i<$num;$i++)
   {  
  	 $str[$i*4+0]=urlencode(mysql_result($result,$i,"phonenumber"))."pxp";
	 $str[$i*4+1]=urlencode(mysql_result($result,$i,"homenumber"))."pxp";	
  	 $str[$i*4+2]=urlencode(mysql_result($result,$i,"homename"))."pxp";
	 $str[$i*4+3]=urlencode(mysql_result($result,$i,"homeway"))."pxp";		  	 	  	
   }
    $response=$response.implode('',$str);

   return  $response;
   
  
}
//----------------数据同步---------------------------------
function  shujutongbu($username,$stationaccount)
{
   
   
   $db=conn();
   $db4=conn4();
   $db2=conn2();
   //$username="18321745565";
   $response="";

   //快递公司设置
  // $result = mysql_query("SELECT *  FROM  expresscompany",$db4);  
   $result = mysql_query("SELECT *  FROM  expresselct  where  stationaccount='$stationaccount'  order by  orderby",$db4);  
   $num= mysql_numrows($result);
   $response1="";
   for($i=0;$i<$num;$i++)
   {
	 $response1=$response1.urlencode(mysql_result($result,$i,"name"))."pxp";
	 $response1=$response1.urlencode(mysql_result($result,$i,"code"))."pxp";
	 $code=mysql_result($result,$i,"code");
     $result1 = mysql_query("SELECT *  FROM  expresscompany  where  code='$code'",$db4);  	 
	 $response1=$response1.urlencode(mysql_result($result1,0,"jianxie"))."pxp";	 	
   }
    $response1=$response1."0"."pxp";
   
    //大客户名称设置
   $result = mysql_query("SELECT *  FROM  jijiandakehu   where  account='$stationaccount'  and  status='0'",$db4);  
   $num= mysql_numrows($result);
   $response11="";
   for($i=0;$i<$num;$i++)
   {
   
	 $response11=$response11.urlencode(mysql_result($result,$i,"dakehuname"))."pxp";
	 $response11=$response11.urlencode(mysql_result($result,$i,"id"))."pxp";
	 $response11=$response11.urlencode(mysql_result($result,$i,"jianxie"))."pxp";
	 $response11=$response11.urlencode(mysql_result($result,$i,"address"))."pxp";
	 // 2017.04.14大客户新增字段	 	
	 $response11=$response11.urlencode(mysql_result($result,$i,"store_id"))."pxp";	 	
	 $response11=$response11.urlencode(mysql_result($result,$i,"build_id"))."pxp";	 	
	 $response11=$response11.urlencode(mysql_result($result,$i,"floor_id"))."pxp";	 	
	 $response11=$response11.urlencode(mysql_result($result,$i,"room_id"))."pxp";	 	
   }   
 
   //短信短语
   $result = mysql_query("SELECT *  FROM  phrase  where  username='$username' order by id",$db);  
   $num= mysql_numrows($result);
   $response2="";
   for($i=0;$i<$num;$i++)
   {
	 $response2=$response2.urlencode(mysql_result($result,$i,"content"))."pxp";	
   } 
   
    //用户账户金额费率
   $result = mysql_query("SELECT *  FROM  user  where  username='$username'  limit 1",$db);  
   $num= mysql_numrows($result);
   $response3="";
   for($i=0;$i<$num;$i++)
   {
	 $response3=$response3.urlencode(mysql_result($result,$i,"fund"))."pxp";
	 $response3=$response3.urlencode(mysql_result($result,$i,"rate"))."pxp";	 	
   }   
 

    //同步绑定数据和货号查询数据，主要是一个站点一个以上pda时，要求每个pda中都具有全站的数据
	// 单号、号码、货号、发短信时间、发短信用户,  同步最近10天的数据，目前是1个月的
    $timecx=time()-3600*24*5;  //5天   //10天数据上不来？ 为啥需查  
	//$timecx0=time()-3600*24*5; 
   //logistics
    $result = mysql_query("SELECT * FROM  logistics  where  stationaccount='$stationaccount' ",$db4);  //and diandantime>$timecx     order by  diandantime desc limit 4000 
    $num= mysql_numrows($result);
    $response4="";


//---------------------	
   $str=array();
   for($i=0;$i<$num;$i++)
   {       
  	 $str[$i*13+0]= urlencode(mysql_result($result,$i,"expressno"))."pxp";
	 $str[$i*13+1]= urlencode(mysql_result($result,$i,"phonenumber"))."pxp";	
	 $huohao=urlencode(mysql_result($result,$i,"huohao")); 
     //货号增加柜号
     $pdasn=mysql_result($result,$i,"pdasn");
	 $pdasn=str_pad($pdasn,12,"0",STR_PAD_LEFT); //对于不足12位的前边补零
	 $result1 = mysql_query("SELECT  stationmark  FROM  smtbx_info  where  devicesn='$pdasn'",$db2);  
     $num1= mysql_numrows($result1);
	 if($num1!=0)
	 {
	    $huohao=mysql_result($result1,0,"stationmark")."-".$huohao;
	 }
	 
	 $str[$i*13+2]=$huohao."pxp";
	 $str[$i*13+3]=urlencode(mysql_result($result,$i,"distributetime"))."pxp";		 
	 $str[$i*13+4]=urlencode(mysql_result($result,$i,"distributeuser"))."pxp";		
	 $str[$i*13+5]=urlencode(mysql_result($result,$i,"homenumber"))."pxp";
	 $str[$i*13+6]=urlencode(mysql_result($result,$i,"homename"))."pxp";
	 $str[$i*13+7]=urlencode(mysql_result($result,$i,"homeway"))."pxp"; 	 
	 $str[$i*13+8]=urlencode(mysql_result($result,$i,"expressname"))."pxp"; 
	 $str[$i*13+9]=urlencode(mysql_result($result,$i,"reason"))."pxp"; 
	 $str[$i*13+10]=urlencode(mysql_result($result,$i,"paycontent"))."pxp";
	 $str[$i*13+11]=urlencode(mysql_result($result,$i,"diandantime"))."pxp"; 
	 // 2017.04.14数据同步添加签单类型
	 $str[$i*13+12]=urlencode(mysql_result($result,$i,"signingkind"))."pxp"; 
	  	  	
   }  
    $response5=implode('',$str);
	

	 $response5=str_replace("%3A","", $response5);  //:  取消符号
	 $response5=str_replace("%2C","", $response5);	//,	
	 $response5=$response5."0"."pxp";
			
      //logistics // 对于到付到付件下载到PDA
      $result = mysql_query("SELECT expressno,daifuprice,daofuprice,diandanuser,diandantime FROM  logistics  where  stationaccount='$stationaccount'  and( (daifuprice<>'' and daifuprice<>'0') or (daofuprice<>'' and daofuprice<>'0'))",$db4);  
    $num= mysql_numrows($result);
    $response4="";
   for($i=0;$i<$num;$i++)
   {  
  	 $response4=$response4.urlencode(mysql_result($result,$i,"expressno"))."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"daifuprice"))."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"daofuprice"))."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"diandanuser"))."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"diandantime"))."pxp";	 
   } 
 
    $response6=$response4."0"."pxp";
	
	//获取该站点账号的所有智能柜账号
  // $result = mysql_query("SELECT *  FROM  stations_manage  where  account=$stationaccount",$db4); 
   $result = mysql_query("SELECT *  FROM  stations_manage  where  account  = '$stationaccount'",$db4); // account='$stationaccount'  
   $num= mysql_numrows($result);
   $response4="";
   //$response4=urlencode(mysql_result($result,0,"allbox"));
   $response4=(mysql_result($result,0,"allbox"));
   $devicesn=split(",",$response4);
   $response4="";
   $len=floor((count($devicesn))/2);
   for($i=0;$i<$len;$i++)
   {
      $devsn=$devicesn[$i*2];
	  $devsn=str_pad($devsn,12,"0",STR_PAD_LEFT); //对于不足12位的前边补零
      $result= mysql_query("SELECT  stationmark  FROM  smtbx_info  where  devicesn='$devsn'",$db2);   
      $num= mysql_numrows($result); 
	  if($num!=0)
	  {
	    $devname=mysql_result($result,0,"stationmark");
	  }
	  else
	  {
	    $devname="";
	  }	
      $response4=$response4.$devsn."pxp".$devname."pxp";
   }
   $response7=$response4."pxp"."0"."pxp";
   	// $response7=str_replace("%3A","pop", $response7);  //:  取消符号
	// $response7=str_replace("%2C","pop", $response7);	//,
	
	
	//获取站点的类别设置：0，学校， 1社区	
   $result = mysql_query("SELECT stationtype,diandanlimit  FROM  stations_manage   where  account='$stationaccount' ",$db4);  
   $num= mysql_numrows($result);
   $response8="";
   if($num>0)
   {
	 $response8=(mysql_result($result,0,"stationtype"))."pxp";
	 $response8=$response8.(mysql_result($result,0,"diandanlimit"))."pxp"."0"."pxp"; 	
   }
    
   
   //获取面单有关数据  
   $result = mysql_query("SELECT *  FROM  expressbill   where  stationaccount='$stationaccount' and state='0' ",$db4);  //未使用面单下载
   $num= mysql_numrows($result);
   $response9="";
  $str=array();  
  for($i=0;$i<$num;$i++)
   {
	 $str[$i*7+0]=(mysql_result($result,$i,"expressno"))."pxp";
	 $str[$i*7+1]=(mysql_result($result,$i,"directiontime"))."pxp"; 
	 $str[$i*7+2]=urlencode(mysql_result($result,$i,"direction"))."pxp"; 
	 $str[$i*7+3]=(mysql_result($result,$i,"expresscode"))."pxp"; 
	 $str[$i*7+4]=(mysql_result($result,$i,"state"))."pxp"; 
	 $str[$i*7+5]=(mysql_result($result,$i,"createtime"))."pxp"; 
	 $str[$i*7+6]=(mysql_result($result,$i,"endtime"))."pxp"; 	 	 	 
   }  
   $response9=implode('',$str);   
   $response9=$response9."pxp"."0"."pxp";
  
  
    //获取白名单  
   $result = mysql_query("SELECT *  FROM  whitelist   where  stationaccount='$stationaccount'",$db4); 
   $num= mysql_numrows($result);
   $response10="";
   for($i=0;$i<$num;$i++)
   {
	 $response10=$response10.(mysql_result($result,$i,"phonenumber"))."pxp";	 	 
   }   
  $response10=$response10."pxp"."0"."pxp";
  
  //获取站点账号
  
   $result = mysql_query("SELECT *  FROM  stations_manage   where  account='$stationaccount' ",$db4);  
   $num= mysql_numrows($result); 
   $stationname="";
   if($num!=0)
   {
      $stationname=urlencode(mysql_result($result,0,"stationname"));
   }
  
  $response12=$stationaccount."pxp".$stationname."pxp"."0"."pxp";

   //2017.04.14快递员信息
   $result = mysql_query("SELECT *  FROM  kdy_info   where  stationaccount='$stationaccount' ",$db4);  
   $num= mysql_numrows($result); 
   $str=array();
   for($i=0;$i<$num;$i++){
	 $str[$i*2+0]=(mysql_result($result,$i,"kdy_tel"))."pxp";
	 $str[$i*2+1]=(mysql_result($result,$i,"kdy_name"))."pxp";  	 
   } 
  $response13=implode('', $str);
  $response13=$response13."pxp"."0"."pxp";

     //2017.04.14品名信息
   $result = mysql_query("SELECT *  FROM  product_station   where  stationaccount='$stationaccount' ",$db4);  
   $num= mysql_numrows($result); 
   $str=array();
   for($i=0;$i<$num;$i++){
	 $str[$i*1+0]=(mysql_result($result,$i,"product_name"))."pxp"; 	 
   } 
  $response14=implode('', $str);
  $response14=$response14."pxp"."0"."pxp";
  

      //合成
   $response=$response1."uxu".$response11."uxu".$response2."uxu".$response3."uxu".$response5."uxu".$response6."uxu".$response7."uxu".$response8."uxu".$response9."uxu".$response10."uxu".$response12."uxu".$response13."uxu".$response14."uxu";	
     
   return  $response;
}


//----------------------短信发送-----------------------------------------------
	function duanxin($username,$stationaccount,$pdasn,$duanxinstr)
	{

	   	$db4=conn4();
		$db1=conn1();
		$db=conn();
		//欠费限制
		 $result = mysql_query("SELECT * FROM user  where  username='$username' ",$db);  
         $num= mysql_numrows ($result);
		 $fund=0;
		 if($num!=0)
		 {
		    $fund=mysql_result($result,0,"fund");		 
		    $rate=mysql_result($result,0,"rate");		 
		 }
		 // whl添加判断条件，费率为0时，即使费用小于0也让发
		 if($fund<=0 && $rate!='1000')  //不能发短信
		 {
		   	$response="nok";
		    return  $response;			 
		 }
		 //------继续发短信-----------------
		
		$t=time();
		$md5str=md5($duanxinstr);
  		$result = mysql_query("SELECT id  FROM  md5string  where  md5='$md5str' ",$db4);  
   		$num= mysql_numrows($result);
  		if($num==0) //第一次发送
		{
		    mysql_query("INSERT INTO `md5string`(`username`,`md5`,`sendtime`) VALUES ('$username', '$md5str', '$t')",$db4);
		   
		    $duanxin=split("pxp",$duanxinstr);		
			$len=floor(count($duanxin)/11);   //11为pda绑定表的有效字段数			
			$end_msm_num=0; //最终的短信数
			
			//---
			 $result = mysql_query("SELECT  stationtype  FROM  stations_manage  where  account='$stationaccount'",$db4);  
     		$num= mysql_numrows($result);
			$stationtype="0";
	 		if($num!=0)
	 		{
	    		$stationtype=urlencode(mysql_result($result,0,"stationtype"));  //stationtype=0学校1社区
	 		}		
			
			//开始发送短信
			for($i=0;$i<$len;$i++)  
		    {  
			   $expressno=$duanxin[$i*11+0];
			   $phonenumber=$duanxin[$i*11+1];
			   $huohao=$duanxin[$i*11+2];
		       $content=$duanxin[$i*11+3];
			   $duanxintime=$duanxin[$i*11+4];
			   $duanxinuser=$duanxin[$i*11+5];
			   $homenumber=$duanxin[$i*11+6];
			   $homename=$duanxin[$i*11+7];
			   $homeway=$duanxin[$i*11+8];
			   $reason=$duanxin[$i*11+9];
			   $expresscode=$duanxin[$i*11+10];
			   $waystr="";
			    switch($homeway)
				{
				 case 0:
				    $waystr="自提";
				 break;
				 case 1:
				    $waystr="送货";
				 break;
				 case 2:
				    $waystr="物业";
				 break;
				 case 3:
				    $waystr="其它";
				 break;				
	
				}
			   			   			   
			   
			   $msm_num=str_len($content,$huohao);  //计算每个短信的实际短信数
			   if($stationtype!=0)
			   {
			      $msm_num=$msm_num+2;  //增加2个字符，如 自提、物业
			   }
			   			   
	 		   $end_msm_num=$end_msm_num+$msm_num;  //累加短信数
			   
			   	//序列号产生
	 		   $m_date= date('YmdHis');
     		   $m_time = mb_substr(microtime(), 2,6); 
     		   $msm_sn=$m_date.$m_time;	   
			   			   
			   
			   //对运单物流表进行补充更新
		   	   //判断该运单是否存在
		     $result = mysql_query("SELECT id FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		     $num= mysql_numrows ($result);
		     if($num==0)
		     {  //在插入状态，将duanxintime同时插入diandantime
		        $sqlstr="INSERT INTO `logistics` (`pdasn`, `stationaccount`,`expressno`,`diandantime`,`diandanuser`,`phonenumber`,`distributeway`,`distributeuser`,`distributetime`,`huohao`,`msm_sn`,`homenumber`,`homename`,`homeway`,`reason`,`expressname`,`uplglflg`,`phase`) 
VALUES ('$pdasn','$stationaccount', '$expressno', '$duanxintime', '$username', '$phonenumber','1','$duanxinuser', '$duanxintime','$huohao','$msm_sn','$homenumber','$homename','$homeway','$reason','$expresscode','1','5')";								  								                mysql_query($sqlstr,$db4);  	
		   	  }
		   	  else
		   	  {
		        $id=mysql_result($result,0,"id");
				if($expresscode!="")
				{
				$expcode=",`expressname`='$expresscode'";
				}
				else
				{
				$expcode="";
				} 	
		        $sqlstr="UPDATE `logistics` SET `phonenumber` = '$phonenumber',`distributeway`='1',`distributetime` = '$duanxintime',`distributeuser` = '$duanxinuser',`huohao` = '$huohao',`msm_sn`='$msm_sn',`homenumber`='$homenumber',`homename`='$homename',`homeway`='$homeway',`reason`='$reason',`phase`='5' ,`uplglflg` =`uplglflg`+1  $expcode WHERE `id` ='$id' LIMIT 1";								  							              
				 mysql_query($sqlstr,$db4);  			   
		      } 	
		       //发送短信
			   
			$rcvnumber_o=str_replace(" ", "", $phonenumber);
			   
			//内容的组装：货号+content
			if($stationtype==0)
			{
				$exname="";
				$result = mysql_query("SELECT expressname FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		        $num= mysql_numrows ($result);
				if($num!=0)
				{
				    $expressname=mysql_result($result,0,"expressname");
					$result = mysql_query("SELECT name FROM  expresscompany  where  code='$expressname'limit 1",$db4);  
		        	$num= mysql_numrows ($result);
					if($num!=0)
					{
					  $exname=mysql_result($result,0,"name");
					}								
				}		
				// $m_content="<取货号:".$huohao.",运单号:".$expressno.",".$exname.">".$content;
				$m_content="取货号:".$huohao.",".$exname.":".$expressno.".".$content;
			}
			else
			{
						    //加上快递公司名称
				$exname="";
				$result = mysql_query("SELECT expressname FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		        $num= mysql_numrows ($result);
				if($num!=0)
				{
				    $expressname=mysql_result($result,0,"expressname");
					$result = mysql_query("SELECT name FROM  expresscompany  where  code='$expressname'limit 1",$db4);  
		        	$num= mysql_numrows ($result);
					if($num!=0)
					{
					  $exname=mysql_result($result,0,"name");
					}								
				}		    		
			   $m_content="<取货号:".$huohao.$waystr.">".$content." ".$exname;
			}
			 
					   
			$m_content=str_replace("'","",$m_content); //有这个字符存不进数据库 
			$sendtime=mktime();
			$frequency=0;
			$status="0";  //0待发，1已发送，2成功，3失
			
			$stationid=$stationaccount;  //  
			//$smsflg=getweixinswitchsms($rcvnumber_o);  //weixin 
		//	$smsflg=getweixinswitchsms($rcvnumber_o,$db);  //weixin 
			

		//	if($smsflg!=2) //weixin  只要不等于2，不关注、关注需要短信  这两种情况都发短信
			{ 
			   if($content!="")  //内容为空的运单将不发送短信
			   {			   
			   		$sqlstr="INSERT INTO  `msmwait` ( `username` ,`rcvnumber` ,`sendtime` ,`huohao` ,`stationid` ,`frequency`,`status`,`content`,`msm_sn`  ) 
                                  VALUES ('$username', '$rcvnumber_o',  '$sendtime',  '$huohao', '$stationid', '$frequency', '$status', '$m_content', '$msm_sn')";								  								 
                    mysql_query($sqlstr,$db1); 
			   }			  

			}
		/*				
			if($smsflg!=0)  //关注后才发微信
			{
			  //sendmessagetoweixin($rcvnumber_o,$huohao,"",$expressno,$content,"1");	//weixin 
			  sendmessagetoweixin($stationid,$msm_sn,$rcvnumber_o,$huohao,"","",$expressno,"您可点开此条消息，转发好友代取",$m_content,"0",$db);  					
			}

		  */ 
		  }//end for
		  
		   //扣除费用,可以为负数，下次若为负数，就不允许发送
		  $result = mysql_query("SELECT * FROM user  where  username='$username' ",$db);  
          $num= mysql_numrows ($result);
          if(($num!=0)&&($content!="")) //在内容为空的情况下，不发送短信也不扣费用
		  {
		     $fund=mysql_result($result,0,"fund");
		     $rate=mysql_result($result,0,"rate");
			 if($rate==0)
			 {
			   $rate=70; //默认7分/条
			 }
			 else if($rate==1000)
			 {
			    $rate=0; //不收费
			 }
		     $needfund=$end_msm_num*$rate;

			 $befor_fund=$fund;
		     $fund=$fund-$needfund;
			 $after_fund=$fund;	
    		 $sqlstr="UPDATE  `user` SET  `fund` =  '$fund'  WHERE  `username` =$username  LIMIT 1" ;
            mysql_query($sqlstr,$db);
			 
			 	//扣除费用记录
            $sqlstr="INSERT INTO  `consumingrecords` (`username` ,`beforsend` ,`aftersend` ,`msm_num` ,`operation` ,`time` ) 
VALUES ('$username',  '$befor_fund',  '$after_fund',  '$end_msm_num',  '1',  '$sendtime')" ;
   			mysql_query($sqlstr,$db);
			 			 
		  
		  } 
			
		}
		else
		{
		   $id=mysql_result($result,0,"id");
		   mysql_query("UPDATE `md5string` SET `sendtime` = '$t' WHERE `id` =$id  LIMIT 1 ",$db4);	
		}	
			
		$response="ok";
		return  $response;			
	}
//--------------上传短语设置----------------------------------	
//还存在半角的单引号双引号问题
	function  uploadduanyu($username,$stationaccount,$duanyustr)
	{	
	    $db=conn();
        $duanyu=split("pxp",$duanyustr);
		$len=count($duanyu)-1;   //1个有效字段数
		if($len!=0)
		{
			//删除该用的所有短语
			mysql_query("DELETE FROM  phrase  where  username=$username",$db);
			//逐条插入
			for($i=0;$i<$len;$i++)
			{
			   $content=$duanyu[$i];
			   $sqlstr="INSERT INTO `phrase`( `username`,`stationid`,`content`) VALUES ('$username', '$stationaccount', '$content')";								  								               mysql_query($sqlstr,$db);
			}
		}

		 $response="ok";
		return  $response;			
	}

//--------------签单----------------------------------
// 签单的种类：0正常（direction:    ），1退件（direction:    ），2问题件（direction:    ）     direction 为字符类型
//---------------------------------------------------	
	function  qiandan($username,$stationaccount,$pdasn,$qiandanstr)
	{	
	    $db4=conn4();
        $yundan=split("pxp",$qiandanstr);
		$len=count($yundan)/9-1;   //6为pda绑定表的有效字段数
		for($i=0;$i<$len;$i++)
		{
		   $expressno=$yundan[$i*9+0];
		   $qiandantime=$yundan[$i*9+1];
		   $qiandanuser=$yundan[$i*9+2];
		   $qiandankind=$yundan[$i*9+3];
		   $expresscode=$yundan[$i*9+4];
		   $direction=$yundan[$i*9+5];
		   $picstatus=$yundan[$i*9+6];
		   $payway=$yundan[$i*9+7];
		   $paycontent=$yundan[$i*9+8];		   		   
		   
		   //判断该运单是否存在
		   $result = mysql_query("SELECT id,phonenumber FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		   $num= mysql_numrows ($result);
		   $num0=$num;
		   if($num==0)
		   { 
		    //太慢  logistics_a  
		 //   $result1 = mysql_query("SELECT id FROM  logistics_a  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		 //      $num= mysql_numrows ($result1); //防止二次签单  
		 //      if($num==0)
			   {
		           //在插入状态，将,'$qiandantime'也同时插入diandantime
		           $sqlstr="INSERT INTO `logistics` ( `pdasn`,`stationaccount`,`expressno`,`diandantime`,`diandanuser`,`signinguser`,`signingtime`,`signingkind`,`direction`,`expressname`,`picstatus`,`payway`,`paycontent`,`uplglflg`,`phase`) VALUES ('$pdasn','$stationaccount', '$expressno','$qiandantime','$username', '$qiandanuser','$qiandantime','$qiandankind','$direction','$expresscode','$picstatus','$payway','$paycontent','1','2')";							  				
				   mysql_query($sqlstr,$db4); 
			   }		 	
		   }
		   else
		   {
		        $id=mysql_result($result,0,"id");
				if($expresscode!="")
				{
				$expcode=",`expressname`='$expresscode'";
				}
				else
				{
				$expcode="";
				} 	

                 if($qiandankind=='2')  //问题件
				 {
				     $phase='3';
				 }
				 else  if($qiandankind=='3')  //退件
				 {
				     $phase='4';
				 }
				 else
				 {
				     $phase='2';   //正常签收
				 }
				 

		        $sqlstr="UPDATE `logistics` SET  `signingtime` = '$qiandantime',`signinguser` = '$qiandanuser',`signingkind` = '$qiandankind',`direction` = '$direction' ,`picstatus` = '$picstatus' ,`payway` = '$payway',`paycontent` = '$paycontent',`phase` = '$phase' ,`uplglflg` =`uplglflg`+1  $expcode  WHERE `id` ='$id' LIMIT 1";								  							              
				 mysql_query($sqlstr,$db4);  			   
		   } 
		   
		   if($num0!=0)  //签单发短信
		   {
		   
		     if($qiandankind=='2') //问题件
			 {
			    
				$content="亲，您的快件($expressno)因$direction ，已转为问题件，如有疑问请联系零公里门店。感谢您对我们的支持！"; 
			 }
		      else if($qiandankind=='3') //退件
			 {
			    $content="亲，您的快件($expressno)因$direction ，已经退回快递公司。感谢您对我们的支持！";
			 }
			 else
			 {
			    $content="亲，小蜜蜂为您派送的快件($expressno)已经被签收。感谢您对我们的支持！";
			 }		   
		    
		      $devicesn=$pdasn;
		      $rcvnumber=mysql_result($result,0,"phonenumber");
			  	//序列号产生
	 		   $m_date= date('YmdHis');
     		   $m_time = mb_substr(microtime(), 2,6); 
     		   $msm_sn=$m_date.$m_time;	
			  if($rcvnumber!="")
			  {
			  	//sendOneSMSonly($rcvnumber,$content,$username,$devicesn,$msm_sn,$db4);
			  }     
		      
		   }

		}

		 $response="ok";
		return  $response;			
	}

//-------------运单跟踪-----------------------------------
//运单跟踪也考虑到订单重复的问题	
	function   yundangenzong($username,$stationaccount,$yundangenzongstr)
	{	 
	    $db4=conn4();
		$db2=conn2();
        $expressno=$yundangenzongstr;
		 //获取该订单内容
		$result = mysql_query("SELECT * FROM  logistics  where   expressno='$expressno' order by id  desc",$db4);  //stationaccount='$stationaccount'   limit 1
		$num= mysql_numrows ($result);				
		$response="";		
		//if($num!=0)
		for($i=0;$i<$num;$i++)
		{		
		   $expressno=urlencode(mysql_result($result,$i,"expressno"));
		   $expressname=urlencode(mysql_result($result,$i,"expressname"));		   
		   $expresstype=urlencode(mysql_result($result,$i,"expresstype"));
		   $daofuprice=urlencode(mysql_result($result,$i,"daofuprice"));
		   $daifuprice=urlencode(mysql_result($result,$i,"daifuprice"));
		   $diandantime=urlencode(mysql_result($result,$i,"diandantime"));		  
		// $diandanuser=urlencode(mysql_result($result,$i,"diandanuser"));   
		   $diandanuser=urlencode(changeMobiletoName(mysql_result($result,$i,"diandanuser")));		   		   
		   $phonenumber=urlencode(mysql_result($result,$i,"phonenumber"));
		   $bangdingtime=urlencode(mysql_result($result,$i,"bangdingtime"));		   
		// $bangdinguser=urlencode(mysql_result($result,$i,"bangdinguser"));
		   $bangdinguser=urlencode(changeMobiletoName(mysql_result($result,$i,"bangdinguser")));		   		   
		   $distributeway=urlencode(mysql_result($result,$i,"distributeway"));		   
		   $distributetime=urlencode(mysql_result($result,$i,"distributetime"));
		  //$distributeuser=urlencode(mysql_result($result,$i,"distributeuser"));
		   $distributeuser=urlencode(changeMobiletoName(mysql_result($result,$i,"distributeuser")));		   
		   $signingtime=urlencode(mysql_result($result,$i,"signingtime"));
		   //$signinguser=urlencode(mysql_result($result,$i,"signinguser")); 
		   $signinguser=urlencode(changeMobiletoName(mysql_result($result,$i,"signinguser")));
		   $smstatus=urlencode(mysql_result($result,$i,"smstatus"));
		   $huohao=urlencode(mysql_result($result,$i,"huohao"));
		   $signingkind=urlencode(mysql_result($result,$i,"signingkind"));
		   $homenumber=urlencode(mysql_result($result,$i,"homenumber"));
		   $homename=urlencode(mysql_result($result,$i,"homename"));
		   $homeway=urlencode(mysql_result($result,$i,"homeway"));			   
		   $waipaitime=urlencode(mysql_result($result,$i,"waipaitime"));
		   //$waipaiuser=urlencode(mysql_result($result,$i,"waipaiuser"));
		   $waipaiuser=urlencode(changeMobiletoName(mysql_result($result,$i,"waipaiuser")));		   
		   $picstatus=urlencode(mysql_result($result,$i,"picstatus")); 
		   
		   $payway=urlencode(mysql_result($result,$i,"payway"));
		   $paycontent=urlencode(mysql_result($result,$i,"paycontent")); 

		   $dd_sign_url=urlencode(mysql_result($result,$i,"dd_sign_url")); 	
		   $qd_sign_url=urlencode(mysql_result($result,$i,"qd_sign_url")); 	
		   $tj_sign_url=urlencode(mysql_result($result,$i,"tj_sign_url")); 	

		        //货号增加柜号
     		$pdasn=mysql_result($result,$i,"pdasn");
	 		$pdasn=str_pad($pdasn,12,"0",STR_PAD_LEFT); //对于不足12位的前边补零
	 		$result1 = mysql_query("SELECT  stationmark  FROM  smtbx_info  where  devicesn='$pdasn'",$db2);  
     		$num1= mysql_numrows($result1);
	 		if($num1!=0)
	 		{
	    		$huohao=mysql_result($result1,0,"stationmark")."-".$huohao;
	 		}
		   
	          //获得站点的地址
			  $stationname="未知";
	       $stationaccount=urlencode(mysql_result($result,$i,"stationaccount"));
		   $result1 = mysql_query("SELECT  stationname  FROM  stations_manage  where  account='$stationaccount'",$db4);  
     		$num1= mysql_numrows($result1);
	 		if($num1!=0)
	 		{
	    		$stationname=urlencode(mysql_result($result1,0,"stationname"));
	 		}
		   
		   $direction=urlencode(mysql_result($result,$i,"direction"));
		   
			$response=$response.$expressno."pxp".$expressname."pxp".$expresstype."pxp".$daofuprice."pxp".$daifuprice."pxp".$diandantime."pxp".$diandanuser."pxp".$phonenumber."pxp".$bangdingtime."pxp".$bangdinguser."pxp".$distributeway."pxp".$distributetime."pxp".$distributeuser."pxp".$signingtime."pxp".$signinguser."pxp".$huohao."pxp".$smstatus."pxp".$stationname."pxp".$signingkind."pxp".$homenumber."pxp".$homename."pxp".$homeway."pxp".$waipaitime."pxp".$waipaiuser."pxp".$picstatus."pxp".$payway."pxp".$paycontent."pxp".$direction."pxp".$dd_sign_url."pxp".$qd_sign_url."pxp".$tj_sign_url."pxp";		   
		
		}

        //--------------------------------
		$result = mysql_query("SELECT * FROM  logistics_a  where   expressno='$expressno'   order by id  desc",$db4);  //stationaccount='$stationaccount'   limit 1
		$num= mysql_numrows ($result);				
		//$response="0";		
		for($i=0;$i<$num;$i++)
		{		
		   $expressno=urlencode(mysql_result($result,$i,"expressno"));
		   $expressname=urlencode(mysql_result($result,$i,"expressname"));		   
		   $expresstype=urlencode(mysql_result($result,$i,"expresstype"));
		   $daofuprice=urlencode(mysql_result($result,$i,"daofuprice"));
		   $daifuprice=urlencode(mysql_result($result,$i,"daifuprice"));
		   $diandantime=urlencode(mysql_result($result,$i,"diandantime"));
		   $diandanuser=urlencode(mysql_result($result,$i,"diandanuser"));
		   $phonenumber=urlencode(mysql_result($result,$i,"phonenumber"));
		   $bangdingtime=urlencode(mysql_result($result,$i,"bangdingtime"));		   
		   $bangdinguser=urlencode(mysql_result($result,$i,"bangdinguser"));		   
		   $distributeway=urlencode(mysql_result($result,$i,"distributeway"));		   
		   $distributetime=urlencode(mysql_result($result,$i,"distributetime"));
		   $distributeuser=urlencode(mysql_result($result,$i,"distributeuser"));
		   $signingtime=urlencode(mysql_result($result,$i,"signingtime"));
		   $signinguser=urlencode(mysql_result($result,$i,"signinguser"));
		   $smstatus=urlencode(mysql_result($result,$i,"smstatus"));
		   $huohao=urlencode(mysql_result($result,$i,"huohao"));
		   $signingkind=urlencode(mysql_result($result,$i,"signingkind"));
		   $homenumber=urlencode(mysql_result($result,$i,"homenumber"));
		   $homename=urlencode(mysql_result($result,$i,"homename"));
		   $homeway=urlencode(mysql_result($result,$i,"homeway"));
		   
		   $waipaitime=urlencode(mysql_result($result,$i,"waipaitime"));
		   $waipaiuser=urlencode(mysql_result($result,$i,"waipaiuser"));
		   $picstatus=urlencode(mysql_result($result,$i,"picstatus")); 
		   
		   $payway=urlencode(mysql_result($result,$i,"payway"));
		   $paycontent=urlencode(mysql_result($result,$i,"paycontent")); 		   		   
		   
		   $dd_sign_url=urlencode(mysql_result($result,$i,"dd_sign_url")); 	
		   $qd_sign_url=urlencode(mysql_result($result,$i,"qd_sign_url")); 	
		   $tj_sign_url=urlencode(mysql_result($result,$i,"tj_sign_url")); 
		   		  
		   
		        //货号增加柜号
     		$pdasn=mysql_result($result,$i,"pdasn");
	 		$pdasn=str_pad($pdasn,12,"0",STR_PAD_LEFT); //对于不足12位的前边补零
	 		$result1 = mysql_query("SELECT  stationmark  FROM  smtbx_info  where  devicesn='$pdasn'",$db2);  
     		$num1= mysql_numrows($result1);
	 		if($num1!=0)
	 		{
	    		$huohao=mysql_result($result1,0,"stationmark")."-".$huohao;
	 		}
		   
	          //获得站点的地址
			  $stationname=urlencode("未知");
	       $stationaccount=urlencode(mysql_result($result,$i,"stationaccount"));
		   $result1 = mysql_query("SELECT  stationname  FROM  stations_manage  where  account='$stationaccount'",$db4);  
     		$num1= mysql_numrows($result1);
	 		if($num1!=0)
	 		{
	    		$stationname=urlencode(mysql_result($result1,0,"stationname"));
	 		}
		      
		   $direction=urlencode(mysql_result($result,$i,"direction"));
		   
			$response=$response.$expressno."pxp".$expressname."pxp".$expresstype."pxp".$daofuprice."pxp".$daifuprice."pxp".$diandantime."pxp".$diandanuser."pxp".$phonenumber."pxp".$bangdingtime."pxp".$bangdinguser."pxp".$distributeway."pxp".$distributetime."pxp".$distributeuser."pxp".$signingtime."pxp".$signinguser."pxp".$huohao."pxp".$smstatus."pxp".$stationname."pxp".$signingkind."pxp".$homenumber."pxp".$homename."pxp".$homeway."pxp".$waipaitime."pxp".$waipaiuser."pxp".$picstatus."pxp".$payway."pxp".$paycontent."pxp".$direction."pxp".$dd_sign_url."pxp".$qd_sign_url."pxp".$tj_sign_url."pxp";	
		
		}
        if($response=="")
		{
		   $response="0";
		}		
		return  $response;			
	}


//----------------数据统计---------------------------------
function  shujutongji($username,$stationaccount,$shujutongjistr)
{


   $db=conn();
   $db4=conn4();
   $db2=conn2();
   //$username="18321745565";  
    $time=time();
   	//$time=mktime();
	$y=date("Y",$time);
	$m=date("m",$time);
	$d=date("d",$time);
	$time0=mktime("0,0,0,$m,$d,$y");  //今天的基准时间 0点0分0秒

	$etime=$time0-1*24*3600;
    $stime=$etime-9*24*3600;
    
	$alldiandan=array();
    $sql = "select qiandannum,diandannum,qiandan1d,qiandan2d,qiandan3d,qiandan4d,qiandan5d,daytime  from datastatistic where stationaccount ='$stationaccount'   and daytime between $stime and $etime";	
	$result = mysql_query($sql,$db4);
	$num= mysql_numrows ($result);
	for($i=0;$i<$num;$i++)
	{
	  $row=mysql_fetch_assoc($result);
	  $alldiandan[$i*7+0]=$row[daytime];
	  $alldiandan[$i*7+1]=$row[diandannum];
	  $alldiandan[$i*7+2]=$row[qiandan1d];
	  $alldiandan[$i*7+3]=$row[qiandan2d]+$alldiandan[$i*7+2];
	  $alldiandan[$i*7+4]=$row[qiandan3d]+$alldiandan[$i*7+3];
	  $alldiandan[$i*7+5]=$row[qiandan4d]+$alldiandan[$i*7+4];
	  $alldiandan[$i*7+6]=$row[qiandan5d]+$alldiandan[$i*7+5];	  	  
	}
	$response1="";	
	for($i=0;$i<$num*7;$i++)
	{
	  $response1=$response1.$alldiandan[$i]."pxp";	
	}
	
 //  $response1="0";
   $response2="0";
   $response3="0";
   //合成
   $response=$response1."uxu".$response2."uxu".$response3."uxu";
   
   return  $response;
     
}

//--------运单查询，在两个表中查，最后将两个表中查询的结果合在一起-------------------------------------------------------------------------------------------------
 	function   yundanchaxun($username,$stationaccount,$yundanchaxunstr)
	{	
	    $db4=conn4();
		$db2=conn2();
		$yundan=split(",",$yundanchaxunstr);
		$type=$yundan[0];
		$hao=$yundan[1];
		$starttime=$yundan[2];

		//在logistics表中找
		if($type==1)//1为电话号码查询
		{
		$result = mysql_query("SELECT expressno,phonenumber,huohao,distributetime,distributeuser,pdasn,homename,homenumber,homeway,expressname   FROM  logistics  where  stationaccount='$stationaccount' and diandantime>$starttime  and  phonenumber='$hao'",$db4);    
		}
		else if($type==2)  //运单号查询
		{		  
         $result = mysql_query("SELECT expressno,phonenumber,huohao,distributetime,distributeuser,pdasn,homename,homenumber,homeway,expressname   FROM  logistics  where  stationaccount='$stationaccount' and diandantime>$starttime  and  expressno='$hao'",$db4);		  
		  
		}		
		else if($type==3)  //姓名
		{		  
         $result = mysql_query("SELECT expressno,phonenumber,huohao,distributetime,distributeuser,pdasn,homename,homenumber,homeway,expressname     FROM  logistics  where  stationaccount='$stationaccount' and diandantime>$starttime  and  homename='$hao'",$db4);		  
		  
		}		
		else if($type==4)  //门牌号
		{		  
         $result = mysql_query("SELECT expressno,phonenumber,huohao,distributetime,distributeuser,pdasn,homename,homenumber,homeway,expressname      FROM  logistics  where  stationaccount='$stationaccount' and diandantime>$starttime  and  homenumber='$hao'",$db4);		  
		  
		}		
		
    $num= mysql_numrows($result);
	

	
    $response4="";
   for($i=0;$i<$num;$i++)
   {
   
  	 $response4=$response4.urlencode(mysql_result($result,$i,"expressno"))."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"phonenumber"))."pxp";	
	 $huohao=urlencode(mysql_result($result,$i,"huohao"));
	  
     //货号增加柜号
     $pdasn=mysql_result($result,$i,"pdasn");
	 $pdasn=str_pad($pdasn,12,"0",STR_PAD_LEFT); //对于不足12位的前边补零
	 $result1 = mysql_query("SELECT  stationmark  FROM  smtbx_info  where  devicesn='$pdasn'",$db2);  
     $num1= mysql_numrows($result1);
	 if($num1!=0)
	 {
	    $huohao=mysql_result($result1,0,"stationmark")."-".$huohao;
	 }
	 $response4=$response4.$huohao."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"distributetime"))."pxp";		 
	 $response4=$response4.urlencode(mysql_result($result,$i,"distributeuser"))."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"homename"))."pxp";		 
	 $response4=$response4.urlencode(mysql_result($result,$i,"homenumber"))."pxp";	 
	 $response4=$response4.urlencode(mysql_result($result,$i,"homeway"))."pxp";	
	 $response4=$response4.urlencode(mysql_result($result,$i,"expressname"))."pxp";	 
	 	 	  	
   } 
   
   
 		//在logistics_a表中找
		if($type==1)//1为电话号码查询
		{
		$result = mysql_query("SELECT expressno,phonenumber,huohao,distributetime,distributeuser,pdasn,homename,homenumber,homeway,expressname   FROM  logistics_a  where  stationaccount='$stationaccount' and diandantime>$starttime  and  phonenumber='$hao'",$db4);    
		}
		else if($type==2) //运单号查询
		{		  
         $result = mysql_query("SELECT expressno,phonenumber,huohao,distributetime,distributeuser,pdasn,homename,homenumber,homeway,expressname     FROM  logistics_a  where  stationaccount='$stationaccount' and diandantime>$starttime  and  expressno='$hao'",$db4);		  
		  
		}	
		else if($type==3) //姓名
		{		  
         $result = mysql_query("SELECT expressno,phonenumber,huohao,distributetime,distributeuser,pdasn ,homename,homenumber,homeway,expressname  FROM  logistics_a  where  stationaccount='$stationaccount' and diandantime>$starttime  and  homename='$hao'",$db4);		  
		  
		}				
		else if($type==4) //门牌号
		{		  
         $result = mysql_query("SELECT expressno,phonenumber,huohao,distributetime,distributeuser,pdasn,homename,homenumber,homeway,expressname   FROM  logistics_a  where  stationaccount='$stationaccount' and diandantime>$starttime  and  homenumber='$hao'",$db4);		  
		  
		}							

    $num= mysql_numrows($result);
	
		
	
    $response5="";
   for($i=0;$i<$num;$i++)
   {
   
  	 $response5=$response5.urlencode(mysql_result($result,$i,"expressno"))."pxp";
	 $response5=$response5.urlencode(mysql_result($result,$i,"phonenumber"))."pxp";	
	 $huohao=urlencode(mysql_result($result,$i,"huohao"));
	  
     //货号增加柜号
     $pdasn=mysql_result($result,$i,"pdasn");
	 $pdasn=str_pad($pdasn,12,"0",STR_PAD_LEFT); //对于不足12位的前边补零
	 $result1 = mysql_query("SELECT  stationmark  FROM  smtbx_info  where  devicesn='$pdasn'",$db2);  
     $num1= mysql_numrows($result1);
	 if($num1!=0)
	 {
	    $huohao=mysql_result($result1,0,"stationmark")."-".$huohao;
	 }
	 $response5=$response5.$huohao."pxp";
	 $response5=$response5.urlencode(mysql_result($result,$i,"distributetime"))."pxp";		 
	 $response5=$response5.urlencode(mysql_result($result,$i,"distributeuser"))."pxp";
	 $response5=$response5.urlencode(mysql_result($result,$i,"homename"))."pxp";		 
	 $response5=$response5.urlencode(mysql_result($result,$i,"homenumber"))."pxp";	 
	 $response5=$response5.urlencode(mysql_result($result,$i,"homeway"))."pxp";	
	 $response5=$response5.urlencode(mysql_result($result,$i,"expressname"))."pxp";	 	  	
   } 
     
    $response=$response4.$response5."0"."pxp";
		
		return  $response;			
 }


//-------------------------------------------------------------------------------------------------
 	function   duanxinchaxun($username,$stationaccount,$duanxinchaxunstr)
	{
	
	    $db4=conn4();
		$db2=conn2();
		$yundan=split(",",$duanxinchaxunstr);
		$datetime0=$yundan[0];
		$datetime1=$yundan[1];
		$xuanze1=$yundan[2];
		$xuanze2=$yundan[3];
			
		
		$x1=0;
		if($xuanze1=="全部")
		{
		   $x1=4;
		}
		else if($xuanze1=="货架")
		{
		   $x1=1;
		}
		else if($xuanze1=="智能柜")
		{
		   $x1=2;
		}
		
		$x2=0;
		if($xuanze2=="全部")
		{
		   $x2=4;
		}
		else if($xuanze2=="成功")
		{
		   $x2=2;
		}
		else if($xuanze2=="失败")
		{
		   $x2=3;
		}		
		else if($xuanze2=="已发")
		{
		   $x2=0;
		}				
		
	
		
		//在logistics表中找	
	if(($xuanze1=="全部")&&($xuanze2=="全部"))
		{  
		  $sqlstr="SELECT expressno,phonenumber,huohao,distributetime,distributeuser,smstatus,distributeway,pdasn  FROM  logistics  where  stationaccount='$stationaccount' and distributetime>$datetime0  and  distributetime<$datetime1 order by distributetime";
		  		  
		}
		else if(($xuanze1=="全部")&&(($xuanze2=="成功")||($xuanze2=="失败")||($xuanze2=="已发")))
		{
		   $sqlstr="SELECT expressno,phonenumber,huohao,distributetime,distributeuser,smstatus,distributeway,pdasn  FROM  logistics  where  stationaccount='$stationaccount' and distributetime>$datetime0  and  distributetime<$datetime1  and  smstatus=$x2  order by distributetime";   //
		}
			else if((($xuanze1=="货架")||($xuanze1=="智能柜"))&&($xuanze2=="全部"))
		{
		   $sqlstr="SELECT expressno,phonenumber,huohao,distributetime,distributeuser,smstatus,distributeway,pdasn  FROM  logistics  where  stationaccount='$stationaccount' and distributetime>$datetime0  and  distributetime<$datetime1 and  distributeway=$x1  order by distributetime";		  
		}
        else if((($xuanze1=="货架")||($xuanze1=="智能柜"))&&(($xuanze2=="成功")||($xuanze2=="失败")||($xuanze2=="已发")))
		{
		   $sqlstr="SELECT expressno,phonenumber,huohao,distributetime,distributeuser,smstatus,distributeway,pdasn  FROM  logistics  where  stationaccount='$stationaccount' and distributetime>$datetime0  and  distributetime<$datetime1  and  smstatus=$x2  and  distributeway=$x1  order by distributetime";		
		
		}			
		$result = mysql_query($sqlstr,$db4);    		
        $num= mysql_numrows($result);
		
        $response4="";
   for($i=0;$i<$num;$i++)
   {
   
  	 $response4=$response4.urlencode(mysql_result($result,$i,"expressno"))."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"phonenumber"))."pxp";	
	 $huohao=urlencode(mysql_result($result,$i,"huohao"));
	  
     //货号增加柜号
     $pdasn=mysql_result($result,$i,"pdasn");
	 $pdasn=str_pad($pdasn,12,"0",STR_PAD_LEFT); //对于不足12位的前边补零
	 $result1 = mysql_query("SELECT  stationmark  FROM  smtbx_info  where  devicesn='$pdasn'",$db2);  
     $num1= mysql_numrows($result1);
	 if($num1!=0)
	 {
		$huohao="box：".mysql_result($result1,0,"stationmark")."-".$huohao;
		$huohao=urlencode($huohao);
	 }
	 $response4=$response4.$huohao."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"distributetime"))."pxp";		 
	 $response4=$response4.urlencode(mysql_result($result,$i,"distributeuser"))."pxp";
	 
	 $response4=$response4.urlencode(mysql_result($result,$i,"distributeway"))."pxp";		 
	 $response4=$response4.urlencode(mysql_result($result,$i,"smstatus"))."pxp";	 
	 	 	  	
   } 
    
	 $response=$response4; 
   
 	 //在logistics_a表中找
			//在logistics表中找	
		if(($xuanze1=="全部")&&($xuanze2=="全部"))
		{
		  $sqlstr="SELECT expressno,phonenumber,huohao,distributetime,distributeuser,smstatus,distributeway,pdasn  FROM  logistics_a  where  stationaccount='$stationaccount' and distributetime>'$datetime0'  and  distributetime<'$datetime1'   order by distributetime";
		}
		else if(($xuanze1=="全部")&&(($xuanze2=="成功")||($xuanze2=="失败")||($xuanze2=="已发")))
		{
		   $sqlstr="SELECT expressno,phonenumber,huohao,distributetime,distributeuser,smstatus,distributeway,pdasn  FROM  logistics_a  where  stationaccount='$stationaccount' and distributetime>$datetime0  and  distributetime<$datetime1  and  smstatus='$x2'  order by distributetime";
		}
		else if((($xuanze1=="货架")||($xuanze1=="智能柜"))&&($xuanze2=="全部"))
		{
		   $sqlstr="SELECT expressno,phonenumber,huohao,distributetime,distributeuser,smstatus,distributeway,pdasn  FROM  logistics_a  where  stationaccount='$stationaccount' and distributetime>$datetime0  and  distributetime<$datetime1 and  distributeway='$x1'  order by distributetime";		  
		}
        else if((($xuanze1=="货架")||($xuanze1=="智能柜"))&&(($xuanze2=="成功")||($xuanze2=="失败")||($xuanze2=="已发")))
		{
		   $sqlstr="SELECT expressno,phonenumber,huohao,distributetime,distributeuser,smstatus,distributeway,pdasn  FROM  logistics_a  where  stationaccount='$stationaccount' and distributetime>$datetime0  and  distributetime<$datetime1  and  smstatus='$x2'  and  distributeway='$x1'  order by distributetime";		
		
		}			
		$result = mysql_query($sqlstr,$db4);    		
        $num= mysql_numrows($result);
		
        $response4="";
   for($i=0;$i<$num;$i++)
   {
   
  	 $response4=$response4.urlencode(mysql_result($result,$i,"expressno"))."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"phonenumber"))."pxp";	
	 $huohao=urlencode(mysql_result($result,$i,"huohao"));
	  
     //货号增加柜号
     $pdasn=mysql_result($result,$i,"pdasn");
	 $pdasn=str_pad($pdasn,12,"0",STR_PAD_LEFT); //对于不足12位的前边补零
	 $result1 = mysql_query("SELECT  stationmark  FROM  smtbx_info  where  devicesn='$pdasn'",$db2);  
     $num1= mysql_numrows($result1);
	 if($num1!=0)
	 {
	    $huohao="box：".mysql_result($result1,0,"stationmark")."-".$huohao;
		$huohao=urlencode($huohao);
		
	 }
	 $response4=$response4.$huohao."pxp";
	 $response4=$response4.urlencode(mysql_result($result,$i,"distributetime"))."pxp";		 
	 $response4=$response4.urlencode(mysql_result($result,$i,"distributeuser"))."pxp";
	 
	 $response4=$response4.urlencode(mysql_result($result,$i,"distributeway"))."pxp";		 
	 $response4=$response4.urlencode(mysql_result($result,$i,"smstatus"))."pxp";	 
	 	 	  	
   } 

    $response=$response.$response4."0"."pxp";
		
	return  $response;
			
 }
 //-----------------------------------------------------------------------------
	function jijian($username,$stationaccount,$jijianstr)
	{	
	    $db4=conn4();
        $yundan=split(",",$jijianstr);
		$len=floor(count($yundan)/9);   //8为pda运单表的有效字段数
		for($i=0;$i<$len;$i++)
		{  
		   $expressno=$yundan[$i*9+0];
		   $expresscode=$yundan[$i*9+1];
		   $weight=$yundan[$i*9+2];   
		   $money=$yundan[$i*9+3];
		   $jijianuser=$yundan[$i*9+4];
		   $jijiantime=$yundan[$i*9+5];
		   $dakehucode=$yundan[$i*9+6];
		   $phonenumber=$yundan[$i*9+7];		   
		   $payway=$yundan[$i*9+8];
		  
		   //判断该运单是否存在
		        $sqlstr="delete from  jijianyundan  where  stationaccount='$stationaccount'  and  expressno='$expressno'";	
                mysql_query($sqlstr,$db4); 
		        $sqlstr="INSERT INTO `jijianyundan`(stationaccount,expressno,expresscode,weight,money,jijianuser,jijiantime,dakehuname,phonenumber,username,payway,dakehucode) VALUES ('$stationaccount','$expressno','$expresscode','$weight','$money','$jijianuser','$jijiantime','$dakehuname','$phonenumber','$username','$payway','$dakehucode')";	
                mysql_query($sqlstr,$db4);  
				
								
		  //关联面单，将同一个面单的状态改变为已寄件	
		 mysql_query("UPDATE `expressbill` SET  `state` ='1'  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);   //state, 0未使用，1已使用，2作废	
								
		  
		}		
		 $response="ok";
		return  $response;			
	} 
 
//----------------寄件统计---------------------------------

function  jijiantongji($username,$stationaccount,$jijiantongjistr)
{
   $db=conn();
   $db4=conn4();
   $db2=conn2();
  
   $yundan=split(",",$jijiantongjistr);
   $kaidiid=$yundan[0];
   $dakehuname=$yundan[1];
    
     $time=time();
   	//$time=mktime();
	$y=date("Y",$time);
	$m=date("m",$time);
	$d=date("d",$time);
	$time0=mktime(0,0,0,$m,$d,$y);  //今天的基准时间 0点0分0秒 
	$day0=$m."-".$d;
		
   
    $time1=mktime(0,0,0,$m,1,$y);  //本月的1月1日 0点0分0秒
	$month1=$m; //本月的月号

  	$y=date("Y",$time1);
	$m=date("m",$time1);
	$d=date("d",$time1);
	
	$time=$time1-3600*24*10;
	 
  	$y=date("Y",$time);
	$m=date("m",$time);
	$d=date("d",$time);
		
	$time2=mktime(0,0,0,$m,1,$y);  //上月的1月1日 0点0分0秒
    $month2=$m;  //上月的月号
  	
    if($dakehuname=="全部")
	{
	   $dakehustr="";
	}
	else
	{
	   $dakehustr="and  dakehuname='$dakehuname'";	
	}
 
    if($kaidiid==0)
	{
	   $kuaidistr="";
	}
	else
	{
	   $kuaidistr="and  expresscode='$kaidiid'";	
	} 
 

     //上月月数据
   $response="";
     $result = mysql_query("SELECT money FROM  jijianyundan  where  stationaccount='$stationaccount'  and  jijiantime>'$time2' and  jijiantime<'$time1'  $dakehustr  $kuaidistr",$db4);	
   
     $num= mysql_numrows ($result);
	 $money=0;
	 for($i=0;$i<$num;$i++)
	 {
	    $money+=mysql_result($result,$i,"money"); 
	 }   
   // $response=$response.$month2."pxp".$num."pxp".$money."pxp";
	$response10=$month2."pxp".$num."pxp".$money."pxp";

   //本月数据
  // $response="";
  
   $result = mysql_query("SELECT money FROM  jijianyundan  where  stationaccount='$stationaccount'  and  jijiantime>'$time1'  $dakehustr  $kuaidistr",$db4);	
  
     $num= mysql_numrows($result);
	  	 
	 $money=0;
	 for($i=0;$i<$num;$i++)
	 {
	    $money+=mysql_result($result,$i,"money"); 
	 }   
   // $response=$response.$month1."pxp".$num."pxp".$money."pxp";
    $response11=$month1."pxp".$num."pxp".$money."pxp";
	
    //当天数据
	$result = mysql_query("SELECT money FROM  jijianyundan  where  stationaccount='$stationaccount'  and  jijiantime>'$time0'  $dakehustr  $kuaidistr",$db4);	
	
	
     $num= mysql_numrows($result); 
	 $money=0;
	 for($i=0;$i<$num;$i++)
	 {
	    $money+=mysql_result($result,$i,"money"); 
	 }   
    //$response=$response.$day0."pxp".$num."pxp".$money."pxp";
	$response12=$day0."pxp".$num."pxp".$money."pxp";
	
   //当天寄件清单    
      $response1="";
	   $result = mysql_query("SELECT expressno,expresscode,weight,money,jijianuser,jijiantime,dakehuname,phonenumber  FROM  jijianyundan  where  stationaccount='$stationaccount'  and  jijiantime>'$time0'   $dakehustr  $kuaidistr",$db4);	

     $num= mysql_numrows($result);
		 
	 for($i=0;$i<$num;$i++)
	 {
	      $expressno=urlencode(mysql_result($result,$i,"expressno"));
		  $expresscode=urlencode(mysql_result($result,$i,"expresscode"));
		  $weight=urlencode(mysql_result($result,$i,"weight"));
		  $money=urlencode(mysql_result($result,$i,"money"));
		  $jijianuser=urlencode(mysql_result($result,$i,"jijianuser"));
		  $jijiantime=urlencode(mysql_result($result,$i,"jijiantime"));
		  $dakehuname=urlencode(mysql_result($result,$i,"dakehuname"));
		  $phonenumber=urlencode(mysql_result($result,$i,"phonenumber"));
	  
	     $response1=$response1.$expressno."pxp".$expresscode."pxp".$weight."pxp".$money."pxp".$jijianuser."pxp".$jijiantime."pxp".$dakehuname."pxp".$phonenumber."pxp";
	 }   
   
    //--------------------------面单的统计---------------------------------------
        //上月月数据
   $response2="";
     $result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'  and  createtime>'$time2' and  createtime<'$time1'   $kuaidistr",$db4);	  
     $num= mysql_numrows ($result);
     $result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'  and  createtime>'$time2' and  createtime<'$time1' and  state='2'  $kuaidistr",$db4);	  
     $num1= mysql_numrows ($result);	 
	 
	
    //$response2=$response2.$month2."pxp".$num."pxp".$num1."pxp";
	$response20=$num."pxp".$num1."pxp";

   //本月数据
  // $response="";
  
   $result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'  and  createtime>'$time1'   $kuaidistr",$db4); 
   $num= mysql_numrows($result);	  	  
   $result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'  and  createtime>'$time1'  and  state='2'   $kuaidistr",$db4); 
   $num1= mysql_numrows($result);	     
  // $response2=$response2.$month1."pxp".$num."pxp".$num1."pxp";
   $response21=$num."pxp".$num1."pxp"; 
	
    //当天数据
	
	$result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'  and  createtime>'$time0'   $kuaidistr",$db4);			
    $num= mysql_numrows($result); 
    $result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'  and  createtime>'$time0'  and  state='2'   $kuaidistr",$db4);			
    $num1= mysql_numrows($result);	
   // $response2=$response2.$day0."pxp".$num."pxp".$num1."pxp";
	$response22=$num."pxp".$num1."pxp";
	
   //全部累计面单数据    
      $response3="";
	  
	$result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'    $kuaidistr",$db4);			
    $num= mysql_numrows($result);
    $result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'  and state='1'   $kuaidistr",$db4);			
    $num1= mysql_numrows($result);	 
    $result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'  and  state='2'   $kuaidistr",$db4);			
    $num2= mysql_numrows($result);		
    $response3=$num."pxp".$num1."pxp".$num2."pxp";
		 
   
	//--------------------------------------------------
    $response=$response10.$response20.$response11.$response21.$response12.$response22."uxu".$response1."uxu".$response3."0"."pxp";	
   
   return  $response;
   
}   
 

//-------------寄件查询-----------------------------------
//运单跟踪也考虑到订单重复的问题	
	function   jijianchaxun($username,$stationaccount,$jijianchaxunstr)
	{	
	    $db4=conn4();
		$db2=conn2();
      //  $expressno=$jijianchaxunstr;
		
		$yundan=split(",",$jijianchaxunstr);
		$expressno=$yundan[0];
		$type=$yundan[1];
		
		
		 //获取该订单内容
		 if($type==1)
		 {
		   	  $result = mysql_query("SELECT * FROM  jijianyundan  where   expressno='$expressno'",$db4); 
		 }
		 else if($type==2)  
		 {
		      $result = mysql_query("SELECT * FROM  jijianyundan  where   phonenumber='$expressno'",$db4);
		 }
		 else
		 {
		      $response=$response."0"."pxp";
		      return  $response;			
		 }		 

		$num= mysql_numrows ($result);				
		$response="";		
		//if($num!=0)
		for($i=0;$i<$num;$i++)
		{	
		  $expressno=urlencode(mysql_result($result,$i,"expressno"));
		  $expresscode=urlencode(mysql_result($result,$i,"expresscode"));
		  $weight=urlencode(mysql_result($result,$i,"weight"));
		  $money=urlencode(mysql_result($result,$i,"money"));
		  $jijianuser=urlencode(mysql_result($result,$i,"jijianuser"));
		  $jijiantime=urlencode(mysql_result($result,$i,"jijiantime"));
		  $dakehuname=urlencode(mysql_result($result,$i,"dakehuname"));
		  $stationaccount=urlencode(mysql_result($result,$i,"stationaccount"));
		  $phonenumber=urlencode(mysql_result($result,$i,"phonenumber"));
		   	   
	          //获得站点的地址
		   $stationname="未知";
	       $stationaccount=urlencode(mysql_result($result,$i,"stationaccount"));
		   $result1 = mysql_query("SELECT  stationname  FROM  stations_manage  where  account='$stationaccount'",$db4);  
     	   $num1= mysql_numrows($result1);
	 	   if($num1!=0)
	 	   {
	    	  $stationname=urlencode(mysql_result($result1,0,"stationname"));
	 	   }
	
			$response=$response.$expressno."pxp".$expresscode."pxp".$weight."pxp".$money."pxp".$jijianuser."pxp".$jijiantime."pxp".$dakehuname."pxp".$stationname."pxp".$phonenumber."pxp";		   
		
		}
        $response=$response."0"."pxp";

		return  $response;			
	}

 //--------------面单----------------------------------	
	function  expressbill($username,$stationaccount,$pdasn,$miandanstr)
	{	
	    $db4=conn4();
        $yundan=split("pxp",$miandanstr);
		$len=count($yundan)/4-1;   //6为pda绑定表的有效字段数
		for($i=0;$i<$len;$i++)
		{
		   $expressno=$yundan[$i*4+0];
		   $expresscode=$yundan[$i*4+1];
		   $direction=$yundan[$i*4+2];
		   $time=$yundan[$i*4+3];
		  
		   		 	   
		   //判断该运单是否存在
		   $result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		   $num= mysql_numrows ($result);
		   if($num==0)
		   { 	          
		       $sqlstr="INSERT INTO `expressbill` ( `pdasn`,`stationaccount`,`expressno`,`username`,`directiontime`,`direction`,`expresscode`,`state`,`createtime`,`endtime`) VALUES ('$pdasn','$stationaccount', '$expressno','$username', '0','$direction','$expresscode','0','$time','0')";							  				
				   mysql_query($sqlstr,$db4); 		   		 	
		   }
		   else
		   {
		        $id=mysql_result($result,0,"id");
				if($direction=="站点使用")
				{
				    $str="`createtime`='$time',`direction`='$direction',`state`='0'";
					$sqlstr="delete from  jijianyundan  where  stationaccount='$stationaccount'  and  expressno='$expressno'";	//删除原来已经寄件的单
					mysql_query($sqlstr,$db4);
				}
				else  if($direction=="面单作废")
				{
				   $str="`state`='2'";
				   
				   $sqlstr="delete from  jijianyundan  where  stationaccount='$stationaccount'  and  expressno='$expressno'";	//删除原来已经寄件的单子
                   mysql_query($sqlstr,$db4);  		   
				} 
				else
				{
				    $str="`directiontime`='$time',`direction`='$direction'";	
					$sqlstr="delete from  jijianyundan  where  stationaccount='$stationaccount'  and  expressno='$expressno'";	//删除原来已经寄件的单
					mysql_query($sqlstr,$db4);					
				}	

		        $sqlstr="UPDATE `expressbill` SET  `expresscode` = '$expresscode', $str  WHERE `id` ='$id' LIMIT 1";								  							              
				 mysql_query($sqlstr,$db4);  			   
		   } 	
		}
		
		 $response="ok";
		return  $response;			
	}
		
 //--------------面单请求----------------------------------	
	function  miandanqingqiu($username,$stationaccount,$pdasn,$miandanstr)
	{	
	    $db4=conn4();
        $yundan=split("pxp",$miandanstr);
		$len=count($yundan)/1-1;   //1为pda绑定表的有效字段数
		$expressno=$yundan[$i*1+0];
		
		$result = mysql_query("SELECT * FROM  expressbill  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		$num= mysql_numrows ($result);
						
		if($num>0)
		{
		     $expressno=urlencode(mysql_result($result,0,"expressno"));    
		     $directiontime=urlencode(mysql_result($result,0,"directiontime")); 
			 $direction=urlencode(mysql_result($result,0,"direction")); 
			 $expresscode=urlencode(mysql_result($result,0,"expresscode")); 
			 $state=urlencode(mysql_result($result,0,"state")); 	
			 $createtime=urlencode(mysql_result($result,0,"createtime")); 
			 $endtime=urlencode(mysql_result($result,0,"endtime")); 			 
			 $response=$expressno."pxp".$directiontime."pxp".$direction."pxp".$expresscode."pxp".$state."pxp".$createtime."pxp".$endtime."pxp";
		}
		else
		{
		   $response="";
		}


        
		return  $response;			
	}
	
	
 //--------------批量面单----------------------------------	
	function  piliangmiandan($username,$stationaccount,$pdasn,$piliangmiandanstr)
	{	
	    $db4=conn4();
        $yundan=split("pxp",$piliangmiandanstr);
		$len=count($yundan)/4-1;   //4为pda绑定表的有效字段数
		if($len>0)
		{
		   $expressno_first=$yundan[$i*4+0];
		   $expressno_n=$yundan[$i*4+1];
		   $expresscode=$yundan[$i*4+2];
		   $time=$yundan[$i*4+3];
		   //截取后五位
		   $strlength=strlen($expressno_first);
		   $str1=substr($expressno_first,0, $strlength-5);
		   $str2=substr($expressno_first,$strlength-5,5);
		    
		   for($i=0;$i<$expressno_n;$i++)
		   {
		      $expressno=$str1.($str2+$i);
		      //判断该运单是否存在
		      $result = mysql_query("SELECT id FROM  expressbill  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		      $num= mysql_numrows ($result);
			  if($num==0)
		      { 	          
		         $sqlstr="INSERT INTO `expressbill` ( `pdasn`,`stationaccount`,`expressno`,`username`,`directiontime`,`direction`,`expresscode`,`state`,`createtime`,`endtime`) VALUES ('$pdasn','$stationaccount', '$expressno','$username', '0','','$expresscode','0','$time','0')";							  				
				  mysql_query($sqlstr,$db4); 		   		 	
		      }
		   }
	   		 	   	
		}	
		 $response="ok";
		return  $response;			
	}
			
	//超期提醒
	function  chaoqitixing($username,$stationaccount,$pdasn,$chaoqitixingstr)
	{	
	    $db=conn();
        $yundan=split("pxp",$chaoqitixingstr);
		$len=count($yundan)/2-1;   //2为pda绑定表的有效字段数
		
		 $time=time();
  	     $y=date("Y",$time);
  	     $m=date("m",$time);
  	     $d=date("d",$time);
		 $h=date("H",$time);  //小时
  	     $t0=mktime(0,0,0,$m,$d,$y);  //今天凌晨
		   
		if($len>0)
		{
		   $expresscode=$yundan[$i*2+0];  //超期代码  1111为全部快递公司
		   $chaoqiday=$yundan[$i*2+1];   //超期的天数， 1,2,3
		   
		   //时间选择
		   if($chaoqiday==1)
		   {
		       $t1=$t0;
			   $t2=$t1+24*3600;   			     
		   }
		   else if($chaoqiday==2)
		   {
		       $t1=$t0-24*3600; 
			   $t2=$t1+24*3600; 	   		   
		   }
		   else if($chaoqiday==3)
		   {
		       if($h<17)
			   {
			     $t1=$t0-24*3600*2; 
			     $t2=$t1+24*3600; 
			   }
			   else
			   {
			     $t1=$t0-24*3600*2; 
			     $t2=$t1; 
			   }	     		   		   
		   }		   
		   else if($chaoqiday==4)
		   {
		       if($h<17)
			   {		       
		          $t1=$t0-24*3600*3; 
			      $t2=$t1+24*3600;
				  $t1=$t2-24*3600*10; 
				  
				  
			   } 
			   else
			   {
		          $t1=$t0-24*3600*2; 
			      $t2=$t1+24*3600;
				  $t1=$t2-24*3600*10; 				  
				  			   
			   }		   		   
		   }		   		   
		   
		   //快递公司选择
		   if($expresscode=="1111")
		   {
		      $express_str="";  
		   }
		   else
		   {
		      $express_str="and expressname='$expresscode'";
		   }
		   		   		   		   
		   $sqlstr="SELECT expressno,huohao,diandantime,expressname,pdasn  FROM  dyhawk.logistics  where  stationaccount='$stationaccount' and  diandantime>'$t1' and  diandantime<'$t2'    $express_str  order by diandantime  desc";
		   $result = mysql_query($sqlstr,$db); 
		   $num= mysql_numrows ($result);
		   $str=array(); 
		   for($i=0;$i<$num;$i++)
   			{  
  	 			$str[$i*4+0]=urlencode(mysql_result($result,$i,"expressno"))."pxp";
				$huohao=mysql_result($result,$i,"huohao");
				//货号增加柜号
     			$pdasn=mysql_result($result,$i,"pdasn");
	 			$pdasn=str_pad($pdasn,12,"0",STR_PAD_LEFT); //对于不足12位的前边补零
	 			$result1 = mysql_query("SELECT  stationmark  FROM  smartbox.smtbx_info  where  devicesn='$pdasn'",$db);  
     			$num1= mysql_numrows($result1);
	 			if($num1!=0)
	 			{
	    			$huohao="z".mysql_result($result1,0,"stationmark")."-".$huohao;
	 			}	
	 			$str[$i*4+1]=urlencode($huohao)."pxp";					
  	 			$str[$i*4+2]=urlencode(mysql_result($result,$i,"diandantime"))."pxp";
	 			$str[$i*4+3]=urlencode(mysql_result($result,$i,"expressname"))."pxp";	  	 	  	
   			}
			
             $response=implode('',$str)."uxu".$num."pxp";   
	  	
	   }	
		
		return  $response;			
	}
			

//----------pda投柜拍照状态上传----------------------------------	
	function  pdapicture($username,$stationaccount,$pdasn,$picturestr)
	{	
	    $db4=conn4();
        $yundan=split("pxp",$picturestr);
		$len=count($yundan)/2-1;   //6为pda绑定表的有效字段数
		for($i=0;$i<$len;$i++)
		{
		   $expressno=$yundan[$i*2+0];	  
		   $pictime=$yundan[$i*2+1];
		   $picstatus=1;
		   //判断该运单是否存在
		   $result = mysql_query("SELECT id FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		   $num= mysql_numrows ($result);
		   if($num==0)
		   { 		  
		           //在插入状态，将,'$qiandantime'也同时插入diandantime
		           $sqlstr="INSERT INTO `logistics` ( `pdasn`,`stationaccount`,`expressno`, `diandantime`,`diandanuser`,`distributetime`,`distributeuser`,`picstatus`) VALUES ('$pdasn','$stationaccount', '$expressno','$pictime','$username','$pictime','$username','$picstatus')";							  				
				   mysql_query($sqlstr,$db4); 			  	 	
		   }
		   else
		   {
		        $id=mysql_result($result,0,"id");
				
		        $sqlstr="UPDATE `logistics` SET `picstatus` = '$picstatus'  WHERE `id` ='$id' LIMIT 1";								  							              
				 mysql_query($sqlstr,$db4);  			   
		   } 	
		}
				
		 $response="ok";
		return  $response;			
	}

//----------签名图片地址上传----------------------------------	
	function  pdapicture($username,$stationaccount,$pdasn,$picturestr)
	{	
	    $db4=conn4();
        $yundan=split("pxp",$picturestr);
		$len=count($yundan)/2-1;   //6为pda绑定表的有效字段数
		for($i=0;$i<$len;$i++)
		{
		   $expressno=$yundan[$i*2+0];	  
		   $pictime=$yundan[$i*2+1];
		   $picstatus=1;
		   //判断该运单是否存在
		   $result = mysql_query("SELECT id FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		   $num= mysql_numrows ($result);
		   if($num==0)
		   { 		  
		           //在插入状态，将,'$qiandantime'也同时插入diandantime
		           $sqlstr="INSERT INTO `logistics` ( `pdasn`,`stationaccount`,`expressno`, `diandantime`,`diandanuser`,`distributetime`,`distributeuser`,`picstatus`) VALUES ('$pdasn','$stationaccount', '$expressno','$pictime','$username','$pictime','$username','$picstatus')";							  				
				   mysql_query($sqlstr,$db4); 			  	 	
		   }
		   else
		   {
		        $id=mysql_result($result,0,"id");
				
		        $sqlstr="UPDATE `logistics` SET `picstatus` = '$picstatus'  WHERE `id` ='$id' LIMIT 1";								  							              
				 mysql_query($sqlstr,$db4);  			   
		   } 	
		}
				
		 $response="ok";
		return  $response;			
	}

	//----------签名图片地址上传----------------------------------	
		function  uploadsignurl($username,$stationaccount,$pdasn,$uploadsignurl)
		{	
		    $db4=conn4();
	        $yundan=split("pxp",$uploadsignurl);
			$len=count($yundan)/3-1;   //6为pda绑定表的有效字段数
			for($i=0;$i<$len;$i++)
			{
			   $expressno=$yundan[$i*3+0];	  
			   $type=$yundan[$i*3+0];	  
			   $signurl=$yundan[$i*3+1];

			   $sign_name = $type.'_sign_url';

			   //判断该运单是否存在
			   $result = mysql_query("SELECT id FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
			   $num= mysql_numrows ($result);
			   if($num>0)
			   { 		  
			        $id=mysql_result($result,0,"id");
					
			        $sqlstr="UPDATE `logistics` SET `".$sign_name."` = '$signurl'  WHERE `id` ='$id' LIMIT 1";								  							              
					 mysql_query($sqlstr,$db4);  		  	 	
			   }
	
			}
					
			 $response="ok";
			return  $response;			
		}

//外派运单  lgl
	function  waipai($username,$stationaccount,$pdasn,$waipaistr)
	{	
	    $db4=conn4();
        $yundan=split("pxp",$waipaistr);
		$len=count($yundan)/6-1;   //5为pda绑定表的有效字段数
		for($i=0;$i<$len;$i++)
		{
		   $rcvnumber=$yundan[$i*6+0];  //收件人的电话号
		   $expressno=$yundan[$i*6+1];  //运单号
		   $kuaidicode=$yundan[$i*6+2];  //快递公司代码
		   $waipaitime=$yundan[$i*6+3];  //外派时间
		   $waipaiphonenumber=$yundan[$i*6+4];  //外派人的电话号码
		   $waipaiphonenumber=$username;  //目前为登录用户   
		   $dakehu=$yundan[$i*6+5];  //外派大客户	  和签单的paycontent一致	   
		   
		   //序列号产生
	 	   $m_date= date('YmdHis');
     	   $m_time = mb_substr(microtime(), 2,6); 
     	   $msm_sn=$m_date.$m_time;	   
		   		   	
			   //判断该运单是否存在
		   $result = mysql_query("SELECT id,phonenumber FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
		   $num= mysql_numrows ($result);
		   if($num==0)
		   {
		        $sqlstr="INSERT INTO `logistics` (`pdasn`, `stationaccount`,`expressno`,`expressname`,`phonenumber`,`waipaiuser`,`waipaitime`,`diandantime`,`diandanuser`,`msm_sn`,`smstatus`,`uplglflg`,`phase`,`paycontent`) 
                  VALUES ('$pdasn','$stationaccount', '$expressno', '$kuaidicode','$rcvnumber', '$waipaiphonenumber', '$waipaitime','$waipaitime', '$username', '$msm_sn','2','1','1','$dakehu')";							  								                mysql_query($sqlstr,$db4);  
 		
		   }
		   else
		   {
		        $id=mysql_result($result,0,"id");     		
		        $sqlstr="UPDATE `logistics` SET  `expressname` = '$kuaidicode',`phonenumber` = '$rcvnumber',`waipaiuser` = '$waipaiphonenumber',`waipaitime` = '$waipaitime',`msm_sn` = '$msm_sn',`smstatus` = '2',`phase` = '1' ,`uplglflg` =`uplglflg`+1 ,`paycontent` = '$dakehu' WHERE `id` ='$id' LIMIT 1";								  							              
				 mysql_query($sqlstr,$db4);  			   
		   } 
		   
		   //发短信
		   
		   	$result = mysql_query("SELECT  stationname FROM  stations_manage  where  account='$stationaccount'",$db4);  
	    	$stationname=mysql_result($result,0,"stationname");  //		
	 		//$expressno="***".substr($expressno,-4);		

		   $content="您的快件($expressno)已经到达本大楼零公里服务中心，小蜜蜂正在为您派送中，请您耐心等待。";
		   
		   
		   $devicesn=$pdasn;
		   if($rcvnumber!="")
		   {
		       //sendOneSMSonly($rcvnumber,$content,$username,$devicesn,$msm_sn,$db4);
		   }	   
		   	   	
		}
		
		 $response="ok";//"ok";
		return  $response;		
	}
	

	
//派件详单  lgl
	function  paijianxiangdan($username,$stationaccount,$pdasn,$paijianxiangdanstr)
	{	
	    $db4=conn4();
        $yundan=split("pxp",$paijianxiangdanstr);
		$len=count($yundan)/4-1;   //1为pda绑定表的有效字段数
		$daytime=$yundan[$i*0+0];  //日期
		$sortby=$yundan[$i*0+1];  //时间的依据，  0 点单时间，1外派时间， 2签单时间,3到件未派送，4派送未签收
		$allstation=$yundan[$i*0+2];  //整站数据还是个人  0个人 1整站
		$expresscode=$yundan[$i*0+3]; 
		
		$dayt0=$daytime;
		$dayt1=$dayt0+3600*24;
		
		$bytime=""; 

		if($sortby==0)
		{
		   $bytime="and diandantime>=$dayt0  and  diandantime<$dayt1";
		   $byuser="and diandanuser='$username'";
		}
		else if($sortby==1)
		{
		   $bytime="and waipaitime>=$dayt0 and waipaitime<$dayt1";
		   $byuser="and waipaiuser='$username'";
		}
		else if($sortby==2)
		{
		   $bytime="and signingtime>=$dayt0  and  signingtime<$dayt1";
		   $byuser="and signinguser='$username'";
		}
		else if($sortby==3)
		{
		   $bytime="and diandantime>=$dayt0  and  diandantime<$dayt1 and  waipaitime=0 and signingtime=0";
		   $byuser="and diandanuser='$username'";
		}		
		else if($sortby==4)
		{
		   $bytime="and waipaitime>=$dayt0 and waipaitime<$dayt1  and signingtime=0";
		   $byuser="and waipaiuser='$username'";		
		}				
	

        if($allstation==1)
	    {
	       $byuser="";  
	    }
	  
       if($expresscode=="0000")
	    {
	       $expressstr="";  
	    }
		else
		{
		   $expressstr="and  expressname='$expresscode'"; 
		}	     
	  
	  

   //logistics
    $result = mysql_query("SELECT * FROM  logistics  where  stationaccount='$stationaccount'  $bytime  $byuser  $expressstr",$db4); // 
    $num= mysql_numrows($result);
	$response="";
	$str=array();
   for($i=0;$i<$num;$i++)
   {  
  	 $str[$i*12+0]=urlencode(mysql_result($result,$i,"expressname"))."pxp";
	 $str[$i*12+1]=urlencode(mysql_result($result,$i,"expressno"))."pxp";	
  	 $str[$i*12+2]=urlencode(mysql_result($result,$i,"daofuprice"))."pxp";
	 $str[$i*12+3]=urlencode(mysql_result($result,$i,"payway"))."pxp";	 
  	 $str[$i*12+4]=urlencode(mysql_result($result,$i,"phonenumber"))."pxp";
	 $str[$i*12+5]=urlencode(mysql_result($result,$i,"diandantime"))."pxp";	
	 $str[$i*12+6]=urlencode(mysql_result($result,$i,"waipaitime"))."pxp";		 
  	 $str[$i*12+7]=changeMobiletoName(mysql_result($result,$i,"waipaiuser"))."pxp";	 
  	 $str[$i*12+8]=urlencode(mysql_result($result,$i,"signingtime"))."pxp";
	 $str[$i*12+9]=urlencode(mysql_result($result,$i,"signingkind"))."pxp";	
	 $str[$i*12+10]=urlencode(mysql_result($result,$i,"smstatus"))."pxp";
	 $str[$i*12+11]=changeMobiletoName(mysql_result($result,$i,"signinguser"))."pxp";	 		 	 	  	 	  	
   }
    $response=$response.implode('',$str);
		
		
   //logistics_a
    $result = mysql_query("SELECT * FROM  logistics_a  where  stationaccount='$stationaccount'  $bytime  $byuser   $expressstr",$db4); // 
    $num= mysql_numrows($result);
	$str=array();
   for($i=0;$i<$num;$i++)
   {  
  	 $str[$i*12+0]=urlencode(mysql_result($result,$i,"expressname"))."pxp";
	 $str[$i*12+1]=urlencode(mysql_result($result,$i,"expressno"))."pxp";	
  	 $str[$i*12+2]=urlencode(mysql_result($result,$i,"daofuprice"))."pxp";
	 $str[$i*12+3]=urlencode(mysql_result($result,$i,"payway"))."pxp";	 
  	 $str[$i*12+4]=urlencode(mysql_result($result,$i,"phonenumber"))."pxp";
	 $str[$i*12+5]=urlencode(mysql_result($result,$i,"diandantime"))."pxp";	
	 $str[$i*12+6]=urlencode(mysql_result($result,$i,"waipaitime"))."pxp";		 
	 $str[$i*12+7]=changeMobiletoName(mysql_result($result,$i,"waipaiuser"))."pxp";
  	 $str[$i*12+8]=urlencode(mysql_result($result,$i,"signingtime"))."pxp";
	 $str[$i*12+9]=urlencode(mysql_result($result,$i,"signingkind"))."pxp";	
	 $str[$i*12+10]=urlencode(mysql_result($result,$i,"smstatus"))."pxp";
	 $str[$i*12+11]=changeMobiletoName(mysql_result($result,$i,"signinguser"))."pxp";	 		 		 	 	  	 	  	
   }
    	$response=$response.implode('',$str)."0"."pxp";		
		
		return  $response;		
}
		
	
//寄件详单  lgl
	function  jijianxiangdan($username,$stationaccount,$pdasn,$jijianxiangdanstr)
	{	
	    $db4=conn4();
        $yundan=split("pxp",$jijianxiangdanstr);
		$len=count($yundan)/3-1;   //1为pda绑定表的有效字段数
		$daytime=$yundan[$i*0+0];  //日期
		$allstation=$yundan[$i*0+1];  //整站数据还是个人  0个人 1整站
		$expresscode=$yundan[$i*0+2]; 
		
		$dayt0=$daytime;
		$dayt1=$dayt0+3600*24;
				
	    $bytime="and  jijiantime>=$dayt0  and  jijiantime<$dayt1";
        $byuser="and  username='$username'";
		
		
		
       if($allstation==1)
	    {
	       $byuser="";  
	    }
	  
       if($expresscode=="0000")
	    {
	       $expressstr="";  
	    }
		else
		{
		   $expressstr="and  expresscode='$expresscode'"; 
		}	     

    $result = mysql_query("SELECT * FROM  jijianyundan  where  stationaccount='$stationaccount'   $bytime    $byuser  $expressstr",$db4); 
    $num= mysql_numrows($result);
	$response="";
	$str=array();
   for($i=0;$i<$num;$i++)
   {  
  	 $str[$i*9+0]=urlencode(mysql_result($result,$i,"expresscode"))."pxp";
	 $str[$i*9+1]=urlencode(mysql_result($result,$i,"expressno"))."pxp";	
  	 $str[$i*9+2]=urlencode(mysql_result($result,$i,"jijianuser"))."pxp";
	 $str[$i*9+3]=urlencode(mysql_result($result,$i,"jijiantime"))."pxp";	 
	 $str[$i*9+4]=urlencode(mysql_result($result,$i,"weight"))."pxp";	
	 $str[$i*9+5]=urlencode(mysql_result($result,$i,"money"))."pxp";		 
  	 $str[$i*9+6]=urlencode(mysql_result($result,$i,"payway"))."pxp";
  	 $str[$i*9+7]=urlencode(mysql_result($result,$i,"dakehucode"))."pxp";
  	 $str[$i*9+8]=changeMobiletoName(mysql_result($result,$i,"username"))."pxp";	  	  	 	  	
   }
    $response=$response.implode('',$str)."0"."pxp";

		return  $response;
			
}

// 新增退件流程接口
function tuijian($username,$stationaccount,$pdasn,$tuijianstr){
    $db4=conn4();
    $yundan=split("pxp",$tuijianstr);
	$len=count($yundan)/3-1;   //6为pda绑定表的有效字段数
	for($i=0;$i<$len;$i++)
	{
	   $expressno=$yundan[$i*3+0];	  
	   $kdy_tel=$yundan[$i*3+0];	  
	   $kdy_name=$yundan[$i*3+1];


	   //判断该运单是否存在
	   $result = mysql_query("SELECT id FROM  logistics  where  stationaccount='$stationaccount'  and  expressno='$expressno' limit 1",$db4);  
	   $num= mysql_numrows ($result);
	   if($num>0)
	   { 		  
	        $id=mysql_result($result,0,"id");
			
	        $sqlstr="UPDATE `logistics` SET `kdy_tel` = '$kdy_tel',`kdy_name` = '$kdy_name'  WHERE `id` ='$id' LIMIT 1";								  							              
			 mysql_query($sqlstr,$db4);  		  	 	
	   }

	}
			
	 $response="ok";
	return  $response;
}
		

	
//-------子函数----------------------------------
function  changeMobiletoName($mobile)
{
   $db4=conn4();
   $result = mysql_query("SELECT name FROM  deeyee.user  where  username='$mobile' ",$db4); //and $bytime  and  $byuser 
   $num= mysql_numrows($result); 
   $name="";  
   if($num!=0)
   {
       $name=mysql_result($result,0,"name");
   }
   if($name=="")
   {
      $name=$mobile;  
   }
   return $name;
}



	
?>