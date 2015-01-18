<?php
\Fuel\Core\Package::load('fuelphp-seo');
/**
 * Seo Package
 *
 * @package    Seo
 * @version    0.1
 * @author     Hinashiki
 * @license    MIT License
 * @copyright  2015 - Hinashiki
 * @link       https://github.com/hinashiki/fuelphp-seo
 * @group      Hinashiki-fuelphp-seo
 */
class Test_Route extends \Fuel\Core\TestCase
{
	/**
	 * confirm reverser_route
	 *
	 * @return void
	 */
	public function test_reverse_route()
	{
		// add router, can reverse
		\Fuel\Core\Router::add('sample', 'hoge/fuga');
		$this->assertEquals(
			Route::reverse_route('hoge/fuga'),
			'sample'
		);
		// delete router, can't reverse
		\Fuel\Core\Router::delete('sample');
		$this->assertEquals(
			Route::reverse_route('hoge/fuga'),
			'hoge/fuga'
		);

		// high level reverse routing
		\Fuel\Core\Router::add('cat/(:num)', 'hoge/category/$1');
		$this->assertEquals(
			Route::reverse_route('hoge/category/35'),
			'cat/35'
		);
		// no num no matched
		$this->assertEquals(
			Route::reverse_route('hoge/category/hoge'),
			'hoge/category/hoge'
		);
		\Fuel\Core\Router::delete('cat/(:num)');

		// multiple value reverse routing
		\Fuel\Core\Router::add('cat/(:num)/(:alpha)', 'a/i/$2/$1');
		$this->assertEquals(
			Route::reverse_route('a/i/u/1'),
			'cat/1/u'
		);
		\Fuel\Core\Router::delete('cat/(:num)/(:alpha)');

		\Fuel\Core\Router::add('cat/(:num)/(:alpha)/(:alnum)', 'a/i/$3/$1/$2');
		$this->assertEquals(
			Route::reverse_route('a/i/0a2/33/str'),
			'cat/33/str/0a2'
		);
		\Fuel\Core\Router::delete('cat/(:num)/(:alpha)/(:alnum)');
	}
}
