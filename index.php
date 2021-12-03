<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>

  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>

</head>
<style>
  .container {
    width: 100%;
  }
</style>

<body>
  <!-- Navigation -->
  <div>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Project IIR</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="#">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="evaluasi.php">Evaluasi</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </div>
  <!-- End of navigation -->

  <!-- input keyword -->
  <div class="container">
    <div class="input-group mb-3 mx-auto my-5" style="width: 50%;">
      <input type="text" class="form-control" placeholder="Keyword" aria-label="Recipient's username" aria-describedby="button-addon2">
      <button class="btn btn-outline-secondary" type="button" id="button-addon2">Search</button>
    </div>
  </div>
  <!-- end input keyword -->

  <!-- method radio -->
  <div class="container">
    <div class="row justify-content-center">
      <div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
          <label class="form-check-label" for="exampleRadios1">
            Dice
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
          <label class="form-check-label" for="exampleRadios2">
            Jaccard
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="option3">
          <label class="form-check-label" for="exampleRadios3">
            Cosine
          </label>
        </div>
      </div>
    </div>
  </div>
  <!-- end radio -->

  <!-- result table -->
  <div class="container">
    <table class="display" id="dataTable">
      <thead>
        <tr>
          <th scope="col">User</th>
          <th scope="col">Tweets</th>
          <th scope="col">Label Sentiment</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td>Mark</td>
          <td>Otto</td>
        </tr>
        <tr>
          <td>2</td>
          <td>Jacob</td>
          <td>Thornton</td>
        </tr>
        <tr>
          <td>3</td>
          <td>Larry the Bird</td>
          <td></td>
        </tr>
      </tbody>
    </table>
  </div>
  <!-- end of table -->

  <!-- pie chart -->
  <div class="container">
    <div class="m-auto" id="piechart" style="width:500px; height:400px;">
      <?php
      ?>
    </div>
  </div>
  <!-- end of pie chart -->



</body>

</html>

<script type="text/javascript">
  $(document).ready(function() {
    $('#dataTable').DataTable();
  });
  // Load google charts
  google.charts.load('current', {
    'packages': ['corechart']
  });
  google.charts.setOnLoadCallback(drawChart);

  // Draw the chart and set the chart values
  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Task', 'Hours per Day'],
      ['Work', 2],
      ['Friends', 2],
      ['Eat', 2],
      ['TV', 2],
      ['Gym', 2],
      ['Sleep', 2]
    ]);

    var options = {
      'title': 'My Average Day',
    };

    // Display the chart inside the <div> element with id="piechart"
    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data, options);
  }
</script>