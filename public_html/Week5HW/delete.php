<?php
require("config.php");
$connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
$db = new PDO($connection_string, $dbuser, $dbpass);
$accountId = -1;
$result = array();
function get($arr, $key){
    if(isset($arr[$key])){
        return $arr[$key];
    }
    return "";
}
if(isset($_GET["accountId"])){
    $accountId = $_GET["accountId"];
    $stmt = $db->prepare("SELECT * FROM Accounts where id = :id");
    $stmt->execute([":id"=>$accountId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$result){
        $accountId = -1;
    }
}
else{
    echo "No accountId provided in url.";
}
?>

<form method="POST">
	<label for="account">Account Name
	<input type="text" id="account" name="name" value="<?php echo get($result, "name");?>" />
	</label>
	<label for="b">Balance
	<input type="number" id="b" name="balance" value="<?php echo get($result, "balance");?>" />
	</label>
    <?php if($accountId > 0):?>
	    <input type="submit" name="updated" value="Update Account"/>
        <input type="submit" name="delete" value="Delete Account"/>
    <?php elseif ($accountId < 0):?>
        <input type="submit" name="created" value="Create Account"/>
    <?php endif;?>
</form>

<?php
if(isset($_POST["updated"]) || isset($_POST["created"]) || isset($_POST["delete"])){
    $delete = isset($_POST["delete"]);
    $name = $_POST["name"];
    $balance = $_POST["balance"];
    if(!empty($name) && !empty($balance)){
        try{
            if($accountId > 0) {
                if($delete){
                    $stmt = $db->prepare("DELETE from Accounts where id=:id");
                    $result = $stmt->execute(array(
                        ":id" => $accountId
                    ));
                }
                else {
                    $stmt = $db->prepare("UPDATE Accounts set name = :name, balance=:balance where id=:id");
                    $result = $stmt->execute(array(
                        ":name" => $name,
                        ":balance" => $balance,
                        ":id" => $accountId
                    ));
                }
            }
            else{
                $stmt = $db->prepare("INSERT INTO Accounts (name, balance) VALUES (:name, :balance)");
                $result = $stmt->execute(array(
                    ":name" => $name,
                    ":balance" => $balance
                ));
            }
            $e = $stmt->errorInfo();
            if($e[0] != "00000"){
                echo var_export($e, true);
            }
            else{
                echo var_export($result, true);
                if ($result){
                    echo "Successfully interacted with account: " . $name;
                }
                else{
                    echo "Error interacting account";
                }
            }
        }
        catch (Exception $e){
            echo $e->getMessage();
        }
    }
    else{
        echo "Name and balance must not be empty.";
    }
}
?>
