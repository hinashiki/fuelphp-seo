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

class Query
{
	/**
	 * 現在のInput::get()からQuery値を検出し
	 * 正しい並び順へ並び替えて出力する
	 *
	 * @return string
	 */
	public static function build($uri = '')
	{
		$query = static::_make_query(\Fuel\Core\Input::get());
		return $uri.$query;
	}

	/**
	 * 出力済のURLを正規化する
	 *
	 * @param string $uri
	 * @return string
	 */
	public static function rebuild($uri)
	{
		// devide query and uri
		$query_pos = strpos($uri, '?');
		if($query_pos === false)
		{
			return $uri;
		}
		$query = preg_replace('/&amp;/', '&', substr($uri, ($query_pos + 1), strlen($uri) - ($query_pos + 1)));
		$uri = substr($uri, 0, $query_pos);

		// rebuild query
		// DO NOT USE parse_str(). That's brreak "+" string.
		$query_arr = array();
		foreach(explode('&', $query) as $q)
		{
			if(strlen($q) < 1)
			{
				continue;
			}
			$match = array();
			$key_and_value = explode('=', $q);
			if(preg_match('/^(.+)\[(.*)\]$/', $key_and_value[0], $match))
			{
				if( ! isset($query_arr[$match[1]]))
				{
					$query_arr[$match[1]] = array();
				}
				if(strlen($match[2]) < 1)
				{
					$query_arr[$match[1]][] = $key_and_value[1];
				}
				else
				{
					$query_arr[$match[1]][$match[2]] = $key_and_value[1];
				}
			}
			else
			{
				$query_arr[$key_and_value[0]] = \Arr::get($key_and_value, 1);
			}
		}
		$query = static::_make_query($query_arr);
		return $uri.$query;
	}

	/**
	 * query値配列からstring値をbuild
	 *
	 * @param array $query_arr key => value配列
	 * @return srting
	 */
	protected static function _make_query($query_arr)
	{
		// init check function
		$check_hash_array = function($arr)
		{
			foreach(array_keys($arr) as $key)
			{
				if(is_string($key))
				{
					return true;
				}
			}
			return false;
		};
		// sort array by name asc
		ksort($query_arr);
		$query = array();
		// re-insert if page exists, get first
		if(strlen(\Fuel\Core\Arr::get($query_arr, 'page')) > 0)
		{
			// if page = 1, it's remove
			if($query_arr['page'] > 1)
			{
				$query['page'] = $query_arr['page'];
			}
			unset($query_arr['page']);
		}
		// re-insert only value exists
		foreach($query_arr as $key => $value)
		{
			if(is_array($value))
			{
				// check has string key
				$is_hash_array = $check_hash_array($value);
				if($is_hash_array)
				{
					ksort($value, SORT_STRING);
				}
				else
				{
					sort($value, SORT_STRING);
				}
				foreach($value as $k => $v)
				{
					if(strlen($v) > 0)
					{
						$query[$key][$k] = $v;
					}
				}
			}
			elseif(strlen($value) > 0)
			{
				$query[$key] = $value;
			}
		}
		if(empty($query))
		{
			return '';
		}
		$return_string = '?';
		foreach($query as $key => $value)
		{
			if(is_array($value))
			{
				$is_hash_array = $check_hash_array($value);
				foreach($value as $k => $v)
				{
					if($is_hash_array)
					{
						$return_string .= $key.'['.$k.']='.rawurlencode($v).'&';
					}
					else
					{
						$return_string .= $key.'[]='.rawurlencode($v).'&';
					}
				}
			}
			else
			{
				$return_string .= $key.'='.rawurlencode($value).'&';
			}
		}
		return substr($return_string, 0, -1);
	}
}
