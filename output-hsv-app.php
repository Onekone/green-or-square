<?php
$files = array_diff(scandir(__DIR__ . '/input_data', 0), array('..', '.'));
$time = (float)microtime(true);
foreach ($files as $file) {
    echo $file . '?';
    $p = hsvAnalyze(imagecreatefromjpeg(__DIR__ . '/input_data/' . $file), $file);
    echo ($p === true ? 'Да' : 'Нет') . PHP_EOL;
}
$time = (float)microtime(true) - $time;
echo 'Время: ' . $time . PHP_EOL;

function hsvAnalyze($imageResource, $file)
{
    $width = max(imagesx($imageResource), 1);
    $height = max(imagesy($imageResource), 1);
    $squareScore = $width * $height * min($width, $height) / max($width, $height);
    $greenScore = 0;

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $color = imagecolorat($imageResource, $x, $y);
            $r = (($color >> 16) & 255) / 255;
            $g = (($color >> 8) & 255) / 255;
            $b = ($color & 255) / 255;
            $c1 = max($r, $g, $b);
            $c2 = min($r, $g, $b);
            if ($c1 === $c2) {
                $h = 0;
            } else {
                $d = $c1 - $c2;
                if ($r === $c2) {
                    $h = (3 - (($g - $b) / $d)) * 60;
                } else if ($b === $c2) {
                    $h = (1 - (($r - $g) / $d)) * 60;
                } else if ($g === $c2) {
                    $h = (5 - (($b - $r) / $d)) * 60;
                }
            }

            if ($h >= 75 && $h <= 165) {
                $greenScore++;
                imagesetpixel($imageResource, $x, $y, 0x00FF00);
            } else {
                imagesetpixel($imageResource, $x, $y, 0xFF0000);
            }
        }
    }
    echo 'green: '. $greenScore . ' square:' . $squareScore.' ';
    imagepng($imageResource, __DIR__ . '/output_data/hsv-'.($greenScore > $squareScore ? 'green':'square').'-' . $file.'.png');
    return $greenScore > $squareScore;
}