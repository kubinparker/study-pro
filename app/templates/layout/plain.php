<!DOCTYPE html>
<html lang="ja">

<head>
    <?php echo $this->Html->charset(); ?>
    <meta name="robots" content="noindex">
    <meta name="viewport" content="<?php include PUBLIC_ROOT . '/admin/common/include/viewport.inc' ?>">
    <link rel="stylesheet" href="./common/css/normalize.css">
    <link rel="stylesheet" href="./common/css/font.css">
    <link rel="stylesheet" href="./common/css/common.css">
    <link rel="stylesheet" href="/common/css/style.css">
    <title>HOMEPAGE MANAGER | LOGIN</title>
    <!--[if lt IE 9]>
        <script src='./common/js/html5shiv.js'></script>
    <![endif]-->
</head>

<body>
    <div id="container">
        <header>
            <div class="status">
                <h1>LOGIN</h1>
            </div>
        </header>

        <div id="content">
            <div class="title_area">
                <h1>LOGIN</h1>
            </div>
            <?php echo $this->fetch('content'); ?>
            <?php include PUBLIC_ROOT . '/admin/common/include/footer.inc' ?>
        </div>
    </div>

    <script src="./common/js/jquery.js"></script>
    <script src="./common/js/base.js"></script>
    <script>
        $(function() {
            $('a.btn_confirm').on('click', function() {
                $('#AdminIndexForm').submit();
            });
            $('a.btn_back').on('click', function() {
                document.getElementById("AdminIndexForm").reset();
            });
        })
    </script>

</body>

</html>