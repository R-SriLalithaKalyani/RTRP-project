<?php
require_once("db_connect.php");

if (isset($_POST['uid'])) {
    $uid = $_POST['uid'];
    
    // Check if UID exists
    $query = "SELECT uid FROM users WHERE uid = '$uid'";
    $result = mysql_query($query);

    if (mysql_num_rows($result) > 0) {
        echo "exists";
    } else {
        echo "available";
    }
}
?>
