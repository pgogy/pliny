<?php
	
	require_once 'google-api-php-client/src/apiClient.php';
	require_once 'google-api-php-client/src/contrib/apiAnalyticsService.php';
	session_start();

	$client = new apiClient();
	$client->setApplicationName("Analytics Paradata");

	// Visit https://code.google.com/apis/console?api=analytics to generate your
	// client id, client secret, and to register your redirect uri.
	$client->setClientId(/*ENTER CLIENT ID HERE - YOU NEED TO MAKE A GOOGLE API ACCOUNT*/);
	$client->setClientSecret(/*ENTER CLIENT SECRET HERE - YOU NEED TO MAKE A GOOGLE API ACCOUNT*/);
	$client->setRedirectUri(/*ENTER HTTPS REDIRECT URL HERE - YOU NEED TO MAKE A GOOGLE API ACCOUNT*/);
	$client->setDeveloperKey(/*ENTER DEVELOPER KEY HERE - YOU NEED TO MAKE A GOOGLE API ACCOUNT*/);
	$service = new apiAnalyticsService($client);

if (isset($_GET['code'])) {
	$client->authenticate();
	$_SESSION['token'] = $client->getAccessToken();
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
	$client->setAccessToken($_SESSION['token']);
}


	echo file_get_contents("intro_ga.txt");

?>
	<title>
		Pliny - submitting Google Analytics to a Learning Registry Node
	</title>
</head>
<?PHP

	echo file_get_contents("post_title.txt");
	echo file_get_contents("menu.txt");

if ($client->getAccessToken()) {

	$accounts = $service->management_accounts->listManagementAccounts();

	if (count($accounts['items']) > 0) {

		echo "<h3>Choose an account you wish to submit for</h3>";
		echo "<p>Pick an account from the list below and then opt for 'Set the dates to provide data for'</p>";

		$items = $accounts['items'];

		while($item = array_shift($items)){

			$firstAccountId = $item['id'];

			if(trim($item['name'])!=""){

				$name = $item['name'];

			}else{

				$name = "Name not set";

			}

			echo "<p><strong>" . $name . "</strong></p><ul style='list-style:none'>";

			$webproperties = $service->management_webproperties
			->listManagementWebproperties($firstAccountId);

			if (count($webproperties['items']) > 0) {

				$inner_items = $webproperties['items'];

				while($inner_item = array_shift($inner_items)){ 

					$firstWebpropertyId = $inner_item['id'];

					$profiles = $service->management_profiles
					->listManagementProfiles($firstAccountId, $firstWebpropertyId);

					if (count($profiles['items']) > 0) {

						$profiles = $profiles['items'];

						while($profile = array_shift($profiles)){  

							echo "<li>" . $profile['websiteUrl'] . " | ";

							echo "<a href='time.php?id=" . $profile['id'] . "&url=" . urlencode($profile['websiteUrl']) . "'>Set dates to provide data for</a></li>";

						}

					}

				}

				echo "</ul>";

			}

		}

	} else {
		throw new Exception('No accounts found for this user.');
	}

	$_SESSION['token'] = $client->getAccessToken();
} else {
	$authUrl = $client->createAuthUrl();
	print "<a class='login' href='$authUrl'>Connect Me!</a>";
}