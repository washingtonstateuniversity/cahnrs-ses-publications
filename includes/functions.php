<?php namespace CAHNRS\Plugin\SES_Pubs;

class Functions_CAHNRS_SES {
    public static $version = '1.0.0';

    public function __construct(){
        $this->init_theme_functions();
    }

    protected function init_theme_functions(){
        $this->add_post_types();
        $this->add_taxonomies();
    }

    protected function add_post_types() {
		require_once __DIR__ . '/../lib/classes/post-types/class-ses-pubs-post-type.php';
	}

    protected function add_taxonomies() {
		require_once __DIR__ . '/../lib/classes/taxonomies/taxonomies-ses-pubs.php';
	}
}

$functions_CAHNRS_SES = new Functions_CAHNRS_SES();

class CAHNRSWP_SESPUB_Init {
	
	private static $instance = null;
	
	public static function get_instance(){
		
		if( null == self::$instance ) {
			
			self::$instance = new self;
			
		} 
		
		return self::$instance;
		
	} // end get_instance
	
	private function __construct(){
		
		define( 'CAHNRSWPRFPURL' , plugin_dir_url( __FILE__ ) ); // PLUGIN BASE URL
		
		define( 'CAHNRSWPRFPDIR' , plugin_dir_path( __FILE__ ) ); // DIRECTORY PATH
		
        add_shortcode( 'sespubslist', array($this, 'cahnrswp_display_ses_publications' ));
		
		add_action( 'edit_form_after_title', array( $this , 'cahnrswp_edit_form_after_title' ) );
		
		add_action( 'init', array( $this, 'cahnrswp_ses_init' ), 1 );
		
		add_action( 'save_post', array( $this , 'cahnrswp_save_post' ) );
			
		add_filter('the_permalink', array( $this , 'cahnrswp_the_permalink' ) );	
		
	} // end constructor
	
		
	public function cahnrswp_ses_init(){
 
        $clauses = '';	
        $wp_query = null;						
		
	} // end cahnrswp_ses_init


	
	
   // Request all profiles with tag school-of-economic-sciences 	

   public function cahnrswp_people_request($people_tag){
   
   $response = wp_remote_get( 'https://people.wsu.edu/wp-json/posts/?type=wsuwp_people_profile&tag=' . $people_tag ,array('sslverify'=> false));
     try {
      // Note that we decode the body's response since it's the actual JSON feed
      $json = json_decode($response['body']);
      
      } catch ( Exception $ex ) {
    	$json = null;   
     } //end try/catch
	return $json;
							
  } // end cahnrswp_people_request



   // display cahnrswp_display_wsuwp_people
 public function cahnrswp_display_wsuwp_people($content){
	  // If we're on a single post or page...
        if ( is_single() ) {
            // ...attempt to make a response to wsuwp_people. Note that you should replace the tag here!
			
	            if ( null == ( $json_response = $this->cahnrswp_people_request('school-of-economic-sciences') ) ) {
				
				// ...display a message that the request failed
                               $html = '<div id="cahnrswp-wsuwp-people">';
 $html .= 'There was a problem communicating with the People Profiles..';
 $html .= '</div>				<!-- /#cahnrswp-wsuwp-people -->';
 
			} else {
			
               $html = '
<div id="cahnrswp-wsuwp-people">';
foreach($json_response as $item) {
 $html .= 'Faculty Name ' . $this->$item->title  . ' link to profile';
}
 $html .= '</div>
<!-- /#cahnrswp-wsuwp-people -->';				
			} //end else of if/else
			
			 $content .= $html;

		}//end if/else
   } //end cahnrswp_display_wsuwp_people
	
 
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
	
	
	
public function sespub_attributes_dropdown_pages_args( $dropdown_args, $post ) {
	
        if ( 'publication' == $post->post_type ) {
                $dropdown_args['post_type'] = 'wsuwp_personnel_directory';
        }
        return $dropdown_args;}
	
