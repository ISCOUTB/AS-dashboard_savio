<?php 

require_once(dirname(__FILE__) . '/../../config.php');

function save_learning_style($course,$act_ref,$sen_int,$vis_vrb,$seq_glo,$act,$ref,$sen,$int,$vis,$vrb,$seq,$glo) {
    GLOBAL $DB, $USER, $CFG;
    if (!$entry = $DB->get_record('learning_style', array('user' => $USER->id, 'course' => $course))) {
        $entry = new stdClass();
        $entry->user = $USER->id;
        $entry->course = $course;
        $entry->state = "1";
        $entry->act_ref = $act_ref;
        $entry->sen_int = $sen_int;
        $entry->vis_vrb = $vis_vrb;
        $entry->seq_glo = $seq_glo;
        $entry->ap_active = $act;
        $entry->ap_reflexivo = $ref;
        $entry->ap_sensorial = $sen;
        $entry->ap_intuitivo = $int;
        $entry->ap_visual = $vis;
        $entry->ap_verbal = $vrb;
        $entry->ap_secuencial = $seq;
        $entry->ap_global = $glo;
        $entry->created_at = time();
        $entry->updated_at = time();
        $entry->id = $DB->insert_record('learning_style', $entry);

        // Data to be saved in the log file
        $data = "{$USER->id}, $act, $sen, $vis, $seq, $ref, $int, $vrb, $glo\n";

        // Path to the log file
        $file = dirname(__FILE__) . '/style.csv';

        // Check if the file exists
        if (!file_exists($file)) {
            // If the file does not exist, write the header first
            $header = "user, act, sen, vis, seq, ref, int, vrb, glo\n";
            file_put_contents($file, $header, FILE_APPEND);
        }

        // Write the data to the log file
        file_put_contents($file, $data, FILE_APPEND);
        return true;
    }else{
        return false;
    }
}
function get_metrics(){
    GLOBAL $DB, $USER, $CFG;
    $response = ["total_students" => 0, 
                "total_students_on_course" => 0, 
                "num_act" => 0, 
                "num_ref" => 0, 
                "num_vis" => 0, 
                "num_vrb" => 0, 
                "num_sen" => 0, 
                "num_int" => 0, 
                "num_sec" => 0, 
                "num_glo" => 0];
    $sql_registros = $DB->get_records("learning_style");
    //print_r(json_encode($sql_registros));
    print_r(json_encode($response));
}
