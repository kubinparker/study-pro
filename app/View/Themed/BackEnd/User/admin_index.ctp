<div class="title_area">
        <h1>一覧画面</h1>
        <div class="pankuzu">
            <ul>
                <li><a href="/admin/" class="icon-icn_home">&nbsp;</a></li>
                <li><span>一覧画面</span></li>
            </ul>
        </div>
    </div>

    <div class="content_inr">
        <div class="box">
            <h3>登録一覧</h3>
            <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Html->url(array('action' => 'edit')); ?>" class="btn_confirm">新規登録</a></div>
            <br/>
            <span class="result"><?php echo $numrows ?? '0' ; ?>件の登録</span>
            <div class="table_area">
                <table>
                    <tr>
                        <th style="width:5em;">Status</th>
                        <th style="">Name</th>
                        <th style="">Username</th>
                        <th style="width:4em;">Sex</th>
                        <th style="width:8em;">Role</th>
                        <th style="width:5em">Review</th>
                        <th style="width:12em;">Sort</th>
                    </tr>

                    <?php foreach ($datas as $key => $_) : ?>
                        <?php $status = (int)$_[$ModelName]['status'] == 0 ? true : false ?>
                        <?php $role = (int)$_[$ModelName]['role'] ?>
                        <tr class="<?= $status ? "visible" : "unvisible" ?>" id="content-<?= $_[$ModelName]['id'] ?>">
                            <td>
                                <div class="<?= $status ? "visi" : "unvisi" ?>"><?= $this->Html->link(($status ? "公開" : "下書き"), array('action' => 'enable', $_[$ModelName]['id'])); ?></div>
                            </td>
                            <td>
                                <?= h($_[$ModelName]['name']) ?>
                            </td>
                            <td>
                                <?= h($_[$ModelName]['username']) ?>
                            </td>
                            <td style="text-align: center;">
                                <?= (int)$_[$ModelName]['sex'] == 1 ? '女' : '男' ?>
                            </td>
                            <td style="text-align: center;">
                                <?= $list_role[$role]?>
                            </td>
                            <td>
                                <?php $preview_url = $this->Html->url(['admin' => false, 'controller' => 'homes', 'action' => 'index', '?' => ['preview' => 'on']]); ?>
                                <div class="prev"><a href="<?= $preview_url ?>" target="_blank">プレビュー</a></div>
                            </td>
                            <td>
                                <ul class="ctrlis">
                                    <?php if ($key > 0) : ?>
                                        <li class="cttop"><?= $this->Html->link('top', array('action' => 'position', $_[$ModelName]["id"], 'top')) ?></li>
                                        <li class="ctup"><?= $this->Html->link('top', array('action' => 'position', $_[$ModelName]["id"], 'up')) ?></li>
                                    <?php else : ?>
                                        <li class="non">&nbsp;</li>
                                        <li class="non">&nbsp;</li>
                                    <?php endif; ?>

                                    <?php if ($key < count($datas) - 1) : ?>
                                        <li class="ctdown"><?= $this->Html->link('top', array('action' => 'position', $_[$ModelName]["id"], 'down')) ?></li>
                                        <li class="ctend"><?= $this->Html->link('bottom', array('action' => 'position', $_[$ModelName]["id"], 'bottom')) ?></li>
                                    <?php else : ?>
                                        <li class="non">&nbsp;</li>
                                        <li class="non">&nbsp;</li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </table>

                <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Html->url(array('action' => 'edit')); ?>" class="btn_confirm">新規登録</a></div>

            </div>
        </div>

        <?php $this->start('beforeBodyClose'); ?>
        <link rel="stylesheet" href="/common/css/cms.css">
        <script src="/common/js/cms_index.js"></script>
        <script>
            $(function() {

            })
        </script>
        <?php $this->end(); ?>