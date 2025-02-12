<?php

namespace CodeFin\Repositories;

use CodeFin\Events\BankStoredEvent;
use CodeFin\Presenters\BankPresenter;
use CodeFin\Repositories\Interfaces\BankRepository;
use Illuminate\Http\UploadedFile;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use CodeFin\Models\Bank;

/**
 * Class BankRepositoryEloquent.
 *
 * @package namespace CodeFin\Repositories;
 */
class BankRepositoryEloquent extends BaseRepository implements BankRepository
{
    public function create(array $attributes)
    {
        $logo = $attributes['logo'];
        $attributes['logo'] = env('BANK_LOGO_DEFAULT');
        $skipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::create($attributes);
        $event = new BankStoredEvent($model,$logo);
        event($event);
        $this->skipPresenter = $skipPresenter;

        return $this->parserResult($model);
    }

    public function update(array $attributes, $id)
    {
        $logo = null;
        if (isset($attributes['logo']) && $attributes['logo'] instanceof UploadedFile){
            $logo = $attributes['logo'];
            unset($attributes['logo']);
        }

        $skipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::update($attributes, $id);
        $event = new BankStoredEvent($model,$logo);
        event($event);
        $this->skipPresenter = $skipPresenter;

        return $this->parserResult($model);
    }


    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Bank::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function presenter()
    {
        return BankPresenter::class;
    }

}
