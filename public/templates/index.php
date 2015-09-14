<!DOCTYPE html>
<html lang="en">
    <head>
        <?= $this->displayTemplate('chunks/meta'); ?>

        <title>Интернет магазин</title>
    </head>

    <body>
        <div class="wrapper">
            <?= $this->displayTemplate('chunks/header'); ?>

            <?=  $this->displayTemplate('chunks/slider'); ?>


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

                            <?php if (!empty($this->tab_categories)) { ?>
                                <div class="category-tab">
                                    <div class="col-sm-12">
                                        <ul class="nav nav-tabs">
                                            <?php $active_exists = false; ?>
                                            <?php foreach ($this->tab_categories as $index => $category) { ?>
                                                <?php if ($category->products) { ?>
                                                    <?php if ($active_exists) { ?>
                                                        <li>
                                                    <?php } else { ?>
                                                        <li class="active">
                                                        <?php $active_exists = true; ?>
                                                    <?php } ?>
                                                    <a href="#tab_<?= $index ?>" data-toggle="tab"><?= $category->name ?></a>
                                                    </li>
                                                <?php } ?>
                                            <?php } ?>
                                        </ul>
                                    </div>

                                    <div class="tab-content">
                                        <?php foreach ($this->tab_categories as $index => $category) { ?>
                                            <div class="tab-pane fade <?php if ($index == 0) { echo 'active in'; } ?>" id="tab_<?= $index ?>">
                                                <?php foreach ($category->products as $product) { ?>
                                                    <div class="col-sm-3">
                                                        <div class="product-image-wrapper">
                                                            <div class="single-products">
                                                                <div class="productinfo text-center">
                                                                    <img src="/assets/images/products/<?= $product->cover->file ?>.png" alt="" width="255" height="237">
                                                                    <h2><?= $product->price ?></h2>
                                                                    <p><a href="<?= App::getLink('Product', array('id' => $product->getId()))?>"><?= $product->name ?></a></p>
                                                                    <a href="#" class="btn btn-default add-to-cart"><i class="fa fa-shopping-cart"></i>Add to cart</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </section>

            <?= $this->displayTemplate('chunks/footer') ?>
        </div>

        <?= $this->displayTemplate('chunks/scripts') ?>
    </body>
</html>




