<?php
/**************************************
 * File Name: practiceAPI-ui.php
 * User: Kyle Wei - cst234
 * Date: 2019-10-10
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- https://bootswatch.com themes: cerulean cosmo cyborg darkly flatly journal litera lumen lux
        materia	minty pulse sandstone simplex sketchy slate solar spacelab superhero united yeti -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/portal-vue/dist/portal-vue.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.js"></script>

    <title>Title</title>
</head>
<body>
<div class="jumbotron text-center">
    <h1>Title</h1>
    <p></p>
</div>
<div class="container" id="app">
    <form method="post" action="">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" v-model="firstName"/>
        </div>

        <button type="submit" class="btn btn-primary" @click="postForm">Submit</button>
    </form>

    <footer class="row bg-info">
        <div class="col-sm-4">
            <h3>Vardump </h3>
            <p><?php var_dump() ?></p>
        </div>
        <div class="col-sm-4">
            <h3>Vardump </h3>
            <p><?php var_dump() ?></p>
        </div>
        <div class="col-sm-4">
            <h3>Vardump </h3>
            <p><?php var_dump() ?></p>
        </div>
    </footer>
</div>

<script>
    new Vue({
       el: "#app",
       data:
           {
               firstName: "",
               lastName: "",
               returnFName: "",
               returnLName: ""
           },
        methods:
            {
                postForm: function()
                {

                }
            }
    });

</script>

</body>
</html>