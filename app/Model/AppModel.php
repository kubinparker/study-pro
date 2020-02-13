<?php

/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');
App::uses('Folder', 'Utility');
App::uses('CakeText', 'Utility');
/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model
{

    //Upload Directory, file convert configure
    //
    // Upload dir is UPLOAD_BASE_URL and UPLOAD_DIR constant at bootstrap.php
    //
    public $uploadDirCreate = true;
    public $uploadDirMask = 0777;
    public $uploadFileMask = 0666;

    //ImageMagick configure
    // public $convertPath = '/usr/local/bin/convert';
    public $convertPath = '/usr/bin/convert';
    public $convertParams = '-thumbnail';

    /**
     * Model::save()時に使う trustを取得する
     * $blackList - テーブルカラムから指定のカラムを除く
     * 指定なし - Model:$attachesに指定したカラムを除いたカラムを使う
     *            fileの場合は fileカラム_nameも除く
     *
     * */
    public function trustList()
    {
        //Remove attache colums
        $schema = array_keys($this->schema());
        $trust = array();
        if ($this->blackList) {
            $trust = array_diff($schema, $this->blackList);
        } else {
            $black_list = array();
            if (!empty($this->attaches['images'])) {
                $black_list = $black_list + array_keys($this->attaches['images']);
            }
            if (!empty($this->attaches['files'])) {
                foreach ($this->attaches['files'] as $key => $_) {
                    $black_list[] = $key;
                    $black_list[] = $key . '_name';
                    $black_list[] = $key . '_size';
                }
            }
            $trust = array_diff($schema, $black_list);
        }
        return $trust;
    }

    /**
     * Events
     * */
    public function afterSave($created, $options = array())
    {

        //アップロード処理
        $this->_uploadAttaches();

        return parent::afterSave($created);
    }

    public function afterFind($results, $primary = false)
    {
        //ファイルパスを設定
        $results = $this->_attachesFind($results, $primary);
        return $results;
    }


    public function beforeDelete($cascade = true)
    {
        return true;
    }

    public function afterDelete()
    {
        return true;
    }

    /**
     * 静的列挙取得
     *
     * Model::enum = array('columns' => array(...) )
     * */
    public static function enum($array, $key)
    {
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
    protected function checkUploadDirectory()
    {
        App::uses('Folder', 'Utility');
        $Folder = new Folder();

        if ($this->uploadDirCreate) {
            $dir = UPLOAD_DIR . $this->alias . DS . 'images';
            if (!is_dir($dir) && !empty($this->attaches['images'])) {
                if (!$Folder->create(UPLOAD_DIR . $this->alias . DS . 'images', $this->uploadDirMask)) { }
            }

            $dir = UPLOAD_DIR . $this->alias . DS . 'files';
            if (!is_dir($dir) && !empty($this->attaches['files'])) {
                if (!$Folder->create($dir, $this->uploadDirMask)) { }
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
    protected function _attachesFind($results, $primary = false)
    {
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
                if (isset($_[$this->alias][$columns])) {
                    $_attaches['0'] = '';
                    $_file = '/' . UPLOAD_BASE_URL . '/' . $this->alias . '/images/' . $_[$this->alias][$columns];
                    if (is_file(WWW_ROOT . $_file)) {
                        $_attaches['0'] = $_file;
                    }
                    if (!empty($_att['thumbnails'])) {
                        foreach ($_att['thumbnails'] as $_name => $_val) {
                            $key_name = (!is_int($_name)) ? $_name : $_val['prefix'];
                            $_attaches[$key_name] = '';
                            $_file = '/' . UPLOAD_BASE_URL . '/' . $this->alias . '/images/' . $_val['prefix'] . $_[$this->alias][$columns];
                            if (!empty($_[$this->alias][$columns]) && is_file(WWW_ROOT . $_file)) {
                                $_attaches[$key_name] = $_file;
                            }
                        }
                    }
                    $results[$key][$this->alias]['attaches'][$columns] = $_attaches;
                } else if (isset($_[$this->alias][0][$columns])) {
                    foreach ($_[$this->alias] as $k => $f) {
                        $_attaches['0'] = '';
                        $_file = '/' . UPLOAD_BASE_URL . '/' . $this->name . '/images/' . $f[$columns];
                        if (is_file(WWW_ROOT . $_file)) {
                            $_attaches['0'] = $_file;
                        }
                        if (!empty($_att['thumbnails'])) {
                            foreach ($_att['thumbnails'] as $_name => $_val) {
                                $key_name = (!is_int($_name)) ? $_name : $_val['prefix'];
                                $_attaches[$key_name] = '';
                                $_file = '/' . UPLOAD_BASE_URL . '/' . $this->name . '/images/' . $_val['prefix'] . $f[$columns];
                                if (!empty($f[$columns]) && is_file(WWW_ROOT . $_file)) {
                                    $_attaches[$key_name] = $_file;
                                }
                            }
                        }
                        $results[$key][$this->alias][$k]['attaches'][$columns] = $_attaches;
                    }
                }
            }

            //file
            foreach ($_att_files as $columns => $_att) {
                $def = array('0', 'src', 'extention', 'name', 'download');
                $def = array_fill_keys($def, null);

                if (isset($_[$this->alias][$columns])) {
                    $_attaches = $def;
                    $_file = '/' . UPLOAD_BASE_URL . '/' . $this->alias . '/files/' . $_[$this->alias][$columns];
                    if (is_file(WWW_ROOT . $_file)) {
                        $_attaches['0'] = $_file;
                        $_attaches['src'] = $_file;
                        $_attaches['extention'] = $this->getExtension($_[$this->alias][$columns . '_name']);
                        $_attaches['name'] = $_[$this->alias][$columns . '_name'];
                        $_attaches['size'] = $_[$this->alias][$columns . '_size'];
                        $_attaches['download'] = '/file/' . $_[$this->alias][$this->primaryKey] . '/' . $columns . '/';
                    }
                    $results[$key][$this->alias]['attaches'][$columns] = $_attaches;
                } else if (isset($_[$this->alias][0][$columns])) {
                    foreach ($_[$this->alias] as $k => $f) {
                        $_attaches = $def;
                        $_file = '/' . UPLOAD_BASE_URL . '/' . $this->name . '/files/' . $f[$columns];
                        if (is_file(WWW_ROOT . $_file)) {
                            $_attaches['0'] = $_file;
                            $_attaches['src'] = $_file;
                            $_attaches['extention'] = $this->getExtension($f[$this->alias][$columns . '_name']);
                            $_attaches['name'] = $f[$columns . '_name'];
                            $_attaches['size'] = $f[$columns . '_size'];
                            $_attaches['download'] = '/file/' . $f[$this->primaryKey] . '/' . $columns . '/';
                        }
                        $results[$key][$this->alias][$k]['attaches'][$columns] = $_attaches;
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
    public function _uploadAttaches()
    {
        $this->checkUploadDirectory();

        $uuid = CakeText::uuid();

        if (!empty($this->data[$this->alias])) {
            $_data = $this->data[$this->alias];
            $id = $this->id;
            $old_data = $this->read(null);

            $_att_images = array();
            $_att_files = array();
            if (!empty($this->attaches['images'])) {
                $_att_images = $this->attaches['images'];
            }
            if (!empty($this->attaches['files'])) {
                $_att_files = $this->attaches['files'];
            }
            //upload images
            foreach ($_att_images as $columns => $val) {
                $image_name = array();
                if (!empty($_data[$columns])) {
                    $image_name = $_data[$columns];
                }
                if (!empty($image_name['tmp_name']) && $image_name['error'] === UPLOAD_ERR_OK) {
                    $basedir = WWW_ROOT . UPLOAD_BASE_URL . DS . $this->alias . DS . 'images' . DS;
                    $imageConf = $_att_images[$columns];
                    $ext = $this->getExtension($image_name['name']);
                    $filepattern = $imageConf['file_name'];
                    $file = $image_name;
                    if ($info = getimagesize($file['tmp_name'])) {
                        //画像 処理方法
                        $convert_method = (!empty($imageConf['method'])) ? $imageConf['method'] : null;

                        if (in_array($ext, $imageConf['extensions'])) {
                            $newname = sprintf($filepattern, $id, $uuid) . '.' . $ext;
                            $this->convert_img(
                                $imageConf['width'] . 'x' . $imageConf['height'],
                                $file['tmp_name'],
                                $basedir . $newname,
                                $convert_method
                            );

                            //サムネイル
                            if (!empty($imageConf['thumbnails'])) {
                                foreach ($imageConf['thumbnails'] as $suffix => $val) {
                                    //画像処理方法
                                    $convert_method = (!empty($val['method'])) ? $val['method'] : null;
                                    //ファイル名
                                    $prefix = (!empty($val['prefix'])) ? $val['prefix'] : $suffix;
                                    $_newname = $prefix . $newname;
                                    //変換
                                    $this->convert_img(
                                        $val['width'] . 'x' . $val['height'],
                                        $file['tmp_name'],
                                        $basedir . $_newname,
                                        $convert_method
                                    );
                                }
                            }
                            $this->saveField($columns, $newname);

                            // 旧ファイルの削除
                            if (!empty($old_data[$this->alias]['attaches'][$columns])) {
                                foreach ($old_data[$this->alias]['attaches'][$columns] as $image_path) {
                                    if ($image_path && is_file(WWW_ROOT . $image_path)) {
                                        @unlink(WWW_ROOT . $image_path);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //upload files
            foreach ($_att_files as $columns => $val) {
                $file_name = array();
                if (!empty($_data[$columns])) {
                    $file_name = $_data[$columns];
                }

                if (!empty($file_name['tmp_name']) && $file_name['error'] === UPLOAD_ERR_OK) {
                    $basedir = WWW_ROOT . UPLOAD_BASE_URL . DS . $this->alias . DS . 'files' . DS;
                    $fileConf = $_att_files[$columns];
                    $ext = $this->getExtension($file_name['name']);
                    $filepattern = $fileConf['file_name'];
                    $file = $file_name;
                    if (in_array($ext, $fileConf['extensions'])) {
                        $newname = sprintf($filepattern, $id, $uuid) . '.' . $ext;
                        move_uploaded_file($file['tmp_name'], $basedir . $newname);
                        chmod($basedir . $newname, $this->uploadFileMask);

                        $this->saveField($columns, $newname);
                        $this->saveField($columns . '_name', $file_name['name']);
                        $this->saveField($columns . '_size', $file_name['size']);

                        // 旧ファイルの削除
                        if (!empty($old_data[$this->alias]['attaches'][$columns])) {
                            foreach ($old_data[$this->alias]['attaches'][$columns] as $file_path) {
                                if ($file_path && is_file(WWW_ROOT . $file_path)) {
                                    @unlink(WWW_ROOT . $file_path);
                                }
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
    public function getExtension($filename)
    {
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
    public function convert_img($size, $source, $dist, $method = 'fit')
    {
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
                if ($method === 'cover' || $method === 'crop') {
                    //中央切り取り
                    $crop = $size;
                    if (($ow / $oh) <= ($sz[0] / $sz[1])) {
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
        $a = system(escapeshellcmd($cmdline . ' ' . '-auto-orient' . ' ' . $option . ' ' . $source . ' ' . $dist));
        @chmod($dist, $this->uploadFileMask);
        return $a;
    }
    /**
     * 画像の必須
     * validationのruleに指定する
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function notBlankImage($data)
    {

        $fname = '';
        foreach ($data as $key => $value) {
            $fname = $key;
            break;
        }
        if (array_key_exists('error', $this->data[$this->alias][$fname]) && $this->data[$this->alias][$fname]['error'] == 0) {
            return true;
        } else {
            $old = $this->find('first', array(
                'conditions' => array($this->alias . '.' . $this->primaryKey => $this->data[$this->alias][$this->primaryKey]),
                'recursive' => -1,
                'fields' => array($fname)
            ));
            if (!empty($old) && $old[$this->alias][$fname] != "") {
                return true;
            }
        }

        return false;
    }
    // 全て入力されていなければfalse
    public function notBlankAnd($data, $target)
    {
        $fname = '';
        foreach ($data as $key => $value) {
            $fname = $key;
            break;
        }

        $res = false;
        if (array_key_exists($target, $this->data[$this->alias])) {
            if ($this->data[$this->alias][$target] != "" && $this->data[$this->alias][$fname] != "") {
                $res = true;
            }
        }
        return $res;
    }
    // どちらかが入力されていればtrue
    // ※使うモデル側にallowEmptyを指定してはいけない
    public function notBlankOr($data, $target)
    {
        $fname = '';
        foreach ($data as $key => $value) {
            $fname = $key;
            break;
        }

        $res = false;
        if ($this->data[$this->alias][$fname] != "") {
            $res = true;
        }
        if (array_key_exists($target, $this->data[$this->alias])) {
            if ($this->data[$this->alias][$target] != "") {
                $res = true;
            }
        }

        return $res;
    }

    public function checkTel($data)
    {
        $fname = '';
        foreach ($data as $key => $value) {
            $fname = $key;
            break;
        }
        // ハイフンあってもなくていいバージョン
        $pattern = '/^(0\d{1,4}[\s-]?\d{1,4}[\s-]?\d{4})$/';
        // ハイフンありじゃないとだめバージョン
        // $pattern = '/^(0\d{1,4}-\d{1,4}-\d{4})$/';

        if (preg_match($pattern, $this->data[$this->alias][$fname])) {
            return true;
        }

        return false;
    }
}
