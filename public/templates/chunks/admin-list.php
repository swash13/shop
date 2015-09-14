<div class="list-wrapper <?= $this->classes ?>" <?= $this->attributes ?>>
    <div class="list">
        <?php if (!empty($this->actions)) { ?>
            <div class="clear">
                <ul class="actions clear">
                    <?php foreach ($this->actions as $action => $data) { ?>
                        <li><a class="<?= isset($data['icon']) ? $data['icon'] : '' ?>" href="<?= App::getLink($data['controller'], array('action' => $action)) ?>" title="<?= isset($data['hint']) ? $data['hint'] : '' ?>"><?= isset($data['title']) ? $data['title'] : '' ?></a></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>

        <table>
            <thead>
            <tr>
                <?php foreach ($this->fields as $field => $data) { ?>
                    <th><?= $data['title'] ?></th>
                <?php } ?>

                <?php if (!empty($this->itemActions)) { ?>
                    <th></th>
                <?php } ?>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($this->items as $item) { ?>
                <tr>
                    <?php foreach ($this->fields as $field => $data) { ?>
                        <td>
                            <?php
                            $itemField = $item;

                            if (strpos($field, '->')) {
                                $links = explode('->', $field);
                                $field = array_pop($links);

                                foreach ($links as $link) {
                                    if ($itemField) {
                                        if (is_array($itemField)) {
                                            $itemField = $itemField[$link];
                                        } else {
                                            $itemField = $itemField->{$link};
                                        }
                                    } else {
                                        break;
                                    }
                                }
                            }

                            switch ($data['type']) {
                                case 'text' :
                                    echo $itemField ? (is_array($itemField) ? $itemField[$field] : $itemField->{$field}) : null;
                                    break;
                                case 'id' :
                                    echo $itemField ? (is_array($itemField) ? $itemField['id'] : $itemField->getId()) : null;
                                    break;
                                case 'image' :
                                    if ($itemField && (is_array($itemField) ? $itemField[$field] : $itemField->{$field})) {
                                        ?><img src="/assets/images/<?= $data['directory'] ?>/<?= (is_array($itemField) ? $itemField[$field] : $itemField->{$field})?><?= $data['size'] ? "_{$data['size']}" : '' ?>.png" /><?php
                                    } else {
                                        ?><img src="/assets/images/default<?= $data['size'] ? "_{$data['size']}" : '' ?>.png" /><?php
                                    }
                                    break;
                                case 'active' :
                                    if ($data['action']) {
                                        ?><a class="active <?= ($itemField && (is_array($itemField) ? $itemField[$field] : $itemField->{$field})) ? 'on' : 'off' ?>" href="<?= App::getLink($data['controller'], array('action' => $data['action'], 'id' => (is_array($itemField) ? $itemField['id'] : $itemField->getId()))) ?>"></a><?php
                                    } else {
                                        ?><a class="active <?= (is_array($itemField) ? $itemField[$field] : $itemField->{$field}) ? 'on' : 'off' ?>"></a><?php
                                    }
                                    break;
                            }
                            ?>
                        </td>
                    <?php } ?>

                    <?php if (!empty($this->itemActions)) { ?>
                        <td>
                            <ul class="actions">
                                <?php foreach ($this->itemActions as $action => $data) { ?>
                                    <?php if (isset($data['icon']) && !($data['icon'] == 'up' && $item == $this->items[0]) && !($data['icon'] == 'down' && $item == $this->items[count($this->items) - 1])) { ?>
                                        <li><a class="<?= isset($data['icon']) ? $data['icon'] : '' ?>" href="<?= App::getLink($data['controller'], array('action' => $action, 'id' => is_array($item) ? $item['id'] : $item->getId())) ?>" title="<?= $data['hint'] ?>"><?= isset($data['title']) ? $data['title'] : '' ?></a></li>
                                    <?php } ?>
                                <?php } ?>
                            </ul>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>