<?php

require_once("../../config.php");
require_once("lib.php");

$id       = optional_param('id',0,PARAM_INT);          // Event id
$cmid    = optional_param('cmid', 0, PARAM_INT);    // Course module ID
$confirm    = optional_param('confirm', 0, PARAM_INT);

$url = new moodle_url('/mod/historytimeline/deletehtevent.php', array('id'=>$id, 'cmid' => $cmid));
if ($confirm !== 0) {
    $url->param('confirm', $confirm);
}

$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('historytimeline', $cmid)) {
    print_error("invalidcoursemodule");
}

if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
    print_error('coursemisconf');
}

if (! $event = $DB->get_record("htevent", array("id"=>$id))) {
    print_error('invalidevent');
}

// Permission checks are based on the course module instance so make sure it is correct.
/*if ($cm->instance != $entry->glossaryid) {
    print_error('invalidentry');
}*/

require_login($course, false, $cm);
$context = context_module::instance($cm->id);
// $manageentries = has_capability('mod/glossary:manageentries', $context);

if (! $historytimeline = $DB->get_record("historytimeline", array("id"=>$cm->instance))) {
    print_error('invalidid', 'historytimeline');
}


$strareyousuredelete = 'Queres borrar?'; //get_string("areyousuredelete","historytimeline");

/*if (($entry->userid != $USER->id) and !$manageentries) { // guest id is never matched, no need for special check here
    print_error('nopermissiontodelentry');
}*/

/// If data submitted, then process and store.

if ($confirm and confirm_sesskey()) { // the operation was confirmed.

    $DB->delete_records("htevent", array('id' => $id));

    // Delete relation.
    $DB->delete_records("historytimeline_htevent", array('htevent_id' => $id, 'historytimeline_id' => $cm->instance));

    $historytimelineurl = new moodle_url('/mod/historytimeline/view.php', array('id'=>$cmid));

    redirect($historytimelineurl);

} else {        // the operation has not been confirmed yet so ask the user to do so

    $PAGE->navbar->add(get_string('delete'));
    $PAGE->set_title($event->title);
    $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
    $areyousure = "<b>un testo</b><p>$strareyousuredelete</p>";
    $linkyes    = 'deletehtevent.php';
    $linkno     = 'view.php';
    $optionsyes = array('cmid'=>$cm->id, 'id'=>$id, 'confirm'=>1, 'sesskey'=>sesskey());
    $optionsno  = array('cmid'=>$cm->id);

    echo $OUTPUT->confirm($areyousure, new moodle_url($linkyes, $optionsyes), new moodle_url($linkno, $optionsno));

    echo $OUTPUT->footer();
}
