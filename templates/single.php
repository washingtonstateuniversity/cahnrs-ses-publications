<?php
  		 	 
   	$authors_meta = get_post_meta(get_the_ID(), '_authors', TRUE);
	$sesauthorsdd_meta = get_post_meta(get_the_ID(), '_sesauthorsdd', TRUE); 
    $issuepages_meta = get_post_meta(get_the_ID(), '_issue_pages', TRUE);
    $redirectURL_meta = get_post_meta(get_the_ID(), '_redirect_to', TRUE);
	$short_title_meta = get_post_meta(get_the_ID(), '_short_title', TRUE);
    $ses_journals_list =  wp_get_object_terms( get_the_ID(), 'journals' );	
    $ses_yearspublished_list =  wp_get_object_terms( get_the_ID(), 'yearspublished' );	
    $allrelated_sesauthors = get_post_meta(get_the_ID(),'related_sesauthors',true);	
			
		  echo '<p>';
			
			if (!empty($allrelated_sesauthors)) {
			   if ( ! is_wp_error( $allrelated_sesauthors ) ) {
				
				$allrelated_sesauthors_array = json_decode($allrelated_sesauthors);
				$numItems = count($allrelated_sesauthors_array);
				$i = 0;
				 foreach($allrelated_sesauthors_array as $allrelated_sesauthor){
				  $sesauthorterm =  get_term($allrelated_sesauthor, 'nonsesauthors');
				  $sesauthorparent = get_term($sesauthorterm, 'nonsesauthors');
				 
    			   if ($sesauthorparent->name == 'SES') {
					echo '<strong>This ' . $sesauthorterm->name . '</strong>';
				   }
				   else {
					   echo $sesauthorterm->name;
				   }
				   
					if (++$i != $numItems) {echo ', ';}
				  } // foreach $allrelated_sesauthors_array
				 } // end not wp_error
			    } //end of !empty
				else {
				 echo $sesauthorsdd_meta . ' ++ ';
				}
			  
			  echo  '. ';
			  
			   if (! empty( $ses_yearspublished_list)){
			  if ( ! is_wp_error( $ses_yearspublished_list ) ) {
                   foreach ( $ses_yearspublished_list as $ses_yearpublished) {
					   echo '' . $ses_yearpublished->name . ''; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty 
			   
			   
			  echo '. ' . get_the_title() . ' <em>';
			  
			   if (! empty( $ses_journals_list)){
			  if ( ! is_wp_error( $ses_journals_list ) ) {
                   foreach ( $ses_journals_list as $ses_journal) {
					   echo '' . $ses_journal->name . ''; 
				   } // end foreach
			  } // end not wp_error
			} // end if not empty
			   
			   echo '</em>. '. $issuepages_meta . '.</p><p>'; 
			   
			   if ($redirectURL_meta != '') 
			   {
			   
			   echo '<strong> URL: </strong>' . '<a href="' . $redirectURL_meta . '" aria-label="Read publication">'. $redirectURL_meta . '</a>'.  '</p>';
			   }
		 
	           echo '<HR><p></p><a href="' . get_site_url() .'/publication/">Return to Publications</a>';

			   