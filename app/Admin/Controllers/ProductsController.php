<?php

namespace App\Admin\Controllers;

use App\Models\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Category;


class ProductsController extends CommonProductsController
{
    public function getProductType()
    {
        return Product::TYPE_NORMAL;
    }

    protected function customGrid(Grid $grid)
    {
        $grid->model()->with(['category']);
        $grid->id('ID')->sortable();
        $grid->title('Product Name');
        $grid->column('category.name', 'Categories');
        $grid->on_sale('On Sale')->display(function ($value) {
            return $value ? 'Yes' : 'No';
        });
        $grid->price('Price');
        $grid->rating('Rating');
        $grid->sold_count('Sold Count');
        $grid->review_count('Review Count');
    }

    protected function customForm(Form $form)
    {

    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Product::class, function (Grid $grid) {
            $grid->model()->where('type', Product::TYPE_NORMAL)->with(['category']);
            $grid->id('ID')->sortable();
            $grid->title('Product Name');
            $grid->column('category.name', 'Categories');
            $grid->on_sale('On Sale')->display(function ($value) {
                return $value ? 'Yes' : 'No';
            });
            $grid->price('Price');
            $grid->rating('Rating');
            $grid->sold_count('Sold Count');
            $grid->review_count('Review Count');

            $grid->actions(function ($actions) {
                //$actions->disableView();
                $actions->disableDelete();
            });
            $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Product::class, function (Form $form) {

            $form->hidden('type')->value(Product::TYPE_NORMAL);

            $form->text('title', 'Name')->rules('required');

            $form->select('category_id', 'Categories')->options(function($id) {
                $category = Category::find($id);
                if ($category) {
                    return [$category->id => $category->full_name ];
                }
            })->ajax('/admin/api/categories?is_directory=0');

            $form->image('image', 'Image')->rules('required|image');

            $form->editor('description', 'Desc')->rules('required');

            $form->radio('on_sale', 'On Sale')->options(['1' => '是', '0'=> '否'])->default('0');

            $form->hasMany('skus', 'SKU List', function (Form\NestedForm $form) {
                $form->text('title', 'SKU Name')->rules('required');
                $form->text('description', 'SKU Desc')->rules('required');
                $form->text('price', 'Price')->rules('required|numeric|min:0.01');
                $form->text('stock', 'On Stock')->rules('required|integer|min:0');
            });

            $form->saving(function (Form $form) {
                $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
            });
        });

    }
}
