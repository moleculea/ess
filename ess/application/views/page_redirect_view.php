<html>
<head>
<script type="text/javascript">
//countdown
function run(){
	var s = document.getElementById("count");
	if(s.innerHTML == 0){
		window.location.replace('<?php echo $url?>')
		return false;
	}
	s.innerHTML = s.innerHTML * 1 - 1;
}
window.setInterval("run();",1000);
</script>
</head>
<body>
<?php 
echo $message; 
echo "<br/>";
?>
If this page does not redirect in <span id="count">3</span> seconds, 
<?php echo anchor($url,"click here.")?>


</body>
</html>