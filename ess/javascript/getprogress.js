function getProgress()
{
var xmlhttp;
var str;

if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
	str = xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","/ess/init/ib_exec_pro",false);
xmlhttp.send();
return Number(str);
}