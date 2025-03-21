<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    {{-- menampilkan data dari db --}}
    <h1>donasi</h1>
    <table>
        <tr>
            <th>no</th>
            <th>user_id</th>
            <th>proof_image</th>
        </tr>
        @foreach ($donations as $item)
        <tr>  
            <td>
                <img src="{{ asset('storage/donations/' . $item->proof_image) }}" alt="Proof Image" style="max-width: 150px;">
            </td>
        </tr>
    @endforeach
    

    </table>
</body>
</html>