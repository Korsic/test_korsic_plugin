<?php
/*Template Name: New Template
*/
get_header(); ?>
	<div id="primary">
		<div id="content" role="main">
			<?php
			$mypost = array( 'post_type' => 'movie_reviews', );
			$loop   = new WP_Query( $mypost );
			?>
			<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<header class="entry-header">
						<!-- Display featured image in right-aligned floating div -->
						<div style="float: right; margin: 10px">
							<?php the_post_thumbnail( array( 100, 100 ) ); ?>
						</div>
						<!-- Display Title and Author Name -->
						<strong>Title: </strong><?php the_title(); ?><br/>
						<strong>Director: </strong>
						<?php echo esc_html( get_post_meta( get_the_ID(), 'movie_director', true ) ); ?>
						<br/>
						<!-- Display yellow stars based on rating -->
						<form method="post" action="">
							<button name="button_buy_product">Купить</button>
						</form>
					</header>
					<!-- Display movie review contents -->
					<div class="entry-content"><?php the_content(); ?></div>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
<?php wp_reset_query(); ?>
<?php get_footer(); ?>