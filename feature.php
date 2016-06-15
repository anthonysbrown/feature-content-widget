<?php
/*
Plugin Name: Feature Content Widget for Cinqsens
Plugin URI: http://codeable.io
Description: A widget with a custom post type that allows you to view content, built for cinqsens
Version: 1.0.0
Author: Anthony Brown
Author URI: http://codeable.io
*/
$feature_content_widget = new feature_content_widget;
add_action('wp_enqueue_scripts',array($feature_content_widget, 'scripts'));

add_action( 'init',array($feature_content_widget, 'post_type'), 0 );
add_shortcode('feature_content_box', array($feature_content_widget, 'shortcode'));


add_action( 'wp_ajax_fcw_ajax_solutions',  array($feature_content_widget, 'ajax_solutions') );
add_action( 'wp_ajax_nopriv_fcw_ajax_solutions', array($feature_content_widget, 'ajax_solutions'));

class feature_content_widget{
	
	
	
	
	
	
	function scripts(){
		
	
	$vars = array('ajax_url' =>	admin_url( 'admin-ajax.php' ),'fcw_asetts'=> plugins_url('asetts', __FILE__));
	
	wp_enqueue_script('jquery');
 
	wp_enqueue_script( 'fcw-select', plugins_url('asetts/js/select.js', __FILE__),array('jquery'));
	wp_register_script( 'fcw-scripts', plugins_url('asetts/js/scripts.js', __FILE__),array('jquery','fcw-select'));
	wp_localize_script( 'fcw-scripts', 'fcw_vars',$vars);
	wp_enqueue_script('fcw-scripts');
	
	
	wp_enqueue_style('fcw-style', plugins_url('asetts/css/style.css', __FILE__));
	}



function problems_dropdown(){
	$terms = get_terms( 'problem', array(
    'hide_empty' => false,	'orderby' =>'title',
) );


	
	$h.= '<select id="fcw-problems-dropdown" class="nice_select"><option value="">Choisir</option>';
	
		if($terms){
			
			foreach ($terms as $term){
				
			$h .='<option value="'.$term->term_id.'">'.$term->name.'</option>';	
			}
		}
	
	$h .= '</select>';
	 wp_reset_query();
	return $h;
}

function get_solution_terms($post_id){
	
	
$terms = wp_get_post_terms( $post_id, 'type');

	if($terms){
		foreach($terms as $term){
			
			$list_terms[] = $term->name;
			
		}
		
	return $list_terms;	
	}else{
	return array();	
	}
	
}
function get_solution_image($post_id){
	
	$terms =  $this->get_solution_terms($post_id);
	
	if(in_array('Visage', $terms)){
	$image = 'visage.jpg';	
	}
	if(in_array('Corps', $terms)){
	$image = 'corps.jpg';	
	}
	if(in_array('Corps', $terms) && in_array('Visage', $terms)){
	$image = 'visage_et_corps.jpg';	
	}
	
	return  plugins_url('asetts/images/'.$image.'', __FILE__);
	
}
function get_solution_url($post_id){

if($post_id != ''){
	
	
	return esc_url( get_permalink($post_id) );
}else{
	
return false;	
}
	
	
}
function  ajax_solutions($problem_id = false){
		add_theme_support( 'post-thumbnails' );
	
	if($problem_id == false){
	$problem_id = $_POST['problem_id'];
	}
	
	$args = array(
	'post_type' => 'treatment',
	'orderby' =>'title',
	'tax_query' => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'problem',
			'field'    => 'term_id',
			'terms'    => $problem_id,
		)
	),
);

$the_query = new WP_Query( $args );

if ( $the_query->have_posts() ) {
	while ( $the_query->have_posts() ) : $the_query->the_post(); 
	
	$this->get_solution_image(get_the_id());
	
	$solutions = $this->get_solution_terms(get_the_id());
	$solution_output .= '<span class="fcw-solutions-list">';
	foreach($solutions as $solution){
	$solution_output .= '<span>'.$solution.'</span>';	
	}
	$solution_output .='</span>';
	echo '<div class="fcw-solution-row">
			<div class="fcw-solution-row-left">
			<a href="'.$this->get_solution_url(fcw_url_get_meta( 'fcw_url_treatment_url' )).'" ><img src="'.$this->get_solution_image(get_the_id()).'"></a>
			</div>
			<div class="fcw-solution-row-middle"><h2><a href="'.$this->get_solution_url(fcw_url_get_meta( 'fcw_url_treatment_url' )).'" >'.get_the_title().'</a></h2><p>'.$solution_output.' - <span>'.get_the_excerpt().'</span></p></div>
			<div class="fcw-solution-row-right"><a href="'.$this->get_solution_url(fcw_url_get_meta( 'fcw_url_treatment_url' )).'" class="button fcw-button">EN SAVOIR PLUS</a></div>
			';
	
	
	echo '<div style="clear:both"></div></div>';
	unset($solutions);unset($solution_output);
	endwhile;
}

	 wp_reset_query();	
