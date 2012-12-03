<h3><?php echo $title; ?></h3>

<?php 
$form_attributes = array(
	'name' => 'pyw_init_form',
	'class' => 'form', 
	'id' => 'pyw_init_form',
);
echo validation_errors(); 
echo form_open('init/pyw_login', $form_attributes); 
#echo form_open('init', $form_attributes);
echo form_fieldset('Login information');
echo "\n";
// table contents

$mw_username_label = "<label>Username:</label>";
$mw_username = form_input('mw_username', 'Mitsuki Kojima');
$mw_psw_label = "<label>Password:</label>";
$mw_psw = form_password('mw_psw');

$pyw_submit = form_submit('pyw_init_form_submit', 'Next');

$back_button = array(
    'name' => 'pyw_back',
    'id' => 'button',
    'type' => 'button',
    'content' => 'Back',
    'onClick' => 'window.history.back();',
);
$pyw_back = form_button($back_button);
// set table

$this->table->add_row(array('data' => 'MediaWiki bot account', 'colspan' => 2, 'align' =>'center'));
$this->table->add_row(array($mw_username_label, $mw_username));
$this->table->add_row(array($mw_psw_label, $mw_psw));
$this->table->add_row(array('data' => $pyw_back,'align' => 'center'),array('data' => $pyw_submit,'align' => 'center'));
// generate table
echo $this->table->generate();

echo form_fieldset_close();
echo form_close()
?>

