<?php
class User extends AppModel
{
    public $attaches = [
        'avatar' => [
            'image' => [
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
            ]
            //image_1
        ],
        'files' => [],
    ];

    public function hash($password)
    {
        return hash('sha256', $password);
    }
}
?>