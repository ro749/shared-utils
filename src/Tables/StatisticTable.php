<?php
namespace Ro749\SharedUtils\Tables;
use Ro749\SharedUtils\Statistics\BaseStatistic;
use Ro749\SharedUtils\Getters\StatisticsGetter;
use Ro749\SharedUtils\FormRequests\BaseFormRequest;
use Illuminate\Support\Facades\DB;

class StatisticTable extends BaseTableDefinition
{
    public BaseStatistic $statistic;
    public function __construct(
        string $id,
        StatisticsGetter $getter,
        View $view = null, 
        Delete $delete = null, 
        BaseFormRequest $form = null
    ){
        parent::__construct(
            id: $id,
            getter: $getter,
            view: $view,
            delete: $delete,
            form: $form
        );
    }

    public function get($start = 0, $length = 10, $search = '',$order = [],$filters = []): mixed
    {
        return $this->getter->get($start, $length, $search,$order,$filters);
    }
}