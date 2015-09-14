<!DOCTYPE html>
<html lang="en">
    <head>
        <?= $this->displayTemplate('chunks/meta'); ?>

        <title>Интернет магазин</title>
    </head>

    <body>
        <div class="wrapper">
            <?= $this->displayTemplate('chunks/header'); ?>

            <section id="cart_items">
                <div class="container">
                    <div class="table-responsive cart_info">
                        <table class="table table-condensed">
                            <thead>
                                <tr class="cart_menu">
                                    <td class="image">Item</td>
                                    <td class="description"></td>
                                    <td class="price">Price</td>
                                    <td class="quantity">Quantity</td>
                                    <td class="total">Total</td>
                                    <td></td>
                                </tr>
                            </thead>

                            <tbody>
                            </tbody>
                                <?php foreach ($this->cart_items as $product) { ?>
                                    <tr>
                                        <td class="cart_product">
                                            <a href="<?= App::getLink('Product', array('id' => $product->getId())) ?>">
                                                <?php if ($product->cover) { ?>
                                                    <img src="<?= App::siteURL("assets/images/products/{$product->cover->file}_110x110.png") ?>" alt="">
                                                <?php } else { ?>
                                                    <img src="<?= App::siteURL("assets/images/default_110x110.png") ?>" alt="">
                                                <?php } ?>
                                            </a>
                                        </td>
                                        <td class="cart_description">
                                            <h4><a href="<?= App::getLink('Product', array('id' => $product->getId())) ?>"><?= $product->name ?></a></h4>
                                            <p>Web ID: <?= $product->articul ?></p>
                                        </td>

                                        <td class="cart_price">
                                            <p>$<?= $product->price ?></p>
                                        </td>

                                        <td class="cart_quantity">
                                            <div class="cart_quantity_button">
                                                <a class="cart_quantity_up" href=""> + </a>
                                                <input class="cart_quantity_input" type="text" name="quantity" value="<?= $product->quantity ?>" autocomplete="off" size="2">
                                                <a class="cart_quantity_down" href=""> - </a>
                                            </div>
                                        </td>
                                        <td class="cart_total">
                                            <p class="cart_total_price">$<?= $product->price * $product->quantity ?></p>
                                        </td>
                                        <td class="cart_delete">
                                            <a class="cart_quantity_delete" href=""><i class="fa fa-times"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            <?= $this->displayTemplate('chunks/footer') ?>
        </div>

        <?= $this->displayTemplate('chunks/scripts') ?>
    </body>
</html>




