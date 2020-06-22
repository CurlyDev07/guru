<?php

class theme_photo_core_renderer extends core_renderer {
    public function full_header(){
        return '';
    }// remove the header in the dashboard

    public function heading($text, $level = 2, $classes = 'main', $id = null) {
        $new_heading_classes = "sub-title text-default pb-4";
        return parent::heading($text, $level, $new_heading_classes, $id);
    }
}

include_once($CFG->dirroot . "/course/renderer.php");


class theme_photo_core_course_renderer extends core_course_renderer {
 
    // place your overridden methods (functions) here.

    protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {
        // open category tag
        $classes = array('category');
        if (empty($coursecat->visible)) {
            $classes[] = 'dimmed_category';
        }
        if ($chelper->get_subcat_depth() > 0 && $depth >= $chelper->get_subcat_depth()) {
            // do not load content
            $categorycontent = '';
            $classes[] = 'notloaded';
            if ($coursecat->get_children_count() ||
                    ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_COLLAPSED && $coursecat->get_courses_count())) {
                $classes[] = 'with_children';
                $classes[] = 'collapsed';
            }
        } else {
            // load category content
            $categorycontent = $this->coursecat_category_content($chelper, $coursecat, $depth);
            $classes[] = 'loaded';
            if (!empty($categorycontent)) {
                $classes[] = 'with_children';
                // Category content loaded with children.
                $this->categoryexpandedonload = true;
            }
        }

        // Make sure JS file to expand category content is included.
        $this->coursecat_include_js();

        $content = html_writer::start_tag('div', array(
            'class' => join(' ', $classes),
            'data-categoryid' => $coursecat->id,
            'data-depth' => $depth,
            'data-showcourses' => $chelper->get_show_courses(),
            'data-type' => self::COURSECAT_TYPE_CATEGORY,
        ));

        // category name
        $categoryname = $coursecat->get_formatted_name();
        $categoryname = html_writer::link(new moodle_url('/course/index.php',
                array('categoryid' => $coursecat->id)),
                $categoryname);
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_COUNT
                && ($coursescount = $coursecat->get_courses_count())) {
            $categoryname .= html_writer::tag('span', ' ('. $coursescount.')',
                    array('title' => get_string('numberofcourses'), 'class' => 'numberofcourse'));
        }
        $content .= html_writer::start_tag('div', array('class' => 'info'));

        $content .= html_writer::tag(($depth > 1) ? 'h4' : 'h3', $categoryname, array('class' => 'categoryname'));
        $content .= html_writer::end_tag('div'); // .info

        #TODO add row tag

        // add category content to the output
        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));

        $content .= html_writer::end_tag('div'); // .category

        // Return the course category tree HTML
        return $content;
    }


    public function frontpage_combo_list() {
        global $CFG;
        // TODO MDL-10965 improve.
        $tree = core_course_category::top();
        if (!$tree->get_children_count()) {
            return '';
        }
        $chelper = new coursecat_helper();
        $chelper->set_subcat_depth($CFG->maxcategorydepth)->
            set_categories_display_options(array(
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url('/course/index.php',
                        array('browse' => 'categories', 'page' => 1))
            ))->
            set_courses_display_options(array(
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url('/course/index.php',
                        array('browse' => 'courses', 'page' => 1))
            ))->
            set_attributes(array('class' => 'row'));
        return $this->coursecat_tree($chelper, $tree);
    }// DONE

    public function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '')
    {
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        
        $content = '';
        // $classes = trim('coursebox clearfix '. $additionalclasses);
        // if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
        //     $nametag = 'h3';
        // } else {
        //     $classes .= ' collapsed';
        //     $nametag = 'div';
        // }

        $content .= html_writer::start_tag('div', array('class' => 'col-4 ppb'));// col-4 ppb


        $nametag = 'div';
        $classes .= ' small-card d-flex';

        // echo('<pre>');
        // var_dump($classes);
        // die();
        // .coursebox
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));
        // $content .= html_writer::empty_tag("img", attributes=null);
        $imgStyle = "width: 110px;height: 110px;background-position: center center;background-repeat: no-repeat;background-size: cover;";
        $content = '<img class="center-cropped-image" style="'.$imgStyle.'" src="https://mdbootstrap.com/img/Mockups/Lightbox/Thumbnail/img%20(67).jpg">';

        $content .= html_writer::start_tag('div', array('class' => 'col pt-2')); //col pt-2
        $content .= html_writer::start_tag('div', array('class' => 'info'));

        // course name
        $coursename = $chelper->get_course_formatted_name($course);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                                            $coursename, array('class' => $course->visible ? '' : 'dimmed'));
        $content .= html_writer::tag($nametag, $coursenamelink, array('class' => 'coursename'));
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('div', array('class' => 'moreinfo'));
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()
                    || $course->has_custom_fields()) {
                $url = new moodle_url('/course/info.php', array('id' => $course->id));
                $image = $this->output->pix_icon('i/info', $this->strings->summary);
                $content .= html_writer::link($url, $image, array('title' => $this->strings->summary));
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }
        $content .= html_writer::end_tag('div'); // .moreinfo

        // print enrolmenticons
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
            foreach ($icons as $pix_icon) {
                $content .= $this->render($pix_icon);
            }
            $content .= html_writer::end_tag('div'); // .enrolmenticons
        }

        $content .= html_writer::end_tag('div'); // .
        
        $content .= html_writer::start_tag('div', array('class' => 'content'));
        $content .= $this->coursecat_coursebox_content($chelper, $course);
        $content .= html_writer::end_tag('div'); // .content

        $content .= html_writer::end_tag('div'); // .col pt-2

       

        $content .= html_writer::end_tag('div'); // .coursebox
        $content .= html_writer::end_tag('div'); // .col-4 ppb

        return $content;
    }
  
 }