<?php

/**
 * @link https://www.php.net/manual/en/function.imagecopymerge.php#92787
 * @param $dst_im
 * @param $src_im
 * @param $dst_x
 * @param $dst_y
 * @param $src_x
 * @param $src_y
 * @param $src_w
 * @param $src_h
 * @param $pct
 */
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){
    // creating a cut resource
    $cut = imagecreatetruecolor($src_w, $src_h);

    // copying relevant section from background to the cut resource
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

    // copying relevant section from watermark to the cut resource
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

    // insert cut resource to destination image
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
}