<?php
 /*
 * handling of the clients operations
 */
 // get the icon
 $icon_person = $settings->icons_person;
 $icon_link   = $settings->icons_link;
 $icon_unlink = $settings->icons_unlink;
 
 // when you click a Submit button on the forms
 // called during add
if (isset($_POST['add'])){
    unset($_POST['add']); // remove the submit button otherwise it will be added to the SQL

    // the client code is generated manually
    $clientname = $_POST['name'];
    $generatedclientcode = $app->generateClientCode( $clientname );
    $_POST['client_code'] = $generatedclientcode;
    
    $ret = $app->createClient( $_POST );
    if ($ret['ok']){
        // go to the clients area
        die("<script>window.location.href='?view=clients';</script>");

    } else {
        die('Error: ' . $ret['error']);
    }
}
// called during edit
if (isset($_POST['edit'])){
    unset($_POST['edit']); // remove the submit button otherwise it will be added to the SQL

    $clientid = $actionid;
    $ret = $app->updateClient( $clientid, $_POST );
    if ($ret['ok']){
        die("<script>window.location.href='?view=clients';</script>"); // navigate the page to this URL
    } else {
        die('Error: ' . $ret['error']);
    }    
}

// handling of ?view=clients&action=
// delete|add|edit|linked-contacts|link-delete|link-contact|link-contact-select|no_parameter
 switch ( $action ){
        case 'add': // add a new client
            $mydb = new CForm( $settings->database_host,
                                $settings->database_user,
                                $settings->database_pwd, 
                                $settings->database_name );

            // show a data editing form on the screen without any data
            $ret = $mydb->generateForm( $settings->tables_clients);        
            if ( $ret['ok'] ){ 
                $body     = [];
                $ignored  = ['client_id', 'client_code'];        
                $required = ['name']; // these values in the array will have the required property added to the input field
                foreach( $ret['data'] as $id=>$v ){
                        $c = @$comments[$id] ? $comments[$id] : ucwords($id);
                        $req = in_array($id, $required) ? "required='required'" : "";
                        $requiredstar = $req ? "<span class='color_red'>*</span>" : '';
                        if (in_array($id, $ignored)) continue;
        
                        $data = "<input type='text' $req name='$id' id='$id' value=\"$v\">";
                        $body[] = "<p><label for='$id'>$c $requiredstar</label><BR>$data</p>";
                }        
                $body = implode('', $body); // flatten the array
                
                echo $settings->html_p;
                echo "<form method='post' enctype='multipart/form-data'>
                        <fieldset>
                            <legend>Client Details</legend>
                            $body
                            <input type='submit' name='add'value='Add Record'>
                        </fieldset>
                        </form>
                        <p>&nbsp;</p>
                        ";
        
            } else {
                echo 'Error: ' . $ret['error'];
            }  
            break;
            
        case 'edit': // edit the client
            $mydb = new CForm(  $settings->database_host,
                                $settings->database_user,
                                $settings->database_pwd, 
                                $settings->database_name);
                                
            // show a data editing form on the screen filled with data for client with client_id = $id
            $ret = $mydb->generateForm( $settings->tables_clients,'client_id', $actionid);
            if  ( $ret['ok'] ){ 
                $body     = [];
                $ignored  = ['client_id','client_code'];        
                $required = ['name'];
                foreach($ret['data'] as $id=>$v){
                        $c = @$comments[$id] ? $comments[$id] : ucwords($id);
                        $req = in_array($id, $required) ? "required='required'" : "";
                        $requiredstar = $req ? "<span class='color_red'>*</span>" : '';
                        if (in_array($id, $ignored)) continue;
        
                        $data = "<input type='text' $req name='$id' id='$id' value=\"$v\">";

                        $body[] = "<p><label for='$id'>$c $requiredstar</label><BR>$data</p>";
                }        
                $body = implode('', $body); // flatten this array
        
                echo "<form method='post' enctype='multipart/form-data'>
                        <fieldset>
                            <legend>Edit Client</legend>
                            $body
                            <input type='submit' name='edit' value='Update Record'>               
                        </fieldset>
                        </form>
                        <p>&nbsp;</p>
                        ";
        
            } else {
                echo 'Error: ' . $ret['error'];
            }    
            break;

        case 'delete': // delete the client
            $result = $app->deleteClient($actionid);
            if ( !$result['ok'] ){
                echo 'Error: ' . $result['error'];
            } else {
                die("<script>window.location.href='?view=clients';</script>");
            }
            break;
            
        case 'linked-contacts': // show all the contacts linked to the client        
            // get details of this client first to show on the form
            $result = $app->crud->read($settings->tables_clients, 'client_id', $actionid);
            if ( !$result['ok'] ){
                die("Client with id $actionid was not found");
                
            } else {
                $name = $result['data']['rows'][0]['name'];// only 1 record
                echo "<h2>Contacts linked to $name</h2>  
                      <a href='?view=client&action=link-contact-select&id=$actionid' class='button'>$icon_link Link a new Contact</a>";
                echo $settings->html_p;
            }

            $sql = $settings->sql_getcontactsforclient;
            $sql = str_replace('{clientid}', $actionid, $sql);            
            $result = $app->crud->readSQL($sql);
            if ( !$result['ok'] ){
                 echo $settings->error_nolinkedcontacts;
                 
            } else {

                $cols = $result['data']['cols'];
                $rows = $result['data']['rows'];
                $th = ''; $td = '';

                foreach($cols as $col){
                    if ($col == 'ccid') continue;
                    $th .= "<th>$col</th>";
                }
                // extra th
                $th .= "<th>Actions</th>";
			
                foreach($rows as $row){
                    $td .= "<tr>";
                    foreach($cols as $col){
                        if ($col == 'ccid') continue;
                        $val = $row[$col];
                        if ($col == 'Full Name') $val = "$icon_person $val";
                        $td .= "<td>$val</td>";
                    }       
                    // actions 
                    $ccid = $row['ccid'];
                    $action = "<a href='?view=client&action=link-delete&id=$actionid&ccid=$ccid'>Unlink this Contact</a>";
                    $td .= "<td>$action</td>";
                    $td .= "</tr>";
                }    
                echo "<table>";
                echo "<thead><tr>$th</tr></thead>";
                echo "<tbody>$td</tbody>";
                echo "</table>";

            }   
            break;     

        case 'link-contact-select': // choose a contact to link to this client
            // get details of this client
            $result = $app->crud->read($settings->tables_clients, 'client_id', $actionid);
            if ( !$result['ok'] ){
                die("Client with id $actionid was not found");

            } else {
                // only 1 record is returned, so it will be at $result['data']['rows'][0]
                $name = $result['data']['rows'][0]['name']; 
                echo "<h2>Select a contact to link to $name</h2>";
                echo $settings->html_p;
            }

            // show all contacts not linked to this client
            $result = $app->getUnlinkedContactsForClient($actionid);
            if (!$result['ok']){
                echo $settings->error_nolinkablecontacts;
                    
            } else {

                $cols = $result['data']['cols'];
                $rows = $result['data']['rows'];
                $th = ''; $td = '';

                foreach($cols as $col){
                    if ($col == 'contact_id') continue;
                    $th .= "<th>$col</th>";
                }
                // extra th
                $th .= "<th>Actions</th>";
                
                foreach($rows as $row){
                    $td .= "<tr>";
                    foreach($cols as $col){
                        if ($col == 'contact_id') continue;
                        $val = $row[$col];
                        if ($col == 'Full Name') $val = "$icon_person $val";
                        $td .= "<td>$val</td>";
                    }       
                    // actions 
                    $id = $row['contact_id'];
                    $td .= "<td><a href='?view=$view&action=link-contact&contactid=$id&clientid=$actionid'>Link this contact</a></td>";
                    $td .= "</tr>";
                }    
                echo "<table>";
                echo "<thead><tr>$th</tr></thead>";
                echo "<tbody>$td</tbody>";
                echo "</table>";
            }   
            break;   
        
        case 'link-contact': // link the chosen contact to this client
            $contactid = (int) @ $_GET['contactid'];
            $clientid = (int) @ $_GET['clientid'];

            // make sure we don't have an existing entry for this
            $sql = $settings->sql_findlinkbetweenclientcontact;
            
            // replace the placeholders in the SQL string with real values
            $sql = str_replace('{table}', $settings->tables_joined, $sql);
            $sql = str_replace('{clientid}', $clientid, $sql);
            $sql = str_replace('{contactid}', $contactid, $sql);
                            
            $result = $app->crud->readSQL( $sql );
            if (!$result['ok']){
                echo 'Error: ' . $result['error'];
            } else {
                if ( $result['total_rows']){
                    die($settings->error_duplicatelink);
                }
            }

            $result = $app->createLink($contactid,$clientid);
            if (!$result['ok']){
                echo 'Error: ' . $result['error'];
            } else {
                // show the current linked contacts for this client
                $url = "<script>window.location.href='?view=clients&action=linked-contacts&id=$clientid';</script>";
                die($url);
            }
            break;
                    
        case 'link-delete':// delete the link between the client and contact
            $ccid = (int) @ $_GET['ccid'];
            $clientid= (int) @ $_GET['id'];
            
            $result = $app->removeLink($ccid);
            if (!$result['ok']){
                echo 'Error: ' . $result['error'];
            } else {
                // show the current linked contacts for this client
                $url = "<script>window.location.href='?view=clients&action=linked-contacts&id=$clientid';</script>";
                die($url);
            }
            break;
                    
      default: // show all the clients 
            echo $settings->html_clients_title;
            echo $settings->buttons_newclient;
            echo $settings->html_p;

            $result = $app->getClients();
            if (!$result['ok']){
                echo $settings->error_noclients;
            
            } else {

                $cols = $result['data']['cols'];
                $rows = $result['data']['rows'];

                $th = ''; $td = '';

                // headers for the table
                foreach($cols as $col){
                    if ($col == 'client_id') continue; // don't show this field on the html form
                    $th .= "<th>$col</th>";
                }
                // extra table header
                $th .= "<th>Client Actions</th>";

                // font awesome icon
                $icon_bank = $settings->icons_bank;
                
                // data for the table
                foreach($rows as $row){
                    $td .= "<tr>";
                    foreach($cols as $col){
                        if ($col == 'client_id') continue; // don't show this field on the html form            
                        $val = $row[$col];
                        if ($col == 'Name') $val = "$icon_bank $val";
                        $td .= "<td>$val</td>";
                    } 
                    
                    $clientid = $row['client_id'];      
                    
                    // extra data for actions 
                    $actions = $settings->html_actions_clients;
                    $actions = str_replace('{clientid}', $clientid, $actions);
                    
                    $td .= "<td>$actions</td>";
                    $td .= "</tr>";
                }    
                echo "<table>";
                echo "<thead><tr>$th</tr></thead>";
                echo "<tbody>$td</tbody>";
                echo "</table>";
                
                // use javascript to confirm if we want to delete record
                echo "<script>
                        function confirmDelete(id){
                            if (confirm('Delete the selected record?')){
                                window.location.href = '?view=clients&action=delete&id='+id;
                            }
                            return false;
                        }		
                    </script>";
            }             
 }
