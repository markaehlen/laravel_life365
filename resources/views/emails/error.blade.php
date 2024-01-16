

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Error Email in Api</title>
</head>
<body>
	<div class="detail">
		<p>Hey check it.Error is occure in Api!</p>
        <p>File No#  <strong>{{$error['filename']??''}}</strong></p>

		<p>{!!$error['error']??''!!}</p>
		
		<p>Regards, <br/>
        <p>Thanks to Life365!</p> 
	</div>
	
</body>
</html>
