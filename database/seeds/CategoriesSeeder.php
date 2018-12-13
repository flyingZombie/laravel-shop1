<?php

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Mobile Accessories',
                'children' => [
                    ['name' => 'Phone Shell'],
                    ['name' => 'Screen Protector'],
                    ['name' => 'Memory Card'],
                    ['name' => 'Data Cable'],
                    ['name' => 'Charger'],
                    [
                        'name' => 'Headset',
                        'children' => [
                            ['name' => 'Wired Headset'],
                            ['name' => 'Bluetooth Headset'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'PC Accessories',
                'children' => [
                    ['name' => 'Monitor'],
                    ['name' => 'Graphics Card'],
                    ['name' => 'RAM'],
                    ['name' => 'CPU'],
                    ['name' => 'Mother Board'],
                    ['name' => 'Hard Disk'],
                ],
            ],
            [
                'name' => 'PC',
                'children' => [
                    ['name' => 'Laptop'],
                    ['name' => 'Desktop'],
                    ['name' => 'Tablet'],
                    ['name' => 'all-in-one'],
                    ['name' => 'Server'],
                    ['name' => 'Workstation'],
                ],
            ],
            [
                'name' => 'Mobile',
                'children' => [
                    ['name' => 'Smart Phone'],
                    ['name' => 'Non-smartphone'],
                    ['name' => 'Interphone'],
                ],
            ],
        ];

        foreach ($categories as $category) {
            $this->createCategory($category);
        }
    }

    protected function createCategory($data, $parent = null ) {

        $category = new Category(['name' => $data['name']]);

        $category->is_directory = isset($data['children']);

        if(!is_null($parent)) {
            $category->parent()->associate($parent);
        }
        $category->save();

        if (isset($data['children']) && is_array($data['children'])) {
            foreach($data['children'] as $child) {
                $this->createCategory($child, $category);
            }
        }
    }
}


