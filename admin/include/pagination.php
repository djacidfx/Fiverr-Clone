<?php

class Pagination 
{
	/**
	 * Current Page
	 *
	 * @var integer
	 */
	var $page;
	
	/**
	 * Size of the records per page
	 *
	 * @var integer
	 */
	var $size;
	
	/**
	 * Total records
	 *
	 * @var integer
	 */
	var $total_records;
	
	/**
	 * Link used to build navigation
	 *
	 * @var string
	 */
	var $link;
    var $align;
	/**
	 * Class Constructor
	 *
	 * @param integer $page
	 * @param integer $size
	 * @param integer $total_records
	 */
	function __construct($page = null, $size = null, $total_records = null, $align = 'justify-content-center')
	{
		$this->page = $page;
		$this->size = $size;
		$this->total_records = $total_records;
        $this->align = $total_records;
	}
	
	/**
	 * Set's the current page
	 *
	 * @param unknown_type $page
	 */
	function setPage($page)
	{
		$this->page = 0+$page;
	}
	
	/**
	 * Set's the records per page
	 *
	 * @param integer $size
	 */
	function setSize($size)
	{
		$this->size = 0+$size;
	}
		
	/**
	 * Set's total records
	 *
	 * @param integer $total
	 */
	function setTotalRecords($total)
	{
		$this->total_records = 0+$total;
	}

    function setAlign($align)
    {
        $this->align = $align;
    }
	
	/**
	 * Sets the link url for navigation pages
	 *
	 * @param string $url
	 */
	function setLink($url)
	{
		$this->link = $url;
	}
	
	/**
	 * Returns the LIMIT sql statement
	 *
	 * @return string
	 */
	function getLimitSql()
	{
		$sql = "LIMIT " . $this->getLimit();
		return $sql;
	}
		
	/**
	 * Get the LIMIT statment
	 *
	 * @return string
	 */
	function getLimit()
	{
		if ($this->total_records == 0)
		{
			$lastpage = 0;
		}
		else 
		{
			$lastpage = ceil($this->total_records/$this->size);
		}
		
		$page = $this->page;		
		
		if ($this->page < 1)
		{
			$page = 1;
		} 
		else if ($this->page > $lastpage && $lastpage > 0)
		{
			$page = $lastpage;
		}
		else 
		{
			$page = $this->page;
		}
		
		$sql = ($page - 1) * $this->size . "," . $this->size;
		
		return $sql;
	}
	
	/**
	 * Creates page navigation links
	 *
	 * @return 	string
	 */
	function create_links()
	{
		$totalItems = $this->total_records;
		$perPage = $this->size;
		$currentPage = $this->page;
		$link = $this->link;
		
		$totalPages = floor($totalItems / $perPage);
		$totalPages += ($totalItems % $perPage != 0) ? 1 : 0;

		if ($totalPages < 1 || $totalPages == 1){
			return null;
		}

		$output = null;
			
		$loopStart = 1; 
		$loopEnd = $totalPages;

		if ($totalPages > 7)
		{
			if ($currentPage <= 7)
			{
				$loopStart = 1;
				$loopEnd = 7;
			}
			else if ($currentPage >= $totalPages - 4)
			{
				$loopStart = $totalPages - 6;
				$loopEnd = $totalPages;
			}
			else
			{
				$loopStart = $currentPage - 2;
				$loopEnd = $currentPage + 2;
			}
		}

		if ($loopStart != 1){
			$output .= sprintf('<li class="page-item active"><a class="page-link" href="' . $link . '"> &#171; </a></li>', '1');
		}
		
		if ($currentPage > 1){
			$output .= sprintf('<li class="page-item"><a class="page-link" href="' . $link . '">Previous</a></li>', $currentPage - 1);
		}
		
		for ($i = $loopStart; $i <= $loopEnd; $i++)
		{
			if ($i == $currentPage){
				$output .= '<li class="page-item active"><a class="page-link" href="'.$_SERVER['REQUEST_URI'].'">' . $i . '</a></li> ';
			} else {
				$output .= sprintf('<li class="page-item"><a class="page-link" href="' . $link . '">', $i) . $i . '</a></li> ';
			}
		}

		if ($currentPage < $totalPages){
			$output .= sprintf('<li class="page-item"><a class="page-link" href="' . $link . '">Next</a></li>', $currentPage + 1);
		}
		
		if ($loopEnd != $totalPages){
			$output .= sprintf('<li class="page-item"><a class="page-link" href="' . $link . '"> &#187; </a></li>', $totalPages);
		}

		return '<ul class="pagination pagination-sm '.$this->align.'">' . $output . '</ul>';
	}
}

?>