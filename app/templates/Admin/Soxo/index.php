<?php
/**
 * 15 ngày / 4tr500k 
 * 1 ngày / 300k
 * 1 ngày / 30 số
 * 1 số / 10k
 * hết 15 ngày 
 * -> vốn k tăng -> dừng
 * -> vốn tăng -> + dồn -> chơi đến hết vốn
 * 
 * trích từ vốn ra 5 ngày - 200k
 * 
 * chức năng cần
 * mỗi ngày lấy ra 30 số ngẫu nhiên 0-100
 * chọc vào trang lịch vạn niên lấy ra giờ đẹp trong ngày 
 * check giờ đăng nhập ngày hôm đó so với số giờ lấy ra trên trang lịch vạn niên
 * lấy số giờ gần nhất sắp tới để render 
 * role = 1 
 * thì render 1 lần duy nhất trong ngày. lần đăng nhập đầu tiên sử dụng logic trên
 * 
 * 
 * role = 0 
 * đăng nhập xem được thành viên role 1 đăng nhập của ngày đó chưa
 * đăng nhập rồi thì xem được số render của ngày hôm đó
 * nếu thành viên đăng nhập rồi mà đến 7h tối không có thông báo lại trên hệ thống sẽ phải nộp 
 * trường hợp ngày đó cả 30 số k trúng :  50k vào vốn (quên hoặc k đánh)
 * trường hơp ngày đó trúng : 100k (lý do: đánh ăn riêng k khai báo lên hệ thống thì tối đa cũng phải bỏ tiền túi là 300k => lãi 400k . vì vậy phạt nộp lên hệ thống 100k (đuơng nhiên))
 * đánh rồi thì xác nhận trên hệ thống
 * hệ thống sẽ báo về mail role 0 và role 100 là thành viên đã đánh
 * role 0 cũng phải xác nhận là đã xem kết quả ngày đó của thành viên đó
 * 
 * 
 * tỉ lệ trúng 7 ngày trong 15 ngày thì được + thêm 100k
 * 
 * khi thành viên role 1 đăng nhập thì hệ thống sẽ lưu số . ngày giờ đăng nhập và gửi vào mail của role 0 role 1 và role 100 (role 100: ad ở vn) 
 * 
 * 5h45p chiều hàng ngày check những thành viên đã đăng nhập, chưa đánh sẽ gửi mail nhắc nhở đi đánh 
 *  
 * 
*/
 ?>

<div class="title_area">
    <h1>バナー: <?= date('d/m/Y')?></h1>
</div>
<div class="content_inr">
    <div class="box">
        <h3><?= '新規登録'; ?></h3> <!-- day so can danh ngay hom nay -->
        <div class="table_area form_area">
            <?php if($data[$ModelName]):?>
                <?= $this->Form->create($data[$ModelName], ['type' => 'file', 'label' => false]); ?>
                <?php 
                    $result = json_decode($data[$ModelName]->result);
                    if(!empty($result)):?>
                        <div class="boxresult">
                            <?= $this->Html->nestedList(array_slice($result, 0, 10), ['id' => 'result_1'])?>
                            <?= $this->Html->nestedList(array_slice($result, 10, 10), ['id' => 'result_2'])?>
                            <?= $this->Html->nestedList(array_slice($result, 20, 10), ['id' => 'result_3'])?>
                        </div>
                        <div class="btn_area">
                            <?= $this->Form->button('登録する', ['type' => 'submit', 'class' => 'btn_confirm submitButton']);?>
                        </div>
                    <?php endif;?>
            <?php endif;?>
        </div>
        <?= $this->Form->end(); ?>
    </div>
</div>
<?php $this->start('beforeBodyClose'); ?>
    <link rel="stylesheet" href="/admin/soxo/css/style.css">
<?php $this->end(); ?>