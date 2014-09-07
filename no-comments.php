<?php

    /*
    Plugin Name: No Comments
    Plugin URI: http://www.stevenfernandez.co.uk/wordpress-plugins/
    Description: `No Comments` plugin totally gets rid of comments. Just activate the plugin and it's all gone!
    Author: Steven Fernandez
    Version: 1.0.1
    Author URI: http://www.stevenfernandez.co.uk

    === RELEASE NOTES ===
    20-04-2014 - v1.0 - first version

    07-09-2014 - v1.0.1 - Compatible with wordpress 4.0 
    */

if (!class_exists ('no_comments')) :
	class no_comments {
	
	public function __construct() {
		no_comments::init();	
	}
	
	public static function init() {
		add_action('init', array('no_comments', 'init_start'));
		add_action('wp_head', array('no_comments', 'no_comments_css'));
		add_action('wp_meta', array('no_comments', 'no_comments_link_meta'));
		add_action('admin_menu', array('no_comments', 'no_discussion_options'));
		add_action('admin_head', array('no_comments', 'dashboard_right_now_no_discussion'));
		add_action('admin_bar_menu', array('no_comments', 'no_admin_bar_comments'), 99);
		add_action('get_comments_number', array('no_comments', 'comments_number_always_zero'));
		add_action('comments_template', array('no_comments', 'change_comments_template'));
		add_action('widgets_init', array('no_comments', 'remove_comments_widget'), 0);
		add_action('wp_dashboard_setup', array('no_comments', 'no_dashboard_comments_widget'), 0);
	}
	
	public static function init_start() {
		$args = array('public' => true, '_builtin' => true);
		
		foreach ( apply_filters( 'hide_comments_post_types', get_post_types( $args ) ) as $post_type )	{
			if ( post_type_supports( $post_type, 'comments' ) )
				remove_post_type_support( $post_type, 'comments' );
				
		}
		
	}
	
	public static function no_comments_css() {
		?>
		<style type="text/css">
			.comments-link {
				display: none;
			}
			<?php do_action( 'no_comments_css' ); ?>
		</style>
		
		<?php
		
	}
	
	public static function no_comments_link_meta() {
		?>
		<style type="text/css">
			.widget_meta li:nth-child(4) {
				display: none;
			}
		</style>
		
		<?php
	}
	
	public static function no_discussion_options() {
		remove_menu_page( 'edit-comments.php' );
		remove_submenu_page( 'options-general.php', 'options-discussion.php' );
		
	}
	
	public static function dashboard_right_now_no_discussion() {
		if ( ! apply_filters( 'hide_comments_dashboard_right_now', true ) )
			return;
		
		?>
		<style type="text/css">
			#dashboard_right_now .table_discussion {
				display: none;
			}
		</style>
		<?php
	}
	
	public static function no_admin_bar_comments( $admin_bar ) {
		$admin_bar->remove_menu( 'comments' );
		return $admin_bar;
		
	}
	
	public static function comments_number_always_zero() {
		return 0;
		
	}
	
	public static function change_comments_template() {
		global $wp_query;
		
		$wp_query->comments = array();
		$wp_query->comments_by_type = array();
		$wp_query->comment_count = '0';
		$wp_query->post->comment_count = '0';
		$wp_query->post->comment_status = 'closed';
		$wp_query->queried_object->comment_count = '0';
		$wp_query->queried_object->comment_status = 'closed';
		
		return apply_filters('hide_comments_template_comments_path', plugin_dir_path( __FILE__ ) . 'temp-comments.php' );
		
	}
	
	public static function remove_comments_widget() {
		if ( function_exists( 'unregister_widget' ) ) {
			unregister_widget( 'WP_Widget_Recent_Comments' );
	
		}
		
	}
	
	public static function no_dashboard_comments_widget() {
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		
	}
	
	
}

new no_comments;

endif;



function hide_comments_activation_hook() {
	if (version_compare(get_bloginfo('version'), '3.5', '<')) {
		wp_die( 'No Comments is not compatible with versions prior to 3.5' );
	}
	
	update_option( 'no_comments_plugin_version', '1.0.0' );	
}
register_activation_hook( __FILE__, 'hide_comments_activation_hook' );
