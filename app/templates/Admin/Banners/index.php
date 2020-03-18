<div class="title_area">
        <h1>新着バナー</h1>
        <div class="pankuzu">
            <ul>
                <li><span>新着バナー</span></li>
            </ul>
        </div>
    </div>

    <div class="content_inr">
        <div class="box">
            <h3>登録一覧</h3>
            <div class="btn_area" style="margin-top:10px; padding-bottom:10px"><?= $this->Html->link('新規登録',['action' => 'edit'], ['class' => 'btn_confirm']); ?></div>
            <span class="result"><?php echo $numrows; ?>件の登録</span>
            <div class="table_area">
                <table>
                    <tr>
                        <th style="width:4em;">ステータス</th>
                        <th style="width:4em;">No</th>
                        <th style="width:8em;">公開日</th>
                        <th style="">企業・団体</th>
                        <th style="width:50em;">リンク</th>
                        <th style="text-align:left;width:27px;">確認</th>
                        <th style="width:12em;">順序の変更</th>
                    </tr>
                    <?php foreach ($datas as $key => $_) : ?>
                        <?php $status = $_['status'] == 'publish' ? true : false ?>

                        <a name="m_<?= $_['id'] ?>"></a>
                        <tr class="<?= $status ? "visible" : "unvisible" ?>" id="content-<?= $_['id'] ?>">
                            <td>
                                <div class="<?= $status ? "visi" : "unvisi" ?>"><?= $this->Html->link(($status ? "公開" : "下書き"), ['action' => 'enable', $_['id']]); ?></div>
                            </td>
                            <td title="表示順：<?= $_['position'] ?>" style="text-align:right;">
                                <?= sprintf("%d", $_['id']) ?>
                            </td>
                            <td style="text-align: center;">
                                <?= !empty($_['date']) ? $_['date'] : "&nbsp;" ?>
                            </td>
                            <td style="text-indent: 1em;">
                                <?= $this->Html->link(h($_["title"]), ['action' => 'edit', $_['id']]); ?>
                            </td>
                            <td style="text-indent: 1em; word-break: break-all;">
                                <span><?= h($_["link"])?></span>
                            </td>
                            <td>
                                <?php $url = $status ? ['controller' => 'homes', 'action' => 'index'] : ['controller' => 'homes', 'action' => 'index', '?' => ['preview' => 'on', 'id' => $_['id']]] ?>
                                <?php $preview_url = $this->Html->link('',$url); ?>
                                <div class=" prev"><?= $this->Html->link('プレビュー',$url, ['target'=>'_blank']) ?></div>
                            </td>
                            <td>
                                <ul class="ctrlis">
                                    <?php if ($key > 0) : ?>
                                        <li class="cttop"><?= $this->Html->link('top', ['action' => 'position', $_["id"], 'top']) ?></li>
                                        <li class="ctup"><?= $this->Html->link('top', ['action' => 'position', $_["id"], 'up']) ?></li>
                                    <?php else : ?>
                                        <li class="non">&nbsp;</li>
                                        <li class="non">&nbsp;</li>
                                    <?php endif; ?>

                                    <?php if ($key < count($datas) - 1) : ?>
                                        <li class="ctdown"><?= $this->Html->link('top', ['action' => 'position', $_["id"], 'down']) ?></li>
                                        <li class="ctend"><?= $this->Html->link('bottom', ['action' => 'position', $_["id"], 'bottom']) ?></li>
                                    <?php else : ?>
                                        <li class="non">&nbsp;</li>
                                        <li class="non">&nbsp;</li>
                                    <?php endif; ?>
                                </ul>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </table>

                <div class="btn_area" style="margin-top:10px;"><?= $this->Html->link('新規登録', ['action' => 'edit'], ['class' => 'btn_confirm']); ?></div>

            </div>
        </div>

        <?php $this->start('beforeBodyClose'); ?>
        <link rel="stylesheet" href="/admin/common/css/cms.css">
        <script src="/admin/common/js/cms_index.js"></script>
        <script>
            $(function() {



            })
        </script>
        <?php $this->end(); ?>