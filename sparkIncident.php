<?php
/**
 * This class quickly checks if a Spark incident exists and fetches the title, this is used for creating release notes
 *
 **/
require_once('SparkService.php');

$sparkService = new SparkService();
$json = $sparkService->getTicketData($sparkService::INCIDENT,$argv[1]);

//Check if key fields exist
if (!isset($json["records"]) ||
	!isset($json["records"]["0"]) || 
	!isset($json["records"]["0"]["active"]) ||
	$json["records"]["0"]["active"] != 'true')
	{
		echo "ERROR: One or more fields missing from returned record.";
		exit(1);
	}

echo $json["records"]["0"]["short_description"] ;
exit(0);
?>