<?php
/**************************************
 * File Name: filelocker-ui.php
 * User: Kyle Wei - cst234
 * Date: 2019-10-01
 * Project: CWEB280
 * CWEB280 A1 Q2 - FileLocker UI
 *
 * FileLocker UI using Vue and Axios for data manipulation and transmission
 * Allows users to upload files and utilizes filelocker-api.php for parsing file information
 * into the $_SESSION supergloabal.
 * Stores returned $_SESSION JSON string in fileInfo and generates and displays link to files, and
 * file information.
 *
 * Note - Chose to allow duplicate file uploads and concatenated them to the file array.
 * Can probably check for duplicate file by comparing name and size against session array
 *
 * Concatenating files so users can maintain and view their uploaded file list for the entire session.
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

    <title>FileLocker - UI</title>
</head>


<body>

<div class="jumbotron text-center">
    <h1><strong>FileLocker - UI</strong></h1>
        <h3>Kyle Wei - CST234</h3>
        <p>Var dumps commented out in code</p>
</div>


<div class="container" id="app">
    <h1><strong><u><em>Feed Me Files</em></u></strong></h1>
    <div>
        <input type="file" multiple @change="fileForm($event)"/>
    </div>

    <div class="col-sm-4">
        <!-- Displaying total size of files in MB to 4 decimals. 0 by default -->
        <h2><br><br>Uploaded Files {{(totalFileSize / 1024 / 1024).toFixed(4)}} MB</h2>

        <ul>
            <!-- using v-bind (":[attribute]" for shorthand) to set the href property-->
            <!-- Found @ https://vuejs.org/v2/guide/syntax.html under the Directives/Arguments heading -->
            <li v-for="file in fileInfo['fileArray']" :value="file.name"><a :href="file.UID">{{file.name}}</a> {{(file.size / 1024).toFixed(2)}}KB</li>
        </ul>
    </div>

    <footer class="row bg-info">
        <!-- Iterating through 'fileErrors' and outputting the error messages -->
        <div class="col-sm-5">
            <ul v-for="error in fileInfo['fileErrors']">
                <li style="color: red"><strong>{{error}}</strong></li>
            </ul>
        </div>

<!--                         -->
<!-- VAR DUMPS FOR DEBUGGING -->
<!--                         -->

<!--        <div class="col-sm-4">-->
<!--            <h3>Vardump Vue Data</h3>-->
<!--            <pre>{{$data}}</pre>-->
<!--        </div>-->
<!--        <div class="col-sm-4">-->
<!--            <h3>Vardump Response</h3>-->
<!--            <pre>{{vardumpResponse.data}}</pre>-->
<!--        </div>-->
<!--        <div class="col-sm-4">-->
<!--            <h3>Vardump Fileinfo</h3>-->
<!--            <pre>{{fileInfo}}</pre>-->
<!--        </div>-->
<!--        <div class="col-sm-4">-->
<!--            <h3>Vardump Errors</h3>-->
<!--            <pre>{{vardumpErrors}}</pre>-->
<!--        </div>-->
<!--        <div class="col-sm-4">-->
<!--            <h3>Vardump Session</h3>-->
<!--            <pre>--><?php //var_dump($_SESSION) ?><!--</pre>-->
<!--        </div>-->
    </footer>
</div>

<script>
    /**
     * Code for that Vue and Axios black magic
     */
    new Vue
    ({
        //Vue is managing the element with id="app"
        el: "#app",
        data:
            {
                fileInfo: [],
                fileErrors:[],
                totalFileSize: 0,
                vardumpResponse: {},
                vardumpErrors: {}
            },
        methods:
            {
                //Function for taking uploaded files and adding them to form data
                //Calls postFiles function to send to API
                fileForm: function(event)
                {
                    //Getting data from the origin of an event
                    let fileInput = event.target;
                    let files = fileInput.files;

                    //Creating FormData object, appending file information to the object
                    let formData = new FormData();

                    for(let i = 0; i < (files.length); i++)
                    {
                        formData.append('file' + (i+1), files[i]);
                    }

                    //Passing formData to postFiles() function
                    this.postFiles(formData);
                },

                /**
                 * Function for sending formData to filelocker-api.php for processing.
                 * Assigns response.data to fileInfo, totalFileSize, and sorts the fileArray by name
                 * @param formData - Data to be sent to fileLocker-api.php
                 */
                postFiles: function(formData)
                {
                    axios.post('filelocker-api.php',formData)
                        .then(response =>
                        {
                            this.vardumpResponse = response;    //Strictly for DEBUG output

                            //For some reason, the response data has to be stringified and then parsed to be usable
                            this.fileInfo = JSON.parse(JSON.stringify(response.data));

                            /**
                                Checks allFileSize property of response/fileInfo, assigns to totalFileSizeBytes
                                Converts totalfileSizeBytes to MB by dividing by 1024^2, and rounding to 4 decimals using toFixed(4)
                                    Source: https://www.w3schools.com/jsref/jsref_tofixed.asp
                                Stores result as totalFileSizeMB
                             */
                            this.totalFileSize = this.fileInfo.allFileSize;
                            this.fileErrors = this.fileInfo.fileErrors;

                            this.sortFileArrayByName();

                        })
                        .catch(errors=>
                        {
                            this.vardumpErrors = errors; //Strictly for DEBUG output
                        })
                },
            },
        computed:
            {
                sortFileArrayByName: function()
                {
                    /**
                     * Defining a function for sort to use
                     * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/sort
                     *      - Example under "Objects can be sorted" line referenced
                     * https://www.w3schools.com/jsref/jsref_sort.asp
                     */
                    this.fileInfo['fileArray'].sort(function(a, b)
                    {
                        //Converts the name property of each object to LowerCase - allows for case-insensitive comparisons
                        let aLower = a.name.toLowerCase();
                        let bLower = b.name.toLowerCase();

                        //Determines value to return. If a > b (larger string value//later), return 1,
                        // else check if a < b (smaller string value//earlier), return -1 if it is,
                        // 0 (equal) otherwise
                        return aLower > bLower ? 1 : aLower < bLower ? -1 : 0;
                    });
                },
            },
        mounted()
        {
            //Calling postFiles on load with empty formData, retrieves session info in API, returns it so it can be output
            this.postFiles(new FormData());
        }
    });
</script>

</body>
</html>