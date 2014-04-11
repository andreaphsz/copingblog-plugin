<?php
/*
Plugin Name: coping blog plugin
Plugin URI: http://copingblog.dev
Description: A brief description of the Plugin.
Version: 0.1
Author: Andrea Cantieni
Author URI: http://schwyz.phz.ch
License: GPL2
*/

add_filter('default_content', 'cb_editor_content');

function cb_editor_content( $content ) {
	
	$new_ps = isset($_GET["new_ps"]) ? $_GET["new_ps"] : false;
	
	if ($new_ps != false) {
		$content = "Datum: <br> Zielebene: <br> Konsequenzen: <br>  [Formular Feinplanung hier hochladen] <br> [Passwort für Mentor anpassen]" ;
	}
	
	$new_refl = isset($_GET["new_reflexion"]) ? $_GET["new_reflexion"] : false;
	
	if ($new_refl) {
		$content = "Datum: <br> [Passwort für Kommilitone anpassen]" ;
	}
	
	return $content;
}

add_filter('default_title', 'cb_editor_title');

function cb_editor_title() {
	$new_ps = isset($_GET["new_ps"]) ? $_GET["new_ps"] : false;
	
	if ($new_ps != false) {
		return strtoupper($new_ps) . ": Titel";
	}
	
	$new_refl = isset($_GET["new_reflexion"]) ? $_GET["new_reflexion"] : false;
	
	if ($new_refl) {
		return "Reflexion: Titel";
	}
}

add_action('save_post', 'cb_save_post');

function cb_save_post( $post_id ){
	$new_ps = isset($_GET["new_ps"]) ? $_GET["new_ps"] : false;
	
	if ($new_ps != false) {
		$ps = get_cat_ID( $new_ps );
		wp_set_post_categories( $post_id, array($ps));
		cb_update_post($post_id, array( 'post_password' => 'for_my_mentor_'.rand(1000,9999), 'ID' => $post_id ) );
	}
	
	$new_refl = isset($_GET["new_reflexion"]) ? $_GET["new_reflexion"] : false;
	
	if ($new_refl) {
		$refl = get_cat_ID( 'Reflexion' );
		wp_set_post_categories( $post_id, array($refl));
		cb_update_post($post_id, array( 'post_password' => 'for_my_fellow_'.rand(1000,9999), 'ID' => $post_id ) );
	}
}

function cb_update_post($post_ID, $data) {
	if ( ! wp_is_post_revision( $post_ID ) ) { //see https://codex.wordpress.org/Function_Reference/wp_update_post
		// unhook this function so it doesn't loop infinitely
		remove_action('save_post', 'cb_set_visibility');
		remove_action('save_post', 'cb_save_post');
		
		wp_update_post( $data );
		/*if ($visi == "pwd") 
		{
			// update the post, which calls save_post again
			wp_update_post( array( 'post_password' => rand(1000,9999), 'ID' => $post_ID ) );
		}

		if ($visi == "private") 
		{
			wp_update_post( array( 'post_status' => 'private', 'ID' => $post_ID ) );
		}
	*/
		// re-hook this function
		add_action('save_post', 'cb_set_visibility');
		add_action('save_post', 'cb_save_post');
	}
}

