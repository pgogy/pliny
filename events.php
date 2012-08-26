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
		$start_date = $_GET['_eventstartYear'] . "-" . str_pad($_GET['_eventstartMonth'], 2, "0", STR_PAD_LEFT) . "-" . str_pad($_GET['_eventstartDay'], 2, "0", STR_PAD_LEFT);
		$end_date = $_GET['_eventendYear'] . "-" . str_pad($_GET['_eventendMonth'], 2, "0", STR_PAD_LEFT) . "-" . str_pad($_GET['_eventendDay'], 2, "0", STR_PAD_LEFT);
		$metrics = "ga:uniqueevents,ga:eventvalue";
		$dimensions = "ga:pagepath,ga:pagetitle,ga:eventcategory,ga:eventaction,ga:eventlabel";
		$optParams = array('dimensions' => $dimensions);

		$data = $service->data_ga->get($ids,$start_date,$end_date,$metrics,$optParams);

		$pages = $data['rows'];

		$pages_bkp = $pages;

		$data_set = array();

		if($_GET['ga_event']=="action"){

			$page_index = 3;

		}else{

			$page_index = 2;

		}

		if(count($pages)!=0){

			while($page = array_shift($pages)){

				if(!isset($data_set[$page[0]][$page[$page_index]])){

					$data_set[$page[0]][$page[$page_index]] = 1;

				}else{

					$data_set[$page[0]][$page[$page_index]]++;

				}
		
			}

			?>
				<h2>
					Choose a page and the events you wish to submit
				</h2>
				<p>
					Below is a list of the pages Google Analytics has data for for the date range provided. Choose the page and associated event you are looking to submit data for and then click 'submit'.
				</p>
			<?PHP

				echo "<div style='float:left; width:100%; clear:both; border-bottom:2px solid black; font-weight:bold'><div style='float:left; width:30%; padding:10px 10px 10px 0px;'>URL</div><div style='float:left; width:40%; padding:10px 10px 10px 0px'>Title</div><div style='float:left; width:25%; padding:10px 10px 10px 0px'>Events" . 
					"</div></div>";

		while($page = array_shift($pages_bkp)){

			if(isset($data_set[$page[0]])){

				echo "<div style='float:left; width:100%; clear:both; border-bottom:1px solid #444;'><div style='float:left; width:30%; padding:10px 10px 10px 0px;'>";

				echo $_GET['url'] . $page[0] . "</div><div style='float:left; width:40%; padding:10px 10px 10px 0px'>" . $page[1] . "</div>";

				$data = $data_set[$page[0]];

				echo "<ul>";

				foreach($data as $key => $value){

					echo "<li>" . $key . " " . $value . " (times) <a target='_blank' href='submit.php?verb=" . $key . "&url=" . urlencode($_GET['url'] . $page[0]) . "&title=" . $page[1] . "&visits=" . $value . "&start=" . $start_date . "&end=" . $end_date . "'>Submit</a> </li>";

				}

				echo "</ul>";

				echo "</div>";

			}

			unset($data_set[$page[0]]);

		}

		$_SESSION['token'] = $client->getAccessToken();

		}else{

			echo "<p>No events found for this range - <a href='time.php?id=" . $_GET['id'] . "&url=" . $_GET['url'] . "'>Set new a date range</a></p>";

		}

	}else{
		$authUrl = $client->createAuthUrl();
		print "<a class='login' href='$authUrl'>Connect Me!</a>";
	}