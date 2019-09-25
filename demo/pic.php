<?php
include_once '../extend/Yzt/Yzt.php';
$Yzt = new \Yzt\Yzt('https://api.uomg.com/api/rand.img1', true, true, rand(20, 270));

       // $info = $Yzt->move('static/upload/');
        //return $info;
        //die();
        header("Content-type: image/png;text/html; charset=utf-8");
       
        imagepng($Yzt->changeCircularImg());
        die();
