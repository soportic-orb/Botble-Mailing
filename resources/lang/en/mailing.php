<?php

return [
    'name' => 'Mailing',

    'campaigns' => [
        'name' => 'Campaigns',
        'create' => 'New campaign',
        'edit' => 'Edit campaign',
        'type' => 'Type',
        'stats' => 'Statistics',
        'send_now' => 'Send now',
        'send_confirm' => 'The campaign will be sent to all subscribed contacts. Continue?',
        'cancel_sending' => 'Cancel sending',
        'sending_started' => 'Sending has started. Emails will go out in batches according to the sending rules.',
        'sending_cancelled' => 'Sending has been cancelled.',
        'already_sending' => 'This campaign is already being sent or has been sent.',
        'cannot_cancel' => 'Only scheduled or in-progress campaigns can be cancelled.',
        'no_contacts' => 'There are no subscribed contacts to send to.',
        'not_editable' => 'This campaign can no longer be edited because sending has already started.',
        'form' => [
            'subject' => 'Email subject',
            'subject_placeholder' => 'Subject of the outgoing email',
            'content' => 'Email content',
            'scheduled_at' => 'Schedule sending at',
            'scheduled_at_helper' => 'Optional. Format: YYYY-MM-DD HH:MM. Leave empty to keep the campaign as a draft and send it manually with the "Send now" button.',
        ],
    ],

    'contacts' => [
        'name' => 'Contacts',
        'create' => 'New contact',
        'edit' => 'Edit contact',
    ],

    'contact_statuses' => [
        'subscribed' => 'Subscribed',
        'unsubscribed' => 'Unsubscribed',
    ],

    'statuses' => [
        'draft' => 'Draft',
        'scheduled' => 'Scheduled',
        'sending' => 'Sending',
        'sent' => 'Sent',
        'cancelled' => 'Cancelled',
    ],

    'types' => [
        'manual' => 'Manual',
        'post_published' => 'New post',
        'monthly_digest' => 'Monthly digest',
    ],

    'log_statuses' => [
        'pending' => 'Pending',
        'sent' => 'Delivered',
        'failed' => 'Bounced',
    ],

    'auto_post' => [
        'campaign_name' => 'New post: :title',
    ],

    'digest' => [
        'campaign_name' => 'Monthly digest — :date',
        'subject' => 'Latest posts — :month',
    ],

    'settings' => [
        'menu' => 'Settings',
        'title' => 'Mailing settings',
        'description' => 'Configure automatic sending, monthly digest, anti-spam sending rules and OTA updates.',
        'auto_post_enabled' => 'Send email when a post is published',
        'auto_post_enabled_helper' => 'When enabled, every time a blog post is published an email is sent automatically to all subscribed contacts with the post title as subject and its content as body.',
        'monthly_enabled' => 'Send monthly digest',
        'monthly_enabled_helper' => 'When enabled, a monthly email is sent with the list of posts published since the last monthly mailing (title and the first lines of each post).',
        'monthly_day' => 'Day of the month for the digest',
        'monthly_day_helper' => 'Day of the month (1-31) on which the monthly digest is sent. For shorter months it is sent on the last day.',
        'monthly_time' => 'Digest sending time',
        'monthly_time_helper' => 'Time of day (HH:MM, 24h) after which the digest is sent on the configured day.',
        'batch_size' => 'Emails per batch',
        'batch_size_helper' => 'Anti-spam sending rule: maximum number of emails sent per batch. Default: 50.',
        'batch_interval' => 'Minutes between batches',
        'batch_interval_helper' => 'Anti-spam sending rule: minimum waiting time (in minutes) between two consecutive batches. Default: 5.',
        'github_repository' => 'GitHub repository (OTA updates)',
        'github_repository_helper' => 'Repository in owner/name format used to check for and download new versions of the plugin.',
        'github_token' => 'GitHub token (optional)',
        'github_token_helper' => 'Only needed for private repositories or to increase the GitHub API rate limit.',
    ],

    'update' => [
        'menu' => 'Update',
        'title' => 'Plugin update (OTA)',
        'current_version' => 'Installed version',
        'latest_version' => 'Latest version available on GitHub',
        'up_to_date' => 'The plugin is up to date.',
        'update_available' => 'Version :version is available. You can update automatically.',
        'update_now' => 'Update now',
        'update_confirm' => 'The plugin will be downloaded from GitHub and updated. It is recommended to make a backup first. Continue?',
        'changelog' => 'Release notes',
        'check_error' => 'Could not check the latest version on GitHub.',
        'no_release' => 'No release or tag was found in the configured GitHub repository.',
        'already_latest' => 'You already have the latest version installed.',
        'download_failed' => 'The download from GitHub failed (HTTP :status).',
        'zip_failed' => 'The downloaded package could not be opened.',
        'invalid_package' => 'The downloaded package is not a valid plugin (plugin.json is missing).',
        'updated_success' => 'Plugin successfully updated to version :version.',
        'source' => 'Update source',
    ],

    'stats' => [
        'title' => 'Statistics: :name',
        'total' => 'Recipients',
        'processed' => 'Processed',
        'delivered' => 'Delivered',
        'bounced' => 'Bounced',
        'opened' => 'Opened',
        'pending' => 'Pending',
        'progress' => 'Sending progress',
        'recent_logs' => 'Latest sendings',
        'log_email' => 'Email',
        'log_status' => 'Status',
        'log_sent_at' => 'Sent at',
        'log_opened_at' => 'Opened at',
        'log_error' => 'Error',
        'no_logs' => 'No emails have been queued yet.',
        'scheduled_for' => 'Scheduled for :date',
        'completed_at' => 'Completed at :date',
        'back_to_list' => 'Back to campaigns',
    ],

    'unsubscribe' => [
        'title' => 'Unsubscribe',
        'success' => 'Your subscription has been cancelled. You will no longer receive our emails.',
        'invalid' => 'The link is not valid or the subscription no longer exists.',
        'back_home' => 'Go to website',
    ],

    'email' => [
        'name' => 'Mailing',
        'description' => 'Emails sent by the Mailing plugin (campaigns, new posts and monthly digest)',
        'templates' => [
            'campaign' => [
                'title' => 'Manual campaign',
                'description' => 'Email sent to all contacts when the administrator sends a manual campaign',
            ],
            'post_published' => [
                'title' => 'New post published',
                'description' => 'Email sent automatically to all contacts when a blog post is published',
            ],
            'monthly_digest' => [
                'title' => 'Monthly digest',
                'description' => 'Monthly email with the list of posts published since the last mailing',
            ],
        ],
        'variables' => [
            'mailing_subject' => 'Email subject',
            'mailing_content' => 'Email main content (HTML)',
            'unsubscribe_url' => 'Unsubscribe URL for the contact',
            'tracking_pixel' => 'Open-tracking pixel',
        ],
    ],
];
