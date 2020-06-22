
if (window.location.pathname == "/guru/course/view.php") {

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
    
    let topics = [];
    
    let course_topics = $('.custom-topics').children();
    
    $.each(course_topics, function( index, value ) {
        let title = $(this).find('.sectionname').html();
        let summary = $(this).find('.summary').html()
    
        topics.push({
            title,summary
        }); 
    });// get topic name and summary
    
    $('.course-content').html(`
        <div class="col-8">
            
        </div>
        <div class="col-4 border-left">
            <div class="align-items-center border-bottom d-flex font-bold fs-20 lh-23 px-3 py-3">
                <span>Course Content</span>
            </div>
        </div>
    
    `);// delete page content
    $('.course-content').addClass('row');// add row to align layout 
    
    let courses = [];
    $.each(topics, function (index, value) {
        // console.log(value.title);
        // console.log(value.summary);
    
        $('.course-content').children().last().append(`
            <div id="${index}" class="course-topic align-items-center d-flex fs-14 lh-23 py-3" style="padding-left: 37px;">
                <div class="form-check pl-0">
                <input type="checkbox" class="form-check-input" id="materialUnchecked${index}">
                <label class="form-check-label fs-14 lh-20" for="materialUnchecked${index}">${value.title}</label>
                </div><!-- Material Unchecked -->
            </div>
        `);
    });// displays all checkbox on the right side
    
    
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
                <ul class="breadcrumbs mb-1" style="padding-top: 27.5px!important;margin-top: -26px;">
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
                <!-- <li class="custom-item active">
                  <a href="" class="custom-link active">Overview</a>
                </li>
                <li class="custom-item">
                  <a href="" class="custom-link">section title</a>
                </li> -->
                <li class="nav-item active">
                  <a class="nav-link active" href="#">Overview</a>
                </li>
                <li class="nav-item">
                  <a class="fs-14 lh-26 nav-link px-4" href="#">Section title</a>
                </li>
                <li class="nav-item">
                  <a class="fs-14 lh-26 nav-link px-4" href="#">Topic title</a>
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


    // COURSE TITLE
    let course_title = $('.page-header-headings').children().first().html();
    $('.page-title').html(course_title);
}    

