
// 一覧ページのアンカーリンク調整
function setPagelink(_headerHight){
  var headerHight = _headerHight;

  /* outpagelink */
  var hash = location.hash;
  if (hash !== '') {
    var idName =  hash.substr(1); // １文字目（ # ）は切り取る
    var position = $('#' + idName).offset().top-headerHight;
    $("html, body").animate({scrollTop:position}, 5, "swing");
  }
}

$(function () {
  setPagelink($('header').height());//ヘッダーの高さを入れる
})
