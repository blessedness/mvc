<?php
declare(strict_types=1);


namespace App\Controllers\User;


use App\Controllers\AbstractController;
use App\Repositories\UserRepository;
use Core\Http\RequestInterface;
use Core\Http\Response;
use Core\Router\Exception\NotFoundException;

class UserViewController extends AbstractController
{
    public function view(RequestInterface $request)
    {
        $id = $request->getAttribute('id');
        $result = (new UserRepository())->findOneById($id);

        if (!$result) {
            throw new NotFoundException(Response::HTTP_NOT_FOUND, sprintf('User with ID: %s not found.', $id));
        }

        return $this->render($result);
    }
}