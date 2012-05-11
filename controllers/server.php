<?php

/**
 * Mail antispam controller.
 *
 * @category   Apps
 * @package    Mail_Antispam
 * @subpackage Controllers
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
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

require clearos_app_base('base') . '/controllers/daemon.php';

use \Exception as Exception;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Mail antispam controller.
 *
 * @category   Apps
 * @package    Mail_Antispam
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mail_antispam/
 */

class Server extends Daemon
{
    function __construct()
    {
        parent::__construct('amavisd', 'mail_antispam');
    }

    /**
     * Daemon status.
     *
     * @return view
     */

    function status()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        $this->load->library('mail_filter/Amavis');

        if ($this->amavis->get_antispam_state())
            $status['status'] = $this->amavis->get_status();
        else
            $status['status'] = \clearos\apps\base\Daemon::STATUS_STOPPED;

        echo json_encode($status);
    }

    /**
     * Daemon start.
     *
     * @return view
     */

    function start()
    {
        $this->load->library('mail_filter/Amavis');

        try {
            $this->amavis->set_antispam_state(TRUE);
            $this->amavis->set_running_state(TRUE);
            $this->amavis->set_boot_state(TRUE);
        } catch (Exception $e) {
            //
        }
    }

    /**
     * Daemon stop.
     *
     * @return view
     */

    function stop()
    {
        $this->load->library('mail_filter/Amavis');

        try {
            $this->amavis->set_antispam_state(FALSE);
            $this->amavis->reset(TRUE);
        } catch (Exception $e) {
            //
        }
    }
}