	public function cahnrswp_edit_form_after_title(){ 
		
		global $post;
		
		$sespub_model = new CAHNRSWP_SESPUB_model(); 
		
		$sespub_model->set_sespub( $post->ID );
		
		$page_view = new CAHNRSWP_SESPUB_view( $this , $sespub_model );
		
		$page_view->output_editor();
		
	} // end add_editor_form
	
	
	public function cahnrswp_save_post( $post_id ){
		
		$sespub_model = new CAHNRSWP_SESPUB_model(); 
		
		$sespub_model->save_sespub( $post_id );
		
	} // end cahnrswp_save_post
	
	public function cahnrswp_template_redirect(){
		
		 global $post;
		 
		 if( 'publication' == $post->post_type && is_singular() ){
			 
			 $meta = \get_post_meta( $post->ID , '_redirect_to' , true );
			 
			 if( $meta ){
				 
				 \wp_redirect( $meta , 302 );
				 
			 } // end if $meta
			 
		 } // end if post_type
		 
	 } // end cahnrswp_template_redirect
	 
	 public function cahnrswp_short_title( $title ){
		
		 global $post;
		 
	if(( 'publication' == $post->post_type ) AND in_the_loop()){
 
		     $meta = get_post_meta( $post->ID , '_short_title' , true );
	
			 if (( $meta != '')) {  
			    
				  $title = $meta;
				  
			 } 
			 else {
				$title = $post->post_title;
			 }
			 
			 // end if $meta
	
		 } // if post_type
		 
			 return $title; 
		 
	} // end cahnrswp_short_title
	
  

// sort by custom taxonomies defined for publication content type 

public function cahnrswp_sespubs_clauses_with_tax( $clauses, $wp_query ) {
	global $wpdb;

  //array of sortable taxonomies
//   $taxonomies = array('yearspublished', 'journals','sesauthors','nonsesauthors');
     $taxonomies = array('yearspublished', 'journals','nonsesauthors');

  if (isset($wp_query->query['orderby']) && in_array($wp_query->query['orderby'], $taxonomies)) {
  $clauses['join'] .= "
     LEFT OUTER JOIN {$wpdb->term_relationships} AS rel2 ON {$wpdb->posts}.ID = rel2.object_id
     LEFT OUTER JOIN {$wpdb->term_taxonomy} AS tax2 ON rel2.term_taxonomy_id = tax2.term_taxonomy_id
     LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
  ";
  $clauses['where'] .= " AND (taxonomy = '{$wp_query->query['orderby']}' OR taxonomy IS NULL)";
  $clauses['groupby'] = "rel2.object_id";
  $clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
  $clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
}
// var_dump($clauses);
return $clauses;
}



public function cahnrswp_display_ses_publications($atts){
	
 extract(shortcode_atts(array(
      'sesdisplay' => 'top',
   ), $atts));

$the_journal ='';
$the_sesauthor ='';
$the_nonsesauthor='';
$the_yearpublished = '';
$my_orderby_var = '';


$the_journal = $_GET['the_journal'];
$the_sesauthor = $_GET['the_sesauthor'];
$the_nonsesauthor = $_GET['the_nonsesauthor'];
$the_yearpublished = $_GET['the_yearpublished'];
$my_orderby_var = $_GET['the_orderby'];


$the_order_var = isset($_GET["the_order"]) ? $_GET["the_order"] : 'DESC';
$neworder = $the_order_var ? 'DESC' : 'ASC';

if ( is_null ($my_orderby_var) ) {
  $my_orderby_var = 'yearspublished' ;	
}

$string ='';  
$my_query = null;
//echo $taxonomyName;
add_filter('posts_clauses', array( $this , 'cahnrswp_sespubs_clauses_with_tax' ), 10, 2 );

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$type = 'publication';
$args=array(
  'post_type' => $type,
  'post_status' => 'publish',
  'posts_per_page' => -1,
  'caller_get_posts'=> 1,
  'orderby' => $my_orderby_var,
  'order' => $the_order_var,
  'paged' => $paged
  );

if ( ! is_null($the_journal) || ! is_null($the_sesauthor) || ! is_null($the_nonsesauthor) || ! is_null($the_yearpublished)) {
 
 $args['tax_query'] = array (
	   array (
	   'taxonomy' =>'journals',
	   'field'=> 'slug',
       'terms'=>$the_journal,
       ),
	   array (
	   'taxonomy' =>'sesauthors',
       'field'=> 'slug',
       'terms'=>$the_sesauthor,
       ),
	    array (
	   'taxonomy' =>'nonsesauthors',
       'field'=> 'slug',
       'terms'=>$the_nonsesauthor,
       ),
	   array (
	   'taxonomy' =>'yearspublished',
       'field'=> 'slug',
       'terms'=>$the_yearpublished,
       ),
	   'relation' => 'OR'
	);    
}

$my_query = new WP_Query($args);
if ($sesdisplay == 'full') {
 $startstring .= '<p>';
 $endstring .= '</p>';
}
else if ($sesdisplay == 'top') {
 
  $startstring .= '<div id="accordion"><table><tr><td width="25%"><strong>Authors</strong></td><td width="35%"><strong>Title</strong></td><td width="25%"><strong><a href="?the_orderby=journals&the_order=ASC">Journal</a></strong></td><td width="15%"><strong><a href="?the_orderby=yearspublished&the_order=' . $neworder .'">Year</strong></td></tr>';	

  $endstring .= '</div></table>';
   $endstring .=	'</td></tr>';
   ob_start();
  
}
if ($sesdisplay == 'accordion'){
$startstring .= '<div class="cahnrs-core-faq"><table><tr><td><strong><a href="?the_orderby=yearspublished&the_order=' . $the_order_var .'">Year</strong></td><td><strong>Title</strong></td><td><strong><a href="?the_orderby=sesauthors&the_order=' . $the_order_var .'">Authors</strong><span class="sorting-indicator"></span></td><td><strong><a href="?the_orderby=journals&the_order=ASC">Journal</a></strong></td></tr></table>';
 $endstring .= '</div>';	
}



if( $my_query->have_posts() ) {
  $string .= $startstring;
  while ($my_query->have_posts()) {
    $my_query->the_post(); 
	$authors_meta = get_post_meta(get_the_ID(), '_authors', TRUE); 
	$sesauthorsdd_meta = get_post_meta(get_the_ID(), '_sesauthorsdd', TRUE); 
	$issuepages_meta = get_post_meta(get_the_ID(), '_issue_pages', TRUE);
	$redirectURL_meta = get_post_meta(get_the_ID(), '_redirect_to', TRUE);
	$short_title_meta = get_post_meta(get_the_ID(), '_short_title', TRUE);
    $ses_author_list =  wp_get_object_terms( get_the_ID(), 'sesauthors' );	
	$nonses_author_list =  wp_get_object_terms( get_the_ID(), 'nonsesauthors' );	
    $ses_journals_list =  wp_get_object_terms( get_the_ID(), 'journals' );	
	$ses_yearspublished_list =  wp_get_object_terms( get_the_ID(), 'yearspublished' );
    $allrelated_sesauthors = get_post_meta(get_the_ID(),'related_sesauthors',true);	

 if ($short_title_meta == "") {
   $short_title_meta = get_the_title();
 } 
 
// Full Display (old) 
 
  switch( $sesdisplay ){
        case 'full': 
	          $string .= '<br>'. $authors_meta . ' and ';
			  
			   if (! empty( $ses_author_list)){			
			  if ( ! is_wp_error( $ses_author_list ) ) {
				    foreach ( $ses_author_list as $ses_author) {
					   $string .= '<strong>' . $ses_author->name . '</strong>'; 
					  
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			  
			  $string .=  '. ';
			  
			   if (! empty( $ses_yearspublished_list)){
			  if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                   foreach ( $ses_yearspublished_list as $ses_yearpublished) {
					   $string .= '' . $ses_yearpublished->name . ''; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty 
			   
			   
			  $string .= '. <a href="'.get_permalink().'" title="' .get_the_title().'">"' . $short_title_meta . '"'.'</a> <em>';
			  
			   if (! empty( $ses_journals_list)){
			  if ( ! is_wp_error( $ses_journals_list ) ) {
                   foreach ( $ses_journals_list as $ses_journal) {
					   $string .= '<a href="' . get_term_link( $ses_journal->slug, $ses_journals_list ) . '">' . $ses_journal->name . '</a></li>'; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			   
			   $string .= '</em>. '. $issuepages_meta . '.<br> URL: ' . '<a href="' . $redirectURL_meta . '">'. $redirectURL_meta . '</a>'.  '</br>';
			 
			 
            break; // End of Full Display

// Top Display - Default and updated

        case 'top': 
	        $string .= '<tr><td>';
			
			if (!empty($allrelated_sesauthors)) {
			   if ( ! is_wp_error( $allrelated_sesauthors ) ) {
				
				$allrelated_sesauthors_array = json_decode($allrelated_sesauthors);
				$numItems = count($allrelated_sesauthors_array);
				$i = 0;
				 foreach($allrelated_sesauthors_array as $allrelated_sesauthor){
				  $sesauthorterm =  get_term($allrelated_sesauthor, 'nonsesauthors');
					//$string .= $sesauthorterm->name;
				//	 $string .= '<a href="?the_nonsesauthor=' . $sesauthorterm->slug . '">' . $sesauthorterm->name . '</a>'; 
				  $sesauthorparent = get_term($sesauthorterm->parent, 'nonsesauthors');
    			   if ($sesauthorparent->name == 'SES') {
					    $string .= '<strong>' . '<a href="?the_nonsesauthor=' . $sesauthorterm->slug . '">' .  $sesauthorterm->name . '</a>' . '</strong>';
				      }
				      else {
					   $string .= $sesauthorterm->name;
				      }
					if (++$i != $numItems) {$string .= ', ';}
				  } // foreach $allrelated_sesauthors_array
				 } // end not wp_error
			    } //end of !empty
				else {
				 $string .= 	$sesauthorsdd_meta . ' ++ ';
				}
                				
				$string .= 	' </td>';
            		
			$string .= '<td><a href="'.get_permalink().'" title="' .get_the_title().'">"' . $short_title_meta . '"'.'</a> ';
    
			if (! empty( $ses_yearspublished_list)){
			  if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                   foreach ( $ses_yearspublished_list as $ses_yearpublished) {
					   $string .= '(' . $ses_yearpublished->name . ') '; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			
			$string .= '</td><td>';
			
			if (! empty( $ses_journals_list)){
			  if ( ! is_wp_error( $ses_journals_list ) ) {
                   foreach ( $ses_journals_list as $ses_journal) {
//					   $string .= '<em>'.  . $ses_journal->name . '</em>'; 
	     	   		   $string .= '<em><a href="?the_journal='.  $ses_journal->slug . '">' . $ses_journal->name . '</em>'; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			$string .= '</td><td><strong><a href="?the_yearpublished=' . $ses_yearpublished->slug . '">' . $ses_yearpublished->name . '</a></strong>'; 

            break;
			
// Accordion Display option			

			case 'accordion':

  $ses_author_string = '';
  $ses_author_string_link = '';
 // $string = '';	

// Generating Output 

//$ses_author_string = '';
 
	$ses_title = $post->post_title;
    	
	if ($short_title_meta == "") {
       $short_title_meta = $post->post_title;
     } 
	 $string .=   '<a href="">';
	 
     if (! empty( $ses_yearspublished_list)){
		  if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                  foreach ( $ses_yearspublished_list as $ses_yearpublished) {
				   $string .= '' . $ses_yearpublished->name . ''; 
				   $year_pub = $ses_yearpublished->name;
				   } // end foreach
			  } // end not wp_error
			} // end if not empty 
	 
    $string .=   ' - '. $short_title_meta ;
	

        if (! empty( $ses_author_list)){			
			  if ( ! is_wp_error( $ses_author_list ) ) {
                   $numItems = count($ses_author_list);
				   $i = 0;
                   foreach ( $ses_author_list as $ses_author) {
					   $ses_author_string_link .= '<strong><a href="?the_sesauthor=' . $ses_author->slug . '">' . $ses_author->name . '</a></strong>.'; 
					    if (++$i != $numItems) {$ses_author_string_link .= ', ';}
		
	   				   $ses_author_string .= ' - ' . $ses_author->name . ''; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
        
			$string .=  $ses_author_string;
	
         	$string .=  '</a>';
				
			$string .=  '<div class="cc-content">';
	 
	        $string .= $authors_meta . ' and ';
			
			$string .= $ses_author_string_link;
			
			$string .= ' ' . $year_pub . '. ';	
			
			$string .= ' ' . get_the_title() . ' ';

			   if (! empty( $ses_journals_list)){
			  if ( ! is_wp_error( $ses_journals_list ) ) {
                   foreach ( $ses_journals_list as $ses_journal) {
                       $string .=  '<em>'. $ses_journal->name . '</em>'; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			   
			   $string .= '. '. $issuepages_meta . '. <p> URL: ' . '<a href="' . $redirectURL_meta . '">'. $redirectURL_meta . '</a>'.  '</p>';
			


$string .= '</div>';

			
			break; //End of 'top'

        default:
		
          $string .= '<tr><td><a href="'.$get_permalink().'" title="' .get_the_title().'">"' . get_the_title(). '"'.'</a><strong> (' . $yearpub_meta . ')</strong></td><td> '. $authors_meta . '</td></tr>';  
            break;
    }

 
     } // end of while have_posts
	 
   $string .= $endstring;
   
    
 }
wp_reset_query();  // Restore global post data stomped by the_post().
return $string;

} //end display_ses_publications 


	 
	 public function cahnrswp_the_permalink( $link ){
		 
		 global $post;
		 
		 if( 'publication' == $post->post_type ) {
			 
			 $meta = get_post_meta( $post->ID , '_redirect_to' , true );
			 
			 if( $meta ){
				 
				 $link = $meta;
				 
			 } // end if $meta
			 
		 } // end if post_type
		 
		 return $link;
		 
	 } // end cahnrswp_the_permalink
	
} // end class 



class CAHNRSWP_SESPUB_Model {
	
	public $post_date;
	
	public $redirect;
	
	
	public function __construct(){
	}
	
	public function set_sespub( $post_id = false ) {
		
		$date = \get_post_meta( $post_id , '_post_date', true );
		
		$this->post_date = ( $date )? date( 'm', $date ).'/'.date( 'd', $date ).'/'.date( 'y', $date ) : $date;
		
		$this->short_title = \get_post_meta( $post_id , '_short_title', true );
		
		$this->redirect = \get_post_meta( $post_id , '_redirect_to', true );
		
		$this->authors = \get_post_meta( $post_id , '_authors', true );

		$this->sesauthorsdd = \get_post_meta( $post_id , '_sesauthorsdd', true );

		$this->issue_pages = \get_post_meta( $post_id , '_issue_pages', true );
		
		
		
	} // end set sespub
	
	public function save_sespub( $post_id ){
		
		if ( ! isset( $_POST['sespub_nonce'] ) ) return;
		
		if ( ! wp_verify_nonce( $_POST['sespub_nonce'], 'submit_sespub' ) ) return;
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		
		if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		
		$fields = array(
			'_post_date'   => 'text',
			'_redirect_to' => 'text',
			'_short_title' => 'text',
			'_authors' => 'text',
			'_issue_pages' => 'text',
			'_sesauthorsdd' => 'textarea',
					
		);
		
		$allowed_tags = array( 'strong' => array(),'b' => array());
		
		foreach( $fields as $f_key => $f_data ){
		
			
			if( isset( $_POST[ $f_key ] ) ){
				if('_sesauthorsdd' == $f_key ) {
				    
				    $instance = $_POST[ $f_key ];
					
				}
				else {
					$instance = sanitize_text_field( $_POST[ $f_key ] );
				}
				
				if( '_post_date' == $f_key ){ 
				
					$instance = strtotime( $instance );
					
				}
				
				update_post_meta( $post_id , $f_key , $instance );
				
			} // end if
			
		} // end foreach
		
	} // end save_sespub
	
} // end class CAHNRSWP_SESPUB_Model

class CAHNRSWP_SESPUB_View {
	
	private $control;
	private $model;
	public $view;
	
	public function __construct( $control , $model ){
		
		$this->control = $control;
		$this->model = $model;
		
	} // end __construct
	
	public function output_editor(){
		  
		include CAHNRSWPRFPDIR . '../inc/editor.php';
		
	}
	
} // end class CAHNRSWP_SESPUB_View

$cahnrswp_SESPUB = CAHNRSWP_SESPUB_Init::get_instance();