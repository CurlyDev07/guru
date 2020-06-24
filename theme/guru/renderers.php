<?php

class theme_guru_core_renderer extends core_renderer {
    // public function full_header(){
    //     return '';
    // }// remove the header in the dashboard

    public function heading($text, $level = 2, $classes = 'main', $id = null) {
        $new_heading_classes = "sub-title text-default pb-4";
        return parent::heading($text, $level, $new_heading_classes, $id);
    }

    public function management_heading($heading, $viewmode = null, $categoryid = null) {
        echo 'dasdddddddddddddddddddddddddddddddddddddddd';
        die();
       
    }// #TODO
}

include_once($CFG->dirroot . "/course/renderer.php");


class theme_guru_core_course_renderer extends core_course_renderer {
    // place your overridden methods (functions) here.

    protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {
        return "
            <a class='d-block col-4 ppb' href='".new moodle_url('/course/index.php?categoryid='.$coursecat->id)."' style='text-decoration: none;'>
                <div class='small-card d-flex'>
                    <img class='center-cropped-image' src='https://mdbootstrap.com/img/Mockups/Lightbox/Thumbnail/img%20(67).jpg'>
                    <div class='col pt-2'>
                        <span class='category-name'>Course</span>
                        <p class='title mb-1 line-clamp'>{$coursecat->name}</p>
                        <div class='description line-clamp'>
                            {$coursecat->description}
                        </div>
                    </div>
                </div>
            </a>
        ";// close
    }// return Category Card

