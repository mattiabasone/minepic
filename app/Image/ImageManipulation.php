<?php

declare(strict_types=1);

namespace Minepic\Image;

use Minepic\Image\Exceptions\ImageTrueColorCreationFailedException;

trait ImageManipulation
{
    /**
     * @see https://www.php.net/manual/en/function.imagecopymerge.php#92787
     * @throws ImageTrueColorCreationFailedException
     */
    protected function imageCopyMergeAlpha(
        \GdImage $dst_im,
        \GdImage $src_im,
        int $dst_x,
        int $dst_y,
        int $src_x,
        int $src_y,
        int $src_w,
        int $src_h,
        int $pct
    ): void {
        // creating a cut resource
        $cut = imagecreatetruecolor($src_w, $src_h);

        if ($cut instanceof \GdImage === false) {
            throw new ImageTrueColorCreationFailedException();
        }

        // copying relevant section from background to the cut resource
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h);

        // copying relevant section from watermark to the cut resource
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);

        // insert cut resource to destination image
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct);
    }
}
