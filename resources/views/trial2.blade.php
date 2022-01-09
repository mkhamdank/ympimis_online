<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, user-scalable=yes, initial-scale=1.0" name="viewport">
	<title>YMPI 情報システム</title>
</head>
<style type="text/css">
	.dot {
		height: 2px;
		width: 2px;
		position: absolute;
		border: 2px solid red;
		z-index: 10;
	}
</style>
<body>
	<div style='position:relative' id='wrapper'>
		<img src='{{ url("images/maps_WH.jpg") }}' style="width: 500px;">
	</div>
</body>
<script type="text/javascript">
	var str = 'gid=4&x=20y=30&x=40y=50&x=100y=100';
	var data = str.match(/(x|y)=(\d+)/g), pointsX = [], pointsY = [];
	for(var i = 0; i < data.length; i++)
	{
		var tmp = data[i].split('=');
		if (tmp[0] == 'x')
			pointsX.push(tmp[1]);
		else
			pointsY.push(tmp[1]);
	}
	for(var i = 0; i < pointsX.length; i++)
	{
		var div = document.createElement('div');
		div.className = 'dot';
		div.style.left = pointsX[i] + 'px';
		div.style.top = pointsY[i] + 'px';
		document.getElementById('wrapper').appendChild(div);
	}    
</script>
</body>
</html>