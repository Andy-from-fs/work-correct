/**
 * Created by Administrator on 2017/3/3.
 */
//导航栏按钮跳转方法
function navJump(address) {
  $('#welcome_p').addClass("fade");
  $('#show').removeClass("fade in").addClass("fade");
  setTimeout(() => {
    $('#welcome_p').hide();
    $('#show').hide();
  }, 400);
  setTimeout(() => {
    $('#show').attr('src', address);
  }, 200);
  setTimeout(() => {
    $('#show').show();
    $('#show').removeClass('fade').addClass("fade in");
  }, 400)
}

//TODO:侧边栏
$(() => {
  var thisTime;
  $('.nav-ul li').mouseleave(function (even) {
    thisTime = setTimeout(thisMouseOut, 1000);
  })

  $('.nav-ul li').mouseenter(function () {
    clearTimeout(thisTime);
    var thisUB = $('.nav-ul li').index($(this));
    if ($.trim($('.nav-slide-o').eq(thisUB).html()) != "") {
      $('.nav-slide').addClass('hover');
      $('.nav-slide-o').hide();
      $('.nav-slide-o').eq(thisUB).show();
    }
    else {
      $('.nav-slide').removeClass('hover');
    }

  })

  function thisMouseOut() {
    $('.nav-slide').removeClass('hover');
  }

  $('.nav-slide').mouseenter(function () {
    clearTimeout(thisTime);
    $('.nav-slide').addClass('hover');
  })
  $('.nav-slide').mouseleave(function () {
    $('.nav-slide').removeClass('hover');
  })

})

//TODO:注册功能
$('#btn_register').click(() => {
  $('#show').attr('src', 'http://www.work.com/admin/admin/register');
  $('#welcome_p').hide();
  $('#show').show();
});

// TODO:导航栏按钮-布置作业
$('#btn-add-work').click(() => {// 布置作业
  if (localStorage.getItem('identity') === '教师') {
    navJump('http://www.work.com/work/work/view_add');
  }
});

// TODO:导航栏按钮-创建课程
$('#btn_add_course').click(() => {//添加课程
  if (localStorage.getItem('identity') === '教师') {
    navJump('http://www.work.com/course/course/view_add');
  }
});

//TODO:导航栏按钮-我的课程
$('#btn_Querycourse').click(() => {
  if (localStorage.getItem('identity') === '学生') {
    navJump('http://www.work.com/course/course/view_queryForStudents');
  }
  if (localStorage.getItem('identity') === '教师') {
    navJump('http://www.work.com/course/course/view_queryForTeacher');
  }
});

//TODO:导航栏按钮-我的作业
$('#btn-query-work').click(() => {
  const identity = localStorage.getItem("identity")
  if (identity === "学生") {
    navJump('http://www.work.com/work/work/view_queryForStu');
  }
  if (identity === "教师") {
    navJump('http://www.work.com/work/work/view_queryForTea');
  }
});

//导航栏需要处理的作业通用方法  WorkOrCourse(0为课程，1为作业) identity（0为学生，1为教师）
function jumpTodo(Id, workOrCourse, identity) {
  if (identity === 0) {
    if (workOrCourse === 0) {
    } else if (workOrCourse === 1) {
      navJump('http://www.work.com/worktostudent/worktostudent/view_finishWorkForStu?workId=' + Id);
    } else {
    }
  } else if (identity === 1) {
    if (workOrCourse === 0) {
    } else if (workOrCourse === 1) {
      navJump("http://www.work.com/worktostudent/worktostudent/view_correctWorkForTea?workId=" + Id);
    } else {
    }
  } else {
  }
}







