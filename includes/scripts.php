<?php namespace CAHNRS\Plugin\SES_Pubs;

class Scripts_CAHNRS_SES{

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_related_sesauthors_meta_box_to_sespublications') );
        
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_related_sesauthors_scripts_and_styles' ), 5 );

        add_action('add_meta_boxes', array( $this, 'add_related_sesauthors_meta_box_to_sespublications'));

        add_action('save_post_publication', array( $this, 'save_meta_information_for_sesauthor'));
	}

    public function enqueue_related_sesauthors_scripts_and_styles(){

        wp_enqueue_style('related-sesaurthors-admin-styles',  plugins_url('../assets/admin/css/admin-related-sesauthors-styles.css', __FILE__));
        wp_enqueue_script('releated-sesauthors-admin-script', plugins_url('../assets/admin/js/admin-related-sesauthors-scripts.js', __FILE__), array('jquery','jquery-ui-droppable','jquery-ui-draggable', 'jquery-ui-sortable'));
        
    }

    public function add_related_sesauthors_meta_box_to_sespublications() {
        add_meta_box(
            'related_sesauthors_meta_box', //unique ID
            'Related SES Authors', //Name shown in the backend
            array($this, 'display_related_sesauthors_meta_box'),
            'publication', //post type this box will attach to
            'normal', //position (side, normal etc)
            'default' //priority (high, default, low etc)
        );
    }

    public function display_related_sesauthors_meta_box($post){
        //create nonce
        wp_nonce_field('sesauthors_meta_box','sesauthors_meta_box_nonce');
        
        //collect ses authors (if we already have some)
        $related_sesauthors = get_post_meta($post->ID,'related_sesauthors',true);
       
        $nonses_author_list = get_terms('nonsesauthors', array( 'hide_empty' => 0 ) );
        $ses_author_list = $nonses_author_list;
        
        echo '<div class="related_pages">';
            //left container (all pages)
            echo '<div class="left_container">';
                echo '<p> Listed below are the authors for SES Publications.</p>';
                echo '<p>Drag these to the other container to add them as related authors </p>';
                //loop through all pages
                
         if (! empty( $ses_author_list)){			
                  if ( ! is_wp_error( $ses_author_list ) ) {
                       foreach ( $ses_author_list as $ses_author) {
                           //collect their id and name and create the page item
                           $page_id = $ses_author->term_id;
                           $page_name = $ses_author->name;
                           echo '<div class="page_item" data-page-id="' . $page_id . '">';
                              echo 	'<div class="page_title">' . $page_name . '</div>';
                           echo 	'<div class="remove_item"> Remove </div>';
                           echo '</div>';
                       } // end foreach
                  } // end not wp_error
                } // end if not empty	
        
                echo '</div>';
                //end left container		 
               //Right container
                echo '<div class="right_container">';
                echo 	'<p>Drag authors from the left container onto this container </p>';
                //if we have previous saved related pages
               if(!empty($related_sesauthors)){
                $related_sesauthors_array = json_decode($related_sesauthors);
                foreach($related_sesauthors_array as $related_sesauthor){
                    //page information
                    $page_id = $related_sesauthor;
                    //$page_name = get_the_title($page_id);
                    $term = get_term($page_id,'nonsesauthors');
                    $page_name = $term->name;
                    
            
                    echo '<div class="page_item" data-page-id="' . $page_id . '">';
                    echo 	'<div class="page_title">' . $page_name . '</div>';
                    echo 	'<div class="remove_item active"> Remove </div>';
                    echo 	'<input type="hidden" name="related_sesauthors[]" value="' . $page_id . '"/>';
                    echo '</div>';
               }
            }
            echo 	'<div class="droppable-helper"></div>';
            echo '</div>';
        echo '<div class="clearfix"></div>';
        echo '</div>';
    
    
    } // Display SES Authors

    public function save_meta_information_for_sesauthor($post_id){

        //test for existence of nonce
        if(!isset($_POST['sesauthors_meta_box_nonce'])){
               return $post_id;
        }
        //verify nonce
        if(!wp_verify_nonce($_POST['sesauthors_meta_box_nonce'],'sesauthors_meta_box')){
            return $post_id;
        }
        //if not autosaving
        if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
            return $post_id;
        }
        //check permissions
        if(!current_user_can('edit_page',$post_id)){
            return $post_id;
        }
        
            
    
        //SAFE to save data, let's go
        $related_sesauthors_value = '';
        //collect related pages (if set)
        
        if(isset($_POST['related_sesauthors'])){
            $related_sesauthors_array = $_POST['related_sesauthors'];
            $related_sesauthors_value = json_encode($related_sesauthors_array);
        }
        
        //update post meta
        update_post_meta($post_id,'related_sesauthors',$related_sesauthors_value);
    
    }

}

$scripts_cahnrs_ses = new Scripts_CAHNRS_SES();