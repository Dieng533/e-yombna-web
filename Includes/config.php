<?php
$host = 'localhost';
$dbname = 'e_yombna';
$username = 'root';
$password = ''; // change selon ton environnement

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}
?>
