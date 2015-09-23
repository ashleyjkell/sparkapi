<?php
/*****************************************************************
*
*       Entry point for creating new Spark tickets
*
*****************************************************************/

require_once('SparkService.php');

$sparkService = new SparkService();
$docroot = realpath(dirname(__FILE__));

if ( !isset($argv[1])||
    !isset($argv[2])||
    !isset($argv[3])||
    !isset($argv[4]) 
   ) 
	{
    	echo "ERROR: One or more arguments missing.";
    	exit(1);
    }

$template = $argv[1];;
$user = $argv[2];;
$start = $argv[3];;
$end = $argv[4];;

//Insert the whole change list as the Spark ticket justification if we are raising a standard change request
//Insert only the relevant changes if the project has it's own specific change required

if ($template == "RTN0000338" ) {
    $justification= file_get_contents($docroot.'/cleancontent');
}else{
    $justification= file_get_contents($docroot.'/justification');
}

$changeNumber = $sparkService->postTicketData($template,$justification,$user,$start,$end);

echo $changeNumber;

?>