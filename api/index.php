<?php
# include 'db-local.php';
include 'db-server.php';
require 'Slim/Slim.php';
date_default_timezone_set('Europe/London');
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->add(new \Slim\Middleware\SessionCookie(array(
    'expires' => '20 minutes',
    'path' => '/',
    'domain' => null,
    'secure' => false,
    'httponly' => false,
    'name' => 'slim_session',
    'secret' => 'CHANGE_ME',
    'cipher' => MCRYPT_RIJNDAEL_256,
    'cipher_mode' => MCRYPT_MODE_CBC
)));


$app->post('/search','searchMessages');
$app->get('/reps','getReps');
$app->get('/messages','getMessages');
$app->post('/insertmessage', 'insertMessage');
$app->post('/messages', 'updateMessage');
$app->delete('/messages/delete/:id','deleteMessage');

$app->run();

function searchMessages() {
  $term = $_POST['term'];
  $replace = array(" ", "-", "_", "\\", "/", "'", '"', "&", "(", ")");
  $replacedTerm = str_replace($replace, "%", $term);
  
  $request = \Slim\Slim::getInstance()->request->getBody();
  $sql = "SELECT * FROM messages WHERE status = 'active' AND category like '$replacedTerm'";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);  
    //$stmt->bindParam("term", $_POST['term']);
    $stmt->execute();
    $updates = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"message": ' . json_encode($updates) . '}';
    insertOutreach();
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  }
}

function insertOutreach() {
  $request = \Slim\Slim::getInstance()->request();
  $update = json_decode($request->getBody());
  $sql = "INSERT INTO outreach
          (term, tweet, twitterLink, rep, twitter, company)
          VALUES 
          (:term, :tweet, :twitterLink, :rep, :twitter, :company)";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);  
    $stmt->bindParam("twitterLink", $_POST['twitterLink']);
    $stmt->bindParam("twitter", $_POST['twitter']);
    $stmt->bindParam("company", $_POST['company']);
    $stmt->bindParam("term", $_POST['term']);
    $stmt->bindParam("tweet", $_POST['tweet']);
    $stmt->bindParam("rep", $_POST['rep']);
    $stmt->execute();
 #   $update->id = $db->lastInsertId();
    $db = null;
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
 #   echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  }
}

function getReps() {
  $sql = "SELECT * FROM Reps";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql); 
    $stmt->execute();		
    $updates = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"reps": ' . json_encode($updates) . '}';

  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  }
}

function getMessages() {
  $sql = "SELECT * FROM messages WHERE status = 'active' ORDER BY created DESC ";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql); 
    $stmt->execute();		
    $updates = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"messages": ' . json_encode($updates) . '}';

  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  }
}

function updateMessage() {
  $request = \Slim\Slim::getInstance()->request();
  $update = json_decode($request->getBody());
  $sql = "UPDATE messages
          SET 
          message=:message,
          status=:status, 
          category=:category
          WHERE id=:id ";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);  
    $stmt->bindParam("message", $update->message);
    $stmt->bindParam("status", $update->status);
    $stmt->bindParam("category", $update->category);
    $stmt->bindParam("id", $update->id);
    $stmt->execute();
    $update->id = $db->lastInsertId();
    $db = null;
    $update_id= $update->id;
    getPostUpdate($update_id);
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  }
}

function insertMessage() {
  $request = \Slim\Slim::getInstance()->request();
  $update = json_decode($request->getBody());
  $sql = "INSERT INTO messages
          (message, status, category, rep)
          VALUES 
          (:message, :status, :category, :rep)";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);  
    $stmt->bindParam("message", $update->message);
    $stmt->bindParam("status", $update->status);
    $stmt->bindParam("category", $update->category);
    $stmt->bindParam("rep", $update->rep);
    $stmt->execute();
    $update->id = $db->lastInsertId();
    $db = null;
    $update_id= $update->id;
    getPostUpdate($update_id);
  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  }
}

function getPostUpdate($id) {
  $sql = "SELECT * FROM messages WHERE id=:id";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->bindParam("id", $id);		
    $stmt->execute();		
    $updates = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    echo '{"messages": ' . json_encode($updates) . '}';

  } catch(PDOException $e) {
    //error_log($e->getMessage(), 3, '/var/tmp/php.log');
    echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  }
}

function deleteMessage($id) {

  $sql = "DELETE FROM messages WHERE id=:id";
  try {
    $db = getDB();
    $stmt = $db->prepare($sql);  
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $db = null;
    echo true;
  } catch(PDOException $e) {
    echo '{"error":{"text":'. $e->getMessage() .'}}'; 
  }

}

?>