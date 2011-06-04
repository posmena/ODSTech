$(document).ready(function(){
    $('#title,#search_term').keypress(function(e) {
        //alert(e.keyCode);
        if(e.keyCode == 13)
        {
            $('#add_amazon_category').click();
            return false;
        }
    });
	$('#add_amazon_category').click(function(){
        if($('#title').val() == "")
        {
            alert('Category Name is required.');
            return false;
        }
		$.get("/manage/amazon", { type: "ajax", func: "add_cat", title: $('#title').val(), search_term: $('#search_term').val(), locale: $('#locale').val(), search_index: $('#search_index').val()},
			function(data){
				$('#amazon_settings_ajax').html(data);
				$('#title').val('');
			});
		return false;
	});
	$('#group_id').change(function(){
		$('#change_selected_group').click();
	});
});