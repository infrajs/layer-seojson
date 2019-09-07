Event.handler('Controller.oncheck', function () {
	if (Controller.store().counter === 1) return;
	var layers = Controller.getWorkLayers();
	var path = false;
	Controller.run(layers, function (layer) {
		if (!Event.fire('Layer.isshow', layer)) return;
		if (layer.seojsontpl) layer.seojson = Template.parse([layer['seojsontpl']], layer);
		if (!layer.seojson) return;
		path = layer.seojson;
	});

	if (path) {
		var seo = Load.loadJSON('-layer-seojson/?src='+encodeURIComponent(encodeURIComponent(path)));
		if (seo && seo.title) document.title = seo.title;	
	}
});