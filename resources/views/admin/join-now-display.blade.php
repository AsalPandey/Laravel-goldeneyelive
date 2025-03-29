<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Now Queries</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Join Now Queries</h2>
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>S.N</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Course</th>
                    <th>Queries</th>
                </tr>
            </thead>
            <tbody>
                @foreach($joinNowQueries as $data)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $data->firstName }}</td>
                    <td>{{ $data->lastName }}</td>
                    <td>{{ $data->email }}</td>
                    <td>{{ $data->phone }}</td>
                    <td>{{ $data->address }}</td>
                    <td>{{ $data->course }}</td>
                    <td>{{ $data->queries }}</td>
                </tr>
                @endforeach
                @if($joinNowQueries->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">No records found</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Bootstrap 5 JS and Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybF1jXaB1PaQje8zO3RrGgAt1Qx72l6b5fuBxib7hhZbVv4FJ" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0sFfKzU6w6ecH2OM5Zhc20R7zVgX+9kk6GgIY5TbXYgKn5E7" crossorigin="anonymous"></script>
</body>
</html>
