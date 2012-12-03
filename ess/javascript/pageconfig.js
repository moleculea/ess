//var urlprefix = '$urlprefix';
$(document).ready(function() {
   var options = new Array();
   	$('#ib_select_div').change(function(){
   		options.splice(0);
   		$('#ib_select_div option:selected').each(function(){
   			options.push($(this).text());
   		});
	
   	});

   	// Automatically filled #index_name with the first checked option
	$('#select').click(function() {
		var i;
		var opt='';
		for (i in options)
		{
			opt += '<a href=\"' + urlprefix + options[i].replace(/ /g,'_') +'\">' + options[i] + '</a><br/>';
			//opt += options[i];
		}
		$('div#ib_info').html(opt);
		//$('#index_name').val(options[0]);
	});
});