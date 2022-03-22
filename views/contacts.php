<?php
 /*
  * handling of the contacts operations
 */
 $icon_person = $settings->icons_person;
			
 // when you click a Submit button on the forms
 // called during add
if (isset($_POST['add'])){
    unset($_POST['add']); // remove the submit button otherwise it will be added to the database
    
    $ret = $app->createContact($_POST);
    if ($ret['ok']){
        // go to the contacts area
        die("<script>window.location.href='?view=contacts';</script>");

    } else {
        die('Error: ' . $ret['error']);
    }
}
// called during edit
if (isset($_POST['edit'])){
    unset($_POST['edit']); // remove the submit button otherwise it will be added to the database
    
	$contactid = $actionid;
    $ret = $app->updateContact($contactid, $_POST);
    if ($ret['ok']){
        die("<script>window.location.href='?view=contacts';</script>"); // navigate the page to this URL
    } else {
        die('Error: ' . $ret['error']);
    }    
}

// handling of ?view=contacts&action=delete|edit|details|add|no_parameter
 switch ($action){
        case 'add': // add a new contact
            $mydb = new CForm(  $settings->database_host,
                                $settings->database_user,
                                $settings->database_pwd, 
                                $settings->database_name);

            // show a data entry form on the screen without any data
            $ret = $mydb->generateForm( $settings->tables_contacts);        
            if ($ret['ok']){ 
                $body     = [];
                $ignored  = ['contact_id'];        
                $required = ['name', 'surname', 'email_address']; // these values in the array will have the required property added to the input field
                foreach($ret['data'] as $id=>$v){
                        $c = @$comments[$id] ? $comments[$id] : ucwords($id);
                        $req = in_array($id, $required) ? "required='required'" : "";
                        $requiredstar = $req ? "<span class='color_red'>*</span>" : '';
                        if (in_array($id, $ignored)) continue;
        
                        $data = "<input type='text' $req name='$id' id='$id' value=\"$v\">";
                        if ($id == 'email_address'){
                            $data = "<input type='email' $req name='$id' id='$id' value=\"$v\">";
                        }
                        
                    $body[] = "<p><label for='$id'>$c $requiredstar</label><BR>$data</p>";
                }        
                $body = implode('', $body);
                
                echo $settings->html_p;
                echo "<form method='post'>
                    <fieldset>
                        <legend>Contact Details</legend>
                        $body
                        <input type='submit' name='add'value='Add Record'>
                    </fieldset>
                    </form>
                    <p>&nbsp;</p>
                    ";
        
            } else {
                if ($ret['error']) echo 'Error: ' . $ret['error'];
            }   
            break;
        
		 case 'edit': // edit the contact
		    $mydb = new CForm(  $settings->database_host,
                                $settings->database_user,
                                $settings->database_pwd, 
                                $settings->database_name );
		                    
            // show a data editing form on the screen with data for contact with contact_id = $actionid
		    $ret = $mydb->generateForm( $settings->tables_contacts,'contact_id', $actionid);
		    
		    if ($ret['ok']){ 
		        $body     = [];
		        $ignored  = ['contact_id'];        
		        $required = ['name','surname','email_address']; // these values in the array will have the required property added to the input field
		        foreach($ret['data'] as $id=>$v){
		                $c = @$comments[$id] ? $comments[$id] : ucwords($id);
		                $req = in_array($id, $required) ? "required='required'" : "";
		                $requiredstar = $req ? "<span class='color_red'>*</span>" : '';
		                if (in_array($id, $ignored)) continue;
		
		                $data = "<input type='text' $req name='$id' id='$id' value=\"$v\">";
		                if ($id == 'email_address'){
                            $data = "<input type='email' $req name='$id' id='$id' value=\"$v\">";
                        }

		                $body[] = "<p><label for='$id'>$c $requiredstar</label><BR>$data</p>";
		        }		
		        $body = implode('', $body);
		
		        echo "<form method='post' enctype='multipart/form-data'>
		               <fieldset>
		                <legend>Edit Contact</legend>
		                $body
                        <input type='submit' name='edit' value='Update Record'>               
		               </fieldset>
		              </form>
		              <p>&nbsp;</p>
		              ";
		
		    } else {
                if ($ret['error']) echo 'Error: ' . $ret['error'];
		    }    
		    break; 

        case 'details': // get details of the contact
            $mydb = new CForm( $settings->database_host,
                               $settings->database_user,
                               $settings->database_pwd, 
                               $settings->database_name );
                            
            $ret = $mydb->generateForm( $settings->tables_contacts,'contact_id', $actionid);
        
            if ($ret['ok']){
                $body     = [];
                $ignored  = ['id'];        
                $required = []; // these values in the array will have the required property added to the input field
                foreach($ret['data'] as $id=>$v){
                        $c = @$comments[$id] ? $comments[$id] : ucwords($id);
                        $req = in_array($id, $required) ? "required='required'" : "";
                        $requiredstar = $req ? "<span class='color_red'>*</span>" : '';
                        if (in_array($id, $ignored)) continue;
        
                        $data = "<span>$v</span>";
                        $body[] = "<tr>
                                     <th><label for='$id'>$c $requiredstar</label></th><td>$data</td>
                                   </tr>";
                }    
                $body = implode('', $body);
        
                echo "<form method='post'>
                       <fieldset>
                        <legend>Contact Details</legend>
                        <table>
                         <tbody>$body</tbody>
                        </table>
                       </fieldset>
                      </form>
                      <p>&nbsp;</p>
                      ";
        
            } else {
                if ($ret['error']) echo 'Error: ' . $ret['error'];
            }    
            break;     
        
        case 'delete': // delete the contact
            $ret = $app->deleteContact($actionid);
            if (!$ret['ok']){
                if ($ret['error']) echo 'Error: ' . $ret['error'];
                
            } else {
                die("<script>window.location.href='?view=contacts';</script>"); // show all contacts
            }
            break;
             
             
     default: // show all the contacts
		echo $settings->html_contacts_title;
		echo $settings->buttons_newcontact;
		echo $settings->html_p;

		$result = $app->getContacts();
		if (!$result['ok']){
		   echo $settings->error_nocontacts;
		   
		} else {

			$cols = $result['data']['cols'];
			$rows = $result['data']['rows'];
			$th = ''; $td = '';

			foreach($cols as $col){
				if ($col == 'contact_id') continue; // don't show this field on the html form
				$th .= "<th>$col</th>";
			}
			// extra th
			$th .= "<th>Contact Actions</th>";

			foreach($rows as $row){
				$td .= "<tr>";
				foreach($cols as $col){
				    if ($col == 'contact_id') continue; // don't show this field on the html form
				    $val = $row[$col];
				    if ($col == 'Full Name') $val = "$icon_person $val";
				    $td .= "<td>$val</td>";
				}       
				// actions 
				$contactid = $row['contact_id'];
                $actions = $settings->html_actions_contacts;
                $actions = str_replace('{contactid}', $contactid, $actions);
                				
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
                            window.location.href = '?view=contacts&action=delete&id='+id;
                        }
                        return false;
                    }		
                </script>";			
		}        
 }
