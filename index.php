<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    function pprint($var = null) {
        echo("<pre>");var_dump($var);echo("</pre><br><br>");
    }
    require_once 'vendor/autoload.php';

    function getUrlFile() {
        $url = parse_url($_SERVER['REQUEST_URI']);
        return $url["path"];
    }
    /*function getFileName($path) {
        return pathinfo($path, PATHINFO_BASENAME);
    }*/
    function getFileExtension($path) {
        return pathinfo($path, PATHINFO_EXTENSION);
    }
    function startsWith( $haystack, $needle ) {
        $length = strlen( $needle );
        return substr( $haystack, 0, $length ) === $needle;
   }
    function getBestSupportedMimeType($mimeTypes = null) {
        $AcceptTypes = Array ();
        $accept = $_SERVER['HTTP_ACCEPT'];
        if (empty($accept)) $accept = $_SERVER["CONTENT_TYPE"];
        $accept = strtolower(str_replace(' ', '', $accept));
        $accept = explode(',', $accept);
        foreach ($accept as $a) {
            $q = 1;
            if (strpos($a, ';q=')) {
                list($a, $q) = explode(';q=', $a);
            }
            $AcceptTypes[$a] = floatval($q);
        }
        $mimes = new \Mimey\MimeTypes;
        $mime = $mimes->getMimeType(getFileExtension(getUrlFile()));
        if (!empty($mime)) $AcceptTypes[$mime] = 1.1;
        arsort($AcceptTypes);
        if (!$mimeTypes) return $AcceptTypes;
        $mimeTypes = array_map('strtolower', (array)$mimeTypes);
            foreach ($AcceptTypes as $mime => $q) {
           if ($q && in_array($mime, $mimeTypes)) return $mime;
        }
        return null;
    }
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $mimes = getBestSupportedMimeType();
    // pprint($mime);
    $msg = $url. " blocked by DNS";
    $mime = array_key_first($mimes);
    if (startsWith($mime, "image/")) {
        function drawText($msg = "") 
        {
            $font = './myfont.ttf'; //default font. directory relative to script directory.
            $size = 24; // default font size.
            $rot = 0; // rotation in degrees.
            $pad = 5; // padding.
            $transparent = 0; // transparency set to on.
            $red = 0; // black text...
            $grn = 0;
            $blu = 0;
            $bg_red = 255; // on white background.
            $bg_grn = 255;
            $bg_blu = 255;
            Header("Content-type: image/png");
            $width = 0;
            $height = 0;
            $offset_x = 0;
            $offset_y = 0;
            $bounds = array();
            $image = "";
        
            // get the font height.
            $bounds = ImageTTFBBox($size, $rot, $font, "W");
            if ($rot < 0) 
            {
                $font_height = abs($bounds[7]-$bounds[1]);		
            } 
            else if ($rot > 0) 
            {
            $font_height = abs($bounds[1]-$bounds[7]);
            } 
            else 
            {
                $font_height = abs($bounds[7]-$bounds[1]);
            }
            // determine bounding box.
            $bounds = ImageTTFBBox($size, $rot, $font, $msg);
            if ($rot < 0) 
            {
                $width = abs($bounds[4]-$bounds[0]);
                $height = abs($bounds[3]-$bounds[7]);
                $offset_y = $font_height;
                $offset_x = 0;
            } 
            else if ($rot > 0) 
            {
                $width = abs($bounds[2]-$bounds[6]);
                $height = abs($bounds[1]-$bounds[5]);
                $offset_y = abs($bounds[7]-$bounds[5])+$font_height;
                $offset_x = abs($bounds[0]-$bounds[6]);
            } 
            else
            {
                $width = abs($bounds[4]-$bounds[6]);
                $height = abs($bounds[7]-$bounds[1]);
                $offset_y = $font_height;;
                $offset_x = 0;
            }
            
            $image = imagecreate($width+($pad*2)+1,$height+($pad*2)+1);
            $background = ImageColorAllocate($image, $bg_red, $bg_grn, $bg_blu);
            $foreground = ImageColorAllocate($image, $red, $grn, $blu);
        
            if ($transparent) ImageColorTransparent($image, $background);
            ImageInterlace($image, false);
        
            // render the image
            ImageTTFText($image, $size, $rot, $offset_x+$pad, $offset_y+$pad, $foreground, $font, $msg);
        
            // output PNG object.
            imagePNG($image);
        }
        drawText($msg);
        exit();
    }
    switch ($mime) {
        case 'application/json':
            header("Content-Type: application/json");
            echo json_encode(array("error"=>$msg,"msg"=>$msg,"message"=>$msg,"fail"=>$msg,"title"=>$msg,"result"=>$msg), JSON_PRETTY_PRINT);
            exit();
        case 'text/html':
        case 'application/php':
            $template = file_get_contents("templates/adguard.html");
            $template = str_replace("{domain}", $_SERVER['HTTP_HOST'], $template);
            $template = str_replace("{url}", $url, $template);
            $template = str_replace("{msg}", $msg, $template);
            echo($template);
            exit();
        default:
            echo($msg);
            exit();
    }
?>