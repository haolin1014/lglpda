<?php

function  str_len($str1,$str2)
{  //将货号短语结合成字符串作为输入，最后输出短信的条数
  //utf-8编码
  //本函数适应于utf-8编码
  //货号的前后两个括号<取货号：>6个，+【递易智能】 6个  共12个字符。 所以短信货号+短语 <=58
  $str=$str1.$str2;
  $all_len=mb_strlen($str,"UTF-8");
  $end_len=$all_len+12+70-1;  //包含70
  $num=floor($end_len/70);
  return  $num;
}
?>