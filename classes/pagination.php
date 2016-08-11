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

class Pagination extends \Fuel\Core\Pagination
{

	public $item_list = array();

	/**
	 * instance configuration values
	 */
	protected $config = array(
		'current_page'            => null,
		'offset'                  => 0,
		'per_page'                => 10,
		'total_pages'             => 0,
		'total_items'             => 0,
		'num_links'               => 5,
		'uri_segment'             => 'page',
		'show_first'              => false,
		'show_last'               => false,
		'pagination_url'          => null,
		'link_offset'             => 0.5,
		'through_seo'             => false,
	);

	/**
	 * constructor
	 *
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		if(!isset($this->through_seo) or $this->through_seo === false)
		{
			Seo::instance()->set_pagination($this);
		}
	}

	/**
	 * check has previous link
	 *
	 * @return boolean
	 */
	public function has_previous()
	{
		return ($this->config['total_pages'] > 1 and $this->config['calculated_page'] > 1);
	}

	/**
	 * get previous uri
	 *
	 * @return string
	 */
	public function get_previous_uri()
	{
		return $this->_make_link($this->config['calculated_page'] - 1);
	}

	/**
	 * check has next link
	 *
	 * @return boolean
	 */
	public function has_next()
	{
		return ($this->config['total_pages'] > 1 and $this->config['total_pages'] > $this->config['calculated_page']);
	}

	/**
	 * get next uri
	 *
	 * @return string
	 */
	public function get_next_uri()
	{
		return $this->_make_link($this->config['calculated_page'] + 1);
	}

	/**
	 * get offset
	 *
	 * @return int
	 */
	public function get_offset()
	{
		return $this->per_page * (\Fuel\Core\Input::get('page', 1) - 1);
	}


	/**
	 * @overwrap
	 */
	protected function _make_link($name)
	{
		$uri = parent::_make_link($name);
		return Query::rebuild($uri);
	}

	/**
	 * find for pagination
	 *
	 * @param  mixed $params array - like model find param
	 *                       Database_Query_Builder_Select instance - you can use raw DB model
	 *         boolean $need_count
	 * @return array
	 *          |-- list (array)
	 *          `-- count (int)
	 */
	public function find($params, $need_count = true)
	{
		if($params instanceof \Fuel\Core\Database_Query_Builder_Select)
		{
			$query_cnt = clone $params;
			$query_cnt->select_array(array(\Fuel\Core\DB::expr('COUNT(*) as cnt')), true);
			$query_list = $params;
			$query_list->limit($this->per_page)->offset($this->get_offset());
		}
		else
		{
			$default = array(
				'select'   => array('*'),
				'where'    => array(),
				'limit'    => $this->per_page,
				'offset'   => $this->get_offset(),
				'order_by' => array(array('id', 'ASC')),
			);
			$params = array_merge($default, $params);
			if( ! isset($params['from']))
			{
				throw new \Fuel\Core\Database_Exception('Query "from" not found!');
			}
			$query_list = \Fuel\Core\DB::select_array($params['select'])
				->from($params['from'])
				->limit($params['limit'])
				->offset($params['offset']);
			$query_cnt = \Fuel\Core\DB::select(\Fuel\Core\DB::expr('COUNT(*) as cnt'))
				->from($params['from']);
			foreach($params['where'] as $w)
			{
				if(count($w) > 1){
					call_user_func_array(array($query_list, 'where'), $w);
					call_user_func_array(array($query_cnt, 'where'), $w);
				}
				else
				{
					$query_list->where($w);
					$query_cnt->where($w);
				}
			}
			foreach($params['order_by'] as $order)
			{
				call_user_func_array(array($query_list, 'order_by'), $order);
			}
			if(isset($params['group_by']))
			{
				if(is_string($params['group_by']))
				{
					$params['group_by'] = array($params['group_by']);
				}
				call_user_func_array(array($query_list, 'group_by'), $params['group_by']);
				call_user_func_array(array($query_cnt, 'group_by'), $params['group_by']);
			}

		}
		$list = $query_list->execute()->as_array();
		$cnt = 0;
		if($need_count)
		{
			$cnt = $query_cnt->execute()->as_array();
			if(count($cnt) > 1)
			{
				$cnt = count($cnt);
			}
			else
			{
				$cnt = \Arr::get(\Arr::get($cnt, 0, array()), 'cnt', 0);
			}
		}
		// set items automatically
		$this->item_list   = $list;
		$this->total_items = $cnt;

		return array(
			'list'  => $list,
			'count' => $cnt,
		);
	}
}
