<?php
	
	session_start();

	echo file_get_contents("intro_ga.txt");

?>
	<title>Pliny - submitting Google Analytics to a Learning Registry Node
	</title>
</head>
<?PHP

	echo file_get_contents("post_title.txt");	
	
	$xml = $_SESSION[urlencode($_GET['url'])];

	$content_info = array();
	$content_info['resource_locator'] = $_GET['url']; 
	$content_info['resource_data_type'] = 'paradata'; 
	$content_info['submitter'] = "pliny"; 
	if($_GET['tos']==""){
		$tos = "None provided";
	}else{
		$tos = $_GET['tos'];
	}
	$content_info['tos'] = $tos; 
	$content_info['curator'] = "pliny"; 
	$content_info['active' ] = TRUE; 
	$content_info['payload_schema'] = 'paradata'; 
	
	$opt_id_fields = array(
		'curator',
		'owner',
		'signer',
	);

	$opt_res_fields = array(
		'submitter_timestamp',
		'submitter_TTL',
		'keys',
		'resource_TTL',
		'payload_schema_locator',
		'payload_schema_format',
	);

	$opt_sig_fields = array(
		'signature',
		'key_server',
		'key_locations',
		'key_owner',
		'signing_method',
	);

	$opt_tos_fields = array(
		'tos_submission_attribution',
	);
	
	// Make some parts of the PHP data structure
	
	$identity = new StdClass;
	$resource_data = new StdClass;
	
	$identity->submitter_type = 'pliny';
	$identity->submitter = $content_info['submitter'];
	
	$tos = new StdClass;

	$tos->submission_TOS = $content_info['tos'];

	// Optional identity values.
	foreach ($opt_id_fields as $field) {
		if (array_key_exists($field, $content_info)) {
			$identity->$field = $content_info[$field];
		}
	}

	// Optional resource_data values.
	foreach ($opt_res_fields as $field) {
		if (array_key_exists($field, $content_info)) {
			$resource_data->$field = $content_info[$field];
		}
	}

	// Optional TOS values.
	foreach ($opt_tos_fields as $field) {
		if (array_key_exists($field, $content_info)) {
			$tos->$field = $content_info[$field];
		}
	}
	
	// Now the data structure is sort of finished, so add in some extra bits

	$resource_data->doc_type = 'resource_data';
	$resource_data->doc_version = '0.23.0';
	$resource_data->resource_data_type = $content_info['resource_data_type'];
	$resource_data->active = $content_info['active'];
	$resource_data->identity = $identity;
	$resource_data->TOS = $tos;
	
	if($_GET['keys']==""){
		$keys = "None provided";
	}else{
		$keys = $_GET['keys'];
	}
	
	$resource_data->keys = explode(",",$keys);

	$resource_data->resource_locator = $content_info['resource_locator'];
	$resource_data->payload_placement = 'inline';
	$resource_data->payload_schema = array($content_info['payload_schema']);

	if(isset($_GET['verb'])){ 
		$verb = $_GET['verb']; 
	}else{ 
		$verb = 'visits'; 
	}

	$resource_data->resource_data = '{
	"activity": {
		"verb": {
			"action": "' . $verb . '",
			"date": "' . $_GET['end'] . '",
			"measure": {
				"sampleSize":"' . $_GET['visits'] . '"
			},
			"context": {
				"id": "' . $_GET['url'] . '",
				"description": "' . $_GET['title'] . '"
      			}
		}
	}
}';

	$submission = new StdClass;
	
	$submission->documents[] = $resource_data;

	$data_to_send = json_encode($submission);

	// Curl is some PHP stuff to send data across the interwebs
	
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_URL, /*ENTER THE URL HERE*/);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 50); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 50); 
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_to_send);
	
	//CURL exec sends the data
	
	$result = curl_exec($ch);
	
	//Traps the error if something bad happens
	
	$error = curl_error($ch);
	curl_close($ch);
	
	// Convert the data from LR json back into PHP so we can check it
	
	$result = json_decode($result);

	echo "<h2>Document submission for " . $_GET['url'] . "</h2>";

	if($result->document_results[0]->{'OK'}==1){

		echo "<p>Your document has been submitted - <a target='_blank' href='http://alpha.mimas.ac.uk/obtain?by_doc_ID=true&request_ID=" . $result->document_results[0]->{'doc_ID'} . "'> ID #" . $result->document_results[0]->{'doc_ID'} . "</a></p>";
		echo "<p>You may now close this window</p>";

	}else{

		echo "<p>There was an error submitting</p>";

	}