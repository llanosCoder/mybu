<?php
session_start();
require_once 'facebook-php-sdk/autoload.php';

$fb = new Facebook\Facebook(['app_id' => '1641950849383890',
  'app_secret' => '91cfc343d2569cedd59bef409003e8b9',
  'default_graph_version' => 'v2.4']);

$helper = $fb->getRedirectLoginHelper();
try {
  //$accessToken = $helper->getAccessToken();
  $accessToken = $_POST['access'];
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  //echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  //echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (isset($accessToken)) {
  // Logged in!
  $_SESSION['facebook_access_token'] = (string) $accessToken;
  $resultado['resultado'] = 1;
  // Now you can redirect to another page and use the
  // access token from $_SESSION['facebook_access_token']
}else{ 
  $resultado['resultado'] = 0;
}

try {
  // Returns a `Facebook\FacebookResponse` object
  $response = $fb->get('/me?fields=id,name,email, picture, gender, age_range', $_SESSION['facebook_access_token']);
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  //echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  //echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

$user = $response->getGraphUser();
//print_r($user);
/*echo 'Name: ' . $user['name'];
echo "\n" . 'Email: ' . $user['email'];
echo "\n" . 'Sexo: ' . $user['gender'];
echo "\n" . 'Rango etario: ' . $user['age_range'];*/

if ($resultado['resultado'] == 1){
    // Instanciamos clase interna de mybu para realizar la verificación
    require_once 'facebook-classes/facebook.model.php';
    $facebook = new Facebook();
    
    //Capturamos el user id
    $usuario = $user['email'];
    
    if ($facebook->verificarUsuario($usuario)){
        $facebook->login($usuario);
        $resultado['resultado'] = 1;
        /*$_SESSI   ON['empresa'] = 1;
        $_SESSION['nombre_empresa'] = 'mybu';
        $_SESSION['host'] = 3;
        $_SESSION['sucursal'] = 1;
        $_SESSION['tipo_cuenta'] = 1;
        $_SESSION['rol'] = 3;*/
    }
    else 
        if ($usuario!=null){
            $resultado['resultado'] = 2;
            $resultado['email']= $usuario;
        }
        else 
            $resultado['resultado'] = 0;
}

echo json_encode($resultado);

?>