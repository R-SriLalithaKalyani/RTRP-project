<?php
	session_start();
	session_destroy();
	header("Location: home.html");
?>
<script>window.close();</script>