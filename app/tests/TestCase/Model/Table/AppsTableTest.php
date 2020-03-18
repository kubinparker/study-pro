<?php 
    namespace App\Test\TestCase\Model\Table;

    use Cake\TestSuite\TestCase;
    use Cake\ORM\TableRegistry;
    use Cake\TestSuite\StringCompareTrait;

    class AppsTableTest extends TestCase
    { 
        use StringCompareTrait;
        public function setUp(): void
        {
            parent::setUp();
            $this->App = TableRegistry::getTableLocator()->get('App');
            $this->App->schema = ['id', 'name', 'createAt', 'createBy', 'image1', 'image2', 'isDelete'];
        }

        /**
         * test function _uploadAttaches
         * when 
         * - has attaches['files']
         */
        public function test_uploadAttaches_files() :void
        {
            $this->App->modelName = 'Banners';
            $this->App->id = 3;
            $dir_img = WWW_ROOT . UPLOAD_BASE_URL . DS . $this->App->modelName . DS . 'files' . DS;
            $this->App->attaches = [
                'images' => [],
                'files' => [
                    'file' => [
                        'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
                        'file_name' => 'auction_file1_%d_%s'
                    ],
                ]
            ];
            $this->App->data = [
                'Banners' => [
                    'file' => [
                        'tmp_name' => __FILE__,
                        'name' => 'MyFile.pdf',
                        'type' => 'pdf',
                        'error' => 0,
                        'size' => 123
                    ]
                ]
            ];
            $this->App->_uploadAttaches();
            $this->assertTrue(is_dir($dir_img));
        }

        /**
         * test function _uploadAttaches
         * when 
         * - has attaches['images']
         */
        public function test_uploadAttaches_images() :void
        {
            $this->App->modelName = 'Banners';
            $this->App->id = 3;
            $dir_img = WWW_ROOT . UPLOAD_BASE_URL . DS . $this->App->modelName . DS . 'images' . DS;
            $this->App->attaches = [
                'images' => [
                    'image1' => [
                        'extensions' => ['jpg', 'jpeg', 'gif', 'png'],
                        'width' => 516,
                        'height' => 354,
                        'file_name' => 'img_%d_%s',
                        'thumbnails' => [
                            's' => [
                                'prefix' => 's_',
                                'width' => 493,
                                'height' => 150
                            ]
                        ],
                    ],
                ],
                'files' => []
            ];
            $this->App->data = [
                'Banners' => [
                    'image1' => [
                        'tmp_name' => WWW_ROOT . DS . 'img' . DS . 'cake.power.gif',
                        'name' => 'MyFile.jpg',
                        'type' => 'image/jpeg',
                        'error' => 0,
                        'size' => 123
                    ]
                ]
            ];
            $this->App->_uploadAttaches();
            $this->assertTrue(is_dir($dir_img));
        }

        /**
         * test function TrustList
         * when 
         * - blackList is not null
         * - attaches has images
         * - attaches has files
         */
        public function testTrustList_HasAll() :void
        {
            $this->App->blackList = ['name', 'isDelete', 'createAt', 'isDelete'];
            $this->App->attaches = [
                'images' => [
                    'image1' => [],
                    'image2' => []
                ],
                'files' => [
                    'file' => []
                ]
            ];
            $trustList = array_values($this->App->trustList());
            $expected = ['id', 'createBy', 'image1', 'image2'];
            $this->assertEquals($expected, $trustList);
        }

        /**
         * test function TrustList
         * when 
         * - blackList is not null
         * - attaches hasnt images
         * - attaches hasnt files
         */
        public function testTrustList_BlackList_Null_Null() :void
        {
            $this->App->blackList = ['name', 'isDelete', 'createAt'];
            $trustList = array_values($this->App->trustList());
            $expected = ['id', 'createBy', 'image1', 'image2'];
            $this->assertEquals($expected, $trustList);
        }

        /**
         * test function TrustList
         * when 
         * - blackList is null
         * - attaches hasnt images
         * - attaches hasnt files
         */
        public function testTrustList_NullAll() :void
        {
            $trustList = $this->App->trustList();

            $this->assertEquals([], $trustList);
        }

        /**
         * test function TrustList
         * when 
         * - blackList is null
         * - attaches has images
         * - attaches has files
         */
        public function testTrustList_Null_Image_Files() :void
        {
            $this->App->attaches = [
                'images' => [
                    'image1' => [],
                    'image2' => []
                ],
                'files' => [
                    'file' => []
                ]
            ];
            $trustList = array_values($this->App->trustList());
            $expected =  ['id', 'name', 'createAt', 'createBy', 'isDelete'];
            $this->assertEquals($expected, $trustList);
        }

        /**
         * test function TrustList
         * when 
         * - blackList is null
         * - attaches has images
         * - attaches hasnt files
         */
        public function testTrustList_Null_Image_Null() :void
        {
            $this->App->attaches = [
                'images' => [
                    'image1' => [],
                    'image2' => []
                ]
            ];
            $trustList = array_values($this->App->trustList());
            
            $expected = ['id', 'name', 'createAt', 'createBy', 'isDelete'];

            $this->assertEquals($expected, $trustList);
        }
    }
    
?>