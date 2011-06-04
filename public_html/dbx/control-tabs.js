$(document).ready(function(){
    $('#tab_text,#tab_url').keypress(function(e) {
        //alert(e.keyCode);
        if(e.keyCode == 13)
        {
            $('#add_tab').click();
            return false;
        }
    });
	$('#add_tab').click(function(){
        if($('#tab_text').val() == "" || $('#tab_url').val() == "")
        {
            alert('Text & URL are both required.');
            return false;
        }
		$.get("/manage/tabs", { type: "ajax", func: "add_tab", tab_text: $('#tab_text').val(), tab_url: $('#tab_url').val()},
			function(data){
				$('#tab_settings_ajax').html(data);
				$('#tab_text').val('');
			});
		return false;
	});

});