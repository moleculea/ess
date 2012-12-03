<?php 
// list view
?>
<h3><?php echo $title; ?></h3>
<?php 
if ($scenario == "config"){
	echo "<h4>Config \"$indexname\"</h4>";
	echo form_fieldset('Search');
	$form_attributes = array(
			'name' => 'search_config_form',
			'class' => 'form', 
			'id' => 'search_config_form',
			'onSubmit' => '',
	);
	
	echo form_open("/search/index/$indexname", $form_attributes);
	echo "<br/>";
	
	$esspath = realpath(".");
	$this->load->file($esspath."/application/dtpm/dtpm_search.php");
	dtpm_search_config($pFile,$ep,$indexname);
	
	echo "<br/>";
	$back_button = array(
					    'name' => 'index_config_back',
					    'id' => 'button',
					    'type' => 'button',
					    'content' => 'Back',
					    'onClick' => 'window.history.back();',
	);
	echo form_button($back_button);
	echo form_submit('search__config_submit', 'Next');
	echo form_fieldset_close();
	echo form_close();
}


if ($scenario == "search"){
	
	echo "<h4>Searching \"$indexname\"</h4>";
	echo form_fieldset('Search');
	$form_attributes = array(
		'name' => 'search_form',
		'class' => 'form', 
		'id' => 'search_form',
		'onSubmit' => '',
	);

	echo form_open("listing/proc/$indexname", $form_attributes);
	echo "<br/>";
	
	$esspath = realpath(".");
	$this->load->file($esspath."/application/dtpm/dtpm_search.php");
	dtpm_search($pFile,$ep,$indexname);
	
	echo "<br/>";
	
	$back_button = array(
				    'name' => 'index_config_back',
				    'id' => 'button',
				    'type' => 'button',
				    'content' => 'Back',
				    'onClick' => 'window.history.back();',
	);
	echo form_button($back_button);
	echo form_submit('search_submit', 'Search');
	
	echo form_fieldset_close();
	echo form_close();
}


if ($scenario == "empty"){
	
	echo "<h4>Choose an index to config and search:</h4>";
	echo "<ul>\n";
	foreach($result as $index){
		echo "<li><a href='search/index/$index->in_name' >".$index->in_name."</a></li>";
	}
	echo "</ul>\n";
	
}