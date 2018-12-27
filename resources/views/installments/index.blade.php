@extends('layouts.app')
@section('title', 'Installments List')

@section('content')

    <div class="row">
      <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading text-center">
                <h2>Installments List</h2>
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                  <thead>
                  <tr>
                  <th>Ref. No</th>
                      <th>Amount</th>
                      <th>Period</th>
                      <th>Rate</th>
                      <th>Status</th>
                      <th>Action</th>
                  </tr>
                  </thead>
                    <tbody>
                    @foreach($installments as $installment)
                        <tr>
                            <td>{{ $installment->no }}</td>
                            <td>${{ $installment->total_amount }}</td>
                            <td>{{ $installment->count }}</td>
                            <td>{{ $installment->fee_rate }}%</td>
                            <td>{{ \App\Models\Installment::$statusMap[$installment->status] }}</td>
                            <td><a class="btn btn-primary btn-xs"
                                   href="{{ route('$installment.show', ['$installment' => $installment->id]) }}">View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pull-right">{{ $installments->render() }}</div>
            </div>
        </div>
      </div>
    </div>
@endsection