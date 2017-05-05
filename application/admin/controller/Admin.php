<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/5
 * Time: 12:39
 */

namespace app\admin\controller;

use app\admin\model\Admin as Model;
use think\Controller;
use think\Request;

class Admin extends Controller
{
  public function index()
  {
    return $this->fetch();
  }

  public function register()//注册界面入口
  {
    return $this->fetch();
  }

  public function queryuserbytel(Request $request)
  {
    $input_tel = $request->post('Telephone');
    $User = Model::get(['telephone' => $input_tel]);
    $result = Array('username' => $User->username,
      'userid' => $User->userid,
      'identity' => $User->identity,
      'telephone' => $User->telephone,
      'password' => $User->password);
    return json($result);
  }

  public function querywork_stu(Request $request)
  {
    $input_studentid = $request->post('studentid');
    $workid = db('worktostudent')
      ->where('studentid', $input_studentid)
      ->whereNull("document")
      ->field('workid')
      ->select();
    $result = array();
    for ($i = 0; $i < count($workid); $i++) {
      $work = model('work/Work')->All(['workid' => $workid[$i]['workid']]);
      array_push($result, $work);
    }
    return $result;
  }


  public function querywork_tea()
  {
//    $input_teacherid = input('get.teacherid');
    $input_teacherid = input('post.teacherid');
    $worktostudent = \model('worktostudent/Worktostudent')
      ->whereNull('grade')
      ->whereNotNull('document')
      ->field('workid')
      ->select();
    $workId = [];
    for ($i = 0; $i < count($worktostudent); $i++) {
      array_push($workId, $worktostudent[$i]["workid"]);
    }

    $work = model('work/Work')->All([
      'workid' => ['IN', $workId],
      'teacherid' => $input_teacherid
    ]);
    return json($work);
  }

  public function add(Request $request)//注册添加用户
  {
    $username = $request->post('username');
    $identity = $request->post('identity');
    $telephone = $request->post('telephone');
    $password = $request->post('password');
    $admin = new Model;
    $admin->username = $username;
    $admin->identity = $identity;
    $admin->telephone = $telephone;
    $admin->password = $password;
    if ($admin->save()) {
      return Array("message" => "注册成功", "status" => 1,
        "userid" => $admin->userid,
        "username" => $admin->username,
        "identity" => $admin->identity);
    } else {
      return Array("message" => "注册失败", "status" => 0);
    }

  }

  public function updateAdminInfo()
  {
//    $userId = 1;
    $userId = input('post.userId');
//    $password = 123;
    $password = input('post.password');
    $admin = Model::get($userId);
    if ($admin->password == $password) {
//      $name = "And";
//      $telephone = 12345678901;
      $telephone = input('post.telephone');
      $name = input('post.name');
      $result = [
        "message" => "",
        "state" => 0
      ];
      if ($name != null) {
        $admin->username = $name;
        $result['message'] = "成功更新用户名，";
      }
      if ($telephone != null) {
        $admin->telephone = $telephone;
        $result['message'] .= "成功更新手机号，";
      }
      if ($admin->save()) {
        $result["state"] = 1;
        $result['message'] .= "并写入数据库。";
        $result["name"] = $admin->username;
      }
      return json($result);
    } else {
      return json([
        "message" => "密码错误",
        "state" => 0
      ]);
    }

  }

  public function checkTel()
  {
    $tel = db('admin')->field("telephone")->select();
    $result = [];
    foreach ($tel as $item) {
      array_push($result, $item["telephone"]);
    }
    return json($result);
  }

  public function updateAdminPwd()
  {
    $pwd=input("post.pwd");
    $userId=input("post.userId");
    $expwd=input("post.expwd");
    $admin=Model::get($userId);
    if($admin->password===$expwd){
      $admin->password=$pwd;
      if ($admin->save()){
        return json("修改密码成功.");
      } else{
        return json($admin->getError());
      }
    }else{
      return json("原密码错误.");
    }
  }
}