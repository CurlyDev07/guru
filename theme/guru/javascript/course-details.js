
if (window.location.pathname.includes('course/view.php')) {
    let bg_image = $(document).find('#course-details-image').attr('src');

    // add style to page content
    $('#page-content').css({
        "display":"flex",
        "flex-wrap":"wrap",
        "margin-right":"-15px",
        "margin-left":"-15px",
        "margin-top":"322px",
    });

    $('.section-modchooser').hide(); // hide Add an activity or resource
    
    let pageSettings = $('#action-menu-2-menubar').clone(true, true);
    
    let topics = [];

    $('#changenumsections').addClass('text-left');// add topic left align
    $('#changenumsections').find('.icon').attr('class', 'fas fa-plus-square mr-2').attr('style', 'color: #373a3c;');// add topic change icon

    let course_topics = $('.custom-topics').children();
    let addTopics = $('#changenumsections').clone(true, true);

    let is_editing_mode = $('#user_is_editing').html();// check if editing mode is state

    $.each(course_topics, function( index, value ) {
        let title = $(this).find('.sectionname').html();
        let summary = $(this).find('.summary').html();
        let settings = "";

        if (is_editing_mode) {
          $(this).find('.section_action_menu').find('.dropdown').children().first().css('color', '#565656').html('<i class="fas fa-cog ml-auto"></i>'); //add cog icon
          settings = $(this).find('.section_action_menu').addClass('position-absolute').css({
            'right':0,
            'top':0,
            'margin-top': 25,
            'padding': '0px 1px',
            'z-index': '998',
          }).parent().html();
        }

        topics.push({
            title,summary,settings
        }); 
    });// get topic name and summary
    
    $('.course-content').html(`
        <div class="col-8">
            
        </div>
        <div class="col-4 border-left">
            <div id="settings-area" class="align-items-center border-bottom d-flex font-bold fs-20 lh-23 py-3">
                <span class="mr-auto">Course Content</span>
            </div>
        </div>
    
    `);// delete page content
    $('.course-content').addClass('row');// add row to align layout 

    let courses = [];
    $.each(topics, function (index, value) {
        // console.log(value.title);
        // console.log(value.summary);
    
        $('.course-content').children().last().append(`
            <div id="${index}" class="course-topic position-relative align-items-center d-flex fs-14 lh-23 py-3" style="padding-left: 37px;">
                <div class="form-check pl-0">
                  <input type="checkbox" class="form-check-input" id="materialUnchecked${index}">
                  <label class="form-check-label fs-14 lh-20" for="materialUnchecked${index}">${value.title}</label>
                </div><!-- Material Unchecked -->
                ${value.settings}
            </div>
        `);
    });// displays all checkbox on the right side
    
    //ADD TOPICS
    $('.course-content').children().last().append(addTopics);
    
    $('.course-content').children().first().html(`
        <small class="h3 font-bold text-dark m-0">${topics[1].title}</small>
        <p class="text-default mb-0" style="margin-top: 28px; font-family: Arial;line-height: 20px;">
        ${topics[1].summary}
        </p>
    `);
    
    $(document).find('.course-topic').click(function () {
        let id = $(this).attr('id');
        let summary = topics[id].summary;
        let title = topics[id].title;
    
        $.each($(document).find(".course-topic"), function () {
            if ($(this).attr('id') == id) {
                $(this).children().first().children().first().prop("checked", true);
            }else{
                $(this).children().first().children().first().prop("checked", false);
            }
        });
    
    
        $('.course-content').children().first().html(`
            <small class="h3 font-bold text-dark m-0">${title}</small>
            <p class="text-default mb-0" style="margin-top: 28px; font-family: Arial;line-height: 20px;">
               ${summary}
            </p>
        `);
    }); // display course content on click of right side checkbox
    
    $('.col-12.pt-3.pb-3').hide();
    
    // HEADER
    $('#page').prepend(`
        <div
            style="
                background-image: url('${bg_image}');
                height: 252px;
                width: 100%;
                position: absolute;
                left: 0;right: 0;
                padding: 25px 130px;
                margin-top: -47px;">
    
            <div style="padding-bottom: 21px;">
                <ul class="breadcrumbs mb-1" style="padding-top: 30.5px!important;margin-top: -26px;">
                  <li class="link text-white"><i class="fas fa-home fa-lg"></i></li>
                  <li class="link text-white">My Courses</li>
                  <li class="link text-white disabled"><i class="fas fa-angle-left fa-flip-horizontal"></i></li>
                  <li class="link text-white">Category Title</li>
                  <li class="link text-white disabled"><i class="fas fa-angle-left fa-flip-horizontal"></i></li>
                  <li class="link text-white">Selected Course Title Goes Here</li>
                </ul>
        
                <div class="page-title text-white" style="margin: 0px!important">Selected Course Title Goes Here</div>
        
                <div class="text-white text-sm mt-2">
                  <div class="">Due: 13 June 2020</div>
                  <div class="mt-2">1.5 hours
                </div>
        
                <!-- <div class="status bg-success" style="display: inline-block;margin-top: 43px;">Success</div> -->
                <a class="btn btn-primary py-2 ml-0" style="font-size: 12px;line-height: 14px;margin-top: 27px;">START</a>
              </div>
            </div>
    
    
        </div>
    `);
    
    
    $('#page').prepend(`
        <nav class="border-bottom col-12 navbar navbar-expand-lg navbar-light position-absolute py-3 shadow-none"
        style="width: 100%;position: absolute;left: 0;background-color: white;right: 0;padding-top: 35px!important;padding: 75px 130px;margin-top: 206px;padding-bottom: 35px!important;"
        >
          <div class="px-0 body ">
            <div class="collapse navbar-collapse" id="navbarSupportedContent-555">
              <ul class="nav-custom navbar-nav mr-auto">
                <li class="nav-item active">
                  <a class="nav-link active" href="#">Overview</a>
                </li>
                <li class="nav-item">
                  <a class="fs-14 lh-26 nav-link px-4" href="#">Activities</a>
                </li>
                <li class="nav-item">
                  <a class="fs-14 lh-26 nav-link px-4" href="#">Resources</a>
                </li>
                <li class="nav-item">
                  <a class="fs-14 lh-26 nav-link px-4" href="#">Topic title</a>
                </li>
                <li class="nav-item">
                  <a class="fs-14 lh-26 nav-link px-4" href="#">Topic title</a>
                </li>
              </ul>
            </div>
          </div>
        </nav>
    `);

    
    $('#settings-area').append(pageSettings);// settings

    // COURSE TITLE
    let course_title = $('.page-header-headings').children().first().html();
    $('.page-title').html(course_title);

    $('.dropdown-toggle').removeClass('dropdown-toggle')// remove toggle drop down; 
}    

