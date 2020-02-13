<div class="title_area">
        <h1>メンバー<?= ($data[$ModelName]['id'] > 0) ? '編集' : '新規登録'; ?></h1>
        <div class="pankuzu">
            <ul>
                <li><a href="/admin/" class="icon-icn_home">&nbsp;</a></li>
                <li><a href="<?= $this->Html->url(array('action' => 'index')); ?>">一覧画面</a></li>
                <li><span><?= ($data[$ModelName]['id'] > 0) ? '編集' : '新規登録'; ?></span></li>
            </ul>
        </div>
    </div>

    <?= $this->element('error_message'); ?>
    <div class="content_inr">
        <div class="box">
            <h3><?= ($data[$ModelName]['id'] > 0) ? '編集' : '新規登録'; ?></h3>
            <div class="table_area form_area">
                <?= $this->Form->create($ModelName, ['type' => 'file', 'inputDefaults' => ['label' => false, 'div' => false]]); ?>
                <?= $this->Form->input('id', ['type' => 'hidden']); ?>
                <?= $this->Form->input('admin_token', ['type' => 'hidden']); ?>
                <table class="vertical_table">

                    <tr>
                        <td>No</td>
                        <td><?= ($this->Form->value('id')) ? sprintf('No. %04d', $this->Form->value('id')) : "新規" ?></td>
                    </tr>

                    <tr>
                        <td>掲載日</td>
                        <td><?= $this->Form->input('date', ['type' => 'text', 'class' => 'datepicker', 'data-auto-date' => '1', 'style' => 'width:100px', 'default' => date('Y-m-d')]); ?></td>
                    </tr>

                    <tr>
                        <td>Name</td>
                        <td><?= $this->Form->input('name', ['type' => 'text', 'maxlength' => 50]); ?><span> 最大50文字</span></td>
                    </tr>

                    <tr>
                        <td>Username<span class="attent">※必須</span></td>
                        <td><?= $this->Form->input('username', ['type' => 'text', 'maxlength' => 20]); ?><span> 最大20文字</span></td>
                    </tr>

                    <tr>
                        <td>Password<span class="attent">※必須</span></td>
                        <td><?= $this->Form->input('password', ['type' => 'password']); ?></td>
                    </tr>

                    <tr>
                        <td>Re-Password<span class="attent">※必須</span></td>
                        <td><?= $this->Form->input('repassword', ['type' => 'password']); ?></td>
                    </tr>

                    <tr>
                        <td>Sex</td>
                        <td><?= $this->Form->input('sex', ['options' => ['女', '男'], 'style' => 'height: 25px; width: 50px;']); ?></td>
                    </tr>

                    <tr>
                        <td>Role</td>
                        <td><?= $this->Form->input('role', ['options' => $list_role, 'style' => 'height: 25px; width: 200px;']); ?></td>
                    </tr>
                    
                    <tr>
                        <td class="text_image">アバター画像</td>
                        <td>
                            <?= $this->Form->input('avatar', ['type' => 'file', 'accept' => 'image/png,image/jpeg,image/gif']); ?>
                            <div class="remark">※jpeg , jpg , gif , png ファイルのみ</div>
                        </td>
                    </tr>
                </table>

                <div class="btn_area">
                    <?php if (!empty($data[$ModelName]['id']) && $data[$ModelName]['id'] > 0) { ?>
                        <a href="#" class="btn_confirm submitButton">変更する</a>
                        <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Html->url(array('action' => 'delete', $data[$ModelName]['id'], 'content')) ?>')" class="btn_delete">削除する</a>
                    <?php } else { ?>
                        <a href="#" class="btn_confirm submitButton">登録する</a>
                    <?php } ?>
                </div>

            </div>
            <?= $this->Form->end(); ?>
        </div>
    </div>


    <?php $this->start('beforeBodyClose'); ?>
    <link rel="stylesheet" href="/common/css/cms.css">
    <link rel="stylesheet" href="/common/css/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css">
    <script src="/common/js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="/common/js/jquery.ui.datepicker-ja.js"></script>
    <script src="/common/js/cms.js"></script>
    <!-- redactor -->
    <link rel="stylesheet" href="/common/css/redactor/redactor.min.css">
    <script src="/common/js/redactor/redactor.min.js"></script>
    <!-- redactor plugins -->
    <script src="/common/js/redactor/ja.js"></script>
    <script src="/common/js/redactor/alignment.js"></script>
    <script src="/common/js/redactor/counter.js"></script>
    <script src="/common/js/redactor/fontcolor.js"></script>
    <script src="/common/js/redactor/imagemanager.js"></script>
    <script>
        $R('#content_type1,#content_type2', {
            focus: false,
            minHeight: '200px',
            //imageUpload: '/admin/info_images/image_upload.json',
            //imageManagerJson: '/admin/info_images/image_list.json',
            //imagePosition: true,
            //imageResizable: true,
            multipleUpload: false,
            plugins: ['fontsize', 'fontcolor', 'counter', 'alignment', 'imagemanager'],
            buttonsHide: ['format', 'italic', 'lists', 'image'],
            lang: 'ja',
            pastePlainText: true,
            //breakline: true,
            //markup: 'br' 
        });
        function changeTextImage (e) {
            let $this = $(e);
            let $textImage = $('.text_image');
            let size = {1 : '大',2 : '中',3 : '小'};
            $textImage.text(size[parseInt($this.val())] ? 'バナー画像' + size[parseInt($this.val())] : 'バナー画像大');
            $('.view_image_'+parseInt($this.val())).show().siblings('div.view_image, .error-message').hide();
        }
    </script>
    <?php $this->end(); ?>