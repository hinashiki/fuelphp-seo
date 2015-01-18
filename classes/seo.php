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

class Seo
{

	protected $_request = null;

	protected static $_instance = null;

	// for seo
	protected $_current_uri = '';
	protected $_current_get = '';
	protected $_noindex_flg = false;
	protected $_canonical_flg = false;
	protected $_canonical_uri = null;
	private $__skip = false;

	/**
	 *
	 * @param object $request Request object
	 * @return Seo object
	 */
	public static function instance($request)
	{
		if(is_null(static::$_instance))
		{
			return static::forge($request);
		}
		return static::$_instance;
	}

	/**
	 *
	 * @param object $request Request object
	 * @return Seo object
	 */
	public static function forge($request)
	{
		return new self($request);
	}

	/**
	 *
	 * @param object $request Request object
	 */
	public function __construct($request)
	{
		$this->_request = $request;
		// make common params
		// current_uri and current_get
		$this->_current_uri = preg_replace('/^\//', '', urldecode($_SERVER['REQUEST_URI']));
		$this->_current_get = '';
		$get_pos = strpos($this->_current_uri, '?');
		if($get_pos > 0)
		{
			$this->_current_get = substr($this->_current_uri, $get_pos, strlen($this->_current_uri) - $get_pos);
			$this->_current_uri = substr($this->_current_uri, 0, $get_pos);
		}

		/// routed_uri (routerで一旦正規化されたURI)
		$this->_routed_uri = $this->_request->uri->get();
		// action_indexの場合はrouted_uriにindex文字列を許容しない
		if(preg_match('/\/index/', $this->_routed_uri))
		{
			$this->_routed_uri = preg_replace('/\/index/', '', $this->_routed_uri);
		}

		// if routed_uri rendering 404, skip all checks
		if($this->_routed_uri === 'welcome/404')
		{
			$this->__skip = true;
		}
	}

	/**
	 * check redirect
	 *
	 * @return void
	 */
	public function check_redirect()
	{
		if($this->__skip === true)
		{
			return;
		}

		// check added www in production env
		if(\Fuel\Core\Fuel::$env === \Fuel\Core\Fuel::PRODUCTION)
		{
			if( ! preg_match('/^www\./', $_SERVER['HTTP_HOST']))
			{
				$new_uri = \Fuel\Core\Input::protocol().'://www.'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				\Fuel\Core\Response::redirect($new_uri, 'location', 301);
			}
		}

		// init
		$redirect_flg = false;
		$redirect_uri = $this->_current_uri.$this->_current_get;

		// reverse routing
		$check_uri = \Seo\Route::reverse_route($this->_routed_uri);
		if($check_uri !== $this->_routed_uri or $check_uri !== $this->_current_uri)
		{
			$redirect_flg = true;
			$redirect_uri = $check_uri.$this->_current_get;
		}

		// page=1 check (301 redirect)
		if(\Fuel\Core\Input::get('page') === '1')
		{
			$redirect_flg = true;
			$redirect_uri = preg_replace('/(&?)(page=1&?)/', '$1', $redirect_uri);
		}

		// last &, ? check (301 redirect)
		if(preg_match('/[&\?]$/', $redirect_uri))
		{
			$redirect_flg = true;
			$redirect_uri = preg_replace('/[&\?]$/', '', $redirect_uri);
		}

		// do 301 redirect
		if($redirect_flg)
		{
			\Fuel\Core\Session::keep_flash();
			// before redirect, if env is development and posted, set notify
			if(\Fuel\Core\Fuel::$env === \Fuel\Core\Fuel::DEVELOPMENT and \Fuel\Core\Input::post())
			{
				\Fuel\Core\Session::set_flash('error', 'Redirect occured, please check post url');
			}
			\Fuel\Core\Log::debug($this->_current_uri.$this->_current_get.': redirect to '.$redirect_uri);
			\Fuel\Core\Response::redirect($redirect_uri, 'location', 301);
		}
	}
}