<?php

/**
 * Javescript helper Mail Antispam.
 *
 * @category   Apps
 * @package    Mail_Antispam
 * @subpackage Javascript
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mail_antispam/
 */

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('mail_antispam');
clearos_load_language('base');

///////////////////////////////////////////////////////////////////////////////
// J A V A S C R I P T
///////////////////////////////////////////////////////////////////////////////

header('Content-Type: application/x-javascript');

echo "

$(document).ready(function() {

    $('#whitelist').css('height', '150px');
    $('#blacklist').css('height', '150px');
    discard_policy();
    quarantine_policy();
    subject_tag_state();

    // Form change events
    //-------------------

    $('#discard_policy').change(function(event) {
        discard_policy();
    });
    $('#quarantine_policy').change(function(event) {
        quarantine_policy();
    });
    $('#subject_tag_state').change(function(event) {
        subject_tag_state();
    });
});

function discard_policy() {
    if ($('#discard_policy').val() > 0)
        $('#discard_policy_level').attr('disabled', false);
    else
        $('#discard_policy_level').attr('disabled', true);
}
function quarantine_policy() {
    if ($('#quarantine_policy').val() > 0)
        $('#quarantine_policy_level').attr('disabled', false);
    else
        $('#quarantine_policy_level').attr('disabled', true);
}
function subject_tag_state() {
    if ($('#subject_tag_state').val() > 0) {
        $('#subject_tag_level').attr('disabled', false);
        $('#subject_tag').attr('disabled', false);
    } else {
        $('#subject_tag_level').attr('disabled', true);
        $('#subject_tag').attr('disabled', true);
    }
}
";

// vim: syntax=javascript ts=4
