<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include PUBLIC_ROOT.'/common/include/ga.php'; ?>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="format-detection" content="telephone=no">
  <meta name="viewport" content="<?php include PUBLIC_ROOT.'/common/include/viewport.php'; ?>">
  <meta name="Description" content="<?php include PUBLIC_ROOT.'/common/include/description.php'; ?>">
  <meta name="Keywords" content="<?php include PUBLIC_ROOT.'/common/include/keywords.php'; ?>">
  <title>代表メッセージ | <?php include PUBLIC_ROOT.'/common/include/title.php'; ?></title>

  <meta property="og:title" content="<?php include PUBLIC_ROOT.'/common/include/title.php'; ?>">
  <meta property="og:url" content="">
  <meta property="og:image" content="">
  <meta property="og:type" content="article">

  <link rel="shortcut icon" href="/favicon.ico?v=83c7f75756f3eb5cba73287ee0814423" type="image/x-icon">
  <link rel="icon" href="/favicon.ico?v=83c7f75756f3eb5cba73287ee0814423" type="image/x-icon">

  <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP:300,400,500,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/common/css/normalize.css?v=a35eedec4247e607ae7830e2b84708d8">
  <link rel="stylesheet" href="/common/css/common.css?v=3c8893cedd7b416494acf2a162621176">
  <link rel="stylesheet" href="/contact/css/style.css?v=9bbf28dd889059658c79ec5bc09d7478">
</head>

<body>
  <div class="container" id="container">
    <!-- <header id="header" class="header">
      <?php include PUBLIC_ROOT.'/common/include/header.php'; ?>
    </header> -->
    <!-- end header -->

    <main>
      <section class="entry">
        <!-- <div class="breadcrumb">
          <ul class="breadcrumb__list row">
            <li><a href="/">トップ</a></li>
            <li>お問い合わせ</li>
          </ul>
        </div> -->
        <!-- end breadcrumb-->

        <div class="ttl">
          <h2 class="ttl__tt row"><span>contact</span>お問い合わせ</h2>
        </div><!-- end ttl-->

        <div class="ctnMain row">
          <?= $this->Form->create($data[$ModelName], [
            'type' => 'post',
            'name' => 'fm',
            'novalidate' => true,
            'inputDefaults' => [
              'required' => false, 
              'label' => false
            ],
          ])?>
            <div class="form_group">
              <div class="box_lb">
                <label for="name">名前<span class="lb_danger">必須</span></label>
              </div>
              <div class="box_ipt">
                <div class="form_control ipt_w02">
                  <?=$this->Form->input('sei', ['type' => 'text','placeholder'=>'姓', 'id' => 'name', 'value' => '黒霧']);?>
                </div>
                <div class="form_control ipt_w02">
                  <?=$this->Form->input('mei', ['type' => 'text','placeholder'=>'名', 'value' => '島']);?>
                </div>
              </div>
            </div>
            <div class="form_group">
              <div class="box_lb">
                <label for="name_read">フリガナ<span class="lb_danger">必須</span></label>
              </div>
              <div class="box_ipt">
                <div class="form_control ipt_w02">
                  <?=$this->Form->input('kana_sei', ['type' => 'text','placeholder'=>'セイ', 'id' => 'name_read', 'value' => 'クロキリ']);?>
                </div>
                <div class="form_control ipt_w02">
                  <?=$this->Form->input('kana_mei', ['type' => 'text','placeholder'=>'メイ', 'value' => 'シマ']);?>
                </div>
              </div>
            </div>
            <div class="form_group">
              <div class="box_lb">
                <label for="phone">電話番号</label>
              </div>
              <div class="box_ipt">
                <div class="form_control">
                  <?=$this->Form->input('tel', ['id' => 'phone', 'value' => '07012345678']);?>
                </div>
              </div>
            </div>
            <div class="form_group">
              <div class="box_lb">
                <label for="mail">E-MAIL<span class="lb_danger">必須</span></label>
              </div>
              <div class="box_ipt">
                <div class="form_control">
                  <?=$this->Form->input('email', ['id' => 'mail', 'value' => 'huy.nguyenthanh@caters.co.jp']);?>
                </div>
              </div>
            </div>
            <div class="form_group align_start">
              <div class="box_lb">
                <label for="company">御社名</label>
              </div>
              <div class="box_ipt">
                <div class="form_control">
                  <?=$this->Form->input('company',['id' => 'company', 'value' => '黒霧']);?>
                  <p class="notes_ipt">※法人の方はご記入ください。</p>
                </div>
              </div>
            </div>
            <div class="form_group">
              <div class="box_lb">
                <label for="inquiry">お問い合わせ内容<span class="lb_danger">必須</span></label>
              </div>
              <div class="box_ipt">
                <div class="form_control">
                <?=$this->Form->input('content', ['type' => 'textarea', 'id' => 'inquiry', 'class' => 'txt_area', 'value' => '黒霧']);?>
                </div>
              </div>
            </div>
            <div class="form_btn btn">
              <button class="btnForm_control" type="submit" value="確認">確認</button>
            </div>
          <?= $this->Form->end();?>
        </div><!-- end ctnMain-->
      </section><!-- end entry-->
    </main>

    <!-- <footer id="footer" class="footer">
      <?php include PUBLIC_ROOT.'/common/include/footer.php'; ?>
    </footer> -->
    <!-- end footer -->
  </div>

  <script src="/common/js/libs.js?v=2b85e49658f683448697b8cb68738bf2"></script>
  <script src="/common/js/base.js?v=32c2ddad09c736db25beb1ced58217f0"></script>
</body>
</html>