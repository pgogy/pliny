<?php
	
	session_start();

	unset($_SESSION['token']);

	session_destroy();

	echo file_get_contents("intro_ga.txt");

?>
	<title>Pliny - submitting Google Analytics to a Learning Registry Node
	</title>
</head>
<?PHP

	echo file_get_contents("post_title.txt"); 
	
?>
<h2>Logged out</h2>
<p>You have been logged out</p>
<p><a href="index.php">log in again</a></p>