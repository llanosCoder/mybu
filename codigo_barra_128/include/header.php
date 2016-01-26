<?php
if (!defined('IN_CB')) { die('You are not allowed to access to this page.'); }

if (version_compare(phpversion(), '5.0.0', '>=') !== true) {
    exit('Sorry, but you have to run this script with PHP5... You currently have the version <b>' . phpversion() . '</b>.');
}

if (!function_exists('imagecreate')) {
    exit('Sorry, make sure you have the GD extension installed before running this script.');
}

include_once('function.php');

// FileName & Extension
$system_temp_array = explode('/', $_SERVER['PHP_SELF']);
$filename = $system_temp_array[count($system_temp_array) - 1];
$system_temp_array2 = explode('.', $filename);
$availableBarcodes = listBarcodes();
$barcodeName = findValueFromKey($availableBarcodes, $filename);
$code = $system_temp_array2[0];

// Check if the code is valid
if (file_exists('config' . DIRECTORY_SEPARATOR . $code . '.php')) {
    include_once('config' . DIRECTORY_SEPARATOR . $code . '.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!--link type="text/css" rel="stylesheet" href="style.css" /-->
        <link type="text/css" rel="stylesheet" href="css/bootstrap.css"/>
        <link href="font-awesome-4.1.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <link rel="shortcut icon" href="favicon.ico" />

    </head>
    <body class="<?php echo $code; ?>">

<?php
$default_value = array();
$default_value['filetype'] = 'PNG';
$default_value['dpi'] = 72;
$default_value['scale'] = isset($defaultScale) ? $defaultScale : 1;
$default_value['rotation'] = 0;
$default_value['font_family'] = 'Arial.ttf';
$default_value['font_size'] = 8;
$default_value['text'] = '';
$default_value['a1'] = '';
$default_value['a2'] = '';
$default_value['a3'] = '';

$filetype = isset($_POST['filetype']) ? $_POST['filetype'] : $default_value['filetype'];
$dpi = isset($_POST['dpi']) ? $_POST['dpi'] : $default_value['dpi'];
$scale = intval(isset($_POST['scale']) ? $_POST['scale'] : $default_value['scale']);
$rotation = intval(isset($_POST['rotation']) ? $_POST['rotation'] : $default_value['rotation']);
$font_family = isset($_POST['font_family']) ? $_POST['font_family'] : $default_value['font_family'];
$font_size = intval(isset($_POST['font_size']) ? $_POST['font_size'] : $default_value['font_size']);
$text = isset($_POST['text']) ? $_POST['text'] : $default_value['text'];

registerImageKey('filetype', $filetype);
registerImageKey('dpi', $dpi);
registerImageKey('scale', $scale);
registerImageKey('rotation', $rotation);
registerImageKey('font_family', $font_family);
registerImageKey('font_size', $font_size);
registerImageKey('text', stripslashes($text));

// Text in form is different than text sent to the image
$text = convertText($text);
?>


  
                    
                    <?php/*
if (isset($baseClassFile) && file_exists('include' . DIRECTORY_SEPARATOR . $baseClassFile)) {
    include_once('include' . DIRECTORY_SEPARATOR . $baseClassFile);
}*/
?>
                    
            
     
        <br><br><br>
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-info">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><center><h4 class="text-default"><i class="fa fa-angle-double-right  fa-1x"></i> Genera tu código.</h4></center></h3>
                                </div>
                                <div class="panel-body">
                                    
                                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" autocomplete="off">
                                        <br><br>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="text">Ingrese el dato para generar el código.</label>
                                            </div>

                                            <div class="col-md-6">
                                    
                                    <div class="generate"><?php echo getInputTextHtml('text', $text, array('type' => 'text', 'required' => 'required', 'class' => 'form-control')); ?>
                                            </div>
                                        </div>
                                            
                                       </div>
                                        
                                            
                                         <br><br>   
                                        <div class="row">
                                           <!--col-md-6 col-md-offset-5-->
                                            <div class="col-md-12">
                                                
                                                <input class="btn btn-success pull-right" type="submit" value="Generar Código" />
                                            
                                            </div>
                                             
                                            
                                        </div>
                                    
                                    
                                
        
 