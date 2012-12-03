<?php 
// config view for pyw
?>
<h3><?php echo $title; ?></h3>

<?php 
$form_attributes = array(
	'name' => 'pyw_init_form',
	'class' => 'form', 
	'id' => 'pyw_init_form',
	'onSubmit' => 'return checkDomain(pyw_init_form.hostname.value)',
);
echo validation_errors(); 
echo form_open('init/pyw_config', $form_attributes); 
#echo form_open('init', $form_attributes);
echo form_fieldset('General information');
echo "\n";
// table contents
$hostname_label = "<label>Hostname:</label>";
$hostname = form_input('hostname', 'wiki.ibeike.com');
$portnumber_label = "<label>Port number:</label>";
$portnumber = form_input(array('name' => 'portnumber', 'value' => '80', 'size' => 2));
$pyw_path_label = "<label>Pywikipedia directory:</label>"; 
$pyw_path = form_input('pyw_path', '/var/local/pywikipedia/');
$ep_path_label = "<label>ESS-Python directory:</label>";
$ep_path = form_input('ep_path', '/var/local/ess-python/');
$back_button = array(
    'name' => 'pyw_back',
    'id' => 'button',
    'type' => 'button',
    'content' => 'Back',
    'onClick' => 'window.history.back();',
);
$pyw_back = form_button($back_button);
$pyw_submit = form_submit('pyw_init_form_submit', 'Next');
// set table
$this->table->add_row(array('data' => 'MediaWiki site', 'colspan' => 2, 'align' =>'center'));
$this->table->add_row(array($hostname_label, $hostname));
$this->table->add_row(array($portnumber_label, $portnumber));
$this->table->add_row(array($pyw_path_label, $pyw_path));
$this->table->add_row(array($ep_path_label, $ep_path));
$this->table->add_row(array('data' => $pyw_back,'align' => 'center'),array('data' => $pyw_submit,'align' => 'center'));

// generate table
echo $this->table->generate();

echo form_fieldset_close();
echo form_close()
?>

