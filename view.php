<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require('../../config.php');
require_once($CFG->dirroot.'/mod/historytimeline/lib.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID

if (!$cm = get_coursemodule_from_id('historytimeline', $id)) {
    print_error('invalidcoursemodule');
}
$historytimeline = $DB->get_record('historytimeline', array('id'=>$cm->instance), '*', MUST_EXIST);

// echo $historytimeline->name;

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
//require_capability('mod/page:view', $context);

// Completion and trigger events.
// page_view($page, $course, $cm, $context);

$PAGE->set_url('/mod/historytimeline/view.php', array('id' => $cm->id));

$options = empty($page->displayoptions) ? array() : unserialize($page->displayoptions);

$PAGE->set_title($course->shortname.': '.$historytimeline->name);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($historytimeline->name), 2);
}

$sql = 'SELECT e.*  
            FROM {htevent} e
            LEFT JOIN {historytimeline_htevent} he ON e.id = he.htevent_id
         WHERE he.historytimeline_id = ?';

$events = $DB->get_records_sql($sql, array($historytimeline->id));

$paint = mod_historytimeline_create_paint($historytimeline->since, $historytimeline->until, $historytimeline->diff, $events);

echo $paint;

echo '<div>';


/// Show the add event button if allowed.
//if (has_capability('mod/glossary:write', $context) && $showcommonelements ) {
$add_event_url = new moodle_url('/mod/historytimeline/htevent.php');
    echo '<div class="singlebutton glossaryaddentry">';
    echo "<form class=\"form form-inline mb-1\" id=\"newentryform\" method=\"get\" action=\"$add_event_url\">";
    echo '<div>';
    echo "<input type=\"hidden\" name=\"cmid\" value=\"$cm->id\" />";
    echo '<input type="submit" value="'.get_string('addevent', 'historytimeline').'" class="btn btn-secondary" />';
    echo '</div>';
    echo '</form>';
    echo "</div>\n";
//}

// echo '<a href="'.$add_event_url.'">Agregar Evento</a>';



// Display events.
echo '<table class="generaltable">';
foreach($events as $event) {

    $editurl = new moodle_url('/mod/historytimeline/htevent.php');
    $editurl->param('cmid', $cm->id);
    $editurl->param('id', $event->id);

    $deleteurl = new moodle_url('/mod/historytimeline/deletehtevent.php');
    $deleteurl->param('cmid', $cm->id);
    $deleteurl->param('id', $event->id);
    $deleteurl->param('confirm', 0);

    echo '<tr>
        <td>'.$event->title.'</td>
        <td>'.$event->description.'</td>
        <td>'.$event->time.'</td>
        <td>
            <a href="'.$deleteurl.'"><i class="icon fa fa-trash fa-fw " title="Delete" aria-label="Delete"></i></a> 
            <a href="'.$editurl.'"><i class="icon fa fa-cog fa-fw " title="Edit" aria-label="Edit"></i></a></td></tr>';
}
echo '</table>';


echo '</div>';

echo $OUTPUT->footer();
