<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'mail_antispam';
$app['version'] = '1.6.5';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('mail_antispam_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('mail_antispam_app_name');
$app['category'] = lang('base_category_server');
$app['subcategory'] = lang('base_subcategory_mail');

/////////////////////////////////////////////////////////////////////////////
// Controllers
/////////////////////////////////////////////////////////////////////////////

$app['controllers']['mail_antispam']['title'] = $app['name'];
$app['controllers']['settings']['title'] = lang('base_settings');
// $app['controllers']['whitelist']['title'] = lang('mail_antispam_whitelist');
// $app['controllers']['blacklist']['title'] = lang('mail_antispam_blacklist');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['requires'] = array(
    'app-network',
);

$app['core_requires'] = array(
    'spamassassin',
    'app-base-core >= 1:1.6.5',
    'app-mail-filter-core >= 1:1.6.5',
    'app-smtp-core',
    'app-tasks-core',
);

$app['core_file_manifest'] = array(
    'app-mail-antispam.cf' => array('target' => '/etc/mail/spamassassin/app-mail-antispam.cf'),
);

