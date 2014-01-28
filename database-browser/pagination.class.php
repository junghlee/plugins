<?php
// Pagination functions
// Originally from http://www.onextrapixel.com/2009/06/22/how-to-add-pagination-into-list-of-records-or-wordpress-plugin/
if ( !class_exists( "Paginator" ) ) {

	class Paginator {
		
		var $limit;
		var $querystringVar;
		var $count;
		var $start;
		var $currentPage;
		var $pageList;
		var $links;
		var $view;
		
		function Paginator( $limit = 10, $querystringVar = "p" ) {
		
			$this->limit = $limit;
			$this->querystringVar = $querystringVar;
			$this->findCurrentPage();
		
		}
		
		function findCurrentPage() {
		
			if ( ( !isset( $_GET[$this->querystringVar] ) ) || ( $_GET[$this->querystringVar] == "1" ) ) {
				$this->currentPage = 1;
			} else {
				$this->currentPage = (int)$_GET[$this->querystringVar];
			}
			
		}

		function findStart() {

			if ( $this->currentPage == 1 ) {
				$this->start = 0;
			} else {
				$this->start = ( $this->currentPage-1 ) * $this->limit;
			}
			return $this->start;
			
		}

		function findPages( $count ) {
		
			if ( $count < $this->limit ) {
				$this->view = $count;
			} else {
				$this->view = ( $this->start + 1 ) . '&ndash;' . ( $this->start + $this->limit );
			}
		
			$this->count = $count;
			$pages = ( ( $count % $this->limit ) == 0 ) ? $count / $this->limit : floor( $count / $this->limit ) + 1; 
			
			$this->pageList = paginate_links( array(
						'base' => add_query_arg( $this->querystringVar, '%#%' ),
						'format' => '',
						'prev_text' => __('&laquo;'),
						'next_text' => __('&raquo;'),
						'total' => $pages,
						'current' => $this->currentPage
					));
			
			$this->links = '
			<div class="tablenav">
			<div class="tablenav-pages">
			<span class="displaying-num">Displaying ' . $this->view . ' of ' . $this->count . '</span>
				' . $this->pageList . '
			</div>
			</div>
			';
			
			return $pages;
			
		}
		
	}

}
?>