@extends('layout')

@section('title')
Radiology Report
@endsection

@section('table')
<tr>
    <td>Order Number</td>
    <td>{{ $data->OrderNo }}</td>
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
            <td>Report ID</td>
            <td>{{ $data->OrdDtlID }}</td>
        </tr>
        <tr>
            <td>Report Date</td>
            <td>{{ $data->ReportDate }}</td>
        </tr>
        <tr>
            <td>Report Name</td>
            <td>{{ $data->Service_Name }}</td>
        </tr>
        <tr>
            <td>Department Name</td>
            <td>{{ $data->SubDepartment_Name }}</td>
        </tr>
        <tr>
            <td>Radiology Result</td>
            <td>{{ $data->RadiologyResult }}</td>
        </tr>
        <!-- Add more rows as needed -->
    </tbody>
</table>
@endsection

