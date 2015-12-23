<?php

use infrajs\ans\Ans;
use infrajs\layer\seojson\Seojson;

$src = $_SERVER['QUERY_STRING'];
$seo = Seojson::load($src);

return Ans::ans($seo);
