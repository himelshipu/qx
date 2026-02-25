@extends('backend.layouts.app')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

    <div>
        <x-form.multiple-select/>
        <x-backend.shell.percentage />
    </div>

    <div>
        <x-backend.shell.chart />
    </div>

    <div>
        <x-backend.shell.statistics-chart />
    </div>

    <div>
        <x-backend.shell.calender-area />
    </div>

</div>

@endsection