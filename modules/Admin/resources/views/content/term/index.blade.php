@extends('layouts::base')

@section('content')
    <div class="card card-bordered h-100">
        <div class="card-inner">
            <div class="card-head">
                <h5 class="card-title">Term and Condition</h5>
            </div>
            <form action="{{ route('admin.content.term.update') }}" method="POST">
                @csrf
                <ul class="nav nav-tabs mt-n3">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#term-condition">Term and Condition</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#policy">Privacy and Policy</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#about">About</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="term-condition">
                        <div class="form-control-wrap">
                            <textarea class="form-control" name="term" id="term" cols="30" rows="10">{{ $term->value }}</textarea>
                        </div>
                    </div>
                    <div class="tab-pane" id="policy">
                        <div class="form-control-wrap">
                            <textarea class="form-control" name="policy" id="policy" cols="30" rows="10">{{ $policy->value }}</textarea>
                        </div>
                    </div>
                    <div class="tab-pane" id="faq">
                        <div class="form-control-wrap">
                            <textarea class="form-control" name="faq" id="faq" cols="30" rows="10">{{ $faq->value }}</textarea>
                        </div>
                    </div>
                    <div class="tab-pane" id="about">
                        <div class="form-control-wrap">
                            <textarea class="form-control" name="about" id="about" cols="30" rows="10">{{ $about->value }}</textarea>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary float-right" style="margin-top: 10px" type="submit">Update</button>
            </form>
        </div>
    </div>
@endsection

@section('page-script')

@endsection

