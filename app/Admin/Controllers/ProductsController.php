<?php

namespace App\Admin\Controllers;

use App\Models\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProductsController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('Products List');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('Editing Product');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('Create Product');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Product::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->title('Product Name');
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

            $form->text('title', 'Name')->rules('required');

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
