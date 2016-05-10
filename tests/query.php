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
class Test_Query extends \Fuel\Core\TestCase
{
	public function test_build()
	{
		$_GET = array();
		$this->assertEquals('', \Seo\Query::build());

		$_GET = array(
			'a' => ''
		);
		$this->assertEquals('hoge/fuga', \Seo\Query::build('hoge/fuga'));

		$_GET = array(
			'a' => 'foo'
		);
		$this->assertEquals('?a=foo', \Seo\Query::build());

		$_GET = array(
			'a' => 'bar'
		);
		$this->assertEquals('fuga/hoge?a=bar', \Seo\Query::build('fuga/hoge'));

		$_GET = array(
			'z' => 'foo',
			'a' => 'bar',
			'c' => ''
		);
		$this->assertEquals('fuga/hoge?a=bar&z=foo', \Seo\Query::build('fuga/hoge'));

		$_GET = array(
			'a' => array(
				'foo',
			),
		);
		$this->assertEquals('fuga/hoge?a[]=foo', \Seo\Query::build('fuga/hoge'));

		$_GET = array(
			'a' => array(
				'foo',
				'bar',
			),
		);
		$this->assertEquals('fuga/hoge?a[]=bar&a[]=foo', \Seo\Query::build('fuga/hoge'));

		$_GET = array(
			'a' => array(
				'aa' => 'foo',
				'bb' => 'bar',
			),
		);
		$this->assertEquals('fuga/hoge?a[aa]=foo&a[bb]=bar', \Seo\Query::build('fuga/hoge'));

		$_GET = array(
			'a' => array(
				'aa' => 'foo',
				'bb' => 'bar',
				'baz',
			),
		);
		$this->assertEquals('fuga/hoge?a[0]=baz&a[aa]=foo&a[bb]=bar', \Seo\Query::build('fuga/hoge'));

	}

	public function test_rebuild()
	{
		// in normal
		$this->assertEquals(
			'hoge/fuga?a=1&b=2',
			\Seo\Query::rebuild('hoge/fuga?a=1&b=2')
		);
		$this->assertEquals(
			'hoge/fuga?a=2&b=1',
			\Seo\Query::rebuild('hoge/fuga?b=1&a=2')
		);
		$this->assertEquals(
			'hoge/fuga?page=5&a=2&b=1',
			\Seo\Query::rebuild('hoge/fuga?b=1&page=5&a=2')
		);
		$this->assertEquals(
			'hoge/fuga?page=5',
			\Seo\Query::rebuild('hoge/fuga?b=&a=&page=5')
		);
		$this->assertEquals(
			'https://hoge.fuga.com/hoge/fuga?page=5',
			\Seo\Query::rebuild('https://hoge.fuga.com/hoge/fuga?a=&b=&c=&page=5')
		);
		$this->assertEquals(
			'?a=2&b=1',
			\Seo\Query::rebuild('?b=1&a=2')
		);
		$this->assertEquals(
			'hoge/fuga?a[]=1&a[]=2',
			\Seo\Query::rebuild('hoge/fuga?a[]=1&a[]=2')
		);
		$this->assertEquals(
			'hoge/fuga?a[]=1&a[]=2',
			\Seo\Query::rebuild('hoge/fuga?a[]=2&a[]=1')
		);
		$this->assertEquals(
			'hoge/fuga?a[]=1&a[]=2&b[]=1',
			\Seo\Query::rebuild('hoge/fuga?a[]=1&a[]=2&b[]=1')
		);
		// ignore only numeric key
		$this->assertEquals(
			'hoge/fuga?a[]=1&a[]=2',
			\Seo\Query::rebuild('hoge/fuga?a[0]=1&a[1]=2')
		);
		$this->assertEquals(
			'hoge/fuga?a[aa]=2&a[bb]=1',
			\Seo\Query::rebuild('hoge/fuga?a[bb]=1&a[aa]=2')
		);
		$this->assertEquals(
			'hoge/fuga?a[0]=baz&a[aa]=2&a[bb]=1',
			\Seo\Query::rebuild('hoge/fuga?a[bb]=1&a[aa]=2&a[]=baz')
		);
		// bug. encoded ampersand decode and resort
		// @see https://github.com/fuel/core/issues/1608
		$this->assertEquals(
			'?a=2&b=1&c=2',
			\Seo\Query::rebuild('?b=1&amp;a=2&amp;c=2')
		);
		// remove if value not exists
		$this->assertEquals(
			'hoge/fuga?b=1',
			\Seo\Query::rebuild('hoge/fuga?a=&b=1')
		);
		// remove all
		$this->assertEquals(
			'hoge/fuga',
			\Seo\Query::rebuild('hoge/fuga?a=&b=')
		);
		// failure (?ナシはそのままデータを返す)
		$this->assertEquals(
			'b=1&a=2',
			\Seo\Query::rebuild('b=1&a=2')
		);
		// query文字列がない場合はそのまま返ってくる
		$this->assertEquals(
			'hoge/fuga',
			\Seo\Query::rebuild('hoge/fuga')
		);
	}
}
