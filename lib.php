<?php

function historytimeline_add_instance($historytimeline){
    global $DB;

    $id = $DB->insert_record("historytimeline", $historytimeline);

    return $id;
}

function historytimeline_update_instance($historytimeline){
    global $DB;
    $historytimeline->id = $historytimeline->instance;
    $DB->update_record("historytimeline", $historytimeline);

    return true;
}

function historytimeline_delete_instance($id){
    global $DB;

    if (!$historytimeline = $DB->get_record('historytimeline', array('id' => $id))) {
        return false;
    }

    $result = true;

    if (!$DB->delete_records('historytimeline_htevent', array('historytimeline_id' => $historytimeline->id))) {
        $result = false;
    }


    $cm = get_coursemodule_from_instance('historytimeline', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'historytimeline', $id, null);

    if (!$DB->delete_records('historytimeline', array('id' => $historytimeline->id))) {
        $result = false;
    }

    return $result;
}

function mod_historytimeline_create_paint($start, $end, $diff, $events = array(), $eras = array()) {

    // Sets.
    $event_long = 50;
    $start_x = 20;

    $paint = '<svg width=100% height=300>';


    // Create base line with years.

    $years_diff = $diff;
    $start = 1900;
    $end = 2000;

    $width = 7;
    $dot_x = 0;
    $dot_y = 100;

    $long = 0;
    for ($x = $start; $x <= $end; $x++) {

        $paint .= '<rect x='.$dot_x.' y='.$dot_y.' width="'.$width.'" height=3 style=stroke:#a4a4a4 fill=#a4a4a4 />';

        // Add dates.
        if ($x%$years_diff == 0) {
            $paint .= '<text  font-family="Verdana" font-size="6" x='.$dot_x.' y=80>'.$x.'</text>';
            $paint .= '<line x1="'.$dot_x.'" x2="'.$dot_x.'" y1="'.$dot_y.'" y2="80" stroke="#a4a4a4" stroke-width="1" stroke-linecap="round" stroke-dasharray="1, 4"/>';
        }

        // Add eras.
        foreach($eras as $era) {
            if ($x >= $era->start && $x <= $era->end) {
                $paint .= '<rect x='.$dot_x.' y=10 width='.$width.' height=40 style=stroke:'.$era->color.' fill='.$era->color.' />';
            }
        }

        // Add events.
        foreach($events as $event) {
            if ($x == $event->time) {

                if ($long == 0) {
                    $height_event = $dot_y + 40;
                } elseif ($long == 1){
                    $height_event = $dot_y + 80;
                } elseif ($long == 2){
                    $height_event = $dot_y + 120;
                } elseif ($long == 3){
                    $height_event = $dot_y + 160;
                } elseif ($long == 4){
                    $height_event = $dot_y + 200;
                }

                // Vertical line.
                $paint .= '<line x1="'.$dot_x.'" x2="'.$dot_x.'" y1="'.$dot_y.'" y2="'.$height_event.'" stroke="#a4a4a4" stroke-width="1" stroke-linecap="round" stroke-dasharray="1, 4"/>';

                if ($long == 4) {
                    $long = 0;
                } else {
                    $long++;
                }

                // Title.
                $paint .= '<text x='.$dot_x.' y='.$height_event.'  font-family="Verdana" font-size="12">'.$x.'|'.$event->title.'</text>';

                // Title.
                $paint .= '<circle cx='.$dot_x.' cy='.$dot_y.' r=5 stroke=#a4a4a4 stroke-width=4 fill=white />';
            }
        }

        $dot_x = $dot_x + 7; //$width;
    }





    /*  $x = $start_x;
      foreach($events as $event) {
          $paint .= '<rect x='.$x.' y=18 width='.$event_long.' height=3 style=stroke:#a4a4a4 fill=#a4a4a4 />'.
              '<circle cx='.$x.' cy=20 r=5 stroke=#a4a4a4 stroke-width=4 fill=white />';
          $x = $x + $event_long;
      }*/


    $paint .= '</svg>';


    return $paint;


}

function mod_historytimeline_get_events($historytimeline_id) {
    global $DB;

    $DB->get_records();
}