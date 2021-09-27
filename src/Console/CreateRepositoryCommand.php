<?php

namespace Taoran\Laravel\Console;

use Illuminate\Console\Command;

class CreateRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     * 创建命令名称和参数
     * @var string
     */
    protected $signature = 'create:repository {name}';

    /**
     * The console command description.
     * 描述
     * @var string
     */
    protected $description = '创建repository层';

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
        //$option = $this->option('resource');

        //处理组合参数
        $args_name = $args['name'];
        if (strstr($args['name'], '/')) {
            $ex = explode('/', $args['name']);
            $args_name = $ex[count($ex)-1];
            $namespace_ext = '/' . substr($args['name'], 0, strrpos($args['name'], '/'));
        }

        $namespace_ext = $namespace_ext ?? '';

        //类名
        $class_name = $args_name . 'Repository';

        //文件名
        $file_name = $class_name . '.php';

        //文件地址
        $file = app_path() . '/Repositorys' . $namespace_ext . '/' . $file_name;

        //命名空间
        $namespace = 'App\Repositorys' . str_replace('/', '\\', $namespace_ext);

        //目录
        $path = dirname($file);

        //获取模板,替换变量
        $template = file_get_contents(dirname(__FILE__) . '/stubs/repository.stub');
        $source = str_replace('{{namespace}}', $namespace, $template);
        $source = str_replace('{{class_name}}', $class_name, $source);
        $source = str_replace('{{args_name}}', $args_name, $source);

        //是否已存在相同文件
        if (file_exists($file)) {
            $this->error('文件已存在');
            exit;
        }

        //创建
        if (file_exists($path) === false) {
            if (mkdir($path, 0777, true) === false) {
                $this->error('目录' . $path . '没有写入权限');
                exit;
            }
        }

        //写入
        if (!file_put_contents($file, $source)) {
            $this->error('创建失败！');
            exit;
        }

        $this->info('创建成功！目录:'. $file);
    }
}
