<?php
/**
 * GET値(query)のURLが正規化用クラス
 *
 */
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
		$query = static::_make_query(Input::get());
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
		$query_arr = array();
		parse_str($query, $query_arr);
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
		// sort array by name asc
		ksort($query_arr);
		$query = array();
		// re-insert if page exists, get first
		if(strlen(Arr::get($query_arr, 'page')) > 0)
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
			if(strlen($value) > 0){
				$query[$key] = $value;
			}
		}
		if(empty($query))
		{
			return '';
		}
		return '?' . http_build_query($query);
	}
}
