<form action='http://www3.hilton.com/en_US/hi/search/findhotels/index.htm' method='post' name='frm'>
<?php
foreach ($_GET as $a => $b) {
echo "<input type='hidden' name='".htmlentities($a)."' value='".htmlentities($b)."'>";
}
?>
</form>
<script language="JavaScript">
document.frm.submit();
</script>