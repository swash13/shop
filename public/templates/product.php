<!DOCTYPE html>
<html lang="en">
    <head>
        <?= $this->displayTemplate('chunks/meta'); ?>

        <title>Товар</title>
    </head><!--/head-->

    <body>
        <div class="wrapper">
            <?= $this->displayTemplate('chunks/header'); ?>

            <section>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3">
                            <?= $this->displayTemplate('chunks/aside'); ?>
                        </div>

                        <div class="col-sm-9 padding-right">
                            <div class="product-details">
                                <div class="col-sm-5">
                                    <div class="view-product">
                                        <?php if (count($this->product->images)) { ?>
                                            <img src="<?= App::siteURL("assets/images/products/{$this->product->images[0]->file}_329x380.png") ?>" alt="" />
                                        <?php } else { ?>
                                            <img src="<?= App::siteURL("assets/images/default_329x380.png") ?>" alt="" />
                                        <?php } ?>
                                    </div>

                                    <?php if (count($this->product->images) > 1) { ?>
                                        <div id="similar-product" class="carousel slide" data-ride="carousel">
                                            <div class="carousel-inner">
                                                <?php $images = $this->product->images; array_shift($images); ?>
                                                <?php foreach ($images as $index => $image) { ?>
                                                    <?php if (($index % 3) == 0) { ?>
                                                        <div class="item <?= ($index == 0) ? 'active' : '' ?>">
                                                    <?php } ?>

                                                    <?php if (($index % 3) == 2) { ?>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>

                                                <?php if (($index % 3) != 2) { ?>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <a class="left item-control" href="#similar-product" data-slide="prev">
                                                <i class="fa fa-angle-left"></i>
                                            </a>
                                            <a class="right item-control" href="#similar-product" data-slide="next">
                                                <i class="fa fa-angle-right"></i>
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>

                                <div class="col-sm-7">
                                    <div class="product-information">
                                        <h2><?= $this->product->name ?></h2>
                                        <p>Web ID: <?= $this->product->articul ?></p>

								        <span>
									        <span><?= $this->product->price ?>$</span>
									        <label>Quantity:</label>
									        <input type="text" value="1" />
									        <button type="button" class="btn btn-fefault cart">
                                                <i class="fa fa-shopping-cart"></i>
                                                Add to cart
                                            </button>
                                        </span>

                                        <p><b>Category:</b> <?= $this->product->category->name ?></p>
                                        <p><b>Brand:</b> <?= $this->product->brand->name ?></p>
                                    </div>
                                </div>
                            </div>

                            <?php if ($this->product->description) { ?>
                                <div class="category-tab shop-details-tab">
                                    <div class="col-sm-12">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a href="#details" data-toggle="tab">Details</a></li>
                                        </ul>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane fade active in" id="details" >
                                            <?= $this->product->description ?>
                                        </div>
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