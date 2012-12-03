<?php 
// list view
?>
<h3><?php echo $title; ?></h3>
<?php 
// If indexname is specified
if ($indexname!=""){
	echo "<h4>Listing index \"$indexname\"</h4>";
	if (!empty($result)){
		echo "<p class='result_number'>$total_num records.</p>";
	}
	echo $this->pagination->create_links();
	$pFile = $ep."index/".$indexname."/$indexname.xml";
	$esspath = realpath(".");
	$this->load->file($esspath."/application/dtpm/dtpm_parse.php");
	
	if (empty($result)){
		echo "<p>No search results.</p>";
	}
	else{
		dtmp_load($pFile,$result,$ess,$hostname,$indexname,$group);
	}
	echo $this->pagination->create_links();
}

else {
	echo "<h4>Choose an index to list:</h4>";
	echo "<ul>\n";
	foreach($result as $index){
		echo "<li><a href='listing/index/$index->in_name' >".$index->in_name."</a></li>";
	}
	echo "</ul>\n";
}