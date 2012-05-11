<?php

/**
 * Settings controller.
 *
 * @category   Apps
 * @package    Mail_Antispam
 * @subpackage Controllers
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
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Settings controller.
 *
 * @category   Apps
 * @package    Mail_Antispam
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/mail_antispam/
 */

class Settings extends ClearOS_Controller
{
    /**
     * Antispam policy controller.
     *
     * @return view
     */

    function index()
    {
        $this->view();
    }

    /**
     * Antispam edit view.
     *
     * @return view
     */

    function edit()
    {
        $this->_view_edit('edit');
    }

    /**
     * Antispam view view.
     *
     * @return view
     */

    function view()
    {
        $this->_view_edit('view');
    }

    /**
     * Antispam common view/edit view.
     *
     * @param string $form_mode form mode
     *
     * @return view
     */

    function _view_edit($form_mode)
    {
        // Load libraries
        //---------------

        $this->lang->load('base');
        $this->load->library('mail_filter/Amavis');

        $quarantine_installed = clearos_app_installed('mail_quarantined');

        // Set validation rules
        //---------------------

        $this->form_validation->set_policy('subject_tag_state', 'mail_filter/Amavis', 'validate_subject_tag_state', TRUE);
        if ((bool)$this->input->post('subject_tag_state')) {
            $this->form_validation->set_policy('subject_tag_level', 'mail_filter/Amavis', 'validate_subject_tag_level', TRUE);
            $this->form_validation->set_policy('subject_tag', 'mail_filter/Amavis', 'validate_subject_tag', TRUE);
        }

        $this->form_validation->set_policy('discard_policy', 'mail_filter/Amavis', 'validate_discard_policy_state', TRUE);
        if ((bool)$this->input->post('discard_policy'))
            $this->form_validation->set_policy('discard_policy_level', 'mail_filter/Amavis', 'validate_discard_policy_level', TRUE);

        $form_ok = $this->form_validation->run();

        // Handle form submit
        //-------------------

        if (($this->input->post('submit') && $form_ok)) {
            try {
                if ($quarantine_installed) {
                    $quarantine_policy = $this->input->post('quarantine_policy');
                    $quarantine_policy_level = $this->input->post('quarantine_policy_level');
                } else {
                    $quarantine_policy = FALSE;
                    $quarantine_policy_level = 15;
                }

                $this->amavis->set_subject_tag_state((bool)$this->input->post('subject_tag_state'));

                if ((bool)$this->input->post('subject_tag_state')) {
                    $this->amavis->set_subject_tag_level($this->input->post('subject_tag_level'));
                    $this->amavis->set_subject_tag($this->input->post('subject_tag'));
                }

                $this->amavis->set_antispam_discard_and_quarantine(
                    $this->input->post('discard_policy'),
                    $this->input->post('discard_policy_level'),
                    $this->input->post('quarantine_policy'),
                    $this->input->post('quarantine_policy_level')
                );

                $this->amavis->reset(TRUE);

                $this->page->set_status_updated();
                redirect('/mail_antispam/settings');
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load view data
        //---------------

        try {
            $data['form_mode'] = $form_mode;
            $data['subject_tag'] = $this->amavis->get_subject_tag();
            $data['subject_tag_level'] = $this->amavis->get_subject_tag_level();
            $data['subject_tag_state'] = $this->amavis->get_subject_tag_state();
            $data['show_quarantine'] = $quarantine_installed;
            $data['spaminfo'] = $this->amavis->get_antispam_discard_and_quarantine();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $this->page->view_form('mail_antispam', $data, lang('base_settings'));
    }
}
