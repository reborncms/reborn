(function($) {
	$(function(){
		
		
		// Tag Input
		if(document.getElementById('email')) {
			$('#email').tagsInput({
				height:'auto',
				width:'29.5%',
				defaultText:'Add Email',
				placeholderColor:'#CBCBCB'
			});
		}

	

	});
})(jQuery);