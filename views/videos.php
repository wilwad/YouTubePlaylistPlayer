<?php
 /*
  * handling of the videos operations
 */
 $icon_person = $settings->icons_person;
 $icon_play   = $settings->icons_play;
 $icon_video  = $settings->icons_video;
  			
 // when you click a Submit button on the forms
 // called during add
if (isset($_POST['add'])){
    unset($_POST['add']); // remove the submit button otherwise it will be added to the database
    
    // handling file uploads
    /*
    if (isset($_FILES)){        
        // we need to delete the existing file.
        // so let's get the current row
        $ret = $mydb->read('girls', 'id', $edit,1); // 1 record
        if (! $ret['ok']) die('Record not found');
        $row = $ret['data']['rows'][0]; // the record

        foreach ($_FILES as $name => $value) {
            $path = $_FILES[$name]['name'];
            $tmp  = $_FILES[$name]['tmp_name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            // ignore if not an image
            //if (! getimagesize ($tmp)) continue;
            //if (!in_array(strtolower($ext), $imageeext)) continue;

            $random = $app->randomPassword(10);
            $to     = $settings->$uploaddir . "$random.$ext";

            if (move_uploaded_file( $tmp, $to)) {
                // only 1 record is returned
                $fileexisting = $row[$name];

                if (file_exists($fileexisting)){
                    @ unlink($fileexisting);
                    echo "Deleted $fileexisting";
                } 

                // value to save in the table
                $_POST[$name] = $to;
            } else {
                echo "Failed to move file $path to $to.<BR>";
            }
        }
    } */
    
    $ret = $app->createVideo($_POST);
    if ($ret['ok']){
        // go to the videos area
        die("<script>window.location.href='?view=videos';</script>");

    } else {
        die('Error: ' . $ret['error']);
    }
}
// called during edit
if (isset($_POST['edit'])){
    unset($_POST['edit']); // remove the submit button otherwise it will be added to the database
    
    // handling file uploads
    /*
    if (isset($_FILES)){        
        foreach ($_FILES as $name => $value) {
            $path = $_FILES[$name]['name'];
            $tmp  = $_FILES[$name]['tmp_name'];
            $ext = pathinfo($path, PATHINFO_EXTENSION);

            // ignore if not an image
            //if (! getimagesize ($tmp)) continue;
            //if (!in_array(strtolower($ext), $imageeext)) continue;

            $random = $app->randomPassword(10);
            $to     = $settings->$uploaddir . "$random.$ext";

            if (move_uploaded_file( $tmp, $to)) {
                // value to save in the table
                $_POST[$name] = $to;
            } else {
                echo "Failed to move file $path to $to.<BR>";
            }
        }
    } */
    
	$videoid = $actionid;
    $ret = $app->updateVideo($videoid, $_POST);
    if ($ret['ok']){
        die("<script>window.location.href='?view=videos';</script>"); // navigate the page to this URL
    } else {
        die('Error: ' . $ret['error']);
    }    
}