    protected function coursecat_subcategories(coursecat_helper $chelper, $coursecat, $depth) {
        global $CFG;
        $subcategories = array();
        if (!$chelper->get_categories_display_option('nodisplay')) {
            $subcategories = $coursecat->get_children($chelper->get_categories_display_options());
        }
        $totalcount = $coursecat->get_children_count();
        if (!$totalcount) {
            // Note that we call core_course_category::get_children_count() AFTER core_course_category::get_children()
            // to avoid extra DB requests.
            // Categories count is cached during children categories retrieval.
            return '';
        }

        // prepare content of paging bar or more link if it is needed
        $paginationurl = $chelper->get_categories_display_option('paginationurl');
        $paginationallowall = $chelper->get_categories_display_option('paginationallowall');
        if ($totalcount > count($subcategories)) {
            if ($paginationurl) {
                // the option 'paginationurl was specified, display pagingbar
                $perpage = $chelper->get_categories_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_categories_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_categories_display_option('viewmoreurl')) {
                // the option 'viewmoreurl' was specified, display more link (if it is link to category view page, add category id)
                if ($viewmoreurl->compare(new moodle_url('/course/index.php'), URL_MATCH_BASE)) {
                    $viewmoreurl->param('categoryid', $coursecat->id);
                }
                $viewmoretext = $chelper->get_categories_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)), array('class' => 'paging paging-showperpage'));
        }

        // display list of subcategories
        $content = html_writer::start_tag('div', array('class' => 'subcategories'));

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        
        $content = html_writer::start_tag('div', array('class' => 'row'));
        foreach ($subcategories as $subcategory) {
            $content .= $this->coursecat_category($chelper, $subcategory, $depth + 1);
        }
        $content .= html_writer::end_tag('div');


        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div');
        return $content;
    }// Loop Category Card

    public function course_category($category) {
        global $CFG;
        $usertop = core_course_category::user_top();
        if (empty($category)) {
            $coursecat = $usertop;
        } else if (is_object($category) && $category instanceof core_course_category) {
            $coursecat = $category;
        } else {
            $coursecat = core_course_category::get(is_object($category) ? $category->id : $category);
        }
        $site = get_site();
        $output = '';

        if ($coursecat->can_create_course() || $coursecat->has_manage_capability()) {
            // Add 'Manage' button if user has permissions to edit this category.
            $managebutton = $this->single_button(new moodle_url('/course/management.php',
                array('categoryid' => $coursecat->id)), get_string('managecourses'), 'get');
            $this->page->set_button($managebutton);
        }

        if (core_course_category::is_simple_site()) {
            // There is only one category in the system, do not display link to it.
            $strfulllistofcourses = get_string('fulllistofcourses');
            $this->page->set_title("$site->shortname: $strfulllistofcourses");
        } else if (!$coursecat->id || !$coursecat->is_uservisible()) {
            $strcategories = get_string('categories');
            $this->page->set_title("$site->shortname: $strcategories");
        } else {
            $strfulllistofcourses = get_string('fulllistofcourses');
            $this->page->set_title("$site->shortname: $strfulllistofcourses");

            // Print the category selector
            $categorieslist = core_course_category::make_categories_list();
            if (count($categorieslist) > 1) {
                $output .= html_writer::start_tag('div', array('class' => 'categorypicker'));
                $select = new single_select(new moodle_url('/course/index.php'), 'categoryid',
                        core_course_category::make_categories_list(), $coursecat->id, null, 'switchcategory');
                $select->set_label(get_string('categories').':');
                $output .= $this->render($select);
                $output .= html_writer::end_tag('div'); // .categorypicker
            }
        }

        // Print current category description
        $chelper = new coursecat_helper();
        if ($description = $chelper->get_category_formatted_description($coursecat)) {
            $output .= $this->box($description, array('class' => 'generalbox info'));
        }

        // Prepare parameters for courses and categories lists in the tree
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)
                ->set_attributes(array('class' => 'row category-browse-'.$coursecat->id));

        $coursedisplayoptions = array();
        $catdisplayoptions = array();
        $browse = optional_param('browse', null, PARAM_ALPHA);
        $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $baseurl = new moodle_url('/course/index.php');
        if ($coursecat->id) {
            $baseurl->param('categoryid', $coursecat->id);
        }
        if ($perpage != $CFG->coursesperpage) {
            $baseurl->param('perpage', $perpage);
        }
        $coursedisplayoptions['limit'] = $perpage;
        $catdisplayoptions['limit'] = $perpage;
        if ($browse === 'courses' || !$coursecat->get_children_count()) {
            $coursedisplayoptions['offset'] = $page * $perpage;
            $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $catdisplayoptions['nodisplay'] = true;
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $catdisplayoptions['viewmoretext'] = new lang_string('viewallsubcategories');
        } else if ($browse === 'categories' || !$coursecat->get_courses_count()) {
            $coursedisplayoptions['nodisplay'] = true;
            $catdisplayoptions['offset'] = $page * $perpage;
            $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $coursedisplayoptions['viewmoretext'] = new lang_string('viewallcourses');
        } else {
            // we have a category that has both subcategories and courses, display pagination separately
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1));
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1));
        }
        $chelper->set_courses_display_options($coursedisplayoptions)->set_categories_display_options($catdisplayoptions);
        // Add course search form.
        $output .= $this->course_search_form();

        // Display course category tree.
        $output .= $this->coursecat_tree($chelper, $coursecat);

        // Add action buttons
        $output .= $this->container_start('buttons');
        if ($coursecat->is_uservisible()) {
            $context = get_category_or_system_context($coursecat->id);
            if (has_capability('moodle/course:create', $context)) {
                // Print link to create a new course, for the 1st available category.
                if ($coursecat->id) {
                    $url = new moodle_url('/course/edit.php', array('category' => $coursecat->id, 'returnto' => 'category'));
                } else {
                    $url = new moodle_url('/course/edit.php',
                        array('category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat'));
                }
                $output .= $this->single_button($url, get_string('addnewcourse'), 'get');
            }
            ob_start();
            print_course_request_buttons($context);
            $output .= ob_get_contents();
            ob_end_clean();
        }
        $output .= $this->container_end();

        return $output;
    }
 
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG, $SITE, $SESSION, $USER;

        // check if course has files
        $hasOverviewfiles = method_exists($course, 'get_course_overviewfiles');
     

        $image = '';
        $img_count = 0;
        $img_placeholder = "https://mdbootstrap.com/img/Mockups/Lightbox/Thumbnail/img%20(67).jpg";

        if ($hasOverviewfiles) {
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                        '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                        $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
    
                if ($isimage) {
                    if ($img_count > 0) {
                        continue;
                    }// if image greater than 1 skip loop
    
                    $image = $url;
                    $img_count++;
                }
            }
        }

        if ($img_count < 1) {
            $image = $img_placeholder;
        }
      
        // echo '<pre>';
        // print_r($course);
        // die();

        $coursename = $chelper->get_course_formatted_name($course);// course name
        $coursenamelink = new moodle_url('/course/view.php', array('id' => $course->id));
        $htm = '
            <div class="col-3 d-block align-items-center d-flex my-0 p-3">
                <div class="card">
                <div class="view overlay">
                    <img class="card-img-top" style="height: 156.57px;" src="'.$image.'">
                    <a href="#!">
                    <div class="mask rgba-white-slight waves-effect waves-light"></div>
                    </a>
                </div>
                <div class="card-body">
                    <h4 class="card-title mb-1">'.$coursename.'</h4>
                    <span class="status bg-warning">Required</span>
                    <div class="card-text mt-2">'.$course->summary.'</div>
                    '.$images.'
                    <div class="d-flex align-items-center">
                        <a href="'.$coursenamelink.'" class="link-primary"><i class="fas fa-external-link-alt mr-1"></i>VIEW COURSE</a>
                    </div>
                </div>
                </div>
            </div>
        ';

        return $htm;
    }// courses card


    public function search_courses($searchcriteria) {
        global $CFG;
        $content = '';
        if (!empty($searchcriteria)) {
            // print search results

            $displayoptions = array('sort' => array('displayname' => 1));
            // take the current page and number of results per page from query
            $perpage = optional_param('perpage', 0, PARAM_RAW);
            if ($perpage !== 'all') {
                $displayoptions['limit'] = ((int)$perpage <= 0) ? $CFG->coursesperpage : (int)$perpage;
                $page = optional_param('page', 0, PARAM_INT);
                $displayoptions['offset'] = $displayoptions['limit'] * $page;
            }
            // options 'paginationurl' and 'paginationallowall' are only used in method coursecat_courses()
            $displayoptions['paginationurl'] = new moodle_url('/course/search.php', $searchcriteria);
            $displayoptions['paginationallowall'] = true; // allow adding link 'View all'

            $class = 'course-search-result row';
            foreach ($searchcriteria as $key => $value) {
                if (!empty($value)) {
                    $class .= ' course-search-result-'. $key;
                }
            }
            $chelper = new coursecat_helper();
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT)->
                    set_courses_display_options($displayoptions)->
                    set_search_criteria($searchcriteria)->
                    set_attributes(array('class' => $class));

            $courses = core_course_category::search_courses($searchcriteria, $chelper->get_courses_display_options());
            $totalcount = core_course_category::search_courses_count($searchcriteria);
            $courseslist = $this->coursecat_courses($chelper, $courses, $totalcount);

            if (!$totalcount) {
                if (!empty($searchcriteria['search'])) {
                    $content .= $this->heading(get_string('nocoursesfound', '', $searchcriteria['search']));
                } else {
                    $content .= $this->heading(get_string('novalidcourses'));
                }
            } else {
                $content .= $this->heading(get_string('searchresults'). ": $totalcount");
                $content .= $courseslist;
            }

            if (!empty($searchcriteria['search'])) {
                // print search form only if there was a search by search string, otherwise it is confusing
                $content .= $this->box_start('generalbox mdl-align');
                $content .= $this->course_search_form($searchcriteria['search']);
                $content .= $this->box_end();
            }
        } else {
            // just print search form
            $content .= $this->box_start('generalbox mdl-align');
            $content .= $this->course_search_form();
            $content .= $this->box_end();
        }
        return $content;
    }// search
}


