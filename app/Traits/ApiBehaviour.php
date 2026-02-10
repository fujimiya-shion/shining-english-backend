<?php

namespace App\Traits;

use App\Services\IService;
use App\ValueObjects\MetaPagination;
use App\ValueObjects\QueryOption;
use Exception;
use Illuminate\Http\Request;

trait ApiBehaviour
{
    use Jsonable;

    abstract protected function service(): IService;

    public function index(Request $request)
    {
        $options = QueryOption::fromArray($request->all(), true);
        $paginator = $this->service()->paginateAll($options);
        $collections = $paginator->getCollection();
        $total = $paginator->total();
        $meta = MetaPagination::fromTotalAndQueryOption($total, $options);

        return $this->success(
            data: $collections,
            meta: $meta->toArray(),
        );
    }

    public function show(Request $request)
    {
        $id = (int) $request->route('id');
        $record = $this->service()->getById($id);
        if (! $record) {
            return $this->notfound();
        }

        return $this->success(data: $record);
    }

    public function store(Request $request)
    {
        try {
            $created = $this->service()->create($request->all());

            return $this->created($created);
        } catch (Exception $e) {
            logger()->error($e->getMessage());

            return $this->error();
        }
    }

    public function update(Request $request)
    {
        try {
            $id = (int) $request->route('id');
            $data = $request->all();
            $updated = $this->service()->update($id, $data);

            return $this->success('Updated', $updated);
        } catch (Exception $e) {
            logger()->error($e->getMessage());

            return $this->error();
        }
    }

    public function delete(Request $request)
    {
        try {
            $id = (int) $request->route('id');
            $deleted = $this->service()->delete($id);

            return $deleted ? $this->deleted() : $this->notfound();
        } catch (Exception $e) {
            logger()->error($e->getMessage());

            return $this->error();
        }
    }
}
