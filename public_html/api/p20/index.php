<?php
//phpinfo();
//die();
/*
if (false === isset($_GET['user']) || false === isset($_GET['pass']) || false ) {
	print 'You are not authorised to use this service.';
	exit;
}

$email    = stripslashes($_GET['user']);
$password = stripslashes($_GET['pass']);

$conn = new Mongo();
$db   = $conn->odstech;
// access collection

$validUser = (bool) $db->ot_users->find(array('username' => $email, 'password' => md5($password)))->count();

if( false == $validUser )
{
	print 'You are not authorised to use this service.';
	exit;
}

*/
$conn = new Mongo('localhost');
// access database
$mdb = $conn->odstech;
// access collection
$collection = $mdb->p20_products;

$arr = $_GET["params"];

$max = isset($_GET["max"]) ? $_GET["max"] : "10";
if( !is_numeric($max)) $max=10 ;

$products = $collection->find($arr)->limit($max);
$p = array();
foreach($products as $product)
{
$p[] = $product;
}

echo(json_encode($p));