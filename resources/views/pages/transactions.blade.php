@extends('layouts.theme')

@section('content')
    <div class="container-fluid my-5">
        <h1>Transactions Query</h1>

        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3 col-xl-2">
                <div class="filters my-4 mx-2">
                    <form action="{{ route('transactions') }}" method="GET">
                        <div class="mb-3">
                            <label class="form-label"><i class="fa-regular fa-calendar"></i> Dates:</label>
                            <input name="dates" class="form-control datepicker">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Operation:</label>
                            <select name="operation" class="form-select">
                                <option value="">ANY</option>
                                @if (isset($filterOptions['operation']))
                                    @foreach ($filterOptions['operation'] as $option)
                                        <option {{ $initialValues['operation'] == $option ? 'selected' : '' }} value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method:</label>
                            <select name="paymentMethod" class="form-select">
                                <option value="">ANY</option>
                                @if (isset($filterOptions['paymentMethod']))
                                    @foreach ($filterOptions['paymentMethod'] as $option)
                                        <option {{ $initialValues['paymentMethod'] == $option ? 'selected' : '' }} value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Error Code:</label>
                            <select name="errorCode" class="form-select">
                                <option value="">ANY</option>
                                @if (isset($filterOptions['errorCode']))
                                    @foreach ($filterOptions['errorCode'] as $option)
                                        <option {{ $initialValues['errorCode'] == $option ? 'selected' : '' }} value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Filter Field:</label>
                            <select id="filterField" name="filterField" class="form-select">
                                <option value="">NONE</option>
                                @if (isset($filterOptions['filterField']))
                                    @foreach ($filterOptions['filterField'] as $option)
                                        <option {{ $initialValues['filterField'] == $option ? 'selected' : '' }} value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                @endif
                            </select>                            
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Filter Value:</label>
                            <input class="form-control" id="filterValue" name="filterValue" value="{{ $initialValues['filterValue'] }}" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status:</label>
                            <select name="status" class="form-select">
                                <option value="">ANY</option>
                                @if (isset($filterOptions['status']))
                                    @foreach ($filterOptions['status'] as $option)
                                        <option {{ $initialValues['status'] == $option ? 'selected' : '' }} value="{{ $option }}">{{ $option }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    
                        <div class="mb-3">
                            <a href="{{ route('transactions') }}" class="btn btn-secondary">Clear</a>
                            <button type="submit" class="btn btn-dark">Apply</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9 col-xl-10">
                @if (isset($error) && $error)
                    <div class="alert alert-danger" style="font-weight: 300;" role="alert">
                        {{ $error }}
                    </div>
                @endif

                <table class="table table-striped table-transactions mt-4">
                    <thead>
                        <tr>
                            <th class="text-center">Date</th>
                            <th>Customer</th>
                            <th>Merchant</th>
                            <th>Acquirer</th>
                            <th>Transaction</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @if (isset($transactions['data']) && is_array($transactions['data']) && count($transactions['data']) > 0)
                            @foreach ($transactions['data'] as $transaction)
                                <tr>
                                    <td class="text-center">
                                        {{  isset($transaction['created_at']) ? \DateTime::createFromFormat('Y-m-d H:i:s', $transaction['created_at'])->format('d.m.Y') : 'N/A' }}
                                    </td>

                                    <td>
                                        @if (isset($transaction['customerInfo']))
                                            {{ $transaction['customerInfo']['billingFirstName'].' '.$transaction['customerInfo']['billingLastName'] }} <br />
                                            {{ $transaction['customerInfo']['email'] }}
                                        @else
                                            {{ 'N/A' }}
                                        @endif
                                    </td>

                                    <td>
                                        {{ isset($transaction['merchant']) ? $transaction['merchant']['name'] : 'N/A' }}
                                    </td>

                                    <td>
                                        {{ isset($transaction['acquirer']) ? $transaction['acquirer']['name'] : 'N/A' }}
                                    </td>
                                    
                                    <td>
                                        @if (isset($transaction['transaction']) && isset($transaction['transaction']['merchant']))
                                            <b>ID: </b> <a class="transactionId" data-transactionId="{{ $transaction['transaction']['merchant']['transactionId'] }}" href="#">{{ $transaction['transaction']['merchant']['transactionId'] }}</a> <br />
                                            <b>Ref: </b> {{ $transaction['transaction']['merchant']['referenceNo'] }}
                                        @else
                                            {{ 'N/A' }}
                                        @endif
                                    </td>

                                    <td class="text-center">
                                        {{ isset($transaction['fx']) && isset($transaction['fx']['merchant']) ? $transaction['fx']['merchant']['originalAmount'].' '.$transaction['fx']['merchant']['originalCurrency'].' > '.$transaction['fx']['merchant']['convertedAmount'].' '.$transaction['fx']['merchant']['convertedCurrency'] : 'N/A' }}
                                    </td>

                                    <td class="text-center">
                                        {{ isset($transaction['transaction']) && isset($transaction['transaction']['merchant']) ? $transaction['transaction']['merchant']['status'] : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>

                <div style="text-align: right;">
                    @if (isset($transactions['prev_page_url']) && $transactions['prev_page_url'])
                        <a href="{{ route('transactions').'?page='.(intval($transactions['current_page']) - 1) }}" class="btn btn-dark">Previous Page</a>
                    @endif

                    @if (isset($transactions['next_page_url']) && $transactions['next_page_url'])
                        <a href="{{ route('transactions').'?page='.(intval($transactions['current_page']) + 1) }}" class="btn btn-dark">Next Page</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title"></div>
                    <button type="button" class="btn close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('additional-javascript-codes')
    @parent

    <script type="text/javascript">
        $('.datepicker').daterangepicker({
            "showDropdowns": true,
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": " | ",
                "applyLabel": "Apply",
                "cancelLabel": "Cancel",
                "fromLabel": "From",
                "toLabel": "To",
                "customRangeLabel": "Custom",
                "weekLabel": "W",
                "daysOfWeek": [
                    "Su",
                    "Mo",
                    "Tu",
                    "We",
                    "Th",
                    "Fr",
                    "Sa"
                ],
                "monthNames": [
                    "January",
                    "February",
                    "March",
                    "April",
                    "May",
                    "June",
                    "July",
                    "August",
                    "September",
                    "October",
                    "November",
                    "December"
                ],
                "firstDay": 1,
            },
            "startDate": "{{ $initialValues['fromDate'] }}",
            "endDate": "{{ $initialValues['toDate'] }}"
        });

        $('#filterField').on('change', function() {
            if($(this).val() == '') {
                $('#filterValue').attr('disabled', 'disabled');
            } else {
                $('#filterValue').removeAttr('disabled');
            }
        });
        $('#filterField').change();
        
        $('a.transactionId').each(function() {
            $(this).on('click', function(e) {
                var transactionId = $(this).attr('data-transactionId');
                $.get("{{ route('transaction', ['transactionId' => '__TRANSACTION_ID__']) }}".replace('__TRANSACTION_ID__', transactionId), function(data) {
                    var modalHeading = "<b>#</b>" + data.transaction.transaction.merchant.transactionId;
                    var modalContent = `
                        <div class="mb-4">
                            <b style="font-size: 16px">Transaction:</b> <br /><br />
                            <b>Date:</b> ` + data.transaction.transaction.merchant.created_at + `<br />
                            <b>Id:</b> ` + data.transaction.transaction.merchant.transactionId + `<br />
                            <b>Ref:</b> ` + data.transaction.transaction.merchant.referenceNo + `<br />
                            <b>Status:</b> ` + data.transaction.transaction.merchant.status + `
                        </div>

                        <div class="my-4">
                            <b style="font-size: 16px">Customer:</b> <br /><br />
                            <b>Company:</b> ` + data.transaction.customerInfo.billingCompany + `<br />
                            <b>First Name:</b> ` + data.transaction.customerInfo.billingFirstName + `<br />
                            <b>Last Name:</b> ` + data.transaction.customerInfo.billingLastName + `<br />
                            <b>Email:</b> ` + data.transaction.customerInfo.email + `
                        </div>

                        <div class="mb-4">
                            <b style="font-size: 16px">Merchant:</b> <br /><br />
                            <b>Name:</b> ` + data.transaction.merchant.name + `<br />
                        </div>

                        <div class="mb-4">
                            <b style="font-size: 16px">FX:</b> <br /><br />
                            <b>Original Amount:</b> ` + data.transaction.fx.merchant.originalAmount + ` ` + data.transaction.fx.merchant.originalCurrency + `<br />
                        </div>
                    `;
                    
                    $('#transactionModal .modal-content .modal-header .modal-title').empty();
                    $('#transactionModal .modal-content .modal-header .modal-title').append(modalHeading);
                    $('#transactionModal .modal-content .modal-body').empty();
                    $('#transactionModal .modal-content .modal-body').append(modalContent);
                    $('#transactionModal').modal('show');
                });
            })
        });
    </script>
@endsection