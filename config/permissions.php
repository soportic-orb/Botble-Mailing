<?php

return [
    [
        'name' => 'Mailing',
        'flag' => 'mailing.index',
    ],
    [
        'name' => 'Campaigns',
        'flag' => 'mailing.campaigns.index',
        'parent_flag' => 'mailing.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'mailing.campaigns.create',
        'parent_flag' => 'mailing.campaigns.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'mailing.campaigns.edit',
        'parent_flag' => 'mailing.campaigns.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'mailing.campaigns.destroy',
        'parent_flag' => 'mailing.campaigns.index',
    ],
    [
        'name' => 'Statistics',
        'flag' => 'mailing.campaigns.stats',
        'parent_flag' => 'mailing.campaigns.index',
    ],
    [
        'name' => 'Send',
        'flag' => 'mailing.campaigns.send',
        'parent_flag' => 'mailing.campaigns.index',
    ],
    [
        'name' => 'Cancel sending',
        'flag' => 'mailing.campaigns.cancel',
        'parent_flag' => 'mailing.campaigns.index',
    ],
    [
        'name' => 'Contacts',
        'flag' => 'mailing.contacts.index',
        'parent_flag' => 'mailing.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'mailing.contacts.create',
        'parent_flag' => 'mailing.contacts.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'mailing.contacts.edit',
        'parent_flag' => 'mailing.contacts.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'mailing.contacts.destroy',
        'parent_flag' => 'mailing.contacts.index',
    ],
    [
        'name' => 'Settings',
        'flag' => 'mailing.settings',
        'parent_flag' => 'mailing.index',
    ],
    [
        'name' => 'Save settings',
        'flag' => 'mailing.settings.update',
        'parent_flag' => 'mailing.settings',
    ],
    [
        'name' => 'Send test email',
        'flag' => 'mailing.settings.test-email',
        'parent_flag' => 'mailing.settings',
    ],
    [
        'name' => 'Microsoft connect',
        'flag' => 'mailing.settings.microsoft.connect',
        'parent_flag' => 'mailing.settings',
    ],
    [
        'name' => 'Microsoft callback',
        'flag' => 'mailing.settings.microsoft.callback',
        'parent_flag' => 'mailing.settings',
    ],
    [
        'name' => 'Microsoft disconnect',
        'flag' => 'mailing.settings.microsoft.disconnect',
        'parent_flag' => 'mailing.settings',
    ],
    [
        'name' => 'Update (OTA)',
        'flag' => 'mailing.update',
        'parent_flag' => 'mailing.index',
    ],
    [
        'name' => 'Run update',
        'flag' => 'mailing.update.run',
        'parent_flag' => 'mailing.update',
    ],
];
