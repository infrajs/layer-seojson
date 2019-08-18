
Event.handler('Layer.onshow', function (layer) {
	if (Layer.pop(layer,'onlyclient')) return;
	if (layer.seojsontpl) layer.seojson = Template.parse([layer['seojsontpl']], layer);
	if (!layer.seojson) return;
	var seo = Load.loadJSON('-layer-seojson/getseo.php?src='+encodeURIComponent(layer.seojson));
	//console.log(seo);
	if (seo && seo.title) document.title = seo.title;
}, 'seojson:tpl');