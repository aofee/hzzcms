<?php
/**
 * Created by PhpStorm.
 * User: aoxiaofei
 * Date: 2018/11/8
 * Time: 19:37
 */
namespace app\admin\controller;

use think\Controller;
use think\Cookie;


class Login extends Controller{
    public function index(){
        if (Cookie::has('token')){
            $this->redirect('/admin/index/index',302);
        }
        return view('login');
    }
    public function login(){
        if (request()->isPost()){
            $loginData = input('post.');
            $userData = db('user')->where(['username'=>$loginData['u'],'state'=>'1'])->find();
            if ($userData){
                if ($userData['password'] == md5($loginData['p'])){
                    $username= $userData['username'];
                    $loginip = request()->ip();
                    $substr = $this->getstr(12);
                    $token = md5($username.$loginip.$substr);
                    db('user')->where('username',$userData['username'])->update(['logintime'=>time(),'token'=>$token,'loginip'=>$loginip]);
                    if (!isset($loginData['online'])){
                        Cookie::set('token',$token,7200);
                    }else{
                        Cookie::set('token',$token,24*60*60*7);
                    }
                    $this->redirect('/admin/index/index');
                }else{
                    $this->error('用户名或密码错误');
                }
            }else{
                $this->error('用户名不存在');
            }
        }else{
            $this->error('非法访问！');
        }
    }
    public function getstr($length){
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for($i = 0;$i < $length;$i++){
            $str .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $str;
    }
    public function wk(){
        $tourl = "http://www.blpack.com/post1.php";
        $data = array("usrname"=>"078687831","usrpass"=>"150191","docinfo"=>"https://wenku.baidu.com/view/81787fbcdb38376baf1ffc4ffe4733687e21fcd8","taskid"=>"up_down_doc1");
        $respose = $this->curl_post($tourl,$data);
        $res = explode('id=',$respose);
        $downurl = "http://www.blpack.com/downcode.php";
        $downdata = array("vcodeid"=>$res[1],"taskid"=>"directDown");
        $resposetwo = $this->curl_post($downurl,$downdata);
        $newdata = explode('----',$resposetwo);
        $caifu = $newdata[1];
        $resquestolddata = explode('/',$newdata[0]);
        $resquesturl = base64_decode($resquestolddata[0].'=');
        $resquestdata = base64_decode($resquestolddata[1]);
        print_r($resquesturl.'?'.$resquestdata);
    }
    public function curl_post($url,$data){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,FALSE);
        if(!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}