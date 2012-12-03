<?php 
// config view for ib
?>
<h3><?php echo $title; ?></h3>

<?php 
// Running: display running icon
if ($flag){
	echo $status;
}
// Finished: display exec interface
else {

	if (empty($iblist)) // No pages return from Pywikipedia command which check "{{{" as a mark for infobox templates
		echo "No template pages results. <br/>Please check whether category for templates you provided is correct.";

	else {
		if (preg_match_all("/#\[\[(Template:([^\]]+))]]/",$iblist,$matches)){
		// Match pages with "Template:" prefixes
			$form_attributes = array(
				'name' => 'ib_exec_form',
				'class' => 'form', 
				'id' => 'ib_exec_form',
			);

			echo form_open('init/ib_save', $form_attributes);
			echo form_fieldset('Infobox list');
			foreach ($matches[1] as $key1 => $line1){
				$infoboxline = array(
					'name'        => 'infobox[]',
				    'id'          => 'infobox',
				    'value'       => $matches[2][$key1],
					'checked'       => TRUE,
				);
				
				#echo form_checkbox('infobox[]', $matches[2][$key1], TRUE);
				echo form_checkbox($infoboxline);
				echo "<a href='http://wiki.ibeike.com/index.php/".$line1."' >";
				echo $matches[2][$key1];
				echo "</a>";
				echo "<br/>\n";
			}
			$check = array(
			    'name'        => 'checkall',
			    'id'          => 'checkall',
			    'value'       => 'accept',
				'checked'       => TRUE,
			);
			echo form_checkbox($check);
			echo "Check All/None<br/>";
			$back_button = array(
			    'name' => 'pyw_back',
			    'id' => 'button',
			    'type' => 'button',
			    'content' => 'Back',
			    'onClick' => 'window.history.back();',
			);
			//$ib_back = form_button($back_button);
			echo form_button($back_button);
			echo form_submit('ib_exec_form_submit', 'Next');
			echo form_fieldset_close();
			echo form_close();
		}
		else
			echo "The category you provided contains pages with inline paramters, but none of these pages has \"Template:\" prefix.<br/>Please check again.";
	}
}
?>
