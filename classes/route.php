<?php
/**
 * Seo Package
 *
 * @package    Seo
 * @version    0.1
 * @author     Hinashiki
 * @license    MIT License
 * @copyright  2015 - Hinashiki
 * @link       https://github.com/hinashiki/fuelphp-seo
 */

namespace Seo;

class Route extends \Fuel\Core\Route
{
	/**
	 *
	 * @return string
	 */
	public function get_compiled()
	{
		return $this->search;
	}

	/**
	 * reverse routing
	 *
	 * @param string $uri
	 * @return string new_uri
	 */
	public static function reverse_route($uri)
	{
		foreach(\Fuel\Core\Router::$routes as $Route)
		{
			// start reverse routing
			if(array_slice(explode('/', $uri), 0, 2) === array_slice(explode('/', $Route->translation), 0, 2))
			{
				// make pattern
				$i = 1;
				$pattern = $Route->translation;
				$matched = array();
				preg_match_all('/(\(.+?\))/', $Route->get_compiled(), $matched);
				while(strpos($Route->translation, '$'.$i) !== false)
				{
					$pattern = str_replace('$'.$i, $matched[1][$i - 1], $pattern);
					$i++;
				}
				// make replacement
				$i = 1;
				$matched = array();
				preg_match_all('/(\(.+?\))/', $pattern, $matched);
				$replacement = $Route->get_compiled();
				while(strpos($Route->translation, '$'.$i) !== false)
				{
					$replacement = str_replace($matched[1][$i - 1], '$'.$i, $replacement);
					$i++;
				}
				// make regex pattern
				$pattern = '/^'.str_replace('/', '\/', $pattern).'$/';
				$uri = preg_replace($pattern, $replacement, $uri);
			}
		}
		return $uri;
	}
}
