<?php

/**
 * Plugin Name: Teamtailor Block
 * Plugin URI: https://github.com/kendafi/teamtailor-block
 * Update URI: teamtailor-block-by-kenda
 * Description: A plugin that enables a custom Gutenberg block that adds a Teamtailor widget displaying job listings in any page or post.
 * Version: 2026.3.26
 * Requires at least: 7.0
 * Author: Kenda
 * Author URI: https://kenda.fi/
 * Text Domain: teamtailor-block
 * Domain Path: /languages
*/

/**
 * Register Teamtailor block
 *
 * This function registers the Teamtailor block with WordPress, defining its attributes, settings, and render callback.
 * It also provides a description that includes important information about the block's functionality and dependencies
 * on Teamtailor account settings. The block is categorized under 'widgets' and supports various features such as
 * auto-registration, anchors, custom class names, and more.
 */

function teamtailorblock_register_teamtailor_block() {

	$information = __( 'Some options depends on your Teamtailor account settings.', 'teamtailor-block' );
	$information .= ' ¹= ' . __( 'These options are excluded if you limit jobs within a specific department and/or location in Teamtailor.', 'teamtailor-block' );
	$information .= ' ²= ' . __( 'This option will only be available if you are using Teamtailor multilingual feature.', 'teamtailor-block' );
	$information .= ' ³= ' . __( 'When pagination is enabled, the number of jobs setting determines how many are displayed per page.', 'teamtailor-block' );

	$attributes = [
		'api-key' => [
			'label' => __( 'Teamtailor API key (required)', 'teamtailor-block' ),
			'type' => 'string',
			'default' => '',
		],
		'job-feed' => [
			'label' => __( 'Job feed', 'teamtailor-block' ),
			'type' => 'string',
			'enum' => [ 'internal', 'public', 'internal, public' ],
			'default' => 'public',
		],
		'job-amount' => [
			'label' => __( 'Number of jobs', 'teamtailor-block' ),
			'type' => 'number', // range does not work with PHP-only...
			'enum' => range( 1, 30 ), // ...so we use a dropdown
			'default' => 12,
		],
		'department-select' => [
			'label' => __( 'Department select ¹', 'teamtailor-block' ),
			'type' => 'boolean',
			'default' => true,
		],
		'role-select' => [
			'label' => __( 'Role select', 'teamtailor-block' ),
			'type' => 'boolean',
			'default' => true,
		],
		'region-select' => [
			'label' => __( 'Region select', 'teamtailor-block' ),
			'type' => 'boolean',
			'default' => true,
		],
		'location-select' => [
			'label' => __( 'Location select ¹', 'teamtailor-block' ),
			'type' => 'boolean',
			'default' => true,
		],
		'language-select' => [
			'label' => __( 'Language select ²', 'teamtailor-block' ),
			'type' => 'boolean',
			'default' => true,
		],
		'remote-select' => [
			'label' => __( 'Remote status select', 'teamtailor-block' ),
			'type' => 'boolean',
			'default' => true,
		],
		'popup' => [
			'label' => __( 'Open links in new tab', 'teamtailor-block' ),
			'type' => 'boolean',
			'default' => true,
		],
		'pagination' => [
			'label' => __( 'Pagination ³', 'teamtailor-block' ),
			'type' => 'boolean',
			'default' => true,
		],
	];

	register_block_type(
		'kenda/teamtailor',
		[
			'title' => __( 'Teamtailor', 'teamtailor-block' ),
			'icon' => 'superhero',
			'category' => 'widgets',
			'description' => $information,
			'supports' => [
				'autoRegister' => true,
				'anchor' => true,
				'className' => true,
				'customCSS' => true,
				'html' => false,
				'multiple' => true,
				'lock' => true,
			],
			'attributes' => $attributes,
			'view_script' => 'teamtailor-widget',
			'render_callback' => 'teamtailorblock_render',
		]
	);

}

add_action( 'init', 'teamtailorblock_register_teamtailor_block' );

/**
 * Register Teamtailor widget script for frontend
 *
 * This registers the Teamtailor widget script, but does not enqueue it. The script will be enqueued conditionally
 * in the frontend and editor when the block is present, optimizing performance and preventing unnecessary script loading.
 */

function teamtailorblock_register_assets() {

	wp_register_script(
		'teamtailor-widget',
		'https://scripts.teamtailor-cdn.com/widgets/eu-pink/jobs.js',
		[],
		null,
		true
	);

}

add_action( 'init', 'teamtailorblock_register_assets' );

