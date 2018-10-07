<?php

declare(strict_types=1);


namespace App\Controllers\Auth;

use App\Controllers\AbstractController;
use App\Repositories\UserRepository;
use Core\Http\RequestInterface;
use Infrastructure\Auth\Services\AuthService;
use Infrastructure\Auth\Services\PasswordService;
use Infrastructure\Common\Exception\FormException;

class AuthLoginController extends AbstractController
{
    public function __invoke(RequestInterface $request)
    {
        $data = json_decode($request->getContent(), true);

        $email = filter_var($data['email'] ?? null, FILTER_SANITIZE_EMAIL);
        $password = filter_var($data['password'] ?? null, FILTER_SANITIZE_STRING);

        $errors = [];

        $this->validate($email, $password, $errors);

        if (!empty($errors)) {
            throw FormException::withMessages($errors);
        }

        $user = (new UserRepository())->findByEmail($email);

        if (!$user || $user['status'] === 'disabled' || !PasswordService::validatePassword($password, $user['password_hash'])) {
            throw FormException::withMessages([
                'email' => 'Incorrect email or password.'
            ]);
        }

        return $this->render(
            (new AuthService())->login($user)
        );
    }

    public function validate($email, $password, &$errors)
    {
        $this->validateForEmpty($email, $password, $errors);
        $this->rulesValidate($email, $errors);
    }

    /**
     * @param string $email
     * @param string $password
     * @param $errors
     * @return mixed
     */
    protected function validateForEmpty(?string $email, ?string $password, &$errors)
    {
        $messageBlank = 'Value cannot be blank.';

        if (!$email) {
            $errors['email'][] = $messageBlank;
        }

        if (!$password) {
            $errors['password'][] = $messageBlank;
        }
    }

    /**
     * @param string $email
     * @param $errors
     * @return void
     */
    protected function rulesValidate(?string $email, &$errors)
    {
        $messageInvalidValue = 'Invalid value.';

        if (!empty($errors)) {
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'][] = $messageInvalidValue;
        }
    }
}