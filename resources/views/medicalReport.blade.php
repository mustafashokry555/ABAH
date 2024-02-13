@extends('layout')

@section('title')
Medical Report
@endsection

@section('table')
<tr>
    <td>Doctor</td>
    <td>{{ $data->DoctorName }}</td>
</tr>
<tr>
    @php
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s.u',  $data->VisitDate);
        $VisitDate = $dateTime->format('Y-m-d');
    @endphp
    <td>Visit Date</td>
    <td>{{ $VisitDate }}</td>

</tr>
<tr>
    <td>Visit Number</td>
    <td>{{ $data->VisitNo }}</td>
</tr>
@endsection

@section('content')    
<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;">Medical History</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Reason for Consultation</td>
            <td>{{ $data->ReasonforConsultation }}</td>
        </tr>
        <tr>
            <td>History of Illness</td>
            <td>{{ $data->Historyofillness }}}</td>
        </tr>
        <tr>
            <td>Treatment Plan</td>
            <td>{{ $data->treatmentplan }}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;">Physical Examination</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Physical Examination</td>
            <td>{{ $data->Significantsigns }}</td>
        </tr>
    </tbody>
</table>
@endsection