if (window.location.pathname.includes('course/edit.php')) {
  console.log('course/edit.php details working');


  // breadcrumb
  let breadcrumb = $('#page-navbar').html();
  $('#page-header .col-12').html('');// remove page Header
  $('#page-header .col-12').html(`
    ${breadcrumb}
    <h1 class="mt-0 page-title" id="yui_3_17_2_1_1592907271894_200">Manage Courses</h1>
  `);//add page title
  $('#page-header .col-12').removeClass('pb-3');// remove class
  $('#page-header .col-12').removeClass('pt-3');// remove class

  let general = $('#id_general').children().last();

  // #FULLNAME
  let full_name = general.find('#fitem_id_fullname');
  full_name.parent().addClass('row'); // add row to parent
  full_name.removeClass('row');
  full_name.children().first().remove(); //remove col-3
  full_name.children().first().removeClass('col-md-9');//remove col-9
  full_name.children().first().children().first().addClass('col-12');//add class col-12
  full_name.children().first().children().first().attr('placeholder', 'Course name');// add place holder
  full_name.remove();// remove fullname

  // #SHORTNAME
  let short_name = general.find('#fitem_id_shortname');
  short_name.removeClass('row');
  short_name.children().first().remove(); //remove col-3
  short_name.children().first().removeClass('col-md-9');//remove col-9
  short_name.children().first().children().first().addClass('col-12');//add class col-12
  short_name.children().first().children().first().attr('placeholder', 'Short name');// add place holder
  short_name.remove();// remove short_name

  // #SHORTNAME
  let category = general.find('#fitem_id_category');
  category.removeClass('row');
  category.children().first().remove(); //remove col-3
  category.children().first().removeClass('col-md-9');//remove col-9
  category.children().first().children().first().addClass('col-12');//add class col-12
  category.children().first().children().first().attr('placeholder', 'Short name');// add place holder
  category.remove(); // remove category

  // #VISIBLE
  let visibility = general.find('#fitem_id_visible');
  visibility.removeClass('row');
  visibility.children().first().remove(); //remove col-3
  visibility.children().first().removeClass('col-md-9');//remove col-9
  visibility.children().first().children().first().addClass('col-12');//add class col-12
  visibility.remove(); // remove visibility

  // #COURSE START DATE
  let startdate = general.find('#fitem_id_startdate');
  startdate.removeClass('row');
  startdate.children().first().remove(); //remove col-3
  startdate.children().first().removeClass('col-md-9');//remove col-9
  startdate.children().first().children().first().addClass('col-12');//add class col-12
  startdate.remove(); // remove startdate

  // #COURSE END DATE
  let enddate = general.find('#fitem_id_enddate');
  enddate.removeClass('row');
  enddate.children().first().remove(); //remove col-3
  enddate.children().first().removeClass('col-md-9');//remove col-9
  enddate.children().first().children().first().addClass('col-12');//add class col-12
  enddate.remove(); // remove enddate

  // #COURSE ID
  let courseID = general.find('#fitem_id_idnumber');
  courseID.removeClass('row');
  courseID.children().first().remove(); //remove col-3
  courseID.children().first().removeClass('col-md-9');//remove col-9
  courseID.children().first().children().first().addClass('col-12');//add class col-12
  courseID.children().first().children().first().attr('placeholder', 'Course ID');// add place holder
  courseID.remove(); // remove courseID

  
  // DISPLAY INPUTS
  $('#id_general').find('.fcontainer').html(`
    <div class="col-12 col-sm-3 mb-4">
      ${full_name.html()}
    </div>
    <div class="col-12 col-sm-3 mb-4">
      ${category.html()}
    </div>
    <div class="col-12 col-sm-6 mb-4">
      ${startdate.html()}
    </div>

    <div class="col-12 col-sm-3 mb-2">
      ${short_name.html()}
    </div>
    <div class="col-12 col-sm-3 mb-2">
      ${visibility.html()}
    </div>
    <div class="col-12 col-sm-6 mb-2">
      ${enddate.html()}
    </div>
    <div class="col-4 mb-4">
      ${courseID.html()}
    </div>
  `);

  // REMOVE COLLAPSE ALL
  $('.collapsible-actions').hide();

  // CHANGE THE SUBTITLE BOTTOM PADDING
  $('#maincontent').next().removeClass('pb-4').addClass('pb-2');

  // ADD CUSTOM BOTTOM BUTTONS
  $('.body').append(`
    <div id="bottom-btn-container" class="d-flex align-items-center justify-content-between">
      <button id="scancel">
        <i class="fa-times-circle fas mr-2" style="color: #444444;font-size: 15px;"></i>
        CANCEL
      </button>
      <div>
        <button id="sreturn">
          <i class="fa-save fas mr-2" id="yui_3_17_2_1_1592885072117_797" style="color: #444444;"></i>
          SAVE AND RETURN
        </button>
        <button id="sdisplay">SAVE AND DISPLAY</button>
      </div>
    </div>`
  );

  $('#scancel').click(()=>{
    $('#id_cancel').trigger('click');
  });

  $('#sreturn').click(()=>{
    $('#id_saveandreturn').trigger('click');
  });
  
  $('#sdisplay').click(()=>{
    $('#id_saveanddisplay').trigger('click');
  });
  
  $('#fgroup_id_buttonar').hide();// HIDE BOTTOM DEFAULT BUTTON
}