function cb_display_feinplanung() {
	echo "<h2>Feinplanung</h2>";
	echo "<a href='#'>Formular herunterladen</a><br><br>";
	
	echo "<h2>Reflexion PS</h2>";

	$cat_ids = array();
	for ($i=0; $i<9; $i++) { 
		$cat_ids[$i] = get_cat_ID('PS'.($i+1));
	}
	$ps_count = get_categories( array('include'=> implode($cat_ids,",")) );
		
	echo "<h3>PS 1 versteht und vermittelt Fachinhalte</h3>";
	echo "<div>Die Lehrperson verfügt über fachwissenschaftliches und fachdidaktisches Wissen, versteht die Inhalte, Strukturen und zentralen Forschungsmethoden ihrer Fachbereiche und sie kann Lern- situationen schaffen, die die fachwissenschaftlichen und fachdidaktischen Aspekte für die Lernenden bedeutsam machen. <br>
&nbsp;&nbsp;1.3 erkennt Zusammenhänge zwischen verschiedenen Fachbereichen (Niveau 1) <br>
&nbsp;&nbsp;1.4 wählt Ziele und Inhalte erziehungs- und gesellschaftswissenschaftlich begründbar aus
(Niveau 1)</div>";
	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'PS1' )."&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[0]) ? $ps_count[0]->count : 0) .")</a> | ";

	echo "<a href='post-new.php?new_ps=ps1'>neu</a><br><br>";
	
	echo "<h3>PS 2 versteht und unterstützt Entwicklungsprozesse</h3>";
	echo "<div>Die Lehrperson versteht, wie Kinder und Erwachsene lernen und sich entwickeln, und sie kann Lerngelegenheiten und Lernwege anbieten, welche die kognitive, soziale und persönliche Entwicklung unterstützen. <br>
&nbsp;&nbsp;2.2 aktiviert Erfahrungen und Wissen (Niveau 2) <br>
&nbsp;&nbsp;2.5 fördert selbstgesteuertes Lernen (Niveau 2)</div>";

	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'PS2' )."&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[1]) ? $ps_count[1]->count : 0) .")</a> | ";
	echo "<a href='post-new.php?new_ps=ps2'>neu</a><br><br>";

	echo "<h3>PS 3 versteht und berücksichtigt Unterschiede im Lernen
</h3>";
	echo "<div>Die Lehrperson versteht, wie verschieden die Wege zum Lernen sind und schafft Unterrichtssituationen, die auf die Lernenden individuell angepasst sind. <br>
&nbsp;&nbsp;3.4 begünstigt eigenständiges Lernen (Niveau 2)</div>";

	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'PS3' )."&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[2]) ? $ps_count[2]->count : 0) .")</a> | ";
	echo "<a href='post-new.php?new_ps=ps3'>neu</a><br><br>";
	
	echo "<h3>PS 5 motiviert und leitet an</h3>";
	echo "<div>Die Lehrperson setzt ihr Verständnis über Motivationsprozesse und Klassenmanagement ge- zielt ein, um Lernsituationen zu schaffen, die die positive soziale Zusammenarbeit der Kinder und Jugendlichen fördert und selbstgesteuertes Lernen zulassen. <br>
&nbsp;&nbsp;5.4 setzt Verhaltenserwartungen, fordert diese ein und fördert sozial erwünschtes Verhalten und das Klassenklima (Niveau 1) <br>
&nbsp;&nbsp;5.5 fördert soziale Zusammenarbeit (Niveau 1)</div>";

	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'PS5' )."&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[4]) ? $ps_count[4]->count : 0) .")</a> | ";
	echo "<a href='post-new.php?new_ps=ps5'>neu</a><br><br>";
	
	echo "<h3>PS 6 kommuniziert und moderiert</h3>";
	echo "<div>Die Lehrperson verwendet ihr Wissen von effektiven verbalen und nicht verbalen Kommunikati- ons- und Medienformen, um aktives Lernen, Mitarbeit und den gegenseitigen Austausch im Klassenzimmer zu fördern. <br>
&nbsp;&nbsp;6.3 fördert die Diskussionskultur (Niveau 1) <br> </div>";

	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'PS6' )."&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[5]) ? $ps_count[5]->count : 0) .")</a> | ";
	echo "<a href='post-new.php?new_ps=ps6'>neu</a><br><br>";
	
	echo "<h3>PS 7 plant und evaluiert</h3>";
	echo "<div>Die Lehrperson plant, realisiert und evaluiert ihren Unterricht auf Grund ihres Verständnisses vom Fachbereich, von Lehrplan und Leitideen der Schule, und auf der Basis des berufswissen- schaftlichen Hintergrundes. <br>
