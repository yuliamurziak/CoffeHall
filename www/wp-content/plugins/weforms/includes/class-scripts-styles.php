<?php

/**
 * The scripts class
 *
 * @since 1.1.0
 */
class WeForms_Scripts_Styles {

    /**
     * The constructor
     */
    function __construct() {

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'register_backend' ) );
        } else {
            add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend' ) );
        }
    }

    /**
     * Register frontend scripts and styles
     *
     * @return void
     */
    public function register_frontend() {
        $this->register_styles( $this->get_frontend_styles() );
        $this->register_scripts( $this->get_frontend_scripts() );

        $this->get_frontend_localized();
    }

    /**
     * Register frontend scripts and styles
     *
     * @return void
     */
    public function register_backend() {
        $this->register_styles( $this->get_admin_styles() );
        $this->register_scripts( $this->get_admin_scripts() );

        $this->get_frontend_localized();
    }

    /**
     * Enqueue all the scripts and styles for frontend
     *
     * @return void
     */
    public function enqueue_frontend() {
        $this->enqueue_scripts( $this->get_frontend_scripts() );
        $this->enqueue_styles( $this->get_frontend_styles() );
    }

    /**
     * Enqueue all the scripts and styles for backend
     *
     * @return void
     */
    public function enqueue_backend() {
        $this->enqueue_scripts( $this->get_admin_scripts() );
        $this->enqueue_styles( $this->get_admin_styles() );
    }

    /**
     * Get file prefix
     *
     * @return string
     */
    public function get_prefix() {
        $prefix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        return $prefix;
    }

    /**
     * Get all registered admin scripts
     *
     * @return array
     */
    public function get_admin_scripts() {
        $prefix = $this->get_prefix();

        $form_builder_js_deps = apply_filters( 'wpuf-form-builder-js-deps', array(
			'jquery',
			'jquery-ui-sortable',
            'jquery-ui-draggable',
            'jquery-ui-datepicker',
			'weforms-tiny-mce',
			'underscore',
        ) );

        $builder_scripts = apply_filters( 'weforms_builder_scripts', array(
            'weforms-tiny-mce' => array(
                'src'       => site_url( '/wp-includes/js/tinymce/tinymce.min.js' ),
                'deps'      => array(),
                'in_footer' => true
            ),
            'weforms-vendor' => array(
				'src'       => WEFORMS_ASSET_URI . '/js/vendor' . $prefix . '.js',
				'deps'      => $form_builder_js_deps,
				'in_footer' => true
			),
			'wpuf-form-builder-mixins' => array(
				'src'       => WEFORMS_ASSET_URI . '/wpuf/js/wpuf-form-builder-mixins.js',
				'deps'      => array('weforms-vendor'),
				'in_footer' => true
			),
			'wpuf-form-builder-mixins-form' => array(
				'src'       => WEFORMS_ASSET_URI . '/js/wpuf-form-builder-contact-forms' . $prefix . '.js',
				'deps'      => array('weforms-vendor'),
				'in_footer' => true
			),
			'wpuf-form-builder-components' => array(
				'src'       => WEFORMS_ASSET_URI . '/wpuf/js/wpuf-form-builder-components' . $prefix . '.js',
				'deps'      => array( 'wpuf-form-builder-mixins', 'wpuf-form-builder-mixins-form' ),
				'in_footer' => true
			),
        ) );

        $spa_scripts = array(
            'weforms-mixins' => array(
                'src'       => WEFORMS_ASSET_URI . '/js/spa-mixins' . $prefix . '.js',
                'deps'      => array( 'weforms-vendor', 'wp-util' ),
                'in_footer' => true
            ),
            'weforms-components' => array(
                'src'       => WEFORMS_ASSET_URI . '/js/form-builder-components' . $prefix . '.js',
                'deps'      => array( 'weforms-vendor', 'wp-util' ),
                'in_footer' => true
            ),
            'weforms-app' => array(
                'src'       => WEFORMS_ASSET_URI . '/js/spa-app' . $prefix . '.js',
                'deps'      => array( 'weforms-vendor', 'wp-util', 'wpuf-form-builder-components' ),
                'in_footer' => true
            ),
        );

        $scripts = array_merge( $builder_scripts, $spa_scripts );

        return apply_filters( 'weforms_admin_scripts', $scripts );
    }

    /**
     * Get admin styles
     *
     * @return array
     */
    public function get_admin_styles() {
        $frontend_styles = $this->get_frontend_styles();

        $backend_styles = array(
            'wpuf-font-awesome' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/font-awesome/css/font-awesome.min.css',
            ),
            'wpuf-sweetalert2' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/sweetalert2/dist/sweetalert2.css',
            ),
            'wpuf-selectize' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/selectize/css/selectize.default.css',
            ),
            'wpuf-toastr' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/toastr/toastr.min.css',
            ),
            'wpuf-tooltip' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/vendor/tooltip/tooltip.css',
            ),
            'wpuf-form-builder' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/css/wpuf-form-builder.css',
                'deps' => array(
					'wpuf-css',
					'wpuf-font-awesome',
					'wpuf-sweetalert2',
					'wpuf-selectize',
					'wpuf-toastr',
					'wpuf-tooltip'
                )
            ),
            'weforms-style' => array(
                'src'  => WEFORMS_ASSET_URI . '/css/admin.css',
            ),
            'weforms-tiny-mce-css' => array(
                'src'  => site_url( '/wp-includes/css/editor.css' ),
                'deps' => array( 'wp-color-picker' )
            )

        );

        $styles = array_merge( $frontend_styles, $backend_styles );

        return apply_filters( 'weforms_admin_styles', $styles );
    }

    /**
     * Get all registered frontend scripts
     *
     * @return array
     */
    public function get_frontend_scripts() {

        $prefix = $this->get_prefix();

        $scripts = array(
            'wpuf-form' => array(
                'src'       => WEFORMS_ASSET_URI . '/wpuf/js/frontend-form' . $prefix . '.js',
                'deps'      => array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-slider' ),
                'in_footer' => false
            ),
            'jquery-ui-timepicker' => array(
                'src'       => WEFORMS_ASSET_URI . '/wpuf/js/jquery-ui-timepicker-addon' . $prefix . '.js',
                'deps'      => array( 'jquery-ui-datepicker' ),
                'in_footer' => false
            ),
            'wpuf-upload' => array(
                'src'       => WEFORMS_ASSET_URI . '/wpuf/js/upload' . $prefix . '.js',
                'deps'      => array( 'jquery', 'plupload-handlers' ),
                'in_footer' => false
            )
        );

        return apply_filters( 'weforms_frontend_scripts', $scripts );
    }

    /**
     * Get all registered frontend styles
     *
     * @return array
     */
    public function get_frontend_styles() {
        $styles = array(
            'wpuf-css' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/css/frontend-forms.css',
            ),
            'jquery-ui' => array(
                'src'  => WEFORMS_ASSET_URI . '/wpuf/css/jquery-ui-1.9.1.custom.css',
            )
        );

        return apply_filters( 'weforms_frontend_styles', $styles );
    }

    /**
     * Frontend localized scripts
     *
     * @return void
     */
    public function get_frontend_localized() {
        wp_localize_script( 'wpuf-form', 'wpuf_frontend', array(
			'ajaxurl'       => admin_url( 'admin-ajax.php' ),
			'error_message' => __( 'Please fix the errors to proceed', 'weforms' ),
			'nonce'         => wp_create_nonce( 'wpuf_nonce' ),
			'word_limit'    => __( 'Word limit reached', 'weforms' )
        ) );

        wp_localize_script( 'wpuf-form', 'error_str_obj', array(
			'required'   => __( 'is required', 'weforms' ),
			'mismatch'   => __( 'does not match', 'weforms' ),
			'validation' => __( 'is not valid', 'weforms' ),
			'duplicate'  => __( 'requires a unique entry and this value has already been used', 'weforms' ),
        ) );

        wp_localize_script( 'wpuf-upload', 'wpuf_frontend_upload', array(
			'confirmMsg' => __( 'Are you sure?', 'weforms' ),
			'nonce'      => wp_create_nonce( 'wpuf_nonce' ),
			'ajaxurl'    => admin_url( 'admin-ajax.php' ),
			'plupload'   => array(
				'url'              => admin_url( 'admin-ajax.php' ) . '?nonce=' . wp_create_nonce( 'wpuf-upload-nonce' ),
				'flash_swf_url'    => includes_url( 'js/plupload/plupload.flash.swf' ),
				'filters'          => array(
					array(
						'title' => __( 'Allowed Files', 'weforms' ),
						'extensions' => '*'
					)
				),
				'multipart'        => true,
				'urlstream_upload' => true,
				'warning'          => __( 'Maximum number of files reached!', 'weforms' ),
				'size_error'       => __( 'The file you have uploaded exceeds the file size limit. Please try again.', 'weforms' ),
				'type_error'       => __( 'You have uploaded an incorrect file type. Please try again.', 'weforms' )
			)
        ) );
    }

    /**
     * Register scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    public function register_scripts( $scripts ) {
        foreach ( $scripts as $handle => $script ) {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;

            wp_register_script( $handle, $script['src'], $deps, WEFORMS_VERSION, $in_footer );
        }
    }

    /**
     * Register styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function register_styles( $styles ) {
        foreach ( $styles as $handle => $style ) {
            $deps = isset( $style['deps'] ) ? $style['deps'] : false;

            wp_register_style( $handle, $style['src'], $deps, WEFORMS_VERSION );
        }
    }

    /**
     * Enqueue the scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    public function enqueue_scripts( $scripts ) {
        foreach ( $scripts as $handle => $script ) {
            wp_enqueue_script( $handle );
        }
    }

    /**
     * Enqueue styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function enqueue_styles( $styles ) {
        foreach ( $styles as $handle => $script ) {
            wp_enqueue_style( $handle );
        }
    }
}
