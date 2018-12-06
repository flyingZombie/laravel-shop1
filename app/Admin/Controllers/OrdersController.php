<?php

namespace App\Admin\Controllers;

use App\Models\Order;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Admin\HandleRefundRequest;
use App\Exceptions\InternalException;

class OrdersController extends Controller
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

            $content->header('Orders List');
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

            $content->header('header');
            $content->description('description');

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

            $content->header('header');
            $content->description('description');

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
        return Admin::grid(Order::class, function (Grid $grid) {

            $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

            $grid->no('Order No');

            $grid->column('user.name', 'Buyer');

            $grid->total_amount('Total Amount')->sortable();

            $grid->paid_at('Paid At')->sortable();

            $grid->ship_status('Ship status')->display(function ($value)
            {
                return Order::$shipStatusMap[$value];
            });

            $grid->refund_status('Refund Status')->display(function ($value)
            {
                return Order::$refundStatusMap[$value];
            });

            $grid->disableCreateButton();

            $grid->actions(function ($actions)
            {
                $actions->disableDelete();
                $actions->disableEdit();
            });

            $grid->tools(function ($tools)
            {
                $tools->batch(function ($batch)
                {
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
        return Admin::form(Order::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function show(Order $order)
    {
        return Admin::content(function (Content $content) use ($order)
        {
            $content->header('View order');
            $content->body(view('admin.orders.show', ['order' => $order]));
        });
    }
    
    public function ship(Order $order, Request $request)
    {
        if (!$order->paid_at) {
            throw new InvalidRequestException('This order is not paid yet!');
        }

        if ($order->ship_status !== Order::SHIP_STATUS_PENDING) {
            throw new InvalidRequestException('This order has been delivered already!');
        }
        
        $data = $this->validate($request, [
            'express_company' => ['required'],
            'express_no' => ['required'], 
         ], [], [
            'express_company' => 'Shipping Company',
            'express_no' => 'Shipping No.'
        ]);
    

        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data' => $data,
        ]);
        return redirect()->back();
    }

    public function handleRefund(Order $order, HandleRefundRequest $request)
    {
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new InvalidRequestException('The status of order is not right!');
        }

        if ($request->input('agree')) {

            $extra = $order->extra ?:[];
            unset($extra['refund_disagree_reason']);
            $order->update([
                'extra' => $extra,
            ]);
            $this->_refundOrder($order);

        } else {
            $extra = $order->extra ?: [];
            $extra['refund_disagree_reason'] = $request->input('reason');
            $order->update([
                'refund_status' => Order::REFUND_STATUS_PENDING,
                'extra' => $extra,
            ]);
        }
        return $order;
    }

    protected function _refundOrder(Order $order) {

        switch ($order->payment_method) {
            case 'wechat':

                break;

            case 'alipay':

                $refundNo = Order::getAvailableRefundNo();

                $ret = app('alipay')->refund([
                    'out_trade_no' => $order->no,
                    'refund_amount' => $order->total_amount,
                    'out_request_no' => $refundNo,
                ]);

                if ($ret->sub_code) {
                    $extra = $order->extra;
                    $extra['refund_failed_code'] = $ret->sub_code;
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => $extra,
                    ]);
                } else {
                  $order->update([
                        'refund_no' => $refundNo,
                          'refund_status' => Order::REFUND_STATUS_SUCCESS,
                      ]);
                }
                break;
            default:
                throw new InternalException('Unknown order payment: '.$order->payment_method);
                break;
        }
    }

}
