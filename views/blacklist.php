<?php

/**
 * Mail Antispam blacklist view.
 *
 * @category   Apps
 * @package    Mail_Antispam
 * @subpackage Views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
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
$this->lang->load('mail_filter');

///////////////////////////////////////////////////////////////////////////////
// Buttons
///////////////////////////////////////////////////////////////////////////////

$buttons = array(
    form_submit_update('submit'),
    anchor_cancel('/app/mail_antispam')
);

///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo form_open('mail_antispam/blacklist');
echo form_header(lang('mail_antispam_blacklist'));

echo field_textarea('blacklist', $blacklist, lang('mail_antispam_list'), FALSE);

echo field_button_set($buttons);

echo form_footer();
echo form_close();
