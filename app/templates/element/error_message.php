<div id="error_message_waku">
<?php $err = $this->Flash->render('errors'); ?>
<?php if($err || $error_messages): ?>
        <div class="error">
            <?= $error_messages?>
            <div><?= $err; ?></div>
        </div>
<?php endif; ?>
</div>