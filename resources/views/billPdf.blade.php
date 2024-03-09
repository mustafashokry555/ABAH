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
        <style>
            td{
                padding-bottom: 10px;
                padding-top: 10px;
            }
        </style>
        <!-- Add your data rows here -->
        <tr>
            <td>Doctor Name</td>
            <td>{{ $data->DoctorName }}</td>
        </tr>
        <tr>
            <td>Department Name</td>
            <td>{{ $data->DoctorSpeciality }}</td>
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
            <td>Total Amount</td>
            <td>{{ $data->TotalAmount }}</td>
        </tr>
        <tr>
            <td>Patient Share</td>
            <td>{{ $data->PatientShare }}</td>
        </tr>
        <tr>
            <td>Patient VAT</td>
            <td>{{ $data->PatientVAT }}</td>
        </tr>
        <!-- Add more rows as needed -->
    </tbody>
</table>
@endsection