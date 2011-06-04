$(document).ready(function(){
    $('#title,#rss_url').keypress(function(e) {
        //alert(e.keyCode);
        if(e.keyCode == 13)
        {
            $('#add_ebay_category').click();
            return false;
        }
    });
	$('#add_ebay_category').click(function(){
        if($('#title').val() == "" ||  $('#rss_url').val() == "")
        {
            alert('Title and RSS URL are both required.');
            return false;
        }
		$.get("/manage/ebay", { type: "ajax", func: "add_cat", title: $('#title').val(), rss_url: $('#rss_url').val()},
			function(data){
				$('#ebay_settings_ajax').html(data);
				$('#title').val('');
                $('#rss_url').val('')
			});
		return false;
	});
	$('#group_id').change(function(){
		$('#change_selected_group').click();
	});
});