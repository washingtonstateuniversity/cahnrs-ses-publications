<?php namespace CAHNRS\Plugin\SES_Pubs;

class Pubs_Category_CAHNRS_SES {

    public function __construct() {
		add_action( 'init', array( $this, 'add_custom_taxonomies' ), 999 );
	} 

    public function add_custom_taxonomies() {
        register_taxonomy('nonsesauthors', 'publication', array(
         'hierarchical' => true,
         'archive_layout' => 'full',
         'labels' => array(
           'name' => _x( 'Related SES Authors', 'taxonomy general name' ),
           'singular_name' => _x( 'Related SES Author', 'taxonomy singular name' ),
           'search_items' =>  __( 'Search Related SES Authors' ),
           'all_items' => __( 'All Related SES Authors' ),
           'parent_item' => __( 'Parent Related SES Author' ),
           'parent_item_colon' => __( 'Parent Related SES Author:' ),
           'edit_item' => __( 'Edit Related SES Author' ),
           'update_item' => __( 'Update Related SES Author' ),
           'add_new_item' => __( 'Add New Related SES Author' ),
           'new_item_name' => __( 'New Related SES Author Name' ),
           'menu_name' => __( 'Related SES Authors' ),
         ),
         'rewrite' => array(
           'slug' => 'nonsesauthors', // This controls the base slug that will display before each term
           'with_front' => false, // Don't display the category base before "/authors/"
           'hierarchical' => true // This will allow URL's like "/authors/authorname/subtopic/"
         ),
       ));  
     
       register_taxonomy('journals', 'publication', array(
         'hierarchical' => true,
         'archive_layout' => 'full',
         'labels' => array(
           'name' => _x( 'Journals', 'taxonomy general name' ),
           'singular_name' => _x( 'Journal', 'taxonomy singular name' ),
           'search_items' =>  __( 'Search Journals' ),
           'all_items' => __( 'All Journal' ),
           'parent_item' => __( 'Parent Journal' ),
           'parent_item_colon' => __( 'Parent Journal:' ),
           'edit_item' => __( 'Edit Journal' ),
           'update_item' => __( 'Update Journal' ),
           'add_new_item' => __( 'Add New Journal' ),
           'new_item_name' => __( 'New Journal Name' ),
           'menu_name' => __( 'Journals' ),
         ),
         'rewrite' => array(
           'slug' => 'journals', // This controls the base slug that will display before each term
           'with_front' => false, // Don't display the category base before "/journals/"
           'hierarchical' => true // This will allow URL's like "/journals/journalname/subtopic/"
         ),
       ));  
      
      register_taxonomy('yearspublished', 'publication', array(
         'hierarchical' => true,
         'archive_layout' => 'full',
         'labels' => array(
           'name' => _x( 'Years Published', 'taxonomy general name' ),
           'singular_name' => _x( 'Year Published', 'taxonomy singular name' ),
           'search_items' =>  __( 'Search Years Published' ),
           'all_items' => __( 'All Years Published' ),
           'parent_item' => __( 'Parent Year Published' ),
           'parent_item_colon' => __( 'Parent Year Published:' ),
           'edit_item' => __( 'Edit Year Published' ),
           'update_item' => __( 'Update Year Published' ),
           'add_new_item' => __( 'Add New Year Published' ),
           'new_item_name' => __( 'New Year Published' ),
           'menu_name' => __( 'Years Published' ),
         ),
         'rewrite' => array(
           'slug' => 'yearspublished', // This controls the base slug that will display before each term
           'with_front' => false, // Don't display the category base before "/authors/"
           'hierarchical' => false // This will allow URL's like "/authors/authorname/subtopic/"
         ),
       )); 
       
     }

}

$pubs_category_cahnrs_ses = new Pubs_Category_CAHNRS_SES();


