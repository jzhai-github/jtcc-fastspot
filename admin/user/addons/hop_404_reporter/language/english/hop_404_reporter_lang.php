<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$lang = [
	//Required for MODULES page
	'hop_404_reporter_module_name'			=> 'Hop 404 Reporter',
	'hop_404_reporter_module_description'	=> 'Manage all 404 errors happening on your website',

	// Setup
	'404_template' => '404 Template',
	'all_good' => 'Everything looks good!',
	'edit_404' => 'Hop 404 Reporter has modified your existing 404 page and added the necessary tag.',
	'create_404' => 'Hop 404 Reporter has created a default 404 page in the default template group for your site.',
	'create_404_duplicate' => 'Hop 404 Reporter found a template named “not_found” in the default template group, but that template is not set as the default site 404 page.<br>
		Please adjust the site template settings to make “not_found” the default 404 page. Then add this code to the top of the template:<br>
		{exp:hop_404_reporter:process_url}',
	'create_404_missing_group' => 'Hop 404 Reporter did not find a default template group. Please set one up and try the setup wizard again.',
	'create_404_missing' => 'Hop 404 Reporter did not find a properly configured 404 page. Please set one up and try the setup wizard again.',
	'create_notification' => 'Hop 404 Reporter has created a basic email notification using the site\'s webmaster email address.',
	'default_page_not_found' => '{exp:hop_404_reporter:process_url}Sorry! Page not found.',
	'setup_wizard_steps' => '<p>This wizard will check to see if everything is set up properly for Hop 404 Reporter to work on your site, and do some of the work for you as well.</p>
		<ol style="margin-top: 10px; padding-left: 20px;">
			<li>It makes sure you have a 404 page template created and set up.</li>
			<li>It make sure the 404 page template has the proper tag added to it.</li>
			<li>It creates an initial email notification that emails the site webmaster.</li>
		</ol>
		<a class="btn" href="%s" style="margin-top: 10px;">Run the Wizard</a>
	',
	'setup_wizard_notice' => 'Hop 404 Reporter can help you setup the necessary tag in the default 404 page.',

	//NAV Headers
	'reports'			=> 'Reports',
	'notifications'		=> 'Notifications',
	'settings'			=> 'Settings',
	'setup_wizard'		=> 'Setup Wizard',
	'support_and_help'	=> 'Support and Help',
	'license'			=> 'License',
	'license_valid'		=> 'Valid',
	'license_invalid'	=> 'Unlicensed',

	//Index view
	'404_urls'					=> '404 URLs',
	'404_urls_description'		=> '<p>This list contains all 404 URLs that occured on your website.</p>',
	'url'						=> 'URL',
	'referrer_url'				=> 'Referrer URL',
	'count'						=> 'Count',
	'last_occurred'				=> 'Date of Last Occurrence',
	'referrer_not_tracked'		=> 'Referrer not tracked',
	'referrer_not_specified'	=> 'Referrer not specified',
	'--with_selected--'			=> '-- with selected --',
	'delete_selected'			=> 'Delete selected',
	'url_deleted_message'		=> '%d URL(s) have been deleted',
	'search_urls'				=> 'Search URLs',
	'url_deleted_success'		=> 'URL(s) deleted',
	// Filters
	'filter_referrer_url'			=> 'Referrer URL',
	'filter_no_referrer_url' 		=> 'Not specified',
	'filter_referrer_url_not_saved' => 'Not tracked',
	'filter_referrer_saved' 		=> 'Specified',
	'custom_date'					=> 'custom date',
	'filter_urls'					=> 'Filter URLs',
	'keywords'						=> 'Keywords',
	'filter_date_range'				=> 'Date range',
	'filter_last_day'				=> 'Last day',
	'filter_last_week'				=> 'Last week',
	'filter_last_month'				=> 'Last month',
	'filter_last_3months'			=> 'Last 3 months',
	'filter_last_6months'			=> 'Last 6 months',
	'filter_last_year'				=> 'Last year',

	//Email list view
	'email_notifications'			=> 'Email Notifications',
	'email_notifications_description'	=> '<p>Hop 404 Reporter can send notifications to an email address when a 404 error occurs.</p>',
	'email_address'					=> 'Email',
	'url_to_match'					=> 'URL to Match',
	'referrer'						=> 'Referrer',
	'frequency'						=> 'Frequency',
	'email_deleted_message'			=> '%d email notification(s) have been deleted',
	'email_reset_message' 			=> '%d email notification(s) have been reset',
	'email_reset_selected'			=> 'Reset selected',
	'create_new_one'				=> 'Create a new one',
	'search_emails_notif'			=> 'Search email notifications',
	'email_deleted_success'			=> 'Email(s) deleted',
	'email_reseted_success'			=> 'Email(s) reseted',
	// Filters
	'filter_email_notifications'	=> 'Filter Email Notifications',
	'filter_frequency'				=> 'Filter by Frequency',

	//Add email view
	'add_new_notification' 					=> 'Add New Notification',
	'email_address_desc'					=> 'The email address to send the notification to',
	'email_notification_url_filter_label'	=> '404 URL Filter (Optional)',
	'email_notification_url_filter_desc'	=> 'If filled in, a notification will be sent only if the 404 URL matches this value. It is a regular expression. See the documentation for examples. If left empty, all URLs will send a notification at least once.',
	'email_notification_referrer_label'		=> 'Require Referrer?',
	'email_notification_referrer_desc' 		=> 'By default, a referrer is required for a notification to be triggered. Notifications without referrer information are harder to take action on. To use this feature, make sure to turn on referrer tracking in the Hop 404 Reporter Settings page.',
	'email_notification_frequency_label'	=> 'Frequency',
	'email_notification_frequency_desc' 	=> 'Select if you want a notification sent once for each unique URL, or always.',
	'email_notification_submit'				=> 'Save',
	'email_notification_frequency_once' 	=> 'Once',
	'email_notification_frequency_always' 	=> 'Always',
	'email_notificaiton_frequency_invalid'	=> 'Invalid interval parameter',
	'emaill_notification_add_success'		=> 'Email notification added',
	'emaill_notification_add_success_desc'	=> 'The new email notification has been saved.',
	'required'								=> 'Required',
	'not_required'							=> 'Not Required',

	//Settings view
	'settings'										=> 'Settings',
	'settings_save'									=> 'Save',
	'settings_save_working'							=> 'Saving...',
	'set_enabled'									=> 'Is Hop 404 Reporter on?',
	'set_enabled_desc'								=> 'If Hop 404 Reporter is off, no URLs will be recorded and no emails will be sent.',
	'set_send_email_notifications'					=> 'Do we send email notifications?',
	'set_send_email_notifications_desc'				=> 'If not, 404 URLs will still be recorded but no emails will be sent.',
	'set_referrer_tracking'							=> 'Is referrer tracking on ?',
	'set_referrer_tracking_desc'					=> 'If enabled, referrer tracking will save the referrer URL (if provided) for each 404 URL, but it makes the table larger and the count values less useful.',
	'set_email_address_sender'						=> 'Sender Email Address',
	'set_email_address_sender_desc'					=> 'This will be the "from" for email notifications. If left empty, the site\'s default email address will be used.',
	'set_email_notification_subject'				=> 'Notification Email Subject',
	'set_email_notification_subject_desc'			=> 'Subject of the email notifications sent when a 404 occurs.',
	'set_404_email_template'						=> 'Notification Email Body',
	'set_404_email_template_desc'					=> 'Tags available: {site_url}, {404_url}, {referrer_url}, {404_date}, {404_time}, {ip_address}',

	//Email Defaults
	'email_notification_subject'	=> 'Notification of a 404 not found URL',
	'email_template'				=> '
You are receiving this email from {site_url}.

On {404_date} at {404_time}, a 404 error occurred at:
{404_url}
from
{referrer_url}

The IP address of the visitor was
{ip_address}

Sincerely,
The Hop 404 Reporter Duck
',
	//END
];