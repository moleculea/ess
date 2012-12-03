<?php 
// infobox config view for indexation
?>
<h3><?php echo $title; ?></h3>
<?php 
$form_attributes = array(
	'name' => 'ib_config_form',
	'class' => 'form', 
	'id' => 'ib_config',
	'onSubmit' => '',
);
#echo "<p>Normally, MediaWiki pages under a certain category share the same infobox and each page may include only one infobox.\n";
#echo "However, exception exists when pages under the same category may include different infoboxes.</p>\n";
/*
echo "<p>The Encyclopedic Search System does not identify a page's infobox name when retrieving the content. It only parse the fieldnames and values which specified on this step. 
This indicates that specifying infobox is not compulsory but just an annotation for the specified fieldnames</p>";
*/
echo form_open('indexation/pg_config', $form_attributes);
echo form_fieldset('Infobox');
echo "\n";
echo "<p>Check parameters in the infobox description page (Press Ctrl to select multiple infoboxes to be generated)</p>";
$options = array();
foreach ($query_result as $row)
{
	$options[$row->ib_id] = $row->ib_name;
}
echo "<div id='ib_select_div' style='display:table-cell;'>";
$attr = 'size="8"';
echo form_multiselect('ib_select', $options, '',$attr);
echo "</div>";
$data = array(
    'name' => 'check',
    'id' => 'check',
    'value' => 'true',
	'content' => 'Check',
);
echo "<div id='ib_select_button' style='display:table-cell;vertical-align:middle;'>";
echo form_button($data);
echo "</div>";
echo "\n<div id='ib_info' style='display:table-cell;vertical-align:top'></div>";
echo form_fieldset_close();
echo "<br/>";
echo form_fieldset('DTPM configuration');
$index_name = array('name' => 'index_name', 'id' => 'index_name' ,'value' => 'infobox', 'size' => 32);
echo "<ul><li>Specify the name for this index:</li></ul>\n";
echo form_label('Index name', 'index_name');
echo form_input($index_name);
echo "<ul><li>Specify DTPM mapping:</li></ul>\n";
$type_options = array(
	'0' => '---',
	'1' => 'NAME',
	'2' => 'DATE',
	'3' => 'WTEXT',
	'4' => 'NUM',
	'5' => 'UNIT',
	'6' => 'CMT',
	'7' => 'IMG',
);
$type_select = array();
$fieldname = array();
for($i = 1; $i <= 5; $i++){
	$parameter[$i] = form_input(array('name' => 'parameter_'.$i, 'id' => 'parameter_'.$i ,'value' => '', 'size' => 24));
	$type_select[$i] = form_dropdown('type_select_'.$i, $type_options, '');
	$fieldname[$i] = form_input(array('name' => 'fieldname_'.$i, 'id' => 'fieldname_'.$i ,'value' => '', 'size' => 24));
	$optional[$i] = form_input(array('name' => 'optional_'.$i, 'id' => 'optional_'.$i ,'value' => '', 'size' => 12, 'disabled' => 'disabled'));
}

$this->table->set_heading('Parameter','Fieldname', 'Type', 'Digit (NUM only)');
for($i = 1; $i <= 5; $i++){
	$type_select_cell = array ('data' => $type_select[$i], 'id' => 's_'.$i);
	$this->table->add_row($parameter[$i],$fieldname[$i],$type_select_cell,$optional[$i]);
}
$tmpl = array(
	'table_open' => '<table name="type_select_table" id="type_select_table" border="1">', 
);

$this->table->set_template($tmpl);
echo $this->table->generate();
$data = array(
    'name' => 'append',
    'id' => 'append',
    'value' => 'true',
	'content' => 'Append',
);
echo form_button($data);
$data = array(
    'name' => 'remove',
    'id' => 'remove',
    'value' => 'true',
	'content' => 'Remove',
);
echo form_button($data);
echo "<br/>";
$back_button = array(
			    'name' => 'index_config_back',
			    'id' => 'button',
			    'type' => 'button',
			    'content' => 'Back',
			    'onClick' => 'window.history.back();',
);
echo form_button($back_button);
echo form_hidden('ib_config', 'submit');
echo form_submit('index_config', 'Next');
echo form_fieldset_close();
echo form_close();


?>