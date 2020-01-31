<?php
/*
Plugin Name: Cinema
Plugin URI: http://bixgomez.com
Description: Post types and taxonomies to support your independent cinema.
Author: Richard Gilbert
Author URI: http://bixgomez.com 
Version: .01
*/

// Custom post types.
include('posttype--film.php');
include('posttype--director.php');
include('posttype--format.php');
include('posttype--series.php');
include('posttype--country.php');

// Shortcodes.
include('shortcode--film-director.php');
include('shortcode--film-length.php');
include('shortcode--film-year.php');
include('shortcode--film-format.php');
include('shortcode--film-country.php');
include('shortcode--film-showtimes.php');
include('shortcode--film-poster.php');
include('shortcode--film-teaser.php');
include('shortcode--film-teasers.php');

// Custom functions.
include('function--max-screenings.php');
include('function--last-screening.php');

/**
 * If more than one page exists, return TRUE.
 */
function is_paginated() {
  global $wp_query;
  if ( $wp_query->max_num_pages > 1 ) {
    return true;
  } else {
    return false;
  }
}

/**
 * If last post in query, return TRUE.
 */
function is_last_post($wp_query) {
  $post_current = $wp_query->current_post + 1;
  $post_count = $wp_query->post_count;
  if ( $post_current == $post_count ) {
    return true;
  } else {
    return false;
  }
}
