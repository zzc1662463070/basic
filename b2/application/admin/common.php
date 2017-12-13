<?php


use app\admin\model\Image;

function getImgPath($imgid){
    return Image::where('id',$imgid)->value('path');
}

function ta($um){
    return $um+1;
}
;