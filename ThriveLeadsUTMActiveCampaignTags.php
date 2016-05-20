<?php
/*
Plugin Name: ThriveLeads UTM parameters Added as Tags to Active Campaign
Plugin URI: http://spectrumwebsolution.com
Description:Adds Google UTM parameters as tags to Active Campaign API calls from ThriveLeads plugin forms when submitted
Author: Ryan Moultrup
Version: 0.1.0
Author URI: http://spectrumwebsolution.com
*/

/**
 * Makes sure that the Thrive Leads plugin is active
*/
function check_thrive_leads_plugin_is_active() {

	if(function_exists('tve_leads_init')) {

		/**
		 * Hooks into a ThriveLeads action hook right before data is sent to Active Campaign API
		 * and adds Google UTM parameters as tags to new subscriber
		 *
		 * @param  array $data ThriveLeads form submit data
		 * @return array $data 
		*/
		function tve_leads_utm_api_form_submit_filter($data) {
			
			// Create an array of keys so that only UTM $_GET parameters are added as tags if there are others set
			$utmArray = array('utm_campaign', 'utm_source', 'utm_content', 'utm_medium', 'utm_term');

			foreach($utmArray as $key) {
				if(array_key_exists($key, $data['thrive_leads']['get_data'])) {
					$tags .= ", " . $data['thrive_leads']['get_data'][$key];
				}
			}

			// Check if any UTM parameters have been set. If they have not the $tags string will be empty
			if(!empty($tags)) {
				// Check if there are tags already set from the ThriveLeads plugin API connection
				if(strlen($data['activecampaign_tags']) > 0) {
					// concat strings with comma
					$data['activecampaign_tags'] .= $tags;
				} else {
					// Remove comma from beginning of string and add new tags string to array
					$tags = substr($tags, 1);
					$data['activecampaign_tags'] = $tags;
				}
			}

			return $data;

		}

		add_action('tcb_api_subscribe_data', 'tve_leads_utm_api_form_submit_filter');
	}	

}

add_action('plugins_loaded', 'check_thrive_leads_plugin_is_active');
