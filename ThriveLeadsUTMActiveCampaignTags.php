<?php
/*
Plugin Name: ThriveLeads: UTM parameters Added as Tags to Active Campaign
Plugin URI: http://spectrumwebsolution.com
Description:Adds Google UTM parameters as tags to Active Campaign API calls from ThriveLeads plugin forms when submitted
Author: Ryan Moultrup
Version: 1.0.0
Author URI: http://spectrumwebsolution.com
*/


if(!function_exists('check_thrive_leads_plugin_is_active')) {
	/**
	 * Makes sure that the Thrive Leads plugin is active
	*/
	function check_thrive_leads_plugin_is_active() {

		if(function_exists('tve_leads_init')) {

			/**
			 * Hooks into a ThriveLeads action right before data is sent to Active Campaign API
			 * and adds Google UTM parameters as tags to subscriber
			 *
			 * @param  array $data ThriveLeads form submit data that contains the UTM parameters
			 * @return array $data ThriveLeads form submit data with UTM params added as Active Cmapaign tags
			*/
			function tve_leads_utm_api_form_submit_filter($data) {
				
				// Check if the Active Camapign tags key exists and that $_GET parameter(s) exist before we do anything
				if(array_key_exists('activecampaign_tags', $data) && array_key_exists('get_data', $data['thrive_leads'])) {

					// Check that any $_GET params are actually UTM params 
					if(count(preg_grep('/^utm[\_]/', array_keys($data['thrive_leads']['get_data']))) > 0) {

						// Get user options to create an array of keys so that only set UTM parameters are added as tags if there are others
						$utmArray = get_option('tvac_utm_setting');

						foreach($utmArray as $key => $val) {
							if(array_key_exists($key, $data['thrive_leads']['get_data'])) {
								$tags .= ", " . $data['thrive_leads']['get_data'][$key];
							}
						}
						
						// Check if there are tags already set from the ThriveLeads plugin API connection
						if(strlen($data['activecampaign_tags']) > 0) {
							// Concat strings with commas
							$data['activecampaign_tags'] .= $tags;
						} else {
							// Remove comma from beginning of string and add new tags string to array
							$tags = substr($tags, 1);
							$data['activecampaign_tags'] = $tags;
						}

					}	

				}	

				return $data;
				die();

			}

			add_filter('tcb_api_subscribe_data', 'tve_leads_utm_api_form_submit_filter');

			/**
			 * Add options page to Settings menu in admin
			*/
			function thrive_active_campaign_utm_admin_menu() {
				add_options_page('Thrive Active Campaign UTM Settings', 'Thrive UTM', 'manage_options', 'thrive-active-campaign-utm', 'thrv_active_campaign_utm_admin_page');
			}

			add_action('admin_menu', 'thrive_active_campaign_utm_admin_menu');

			/**
			 * Callback function for admin options page HTML output
			*/
			function thrv_active_campaign_utm_admin_page() {
				if ( !current_user_can( 'manage_options' ) )  {
					wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
				}
				
				include plugin_dir_path( __FILE__ ) . "/admin/utm_thrive_to_active_campaign.php";
				die();
			}
			
			/**
			 * Register options page settings to use Settings API
			*/
			function tvac_utm_admin_init() {
			    register_setting( 'tvac_utm_settings_group', 'tvac_utm_setting' );
			    add_settings_section( 'section-one', 'Google UTM Parameters', 'section_one_callback', 'thrive-active-campaign-utm' );
			    add_settings_field( 'field-two', 'utm_source', 'tvac_field_utm_source_callback', 'thrive-active-campaign-utm', 'section-one' );
			    add_settings_field( 'field-five', 'utm_medium', 'tvac_field_utm_medium_callback', 'thrive-active-campaign-utm', 'section-one' );
			    add_settings_field( 'field-three', 'utm_term', 'tvac_field_utm_term_callback', 'thrive-active-campaign-utm', 'section-one' );
			    add_settings_field( 'field-four', 'utm_content', 'tvac_field_utm_content_callback', 'thrive-active-campaign-utm', 'section-one' );
			    add_settings_field( 'field-one', 'utm_campaign', 'tvac_field_utm_campaign_callback', 'thrive-active-campaign-utm', 'section-one' );
			}

			add_action( 'admin_init', 'tvac_utm_admin_init' );

			/**
			 * Callback function for admin screeen section
			*/
			function section_one_callback() {
			    echo 'Check which UTM parameters you would like saved as tags if they are present in the URL.';
			}

			/**
			 * Callback function for utm_campaign checkbox field 
			*/
			function tvac_field_utm_campaign_callback() {
			   tvac_checkbox('utm_campaign');
			}

			/**
			 * Callback function for utm_source checkbox field
			*/
			function tvac_field_utm_source_callback() {
			    tvac_checkbox('utm_source');
			}

			/**
			 * Callback function for utm_term checkbox field
			*/
			function tvac_field_utm_term_callback() {
			    tvac_checkbox('utm_term');
			}

			/**
			 * Callback function for utm_content checkbox field
			*/
			function tvac_field_utm_content_callback() {
			    tvac_checkbox('utm_content');
			}

			/**
			 * Callback function for utm_medium checkbox field
			*/
			function tvac_field_utm_medium_callback() {
			    tvac_checkbox('utm_medium');
			}

			/**
			 * Build the HTML for the checkboxes through Settings API
			 *
			 * @param  string $utm The name of the utm paremeter  
			*/
			function tvac_checkbox($utm) {
				$setting = (array) get_option('tvac_utm_setting');
				echo "<input type='checkbox' name='tvac_utm_setting[$utm]' value='1'" . checked( 1, $setting[$utm], false) . " />";
			}

		}	

	}

	add_action('plugins_loaded', 'check_thrive_leads_plugin_is_active');
}