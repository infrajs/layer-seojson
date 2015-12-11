<?php
namespace infrajs\layer\seojson;
use infrajs\path\Path;
use infrajs\controller\Layer;
use infrajs\layer\tpl\Tpl;
use infrajs\event\Event;

Path::req('*controller/infra.php');
Event::handler('layer.onshow', function (&$layer) {
	//seojson
	if (Layer::pop($layer,'onlyclient')) return;
	Seojson::check($layer);
}, 'seojson:tpl');