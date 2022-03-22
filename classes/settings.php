<?php
/*
 * class to hold configuration data for the project
*/
class settings {
        public $title          = 'Clients App OOP';     
        public $showPHPerrors  = true;
        
        // database connection
        public $database_host  = 'localhost';
        public $database_name  = 'clientsapp';
        public $database_user  = 'root';
        public $database_pwd   = 'Admin.2015!';

         // tables
        public $tables_clients  = 'clients';
        public $tables_contacts = 'contacts';
        public $tables_joined   = 'clientscontacts';

         // errors
        public $error_noclients = "No client(s) found";
        public $error_nocontacts= "No contact(s) found";
        public $error_nolinkedcontacts= "No contacts found";
        public $error_nolinkablecontacts= "No contacts found that can be linked to this client.";
        public $error_duplicatelink = "The contact specified is already linked to this client.";

		// buttons
        public $buttons_clients    = "<a href='?view=clients'>All Clients</a>";
        public $buttons_contacts   = "<a href='?view=contacts'>All Contacts</a>";
        public $buttons_test       = "<a href='?view=test'>Client Code Generation Test</a>";
        public $buttons_newclient  = "<a href='?view=clients&action=add' class='button'>+ Add new Client</a><BR>";
        public $buttons_newcontact = "<a href='?view=contacts&action=add' class='button'>+ Add new Contact</a><BR>";
    		
		// HTML 
		public $html_author         = "<small class='float-right'>Create by AJN IT Solutions</small>";   
		public $html_clients_title  = "<h1 class='align-center'>All Clients</h1>";
		public $html_contacts_title = "<h1 class='align-center'>All Contacts</h1>";
        public $html_hr = '<HR>';
        public $html_slash = ' / ';
        public $html_p  = '<p></p>';  
        
        // actions
        public $html_actions_clients = "<a href='?view=clients&action=edit&id={clientid}'>Edit</a> 
						                <a href='#' onclick='return confirmDelete({clientid})'>Delete</a>
						                <a href='?view=clients&action=linked-contacts&id={clientid}'>Linked Contacts</a>";

		public $html_actions_contacts = "<a href='?view=contacts&action=details&id={contactid}'>Details</a>
										<a href='?view=contacts&action=edit&id={contactid}'>Edit</a> 
										<a href='#' onclick='return confirmDelete({contactid});'>Delete</a>";
												
        // font awesome icons
        public $icons_person = "<span class='fa fa-fw fa-user'></span>";
        public $icons_bank   = "<span class='fa fa-fw fa-bank'></span>";
        public $icons_link   = "<span class='fa fa-fw fa-link'></span>";
        public $icons_unlink = "<span class='fa fa-fw fa-unlink'></span>";

		// SQL
        public $sql_getclients  = "SELECT client_id, 
        								  name AS `Name`, 
        								  client_code AS `Client Code`, 
		                       			(SELECT 
		                       					COUNT(ccid) 
		                       			 FROM 
					                     	  clientscontacts cc 
		        			             WHERE 
		        			             	  cc.client_id=c.client_id
		        			             ) AS `No. of linked contacts` 
				                    FROM 
				                       		`clients` c 
				                    ORDER BY 
				                       		c.name 
				                    ASC;";
                            
        public $sql_getcontacts = "SELECT 
        								contact_id, 
        	                            CONCAT(c.surname, ' ', c.name) AS `Full Name`, 
			         					email_address As `E-mail`,
			                           (SELECT 
			                           		COUNT(ccid) 
			                           	FROM 
				                           clientscontacts cc 
                				        WHERE 
                				        	cc.contact_id=c.contact_id
                				        ) AS `No. of linked clients`
                           			FROM 
                           				`contacts` c 
                           			ORDER BY 
                           				`Full Name` 
                           			ASC;";
		
		public $sql_getcontactsnotlinkedtoclient = "SELECT 
														contact_id, 
														CONCAT(surname, ' ', name) AS `Full Name` 
													FROM 
														`contacts` c 
                									WHERE 
                										c.contact_id NOT IN 
                                                        (SELECT 
                                                                contact_id 
                                                            FROM 
                                                                clientscontacts cc 
                                                            WHERE 
                                                                cc.client_id={clientid}
                                                        ) 
                									ORDER BY 
                										`Full Name` 
                									ASC;";
                									
        public $sql_findlinkbetweenclientcontact = "SELECT 
                                                           * 
                                                    FROM 
                                                          `{table}` 
        											WHERE 
        											      client_id={clientid} 
        											AND 
        											      contact_id={contactid};";
        											
        public $sql_getcontactsforclient = "SELECT 
                                            	ccid, 
                                            	concat(c.surname,' ', c.name) as `Full Name`, 
                                            	`email_address` AS `E-mail`
											FROM 
											     contacts c, 
											     clientscontacts cc 
											WHERE 
											     cc.client_id={clientid} 
											AND 
											     cc.contact_id = c.contact_id;";       
												 
		public $sql_getclientsforcontact = "SELECT 
												 c.client_id, 
												 c.name As `Client Name`
											 FROM 
												  clients c, 
												  clientscontacts cc 
											 WHERE 
												  cc.client_id=c.client_id
											 AND 
												  cc.contact_id = {contactid};";       												 
 }
