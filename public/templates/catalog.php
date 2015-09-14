<!DOCTYPE html>
<html lang="en">
    <head>
        <?= $this->displayTemplate('chunks/meta'); ?>

        <title>Интернет магазин</title>
    </head>

    <body>
        <div class="wrapper">
            <?= $this->displayTemplate('chunks/header'); ?>

            <?=  $this->displayTemplate('chunks/advertisement'); ?>

            <section>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3">
                            <?= $this->displayTemplate('chunks/aside'); ?>
                        </div>

                        <div class="col-sm-9 padding-right">
                            <div class="features_items">
                                <?=
                                $this->displayTemplate(
                                    'chunks/product-list',
                                    array(
                                        'product_list_title' => $this->product_list_title,
                                        'product_list_items' => $this->product_list_items
                                    )
                                );
                                ?>
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




