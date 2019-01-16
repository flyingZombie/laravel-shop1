<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class SeckillProductsController extends CommonProductsController
{
    public function getProductType()
    {
        return Product::TYPE_SECKILL;
    }

    protected function customGrid(Grid $grid)
    {
        // TODO: Implement customGrid() method.
        $grid->id('ID')->sortable();
        $grid->title('Product Name');
        $grid->on_sale('On Sale')->display(function ($value) {
            return $value ? 'Yes' : 'No';
        });
        $grid->price('Price');
        $grid->column('seckill.start_at', 'Start time');
        $grid->column('seckill.end_at', 'End time');
        $grid->sold_count('Sold Count');
    }

    protected function customForm(Form $form)
    {
        // TODO: Implement customForm() method.
        $form->datetime('seckill.start_at', 'Start time')->rules('required|date');
        $form->datetime('seckill.end_at', 'End time')->rules('required|date');
    }
}