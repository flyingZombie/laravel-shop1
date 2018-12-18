<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Layout\Content;
use Encore\Admin\Grid;
use Encore\Admin\Form;
use App\Models\Category;

abstract class CommonProductsController extends Controller
{
  use HasResourceActions;

  abstract public function getProductType();

  public function index(Content $content)
  {
      return $content->header(Product::$typeMap[$this->getProductType()].' List')
                     ->body($this->grid());
  }

  public function edit($id, Content $content)
  {
      return $content->header('Edit '.Product::$typeMap[$this->getProductType()])
                     ->body($this->form()->edit($id));
  }

  public function create(Content $content)
  {
      return $content
          ->header('Create '.Product::$typeMap[$this->getProductType()])
          ->body($this->form());
  }

  abstract protected function customGrid(Grid $grid);

  protected function grid()
    {
        $grid = new Grid(new Product());

        $grid->model()->where('type', $this->getProductType())->orderBy('id', 'desc');

        $this->customGrid($grid);

        $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
            });
        $grid->tools(function ($tools) {
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        return $grid;
    }

  abstract protected function customForm(Form $form);

  protected function form()
  {
      $form = new Form(new Product());

      $form->hidden('type')->value($this->getProductType());

      $form->text('title', 'Product Name')->rules('required');

      $form->select('category_id', 'Category')->options(function ($id) {
          $category = Category::find($id);
          if ($category) {
              return [$category->id => $category->full_name];
          }
      })->ajax('/admin/api/categories?is_directory=0');

      $form->image('image', 'Image')->rules('required|image');

      $form->editor('description', 'Description')->rules('required');

      $form->radio('on_sale', 'On sale')->options(['1' => 'Yes', '0'=>'No'])->default(0);
        /*
      $form->text('crowdfunding.target_amount','Target Amount')->rules('required|numeric|min:0.01');

      $form->datetime('crowdfunding.end_at', 'End at')->rules('required|date');
        */
        $this->customForm($form);

      $form->hasMany('skus', function (Form\NestedForm $form) {
          $form->text('title', 'SKU name')->rules('required');
          $form->text('description', 'SKU desc')->rules('required');
          $form->text('price', 'Price')->rules('required|numeric|min:0.01');
          $form->text('stock', 'In Stock')->rules('required|integer|min:0');
      });

      $form->saving(function (Form $form) {
          $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price');
      });

      return $form;
  }
}