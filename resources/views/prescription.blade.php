
@extends('layout')

@section('title')
Prescription
@endsection

@section('table')
<tr>
    <td>Doctor</td>
    <td>{{ $data[0]->DocInCharge }}</td>
</tr>
<tr>
    <td>Complaint</td>
    <td>{{ $data[0]->Cheif_Complaint }}</td>
</tr>
<tr>
    <td>VisitDate</td>
    <td>{{ $data[0]->VisitDate }}</td>
</tr>
<tr>
    <td>Prescription ID</td>
    <td>{{ $data[0]->PreID }}</td>
</tr>
@endsection

@section('content')    
<table>
    <thead>
        <tr>
            <th>Medication</th>
            <th>Dosage</th>
            <th>Type</th>
            <th>Duration</th>
        </tr>
    </thead>
    <tbody>
        <!-- Add your prescription data rows here -->
        @foreach ($data as $item)
        <tr>
            <td>{{ $item->Medicine }}</td>
            <td>{{ $item->Frequency }}</td>
            <td>{{ $item->Route }}</td>
            <td>{{ $item->Days }}</td>
        </tr>
        @endforeach
        <!-- Add more rows as needed -->
    </tbody>
</table>
@endsection

