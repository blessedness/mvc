<?php
declare(strict_types=1);


namespace App\Controllers\User;


use App\Controllers\AbstractController;
use App\Repositories\UserRepository;
use Core\Http\RequestInterface;

class UserIndexController extends AbstractController
{
    public function __invoke(RequestInterface $request)
    {
        $result = (new UserRepository())->getAll();

        return $this->render($result);
    }
}