<?php
/**
 * Created by PhpStorm.
 * User: aoxiaofei
 * Date: 2018/11/14
 * Time: 17:13
 */
namespace app\admin\controller;

use think\Controller;
use think\Cookie;
use think\Request;
class Api extends Controller
{
    /**
     * 初始化操作 验证登录
     * @access public
     */
    public function _initialize()
    {
        if (!Cookie::has('token')){
            $message = ['status'=>-1,'msg'=>'You don\'t have permission to access.'];
            return $message;
            die();
        }else{
            $token = Cookie::get('token');
            $userData = db('user')->where('token',$token)->find();
            if ($token != $userData['token']){
                Cookie::set('token',null);
                $message = ['status'=>-1,'msg'=>'You don\'t have permission to access.'];
                return $message;
                die();
            }
        }
    }

    /**
     * @return \think\response\Json
     */
    public function column(){
        $columnData = [];
        $field = 'id,name,key,title,weight,type';
        $getColumn = db('column')->where(['state'=>1,'istop'=>1])->order('weight desc')->field($field)->select();
        foreach ($getColumn as $key=>$value){
            $columnData[$key] = $value;
            $getSonColumn = db('column')->where(['state'=>1,'father_key'=>$value['key'],'father_id'=>$value['id'],'type'=>['<','4']])->field($field)->order('weight desc')->select();
            if (count($getSonColumn) == 0){
                $getSonModule = db('column')->where(['state'=>1,'father_key'=>$value['key'],'father_id'=>$value['id'],'type'=>['>','3']])->field($field)->order('weight desc')->select();
                $columnData[$key]['children'] = $getSonModule;
            }else{
                foreach ($getSonColumn as $sonKey=>$sonValue){
                    $columnData[$key]['children'][$sonKey] = $sonValue;
                    $getSonSonColumn = db('column')->where(['state'=>1,'father_key'=>$columnData[$key]['children'][$sonKey]['key'],'father_id'=>$columnData[$key]['children'][$sonKey]['id'],'type'=>['<','4']])->field($field)->order('weight desc')->select();
                    if (count($getSonSonColumn) == 0){
                        $getSonSonModule = db('column')->where(['state'=>1,'father_key'=>$columnData[$key]['children'][$sonKey]['key'],'father_id'=>$columnData[$key]['children'][$sonKey]['id'],'type'=>['>','3']])->field($field)->order('weight desc')->select();
                        $columnData[$key]['children'][$sonKey]['children'] = $getSonSonModule;
                    }else{
                        $columnData[$key]['children'][$sonKey]['children'] = $getSonSonColumn;
                        foreach ($getSonSonColumn as $sonSonKey=>$sonSonValue){
                            $getSonSonSonModule = db('column')->where(['state'=>1,'father_key'=>$columnData[$key]['children'][$sonKey]['children'][$sonSonKey]['key'],'father_id'=>$columnData[$key]['children'][$sonKey]['children'][$sonSonKey]['id'],'type'=>['>','3']])->field($field)->order('weight desc')->select();
                            if (count($getSonSonSonModule) !== 0) {
                                $columnData[$key]['children'][$sonKey]['children'][$sonSonKey]['children'] = $getSonSonSonModule;
                            }
                        }
                    }
                }
            }
        }
        return json($columnData);
    }
    public function column_demo(){
        $columnData = [];
        $field = 'id,name,key,title,weight,type';
        $getColumn = db('column')->where(['state'=>1,'istop'=>1])->order('weight desc')->field($field)->select();
        foreach ($getColumn as $key=>$value){
            $columnData[$key] = $value;
            $getSonColumn = db('column')->where(['state'=>1,'father_key'=>$value['key'],'father_id'=>$value['id'],'type'=>['<','4']])->field($field)->order('weight desc')->select();
            $getSonModule = db('column')->where(['state'=>1,'father_key'=>$value['key'],'father_id'=>$value['id'],'type'=>['>','3']])->field($field)->order('weight desc')->select();
            if (count($getSonColumn) == 0){
                $columnData[$key]['children'] = $getSonModule;
            }else{
                $columnData[$key]['children'] = $getSonModule;
                foreach ($getSonColumn as $sonKey=>$sonValue){
                    $columnData[$key]['children'][$sonKey] = $sonValue;
                    $getSonSonColumn = db('column')->where(['state'=>1,'father_key'=>$columnData[$key]['children'][$sonKey]['key'],'father_id'=>$columnData[$key]['children'][$sonKey]['id'],'type'=>['<','4']])->field($field)->order('weight desc')->select();
                    $getSonSonModule = db('column')->where(['state'=>1,'father_key'=>$columnData[$key]['children'][$sonKey]['key'],'father_id'=>$columnData[$key]['children'][$sonKey]['id'],'type'=>['>','3']])->field($field)->order('weight desc')->select();

                    if (count($getSonSonColumn) == 0){
                        $columnData[$key]['children'][$sonKey]['children'] = $getSonSonModule;
                    }else{
                        foreach ($getSonSonModule as $sonSonModule){
                            $columnData[$key]['children'][$sonKey]['children'] = $sonSonModule;
                        }
                        $columnData[$key]['children'][$sonKey]['children'] = $getSonSonModule;
                        foreach ($getSonSonColumn as $sonSonKey=>$sonSonValue){
                            $getSonSonSonModule = db('column')->where(['state'=>1,'father_key'=>$columnData[$key]['children'][$sonKey]['children'][$sonSonKey]['key'],'father_id'=>$columnData[$key]['children'][$sonKey]['children'][$sonSonKey]['id'],'type'=>['>','3']])->field($field)->order('weight desc')->select();
                            if (count($getSonSonSonModule) !== 0) {
                                $columnData[$key]['children'][$sonKey]['children'][$sonSonKey]['children'] = $getSonSonSonModule;
                            }
                        }
                    }
                }
            }
        }
        return json($columnData);
    }

