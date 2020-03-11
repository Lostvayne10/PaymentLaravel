@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Make a payment</div>
                    <form action="" method="POST" id="paymentform">
                        @csrf 

                        <div class="row">
                            <div class="col- offset-md-1">
                                <label >How mucho you want to pay?</label>
                                <input type="number" 
                                name="value" 
                                min="5" 
                                step="0.01" 
                                class="form-control"
                                required
                                value="{{ mt_rand(500,100000) /100 }}">
                                <small class="form-text text-muted">
                                    Use values with up to two decimal positions,
                                    using dot '.'
                                </small>

                            </div>
                            <div class="col-">
                                <label for="">Currency</label>
                                <Select class="custom-select" name="currency" required>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->iso }}">
                                            {{ strtoupper($currency->iso) }}
                                        </option>
                                    @endforeach
                                </Select>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" id="payButton" class="btn btn-primary btn-lg">Pay</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
