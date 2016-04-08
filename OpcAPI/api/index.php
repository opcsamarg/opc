<?php
include 'db.php';
require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/users','getUsers');
$app->get('/updates','getUserUpdates');
$app->post('/updates', 'insertUpdate');
$app->delete('/updates/delete/:update_id','deleteUpdate');
$app->get('/users/search/:query','getUserSearch');

$app->run();

function getUsers() {
	$sql = "SELECT user_id id,username uname,name name,profile_pic img FROM users ";
	try {
		echo json_encode(execDB($sql));
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getUserUpdates() {
	$sql = "SELECT A.user_id, A.username, A.name, A.profile_pic, B.update_id, B.user_update, B.created FROM users A, updates B WHERE A.user_id=B.user_id_fk  ORDER BY B.update_id DESC";
	try {
		$db = getDB();
		$stmt = $db->prepare($sql); 
		$stmt->execute();		
		$updates = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"updates": ' . json_encode($updates) . '}';
		
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function getUserUpdate($update_id) {
	$sql = "SELECT A.user_id, A.username, A.name, A.profile_pic, B.update_id, B.user_update, B.created FROM users A, updates B WHERE A.user_id=B.user_id_fk AND B.update_id=:update_id";
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
        $stmt->bindParam("update_id", $update_id);		
		$stmt->execute();		
		$updates = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"updates": ' . json_encode($updates) . '}';
		
	} catch(PDOException $e) {
	    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function insertUpdate() {
	$request = \Slim\Slim::getInstance()->request();
	$update = json_decode($request->getBody());
	$sql = "INSERT INTO updates (user_update, user_id_fk, created, ip) VALUES (:update, :uid, :created, :uip)";
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("update", $update->update);
		$stmt->bindParam("uid", $update->uid);
		$time=time();
		$stmt->bindParam("created", $time);
		$ip=$_SERVER['REMOTE_ADDR'];
		$stmt->bindParam("uip", $ip);
		$stmt->execute();
		$update->id = $db->lastInsertId();
		$db = null;
		echo "{ Res:".$update->id.",Msg:'Insert Successfully'}";
	} catch(PDOException $e) {
		//error_log($e->getMessage(), 3, '/var/tmp/php.log');
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}

function deleteUpdate($update_id) {
   
	$sql = "DELETE FROM updates WHERE update_id=:update_id";
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);  
		$stmt->bindParam("update_id", $update_id);
		$stmt->execute();
		$db = null;
		echo true;
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
	
}

function getUserSearch($query) {
	$sql = "SELECT user_id,username,name,profile_pic FROM users WHERE UPPER(name) LIKE :query ORDER BY user_id";
	try {
		$db = getDB();
		$stmt = $db->prepare($sql);
		$query = "%".$query."%";  
		$stmt->bindParam("query", $query);
		$stmt->execute();
		$users = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo '{"users": ' . json_encode($users) . '}';
	} catch(PDOException $e) {
		echo '{"error":{"text":'. $e->getMessage() .'}}'; 
	}
}
?>