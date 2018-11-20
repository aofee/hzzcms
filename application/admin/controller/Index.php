<?php
namespace app\admin\controller;

use think\Controller;
use think\Cookie;
use think\Request;

class Index extends Controller
{
    /**
     * 初始化操作 验证登录
     * @access public
     */
    public function _initialize()
    {
        if (!Cookie::has('token')){
            $this->redirect('/admin/login/index',302,'','');
        }else{
            $token = Cookie::get('token');
            $userData = db('user')->where('token',$token)->find();
            if ($token != $userData['token']){
                Cookie::set('token',null);
                $this->redirect('/admin/login/index',302);
            }
        }
    }
    /**
     * 菜单结构输出
     * @access protected
     */
    protected function menu(){
        $request = Request::instance();
        $menu = '/admin/index/'.$request->action();
        $where['url'] = ['like',"%$menu%"];
        $menuID = db('menu')->where($where)->cache(true,1800)->find();
        $menu = db('menu')->where(['state'=>1,'show'=>1])->select();
        for ($n=0;$n<count($menu);$n++){
            if (strpos($menu[$n]['url'],',')){
                $listurl = explode(',',$menu[$n]['url']);
                $menu[$n]['url'] = $listurl[0];
            }
            if ($menu[$n]['father'] == 0){
                $fatherMenu[$menu[$n]['id']] = $menu[$n];
                if ($menuID['father'] == $menu[$n]['id']){
                    $fatherMenu[$menu[$n]['id']]['check'] = 1;
                }else{
                    $fatherMenu[$menu[$n]['id']]['check'] = 0;
                }
            }else{
                $fatherMenu[$menu[$n]['father']]['son'][$menu[$n]['id']] = $menu[$n];
                if ($menuID['id'] == $menu[$n]['id']){
                    $fatherMenu[$menu[$n]['father']]['son'][$menu[$n]['id']]['check'] = 1;
                }else{
                    $fatherMenu[$menu[$n]['father']]['son'][$menu[$n]['id']]['check'] = 0;
                }
            }
        }
        return $fatherMenu;
    }

    protected function getUser(){
        $token = Cookie::get('token');
        $user = db('user')->where('token',$token)->field('nickname')->find();
        return $user;
    }
    public function index()
    {
        $data['host'] = $_SERVER['SERVER_NAME'];
        $data['software'] = $_SERVER['SERVER_SOFTWARE'];
        $data['serip'] = $_SERVER['SERVER_ADDR'];
        $data['time'] = $_SERVER['REQUEST_TIME'];
        $user = $this->getUser();
        $fatherMenu = $this->menu();
        return view('index',[
            'data' =>$data,
            'menu'=>'index',
            'user'=>$user,
            'fatherMenu'=>$fatherMenu,
        ]);
    }
    public function welcome(){
        return view('welcome');
    }
    public function column_list(){
        $data = db('column')->where(['state'=>1,'istop'=>1])->order('weight desc')->select();
        return view('column_list',[
            'data'=>$data,
        ]);
    }
    public function demo(){
        $var = (string) 99;
        $int = (object) 89;

        print_r(gettype($var));
//        $demo = $var + $int;
        print_r($int);
    }

    /**
     *
     */





    public function column_edit(){
        return view('column-edit');
    }
    public function model_list(){
        return view('model-list');
    }
    public function model_add(){
        return view('model-add');
    }
    public function column_add(){
        return view('column-add');
    }
    public function model_edit(){
        return view('model-edit',[

        ]);
    }
    public function loginout(){
        Cookie::set('token',null);
        $this->redirect('/admin/login/index');
    }
}
