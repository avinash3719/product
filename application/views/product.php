<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Products</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
</head>
<body>
<table id="tableProduct">
	<tr>
		<th>#</th>
		<th>Product Name</th>
		<th>Product Price</th>
		<th>Action</th>
	</tr>
</table>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>

<script>
	$(document).ready(function () {
// alert("test");
		$('#tableProduct').DataTable( {
			processing: true,
			serverSide: true,
			ajax: {
				url: 'getProducts'
			},
			columns: [
				{ data: 'id' },
				{ data: 'product_name' },
				{ data: 'product_price' },
				{ data: 1 }
				]
		} );
	})
</script>
</body>
</html>
