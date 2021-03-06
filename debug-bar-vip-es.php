<?php
/*
	Plugin Name: Debug Bar VIP Elasticsearch Add-on
	Plugin URI: http://www.alleyinteractive.com/
	Description: Simple debug-bar add-on for working with VIP's Elasticsearch Service
	Version: 0.1
	Author: Matthew Boynes
	Author URI: http://www.alleyinteractive.com/
*/
/*  This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


if ( !class_exists( 'Debug_Bar_VIP_ES' ) ) :

class Debug_Bar_VIP_ES {

	private static $instance;

	public $content;

	private function __construct() {
		/* Don't do anything, needs to be initialized via instance() method */
	}

	public function __clone() { wp_die( "Please don't __clone Debug_Bar_VIP_ES" ); }

	public function __wakeup() { wp_die( "Please don't __wakeup Debug_Bar_VIP_ES" ); }

	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Debug_Bar_VIP_ES;
			self::$instance->setup();
		}
		return self::$instance;
	}

	public function setup() {
		$this->content = array(
			'es_wp_query_args'   => array(),
			'es_query_args'      => array(),
			'wrp_args'           => array(),
			'response'           => array()
		);
		add_filter( 'debug_bar_panels',                   array( $this, 'add_panels' )                );
		add_filter( 'wpcom_elasticsearch_wp_query_args',  array( $this, 'es_wp_query_args' ),  99     );
		add_filter( 'wpcom_elasticsearch_query_args',     array( $this, 'es_query_args' ),     99     );
		add_filter( 'wrp_es_api_search_index_args',       array( $this, 'wrp_args' ),          99     );
		add_filter( 'http_api_debug',                     array( $this, 'post_request' ),      10, 5  );
		add_action( 'debug_bar_enqueue_scripts',          array( $this, 'static_files' )              );
	}

	public function static_files() {
		wp_enqueue_style( 'debug-bar-vip-es', plugins_url( "css/debug-bar-vip-es.css", __FILE__ ), array(), '1.0.1' );
	}

	public function add_panels( $panels ) {
		require_once( 'class-debug-bar-vip-es-panel.php' );
		$panels[] = new Debug_Bar_VIP_ES_Panel();
		return $panels;
	}

	public function es_wp_query_args( $args ) {
		$count = count( $this->content['es_wp_query_args'] ) + 1;
		$this->content['es_wp_query_args'][] = "
		<h4>WordPress Args Request #{$count}</h4>
		<pre>" . print_r( $args, 1 ) . "</pre>
		";
		return $args;
	}

	public function es_query_args( $args ) {
		$count = count( $this->content['es_query_args'] ) + 1;
		$this->content['es_query_args'][] = "
		<h4>ES Args #{$count}</h4>
		<dl>
			<dt>PHP</dt>
				<dd><pre>" . print_r( $args, 1 ) . "</pre></dd>
			<dt>JSON</dt>
				<dd><pre>" . json_encode( $args ) . "</pre></dd>
		</dl>
		";
		return $args;
	}

	public function wrp_args( $args ) {
		$count = count( $this->content['wrp_args'] ) + 1;
		$this->content['wrp_args'][] = "
		<h4>WRP Args #{$count}</h4>
		<dl>
			<dt>PHP</dt>
				<dd><pre>" . print_r( $args, 1 ) . "</pre></dd>
			<dt>JSON</dt>
				<dd><pre>" . json_encode( $args ) . "</pre></dd>
		</dl>
		";
		return $args;
	}

	public function post_request( $response, $type, $class, $args, $url ) {
		if ( preg_match( "#http://public-api\.wordpress\.com/rest/v1/sites/\d+/search#i", $url ) ) {
			$body = wp_remote_retrieve_body( $response );
			$count = count( $this->content['response'] ) + 1;
			$this->content['response'][] = "
			<h4>HTTP Request #{$count}</h4>
			<dl>
				<dt>URL</dt>
					<dd>{$url}</dd>
				<dt>Request Body</dt>
					<dd><pre>{$args['body']}</pre></dd>
				<dt>Response (full)</dt>
					<dd><pre>" . print_r( $response, 1 ) . "</pre></dd>
				<dt>Response Body (raw)</dt>
					<dd><pre>{$body}</pre></dd>
				<dt>Response Body (decoded)</dt>
					<dd><pre>" . print_r( json_decode( $body ), 1 ) . "</pre></dd>
			</dl>
			";
		}
		return $response;
	}

	public function listify( $array ) {
		if ( !empty( $array ) )
			return "
			<ol class='vip-es-debug-list'>
				<li class='vip-es-debug-list-item'>
					" . implode( "</li>\n\t\t\t\t<li class='vip-es-debug-list-item'>", $array ) . "
				</li>
			</ol>
			";
		else
			return "<p>None found</p>";
	}
}

function Debug_Bar_VIP_ES() {
	return Debug_Bar_VIP_ES::instance();
}
add_action( 'plugins_loaded', 'Debug_Bar_VIP_ES' );

endif;