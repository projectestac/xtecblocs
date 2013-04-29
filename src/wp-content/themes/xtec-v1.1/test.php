<?php

header("Content-type:  image/gif");
dl('php3_gd.dll');
//header("Content-type:  image/png");
$image = imageCreate(100, 100);
imageGIF($image);
//imagePNG($image);

// create an interlaced image for better loading in the browser
imageInterlace($image, 1);
// mark background color as being transparent
$colorBackgr       = imageColorAllocate($image, 192, 192, 192);
imageColorTransparent($image, $colorBackgr);

?>