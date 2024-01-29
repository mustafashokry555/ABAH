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

        footer {
            background-color: #B21F24;
            color: #fff;
            text-align: center;
            /* padding: 10px 0; */
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
    </style>
</head>
<body>

    <header>
        <img src="{{'data:image/png;base64,'.base64_encode(file_get_contents(public_path('assets/MainLogo.png')))}}">
        {{-- <h1>Hospital Report</h1> --}}
    </header>

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

    <footer>
        <p>For inquiries, call our 24/7 helpline: +1-123-456-7890</p>
        <p>Follow us on <a href="https://www.facebook.com/hospitalpage" target="_blank">Facebook</a></p>
    </footer>

</body>
</html>
