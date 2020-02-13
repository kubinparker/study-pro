<!DOCTYPE html>
<html lang="ja">

    <head>
        <?php echo $this->Html->charset(); ?>
        <meta name="robots" content="noindex">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>HOMEPAGE MANAGER</title>
        <link rel="stylesheet" href="/common/css/normalize.css">
        <link rel="stylesheet" href="/common/css/font.css">
        <link rel="stylesheet" href="/common/css/common.css">
        <link rel="stylesheet" href="/common/css/jquery.mCustomScrollbar.min.css">
        <link rel="stylesheet" href="/common/css/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css">
        <link rel="stylesheet" href="/common/css/colorbox.css">
        <script src="/common/js/jquery.js"></script>
        <script src="/common/js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="/common/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script src="/common/js/jquery.colorbox-min.js"></script>
        <script src="/common/js/colorbox.js"></script>
        <script src="/common/js/base.js"></script>

        <!--[if lt IE 9]>
            <script src='/common/js/html5shiv.js'></script>
        <![endif]-->

        <?php echo $this->fetch('beforeHeaderClose'); ?>
    </head>

    <body>
        <?php echo $this->fetch('afterBodyStart'); ?>

        <div id="container">
            <?php echo $this->element('header'); ?>
            <?php echo $this->element('side'); ?>

            <?php echo $this->fetch('beforeContentStart'); ?>

            <div id="content">
                <?php echo $this->fetch('content'); ?>
                <?php echo $this->element('footer'); ?>
            </div>

            <?php echo $this->fetch('afterContentClose'); ?>
        </div>

        <?php echo $this->fetch('beforeBodyClose'); ?>

        <script type="text/javascript">
            $(function() {
                $(".scrollbar").mCustomScrollbar({
                    scrollInertia: 0,
                    mouseWheelPixels: 50
                });
                var re = document.getElementById('clock');
                var item = function() {
                    var items = new Date();
                    h = ('0' + items.getHours()).slice(-2);
                    m = ('0' + items.getMinutes()).slice(-2);
                    s = ('0' + items.getSeconds()).slice(-2);
                    re.innerHTML = h + ':' + m;
                    setTimeout(item, 100);
                }
                setTimeout(item, 100);
            });
        </script>
    </body>
</html>