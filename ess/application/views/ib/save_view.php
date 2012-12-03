<?php 
// save view for ib
?>
<h3><?php echo $title; ?></h3>
<?php 
$form_attributes = array(
				'name' => 'ib_save_form',
				'class' => 'form', 
				'id' => 'ib_save_form',
);
echo form_open('init/ib_save', $form_attributes);
echo form_fieldset('Infobox list');
if ($infobox_array){
	echo "The following list of infobox will be saved. Click \"Save\" if you are sure.<br/>";
	echo "<ol>";
	foreach ($infobox_array as $infobox)
	{
		echo "<li>".$infobox."<br>";
	}
	echo "</ol>";
}
else 
	echo "No infobox selected.<br/>";
$back_button = array(
    'name' => 'ib_back',
    'id' => 'button',
    'type' => 'button',
    'content' => 'Back',
    'onClick' => 'window.history.back();',
);

echo form_button($back_button);
echo form_submit('ib_save_form_submit', 'Save');
echo form_hidden('ib_save_form_hidden', 'Hidden');
echo form_fieldset_close();
echo form_close();
?>