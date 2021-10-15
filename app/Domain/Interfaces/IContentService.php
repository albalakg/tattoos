<?php

namespace App\Domain\Interfaces;

interface IContentService
{
    public function create(array $item, int $created_id);
    public function update(array $item, int $updated_id);
    public function multipleDelete(array $ids, int $deleted_by);
    public function delete(int $id, int $deleted_by): bool;
    public function forceDelete(int $id, int $deleted_by): bool;
    public function getAll(): object;
}