if (window.location.pathname.includes('course/management.php')) {
console.log('course/management.php details working');

  let create_new_category_link = $('.listing-actions').children().first().attr('href');// get create new category link
  let create_new_course_link = $('.listing-actions').last().children().first().attr('href');// get create new course link

  $('.listing-actions').hide();// hide old category btn
  $('.coursecat-management-header').hide();// hide header

  let course_title = $('#course-listing-title').html();

  $('#course-listing-title').parent().prepend(
    `<h3 class="align-items-center py-3 bg-white card-header d-flex justify-content-between" id="category-listing-title" style="
      font-family: Arial;font-style: normal;font-weight: bold;font-size: 28px;line-height: 32px;color: #555555;">
      ${course_title}
        <a href="${create_new_course_link}" class="btn btn-primary waves-effect waves-light" style="padding: 10px;">Create New Course</a>
    </h3>`
  );// add new desin button of create new course
  $('#course-listing-title').hide();// hide old create new course button

  // breadcrumb
  let breadcrumb = $('#page-navbar').html();
  $('#page-header .col-12').html('');// remove page Header
  $('#page-header .col-12').html(`
    ${breadcrumb}
    <h1 class="mt-0 page-title" id="yui_3_17_2_1_1592907271894_200">Manage Courses</h1>
  `);//add page title
  $('#page-header .col-12').removeClass('pb-3');// remove class
  $('#page-header .col-12').removeClass('pt-3');// remove class

  $('#region-main').removeAttr('id'); // remove  container styles

  $('#category-listing').removeClass('px-3').addClass('px-0'); // remove category padding
  $('#course-listing').removeClass('px-3').addClass('px-0');// remove course padding

  $('#category-listing-title').hide(); //hide create category button
  $('#category-listing-title').parent().prepend(`
    <h3 class="align-items-center py-3 bg-white card-header d-flex justify-content-between" id="category-listing-title" style="
      font-family: Arial;
      font-style: normal;
      font-weight: bold;
      font-size: 28px;
      line-height: 32px;
      color: #555555;">
      Categories
        <a href="${create_new_category_link}" class="btn btn-primary waves-effect waves-light" style="padding: 10px;">Create New Category</a>
    </h3>
  `);// prepend new category button 

  $('.fa-arrows').attr('class', 'icon fas fa-arrows-alt fa-fw');// change the drag icon
} 


