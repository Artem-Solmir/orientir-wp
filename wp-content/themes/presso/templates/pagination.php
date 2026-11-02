<?php global $wp_query, $wp_rewrite; ?>
<?php if ( $wp_query->max_num_pages > 1 ) : ?>
	<div id="pagination" class="header-font clearfix">
		<?php
			$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
			$pagination = array(
				'base' => @add_query_arg( 'paged','%#%' ),
				'format' => '',
				'total' => $wp_query->max_num_pages,
				'current' => $current,
				'prev_text' => __( '&laquo;', 'envirra' ),
				'next_text' => __( '&raquo;', 'envirra' ),
				'type' => 'plain'
			);
			
			if( $wp_rewrite->using_permalinks() )
				$pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );

			if( !empty( $wp_query->query_vars['s'] ) )
				$pagination['add_args'] = array( 's' => urlencode( get_query_var( 's' ) ) );

			// echo '<span>' . sprintf( __( 'Page&nbsp;&nbsp;%s&nbsp;&nbsp;of&nbsp;&nbsp;%s', 'envirra' ), $current, $wp_query->max_num_pages ) . '</span>'.' ';
			echo paginate_links( $pagination );

			if ( false ) posts_nav_link();
		?>
	</div>
<?php endif; ?>