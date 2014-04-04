<?php

class Reflexion {
	
	public static function display_old() {
		
		$form = '<br><div id="reflex_form" style="width:60%">';
		$form .= '<form action="">
			
			<h3><a id="a_ps1" href="#">PS 1 versteht und vermittelt Fachinhalte ([5])</a></h3>
			<div id="ps1" style="display: none;">
			<p>Die Lehrperson verfügt...</p>';

		$ps = array();
		$max_i = array();
		$max_i[1] = 2;
		$max_i[2] = 3;
		for ($j=1; $j<3; $j++) {
			for ($i=1; $i<=$max_i[$j]; $i++) {
				$ps[$j] .= "<div id='ps_{$j}_{$i}' style='border-bottom:1px solid;margin-left:20px;'>
					<p>Datum:
					<input type='text1_{$j}_{$i}'></p>
					<p>Zielebene:</p>
					<textarea name='text2_{$j}_{$i}' cols='60' rows='5'></textarea><br>
					<p>Konsequenzen:</p>
					<textarea name='text3_{$j}_{$i}' cols='60' rows='5'>hallo</textarea><br>
					</div>";
			}
		}	
		
		/*	<div id="ps1_2" style="border-bottom:1px solid;">
			<p>Datum:
			<input type="text"></p>
			<p>Zielebene:</p>
			<textarea name="text1_2" cols="60" rows="5"></textarea><br>
			<p>Konsequenzen:</p>
			<textarea name="text2_2" cols="60" rows="5">hallo</textarea><br>
			<p><a href="">hinzufügen</a></p>
			</div>
		*/

		/*	</div>
			<h3>PS 2 versteht und vermittelt Fachinhalte ([5])</h3>
			<textarea name="text3" cols="40" rows="5" >hallo</textarea><br>
			';
		*/
		echo $form;
		echo $ps[1];
		echo '<a href="#" id="neu_ps1">+neu+</a>';
		echo '</div>';
		echo '<h3><a id="a_ps2" href="#">PS 2 versteht und vermittelt Fachinhalte ([5])</a></h3>
			<div id="ps2" style="display: none;">
			<p>Die Lehrperson verfügt...</p>';
		echo $ps[2];
		echo '</div>';
		/*echo '<div style="display:true;">';
		wp_editor( "hallo", "text2", array('media_buttons'=>false));
		echo '</div>';
		*/
		
		echo '</form></div>';
		

		
	} //end display
	
	public static function display_feinplanung() {
		echo "Formular downloaden<br>";
		echo "Formular uploaden";
	}
	
	public static function display_reflexion() {
		$new_ps = isset($_GET["new_ps"]) ? $_GET["new_ps"] : false;
		if ($new_ps) {
			Reflexion::new_ps();
			exit;
		}
		echo "PS 1 versteht und vermittelt Fachinhalte<br>";
		echo "<a href='#'>anzeigen</a><br>";
		echo "<a href='?page=reflexion_menu&new_ps=true'>neu</a><br><br>";
		echo "PS 2 ...";
		
		
	}
	
	public static function new_ps() {
		
		// Create post object
		$my_post = array(
		  'post_title'    => 'My post',
		  'post_content'  => 'This is my post.',
		  'post_status'   => 'publish',
		  'post_author'   => 1,
		  'post_category' => array(1)
		);

		// Insert the post into the database
		//$post_id = wp_insert_post( $my_post );
		//wp_redirect("wp-admin/post.php?post=$post_id&action=edit");
		wp_redirect( home_url() );
		exit;
		
	}
	
	public static function init() {
		add_menu_page( "Feinplanung", "Feinplanung", "read", "feinplanung_menu", array(__CLASS__, 'display_feinplanung'));
		add_submenu_page( "feinplanung_menu", "Reflexion", "Reflexion", "read", reflexion_menu, array(__CLASS__, 'display_reflexion'));
	}
} //end class