<?php namespace CAHNRS\Plugin\SES_Pubs;

class Pubs_Post_Type_CAHNRS_SES {

    public function __construct() {
		add_action( 'init', array( $this, 'add_custom_post_type' ) );
        add_action('the_content', array($this, 'cahnrs_pub_template'));
	} // End __construct

    public function add_custom_post_type(){
		
        $labels = array(
            'name'               => _x( 'SES Publications', 'post type general name', 'ses-Pubs' ),
            'singular_name'      => _x( 'SES Publication', 'post type singular name', 'ses-Pubs' ),
            'menu_name'          => _x( 'SES Publications', 'admin menu', 'ses-Pubs' ),
            'name_admin_bar'     => _x( 'SES Publication', 'add new on admin bar', 'ses-Pubs' ),
            'add_new'            => _x( 'Add New', 'Publication', 'ses-Pubs' ),
            'add_new_item'       => __( 'Add New SES Publication', 'ses-Pubs' ),
            'new_item'           => __( 'New SES Publication', 'ses-Pubs' ),
            'edit_item'          => __( 'Edit SES Publication', 'ses-Pubs' ),
            'view_item'          => __( 'View SES Publication', 'ses-Pubs' ),
            'all_items'          => __( 'All SES Publications', 'ses-Pubs' ),
            'search_items'       => __( 'Search SES Publications', 'ses-Pubs' ),
            'parent_item_colon'  => __( 'Parent SES Publications:', 'ses-Pubs' ),
            'not_found'          => __( 'No SES Publications found.', 'ses-Pubs' ),
            'not_found_in_trash' => __( 'No SES Publications found in Trash.', 'ses-Pubs' )
        ); // end $labels
    
        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'publication' ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => true,
            'menu_position'      => null,
            'taxonomies' => array( 'sesauthor','journal', 'yearspublished'),
            'supports'           => array( 'title', 'thumbnail', 'excerpt')			
        ); // end $args
    
        register_post_type( 'publication', $args );
        
    }

      public function cahnrswp_pub_fields( $content ){
 
        $pub_fields_string ='';		 				
        global $post;
                
         if( 'publication' == $post->post_type ) {
                       
            $authors_meta = get_post_meta(get_the_ID(), '_authors', TRUE);
           $sesauthorsdd_meta = get_post_meta(get_the_ID(), '_sesauthorsdd', TRUE);
           $issuepages_meta = get_post_meta(get_the_ID(), '_issue_pages', TRUE);
           $redirectURL_meta = get_post_meta(get_the_ID(), '_redirect_to', TRUE);
           $ses_author_list =  wp_get_object_terms( get_the_ID(), 'sesauthors' );	
           $nonses_author_list =  wp_get_object_terms( get_the_ID(), 'nonsesauthors' );
           
           $ses_journals_list =  wp_get_object_terms( get_the_ID(), 'journals' );	
           $ses_yearspublished_list =  wp_get_object_terms( get_the_ID(), 'yearspublished' );	
                   
                   
           $pub_fields_string .= '<br>'.$issuepages_meta.'</br>';		 							 
           $pub_fields_string .='<br>'.$redirectURL_meta.'</br>';
                    
                    if (! empty( $ses_author_list)){			
                     if ( ! is_wp_error( $ses_author_list ) ) {
                          foreach ( $ses_author_list as $ses_author) {
                              $pub_fields_string .= '<br>WSU Author(s): <strong>' . $ses_author->name . '</strong></br>'; 
                          } // end foreach
                     } // end not wp_error
                   } // end if not empty			 
        
                 
                  $pub_fields_string .= '<br>Non-WSU Author(s) <strong>'.$authors_meta.'</strong></br>';
                 
                  if (! empty( $ses_journals_list)){
                     if ( ! is_wp_error( $ses_journals_list ) ) {
                          foreach ( $ses_journals_list as $ses_journal) {
                             $pub_fields_string .= '<br>Jounnal Name: <strong>' . $ses_journal->name . '</strong></br>'; 
                          } // end foreach
                     } // end not wp_error
                   } // end if not empty
                 
        
                    
               if (! empty( $ses_yearspublished_list)){
                     if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                          foreach ( $ses_yearspublished_list as $ses_yearpublished) {
                              $pub_fields_string .= '<p>Years Published: <strong>(' . $ses_yearpublished->name . ')</strong></p> '; 
                          } // end foreach
                     } // end not wp_error
                   } // end if not empty	 
       
                
                   return $pub_fields_string;
               
                } // if publication 
                
         } // end cahnrswp_pub_fields	

         public function cahnrs_pub_template( $content ) {

            global $post;
    
            if ( 'publication' === $post->post_type ) {
                ob_start();
                include dirname( __FILE__ ) . '/../../../templates/single.php';
                $single_template = ob_get_clean();
                return $single_template;
            }
            return $content;
        }
}

$pubs_Post_Type_CAHNRS_SES = new Pubs_Post_Type_CAHNRS_SES();