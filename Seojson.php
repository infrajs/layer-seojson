<?php

namespace infrajs\layer\seojson;
use infrajs\load\Load;
use infrajs\view\View;
use infrajs\config\Config;
use infrajs\path\Path;
use infrajs\template\Template;

class Seojson
{
	public static $conf = [];
	public static function getSite() {
		$conf = Config::get('layer-seojson');
		if ($conf['site']) {
			return $conf['site'];
		} else {
			return preg_replace("/\/$/", "", View::getPath());
		}
	}
	public static function check(&$layer)
	{
		if (!empty($layer['seojsontpl'])) {
			$layer['seojson'] = Template::parse(array($layer['seojsontpl']), $layer);
		}
		if (empty($layer['seojson'])) return;
		
		$item = self::load($layer['seojson']);
		if (!$item) return;

		$html = View::html();
		

		if (!empty($item['image_src'])) {
			if (empty($item['names']['twitter:card'])) self::meta($html, $item, 'name', 'twitter:card', 'summary_large_image');
			self::meta($html, $item, 'link', 'image_src');
			self::meta($html, $item, 'property', 'og:image', $item['image_src']);
			self::meta($html, $item, 'name', 'twitter:image', $item['image_src']);
			//self::meta($html, $item, 'itemprop', 'image', $item['image_src']);
		}
		/*if (empty($item['canonical'])) {
			$query = preg_replace('/^\//','',$_SERVER['REQUEST_URI']);
			$query = preg_replace('/\?.*$/','',$query);
			$query = preg_replace('/\/+$/','',$query);

			$conf = Config::get('layer-seojson');
			if($conf['site']) {
				if ($query) $item['canonical'] = $conf['site'].'/'.$query;
				else $item['canonical'] = $conf['site'];
			} else {
				$item['canonical'] = View::getPath().$query;
			}
			
		}*/
		if (!empty($item['canonical'])) {
			self::meta($html, $item, 'link', 'canonical');
			self::meta($html, $item, 'name', 'twitter:site', $item['canonical']);
			self::meta($html, $item, 'property', 'og:url', $item['canonical']);
			//self::meta($html, $item, 'property', 'business:contact_data:website', $item['canonical']);
		}

		if (!empty($item['description'])) {
			self::meta($html, $item, 'name', 'description');
			self::meta($html, $item, 'property', 'og:description', $item['description']);
			self::meta($html, $item, 'name', 'twitter:description', $item['description']);
		}

		self::meta($html, $item, 'name', 'keywords');

		if (!empty($item['title'])) {
			self::meta($html, $item, 'title', 'title');
			self::meta($html, $item, 'property', 'og:title', $item['title']);
			self::meta($html, $item, 'name', 'twitter:title', $item['title']);
		}

		if (!empty($item['site_name'])) {
			self::meta($html, $item, 'property', 'site_name');
			self::meta($html, $item, 'itemprop', 'name', $item['site_name']);
			self::meta($html, $item, 'property', 'og:site_name', $item['site_name']);
		}

		if (empty($item['properties']['og:type'])) self::meta($html, $item, 'property', 'og:type', 'website');
		if (!empty($item['properties'])) {
			foreach ($item['properties'] as $k => $v) {
				self::meta($html, $item['properties'], 'property', $k, $v);
			}
		}
		if (!empty($item['names'])) {
			foreach ($item['names'] as $k => $v) {
				self::meta($html, $item['names'], 'name', $k, $v);
			}
		}
		if (!empty($item['itemprops'])) {
			foreach ($item['itemprops'] as $k => $v) {
				self::meta($html, $item['itemprops'], 'itemprop', $k, $v);
			}
		}

		View::html($html, true);
	}
	public static function load($src)
	{
		if (!$src) return ['result'=>0];
		$src = urldecode($src);
		$item = Load::loadJSON($src);

		if (!$item) {
			$item = array('result'=>0);
		}

		if (!empty($item['external'])) {
			if (!is_array($item['external'])) {
				$item['external'] = explode(', ', $item['external']);
			}

			foreach ($item['external'] as $esrc) {
				if (!Path::theme($esrc)) continue;
				$ext = self::load($esrc);
				foreach ($ext as $k => $v) {
					if (!isset($item[$k])) {
						$item[$k] = $v;
						continue;
					}
				}
				if (!empty($ext['properties'])) {
					foreach ($ext['properties'] as $k => $v) {
						if (isset($item['properties'][$k])) {
							continue;
						}
						$item['properties'][$k] = $v;
					}
				}
				if (!empty($ext['names'])) {
					foreach ($ext['names'] as $k => $v) {
						if (isset($item['names'][$k])) {
							continue;
						}
						$item['names'][$k] = $v;
					}
				}
				if (!empty($ext['itemprops'])) {
					foreach ($ext['itemprops'] as $k => $v) {
						if (isset($item['itemprops'][$k])) {
							continue;
						}
						$item['itemprops'][$k] = $v;
					}
				}
			}
		}

		return $item;
	}
	public static function value($value)
	{
		//load для <input value="...
		$value = preg_replace('/\$/', '&#36;', $value);
		$value = preg_replace('/"/', '&quot;', $value);

		return $value;
	}
	public static function meta(&$html, $item, $type, $name, $val = null)
	{
		if (is_null($val)&&isset($item[$name])) {
			$val = $item[$name];
		}
		if (empty($val)) return;

		$val = self::value($val);

		if ($type == 'property') {
			$r = preg_match('/<meta.*property=.{0,1}'.$name.'.{0,1}.*>/i', $html);
			if (!$r) {
				$html = str_ireplace('</head>', "\n\t<meta property=\"".$name.'" content="'.$val.'"/>'."\n</head>", $html);
			} else {
				$html = preg_replace('/(<meta.*property=.{0,1}'.$name.'.{0,1})(.*>)/i', '<meta property="'.$name.'" content="'.$val.'" >', $html);
			}
		} elseif ($type == 'title') {
			$r = preg_match('/<'.$name.'>/i', $html);
			if (!$r) {
				$html = str_ireplace('<head>', "<head>\n\t<".$name.'>'.$val.'</'.$name.'>', $html);
			} else {
				$html = preg_replace('/<'.$name.'>.*<\/'.$name.'>/i', '<'.$name.'>'.$val.'</'.$name.'>', $html);
			}
		} elseif ($type == 'name') {
			$r = preg_match('/<meta.*name=.{0,1}'.$name.'.{0,1}.*>/i', $html);
			if (!$r) {
				$html = str_ireplace('</head>', "\n\t<meta name=\"".$name.'" content="'.$val.'"/>'."\n</head>", $html);
			} else {
				$html = preg_replace('/(<meta.*name=.{0,1}'.$name.'.{0,1})(.*>)/i', '<meta name="'.$name.'" content="'.$val.'" >', $html);
			}
		} elseif ($type == 'link') {
			if (isset($item[$name])) {
				$r = preg_match('/<link.*rel=.{0,1}'.$name.'.{0,1}.*>/i', $html);
				if (!$r) {
					$html = str_ireplace('</head>', "\n\t<link rel=\"".$name.'" href="'.$val.'"/>'."\n</head>", $html);
				} else {
					$html = preg_replace('/(<link.*rel=.{0,1}'.$name.'.{0,1})(.*>)/i', '<link rel="'.$name.'" href="'.$val.'" >', $html);
				}
			}
		} elseif ($type == 'itemprop') {
			$r = preg_match('/<meta.*itemprop=.{0,1}'.$name.'.{0,1}.*>/i', $html);
			if (!$r) {
				$html = str_ireplace('</head>', "\n\t<meta itemprop=\"".$name.'" content="'.$val.'"/>'."\n</head>", $html);
			} else {
				$html = preg_replace('/(<meta.*itemprop=.{0,1}'.$name.'.{0,1})(.*>)/i', '<meta itemprop="'.$name.'" content="'.$val.'" >', $html);
			}
		}
	}
}
