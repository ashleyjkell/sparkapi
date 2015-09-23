<?php
/**
 * This class creates the connections to Spark for getting ticket details or posting new tickets
 *
 **/
class SparkService {

    const INCIDENT = '30';
    const CHANGE = '16';
    const NEWTICKET = '10';
    private $postStr = "";
    private $data = array();
    private $ch;

    private function callSpark() {

        $this->curlOpts();
        return json_decode(substr(curl_exec($this->ch),3), true);
    }

    public function getTicketData($service,$ticketNumber) {

        if (!isset($service) || !isset($ticketNumber)) {
            return null;
        }

        //Create an array of the data necessary for fetching ticket data
        $this->data['service'] = $service;
        $this->data['ticket_number'] = $ticketNumber;

        $this->buildPostString();

        return $this->callSpark();
    }

    private function curlOpts(){

        //Set our default Curl options for interacting withSpark
        $this->ch = curl_init();
        $sparkHost = "SPARKIP_SEE_API_DOCUMENTATION";
        $sparkPort = "8000";
        $sparkUrl = $sparkHost.':'.$sparkPort;

        curl_setopt($this->ch, CURLOPT_URL, $sparkUrl);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, substr($this->postStr, 0, -1));
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
    }

    private function postSpark() {

        $this->curlOpts();

        //POST specific Curl options
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/plain;charset=ascii',
            'Expect: ',));

        //Get the status code of the request. If it's 200, return the ticket number. If it's anything else, return the full error message
        $loopCount = 0;

        do {
            $result = explode(' ',trim((curl_exec($this->ch))));
            $statusCode = $result[0];

            $loopCount++;

            if (isset($result[0])){
                if ($result[0] != "200"){
                    $result = (implode(" ", (array_slice($result, 1))));
                }else{
                    $result = $result[1];
                }
            }else{
                $result = "Error when creating Spark ticket, unable to get API status code.";
                break;
            }
        } while ($statusCode != "200" && $loopCount < 5);

        if ($loopCount >= 5) {
                $result = "Error when creating Spark ticket, unable to connect to Spark API.";
        }

        return $result;

    }

    public function postTicketData($template,$justification,$user,$start,$end) {

        //Create an array of the data necessary for creating tickets 
        $this->data['service'] = $this::NEWTICKET;
        $this->data['template'] = $template;
        $this->data['business_justification'] = $justification;
        $this->data['user_name'] = $user;
        $this->data['start_date'] = $start;
        $this->data['end_date'] = $end;

        $this->buildPostString();

        return $this->postSpark();
    }

    private function buildPostString(){

        //Format the string correctly for Curl
        foreach($this->data as $key=>$val) {
            $this->postStr .= $key.'='.urlencode($val).'&';
        }
    }
}
?>