&nbsp;&nbsp;7.1 setzt Leitideen und Lehrplan im Unterricht um (Niveau 1) <br>
&nbsp;&nbsp;7.2 plant den Unterricht systematisch (Niveau 1) <br>
&nbsp;&nbsp;7.3 passt den Unterricht situativ an (Niveau 1)</div>";

	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'PS7' )."&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[6]) ? $ps_count[6]->count : 0) .")</a> | ";
	echo "<a href='post-new.php?new_ps=ps7'>neu</a><br><br>";
	
	echo "<h3>PS 8 beobachtet und fördert</h3>";
	echo "<div>Die Lehrperson versteht und verwendet gezielt unterschiedliche Beurteilungssysteme, um die kognitive, soziale und persönliche Entwicklung der Kinder und Jugendlichen fortlaufend einzuschätzen, zu sichern und zu fördern. <br>
&nbsp;&nbsp;8.1 beurteilt und bewertet differenziert (Niveau 1)</div>";

	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'PS8' )."&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[7]) ? $ps_count[7]->count : 0) .")</a> | ";
	echo "<a href='post-new.php?new_ps=ps8'>neu</a><br><br>";
	
	echo "<h3>PS 9 reflektiert ihre eigene Professionalität (Erfahrungen)</h3>";
	echo "<div>Die Lehrperson reflektiert fortlaufend die Wirkung ihrer Entscheide und Tätigkeiten auf andere (Lernende, Eltern und auf andere Lehrende) und sie geht ihre professionelle Weiterentwicklung aktiv und verantwortungsbewusst an. <br>
&nbsp;&nbsp;9.1 reflektiert und entwickelt den Unterricht (Niveau 1) </div>";

	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'PS9' )."&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[8]) ? $ps_count[8]->count : 0) .")</a> | ";
	echo "<a href='post-new.php?new_ps=ps9'>neu</a><br><br>";
	
} //end cb_display_feinplanung

function cb_display_reflexion() {
	echo "<h2>Reflexion</h2>";
	
	$cat_ids =  get_cat_ID('Reflexion');
	$ps_count = get_categories( array('include'=> $cat_ids) ); 
	
	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'Reflexion' )."&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[0]) ? $ps_count[0]->count : 0) .")</a> | ";
	echo "<a href='post-new.php?new_reflexion=true'>neu</a><br><br>";
	
}

function cb_display_evaluation() {
	echo "<h2>Evaluation</h2>";
	echo "<a href='http://fragebogen.blogpraktikum.ch' target=_blank>Link zum Fragebogen</a>";
}

add_action('admin_menu', 'cb_menu_copingblog');

function cb_menu_copingblog() {
	$menu_blogpr = get_site_option( 'cb_menu_blogpr' );
	if(isset($menu_blogpr) && $menu_blogpr==1) {
		add_menu_page("Blogging Tool", "Blogging Tool", "edit_posts", "feinplanung_menu", 'cb_display_feinplanung');
		add_submenu_page( "feinplanung_menu", "Feinplanung", "Feinplanung", "edit_posts", "feinplanung_menu", 'cb_display_feinplanung');
		add_submenu_page( "feinplanung_menu", "Reflexion", "Reflexion", "edit_posts", "reflexion_menu", 'cb_display_reflexion');
		add_submenu_page( "feinplanung_menu", "Evaluation", "Evaluation", "edit_posts", "evaluation_menu", 'cb_display_evaluation');
	}

}

add_action('update_wpmu_options', 'cb_update_options');

function cb_update_options() {
	$options = array('cb_menu_tumblr', 'cb_menu_blogpr');
	foreach($options as $option_name) {
		$value = wp_unslash( $_POST[$option_name] );
		update_site_option( $option_name, $value );		
	}
}

add_action('wpmu_options', 'cb_network_plugin_settings');

