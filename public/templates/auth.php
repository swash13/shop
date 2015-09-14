<!DOCTYPE html>
    <html lang="en">
    <head>
        <?= $this->displayTemplate('chunks/meta'); ?>

        <title>Интернет магазин</title>
    </head>

    <body>
        <div class="wrapper">
            <?= $this->displayTemplate('chunks/header'); ?>

            <section id="form">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4 col-sm-offset-1">
                            <div class="login-form"><!--login form-->
                                <h2><?= App::t('Login to your account') ?></h2>
                                <form action="<?= App::getLink('Auth', array('action' => 'login')) ?>" method="post">
                                    <input type="text" name="email" placeholder="<?= App::t('Email Address') ?>">
                                    <input type="password" name="password" placeholder="<?= App::t('Password') ?>">
                                    <button type="submit" class="btn btn-default"><?= App::t('Login') ?></button>
                                </form>
                            </div>
                        </div>

                        <div class="col-sm-1">
                            <h2 class="or"><?= App::t('OR') ?></h2>
                        </div>

                        <div class="col-sm-4">
                            <div class="signup-form">
                                <h2><?= App::t('зарегистрируйте новый') ?></h2>
                                <form action="<?= App::getLink('Auth', array('action' => 'registration')) ?>" method="post">
                                    <input type="text"  name="name" placeholder="<?= App::t('Name') ?>" value="<?= $this->post ? $this->post['name'] : ''?>">
                                    <?php if ($this->registration_errors && !empty($this->registration_errors['name'])) { ?><span class="error"><?= $this->registration_errors['name'] ?></span><?php } ?>
                                    <input type="email" name="email" placeholder="<?= App::t('Email Address') ?>" value="<?= $this->post ? $this->post['email'] : ''?>">
                                    <?php if ($this->registration_errors && !empty($this->registration_errors['email'])) { ?><span class="error"><?= $this->registration_errors['email'] ?></span><?php } ?>
                                    <input type="password" name="password" placeholder="<?= App::t('Password') ?>">
                                    <?php if ($this->registration_errors && !empty($this->registration_errors['password'])) { ?><span class="error"><?= $this->registration_errors['password'] ?></span><?php } ?>
                                    <input type="password" name="confirm" placeholder="<?= App::t('Confirm') ?>">
                                    <?php if ($this->registration_errors && !empty($this->registration_errors['confirm'])) { ?><span class="error"><?= $this->registration_errors['confirm'] ?></span><?php } ?>
                                    <button type="submit" class="btn btn-default"><?= App::t('Регистрация') ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <?= $this->displayTemplate('chunks/footer') ?>
        </div>

        <?= $this->displayTemplate('chunks/scripts') ?>
    </body>
</html>




