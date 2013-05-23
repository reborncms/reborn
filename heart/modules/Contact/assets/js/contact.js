(function($) {
	$(function(){
		
		
		// Tag Input
		if(document.getElementById('email')) {
			$('#email').tagsInput({
				height:'auto',
				width:'50%',
				defaultText:'Add Email',
				placeholderColor:'#CBCBCB'
			});
		}

	

	});
})(jQuery);