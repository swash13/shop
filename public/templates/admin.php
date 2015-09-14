<!Doctype html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Админка</title>

        <link rel="stylesheet" href="<?= App::siteURL('assets/css/admin.css') ?>" />
    </head>

    <body>
        <div class="wrapper clear">
            <?= $this->displayTemplate('chunks/admin-header'); ?>

            <div class="content">
                <?php if (isset($this->title)) { ?>
                    <h1><?= $this->title ?></h1>
                <?php } ?>

                <?php
                    if (is_object($this->content)) {
                        echo $this->content->display();
                    } else {
                        echo $this->content;
                    }
                ?>
            </div>
        </div>

        <script src="<?= App::siteURL('assets/js/jquery-1.11.2.min.js') ?>"></script>
        <script src="<?= App::siteURL('assets/js/swfupload.js') ?>"></script>
        <script src="<?= App::siteURL('assets/js/admin.js') ?>"></script>
    </body>
</html>