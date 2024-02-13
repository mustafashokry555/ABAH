@extends('layout')

@section('title')
Billing Report
@endsection

@section('table')
<tr>
    <td>Bill Number</td>
    <td>{{ $data->BillNo }}</td>
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
            <td>Doctor Name</td>
            <td>{{ $data->DoctorName }}</td>
        </tr>
        <tr>
            <td>Department Name</td>
            <td>{{ $data->Department_Name }}</td>
        </tr>
        <tr>
            <td>Service Name</td>
            <td>{{ $data->ServiceName }}</td>
        </tr>
        <tr>
            <td>Bill Date</td>
            <td>{{ $data->BillDate }}</td>
        </tr>
        <tr>
            <td>Rate</td>
            <td>{{ $data->Rate }}</td>
        </tr>
        <tr>
            <td>Amount</td>
            <td>{{ $data->Amount }}</td>
        </tr>
        <tr>
            <td>TotalAmount</td>
            <td>{{ $data->TotalAmount }}</td>
        </tr>
        <!-- Add more rows as needed -->
    </tbody>
</table>
@endsection