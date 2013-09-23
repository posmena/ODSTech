function ubud_js(src) {
    ubud_url = '//' + src;
    document.write('<script src="' + ubud_url + '" type="text/javascript"></scr' + 'ipt>');
}

ubud_js('odst.co.uk/api/ubud/track.php?id=' + ubud_id  + '&site=' + ubud_site + '&type=' + ubud_conversion );