if (window.location.pathname.includes("course/editsection.php")) {
    let subtitle = $('.sub-title').last().html();

    // breadcrumb
    let breadcrumb = $('#page-navbar').html();
    $('#page-header .col-12').html('');// remove page Header
    $('#page-header .col-12').html(`
      ${breadcrumb}
      <h1 class="mt-0 page-title" id="yui_3_17_2_1_1592907271894_200">${subtitle}</h1>
    `);//add page title
    $('#page-header .col-12').removeClass('pb-3');// remove class
    $('#page-header .col-12').removeClass('pt-3');// remove class

    $('h2').hide();
    $('.collapsible-actions').hide();
}


if (window.location.pathname.includes("course/index.php")) {
  $('.courses').css({'display':'flex', 'flex-wrap' : 'wrap'}); // align course
  $('.content > .row').addClass('px-3');// add some padding

  // Page Header
  let subtitle = $('.sub-title').last().html().toLowerCase();
  let breadcrumb = $('#page-navbar').html();
  $('#page-header .col-12').html('');// remove page Header
  $('#page-header .col-12').html(`
    ${breadcrumb}
    <h1 class="mt-0 page-title" id="yui_3_17_2_1_1592907271894_200">${capitalize(subtitle)}</h1>
  `);//add page title
  $('#page-header .col-12').removeClass('pb-3');// remove class
  $('#page-header .col-12').removeClass('pt-3');// remove class

  
  $('.collapsible-actions').hide();// hide collapse
  $('#coursesearch').addClass('d-flex mb-5 justify-content-center'); // center search
}

if (window.location.pathname.includes("my/")) {
  
  let courseManagementLink = '/course/management.php';
  let is_admin = $('#is_admin').html();
  if (is_admin == 'true') {
    $('.body').append(`
      <div id="bottom-btn-container" class="d-flex align-items-center justify-content-between">
        <button id="scancel" class="invisible">
          <i class="fa-times-circle fas mr-2" style="color: #444444;font-size: 15px;"></i>
          CANCEL
        </button>
        <div>
          <a href="/bwsi2/course/management.php" id="sreturn">
            <i class="fa-graduation-cap fas mr-2" id="yui_3_17_2_1_1592885072117_797" style="color: #444444;"></i>
            MANAGE COURSES
          </a>
          <a  href="/bwsi2/course/edit.php" id="sdisplay">ADD COURSE</a>
        </div>
      </div>`
    );
  }

}

// HOMEPAGE
let url = window.location.pathname.split('/');
if(!url[2]){
  $('#page-header').hide();
  $('.collapsible-actions').hide();// hide collapse
  $('#region-main-box').prepend(`<h1 class="mt-0 page-title" id="yui_3_17_2_1_1592907271894_200">${capitalize('Home')}</h1>`);
}

function capitalize(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}