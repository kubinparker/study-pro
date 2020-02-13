<div id="error_message_waku">
    <?php if ($error_messages || $this->Session->check('Message.flash.message')): ?>
        <div class="error">
            <?= $error_messages; ?>
            <div><?= $this->Flash->render(); ?></div>
        </div>
    <?php endif; ?>
</div>