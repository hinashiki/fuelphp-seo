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

class Seo_Html
{
	/**
	 * create noindex tag
	 *
	 * @return string
	 */
	public static function noindex()
	{
		return '<link rel="robots" content="noindex, nofollow" />';
	}

	/**
	 * create canonical tag
	 *
	 * @param string $uri
	 * @return string
	 */
	public static function canonical($uri)
	{
		return '<link rel="canonical" href="'.$uri.'" />';
	}

	/**
	 * create nocache tag
	 *
	 * @return string
	 */
	public static function nocache()
	{
		$html = '<meta http-equiv="Pragma" content="no-cache" />'.PHP_EOL
		      . '<meta http-equiv="Cache-Control" content="nocache" />'.PHP_EOL
		      . '<meta http-equiv="Expires" content="0" />'.PHP_EOL;
		return $html;
	}

	/**
	 * check and create prev next tag
	 *
	 * @param object $Pagination
	 * @return string
	 */
	public static function prev_next(Pagination $Pagination)
	{
		if( ! $Pagination instanceof Pagination)
		{
			return '';
		}
		$return_html = '';
		// check prev
		if($Pagination->has_previous())
		{
			$return_html .= '<link rel="previous" href="'.$Pagination->get_previous_uri().'" />'."\n";
		}
		// check next
		if($Pagination->has_next())
		{
			$return_html .= '<link rel="next" href="'.$Pagination->get_next_uri().'" />'."\n";
		}
		return $return_html;
	}
}
