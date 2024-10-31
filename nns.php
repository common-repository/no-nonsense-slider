<?php
/**
 * Plugin Name: No Nonsense Slider
 * Plugin URI: http://swiftthemes.com/nns/
 * Description: A very lightweight slider for WordPress built using [Unslider](https://idiot.github.io/unslider/).
 * Around 10KB footprint, less than 5KB when gzipped.
 * * Version: 0.12
 * Author: Satish Gandham
 * Author URI: http://SatishGandham.Com
 *
 * @author Satish Gandham <hello@satishgandham.com>
 * License: GPLv2 or later
 *
 */

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


define( 'NNS_URI', plugin_dir_url( __FILE__ ) );


add_action( 'wp_enqueue_scripts', 'nns_register_styles', 8 );
add_action( 'wp_enqueue_scripts', 'nns_enqueue_styles', 9 );

add_action( 'wp_enqueue_scripts', 'nns_register_scripts', 8 );
add_action( 'wp_enqueue_scripts', 'nns_enqueue_scripts', 9 );


function nns_register_styles() {
	wp_register_style( 'nns-slider-styles', NNS_URI . 'assets/css/unslider.css' );
}

function nns_enqueue_styles() {
	wp_enqueue_style( 'nns-slider-styles' );
}

function nns_register_scripts() {
	wp_register_script( 'nns-unslider', NNS_URI . 'assets/js/unslider-min.js', array( 'jquery' ) );
}

function nns_enqueue_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'nns-unslider' );
}

//add_action( 'pagespeed_after_header', 'nns_home_slider', 8 );
//add_action( 'pagespeed_content_start', 'nns_query_slider', 8 );

function nns_home_slider() {

	if ( ! is_home() ) {
		return;
	}
	$args        = array(
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => 4,
	);
	$recentPosts = new WP_Query( $args );
	if ( have_posts() ) :
		$size = array( 1428, 800 );
		?>
        <div class="he-slider" style="height: 800px">
            <ul>
				<?php while ( $recentPosts->have_posts() ) : $recentPosts->the_post(); ?>
                    <li>
                        <a class="image-bg"
                           style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url( null, $size ) ) ?>') ">
                            <div class="caption">
                                <h2 class="post-title">
									<?php the_title(); ?>
                                </h2>
								<?php the_excerpt() ?>
                            </div>
                        </a>
                    </li>
				<?php endwhile; ?>
            </ul>
        </div>
		<?php
	endif;
}

/**
 * @param $query_args arguments for wp_query
 * @param string $template Which style to use for slides
 * @param array $img_size image sizes
 * @param bool $show_excerpt show excerpts or not
 * @param string $classes CSS classes for the slider.
 */
function nns_query_slider(
	$query_args, $template = '', $img_size = array(
	1200,
	600
), $show_excerpt = false, $classes = ''
) {


	$defaults    = array(
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => 4,
	);
	$query_args  = wp_parse_args( $query_args, $defaults );
	$recentPosts = new WP_Query( $query_args );
	$height      = $img_size[1] . 'px';

	if ( have_posts() ) :

		?>
        <div class="nns-slider" style="height: <?php echo $height; ?>">
            <ul>
				<?php
				while ( $recentPosts->have_posts() ) : $recentPosts->the_post();
					if ( 'background_image' === $template ) {
						nns_slide_background_image( $img_size, $show_excerpt );
					} else {
						nns_slide_inline_image( $img_size, $show_excerpt );
					}
				endwhile;
				?>
            </ul>
        </div>
		<?php
	endif;
}


function nns_slide_background_image( $size, $excerpt = false ) {
	?>
    <li>
        <div class="image-bg" onclick="window.open('<?php the_permalink(); ?>','_self');return false;"
             style="background-image:url('<?php echo esc_url( get_the_post_thumbnail_url( null, $size ) ) ?>'); "
        >
            <div class="caption">
                <h2 class="post-title">
					<?php the_title(); ?>
                </h2>
				<?php if ( $excerpt ) {
					the_excerpt();
				} ?>
            </div>
        </div>
    </li>
	<?php
}

function nns_slide_inline_image( $size, $excerpt = false ) {
	?>
    <li>
        <a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( $size, array( 'class' => 'alignleft' ) ) ?></a>
        <div class="caption">
            <h2 class="post-title">
                <a href="<?php the_permalink(); ?>"
                   title="<?php printf( esc_attr__( 'Permalink to %s', 'swift' ), the_title_attribute( 'echo=0' ) ); ?>"
                   rel="bookmark"><?php the_title(); ?> </a>
            </h2>
			<?php if ( $excerpt ) {
				the_excerpt();
			} ?>
        </div>
    </li>
	<?php
}