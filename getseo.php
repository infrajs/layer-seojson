<?php

use infrajs\ans\Ans;
use infrajs\layer\seojson\Seojson;

$src = Ans::GET('src');
$seo = Seojson::load($src);

return Ans::ans($seo);