/**
 * Teamtailor widget render callback
 *
 * This function generates the HTML output for the Teamtailor widget based on the block attributes.
 * It checks for the presence of the required API key and conditionally renders the widget or
 * displays a warning message if the API key is missing. The function also ensures that the widget
 * is rendered with the appropriate data attributes based on the block settings.
 */

function teamtailorblock_render( $attributes ) {

	if ( strlen( esc_attr( $attributes['api-key'] ) ) < 1 ) {

		// We have no API key.

		if ( current_user_can( 'edit_posts' ) ) {

			// Display a warning message in both the editor and frontend if the API key is missing,
			// but only for users who can edit posts (e.g., admins and editors).
			return '<p><strong>' . __( 'Teamtailor API key is missing.', 'teamtailor-block' ) . '</strong></p>';

		}
		else {

			// Display a comment in the frontend source code for non-logged-in users or those without edit permissions.
			return '<!-- ' . __( 'Teamtailor API key is missing.', 'teamtailor-block' ) . ' -->';

		}

	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {

		// Display placeholder in content editor.

		$eol = "\r\n";
		$enabled = __( 'enabled', 'teamtailor-block' );

		return '<div class="teamtailor-placeholder" style="border: 1px solid #ccc; padding: 0 0 0 20px; background: #f9f9f9; font-size: 75%;">
			<pre>' . __( 'Teamtailor Jobs Widget placeholder', 'teamtailor-block' ) . $eol
			. __( 'We avoid loading external scripts here in admin.', 'teamtailor-block' ) . $eol
			. __( 'Job listings will appear on the frontend.', 'teamtailor-block' ) . $eol
			. __( 'Please save and preview this page.', 'teamtailor-block' ) . $eol . $eol
			. __( 'Job feed', 'teamtailor-block' ) . ': ' . esc_html( $attributes['job-feed'] ) . $eol
			. __( 'Number of jobs', 'teamtailor-block' ) . ': ' . (int) $attributes['job-amount'] . $eol
			. __( 'Department select', 'teamtailor-block' ) . ': ' . ( $attributes['department-select'] ? $enabled : '-' ) . $eol
			. __( 'Role select', 'teamtailor-block' ) . ': ' . ( $attributes['role-select'] ? $enabled : '-' ) . $eol
			. __( 'Region select', 'teamtailor-block' ) . ': ' . ( $attributes['region-select'] ? $enabled : '-' ) . $eol
			. __( 'Location select', 'teamtailor-block' ) . ': ' . ( $attributes['location-select'] ? $enabled : '-' ) . $eol
			. __( 'Language select', 'teamtailor-block' ) . ': ' . ( $attributes['language-select'] ? $enabled : '-' ) . $eol
			. __( 'Remote status select', 'teamtailor-block' ) . ': ' . ( $attributes['remote-select'] ? $enabled : '-' ) . $eol
			. __( 'Open links in new tab', 'teamtailor-block' ) . ': ' . ( $attributes['popup'] ? $enabled : '-' ) . $eol
			. __( 'Pagination', 'teamtailor-block' ) . ': ' . ( $attributes['pagination'] ? $enabled : '-' ) . '</pre>
		</div>';

	}

	// Display job listing in frontend.

	$wrapper_attributes = get_block_wrapper_attributes();

	return sprintf(
	'<div ' . $wrapper_attributes . '>
		<div class="teamtailor-jobs-widget"
		data-teamtailor-api-key="%s"
		data-teamtailor-feed="%s"
		data-teamtailor-limit="%d"
		%s
		%s
		%s
		%s
		%s
		%s
		%s
		%s
		></div>
	</div>',
	esc_html( $attributes['api-key'] ),
	esc_html( $attributes['job-feed'] ),
	(int) $attributes['job-amount'],
	$attributes['department-select'] ? 'data-teamtailor-department-select="true"' : '',
	$attributes['role-select'] ? 'data-teamtailor-role-select="true"' : '',
	$attributes['region-select'] ? 'data-teamtailor-region-select="true"' : '',
	$attributes['location-select'] ? 'data-teamtailor-location-select="true"' : '',
	$attributes['language-select'] ? 'data-teamtailor-language-select="true"' : '',
	$attributes['remote-select'] ? 'data-teamtailor-remote-select="true"' : '',
	$attributes['popup'] ? 'data-teamtailor-popup="true"' : '',
	$attributes['pagination'] ? 'data-teamtailor-pagination="true"' : ''
	);

}
