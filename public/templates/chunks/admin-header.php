<header class="clear">
    <nav>
        <ul class="clear">
            <li><a class="<?= (App::getController() == 'AdminProducts') ? 'active' : '' ?>" href="<?= App::getLink('AdminProducts') ?>"><?= App::t('Товары') ?></a></li>
            <li><a class="<?= (App::getController() == 'AdminCategories') ? 'active' : '' ?>" href="<?= App::getlink('AdminCategories') ?>"><?= App::t('Категории') ?></a></li>
            <li><a class="<?= (App::getController() == 'AdminBrands') ? 'active' : '' ?>" href="<?= App::getLink('AdminBrands') ?>"><?= App::t('Производители') ?></a></li>
            <li class="logout"><a href=""><?= App::t('Logout') ?></a></li>
        </ul>
    </nav>

    <ul class="langs-list clear">
        <?php foreach (App::getLangs() as $lang) { ?>
            <li><a class="<?= ($lang->getId() == App::currentLang()->getId()) ? 'active' : '' ?>" data-lang="<?= $lang->code ?>" href="<?= App::getLink('Lang', array('lang' => $lang->getId())) ?>"></a></li>
        <?php } ?>
    </ul>
</header>
