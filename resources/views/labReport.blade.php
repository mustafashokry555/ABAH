
@extends('layout')

@section('title')
Lap Report
@endsection

@section('table')
<tr>
    <td>Lab Number</td>
    <td>{{ $data->LabNo }}</td>
</tr>
@endsection

@section('content')    
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Desc</th>
        </tr>
    </thead>
    <tbody>
        <!-- Add your data rows here -->
        <tr>
            <td>Test Name</td>
            <td>{{ $data->TestName }}</td>
        </tr>
        <tr>
            <td>Parametar Name</td>
            <td>{{ $data->ParamName }}</td>
        </tr>
        <tr>
            <td>Report Date</td>
            <td>{{ $data->ReportDate }}</td>
        </tr>
        <tr>
            <td>Normal Range</td>
            <td>{{ $data->ParamNormalRange }}</td>
        </tr>
        <tr>
            <td>Result</td>
            <td>{{ $data->Result }}</td>
        </tr>
        <!-- Add more rows as needed -->
    </tbody>
</table>
@endsection
