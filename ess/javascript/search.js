$(document).ready(function() {
	$('input').blur(function(){
		// automated trim when blur
		$(this).val($.trim($(this).val()));
	});
	
	$('select[id$=_start_year]').change(function(){
		prf = $(this).attr('id');
		value = $(this).val();
		value = parseInt(value);
		index = prf.search(/_start_year/)
		pr = prf.substr(0,index);
		if ($('input[name=enable_'+pr+']').is(':checked')){
			inner = '';
			if (value!=0){
			 	for(i=value;i<2012;i++){
			 		inner += '<option value=\"'+i+'\">'+i+'</option>';
			 	}
				$('select[id='+ pr +'_end_year]').html(inner);
			}
		}
	});
	
	
	$('select[id$=_start_month]').change(function(){
		
		prf = $(this).attr('id');
		value = $(this).val();
		value = parseInt(value);
		index = prf.search(/_start_month/)
		pr = prf.substr(0,index);
		if ($('input[name=enable_'+pr+']').is(':checked')){
			endvalue = $('select[id='+ pr +'_end_year]').val();
			startvalue = $('select[id='+ pr +'_start_year]').val();
			
			endvalue = parseInt(endvalue);
			startvalue = parseInt(startvalue);
			if (endvalue == startvalue){
				$('select[id='+ pr +'_end_month]').val(value)
			}
		}
	});
	
	
	$('select[id$=_start_day]').change(function(){
		
		prf = $(this).attr('id');
		value = $(this).val();
		index = prf.search(/_start_day/)
		pr = prf.substr(0,index);
		if ($('input[name=enable_'+pr+']').is(':checked')){

			endvalue = $('select[id='+ pr +'_end_year]').val();
			startvalue = $('select[id='+ pr +'_start_year]').val();
			
			endmvalue = $('select[id='+ pr +'_end_month]').val();
			startmvalue = $('select[id='+ pr +'_start_month]').val();
			
			endvalue = parseInt(endvalue);
			startvalue = parseInt(startvalue);
			
			endmvalue = parseInt(endmvalue);
			startmvalue = parseInt(startmvalue);
			
			if (endvalue == startvalue && endmvalue == startmvalue){
				$('select[id='+ pr +'_end_day]').val(value)
			}
		}
	});
	
	$('input[name^=enable_]').click(function(){
		prf = $(this).attr('name');
		pr = prf.substr(7);

		 if ($(this).is(':checked')) { 
			 $('select[id^='+ pr +'_end_]').attr('disabled',false);
		 } 
		 else { 
			 $('select[id^='+ pr +'_end_]').attr('disabled',true);
		 } 
	});
		
	
});