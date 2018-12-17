<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\CrowdfundingProduct;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class CrowdfundingProductsController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Crowd Funding Product List')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit Crowd-funding Product')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create Crowd-funding Product')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->model()->where('type', Product::TYPE_CROWDFUNDING);
        $grid->id('Id')->sortable();
        $grid->category_id('Category id');
        $grid->title('Product Name');
        $grid->on_sale('On sale')->display(function ($value) {
            return $value ? 'Yes' : 'No';
        });
        $grid->price('Price');
        $grid->column('crowdfunding.target_amount','Target Amount');
        $grid->column('crowdfunding.end_at', 'End at');
        $grid->column('crowdfunding.total_amount', 'Total Amount');
        $grid->column('crowdfunding.status',' Status')
            ->display(function ($value) {
              return CrowdfundingProduct::$statusMap[$value];
            });
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

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Product::findOrFail($id));

        $show->id('Id');
        $show->type('Type');
        $show->category_id('Category id');
        $show->title('Title');
        $show->description('Description');
        $show->image('Image');
        $show->on_sale('On sale');
        $show->rating('Rating');
        $show->sold_count('Sold count');
        $show->review_count('Review count');
        $show->price('Price');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        $form->hidden('type')->value(Product::TYPE_CROWDFUNDING);

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

        $form->text('crowdfunding.target_amount','Target Amount')->rules('required|numeric|min:0.01');

        $form->datetime('crowdfunding.end_at', 'End at')->rules('required|date');

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
