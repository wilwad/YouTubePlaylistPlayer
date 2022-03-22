<?php 
 /**
  * Class specific to a specific process
  */
 class App {
    private $settings;
    public $crud;  // database CRUD operations
    
    // class constructor 
    public function __construct($appsettings){
            $this->crud = new CRUD($appsettings->database_host, 
                                    $appsettings->database_user, 
                                    $appsettings->database_pwd, 
                                    $appsettings->database_name);
                                    
                $this->settings = $appsettings;
    }
    
    // handling contacts
    public function getContacts(){
            return $this->crud->readSQL($this->settings->sql_getcontacts);
    }
    public function createContact($postdata){
            return $this->crud->create( $this->settings->tables_contacts, $postdata);
    }
    public function updateContact($contactid, $postdata){
            return $this->crud->update( $this->settings->tables_contacts, $postdata, 'contact_id', $contactid);
    }
    public function deleteContact($contactid){
            return $this->crud->delete($this->settings->tables_contacts, 'contact_id', $contactid);
    }
    public function createLink($contactid, $clientid){
            return $this->crud->create($this->settings->tables_joined, ['contact_id'=>$contactid, 'client_id'=>$clientid]);
    }
    public function removeLink($linkid){
            return $this->crud->delete($this->settings->tables_joined, 'ccid', $linkid);
    }      
    public function getContactsLinkedToClient($clientid){
            $sql = $this->settings->sql_getcontactsforclient;
            $sql = str_replace('{clientid}', $clientid, $sql);               
            return $this->crud->readSQL($sql);
    }
    public function getContactsNotLinkedToClient($clientid){
        $sql = str_replace('{clientid}', $clientid, $this->settings->sql_getcontactsnotlinkedtoclient);            
        return $this->crud->readSQL($sql);   
    }
    public function getClientsLinkedToContact($contactid){
            $sql = $this->settings->sql_getclientsforcontact;
            $sql = str_replace('{contactid}', $contactid, $sql);               
            return $this->crud->readSQL($sql);
    }    
    public function getExistingClientCodes(){
            $existingclientcodes = [];
            // get existing client codes: race condition: if the client codes are updated in the database
            // after this SELECT statement returns (during SQL INSERT), that client code will not be here
            $table = $this->settings->tables_clients;
            $sql = "SELECT `client_code` FROM `$table`;";
            $ret = $this->crud->readSQL($sql);
            if ($ret['ok']){
                $rows = $ret['data']['rows'];
                foreach($rows as $row){
                    $existingclientcodes[] = $row['client_code'];
                }  
            }            
            return $existingclientcodes;
    }
    
    /*
        using uppercase characters only
        total of 6 characters: 3 alphja, 3 numeric
        First National Bank becomes: FNB001, where alpha: FNB, numeric: 001 by running through 001, 002, 003
        Protea becomes: PRO001, where alpha: PRO , numeric: 001
        If client name is shorter than 3 characters, fill chars with A-Z
        
        Unit tests:
        
        $test = ['First National Bank', 'Bank Windhoek', 'Telecom Namibia ltd', 'Protea', 'OK', 'A'];
        foreach($test as $val){
            echo "generateClientCode('$val') === " . $app->generateClientCode($val) . "<BR>";
        }
        
        Warning:
        possibility of a race condition in a multi-user environment 
    */   
    public function generateClientCode($clientname){
            $clientname  = trim($clientname);
            $clientlength = strlen($clientname);
            $clientname  = strtoupper($clientname); // uppercase
			$existingclientcodes = $this->getExistingClientCodes();
             
            if ($clientlength == 1) throw new Exception("Error: Client name length == 1");
            
            if ( $clientlength == 2 ){
                return $this->bruteforce($clientname, $existingclientcodes);  
            } 
            
            $parts = explode(' ', $clientname);
            $partslen = sizeof($parts);

            if ( $partslen == 1 ){
                // Protea == PRO
                $name = substr($clientname, 0,3);      
                
            } else if ( $partslen == 2){
                // first national == FN
                $firstletter  = substr($parts[0], 0, 1); //[F]irst
                $secondletter = substr($parts[1], 0, 1); //[N]ational
                $name = "$firstletter$secondletter";
                
            } else {
                // first national bank ltd == FNB
                $firstletter  = substr($parts[0], 0, 1); //[F]irst
                $secondletter = substr($parts[1], 0, 1); //[N]ational
                $thirdletter  = substr($parts[2], 0, 1); //[B]ank
                $name = "$firstletter$secondletter$thirdletter";
            }
            
            return $this->bruteforce($name, $existingclientcodes);
    }
    private function bruteforce($clientname, $existing){
                $alphabet    = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                $clientlen   = strlen(trim($clientname));
                $alphabetmax = strlen($alphabet);
                $numericmax  = 20;
                $clientcode  = '';

                if ($clientlen == 2){
                    // generate an extra letter for TN, OK
                    for($idx=0; $idx<$alphabetmax;$idx++){
                        $char = $alphabet[$idx]; // A-Z
                        
                        for($idy = 1; $idy < $numericmax; $idy++){
                            $formattedidy = str_pad($idy,3,'0', STR_PAD_LEFT);
                            $clientcode = "$clientname$char$formattedidy";
                            if (!in_array($clientcode, $existing)){
                                return $clientcode;
                            }
                        }
                    }
                } else {
                    // generate numbers only for MTC, CNA, FNB
                    for($idy = 1; $idy < $numericmax; $idy++){
                        $formattedidy = str_pad($idy,3,'0', STR_PAD_LEFT);
                        $clientcode = "$clientname$formattedidy";
                        if (!in_array($clientcode, $existing)){
                            return $clientcode;
                        }
                    }
                }
                
                throw new Exception("Error: bruteforce() failed to generate a client code!");
    }
    
    // handling clients
    public function getClients(){
            return $this->crud->readSQL($this->settings->sql_getclients);
    }
    public function createClient($postdata){
            return $this->crud->create( $this->settings->tables_clients, $postdata);
    }
    public function updateClient($clientid, $postdata){
            return $this->crud->update( $this->settings->tables_clients, $postdata, 'client_id', $clientid);
    }
    public function deleteClient($clientid){
            return $this->crud->delete($this->settings->tables_clients, 'client_id', $clientid);
    }
        
 } // class App end 
