<?php
$files = array_diff(scandir(__DIR__ . '/input_data', 0), array('..', '.'));
$time = (float)microtime(true);
foreach ($files as $file) {
    echo $file . '?';
    $p = analyze(imagecreatefromjpeg(__DIR__ . '/input_data/' . $file));
    echo ($p === true ? 'Да' : 'Нет') . PHP_EOL;
}
$time = (float)microtime(true) - $time;
echo 'Время: ' . $time . PHP_EOL;

function analyze($imageResource)
{
    $width = max(imagesx($imageResource), 1);
    $height = max(imagesy($imageResource), 1);

    $squareScore = $width * $height * min($width, $height) / max($width, $height);
    $greenScore = 0;

    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $color = imagecolorat($imageResource, $x, $y);
            $r = ($color >> 16) & 255;
            $g = ($color >> 8) & 255;
            $b = ($color & 255);
            $greenScore += ($g > $r && $g > $b) ? 1 : 0;
            if ($greenScore > $squareScore) {
                return true;
            }
        }
    }
    return false;
}