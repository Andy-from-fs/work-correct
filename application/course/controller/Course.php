<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/5
 * Time: 15:15
 */

namespace app\course\controller;

use app\course\model\Course as Model;
use think\Controller;
use think\Request;

class Course extends Controller
{
  public function view_add()
  {
    return $this->fetch();
  }

  public function view_queryForStudents()
  {
    return $this->fetch();
  }

  public function view_queryForTeacher()
  {
    return $this->fetch();
  }

  //TODO:后台教师查询课程
  public function queryCourseFortea(Request $request)
  {
    $teacherId = $request->post('teacherid');
    $course = Model::all(['teacherid' => $teacherId]);
    return json($course);
  }

  //TODO:后台教师创建课程页面-加载所有学生
  public function loadAllStudents()
  {
    return json($students = \model("admin/Admin")::all(["identity" => "学生"]));
  }

  //TODO:后台教师创建课程页面-创建课程
  public function createCourse()
  {
    $teacherId = input("post.teacherId");
    $className = input("post.className");
    $introduce = input("post.introduce");
    $studentsId = explode(",", input("post.studentsId"));;
    //操作Course表的添加
    $newCourse = new Model();
    $newCourse->classname = $className;
    $newCourse->introduce = $introduce;
    $newCourse->teacherid = $teacherId;
    if ($newCourse->save()) {
      $courseId = $newCourse->courseid;

      //操作selectcourse表的添加
      $List = [];
      for ($i = 0; $i < count($studentsId); $i++) {
        array_push($List, [
          "studentid" => $studentsId[$i],
          "courseid" => $courseId
        ]);
      }
      $selectCourse = model("selectcourse/Selectcourse");
      if ($selectCourse->saveAll($List)) {
        return "创建课程成功，并成功添加学生进入";
      } else {
        return "创建课程成功，但添加学生进入失败";
      }
    } else {
      return "创建课程失败";
    }
  }

  //TODO:后台教师查询课程页面-加载课程内学生
  public function loadStudents(Request $request)
  {
    $inputCourseId = $request->post('courseId');
    $studentId = model('selectcourse/Selectcourse')->where('courseid', $inputCourseId)->column('studentid');
    $students = Array();
    foreach ($studentId as $item) {
      $student = model('admin/Admin')->All(['userid' => $item]);
      array_push($students, $student);
    }
    return json($students);
  }

  //TODO:后台教师更新课程基本信息
  public function updateCourse()
  {
    $courseId = input("post.courseid");
    $className = input("post.classname");
    $introduce = input("post.introduce");
    $course = Model::get($courseId);
    $responseString = '更新course成功，内容';

    if ($className == $course->classname && $introduce == $course->introduce) {
      return array("result" => false, "message" => "课程信息没变，不需要修改");

    } else {
      if ($className != $course->classname) {
        $ex_className = $course->classname;
        $course->classname = $className;
        $responseString = $responseString . '课程名称由' . $ex_className . '改为' . $course->classname;
      }
      if ($introduce != $course->introduce) {
        $ex_introduce = $course->introduce;
        $course->introduce = $introduce;
        $responseString = $responseString . '课程介绍由' . $ex_introduce . '改为' . $course->introduce;
      }
      if ($course->save()) {
        return array("result" => true, "message" => $responseString);

      } else {
        return array("result" => false, "message" => $course->getError());
      }
    }
  }

  //TODO:后台查询未在课程内的学生
  public function queryStudentsExpectAdded()
  {
    $hasAddedId = Array();
    $courseId = input("post.courseId");
    $hasadded = db("selectcourse")
      ->where("courseid", "=", $courseId)
      ->field('studentid')
      ->select();
    for ($i = 0; $i < count($hasadded); $i++) {
      array_push($hasAddedId, $hasadded[$i]["studentid"]);
    }
    if (count($hasAddedId) > 0) {
      return
        db("admin")
          ->where([
            'identity' => '学生',
            '	userid' => ['NOTIN', $hasAddedId]
          ])
          ->order('userid asc')
          ->select();
    } else {
      return
        db("admin")
          ->where(['identity' => '学生'])
          ->order('userid asc')
          ->select();
    }
  }

  //TODO:后台添加学生进入课程
  public function addStudents()
  {
    $data = input('post.');
    $courseId = $data['courseId'];
    $studentsString = $data['addStudentsId'];
    $students = explode(",", $studentsString);
    $List = [];
    for ($i = 0; $i < count($students); $i++) {
      $item = array(
        'courseid' => $courseId,
        'studentid' => $students[$i]
      );
      array_push($List, $item);
    }
    $selectcourse = model('selectcourse/Selectcourse')->saveAll($List);
    return $selectcourse;
  }

  //TODO:后台删除课程内学生
  public function delStu()
  {
    $data = input('post.');
    $delStuId = $data['delStuId'];
    $courseId = $data['courseId'];
    $n = 0;
    for ($i = 0; $i < count($delStuId); $i++) {
      if (db('selectcourse')
          ->where('studentid', $delStuId[$i])
          ->where('courseid', $courseId)
          ->delete() > 0
      ) {
        $n++;
      }
    }
    return $n;
  }

  //TODO:后台删除课程
  public function delCourse()
  {
    $courseId = input('post.courseId');
    $result = [
      'state' => 0,
      'number' => 0,
    ];

    $del = Model::get($courseId);

//    print_r($del."||".gettype($del));
    if ($del->delete()) {
      $result['state'] = 1;
      $num = model('selectcourse/Selectcourse')
        ->where("courseid", '=', $courseId)
        ->delete();
      $result["number"] += $num;
    } else {
      $result['state'] = 0;
    }
    return $result;
  }

  //TODO:后台学生查询课程
  public function queryCourseForStu()
  {
//    $studentId = 1;
    $studentId = input("post.studentId");
    $courseId = \db("selectcourse")
      ->where("studentid", "=", $studentId)
      ->field("courseid")
      ->select();
    $courseid = [];
    foreach ($courseId as $item) {
      array_push($courseid, $item["courseid"]);
    }
    $course = Model::all($courseid);
    return json($course);
  }


}