<?php

class block_learning_style extends block_base
{

    public function init()
    {
        $this->title = get_string('pluginname', 'block_learning_style');
    }

    public function instance_allow_multiple()
    {
        return false;
    }

    public function my_slider($value, $izq_val, $der_val, $izq_title, $der_title)
    {
        global $OUTPUT;

        $slider = '';
        $slider .= '<div class="slider-container" style="text-align:center; margin: 10px 0px;">';
        $p = (($value + 11) / 22) * 100;
        if ($value >= 0 ){
            $slider .= "<span title='$izq_title'>$izq_val</span> ⇄ <strong title='$der_title'> $der_val </strong><br>";
            $slider .= "<div class=\"progress\"><div class=\"progress-bar progress-bar-striped bg-success\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style='width: $p%'></div></div>";
        }else {
            $slider .= "<strong title='$izq_title'>$izq_val</strong> ⇄ <span title='$der_title'> $der_val </span><br>";
            $slider .= "<div class=\"progress\"><div class=\"progress-bar progress-bar-striped bg-success\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style='width: $p%'></div></div>";
        }
        $slider .= '</div>';
        return $slider;
    }

    public function get_content()
    {

        global $OUTPUT, $CFG, $DB, $USER, $COURSE, $SESSION;

        if ($COURSE->id == SITEID) {
            return;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = "";
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if (!isloggedin()) {
            return;
        }

        $COURSE_ROLED_AS_STUDENT = $DB->get_record_sql("  SELECT m.id
                FROM {user} m 
                LEFT JOIN {role_assignments} m2 ON m.id = m2.userid 
                LEFT JOIN {context} m3 ON m2.contextid = m3.id 
                LEFT JOIN {course} m4 ON m3.instanceid = m4.id 
                WHERE (m3.contextlevel = 50 AND m2.roleid IN (5) AND m.id IN ( {$USER->id} )) AND m4.id = {$COURSE->id} ");

        //Check if user is student
        if (isset($COURSE_ROLED_AS_STUDENT->id) && $COURSE_ROLED_AS_STUDENT->id) {
            //check if user already have the learning style
            $entry = $DB->get_record('learning_style', array('user' => $USER->id, 'course' => $COURSE->id));

            if (!$entry) {
                if (isset($this->config->learning_style_content) && isset($this->config->learning_style_content["text"])) {
                    $SESSION->learning_style = $this->config->learning_style_content["text"];
                    $redirect = new moodle_url('/blocks/learning_style/view.php', array('cid' => $COURSE->id));
                    redirect($redirect);
                }
            } else {
                $final_style = [];

                $izq_title = "Se sugiere utilizar actividades prácticas, resolución de problemas, realizar experimentos, proyectos prácticos, participar en discusiones grupales, trabajar en grupos.";
                $der_title = "Se sugiere desarrollar lecturas reflexivas, tomar notas y reflexionar sobre el material de aprendizaje, crear diagramas y organizar información, tomarse el tiempo para considerar las opciones antes de tomar decisiones, actividades de análisis de casos y actividades de autoevaluación.";
                if ($entry->act_ref[1] == 'a') {

                    $final_style[$entry->act_ref[0] . "ar"] = $this->my_slider($entry->act_ref[0] * -1, get_string("active", 'block_learning_style'), get_string("reflexive", 'block_learning_style'),$izq_title,$der_title);
                    $final_style[$entry->act_ref[0] . "ar"] .= "<button data-bs-placement='top' type='button' class='btn btn-primary' style='width: 100%;' data-bs-toggle='popover' data-bs-title='Activo' data-bs-content='$izq_title'>Ver sugerencias de estudio</button>";
                } else {
                    $final_style[$entry->act_ref[0] . "ar"] = $this->my_slider($entry->act_ref[0], get_string("active", 'block_learning_style'), get_string("reflexive", 'block_learning_style'),$izq_title,$der_title);
                    $final_style[$entry->act_ref[0] . "ar"] .= "<button data-bs-placement='top' type='button' class='btn btn-primary' style='width: 100%;' data-bs-toggle='popover' data-bs-title='Reflexivo' data-bs-content='$der_title'>Ver sugerencias de estudio</button>";
                }

                $izq_title = "Se sugiere realizar una observación detallada y aplicación práctica de conceptos, utilizar ejemplos concretos y aplicaciones prácticas del material de aprendizaje, realizar actividades de laboratorio y proyectos. Desarrollar trabajo práctico. ";
                $der_title = "Se sugiere utilizar buscar conexiones y patrones en la información, utilizar analogías e historias para ilustrar los conceptos, hacer preguntas y explorar nuevas ideas. Actividades como la resolución de problemas complejos, actividades creativas y discusiones teóricas.";
                if ($entry->sen_int[1] == 'a') {
                    $final_style[$entry->sen_int[0] . "si"] = $this->my_slider($entry->sen_int[0] * -1, get_string("sensitive", 'block_learning_style'), get_string("intuitive", 'block_learning_style'),$izq_title,$der_title);
                    $final_style[$entry->sen_int[0] . "si"] .= "<button data-bs-placement='top' type='button' class='btn btn-primary' style='width: 100%;' data-bs-toggle='popover' data-bs-title='Sensitivo' data-bs-content='$izq_title'>Ver sugerencias de estudio</button>";
                } else {
                    $final_style[$entry->sen_int[0] . "si"] = $this->my_slider($entry->sen_int[0], get_string("sensitive", 'block_learning_style'), get_string("intuitive", 'block_learning_style'),$izq_title,$der_title);
                    $final_style[$entry->sen_int[0] . "si"] .= "<button data-bs-placement='top' type='button' class='btn btn-primary' style='width: 100%;' data-bs-toggle='popover' data-bs-title='Intuitivo' data-bs-content='$der_title'>Ver sugerencias de estudio</button>";
                }

                $izq_title = "Se sugiere utilizar gráficos, diagramas, videos y otros recursos visuales para representar la información, realizar mapas mentales y dibujar imágenes para comprender el material. ";
                $der_title = "Se sugiere leer y escribir notas, desarrollar resúmenes del material, discutir el material en grupos o con un compañero de estudio, utilizar técnicas de memorización como la repetición verbal, discusiones o explicaciones verbales.";
                if ($entry->vis_vrb[1] == 'a') {
                    $final_style[$entry->vis_vrb[0] . "vv"] = $this->my_slider($entry->vis_vrb[0] * -1, get_string("visual", 'block_learning_style'), get_string("verbal", 'block_learning_style'),$izq_title,$der_title);
                    $final_style[$entry->vis_vrb[0] . "vv"] .= "<button data-bs-placement='top' type='button' class='btn btn-primary' style='width: 100%;' data-bs-toggle='popover' data-bs-title='Visual' data-bs-content='$izq_title'>Ver sugerencias de estudio</button>";
                } else {
                    $final_style[$entry->vis_vrb[0] . "vv"] = $this->my_slider($entry->vis_vrb[0], get_string("visual", 'block_learning_style'), get_string("verbal", 'block_learning_style'),$izq_title,$der_title);
                    $final_style[$entry->vis_vrb[0] . "vv"] .= "<button data-bs-placement='top' type='button' class='btn btn-primary' style='width: 100%;' data-bs-toggle='popover' data-bs-title='Verbal' data-bs-content='$der_title'>Ver sugerencias de estudio</button>";
                }

                $izq_title = "Se sugiere seguir una estructura lógica y organizada para aprender, tomar notas y resumir el material de aprendizaje, trabajar, analizar a través de pasos a pasos para resolver problemas.";
                $der_title = "Se sugiere buscar conexiones y patrones en la información, trabajar con el material de aprendizaje en su conjunto antes de enfocarse en los detalles, utilizar analogías y metáforas para ilustrar los conceptos. Trabajar en actividades que permiten la exploración y conexión de conceptos, aprendizaje basado en proyectos y discusión de temas complejos.";
                if ($entry->seq_glo[1] == 'a') {
                    $final_style[$entry->seq_glo[0] . "sg"] = $this->my_slider($entry->seq_glo[0] * -1, get_string("sequential", 'block_learning_style'), get_string("global", 'block_learning_style'),$izq_title,$der_title);
                    $final_style[$entry->seq_glo[0] . "sg"] .= "<button data-bs-placement='top' type='button' class='btn btn-primary' style='width: 100%;' data-bs-toggle='popover' data-bs-title='Secuencial' data-bs-content='$izq_title'>Ver sugerencias de estudio</button>";
                } else {
                    $final_style[$entry->seq_glo[0] . "sg"] = $this->my_slider($entry->seq_glo[0], get_string("sequential", 'block_learning_style'), get_string("global", 'block_learning_style'),$izq_title,$der_title);
                    $final_style[$entry->seq_glo[0] . "sg"] .= "<button data-bs-placement='top' type='button' class='btn btn-primary' style='width: 100%;' data-bs-toggle='popover' data-bs-title='Global' data-bs-content='$der_title'>Ver sugerencias de estudio</button>";
                }

                krsort($final_style);

                $this->content->text .= "<p class='alpyintro'>Según el modelo de Estilos de Aprendizaje de Felder y Soloman, toda persona tiene mayor inclinación a un estilo u otro. En tu caso, el estilo que más predomina es:</p>";
                $this->content->text .= "<link rel='stylesheet' href='".$CFG->wwwroot."/blocks/learning_style/styles.css'>";
                //bootstrap css
                //$this->content->text .= "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'>";
                //bootstrap js
                $this->content->text .= "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'></script>";
                $this->content->text .= "<ul class='lsorder'>";
                foreach ($final_style as $key => $val) {
                    $this->content->text .= "<li>$val</li>";
                }
                $this->content->text .= '<script>document.addEventListener("DOMContentLoaded", function () { const popoverTriggerList = [].slice.call(document.querySelectorAll(\'[data-bs-toggle="popover"]\')); popoverTriggerList.forEach(function (popoverTriggerEl) { new bootstrap.Popover(popoverTriggerEl); }); });</script>';

            }
        } else {
            if (isset($this->config->learning_style_content) && isset($this->config->learning_style_content["text"])) {
                //Aquí se debe maquetar el dashboard
                $view = file_get_contents($CFG->dirroot . '/blocks/learning_style/dashboard/view.php');
                $this->content->text = $view;
            } else {
                $this->content->text = "<img src='" . $OUTPUT->pix_url('warning', 'block_learning_style') . "'>" . get_string('learning_style_configempty', 'block_learning_style');
            }
        }

        return $this->content;
    }
}
