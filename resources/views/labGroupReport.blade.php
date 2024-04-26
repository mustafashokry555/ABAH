
@extends('layout')

@section('title')
Lap Report
@endsection

@section('table')
<tr>
    <td>Lab Number</td>
    <td>{{ $data[0]->LabNo }}</td>
</tr>
<tr>
    <td>Report Date</td>
    <td>{{ $data[0]->ReportDate }}</td>
</tr>
@endsection

@section('content')    
<table>
    <thead>
        <tr>
            <th>Test Name</th>
            <th>Param Name</th>
            <th>Result</th>
            <th>Param Normal Range</th>
            {{-- <th>Remarks</th> --}}
        </tr>
    </thead>
    <tbody>
        <!-- Add your data rows here -->
        @foreach ($data as $item)    
        <tr>
            <td>{{ $item->TestName }}</td>
            <td>{{ $item->ParamName }}</td>
            <td>{{ $item->Result }}</td>
            <td>{{ $item->ParamNormalRange }}</td>
            {{-- <td>{{ $item->Remarks }}</td> --}}
        </tr>
        @endforeach
        <!-- Add more rows as needed -->
    </tbody>
</table>
@endsection
