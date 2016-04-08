<?php
function getDB() {
	$dbhost="localhost";
	$dbuser="root";
	$dbpass="";
	$dbname="socialproject";
	$dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);	
	$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $dbConnection;
}
function execDB($qry)
{
        $db = getDB();
		$stmt = $db->query($qry);  
		$obj = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		return $obj;
}
?>