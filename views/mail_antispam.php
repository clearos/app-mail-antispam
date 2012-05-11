<?php

/**
 * Mail antispam settings view.
 *
 * @category   ClearOS
 * @package    Mail_Antispam
 * @subpackage Views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mail_antispam/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.  
//  
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('mail_antispam');
$this->lang->load('base');

///////////////////////////////////////////////////////////////////////////////
// Form handler
///////////////////////////////////////////////////////////////////////////////

if ($form_mode === 'edit') {
    $read_only = FALSE;
    $buttons = array(
        form_submit_update('submit'),
        anchor_cancel('/app/mail_antispam/settings')
    );
} else {
    $read_only = TRUE;
    $buttons = array(
        anchor_edit('/app/mail_antispam/settings/edit')
    );
}

for ($score = 3; $score <= 10; $score+= 1) {
    switch ((int)$score) {
        case 3:
            $subject_tag_policy_options[$score] = $score . ' - ' . lang('base_low');
            break;
        case 5:
            $subject_tag_policy_options[$score] = $score . ' - ' . lang('base_medium');
            break;
        case 7:
            $subject_tag_policy_options[$score] = $score . ' - ' . lang('base_high');
            break;
        case 9:
            $subject_tag_policy_options[$score] = $score . ' - ' . lang('base_very_high');
            break;
        default:
            $subject_tag_policy_options[$score] = $score;
    }
}

// Tack on a couple of more
$subject_tag_policy_options[15.0] = 15.0;
$subject_tag_policy_options[20.0] = 20.0;

for ($score = 5; $score <= 25; $score++) {
    switch ($score) {
        case 5:
            $quarantine_policy_options[$score] = $score . ' - ' . lang('base_very_low');
            $discard_policy_options[$score] = $score . ' - ' . lang('base_very_low');
            break;
        case 10:
            $quarantine_policy_options[$score] = $score . ' - ' . lang('base_low');
            $discard_policy_options[$score] = $score . ' - ' . lang('base_low');
            break;
        case 15:
            $quarantine_policy_options[$score] = $score . ' - ' . lang('base_medium');
            $discard_policy_options[$score] = $score . ' - ' . lang('base_medium');
            break;
        case 20:
            $quarantine_policy_options[$score] = $score . ' - ' . lang('base_high');
            $discard_policy_options[$score] = $score . ' - ' . lang('base_high');
            break;
        case 25:
            $quarantine_policy_options[$score] = $score . ' - ' . lang('base_very_high');
            $discard_policy_options[$score] = $score . ' - ' . lang('base_very_high');
            break;
        default:
            $quarantine_policy_options[$score] = $score;
            $discard_policy_options[$score] = $score;
    }
}

///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo form_open('mail_antispam/settings/edit');
echo form_header(lang('base_settings'));

echo fieldset_header(lang('mail_antispam_discard_policy'));
echo field_toggle_enable_disable('discard_policy', $spaminfo['discard'], lang('base_status'), $read_only);
echo field_dropdown('discard_policy_level', $discard_policy_options, $spaminfo['discard_level'], lang('mail_antispam_level'), $read_only);

if ($show_quarantine) {
    echo fieldset_header(lang('mail_antispam_quarantine_policy'));
    echo field_toggle_enable_disable('quarantine_policy', $spaminfo['quarantine'], lang('base_status'), $read_only);
    echo field_dropdown('quarantine_policy_level', $quarantine_policy_options, $spaminfo['quarantine_level'], lang('mail_antispam_level'), $read_only);
}

echo fieldset_header(lang('mail_antispam_subject_tag'));
echo field_toggle_enable_disable('subject_tag_state', $subject_tag_state, lang('base_status'), $read_only);
echo field_dropdown('subject_tag_level', $subject_tag_policy_options, $subject_tag_level, lang('mail_antispam_level'), $read_only);
echo field_input('subject_tag', $subject_tag, lang('mail_antispam_subject_tag'), $read_only);

echo field_button_set($buttons);

echo form_footer();
echo form_close();
