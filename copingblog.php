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

add_filter('default_content', 'my_editor_content');

function my_editor_content( $content ) {
	
	$new_ps = isset($_GET["new_ps"]) ? $_GET["new_ps"] : false;
	
	if ($new_ps != false) {
		$content = "Datum: <br> Zielebene: <br> Konsequenzen: <br>" ;
	}
	
	return $content;
}

add_filter('default_title', 'my_editor_title');

function my_editor_title() {
	$new_ps = isset($_GET["new_ps"]) ? $_GET["new_ps"] : false;
	
	if ($new_ps != false) {
		return $new_ps . ": mein schöner titel";
	}
}

add_action('save_post', 'my_save_post');

function my_save_post( $post_id ){
	$new_ps = isset($_GET["new_ps"]) ? $_GET["new_ps"] : false;
	
	if ($new_ps != false) {
		$ps = get_cat_ID( $new_ps );
		wp_set_post_categories( $post_id, array($ps));
	}
}

function display_feinplanung() {
	echo "link zum fragebogen";
}

function display_reflexion() {
	$cat_ids =  get_cat_ID('ps1').",".get_cat_ID('ps2');
	$ps_count = get_categories( array('include'=> $cat_ids) ); 
	
	echo "<h3>PS 1 versteht und vermittelt Fachinhalte</h3>";
	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'ps1' )."&paged=1&mode=excerpt'>anzeigen (".$ps_count[0]->count.")</a><br>";
	echo "<a href='post-new.php?new_ps=ps1'>neu</a><br><br>";
	echo "<h3>PS 2 ...</h3>";
	echo "<a href='edit.php?post_status=all&post_type=post&cat=".get_cat_ID( 'ps2' )."&paged=1&mode=excerpt'>anzeigen (".$ps_count[1]->count.")</a><br>";
	echo "<a href='post-new.php?new_ps=ps2'>neu</a><br><br>";
	
}

function menu_copingblog() {
	//include_once('reflexion.class.php');
	//$reflexion = new Reflexion();
	//$reflexion->init();
	add_menu_page( "Feinplanung", "Feinplanung", "read", "feinplanung_menu", 'display_feinplanung');
	add_submenu_page( "feinplanung_menu", "Reflexion", "Reflexion", "read", "reflexion_menu", 'display_reflexion');
}
add_action('admin_menu', 'menu_copingblog');

add_action('save_post','set_visibility', 10, 2);

function set_visibility($post_ID, $post)
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
		remove_action('save_post', 'set_visibility');
		
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
		add_action('save_post', 'set_visibility');
	}
	
}

add_action( 'admin_enqueue_scripts', 'my_enqueue', 10, 1 );

function my_enqueue($hook) {
    //if( 'post-new.php' != $hook && 'post.php' != $hook)
    //   return;
    wp_enqueue_script( 'my_custom_script', plugins_url('/copingblog.js', __FILE__) );

}

//add_filter("login_redirect", "my_login_redirect", 10, 3);

function my_login_redirect($redirect_to, $request, $user){
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

