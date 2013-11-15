<?php 
include 'src/facebook.php';


  $config = array(
      'appId' => '383422028455958',
      'secret' => 'b7bde406b21eac1a0f5ae7d54154aa67',
      'fileUpload' => false // optional
  );

  $facebook = new Facebook($config);
  echo '<pre>';
  var_dump($facebook);
  exit;