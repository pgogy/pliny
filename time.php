<?php
	
	session_start();

	echo file_get_contents("intro_ga.txt");

?>
	<title>Pliny - submitting Google Analytics to a Learning Registry Node
	</title>
</head>
<?PHP

	echo file_get_contents("post_title.txt");
	echo file_get_contents("menu.txt"); 
	
?>
	<p>
		Now choose the date range you wish to submit for - If you have Google Analytics "events" you'd like to submit as different verbs then use the bottom form. If you just want to submit visitor data then please use the top form
	</p>
	<form action="list.php" method="GET" style="border-bottom:1px dashed #333; padding-bottom:20px; margin-bottom:20px">
	<h2>
		Submitting visits
	</h2>
<?PHP

	FUNCTION DateSelector($inName, $useDate=0) 
	{ 
		/* create array so we can name months */ 
		$monthName = ARRAY(1=> "January", "February", "March", 
			"April", "May", "June", "July", "August", 
			"September", "October", "November", "December"); 
 
		/* if date invalid or not supplied, use current time */ 
		IF($useDate == 0) 
		{ 
			$useDate = TIME(); 
		} 

		/* make month selector */ 
		ECHO "<SELECT NAME=" . $inName . "Month>\n"; 
		FOR($currentMonth = 1; $currentMonth <= 12; $currentMonth++) 
		{ 
			ECHO "<OPTION VALUE=\""; 
			ECHO INTVAL($currentMonth); 
			ECHO "\""; 
			IF(INTVAL(DATE( "m", $useDate))==$currentMonth) 
			{ 
				ECHO " SELECTED"; 
			} 
			ECHO ">" . $monthName[$currentMonth] . "\n"; 
		} 
		ECHO "</SELECT>"; 
 
		/* make day selector */ 
		ECHO "<SELECT NAME=" . $inName . "Day>\n"; 
		FOR($currentDay=1; $currentDay <= 31; $currentDay++) 
		{ 
			ECHO "<OPTION VALUE=\"$currentDay\""; 
			IF(INTVAL(DATE( "d", $useDate))==$currentDay) 
			{
				ECHO " SELECTED"; 
			} 
			ECHO ">$currentDay\n"; 
		} 
		ECHO "</SELECT>"; 
 
		/* make year selector */ 
		ECHO "<SELECT NAME=" . $inName . "Year>\n"; 
		$startYear = DATE( "Y", $useDate); 
		FOR($currentYear = $startYear - 5; $currentYear <= $startYear+5;$currentYear++) 
		{ 
			ECHO "<OPTION VALUE=\"$currentYear\""; 
			IF(DATE( "Y", $useDate)==$currentYear) 
			{ 
				ECHO " SELECTED"; 
			} 
			ECHO ">$currentYear\n"; 
		} 
		ECHO "</SELECT>"; 
 
	}

	DateSelector("_start",time()-(31*86400));
	echo "<br />";
	DateSelector("_end");
    
?>
		<input type="hidden" name="id" value="<?PHP echo $_GET['id']; ?>" />
		<input type="hidden" name="url" value="<?PHP echo $_GET['url']; ?>" /><br />
		<input type="submit" value="View" />
	</form>
	<h2>
		Submitting events
	</h2>
	<form action="events.php" method="GET"><?PHP 

		DateSelector("_eventstart",time()-(31*86400));
		echo "<br />";
		DateSelector("_eventend");

	?><br />
	<select name="ga_event">
		<option value="category">Event Category</option>
		<option value="action">Event Action</option>
	</select>
	<input type="hidden" name="id" value="<?PHP echo $_GET['id']; ?>" />
	<input type="hidden" name="url" value="<?PHP echo $_GET['url']; ?>" /><br />
	<input type="submit" value="View" />
</form>
