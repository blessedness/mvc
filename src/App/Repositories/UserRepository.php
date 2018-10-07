<?php

declare(strict_types=1);


namespace App\Repositories;


use App\Helpers\FileHelper;

class UserRepository
{
    public function findOneById(string $id): ?array
    {
        $result = array_filter($this->getAll(), function ($item) use ($id) {
            return $item['id'] === $id;
        });

        return empty($result) ? null : array_shift($result);
    }

    public function getAll(): array
    {
        return $this->getModels('users.json');
    }

    protected function getModels(string $file)
    {
        return FileHelper::readJsonFile(__DIR__ . DIRECTORY_SEPARATOR . $file);
    }

    public function findByEmail(string $email): ?array
    {
        $users = $this->getModels('auth-users.json');

        $result = array_filter($users, function ($item) use ($email) {
            return $item['email'] === $email;
        });

        return empty($result) ? null : array_shift($result);
    }
}