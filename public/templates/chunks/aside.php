<div class="left-sidebar">
    <h2>Категории</h2>
    <div class="panel-group category-products" id="accordian">
        <!--
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordian" href="#sportswear">
                            <span class="badge pull-right"><i class="fa fa-plus"></i></span>
                            Sportswear
                        </a>
                    </h4>
                </div>
                <div id="sportswear" class="panel-collapse collapse">
                    <div class="panel-body">
                        <ul>
                            <li><a href="#">Nike </a></li>
                            <li><a href="#">Under Armour </a></li>
                            <li><a href="#">Adidas </a></li>
                            <li><a href="#">Puma</a></li>
                            <li><a href="#">ASICS </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        -->
        <?php foreach ($this->aside_categories as $category) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a href="<?= App::getLink('Catalog', array('category' => $category->getId())) ?>">
                            <?= $category->name ?>
                        </a>
                    </h4>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="brands_products">
        <h2>Производители</h2>
        <div class="brands-name">
            <ul class="nav nav-pills nav-stacked">
                <?php foreach ($this->aside_brands as $brand) { ?>
                    <li><a href="<?= App::getLink('Catalog', array('brand' => $brand->getId())) ?>"><?= $brand->name ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <?php if (App::getController() != 'ProductController' && ($this->filter_price['min'] != $this->filter_price['max'])) { ?>
        <div class="price-range">
            <h2>Цены</h2>
            <div class="well text-center">
                <input type="text" class="span2" value="" data-slider-min="<?= floor($this->filter_price['min']) ?>" data-slider-max="<?= ceil($this->filter_price['max']) ?>" data-slider-step="1" data-slider-value="[<?= $this->filter_price['min'] ?>,<?= $this->filter_price['max'] ?>]" id="sl2"><br>
                <b class="pull-left">$ <?= $this->filter_price['min'] ?></b> <b class="pull-right">$ <?= $this->filter_price['max'] ?></b>
            </div>
        </div>
    <?php } ?>
</div>