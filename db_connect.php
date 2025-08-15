<?php
    function mysql_query($sql) {
        $con = mysqli_connect("localhost","root","", "green");
		if (!$con) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }
        
        $result = mysqli_query($con, $sql);
        return $result;
	}
	
	function mysql_num_rows($result) {
	    return mysqli_num_rows($result);   
	}
	
	function mysql_fetch_assoc($result) {
	    return mysqli_fetch_assoc($result);
	}
	
	function mysql_fetch_array($result) {
	    return mysqli_fetch_array($result);
	}
	
	function mysql_fetch_row($result) {
	    return mysqli_fetch_row($result);
	}
?>
