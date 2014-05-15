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

function cb_get_user_experimental_group() {
    global $wpdb, $user_email;
    get_currentuserinfo();

	$group = $wpdb->get_row("SELECT * FROM cb_students WHERE email='$user_email' ", ARRAY_A);

	return $group['group'];
}

add_filter('default_content', 'cb_editor_content');

function cb_editor_content( $content ) {
	
	$new_ps = isset($_GET["new_ps"]) ? $_GET["new_ps"] : false;
	if ($new_ps != false) {
		$content = "Datum: <br> Zielebene: <br> Konsequenzen: <br>" ;
	}
	
	$new_refl = isset($_GET["new_reflexion"]) ? $_GET["new_reflexion"] : false;
	if ($new_refl != false) {
		$group_type = cb_get_user_experimental_group();
		$img_1 = '<img class="aligncenter size-full wp-image-306" alt="cb-reflexion" src="' . CB_PLUGIN_URL . '/'.'templates/';
		$img_2 = array(
			CB_GROUP_EF    => 'ef.png" />',
			CB_GROUP_EF_FB => 'ef_fb.png" />',
			CB_GROUP_PF    => 'pf.png"  />',
			CB_GROUP_PF_FB => 'pf_fb.png"  />'			
		);
		if ($group_type == CB_GROUP_EF || $group_type == CB_GROUP_EF_FB) {
			$content  = $img_1 . $img_2[$group_type];
			$content .= "<br> 1) Situationsbeschreibung <br>";
			$content .= "<br> 2) Denkweisen ausprobieren <br>";
			$content .= "<br> 3) Neue Situationsbewertung <br>";
		}
		if ($group_type == CB_GROUP_PF || $group_type == CB_GROUP_PF_FB) {
			$content  = $img_1 . $img_2[$group_type];			
			$content .= "<br> 1) Problemanalyse <br>";
			$content .= "<br> 2) Ideensammlung <br>";
			$content .= "<br> 3) Idee auswählen <br>";
			$content .= "<br> 4) Problemlösestrategie <br>";
			$content .= "<br> 5) Im Alltag handeln und Bilanz ziehen <br>";
		}
		if ($new_refl == "pwd") $content .= "<br>[Passwort für Kommilitone anpassen]" ;
		
	}

	$new_feinpl = isset($_GET["new_feinplanung"]) ? $_GET["new_feinplanung"] : false;
	if ($new_feinpl) {
		$content = "[Formular Feinplanung als PDF hier hochladen]  <br> [Passwort für Mentor anpassen]";
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
	if ($new_refl != false) {
		return "Reflexion: Titel";
	}

	$new_feinpl = isset($_GET["new_feinplanung"]) ? $_GET["new_feinplanung"] : false;
	if ($new_feinpl) {
		return "Feinplanung: Titel";
	}
	
	
}

add_action('save_post', 'cb_save_post');

function cb_save_post( $post_id ){
	$new_ps = isset($_GET["new_ps"]) ? $_GET["new_ps"] : false;
	if ($new_ps != false) {
		$ps = get_cat_ID( $new_ps );
		wp_set_post_categories( $post_id, array($ps));
		cb_update_post($post_id, array( 'post_password' => 'for_my_mentor_'.rand(1000,9999), 'ID' => $post_id ));
	}
	
	$new_refl = isset($_GET["new_reflexion"]) ? $_GET["new_reflexion"] : false;
	if ($new_refl) {
		$refl = get_cat_ID( 'Reflexion' );
		wp_set_post_categories( $post_id, array($refl));
		if($new_refl == "pwd") {
			cb_update_post($post_id, array( 'post_password' => 'for_my_fellow_'.rand(1000,9999), 'ID' => $post_id ));
		}
		if($new_refl == "private") {
			cb_update_post($post_id, array( 'post_status' => 'private', 'ID' => $post_id ));
		}
	}
	
	$new_planung = isset($_GET["new_planung"]) ? $_GET["new_planung"] : false;
	if ($new_planung) {
		$cat = array('Feinplanung', 'Wochenplanung', 'Grobplanung');
		$cat_id = get_cat_ID( $cat[$new_planung-1] );
		wp_set_post_categories( $post_id, array($cat_id));
		cb_update_post($post_id, array( 'post_password' => 'for_my_mentor_'.rand(1000,9999), 'ID' => $post_id ));
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
	echo '<h2><img src="'.CB_PLUGIN_URL.'/'.'icons/glyphicons_119_table.png">'.' Planungsinstrumente</h2>';

	$title = array('Feinplanung', 'Wochenplanung', 'Grobplanung');

	$cat_ids = array();
	for ($i=0; $i<3; $i++) $cat_ids[$i] =  get_cat_ID($title[$i]);

	$ps_count = get_categories( array('include'=> implode($cat_ids,",")) ); 

	$url = array('../wp-content/plugins/copingblog/templates/phsz_feinplanungsraster_fachpraktikum_ohne_ref_ps.docx',
				'https://intranet.phsz.ch/fileadmin/autoren/intranet_berufspraktische_studien/phsz_wochenplanung.docx',
				'https://intranet.phsz.ch/fileadmin/autoren/intranet_berufspraktische_studien/phsz_grobplanung.docx'
				);
	$new = array('new_planung=1', 'new_planung=2', 'new_planung=3');
	for($i=0; $i<3; $i++) {
		echo '<h3>'.$title[$i].'</h3>';
		echo "<a href='".$url[$i]."'>Formular herunterladen</a> | ";
		echo "<a href='edit.php?post_status=all&post_type=post&cat=".$cat_ids[$i].
			"&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[$i]) ? $ps_count[$i]->count : 0) .")</a> | ";
		echo "<a href='post-new.php?".$new[$i]."'>neu</a><br><br>";
	}
}

function cb_display_reflexionps() {
	echo '<h2><img src="'.CB_PLUGIN_URL.'/'.'icons/glyphicons_082_roundabout.png">'.' Reflexion PS</h2>';

	$cat_ids = array();
	for ($i=0; $i<9; $i++) { 
		$cat_ids[$i] = get_cat_ID('PS'.($i+1));
	}
	$ps_count = get_categories( array('include'=> implode($cat_ids,",")) );
		
	$pre = array();
	for ($i=0; $i<9; $i++) {
		$i_1 = $i+1;
		$pre[$i] = '<a href="edit.php?post_status=all&post_type=post&cat='.
			get_cat_ID( "PS".$i_1 ) . '&paged=1&mode=excerpt" title="anzeigen"><img src="' .
			CB_PLUGIN_URL.'/'.'icons/glyphicons_195_circle_info_small.png"></a>&nbsp;';

		$pre[$i] .= "<a href=\"post-new.php?new_ps=ps$i_1\" title=\"neu\"><img src=\"" . 
			CB_PLUGIN_URL.'/'."icons/glyphicons_190_circle_plus_small.png\"></a>&nbsp;&nbsp;";
	}
	echo "<br>";
	echo "<h3>".$pre[0];
	echo "PS 1 versteht und vermittelt Fachinhalte [".(isset($ps_count[0]) ? $ps_count[0]->count : 0)."]</h3>";
	echo "<div>".CB_PS1_HTML."</div>";
	
	echo "<br><h3>".$pre[1];	
	echo "PS 2 versteht und unterstützt Entwicklungsprozesse [".(isset($ps_count[1]) ? $ps_count[1]->count : 0)."]</h3>";
	echo "<div>".CB_PS2_HTML."</div>";

	echo "<br><h3>".$pre[2];
	echo "PS 3 versteht und berücksichtigt Unterschiede im Lernen [".(isset($ps_count[2]) ? $ps_count[2]->count : 0)."]</h3>";
	echo "<div>".CB_PS3_HTML."</div>";

	echo "<br><h3>".$pre[4];
	echo "PS 5 motiviert und leitet an[".(isset($ps_count[4]) ? $ps_count[4]->count : 0)."]</h3>";
	echo "<div>".CB_PS5_HTML."</div>";

	echo "<br><h3>".$pre[5];
	echo "PS 6 kommuniziert und moderiert[".(isset($ps_count[5]) ? $ps_count[5]->count : 0)."]</h3>";
	echo "<div>".CB_PS6_HTML."</div>";

	echo "<br><h3>".$pre[6];
	echo "PS 7 plant und evaluiert[".(isset($ps_count[6]) ? $ps_count[6]->count : 0)."]</h3>";
	echo "<div>".CB_PS7_HTML."</div>";

	echo "<br><h3>".$pre[7];
	echo "PS 8 beobachtet und fördert[".(isset($ps_count[7]) ? $ps_count[7]->count : 0)."]</h3>";
	echo "<div>".CB_PS8_HTML."</div>";

	echo "<br><h3>".$pre[8];
	echo "PS 9 reflektiert ihre eigene Professionalität (Erfahrungen)[".(isset($ps_count[8]) ? $ps_count[8]->count : 0)."]</h3>";
	echo "<div>".CB_PS9_HTML."</div>";
	
} //end cb_display_feinplanung

function cb_display_reflexion() {
    global $wpdb;

	echo '<h2><img src="'.CB_PLUGIN_URL.'/'.'icons/glyphicons_080_retweet.png">'.' Bloggen</h2>';
	$group = cb_get_user_experimental_group();
	if ( $group == CB_GROUP_EF || $group == CB_GROUP_EF_FB ) { 
		echo "<div><ol>";
		echo "<li>".CB_EF_REFLEXION_HTML_1."</li>";
		echo "<li>".CB_EF_REFLEXION_HTML_2."</li>";
		echo "<li>".CB_EF_REFLEXION_HTML_3."</li>";
		if ($group == CB_GROUP_EF_FB) echo "<li>".CB_EF_REFLEXION_HTML_4."</li>";
		echo "</ol></div>";
	}
	if ( $group == CB_GROUP_PF  ||  $group == CB_GROUP_PF_FB ) { 
		echo "<div><ol>";
		echo "<li>".CB_PF_REFLEXION_HTML_1."</li>";
		echo "<li>".CB_PF_REFLEXION_HTML_2."</li>";
		echo "<li>".CB_PF_REFLEXION_HTML_3."</li>";
		echo "<li>".CB_PF_REFLEXION_HTML_4."</li>";
		echo "<li>".CB_PF_REFLEXION_HTML_5."</li>";
		if ($group == CB_GROUP_PF_FB) echo "<li>".CB_PF_REFLEXION_HTML_6."</li>";
		echo "</ol></div>";
	}
	
	$cat_ids =  get_cat_ID('Reflexion');
	$ps_count = get_categories( array('include'=> $cat_ids) ); 
	
	//echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'Reflexion' )."&paged=1&mode=excerpt'>anzeigen (". (isset($ps_count[0]) ? $ps_count[0]->count : 0) .")</a> | ";
	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'Reflexion' )."&paged=1&mode=excerpt'><img src='".CB_PLUGIN_URL.'/'."icons/glyphicons_195_circle_info_small.png'></a>&nbsp;&nbsp;";
	echo "<a href='post-new.php?new_reflexion=".($group==CB_GROUP_EF_FB || $group==CB_GROUP_PF_FB ? "pwd" : "private")."'><img src='".CB_PLUGIN_URL.'/'."icons/glyphicons_190_circle_plus_small.png'></a><br><br>";
}

