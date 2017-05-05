<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6
 * Time: 10:25
 */

namespace app\work\controller;

use app\work\model\Work as Model;
use think\Controller;
use think\Request;

class Work extends Controller
{
  public function view_add()
  {
    return $this->fetch();
  }

  public function view_queryForTea()
  {
    return $this->fetch();
  }

  public function view_queryForStu()
  {
    return $this->fetch();
  }

  public function queryWorkByWorkId()
  {
    return json(Model::get(input('post.workId')));
//    return json(Model::get(1));
  }

  public function querycoursebyteacherid()
  {
    $teacherid = input('post.teacherid');
    $course = model('course/Course')->All(['teacherid' => $teacherid]);
    return json($course);
  }


//TODO:后台添加作业，操作work表，worktostudent表
  public function addwork_TableWork_worktostudent(Request $request)
  {
    $answer = $request->file('answer');
    $workname = $request->post('workname');
    $content = $request->post('content');
    $answername = $request->post('answername');
    $courseid = $request->post('courseid');
    $teacherid = $request->post('teacherid');
    $answerExtension = $request->post('answerextension');
    //上传文件
    if (empty($answer)) {
      $this->error("上传文件为空。");
      return json($result = "上传文件为空。");
    } else {
      $info = $answer->move(ROOT_PATH . 'public' . DS . 'answer');
      if ($info) {
        //添加work表
        $Work = new Model();
        $Work->workname = $workname;
        $Work->content = $content;
        $Work->answername = $answername;
        $Work->teacherid = $teacherid;
        $Work->answer = $info->getRealPath();
        $Work->answerextension = $answerExtension;
        if ($Work->save()) {
          //添加worktostudent表
          $workid = $Work->workid;
          $studentid = db('selectcourse')->distinct(true)->field('studentid')->where('courseid', $courseid)->select();
          $list = Array();
          foreach ($studentid as $item) {
            array_push($list, ['studentid' => $item['studentid'], 'workid' => $workid]);
          };
          $add = db('worktostudent')->insertAll($list);
          if ($add > 0) {
            $result = array('message' => '添加成功', 'status' => 1);
            return json($result);
          } else {
            $result = array('message' => '表selectcourse添加失败', 'status' => -2);
            return json($result);
          }
        } else {
          $resutl = array('message' => $Work->getError(),
            'status' => 0);
          return json($resutl);
        }
      } else {
        $resutl = array('message' => "" . $answer->getError(),
          'status' => -1);
        return json($resutl);
      }
    }
  }

//TODO:后台下载文件通用方法
  public function download_file($file)
  {
    if (is_file($file)) {
      $length = filesize($file);
      $type = mime_content_type($file);
      $showname = ltrim(strrchr($file, '/'), '/');
      header("Content-Description: File Transfer");
      header(' Content-type: ' . $type);
      header('Content-Length:' . $length);
      if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
        header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
      } else {
        header('Content-Disposition: attachment; filename="' . $showname . '"');
      }
      readfile($file);
      exit;
    } else {
      exit('文件已被删除！');
    }
  }


//TODO:后台老师查询作业
  public function queryWorkForTea()
  {
//    $teacherId = 2;
    $teacherId = input("post.teacherId");
    $work = Model::all(["teacherid" => $teacherId]);
    return json($work);
  }

//TODO:后台下载作业答案
  public function downloadAnswer()
  {
    $workid = input('post.workId');
    $this->download_file(Model::get($workid)->answer);
  }

