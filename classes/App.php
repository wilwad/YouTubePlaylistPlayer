<?php 
 /**
  * Class specific to a specific process
  */
 class App {
    private $settings;
    public $crud;  // database CRUD operations
    
    // class constructor 
    public function __construct($appsettings){
            $this->crud = new CRUD( $appsettings->database_host, 
                                    $appsettings->database_user, 
                                    $appsettings->database_pwd, 
                                    $appsettings->database_name );
                                    
            $this->settings = $appsettings;
    }
    
    // handling videos
    public function getVideos(){
            return $this->crud->readSQL($this->settings->sql_getvideos);
    }
    public function createVideo($postdata){
            return $this->crud->create( $this->settings->tables_videos, $postdata);
    }
    public function updateVideo($videoid, $postdata){
            return $this->crud->update( $this->settings->tables_videos, $postdata, 'video_id', $videoid);
    }
    public function deleteVideo($videoid){
            return $this->crud->delete($this->settings->tables_videos, 'video_id', $videoid);
    }
    public function createLink($videoid, $playlistid){
            return $this->crud->create($this->settings->tables_joined, ['video_id'=>$videoid, 'playlist_id'=>$playlistid]);
    }
    public function removeLink($linkid){
            return $this->crud->delete($this->settings->tables_joined, 'psid', $linkid);
    }      
    public function getVideosLinkedToPlaylist($playlistid){
            $sql = $this->settings->sql_getvideosforplaylist;
            $sql = str_replace('{playlistid}', $playlistid, $sql);             
            return $this->crud->readSQL($sql);
    }
    public function getVideosNotLinkedToPlaylist($playlistid){
        $sql = str_replace('{playlistid}', $playlistid, $this->settings->sql_getvideosnotlinkedtoplaylist);           
        return $this->crud->readSQL($sql);   
    }
    public function getPlaylistsLinkedToVideo($videoid){
            $sql = $this->settings->sql_getplaylistsforvideo;
            $sql = str_replace('{videoid}', $videoid, $sql);               
            return $this->crud->readSQL($sql);
    }    
    
    // handling playlists
    public function getPlaylists(){
            return $this->crud->readSQL($this->settings->sql_getplaylists);
    }
    public function createPlaylist($postdata){
            return $this->crud->create( $this->settings->tables_playlists, $postdata);
    }
    public function updatePlaylist($playlistid, $postdata){
            return $this->crud->update( $this->settings->tables_playlists, $postdata, 'playlist_id', $playlistid);
    }
    public function deletePlaylist($playlistid){
            return $this->crud->delete($this->settings->tables_playlists, 'playlist_id', $playlistid);
    }
        
 } // class App end 
