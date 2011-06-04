$(document).ready(function(){

	$('a.remove_cat').each(function(){
		$(this).click(function(){
			var cat_id = $(this).attr('href');
			$.get("/manage/cats", { type: "ajax", func: "remove_cat", cat_id: cat_id },
			function(data){
				$('#listcat'+cat_id).hide('fast');
			});
			return false;
		});	
	});

});