require_once($CFG->dirroot.'/course/format/topics/renderer.php');

class theme_guru_format_topics_renderer extends format_topics_renderer {

   
    protected function section_header($section, $course, $onsectionpage, $sectionreturn=null) {
        global $PAGE;

        $o = '';
        $currenttext = '';
        $sectionstyle = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            }
            if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $data = [
            'course_title' => get_section_name($course, $section),
        ];
        // return $data[]
        
        $o.= html_writer::start_tag('li', array('id' => 'section-'.$section->section,
            'class' => 'section main clearfix'.$sectionstyle, 'role'=>'region',
            'aria-label'=> get_section_name($course, $section)));

        // Create a span that contains the section title to be used to create the keyboard section move menu.
        $o .= html_writer::tag('span', get_section_name($course, $section), array('class' => 'hidden sectionname'));

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $o.= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o.= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
        }
        $sectionname = html_writer::tag('span', $this->section_title($section, $course));
        $o.= $this->output->heading($sectionname, 3, 'sectionname' . $classes);

     

        $o .= $this->section_availability($section);

        $o .= html_writer::start_tag('div', array('class' => 'summary'));
        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting
            // "Hidden sections are shown in collapsed form".
            $o .= $this->format_summary_text($section);
        }
        $o .= html_writer::end_tag('div');
        $image = new moodle_url('/theme/guru/pix/course-details-header.png');
        $o .= '<img id="course-details-image" src="'.$image.'">';
        return $o;


        // return $this->format_summary_text($section);
        // echo '<pre>';
        // var_dump();
        // die();

        // return $this->format_summary_text($section);

        $hidden_topics = '
            <div 
                class="custom-topics d-none" 
                topic="'.get_section_name($course, $section).'"
            ></div>
        ';

        return $hidden_topics;

        // return get_section_name($course, $section);
        return $section->summary;
        return $this->format_summary_text($section);
    }

    protected function start_section_list() {
        return html_writer::start_tag('div', array('class' => 'custom-topics topics'));
    }

    protected function end_section_list() {
        return html_writer::end_tag('div');
    }
}

require_once($CFG->dirroot.'/course/classes/management_renderer.php');

class theme_guru_classes_core_course_management_renderer extends core_course_management_renderer {

}// #TODO
