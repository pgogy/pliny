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
	echo file_get_contents("menu.txt");
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

if (isset($_GET['code'])) {
	$client->authenticate();
	$_SESSION['token'] = $client->getAccessToken();
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['token'])) {
	$client->setAccessToken($_SESSION['token']);
}

if ($client->getAccessToken()) {

	$ids = "ga:" . $_GET['id'];
	$start_date = $_GET['_startYear'] . "-" . str_pad($_GET['_startMonth'], 2, "0", STR_PAD_LEFT) . "-" . str_pad($_GET['_startDay'], 2, "0", STR_PAD_LEFT);
	$end_date = $_GET['_endYear'] . "-" . str_pad($_GET['_endMonth'], 2, "0", STR_PAD_LEFT) . "-" . str_pad($_GET['_endDay'], 2, "0", STR_PAD_LEFT);
	$metrics = "ga:pageviews";
	$dimensions = "ga:pagepath,ga:pagetitle";
	$optParams = array('dimensions' => $dimensions);

?>
<h2>
	Choose a page
</h2>
<p>
	Below is a list of the pages Google Analytics has data for for the date range provided. Choose the page you are looking to submit data for and then click 'submit'.
</p>
<?PHP

	echo "<div style='float:left; width:100%; clear:both; border-bottom:2px solid black; font-weight:bold'><div style='float:left; width:30%; padding:10px 10px 10px 0px;'>URL</div><div style='float:left; width:40%; padding:10px 10px 10px 0px'>Title</div><div style='float:left; width:10%; padding:10px 10px 10px 0px'>Visits" . 
	"</div><div style='float:left; width:10%; padding:10px 10px 10px 0px'>Submit</div></div>";

	$data = $service->data_ga->get($ids,$start_date,$end_date,$metrics,$optParams);

	$pages = $data['rows'];

	while($page = array_shift($pages)){

		echo "<div style='float:left; width:100%; clear:both;'><div style='float:left; width:30%; padding:10px 10px 10px 0px;'><a target='_blank' href='" . $_GET['url'] . $page[0] . "'>" . $_GET['url'] . $page[0] . "</a></div><div style='float:left; width:40%; padding:10px 10px 10px 0px'>" . $page[1] . "</div><div style='float:left; width:10%; padding:10px 10px 10px 0px'>" . $page[2] . " " . 
		"</div><div style='float:left; width:10%; padding:10px 10px 10px 0px'><a target='_blank' href='submit.php?url=" . urlencode($_GET['url'] . $page[0]) . "&title=" . $page[1] . "&visits=" . $page[2] . "&start=" . $start_date . "&end=" . $end_date . "'>Submit</a></div></div>";

	}

	$_SESSION['token'] = $client->getAccessToken();

} else {

	$authUrl = $client->createAuthUrl();
	print "<a class='login' href='$authUrl'>Connect Me!</a>";

}