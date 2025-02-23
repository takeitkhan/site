<?php
namespace Tritiyo\Site\Repositories;

interface SiteInvoiceInterface
{
    public function getAll();

    public function getById($id);

    public function getByAny($column, $value);

    public function create(array $attributes);

    public function update($id, array $attributes);

    public function delete($id);

    public function getByAnyWithPaginate($column, $value);

}
