<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use function foo\func;

class CouponCodesController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     * @return Content
     */
    public function index()
    {
        return \Admin::content(function (Content $content) {
            $content->header('Coupon List');
            $content->body($this->grid());
        });
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
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
     * @param mixed   $id
     * @param Content $content
     * @return Content
     */
    public function edit($id)
    {
        return \Admin::content(function (Content $content) use ($id) {
            $content->header('Editing coupon');
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return \Admin::content(function (Content $content) {
            $content->header('Create new coupon');
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
        return \Admin::grid(CouponCode::class, function (Grid $grid) {
            $grid->model()->orderBy('created_at','desc');
            $grid->id('ID')->sortable();
            $grid->name('Name');
            $grid->code('Coupon Code');
            $grid->description('Desc');
            $grid->column('usage', 'Usage')->display(function ($value) {
                return "{$this->used} / {$this->total}";
            });
            $grid->enabled('Enabled')->display(function ($value) {
                return $value?'Yes':'No';
            });

            $grid->created_at('Created At');
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(CouponCode::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->code('Code');
        $show->type('Type');
        $show->value('Value');
        $show->total('Total');
        $show->used('Used');
        $show->min_amount('Min amount');
        $show->not_before('Not before');
        $show->not_after('Not after');
        $show->enabled('Enabled');
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
        return \Admin::form(CouponCode::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', 'Name')->rules('required');

            //$form->text('code', 'Code')->rules('nullable|unique:coupon_codes');

            $form->text('code', 'Code')->rules(function($form) {
              if ($id = $form->model()->id) {
                return 'nullable|unique:coupon_codes,code,'.$id.',id'; //TODO
              } else {
                  return 'nullable|unique:coupon_codes';
              }
            });

            $form->radio('type', 'Type')->options(CouponCode::$typeMap)->rules('required');
            $form->text('value', 'Discount')->rules(function ($form) {
                if ($form->type === CouponCode::TYPE_PERCENT) {
                    return 'required|numeric|between:1,99';
                } else {
                    return 'required|numeric|min:0.01';
                }
            });
            $form->text('total', 'Total')->rules('required|numeric|min:0');
            $form->text('min_amount', 'Minimum')->rules('required|numeric|min:0');
            $form->datetime('not_before', 'Start Since');
            $form->datetime('not_after', 'End by');
            $form->radio('enabled', 'Enabled')->options(['1'=>'Y', '0' => 'N']);
            $form->saving(function (Form $form) {
                if ( !$form->code ) {
                  $form->code = CouponCode::findAvailable();
                }
            });
        });
    }
}
