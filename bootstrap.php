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

\Autoloader::add_namespace('Seo', __DIR__.'/classes/');
\Autoloader::add_core_namespace('Seo');
\Autoloader::add_classes(array(
	'Seo\\Seo'        => __DIR__.'/classes/seo.php',
	'Seo\\Seo_Html'   => __DIR__.'/classes/seo/html.php',
	'Seo\\Route'      => __DIR__.'/classes/route.php',
	'Seo\\Query'      => __DIR__.'/classes/query.php',
	'Seo\\Pagination' => __DIR__.'/classes/pagination.php',
));
\Config::load('seo', true);
