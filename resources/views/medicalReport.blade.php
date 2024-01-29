<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
            color: #333;
        }

        header {
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
            padding: 10px 0;
        }

        table {
            width: 100%;
            margin-top: 20px;
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

        section {
            margin: 20px 0;
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
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
    </header>

    <table>
        <tbody>
            <tr>
                <td>Patient ID</td>
                <td>{{ $data->PatientID }}</td>
            </tr>
            <tr>
                <td>Visit Number</td>
                <td>{{ $data->VisitNo }}</td>
            </tr>
            <tr>
                <td>Doctor In Charge</td>
                <td>{{ $data->DocInCharge }}</td>
            </tr>
            <!-- Add more rows with relevant data -->
        </tbody>
    </table>

    <section>
        <h2>Medical History</h2>
        <p>Reason for Consultation: {{ $data->ReasonforConsultation }}</p>
        <p>History of Illness: {{ $data->Historyofillness }}</p>
        <p>Treatment Plan: {{ $data->treatmentplan }}</p>
        <!-- Add more medical history details as needed -->
    </section>

    <section>
        <h2>Physical Examination</h2>
        <p>Significant Signs: {{ $data->Significantsigns }}</p>
        <!-- Add more physical examination details as needed -->
    </section>

    <footer>
        <p>Medical Report issued by Dr. {{ $data->DoctorName }}, {{ $data->DocDepartment }}</p>
        <p>Contact our hospital at: +1-123-456-7890</p>
    </footer>

</body>
</html>
