<?php

declare(strict_types=1);

/**
 * @see https://www.php.net/manual/en/function.imagecopymerge.php#92787
 * @param \GdImage $dst_im
 * @param \GdImage $src_im
 * @param int $dst_x
 * @param int $dst_y
 * @param int $src_x
 * @param int $src_y
 * @param int $src_w
 * @param int $src_h
 * @param int $pct
 */
function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
{
    // creating a cut resource
    $cut = imagecreatetruecolor($src_w, $src_h);

    // copying relevant section from background to the cut resource
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

    // copying relevant section from watermark to the cut resource
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

    // insert cut resource to destination image
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
}
