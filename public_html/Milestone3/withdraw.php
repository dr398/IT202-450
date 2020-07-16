<?php
include("header.php");

$email=$_SESSION["user"]["email"];
$accounts=$_SESSION["user"]["accounts"];
$new_arr = array_column($accounts,'Account_Number');
echo "Hello". $email;?>
    <form method="POST">
        <label for="name">Account
        </label>
        <select name="Name" id="Name">
            <?php
            foreach($new_arr as $item){
                ?>
                <option value="<?php echo strtolower($item); ?>"><?php echo $item; ?></option>
                <?php
            }
            ?>
        </select>
        <label for="balance">Amount
            <input type="number" id="balance" name="Balance" />
        </label>
        <input type="submit" name="Withdraw" value="Withdraw"/>
    </form>
<?php
require("common.inc.php");
if(isset($_POST["Withdraw"])){
    $name = $_POST["Name"];
    $balance = $_POST["Balance"];
    $balance=$balance * -1;
    if(!empty($name) && !empty($balance)){
        require("config.php");
        $connection_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
        try{
            $db = new PDO($connection_string, $dbuser, $dbpass);
            $stmt = $db->prepare("INSERT INTO Transactions (act_src_id, act_dest_id,type,amount,expected_total) VALUES (:accnum,:accnum1, :type,:balance,:exp_balance)");
            $result = $stmt->execute(array(
                ":accnum" => $name,
                ":accnum1" => "000000000000",
                ":type" => "Withdraw",
                ":balance" => $balance,
                ":exp_balance" => $balance
            ));
            $e = $stmt->errorInfo();
            if($e[0] != "00000"){
                var_dump($e);
                echo "setting eee ".$e."<br>";
            }
            $balance =$balance * -1;
            echo $balance;
            $stmt2 = $db->prepare("INSERT INTO Transactions (act_src_id, act_dest_id,Type,Amount,expected_total) VALUES (:acc1,:acc, :type,:balance,:exp_balance)");
            $result1 = $stmt2->execute(array(
                ":acc1" => "000000000000",
                ":acc" => $name,
                ":type" => "Deposit",
                ":balance" => $balance,
                ":exp_balance" => $balance
            ));
            $e = $stmt2->errorInfo();
            if($e[0] != "00000"){
                var_dump($e);
                $stmt2->debugDumpParams();
            }
            $stmt = $db->prepare("update Accounts set Balance= (SELECT sum(Amount) FROM Transactions WHERE act_src_id=:accnum) where Account_Number=:accnum");
            $result = $stmt->execute(array(
                ":accnum" => $name
            ));
            if ($result){
                echo "Successfully inserted new thing: " . $name;
            }
            else{
                echo "Error inserting record";
            }
        }
        catch (Exception $e){
            echo "Error inserting record 1";
            echo $e->getMessage();
        }
    }
    else{
        echo "<div>Account and Amount must not be empty.<div>";
    }
}
$stmt = $db->prepare("SELECT * FROM Accounts");
$stmt->execute();
?>