
<?= $this->element('error_message'); ?>
<div class="content_inr">
    <div class="box" style="max-width:600px;margin-left:auto;margin-right:auto;">
        <?= $this->Form->create($data, ['id' => 'AdminIndexForm']); ?>
        <div class="table_area form_area">
            <table class="vertical_table">
                <tr>
                    <td>ユーザーID</td>
                    <td><input name="username" type="text" id="id" /></td>
                </tr>
                <tr>
                    <td>パスワード</td>
                    <td><input name="password" type="password" id="pw" /></td>
                </tr>
            </table>
            <div class="btn_area">
                <a href="javascript:void(0);" class="btn_confirm">ログイン</a>
            </div>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>

