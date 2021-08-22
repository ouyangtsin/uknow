<?php
// +----------------------------------------------------------------------
// | UKnowing [You Know] 简称 UK
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------


namespace app\ask\backend;
use app\common\controller\Backend;
use app\common\library\helper\TreeHelper;
use Overtrue\Pinyin\Pinyin;
use think\App;
use think\facade\Request;

class Topic extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\ask\model\Topic();
        $this->table = 'topic';
    }

    public function index()
    {
        $columns = [
            ['id'  , 'ID'],
            ['title','话题标题','link',(string)url('ask/topic/detail',['id'=>'__id__'])],
            ['pic','话题图片','image'],
            ['discuss','讨论计数'],
            ['discuss_week','一周讨论'],
            ['discuss_month','一月讨论'],
            ['focus','关注计数'],
            ['lock', '是否锁定', 'radio', '0',[1 => '是',0 => '否']],
            ['top', '推荐首页', 'radio', '0',[1 => '是',0 => '否']],
            ['related', '是否被用户关联', 'radio', '0',[1 => '是',0 => '否']],
            ['create_time', '创建时间','datetime'],
        ];
        $search = [
            ['text', 'title', '话题标题', 'LIKE'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere('id',$search);
            // 排序处理
            $list = db($this->table)
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->select()
                ->toArray();

            $list = TreeHelper::tree($list);
            foreach ($list as $k => $v) {
                $list[$k]['title'] = $v['left_title'];
            }
            return [
                'total' => count($list),
                'per_page' => 10000,
                'current_page' => 1,
                'last_page' => 1,
                'data' => $list,
            ];
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->setPagination('false')
            ->setParentIdField('pid')
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $pinyin = new Pinyin();
            $data['url_token'] = $pinyin->permalink($data['title']);
            $result = $this->model->create($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        $list = db('topic')
            ->select()
            ->toArray();

        $list = TreeHelper::tree($list);
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }

        return $this->formBuilder
            ->addSelect('pid','父级话题','选择父级话题',$result)
            ->addImage('pic','话题封面','上传话题封面')
            ->addText('title','话题名称','填写话题名称')
            ->addEditor('description','话题描述')
            ->addText('seo_title','SEO标题','填写SEO标题')
            ->addText('seo_keywords','SEO关键词','填写SEO关键词')
            ->addText('seo_description','SEO描述','填写SEO描述')
            ->addRadio('lock','是否锁定','选择是否锁定',['0' => '未锁定','1' => '已锁定'],'0')
            ->addRadio('top','是否推荐','选择是否推荐',['0' => '不推荐','1' => '已推荐'],'0')
            ->fetch();
    }

    public function edit(string $id)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file']);
            $pinyin = new Pinyin();
            $data['url_token'] = $pinyin->permalink($data['title']);
            $result = $this->model->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('修改失败');
            }
        }

        $info =$this->model->find($id)->toArray();

        $list = db('topic')
            ->select()
            ->toArray();

        $list = TreeHelper::tree($list);
        $result = [];
        foreach ($list as $k => $v) {
            $result[$v['id']] = $v['left_title'];
        }

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addSelect('pid','父级话题','选择父级话题',$result,$info['pid'])
            ->addImage('pic','话题封面','上传话题封面',$info['pic'])
            ->addText('title','话题名称','填写话题名称',$info['title'])
            ->addEditor('description','话题描述','',htmlspecialchars_decode($info['description']))
            ->addText('seo_title','SEO标题','填写SEO标题',$info['seo_title'])
            ->addText('seo_keywords','SEO关键词','填写SEO关键词',$info['seo_keywords'])
            ->addText('seo_description','SEO描述','填写SEO描述',$info['seo_description'])
            ->addRadio('lock','是否锁定','选择是否锁定',['0' => '未锁定','1' => '已锁定'],$info['lock'])
            ->addRadio('top','是否推荐','选择是否推荐',['0' => '不推荐','1' => '已推荐'],$info['top'])
            ->fetch();
    }
}