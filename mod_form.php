<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_historytimeline_mod_form extends moodleform_mod {

    function definition() {
        global $CFG, $DB, $OUTPUT;

        $mform =& $this->_form;

        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $this->standard_intro_elements();

        // Appearance.
        $mform->addElement('header', 'appearancehdr', get_string('appearance'));

        $mform->addElement('text', 'since', get_string('from', 'historytimeline'));
        $mform->setType('since', PARAM_INT);

        $mform->addElement('text', 'until', get_string('to', 'historytimeline'));
        $mform->setType('until', PARAM_INT);

        $mform->addElement('text', 'diff', get_string('diff', 'historytimeline'));
        $mform->setType('diff', PARAM_INT);

        $this->standard_coursemodule_elements();

        //-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }
}