function cb_network_plugin_settings() {
	?>
	<h3><?php _e( 'Blogpraktikum Settings' ); ?></h3>
	<table id="cb-menu" class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e( 'Enable tumblr like menu' ); ?></th>
			<td>
		<?php
		$menu_tumblr = get_site_option( 'cb_menu_tumblr' );
		echo "<label><input type='checkbox' name='cb_menu_tumblr' value='1'" . ( isset( $menu_tumblr ) ? checked( $menu_tumblr, '1', false ) : '' ) . " /> " . esc_html( "tumblr" ) . "</label><br/>";
		?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Enable blogpraktikum menu' ); ?></th>
			<td>
		<?php
		$menu_blogpr = get_site_option( 'cb_menu_blogpr' );
		echo "<label><input type='checkbox' name='cb_menu_blogpr' value='1'" . ( isset( $menu_blogpr ) ? checked( $menu_blogpr, '1', false ) : '' ) . " /> " . esc_html( "blogpraktikum" ) . "</label><br/>";
		?>
			</td>
		</tr>
	</table>
	<?php
}

add_action('save_post','cb_set_visibility', 10, 2);

function cb_set_visibility($post_ID, $post)
{
	global $wpdb;
	/*
	$form = isset($_GET["form"]) ? $_GET["form"] : "";
	
	if ($form == "reflex")
	{
		$wpdb->update( $wpdb->posts, array( 'post_content' => "<h2>Reflexion</h2>" ), array( 'ID' => $post_ID ) );
		$wpdb->update( $wpdb->posts, array( 'post_title' => "Reflexion" ), array( 'ID' => $post_ID ) );
	}
	*/
	$visi = isset($_GET["visi"]) ? $_GET["visi"] : "public";

	if ( ! wp_is_post_revision( $post_ID ) ) { //see https://codex.wordpress.org/Function_Reference/wp_update_post
		// unhook this function so it doesn't loop infinitely
		remove_action('save_post', 'cb_set_visibility');
		
		if ($visi == "pwd") 
		{
			// update the post, which calls save_post again
			wp_update_post( array( 'post_password' => rand(1000,9999), 'ID' => $post_ID ) );
		}

		if ($visi == "private") 
		{
			wp_update_post( array( 'post_status' => 'private', 'ID' => $post_ID ) );
		}
	
		// re-hook this function
		add_action('save_post', 'cb_set_visibility');
	}
	
}

add_action( 'admin_enqueue_scripts', 'cb_enqueue', 10, 1 );

function cb_enqueue($hook) {
    //if( 'post-new.php' != $hook && 'post.php' != $hook)
    //   return;
    wp_enqueue_script( 'my_custom_script', plugins_url('/copingblog.js', __FILE__) );

}

add_action( 'wpmu_new_blog', 'cb_new_blog', 10, 6);

function cb_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

	switch_to_blog( $blog_id );
 
	// Change to a different theme
	switch_theme( 'Blogpraktikum' );
 
	
	//$parent_id = wp_create_category( 'Professionsstandards' );
	
	for ($i=1; $i<10; $i++) {
		wp_create_category( 'PS'.$i );
	}

	wp_create_category( 'Reflexion' );

	// Restore to the current blog
	restore_current_blog();
}

register_activation_hook( __FILE__, 'cb_plugin_activate' );

function cb_plugin_activate() {

	add_site_option( 'cb_menu_tumblr', '1' );
	add_site_option( 'cb_menu_blogpr', '' );
}


//add_filter("login_redirect", "cb_login_redirect", 10, 3);

function cb_login_redirect($redirect_to, $request, $user){
	$current_user_blog = get_active_blog_for_user($user->ID);
	$redirect_to = $current_user_blog->siteurl;

    return $redirect_to;
}


//add_action( 'login_enqueue_scripts', 'dp_redir_login' );

function dp_redir_login() {
	if( $_GET['action']=="")
	{ 
		wp_redirect('/');
	 	exit;
	}
}

