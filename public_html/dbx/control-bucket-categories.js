$(document).ready(function(){
    $('#title').keypress(function(e) {
        //alert(e.keyCode);
        if(e.keyCode == 13)
        {
            $('#add_bucket_category').click();
            return false;
        }
    });
	$('#add_bucket_category').click(function(){
        if($('#title').val() == "")
        {
            alert('Title is required.');
            return false;
        }
		$.get("/manage/buckets", { type: "ajax", func: "add_cat", title: $('#title').val()},
			function(data){
				$('#bucket_settings_ajax').html(data);
				$('#title').val('');
			});
		return false;
	});
	$('#group_id').change(function(){
		$('#change_selected_group').click();
	});

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