<?php
require("common.inc.php");
try{
    $query = file_get_contents(__DIR__ . "/queries/create_table_accounts.sql");
    if(isset($query) && !empty($query)) {
        $stmt = getDB()->prepare($query);
        $r = $stmt->execute();
        $e = $stmt->errorInfo();
        if($e[0] == "00000"){
            echo "Table created successfully or already exists";
        }
        else{
            echo "Error creating table";
            echo "<pre>" . var_export($e, true) . "</pre>";
        }
    }
    else{
        echo "Failed to find create_table_accounts.sql file";
    }
}
catch (Exception $e){
    echo $e->getMessage();
}
?>