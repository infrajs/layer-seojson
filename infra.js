import { Event } from '/vendor/infrajs/event/Event.js'
import { Controller } from '/vendor/infrajs/controller/src/Controller.js'
import { Load } from '/vendor/akiyatkin/load/Load.js'

Controller.hand('check', () => {
	let path = false;
	Controller.run(Controller.getWorkLayers(), layer => {
 		if (!Event.fire('Layer.isshow', layer)) return
 		if (layer.seojsontpl) layer.seojson = Template.parse([layer['seojsontpl']], layer)
 		if (!layer.seojson) return
 		path = layer.seojson
 	}); 
 	if (!path) return
 	Load.fire('json', '-layer-seojson/?src='+encodeURIComponent(encodeURIComponent(path))).then(seo => {
 		if (!seo) return
 		if (!seo.title) return
 		document.title = seo.title	
 	})
})


// Event.handler('Controller.oncheck', function () {
	
// 	var layers = Controller.getWorkLayers();
// 	var path = false;
// 	Controller.run(layers, function (layer) {
// 		if (!Event.fire('Layer.isshow', layer)) return;
// 		if (layer.seojsontpl) layer.seojson = Template.parse([layer['seojsontpl']], layer);
// 		if (!layer.seojson) return;
// 		path = layer.seojson;
// 	});

// 	if (path) {
// 		var seo = Load.loadJSON('-layer-seojson/?src='+encodeURIComponent(encodeURIComponent(path)));
// 		if (seo && seo.title) document.title = seo.title;	
// 	}
// });