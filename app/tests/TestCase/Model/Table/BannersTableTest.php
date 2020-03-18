<?php
    namespace App\Test\TestCase\Model\Table;

    use App\Model\Table\BannersTable;
    use Cake\ORM\TableRegistry;
    use Cake\TestSuite\TestCase;

    class BannersTableTest extends TestCase
    {
        // public $fixtures = ['app.Banners'];

        public function setUp(): void
        {
            parent::setUp();
            $this->Banners = TableRegistry::getTableLocator()->get('Banners');
        }

        public function testFindPublished(): void
        {
            $query = $this->Banners->find('published');
            $this->assertInstanceOf('Cake\ORM\Query', $query);
            $result = $query->enableHydration(false)->toArray();
            $expected = [
                ['id' => 3, 'position' => 9],
                ['id' => 4, 'position' => 8],
                ['id' => 5, 'position' => 10]
            ];

            $this->assertEquals($expected, $result);
        }
    }
?>