<?php
/**
* This class ensures we have a valid change request for each project submitted
*
**/
require_once('SparkTicketStatus.php');

class ValidateSparkChangeRequests {

    private $projects = array();
    private $changeRequests = array();
    private $product = array();
    private $noTicketProjects = array();
    private $ticketTitle = array();
    private $badTickets = array();
    private $exitcode = "0";

    //As tickets are raised from templates they will always have the same title
    private $routineChangeTitles = array(
        'hub' => 'Sky Sports Digital Media: api.skysports.com app release',
        'clientapi' => 'Sky Sports Digital Media: api.skysports.com app release',
        'shared' => 'Sky Sports Digital Media project shared release',
        'shakira' => 'Shakira Java Release Routine',
        'db' => 'Shakira Java Release Routine',
        'cms-ng' => 'Sky Sports Content Management System (CMS)',
        'default' => 'Sky Sports Digital Media Release (Websites)'
    );

    public function setProjects(array $projects) {

        //Read input from main script
        $this->projects = $projects;
    }

    public function setChangeReqTickets(array $changeRequests) {

        //Read input from main script
        $this->changeRequests = $changeRequests;
    }

    public function validateChangeRequests() {

        $result = null;
        $this->getTicketStatus();

        //Output ticket statuses and any errors
        if (!empty($this->projects)){
            echo "\rProjects without a valid ticket: ". (implode(',',$this->projects)). "\r";
            $this->exitcode = "1";
        }

        foreach($this->product as $key => $value){
            echo "\r$key\r\t". (implode("\r\t",$value)). "\r";
        }

        foreach($this->badTickets as $key => $value){
            echo "\r$key \r\n\t$value\r";
        }

        if ($this->exitcode == "1"){
            echo "\rSpark Ticket Error.\r";
        }

        return $result;
    }

    private function getTicketStatus(){

        //Get status of each ticket
        foreach ($this->changeRequests as $ticket){
            $sts = new SparkTicketStatus();
            $sts->setSparkTicket($ticket);
            $this->ticketTitle[$ticket] = $sts->getCanBeImplemented();

            if (preg_match('/ERROR/',$this->ticketTitle[$ticket])){
                $this->badTickets[$ticket] = $this->ticketTitle[$ticket];
                $this->exitcode = "1";
            }
        }

        //Assign projects to relevant tickets
        foreach ($this->changeRequests as $ticket){
            $this->assignProjects($ticket);
        }
    }

    private function addTicket($ticket,$project,&$product) {

        if (!isset($product[$ticket])){
            $product[$ticket] = array();
        }

       $this->product[$ticket][] = $project;
       $this->removeProject($project);
    }

    private function removeProject($project){
        //Once we've assigned a project to a ticket, stop looking for a ticket to assign it to
        $key = array_search($project,$this->projects);
        if($key!==false){
            unset($this->projects[$key]);
        }
    }

    private function assignProjects($ticket){
                    
        foreach ($this->projects as $project){

            if (array_key_exists($project,$this->routineChangeTitles)) {
                if ($ticket == array_search($this->routineChangeTitles[$project],$this->ticketTitle)) {
                    //Cater for projects that need specific change requests
                    $this->addTicket($ticket,$project,$product);
                    $this->removeProject($project);
                    continue;
                }
            } 
            elseif ($ticket == array_search($this->routineChangeTitles['default'],$this->ticketTitle)) {
                //For tickets that are covered by the default change request
                $this->addTicket($ticket,$project,$product);
                $this->removeProject($project);
                continue;
            }
        }
    }
}