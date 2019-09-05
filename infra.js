Event.handler('Controller.oncheck', function () {
	if (Controller.store().counter === 1) return;
	var layers = Controller.getWorkLayers();
	Controller.run(layers, function (layer) {
		if (!Event.fire('Layer.isshow', layer)) return;
		if (layer.seojsontpl) layer.seojson = Template.parse([layer['seojsontpl']], layer);
		if (!layer.seojson) return;
		var seo = Load.loadJSON('-layer-seojson/?src='+encodeURIComponent(layer.seojson));
		if (seo && seo.title) document.title = seo.title;
	});
});