die();	
}

function shortcode($atts){
	
return $this->view($atts);
	
}
function view($atts){
	
	
	
	$h .= '<div class="fcw-wrapper">';
	$h .= '<div class="fcw-header">';
	$h .= '<div class="fcw-header-left"><hr>QUE DÉSIREZ-VOUS TRAITER ?</div>';
	$h .= '<div class="fcw-header-right">'.$this->problems_dropdown().'</div>';
	$h .= '<div style="clear:both"></div></div>';
	$h .= '<div class="fcw-solutions"></div>';
	$h .= '<div style="clear:both"></div></div>';
	return $h;
}

function post_type() {

	$labels = array(
		'name'                  => _x( 'Treatments', 'Post Type General Name', 'feature_content_widget' ),
		'singular_name'         => _x( 'Treatment', 'Post Type Singular Name', 'feature_content_widget' ),
		'menu_name'             => __( 'Treatments', 'feature_content_widget' ),
		'name_admin_bar'        => __( 'Treatments', 'feature_content_widget' ),
		'archives'              => __( 'Item Archives', 'feature_content_widget' ),
		'parent_item_colon'     => __( 'Parent Item:', 'feature_content_widget' ),
		'all_items'             => __( 'All Items', 'feature_content_widget' ),
		'add_new_item'          => __( 'Add New Item', 'feature_content_widget' ),
		'add_new'               => __( 'Add New', 'feature_content_widget' ),
		'new_item'              => __( 'New Item', 'feature_content_widget' ),
		'edit_item'             => __( 'Edit Item', 'feature_content_widget' ),
		'update_item'           => __( 'Update Item', 'feature_content_widget' ),
		'view_item'             => __( 'View Item', 'feature_content_widget' ),
		'search_items'          => __( 'Search Item', 'feature_content_widget' ),
		'not_found'             => __( 'Not found', 'feature_content_widget' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'feature_content_widget' ),
		'featured_image'        => __( 'Featured Image', 'feature_content_widget' ),
		'set_featured_image'    => __( 'Set featured image', 'feature_content_widget' ),
		'remove_featured_image' => __( 'Remove featured image', 'feature_content_widget' ),
		'use_featured_image'    => __( 'Use as featured image', 'feature_content_widget' ),
		'insert_into_item'      => __( 'Insert into item', 'feature_content_widget' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'feature_content_widget' ),
		'items_list'            => __( 'Items list', 'feature_content_widget' ),
		'items_list_navigation' => __( 'Items list navigation', 'feature_content_widget' ),
		'filter_items_list'     => __( 'Filter items list', 'feature_content_widget' ),
	);
	$args = array(
		'label'                 => __( 'Treatment', 'feature_content_widget' ),
		'description'           => __( 'Treaments', 'feature_content_widget' ),
		'labels'                => $labels,
		'supports'              => array( 'title',  'excerpt'),
		'taxonomies'            => array(false),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-visibility',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'treatment', $args );
	
	
	$labels = array(
		'name'                       => _x( 'Problems', 'taxonomy general name' ),
		'singular_name'              => _x( 'Problem', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Problems' ),
		'popular_items'              => __( 'Popular Problems' ),
		'all_items'                  => __( 'All Problems' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Problem' ),
		'update_item'                => __( 'Update Problem' ),
		'add_new_item'               => __( 'Add New Problem' ),
		'new_item_name'              => __( 'New Problem Name' ),
		'separate_items_with_commas' => __( 'Separate problems with commas' ),
		'add_or_remove_items'        => __( 'Add or remove problems' ),
		'choose_from_most_used'      => __( 'Choose from the most used problems' ),
		'not_found'                  => __( 'No problems found.' ),
		'menu_name'                  => __( 'Problems' ),
	);
	register_taxonomy(
		'problem',
		'treatment',
		array(
			'labels'                => $labels,
			'rewrite' => array( 'slug' => 'problem' ),
			'hierarchical' => true,
		)
	);
unset($labels);
$labels = array(
		'name'                       => _x( 'Types', 'taxonomy general name' ),
		'singular_name'              => _x( 'Type', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Types' ),
		'popular_items'              => __( 'Popular Types' ),
		'all_items'                  => __( 'All Types' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Type' ),
		'update_item'                => __( 'Update Type' ),
		'add_new_item'               => __( 'Add New Type' ),
		'new_item_name'              => __( 'New Type Name' ),
		'separate_items_with_commas' => __( 'Separate Types with commas' ),
		'add_or_remove_items'        => __( 'Add or remove Types' ),
		'choose_from_most_used'      => __( 'Choose from the most used Types' ),
		'not_found'                  => __( 'No Types found.' ),
		'menu_name'                  => __( 'Types' ),
	);
register_taxonomy(
		'type',
		'treatment',
		array(
			'labels'                => $labels,
			'rewrite' => array( 'slug' => 'type' ),
			'hierarchical' => true,
		)
	);
}
	
	
}
include_once('widgets/filter-widget.php');
include_once('meta/url.php');