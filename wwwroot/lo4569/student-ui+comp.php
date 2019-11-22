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


    <title>Student UI with Components</title>
</head>
<body>
<div class="jumbotron text-center">
    <h1>Student UI with Components</h1>
</div>

<!--<div id="comp-demo">-->
<!--    <button-counter></button-counter>-->
<!--</div>-->

<div class="container" id="managed_by_vue_js">
    <!--Student Table-->
    <table class="table table-dark table-striped table-bordered table-hover">
        <tr>
            <th scope="col">Student ID</th>
            <th scope="col">Family Name</th>
            <th scope="col">Given Name</th>
            <th scope="col">Preferred Name</th>
            <th scope="col">Username</th>
        </tr>
        <!--Vue takes in prop names in camelCase but they are called in kebob-case-->
        <!--Input New Student-->
        <tr is="student-input" hide-save class="table-info" ></tr>

        <!--Student List-->
        <tr is="student-input" hide-save v-for="stu in students" :key="stu.studentID" :student="stu"></tr>
    </table>

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
        el: '#managed_by_vue_js',
        data: {
            students:[],
            axiosResult: {}
        },
        methods:
            {
            getData: function () {
                axios.get('students-api.php', {params: {}})
                    .then(response => {
                        this.axiosResult = response;//ONLY FOR DEBUG
                        this.students = response.data;
                    })
                    .catch(errors => {
                        let response = errors.response;
                        this.axiosResult = response;//ONLY FOR DEBUG
                        if (response.status == 404) {//NOT FOUND
                            alert(response.config.url + " was not found");
                        }
                    })
                    .finally()

                }
            },
        components:
            {
              'student-input':  httpVueLoader('./StudentInput.vue')
            },
        mounted() {
            this.getData();
        }
    });

    Vue.component('button-counter', {
        data: function () {
            return {
                count: 0
            }
        },
        template: '<button v-on:click ="count++">You clicked me {{ count }} times.</button>'
    });

    new Vue({
        el: '#comp-demo'
    });
</script>

</body>
</html>