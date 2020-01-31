<?php

function film_teaser_function($atts = [], $content = null, $tag = '') {

  // normalize attribute keys, lowercase
  $atts = array_change_key_case((array)$atts, CASE_LOWER);

  // override default attributes with user attributes
  $filmteaser_atts = shortcode_atts([
    'film_id' => '0',
  ], $atts, $tag);

  $film_id = $filmteaser_atts['film_id'];

  $query = new WP_Query( array( 'p' => $film_id, 'post_type' => 'film' ) );

  $output = '';

  // open box
  $output .= '<div class="film-teaser">';

    if ( $query->have_posts() ){
      while ( $query->have_posts() ) {

        $query->the_post();

        $first_screening = get_field('first_screening', $post_id);
        $last_screening = get_field('last_screening', $post_id);

        $this_director = do_shortcode('[film_director]');
        $this_director = (string) $this_director;

        $this_year = do_shortcode('[film_year]');
        $this_year = (string) $this_year;

        $this_country = do_shortcode('[film_country]');
        $this_country = (string) $this_country;

        $this_length = do_shortcode('[film_length]');
        $this_length = (string) $this_length;

        $this_format = do_shortcode('[film_format]');
        $this_format = (string) $this_format;

        $this_description = get_the_content();
        $this_description = wpautop( $this_description, true/false );

        $output .= '<div class="film-teaser--sidebar">';
        $output .= do_shortcode('[film_poster]');
        $output .= '</div>';

        $output .= '<div class="film-teaser--main">';

        $output .= '<h2 class="film-teaser--title">';
        $output .=  get_the_title();
        $output .= '</h2>';

        $playing_from = gmdate("M j", $first_screening);
        $playing_until =  gmdate("M j", $last_screening);

        $output .= '<div class="film-teaser--film-info">';

        $output .= '<div class="film-teaser--director">';
        $output .= $this_director;
        if ($this_director && $this_year) { $output .= ' · '; }
        $output .= $this_year;
        if ($this_country) { $output .= ' · '; }
        $output .= $this_country;
        $output .= '</div>';

        $output .= '<div class="film-teaser--format">';
        $output .= $this_length;
        if ($this_length && $this_format) {  $output .= ' · '; }
        $output .= $this_format;
        $output .= '</div>';

        $output .= '<div class="film-teaser--screening-range">';
        $output .= 'Playing ' . $playing_from . ' through ' . $playing_until;
        $output .= '</div>';

        $output .= '</div>';

        $output .= '<div class="film-teaser--content">';
        $output .= $this_description;

        $output .= '</div>';

        $output .= '<div class="film-teaser--screenings">';
        $output .= do_shortcode('[film_showtimes]');
        $output .= '</div>';
        $output .= '</div>';
      }
    }

  // close box
  $output .= '</div>';

  return $output;
}

function film_teaser_init() {
  add_shortcode('film_teaser', 'film_teaser_function');
}

add_action('init', 'film_teaser_init');
