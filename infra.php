<?php
namespace infrajs\layer\seojson;
use infrajs\path\Path;
use infrajs\controller\Layer;
use infrajs\controller\Tpl;
use infrajs\event\Event;

Event::handler('layer.onshow', function (&$layer) {
	if (Layer::pop($layer,'onlyclient')) return;
	Seojson::check($layer);
}, 'seojson:tpl');