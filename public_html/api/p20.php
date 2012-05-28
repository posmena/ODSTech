<?php
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

$arr = $_GET;
array_shift ( $arr )
array_shift ( $arr )

$products = $collection->find($arr);

print_r($products);
