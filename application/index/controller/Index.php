<?php
namespace app\index\controller;

use think\Controller;

class Index extends Controller
{
    public function index($grandfather = 'index',$father='',$son='')
    {
        if (empty($grandfather)){$grandfather='index';}
        $list = [];
        $loop = [];
        $moduledata = [];
        $column = [];
        if (empty($father) and empty($son)){
            $ishave = db('column')->where(['key'=>$grandfather,'state'=>1])->find();
            if ($ishave){
                if ($ishave['type'] == 3){
                    $this->redirect($ishave['tpl']);
                }
                if ($ishave['type'] == 5 or $ishave['type'] == 4){
                    return "数据模块，不允许访问！";
                }

                $isHaveModule = db('column')->where(['father_key'=>$ishave['key'],'father_id'=>$ishave['id'],'state'=>1,'type'=>['>','3']])->select();
                foreach ($isHaveModule as $haveModule){
                    $column[$haveModule['key']] = $haveModule;
                    if ($haveModule['type'] == '5'){
                        $loop[$haveModule['key']] = db('module_loop')->where(['key'=>$haveModule['key'],'state'=>1])->order('weight desc')->select();
                    }
                    if ($haveModule['type'] == '4'){
                        $moduledata[$haveModule['key']] = db('module')->where(['key'=>$haveModule['key'],'state'=>1,'type'=>'module'])->order('weight desc')->find();
                    }
                }

//                var_dump($moduledata);
//                var_dump($list);
//                var_dump($loop);
//                var_dump($column);

                return view($ishave['tpl'],[
                    'column'=>$column,
                    'module'=>$moduledata,
                    'list'=>$list,
                    'loop'=>$loop,
                ]);
            }else{
                return "栏目不存在";
            }
        }elseif(empty($son)){
            //此处先判断是否为文章
            $fatherIsArticle = db('module')->where(['type'=>'article','state'=>1,'father_key'=>$grandfather])->find();
            if ($fatherIsArticle){
                //当前为文章
                $isExist = db('module')->where(['type'=>'article','state'=>1,'father_key'=>$grandfather,'key|id'=>$father])->find();
                if (!$isExist){
                    return "文章不存在";
                }
                return "this is a article";
            }
            $isColumn = db('column')->where(['key'=>$father,'state'=>1])->find();
            if ($isColumn){
                if ($isColumn['type'] == 3){
                    $this->redirect($isColumn['tpl']);
                }
                if ($isColumn['type'] == 5 or $isColumn['type'] == 4){
                    return "数据模块，不允许访问！";
                }
                $isHaveModule = db('column')->where(['father_key'=>$father,'father_id'=>$isColumn['id'],'state'=>1,'type'=>['>','3']])->select();
                foreach ($isHaveModule as $haveModule){
                    $column[$haveModule['key']] = $haveModule;
                    if ($haveModule['type'] == '5'){
                        $loop[$haveModule['key']] = db('module_loop')->where(['key'=>$haveModule['key'],'state'=>1])->order('weight desc')->select();
                    }
                    if ($haveModule['type'] == '4'){
                        $moduledata[$haveModule['key']] = db('module')->where(['key'=>$haveModule['key'],'state'=>1,'type'=>'module'])->order('weight desc')->find();
                    }
                }
                var_dump($moduledata);
                var_dump($list);
                var_dump($loop);
                var_dump($column);
                return view($isColumn['tpl'],[
                    'column'=>$column,
                    'module'=>$moduledata,
                    'list'=>$list,
                    'loop'=>$loop,
                ]);

            }else{
                return "栏目不存在";
            }

        }else{
            $fatherIsArticle = db('module')->where(['type'=>'article','state'=>1,'father_key'=>$father])->find();
            if ($fatherIsArticle){
                //当前为文章
                $isExist = db('module')->where(['type'=>'article','state'=>1,'father_key'=>$father,'key|id'=>$son])->find();
                if (!$isExist){
                    return "文章不存在";
                }
                return "this is a article";
            }
            $isColumn = db('column')->where(['key'=>$father,'state'=>1])->find();
            if ($isColumn){
                if ($isColumn['type'] == 3){
                    $this->redirect($isColumn['tpl']);
                }
                if ($isColumn['type'] == 5 or $isColumn['type'] == 4){
                    return "数据模块，不允许访问！";
                }
                $isHaveModule = db('column')->where(['father_key'=>$father,'father_id'=>$isColumn['id'],'state'=>1,'type'=>['>','3']])->select();
                foreach ($isHaveModule as $haveModule){
                    $column[$haveModule['key']] = $haveModule;
                    if ($haveModule['type'] == '5'){
                        $loop[$haveModule['key']] = db('module_loop')->where(['key'=>$haveModule['key'],'state'=>1])->order('weight desc')->select();
                    }
                    if ($haveModule['type'] == '4'){
                        $moduledata[$haveModule['key']] = db('module')->where(['key'=>$haveModule['key'],'state'=>1,'type'=>'module'])->order('weight desc')->find();
                    }
                }
                var_dump($moduledata);
                var_dump($list);
                var_dump($loop);
                var_dump($column);
                return view($isColumn['tpl'],[
                    'column'=>$column,
                    'module'=>$moduledata,
                    'list'=>$list,
                    'loop'=>$loop,
                ]);

            }else{
                return "栏目不存在";
            }
        }


    }
}
