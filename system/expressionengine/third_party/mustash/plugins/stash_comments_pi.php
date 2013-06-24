<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Stash_comments_pi
 * Mustash cache-breaking plugin
 *
 * @package		Mustash
 * @author  	Mark Croxton
 */

class Stash_comments_pi extends Mustash_plugin {

	/**
	 * Name
	 *
	 * @var 	string
	 * @access 	public
	 */
	public $name = 'Comments';

	/**
	 * Version
	 *
	 * @var 	string
	 * @access 	public
	 */
	public $version = '1.0.0';

	/**
	 * Extension hook priority
	 *
	 * @var 	integer
	 * @access 	public
	 */
	public $priority = '10';

	/**
	 * Extension hooks and associated markers
	 *
	 * @var 	array
	 * @access 	protected
	 */
	protected $hooks = array(
		'insert_comment_end' => array(
			'channel_id',
			'channel_name',
			'author_id',
		    'entry_id',
		    'url_title'
		),
		'delete_comment_additional' => array(
			'channel_id',
			'channel_name',
			'author_id',
		    'entry_id',
		    'url_title'
		),
		'update_comment_additional' => array(
			'channel_id',
			'channel_name',
			'author_id',
		    'entry_id',
		    'url_title'
		),
	);

	/**
	 * Constructor
	 * 
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->EE->load->model('mustash_channel_data');
	}

	/**
	 * Get groups for this object
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_groups()
	{
		return $this->EE->mustash_channel_data->get_channels();
	}

	/*
	================================================================
	Hooks
	================================================================
	*/

	/**
	 * Hook: insert_comment_end
	 *
	 * @access	public
	 * @param	array
	 * @param	string (y|n)
	 * @param	integer
	 * @return	void
	 */
	public function insert_comment_end($data, $comment_moderate, $comment_id)
	{
		/* $data
		Array
		(
		    [channel_id] => 1
		    [entry_id] => 1
		    [author_id] => 1
		    [name] => Mark Croxton
		    [email] => mcroxton@hallmark-design.co.uk
		    [url] => 
		    [location] => 
		    [comment] => asdasd
		    [comment_date] => 1371229847
		    [ip_address] => 192.168.0.6
		    [status] => o
		    [site_id] => 1
		)
		*/

		// if the comment is going to be moderated, the cached output won't change 
		if ($comment_moderate == 'n')
		{
			// prep marker data
			$markers = array(
				'channel_id' 	=> $data['channel_id'],
				'channel_name'	=> $this->EE->mustash_channel_data->get_channel_name($data['channel_id']),
				'author_id'		=> $data['author_id'],
			    'entry_id'		=> $data['entry_id'],
			    'url_title'		=> $this->EE->mustash_channel_data->get_url_title($data['entry_id']),
			);

			// flush cache
			$this->flush_cache(__FUNCTION__, $data['channel_id'], $markers);
		}	
	}

	/**
	 * Hook: delete_comment_additional
	 *
	 * @access	public
	 * @param	array Comment IDs being deleted
	 * @return	void
	 */
	public function delete_comment_additional($comment_ids)
	{
		foreach($comment_ids as $id)
		{
			// get comment data
			$comment = $this->EE->mustash_channel_data->get_comment($id);

			// prep marker data
			$markers = array(
				'channel_id' 	=> $comment['channel_id'],
				'channel_name'	=> $comment['channel_name'],
				'author_id'		=> $comment['author_id'],
			    'entry_id'		=> $comment['entry_id'],
			    'url_title'		=> $this->EE->mustash_channel_data->get_url_title($comment['entry_id']),
			);

			// flush cache for each comment
			$this->flush_cache(__FUNCTION__, $comment['channel_id'], $markers);
		}
	}

	/**
	 * Hook: update_comment_additional
	 *
	 * @access	public
	 * @param	integer
	 * @param	array
	 * @return	void
	 */
	public function update_comment_additional($comment_id, $data)
	{
		/*
		$data:
		Array
		(
		    [comment_id] => 4
		    [site_id] => 1
		    [entry_id] => 1
		    [channel_id] => 1
		    [author_id] => 1
		    [status] => c
		    [name] => Mark Croxton
		    [email] => mcroxton@hallmark-design.co.uk
		    [url] => 
		    [location] => 
		    [ip_address] => 192.168.0.6
		    [comment_date] => 1371229847
		    [edit_date] => 
		    [comment] => asdasd
		)
		*/

		// prep marker data
		$markers = array(
			'channel_id' 	=> $data['channel_id'],
			'channel_name'	=> $this->EE->mustash_channel_data->get_channel_name($data['channel_id']),
			'author_id'		=> $data['author_id'],
		    'entry_id'		=> $data['entry_id'],
		    'url_title'		=> $this->EE->mustash_channel_data->get_url_title($data['entry_id']),
		);

		// flush cache
		$this->flush_cache(__FUNCTION__, $data['channel_id'], $markers);
		
	}

}
