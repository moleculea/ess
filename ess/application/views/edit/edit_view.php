<?php
// edit view
?>
<h3><?php echo $title; ?></h3>
<?php
if ($scenario == "index_list"){
	
	echo "<h4>Choose an index to edit:</h4>";
	echo "<ul>\n";
	foreach($result as $index){
		echo "<li><a href='edit/index/$index->in_name' >".$index->in_name."</a></li>";
	}
	echo "</ul>\n";
	
}

if ($scenario == "index"){

	echo "<h4>Edit index \"$indexname\"</h4>";
	echo "<ul>";
	echo "<li>";
	echo anchor("edit/index/$indexname/delete","Delete this index");
	echo "</li>";
	echo "</ul>";
	if (!empty($result)){
		echo "<p class='result_number'>$total_num records.</p>";
	}
	echo $this->pagination->create_links();

	if (empty($result)){
		echo "<p>No search results.</p>";
	}
	else{
		echo "<ul>";
		foreach ($result as $page){
			echo "<li>";
			echo anchor("edit/index/$indexname/pg_id=$page->pg_id","$page->pg_name");
			echo "</li>";
		}
		
		echo "</ul>";
	}
	echo $this->pagination->create_links();


}

if ($scenario == "page"){

	$v_path = $page->v_path;
	$pg_name = $page->pg_name;
	$pg_id = $page->pg_id;
	$ess = base_url();
	$vFile = $ess."indexed/".$v_path.$pg_name.".xml";

	$vxml = simplexml_load_file($vFile);
	$pxml = simplexml_load_file($pFile);
	
	$form_attributes = array(
		'name' => 'edit_form',
		'class' => 'form', 
		'id' => 'edit_form',
		'onSubmit' => '',
	);
	
	echo form_open("edit/index/$indexname/pg_id=$pg_id", $form_attributes);
	echo form_fieldset('Edit page');
	
	echo "<table>\n";
	
	$n = 0;
	$parameter = $pxml->children();
	
	foreach($vxml->children() as $children){
		//echo $children;
		
		echo "<tr><td>";
		if ($parameter[$n]=="")
			echo form_label("分类", $children->getName());
		else
			echo form_label($parameter[$n], $children->getName());
		echo "</td>";
		echo "<td>";
		//$attr = 'rows = "2" cols="40"';
		//echo form_textarea($children->getName(), $children,$attr);
		echo "<textarea name='".$children->getName()."' rows = '2' cols='40'>$children";
		echo "</textarea>";
		echo "</td></tr>\n";
		$n++;
	}
	echo "</table>\n";
	echo form_checkbox('sync', 'yes', TRUE);
	echo form_label("同步至 MediaWiki", "sync_label");
	echo "<br/>";
	echo form_submit('submit', 'Edit');
	echo form_fieldset_close();
	echo form_close();

}
