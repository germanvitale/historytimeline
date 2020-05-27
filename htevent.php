<?php

require('../../config.php');

require_once($CFG->dirroot.'/mod/historytimeline/htevent_form.php');
global $DB;

$cmid = optional_param('cmid', 0, PARAM_INT); // Course Module ID
$id      = optional_param('id', 0, PARAM_INT); // Event ID
$delete  = optional_param('id', 0, PARAM_INT); // Event ID

if (!$cm = get_coursemodule_from_id('historytimeline', $cmid)) {
    print_error('invalidcoursemodule');
}
$historytimeline = $DB->get_record('historytimeline', array('id'=>$cm->instance), '*', MUST_EXIST);

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
//require_capability('mod/page:view', $context);

// Completion and trigger events.
// page_view($page, $course, $cm, $context);

$url = new moodle_url('/mod/historytimeline/htevent.php', array('cmid'=>$cm->id));
if (!empty($id)) {
    $url->param('id', $id);
}

$PAGE->set_url($url);

$PAGE->set_title($course->shortname.': '.$historytimeline->name);
$PAGE->set_heading($course->fullname);

//Instantiate simplehtml_form
if ($id) {
    $event = $DB->get_record('htevent', array('id' => $id), '*', MUST_EXIST);
} else {
    $event = new stdClass();
}


$mform = new htevent_form($url, ['cm' => $cm, 'current' => $event]);

$historytimeline_url = new moodle_url('/mod/historytimeline/view.php');
$historytimeline_url->param('id', $cm->id);

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //die('canceled');
    redirect($historytimeline_url);
    //Handle form cancel operation, if cancel button is present on form
} else if ($fromform = $mform->get_data()) {
    //In this case you process validated data. $mform->get_data() returns data posted in form.

    if ($id) {

        // Edit.
        $event = new stdClass();
        $event->id = $id;
        $event->title = $fromform->title;
        $event->time = $fromform->time;
        $event->description = $fromform->description;
        $event_id = $DB->update_record("htevent", $event);


    } else {
        $event_id = $DB->insert_record("htevent", $fromform);

        $historytimeline_htevent = new stdClass();
        $historytimeline_htevent->historytimeline_id = $historytimeline->id;
        $historytimeline_htevent->htevent_id = $event_id;
        $historytimeline_htevent_id = $DB->insert_record("historytimeline_htevent", $historytimeline_htevent);
    }

    redirect($historytimeline_url);

} else {
    // die('mas');
    // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
    // or on the first display of the form.
   // echo 'holissss';
    //Set default data (if any)
    //$mform->set_data($toform);
    //displays the form
    // $mform->display();
}

echo $OUTPUT->header();
if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($historytimeline->name), 2);
}

$mform->display();

echo $OUTPUT->footer();