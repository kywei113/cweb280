<!--/**************************************-->
<!-- * File Name: regions-ui.php-->
<!-- * User: Kyle Wei - cst234-->
<!-- * Date: 2019-09-25-->
<!-- * Project: CWEB280-->
<!-- *-->
<!-- *-->
<!-- **************************************/-->

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

    <title>Regions AJAX Example</title>
</head>


<body>
<div class="jumbotron text-center">
    <h1>Regions AJAX Example</h1>
</div>

<div class="container" id="managed-by-vue-js"> <!-- Normally the id will be "app" -->

    <!--MINICISE 11: Connect the radio buttons to selectedCountry.
    Add States to the region-api
    When the radio buttons change, call the getRegions function
    -->
    <!--MINICISE 12: Close the modal when a user clicks the radio buttons -->
    <b-modal title="Select Country" v-model="modalShown" no-close-on-backdrop no-close-on-esc hide-footer hide-header-close @shown="fixBackdrop">
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" value="ca" v-model="selectedCountry" @change="getRegions(); modalShown=false">Canada
                </label>
            </div>

            <div class="form-check">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input" value="us" v-model="selectedCountry" @change="getRegions(); modalShown=false">United States
                </label>
            </div>
        </div>
    </b-modal>


    <div v-if="selectedCountry.length > 0" class="form-group">
        <label for="select-regions">Select Region:</label>
        <select id="select-regions" v-model="selectedRegion">
            <option value="">Please select a region</option>
            <option v-for="reg in regions" :value="reg.abbr">{{reg.name}}</option>
        </select>
    </div>
<!-- TYPE REGION INPUT -->
    <datalist id="dlist-regions">
        <option value="">Please select a region</option>
        <option v-for="reg in regions" :value="reg.abbr">{{reg.name}}</option>
    </datalist>

    <div v-if="selectedCountry.length > 0" class="form-group">
        <label for="type-region">Type Region:</label>
        <input type="text" class="form-control" id="type-region" list="dlist-regions" v-model="selectedRegion">
    </div>


    <!-- FULLNAME Input-->
    <div class="form-group">
        <label for="type-region">Type Full Name:</label>
<!--MINICISE 13 - When the User changes the fullname (presses ENTER after typing),
        call the postFullName function
        then display the greeting on the page-->
        <input type="text" class="form-control" v-model="fullName" @keyup.enter="postFullName">
    </div>
<!--    Display greeting here in a SPAN tag -->
    <span>{{greeting}}</span>

    <!-- FILE INPUT -->
    <div class="form-group">
        <label for="type-region">Select File </label>

        <!-- Unlike other forms of input, VUEjs does not work with the v-model directive with file inputs -->
        <input type="file" class="form-control" @change="postFiles($event)" multiple/>
    </div>

    <ol>
        <li v-for="img in imageFiles">{{img.name}}</li>
    </ol>

    <button v-if="selectedCountry.length > 0" class="btn btn-primary" @click="getRegions">Get Regions</button>
    <button @click="modalShown = true">Select Country</button>


    <footer class="row bg-info">
        <div class="col-sm-6">
            <h3>Vardump Vue Data object </h3>
            <pre>{{$data}}</pre>

        </div>

        <div class="col-sm-6">
            <h3>Vardump axios errors</h3>
            <pre>{{vardumpErrors}}</pre>
        </div>

        <div class="col-sm-6">
            <h3>Vardump axios errors</h3>
            <pre><?php var_dump($_SESSION) ?></pre>
        </div>
    </footer>
</div>

<script>
    new Vue
    ({
        el:'#managed-by-vue-js',
        data:
            {
                fileInfos: [],
                greeting: "",
                fullName: "",
                modalShown: false,  /* True = Show Modal, False = Hide Modal */
                regions:[],
                selectedRegion: "",
                selectedCountry: "",
                vardumpResponse:{},
                vardumpErrors:{}
            },
        methods:
            {
                getRegions: function()
                {
                    axios.get('regions-api.php', {params:{country:this.selectedCountry}})
                        .then(response =>
                        {
                            this.vardumpResponse = response;    //Strictly for DEBUG output
                            this.regions = response.data;
                        })
                        .catch(errors=>
                        {
                            this.vardumpErrors = errors; //Strictly for DEBUG output
                        })
                },

                fixBackdrop: function()
                {
                    document.querySelector('.modal-backdrop').classList.add('show');
                },

                postFullName: function()
                {
                    //Need to use the built-in JavaScript Object "FormData" to send to the server
                    //so PHP can read the posted data and put it in the $_POST superglobal
                    //Use axios to make a POST request
                    let formData = new FormData();
                    formData.append('fullname', this.fullName);

                    axios.post('fullname-api.php',formData)
                        .then(response =>
                        {
                            this.vardumpResponse = response;    //Strictly for DEBUG output
                            this.greeting = response.data;
                        })
                        .catch(errors=>
                        {
                            this.vardumpErrors = errors; //Strictly for DEBUG output
                        })
                },

                postFiles: function(event)
                {
                    //Get the file input object from the event
                    let fileInput = event.target;   //The "Target" is the element that fired the change event. In this case, the file input.
                    let files = fileInput.files;    //Files is the array (actually a FileList object) of files the user selected from the file dialogue
                    let formData = new FormData();  //Need to use FormData so PHP can fill the $_POST and $_FILES superglobals

                    //MINICISE 14: Use a FOR loop to loop through the files array and append each file to the formData
                    //Remember, the key has to be unique
                    for(let i = 0; i < files.length; i++)
                    {
                        formData.append('file' + (i+1), files[i]);
                    }

                    axios.post('fileupload-api.php', formData, {header:{'Content-type':'multipart/form-data'}})
                        .then(response =>
                        {
                            this.vardumpResponse = response;
                            this.fileInfos = response.data;
                        })
                        .catch(errors=>
                        {
                           this.vardumpErrors = errors;
                        })
                }
            },
        computed: //Computed Properties - they're actually JavaScript functions that return something (array, int, strings, objects, etc.)
            {
                imageFiles: function()
                {
                    let images = new Array();
                    // Object.values(this.fileInfos).forEach
                    // (fi=>
                    // {
                    //     if(fi.type.startsWith('image/'))
                    //     {
                    //         images.push(fi);
                    //     }
                    // });

                    Object.values(this.fileInfos).forEach(fi=> { fi.type.startsWith('image/') ? images.push(fi) : ""});

                    return images;
                }
            },
        mounted()
        {
            //Mounted is essentially the equivalent to $(document).ready in jQuery
            this.modalShown = true;
        }
    });
</script>

</body>
</html>