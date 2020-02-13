<div id="side">
    <h1><a href="/admin/">HOMEPAGE<br>MANAGER</a><br /><span class="version">ver1.1</span></h1>
    <span style="border-bottom: 1px solid #fff; line-height: 50px; display: block; background: #4795df; border-top: 1px solid #fff; font-weigth: bold; text-align: center;">メインメニュー</span>
    <nav>
        <ul class="menu scrollbar">
           
        </ul>
    </nav>
    <?php if ((int) $this->Session->read('role') === 0) : ?>
        <span style="border-bottom: 1px solid #fff; line-height: 50px; display: block; background: #4795df; border-top: 1px solid #fff; font-weigth: bold; text-align: center;">システム管理メニュー</span>
        <nav>
            <ul class="menu scrollbar">
                <li>
                    <a href="/admin/user/">メンバー管理</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>