function cb_display_evaluation() {
	echo '<h2><img src="'.CB_PLUGIN_URL.'/'.'icons/glyphicons_041_charts.png">'.' Evaluation</h2>';
	echo "<a href='http://fragebogen.blogpraktikum.ch' target=_blank>Link zum Fragebogen</a>";
}

//add_action('admin_menu', 'cb_menu_copingblog');
add_action('admin_menu', 'cb_menu_copingblog_minimal');

function cb_menu_copingblog_minimal() {
	$menu_blogpr = get_site_option( 'cb_menu_blogpr' );
	if(isset($menu_blogpr) && $menu_blogpr==1) {
		if (cb_get_user_experimental_group() != CB_GROUP_CTRL) add_menu_page( "Bloggen", "Bloggen", "edit_posts", "reflexion_menu", 'cb_display_reflexion', CB_PLUGIN_URL.'/'.'icons/glyphicons_330_blog_invers.png');
	}

}

function cb_menu_copingblog() {
	$menu_blogpr = get_site_option( 'cb_menu_blogpr' );
	if(isset($menu_blogpr) && $menu_blogpr==1) {
		add_menu_page("Blogging Tool", "Blogging Tool", "edit_posts", "feinplanung_menu", 'cb_display_feinplanung', CB_PLUGIN_URL.'/'.'icons/glyphicons_330_blog_invers.png');
		add_submenu_page( "feinplanung_menu", "Planungsinstrumente", "Planungsinstrumente", "edit_posts", "feinplanung_menu", 'cb_display_feinplanung');
		add_submenu_page( "feinplanung_menu", "Reflexion PS", "Reflexion PS", "edit_posts", "reflexionps_menu", 'cb_display_reflexionps');
		if (cb_get_user_experimental_group() != CB_GROUP_CTRL) add_submenu_page( "feinplanung_menu", "Bloggen", "Bloggen", "edit_posts", "reflexion_menu", 'cb_display_reflexion');
		add_submenu_page( "feinplanung_menu", "Evaluation", "Evaluation", "edit_posts", "evaluation_menu", 'cb_display_evaluation');
	}

}

