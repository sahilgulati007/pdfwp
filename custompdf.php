<?php
/**
 * Plugin Name:       Custom PDF
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Handle the basics with this plugin.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sahil Gulati
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-pdf
 * Domain Path:       /languages
 */

function add_sticky_column( $columns ) {
	$columns['pdf'] = __('PDF');
        return $columns;
}
add_filter( 'manage_posts_columns' , 'add_sticky_column' );
function wpdocs_posts_custom_columns( $column_name, $id ) {
    if ( 'pdf' === $column_name ) {
        echo $id;
        echo "<button type='button' onClick='myfun(".$id.")'>Click for PDF</button>";
        echo "<a href='http://localhost/wordpressnew/wp-content/plugins/custompdf/genratepdf.php?postid=".$id."'>Download</a>'";
        echo "<a href='".admin_url( 'admin-ajax.php' )."?action=my_action&postid=".$id."' target='_blank'>Download</a>'";
    }
}
add_action( 'manage_posts_custom_column', 'wpdocs_posts_custom_columns', 5, 2 );

//pages
function add_sticky_column_page( $columns ) {
	$columns['pdf'] = __('PDF');
        return $columns;
}
add_filter( 'manage_page_posts_columns' , 'add_sticky_column_page' );

function wpdocs_page_posts_custom_columns( $column_name, $id ) {
    if ( 'pdf' === $column_name ) {
        echo $id;
        echo "<button>Click for PDF</button>";
    }
}
add_action( 'manage_page_posts_custom_column', 'wpdocs_page_posts_custom_columns', 5, 2 );

function my_load_scripts($hook) {
 
    // create my own version codes
    $my_js_ver  = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . 'js/custom.js' ));
    //$my_css_ver = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . 'style.css' ));
     
    // 
    wp_enqueue_script( 'custom_js', plugins_url( 'js/custom.js', __FILE__ ), array(), $my_js_ver );
    // wp_register_style( 'my_css',    plugins_url( 'style.css',    __FILE__ ), false,   $my_css_ver );
    // wp_enqueue_style ( 'my_css' );
 
}
add_action('wp_enqueue_scripts', 'my_load_scripts');
add_action( 'admin_enqueue_scripts', 'my_load_scripts' );

add_action( 'wp_ajax_my_action', 'my_action' );

function my_action() {
    global $wpdb; // this is how you get access to the database

    $id = intval( $_REQUEST['postid'] );

    $table_name = $wpdb->prefix . 'posts';
$p = $wpdb->get_row("SELECT * from $table_name where id=".$id);
// $p= get_post($id)
// echo "<pre>";
// print_r($p);
// wp_die();

        require_once __DIR__ . '/vendor/autoload.php';

// $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
$mpdf = new \Mpdf\Mpdf();
$html ="<h1>".$p->post_title."</h1>";
$html .="<p>".$p->post_content."</p>";
$mpdf->WriteHTML($html);
// $mpdf->Output();
$mpdf->Output('filename.pdf','D');

    wp_die(); // this is required to terminate immediately and return a proper response
}
define('ROOTDIR', plugin_dir_path(__FILE__));
//require_once(ROOTDIR . 'genratepdf.php');
