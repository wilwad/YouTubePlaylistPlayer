 <?php
  require('classes/settings.php');
  require('classes/CRUD.php');
  require('classes/CForm.php');  
  require('classes/App.php');
  
  $settings = new settings();
  
  if ( $settings->showPHPerrors ){
    ini_set('display_startup_errors',1);
    ini_set('display_errors',1);
    error_reporting(-1);  
  }  

  $app = new App( $settings );  
 ?>
 <!DOCTYPE html>
 <html lang='en'>
  <head>
   <meta charset='utf-8'>
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />   
   <title><?php echo $settings->title; ?></title>
   
   <!-- font-awesome icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css" integrity="sha512-10/jx2EXwxxWqCLX/hHth/vu2KY3jCF70dCQB8TSgNjbCVAC/8vai53GfMDrO2Emgwccf2pJqxct9ehpzG+MTw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <link rel='stylesheet' href='css/style.css'>
  </head>
  <body>
  
   <?php
    echo $settings->buttons_playlists;
    echo $settings->html_slash;
    echo $settings->buttons_videos;
    
    echo $settings->html_author;
    echo $settings->html_hr;    

    /* using @ in case these parameters are not set */
    $view     = @ $_GET['view'];
    $action   = @ $_GET['action'];
    $actionid = (int) @ $_GET['id'];

    switch ($view){
        case 'videos':
          require('views/videos.php');
          break;
			
        default:
          // default or when no view is set
          require('views/playlists.php');
    }
   ?>
    <script src="https://www.youtube.com/iframe_api" defer></script>   
    <script src="js/main.js" defer></script>
  </body>
 </html>
