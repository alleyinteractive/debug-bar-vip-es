<?php

class Debug_Bar_VIP_ES_Panel extends Debug_Bar_Panel {

	public function init() {
		$this->title( __( 'Elasticsearch', 'debug-bar-vip-es' ) );
	}

	public function prerender() {
		$this->set_visible( true );
	}

	public function render() {
		?>
		<div id="debug-bar-vip-es">
			<h3>es_wp_query_args</h3>
			<?php echo Debug_Bar_VIP_ES()->listify( Debug_Bar_VIP_ES()->content['es_wp_query_args'] ) ?>

			<h3>es_query_args</h3>
			<?php echo Debug_Bar_VIP_ES()->listify( Debug_Bar_VIP_ES()->content['es_query_args'] ) ?>

			<h3>wrp_args</h3>
			<?php echo Debug_Bar_VIP_ES()->listify( Debug_Bar_VIP_ES()->content['wrp_args'] ) ?>

			<h3><?php _e( 'HTTP Endpoint Requests', 'debug-bar-vip-es' ); ?></h3>
			<?php echo Debug_Bar_VIP_ES()->listify( Debug_Bar_VIP_ES()->content['response'] ) ?>
		</div>
		<?php
	}
}
