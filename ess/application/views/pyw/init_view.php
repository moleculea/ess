<h3><?php echo $title; ?></h3>
<?php

$form_attributes = array(
	'name' => 'pyw_init_form',
	'class' => 'form', 
	'id' => 'pyw_init_form',
);

echo form_open('init/pyw_init', $form_attributes);
#echo form_open('init', $form_attributes);
echo form_fieldset('Database creation');
echo "\n";
echo "Pywikipedia login validation is complete.<br/>";
echo "By clicking \"Continue\", the default database (`ess_db`) will be created and we will enter the final step of initialization.<br/> 
Make sure you have already specified connection prerequisites at \"application/config/database.php\"";

$pyw_submit =  form_submit('pyw_init_form_submit', 'Continue');
$hidden =  form_hidden('pyw_init_form_hidden', 'Hidden');
$back_button = array(
    'name' => 'pyw_back',
    'id' => 'button',
    'type' => 'button',
    'content' => 'Back',
    'onClick' => 'window.history.back();',
);
$pyw_back = form_button($back_button);

echo "\n";
$this->table->add_row(array('data' => $pyw_back,'align' => 'center'),array('data' => $pyw_submit,'align' => 'center'));
echo $hidden;
// generate table
echo "\n";
echo $this->table->generate();
echo "\n";
echo form_fieldset_close();
echo form_close();