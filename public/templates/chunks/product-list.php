<h2 class="title text-center"><?= $this->product_list_title ?></h2>

<?php if ($this->product_list_items) { ?>
    <?php foreach ($this->product_list_items as $product) { ?>
        <div class="col-sm-4">
            <div class="product-image-wrapper">
                <div class="single-products">
                    <div class="productinfo text-center">
                        <img src="/assets/images/products/<?= $product->cover->file ?>.png" width="255" height="237" alt="">
                        <h2><?= $product->price ?></h2>
                        <p><a href="<?= App::getLink('Product', array('id' => $product->getId())) ?>"><?= $product->name ?></a></p>
                        <a class="btn btn-default add-to-cart" data-id="<?= $product->getId() ?>" href="" ><i class="fa fa-shopping-cart"></i>Add to cart</a>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>
