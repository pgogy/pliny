<?php
	
	require_once 'google-api-php-client/src/apiClient.php';
	require_once 'google-api-php-client/src/contrib/apiAnalyticsService.php';
	session_start();

	echo file_get_contents("intro_ga.txt");

?>
	<title>Pliny - submitting Google Analytics to a Learning Registry Node
	</title>
</head>
<?PHP

	echo file_get_contents("post_title.txt");

	$client = new apiClient();
	$client->setApplicationName("Analytics Paradata");

	// Visit https://code.google.com/apis/console?api=analytics to generate your
	// client id, client secret, and to register your redirect uri.
	$client->setClientId(/*ENTER CLIENT ID HERE - YOU NEED TO MAKE A GOOGLE API ACCOUNT*/);
	$client->setClientSecret(/*ENTER CLIENT SECRET HERE - YOU NEED TO MAKE A GOOGLE API ACCOUNT*/);
	$client->setRedirectUri(/*ENTER HTTPS REDIRECT URL HERE - YOU NEED TO MAKE A GOOGLE API ACCOUNT*/);
	$client->setDeveloperKey(/*ENTER DEVELOPER KEY HERE - YOU NEED TO MAKE A GOOGLE API ACCOUNT*/);
	$service = new apiAnalyticsService($client);

	if (isset($_GET['logout'])) {
		unset($_SESSION['token']);
	}
?>
	<H3>Welcome to Pliny</h3>
	<p>
		Pliny is a tool designed to allow for paradata around educational resources to be extracted from Google Analytics and then submitted to a Learning Registry Node. At present this page 
only works with the Mimas Jlern Node</p>
	<p>
		To use this tool you will need to have a Google Analytics account for the resources you wish to submit for, else this code can't work for you. Sorry :(
</p>
<p>
	If you want to continue, then be aware this tool uses OAuth to access Google Analytics, and won't remember any of your details.
	</p>
<?PHP

	$authUrl = $client->createAuthUrl();
	print "<a class='login' href='$authUrl'>Connect to Google Analytics and start the process.</a>";