add_action('update_wpmu_options', 'cb_update_options');

function cb_update_options() {
	$options = array('cb_menu_tumblr', 'cb_menu_blogpr', 'cb_group_type');
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
		<tr valign="top">
			<th scope="row"><?php _e( 'Group type' ) ?></th>
			<?php
			if ( !get_site_option( 'cb_group_type' ) )
				update_site_option( 'cb_group_type', 'ef' );
			$reg = get_site_option( 'cb_group_type' );
			?>
			<td>
				<label><input name="cb_group_type" type="radio" id="cb_group_type_1" value="ef"<?php checked( $reg, 'ef') ?> /> <?php _e( 'Emotion focused group' ); ?></label><br />
				<label><input name="cb_group_type" type="radio" id="cb_group_type_2" value="pf"<?php checked( $reg, 'pf') ?> /> <?php _e( 'Problem focused group' ); ?></label><br />
				<label><input name="cb_group_type" type="radio" id="cb_group_type_3" value="ctrl"<?php checked( $reg, 'ctrl') ?> /> <?php _e( 'Control group' ); ?></label><br />
			</td>
		</tr>

	</table>
	<?php
}

add_action('save_post','cb_set_visibility', 10, 1);

function cb_set_visibility($post_ID)
{
	//global $wpdb;
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

add_action( 'wpmu_new_blog', 'cb_new_blog', 1000, 6); //do it after set_blog_defaults

function cb_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

	switch_to_blog( $blog_id );
 
	// Change to a different theme
	//switch_theme( 'Blogpraktikum' );
	
	wp_create_category( 'Reflexion' );
	/* 
	for ($i=1; $i<10; $i++) {
		wp_create_category( 'PS'.$i );
	}
	
	wp_create_category( 'Feinplanung' );
	wp_create_category( 'Wochenplanung' );
	wp_create_category( 'Grobplanung' );
	*/
	
	//sitebar categories widget active
	delete_option('sidebars_widgets');
	
	// Restore to the current blog
	restore_current_blog();
}

