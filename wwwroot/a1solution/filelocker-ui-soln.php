<?php
/**************************************
 * File Name: filelocker-ui.php
 * User: ins226
 * Date: 2019-10-01
 * Project: CWEB280
 *
 *
 **************************************/
/*****SERVER BLOCK*******/
session_start();
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

    <title>Upload Files</title>
</head>
<body>
<div class="jumbotron text-center">
    <h1>File Locker UI</h1>
    <p></p>
</div>
<div class="container" id="managed_by_vue_js">
    <div class="form-group">
        <label for="files">Select Files:</label>
        <input type="file" class="form-control" id="files" @change="postFiles($event)" multiple />
    </div>
    <!--FILE LIST bootstrap table classes https://www.w3schools.com/bootstrap4/bootstrap_tables.asp -->
    <table class="table table-hover table-striped">
        <tr class="table-info">
            <!--output total file size in MB rounded to 2 decimal places -->
            <th colspan="2">Total File Size: {{totalSize}}MB</th>
        </tr>
        <tr class="table-primary">
            <th>File Name</th>
            <th>File Size (KB)</th>
        </tr>
        <!--loop through sortedFiles which is a computed array -->
        <tr v-for="file in sortedFiles">
            <td><a :href="file.uniqueName" target="_blank"> {{file.name}}</a></td>
            <!-- not required but nice to have -  round to 2 decimal places
            ttps://www.w3schools.com/jsref/jsref_tolocalestring_number.asp convert number to localstring with 2 digits decimal-->
            <td>{{file.size.toLocaleString('en-CA',{maximumFractionDigits:2})}}</td>
        </tr>
    </table>

    <footer class="row bg-info mt-5">
        <div class="col-sm-7">
            <h3>Vardump Vue Data</h3>
            <pre>{{$data}}</pre>
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
        data:{
            fileInfos:[],
            axiosResult:{}
        },
        methods:{
            getSessionFiles:function(){
                axios.get('filelocker-api-soln.php',)
                    .then(response=>{
                        this.axiosResult = response;//ONLY FOR DEBUG
                        this.fileInfos = response.data;
                    })
                    .catch(errors=>{
                        this.axiosResult = errors;//ONLY FOR DEBUG
                    })
            },
            postFiles:function(event){
                var files = event.target.files; //get the file list from the event target
                var formData = new FormData(); //need to post FormData object so php can  populate the $_FILES superglobal

                //use for loop to loop file list
                for( var i=0; i< files.length; i++){
                    //add each file to the formData
                    formData.append('file'+i, files[i]);
                }
                //post the form data to api with proper header
                axios.post('filelocker-api-soln.php',formData, {headers: {'Content-Type': 'multipart/form-data'}})
                    .then(response=>{
                        this.axiosResult = response;//ONLY FOR DEBUG
                        this.fileInfos = response.data;
                    })
                    .catch(errors=>{
                        this.axiosResult = errors;//ONLY FOR DEBUG
                    })
            },
        },
        computed:{
            totalSize:function(){
                var totSize = 0;
                this.fileInfos.forEach(fi=>{totSize+=fi.size})

                //no required but nice to have - round to 2 decimal places
                // https://www.w3schools.com/jsref/jsref_tolocalestring_number.asp convert number to localstring with 2 digits decimal
                return (totSize/1024).toLocaleString('en-CA',{maximumFractionDigits:2});
            },
            sortedFiles:function(){
                //BONUS 2 marks
                // use array sort https://www.w3schools.com/js/js_array_sort.asp
                return this.fileInfos.sort((a,b)=>{
                    // use to lowercase in comparer to make sort case insensitive
                    return a.name.toLowerCase()<b.name.toLowerCase()? -1 : 1;
                });
            }
        },
        mounted() {
            this.getSessionFiles();
        }
    });
</script>

</body>
</html>