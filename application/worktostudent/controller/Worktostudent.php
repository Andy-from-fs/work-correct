<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/6
 * Time: 11:37
 */

namespace app\worktostudent\controller;

use app\worktostudent\model\Worktostudent as Model;
use think\Controller;
use think\Request;

class Worktostudent extends Controller
{
  public function view_correctWorkForTea()
  {
    return $this->fetch();
  }

  public function view_finishWorkForStu()
  {
    return $this->fetch();
  }

  //后台下载文件通用方法
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

  public function queryworkidbystuid($studentid)
  {
    $worktostudent = Model::all('studentid');
    return $worktostudent->workid;
  }

  public function queryWorktostudentByWorkIdAndStudentId()
  {
//    $workId=1;
//    $studentId=1;
    $workId = input('post.workId');
    $studentId = input('post.studentId');
    $worktostudent = Model::all([
      'workid' => $workId,
      'studentid' => $studentId
    ]);
    return json($worktostudent);
  }

  public function queryWorkByWorkId()
  {
//    $workid = 1;
    $workid = input('post.workId');
    $work = \model('work/Work')::get($workid);
    return json($work);
  }

  //TODO:后台完成作业上传
  public function finishWork_Upload(Request $request)
  {
    $document = $request->file("document");
    $workToStuId = $request->post("workToStuId");
    $documentName = $request->post("documentName");
    $documentExtension = $request->post("documentExtension");
    $workToStudent = Model::get($workToStuId);
    $result = ["message" => "",];
    if (empty($document)) {
      $this->error("上传文件为空。");
      $result["message"] = "上传文件为空。";
      return json($result);
    } else {
      $info = $document->move(ROOT_PATH . 'public' . DS . 'homework');
      if ($info) {
        $result["message"] = "上传文件至" . $info->getRealPath();
        $workToStudent->document = $info->getRealPath();
        $workToStudent->documentname = $documentName;
        $workToStudent->documentextension = $documentExtension;
        if ($workToStudent->save()) {
          $result["message"] .= "更新数据库成功";
          return json($result);
        } else {
          $result["message"] .= "更新数据库失败";
          return json($result);
        }
      } else {
        $this->error($workToStudent->getError());
        $result["message"] = ("上传失败" . $workToStudent->getError());
        return json($result);
      }
      return json($result);
    }
  }

  //TODO:后台修改作业上传
  public function UpdateWork_Upload(Request $request)
  {
    $document = $request->file("document");
    $workToStuId = $request->post("workToStuId");
    $documentName = $request->post("documentName");
    $documentExtension = $request->post("documentExtension");
    $workToStudent = Model::get($workToStuId);
    $result = "";
    if (empty($document)) {
      $this->error("上传文件为空。");
      return json($result = "上传文件为空。");
    } else {
      if (file_exists($workToStudent->document)) {
        if (unlink($workToStudent->document)) {
          $result = "后台删除源文件成功，";
        }
      } else {
        $result = "后台未发现源文件，";
      }
      $info = $document->move(ROOT_PATH . 'public' . DS . 'homework');
      if ($info) {
        $result = "上传文件至" . $info->getRealPath();
        $workToStudent->document = $info->getRealPath();
        $workToStudent->documentname = $documentName;
        $workToStudent->documentextension = $documentExtension;
        if ($workToStudent->save()) {
          return $result .= "更新数据库成功";
        } else {
          return $result .= "更新数据库失败";
        }
      } else {
        $this->error($workToStudent->getError());
        return $result = ("上传失败" . $workToStudent->getError());
      }
      return json($result);
    }
  }

  //TODO:后台查询WorktoStudent根据workId
  public function queryWorktostudentAndStudentByWorkId()
  {
//    $workId = 1;
    $workId=input("post.workId");
    $workToStudent = Model::where(['workid' => $workId])
      ->order("studentid asc")
      ->select();


    $studentId = [];
    foreach ($workToStudent as $item) {
      array_push($studentId, $item["studentid"]);
    }
    $students = \model("admin/Admin")
      ->where(["userid" => ["IN", $studentId]])
      ->order("userid asc")
      ->select();
    return json([
      "workToStudent" => $workToStudent,
      "students" => $students
    ]);
  }

  //TODO:后台下载作业文件
  public function downloadDocument()
  {
    $worktostuid = input('post.id');
    $this->download_file(Model::get($worktostuid)->document);
  }

  //TODO:后台教师批改作业（包括修改）
  public function correctWork(Request $request)
  {
    $workToStuId = $request->post("workToStuId");
    $message = $request->post("message");
    $grade = $request->post("grade");
    $hasDocument = $request->post("hasDocument");
    $workToStu = Model::get($workToStuId);
    $result = [
      "isUpdate" => false,
      "info" => ""
    ];
    if ($workToStu->grade !== null) {
      $result["isUpdate"] = true;
    }
    if ($hasDocument) {
      $document = $request->file("document");
      $documentName = $request->post("documentName");
      $documentExtension = $request->post("documentExtension");
      if (file_exists($workToStu->document)) {
        if (unlink($workToStu->document)) {
          $result["info"] = "后台删除源文件成功,";
        }
      } else {
        $result["info"] = "后台未发现源文件，";
      }
      $info = $document->move(ROOT_PATH . "public" . DS . "homework");
      if ($info) {
        $workToStu->document = $info->getRealPath();
        $workToStu->documentname = $documentName;
        $workToStu->documentextension = $documentExtension;
        $result["info"] .= "文件成功上传至" . $info->getRealPath();
      } else {
        $this->error($workToStu->getError());
        $result["info"] .= ("上传文件失败" . $workToStu->getError());
      }
    }
    $workToStu->message = $message;
    $workToStu->grade = $grade;
    if($workToStu->save()){
      $result["info"] .= "数据库更新成功";
    } else {
      $result["info"] .= "更新数据库失败";
    }
    return json($result);
  }
}