    /**
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function column_delete(){
        $data = input('get.');
        $isHaveSon = db('column')->where(['father_id'=>$data['id'],'father_key'=>$data['key'],'state'=>1])->find();
        if ($isHaveSon){
            $return = ['status'=>0,'msg'=>'栏目存在子类，无法删除'];
        }else{
            $res = db('column')->where(['id'=>$data['id'],'state'=>1])->update(['state'=>0]);
            if ($res){
                db('module')->where(['key'=>$data['key'],'state'=>1])->update(['state'=>0]);
                db('module_loop')->where(['key'=>$data['key'],'state'=>1])->update(['state'=>0]);
                $return = ['status'=>1,'msg'=>'删除成功'];
            }else{
                $return = ['status'=>0,'msg'=>'删除失败'];
            }
        }
        return json($return);
    }
    public function model_list(){
        $get = input('get.');
        $columnData = db('column')->where(['id'=>$get['id'],'state'=>1])->find();
        if ($columnData['type'] == 4){
            $modelData = db('module')->where(['key'=>$columnData['key'],'father_key'=>$columnData['father_key'],'state'=>1])->find();
        }else{
            $modelData = db('module_loop')->where(['key'=>$columnData['key'],'father_key'=>$columnData['father_key'],'state'=>1])->select();
        }
        return json(array('code'=>0,'msg'=>'','count'=>count($modelData),'data'=>$modelData));
    }
    public function module_loop_data(){
        $id = input('get.id');
        $data = db('module_loop')->where(['id'=>$id,'state'=>1])->find();
        return json($data);
    }
    public function module_loop_data_delete(){
        $id = input('get.id');
        $data = db('module_loop')->where(['id'=>$id,'state'=>1])->update(['state'=>0]);
        return json($data);
    }
    public function module_data(){
        $id = input('get.id');
        $data = db('module')->where(['id'=>$id,'state'=>1])->find();
        return json($data);
    }
    public function module_data_delete(){
        $id = input('get.id');
        $data = db('module')->where(['id'=>$id,'state'=>1])->update(['state'=>0]);
        return json($data);
    }

    public function column_data(){
        $id = input('get.id');
        $data = db('column')->where(['id'=>$id,'state'=>1])->find();
        return json($data);
    }

    /**
     *  api提交数据添加栏目
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function column_add(){
        $post = input('post.');
        if (empty($post['father_id'])){
            $post['father_id'] = '';
        }
        $isHaveKey = db('column')->where(['key'=>$post['key'],'state'=>1])->find();
        if ($isHaveKey){
            return json(array(['status'=>0,'msg'=>'该关键字已经存在','data'=>'']));
        }
        $father = db('column')->where(['id'=>$post['father_id'],'state'=>1])->find();
        if (empty($post['key']) or empty($post['name']) or empty($post['title']) or empty($post['type'])){
            return json(array(['status'=>0,'msg'=>'必填字段不能为空','data'=>'']));
        }else{
            if (empty($post['tpl'])){
                $post['tpl'] = $post['key'];
            }
            if ($post['type'] < 4){
                //type小于4的时候检查是否存在模块
                $isHaveModule = db('column')->where(['father_id'=>$post['father_id'],'state'=>1,'type'=>['>','3']])->find();
                if ($isHaveModule){
                    return json(array(['status'=>0,'msg'=>'添加失败，该栏目下已经存在模块，无法添加子级栏目','data'=>'']));
                }
            }else{
                //type不小于4的时候检查是否存在栏目
                $isHaveColumn = db('column')->where(['father_id'=>$post['father_id'],'state'=>1,'type'=>['<','4']])->find();
                if ($isHaveColumn){
                    return json(array(['status'=>0,'msg'=>'添加失败，该栏目下已经存在栏目，无法添加模块','data'=>'']));
                }
            }
            $dbres = db('column')->insert(['key'=>$post['key'],'name'=>$post['name'],'title'=>$post['title'],'keywords'=>$post['keywords'],
                'description'=>$post['description'],'weight'=>$post['weight'],'father_key'=>$father['key'],'father_id'=>$father['id'],'time'=>time(),
                'tpl'=>$post['tpl'],'tpl_header'=>$post['tpl_header'], 'tpl_footer'=>$post['tpl_footer'],'type'=>$post['type'], 'istop'=>$post['istop']]);
            if ($dbres){
                return json(array(['status'=>1,'msg'=>'添加成功','data'=>'']));
            }else{
                return json(array(['status'=>0,'msg'=>'sql语句插入失败','data'=>'']));
            }
        }
    }


    /**
     *
     */
    public function module_add(){
        $post = input('post.');
        $father = db('column')->where(['id'=>$post['column_id'],'state'=>1])->find();
        if (empty($post['column_id']) or empty($post['name']) or empty($post['title'])){
            return json(array(['status'=>0,'msg'=>'必填字段不能为空','data'=>'']));
        }else{
            if ($father['type'] == 4){
                $dbname = "column";
            }elseif($father['type'] == 5){
                $dbname = "column_loop";
            }else{
                return json(array(['status'=>0,'msg'=>'type值有误','data'=>'']));
            }
        }
        $file = request()->file('img');
        if($file){
            $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'static'.DS.'images'.DS.date('Y-m-d',time()));
            $img = '/public/static/images/'.date('Y-m-d',time()).'/'.$info->getSaveName();
        }else{
            $img = '';
        }
        $res = db($dbname)->insert([
            'key'=>$father['key'],'father_key'=>$father['father_key'],'father_id'=>$father['father_id'],'title'=>$post['title'],'subtitle'=>$post['subtitle'],
            'name'=>$post['name'],'img'=>$img,'content'=>$post['content'],'weight'=>$post['weight'],'jump_url'=>$post['jump_url'],
            'other'=>$post['other'],'tags'=>$post['content'],'other_class'=>$post['other_class'],
        ]);
        if ($res){
            return json(array(['status'=>1,'msg'=>'添加成功','data'=>'']));
        }else{
            return json(array(['status'=>0,'msg'=>'sql语句插入失败','data'=>'']));
        }
    }
    public function module_edit(){
        $post = input('post.');
        $father = db('column')->where(['id'=>$post['column_id'],'state'=>1])->find();
        if (empty($post['column_id']) or empty($post['name']) or empty($post['title'])){
            return json(array(['status'=>0,'msg'=>'必填字段不能为空','data'=>'']));
        }else{
            if ($father['type'] == 4){
                $dbname = "column";
            }elseif($father['type'] == 5){
                $dbname = "column_loop";
            }else{
                return json(array(['status'=>0,'msg'=>'type值有误','data'=>'']));
            }
        }
        $file = request()->file('img');
        if($file){
            $info = $file->rule('uniqid')->move(ROOT_PATH . 'public' . DS . 'static'.DS.'images'.DS.date('Y-m-d',time()));
            $img = '/public/static/images/'.date('Y-m-d',time()).'/'.$info->getSaveName();
            $res = db($dbname)->where(['id'=>$post['id']])->update([
               'title'=>$post['title'],'subtitle'=>$post['subtitle'],
                'name'=>$post['name'],'img'=>$img,'content'=>$post['content'],'weight'=>$post['weight'],'jump_url'=>$post['jump_url'],
                'other'=>$post['other'],'tags'=>$post['tags'],'other_class'=>$post['other_class'],
            ]);
        }else{
            $res = db($dbname)->where(['id'=>$post['id']])->update([
                'title'=>$post['title'],'subtitle'=>$post['subtitle'],
                'name'=>$post['name'],'content'=>$post['content'],'weight'=>$post['weight'],'jump_url'=>$post['jump_url'],
                'other'=>$post['other'],'tags'=>$post['tags'],'other_class'=>$post['other_class'],
            ]);
        }
        if ($res){
            return json(array(['status'=>1,'msg'=>'添加成功','data'=>'']));
        }else{
            return json(array(['status'=>0,'msg'=>'sql语句插入失败','data'=>'']));
        }
    }






























}