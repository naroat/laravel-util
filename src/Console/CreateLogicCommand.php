<?php

namespace Taoran\Laravel\Console;

use Illuminate\Console\Command;

class CreateLogicCommand extends Command
{
    /**
     * The name and signature of the console command.
     * 创建命令名称和参数
     * @var string
     */
    protected $signature = 'create:logic {name} {--resource}';

    /**
     * The console command description.
     * 描述
     * @var string
     */
    protected $description = 'create logic';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取参数
        $args = $this->arguments();

        //获取可选参数
        $option = $this->option('resource');

        //处理组合参数
        $args_name = $args['name'];
        if (strstr($args['name'], '/')) {
            $ex = explode('/', $args['name']);
            $args_name = $ex[count($ex)-1];
            $namespace_ext = '/' . substr($args['name'], 0, strrpos($args['name'], '/'));
        }

        $namespace_ext = $namespace_ext ?? '';

        //类名
        $class_name = $args_name . 'Logic';

        //文件名
        $file_name = $class_name . '.php';

        //文件地址
        $logic_file = app_path() . '/Logic' . $namespace_ext . '/' . $file_name;

        //命名空间
        $namespace = 'App\Logic' . str_replace('/', '\\', $namespace_ext);

        //目录
        $logic_path = dirname($logic_file);

        //获取模板,替换变量
        $template = file_get_contents(dirname(__FILE__) . '/stubs/logic.stub');
        $default_method = $option ? file_get_contents(dirname(__FILE__) . '/stubs/default_method.stub') : '';
        $source = str_replace('{{namespace}}', $namespace, $template);
        $source = str_replace('{{class_name}}', $class_name, $source);
        $source = str_replace('{{default_method}}', $default_method, $source);

        //是否已存在相同文件
        if (file_exists($logic_file)) {
            $this->error('文件已存在');
            exit;
        }

        //创建
        if (file_exists($logic_path) === false) {
            if (mkdir($logic_path, 0777, true) === false) {
                $this->error('目录' . $logic_path . '没有写入权限');
                exit;
            }
        }

        //写入
        if (!file_put_contents($logic_file, $source)) {
            $this->error('创建失败！');
            exit;
        }

        $this->info('创建成功！');
    }

}
