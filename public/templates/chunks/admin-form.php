<div class="form-wrapper <?= $this->classes ?>">
    <form action="" method="post">
        <?php foreach ($this->fields as $name => $field) { ?>
            <?php
                switch ($field['type']) {
                    case 'text' :
                        ?>
                            <?php if (isset($field['error'])) { ?>
                                <div class="error"><?= $field['error'] ?></div>
                            <?php } ?>

                            <div class="field text">
                                <?php if ($field['langs']) { ?>
                                    <?php foreach (App::getLangs() as $lang) { ?>
                                        <span class="langs-field lang-<?= $lang->code ?> <?= ($lang->getId() == 1) ? 'active' : '' ?>">
                                            <label for="<?= $name ?>_<?= $lang->getId() ?>_field"><?= $field['title'] ?></label>
                                            <input type="text" id="<?= $name ?>_<?= $lang->getId() ?>_field" name="<?= $name ?>[<?= $lang->getId() ?>]" value="<?= $field['value'][$lang->getId()] ?>" />
                                        </span>
                                    <?php } ?>

                                    <ul class="langs-list clear">
                                        <?php foreach (App::getLangs() as $lang) { ?>
                                            <li><a class="<?= ($lang->getId() == 1) ? 'active' : '' ?>" data-lang="<?= $lang->code ?>" href="" onclick="return false;"></a></li>
                                        <?php } ?>
                                    </ul>
                                <?php } else { ?>
                                    <label for="<?= $name ?>_field"><?= $field['title'] ?></label>
                                    <input type="text" id="<?= $name ?>_field" name="<?= $name ?>" value="<?= $field['value'] ?>" />
                                <?php } ?>
                            </div>
                        <?php
                        break;

                    case 'textarea' :
                        ?>
                            <?php if (isset($field['error'])) { ?>
                                <div class="error"><?= $field['error'] ?></div>
                            <?php } ?>

                            <div class="field textarea">
                                <?php if ($field['langs']) { ?>
                                    <?php foreach (App::getLangs() as $lang) { ?>
                                        <span class="langs-field lang-<?= $lang->code ?> <?= ($lang->getId() == 1) ? 'active' : '' ?>">
                                            <label for="<?= $name ?>_<?= $lang->getId() ?>_field"><?= $field['title'] ?></label>
                                            <textarea id="<?= $name ?>_<?= $lang->getId() ?>_field" name="<?= $name ?>[<?= $lang->getId() ?>]"><?= $field['value'][$lang->getId()] ?></textarea>
                                        </span>
                                    <?php } ?>

                                    <ul class="langs-list clear">
                                        <?php foreach (App::getLangs() as $lang) { ?>
                                            <li><a class="<?= ($lang->getId() == 1) ? 'active' : '' ?>" data-lang="<?= $lang->code ?>" href="" onclick="return false;"></a></li>
                                        <?php } ?>
                                    </ul>
                                <?php } else { ?>
                                    <label for="<?= $name ?>_field"><?= $field['title'] ?></label>
                                    <textarea class="<?= $field['editor'] ? 'editor' : '' ?>" id="<?= $name ?>_field" name="<?= $name ?>"><?= $field['value'][$lang] ?></textarea>
                                <?php } ?>
                            </div>
                        <?php
                        break;

                    case 'select' :
                        ?>
                            <?php if (isset($field['error'])) { ?>
                                <span class="error"><?= $field['error'] ?></span>
                            <?php } ?>

                            <div class="field select">
                                <label for="<?= $name ?>_field"><?= $field['title'] ?></label>
                                <select name="<?= $name ?>">
                                    <?php if ($field['empty']) { ?>
                                        <option value=""><?= $field['empty'] ?></option>
                                    <?php } ?>

                                    <?php foreach ($field['options'] as $option) { ?>
                                        <option value="<?= $option['value'] ?>" <?= ($field['value'] == $option['value']) ? 'selected' : '' ?> ><?= $option['text'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php
                        break;

                    case 'checkbox' :
                        ?>
                            <div class="field checkbox">
                                <label for="<?= $name ?>"><?= $field['title'] ?></label>
                                <input type="checkbox" id="<?= $name ?>" name="<?= $name ?>" value="1" <?= $field['checked'] ? 'checked' : '' ?> />
                                <label class="checkbox-overlay" for="<?= $name ?>"></label>
                            </div>
                        <?php
                        break;

                    case 'uploader' :
                        ?>
                            <?php if (isset($field['error'])) { ?>
                                <span class="error"><?= $field['error'] ?></span>
                            <?php } ?>

                            <div class="field uploader" data-post-name="<?= $name ?>" data-url="<?= $field['url'] ?>" data-file-types="<?= $field['file_types'] ?>" data-file-description="<?= $field['file_description'] ?>" data-size-limit="<?= $field['size_limit'] ?>">
                                <label for="<?= $name ?>_field"><?= $field['title'] ?></label>
                                <div class="uploader-placeholder" id="<?= $name ?>_field"></div>
                            </div>

                            <div class="uploader-value" id="<?= $name ?>_value">
                                <?php
                                if (is_object($field['value'])) {
                                    echo $field['value']->display();
                                } else {
                                    echo $field['value'];
                                }
                                ?>

                                <?php if ($field['post_params']) { ?>
                                    <input type="hidden" id="<?= $name ?>_post_params" name="<?= $name ?>_post_params" value='<?= json_encode($field['post_params']) ?>' />
                                <?php } ?>
                            </div>
                        <?php
                        break;
                }
            ?>
        <?php } ?>

        <div>
            <input type="submit" value="Сохранить" />
        </div>
    </form>
</div>
