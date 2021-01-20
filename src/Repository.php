<?php


namespace Taoran\Laravel;


use Taoran\Laravel\Traits\CallbackTrait;
use Taoran\Laravel\Exception\ApiException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    protected $model;

    public function __construct()
    {

    }

    /**
     * 获取单条
     *
     * @param array $param
     * @return mixed
     */
    public function getOne(callable $where = null)
    {
        $orm = $this->model->where('is_on', 1);

        //自定义条件
        CallbackTrait::callback($where, $orm);

        return $orm->first();
    }

    /**
     * 获取单条 - 通过id
     *
     * @param $id
     * @param callable|null $where
     * @return mixed
     */
    public function getOneById($id, callable $where = null)
    {
        $orm = $this->model->where('is_on', 1);

        //自定义条件
        CallbackTrait::callback($where, $orm);

        return $orm->find($id);
    }

    /**
     * 获取多条
     *
     * @param array $param
     * @param callable|null $where
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Builder|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getList(array $param, callable $where = null)
    {
        $orm = $this->model->where('is_on', 1);

        //自定义条件
        CallbackTrait::callback($where, $orm);

        return $this->read($orm, $param);
    }

    /**
     * 新增
     *
     * @param $param
     * @return mixed
     */
    public function create(array $param)
    {
        set_save_data($this->model, $param);
        $res = $this->model->save();
        if (!$res) {
            \DB::rollBack();
            throw new ApiException();
        }
        return $this->model;
    }

    /**
     * 更新
     *
     * @param $data
     * @return bool
     * @throws \App\Exceptions\ApiException
     */
    public function update(Model $model, array $param)
    {
        set_save_data($model, $param);
        $res = $model->save();
        if (!$res) {
            \DB::rollBack();
            throw new ApiException();
        }
        return $model;
    }

    /**
     * 获取数据
     *
     * @param Builder $orm
     * @param array $param
     */
    public function read(Builder $orm, array $param)
    {
        if (isset($param['is_all']) && $param['is_all'] == 1) {
            $orm = $orm->get();
        } else {
            $orm = $orm->paginate();
        }
        return $orm;
    }

    /**
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->$method(...$parameters);
    }

    /**
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }
}
