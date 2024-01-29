<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Prescription</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            color: #333;
            text-align: center;
            padding: 20px 0;
        }

        header img {
            max-width: 200px; /* Adjust the width as needed */
            height: auto;
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }

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
            background-color: #333;
            color: #fff;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        p {
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <header>
        <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('assets/MainLogo.png')))}}">
        {{-- <h1>Medical Prescription</h1> --}}
    </header>

    <section>
        <p><strong>Patient Name:</strong>{{$data[0]->PatientName}}</p>
        <p><strong>Visit Date:</strong>{{$data[0]->VisitDate}}</p>
        <p><strong>Age:</strong>{{$data[0]->VisitDate}}</p>
    </section>

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

    <footer>
        <p>Prescription issued by Dr. John Doe, MD</p>
        <p>For any concerns, please contact our pharmacy: +1-123-456-7890</p>
    </footer>

</body>
</html>
