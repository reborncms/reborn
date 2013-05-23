jQuery(function(){
	/* $('.confirm_delete').click(function(){
		return confirm("Are you sure you want to delete ?");
	}); */
	$(".confirm_multiple_delete").click(function(){
    	var n = $("input:checked").length;
    	confirm("This will delete "+ n +" comments.You cannot roll back after this. Are you sure you want to do this ?");
    });

    $(".admin_reply").colorbox({
    	width:"40%",
    	height:"40%"
    });
});