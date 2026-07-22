<?php

return [
    'name' => 'plugins/mailing::mailing.email.name',
    'description' => 'plugins/mailing::mailing.email.description',
    'templates' => [
        'campaign' => [
            'title' => 'plugins/mailing::mailing.email.templates.campaign.title',
            'description' => 'plugins/mailing::mailing.email.templates.campaign.description',
            'subject' => '{{ mailing_subject }}',
            'can_off' => false,
            'variables' => [
                'mailing_subject' => 'plugins/mailing::mailing.email.variables.mailing_subject',
                'mailing_content' => 'plugins/mailing::mailing.email.variables.mailing_content',
                'unsubscribe_url' => 'plugins/mailing::mailing.email.variables.unsubscribe_url',
                'tracking_pixel' => 'plugins/mailing::mailing.email.variables.tracking_pixel',
            ],
        ],
        'post-published' => [
            'title' => 'plugins/mailing::mailing.email.templates.post_published.title',
            'description' => 'plugins/mailing::mailing.email.templates.post_published.description',
            'subject' => '{{ mailing_subject }}',
            'can_off' => false,
            'variables' => [
                'mailing_subject' => 'plugins/mailing::mailing.email.variables.mailing_subject',
                'mailing_content' => 'plugins/mailing::mailing.email.variables.mailing_content',
                'unsubscribe_url' => 'plugins/mailing::mailing.email.variables.unsubscribe_url',
                'tracking_pixel' => 'plugins/mailing::mailing.email.variables.tracking_pixel',
            ],
        ],
        'monthly-digest' => [
            'title' => 'plugins/mailing::mailing.email.templates.monthly_digest.title',
            'description' => 'plugins/mailing::mailing.email.templates.monthly_digest.description',
            'subject' => '{{ mailing_subject }}',
            'can_off' => false,
            'variables' => [
                'mailing_subject' => 'plugins/mailing::mailing.email.variables.mailing_subject',
                'mailing_content' => 'plugins/mailing::mailing.email.variables.mailing_content',
                'unsubscribe_url' => 'plugins/mailing::mailing.email.variables.unsubscribe_url',
                'tracking_pixel' => 'plugins/mailing::mailing.email.variables.tracking_pixel',
            ],
        ],
    ],
];
