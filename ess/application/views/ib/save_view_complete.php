<?php 
// complete view in save view for ib
// initialization complete view
?>
<h3><?php echo $title; ?></h3>
<?php 
echo $save_complete;
echo $init_complete;
echo "<br/>";
$back_button = array(
    'name' => 'ib_back',
    'id' => 'button',
    'type' => 'button',
    'content' => 'Back',
    'onClick' => 'window.history.back();',
);

echo form_button($back_button);
?>

