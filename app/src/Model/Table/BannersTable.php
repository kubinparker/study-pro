<?php
    namespace App\Model\Table;
    
    use Cake\Http\Exception\NotFoundException;
    use Cake\Validation\Validator;

    class BannersTable extends AppTable
    {
        
        public $blackList = [];

        public $attaches = [
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
                'image2' => [
                    'extensions' => ['jpg', 'jpeg', 'gif', 'png'],
                    'width' => 516,
                    'height' => 354,
                    'file_name' => 'img_%d_%s',
                    'thumbnails' => [
                        's' => [
                            'prefix' => 's_',
                            'width' => 325,
                            'height' => 90
                        ]
                    ],
                ],
                'image3' => [
                    'extensions' => ['jpg', 'jpeg', 'gif', 'png'],
                    'width' => 516,
                    'height' => 354,
                    'file_name' => 'img_%d_%s',
                    'thumbnails' => [
                        's' => [
                            'prefix' => 's_',
                            'width' => 240,
                            'height' => 70
                        ]
                    ],
                ],
                //image_1
            ],
            'files' => [
                'file1' => [
                    'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
                    'file_name' => 'file_%d_%s'
                ],
                'file2' => [
                    'extensions' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
                    'file_name' => 'file_%d_%s'
                ],
            ],
        ];

        public function initialize(array $config): void
        {
            parent::initialize($config);
        }

        // validation
        public function validationData($validator)
        {
            $validator
                ->notEmptyString('title', __('You need to provide a title'))
                ->notEmptyString('link', __('A body is required'))
                ->add('link', __('valid-url'), ['rule' => 'url']);
            return $validator;
        }

    }
    
?>