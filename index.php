<?php


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Home</title>

  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>

</head>
<style>
  .container {
    width: 100%;
  }

  .loader {
    border: 16px solid #f3f3f3;
    /* Light grey */
    border-top: 16px solid #3498db;
    /* Blue */
    border-radius: 50%;
    width: 120px;
    height: 120px;
    animation: spin 2s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }
</style>

<body>
  <!-- Navigation -->
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
  <!-- End of navigation -->
  <!-- input keyword -->
  <div class="container">
    <!-- <form action="uas.php" method="post"> -->
    <div class="input-group mb-3 mx-auto my-5" style="width: 50%;">
      <h3>Keyword&nbsp&nbsp&nbsp</h3>
      <input type="text" class="form-control" placeholder="Keyword" aria-label="Recipient's username" aria-describedby="button-addon2" id="keyword" name="keyword">
      <button class="btn btn-outline-secondary" type="submit" id="button-addon2" onclick="process()">Search</button>
    </div>
  </div><br><br>
  <!-- end input keyword -->

  <!-- method radio -->
  <div class="container">
    <div class="row justify-content-center">
      <p>Pilih Metode Similaritas</p>
      <div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="method" id="exampleRadios1" value="Overlap" checked>
          <label class="form-check-label">
            Overlap
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="method" id="exampleRadios2" value="Asymmetric">
          <label class="form-check-label">
            Asymmetric
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="method" id="exampleRadios3" value="Cosine">
          <label class="form-check-label">
            Cosine
          </label>
        </div>
      </div>
    </div>
  </div><br><br>
  <!-- </form> -->
  <!-- end radio -->
  <!-- result table -->
  <div class="container">
    <table class="display" id="dataTable" style="display: none;">
      <thead>
        <tr>
          <th scope="col">User</th>
          <th scope="col">Tweets</th>
          <th scope="col">Label Sentiment</th>
        </tr>
      </thead>
      <tbody id="body">
      </tbody>
    </table>
    <div class="loader" id="loader" style="display: none;"></div>
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
  var negative = 0
  var neutral = 0
  var positive = 0

  function process() {
    negative = 0
    neutral = 0
    positive = 0
    var keyword = document.getElementById("keyword").value;
    var method = document.getElementsByName('method');
    $('#body').empty();
    $('#loader').show();
    for (i = 0; i < method.length; i++) {
      if (method[i].checked) {
        method = method[i].value;
        break;
      }
    }

    $.ajax({
      type: 'POST',
      url: 'uas.php',
      data: {
        'keyword': keyword,
        'method': method,
      },
      dataType: 'json',
      success: function(data) {
        $('#loader').hide();
        $('#dataTable').show();
        var hasil = ""
        for (var i = 0; i < data.length; i++) {
          ((data[i]['predictedLabel'] == 1) ? positive++ : ((data[i]['predictedLabel'] == 0) ? negative++ : neutral++))
          $('#body').append('<tr> <td>' + data[i]['crawl']['userid'] + '</td>' +
            '<td>' + data[i]['crawl']['text'] + '</td>' +
            '<td>' + ((data[i]['predictedLabel'] == 1) ? 'Positive' : ((data[i]['predictedLabel'] == 0) ? 'Negative' : 'Neutral')) + '</td></tr>');
        }
        $('#dataTable').DataTable();

        // Load google charts
        google.charts.load('current', {
          'packages': ['corechart']
        });
        google.charts.setOnLoadCallback(drawChart);
      },
      error: function(xhr) {
        $('#loader').hide();
        console.log(xhr);
      }
    });
  }

  // Draw the chart and set the chart values
  function drawChart() {
    var data = google.visualization.arrayToDataTable([
      ['Task', 'Hours per Day'],
      ['Negative', negative],
      ['Nutral', neutral],
      ['Positive', positive]
    ]);

    // Display the chart inside the <div> element with id="piechart"
    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
    chart.draw(data);
  }
</script>