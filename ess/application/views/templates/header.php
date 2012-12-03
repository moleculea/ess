<?php echo doctype('html4-trans'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="utf-8" lang="utf-8" >
<head>
<?php 
echo meta('Content-type', 'text/html; charset=utf-8', 'equiv'); 
if (isset($meta)) // list extra meta lins if set
	echo $meta;
echo link_tag('css/main.css');
echo "\n";

if (isset($jquery)) {
	if ($jquery == TRUE)
		echo script_tag('javascript/jquery.min.js');
}
if (isset($script_tags)) { // list javascript tag lines if set
 		foreach ($script_tags as $script)
			echo $script;
}

if (isset($script_inline))
	print $script_inline."\n";


?>
<title><?php echo $title." - ESS"; ?></title>
<!-- ****************** ESS-VIEW Starts**********************-->
<style type="text/css">



</style>
<!-- ****************** ESS-VIEW Ends**********************-->
</head>
<body <?php if (isset($body_onload)) echo $body_onload;?>>

<div class="container">
  <div class="header">
 	<a href="/ess">
 	<?php echo img('images/icon/USTB-ESS_Logo.png');?>
 	<!--  
 	<img src="" alt="logo" name="Insert_logo" width="140" height="80" id="Insert_logo" style="background: #C6D580; display:inline;" />
 	-->
 	</a> 
	<div class="headline">
	<div class="login">
<?php 
$this->load->helper('cookie');
if ($this->input->cookie('username')){
	$username = $this->input->cookie('username');
	//echo anchor("login","Login",array('class' => 'login_link'));
	echo $username;
	echo "/";
	echo anchor("login/logout","Logout",array('class' => 'login_link'));;
}
else
	echo anchor("login","Login",array('class' => 'login_link'));	

//echo $this->input->cookie('username', TRUE);
//echo "cookie";
?>
</div>
</div><!-- end .header --></div>
  <div class="sidebar1" >
    <ul class="nav">
<?php 
$this->load->helper('url');
if ($this->input->cookie('group')=='1'){
	echo "<li>";
	echo anchor("init","Initialization",array('class' => 'navlink'));
	echo "</li>\n";
	
	echo "<li>";
	echo anchor("indexation","Indexation",array('class' => 'navlink'));
	echo "</li>\n";
}
echo "<li>";
echo anchor("listing","List",array('class' => 'navlink'));
echo "</li>\n";

echo "<li>";
echo anchor("search","Search",array('class' => 'navlink'));
echo "</li>\n";

if ($this->input->cookie('group')=='1'){

	echo "<li>";
	echo anchor("edit","Edit",array('class' => 'navlink'));
	echo "</li>\n";
}

?>

    </ul>
    <p><br/></p>
    <p>&nbsp;<br /><br /><br /></p>
    <!-- end .sidebar1 --></div>
    
<div class="content">
<div class="function_tags">
<?php 
if ($this->uri->segment(3)){
	$indexname = $this->uri->segment(3);
	if ($this->input->cookie('group')=='1'){
		echo anchor("edit/index/$indexname","Edit",array('class' => 'function_tag'));
		echo anchor("search/config/$indexname","Config",array('class' => 'function_tag'));
	}
	echo anchor("search/index/$indexname","Search",array('class' => 'function_tag'));
	echo anchor("listing/index/$indexname","List",array('class' => 'function_tag'));
}


?>
</div>