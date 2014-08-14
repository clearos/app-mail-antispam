<?php

/**
 * SpamAssassin class.
 *
 * @category   apps
 * @package    mail-antispam
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2014 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mail_antispam/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\mail_antispam;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('mail_antispam');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

// Classes
//--------

use \clearos\apps\base\Daemon as Daemon;
use \clearos\apps\base\File as File;
use \clearos\apps\base\Shell as Shell;
use \clearos\apps\mail_filter\Amavis as Amavis;
use \clearos\apps\tasks\Cron as Cron;

clearos_load_library('base/Daemon');
clearos_load_library('base/File');
clearos_load_library('base/Shell');
clearos_load_library('mail_filter/Amavis');
clearos_load_library('tasks/Cron');

// Exceptions
//-----------

use \clearos\apps\base\File_No_Match_Exception as File_No_Match_Exception;

clearos_load_library('base/File_No_Match_Exception');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * SpamAssassin class.
 *
 * @category   apps
 * @package    mail-antispam
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2006-2014 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mail_antispam/
 */

class SpamAssassin extends Daemon
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const FILE_CONFIG = '/etc/mail/spamassassin/local.cf';
    const FILE_SYSCONFIG = '/etc/sysconfig/spamassassin';
    const FILE_CRONFILE = 'app-mail-antispam';
    const COMMAND_SA_UPDATE = '/usr/bin/sa-update';
    const COMMAND_AUTOUPDATE = '/usr/sbin/app-sa-update';
    const DEFAULT_MAX_CHILDREN = 5;

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * SpamAssassin constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);

        parent::__construct('spamassassin');
    }

    /**
     * Returns blacklist addresses.
     *
     * @return string addresses
     * @throws Engine_Exception
     */

    function get_black_list()
    {
        clearos_profile(__METHOD__, __LINE__);

        $blacklist = '';

        try {
            $file = new File(self::FILE_CONFIG);
            $blacklist = $file->lookup_value('/^blacklist_from/i');
            $blacklist = preg_replace("/[\t ,;:]+/", "\n", $blacklist);
        } catch (File_No_Match_Exception $e) {
            return '';
        }

        return $blacklist;
    }

    /**
     * Returns the maximum children spawned by spamd.
     *
     * @return integer max children spawned
     * @throws Engine_Exception
     */

    function get_max_children()
    {
        clearos_profile(__METHOD__, __LINE__);

        $children = '';

        try {
            $file = new File(self::FILE_SYSCONFIG);
            $children = $file->lookup_value('/^SPAMDOPTIONS=/');
        } catch (File_No_Match_Exception $e) {
            return self::DEFAULT_MAX_CHILDREN;
        }

        $children = preg_replace("/\"/", '', $children);
        $children = preg_replace("/.*-m/", '', $children);
        $children = ltrim($children);
        $children = preg_replace("/\s+.*/", '', $children);

        if ($children)
            return $children;
        else
            return self::DEFAULT_MAX_CHILDREN;
    }

    /**
     * Returns whitelist addresses.
     *
     * @return string addresses
     * @throws Engine_Exception
     */

    function get_white_list()
    {
        clearos_profile(__METHOD__, __LINE__);

        $whitelist = '';

        try {
            $file = new File(self::FILE_CONFIG);
            $whitelist = $file->lookup_value('/^whitelist_from/i');
            $whitelist = preg_replace("/[\t ,;:]+/", "\n", $whitelist);
        } catch (File_No_Match_Exception $e) {
            return '';
        }

        return $whitelist;
    }

    /**
     * Runs auto update.
     *
     * @return void
     * @throws Engine_Exception
     */

    function run_update()
    {
        clearos_profile(__METHOD__, __LINE__);

        // Exit code can be misleading
        $options['validate_exit_code'] = FALSE;

        $shell = new Shell();
        $shell->execute(self::COMMAND_SA_UPDATE, '', TRUE, $options);

        $amavis = new Amavis();
        $amavis->reset();
    }

    /**
     * Sets auto-update cron job.
     *
     * @return void
     * @throws Engine_Exception
     */

    public function set_auto_update_time()
    {
        clearos_profile(__METHOD__, __LINE__);

        $cron = new Cron();

        if ($cron->exists_configlet(self::FILE_CRONFILE))
            $cron->delete_configlet(self::FILE_CRONFILE);

        $nextday = date('w') + 1;

        $cron->add_configlet_by_parts(
            self::FILE_CRONFILE, 
            rand(0, 59),
            rand(1, 12), 
            '*',
            '*',
            $nextday,
            'root',
            self::COMMAND_AUTOUPDATE . ' >/dev/null 2>&1'
        );
    }

    /**
     * Sets black listed addresses.
     *
     * @param string $addresses addresses
     *
     * @return void
     * @throws Engine_Exception, Validation_Exception
     */

    function set_black_list($addresses)
    {
        clearos_profile(__METHOD__, __LINE__);

        // Put addresses on one line
        $addresses = preg_replace("/[\n\r\t ,;:]+/", ' ', $addresses);

        $file = new File(self::FILE_CONFIG);
        $match = $file->replace_lines("/^blacklist_from\s*/i", "blacklist_from $addresses\n");
        if (!$match)
            $file->add_lines_after("blacklist_from $addresses\n", "/^[^#]/");
    }

    /**
     * Sets white listed addresses.
     *
     * @param string $addresses addresses
     *
     * @return void
     * @throws Engine_Exception, Validation_Exception
     */

    function set_white_list($addresses)
    {
        clearos_profile(__METHOD__, __LINE__);

        // Put addresses on one line
        $addresses = preg_replace("/[\n\r\t ,;:]+/", ' ', $addresses);

        $file = new File(self::FILE_CONFIG);
        $match = $file->replace_lines("/^whitelist_from\s*/i", "whitelist_from $addresses\n");

        if (!$match)
            $file->add_lines_after("whitelist_from $addresses\n", "/^[^#]/");
    }

}

// vim: syntax=php ts=4
