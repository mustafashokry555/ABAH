<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            /* background-color: #007bff; */
            color: #fff;
            text-align: center;
            padding: 20px 0;
        }

        header img {
            max-width: 200px; /* Adjust the width as needed */
            height: auto;
        }

        /* footer {
            background-color: #B21F24;
            color: #fff;
            text-align: center;
            padding: 10px 0; 
            position: fixed;
            bottom: 0;
            width: 100%;
        } */

        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #B21F24;
            color: #fff;
        }

        tr:hover {
            background-color: #f5f5f5;
        }
        p{
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .red-line {
            border-top: 3px solid red;
            margin-top: 20px; /* Adjust as needed */
        }
    </style>
</head>
<body>

    <header>
        <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('assets/MainLogo.png')))}}">
        {{-- <h1>Hospital Report</h1> --}}
    </header>
    <hr class="red-line">

    <h1 style="text-align: center">Billing Report</h1>
    <table>
        <tbody>
            <tr>
                <td>Patient MRN</td>
                <td>{{ $patient->Registration_No }}</td>
            </tr>
            <tr>
                <td>Patient Name</td>
                <td>{{ "$patient->First_Name $patient->Middle_Name $patient->Last_Name" }}</td>
            </tr>
            <tr>
                <td>Patient Age</td>
                <td>{{ calculateAge($patient->Date_Of_Birth) }}</td>
            </tr>
            {{-- <tr>
                <td>Patient Number</td>
                <td>{{ $patient->Mobile }}</td>
            </tr> --}}
            <tr>
                {{-- <td>Bill Number</td>
                <td>{{ $data->BillNo }}</td> --}}
                <td>Bill Number</td>
                <td>{{ $data[0]->BillNo }}</td>
            </tr>
            <tr>
                <td>Bill Date</td>
                <td>{{ $data[0]->BillDate }}</td>
            </tr>
            <tr>
                <td>Doc.Name</td>
                <td>{{ $data[0]->DoctorName }}</td>
            </tr>
            <tr>
                <td>Dep.Name</td>
                <td>{{ $data[0]->DoctorSpeciality }}</td>
            </tr>
            <!-- Add more rows with relevant data -->
        </tbody>
    </table>

    <table>
        <thead>
            <tr>
                {{-- <th>Doc.Name</th>
                <th>Dep.Name</th> --}}
                <th>Service Name</th>
                <th>Rate</th>
                <th>Amount</th>
                <th>Tot.Amount</th>
                <th>Patient Share</th>
                <th>Patient VAT</th>
                {{-- <th>Desc</th> --}}
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
            @foreach ($data as $row)
                <tr>
                    {{-- <td>{{ $row->DoctorName }}</td>
                    <td>{{ $row->DoctorSpeciality }}</td> --}}
                    <td>{{ $row->ServiceName }}</td>
                    <td>{{ $row->Rate }}</td>
                    <td>{{ $row->Amount }}</td>
                    <td>{{ $row->TotalAmount }}</td>
                    <td>{{ $row->PatientShare }}</td>
                    <td>{{ $row->PatientVAT }}</td>
                </tr>
            @endforeach
            
        </tbody>
    </table>
</body>
</html>