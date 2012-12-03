<?php 
// config view for ib
?>
<h3><?php echo $title; ?></h3>

<?php 
$form_attributes = array(
	'name' => 'ib_init_form',
	'class' => 'form', 
	'id' => 'ib_init_form',

);
echo validation_errors(); 
echo form_open('init/ib_config', $form_attributes); 
echo form_fieldset('Infobox scope');
echo "\n";
// table contents
$ib_cat_label = "<label>Infobox category:</label>";
$ib_cat = form_input('ib_cat', '信息框模板');
$ib_prefix_label = "<label>Infobox prefix:</label>";
$ib_prefix = form_input('ib_prefix', 'infobox');

$ib_submit = form_submit('ib_form_sumbit', 'Next');

$back_button = array(
    'name' => 'ib_back',
    'id' => 'button',
    'type' => 'button',
    'content' => 'Back',
    'onClick' => 'window.history.back();',
);
$ib_back = form_button($back_button);
// set table
$this->table->add_row(array('data' => 'MediaWiki site', 'colspan' => 2, 'align' =>'center'));
$this->table->add_row(array($ib_cat_label, $ib_cat));
$this->table->add_row(array($ib_prefix_label, $ib_prefix));
$this->table->add_row(array('data' => $ib_back,'align' => 'center'),array('data' => $ib_submit,'align' => 'center'));

// generate table
echo $this->table->generate();

echo form_fieldset_close();
echo form_close()
?>
