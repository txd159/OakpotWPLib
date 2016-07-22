<?php
namespace OakpotWPLib\Helper;

class PaginationMaker 
{

	private $configs=array(
		 'prev_lable' =>'&larr; Previous',
		'next_lable' => 'Next &rarr;',
		'show_first_page' => false,
		'show_last_page' => false,
		'wrapper_class' => '',
		'before' => '',
		'after' => ''
		);


	private $output_content='';

	function __construct($user_configs=array())
	{
		$this->configs=array_merge($this->configs,$user_configs);

	}

	private function generateOutput()
	{
		global $wpdb, $wp_query;
		$request = $wp_query->request;
		$posts_per_page = intval(get_query_var('posts_per_page'));
		$paged = intval(get_query_var('paged'));
		$numposts = $wp_query->found_posts;
		$max_page = $wp_query->max_num_pages;
		if ($numposts <= $posts_per_page) {
			return;
		}
		if (empty($paged) || $paged == 0) {
			$paged = 1;
		}
		$pages_to_show = 7;
		$pages_to_show_minus_1 = $pages_to_show - 1;
		$half_page_start = floor($pages_to_show_minus_1 / 2);
		$half_page_end = ceil($pages_to_show_minus_1 / 2);
		$start_page = $paged - $half_page_start;
		if ($start_page <= 0) {
			$start_page = 1;
		}
		$end_page = $paged + $half_page_end;
		if (($end_page - $start_page) != $pages_to_show_minus_1) {
			$end_page = $start_page + $pages_to_show_minus_1;
		}
		if ($end_page > $max_page) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page = $max_page;
		}
		if ($start_page <= 0) {
			$start_page = 1;
		}

		$this->output_content=$this->configs['before'] . '<div class="pagination ' . $this->configs['wrapper_class'] . '" role="navigation" aria-label="Pagination">' . "";
		if (($paged > 1) && $this->configs['show_first_page']) {
			$first_page_text = '<i class="material-icons">undo</i>';
			$this->output_content.='<a class="prev ui button" href="' . get_pagenum_link() . '" title="First">' . $first_page_text . '</a>';
		}

		$prevposts = get_previous_posts_link($this->configs['prev_lable']);
		if ($prevposts) {
			$this->output_content.='<li>' . $prevposts . '</li>';
		} else {
			$this->output_content.='<li class="disabled">' . $this->configs['prev_lable'] . '</li>';
		}

		for ($i = $start_page; $i <= $end_page; $i++) {
			if ($i == $paged) {
				$this->output_content.='<li class="current">' . $i . '</li>';
			} else {
				$this->output_content.='<li><a href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
			}
		}

		$nextpost = get_next_posts_link($this->configs['next_lable']);
		$this->output_content.='<li>' . $nextpost . '</li>';

		if (($end_page < $max_page) && $this->show_last_page) {
			$last_page_text = '<i class="material-icons">redo</i>';
			$this->output_content.='<a class="next ui button" href="' . get_pagenum_link($max_page) . '" title="Last">' . $last_page_text . '</a>';
		}

		$this->output_content.='</div>' . $this->configs['after'] . "";

	}

	public function output()
	{
		$this->generateOutput();
		echo $this->output_content;
	}

	public function getOutput()
	{
		$this->generateOutput();
		return $this->output_content;
	}

}

