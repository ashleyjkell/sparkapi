# sparkapi
Spark API Integration Example

## sparkIncident 
Checks if a ticket exists and fetches the title of that ticket 

Use: "php sparkIncident.php [INCIDENT NUMBER]"

## createSparkTicket 
Creates a Spark ticket from a routine change template, uses a file called "justification" in the docroot to fill in the Business Justification field 

Use: "php createSparkTicket [TEMPLATE] [SPARKUSER] [START EPOCH TIME] [END EPOCH TIME]"
