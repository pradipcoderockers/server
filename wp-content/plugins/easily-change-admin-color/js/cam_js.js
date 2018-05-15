jQuery(document).ready(function($){
   var myOptions = {
    palettes: true
	};
   
   
    $('.my-color-field').wpColorPicker(myOptions);
	
	
	
	
	//lets play with color schemes.
	$('.scheme_hide').hide();
	$('.scheme_hide').first().show();
	
	$('#schemes').change(function(){
		var scheme = $('#schemes').val();
		$('.scheme_hide').hide();
		
		
		
		if(scheme == 'allow-deny'){
			$('#scheme-allow-deny').show();
		}
		
		if(scheme == 'red'){
			$('#scheme-red').show();
		}
		
		if(scheme == 'blue'){
			$('#scheme-blue').show();
		}
		
		if(scheme == 'primary'){
			$('#scheme-primary').show();
		}
		
		if(scheme == 'purple'){
			$('#scheme-purple').show();	
		}
		
		if(scheme == 'green'){
			$('#scheme-green').show();
		}
		
		if(scheme == 'orange'){
			$('#scheme-orange').show();
		}
		
		if(scheme == 'yellow'){
			$('#scheme-yellow').show();
		}
	})
	});