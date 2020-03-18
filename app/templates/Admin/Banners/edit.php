<div class="title_area">
    <h1>バナー</h1>
    <div class="pankuzu">
        <ul>
            <?= $this->element('pankuzu_home'); ?>
            <li><?= $this->Html->link('バナー管理', ['action' => 'index']); ?></li>
            <li><span><?= ($data[$ModelName]['id'] > 0) ? '編集' : '新規登録'; ?></span></li>
        </ul>
    </div>
</div>
<?= $this->element('error_message'); ?>
<div class="content_inr">
    <div class="box">
        <h3><?= ($data[$ModelName]['id'] > 0) ? '編集' : '新規登録'; ?></h3>
        <div class="table_area form_area">
            <?= $this->Form->create($data[$ModelName], ['type' => 'file', 'label' => false]); ?>
            <?= $this->Form->hidden('position', ['default' => 0]); ?>
            <?= $this->Form->input('id', array('type' => 'hidden')); ?>
            <?= $this->Form->input('admin_token', ['type' => 'hidden']); ?>
            <table class="vertical_table">

                <tr>
                    <td>No</td>
                    <td><?= ($this->Form->value('id')) ? sprintf('No. %04d', $this->Form->value('id')) : "新規" ?></td>
                </tr>

                <tr>
                    <td>掲載日</td>
                    <td><?= $this->Form->input('date', ['type' => 'date', 'class' => 'datepicker', 'data-auto-date' => '1', 'style' => 'width:200px', 'default' => date('Y-m-d')]); ?></td>
                </tr>

                <tr>
                    <td>企業・団体<span class="attent">※必須</span></td>
                    <td><?= $this->Form->control('title', ['type' => 'text', 'maxlength' => 40, 'autofocus','label' => false]); ?><span> 最大40文字</span></td>
                </tr>

                <tr>
                    <td>リンク<span class="attent">※必須</span></td>
                    <td><?= $this->Form->control('link', ['type' => 'text', 'autofocus', 'label' => false]); ?></td>
                </tr>

                <tr>
                    <td>
                        サイズ<span class="attent">※必須</span><br/>
                        <div style="font-size:11px;">
                            &nbsp;&nbsp;大｜708×213<br/>
                            &nbsp;&nbsp;中｜540×150<br/>
                            &nbsp;&nbsp;小｜344×100
                        </div>
                    </td>
                    <td><?= $this->Form->select('size', $Size, ['style' => 'width: 80px;height: 30px;', 'onchange' => 'changeTextImage(this)']); ?></td>
                </tr>
                
                <tr>
                    <td class="text_image">バナー画像<?= isset($Size[$data[$ModelName]['size']]) ? $Size[$data[$ModelName]['size']] : '大'; ?></td>
                    <td>
                        <?= $this->Form->input('image'. (isset($data[$ModelName]['size']) ? $data[$ModelName]['size'] : 1), ['type' => 'file', 'accept' => 'image/png,image/jpeg,image/gif', 'id' => 'image']); ?>
                        <div class="remark">※jpeg , jpg , gif , png ファイルのみ</div>

                        <?php foreach ($Size as $k=>$v) : ?>
                            <div class="view_image view_image_<?=$k?>" style="display:<?= $data[$ModelName]['size'] == $k ? "block" : "none"; ?>">
                                <?php if (!empty($data[$ModelName]->attaches)) : ?>
                                    <?php if (!empty($data[$ModelName]->attaches[ATTACHES_IMAGE])) :?>
                                        <?php if (!empty($data[$ModelName]->attaches[ATTACHES_IMAGE]['image'.$k])) :?>
                                            <br />
                                            <?= $this->Html->image($data[$ModelName]->attaches[ATTACHES_IMAGE]['image'.$k]['s'])?><br>
                                            <div class="btn_area"><?= $this->Html->link('画像の削除', ['action' => 'delete', $data[$ModelName]['id'], 'image', 'image'.$k], ['class' => 'btn_delete', 'confirm' => '画像大を削除します。よろしいですか？'] ) ?></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>
                <?php for($i = 1; $i < 3; $i++) :?>
                <tr>
                    <td class="text_file">ファイル<?= $i?></td>
                    <td>
                        <?= $this->Form->input('file' . $i, ['type' => 'file', 'accept' => 'pdf,doc,docx,xls,xlsx']); ?>
                        <div class="remark">※PDF、Office(.doc, .docx, .xls, .xlsx)ファイルのみ</div>
                        <?php if (!empty($data[$ModelName]->attaches)) : ?>
                            <?php if (!empty($data[$ModelName]->attaches[ATTACHES_FILE])) :?>
                                <?php if (!empty($data[$ModelName]->attaches[ATTACHES_FILE]['file'.$i])) :?>
                                    <ul class="file">
                                        <li class="<?= $data[$ModelName]->attaches[ATTACHES_FILE]['file'.$i]['extention'] ?>"><?= $this->Html->link($data[$ModelName]->{'file'.$i.'_name'}, $data[$ModelName]->attaches[ATTACHES_FILE]['file'.$i][0], ['target' => '_blank']) ?></li>
                                    </ul>
                                    <ul class="return">
                                        <li class="btn_area"><?= $this->Html->link('ファイルの削除', ['action' => 'delete', $data[$ModelName]->id, 'file', 'file'.$i], ['confirm' => 'ファイルを削除します。よろしいですか？', 'class' => 'btn_delete']) ?></li>
                                    </ul>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endfor;?>
                <tr>
                    <td class="text_file">ファイル２</td>
                    <td>
                        <?= $this->Form->input('file2', ['type' => 'file', 'accept' => 'pdf,doc,docx,xls,xlsx']); ?>
                        <div class="remark">※pdf, doc, docx, xls, xlsx ファイルのみ</div>
                    </td>
                </tr>
            </table>

            <div class="btn_area">
                <?php if (!empty($data[$ModelName]['id']) && $data[$ModelName]['id'] > 0) { ?>
                    <a href="#" class="btn_confirm submitButton">変更する</a>
                    <?php echo $this->Html->link('削除する', 
                    ['controller' => 'Banners', 'action' => 'delete', $data[$ModelName]['id'], 'content'], 
                    [
                        'class' => 'btn_delete',
                        'confirm' => 'データを完全に削除します。よろしいですか？'
                    ]
                    ); ?>
                <?php } else { ?>
                    <a href="#" class="btn_confirm submitButton">登録する</a>
                <?php } ?>
            </div>

        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>


    <?php $this->start('beforeBodyClose'); ?>
        <link rel="stylesheet" href="/admin/common/css/cms.css">
        <link rel="stylesheet" href="/admin/common/css/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css">
        <script src="/admin/common/js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="/admin/common/js/jquery.ui.datepicker-ja.js"></script>
        <script src="/admin/common/js/cms.js"></script>
        <!-- redactor -->
        <link rel="stylesheet" href="/admin/common/css/redactor/redactor.min.css">
        <script src="/admin/common/js/redactor/redactor.min.js"></script>
        <!-- redactor plugins -->
        <script src="/admin/common/js/redactor/ja.js"></script>
        <script src="/admin/common/js/redactor/alignment.js"></script>
        <script src="/admin/common/js/redactor/counter.js"></script>
        <script src="/admin/common/js/redactor/fontcolor.js"></script>
        <script src="/admin/common/js/redactor/imagemanager.js"></script>
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
                $('#image').attr('name', 'image'+parseInt($this.val()));
            }
        </script>
    <?php $this->end(); ?>
