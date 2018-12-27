@extends('layouts.app')
@section('title', 'Installment Details')

@section('content')
    <div class="row">
        <div class="col-lg-10 col-lg-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading text-center">
                    <h4>Installment Details</h4>
                </div>
                <div class="panel-body">
                    <div class="installment-top">
                        <div class="installment-info">
                            <div class="line">
                                <div class="line-label">Order：</div>
                                <div class="line-value">
                                    <a target="_blank" href="{{ route('orders.show', ['order' => $installment->order_id]) }}">View</a>
                                </div>
                            </div>
                            <div class="line">
                                <div class="line-label">Amount：</div>
                                <div class="line-value">${{ $installment->total_amount }}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">Period：</div>
                                <div class="line-value">Period {{ $installment->count }}</div>
                            </div>
                            <div class="line">
                                <div class="line-label">Rate：</div>
                                <div class="line-value">{{ $installment->fee_rate }}%</div>
                            </div>
                            <div class="line">
                                <div class="line-label">Fine：</div>
                                <div class="line-value">{{ $installment->fine_rate }}%</div>
                            </div>
                            <div class="line">
                                <div class="line-label">status：</div>
                                <div class="line-value">{{ \App\Models\Installment::$statusMap[$installment->status] }}</div>
                            </div>
                        </div>
                        <div class="installment-next text-right">
                            <!-- 如果已经没有未还款的还款计划，说明已经结清 -->
                            @if(is_null($nextItem))
                                <div class="installment-clear text-center">All cleared</div>
                            @else
                                <div>
                                    <span>To pay：</span>
                                    <div class="value total-amount">${{ $nextItem->total }}</div>
                                </div>
                                <div>
                                    <span>Due by：</span>
                                    <div class="value">{{ $nextItem->due_date->format('Y-m-d') }}</div>
                                </div>
                                <div class="payment-buttons">
                                    <a class="btn btn-primary btn-sm"
                                       href="{{ route('installments.alipay', ['installment' => $installment->id]) }}">Alipay</a>
                                    <button class="btn btn-sm btn-success" id='btn-wechat'>Wechat</button>
                                </div>
                            @endif
                        </div>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Period</th>
                            <th>Due by</th>
                            <th>Status</th>
                            <th>Base</th>
                            <th>Rate</th>
                            <th>Fine</th>
                            <th class="text-right">Sum</th>
                        </tr>
                        </thead>
                        @foreach($items as $item)
                            <tr>
                                <td>
                                    Period {{ $item->sequence + 1 }}/{{ $installment->count }}
                                </td>
                                <td>{{ $item->due_date->format('d-m-Y') }}</td>
                                <td>
                                    <!-- 如果是未还款 -->
                                @if(is_null($item->paid_at))
                                    <!-- 这里使用了我们之前在模型里定义的访问器 -->
                                        @if($item->is_overdue)
                                            <span class="overdue">Overdue</span>
                                        @else
                                            <span class="needs-repay">To pay</span>
                                        @endif
                                    @else
                                        <span class="repaid">Paid</span>
                                    @endif
                                </td>
                                <td>${{ $item->base }}</td>
                                <td>${{ $item->fee }}</td>
                                <td>{{ is_null($item->fine) ? 'No' : ('$'.$item->fine) }}</td>
                                <td class="text-right">${{ $item->total }}</td>
                            </tr>
                        @endforeach
                        <tr><td colspan="7"></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection