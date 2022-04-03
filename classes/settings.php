<?php
/*
 * class to hold configuration data for the project
*/
class settings {
        public $title          = 'YouTube Player';     
        public $showPHPerrors  = true;
        
        // database connection
        public $database_host  = 'localhost';
        public $database_name  = 'youtube';
        public $database_user  = '';
        public $database_pwd   = '';

         // tables
        public $tables_playlists  = 'playlists';
        public $tables_videos = 'videos';
        public $tables_joined   = 'playlistsvideos';

         // errors
         
        public $error_noplaylists         = "No playlist(s) found";
        public $error_novideos        = "No video(s) found";
        public $error_nolinkedvideos  = "No videos found";
        public $error_nolinkablevideos= "No videos found that can be linked to this playlist.";
        public $error_duplicatelink     = "The video specified is already linked to this playlist.";

		// buttons
        public $buttons_playlists    = "<a href='?view=playlists'>My Playlists</a>";
        public $buttons_videos   = "<a href='?view=videos'>All Videos</a>";
        public $buttons_test       = "<a href='?view=test'>playlist Code Generation Test</a>";
        public $buttons_newplaylist  = "<a href='?view=playlists&action=add' class='button'>+ Add new playlist</a><BR>";
        public $buttons_newvideo = "<a href='?view=videos&action=add' class='button'>+ Add new video</a><BR>";
    		
		// HTML 
		public $html_author         = "<small class='float-right'>by Sengdara</small>";   
		public $html_playlists_title  = "<h1 class='align-center'>My Playlists</h1>";
		public $html_videos_title = "<h1 class='align-center'>All YouTube videos</h1>";
        public $html_hr             = '<HR>';
        public $html_slash          = ' / ';
        public $html_p              = '<p></p>';  
        public $html_hint_autoplay  = '<p>Play one video and the rest will auto-play thereafter</p>';  
        public $html_searchbox = "<input name='term' style='padding:10px; width: 80vw' maxlength=200 placeholder='Enter something to search the list'>";
		        
        // actions
        public $html_actions_playlists = "<a href='?view=playlists&action=edit&id={playlistid}'>Edit</a> /
						                <a href='#' onclick='return confirmDelete({playlistid})'>Delete</a>";

		public $html_actions_videos = "<a href='?view=videos&action=details&id={videoid}'>Details</a> /
										<a href='?view=videos&action=edit&id={videoid}'>Edit</a> /
										<a href='#' onclick='return confirmDelete({videoid});'>Delete</a>";
												
        // font awesome icons
        public $icons_person = "<span class='fa fa-fw fa-user'></span>";
        public $icons_bank   = "<span class='fa fa-fw fa-bank'></span>";
        public $icons_list   = "<span class='fa fa-fw fa-list'></span>";
        public $icons_play   = "<span class='fa fa-fw fa-play'></span>";
        public $icons_link   = "<span class='fa fa-fw fa-link'></span>";
        public $icons_unlink = "<span class='fa fa-fw fa-unlink'></span>";
        public $icons_video  = "<span class='fa fa-fw fa-video'></span>";

		// SQL
        public $sql_getplaylists  = "SELECT 
										playlist_id,
										title AS `Name`,
										(SELECT 
												COUNT(psid)
											FROM
												playlistsvideos cc
											WHERE
												cc.playlist_id = c.playlist_id) AS `Videos`
									FROM
										`playlists` c
									ORDER BY c.title ASC;";
																
        public $sql_getvideos = "SELECT 
										video_id,
										title AS `Title`,
										url,
										(SELECT 
												COUNT(psid)
											FROM
												playlistsvideos cc
											WHERE
												cc.video_id = c.video_id) AS `Playlists`
									FROM
										`videos` c
									ORDER BY `title` ASC;";
		
		public $sql_getvideosnotlinkedtoplaylist = "SELECT 
														video_id, 
														title AS `Title` 
													FROM 
														`videos` c 
                									WHERE 
                										c.video_id NOT IN 
                                                        (SELECT 
                                                                video_id 
                                                            FROM 
                                                                playlistsvideos cc 
                                                            WHERE 
                                                                cc.playlist_id={playlistid}
                                                        ) 
                									ORDER BY 
                										`Title` 
                									ASC;";
                									
        public $sql_findlinkbetweenplaylistvideo = "SELECT 
                                                           * 
                                                    FROM 
                                                          `{table}` 
        											WHERE 
        											      playlist_id={playlistid} 
        											AND 
        											      video_id={videoid};";
        											
        public $sql_getvideosforplaylist = "SELECT 
                                            	psid, 
                                            	c.video_id,
                                            	title AS `Title`,
                                            	url
											FROM 
											     videos c, 
											     playlistsvideos cc 
											WHERE 
											     cc.playlist_id={playlistid} 
											AND 
											     cc.video_id = c.video_id;";       
												 
		public $sql_getplaylistsforvideo = "SELECT 
												 c.playlist_id, 
												 c.title As `Title`
											 FROM 
												  playlists c, 
												  playlistsvideos cc 
											 WHERE 
												  cc.playlist_id=c.playlist_id
											 AND 
												  cc.video_id = {videoid};";       												 
 }
