<?php
include("header.php");
require("common.inc.php");
require("config.php");
$email=$_SESSION["user"]["email"];
$account=$_GET["account"];
$connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
$db = new PDO($connection_string, $dbuser, $dbpass);
$stmt = $db->prepare("SELECT count(*) as num FROM Transactions where acc_dest_id=:acc");
$stmt->execute(array(
    ":acc" => $account
));
$res = $stmt->fetchAll();
$e = $stmt->errorInfo();
if($e[0] != "00000"){
    var_dump($e);
    echo "setting eee ".$e."<br>";
}
$num=$res[0]["num"];

$stmt1 = $db->prepare("SELECT * FROM Accounts where acc_num=:acc");
$stmt1->execute(array(
    ":acc" => $account
));
$result = $stmt1->fetchAll();
$e = $stmt1->errorInfo();
if($e[0] != "00000"){
    var_dump($e);
    echo "setting eee ".$e."<br>";
}
$type=$result[0]["acctype"];
$amount=$result[0]["Balance"];

echo "<h3>Details for ".$account."</h3>";
echo "<h4>Account Type: ".$type."</h4>";
echo "<h4>Account Balance : $".$amount."</h4>";
?>