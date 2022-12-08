<!doctype html>
<html lang="en">

<head>
    <title>Add Remove input fields dynamically using jQuery bootstrap</title>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="assets/js/custom.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/sticky-footer.css">
</head>

<body>
<div class="container"  style="margin-top: 20px">
    <h4>Add Keywords</h4>
    <form name="keywords" id="keywords">
        <div class="input-group control-group after-add-more">
            <input type="text" name="keywords[]" class="form-control" placeholder="Type keyword here...">
            <div class="input-group-btn">
                <button class="btn btn-success add-more" type="button"><i class="glyphicon glyphicon-plus"></i> Add</button>
            </div>
        </div>
    </form>
    <div class="container">
        <div class="row">
            <br>
            <a href="#" class="btn btn-lg btn-primary btn-success" id="submit"><span class="glyphicon glyphicon-search"></span> Submit</a>
        </div>
    </div>
    <div class="copy-fields hide">
        <div class="control-group input-group" style="margin-top:10px">
            <input type="text" name="keywords[]" class="form-control" placeholder="Type keyword here...">
            <div class="input-group-btn">
                <button class="btn btn-danger remove" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>Search result for submitted keyword(s)</h1>
        <section class="col-xs-12 col-sm-6 col-md-12" id="final_result">
        </section>
    </div>

    <div id="loader" class="lds-dual-ring hidden overlay"></div>

</body>

</html>
