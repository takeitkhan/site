<?php
namespace Tritiyo\Site\Repositories;

use Tritiyo\Site\Models\Site;

class SiteEloquent implements SiteInterface
{
    private $model;

    /**
     * SiteEloquent constructor.
     * @param SiteInterface $model
     */
    public function __construct(Site $model)
    {
        $this->model = $model;
    }

    /**
     *
     */
    public function getAll()
    {
        return $this->model
               ->orderBy('id', 'desc')
               //->take(100)
               ->paginate(30);
    }

    public function getDataByFilter(array $options = [])
    {
        $default = [
            'search_key' => null,
            'limit' => 10,
            'offset' => 0
        ];
        $no = array_merge($default, $options);         

        if (!empty($no['limit'])) {
            $limit = $no['limit'];
        } else {
            $limit = 10;
        }

        if (!empty($no['offset'])) {
            $offset = $no['offset'];
        } else {
            $offset = 0;
        }

        if (!empty($no['sort_type'])) {
            $orderBy = $no['column'] . ' ' . $no['sort_type'];
        } else {
            $orderBy = 'id desc';
        }

        if (!empty($no['search_key'])) {
            $sites = $this->model
            ->leftjoin('projects', 'projects.id', 'sites.project_id')
            ->select('sites.*', 'projects.name', 'projects.code', 'projects.type', 'projects.customer')
            ->where('project_id', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('sites.location', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('sites.site_code', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('sites.material', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('sites.site_head', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('sites.budget', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('projects.name', 'LIKE', '%'.$no['search_key'].'%')
            //->toSql();
            ->orWhere('projects.code', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('projects.type', 'LIKE', '%'.$no['search_key'].'%')
            ->orWhere('projects.customer', 'LIKE', '%'.$no['search_key'].'%')
            ->paginate('48');

            //dd($sites);
        } else {
            $sites = [];
        }

        //dd($sites);
        return $sites;
    }


    /**
     * @param $id
     */
    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
    * @param $column
    * @param $value
    */
    public function getByAny($column, $value)
    {
        return $this->model->where($column, $value)->get();
    }

    /**
     * @param array $att
     */
    public function create(array $att)
    {
        return $this->model->create($att);
    }

    /**
     * @param $id
     * @param array $att
     */
    public function update($id, array $att)
    {
        $todo = $this->getById($id);
        $todo->update($att);
        return $todo;
    }

    public function delete($id)
    {
        $this->getById($id)->delete();
        return true;
    }

    /**
    * @param $column
    * @param $value
    */
    public function getByAnyWithPaginate($column, $value)
    {
        return $this->model->where($column, $value)->paginate(20);
    }
}