add_action( 'init', 'cb_init' );

function cb_init() {
	if ( !defined( 'CB_GROUP_EF' ) )    define( 'CB_GROUP_EF', 'ef');
	if ( !defined( 'CB_GROUP_EF_FB' ) ) define( 'CB_GROUP_EF_FB', 'ef_fb');
	if ( !defined( 'CB_GROUP_PF' ) )    define( 'CB_GROUP_PF', 'pf');
	if ( !defined( 'CB_GROUP_PF_FB' ) ) define( 'CB_GROUP_PF_FB', 'pf_fb');
	if ( !defined( 'CB_GROUP_CTRL' ) )  define( 'CB_GROUP_CTRL', 'ctrl');
	
	if ( !defined( 'CB_PLUGIN_URL' ) )  define( 'CB_PLUGIN_URL', plugins_url( '', __FILE__ ) );
	
	if ( !defined( 'CB_EF_REFLEXION_HTML_1' ) )     define( 'CB_EF_REFLEXION_HTML_1', "<strong>Situationsbeschreibung</strong> <br> Beschreiben Sie eine Situation im Praktikum, die Sie heute als Herausforderung oder als Belastung erlebt haben und erklären Sie, wie Sie über diese Situation denken und welche Bedeutung sie für Sie hat." );
	
	if ( !defined( 'CB_EF_REFLEXION_HTML_2' ) )     define( 'CB_EF_REFLEXION_HTML_2', "<strong>Denkweisen ausprobieren</strong> <br> Überlegen Sie, wie Sie anders über die Situation denken könnten. Was ist das Gute an dieser Situation und welche Stärken und Ressourcen haben Sie?");

	if ( !defined( 'CB_EF_REFLEXION_HTML_3' ) )     define( 'CB_EF_REFLEXION_HTML_3', "<strong>Neue Situationsbewertung</strong> <br> Formulieren Sie eine für Sie förderliche Denkweise, die Ihnen hilft, mit der Situation in positiver Weise umzugehen.");

	if ( !defined( 'CB_EF_REFLEXION_HTML_4' ) )     define( 'CB_EF_REFLEXION_HTML_4', "<i>Social Blogging (mit Peerfeedback) <br> Lesen Sie mindestens einen aktuellen Beitrag von einem Kollegen oder einer Kollegin und schreiben Sie einen kurzen Kommentar. Geben Sie Feedback oder Hinweise und unterstützen Sie sich gegenseitig.</i>");
	
	if ( !defined( 'CB_PF_REFLEXION_HTML_1' ) )     define( 'CB_PF_REFLEXION_HTML_1', "<strong>Problemanalyse</strong> <br> Beschreiben Sie eine Situation im Praktikum, die Sie heute als Herausforderung oder als Belastung erlebt haben und erklären Sie, welche Aspekte in dieser Situation zusammenspielen.");

	if ( !defined( 'CB_PF_REFLEXION_HTML_2' ) )     define( 'CB_PF_REFLEXION_HTML_2', "<strong>Ideensammlung</strong> <br> Überlegen Sie sich verschiedene Strategien, wie Sie die geschilderte Herausforderung künftig vermeiden, verändern, bewältigen könnten und schreiben Sie das stichwortartig auf. Seien Sie kreativ.");

	if ( !defined( 'CB_PF_REFLEXION_HTML_3' ) )     define( 'CB_PF_REFLEXION_HTML_3', "<strong>Idee auswählen</strong> <br> Welche Aspekte der Situation können Sie beeinflussen und welche nicht? Welche sind wichtig und welche weniger entscheidend?");
	
	if ( !defined( 'CB_PF_REFLEXION_HTML_4' ) )     define( 'CB_PF_REFLEXION_HTML_4', "<strong>Problemlösestrategie</strong> <br> Wählen Sie die Strategie, die für Sie am besten passt und beschreiben Sie die konkreten Handlungsschritt, die Sie nun umsetzen wollen.");

	if ( !defined( 'CB_PF_REFLEXION_HTML_5' ) )     define( 'CB_PF_REFLEXION_HTML_5', "<strong>Im Alltag handeln und Bilanz ziehen</strong> <br> Im nächsten Blogeintrag sollten Sie dann kurz beschreiben, ob Sie mit Ihrer Strategie erfolgreich waren. Was hat gut funktioniert und was weniger? Warum?");

	if ( !defined( 'CB_PF_REFLEXION_HTML_6' ) )     define( 'CB_PF_REFLEXION_HTML_6', "<i>Social Blogging (mit Peerfeedback) <br> Lesen Sie mindestens einen aktuellen Beitrag von einem Kollegen oder einer Kollegin und schreiben Sie einen kurzen Kommentar. Geben Sie Feedback oder Hinweise und unterstützen Sie sich gegenseitig.</i>");

	if ( !defined( 'CB_PS1_HTML' ) )     define( 'CB_PS1_HTML', 'Die Lehrperson verfügt über fachwissenschaftliches und fachdidaktisches Wissen, versteht die Inhalte, Strukturen und zentralen Forschungsmethoden ihrer Fachbereiche und sie kann Lern- situationen schaffen, die die fachwissenschaftlichen und fachdidaktischen Aspekte für die Lernenden bedeutsam machen. <br>
	&nbsp;&nbsp;1.3 erkennt Zusammenhänge zwischen verschiedenen Fachbereichen (Niveau 1) <br>
	&nbsp;&nbsp;1.4 wählt Ziele und Inhalte erziehungs- und gesellschaftswissenschaftlich begründbar aus
	(Niveau 1)');

	if ( !defined( 'CB_PS2_HTML' ) )     define( 'CB_PS2_HTML', 'Die Lehrperson versteht, wie Kinder und Erwachsene lernen und sich entwickeln, und sie kann Lerngelegenheiten und Lernwege anbieten, welche die kognitive, soziale und persönliche Entwicklung unterstützen. <br>
	&nbsp;&nbsp;2.2 aktiviert Erfahrungen und Wissen (Niveau 2) <br>
	&nbsp;&nbsp;2.5 fördert selbstgesteuertes Lernen (Niveau 2)');
	
	if ( !defined( 'CB_PS3_HTML' ) )     define( 'CB_PS3_HTML', 'Die Lehrperson versteht, wie verschieden die Wege zum Lernen sind und schafft Unterrichtssituationen, die auf die Lernenden individuell angepasst sind. <br>
&nbsp;&nbsp;3.4 begünstigt eigenständiges Lernen (Niveau 2)');
	
	if ( !defined( 'CB_PS5_HTML' ) )     define( 'CB_PS5_HTML', 'Die Lehrperson setzt ihr Verständnis über Motivationsprozesse und Klassenmanagement ge- zielt ein, um Lernsituationen zu schaffen, die die positive soziale Zusammenarbeit der Kinder und Jugendlichen fördert und selbstgesteuertes Lernen zulassen. <br>
&nbsp;&nbsp;5.4 setzt Verhaltenserwartungen, fordert diese ein und fördert sozial erwünschtes Verhalten und das Klassenklima (Niveau 1) <br>
&nbsp;&nbsp;5.5 fördert soziale Zusammenarbeit (Niveau 1)');
	
	if ( !defined( 'CB_PS6_HTML' ) )     define( 'CB_PS6_HTML', 'Die Lehrperson verwendet ihr Wissen von effektiven verbalen und nicht verbalen Kommunikati- ons- und Medienformen, um aktives Lernen, Mitarbeit und den gegenseitigen Austausch im Klassenzimmer zu fördern. <br>
&nbsp;&nbsp;6.3 fördert die Diskussionskultur (Niveau 1)');
	
	if ( !defined( 'CB_PS7_HTML' ) )     define( 'CB_PS7_HTML', 'Die Lehrperson plant, realisiert und evaluiert ihren Unterricht auf Grund ihres Verständnisses vom Fachbereich, von Lehrplan und Leitideen der Schule, und auf der Basis des berufswissen- schaftlichen Hintergrundes. <br>
&nbsp;&nbsp;7.1 setzt Leitideen und Lehrplan im Unterricht um (Niveau 1) <br>
&nbsp;&nbsp;7.2 plant den Unterricht systematisch (Niveau 1) <br>
&nbsp;&nbsp;7.3 passt den Unterricht situativ an (Niveau 1)');
	
	if ( !defined( 'CB_PS8_HTML' ) )     define( 'CB_PS8_HTML', 'Die Lehrperson versteht und verwendet gezielt unterschiedliche Beurteilungssysteme, um die kognitive, soziale und persönliche Entwicklung der Kinder und Jugendlichen fortlaufend einzuschätzen, zu sichern und zu fördern. <br>
&nbsp;&nbsp;8.1 beurteilt und bewertet differenziert (Niveau 1)');
	
	if ( !defined( 'CB_PS9_HTML' ) )     define( 'CB_PS9_HTML', 'Die Lehrperson reflektiert fortlaufend die Wirkung ihrer Entscheide und Tätigkeiten auf andere (Lernende, Eltern und auf andere Lehrende) und sie geht ihre professionelle Weiterentwicklung aktiv und verantwortungsbewusst an. <br>
&nbsp;&nbsp;9.1 reflektiert und entwickelt den Unterricht (Niveau 1)');

}

register_activation_hook( __FILE__, 'cb_plugin_activate' );

function cb_plugin_activate() {

	add_site_option( 'cb_menu_tumblr', '1' );
	add_site_option( 'cb_menu_blogpr', '' );
	add_site_option( 'cb_group_type', 'ef' );
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

