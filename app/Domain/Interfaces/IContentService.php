<?php

namespace App\Domain\Interfaces;

interface IContentService
{
    public function create(object $item, int $created_id);
    public function update(object $item, int $updated_id);
    public function multipleDelete(array $ids, int $deleted_by);
    public function delete(int $id, int $deleted_by);
    public function getAll(): object;
}