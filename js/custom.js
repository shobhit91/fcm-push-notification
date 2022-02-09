
jQuery('.ios_send').click(function(){
	if(jQuery('#ios_certificate').val() == undefined || jQuery('#ios_certificate').val() == null || jQuery('#ios_certificate').val() ==''){
		alert('Please upload certificate file');
		jQuery('#ios_certificate').focus();
		return false;
	}else{
		jQuery('#ios_form').submit();
	}
});

