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
	$('#check').click(function() {
		var i;
		var opt='';
		for (i in options)
		{
			opt += '<a href=\"' + urlprefix + options[i].replace(/ /g,'_') +'\">' + options[i] + '</a><br/>';
			//opt += options[i];
		}
		$('div#ib_info').html(opt);
		$('#index_name').val(options[0]);
	});
	
	// function append is in HTML for it contains PHP variables
	$('#remove').click(function() {
	   	var table1 = $('#type_select_table tr');  
	    var len1 = table1.length;  
		$('tr[id='+(len1-1)+']').remove();
	});	
	/*
	var cnt = new Array(0,1,1,1,1,1,1,1); // initialize global variable counters
	$('body').live('click',function(){
	   	var raw_id;
	   	var index;
	   	var id;
	  	var input;   		
		index = event.target.selectedIndex;
		raw_id = event.target.parentNode.id;
		id = raw_id.substring(2);
		input = $.trim($('input#fieldname_'+id).val());
		switch(index)
		{	
			case 1:
				if (input == ''){
					$('input#fieldname_'+id).val('name_' + lpad(cnt[1],2));
					cnt[1]++;
				}
				break;
			case 2:
				if (input == ''){  			
	  				$('input#fieldname_'+id).val('date_' + lpad(cnt[2],2));
	  				cnt[2]++;
				}
				break;
			case 3:
				if (input == ''){ 	
	  				$('input#fieldname_'+id).val('wtext_' + lpad(cnt[3],2));
	  				cnt[3]++;
				}
				break;
			case 4:
				if (input == ''){ 
	  				$('input#fieldname_'+id).val('num_' + lpad(cnt[4],2));
	  				cnt[4]++;
				}
				break;
			case 5:
				if (input == ''){ 
	  				$('input#fieldname_'+id).val('unit_' + lpad(cnt[5],2));
	  				cnt[5]++;
				}
				break;
			case 6:
				if (input == ''){ 
	  				$('input#fieldname_'+id).val('cmt_' + lpad(cnt[6],2)); 	
	  				cnt[6]++;
	  			}
				break;
			case 7:
				if (input == ''){ 
	  				$('input#fieldname_'+id).val('img_' + lpad(cnt[7],2));	
	  				cnt[7]++;
	  			}
				break;		
		}
		if (index == 4)
			$('input#optional_'+id).attr('disabled','');
		else
			$('input#optional_'+id).attr('disabled','disabled');
		
	});
	*/
	var cnt = new Array(0,1,1,1,1,1,1,1); // initialize global variable counters

	$('select[name^=type_select_]').live("change",function(){

		var pr;
		pr = $(this);
		
		id = pr.attr('name').substr(-1);
		id2 = pr.attr('name').substr(-2);
		var re = /^\d+$/;
		if (re.test(id2))
			id = id2;
		index = pr.children(":selected").val();

		//alert(id);
		//alert(index);
		input = $.trim($('input#fieldname_'+id).val());
		index = parseInt(index)
		switch(index)
		{
			case 1:
				if (input == ''){
					$('input#fieldname_'+id).val('name_' + lpad(cnt[1],2));
					cnt[1]++;
				}
				break;
			case 2:
				if (input == ''){  			
	  				$('input#fieldname_'+id).val('date_' + lpad(cnt[2],2));
	  				cnt[2]++;
				}
				break;
			case 3:
				if (input == ''){ 	
	  				$('input#fieldname_'+id).val('wtext_' + lpad(cnt[3],2));
	  				cnt[3]++;
				}
				break;
			case 4:
				if (input == ''){ 
	  				$('input#fieldname_'+id).val('num_' + lpad(cnt[4],2));
	  				cnt[4]++;
				}
				break;
			case 5:
				if (input == ''){ 
	  				$('input#fieldname_'+id).val('unit_' + lpad(cnt[5],2));
	  				cnt[5]++;
				}
				break;
			case 6:
				if (input == ''){ 
	  				$('input#fieldname_'+id).val('cmt_' + lpad(cnt[6],2)); 	
	  				cnt[6]++;
	  			}
				break;
			case 7:
				if (input == ''){ 
	  				$('input#fieldname_'+id).val('img_' + lpad(cnt[7],2));	
	  				cnt[7]++;
	  			}
				break;	
			default:	
		}
		if (index == 4)
			$('input#optional_'+id).attr('disabled','');
		else
			$('input#optional_'+id).attr('disabled','disabled');

	});
	

	$('input[id^=fieldname_]').blur(function(){ // automated trim when blur
		$(this).val($.trim($(this).val()));
	});
	$('input[id^=parameter_]').blur(function(){ // automated trim when blur
		$(this).val($.trim($(this).val()));
	});
	
	function lpad(num, n) {
	    var len = num.toString().length;
	    while(len < n) {
	        num = '0' + num;
		        len++;
		    }
		    return num;
		}
	
	//Submit check
	$('input[name=index_config]').click(function() {
	var index;
	var stayflag = 0;
	var flag = new Array();
		$('input[name^=parameter_]').each(function(){
			pr = $(this);
			index = pr.attr('name').substr(-1);
			
			index2 = pr.attr('name').substr(-2);
			var re = /^\d+$/;
			if (re.test(index2))
				 index = index2;
			
			fd = $('input[name=fieldname_'+index+']');
			tp = $('select[name=type_select_'+index+']');

			if (!(pr.val() =='' && fd.val() == '' && tp.val() == '0')){
				//stayflag = 1;
				if (pr.val() ==''){
					pr.css({'background-color':'#FFA07A'});
					pr.attr('class','invalid');
				}
					
				if (fd.val() ==''){
					fd.css({'background-color':'#FFA07A'});
					fd.attr('class','invalid');
				}

				if (tp.val() =='0'){
					tp.css({'background-color':'#FFA07A'});
					tp.attr('class','invalid');
				}

			}
			if (pr.attr('class')=='invalid' || fd.attr('class')=='invalid' || tp.attr('class')=='invalid')
			{
			 	flag.push(1);
			}
			else
				flag.push(0);
		});
		
		var i;
		stayflag = 0;
		for (i in flag)
		{
		//alert(flag[i]);
		 if (flag[i] == 1)
			{stayflag = 1;}
		}
		
		// Fieldname validation check
		$('input[name^=fieldname_]').each(function(){
			reg = /^([a-zA-Z])+([a-zA-Z0-9_-])*$/;
			value = $(this).val();
			if (value!=""){
				if(!reg.test(value)){
					stayflag = 1;
					$(this).css({'background-color':'#FFA500'});
					$(this).attr('class','invalid');
				}
			}
		
		});
		//Fieldname identical check
		//var prevalue == $('input[name^=fieldname_1]').val();
		
		$('input[name^=fieldname_]').each(function(){
			p_fd = $(this);
			p_index = p_fd.attr('name').substr(-1);
			p_index2 = p_fd.attr('name').substr(-2);
			var re = /^\d+$/;
			if (re.test(p_index2))
				 p_index = p_index2;
			p_value = p_fd.val();
			if (p_value){
				$('input[name^=fieldname_]').each(function(){
					c_fd = $(this);
					c_index = c_fd.attr('name').substr(-1);
					c_index2 = c_fd.attr('name').substr(-2);
					re = /^\d+$/;
					if (re.test(c_index2))
						 c_index = c_index2;
					c_value = c_fd.val();
					if (c_index > p_index){
						if(p_value == c_value){	
							stayflag = 1;
							p_fd.css({'background-color':'#DAA520'});
							p_fd.attr('class','identical');
							c_fd.css({'background-color':'#DAA520'});
							c_fd.attr('class','identical');
							
						}
					}
					
				});
			}
			
		});
		
		//alert('stayflag:'+stayflag);
		if (stayflag == 1){
		// Stay in the page
			return false;
		}
		else{
			// Final step, check whether index_name table exists in database and generate confirm box
			//$('input[name=index_config]').click(function() {
				var sflag = 0;
				var indexname = $('#index_name').val(); 
				var c;
				
				// Set Ajax to synchronous so that Javascript will process the following until it has return value
				$.ajaxSetup({async:false}); 
				
				$.post('/ess/indexation/pg_config',{validate:'submit',index_name:indexname},function(data){
					if (data > 0){
						c = confirm('Index '+ indexname +' already exists. Are you sure that you continue and drop all the existing data?');
						if (!c)
							{sflag = 1;}
					}
							
				});
				if (sflag == 1){
					return false;
				}
			//});
		}
	});
	
	$('input,select').live("change",function(){
		pr = $(this);
		index = pr.attr('name').substr(-1);
		
		index2 = pr.attr('name').substr(-2);
		var re = /^\d+$/;
		if (re.test(index2))
			 index = index2;
		
		$(this).css({'background-color':'white'});
		$(this).attr('class','');
		
		$('input#fieldname_'+index).css({'background-color':'white'});
		$('input#fieldname_'+index).attr('class','');
		
		$('input#parameter_'+index).css({'background-color':'white'});
		$('input#parameter_'+index).attr('class','');
		
		
	});
	

	
});