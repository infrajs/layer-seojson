<?php
namespace infrajs\layer\seojson;
use infrajs\path\Path;
use infrajs\controller\Layer;
use infrajs\layer\tpl\Tpl;
use infrajs\event\Event;
use infrajs\infra\Infra;

Infra::req('controller');

Event::handler('layer.onshow', function (&$layer) {
	//seojson
	if (Layer::pop($layer,'onlyclient')) return;
	Seojson::check($layer);
}, 'seojson:tpl');