$(document).ready(function(){
	$('a.remove_group').each(function(){
		$(this).click(function(){
			var group_id = $(this).attr('href');
			$.get("/manage/groups", { type: "ajax", func: "remove_group", group_id: group_id },
			function(data){
				//alert(data);
				$('#group'+group_id).hide('fast');
			});
			return false;
		});	
	});
});