// handling of ?view=videos&action=delete|edit|details|add|no_parameter
 switch ($action){
        case 'add': // add a new video
            $mydb = new CForm(  $settings->database_host,
                                $settings->database_user,
                                $settings->database_pwd, 
                                $settings->database_name);

            $metadata = [];
            $ret = $mydb->getTableMetadata($settings->tables_videos);
            if ($ret['ok']){
                $metadata = $ret['data'];
            } 
            
            // show a data entry form on the screen without any data
            $ret = $mydb->generateForm( $settings->tables_videos);        
            if ($ret['ok']){ 
                $body     = [];
                $ignored  = ['video_id'];        
                $required = ['title', 'url']; // these values in the array will have the required property added to the input field
                foreach($ret['data'] as $id=>$v){
                        $c = @$comments[$id] ? $comments[$id] : ucwords($id);
                        $req = in_array($id, $required) ? "required='required'" : "";
                        $requiredstar = $req ? "<span class='color_red'>*</span>" : '';
                        if (in_array($id, $ignored)) continue;
        
                        $type = $metadata[$id]['type'];
                        $size = $mydb->numbersFromString($type);                        
                        $extra = $size ? "maxlength=$size" : '';
                        
                        $data = "<input type='text' $extra $req name='$id' id='$id' value=\"$v\">";
                        /* handle some columns */
                        switch ($id){
                            case 'photo':
                            case 'picture':
                                $data = "<input $req type='file' accept='.jpg,.jpeg,.png' name='$id' id='$id'>";
                                break;
                                
                            case 'filename':
                                $data = "<input $req type='file' accept='.txt,.pdf,.doc' name='$id' id='$id'>";
                                break;
                                
                            case 'author_id':
                                $ret = $movies->getTableColumnData('books_authors', 'name');
                                if ($ret['ok']){
                                    $values = '';
                                    
                                    foreach($ret['data'] as $id0=>$val0){
                                        $values .= "<option value='$id0'>$val0</option>";
                                    }
                                    
                                    $data = "<select $required name='$id' id='$id'>$values</select>";
                                }
                                break;
                                
                            case 'category_id*':
                                $ret = $mydb->getTableColumnData('categories', 'name');
                                if ($ret['ok']){
                                    $values = '';
                                    
                                    foreach($ret['data'] as $id=>$val){
                                        $values .= "<option value='$id'>$val</option>";
                                    }
                                    
                                    $data = "<select $req id='$id'>$values</select>";
                                }           			
                                break;
                                
                            case 'email':
                            case 'email_address': 
                                $data = "<input type='email' $req name='$id' id='$id' value=\"$v\">";
                                break;
                                
                            default:
                                break;
                        } /* handle some columns */  
                        
                        if ($id == 'email_address'){
                            $data = "<input type='email' $req name='$id' id='$id' value=\"$v\">";
                        }
                        
                    $body[] = "<p><label for='$id'>$c $requiredstar</label><BR>$data</p>";
                }        
                $body = implode('', $body);
                
                echo $settings->html_p;
                echo "<form method='post'>
                    <fieldset>
                        <legend>Video Details</legend>
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
        
		 case 'edit': // edit the video
		    $mydb = new CForm(  $settings->database_host,
                                $settings->database_user,
                                $settings->database_pwd, 
                                $settings->database_name );
		                    
            $metadata = [];
            $ret = $mydb->getTableMetadata($settings->tables_videos);
            if ($ret['ok']){
                $metadata = $ret['data'];
            } 
            
            // show a data editing form on the screen with data for video with video_id = $actionid
		    $ret = $mydb->generateForm( $settings->tables_videos,'video_id', $actionid);
		    
		    if ($ret['ok']){ 
		        $body     = [];
		        $ignored  = ['video_id'];        
		        $required = ['title', 'url']; // these values in the array will have the required property added to the input field
		        foreach($ret['data'] as $id=>$v){
		                $c = @$comments[$id] ? $comments[$id] : ucwords($id);
		                $req = in_array($id, $required) ? "required='required'" : "";
		                $requiredstar = $req ? "<span class='color_red'>*</span>" : '';
		                if (in_array($id, $ignored)) continue;
		
                        $type = @$metadata[$id]['type'];
                        $size = $mydb->numbersFromString($type);                        
                        $extra = $size ? "maxlength=$size" : '';

                        $data = "<input type='text' $extra $req name='$id' id='$id' value=\"$v\">";
                        
                        /* handle some columns */
                        switch ($id){
                            case 'photo':
                            case 'picture':
                                $data = "<input $req type='file' accept='.jpg,.jpeg,.png' name='$id' id='$id'>";
                                break;
                                
                            case 'filename':
                                $data = "<input $req type='file' accept='.txt,.pdf,.doc' name='$id' id='$id'>";
                                break;
                                
                            case 'author_id':
                                $ret = $movies->getTableColumnData('books_authors', 'name');
                                if ($ret['ok']){
                                    $values = '';
                                    
                                    foreach($ret['data'] as $id0=>$val0){
                                        $values .= "<option value='$id0'>$val0</option>";
                                    }
                                    
                                    $data = "<select $required name='$id' id='$id'>$values</select>";
                                }
                                break;
                                
                            case 'category_id*':
                                $ret = $mydb->getTableColumnData('categories', 'name');
                                if ($ret['ok']){
                                    $values = '';
                                    
                                    foreach($ret['data'] as $id=>$val){
                                        $values .= "<option value='$id'>$val</option>";
                                    }
                                    
                                    $data = "<select $req id='$id'>$values</select>";
                                }           			
                                break;
                                
                            case 'email':
                            case 'email_address': 
                                $data = "<input type='email' $req name='$id' id='$id' value=\"$v\">";
                                break;
                                
                            default:
                                break;
                        } /* handle some columns */  

		                $body[] = "<p><label for='$id'>$c $requiredstar</label><BR>$data</p>";
		        }		
		        $body = implode('', $body);
		
		        echo "<form method='post' enctype='multipart/form-data'>
		               <fieldset>
		                <legend>Edit Video</legend>
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

        case 'details': // get details of the video
            $mydb = new CForm( $settings->database_host,
                               $settings->database_user,
                               $settings->database_pwd, 
                               $settings->database_name );
                            
            $ret = $mydb->generateForm( $settings->tables_videos,'video_id', $actionid);
        
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
                        <legend>Video Details</legend>
                        <table>
                         <tbody>$body</tbody>
                        </table>
                       </fieldset>
                      </form>
                      <!--p>&nbsp;</p-->
                      ";
        
                echo "<p class='text-underline'>Playlists linked to this video</p>";

                $result = $app->getPlaylistsLinkedToVideo($actionid);
                if ( !$result['ok'] ){
                     echo $settings->error_noplaylists;
                     if ($result['error']) echo $result['error'];
                } else {
    
                    $cols = $result['data']['cols'];
                    $rows = $result['data']['rows'];
                    $th = ''; $td = '';
    
                    foreach($cols as $col){
                        if ($col == 'playlist_id') continue;
                        $th .= "<th>$col</th>";
                    }
                
                    foreach($rows as $row){
                        $td .= "<tr>";
                        foreach($cols as $col){
                            if ($col == 'playlist_id') continue;
                            $val = $row[$col];
                           // if ($col == 'Title') $val = "$icon_video $val";
                            $td .= "<td>$val</td>";
                        }       
                        $td .= "</tr>";
                    }    
                    echo "<table>";
                    echo "<thead><tr>$th</tr></thead>";
                    echo "<tbody>$td</tbody>";
                    echo "</table>";
    
                } 

            } else {
                if ($ret['error']) echo 'Error: ' . $ret['error'];
            }    
            break;     
        
        case 'delete': // delete the video
            $ret = $app->deleteVideo($actionid);
            if (!$ret['ok']){
                if ($ret['error']) echo 'Error: ' . $ret['error'];
                
            } else {
                die("<script>window.location.href='?view=videos';</script>"); // show all videos
            }
            break;
             
             
     default: // show all the videos
		echo $settings->html_videos_title;
		echo $settings->buttons_newvideo;
		echo $settings->html_p;
        echo $settings->html_searchbox;
        echo $settings->html_p;
        
		$result = $app->getVideos();
		if (!$result['ok']){
		   echo $settings->error_novideos;
		   
		} else {

			$cols = $result['data']['cols'];
			$rows = $result['data']['rows'];
			$th = ''; $td = '';

			foreach($cols as $col){
				if ($col == 'video_id') continue; // don't show this field on the html form
				if ($col == 'url') continue;  
				$th .= "<th>$col</th>";
			}
			// extra th
			$th .= "<th>Video Actions</th>";

			$idx = 0;
			
			foreach($rows as $row){
				$td .= "<tr>";
                $url = $row['url'];
                
                // handling short URL
                if (strpos($url, 'https://youtu.be') > -1) {
                    $id = str_replace('https://youtu.be/', '', $url);
                } else {
                    $id    = explode('=',$url)[1];
                    // sometimes id includes playlist so remove that
                    if ( strpos($id,'&') != -1){
                    	$id = explode('&', $id)[0]; // first index
                    }
                }
                    				
				foreach($cols as $col){
				    if ($col == 'video_id') continue; // don't show this field on the html form
				    if ($col == 'url') continue;  
				    $val = $row[$col];
				    //if ($col == 'Title') $val = "<a href='#' class='youtube-video' onclick=\"queueVideo($idx);\" data-youtube-id=\"$id\">$val</a>";
				    $td .= "<td>$val</td>";
				}       
				// actions 
				$videoid = $row['video_id'];
                $actions = $settings->html_actions_videos;
                $actions = str_replace('{videoid}', $videoid, $actions);
                				
				$td .= "<td>$actions</td>";
				$td .= "</tr>";
				$idx++;
			}    
			echo "<table>";
			echo "<thead><tr>$th</tr></thead>";
			echo "<tbody>$td</tbody>";
			echo "</table>";   
			
			// use javascript to confirm if we want to delete record
            echo "<script>
                    function confirmDelete(id){
                        if (confirm('Delete the selected record?')){
                            window.location.href = '?view=videos&action=delete&id='+id;
                        }
                        return false;
                    }		
                </script>";			
		}  
		
		?>
<script>
/*
var currIdx = 0;
var player;   
var videos = [];
 document.querySelectorAll('.youtube-video').forEach(el=>videos.push(el.getAttribute('data-youtube-id')));

var queueVideo = function (idx){
		if (idx >= videos.length) idx = 0;
		
		let url = videos[idx]
		document.querySelectorAll('a.youtube-video').forEach((el,idx0)=>{
            if (idx0 == idx) 
                el.classList.add('playing')
            else
                el.classList.remove('playing')
            });
		player.cueVideoById(url);
		currIdx = idx
}

var onYouTubeIframeAPIReady = function () {
								if (!videos.length) return;
                                player = new YT.Player('video-placeholder', {
                                                        width: 600,
                                                        height: 400,
                                                        videoId: videos[0],
                                                        playerVars: {
                                                                    autoplay: 1,
                                                                    loop: 1,
                                                                    controls: 1,
                                                                    showinfo: 0,
                                                                    autohide: 1,        
                                                                    color: 'white'//                                                            playlist: 'taJ60kskkns,FG0fTKAqZ5g'
                                                        },
                                                        events: {
                                                            'onReady': onPlayerReady,
                                                            'onStateChange': onPlayerStateChange            
                                                        }
                                                    });
}

// 4. The API will call this function when the video player is ready.
function onPlayerReady(event) {}

const playerstates = {5: 'loaded', 
		              3: 'buffering',
		              1: 'playing',
		              2: 'paused',
		              '-1':'stopped',
		              0:'ended'}
                    
function onPlayerStateChange(event) {
		console.log (event.data, playerstates[event.data])
        console.log( playerstates[event.data] )
		
		if (playerstates[event.data] == 'loaded'){
            // video can now be played
            player.playVideo()
        }
        
		if (playerstates[event.data] == 'ended'){
            // queue next video
			currIdx +=1
			queueVideo(currIdx)
		}
}*/
</script>
		<?php      
 }
