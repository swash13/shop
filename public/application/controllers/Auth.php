<?php

class AuthController extends ControllerFront {
    protected static function actionDefault() {
        if (App::currentUser()) {
            self::redirect('/');
        }

        return self::displayPage('auth');
    }

    protected static function actionRegistration() {
        if (empty($_POST)) {
            self::redirect(App::getLink('Auth'));
        }

        $errors = array();
        if (empty($_POST['name'])) {
            $errors['name'] = App::t('Введите имя');
        }

        if (empty($_POST['email'])) {
            $errors['email'] = App::t('Введите email');
        } else if (!preg_match('/^[-A-Za-z0-9_\.]+@[-A-Za-z0-9_\.]+\.[a-z0-9]{2,4}$/', $_POST['email'])) {
            $errors['email'] = App::t('Некорректный email');
        }

        if (empty($_POST['password'])) {
            $errors['password'] = App::t('Введите пароль');
        } else if (empty($_POST['confirm'])) {
            $errors['confirm'] = App::t('Введите подтверждение');
        } else if ($_POST['password'] != $_POST['confirm']) {
            $errors['password'] = $errors['confirm'] = App::t('Пароли не совпадают');
        }

        if (empty($errors) && UserModel::getByEmail($_POST['email'])) {
            $errors['email'] = App::t('Этот email уже используется');
        }

        if (empty($errors)) {
            $user = new UserModel();
            $user->name = $_POST['name'];
            $user->email = $_POST['email'];
            $user->password = md5($_POST['password']);
            $user->save();

            App::currentUser($user);
            self::redirect('/');
        }

        self::templateVar('registration_errors', $errors);
        self::templateVar('post', $_POST);
        return self::displayPage('auth');
    }

    protected static function actionLogin() {
        if (empty($_POST)) {
            self::redirect(App::getLink('Auth'));
        }

        $errors = array();
        if (empty($_POST['email'])) {
            $errors['email'] = App::t('Введите email');
        } else if (!preg_match('/^[-A-Za-z0-9_\.]+@[-A-Za-z0-9_\.]+\.[a-z0-9]{2,4}$/', $_POST['email'])) {
            $errors['email'] = App::t('Некорректный email');
        }

        if (empty($_POST['password'])) {
            $errors['password'] = App::t('Введите пароль');
        }

        if (empty($errors)) {
            $user = UserModel::getByEmail($_POST['email']);
            if (!$user) {
                $errors['email'] = App::t('Пользователь не найден');
            }

            if ($user->password == md5($_POST['password'])) {
                App::currentUser($user);
                self::redirect('/');
            } else {
                $errors['password'] = App::t('Неверный пароль');
            }
        }
    }

    protected static function actionLogout() {
        $_SESSION['current_user_id'] = 0;
        self::redirect('/');
    }
}