<?php
    namespace App\Model\Table;

    use Cake\ORM\Table;
    use Cake\Filesystem\Folder;
    use Cake\Utility\Text;
    use Cake\Event\Event;
    use Cake\Http\Exception\NotFoundException;
    use Cake\Datasource\EntityInterface;

    use Cake\I18n\Time;

    use ArrayObject;

    class AppTable extends Table
    {
        public $useTable = null;

        //Upload Directory, file convert configure
        //
        // Upload dir is UPLOAD_BASE_URL and UPLOAD_DIR constant at paths.php
        //
        public $uploadDirCreate = true;
        public $uploadDirMask = 0777;
        public $uploadFileMask = 0666;

        //ImageMagick configure
        public $convertPath = '/usr/bin/convert';
        public $convertParams = '-thumbnail';

        // list schema ** After test with file Behavior
        public $schema; 

        // public $basedir;

        public $data;

        /**
         * initialize
         *
         * @return void
         */
        public function initialize(array $config): void
        {
            $this->addBehavior('Position');
        }
        /**
         * Model::save()時に使う trustを取得する
         * $blackList - テーブルカラムから指定のカラムを除く
         * 指定なし - Model:$attachesに指定したカラムを除いたカラムを使う
         *            fileの場合は fileカラム_nameも除く
         *
         * */
        public function trustList() {
            $trust = [];
            // schema is list column name of current table
            $schema = $this->getSchema()->columns();
            //Remove attache colums
            if (isset($this->blackList) && !empty($this->blackList)) {
                $trust = array_diff($schema, $this->blackList);
            } else {
                $black_list = [];
                if(isset($this->attaches)){
                    $attaches = $this->attaches;
                    if (!empty($attaches['images'])) {
                        $black_list = $black_list + array_keys($attaches['images']);
                    }
                    if (!empty($attaches['files'])) {
                        foreach ($attaches['files'] as $key => $_) {
                            $black_list[] = $key;
                            $black_list[] = $key . '_name';
                            $black_list[] = $key . '_size';
                        }
                    }
                    $trust = array_diff($schema, $black_list);
                }
                
            }
            return array_values($trust);
        }

        public function oppositeTrustList(){
            if (isset($this->blackList) && !empty($this->blackList)) {
                return $this->blackList;
            } else {
                $black_list = [];
                if(isset($this->attaches)){
                    $attaches = $this->attaches;
                    if (!empty($attaches['images'])) {
                        $black_list = $black_list + array_keys($attaches['images']);
                    }
                    if (!empty($attaches['files'])) {
                        foreach ($attaches['files'] as $key => $_) {
                            $black_list[] = $key;
                            $black_list[] = $key . '_name';
                            $black_list[] = $key . '_size';
                        }
                    }
                }
                return $black_list;
            }
        }

        /**
         * Events
         * */
        public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options) 
        {
            //アップロード処理
            $this->_uploadAttaches($entity); 
        }

        public function afterFind($results, $primary = false) {
            //ファイルパスを設定
            $results = $this->_attachesFind($results, $primary);
            return $results;
        }


        public function beforeDelete($cascade = true) {
            return true;
        }

        public function afterDelete() {
            return true;
        }

        /**
         * 静的列挙取得
         *
         * Model::enum = array('columns' => array(...) )
         * */
        public static function enum($array, $key){
            $model = get_called_class();
            $index = null;
            $value = '';
            if (isset($array[$model][$key])) {
                $index = $array[$model][$key];
            }

            if ($index !== null) {
                $enums = $model::$enum;
                if (isset($enums[$key][$index])) {
                    $value = $enums[$key][$index];
                }
            }
            return $value;
        }


        /**
         * upload以下のフォルダを作成/書き込み権限のチェック
         * afterFind()
         * */
        protected function checkUploadDirectory() {
            $Folder = new Folder();
            if ($this->uploadDirCreate) {
                $dir = UPLOAD_DIR . $this->getAlias() . DS . IMAGES_BASE_URL;
                if (!is_dir($dir) && !empty($this->attaches['images'])) {
                    if (!$Folder->create($dir, $this->uploadDirMask)) {

                    }
                }

                $dir = UPLOAD_DIR . $this->getAlias() . DS . FILES_BASE_URL;
                if (!is_dir($dir) && !empty($this->attaches['files'])) {
                    if (!$Folder->create($dir, $this->uploadDirMask)) {

                    }
                }
            }
        }

        /**
         * 記事取得時に画像/ファイルパスを設定
         * afterFind()
         *
         * [Model]['attaches'][columns_name]['0'] - original size
         * [Model]['attaches'][columns_name][thumnsnail_prefix]
         * */
        protected function _attachesFind($results, $primary = false) {
            $this->checkUploadDirectory();
            $_att_images = array();
            $_att_files = array();
            if (!empty($this->attaches['images'])) {
                $_att_images = $this->attaches['images'];
            }
            if (!empty($this->attaches['files'])) {
                $_att_files = $this->attaches['files'];
            }
            foreach ($results as $key => $_) {
                $columns = null;

                //image
                foreach ($_att_images as $columns => $_att) {
                    $_attaches = array();
                    if (isset($_[$this->getAlias()][$columns])) {
                        $_attaches['0'] = '';
                        $_file = UPLOAD_DIR . $this->getAlias() . '/images/' . $_[$this->getAlias()][$columns];
                        if (is_file($_file)) {
                            $_attaches['0'] = $_file;
                        }
                        if (!empty($_att['thumbnails'])) {
                            foreach ($_att['thumbnails'] as $_name => $_val) {
                                $key_name = (!is_int($_name))? $_name: $_val['prefix'];
                                $_attaches[$key_name] = '';
                                $_file = UPLOAD_DIR . $this->getAlias() . '/images/' . $_val['prefix'] . $_[$this->getAlias()][$columns];
                                if (!empty($_[$this->getAlias()][$columns]) && is_file($_file)) {
                                    $_attaches[$key_name] = $_file;
                                }
                            }
                        }
                        $results[$key][$this->getAlias()]['attaches'][$columns] = $_attaches;
                    } 
                    
                    else if (isset($_[$this->getAlias()][0][$columns])) {
                        foreach($_[$this->getAlias()] as $k => $f) {
                            $_attaches['0'] = '';
                            $_file = UPLOAD_DIR . $this->name . '/images/' . $f[$columns];
                            if (is_file($_file)) {
                                $_attaches['0'] = $_file;
                            }
                            if (!empty($_att['thumbnails'])) {
                                foreach ($_att['thumbnails'] as $_name => $_val) {
                                    $key_name = (!is_int($_name))? $_name: $_val['prefix'];
                                    $_attaches[$key_name] = '';
                                    $_file = UPLOAD_DIR . $this->name . '/images/' . $_val['prefix'] . $f[$columns];
                                    if (!empty($f[$columns]) && is_file($_file)) {
                                        $_attaches[$key_name] = $_file;
                                    }
                                }
                            }
                            $results[$key][$this->getAlias()][$k]['attaches'][$columns] = $_attaches;
                        }
                    }
                }

                //file
                foreach ($_att_files as $columns => $_att) {
                    $def = array('0', 'src', 'extention', 'name', 'download');
                    $def = array_fill_keys($def, null);

                    if (isset($_[$this->getAlias()][$columns])) {
                        $_attaches = $def;
                        $_file = UPLOAD_DIR . $this->getAlias() . '/files/' . $_[$this->getAlias()][$columns];
                        if (is_file($_file)) {
                            $_attaches['0'] = $_file;
                            $_attaches['src'] = $_file;
                            $_attaches['extention'] = $this->getExtension($_[$this->getAlias()][$columns . '_name']);
                            $_attaches['name'] = $_[$this->getAlias()][$columns . '_name'];
                            $_attaches['size'] = $_[$this->getAlias()][$columns . '_size'];
                            $_attaches['download'] = '/file/' . $_[$this->getAlias()][$this->primaryKey] . '/' . $columns . '/';
                        }
                        $results[$key][$this->getAlias()]['attaches'][$columns] = $_attaches;
                    } else if (isset($_[$this->getAlias()][0][$columns])) {
                        foreach($_[$this->getAlias()] as $k => $f) {
                            $_attaches = $def;
                            $_file = UPLOAD_DIR . $this->name . '/files/' . $f[$columns];
                            if (is_file($_file)) {
                                $_attaches['0'] = $_file;
                                $_attaches['src'] = $_file;
                                $_attaches['extention'] = $this->getExtension($f[$this->getAlias()][$columns . '_name']);
                                $_attaches['name'] = $f[$columns . '_name'];
                                $_attaches['size'] = $f[$columns . '_size'];
                                $_attaches['download'] = '/file/' . $f[$this->primaryKey] . '/' . $columns . '/';
                            }
                            $results[$key][$this->getAlias()][$k]['attaches'][$columns] = $_attaches;
                        }
                    }
                }
            }
            return $results;
        }

        /**
         * 画像、ファイルアップロード
         * afterSave()
         *
         * */
        public function _uploadAttaches($entity) 
        {
            $this->checkUploadDirectory();
            $uuid = Text::uuid();
            
            $id = $entity->id;
            
            if (!empty($this->data[$this->getAlias()])) {
                $_data = $this->data[$this->getAlias()];

                $old_data = $this->get($id)->toArray();
                $_att_images = [];
                $_att_files = [];

                if (!empty($this->attaches['images'])) {
                    $_att_images = $this->attaches['images'];
                }
                if (!empty($this->attaches['files'])) {
                    $_att_files = $this->attaches['files'];
                }
                
                //upload images
                foreach ($_att_images as $columns => $val) {
                    $image_name = [];
                    if(!$this->hasField($columns)){
                        continue;
                    }
                    if (!empty($_data[$columns])) {
                        $image_name = $_data[$columns];
                    }
                    if (!empty($image_name['tmp_name']) && $image_name['error'] === UPLOAD_ERR_OK) {
                        $basedir = UPLOAD_DIR . $this->getAlias() . DS . IMAGES_BASE_URL . DS;
                        $imageConf = $_att_images[$columns];
                        // get ext of image from client
                        $ext = $this->getExtension($image_name['name']);
                        // get file name config or $attaches in table
                        $filepattern = $imageConf['file_name'];
                        // file from client
                        $file = $image_name;
                        if ($info = getimagesize($file['tmp_name'])) {
                            //画像 処理方法
                            $convert_method = (!empty($imageConf['method'])) ? $imageConf['method'] : null;
    
                            if (in_array($ext, $imageConf['extensions'])) {
                                $newname = sprintf($filepattern, $id, $uuid) . '.' . $ext;
                                $this->generate_thumbnail($file['tmp_name'], $basedir . $newname, $imageConf['width'], $imageConf['height']);
    
                                //サムネイル
                                if (!empty($imageConf['thumbnails'])) {
                                    foreach ($imageConf['thumbnails'] as $suffix => $val) {
                                        //画像処理方法
                                        $convert_method = (!empty($val['method'])) ? $val['method'] : null;
                                        //ファイル名
                                        $prefix = (!empty($val['prefix'])) ? $val['prefix'] : $suffix;
                                        $_newname = $prefix . $newname;
                                        //変換
                                        $this->generate_thumbnail($file['tmp_name'], $basedir . $_newname, $val['width'], $val['height']);
                                    }
                                }

                                $query = $this->query();
                                $query->update()
                                    ->set([$columns => $newname])
                                    ->where(['id' => $id])
                                    ->execute();

                                // 旧ファイルの削除
                                if (!empty($old_data[$columns])) {
                                    $image_path = $old_data[$columns];
                                    if ($image_path && is_file($basedir . $image_path)) {
                                        @unlink($basedir . $image_path);
                                    }

                                    /// remove old thumbnails images
                                    if (!empty($imageConf['thumbnails'])) {
                                        foreach ($imageConf['thumbnails'] as $suffix => $val) {
                                            $prefix = (!empty($val['prefix'])) ? $val['prefix'] : $suffix;
                                            $_file = $basedir . $prefix . $image_path;
                                            if (is_file($_file)) {
                                                @unlink($_file);
                                            }
                                        }
                                    }
                                }
                                
                            }
                        }
                    }
                }
                //upload files
                foreach ($_att_files as $columns => $val) {
                    $file_name = [];
                    if(!$this->hasField($columns)){
                        continue;
                    }
                    if (!empty($_data[$columns])) {
                        $file_name = $_data[$columns];
                    }
                    
                    if (!empty($file_name['tmp_name']) && $file_name['error'] === UPLOAD_ERR_OK) {
                        $basedir = UPLOAD_DIR . $this->getAlias() . DS . FILES_BASE_URL . DS;
                        $fileConf = $_att_files[$columns];
                        $ext = $this->getExtension($file_name['name']);
                        $filepattern = $fileConf['file_name'];
                        $file = $file_name;
                        
                        if (in_array($ext, $fileConf['extensions'])) {
                            $newname = sprintf($filepattern, $id, $uuid) . '.' . $ext;

                            $query = $this->query();
                            $query->update()
                                ->set([$columns => $newname, $columns.'_name' => $file_name['name'], $columns.'_size' => $file_name['size']])
                                ->where(['id' => $id])
                                ->execute();
                            
                            move_uploaded_file($file['tmp_name'], $basedir . $newname);
                            chmod($basedir . $newname, $this->uploadFileMask);
                            
                            
                            // 旧ファイルの削除
                            if (!empty($old_data[$columns])) {
                                $file_path = $old_data[$columns];
                                if ($file_path && is_file($basedir . $file_path)) {
                                    @unlink($basedir . $file_path);
                                }
                            }
                        }

                    }
                }
            }
        }

        /**
         * 拡張子の取得
         * */
        public function getExtension($filename) {
            return strtolower(substr(strrchr($filename, '.'), 1));
        }

        /**
         * ファイルアップロード
         * @param $size [width]x[height]
         * @param $source アップロード元ファイル(フルパス)
         * @param $dist 変換後のファイルパス（フルパス）
         * @param $method 処理方法
         *        - fit     $size内に収まるように縮小
         *        - cover   $sizeの短い方に合わせて縮小
         *        - crop    cover 変換後、中心$sizeでトリミング
         * */
        public function convert_img($size, $source, $dist, $method = 'fit') {
            list($ow, $oh, $info) = getimagesize($source);
            $sz = explode('x', $size);
            $cmdline = $this->convertPath;
            //サイズ指定ありなら
            if (0 < $sz[0] && 0 < $sz[1]) {
                if ($ow <= $sz[0] && $oh <= $sz[1]) {
                    //枠より完全に小さければ、ただのコピー
                    $size = $ow . 'x' . $oh;
                    $option = $this->convertParams . ' ' . $size . '>';
                } else {
                    //枠をはみ出していれば、縮小
                    if($method === 'cover' || $method === 'crop'){
                        //中央切り取り
                        $crop = $size;
                        if(($ow / $oh) <= ($sz[0] / $sz[1])){
                            //横を基準
                            $size = $sz[0] . 'x';
                        } else {
                            //縦を基準
                            $size = 'x' . $sz[1];
                        }

                        //cover
                        $option = '-thumbnail ' . $size . '>';

                        //crop
                        if ($method === 'crop') {
                            $option .= ' -gravity center -crop ' . $crop . '+0+0';
                        }
                    } else {
                        //通常の縮小 拡大なし
                        $option = $this->convertParams . ' ' . $size . '>';
                    }
                }
            } else {
                //サイズ指定なしなら 単なるコピー
                $size = $ow . 'x' . $oh;
                $option = $this->convertParams . ' ' . $size . '>';
            }
            $a = system(escapeshellcmd($cmdline . ' ' . $option . ' ' . $source . ' ' . $dist));
            @chmod($dist, $this->uploadFileMask);
            return $a;
        }
        /**
         * 画像の必須
         * validationのruleに指定する
         * @param  [type] $data [description]
         * @return [type]       [description]
         */
        public function notBlankImage($data) {
            
            $fname = '';
            foreach ($data as $key => $value) {
                $fname = $key;
                break;
            }
            if (array_key_exists('error', $this->data[$this->getAlias()][$fname]) && $this->data[$this->getAlias()][$fname]['error'] == 0) {
                return true;
            } else {
                $old = $this->find('first', array('conditions' => array($this->getAlias().'.'.$this->primaryKey => $this->data[$this->getAlias()][$this->primaryKey]),
                                                'recursive' => -1,
                                                'fields' => array($fname)));
                if (!empty($old) && $old[$this->getAlias()][$fname] != "") {
                    return true;
                }
            }

            return false;
        }
        // 全て入力されていなければfalse
        public function notBlankAnd($data, $target) {
            $fname = '';
            foreach ($data as $key => $value) {
                $fname = $key;
                break;
            }

            $res = false;
            if (array_key_exists($target,$this->data[$this->getAlias()]) ) {
                if ($this->data[$this->getAlias()][$target] != "" && $this->data[$this->getAlias()][$fname] != "") {
                    $res = true;
                }
            }
            return $res;
        }
        // どちらかが入力されていればtrue
        // ※使うモデル側にallowEmptyを指定してはいけない
        public function notBlankOr($data, $target) {
            $fname = '';
            foreach ($data as $key => $value) {
                $fname = $key;
                break;
            }

            $res = false;
            if ($this->data[$this->getAlias()][$fname] != "") {
                $res = true;
            }
            if (array_key_exists($target,$this->data[$this->getAlias()]) ) {
                if ($this->data[$this->getAlias()][$target] != "") {
                    $res = true;
                }
            }

            return $res;
        }

        public function checkTel($data) {
            $fname = '';
            foreach ($data as $key => $value) {
                $fname = $key;
                break;
            }
            // ハイフンあってもなくていいバージョン
            $pattern = '/^(0\d{1,4}[\s-]?\d{1,4}[\s-]?\d{4})$/';
            // ハイフンありじゃないとだめバージョン
            // $pattern = '/^(0\d{1,4}-\d{1,4}-\d{4})$/';
            
            if(preg_match($pattern,$this->data[$this->getAlias()][$fname])) {
                return true;
            }

            return false;
        }

        public function exitsId($id){
            // Idが存在かどうかをチェック
            if((int)$id > 0){
                $item = $this->exists([$this->getPrimaryKey() => $id]);
                if (!$item) {
                    throw new NotFoundException(__('Record not found'));
                }
                else return true;
            }
        }

        public function generate_thumbnail($dir_file, $dir_thumb_file, $max_width, $max_height, $quality = 0.75)
        {
            // The original image must exist
            if(is_file($dir_file))
            {
                // Let's create the directory if needed
                $th_path = dirname($dir_thumb_file);
                if(!is_dir($th_path))
                    mkdir($th_path, 0777, true);
                // If the thumb does not aleady exists
                if(!is_file($dir_thumb_file))
                {
                    // Get Image size info
                    list($width_orig, $height_orig, $image_type) = @getimagesize($dir_file);
                    if(!$width_orig)
                        return 2;
                    switch($image_type)
                    {
                        case 1: $src_im = @imagecreatefromgif($dir_file);    break;
                        case 2: $src_im = @imagecreatefromjpeg($dir_file);   break;
                        case 3: $src_im = @imagecreatefrompng($dir_file);    break;
                    }
                    if(!$src_im)
                        return 3;


                    $aspect_ratio = (float) $height_orig / $width_orig;

                    $thumb_height = $max_height;
                    $thumb_width = round($thumb_height / $aspect_ratio);
                    if($thumb_width > $max_width)
                    {
                        $thumb_width    = $max_width;
                        $thumb_height   = round($thumb_width * $aspect_ratio);
                    }

                    $width = $thumb_width;
                    $height = $thumb_height;

                    $dst_img = @imagecreatetruecolor($width, $height);
                    if(!$dst_img)
                        return 4;
                    $success = @imagecopyresampled($dst_img,$src_im,0,0,0,0,$width,$height,$width_orig,$height_orig);
                    if(!$success)
                        return 4;
                    switch ($image_type) 
                    {
                        case 1: $success = @imagegif($dst_img,$dir_thumb_file); break;
                        case 2: $success = @imagejpeg($dst_img,$dir_thumb_file,intval($quality*100));  break;
                        case 3: $success = @imagepng($dst_img,$dir_thumb_file,intval($quality*9)); break;
                    }
                    if(!$success)
                        return 4;
                }
                return 0;
            }
            return 1;
        }

        public function converts_data_return ($data){
            if(empty($data[$this->getAlias()])) return $data;
            // set attaches for $data
            if(!empty($this->attaches)){
                $data_attaches = [];
                $attaches = $this->attaches;
                foreach($attaches as $key => $item){
                    if(!isset($data_attaches[$key])){
                        $data_attaches[$key] = [];
                    }
                    foreach($item as $k => $v){
                        if(!isset($data[$this->getAlias()]->{$k}) || empty($data[$this->getAlias()]->{$k}) || $data[$this->getAlias()]->{$k} == '') continue;
                        if(!isset($data_attaches[$key][$k])){
                            $data_attaches[$key][$k] = [];
                        }
                        if($key == ATTACHES_IMAGE){
                            $data_attaches[$key][$k] = [
                                DS.UPLOAD_BASE_URL.DS.$this->getAlias().DS.IMAGES_BASE_URL.DS.$data[$this->getAlias()]->{$k}
                            ];
                            if(isset($v['thumbnails']) && !empty($v['thumbnails'])){
                                foreach($v['thumbnails'] as $t => $thumb){
                                    $thumbnails = isset($thumb['prefix']) && $thumb['prefix'] != '' ? $thumb['prefix'] : $t . '_';
                                    $data_attaches[$key][$k][$t] = DS.UPLOAD_BASE_URL.DS.$this->getAlias().DS.IMAGES_BASE_URL.DS.$thumbnails.$data[$this->getAlias()]->{$k};
                                }
                            }
                        }

                        elseif($key == ATTACHES_FILE){
                            $ext = $this->getExtension($data[$this->getAlias()]->{$k.'_name'});
                            $data_attaches[$key][$k] = [
                                DS.UPLOAD_BASE_URL.DS.$this->getAlias().DS.FILES_BASE_URL.DS.$data[$this->getAlias()]->{$k},
                                'extention' => $ext
                            ];
                            

                        }
                    }
                }
                $data[$this->getAlias()]->attaches = $data_attaches;
            }
        
            if(isset($data[$this->getAlias()]->date) && $data[$this->getAlias()]->date != null){
                // convert datetime when date of data is FrozenDate object
                try{
                    $time = Time::parse($data[$this->getAlias()]->date);
                    $strTime = $time->i18nFormat('yyyy-MM-dd');
                    $data[$this->getAlias()]->date = $strTime;
                }
                catch (\Exception $e){
                    $data[$this->getAlias()]->date = null;
                }
            }
            
            return $data;
        }

    }
?>