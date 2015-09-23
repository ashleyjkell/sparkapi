<?php
/**
 * Entry point for validating change requests and getting their titles
 *
 **/
require_once('ValidateSparkChangeRequests.php');

if ( !isset($argv[1])||
    !isset($argv[2])
   ) 
	{
    	echo "ERROR: One or more arguments missing.";
    	exit(1);
    }
$vscr = new ValidateSparkChangeRequests();
$vscr->setProjects(explode(',',$argv[1]));
$vscr->setChangeReqTickets(explode(',',$argv[2]));
$vscr->validateChangeRequests();


?>