//TODO:后台教师更新作业信息
  public function updateWork()
  {

    $workId = input("post.workId");
    $workName = input("post.workName");
    $content = input("post.content");

//    $workId = 7;
//    $workName = "test+2";
//    $content = "test+2";

    $work = Model::get($workId);
    $responseString = "更新work内容，内容";
    if ($workName == $work->workname && $content == $work->content) {
      return json(array("result" => false, "message" => "作业信息没变，不需要修改"));
    } else {
      if ($workName != $work->workname) {
        $ex_workname = $work->workname;
        $work->workname = $workName;
        $responseString = $responseString . '课程名称由' . $ex_workname . '改为' . $work->workname;
      }
      if ($content != $work->content) {
        $ex_content = $work->content;
        $work->content = $content;
        $responseString = $responseString . '课程介绍由' . $ex_content . '改为' . $work->content;
      }
      if ($work->save()) {
        return json(array("result" => true, "message" => $responseString));

      } else {
        return json(array("result" => false, "message" => $work->getError()));
      }
    }
  }

  //TODO:后台教师更新作业答案
  public function answerUpdate(Request $request)
  {
    $wordId = $request->post("wordId");
    $answer = $request->file("answer");
    $answerName = $request->post("answerName");
    $answerExtension = $request->post("answerExtension");
    $work = Model::get($wordId);
    $result = "";
    if (empty($answer)) {
      $this->error("上传文件为空。");
      $result = "上传文件为空。";
      return json($result);
    } else {
      if (file_exists($work->answer)) {
        if (unlink($work->answer)) {
          $result .= "后台删除源文件成功，";
        }
      } else {
        $result .= "后台未发现源文件，";
      }
      $info = $answer->move(ROOT_PATH . 'public' . DS . 'answer');
      if ($info) {
        $work->answername = $answerName;
        $work->answer = $info->getRealPath();
        $work->answerextension = $answerExtension;
        if ($work->save()) {
          return $result .= ("更新数据表成功，并上传文件至" . $info->getRealPath());
        } else {
          return $result .= ("更新数据库失败，上传至" . $info->getRealPath());
        }
      } else {
        $this->error($work->getError());
        return $result = ("上传失败" . $work->getError());
      }
      return json($result);
    }
  }

  //TODO:后台教师删除作业
  public function delWork()
  {
//    $workId = 11;
    $workId = input('post.workId');
    $work = Model::get($workId);

    $result = array(
      'state' => 0,
      'number' => 0,
      'delFile' => 0,
    );
    if (unlink($work->answer)) {
      $result['delFile']++;
    }
    $result['number'] += Model::destroy($workId);
    if ($result['number'] > 0) {
      $worktostudent = model('worktostudent/Worktostudent')
        ->where("workid", '=', $workId)
        ->select();
      foreach ($worktostudent as $item){
        if(file_exists($item->document)){
          if(unlink($item->document)){
            $result["delFile"]+=1;
          }
        }
      }
      $num= model('worktostudent/Worktostudent')
        ->where("workid", '=', $workId)
        ->delete();
      if ($num > 0) {
        $result['number'] += $num;
        $result['state'] = 1;
      } else {
        $result['state'] = 2;
      }
    } else {
      $result['state'] = 0;
    }
    return json($result);
  }

  //TODO:后台教师查询学生完成作业情况
  public function loadStuForTea()
  {
//    $workId=1;
    $workId = input("post.workId");
    $workToStu = \model("worktostudent/Worktostudent")
      ->where('workid', $workId)
      ->order('studentid asc')
      ->select();
    $stuId = [];
    for ($i = 0; $i < count($workToStu); $i++) {
      array_push($stuId, $workToStu[$i]["studentid"]);
    }
    $admin = \model('admin/Admin')
      ->where(["userid" => ["IN", $stuId]])
      ->order("userid asc")
      ->select();
    $result = [];
    for ($i = 0; $i < count($admin); $i++) {
      if ($admin[$i]["userid"] == $workToStu[$i]["studentid"]) {
        array_push($result, [
          "studentid" => $admin[$i]["userid"],
          "name" => $admin[$i]["username"],
          "telephone" => $admin[$i]["telephone"],
          "grade" => $workToStu[$i]["grade"]
        ]);
      }
    }
    return json($result);

  }

  //TODO:后台学生查询作业
  public function queryWorkForStu()
  {
    $studentId = 4;
//    $studentId=input("post.studentId");
    $worktostudent = \model("worktostudent/Worktostudent")
      ->where("studentid", $studentId)
      ->order("workid asc")
      ->select();
//    return json($worktostudent);
    $workIds = [];
    foreach ($worktostudent as $item) {
      array_push($workIds, $item['workid']);
    }
//    return json($workIds);
    $work = Model::
    where("workid", "IN", $workIds)
      ->order("workid asc")
      ->select();
    return json($result = [
      "worktostudent" => $worktostudent,
      "work" => $work
    ]);
  }

  public function queryAnswerNameExtension(){
    $workId=input('post.workId');
    $work=Model::get($workId);
    return json([
      "answerName"=>$work->answername,
      "answerExtension"=>$work->answerextension
    ]);
  }
}