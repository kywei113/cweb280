<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- https://bootswatch.com themes: cerulean cosmo cyborg darkly flatly journal litera lumen lux
	materia	minty pulse sandstone simplex sketchy slate solar spacelab superhero united yeti -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.11.2/css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/portal-vue/dist/portal-vue.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-vue/dist/bootstrap-vue.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/http-vue-loader/src/httpVueLoader.js"></script>


    <title>Final Practice</title>
</head>

<body>
<div class="jumbotron text-center">
    <h1>Final Practice</h1>
</div>

<div class="container" id="app">

    <prac-component :title="pTitle" @send-text="updateBox"></prac-component>

    <p>{{pInfo}}</p>
    <footer class="row bg-info">
        <div class="col-sm-7">
            <h3>Vardump Vue Data</h3>
            <pre>{{}}</pre>
        </div>

        <div class="col-sm-5">
            <h3>Vardump axios result</h3>
            <pre>{{axiosResult}}</pre>
        </div>
    </footer>
</div>


<script>
    new Vue({
        el: '#app',
        data: {
            pInfo: "",
            pTitle: ""
        },
        methods:
            {
                updateBox: function(info)
                {
                    this.pInfo = info;
                    this.pTitle = this.pInfo;
                }
            },
        components:
            {
                'prac-component': httpVueLoader('finalPrac_Table.vue')
            }
    });
</script>

</body>
</html>

