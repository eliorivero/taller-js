<?php
/**
 * Plugin Name: Taller de JS
 * Plugin URI: https://github.com/eliorivero/taller-js/
 * Description: Ejemplo de cómo integrar JS con WordPress y crear un widget que muestre entradas.
 * Author: Elio Rivero
 * Version: 0.0.7
 * Author URI: https://instagram.com/eliorivero
 * Text Domain: tallerjs
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

class TallerJS extends WP_Widget {
	static $pocos = 5;
	static $muchos = 10;

	function __construct() {
		parent::__construct(
			'tallerjs',
			esc_html__( 'Taller JS', 'tallerjs' ),
			array(
				'classname' => 'tallerjs',
				'description' => esc_html__( 'Ejemplo de cómo integrar JS con WordPress', 'tallerjs' ),
			)
		);

		add_action( 'plugins_loaded', array( $this, 'localization' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	function form( $instance ) {
		$title = isset( $instance['title' ] )
			? $instance['title']
			: '';
		$number = isset( $instance['number'] )
			? $instance['number']
			: self::$pocos;

		if ( ! in_array( $number, array( self::$pocos, self::$muchos ) ) ) {
			$number = self::$pocos;
		}

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Título:', 'tallerjs' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label><?php esc_html_e( 'Mostrar', 'tallerjs' ); ?></label>
		</p>
		<ul>
			<li>
				<label><input id="<?php echo $this->get_field_id( 'number' ); ?>-pocos"  name="<?php echo $this->get_field_name( 'number' ); ?>" type="radio" value="<?php echo self::$pocos ?>" <?php checked( self::$pocos, $number ); ?> /> <?php esc_html_e( 'pocos', 'tallerjs' ); ?></label>
			</li>
			<li>
				<label><input id="<?php echo $this->get_field_id( 'number' ); ?>-muchos" name="<?php echo $this->get_field_name( 'number' ); ?>" type="radio" value="<?php echo self::$muchos ?>" <?php checked( self::$muchos, $number ); ?> /> <?php esc_html_e( 'muchos', 'tallerjs' ); ?></label>
			</li>
		</ul>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = wp_kses( $new_instance['title'], array() );
		$instance['number'] = (int) $new_instance['number'];
		if ( !in_array( $instance['number'], array( self::$pocos, self::$muchos ) ) ) {
			$instance['number'] = self::$pocos;
		}

		return $instance;
	}

	function widget( $args, $instance ) {
		wp_enqueue_script( 'tallerjs' );

		$title = isset( $instance['title'] )
			? $args['before_title'] . apply_filters( 'widget_title', esc_html( $instance['title'] ) ) . $args['after_title']
			: '';

		echo $args['before_widget'] . $title . '<ul id="tallerjs"></ul>' . $args['after_widget'];
	}

	function localization() {
		load_plugin_textdomain( 'tallerjs', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}

	function register_assets() {
		$settings = $this->get_settings();

		//wp_register_script( 'lodash', 'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.core.min.js', array(), false, true );
		wp_register_script( 'tallerjs-moment', plugins_url( 'js/moment.min.js', __FILE__ ), array(), false, true );
		wp_register_script( 'tallerjs-lodash', plugins_url( 'js/lodash.core.min.js', __FILE__ ), array(), false, true );
		wp_register_script( 'tallerjs', plugins_url( 'js/script.js', __FILE__ ), array( 'jquery', 'tallerjs-lodash', 'tallerjs-moment' ), false, true );
		wp_localize_script( 'tallerjs', 'tallerJsData', wp_parse_args(
			$settings[ $this->number ],
			array(
				'number' => self::$pocos,
				'wpApiRoot' => esc_url_raw( rest_url() ),
				'dateFormat' => strtr( get_option( 'date_format' ), array(
					'F' => 'MMMM',
					'j' => 'D',
					'Y' => 'YYYY',
				) ),
			)
		) );
	}
}

function tallerjs_widgets_init() {
	register_widget( 'TallerJS' );
}
add_action( 'widgets_init', 'tallerjs_widgets_init' );
