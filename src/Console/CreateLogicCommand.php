<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateLogicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:logic {name} {--resource=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Logic';

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

        //命名空间
        $namespace = 'App\Logic\Api';

        //类名
        $args_name = '';
        if (strstr($args['name'], '/')) {
            $ex = explode('/', $args['name']);
            $args_name = $ex[count($ex)-1];
        }

        $class_name = $args_name . 'Logic';

        //文件名
        $file_name = $class_name . '.php';

        //文件地址
        $logic_file = app_path() . '/Logic/' . $file_name;

        //目录
        $logic_path = dirname($logic_file);

        //获取模板,替换变量
        $template = file_get_contents(dirname(__FILE__) . '/stubs/logic.stub');
        $source = str_replace('{{namespace}}', $namespace, $template);
        $source = str_replace('{{class_name}}', $class_name, $source);

        //是否已存在相同文件
        if (file_exists($logic_file)) {
            $this->error('文件已存在');
        }

        //创建
        if (file_exists($logic_path) === false) {
            if (mkdir($logic_path, 0777, true) === false) {
                $this->error('目录' . $logic_path . '没有写入权限');
            }
        }

        //写入
        if (!file_put_contents($logic_file, $source)) {
            $this->error('Logic created failed.');
        }

        $this->info('Logic created successfully.');
    }

}
