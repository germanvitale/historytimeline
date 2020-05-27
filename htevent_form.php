<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once("$CFG->libdir/formslib.php");

class htevent_form extends moodleform {

    public function definition() {

        $mform = $this->_form; // Don't forget the underscore!

        $cm                = $this->_customdata['cm'];
        $current           = $this->_customdata['current'];

        $context  = context_module::instance($cm->id);

        $mform->addElement('text', 'time', get_string('date', 'historytimeline'));
        $mform->setType('time', PARAM_INT);
        $mform->setDefault('time', '');

        $mform->addElement('text', 'title', get_string('title', 'historytimeline'));
        $mform->setType('title', PARAM_RAW);

        $mform->addElement('text', 'description', get_string('description', 'historytimeline'));
        $mform->setType('description', PARAM_RAW);

        // Submit buttons.
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton',
            get_string('save', 'historytimeline'));
        $buttonarray[] = $mform->createElement('cancel');

        $mform->addGroup($buttonarray, 'buttonbar', '', array(' '), false);

        $this->set_data($current);

    }

    function validation($data, $files) {

    }
}
