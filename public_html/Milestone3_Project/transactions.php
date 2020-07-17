<?php
include("header.php");
?>
    <h2>Make a Transaction</h2>
<?php
ini_set('display_errors',1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
function do_bank_action($account2, $account3, $amountChange, $acc_type){
    require("config.php");
    $conn_string = "mysql:host=$dbhost;dbname=$dbdatabase;charset=utf8mb4";
    $db = new PDO($conn_string, $dbuser, $dbpass);
    $a1total = 0;//TODO get total of account 1
    $a2total = 0;//TODO get total of account 2
    $query = "INSERT INTO `Transactions` (`acc_src_id`, `acc_dest_id`, `amount`, `acc_type`, `exp_total`) 
	VALUES(:p1a1, :p1a2, :p1change, :type, :a1total), 
			(:p2a1, :p2a2, :p2change, :type, :a2total)";

    $stmt = $db->prepare($query);
    $stmt->bindValue(":p1a1", $account2);
    $stmt->bindValue(":p1a2", $account3);
    $stmt->bindValue(":p1change", $amountChange);
    $stmt->bindValue(":acc_type", $acc_type);
    $stmt->bindValue(":a1total", $a1total);
    //flip data for other half of transaction
    $stmt->bindValue(":p2a1", $account3);
    $stmt->bindValue(":p2a2", $account2);
    $stmt->bindValue(":p2change", ($amountChange*-1));
    $stmt->bindValue(":acc_type", $acc_type);
    $stmt->bindValue(":a2total", $a2total);
    $result = $stmt->execute();
    echo var_export($result, true);
    echo var_dump($account2, true);
    echo var_dump ($account3, true);
    echo var_export($stmt->errorInfo(), true);
    return $result;
}
?>
    <form method="POST">
        <input type="text" name="account2" placeholder="Account ID">
        <!-- If our sample is a transfer show other account field-->
        <?php if($_GET['acc_type'] == 'transfer') : ?>
            <input type="text" name="account3" placeholder="Other Account ID">
        <?php endif; ?>

        <input type="number" name="amount" placeholder="$0.00"/>
        <input type="hidden" name="type" value="<?php echo $_GET['acc_type'];?>"/>

        <!--Based on sample acc_type change the submit button display-->
        <input type="submit" value="Transfer Money"/>
    </form>

<?php
if(isset($_POST['acc_type']) && isset($_POST['account1']) && isset($_POST['amount'])){
    $type = $_POST['acc_type'];
    $amount = (int)$_POST['amount'];
    switch($acc_type){
        case 'deposit':
            do_bank_action("000000000000", $_POST['account1'], ($amount * -1), $acc_type);
            break;
        case 'withdraw':
            do_bank_action($_POST['account2'], "000000000000", ($amount * -1), $acc_type);
            break;
        case 'transfer':
            //TODO figure it out
            break;
    }
}
?>