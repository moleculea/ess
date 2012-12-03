<?php 
// page config view for indexation
?>
<h3><?php echo $title; ?></h3>
<?php 

if ($view == "page" ){
$form_attributes = array(
	'name' => 'pg_config_form',
	'class' => 'form', 
	'id' => 'pg_config',
	'onSubmit' => '',
);

echo form_open('indexation/pg_config', $form_attributes);
echo form_fieldset('Infobox');
echo "\n";
echo "<p>Select infoboxes referenced in the index \"$indexname\" <br/>(Press Ctrl to select multiple infoboxes to be generated)</p>";
$options = array();
foreach ($query_result as $row)
{
	$options[$row->ib_id] = $row->ib_name;
}
echo "<div id='ib_select_div' style='display:table-cell;'>";
$attr = 'size="8"';
echo form_multiselect('ib_select[]', $options, '',$attr);
echo "</div>";
$data = array(
    'name' => 'select',
    'id' => 'select',
    'value' => 'true',
	'content' => 'Select',
);
echo "<div id='ib_select_button' style='display:table-cell;vertical-align:middle;'>";
echo form_button($data);
echo "</div>";
echo "\n<div id='ib_info' style='display:table-cell;vertical-align:top'></div>";

// Send the indexname to the next page for MySQL import
echo form_hidden('indexname', $indexname);

$back_button = array(
			    'name' => 'pg_config_back',
			    'id' => 'button',
			    'type' => 'button',
			    'content' => 'Back',
			    'onClick' => 'window.history.back();',
);
echo form_button($back_button);
echo form_hidden('pg_config', 'submit');
echo form_submit('pg_config_submit', 'Next');

echo form_fieldset_close();
echo "<br/>";
}

if ($view =="start"){
	
$form_attributes = array(
	'name' => 'pg_config_form',
	'class' => 'form', 
	'id' => 'pg_config',
	'onSubmit' => '',
);

echo form_open('indexation/init', $form_attributes);
echo form_fieldset('Indexation');
echo "\n";
echo "Indexation configuration is complete. <br/>\n Verify the index information below and press start to start indexation.";
echo "<br/>";
echo "<p><b>Index name</b>: $indexname</p>";
echo "<p><b>DTPM</b>: </p>";
if ($xml){
	echo "<ul>";
	echo "Fieldname, Parameter, Type";
	foreach($xml->children() as $child)
	{
		echo "<li>";
		echo $child->getName();
		echo ", ";
		echo $child;
		echo ", ";
		echo $child->attributes();
		echo "; ";
		echo "</li>\n";
	}
	echo "</ul>";
}
echo "<p><b>Referenced infobox(es)</b>: </p>";
echo "<ul>";
if (!$iblist){
	echo '<span style="color:red">No referenced infobox selected. Please specifiy infobox(es) for this index.</span>';
}
else{
	foreach($ib_name as $ib)
	{
		echo "<li>";
		echo $ib;
		echo "</li>\n";
	}
}
echo "</ul>";
$back_button = array(
			    'name' => 'pg_config_back',
			    'id' => 'button',
			    'type' => 'button',
			    'content' => 'Back',
			    'onClick' => 'window.history.back();',
);
echo "<p><b>Total pages to be indexed</b>: </p>";
echo "<ul>";
if (!$iblist){
echo "<div id='pg_num'>0 Pages.</div>";
}
else
echo "<div id='pg_num'>$status</div>";
echo "</ul>";

echo form_hidden('indexname', $indexname);
echo form_hidden('pagelistfile', $pagelistfile);
echo form_button($back_button);
if ($iblist){
echo form_submit('pg_config_submit', 'Start');
}
echo form_fieldset_close();

}