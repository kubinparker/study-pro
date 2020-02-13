<div class="title_area">
    <h1>CMS管理画面</h1>
    <div class="pankuzu">
        <ul>
            <li><a href="/admin/" class="icon-icn_home">&nbsp;</a></li>
            <li><span>CMS管理画面</span></li>
        </ul>
    </div>
</div>

<?= $this->element('error_message'); ?>

<div class="content_inr">
    <div class="box">
        <h3>メインメニュー</h3>
        <div class="list_area">
            <ul class="list">
                <li>
                    <a href="">新着情報</a>
                </li>
            </ul>
        </div>
    </div>

    <!-- <?php if ((int) $this->Session->read('role') === 0) : ?>
        <div class="box">
            <h3>システム管理メニュー</h3>
            <div class="list_area">
                <ul class="list">
                    <li>
                        <a href="<?= $this->Html->url(['admin' => true, 'controller' => 'competitionMasters', 'action' => 'index']); ?>">競技マスタ管理</a>
                    </li>
                    <li>
                        <a href="<?= $this->Html->url(['admin' => true, 'controller' => 'mst_literal', 'action' => 'index']); ?>">定数管理</a>
                    </li>
                </ul>
            </div>
        </div>
    <?php endif; ?> -->
</div>