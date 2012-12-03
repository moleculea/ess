<?php
if ($scenario == "signup"){
	?>
<h3><?php echo $title; ?></h3>

<?php 

$form_attributes = array(
	'name' => 'signup_form',
	'class' => 'form', 
	'id' => 'signup_form',
	'onSubmit' => '',
);

echo form_open('login/signup', $form_attributes);
echo form_fieldset('Signup');

echo "<table>\n";
echo "<tr><td>";
echo form_label("Username", "username");
echo "</td>";
echo "<td>";
echo form_input('username', '');
echo "</td></tr>\n";
echo "<tr><td>";
echo form_label("Password", "password");
echo "</td>";
echo "<td>";
echo form_password('password', '');
echo "</td></tr>\n";
echo "<tr><td>";
echo form_label("Confirm password ", "password_conf");
echo "</td>";
echo "<td>";
echo form_password('conf_password', '');
echo "</td></tr>\n";
echo "</table>\n";
echo form_reset('reset','Reset');
echo form_submit('submit', 'Signup');
echo form_fieldset_close();
echo form_close();

}

if ($scenario =="result"){
	?>
<h3><?php echo $title; ?></h3>
<?php 

if ($num > 0){
	echo "<p>";
	echo $msg;
	echo "<br/>";
	echo "Please <a href='login'>login</a>.";
	echo "</p>";
}
else{
	
	echo "<p>";
	echo $msg;
	echo "<br/>";
	echo "Please <a href='login/signup'>try again</a>.";
	echo "</p>";
}
}

if ($scenario == "login"){
	?>
<h3><?php echo $title; ?></h3>

<?php 
$form_attributes = array(
	'name' => 'login_form',
	'class' => 'form', 
	'id' => 'login_form',
	'onSubmit' => '',
);

echo form_open('login/index', $form_attributes);
echo form_fieldset('Signup');

echo "<table>\n";
echo "<tr><td>";
echo form_label("Username", "username");
echo "</td>";
echo "<td>";
echo form_input('username', '');
echo "</td></tr>\n";
echo "<tr><td>";
echo form_label("Password", "password");
echo "</td>";
echo "<td>";
echo form_password('password', '');
echo "</td></tr>\n";
echo "<tr><td colspan ='2' align='center'>";
echo "Not a user?";
echo "<a href='login/signup'>Signup</a><br/>";
echo "</td></tr>\n";
echo "</table>\n";
echo form_reset('reset','Reset');
echo form_submit('submit', 'Login');
echo form_fieldset_close();
echo form_close();


}

if ($scenario == "login_result"){
?>
<h3><?php echo $title; ?></h3>
	
<?php 
echo "<p>";
echo $msg;
echo "</p>";

}

if ($scenario == "logout"){
	?>
<h3><?php echo $title; ?></h3>
	
<?php 
echo "<p>";
echo $msg;
echo "</p>";

}